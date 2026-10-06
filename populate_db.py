# -*- coding: utf-8 -*-
import sqlite3
import os
import json
from datetime import datetime, timedelta

db_path = r'storage/data/database.sqlite'
os.makedirs(os.path.dirname(db_path), exist_ok=True)
conn = sqlite3.connect(db_path)
cursor = conn.cursor()

# Enable Foreign Keys
cursor.execute("PRAGMA foreign_keys = OFF;")

# Clear existing sample data to cleanly reseed
tables = [
    'categories', 'brands', 'products', 'product_attributes', 'product_images',
    'reviews', 'coupons', 'shipping_methods', 'settings', 'theme_settings',
    'pages', 'currencies', 'languages', 'roles', 'users'
]
for t in tables:
    cursor.execute(f"DELETE FROM {t};")
    cursor.execute(f"DELETE FROM sqlite_sequence WHERE name='{t}';")

now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
future_60d = (datetime.now() + timedelta(days=60)).strftime('%Y-%m-%d %H:%M:%S')
future_45d = (datetime.now() + timedelta(days=45)).strftime('%Y-%m-%d %H:%M:%S')

# 1. Roles
cursor.executemany("""
INSERT INTO roles (name, slug, description, created_at, updated_at)
VALUES (?, ?, ?, ?, ?)
""", [
    ('Super Admin', 'super_admin', 'Full control over system', now, now),
    ('Manager', 'manager', 'Store manager', now, now),
    ('Customer', 'customer', 'Registered store shopper', now, now)
])

# 2. Users (Admin & Demo Customer)
cursor.execute("""
INSERT INTO users (name, email, password, phone, role, status, is_approved, affiliate_code, city, country, address, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
""", (
    'Alpha Gaming Admin', 'admin@store.com',
    '$2y$12$lZ6CWaALA8P2lq7f1DZPwuXtgvqBoZIuQSnsCfJTe7XUqfWajgvgi', # password123
    '+213 555 12 34 56', 'admin', 'active', 1, 'ALPHA01', 'الجزائر العاصمة', 'Algeria', 'شارع ديدوش مراد، وسط الجزائر العاصمة', now, now
))

cursor.execute("""
INSERT INTO users (name, email, password, phone, role, status, is_approved, affiliate_code, city, country, address, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
""", (
    'يوسف سماتي (Youssef S.)', 'customer@store.com',
    '$2y$12$lZ6CWaALA8P2lq7f1DZPwuXtgvqBoZIuQSnsCfJTe7XUqfWajgvgi', # password123
    '+213 550 98 76 54', 'customer', 'active', 1, 'YOUSSEF10', 'الجزائر العاصمة', 'Algeria', 'حي الموز، باب الزوار، الجزائر', now, now
))

# 3. Currencies
cursor.executemany("""
INSERT INTO currencies (code, name_ar, name_en, symbol_ar, symbol_en, exchange_rate, is_default, status, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
""", [
    ('DZD', 'دينار جزائري', 'Algerian Dinar', 'د.ج', 'DA', 1.0, 1, 'active', now, now),
    ('USD', 'دولار أمريكي', 'US Dollar', '$', '$', 0.0074, 0, 'active', now, now),
    ('EUR', 'يورو أوروبي', 'Euro', '€', '€', 0.0068, 0, 'active', now, now),
    ('SAR', 'ريال سعودي', 'Saudi Riyal', 'ر.س', 'SAR', 0.0278, 0, 'active', now, now)
])

# 4. Languages
cursor.executemany("""
INSERT INTO languages (code, name, direction, flag, is_default, status, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)
""", [
    ('ar', 'العربية', 'rtl', 'dz', 1, 'active', now, now),
    ('en', 'English', 'ltr', 'us', 0, 'active', now, now)
])

# 5. Categories
categories_data = [
    (1, None, 'ألعاب الكمبيوتر والكونسول (PC & Console Games)', 'PC & Console Games', 'pc-console-games',
     'أقوى ألعاب Steam، PlayStation و Xbox بحسابات أصلية ومفاتيح تفعيل رسمية.',
     'Top PC, PlayStation and Xbox original games with full access & instant delivery.',
     'insta_27.png', 'device-phone-mobile', 1, 1, 'active', now, now),
    
    (2, None, 'بكجات وحسابات ستيم (Steam Mega Packs)', 'Steam Mega Packs', 'steam-mega-packs',
     'أضخم مكتبات ألعاب Steam أوفلاين وأونلاين تضم آلاف الألعاب الحصرية بأسعار تبدأ من 500 DA.',
     'Massive Steam offline & online libraries with thousands of AAA games starting from 500 DA.',
     'insta_20.png', 'trophy', 2, 1, 'active', now, now),
    
    (3, None, 'سلسلة EA SPORTS FC (FC 27 / FIFA)', 'EA SPORTS FC 27 Series', 'ea-sports-fc',
     'جميع نسخ EA Sports FC 27 للكمبيوتر و PS4 و PS5 (عادية وألتمت) بحسابات أصلية.',
     'All EA Sports FC 27 editions for PC, PS4 and PS5 with early access and Ultimate Team perks.',
     'insta_21.png', 'sparkles', 3, 1, 'active', now, now),
    
    (4, None, 'ماين كرافت الأصلية (Minecraft Official)', 'Minecraft Official Accounts', 'minecraft',
     'حسابات ماين كرافت Java & Bedrock أصلية للكمبيوتر والجوال مع دعم Hypixel.',
     'Official Minecraft Java & Bedrock accounts for PC & iOS with full access and Hypixel support.',
     'insta_11.png', 'cube', 4, 1, 'active', now, now),
    
    (5, None, 'اشتراكات البرامج والترفيه (Software & Subscriptions)', 'Software & Subscriptions', 'subscriptions',
     'اشتراكات Adobe Creative Cloud، Xbox Game Pass، Netflix 4K، و Spotify Premium.',
     'Adobe Creative Cloud 12M, Xbox Game Pass Ultimate, Netflix 4K and Spotify Premium.',
     'insta_05.png', 'sparkles', 5, 1, 'active', now, now),
    
    (6, None, 'بطاقات الشحن والجيفت كارد (Gift Cards & Wallet)', 'Gift Cards & Wallet', 'gift-cards',
     'بطاقات شحن رصيد ستيم Steam، بلايستيشن ستور، وبطاقات إكس بوكس.',
     'Steam Wallet codes, PlayStation Network and Xbox digital gift cards.',
     'steam_card.jpg', 'shopping-bag', 6, 1, 'active', now, now)
]
cursor.executemany("""
INSERT INTO categories (id, parent_id, name_ar, name_en, slug, description_ar, description_en, image, icon, sort_order, is_featured, status, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
""", categories_data)

