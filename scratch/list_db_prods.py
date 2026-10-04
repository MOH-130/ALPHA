import json
import sys

sys.stdout.reconfigure(encoding='utf-8')

with open('scratch/db_products.json', encoding='utf-8') as f:
    products = json.load(f)

print(f"Total DB products: {len(products)}\n")
for p in products:
    print(f"ID {p['id']:2d} | Slug: {p['slug']:<35} | Img: {p['primary_image']:<25} | Price: {p['price']:<7} | Name: {p['name_ar']}")
