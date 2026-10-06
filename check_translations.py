import os
import re
import sys

def parse_php_array(filepath):
    content = open(filepath, encoding='utf-8', errors='ignore').read()
    matches = re.findall(r"['\"]([^'\"]+)['\"]\s*=>\s*['\"](.*)['\"]", content)
    return {k: v for k, v in matches}

ar_dict = parse_php_array('lang/ar/messages.php')
en_dict = parse_php_array('lang/en/messages.php')

used_keys = set()
for root, _, files in os.walk('resources/views'):
    for f in files:
        if f.endswith('.php'):
            path = os.path.join(root, f)
            content = open(path, encoding='utf-8', errors='ignore').read()
            keys = re.findall(r"__\(['\"]([^'\"]+)['\"]\)", content)
            for k in keys:
                used_keys.add((k, path))

missing_ar = []
missing_en = []
for k, path in used_keys:
    parts = k.split('.')
    key_name = parts[-1] if len(parts) > 1 else k
    
    # Check if k or key_name is in ar_dict
    if k not in ar_dict and key_name not in ar_dict:
        missing_ar.append((k, path))
    if k not in en_dict and key_name not in en_dict:
        missing_en.append((k, path))

print(f"Total unique translation calls: {len(used_keys)}")
print(f"Missing in Arabic: {len(missing_ar)}")
for k, path in sorted(set(missing_ar)):
    print(f"  AR Missing: '{k}' used in {os.path.relpath(path)}")

print(f"\nMissing in English: {len(missing_en)}")
for k, path in sorted(set(missing_en)):
    print(f"  EN Missing: '{k}' used in {os.path.relpath(path)}")
