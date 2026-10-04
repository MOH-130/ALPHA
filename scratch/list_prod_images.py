import sqlite3, sys
sys.stdout.reconfigure(encoding='utf-8')

conn = sqlite3.connect('storage/data/database.sqlite')
cursor = conn.cursor()

for row in cursor.execute("SELECT id, name_ar, primary_image FROM products ORDER BY id"):
    print(f"Product {row[0]}: {row[2]} -> {row[1][:40]}")

conn.close()
