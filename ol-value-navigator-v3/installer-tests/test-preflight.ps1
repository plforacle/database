$ErrorActionPreference = 'Stop'
$root = Split-Path $PSScriptRoot -Parent
$php = Join-Path $root 'release-build-20260916/php/php.exe'
$source = Join-Path $root 'package-source'
$testDir = Join-Path $root ('release-build-20260916/installer-test-' + [guid]::NewGuid().ToString('N'))
New-Item -ItemType Directory -Path $testDir | Out-Null
Copy-Item -LiteralPath (Join-Path $source 'configure.php') -Destination $testDir
Copy-Item -LiteralPath (Join-Path $PSScriptRoot 'runtime.php') -Destination $testDir
$config = Join-Path $testDir 'runtime.json'
$script = Join-Path $testDir 'configure.php'
function Invoke-SetupTest([string[]]$Arguments, [string]$Password, [int]$ExpectedExit) {
    # Use stdin without a trailing newline, matching the real installer.
    $psi = New-Object Diagnostics.ProcessStartInfo
    $psi.FileName = $php
    $psi.Arguments = '"' + $script + '" ' + ($Arguments -join ' ')
    $psi.UseShellExecute = $false
    $psi.RedirectStandardInput = $true
    $psi.RedirectStandardOutput = $true
    $psi.RedirectStandardError = $true
    $p = [Diagnostics.Process]::Start($psi)
    $p.StandardInput.Write($Password)
    $p.StandardInput.Close()
    $stdout = $p.StandardOutput.ReadToEnd()
    $stderr = $p.StandardError.ReadToEnd()
    $p.WaitForExit()
    if ($p.ExitCode -ne $ExpectedExit) { throw "Unexpected exit $($p.ExitCode): $stdout $stderr" }
    if (($stdout + $stderr).Contains($Password) -and $Password.Length -gt 0) { throw 'Password leaked in output' }
    $p.Dispose()
}
Invoke-SetupTest @('--preflight','10.0.1.88','bundled') 'wrong-test-password' 1
if (Test-Path $config) { throw 'Rejected credentials created configuration' }
Write-Output 'PASS: rejected password leaves no configuration'
Invoke-SetupTest @('--preflight','10.0.1.88','bundled') 'invented-test-password' 0
if (Test-Path $config) { throw 'Preflight wrote configuration' }
Write-Output 'PASS: successful preflight is read-only'
Invoke-SetupTest @('10.0.1.88','bundled') 'invented-test-password' 0
$hash = (Get-FileHash $config).Hash
Invoke-SetupTest @('10.0.1.88','bundled') 'invented-test-password' 1
if ((Get-FileHash $config).Hash -ne $hash) { throw 'Existing credentials changed' }
if (@(Get-ChildItem $testDir -Filter '.setup-*' -Force).Count) { throw 'Temporary credential file remained' }
Write-Output 'PASS: credential creation is atomic and existing credentials are preserved'
$install = [IO.File]::ReadAllText((Join-Path $source 'install.sh'))
if ($install.IndexOf('php "$SOURCE/configure.php" --preflight') -gt $install.IndexOf('install -d -m 0755 /opt/olvn-v3')) { throw 'Deployment precedes preflight' }
if (-not $install.Contains('cmp -s "$SOURCE/SHA256SUMS" "$TARGET/SHA256SUMS"')) { throw 'Retry identity guard missing' }
if (-not $install.Contains('cmp -s "$SOURCE/apache.conf" "$APACHE"')) { throw 'Apache conflict guard missing' }
Write-Output 'PASS: installer ordering and exact-package/conflict guards present (static checks)'
Write-Output 'Database is mocked. Linux ownership, SELinux, Apache and end-to-end retries remain untested.'
