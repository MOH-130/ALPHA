import os
import re

files = [
    'audit_images_and_settings.py',
    'audit_views.py',
    'check_missing_images.py',
    'check_translations.py',
    'populate_db.py',
    'setup_php.py',
    'test_admin_flow.py',
    'test_all_pages_e2e.py',
    'update_all_store_settings.py'
]

pattern = re.compile(r'# auto-generated; do not edit\r?\n.*?except Exception:\r?\n\s*pass\r?\n*', re.DOTALL)

for f in files:
    if os.path.exists(f):
        with open(f, 'r', encoding='utf-8', errors='ignore') as fp:
            content = fp.read()
        if '# auto-generated; do not edit' in content:
            new_content = pattern.sub('', content)
            with open(f, 'w', encoding='utf-8') as fp:
                fp.write(new_content)
            print(f"Cleaned {f}")
        else:
            print(f"Already clean: {f}")