# 6. Brands
brands_data = [
    (1, 'روكستار جيمز (Rockstar Games)', 'Rockstar Games', 'rockstar-games', 'insta_27.png', 'ألعاب روكستار العالمية GTA و RDR', 'Rockstar Games creator of GTA & RDR', 1, 'active', now, now),
    (2, 'ستيم (Steam / Valve)', 'Steam / Valve', 'steam', 'insta_04.png', 'منصة الألعاب الأكبر عالمياً Valve Steam', 'Steam by Valve Corporation', 1, 'active', now, now),
    (3, 'إكس بوكس (Xbox / Microsoft)', 'Xbox', 'xbox', 'insta_30.png', 'منصة اشتراكات وألعاب إكس بوكس مايكروسوفت', 'Microsoft Xbox gaming and subscriptions', 1, 'active', now, now),
    (4, 'بلايستيشن (PlayStation / Sony)', 'PlayStation', 'playstation', 'ps_plus.jpg', 'منصة بلايستيشن سوني العالمية', 'Sony PlayStation games and services', 1, 'active', now, now),
    (5, 'أدوبي (Adobe)', 'Adobe', 'adobe', 'insta_05.png', 'برامج التصميم والمونتاج الاحترافية', 'Adobe Creative software suite', 1, 'active', now, now),
    (6, 'إي أيه سبورتس (EA Sports)', 'EA Sports', 'ea-sports', 'insta_21.png', 'سلسلة ألعاب FC 27 وكرة القدم العالمية', 'EA Sports football & sports games', 1, 'active', now, now),
    (7, 'موجانج (Mojang / Minecraft)', 'Mojang', 'mojang', 'insta_11.png', 'مطور لعبة ماين كرافت العالمية', 'Mojang Studios creator of Minecraft', 1, 'active', now, now),
    (8, 'نتفلكس (Netflix)', 'Netflix', 'netflix', 'netflix.jpg', 'خدمة البث الترفيهي والأفلام والمسلسلات', 'Global entertainment streaming platform', 1, 'active', now, now),
    (9, 'سبوتيفاي (Spotify)', 'Spotify', 'spotify', 'spotify.jpg', 'منصة الموسيقى والبودكاست الأولى', 'Global music and audio streaming', 1, 'active', now, now)
]
cursor.executemany("""
INSERT INTO brands (id, name_ar, name_en, slug, logo, description_ar, description_en, is_featured, status, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
""", brands_data)

