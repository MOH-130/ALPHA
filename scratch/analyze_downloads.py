import os
import sys
import json
import sqlite3

sys.stdout.reconfigure(encoding='utf-8')

folder = r"C:\Users\elnou\Downloads\New folder"
files_info = []
for root, dirs, files in os.walk(folder):
    for f in sorted(files):
        p = os.path.join(root, f)
        files_info.append({
            'name': f,
            'rel_path': os.path.relpath(p, folder),
            'full_path': p,
            'size': os.path.getsize(p)
        })

print(f"Total files found: {len(files_info)}")
with open("scratch/folder_files.json", "w", encoding="utf-8") as out:
    json.dump(files_info, out, ensure_ascii=False, indent=2)

conn = sqlite3.connect("storage/data/database.sqlite")
conn.row_factory = sqlite3.Row
cur = conn.cursor()

products = cur.execute("SELECT id, name_ar, name_en, slug, price, compare_price, primary_image, category_id, brand_id, short_description_ar FROM products").fetchall()
print(f"Total existing products in DB: {len(products)}")

cats = cur.execute("SELECT id, name_ar, name_en, slug FROM categories").fetchall()
print(f"Total categories: {len(cats)}")
for c in cats:
    print(f"  Cat {c['id']}: {c['name_ar']} / {c['name_en']} ({c['slug']})")

brands = cur.execute("SELECT id, name_ar, name_en, slug FROM brands").fetchall()
print(f"Total brands: {len(brands)}")
for b in brands:
    print(f"  Brand {b['id']}: {b['name_ar']} / {b['name_en']} ({b['slug']})")

with open("scratch/db_products.json", "w", encoding="utf-8") as out:
    json.dump([dict(p) for p in products], out, ensure_ascii=False, indent=2)

print("Done writing scratch/folder_files.json and scratch/db_products.json")
