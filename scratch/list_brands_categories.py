import sqlite3, sys
sys.stdout.reconfigure(encoding='utf-8')

conn = sqlite3.connect('storage/data/database.sqlite')
cursor = conn.cursor()

print("--- BRANDS ---")
for row in cursor.execute("SELECT id, name_ar, name_en, slug FROM brands").fetchall():
    print(row)

print("\n--- CATEGORIES ---")
for row in cursor.execute("SELECT id, name_ar, name_en, slug FROM categories").fetchall():
    print(row)

conn.close()
