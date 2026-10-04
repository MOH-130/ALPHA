import os
import sys
import json
import hashlib

sys.stdout.reconfigure(encoding='utf-8')

# 1. Load unique images from Downloads
with open("scratch/unique_images.json", encoding="utf-8") as f:
    unique_downloads = json.load(f)

downloads_by_hash = {item['hash']: item for item in unique_downloads}

# 2. Hash existing uploads
existing_by_hash = {}
for root, dirs, files in os.walk("public/uploads"):
    for f in files:
        p = os.path.join(root, f)
        with open(p, 'rb') as fp:
            h = hashlib.sha256(fp.read()).hexdigest()
        existing_by_hash[h] = p

print(f"Total existing uploads hashed: {len(existing_by_hash)}")

# 3. Check matches
already_in_store = []
not_in_store = []

for h, item in downloads_by_hash.items():
    if h in existing_by_hash:
        item['existing_path'] = existing_by_hash[h]
        already_in_store.append(item)
    else:
        not_in_store.append(item)

print(f"Downloads images ALREADY in public/uploads: {len(already_in_store)}")
print(f"Downloads images NOT YET in store: {len(not_in_store)}\n")

print("=== ALREADY IN STORE ===")
for item in already_in_store:
    print(f"  [ALREADY EXISTS] {item['chosen_name']} -> {item['existing_path']}")

print("\n=== NOT YET IN STORE (NEW CANDIDATES) ===")
for item in not_in_store:
    print(f"  [NEW] Group {item['group_id']}: {item['chosen_name']} (Aliases: {len(item['all_names'])})")

with open("scratch/already_in_store.json", "w", encoding="utf-8") as out:
    json.dump(already_in_store, out, ensure_ascii=False, indent=2)

with open("scratch/not_in_store.json", "w", encoding="utf-8") as out:
    json.dump(not_in_store, out, ensure_ascii=False, indent=2)
