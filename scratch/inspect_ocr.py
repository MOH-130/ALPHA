import json
import sys

sys.stdout.reconfigure(encoding='utf-8')

with open('scratch/ocr_results.json', encoding='utf-8-sig') as f:
    results = json.load(f)

print(f"Total OCR results: {len(results)}\n")

for r in results:
    text_ar = r.get('ocr_ar', '').strip().replace('\n', ' | ')
    text_en = r.get('ocr_en', '').strip().replace('\n', ' | ')
    prices = r.get('detected_prices', [])
    print(f"--- Group {r['group_id']}: {r['chosen_name']} ---")
    if prices:
        print(f"    Prices: {prices}")
    if text_ar:
        print(f"    AR: {text_ar[:120]}")
    if text_en:
        print(f"    EN: {text_en[:120]}")
    print()