# 7. Products
products_data = [
    # 1: PROMO GTA 6 - PS5
    (1, 1, 1, 'PROMO Grand Theft Auto VI (GTA 6) - تفعيل رسمي PS5', 'PROMO GTA 6 - PlayStation 5 Official Activation',
     'promo-gta-6-playstation-5', 'ALPHA-GTA6-PS5', 'عرض برومو حصري: احجز GTA 6 لجهاز PS5 بتفعيل رسمي على حسابك وضمان كامل 100%.',
     'Exclusive Promo: Grand Theft Auto VI for PS5 with official account activation and full warranty.',
     'عش تجربة الجيل الجديد الأسطورية مع GTA VI على PlayStation 5. عالم مفتوح ضخم، جرافيك غير مسبوق، طور أونلاين متطور، وتفعيل رسمي ومباشر على حسابك مع دعم فني 24/7.',
     'Next-gen GTA VI experience on PlayStation 5 with official activation on your personal account.',
     13750.00, 16500.00, 11000.00, 40, 5, 'insta_27.png', None, 1, 1, 1, 13750.00, 6400, 189, 'active', now, now),

    # 2: GTA 6 PC Pre-Order
    (2, 1, 1, 'Grand Theft Auto VI (GTA 6) - طلب مسبق رسمي PC / Steam', 'Grand Theft Auto VI (GTA 6) - Official PC Pre-Order',
     'grand-theft-auto-vi-gta-6-pc', 'ALPHA-GTA6-PC', 'الطلب المسبق الرسمي للعبة الأضخم في التاريخ GTA 6 للكمبيوتر مع مكافآت حصرية.',
     'Official PC pre-order for GTA VI with pre-order perks and instant delivery upon release.',
     'حساب أصلي للعبة GTA 6 للكمبيوتر على منصة Steam / Rockstar مع ضمان مدى الحياة ودعم فني على مدار الساعة.',
     'Complete PC edition of Grand Theft Auto VI with lifetime warranty and 24/7 support.',
     13900.00, 16500.00, 11000.00, 50, 5, 'gta6.png', None, 1, 0, 0, None, 4500, 140, 'active', now, now),

    # 3: Red Dead Redemption 2
    (3, 1, 1, 'Red Dead Redemption 2 - النسخة الكاملة + أونلاين', 'Red Dead Redemption 2 - Full Story & Online',
     'red-dead-redemption-2', 'ALPHA-RDR2-FULL', 'التحفة الفنية من روكستار: قصة آرثر مورغان كاملة مع Red Dead Online وضمان كامل.',
     'The complete masterpiece by Rockstar Games with Story Mode & Online.',
     'حساب أصلي كامل الصلاحيات Full Access مع إمكانية تغيير كافة بيانات الحساب وضمان ممتد.',
     'Original Full Access account with email change capability and full guarantee.',
     3400.00, 4800.00, 2500.00, 45, 5, 'rdr2.png', None, 1, 0, 0, None, 3800, 95, 'active', now, now),

    # 4: Ghost Recon Breakpoint ONLINE
    (4, 1, 2, "Tom Clancy's Ghost Recon Breakpoint ONLINE (Steam Brazil)", 'Ghost Recon Breakpoint ONLINE Steam Brazil',
     'ghost-recon-breakpoint-online-steam', 'ALPHA-GHOST-RECON', 'تجربة أونلاين كاملة: اكتشف جزيرة أورورا وواجه أخطر التحديات مع أصدقائك بـ 1800 DA فقط.',
     'Full online co-op tactical shooter on Steam Brazil region for only 1800 DA.',
     'اللعب أونلاين بدون أي قيود، حساب كامل Full Access مع تملك الحساب 100%، حساب ستيم أصلي وليست مشاركة عائلة، جاهز للعب مباشرة مع ضمان كامل على الحساب.',
     'Full access original Steam account ready to play without restrictions with lifetime warranty.',
     1800.00, 3200.00, 1200.00, 30, 5, 'insta_02.png', None, 1, 1, 0, None, 2900, 84, 'active', now, now),

    # 5: Backrooms Escape Together ONLINE
    (5, 1, 2, 'Backrooms: Escape Together ONLINE - حساب Steam أصلي أونلاين', 'Backrooms: Escape Together ONLINE Steam Account',
     'backrooms-escape-together-online', 'ALPHA-BACKROOMS', 'لعبة الرعب الجماعية: العب مع أصدقائك أونلاين في عالم الباكرومز المخيف بسعر 1000 DA فقط.',
     'Co-op multiplayer horror game: play online with friends for only 1000 DA.',
     'مغامرة رعب وتعاون في عالم الـ Backrooms المخيف. ابحث عن المخرج واهرب مع أصدقائك! متوافق مع PC، تسليم فوري وتلقائي، وصول كامل للحساب Full Access، ضمان كامل وتحديثات مستمرة.',
     'PC Steam original multiplayer account with instant delivery and full warranty.',
     1000.00, 1500.00, 600.00, 60, 5, 'insta_03.png', None, 1, 1, 0, None, 5200, 160, 'active', now, now),

    # 6: RUST Steam Online
    (6, 1, 2, 'RUST - Steam Online Account (حساب ستيم أونلاين كامل جاهز للعب)', 'RUST - Steam Online Account Full Access',
     'rust-steam-online-account', 'ALPHA-RUST-ONLINE', 'حساب ستيم أصلي للعبة روست RUST للعب أونلاين على كافة السيرفرات الرسمية والمعدلة بـ 2800 DA.',
     'Full access original Steam account for RUST with official server access for 2800 DA.',
     'Steam Online، Full Online Access، حساب رسمي 100%، تسليم فوري بعد الدفع، ضمان معتمد، جاهز للعب مباشرة ودعم فني متواصل 24/7.',
     'Full online multiplayer Steam account ready to play with 24/7 support.',
     2800.00, 4500.00, 2000.00, 25, 5, 'insta_14.png', None, 1, 0, 0, None, 3400, 72, 'active', now, now),

    # 7: Forza Horizon 6 Ultimate
    (7, 1, 3, 'Forza Horizon 6 - النسخة النهائية Ultimate VIP', 'Forza Horizon 6 - Ultimate VIP Edition',
     'forza-horizon-6-ultimate', 'ALPHA-FORZA6-ULT', 'النسخة الأقوى لعشاق السيارات: تشمل كافة التوسعات ومواسم السيارات وباقة الـ VIP.',
     'The ultimate racing experience with all car passes and VIP bonuses.',
     'تتضمن حزمة السيارات الكاملة Car Pass، وتوسعات الخريطة، وحساب مفعل رسمياً مع إمكانية اللعب أونلاين على PC و Xbox.',
     'Includes full car pass, VIP membership and lifetime activation.',
     11500.00, 14000.00, 9000.00, 30, 5, 'forza_horizon_6.png', None, 0, 0, 0, None, 2100, 45, 'active', now, now),

    # 8: EA SPORTS FC 27 PC Pre-Order
    (8, 3, 6, 'EA SPORTS FC 27 (FIFA 27) - احجز نسختك الآن للكمبيوتر PC', 'EA SPORTS FC 27 - Pre-Order PC Edition',
     'ea-sports-fc-27-pre-order-pc', 'ALPHA-FC27-PC-PRE', 'الطلب المسبق الرسمي للعبة FC 27: العب قبل الجميع مع محتوى حصري ونقاط FC وطور Ultimate Team.',
     'Official PC pre-order for FC 27 with early access and Ultimate Team perks.',
     'احجز نسختك للكمبيوتر، تتضمن: اللعب المبكر قبل الجميع، محتوى حصري للطلب المسبق، تجربة UT متكاملة، طور مسيرتك، وحركات واقعية HyperMotion V. متوفرة بنسخة عادية وألتمت وألتمت بلس.',
     'Pre-order FC 27 PC version with early access and exclusive FUT points.',
     13750.00, 15500.00, 10000.00, 50, 5, 'insta_21.png', None, 1, 1, 0, None, 7800, 215, 'active', now, now),

    # 9: EA SPORTS FC 27 Steam Preloaded
    (9, 3, 6, 'EA SPORTS FC 27 - حساب Steam رسمي محمل مسبقاً أونلاين', 'EA SPORTS FC 27 - Steam Official Account',
     'ea-sports-fc-27-steam-official', 'ALPHA-FC27-STEAM', 'حساب ستيم رسمي محمل مسبقاً باللعبة ويدعم الأونلاين كامل بـ 12000 DA (48$).',
     'Official Steam account preloaded with FC 27 with full online multiplayer support.',
     'حساب ستيم رسمي وليس مفتاح تفعيل، اللعبة محملة مسبقاً على الحساب وتدعم طور الأونلاين بالكامل. ستتلقى بيانات الحساب فوراً (اسم المستخدم، كلمة المرور، البريد، وكود Steam Guard) مع ضمان كامل ودعم 24/7.',
     'Original Steam account with full online support and immediate credentials delivery.',
     12000.00, 14500.00, 9500.00, 40, 5, 'insta_22.png', None, 1, 0, 0, None, 4600, 112, 'active', now, now),

    # 10: FC 27 Ultimate Edition PS4
    (10, 3, 6, 'EA SPORTS FC 27 - نسخة الألتمت لجهاز PS4 (بحسابك الأساسي)', 'EA SPORTS FC 27 Ultimate Edition PS4',
     'ea-sports-fc-27-ultimate-ps4', 'ALPHA-FC27-ULT-PS4', 'نسخة الألتمت لجهاز PS4: العبها بحسابك الأساسي مع تحسينات Ultimate Team بـ 9000 DA (36$).',
     'Ultimate Edition for PS4 playable on your primary account for 9000 DA.',
     'ادخل الملعب قبل الجميع والعبها بحسابك الأساسي Play It with Your Primary Account. أطوار لعبة متعددة ومطورة، محسن Ultimate Team، رسومات واقعية، تفعيل رسمي 100% وتسليم فوري.',
     'Play FC 27 Ultimate Edition on your main PS4 account with lifetime warranty.',
     9000.00, 12000.00, 7000.00, 35, 5, 'insta_23.png', None, 0, 0, 0, None, 3200, 68, 'active', now, now),

    # 11: FC 27 Ultimate Edition PS5
    (11, 3, 6, 'EA SPORTS FC 27 - نسخة الألتمت لجهاز PS5 (نقاط FC + وصول مبكر)', 'EA SPORTS FC 27 Ultimate Edition PS5',
     'ea-sports-fc-27-ultimate-ps5', 'ALPHA-FC27-ULT-PS5', 'نسخة الألتمت لجهاز PS5: مزايا UT حصرية، وصول مبكر، حزم لاعبين ونقاط FC إضافية بـ 16250 DA (65$).',
     'Ultimate Edition for PS5 with FC points, early access and player packs for 16250 DA.',
     'تجربة كرة قدم واقعية على الجيل الجديد برسومات مذهلة وأداء فائق على PS5. تتضمن نقاط FC إضافية وحزم لاعبين نادرة ووصول مبكر قبل الجميع.',
     'Next-gen FC 27 Ultimate Edition with exclusive FC Points on PS5.',
     16250.00, 19500.00, 13000.00, 30, 5, 'insta_24.png', None, 1, 0, 0, None, 5100, 94, 'active', now, now),

    # 12: FC 27 Standard Edition PS4
    (12, 3, 6, 'EA SPORTS FC 27 - Standard Edition PS4 (بحسابك الأساسي)', 'EA SPORTS FC 27 Standard Edition PS4',
     'ea-sports-fc-27-standard-ps4', 'ALPHA-FC27-STD-PS4', 'النسخة القياسية للعبة FC 27 على PS4: تلعبها بحسابك الأساسي بـ 7500 DA (32$).',
     'Standard Edition for PS4 playable on your primary account for 7500 DA.',
     'منتج أصلي 100%، تفعيل سريع وضمان كامل، تلعب بحسابك الأساسي مع أحدث الانتقالات والفرق والرسومات المحسنة.',
     'FC 27 Standard edition for PS4 with full online multiplayer on your main account.',
     7500.00, 9500.00, 5500.00, 45, 5, 'insta_25.png', None, 0, 0, 0, None, 2900, 75, 'active', now, now),

    # 13: FC 27 Standard Edition PS5
    (13, 3, 6, 'EA SPORTS FC 27 - Standard Edition PS5 (بحسابك الأساسي)', 'EA SPORTS FC 27 Standard Edition PS5',
     'ea-sports-fc-27-standard-ps5', 'ALPHA-FC27-STD-PS5', 'النسخة القياسية على PS5: جرافيك مذهل وتكتيكات ذكية تلعبها بحسابك الأساسي بـ 9000 DA (36$).',
     'Standard Edition for PS5 playable on your primary account for 9000 DA.',
     'التجربة الأكثر واقعية في تاريخ السلسلة: أسلوب لعب أكثر واقعية، Ultimate Team محسن بالكامل، تكتيكات ذكية وأداء متطور على PS5. تلعب بحسابك الأساسي وتفعيل سريع خلال دقائق.',
     'Next-gen standard edition for PS5 with instant activation on your personal account.',
     9000.00, 11500.00, 6800.00, 40, 5, 'insta_26.png', None, 0, 0, 0, None, 3700, 89, 'active', now, now),

    # 14: Steam Any Game Offline (500 DA)
    (14, 2, 2, 'أي لعبة في ستيم تريدها (Steam Account OFFLINE) - سعر 500 DA فقط!', 'Any Steam Game You Want - OFFLINE Account',
     'any-steam-game-offline-account', 'ALPHA-STEAM-500', 'اختر أي لعبة في ستيم (Elden Ring, RDR2, Wukong, Cyberpunk, RE4, God of War...) وادفع 500 DA فقط!',
     'Choose any game on Steam for only 500 DA! Instant delivery, no VPN needed.',
     'اختر أي لعبة تريدها على الإطلاق وادفع 500 DA فقط! تسليم فوري بعد الدفع، حساب أوفلاين 100% آمن، ألعاب أصلية 100%، لا يحتاج برامج أو VPN، دعم فني متواصل 24/7 وضمان كامل على الحساب.',
     'Get any single AAA game of your choice on Steam offline for 500 DA with lifetime access.',
     500.00, 2000.00, 200.00, 100, 10, 'insta_04.png', None, 1, 1, 0, None, 9500, 420, 'active', now, now),

    # 15: Mega Steam Offline +45,000 Games
    (15, 2, 2, 'حساب ستيم أوفلاين أسطوري يحتوي على أكثر من +45,000 PREMIUM GAMES', 'Mega Steam Account +45,000 Premium Games',
     'mega-steam-account-45000-games', 'ALPHA-STEAM-45K', 'أضخم مكتبة ألعاب في العالم: أكثر من 45 ألف لعبة مدفوعة بحساب ستيم رسمي واحد بـ 9500 DA (37$).',
     'The largest game library with 45,000+ premium games on a single Steam account for 9500 DA.',
     'حساب ستيم رسمي 100% أوفلاين يضم كل الألعاب المدفوعة العالمية (Wukong, RDR2, GTA V, Elden Ring, God of War, Cyberpunk, Subnautica 2, Spider-Man...). تحديثات مستمرة، سهولة الاستخدام، خصوصية تامة وضمان 100%.',
     '45,000+ game mega Steam account with all genres, lifetime access and full warranty.',
     9500.00, 18000.00, 6500.00, 30, 5, 'insta_20.png', None, 1, 0, 0, None, 6100, 130, 'active', now, now),

    # 16: Mega Steam Offline +17,000 PC Games
    (16, 2, 2, 'حساب ستيم أوفلاين أسطوري +17,000 لعبة PC مدفوعة (مع هدية داخل الحساب)', 'Mega Steam Account +17,000 PC Games with Gift',
     'mega-steam-account-17000-games', 'ALPHA-STEAM-17K-PVT', 'أكبر مكتبة ألعاب في حساب واحد خاص غير مشترك +17000 لعبة PC مع هدية حصرية بـ 6000 DA.',
     'Private non-shared Steam account with 17,000+ PC games and bonus gift for 6000 DA.',
     'حساب غير مشترك 100% آمن، بدون كراكات أو اختراق، حساب شخصي خاص بك، تحديثات مستمرة للألعاب، أمان كامل بدون حظر، مع هدية لعبة أو محتوى حصري داخل الحساب.',
     'Private Steam account with 17k+ games, zero cracks, lifetime security and bonus gifts.',
     6000.00, 12000.00, 4000.00, 20, 5, 'insta_06.png', None, 1, 0, 0, None, 4300, 88, 'active', now, now),

    # 17: Pack 17,000 Steam Shared
    (17, 2, 2, 'بكج الأحلام +17,000 لعبة PC عبر STEAM (حسابات مشتركة - سعر ناري 5000 DA)', 'Dream Pack +17,000 PC Games Steam Shared',
     'dream-pack-17000-games-steam-shared', 'ALPHA-STEAM-17K-SHR', 'بكج الأحلام: يغطي كل أنواع الألعاب (قصة، تختيم، رعب، محاكاة، أكشن) بـ 5000 DA فقط.',
     'Dream collection of 17,000 PC games across all genres on Steam for only 5000 DA.',
     'مجموعة مبنية على حسابات Steam مدفوعة، وكأنك فاتح متجر ألعاب ستيم كامل جاهز على حساباتك، بدون مواقع مشبوهة وبدون كراكات مع تسليم فوري وضمان مستمر.',
     'Massive 17k Steam game library covering all genres with instant delivery.',
     5000.00, 8500.00, 3200.00, 35, 5, 'insta_17.png', None, 0, 0, 0, None, 3600, 78, 'active', now, now),

    # 18: Pack +4,400 Steam Games
    (18, 2, 2, 'بكج الألعاب الأكبر بالتاريخ! +4,400 لعبة PC عبر Steam (عرض جزائري 2800 DA)', 'Largest Steam Pack +4,400 PC Games',
     'largest-steam-pack-4400-games', 'ALPHA-STEAM-4400', 'عرض جزائري سعر ناري: أكثر من 4400 لعبة PC مع تحديثات وإضافات مستمرة بدون أي رسوم بـ 2800 DA.',
     'Algerian special deal: 4,400+ Steam games with free continuous updates for 2800 DA.',
     'يشمل أقوى العناوين: Elden Ring, RDR2, Cyberpunk 2077, God of War, Hogwarts Legacy, Resident Evil 4, Forza 5, The Last of Us. أمان وخصوصية 100% وضمان كامل على الحسابات.',
     'Over 4,400 top PC games on Steam with full warranty and 100% privacy.',
     2800.00, 5000.00, 1800.00, 40, 5, 'insta_18.png', None, 1, 0, 0, None, 4100, 95, 'active', now, now),

    # 19: Steam Pack +500 Games (2500 DA)
    (19, 2, 2, 'حساب +500 لعبة STEAM للكمبيوتر (نخبة الألعاب في بكج واحد + هدية)', 'Steam PC +500 Games Elite Pack with Gift',
     'steam-pc-500-games-elite-pack', 'ALPHA-STEAM-500-ELT', 'نخبة الألعاب في بكج واحد: أكثر من 500 لعبة مدفوعة 100% مع هدية حصرية داخل الحساب بـ 2500 DA.',
     'Elite 500+ game Steam pack with bonus gift and full access for 2500 DA.',
     'مكتبة ضخمة جاهزة للعب تضم أفضل الألعاب العالمية، التحميل من Steam نفسه، آمن 100% بدون حظر، تسليم فوري ودعم فني متواصل.',
     '500+ AAA games package on Steam with immediate delivery and warranty.',
     2500.00, 4500.00, 1600.00, 30, 5, 'insta_07.png', None, 0, 0, 0, None, 2800, 62, 'active', now, now),

    # 20: Steam Pack 500 Games (1500 DA Promo)
    (20, 2, 2, 'بكج 500 لعبة STEAM PC (يغنيك عن شراء الألعاب - سعر ناري 1500 DA)', 'Steam 500 Games Package Promo 1500 DA',
     'steam-500-games-package-promo', 'ALPHA-STEAM-500-PRM', 'بكج يغنيك عن شراء الألعاب: 500 لعبة ستيم جاهزة على حساباتك بـ 1500 DA فقط.',
     '500 games package on Steam accounts ready to download for only 1500 DA.',
     'تختيم، رعب، محاكاة، أكشن، مغامرات، سباقات، رياضة. كأنك فاتح متجر ألعاب كامل بدون مواقع مشبوهة ولا كراكات نهائياً.',
     'All game genres in one 500-game package for 1500 DA.',
     1500.00, 3500.00, 900.00, 50, 5, 'insta_16.png', None, 1, 1, 0, None, 5800, 175, 'active', now, now),

    # 21: Steam Offline Account 1500+ Games (Private)
    (21, 2, 2, 'حساب ستيم OFFLINE خاص أكثر من 1500 لعبة (يشمل Forza 4, 5, 6 و Wukong)', 'Steam Offline Private Account 1500+ Games',
     'steam-offline-private-1500-games', 'ALPHA-STEAM-1500-PVT', 'حساب خاص بالكامل أوفلاين يضم أكثر من 1500 لعبة AAA حديثة بـ 1900 DA فقط.',
     'Private offline account with 1500+ AAA games including Forza 4, 5, 6 and Wukong for 1900 DA.',
     'حساب خاص 100% آمن بدون مشاركة بدون تغيير، كل الألعاب متاحة، تحديثات مستمرة، ألعاب AAA حديثة وعالم مفتوح ومغامرات وأكشن وسباقات.',
     'Private offline Steam account with 1500+ games and full warranty.',
     1900.00, 3800.00, 1100.00, 35, 5, 'insta_28.png', None, 0, 0, 0, None, 3100, 70, 'active', now, now),

    # 22: Steam Account 650+ Games (Full Info Change)
    (22, 2, 2, 'حساب ستيم خاص أكثر من 650 لعبة (إمكانية تغيير كامل المعلومات)', 'Steam Account 650+ Games Full Access Editable',
     'steam-account-650-games-full-access', 'ALPHA-STEAM-650-FA', 'حساب خاص مضمون 100% يضم 650+ لعبة مع إمكانية تغيير الإيميل وكلمة السر بـ 1800 DA.',
     '100% guaranteed private account with 650+ games and full credentials change support.',
     'حساب خاص يمكنك تغيير كامل المعلومات، آمن 100%، تسليم فوري، مكتبة ضخمة تضم أحدث الألعاب والإصدارات مع تحميل وتثبيت من حسابك الشخصي.',
     'Editable personal Steam account with 650+ games and lifetime support.',
     1800.00, 3500.00, 1000.00, 30, 5, 'insta_29.png', None, 0, 0, 0, None, 2900, 64, 'active', now, now),

    # 23: Steam Offline Account 120+ Games
    (23, 2, 2, 'حساب ستيم أوفلاين +120 لعبة مدفوعة (مع هدية لعبة عشوائية + DLCs)', 'Steam Offline Account +120 Games with DLCs & Gift',
     'steam-offline-account-120-games', 'ALPHA-STEAM-120', 'أكثر من 120 لعبة من أقوى الألعاب العالمية مع لعبة عشوائية إضافية ومحتويات حصرية بـ 1500 DA.',
     '120+ top Steam games offline with bonus random game and DLCs for 1500 DA.',
     'يشمل Red Dead 2, Elden Ring, God of War, Cyberpunk 2077, Hogwarts Legacy, Forza 5, Spider-Man, Ghost of Tsushima, The Last of Us. استخدام مدى الحياة وتسليم فوري.',
     '120+ top tier games offline package with lifetime access.',
     1500.00, 2800.00, 900.00, 40, 5, 'insta_08.png', None, 0, 0, 0, None, 3200, 80, 'active', now, now),

    # 24: Steam Offline Account 80+ Games
    (24, 2, 2, 'حساب ستيم أوفلاين +80 لعبة مدفوعة (يشمل Wukong و Ghost of Tsushima)', 'Steam Offline Account 80+ Games Wukong & Ghost',
     'steam-offline-account-80-games', 'ALPHA-STEAM-80', 'أكثر من 80 لعبة مدفوعة تشمل Black Myth Wukong و Resident Evil 4 بـ 1000 DA فقط.',
     '80+ premium Steam games including Black Myth Wukong and RE4 for 1000 DA.',
     'حساب Steam أوفلاين رسمي، جاهز للتحميل واللعب فوراً، تسليم سريع وضمان كامل مع دعم فني 24/7.',
     '80+ games Steam offline account ready to play with 24/7 support.',
     1000.00, 2200.00, 600.00, 50, 5, 'insta_10.png', None, 1, 0, 0, None, 4100, 105, 'active', now, now),

    # 25: Minecraft Java + Bedrock LICENSE Lifetime
    (25, 4, 7, 'Minecraft Java + Bedrock Edition [LICENSE] - حساب مدى الحياة رسمي', 'Minecraft Java + Bedrock LICENSE Lifetime Account',
     'minecraft-java-bedrock-license-lifetime', 'ALPHA-MC-LIFETIME', 'حساب مرخص رسمي 100% مدى الحياة: إيميل ووصول كامل Full Access ودعم كامل لسيرفر Hypixel بـ 3500 DA.',
     'Official lifetime Minecraft Java + Bedrock license with full access and Hypixel support for 3500 DA.',
     'حساب رسمي مرخص يحتوي على النسختين Java Edition + Bedrock Edition. وصول كامل إلى الحساب والإيميل، إمكانية تغيير جميع البيانات (البريد، كلمة المرور، اسم اللاعب، وربط رقم الهاتف)، لعب أونلاين على جميع السيرفرات بدون حظر على Hypixel مع تسليم فوري بعد الشراء.',
     '100% official Minecraft license with full account transfer and no ban on Hypixel.',
     3500.00, 5500.00, 2200.00, 40, 5, 'insta_11.png', None, 1, 1, 0, None, 6800, 195, 'active', now, now),

    # 26: Minecraft Microsoft Store Activation (700 DA)
    (26, 4, 7, 'Minecraft Bedrock + Java Edition (تفعيل رسمي على حساب Microsoft الخاص بك)', 'Minecraft Bedrock + Java Microsoft Store Activation',
     'minecraft-bedrock-java-microsoft-store', 'ALPHA-MC-MS-700', 'شراء رسمي يضاف إلى حسابك Microsoft مباشرة: لعب أونلاين وتفعيل سريع بـ 700 DA فقط.',
     'Official purchase activated directly to your personal Microsoft account for 700 DA.',
     'شراء رسمي من Microsoft Store، يضاف إلى حسابك الشخصي To Your Account، متوافق مع PC، يدعم اللعب أونلاين وتفعيل سريع خلال دقائق مع ضمان كامل.',
     'Original Microsoft Store Minecraft activation on your own personal account.',
     700.00, 1500.00, 400.00, 60, 5, 'insta_12.png', None, 1, 0, 0, None, 5400, 145, 'active', now, now),

    # 27: Minecraft iOS / iPadOS
    (27, 4, 7, 'Minecraft iOS / iPadOS (نسخة أصلية للأيفون والأيباد على حسابك)', 'Minecraft iOS / iPadOS Official App',
     'minecraft-ios-ipados-official', 'ALPHA-MC-IOS', 'ماين كرافت الأصلية لأجهزة iPhone و iPad: تفعيل رسمي على حسابك بدون تسجيل دخول بـ 1300 DA.',
     'Official Minecraft for iPhone and iPad on your personal Apple account for 1300 DA.',
     'رسمي 100% بدون تسجيل دخول، يضاف على حسابك الشخصي، الحساب خاص بك ويمكنك تغيير جميع المعلومات مع تفعيل تلقائي وسريع وضمان كامل.',
     'Official Minecraft iOS/iPadOS game with full guarantee and automatic issue.',
     1300.00, 2200.00, 800.00, 30, 5, 'insta_13.png', None, 0, 0, 0, None, 3900, 82, 'active', now, now),

    # 28: Adobe Creative Cloud (All Apps) 12 Months
    (28, 5, 5, 'Adobe Creative Cloud (All Apps) + Acrobat Pro DC - اشتراك 12 شهراً', 'Adobe Creative Cloud All Apps + Acrobat 12M',
     'adobe-creative-cloud-all-apps-12m', 'ALPHA-ADOBE-12M', 'تخفيض 70%: كل تطبيقات أدوبي الاحترافية (+20 تطبيق) في اشتراك رسمي لمدة عام بـ 1650 DA.',
     '70% OFF: All 20+ Adobe Apps including Photoshop, Premiere, Illustrator for 12 months at 1650 DA.',
     'اشتراك رسمي لمدة عام كامل (12 شهراً) يشمل أكثر من 20 تطبيق احترافي (Photoshop, Illustrator, Premiere Pro, After Effects, InDesign, Audition, Lightroom Classic, Acrobat Pro DC...). تحميل مباشر من موقع أدوبي الرسمي، يعمل على ويندوز وماك مع رموز تلقائية واستلام فوري وضمان كامل.',
     'Official 1-year Adobe Creative Cloud All Apps license with direct Adobe download and full warranty.',
     1650.00, 5500.00, 1000.00, 50, 5, 'insta_05.png', None, 1, 1, 1, 1650.00, 7200, 230, 'active', now, now),

    # 29: Xbox Game Pass Ultimate Console
    (29, 5, 3, 'اشتراك إكس بوكس جيم باس Xbox Game Pass Ultimate (يشمل EA Play و Ubisoft+)', 'Xbox Game Pass Ultimate Console Membership',
     'xbox-game-pass-ultimate-console', 'ALPHA-XBOX-GPU-CON', 'مكتبة ضخمة تضم أكثر من 450 لعبة على Xbox One / Series S|X مع EA Play و Ubisoft+ أونلاين بـ 4200 DA.',
     'Access 450+ console games including EA Play and Ubisoft+ with online multiplayer.',
     'اشتراك Game Pass Ultimate خاص بـ Xbox One / Series X|S بنظام Switch / Home بأسعار منخفضة جداً. يشمل إمكانية اللعب أونلاين، مكتبة EA Play، مكتبة Ubisoft+، تسليم سريع وحسابات مضمونة 100%.',
     'Xbox Game Pass Ultimate console membership with 450+ titles, EA Play & full multiplayer.',
     4200.00, 6500.00, 2800.00, 40, 5, 'insta_30.png', None, 1, 0, 0, None, 4800, 110, 'active', now, now),

    # 30: Netflix Premium 4K
    (30, 5, 8, 'اشتراك نتفلكس بريميوم Netflix Premium 4K Ultra HD (ضمان كامل)', 'Netflix Premium 4K Ultra HD Membership',
     'netflix-premium-4k-ultra-hd', 'ALPHA-NETFLIX-4K', 'حساب Netflix رسمي بدقة 4K Ultra HD مع دعم كامل للشاشات الذكية والجوال بدون انقطاع.',
     'Official 4K Netflix Premium account with lifetime guarantee and multiple profiles.',
     'استمتع بآلاف الأفلام والمسلسلات الحصرية بأعلى دقة 4K مع صوت محيطي Dolby Atmos. الحساب رسمي ومفعل بدون انقطاع مع ضمان كامل طوال فترة الاشتراك.',
     'Netflix 4K Ultra HD subscription with zero interruptions and full warranty.',
     1200.00, 1800.00, 700.00, 50, 5, 'netflix.jpg', None, 1, 1, 0, None, 6500, 210, 'active', now, now),

    # 31: PlayStation Plus Deluxe / Extra
    (31, 5, 4, 'اشتراك بلايستيشن بلس PlayStation Plus Deluxe / Extra', 'PlayStation Plus Deluxe / Extra Membership',
     'playstation-plus-deluxe-extra', 'ALPHA-PS-PLUS', 'اشتراك PS Plus Deluxe يمنحك اللعب أونلاين مع مكتبة مئات ألعاب PS4 و PS5 الكلاسيكية والحديثة.',
     'PS Plus Deluxe tier with online multiplayer and 400+ blockbuster games catalog.',
     'تفعيل رسمي على جهازك PS4 / PS5 مع إمكانية اللعب بحسابك الشخصي الأساسي وضمان كامل طوال مدة الاشتراك.',
     'Official PlayStation Plus membership with full catalog access and cloud storage.',
     9500.00, 13500.00, 6800.00, 25, 5, 'ps_plus.jpg', None, 0, 0, 0, None, 3100, 55, 'active', now, now),

    # 32: Steam Wallet 50$
    (32, 6, 2, 'بطاقة شحن رصيد ستيم Steam Wallet Gift Card 50$', 'Steam Wallet 50$ Digital Gift Card',
     'steam-wallet-card-50-usd', 'ALPHA-STEAM-50USD', 'كود شحن رصيد Steam أصلي فئة 50 دولار صالح لكافة الحسابات العالمية مع شحن فوري.',
     'Official 50$ Steam digital wallet voucher code with instant delivery.',
     'كود رسمي 100% لشحن حسابك في Steam وشراء أي لعبة أو سكنات Counter-Strike و Dota 2 مباشرة.',
     'Instant redeemable 50$ digital code for Steam store and market.',
     7200.00, 8000.00, 6500.00, 100, 10, 'steam_card.jpg', None, 0, 0, 0, None, 2200, 48, 'active', now, now),

    # 33: Spotify Premium
    (33, 5, 9, 'اشتراك سبوتيفاي بريميوم Spotify Premium (بدون إعلانات وأوفلاين)', 'Spotify Premium Individual Membership',
     'spotify-premium-individual', 'ALPHA-SPOTIFY-PRM', 'استمع لملايين الأغاني والبودكاست بأعلى جودة صوت بدون أي إعلانات مع إمكانية التحميل أوفلاين.',
     'Ad-free Spotify Premium with offline downloads and high quality audio.',
     'ترقية رسمية لحسابك الشخصي على Spotify بدون الحاجة لتغيير الحساب أو فقدان قوائم التشغيل الخاصة بك، مع ضمان كامل.',
     'Personal account Spotify upgrade with offline playback and highest audio fidelity.',
     850.00, 1400.00, 500.00, 80, 5, 'spotify.jpg', None, 0, 0, 0, None, 2900, 70, 'active', now, now)
]

