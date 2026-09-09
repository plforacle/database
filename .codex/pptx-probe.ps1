$ErrorActionPreference='Stop'
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem
$source='C:/Users/Perside/Documents/GitHub/database/olvn-powerpoint-test.pptx'
$zip=[IO.Compression.ZipFile]::OpenRead($source)
$parts=@{}
try {foreach($entry in $zip.Entries){$reader=New-Object IO.StreamReader($entry.Open());try{$parts[$entry.FullName]=$reader.ReadToEnd()}finally{$reader.Dispose()}}}finally{$zip.Dispose()}
foreach($variant in @('presentation-theme','notes-theme','both-themes')) {
 $out=$parts.Clone()
 if($variant -in @('presentation-theme','both-themes')) {
  $out['ppt/_rels/presentation.xml.rels']=$out['ppt/_rels/presentation.xml.rels'].Replace('</Relationships>','<Relationship Id="rId7" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme" Target="theme/theme1.xml"/></Relationships>')
 }
 if($variant -in @('notes-theme','both-themes')) {
  $out['ppt/notesMasters/_rels/notesMaster1.xml.rels']=$out['ppt/notesMasters/_rels/notesMaster1.xml.rels'].Replace('theme1.xml','theme2.xml')
  $out['ppt/theme/theme2.xml']=$out['ppt/theme/theme1.xml']
  $out['[Content_Types].xml']=$out['[Content_Types].xml'].Replace('</Types>','<Override PartName="/ppt/theme/theme2.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml"/></Types>')
 }
 $path=Join-Path (Split-Path $PSScriptRoot) ($variant+'.pptx')
 $new=[IO.Compression.ZipFile]::Open($path,[IO.Compression.ZipArchiveMode]::Create)
 try {foreach($key in $out.Keys){$entry=$new.CreateEntry($key);$stream=$entry.Open();$writer=New-Object IO.StreamWriter($stream,(New-Object Text.UTF8Encoding($false)));try{$writer.Write($out[$key])}finally{$writer.Dispose();$stream.Dispose()}}}finally{$new.Dispose()}
 Write-Output $path
}
