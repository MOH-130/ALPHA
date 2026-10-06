import os
import sqlite3
import re

db_path = 'storage/data/database.sqlite'
conn = sqlite3.connect(db_path)
cursor = conn.cursor()

print("1. Auditing Product Images in SQLite...")
products = cursor.execute("SELECT id, name_ar, primary_image FROM products").fetchall()
uploads_dir = 'public/uploads/products'
available_files = set(os.listdir(uploads_dir)) if os.path.exists(uploads_dir) else set()

missing_images = []
for pid, name, img in products:
    if not img or img not in available_files:
        missing_images.append((pid, name, img))

print(f"Total products: {len(products)}")
print(f"Missing or broken product images: {len(missing_images)}")
for pid, name, img in missing_images:
    print(f"  [Product #{pid}] {name}: '{img}' NOT FOUND in {uploads_dir}")

print("\n2. Available files in public/uploads/products:")
for f in sorted(available_files):
    print(f"  {f}")