cursor.executemany("""
INSERT INTO products (
    id, category_id, brand_id, name_ar, name_en, slug, sku, short_description_ar, short_description_en,
    description_ar, description_en, price, compare_price, cost_price, stock_quantity, low_stock_threshold,
    primary_image, video_url, is_featured, is_best_seller, is_deal, deal_price, views_count, sales_count, status, created_at, updated_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
""", products_data)

# 8. Product Attributes
attributes_data = [
    (8, 'النسخة (Edition)', 'Edition', json.dumps(['Regular Version (13750 DA / 55$)', 'Ultimate Version (21250 DA / 85$)', 'Ultimate Plus Version (27250 DA / 109$)'], ensure_ascii=False), 0.0, now, now),
    (14, 'اللعبة المطلوبة (Game Title)', 'Game Title', json.dumps(['Elden Ring', 'Black Myth: Wukong', 'Red Dead Redemption 2', 'God of War Ragnarök', 'Hogwarts Legacy', 'Cyberpunk 2077', 'Spider-Man Remastered', 'Resident Evil 4 Remake', 'Forza Horizon 5', 'لعبة أخرى (اكتبها في الملاحظات)'], ensure_ascii=False), 0.0, now, now)
]
cursor.executemany("""
INSERT INTO product_attributes (product_id, name_ar, name_en, values_json, price_modifier, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?)
""", attributes_data)

