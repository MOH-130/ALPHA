import urllib.request
import urllib.parse
import http.cookiejar
import sys

sys.stdout.reconfigure(encoding='utf-8')

cookie_jar = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cookie_jar))

print("1. Testing Storefront GET /...")
resp = opener.open("http://127.0.0.1:8000/")
print(f"Storefront Status: {resp.status}, Content Length: {len(resp.read())}")

print("2. Testing Admin Login Page GET /admin/login...")
resp = opener.open("http://127.0.0.1:8000/admin/login")
html = resp.read().decode('utf-8')
print(f"Admin Login Page Status: {resp.status}")

# Extract CSRF token if present
import re
token_match = re.search(r'name="_token" value="([^"]+)"', html)
token = token_match.group(1) if token_match else ''
print(f"CSRF Token extracted: {token[:12]}...")

# Extract and solve CAPTCHA
captcha_match = re.search(r'id="captcha-question-text">\s*([0-9]+)\s*([\+\-×x*])\s*([0-9]+)\s*=', html)
if captcha_match:
    n1 = int(captcha_match.group(1))
    op = captcha_match.group(2)
    n2 = int(captcha_match.group(3))
    if op == '+':
        ans = n1 + n2
    elif op == '-':
        ans = n1 - n2
    else:
        ans = n1 * n2
    print(f"CAPTCHA solved: {n1} {op} {n2} = {ans}")
else:
    ans = '0'

print("3. Testing Admin Login POST /admin/login...")
post_data = urllib.parse.urlencode({
    '_token': token,
    'email': 'admin@store.com',
    'password': 'password123',
    'captcha': str(ans)
}).encode('utf-8')

req = urllib.request.Request("http://127.0.0.1:8000/admin/login", data=post_data)
resp = opener.open(req)
print(f"Login Response URL: {resp.geturl()}")
print(f"Login Status: {resp.status}")

print("4. Testing Admin Dashboard GET /admin/dashboard...")
resp = opener.open("http://127.0.0.1:8000/admin/dashboard")
dashboard_html = resp.read().decode('utf-8')
print(f"Dashboard Status: {resp.status}, Bytes: {len(dashboard_html)}")
assert "Alpha Gaming Admin" in dashboard_html or "لوحة التحكم" in dashboard_html or "Dashboard" in dashboard_html, "Admin name or dashboard text missing"

print("5. Testing Admin Theme Settings GET /admin/theme...")
resp = opener.open("http://127.0.0.1:8000/admin/theme")
theme_html = resp.read().decode('utf-8')
print(f"Theme Customizer Status: {resp.status}, Bytes: {len(theme_html)}")
assert "Background Presets" in theme_html or "قوالب" in theme_html, "Theme presets missing"

print("\n🎉 ALL TESTS PASSED! ADMIN LOGIN AND THEME CUSTOMIZER FULLY OPERATIONAL!")
