from pathlib import Path
import json,re,sys
from urllib.parse import urlsplit,unquote
root=Path(__file__).resolve().parents[1]
errors=[]; checks=0; images=0; links=0
manifest=root/'workshops/tenancy/manifest.json'
m=json.loads(manifest.read_text(encoding='utf-8'))
for t in m['tutorials']:
 checks+=1
 p=(manifest.parent/t['filename']).resolve()
 if not p.is_relative_to(root) or not p.is_file(): errors.append('Missing manifest target: '+t['filename'])
for p in root.rglob('*'):
 if p.suffix not in ['.md','.html','.json','.py','.sh','.txt']:continue
 s=p.read_text(encoding='utf-8');checks+=1
 if chr(8212) in s:errors.append('Em dash: '+str(p.relative_to(root)))
 if p.suffix!='.md':continue
 if p.name!='README.md':
  checks+=1
  nums=[int(n) for n in re.findall(r'^## Task (\d+):',s,re.M)]
  if nums!=list(range(1,len(nums)+1)):errors.append('Task sequence: '+str(p))
 for img,alt,target in re.findall(r'(!?)\[([^\]]*)\]\(([^)]+)\)',s):
  if img:
   images+=1;checks+=1
   if not alt.strip():errors.append('Missing image alt: '+str(p))
  if urlsplit(target).scheme or target.startswith('#'):continue
  links+=1;checks+=1
  dest=(p.parent/unquote(target.split('#')[0])).resolve()
  if not dest.is_relative_to(root) or not dest.exists():errors.append('Unresolved local target: '+str(p)+' '+target)
report={'status':'PASS' if not errors else 'FAIL','checks':checks,'manifest_tutorials':len(m['tutorials']),'referenced_images':images,'local_link_targets':links,'errors':errors,'upstream_validator':'See livelabs-validator.txt; separate upstream Oracle markdown validation.','limits':['No OCI runtime execution.','No browser render verification.','No saved private-ingress screenshot in source recording.'],'mode':'how-to-guide'}
(root/'authoring/qa-results.json').write_text(json.dumps(report,indent=2)+'\n',encoding='utf-8')
print(json.dumps(report,indent=2));sys.exit(bool(errors))
