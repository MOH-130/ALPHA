import json, sys
sys.stdout.reconfigure(encoding='utf-8')

not_in_store = json.load(open('scratch/not_in_store.json', encoding='utf-8-sig'))
all_not_in_store_gids = [x['group_id'] for x in not_in_store]

reviewed_gids = [2, 6, 7, 10, 11, 12, 13, 14, 15, 16, 17, 19, 20, 21, 22, 23, 24, 25, 36, 37, 38, 39, 44, 46, 47, 48, 52, 53, 54, 55, 57]
remaining = [gid for gid in all_not_in_store_gids if gid not in reviewed_gids]

print(f"Total not in store: {len(all_not_in_store_gids)}")
print(f"Total reviewed: {len(reviewed_gids)}")
print(f"Remaining: {remaining}")