# 9. Product Additional Images (Gallery)
images_data = [
    (1, 'insta_27.png', 'GTA 6 PS5 Promo Poster', 1, 1, now, now),
    (4, 'insta_02.png', 'Ghost Recon Breakpoint Steam Brazil', 1, 1, now, now),
    (5, 'insta_03.png', 'Backrooms Escape Together Steam Online', 1, 1, now, now),
    (6, 'insta_14.png', 'RUST Steam Online Account', 1, 1, now, now),
    (8, 'insta_21.png', 'FC 27 PC Editions Pre-Order', 1, 1, now, now),
    (9, 'insta_22.png', 'FC 27 Steam Account Preloaded', 1, 1, now, now),
    (10, 'insta_23.png', 'FC 27 Ultimate Edition PS4', 1, 1, now, now),
    (11, 'insta_24.png', 'FC 27 Ultimate Edition PS5', 1, 1, now, now),
    (12, 'insta_25.png', 'FC 27 Standard Edition PS4', 1, 1, now, now),
    (13, 'insta_26.png', 'FC 27 Standard Edition PS5', 1, 1, now, now),
    (14, 'insta_04.png', 'Any Steam Game 500 DA Promo', 1, 1, now, now),
    (15, 'insta_20.png', 'Mega Steam 45k Games Poster', 1, 1, now, now),
    (16, 'insta_06.png', 'Mega Steam 17k Games Private Poster', 1, 1, now, now),
    (17, 'insta_17.png', 'Dream Pack 17k Steam Shared Poster', 1, 1, now, now),
    (18, 'insta_18.png', 'Largest Steam Pack 4400 Games Poster', 1, 1, now, now),
    (19, 'insta_07.png', 'Steam PC 500 Games Elite Pack Poster', 1, 1, now, now),
    (20, 'insta_15.png', 'Steam 500 Games Promo Poster 1', 1, 0, now, now),
    (20, 'insta_16.png', 'Steam 500 Games Promo Poster 2', 2, 1, now, now),
    (21, 'insta_28.png', 'Steam Offline 1500 Games Poster', 1, 1, now, now),
    (22, 'insta_29.png', 'Steam Account 650 Games Editable Poster', 1, 1, now, now),
    (23, 'insta_08.png', 'Steam Offline 120 Games Poster 1', 1, 1, now, now),
    (23, 'insta_09.png', 'Steam Offline 120 Games Poster 2', 2, 0, now, now),
    (24, 'insta_10.png', 'Steam Offline 80 Games Poster', 1, 1, now, now),
    (25, 'insta_11.png', 'Minecraft Java Bedrock Lifetime License Poster', 1, 1, now, now),
    (26, 'insta_12.png', 'Minecraft Microsoft Store Activation Poster', 1, 1, now, now),
    (27, 'insta_13.png', 'Minecraft iOS iPadOS Poster', 1, 1, now, now),
    (28, 'insta_05.png', 'Adobe Creative Cloud 12M Poster', 1, 1, now, now),
    (29, 'insta_30.png', 'Xbox Game Pass Ultimate Console Poster', 1, 1, now, now)
]
cursor.executemany("""
INSERT INTO product_images (product_id, image_path, alt_text, sort_order, is_primary, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?)
""", images_data)

