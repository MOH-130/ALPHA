import json, sys
sys.stdout.reconfigure(encoding='utf-8')
prods = json.load(open('scratch/db_products.json', encoding='utf-8'))
if prods:
    print("Keys:", list(prods[0].keys()))
for p in prods:
    name = p.get('name') or p.get('title') or p.get('name_ar') or p.get('slug')
    print(f"{p.get('id')} | {name} | Price: {p.get('price')} | Cat: {p.get('category_id')} | Img: {p.get('image') or p.get('thumbnail')}")
