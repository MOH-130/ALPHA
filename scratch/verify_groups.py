import json, sys
sys.stdout.reconfigure(encoding='utf-8')

groups = json.load(open('scratch/unique_images.json', encoding='utf-8-sig'))
already = json.load(open('scratch/already_in_store.json', encoding='utf-8-sig'))
not_in = json.load(open('scratch/not_in_store.json', encoding='utf-8-sig'))

print(f"Total Unique Groups: {len(groups)}")
print(f"Already in store: {len(already)}")
print(f"Not in store: {len(not_in)}")

already_gids = set(x['group_id'] for x in already)
not_in_gids = set(x['group_id'] for x in not_in)

print(f"Union: {len(already_gids | not_in_gids)}")
