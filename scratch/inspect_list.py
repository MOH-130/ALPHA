import json
import sys

sys.stdout.reconfigure(encoding='utf-8')

with open('scratch/folder_files.json', encoding='utf-8') as f:
    files = json.load(f)

for idx, file in enumerate(files, 1):
    print(f"{idx:2d}. {file['name']} ({file['size']} bytes)")
