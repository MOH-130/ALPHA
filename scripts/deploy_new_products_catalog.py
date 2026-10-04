import os, shutil, sqlite3, sys
from datetime import datetime

sys.stdout.reconfigure(encoding='utf-8')

src_dir = r"C:\Users\elnou\Downloads\New folder"
dest_dir = r"C:\Users\elnou\.gemini\antigravity\scratch\ecommerce-store\public\uploads\products"
backup_dir = r"C:\Users\elnou\.gemini\antigravity\scratch\ecommerce-store\backups\products_images"
db_path = r"C:\Users\elnou\.gemini\antigravity\scratch\ecommerce-store\storage\data\database.sqlite"

os.makedirs(dest_dir, exist_ok=True)
os.makedirs(backup_dir, exist_ok=True)

# 1. Image Copying
file_mappings = [
    # New products
    ("Snapchat Plus abonnement exceptionnel.png", "prod_snapchat_plus.png"),
    ("Publicité futuriste pour Gemini 3.1.png", "prod_gemini_3_1_ai_pro.png"),
    ("ChatGPT Image 16 juil. 2026, 15_53_40.png", "prod_google_ai_pro_veo.png"),
    ("ChatGPT Image 10 juil. 2026, 17_57_38.png", "prod_chatgpt_plus.png"),
    ("ChatGPT Image 16 juil. 2026, 17_29_24.png", "prod_deezer_premium.png"),
    ("ChatGPT Image 18 juil. 2026, 18_35_29.png", "prod_nordvpn_3m.png"),
    ("ChatGPT Image 14 juil. 2026, 19_18_03.png", "prod_promax_iptv_12m.png"),
    ("ChatGPT Image 10 juil. 2026, 18_06_19.png", "prod_ea_fc_26_pc_steam.png"),
    ("Publicité vibrante pour eFootball PES 2026.png", "prod_pes_2026_gold_android.png"),
    ("ChatGPT Image 14 juil. 2026, 21_01_29.png", "prod_pes_2025_ios.png"),
    ("النسخة الكاملة من لعبة القراصنة.png", "prod_ac4_black_flag_resynced.png"),
    ("ChatGPT Image 14 juil. 2026, 19_21_36.png", "prod_ac4_black_flag_resynced_deluxe.png"),
    ("Affiche Adobe Creative Cloud colorée en arabe.png", "prod_adobe_creative_cloud_3m.png"),
    ("ChatGPT Image 28 juil. 2026, 12_48_29.png", "prod_xbox_game_pass_switch.png"),
    # Enhanced existing products
    ("Offre Netflix 4K Premium.png", "prod_netflix_4k_premium.png"),
    ("Affiche promotionnelle des écrans avec prix.png", "prod_netflix_screens_pricing.png"),
    ("ChatGPT Image 16 juil. 2026, 17_01_55.png", "prod_spotify_premium.png"),
    ("XBOX Game Pass Ultimate promotion.png", "prod_xbox_game_pass_ultimate.png"),
    ("Xbox Game Pass Ultimate promotion(2).png", "prod_xbox_game_pass_ultimate_features.png"),
    ("عرض ألفا_ اشتراك Adobe الإبداعي.png", "prod_adobe_creative_cloud_1year_poster.png"),
    ("ChatGPT Image 21 juil. 2026, 17_20_21.png", "prod_adobe_creative_cloud_365d.png"),
    ("Affiche promotionnelle Steam néonʻani.png", "prod_steam_120_games.png"),
    ("ChatGPT Image 22 août 2026, 12_51_47.png", "prod_steam_45k_games.png"),
    ("ألفا_ أكبر باقة ألعاب PC.png", "prod_steam_4400_games.png"),
]

print("--- Step 1: Copying and backing up images ---")
for src_name, dest_name in file_mappings:
    src_file = os.path.join(src_dir, src_name)
    target_file = os.path.join(dest_dir, dest_name)
    backup_file = os.path.join(backup_dir, dest_name)
    shutil.copy2(src_file, target_file)
    shutil.copy2(src_file, backup_file)
    print(f"Copied: {src_name} -> {dest_name} ({os.path.getsize(target_file):,} bytes)")

print("\n--- Step 2: Database Operations ---")
conn = sqlite3.connect(db_path)
cursor = conn.cursor()
now_str = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

# Brands setup
brands_to_ensure = [
    ('جوجل (Google)', 'Google', 'google', 'Google AI, Gemini, Drive'),
    ('أوبن إيه آي (OpenAI)', 'OpenAI', 'openai', 'OpenAI ChatGPT, GPT-4o'),
    ('سناب شات (Snapchat)', 'Snapchat', 'snapchat', 'Snapchat Plus Official Subscriptions'),
    ('ديزر (Deezer)', 'Deezer', 'deezer', 'Deezer Premium HiFi Music'),
    ('نورد في بي إن (Nord Security)', 'NordVPN', 'nordvpn', 'NordVPN Cybersecurity and Privacy'),
    ('كونامي (Konami)', 'Konami', 'konami', 'Konami eFootball PES Series'),
    ('يوبي سوفت (Ubisoft)', 'Ubisoft', 'ubisoft', 'Ubisoft Assassin\'s Creed Series')
]

