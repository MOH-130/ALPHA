import sqlite3

conn = sqlite3.connect('storage/data/database.sqlite')
cursor = conn.cursor()

for table in ['brands', 'products', 'product_images']:
    cursor.execute(f"PRAGMA table_info({table})")
    print(f"--- Table: {table} ---")
    for col in cursor.fetchall():
        print(f"  {col[1]} ({col[2]})")

conn.close()
