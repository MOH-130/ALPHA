import os
import sys
import json
import hashlib
from collections import defaultdict

sys.stdout.reconfigure(encoding='utf-8')

folder = r"C:\Users\elnou\Downloads\New folder"
hash_map = defaultdict(list)

for f in sorted(os.listdir(folder)):
    p = os.path.join(folder, f)
    if os.path.isfile(p):
        with open(p, 'rb') as fp:
            h = hashlib.sha256(fp.read()).hexdigest()
        hash_map[h].append((f, os.path.getsize(p)))

print(f"Total unique images by SHA256: {len(hash_map)} out of {sum(len(v) for v in hash_map.values())} files\n")

unique_images = []
for idx, (h, file_list) in enumerate(hash_map.items(), 1):
    main_name = file_list[0][0]
    # Pick the most descriptive name if any
    descriptive_names = [name for name, _ in file_list if not name.startswith("ChatGPT Image")]
    chosen_name = descriptive_names[0] if descriptive_names else main_name
    unique_images.append({
        'group_id': idx,
        'hash': h,
        'size': file_list[0][1],
        'chosen_name': chosen_name,
        'all_names': [name for name, _ in file_list],
        'duplicates_count': len(file_list)
    })
    print(f"Group {idx:2d} (x{len(file_list)}): {chosen_name}")
    if len(file_list) > 1:
        for name, _ in file_list:
            if name != chosen_name:
                print(f"    -> Alias/Duplicate: {name}")

with open("scratch/unique_images.json", "w", encoding="utf-8") as out:
    json.dump(unique_images, out, ensure_ascii=False, indent=2)
