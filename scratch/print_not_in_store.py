import json
import sys

sys.stdout.reconfigure(encoding='utf-8')

with open('scratch/not_in_store.json', encoding='utf-8') as f:
    items = json.load(f)

print(f"Total not in store: {len(items)}\n")
for idx, item in enumerate(items, 1):
    print(f"{idx:2d}. Group {item['group_id']:2d}: {item['chosen_name']}")
    if len(item['all_names']) > 1:
        print(f"    Aliases: {item['all_names']}")