brand_ids = {}
for name_ar, name_en, slug, desc in brands_to_ensure:
    cursor.execute("SELECT id FROM brands WHERE slug = ?", (slug,))
    res = cursor.fetchone()
    if res:
        brand_ids[slug] = res[0]
    else:
        cursor.execute("""
            INSERT INTO brands (name_ar, name_en, slug, description_ar, description_en, is_featured, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 1, 'active', ?, ?)
        """, (name_ar, name_en, slug, desc, desc, now_str, now_str))
        brand_ids[slug] = cursor.lastrowid
        print(f"Created brand: {name_en} (ID: {brand_ids[slug]})")

# Also fetch existing brands
for b_slug in ['rockstar-games', 'steam', 'xbox', 'playstation', 'adobe', 'ea-sports', 'mojang', 'netflix', 'spotify']:
    cursor.execute("SELECT id FROM brands WHERE slug = ?", (b_slug,))
    res = cursor.fetchone()
    if res:
        brand_ids[b_slug] = res[0]

print("Brand IDs Map:", brand_ids)

# List of new products to insert
new_products = [
    {
        'category_id': 5, # Software & Subscriptions
        'brand_id': brand_ids['snapchat'],
        'name_ar': 'اشتراك سناب شات بلس Snapchat+ Premium (تفعيل فوري لجميع الأجهزة)',
        'name_en': 'Snapchat+ Premium Official Subscription',
        'slug': 'snapchat-plus-premium',
        'sku': 'AG-SUB-SNAP-01',
        'price': 1500,
        'compare_price': 3000,
        'cost_price': 1000,
        'stock_quantity': 100,
        'primary_image': 'prod_snapchat_plus.png',
        'is_featured': 1,
        'is_best_seller': 1,
        'short_description_ar': 'احصل على اشتراك Snapchat+ الرسمي واستمتع بجميع الميزات الحصرية مثل شارة النجمة ورؤية من أعاد مشاهدة القصة وتثبيت صديقك المفضل رقم 1 بأفضل سعر في الجزائر.',
        'short_description_en': 'Official Snapchat+ Premium subscription with exclusive features, story rewatch indicator, custom icons, and instant delivery.',
        'description_ar': '''استمتع بتجربة سناب شات من المستوى التالي مع اشتراك Snapchat Plus الرسمي من ALPHA GAMING SHOP!

الميزات الحصرية:
• معرفة من أعاد مشاهدة قصتك (Story Rewatch Count).
• تثبيت صديقك المفضل كـ Best Friend #1 على رأس المحادثات.
• شارة النجمة الذهبية الحصرية بجانب اسمك.
• أكثر من 40 أيقونة تطبيق ورمز تعبيري مخصص لتطبيق سناب شات.
• مشاهدة القصص دون قيود وبسرعة فائقة وخلفيات دردشة حصرية.
• تفعيل فوري ومباشر على حسابك الشخصي دون الحاجة لكلمة المرور.
• متوافق مع كافة هواتف Android و iOS / iPhone.
• ضمان كامل طوال فترة الاشتراك ودعم فني متواصل 24/7.

خطط الاشتراك المتاحة:
- 3 أشهر: 1500 دج (أفضل تجربة)
- 6 أشهر: 2500 دج (أفضل توفير)
- 12 شهراً: 3000 دج (الأكثر طلباً والأفضل قيمة)''',
        'description_en': 'Official Snapchat+ Premium subscription with instant activation, story rewatch indicator, exclusive badge, and dedicated 24/7 support from ALPHA GAMING SHOP.',
        'images': [('prod_snapchat_plus.png', 'Snapchat Plus Official Banner', 1, 1)]
    },
    {
        'category_id': 5, # Software & Subscriptions
        'brand_id': brand_ids['google'],
        'name_ar': 'اشتراك جوجل جيميني برو Gemini 3.1 AI Pro - تفعيل رسمي لمدة 18 شهراً',
        'name_en': 'Google Gemini 3.1 AI Pro Official (18 Months)',
        'slug': 'gemini-3-1-ai-pro-18-months',
        'sku': 'AG-AI-GEMINI-18M',
        'price': 1000,
        'compare_price': 2500,
        'cost_price': 600,
        'stock_quantity': 80,
        'primary_image': 'prod_gemini_3_1_ai_pro.png',
        'is_featured': 1,
        'is_best_seller': 1,
        'short_description_ar': 'أقوى أدوات الذكاء الاصطناعي من جوجل Gemini 3.1 Pro بتفعيل رسمي على حسابك لمدة 18 شهراً كاملة بسعر لا يقبل المنافسة 1000 DA فقط.',
        'short_description_en': 'Google Gemini 3.1 AI Pro official 18-month account activation with unlimited reasoning, code generation, and research capabilities.',
        'description_ar': '''انطلق بإمكانيات غير محدودة في الدراسة والعمل والبرمجة مع اشتراك Gemini 3.1 AI Pro من Alpha Gaming Shop!

أبرز المزايا:
• تفعيل رسمي ومباشر على حسابك الشخصي لمدة 18 شهراً كاملة.
• أحدث وأقوى نماذج التفكير الذكي المتقدم من Google (Gemini 3.1 Pro).
• كتابة وتدقيق الأكواد البرمجية المعقدة وحل المسائل البرمجية بدقة فائقة.
• إعداد الأبحاث الجامعية والتقارير الاحترافية والمقالات المتقنة.
• تحليل مستندات PDF الضخمة، جداول البيانات، والصور المعقدة.
• محادثات غير محدودة وتفاعل طبيعي وسريع بدون انقطاع.
• ضمان كامل طوال فترة الاشتراك مع دعم فني متواصل 24/7 عبر واتساب وتيليجرام.''',
        'description_en': 'Google Gemini 3.1 AI Pro subscription providing 18 months of next-gen AI reasoning, document analysis, and coding assistance.',
        'images': [('prod_gemini_3_1_ai_pro.png', 'Gemini 3.1 AI Pro 18 Months Poster', 1, 1)]
    },
    {
        'category_id': 5, # Software & Subscriptions
        'brand_id': brand_ids['google'],
        'name_ar': 'اشتراك Google AI Pro الشامل (Gemini + Veo AI + 2TB Drive) لمدة 18 شهراً',
        'name_en': 'Google AI Pro All-in-One (Gemini, Veo, 2TB Storage) 18 Months',
        'slug': 'google-ai-pro-veo-2tb-18m',
        'sku': 'AG-AI-GOOGLE-VEO-2TB',
        'price': 3000,
        'compare_price': 6000,
        'cost_price': 1800,
        'stock_quantity': 50,
        'primary_image': 'prod_google_ai_pro_veo.png',
        'is_featured': 1,
        'is_best_seller': 0,
        'short_description_ar': 'الباقة العملاقة من جوجل (12+6 أشهر = 18 شهراً): Gemini Pro + أداة توليد الفيديو Veo + مساحة تخزين 2TB Google Drive بسعر استثنائي 3000 دج.',
        'short_description_en': 'Complete Google AI Pro suite: Gemini AI, Veo Video Generator, Nano Banana tools, and 2TB Google Drive cloud storage for 18 months.',
        'description_ar': '''الحزمة الأكثر تكاملاً للمبدعين وصناع المحتوى والمهندسين من متجر ALPHA GAMING SHOP!

محتويات باقة Google AI Pro:
• 18 شهراً اشتراك كامل (12 شهراً أساسية + 6 أشهر إضافية مجانية).
• أداة صناعة الفيديوهات الثورية بالذكاء الاصطناعي Veo AI بدقة سينمائية وتأثيرات واقعية.
• نموذج الذكاء الاصطناعي المتقدم Gemini Pro لأعقد المهام والبرمجة وتحليل الملفات.
• أدوات Nano Banana الإبداعية لتحرير ومعالجة وتوليد الصور بالذكاء الاصطناعي.
• مساحة تخزين سحابية عملاقة 2 تيرابايت (2TB) على Google Drive لجميع ملفاتك وفيديوهاتك ونسخك الاحتياطية.
• حساب رسمي وآمن يعمل على كافة الأجهزة (الكمبيوتر، الهاتف الذكي، الأجهزة اللوحية).
• تسليم فوري بعد التأكيد وضمان شامل طوال فترة الاشتراك.''',
        'description_en': 'Google AI Pro 18-month bundle featuring Veo AI video generator, Gemini Pro, Nano Banana creative suite, and 2TB cloud storage.',
        'images': [('prod_google_ai_pro_veo.png', 'Google AI Pro Veo and 2TB Drive Banner', 1, 1)]
    },
    {
        'category_id': 5, # Software & Subscriptions
        'brand_id': brand_ids['openai'],
        'name_ar': 'اشتراك شات جي بي تي بلس ChatGPT Plus - حساب خاص وتفعيل رسمي',
        'name_en': 'ChatGPT Plus Official Private Account Subscription',
        'slug': 'chatgpt-plus-private-account',
        'sku': 'AG-AI-CHATGPT-PLUS',
        'price': 800,
        'compare_price': 1500,
        'cost_price': 500,
        'stock_quantity': 60,
        'primary_image': 'prod_chatgpt_plus.png',
        'is_featured': 1,
        'is_best_seller': 1,
        'short_description_ar': 'حساب شات جي بي تي بلس ChatGPT Plus خاص بك 100% مع أحدث قدرات GPT-4o وتوليد الصور والبحث المباشر بسعر يبدأ من 800 دج.',
        'short_description_en': 'Official ChatGPT Plus private account with full access to GPT-4o, DALL-E, advanced voice, and canvas tools.',
        'description_ar': '''تجربة ذكاء اصطناعي احترافية بامتياز مع اشتراك ChatGPT Plus من Alpha Gaming Shop!

المزايا الرئيسية:
• حساب خاص بك 100% (وليس حساباً مشتركاً) لأقصى درجات الخصوصية والاستقرار.
• وصول كامل وغير محدود لأحدث نماذج OpenAI الذكية (GPT-4o و Canvas).
• استجابات أسرع وأذكى مع أولوية مطلقة في أوقات الذروة.
• إنشاء وتعديل الصور بدقة عالية عبر DALL-E مدمج.
• تحليل البيانات وجداول Excel وملفات البرمجة وتقديم حلول جاهزة.
• التفعيل عبر رابط رسمي آمن وموثوق خلال دقائق معدودة.
• دعم فني متواصل وضمان تشغيل مستمر طوال فترة الاشتراك.''',
        'description_en': 'Private ChatGPT Plus official subscription with GPT-4o access, code analysis, and high-speed generation.',
        'images': [('prod_chatgpt_plus.png', 'ChatGPT Plus Private Account Poster', 1, 1)]
    },
    {
        'category_id': 5, # Software & Subscriptions
        'brand_id': brand_ids['deezer'],
        'name_ar': 'اشتراك ديزر بريميوم Deezer Premium لمدة سنة كاملة (حساب خاص بك)',
        'name_en': 'Deezer Premium 12 Months Private Account',
        'slug': 'deezer-premium-12-months',
        'sku': 'AG-MUSIC-DEEZER-12M',
        'price': 2000,
        'compare_price': 4000,
        'cost_price': 1300,
        'stock_quantity': 40,
        'primary_image': 'prod_deezer_premium.png',
        'is_featured': 0,
        'is_best_seller': 0,
        'short_description_ar': 'اشتراك Deezer Premium لمدة 12 شهراً على حساب خاص بك، استمتع بملايين الأغاني بجودة عالية وبدون إعلانات مع التحميل أوفلاين بسعر 2000 دج.',
        'short_description_en': 'Deezer Premium 12 months subscription with ad-free music, Hi-Fi sound quality, offline downloads, and multi-device support.',
        'description_ar': '''عش تجربة موسيقية راقية مع Deezer Premium لمدة عام كامل من ALPHA GAMING SHOP!

المميزات:
• حساب خاص بك 100% (Private Account) بكامل صلاحيات التخصيص.
• مدة الاشتراك: 12 شهراً كاملة (سنة كاملة) مع ضمان شامل.
• استماع غير محدود لملايين المسارات الصوتية بدون أي إعلانات.
• جودة صوت فائقة النقاء وعالية الدقة (HiFi Sound Quality).
• ميزة Flow الذكية التي تتعرف على ذوقك وتقدم لك قوائم مخصصة باستمرار.
• إمكانية تحميل الأغاني والقوائم والاستماع بدون اتصال بالإنترنت (Offline Mode).
• يعمل بسلاسة على الهواتف الذكية، أجهزة الكمبيوتر، والسيارة ومكبرات الصوت الذكية.''',
        'description_en': 'Official Deezer Premium 12-month private subscription with offline listening and crystal-clear HiFi audio.',
        'images': [('prod_deezer_premium.png', 'Deezer Premium 12 Months Poster', 1, 1)]
    },
    {
        'category_id': 5, # Software & Subscriptions
        'brand_id': brand_ids['nordvpn'],
        'name_ar': 'اشتراك نورد في بي إن NordVPN Basic لمدة 3 أشهر (حتى 10 أجهزة)',
        'name_en': 'NordVPN Basic 3 Months Subscription (Up to 10 Devices)',
        'slug': 'nordvpn-basic-3-months',
        'sku': 'AG-SEC-NORDVPN-3M',
        'price': 1800,
        'compare_price': 3500,
        'cost_price': 1100,
        'stock_quantity': 50,
        'primary_image': 'prod_nordvpn_3m.png',
        'is_featured': 0,
        'is_best_seller': 0,
        'short_description_ar': 'حماية وتشفير فائق لاتصالك بالإنترنت عبر أفضل خدمة VPN عالمياً NordVPN لمدة 3 أشهر تدعم حتى 10 أجهزة في وقت واحد بسعر 1800 دج.',
        'short_description_en': 'NordVPN Basic 3-month secure subscription protecting up to 10 devices simultaneously with high-speed global servers and strict no-logs policy.',
        'description_ar': '''أمانك الرقمي يبدأ بنقرة واحدة مع NordVPN من ALPHA GAMING SHOP!

أبرز مميزات الخدمة:
• اشتراك واحد يحمي حتى 10 أجهزة في نفس الوقت (Windows, Mac, Android, iOS, Routers).
• مدة الاشتراك: 3 أشهر مع ضمان كامل طوال المدة.
• تشفير عسكري قوي لحماية خصوصيتك وبياناتك البنكية على جميع الشبكات.
• آلاف الخوادم فائقة السرعة حول العالم لتجاوز الحجب وفتح المواقع والألعاب المحظورة.
• تقنية منع الإعلانات وحجب البرمجيات الخبيثة تلقائياً (Threat Protection).
• سياسة صارمة لمنع تسجيل النشاط (Strict No-Logs Policy) المعتمدة عالمياً.
• اتصال مستقر وسريع جداً مخصص للبث المباشر والألعاب عبر الإنترنت.''',
        'description_en': 'NordVPN Basic 3-month subscription for up to 10 devices with ultra-fast secure servers and zero logs.',
        'images': [('prod_nordvpn_3m.png', 'NordVPN 3 Months Poster', 1, 1)]
    },
    {
        'category_id': 5, # Software & Subscriptions
        'brand_id': None,
        'name_ar': 'اشتراك سيرفر Promax 4K OTT IPTV بريميوم (12 شهراً - جودة فائقة 4K)',
        'name_en': 'Promax 4K OTT IPTV Premium 12 Months Subscription',
        'slug': 'promax-4k-ott-iptv-12m',
        'sku': 'AG-IPTV-PROMAX-12M',
        'price': 4000,
        'compare_price': 6500,
        'cost_price': 2500,
        'stock_quantity': 100,
        'primary_image': 'prod_promax_iptv_12m.png',
        'is_featured': 1,
        'is_best_seller': 1,
        'short_description_ar': 'اشتراك IPTV الأقوى والأكثر استقراراً في الجزائر! سيرفر Promax 4K العالمي لمدة سنة كاملة لمتابعة أقوى البطولات والأفلام بسعر 4000 دج فقط.',
        'short_description_en': 'Top tier Promax 4K OTT IPTV server with worldwide channels (beIN, Sky, OSN), massive 4K movie and series library, and zero-freeze streaming for 12 months.',
        'description_ar': '''تجربة ترفيهية ورياضية بلا حدود مع سيرفر PROMAX 4K OTT IPTV العالمي من ALPHA GAMING SHOP!

المواصفات والمزايا:
• اشتراك لمدة 12 شهراً كاملة (حساب خاص بريميوم Global).
• جودة بث أصلية فائقة تصل إلى 4K UHD و FHD بدون ضغط أو تشويش.
• نقل مباشر لجميع الدوريات الكبرى والبطولات العالمية عبر باقات beIN Sports، Sky Sports، SSC، OSN.
• آلاف القنوات التلفزيونية العربية والعالمية المنظمة بدقة عالية.
• مكتبة VOD عملاقة ومحدثة يومياً بأحدث الأفلام والمسلسلات العربية والأجنبية والأنمي مع الترجمة الاحترافية.
• سيرفرات فائقة القوة والاستقرار ومضادة للضغط والتقطيع في أهم المباريات (Anti-Freeze 24/7).
• متوافق مع كافة الأجهزة: Smart TV, Android Box, iOS, Firestick, MAG, PC, Enigma2.''',
        'description_en': 'Promax 4K OTT IPTV premium 1-year subscription with high-speed servers, worldwide sports coverage, and massive on-demand movie library.',
        'images': [('prod_promax_iptv_12m.png', 'Promax 4K IPTV 12 Months Poster', 1, 1)]
    },
    {
        'category_id': 3, # EA SPORTS FC
        'brand_id': brand_ids['ea-sports'],
        'name_ar': 'لعبة EA SPORTS FC 26 للكمبيوتر PC - حساب Steam رسمي أونلاين',
        'name_en': 'EA SPORTS FC 26 PC Steam Online Official Account',
        'slug': 'ea-sports-fc-26-pc-steam',
        'sku': 'AG-FC-26-PC-STEAM',
        'price': 1000,
        'compare_price': 2500,
        'cost_price': 600,
        'stock_quantity': 75,
        'primary_image': 'prod_ea_fc_26_pc_steam.png',
        'is_featured': 1,
        'is_best_seller': 1,
        'short_description_ar': 'حساب Steam أصلي وخاص لتشغيل لعبة EA SPORTS FC 26 على الكمبيوتر مع إمكانية اللعب أونلاين والتمتع بكامل أطوار اللعبة بسعر 1000 دج فقط.',
        'short_description_en': 'Official EA SPORTS FC 26 PC account on Steam with full online features, instant delivery, and 24/7 technical support.',
        'description_ar': '''عش متعة كرة القدم الحقيقية مع لعبة EA SPORTS FC 26 للكمبيوتر من متجر Alpha Gaming Shop!

المميزات:
• حساب Steam رسمي وخاص وآمن 100%.
• اللعب أونلاين متوفر بدون أي مشاكل أو قيود.
• نسخة مدفوعة أصلية تشمل جميع الفرق واللاعبين وأحدث الانتقالات.
• تسليم سريع وفوري للبيانات بعد إتمام الطلب مباشرة.
• خصوصية تامة وضمان تشغيل دائم على الحساب.''',
        'description_en': 'Official EA SPORTS FC 26 PC version on Steam with online multiplayer capabilities and instant delivery.',
        'images': [('prod_ea_fc_26_pc_steam.png', 'EA SPORTS FC 26 PC Steam Poster', 1, 1)]
    },
    {
        'category_id': 1, # PC & Console Games
        'brand_id': brand_ids['konami'],
        'name_ar': 'حساب بيس eFootball PES 2026 Gold للأندرويد (9000+ عملة Gold وفريق أساطير)',
        'name_en': 'eFootball PES 2026 Gold Android (9000+ Gold Coins & Epic Squad)',
        'slug': 'efootball-pes-2026-gold-android',
        'sku': 'AG-PES-2026-GOLD-AND',
        'price': 7000,
        'compare_price': 10000,
        'cost_price': 5000,
        'stock_quantity': 15,
        'primary_image': 'prod_pes_2026_gold_android.png',
        'is_featured': 1,
        'is_best_seller': 0,
        'short_description_ar': 'حساب أسطوري مهيأ بالكامل للعبة بيس eFootball PES 2026 للأندرويد يحتوي على أكثر من 9000 كوينز ذهبية وفريق مدجج بنجوم Epic و Legendary بسعر 7000 دج.',
        'short_description_en': 'Packed eFootball PES 2026 Gold Android account featuring 9000+ Gold Coins, 80-100 5-star players, and legendary epic cards.',
        'description_ar': '''حساب أسطوري يمنحك الأفضلية المطلقة في لعبة eFootball PES 2026 للأندرويد!

تفاصيل الحساب الاستثنائية:
• رصيد ضخم يتجاوز 9000 عملة ذهبية (GOLD Coins) لفتح أي بكج تريده فوراً.
• من 80 إلى 100 لاعب بتقييم 5 نجوم (ميسي، مبابي، هالاند، صلاح، مودريتش، فان دايك والمزيد).
• من 3 إلى 9 لاعبين أساطير ونادرين للغاية (Epic & Legendary Cards).
• تشكيلات احترافية ومدربون مميزون وجاهزية تامة للمنافسة في أقوى الأحداث.
• مخصص لأجهزة Android فقط مع ربط آمن وسهل.
• تسليم فوري لحساب آمن ومضمون 100% مع ضمان كامل من ALPHA GAMING SHOP.''',
        'description_en': 'Elite eFootball PES 2026 Android account with 9000+ Gold Coins, 80-100 5-star players, and legendary epic cards.',
        'images': [('prod_pes_2026_gold_android.png', 'eFootball PES 2026 Gold Android Poster', 1, 1)]
    },
    {
        'category_id': 1, # PC & Console Games
        'brand_id': brand_ids['konami'],
        'name_ar': 'حساب بيس eFootball 2025 PES للأيفون iOS (5100 كوينز + 4.5 مليون GP)',
        'name_en': 'eFootball 2025 PES iOS Account (5100 Coins + 4.5M GP)',
        'slug': 'efootball-pes-2025-ios',
        'sku': 'AG-PES-2025-IOS',
        'price': 5000,
        'compare_price': 7500,
        'cost_price': 3500,
        'stock_quantity': 20,
        'primary_image': 'prod_pes_2025_ios.png',
        'is_featured': 0,
        'is_best_seller': 0,
        'short_description_ar': 'حساب جاهز وقوي للعبة eFootball 2025 PES مخصص لأجهزة الأيفون والأيباد iOS يشمل 5000-5100 كوينز و 4.5M GP مع رابط Konami ID رسمي بسعر 5000 دج.',
        'short_description_en': 'Ready-to-play eFootball 2025 PES iOS account loaded with 5000+ Coins, 4.5M GP, top-rated players, and verified Konami ID.',
        'description_ar': '''حساب جاهز بخيارات نارية لتجربة لعب أسطورية على أجهزة Apple iOS (iPhone / iPad)!

مواصفات الحساب:
• رصيد كوينز جاهز: من 5000 إلى 5100 عملة eFootball Coins.
• رصيد GP ضخم: 4.5 مليون GP للتعاقد مع المدربين وتطوير اللاعبين.
• من 15 إلى 30 لاعباً مميزاً (Highlight Players).
• من 3 إلى 8 لاعبين أساطير وبطاقات نادرة (Epic Players).
• رابط Konami ID رسمي وجاهز للنقل المباشر لجهازك.
• تسليم فوري بعد الدفع وضمان أمان 100% بدون أي مشاكل.''',
        'description_en': 'eFootball 2025 PES iOS verified account with 5100 coins, 4.5M GP, and strong highlight squad.',
        'images': [('prod_pes_2025_ios.png', 'eFootball 2025 PES iOS Poster', 1, 1)]
    },
    {
        'category_id': 1, # PC & Console Games
        'brand_id': brand_ids['ubisoft'],
        'name_ar': 'لعبة Assassin\'s Creed IV Black Flag Resynced كاملة مع مكافآت الديلوكس',
        'name_en': 'Assassin\'s Creed IV Black Flag Resynced Deluxe Edition',
        'slug': 'assassins-creed-iv-black-flag-resynced',
        'sku': 'AG-GAME-AC4-RESYNCED',
        'price': 1000,
        'compare_price': 3750,
        'cost_price': 500,
        'stock_quantity': 50,
        'primary_image': 'prod_ac4_black_flag_resynced.png',
        'is_featured': 1,
        'is_best_seller': 0,
        'short_description_ar': 'عِش ملحمة القراصنة الكبرى بنسخة إعادة المزامنة المحسنة Resynced مع جميع مكافآت وحزم الديلوكس وجرافيك مطور بسعر عرض خاص 1000 دج فقط.',
        'short_description_en': 'Assassin\'s Creed IV Black Flag Resynced Deluxe edition with enhanced visuals, full story content, all pirate bonus packs, and offline mode.',
        'description_ar': '''النسخة الكاملة والنهائية من تحفة ألعاب القراصنة Assassin\'s Creed IV Black Flag Resynced من ALPHA GAMING SHOP!

محتويات وميزات النسخة:
• نسخة مُعاد مزامنتها ومحسّنة (جرافيك أفضل، أداء أكثر سلاسة، وتجربة لعب أصلية ممتعة).
• اللعبة كاملة بنسبة 100% مع جميع المهام والمناطق البحرية والبرية.
• تشمل جميع مكافآت الديلوكس الحصرية:
  - حزمة القراصنة الشهيرين (The Castaway\'s Prize).
  - حزمة إرث السير غان (Sir Gunn\'s Legacy).
  - حزمة إرث الكابتن كينواي (Captain Kenway\'s Legacy).
  - حزمة الأسرار المقدسة (Sacrificed Secrets).
• تسجيل دخول أوفلاين آمن ومضمون بدون انقطاع.
• تسليم فوري بعد تأكيد الطلب مع دعم فني متواصل 24/7.''',
        'description_en': 'Assassin\'s Creed IV Black Flag Resynced Deluxe with all bonus packs, enhanced visuals, and smooth offline performance.',
        'images': [
            ('prod_ac4_black_flag_resynced.png', 'Assassin\'s Creed IV Black Flag Resynced Poster 1000 DA', 1, 1),
            ('prod_ac4_black_flag_resynced_deluxe.png', 'Assassin\'s Creed IV Deluxe Details', 2, 0)
        ]
    },
    {
        'category_id': 5, # Software & Subscriptions
        'brand_id': brand_ids['adobe'],
        'name_ar': 'اشتراك أدوبي كرييتف كلاود Adobe Creative Cloud (جميع التطبيقات) - 3 أشهر',
        'name_en': 'Adobe Creative Cloud All Apps 3 Months Subscription',
        'slug': 'adobe-creative-cloud-3-months',
        'sku': 'AG-ADOBE-CC-3M',
        'price': 4000,
        'compare_price': 6000,
        'cost_price': 2800,
        'stock_quantity': 45,
        'primary_image': 'prod_adobe_creative_cloud_3m.png',
        'is_featured': 1,
        'is_best_seller': 1,
        'short_description_ar': 'باقة الـ 3 أشهر الاقتصادية من Adobe Creative Cloud: وصول كامل لأكثر من 20 برنامج تصميم ومونتاج رسمي مع 100GB مساحة سحابية بسعر 4000 دج فقط.',
        'short_description_en': 'Official 3-month Adobe Creative Cloud All Apps subscription with 100GB cloud storage, continuous updates, and private account warranty.',
        'description_ar': '''أطلق العنان لإبداعك مع اشتراك Adobe Creative Cloud الرسمي لمدة 3 أشهر كاملة من ALPHA GAME SHOP!

المزايا المتضمنة:
• جميع برامج أدوبي الرسمية (Photoshop, Illustrator, Premiere Pro, After Effects, Lightroom, Adobe XD, Audition والمزيد).
• 100GB مساحة تخزين سحابية سريعة على سحابة Adobe Cloud لمزامنة ملفاتك ومشاريعك.
• حساب خاص بك 100% بدون مشاركة، مع تحديثات مستمرة لآخر الإصدارات.
• ضمان كامل طوال فترة الاشتراك (90 يوماً).
• تفعيل سريع وفوري ودعم فني على مدار الساعة.''',
        'description_en': 'Full Adobe Creative Cloud All Apps suite for 3 months with 100GB cloud storage and private account warranty.',
        'images': [('prod_adobe_creative_cloud_3m.png', 'Adobe Creative Cloud 3 Months Poster', 1, 1)]
    },
    {
        'category_id': 5, # Software & Subscriptions
        'brand_id': brand_ids['xbox'],
        'name_ar': 'اشتراك إكس بوكس جيم باس Xbox Game Pass (حساب Switch / مشترك)',
        'name_en': 'Xbox Game Pass Switch Account Subscription',
        'slug': 'xbox-game-pass-switch-account',
        'sku': 'AG-XBOX-GP-SWITCH',
        'price': 1000,
        'compare_price': 1500,
        'cost_price': 700,
        'stock_quantity': 60,
        'primary_image': 'prod_xbox_game_pass_switch.png',
        'is_featured': 0,
        'is_best_seller': 0,
        'short_description_ar': 'تمتع بأضخم مكتبة ألعاب فيديو على أجهزة Xbox و PC و Switch بأسعار اقتصادية تبدأ من 1000 دج فقط مع اشتراك Game Pass Switch المشترك.',
        'short_description_en': 'Affordable Xbox Game Pass Switch account offering access to hundreds of top PC and Xbox titles with flexible 1, 3, 6, and 12-month options.',
        'description_ar': '''ألعاب أكثر، متعة أكثر، ومغامرات بلا حدود مع اشتراك Xbox Game Pass (Switch Account) من متجر ALPHA GAMING SHOP!

خيارات وباقات الاشتراك:
- شهر واحد: 1000 دج (أفضل تجربة)
- 3 أشهر: 1500 دج (أفضل قيمة)
- 6 أشهر: 3000 دج (توفير أكبر)
- 12 شهراً: 6000 دج (أفضل عرض سنوي)

مميزات الباقة:
• مكتبة ضخمة تضم مئات ألعاب AAA بجودة عالية.
• إضافة ألعاب جديدة باستمرار كل شهر.
• خصومات حصرية للأعضاء على الألعاب والإضافات.
• حساب مضمون وآمن بجودة عالية واستقرار دائم.
• يعمل على Xbox, PC, و Switch Account.''',
        'description_en': 'Flexible Xbox Game Pass subscription for Switch and PC platforms with extensive game catalog.',
        'images': [('prod_xbox_game_pass_switch.png', 'Xbox Game Pass Switch Account Poster', 1, 1)]
    }
]