# 10. Authentic Customer Reviews (from Poster insta_01.png)
reviews_data = [
    (1, 2, 'يوسف س.', 'youssef@customer.dz', 5, 'تعامل ممتاز وسريع، الحساب وصلني شغال 100%، أنصح بالتعامل بقوة 🔥 (EA Sports)', 1, 1, now, now),
    (1, 2, 'أمين ب.', 'amine@customer.dz', 5, 'ثقة 100% ألعاب أصلية وسعر مناسب بالدينار والدعم متاح طول الوقت 👍', 1, 1, now, now),
    (8, 2, 'محمد ر.', 'mohamed@customer.dz', 5, 'أول مرة نتعامل معاكم والله ما راح تكون الأخيرة، خدمة سريعة ورائعة جداً ❤️', 1, 1, now, now),
    (5, 2, 'إسحاق ن.', 'ishak@customer.dz', 5, 'حساب Steam رهيب ولعبة Backrooms شغالة أونلاين مع صحابي بدون مشاكل، شكراً لكم 🔥', 1, 1, now, now),
    (14, 2, 'ريان خ.', 'rayan@customer.dz', 5, 'سعر مناسب جداً مقارنة بالمواقع وتسليم فوري والحساب عليه ضمان كامل، أنصح الجميع 👌', 1, 1, now, now),
    (28, 2, 'سليم ذ.', 'salim@customer.dz', 5, 'دعم فني سريع ومتعاونين ويجاوبون على كل الأسئلة ✅ تجربة ممتازة.', 1, 1, now, now)
]
cursor.executemany("""
INSERT INTO reviews (product_id, user_id, author_name, author_email, rating, comment, is_verified_purchase, is_approved, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
""", reviews_data)

