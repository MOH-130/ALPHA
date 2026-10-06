import os
import re

view_files = []
for root, _, files in os.walk('resources/views'):
    for f in files:
        if f.endswith('.blade.php') or f.endswith('.php'):
            view_files.append(os.path.join(root, f))

print(f"Total view files: {len(view_files)}")
issues = []

for path in view_files:
    if 'invoice' in path: continue # invoices can have white for printing
    content = open(path, encoding='utf-8', errors='ignore').read()
    # Check for hardcoded white backgrounds
    matches = re.findall(r'background\s*:\s*(#ffffff|#fff\b|rgb\(255,\s*255,\s*255\)|#f8fafc|#f1f5f9)', content, re.IGNORECASE)
    if matches:
        issues.append((path, len(matches)))

print("\nFiles with hardcoded light backgrounds:")
for p, count in issues:
    print(f"  {os.path.relpath(p)}: {count} matches")