print(f"\n--- Step 3: Inserting {len(new_products)} New Products ---")
inserted_count = 0
for p in new_products:
    cursor.execute("SELECT id FROM products WHERE slug = ?", (p['slug'],))
    existing = cursor.fetchone()
    if existing:
        prod_id = existing[0]
        print(f"Product already exists (ID: {prod_id}): {p['slug']} - Updating fields...")
        cursor.execute("""
            UPDATE products SET
                category_id = ?, brand_id = ?, name_ar = ?, name_en = ?,
                sku = ?, price = ?, compare_price = ?, cost_price = ?,
                stock_quantity = ?, primary_image = ?, is_featured = ?, is_best_seller = ?,
                short_description_ar = ?, short_description_en = ?,
                description_ar = ?, description_en = ?, status = 'active', updated_at = ?
            WHERE id = ?
        """, (
            p['category_id'], p['brand_id'], p['name_ar'], p['name_en'],
            p['sku'], p['price'], p['compare_price'], p['cost_price'],
            p['stock_quantity'], p['primary_image'], p['is_featured'], p['is_best_seller'],
            p['short_description_ar'], p['short_description_en'],
            p['description_ar'], p['description_en'], now_str, prod_id
        ))
    else:
        cursor.execute("""
            INSERT INTO products (
                category_id, brand_id, name_ar, name_en, slug, sku,
                short_description_ar, short_description_en, description_ar, description_en,
                price, compare_price, cost_price, stock_quantity, low_stock_threshold,
                primary_image, is_featured, is_best_seller, is_deal, status,
                views_count, sales_count, weight, created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?, ?, 5,
                ?, ?, ?, 0, 'active',
                0, 0, 0.1, ?, ?
            )
        """, (
            p['category_id'], p['brand_id'], p['name_ar'], p['name_en'], p['slug'], p['sku'],
            p['short_description_ar'], p['short_description_en'], p['description_ar'], p['description_en'],
            p['price'], p['compare_price'], p['cost_price'], p['stock_quantity'],
            p['primary_image'], p['is_featured'], p['is_best_seller'], now_str, now_str
        ))
        prod_id = cursor.lastrowid
        inserted_count += 1
        print(f"Inserted new product (ID: {prod_id}): {p['name_ar']}")

    # Insert images for this product
    cursor.execute("DELETE FROM product_images WHERE product_id = ?", (prod_id,))
    for img_path, alt_txt, sort_ord, is_prim in p['images']:
        cursor.execute("""
            INSERT INTO product_images (product_id, image_path, alt_text, sort_order, is_primary, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        """, (prod_id, img_path, alt_txt, sort_ord, is_prim, now_str, now_str))