# 11. Coupons
coupons_data = [
    ('ALPHA10', 'percentage', 10.0, 2000.0, 1500.0, 500, 84, now, future_60d, 'active', now, now),
    ('GAMER500', 'fixed', 500.0, 3000.0, 500.0, 200, 45, now, future_45d, 'active', now, now)
]
cursor.executemany("""
INSERT INTO coupons (code, type, value, min_order_amount, max_discount_amount, usage_limit, used_count, start_date, end_date, status, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
""", coupons_data)

# 12. Shipping Methods
shipping_data = [
    ('instant_whatsapp', 'تسليم فوري عبر واتساب WhatsApp (مجاناً)', 'Instant WhatsApp Delivery (Free)', 'يتم إرسال بيانات الحساب والتفعيل فوراً عبر رسالة WhatsApp خلال 5 دقائق.', 'Instant account credentials dispatch via WhatsApp.', 0.0, 0.0, 'active', 1, now, now),
    ('instant_email', 'إرسال مباشر عبر البريد الإلكتروني (مجاناً)', 'Instant Email Dispatch (Free)', 'يصلك إيميل يحتوي على كافة معلومات الدخول وخطوات التفعيل وضمان الحساب.', 'Instant account credentials delivered to your inbox.', 0.0, 0.0, 'active', 2, now, now)
]
cursor.executemany("""
INSERT INTO shipping_methods (code, name_ar, name_en, description_ar, description_en, cost, free_threshold, status, sort_order, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
""", shipping_data)

