import json
import sys

sys.stdout.reconfigure(encoding='utf-8')

with open('scratch/ocr_results.json', encoding='utf-8-sig') as f:
    ocr_data = json.load(f)

with open('scratch/already_in_store.json', encoding='utf-8') as f:
    already = json.load(f)

already_hashes = {a['hash']: a for a in already}

print(f"Total OCR records: {len(ocr_data)}\n")

new_items = []
existing_items = []

for item in ocr_data:
    h = item['hash'] if 'hash' in item else None
    name = item['chosen_name']
    text_ar = item.get('ocr_ar', '').strip()
    text_en = item.get('ocr_en', '').strip()
    prices = item.get('detected_prices', [])
    
    summary = {
        'group_id': item['group_id'],
        'name': name,
        'all_names': item['all_names'],
        'prices': prices,
        'text_sample_ar': ' | '.join([l.strip() for l in text_ar.split('\n') if l.strip()][:4]),
        'text_sample_en': ' | '.join([l.strip() for l in text_en.split('\n') if l.strip()][:4])
    }
    
    # Check if in already_hashes or matching name
    is_already = any(name in a['all_names'] for a in already)
    if is_already:
        existing_items.append(summary)
    else:
        new_items.append(summary)

print(f"=== NEW CANDIDATE IMAGES ({len(new_items)}) ===")
for it in new_items:
    print(f"\n[GROUP {it['group_id']:2d}] {it['name']}")
    print(f"   Prices: {it['prices']}")
    print(f"   AR: {it['text_sample_ar']}")
    print(f"   EN: {it['text_sample_en']}")

with open('scratch/new_candidates_parsed.json', 'w', encoding='utf-8') as out:
    json.dump(new_items, out, ensure_ascii=False, indent=2)
