import os
import sqlite3
import sys

sys.stdout.reconfigure(encoding='utf-8')
db_path = 'storage/data/database.sqlite'
conn = sqlite3.connect(db_path)
cursor = conn.cursor()

products = cursor.execute("SELECT id, name_ar, name_en, primary_image FROM products").fetchall()
uploads_dir = 'public/uploads/products'
available_files = set(os.listdir(uploads_dir)) if os.path.exists(uploads_dir) else set()

missing = []
for pid, name_ar, name_en, img in products:
    if not img or img not in available_files:
        missing.append((pid, name_ar, name_en, img))

print(f"Total products: {len(products)}")
print(f"Missing images: {len(missing)}")
for pid, name_ar, name_en, img in missing:
    print(f"  ID {pid}: '{name_ar}' / '{name_en}' -> Image: '{img}'")

print("\nFiles in public/uploads/products:")
for f in sorted(available_files):
    print(f"  {f}")
