import sqlite3, json, sys
sys.stdout.reconfigure(encoding='utf-8')

conn = sqlite3.connect('storage/data/database.sqlite')
cursor = conn.cursor()
cursor.execute("SELECT id, name_ar, price, (SELECT image_path FROM product_images WHERE product_id = products.id AND is_primary = 1 LIMIT 1) as img FROM products WHERE id = 23")
print(cursor.fetchall())
conn.close()