print(f"\n--- Step 4: Enhancing Existing Products with Official Posters & Prices ---")
existing_updates = [
    # Netflix (ID 30)
    {
        'id': 30,
        'primary_image': 'prod_netflix_4k_premium.png',
        'price': 1500,
        'compare_price': 2500,
        'images': [
            ('prod_netflix_4k_premium.png', 'Netflix 4K Premium Shared Poster', 1, 1),
            ('prod_netflix_screens_pricing.png', 'Netflix Screens Multi-Device Pricing Grid', 2, 0)
        ]
    },
    # Spotify (ID 33)
    {
        'id': 33,
        'primary_image': 'prod_spotify_premium.png',
        'price': 800,
        'compare_price': 1200,
        'images': [
            ('prod_spotify_premium.png', 'Spotify Premium Official Poster', 1, 1)
        ]
    },
    # Xbox Game Pass Ultimate (ID 29)
    {
        'id': 29,
        'primary_image': 'prod_xbox_game_pass_ultimate.png',
        'price': 1500,
        'compare_price': 2500,
        'images': [
            ('prod_xbox_game_pass_ultimate.png', 'Xbox Game Pass Ultimate Official Poster', 1, 1),
            ('prod_xbox_game_pass_ultimate_features.png', 'Xbox Game Pass Ultimate Details Poster', 2, 0)
        ]
    },
    # Adobe Creative Cloud 1 Year (ID 28)
    {
        'id': 28,
        'primary_image': 'prod_adobe_creative_cloud_1year_poster.png',
        'price': 9500,
        'compare_price': 14000,
        'images': [
            ('prod_adobe_creative_cloud_1year_poster.png', 'Adobe Creative Cloud 1 Year 9500 DA Poster', 1, 1),
            ('prod_adobe_creative_cloud_365d.png', 'Adobe Photoshop & Creative Cloud 365 Days', 2, 0)
        ]
    },
    # Steam +120 Games (ID 23)
    {
        'id': 23,
        'primary_image': 'prod_steam_120_games.png',
        'price': 1000,
        'compare_price': 2000,
        'images': [
            ('prod_steam_120_games.png', 'Steam 120 Games Official Poster 1000 DA', 1, 1)
        ]
    },
    # Steam +45k Games (ID 15)
    {
        'id': 15,
        'primary_image': 'insta_20.png',
        'images': [
            ('insta_20.png', 'Steam 45k Games Primary', 1, 1),
            ('prod_steam_45k_games.png', 'Steam 45,000 Games Massive Library Showcase', 2, 0)
        ]
    },
    # Steam +4400 Games (ID 18)
    {
        'id': 18,
        'primary_image': 'prod_steam_4400_games.png',
        'images': [
            ('prod_steam_4400_games.png', 'Steam 4400 Games Official Neon Poster', 1, 1)
        ]
    }
]

