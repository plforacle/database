param([switch]$Zip)
$ErrorActionPreference = 'Stop'
$workshopRoot = Split-Path $PSScriptRoot -Parent
$buildRoot = Join-Path $workshopRoot 'release-build-20260916'
$sourceRoot = Join-Path $buildRoot 'source'
$packageRoot = Join-Path $buildRoot 'ol-value-navigator-v3-r3'
if (-not $Zip) {
    if (Test-Path -LiteralPath $packageRoot) { throw 'Package staging already exists.' }
    New-Item -ItemType Directory -Path (Join-Path $packageRoot 'app') | Out-Null
    foreach ($name in @('app','config','public','resources','vendor','composer.json','composer.lock')) {
        Copy-Item -LiteralPath (Join-Path $sourceRoot $name) -Destination (Join-Path $packageRoot 'app') -Recurse
    }
    foreach ($file in Get-ChildItem -LiteralPath $PSScriptRoot -File) {
        if ($file.Name -notin @('build-package.ps1','entry.php')) { Copy-Item -LiteralPath $file.FullName -Destination $packageRoot }
    }
    Copy-Item -LiteralPath (Join-Path $PSScriptRoot 'entry.php') -Destination (Join-Path $packageRoot 'app/public/package-index.php')
    New-Item -ItemType Directory -Path (Join-Path $packageRoot 'examples') | Out-Null
    Copy-Item -LiteralPath (Join-Path $sourceRoot 'tests/Fixtures/Coverage/synthetic-import-v1.3.1.tsv') -Destination (Join-Path $packageRoot 'examples/synthetic-import.tsv')
    Write-Output $packageRoot
    exit
}
if (-not (Test-Path -LiteralPath (Join-Path $packageRoot 'BUILD-REPORT.md'))) { throw 'Record actual QA before packaging.' }
$manifest = foreach ($file in Get-ChildItem -LiteralPath $packageRoot -Recurse -File | Sort-Object FullName) {
    if ($file.Name -eq 'SHA256SUMS') { continue }
    $relative = $file.FullName.Substring($packageRoot.Length + 1).Replace('\','/')
    if ($relative -match '(^|/)(runtime\.json|database\.json|\.env|id_rsa|composer\.phar)$') { throw 'Private/build file detected.' }
    (Get-FileHash -LiteralPath $file.FullName -Algorithm SHA256).Hash.ToLowerInvariant() + '  ' + $relative
}
[IO.File]::WriteAllText((Join-Path $packageRoot 'SHA256SUMS'), (($manifest -join "`n") + "`n"), [Text.UTF8Encoding]::new($false))
$releaseRoot = Join-Path $workshopRoot 'catalog-database/files'
New-Item -ItemType Directory -Path $releaseRoot -Force | Out-Null
$zipPath = Join-Path $releaseRoot 'ol-value-navigator-v3-r3.zip'
if (Test-Path -LiteralPath $zipPath) { throw 'Release ZIP already exists; do not overwrite it.' }
Add-Type -AssemblyName System.IO.Compression.FileSystem
Add-Type -AssemblyName System.IO.Compression
$archive = [IO.Compression.ZipFile]::Open($zipPath, [IO.Compression.ZipArchiveMode]::Create)
try {
    foreach ($file in Get-ChildItem -LiteralPath $packageRoot -Recurse -File | Sort-Object FullName) {
        $relative = $file.FullName.Substring($packageRoot.Length + 1).Replace('\','/')
        $entryName = (Split-Path $packageRoot -Leaf) + '/' + $relative
        [void][IO.Compression.ZipFileExtensions]::CreateEntryFromFile($archive, $file.FullName, $entryName, [IO.Compression.CompressionLevel]::Optimal)
    }
} finally {
    $archive.Dispose()
}
$hash = (Get-FileHash -LiteralPath $zipPath -Algorithm SHA256).Hash.ToLowerInvariant()
[IO.File]::WriteAllText(($zipPath + '.sha256'), "$hash  ol-value-navigator-v3-r3.zip`n", [Text.UTF8Encoding]::new($false))
Write-Output "$hash  $zipPath"