# 13. Settings
settings = {
    'site_name': 'Alpha Gaming Shop',
    'site_description': 'المتجر الجزائري الأول لشراء وتفعيل الألعاب الأصلية، حسابات Steam وبكجات الألعاب، واشتراكات Netflix و Game Pass و Adobe بأفضل الأسعار بالدينار الجزائري مع تسليم فوري وضمان كامل.',
    'site_phone': '+213 555 12 34 56',
    'site_email': 'contact@alphagamingshop.dz',
    'site_address': 'الجزائر العاصمة، الجزائر (Algeria)',
    'site_currency': 'DZD',
    'site_logo': 'uploads/logo.png',
    'site_favicon': 'uploads/favicon.png',
    'instagram_url': 'https://www.instagram.com/alpha_gaming_shop/',
    'whatsapp_number': '+213555123456',
    'payment_baridimob': '00799999000123456789 (MENOUER MOHAMED EL AMINE)',
    'payment_ccp': '12345678 Clé 90 (الجزائر)',
    'payment_paypal': 'active',
    'payment_cash_on_delivery': 'inactive',
}
for k, v in settings.items():
    cursor.execute("INSERT INTO settings (key, value, created_at, updated_at) VALUES (?, ?, ?, ?)", (k, v, now, now))

# 14. Theme Settings
theme_settings = {
    'primary_color': '#00d2ff',
    'secondary_color': '#00ff88',
    'accent_color': '#ffaa00',
    'dark_bg_color': '#0b0f19',
    'font_family': 'Cairo',
    'hero_banner_image': 'insta_19.png',
    'hero_title_ar': 'Alpha Gaming Shop - كل ما يحتاجه اللاعب في مكان واحد',
    'hero_subtitle_ar': 'أقوى ألعاب PC و PlayStation و Xbox، وبكجات Steam الأسطورية، واشتراكات الترفيه بأسعار حصرية بالدينار الجزائري وتسليم فوري!'
}
for k, v in theme_settings.items():
    cursor.execute("INSERT INTO theme_settings (key, value, created_at, updated_at) VALUES (?, ?, ?, ?)", (k, v, now, now))

# 15. Pages
pages_data = [
    ('about-us', 'من نحن - Alpha Gaming Shop', 'About Alpha Gaming Shop',
     '<h3>مرحباً بكم في Alpha Gaming Shop</h3><p>المتجر الجزائري الرائد في توفير الحسابات الأصلية للألعاب، بكجات Steam الضخمة، والاشتراكات الرقمية (Netflix, Game Pass, Adobe, Spotify) بأفضل الأسعار مع ضمان كامل ودعم فني على مدار الساعة.</p>',
     'Welcome to Alpha Gaming Shop, Algeria\'s premier destination for original gaming accounts and digital subscriptions.',
     'من نحن | Alpha Gaming Shop', 'تعرف على متجر Alpha Gaming Shop الرائد في الجزائر', 'active', now, now),
    
    ('payment-methods', 'طرق الدفع والتسليم في الجزائر', 'Payment & Delivery Methods in Algeria',
     '<h3>طرق الدفع المعتمدة:</h3><ul><li><strong>تطبيق بريدي موب (BaridiMob):</strong> تحويل مباشر وسريع.</li><li><strong>الحساب البريدي الجاري (CCP):</strong> عبر أي مركز بريد في الجزائر.</li><li><strong>PayPal والبطاقات الدولية:</strong> للمقيمين والمهتمين بالدفع الإلكتروني.</li></ul><h3>طريقة التسليم:</h3><p>يتم تسليم الحسابات والأكواد فوراً عبر رسالة WhatsApp والبريد الإلكتروني مع كافة بيانات التفعيل وضمان كامل.</p>',
     'We accept BaridiMob, CCP, and PayPal with instant delivery via WhatsApp and Email.',
     'طرق الدفع والتسليم | Alpha Gaming Shop', 'شرح طرق الدفع عبر بريدي موب و CCP في الجزائر', 'active', now, now)
]
cursor.executemany("""
INSERT INTO pages (slug, title_ar, title_en, content_ar, content_en, meta_title, meta_description, status, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
""", pages_data)

conn.commit()
conn.close()
print("Database successfully populated with 33 products and all Instagram assets!")