for upd in existing_updates:
    p_id = upd['id']
    if 'price' in upd:
        cursor.execute("UPDATE products SET primary_image = ?, price = ?, compare_price = ?, updated_at = ? WHERE id = ?",
                       (upd['primary_image'], upd['price'], upd['compare_price'], now_str, p_id))
    else:
        cursor.execute("UPDATE products SET primary_image = ?, updated_at = ? WHERE id = ?",
                       (upd['primary_image'], now_str, p_id))
    
    cursor.execute("DELETE FROM product_images WHERE product_id = ?", (p_id,))
    for img_path, alt_txt, sort_ord, is_prim in upd['images']:
        cursor.execute("""
            INSERT INTO product_images (product_id, image_path, alt_text, sort_order, is_primary, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        """, (p_id, img_path, alt_txt, sort_ord, is_prim, now_str, now_str))
    print(f"Updated product ID {p_id} with official images & prices")

conn.commit()

# Total count
cursor.execute("SELECT COUNT(*) FROM products WHERE status = 'active'")
active_count = cursor.fetchone()[0]
cursor.execute("SELECT COUNT(*) FROM products")
total_count = cursor.fetchone()[0]
conn.close()

print(f"\n=== SUMMARY ===")
print(f"New Products Inserted: {inserted_count}")
print(f"Total Products in DB: {total_count}")
print(f"Active Products: {active_count}")
print("ALL DONE SUCCESSFULLY!")
