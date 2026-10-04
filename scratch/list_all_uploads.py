import os, sys
sys.stdout.reconfigure(encoding='utf-8')

upload_dir = 'public/uploads/products'
files = os.listdir(upload_dir)
print(f"Total files in {upload_dir}: {len(files)}")
for f in sorted(files):
    if f.endswith(('.png', '.jpg', '.jpeg', '.webp')):
        sz = os.path.getsize(os.path.join(upload_dir, f))
        print(f"  {f} ({sz:,} bytes)")
