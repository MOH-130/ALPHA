<?php

namespace Database\Seeders;

use App\Core\Database;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductAttribute;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Coupon;
use App\Models\Deal;
use App\Models\Review;
use App\Models\ShippingMethod;
use App\Models\Setting;
use App\Models\ThemeSetting;
use App\Models\Page;

class DatabaseSeeder {
    public static function run(): void {
        // 1. Roles
        Role::create(['name' => 'Super Admin', 'slug' => 'super_admin', 'description' => 'Full control over system']);
        Role::create(['name' => 'Manager', 'slug' => 'manager', 'description' => 'Store manager']);
        Role::create(['name' => 'Customer', 'slug' => 'customer', 'description' => 'Registered store shopper']);

        // 2. Users (Admin & Customer)
        $admin = User::create([
            'name' => 'Alpha Gaming Admin',
            'email' => 'admin@store.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'phone' => '+213 555 12 34 56',
            'role' => 'admin',
            'status' => 'active',
            'is_approved' => 1,
            'affiliate_code' => 'ALPHA01',
            'city' => 'الجزائر العاصمة',
            'country' => 'Algeria'
        ]);

        $customer = User::create([
            'name' => 'يوسف سماتي (Youssef S.)',
            'email' => 'customer@store.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'phone' => '+213 550 98 76 54',
            'role' => 'customer',
            'status' => 'active',
            'is_approved' => 1,
            'affiliate_code' => 'YOUSSEF10',
            'address' => 'شارع ديدوش مراد، وسط الجزائر العاصمة',
            'city' => 'الجزائر العاصمة',
            'country' => 'Algeria'
        ]);

        // 3. Currencies (Default: Algerian Dinar - DZD / د.ج / DA)
        Currency::create([
            'code' => 'DZD',
            'name_ar' => 'دينار جزائري',
            'name_en' => 'Algerian Dinar',
            'symbol_ar' => 'د.ج',
            'symbol_en' => 'DA',
            'exchange_rate' => 1.0,
            'is_default' => 1,
            'status' => 'active'
        ]);
        Currency::create([
            'code' => 'USDT',
            'name_ar' => 'تيذر رقمي (Binance P2P)',
            'name_en' => 'Tether USDT (P2P)',
            'symbol_ar' => 'USDT',
            'symbol_en' => 'USDT',
            'exchange_rate' => 0.0040, // 1 USDT = 250.00 DZD (Binance & Bybit P2P parallel market)
            'is_default' => 0,
            'status' => 'active'
        ]);
        Currency::create([
            'code' => 'USD',
            'name_ar' => 'دولار أمريكي (سوق موازي)',
            'name_en' => 'US Dollar',
            'symbol_ar' => '$',
            'symbol_en' => '$',
            'exchange_rate' => 0.0040, // 1 USD = 250.00 DZD (Parallel Black Market)
            'is_default' => 0,
            'status' => 'active'
        ]);
        Currency::create([
            'code' => 'EUR',
            'name_ar' => 'يورو أوروبي (السكوار)',
            'name_en' => 'Euro (Square Port-Saïd)',
            'symbol_ar' => '€',
            'symbol_en' => '€',
            'exchange_rate' => 0.0037, // 1 EUR = 270.00 DZD (Square Port-Saïd Market)
            'is_default' => 0,
            'status' => 'active'
        ]);

        // 4. Languages
        Language::create(['code' => 'ar', 'name' => 'العربية', 'direction' => 'rtl', 'flag' => 'dz', 'is_default' => 1, 'status' => 'active']);
        Language::create(['code' => 'en', 'name' => 'English', 'direction' => 'ltr', 'flag' => 'us', 'is_default' => 0, 'status' => 'active']);

        // 5. Categories for Alpha Gaming Shop
        $catGames = Category::create([
            'name_ar' => 'ألعاب الكمبيوتر والكونسول (PC & Console Games)',
            'name_en' => 'PC & Console Games',
            'slug' => 'pc-console-games',
            'icon' => 'device-phone-mobile',
            'description_ar' => 'أقوى ألعاب Steam، PlayStation و Xbox بحسابات أصلية ومفاتيح تفعيل رسمية.',
            'description_en' => 'Top PC, PlayStation and Xbox original games with full access & instant delivery.',
            'sort_order' => 1,
            'is_featured' => 1,
            'status' => 'active'
        ]);

        $catSteamPacks = Category::create([
            'name_ar' => 'بكجات وحسابات ستيم (Steam Mega Packs)',
            'name_en' => 'Steam Mega Packs',
            'slug' => 'steam-mega-packs',
            'icon' => 'trophy',
            'description_ar' => 'أضخم مكتبات ألعاب Steam أوفلاين وأونلاين تضم آلاف الألعاب الحصرية بأسعار تبدأ من 500 DA.',
            'description_en' => 'Massive Steam offline & online libraries with thousands of AAA games starting from 500 DA.',
            'sort_order' => 2,
            'is_featured' => 1,
            'status' => 'active'
        ]);

        $catFC27 = Category::create([
            'name_ar' => 'سلسلة EA SPORTS FC (FC 27 / FIFA)',
            'name_en' => 'EA SPORTS FC 27 Series',
            'slug' => 'ea-sports-fc',
            'icon' => 'sparkles',
            'description_ar' => 'جميع نسخ EA Sports FC 27 للكمبيوتر و PS4 و PS5 (عادية وألتمت) بحسابات أصلية.',
            'description_en' => 'All EA Sports FC 27 editions for PC, PS4 and PS5 with early access and Ultimate Team perks.',
            'sort_order' => 3,
            'is_featured' => 1,
            'status' => 'active'
        ]);

        $catMinecraft = Category::create([
            'name_ar' => 'ماين كرافت الأصلية (Minecraft Official)',
            'name_en' => 'Minecraft Official Accounts',
            'slug' => 'minecraft',
            'icon' => 'cube',
            'description_ar' => 'حسابات ماين كرافت Java & Bedrock أصلية للكمبيوتر والجوال مع دعم Hypixel.',
            'description_en' => 'Official Minecraft Java & Bedrock accounts for PC & iOS with full access and Hypixel support.',
            'sort_order' => 4,
            'is_featured' => 1,
            'status' => 'active'
        ]);

        $catSubscriptions = Category::create([
            'name_ar' => 'اشتراكات البرامج والترفيه (Software & Subscriptions)',
            'name_en' => 'Software & Subscriptions',
            'slug' => 'subscriptions',
            'icon' => 'sparkles',
            'description_ar' => 'اشتراكات Adobe Creative Cloud، Xbox Game Pass، Netflix 4K، و Spotify Premium.',
            'description_en' => 'Adobe Creative Cloud 12M, Xbox Game Pass Ultimate, Netflix 4K and Spotify Premium.',
            'sort_order' => 5,
            'is_featured' => 1,
            'status' => 'active'
        ]);

        $catGiftCards = Category::create([
            'name_ar' => 'بطاقات الشحن والجيفت كارد (Gift Cards & Wallet)',
            'name_en' => 'Gift Cards & Wallet',
            'slug' => 'gift-cards',
            'icon' => 'shopping-bag',
            'description_ar' => 'بطاقات شحن رصيد ستيم Steam، بلايستيشن ستور، وبطاقات إكس بوكس.',
            'description_en' => 'Steam Wallet codes, PlayStation Network and Xbox digital gift cards.',
            'sort_order' => 6,
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // 6. Brands / Publishers
        $brandRockstar = Brand::create(['name_ar' => 'روكستار جيمز (Rockstar Games)', 'name_en' => 'Rockstar Games', 'slug' => 'rockstar-games', 'is_featured' => 1, 'status' => 'active']);
        $brandSteam = Brand::create(['name_ar' => 'ستيم (Steam / Valve)', 'name_en' => 'Steam / Valve', 'slug' => 'steam', 'is_featured' => 1, 'status' => 'active']);
        $brandXbox = Brand::create(['name_ar' => 'إكس بوكس (Xbox / Microsoft)', 'name_en' => 'Xbox', 'slug' => 'xbox', 'is_featured' => 1, 'status' => 'active']);
        $brandPlaystation = Brand::create(['name_ar' => 'بلايستيشن (PlayStation / Sony)', 'name_en' => 'PlayStation', 'slug' => 'playstation', 'is_featured' => 1, 'status' => 'active']);
        $brandAdobe = Brand::create(['name_ar' => 'أدوبي (Adobe)', 'name_en' => 'Adobe', 'slug' => 'adobe', 'is_featured' => 1, 'status' => 'active']);
        $brandEA = Brand::create(['name_ar' => 'إي أيه سبورتس (EA Sports)', 'name_en' => 'EA Sports', 'slug' => 'ea-sports', 'is_featured' => 1, 'status' => 'active']);
        $brandMojang = Brand::create(['name_ar' => 'موجانج (Mojang / Minecraft)', 'name_en' => 'Mojang', 'slug' => 'mojang', 'is_featured' => 1, 'status' => 'active']);
        $brandNetflix = Brand::create(['name_ar' => 'نتفلكس (Netflix)', 'name_en' => 'Netflix', 'slug' => 'netflix', 'is_featured' => 1, 'status' => 'active']);
        $brandSpotify = Brand::create(['name_ar' => 'سبوتيفاي (Spotify)', 'name_en' => 'Spotify', 'slug' => 'spotify', 'is_featured' => 1, 'status' => 'active']);

        // 7. Products from user's Instagram folder

        // P1: PROMO GTA 6 - PS5 Official
        $p1 = Product::create([
            'category_id' => $catGames->id,
            'brand_id' => $brandRockstar->id,
            'name_ar' => 'PROMO Grand Theft Auto VI (GTA 6) - تفعيل رسمي PS5',
            'name_en' => 'PROMO GTA 6 - PlayStation 5 Official Activation',
            'slug' => 'promo-gta-6-playstation-5',
            'sku' => 'ALPHA-GTA6-PS5',
            'short_description_ar' => 'عرض برومو حصري: احجز GTA 6 لجهاز PS5 بتفعيل رسمي على حسابك وضمان كامل 100%.',
            'short_description_en' => 'Exclusive Promo: Grand Theft Auto VI for PS5 with official account activation and full warranty.',
            'description_ar' => 'عش تجربة الجيل الجديد الأسطورية مع GTA VI على PlayStation 5. عالم مفتوح ضخم، جرافيك غير مسبوق، طور أونلاين متطور، وتفعيل رسمي ومباشر على حسابك مع دعم فني 24/7.',
            'description_en' => 'Next-gen GTA VI experience on PlayStation 5 with official activation on your personal account.',
            'price' => 13750.00,
            'compare_price' => 16500.00,
            'cost_price' => 11000.00,
            'stock_quantity' => 40,
            'primary_image' => 'insta_27.png',
            'is_featured' => 1,
            'is_best_seller' => 1,
            'is_deal' => 1,
            'deal_price' => 13750.00,
            'views_count' => 6400,
            'sales_count' => 189,
            'status' => 'active'
        ]);

        // P2: GTA 6 Pre-Order PC/Steam
        Product::create([
            'category_id' => $catGames->id,
            'brand_id' => $brandRockstar->id,
            'name_ar' => 'Grand Theft Auto VI (GTA 6) - طلب مسبق رسمي PC / Steam',
            'name_en' => 'Grand Theft Auto VI (GTA 6) - Official PC Pre-Order',
            'slug' => 'grand-theft-auto-vi-gta-6-pc',
            'sku' => 'ALPHA-GTA6-PC',
            'short_description_ar' => 'الطلب المسبق الرسمي للعبة الأضخم في التاريخ GTA 6 للكمبيوتر مع مكافآت حصرية.',
            'short_description_en' => 'Official PC pre-order for GTA VI with pre-order perks and instant delivery upon release.',
            'description_ar' => 'حساب أصلي للعبة GTA 6 للكمبيوتر على منصة Steam / Rockstar مع ضمان مدى الحياة ودعم فني على مدار الساعة.',
            'description_en' => 'Complete PC edition of Grand Theft Auto VI with lifetime warranty and 24/7 support.',
            'price' => 13900.00,
            'compare_price' => 16500.00,
            'stock_quantity' => 50,
            'primary_image' => 'gta6.png',
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // P3: Red Dead Redemption 2
        Product::create([
            'category_id' => $catGames->id,
            'brand_id' => $brandRockstar->id,
            'name_ar' => 'Red Dead Redemption 2 - النسخة الكاملة + أونلاين',
            'name_en' => 'Red Dead Redemption 2 - Full Story & Online',
            'slug' => 'red-dead-redemption-2',
            'sku' => 'ALPHA-RDR2-FULL',
            'short_description_ar' => 'التحفة الفنية من روكستار: قصة آرثر مورغان كاملة مع Red Dead Online وضمان كامل.',
            'short_description_en' => 'The complete masterpiece by Rockstar Games with Story Mode & Online.',
            'description_ar' => 'حساب أصلي كامل الصلاحيات Full Access مع إمكانية تغيير كافة بيانات الحساب وضمان ممتد.',
            'description_en' => 'Original Full Access account with email change capability and full guarantee.',
            'price' => 3400.00,
            'compare_price' => 4800.00,
            'stock_quantity' => 45,
            'primary_image' => 'rdr2.png',
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // P4: Tom Clancy's Ghost Recon Breakpoint ONLINE
        Product::create([
            'category_id' => $catGames->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'Tom Clancy\'s Ghost Recon Breakpoint ONLINE (Steam Brazil)',
            'name_en' => 'Ghost Recon Breakpoint ONLINE Steam Brazil',
            'slug' => 'ghost-recon-breakpoint-online-steam',
            'sku' => 'ALPHA-GHOST-RECON',
            'short_description_ar' => 'تجربة أونلاين كاملة: اكتشف جزيرة أورورا وواجه أخطر التحديات مع أصدقائك بـ 1800 DA فقط.',
            'short_description_en' => 'Full online co-op tactical shooter on Steam Brazil region for only 1800 DA.',
            'description_ar' => 'اللعب أونلاين بدون أي قيود، حساب كامل Full Access مع تملك الحساب 100%، حساب ستيم أصلي وليست مشاركة عائلة، جاهز للعب مباشرة مع ضمان كامل على الحساب.',
            'description_en' => 'Full access original Steam account ready to play without restrictions with lifetime warranty.',
            'price' => 1800.00,
            'compare_price' => 3200.00,
            'stock_quantity' => 30,
            'primary_image' => 'insta_02.png',
            'is_featured' => 1,
            'is_best_seller' => 1,
            'status' => 'active'
        ]);

        // P5: Backrooms Escape Together ONLINE
        Product::create([
            'category_id' => $catGames->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'Backrooms: Escape Together ONLINE - حساب Steam أصلي أونلاين',
            'name_en' => 'Backrooms: Escape Together ONLINE Steam Account',
            'slug' => 'backrooms-escape-together-online',
            'sku' => 'ALPHA-BACKROOMS',
            'short_description_ar' => 'لعبة الرعب الجماعية: العب مع أصدقائك أونلاين في عالم الباكرومز المخيف بسعر 1000 DA فقط.',
            'short_description_en' => 'Co-op multiplayer horror game: play online with friends for only 1000 DA.',
            'description_ar' => 'مغامرة رعب وتعاون في عالم الـ Backrooms المخيف. ابحث عن المخرج واهرب مع أصدقائك! متوافق مع PC، تسليم فوري وتلقائي، وصول كامل للحساب Full Access، ضمان كامل وتحديثات مستمرة.',
            'description_en' => 'PC Steam original multiplayer account with instant delivery and full warranty.',
            'price' => 1000.00,
            'compare_price' => 1500.00,
            'stock_quantity' => 60,
            'primary_image' => 'insta_03.png',
            'is_featured' => 1,
            'is_best_seller' => 1,
            'status' => 'active'
        ]);

        // P6: RUST - Steam Online Account
        Product::create([
            'category_id' => $catGames->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'RUST - Steam Online Account (حساب ستيم أونلاين كامل جاهز للعب)',
            'name_en' => 'RUST - Steam Online Account Full Access',
            'slug' => 'rust-steam-online-account',
            'sku' => 'ALPHA-RUST-ONLINE',
            'short_description_ar' => 'حساب ستيم أصلي للعبة روست RUST للعب أونلاين على كافة السيرفرات الرسمية والمعدلة بـ 2800 DA.',
            'short_description_en' => 'Full access original Steam account for RUST with official server access for 2800 DA.',
            'description_ar' => 'Steam Online، Full Online Access، حساب رسمي 100%، تسليم فوري بعد الدفع، ضمان معتمد، جاهز للعب مباشرة ودعم فني متواصل 24/7.',
            'description_en' => 'Full online multiplayer Steam account ready to play with 24/7 support.',
            'price' => 2800.00,
            'compare_price' => 4500.00,
            'stock_quantity' => 25,
            'primary_image' => 'insta_14.png',
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // P7: Forza Horizon 6 Ultimate
        Product::create([
            'category_id' => $catGames->id,
            'brand_id' => $brandXbox->id,
            'name_ar' => 'Forza Horizon 6 - النسخة النهائية Ultimate VIP',
            'name_en' => 'Forza Horizon 6 - Ultimate VIP Edition',
            'slug' => 'forza-horizon-6-ultimate',
            'sku' => 'ALPHA-FORZA6-ULT',
            'short_description_ar' => 'النسخة الأقوى لعشاق السيارات: تشمل كافة التوسعات ومواسم السيارات وباقة الـ VIP.',
            'short_description_en' => 'The ultimate racing experience with all car passes and VIP bonuses.',
            'description_ar' => 'تتضمن حزمة السيارات الكاملة Car Pass، وتوسعات الخريطة، وحساب مفعل رسمياً مع إمكانية اللعب أونلاين على PC و Xbox.',
            'description_en' => 'Includes full car pass, VIP membership and lifetime activation.',
            'price' => 11500.00,
            'compare_price' => 14000.00,
            'stock_quantity' => 30,
            'primary_image' => 'forza_horizon_6.png',
            'status' => 'active'
        ]);

        // P8: EA SPORTS FC 27 - Pre-Order PC (Regular / Ultimate / Ultimate Plus)
        $pFC27PC = Product::create([
            'category_id' => $catFC27->id,
            'brand_id' => $brandEA->id,
            'name_ar' => 'EA SPORTS FC 27 (FIFA 27) - احجز نسختك الآن للكمبيوتر PC',
            'name_en' => 'EA SPORTS FC 27 - Pre-Order PC Edition',
            'slug' => 'ea-sports-fc-27-pre-order-pc',
            'sku' => 'ALPHA-FC27-PC-PRE',
            'short_description_ar' => 'الطلب المسبق الرسمي للعبة FC 27: العب قبل الجميع مع محتوى حصري ونقاط FC وطور Ultimate Team.',
            'short_description_en' => 'Official PC pre-order for FC 27 with early access and Ultimate Team perks.',
            'description_ar' => 'احجز نسختك للكمبيوتر، تتضمن: اللعب المبكر قبل الجميع، محتوى حصري للطلب المسبق، تجربة UT متكاملة، طور مسيرتك، وحركات واقعية HyperMotion V. متوفرة بنسخة عادية وألتمت وألتمت بلس.',
            'description_en' => 'Pre-order FC 27 PC version with early access and exclusive FUT points.',
            'price' => 13750.00,
            'compare_price' => 15500.00,
            'stock_quantity' => 50,
            'primary_image' => 'insta_21.png',
            'is_featured' => 1,
            'is_best_seller' => 1,
            'status' => 'active'
        ]);
        ProductAttribute::create([
            'product_id' => $pFC27PC->id,
            'name_ar' => 'النسخة (Edition)',
            'name_en' => 'Edition',
            'values_json' => json_encode(['Regular Version (13750 DA / 55$)', 'Ultimate Version (21250 DA / 85$)', 'Ultimate Plus Version (27250 DA / 109$)'])
        ]);

        // P9: EA SPORTS FC 27 - Steam Official Account
        Product::create([
            'category_id' => $catFC27->id,
            'brand_id' => $brandEA->id,
            'name_ar' => 'EA SPORTS FC 27 - حساب Steam رسمي محمل مسبقاً أونلاين',
            'name_en' => 'EA SPORTS FC 27 - Steam Official Account',
            'slug' => 'ea-sports-fc-27-steam-official',
            'sku' => 'ALPHA-FC27-STEAM',
            'short_description_ar' => 'حساب ستيم رسمي محمل مسبقاً باللعبة ويدعم الأونلاين كامل بـ 12000 DA (48$).',
            'short_description_en' => 'Official Steam account preloaded with FC 27 with full online multiplayer support.',
            'description_ar' => 'حساب ستيم رسمي وليس مفتاح تفعيل، اللعبة محملة مسبقاً على الحساب وتدعم طور الأونلاين بالكامل. ستتلقى بيانات الحساب فوراً (اسم المستخدم، كلمة المرور، البريد، وكود Steam Guard) مع ضمان كامل ودعم 24/7.',
            'description_en' => 'Original Steam account with full online support and immediate credentials delivery.',
            'price' => 12000.00,
            'compare_price' => 14500.00,
            'stock_quantity' => 40,
            'primary_image' => 'insta_22.png',
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // P10: FC 27 Ultimate Edition PS4
        Product::create([
            'category_id' => $catFC27->id,
            'brand_id' => $brandEA->id,
            'name_ar' => 'EA SPORTS FC 27 - نسخة الألتمت لجهاز PS4 (بحسابك الأساسي)',
            'name_en' => 'EA SPORTS FC 27 Ultimate Edition PS4',
            'slug' => 'ea-sports-fc-27-ultimate-ps4',
            'sku' => 'ALPHA-FC27-ULT-PS4',
            'short_description_ar' => 'نسخة الألتمت لجهاز PS4: العبها بحسابك الأساسي مع تحسينات Ultimate Team بـ 9000 DA (36$).',
            'short_description_en' => 'Ultimate Edition for PS4 playable on your primary account for 9000 DA.',
            'description_ar' => 'ادخل الملعب قبل الجميع والعبها بحسابك الأساسي Play It with Your Primary Account. أطوار لعبة متعددة ومطورة، محسن Ultimate Team، رسومات واقعية، تفعيل رسمي 100% وتسليم فوري.',
            'description_en' => 'Play FC 27 Ultimate Edition on your main PS4 account with lifetime warranty.',
            'price' => 9000.00,
            'compare_price' => 12000.00,
            'stock_quantity' => 35,
            'primary_image' => 'insta_23.png',
            'status' => 'active'
        ]);

        // P11: FC 27 Ultimate Edition PS5
        Product::create([
            'category_id' => $catFC27->id,
            'brand_id' => $brandEA->id,
            'name_ar' => 'EA SPORTS FC 27 - نسخة الألتمت لجهاز PS5 (نقاط FC + وصول مبكر)',
            'name_en' => 'EA SPORTS FC 27 Ultimate Edition PS5',
            'slug' => 'ea-sports-fc-27-ultimate-ps5',
            'sku' => 'ALPHA-FC27-ULT-PS5',
            'short_description_ar' => 'نسخة الألتمت لجهاز PS5: مزايا UT حصرية، وصول مبكر، حزم لاعبين ونقاط FC إضافية بـ 16250 DA (65$).',
            'short_description_en' => 'Ultimate Edition for PS5 with FC points, early access and player packs for 16250 DA.',
            'description_ar' => 'تجربة كرة قدم واقعية على الجيل الجديد برسومات مذهلة وأداء فائق على PS5. تتضمن نقاط FC إضافية وحزم لاعبين نادرة ووصول مبكر قبل الجميع.',
            'description_en' => 'Next-gen FC 27 Ultimate Edition with exclusive FC Points on PS5.',
            'price' => 16250.00,
            'compare_price' => 19500.00,
            'stock_quantity' => 30,
            'primary_image' => 'insta_24.png',
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // P12: FC 27 Standard Edition PS4
        Product::create([
            'category_id' => $catFC27->id,
            'brand_id' => $brandEA->id,
            'name_ar' => 'EA SPORTS FC 27 - Standard Edition PS4 (بحسابك الأساسي)',
            'name_en' => 'EA SPORTS FC 27 Standard Edition PS4',
            'slug' => 'ea-sports-fc-27-standard-ps4',
            'sku' => 'ALPHA-FC27-STD-PS4',
            'short_description_ar' => 'النسخة القياسية للعبة FC 27 على PS4: تلعبها بحسابك الأساسي بـ 7500 DA (32$).',
            'short_description_en' => 'Standard Edition for PS4 playable on your primary account for 7500 DA.',
            'description_ar' => 'منتج أصلي 100%، تفعيل سريع وضمان كامل، تلعب بحسابك الأساسي مع أحدث الانتقالات والفرق والرسومات المحسنة.',
            'description_en' => 'FC 27 Standard edition for PS4 with full online multiplayer on your main account.',
            'price' => 7500.00,
            'compare_price' => 9500.00,
            'stock_quantity' => 45,
            'primary_image' => 'insta_25.png',
            'status' => 'active'
        ]);

        // P13: FC 27 Standard Edition PS5
        Product::create([
            'category_id' => $catFC27->id,
            'brand_id' => $brandEA->id,
            'name_ar' => 'EA SPORTS FC 27 - Standard Edition PS5 (بحسابك الأساسي)',
            'name_en' => 'EA SPORTS FC 27 Standard Edition PS5',
            'slug' => 'ea-sports-fc-27-standard-ps5',
            'sku' => 'ALPHA-FC27-STD-PS5',
            'short_description_ar' => 'النسخة القياسية على PS5: جرافيك مذهل وتكتيكات ذكية تلعبها بحسابك الأساسي بـ 9000 DA (36$).',
            'short_description_en' => 'Standard Edition for PS5 playable on your primary account for 9000 DA.',
            'description_ar' => 'التجربة الأكثر واقعية في تاريخ السلسلة: أسلوب لعب أكثر واقعية، Ultimate Team محسن بالكامل، تكتيكات ذكية وأداء متطور على PS5. تلعب بحسابك الأساسي وتفعيل سريع خلال دقائق.',
            'description_en' => 'Next-gen standard edition for PS5 with instant activation on your personal account.',
            'price' => 9000.00,
            'compare_price' => 11500.00,
            'stock_quantity' => 40,
            'primary_image' => 'insta_26.png',
            'status' => 'active'
        ]);

        // P14: Steam Any Game Offline (500 DA)
        $pAnyGame = Product::create([
            'category_id' => $catSteamPacks->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'أي لعبة في ستيم تريدها (Steam Account OFFLINE) - سعر 500 DA فقط!',
            'name_en' => 'Any Steam Game You Want - OFFLINE Account',
            'slug' => 'any-steam-game-offline-account',
            'sku' => 'ALPHA-STEAM-500',
            'short_description_ar' => 'اختر أي لعبة في ستيم (Elden Ring, RDR2, Wukong, Cyberpunk, RE4, God of War...) وادفع 500 DA فقط!',
            'short_description_en' => 'Choose any game on Steam for only 500 DA! Instant delivery, no VPN needed.',
            'description_ar' => 'اختر أي لعبة تريدها على الإطلاق وادفع 500 DA فقط! تسليم فوري بعد الدفع، حساب أوفلاين 100% آمن، ألعاب أصلية 100%، لا يحتاج برامج أو VPN، دعم فني متواصل 24/7 وضمان كامل على الحساب.',
            'description_en' => 'Get any single AAA game of your choice on Steam offline for 500 DA with lifetime access.',
            'price' => 500.00,
            'compare_price' => 2000.00,
            'stock_quantity' => 100,
            'primary_image' => 'insta_04.png',
            'is_featured' => 1,
            'is_best_seller' => 1,
            'status' => 'active'
        ]);
        ProductAttribute::create([
            'product_id' => $pAnyGame->id,
            'name_ar' => 'اللعبة المطلوبة (Game Title)',
            'name_en' => 'Game Title',
            'values_json' => json_encode(['Elden Ring', 'Black Myth: Wukong', 'Red Dead Redemption 2', 'God of War Ragnarök', 'Hogwarts Legacy', 'Cyberpunk 2077', 'Spider-Man Remastered', 'Resident Evil 4 Remake', 'Forza Horizon 5', 'لعبة أخرى (اكتبها في الملاحظات)'])
        ]);

        // P15: Mega Steam Offline +45,000 Games
        Product::create([
            'category_id' => $catSteamPacks->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'حساب ستيم أوفلاين أسطوري يحتوي على أكثر من +45,000 PREMIUM GAMES',
            'name_en' => 'Mega Steam Account +45,000 Premium Games',
            'slug' => 'mega-steam-account-45000-games',
            'sku' => 'ALPHA-STEAM-45K',
            'short_description_ar' => 'أضخم مكتبة ألعاب في العالم: أكثر من 45 ألف لعبة مدفوعة بحساب ستيم رسمي واحد بـ 9500 DA (37$).',
            'short_description_en' => 'The largest game library with 45,000+ premium games on a single Steam account for 9500 DA.',
            'description_ar' => 'حساب ستيم رسمي 100% أوفلاين يضم كل الألعاب المدفوعة العالمية (Wukong, RDR2, GTA V, Elden Ring, God of War, Cyberpunk, Subnautica 2, Spider-Man...). تحديثات مستمرة، سهولة الاستخدام، خصوصية تامة وضمان 100%.',
            'description_en' => '45,000+ game mega Steam account with all genres, lifetime access and full warranty.',
            'price' => 9500.00,
            'compare_price' => 18000.00,
            'stock_quantity' => 30,
            'primary_image' => 'insta_20.png',
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // P16: Mega Steam Offline +17,000 PC Games
        Product::create([
            'category_id' => $catSteamPacks->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'حساب ستيم أوفلاين أسطوري +17,000 لعبة PC مدفوعة (مع هدية داخل الحساب)',
            'name_en' => 'Mega Steam Account +17,000 PC Games with Gift',
            'slug' => 'mega-steam-account-17000-games',
            'sku' => 'ALPHA-STEAM-17K-PVT',
            'short_description_ar' => 'أكبر مكتبة ألعاب في حساب واحد خاص غير مشترك +17000 لعبة PC مع هدية حصرية بـ 6000 DA.',
            'short_description_en' => 'Private non-shared Steam account with 17,000+ PC games and bonus gift for 6000 DA.',
            'description_ar' => 'حساب غير مشترك 100% آمن، بدون كراكات أو اختراق، حساب شخصي خاص بك، تحديثات مستمرة للألعاب، أمان كامل بدون حظر، مع هدية لعبة أو محتوى حصري داخل الحساب.',
            'description_en' => 'Private Steam account with 17k+ games, zero cracks, lifetime security and bonus gifts.',
            'price' => 6000.00,
            'compare_price' => 12000.00,
            'stock_quantity' => 20,
            'primary_image' => 'insta_06.png',
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // P17: Pack 17,000 Steam Shared
        Product::create([
            'category_id' => $catSteamPacks->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'بكج الأحلام +17,000 لعبة PC عبر STEAM (حسابات مشتركة - سعر ناري 5000 DA)',
            'name_en' => 'Dream Pack +17,000 PC Games Steam Shared',
            'slug' => 'dream-pack-17000-games-steam-shared',
            'sku' => 'ALPHA-STEAM-17K-SHR',
            'short_description_ar' => 'بكج الأحلام: يغطي كل أنواع الألعاب (قصة، تختيم، رعب، محاكاة، أكشن) بـ 5000 DA فقط.',
            'short_description_en' => 'Dream collection of 17,000 PC games across all genres on Steam for only 5000 DA.',
            'description_ar' => 'مجموعة مبنية على حسابات Steam مدفوعة، وكأنك فاتح متجر ألعاب ستيم كامل جاهز على حساباتك، بدون مواقع مشبوهة وبدون كراكات مع تسليم فوري وضمان مستمر.',
            'description_en' => 'Massive 17k Steam game library covering all genres with instant delivery.',
            'price' => 5000.00,
            'compare_price' => 8500.00,
            'stock_quantity' => 35,
            'primary_image' => 'insta_17.png',
            'status' => 'active'
        ]);

        // P18: Pack +4,400 Steam Games
        Product::create([
            'category_id' => $catSteamPacks->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'بكج الألعاب الأكبر بالتاريخ! +4,400 لعبة PC عبر Steam (عرض جزائري 2800 DA)',
            'name_en' => 'Largest Steam Pack +4,400 PC Games',
            'slug' => 'largest-steam-pack-4400-games',
            'sku' => 'ALPHA-STEAM-4400',
            'short_description_ar' => 'عرض جزائري سعر ناري: أكثر من 4400 لعبة PC مع تحديثات وإضافات مستمرة بدون أي رسوم بـ 2800 DA.',
            'short_description_en' => 'Algerian special deal: 4,400+ Steam games with free continuous updates for 2800 DA.',
            'description_ar' => 'يشمل أقوى العناوين: Elden Ring, RDR2, Cyberpunk 2077, God of War, Hogwarts Legacy, Resident Evil 4, Forza 5, The Last of Us. أمان وخصوصية 100% وضمان كامل على الحسابات.',
            'description_en' => 'Over 4,400 top PC games on Steam with full warranty and 100% privacy.',
            'price' => 2800.00,
            'compare_price' => 5000.00,
            'stock_quantity' => 40,
            'primary_image' => 'insta_18.png',
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // P19: Steam Pack +500 Games (2500 DA)
        Product::create([
            'category_id' => $catSteamPacks->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'حساب +500 لعبة STEAM للكمبيوتر (نخبة الألعاب في بكج واحد + هدية)',
            'name_en' => 'Steam PC +500 Games Elite Pack with Gift',
            'slug' => 'steam-pc-500-games-elite-pack',
            'sku' => 'ALPHA-STEAM-500-ELT',
            'short_description_ar' => 'نخبة الألعاب في بكج واحد: أكثر من 500 لعبة مدفوعة 100% مع هدية حصرية داخل الحساب بـ 2500 DA.',
            'short_description_en' => 'Elite 500+ game Steam pack with bonus gift and full access for 2500 DA.',
            'description_ar' => 'مكتبة ضخمة جاهزة للعب تضم أفضل الألعاب العالمية، التحميل من Steam نفسه، آمن 100% بدون حظر، تسليم فوري ودعم فني متواصل.',
            'description_en' => '500+ AAA games package on Steam with immediate delivery and warranty.',
            'price' => 2500.00,
            'compare_price' => 4500.00,
            'stock_quantity' => 30,
            'primary_image' => 'insta_07.png',
            'status' => 'active'
        ]);

        // P20: Steam Pack 500 Games (1500 DA Promo)
        Product::create([
            'category_id' => $catSteamPacks->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'بكج 500 لعبة STEAM PC (يغنيك عن شراء الألعاب - سعر ناري 1500 DA)',
            'name_en' => 'Steam 500 Games Package Promo 1500 DA',
            'slug' => 'steam-500-games-package-promo',
            'sku' => 'ALPHA-STEAM-500-PRM',
            'short_description_ar' => 'بكج يغنيك عن شراء الألعاب: 500 لعبة ستيم جاهزة على حساباتك بـ 1500 DA فقط.',
            'short_description_en' => '500 games package on Steam accounts ready to download for only 1500 DA.',
            'description_ar' => 'تختيم، رعب، محاكاة، أكشن، مغامرات، سباقات، رياضة. كأنك فاتح متجر ألعاب كامل بدون مواقع مشبوهة ولا كراكات نهائياً.',
            'description_en' => 'All game genres in one 500-game package for 1500 DA.',
            'price' => 1500.00,
            'compare_price' => 3500.00,
            'stock_quantity' => 50,
            'primary_image' => 'insta_16.png',
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // P21: Steam Offline Account 1500+ Games (Private)
        Product::create([
            'category_id' => $catSteamPacks->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'حساب ستيم OFFLINE خاص أكثر من 1500 لعبة (يشمل Forza 4, 5, 6 و Wukong)',
            'name_en' => 'Steam Offline Private Account 1500+ Games',
            'slug' => 'steam-offline-private-1500-games',
            'sku' => 'ALPHA-STEAM-1500-PVT',
            'short_description_ar' => 'حساب خاص بالكامل أوفلاين يضم أكثر من 1500 لعبة AAA حديثة بـ 1900 DA فقط.',
            'short_description_en' => 'Private offline account with 1500+ AAA games including Forza 4, 5, 6 and Wukong for 1900 DA.',
            'description_ar' => 'حساب خاص 100% آمن بدون مشاركة بدون تغيير، كل الألعاب متاحة، تحديثات مستمرة، ألعاب AAA حديثة وعالم مفتوح ومغامرات وأكشن وسباقات.',
            'description_en' => 'Private offline Steam account with 1500+ games and full warranty.',
            'price' => 1900.00,
            'compare_price' => 3800.00,
            'stock_quantity' => 35,
            'primary_image' => 'insta_28.png',
            'status' => 'active'
        ]);

        // P22: Steam Account 650+ Games (Full Info Change)
        Product::create([
            'category_id' => $catSteamPacks->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'حساب ستيم خاص أكثر من 650 لعبة (إمكانية تغيير كامل المعلومات)',
            'name_en' => 'Steam Account 650+ Games Full Access Editable',
            'slug' => 'steam-account-650-games-full-access',
            'sku' => 'ALPHA-STEAM-650-FA',
            'short_description_ar' => 'حساب خاص مضمون 100% يضم 650+ لعبة مع إمكانية تغيير الإيميل وكلمة السر بـ 1800 DA.',
            'short_description_en' => '100% guaranteed private account with 650+ games and full credentials change support.',
            'description_ar' => 'حساب خاص يمكنك تغيير كامل المعلومات، آمن 100%، تسليم فوري، مكتبة ضخمة تضم أحدث الألعاب والإصدارات مع تحميل وتثبيت من حسابك الشخصي.',
            'description_en' => 'Editable personal Steam account with 650+ games and lifetime support.',
            'price' => 1800.00,
            'compare_price' => 3500.00,
            'stock_quantity' => 30,
            'primary_image' => 'insta_29.png',
            'status' => 'active'
        ]);

        // P23: Steam Offline Account 120+ Games
        Product::create([
            'category_id' => $catSteamPacks->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'حساب ستيم أوفلاين +120 لعبة مدفوعة (مع هدية لعبة عشوائية + DLCs)',
            'name_en' => 'Steam Offline Account +120 Games with DLCs & Gift',
            'slug' => 'steam-offline-account-120-games',
            'sku' => 'ALPHA-STEAM-120',
            'short_description_ar' => 'أكثر من 120 لعبة من أقوى الألعاب العالمية مع لعبة عشوائية إضافية ومحتويات حصرية بـ 1500 DA.',
            'short_description_en' => '120+ top Steam games offline with bonus random game and DLCs for 1500 DA.',
            'description_ar' => 'يشمل Red Dead 2, Elden Ring, God of War, Cyberpunk 2077, Hogwarts Legacy, Forza 5, Spider-Man, Ghost of Tsushima, The Last of Us. استخدام مدى الحياة وتسليم فوري.',
            'description_en' => '120+ top tier games offline package with lifetime access.',
            'price' => 1500.00,
            'compare_price' => 2800.00,
            'stock_quantity' => 40,
            'primary_image' => 'insta_08.png',
            'status' => 'active'
        ]);

        // P24: Steam Offline Account 80+ Games
        Product::create([
            'category_id' => $catSteamPacks->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'حساب ستيم أوفلاين +80 لعبة مدفوعة (يشمل Wukong و Ghost of Tsushima)',
            'name_en' => 'Steam Offline Account 80+ Games Wukong & Ghost',
            'slug' => 'steam-offline-account-80-games',
            'sku' => 'ALPHA-STEAM-80',
            'short_description_ar' => 'أكثر من 80 لعبة مدفوعة تشمل Black Myth Wukong و Resident Evil 4 بـ 1000 DA فقط.',
            'short_description_en' => '80+ premium Steam games including Black Myth Wukong and RE4 for 1000 DA.',
            'description_ar' => 'حساب Steam أوفلاين رسمي، جاهز للتحميل واللعب فوراً، تسليم سريع وضمان كامل مع دعم فني 24/7.',
            'description_en' => '80+ games Steam offline account ready to play with 24/7 support.',
            'price' => 1000.00,
            'compare_price' => 2200.00,
            'stock_quantity' => 50,
            'primary_image' => 'insta_10.png',
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // P25: Minecraft Java + Bedrock LICENSE Lifetime
        Product::create([
            'category_id' => $catMinecraft->id,
            'brand_id' => $brandMojang->id,
            'name_ar' => 'Minecraft Java + Bedrock Edition [LICENSE] - حساب مدى الحياة رسمي',
            'name_en' => 'Minecraft Java + Bedrock LICENSE Lifetime Account',
            'slug' => 'minecraft-java-bedrock-license-lifetime',
            'sku' => 'ALPHA-MC-LIFETIME',
            'short_description_ar' => 'حساب مرخص رسمي 100% مدى الحياة: إيميل ووصول كامل Full Access ودعم كامل لسيرفر Hypixel بـ 3500 DA.',
            'short_description_en' => 'Official lifetime Minecraft Java + Bedrock license with full access and Hypixel support for 3500 DA.',
            'description_ar' => 'حساب رسمي مرخص يحتوي على النسختين Java Edition + Bedrock Edition. وصول كامل إلى الحساب والإيميل، إمكانية تغيير جميع البيانات (البريد، كلمة المرور، اسم اللاعب، وربط رقم الهاتف)، لعب أونلاين على جميع السيرفرات بدون حظر على Hypixel مع تسليم فوري بعد الشراء.',
            'description_en' => '100% official Minecraft license with full account transfer and no ban on Hypixel.',
            'price' => 3500.00,
            'compare_price' => 5500.00,
            'stock_quantity' => 40,
            'primary_image' => 'insta_11.png',
            'is_featured' => 1,
            'is_best_seller' => 1,
            'status' => 'active'
        ]);

        // P26: Minecraft Microsoft Store Activation (700 DA)
        Product::create([
            'category_id' => $catMinecraft->id,
            'brand_id' => $brandMojang->id,
            'name_ar' => 'Minecraft Bedrock + Java Edition (تفعيل رسمي على حساب Microsoft الخاص بك)',
            'name_en' => 'Minecraft Bedrock + Java Microsoft Store Activation',
            'slug' => 'minecraft-bedrock-java-microsoft-store',
            'sku' => 'ALPHA-MC-MS-700',
            'short_description_ar' => 'شراء رسمي يضاف إلى حسابك Microsoft مباشرة: لعب أونلاين وتفعيل سريع بـ 700 DA فقط.',
            'short_description_en' => 'Official purchase activated directly to your personal Microsoft account for 700 DA.',
            'description_ar' => 'شراء رسمي من Microsoft Store، يضاف إلى حسابك الشخصي To Your Account، متوافق مع PC، يدعم اللعب أونلاين وتفعيل سريع خلال دقائق مع ضمان كامل.',
            'description_en' => 'Original Microsoft Store Minecraft activation on your own personal account.',
            'price' => 700.00,
            'compare_price' => 1500.00,
            'stock_quantity' => 60,
            'primary_image' => 'insta_12.png',
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // P27: Minecraft iOS / iPadOS
        Product::create([
            'category_id' => $catMinecraft->id,
            'brand_id' => $brandMojang->id,
            'name_ar' => 'Minecraft iOS / iPadOS (نسخة أصلية للأيفون والأيباد على حسابك)',
            'name_en' => 'Minecraft iOS / iPadOS Official App',
            'slug' => 'minecraft-ios-ipados-official',
            'sku' => 'ALPHA-MC-IOS',
            'short_description_ar' => 'ماين كرافت الأصلية لأجهزة iPhone و iPad: تفعيل رسمي على حسابك بدون تسجيل دخول بـ 1300 DA.',
            'short_description_en' => 'Official Minecraft for iPhone and iPad on your personal Apple account for 1300 DA.',
            'description_ar' => 'رسمي 100% بدون تسجيل دخول، يضاف على حسابك الشخصي، الحساب خاص بك ويمكنك تغيير جميع المعلومات مع تفعيل تلقائي وسريع وضمان كامل.',
            'description_en' => 'Official Minecraft iOS/iPadOS game with full guarantee and automatic issue.',
            'price' => 1300.00,
            'compare_price' => 2200.00,
            'stock_quantity' => 30,
            'primary_image' => 'insta_13.png',
            'status' => 'active'
        ]);

        // P28: Adobe Creative Cloud (All Apps) 12 Months
        Product::create([
            'category_id' => $catSubscriptions->id,
            'brand_id' => $brandAdobe->id,
            'name_ar' => 'Adobe Creative Cloud (All Apps) + Acrobat Pro DC - اشتراك 12 شهراً',
            'name_en' => 'Adobe Creative Cloud All Apps + Acrobat 12M',
            'slug' => 'adobe-creative-cloud-all-apps-12m',
            'sku' => 'ALPHA-ADOBE-12M',
            'short_description_ar' => 'تخفيض 70%: كل تطبيقات أدوبي الاحترافية (+20 تطبيق) في اشتراك رسمي لمدة عام بـ 1650 DA.',
            'short_description_en' => '70% OFF: All 20+ Adobe Apps including Photoshop, Premiere, Illustrator for 12 months at 1650 DA.',
            'description_ar' => 'اشتراك رسمي لمدة عام كامل (12 شهراً) يشمل أكثر من 20 تطبيق احترافي (Photoshop, Illustrator, Premiere Pro, After Effects, InDesign, Audition, Lightroom Classic, Acrobat Pro DC...). تحميل مباشر من موقع أدوبي الرسمي، يعمل على ويندوز وماك مع رموز تلقائية واستلام فوري وضمان كامل.',
            'description_en' => 'Official 1-year Adobe Creative Cloud All Apps license with direct Adobe download and full warranty.',
            'price' => 1650.00,
            'compare_price' => 5500.00,
            'cost_price' => 1000.00,
            'stock_quantity' => 50,
            'primary_image' => 'insta_05.png',
            'is_featured' => 1,
            'is_best_seller' => 1,
            'is_deal' => 1,
            'deal_price' => 1650.00,
            'status' => 'active'
        ]);

        // P29: Xbox Game Pass Ultimate Console (Switch / Home)
        Product::create([
            'category_id' => $catSubscriptions->id,
            'brand_id' => $brandXbox->id,
            'name_ar' => 'اشتراك إكس بوكس جيم باس Xbox Game Pass Ultimate (يشمل EA Play و Ubisoft+)',
            'name_en' => 'Xbox Game Pass Ultimate Console Membership',
            'slug' => 'xbox-game-pass-ultimate-console',
            'sku' => 'ALPHA-XBOX-GPU-CON',
            'short_description_ar' => 'مكتبة ضخمة تضم أكثر من 450 لعبة على Xbox One / Series S|X مع EA Play و Ubisoft+ أونلاين بـ 4200 DA.',
            'short_description_en' => 'Access 450+ console games including EA Play and Ubisoft+ with online multiplayer.',
            'description_ar' => 'اشتراك Game Pass Ultimate خاص بـ Xbox One / Series X|S بنظام Switch / Home بأسعار منخفضة جداً. يشمل إمكانية اللعب أونلاين، مكتبة EA Play، مكتبة Ubisoft+، تسليم سريع وحسابات مضمونة 100%.',
            'description_en' => 'Xbox Game Pass Ultimate console membership with 450+ titles, EA Play & full multiplayer.',
            'price' => 4200.00,
            'compare_price' => 6500.00,
            'stock_quantity' => 40,
            'primary_image' => 'insta_30.png',
            'is_featured' => 1,
            'status' => 'active'
        ]);

        // P30: Netflix Premium 4K
        Product::create([
            'category_id' => $catSubscriptions->id,
            'brand_id' => $brandNetflix->id,
            'name_ar' => 'اشتراك نتفلكس بريميوم Netflix Premium 4K Ultra HD (ضمان كامل)',
            'name_en' => 'Netflix Premium 4K Ultra HD Membership',
            'slug' => 'netflix-premium-4k-ultra-hd',
            'sku' => 'ALPHA-NETFLIX-4K',
            'short_description_ar' => 'حساب Netflix رسمي بدقة 4K Ultra HD مع دعم كامل للشاشات الذكية والجوال بدون انقطاع.',
            'short_description_en' => 'Official 4K Netflix Premium account with lifetime guarantee and multiple profiles.',
            'description_ar' => 'استمتع بآلاف الأفلام والمسلسلات الحصرية بأعلى دقة 4K مع صوت محيطي Dolby Atmos. الحساب رسمي ومفعل بدون انقطاع مع ضمان كامل طوال فترة الاشتراك.',
            'description_en' => 'Netflix 4K Ultra HD subscription with zero interruptions and full warranty.',
            'price' => 1200.00,
            'compare_price' => 1800.00,
            'stock_quantity' => 50,
            'primary_image' => 'netflix.jpg',
            'is_featured' => 1,
            'is_best_seller' => 1,
            'status' => 'active'
        ]);

        // P31: PlayStation Plus Deluxe / Extra
        Product::create([
            'category_id' => $catSubscriptions->id,
            'brand_id' => $brandPlaystation->id,
            'name_ar' => 'اشتراك بلايستيشن بلس PlayStation Plus Deluxe / Extra',
            'name_en' => 'PlayStation Plus Deluxe / Extra Membership',
            'slug' => 'playstation-plus-deluxe-extra',
            'sku' => 'ALPHA-PS-PLUS',
            'short_description_ar' => 'اشتراك PS Plus Deluxe يمنحك اللعب أونلاين مع مكتبة مئات ألعاب PS4 و PS5 الكلاسيكية والحديثة.',
            'short_description_en' => 'PS Plus Deluxe tier with online multiplayer and 400+ blockbuster games catalog.',
            'description_ar' => 'تفعيل رسمي على جهازك PS4 / PS5 مع إمكانية اللعب بحسابك الشخصي الأساسي وضمان كامل طوال مدة الاشتراك.',
            'description_en' => 'Official PlayStation Plus membership with full catalog access and cloud storage.',
            'price' => 9500.00,
            'compare_price' => 13500.00,
            'stock_quantity' => 25,
            'primary_image' => 'ps_plus.jpg',
            'status' => 'active'
        ]);

        // P32: Steam Wallet 50$
        Product::create([
            'category_id' => $catGiftCards->id,
            'brand_id' => $brandSteam->id,
            'name_ar' => 'بطاقة شحن رصيد ستيم Steam Wallet Gift Card 50$',
            'name_en' => 'Steam Wallet 50$ Digital Gift Card',
            'slug' => 'steam-wallet-card-50-usd',
            'sku' => 'ALPHA-STEAM-50USD',
            'short_description_ar' => 'كود شحن رصيد Steam أصلي فئة 50 دولار صالح لكافة الحسابات العالمية مع شحن فوري.',
            'short_description_en' => 'Official 50$ Steam digital wallet voucher code with instant delivery.',
            'description_ar' => 'كود رسمي 100% لشحن حسابك في Steam وشراء أي لعبة أو سكنات Counter-Strike و Dota 2 مباشرة.',
            'description_en' => 'Instant redeemable 50$ digital code for Steam store and market.',
            'price' => 7200.00,
            'compare_price' => 8000.00,
            'stock_quantity' => 100,
            'primary_image' => 'steam_card.jpg',
            'status' => 'active'
        ]);

        // P33: Spotify Premium
        Product::create([
            'category_id' => $catSubscriptions->id,
            'brand_id' => $brandSpotify->id,
            'name_ar' => 'اشتراك سبوتيفاي بريميوم Spotify Premium (بدون إعلانات وأوفلاين)',
            'name_en' => 'Spotify Premium Individual Membership',
            'slug' => 'spotify-premium-individual',
            'sku' => 'ALPHA-SPOTIFY-PRM',
            'short_description_ar' => 'استمع لملايين الأغاني والبودكاست بأعلى جودة صوت بدون أي إعلانات مع إمكانية التحميل أوفلاين.',
            'short_description_en' => 'Ad-free Spotify Premium with offline downloads and high quality audio.',
            'description_ar' => 'ترقية رسمية لحسابك الشخصي على Spotify بدون الحاجة لتغيير الحساب أو فقدان قوائم التشغيل الخاصة بك، مع ضمان كامل.',
            'description_en' => 'Personal account Spotify upgrade with offline playback and highest audio fidelity.',
            'price' => 850.00,
            'compare_price' => 1400.00,
            'stock_quantity' => 80,
            'primary_image' => 'spotify.jpg',
            'status' => 'active'
        ]);

        // 8. Coupons
        Coupon::create([
            'code' => 'ALPHA10',
            'type' => 'percentage',
            'value' => 10.0,
            'min_spend' => 2000.0,
            'max_discount' => 1500.0,
            'usage_limit' => 500,
            'usage_count' => 84,
            'start_date' => date('Y-m-d 00:00:00'),
            'end_date' => date('Y-m-d 23:59:59', strtotime('+60 days')),
            'status' => 'active'
        ]);
        Coupon::create([
            'code' => 'GAMER500',
            'type' => 'fixed',
            'value' => 500.0,
            'min_spend' => 3000.0,
            'usage_limit' => 200,
            'usage_count' => 45,
            'start_date' => date('Y-m-d 00:00:00'),
            'end_date' => date('Y-m-d 23:59:59', strtotime('+45 days')),
            'status' => 'active'
        ]);

        // 9. Shipping / Instant Delivery Methods
        ShippingMethod::create([
            'name_ar' => 'تسليم فوري عبر واتساب WhatsApp (مجاناً)',
            'name_en' => 'Instant WhatsApp Delivery (Free)',
            'cost' => 0.0,
            'estimated_days' => 'فوري (خلال 5 دقائق)',
            'status' => 'active'
        ]);
        ShippingMethod::create([
            'name_ar' => 'إرسال مباشر عبر البريد الإلكتروني (مجاناً)',
            'name_en' => 'Instant Email Dispatch (Free)',
            'cost' => 0.0,
            'estimated_days' => 'فوري (خلال دقيقة)',
            'status' => 'active'
        ]);

        // 10. Authentic Customer Reviews (from User's Poster insta_01.png)
        Review::create([
            'product_id' => $p1->id,
            'user_id' => $customer->id,
            'author_name' => 'يوسف س.',
            'author_email' => 'youssef@customer.dz',
            'rating' => 5,
            'comment' => 'تعامل ممتاز وسريع، الحساب وصلني شغال 100%، أنصح بالتعامل بقوة 🔥 (EA Sports)',
            'is_approved' => 1
        ]);
        Review::create([
            'product_id' => $p1->id,
            'user_id' => $customer->id,
            'author_name' => 'أمين ب.',
            'author_email' => 'amine@customer.dz',
            'rating' => 5,
            'comment' => 'ثقة 100% ألعاب أصلية وسعر مناسب بالدينار والدعم متاح طول الوقت 👍',
            'is_approved' => 1
        ]);
        Review::create([
            'product_id' => $p1->id,
            'user_id' => $customer->id,
            'author_name' => 'محمد ر.',
            'author_email' => 'mohamed@customer.dz',
            'rating' => 5,
            'comment' => 'أول مرة نتعامل معاكم والله ما راح تكون الأخيرة، خدمة سريعة ورائعة جداً ❤️',
            'is_approved' => 1
        ]);
        Review::create([
            'product_id' => $p1->id,
            'user_id' => $customer->id,
            'author_name' => 'إسحاق ن.',
            'author_email' => 'ishak@customer.dz',
            'rating' => 5,
            'comment' => 'حساب Steam رهيب ولعبة Backrooms شغالة أونلاين مع صحابي بدون مشاكل، شكراً لكم 🔥',
            'is_approved' => 1
        ]);
        Review::create([
            'product_id' => $p1->id,
            'user_id' => $customer->id,
            'author_name' => 'ريان خ.',
            'author_email' => 'rayan@customer.dz',
            'rating' => 5,
            'comment' => 'سعر مناسب جداً مقارنة بالمواقع وتسليم فوري والحساب عليه ضمان كامل، أنصح الجميع 👌',
            'is_approved' => 1
        ]);
        Review::create([
            'product_id' => $p1->id,
            'user_id' => $customer->id,
            'author_name' => 'سليم ذ.',
            'author_email' => 'salim@customer.dz',
            'rating' => 5,
            'comment' => 'دعم فني سريع ومتعاونين ويجاوبون على كل الأسئلة ✅ تجربة ممتازة.',
            'is_approved' => 1
        ]);

        // 11. General Store Settings
        $settings = [
            'site_name' => 'Alpha Gaming Shop',
            'site_description' => 'المتجر الجزائري الأول لشراء وتفعيل الألعاب الأصلية، حسابات Steam وبكجات الألعاب، واشتراكات Netflix و Game Pass و Adobe بأفضل الأسعار بالدينار الجزائري مع تسليم فوري وضمان كامل.',
            'site_phone' => '+213 555 12 34 56',
            'site_email' => 'contact@alphagamingshop.dz',
            'site_address' => 'الجزائر العاصمة، الجزائر (Algeria)',
            'site_currency' => 'DZD',
            'site_logo' => 'assets/images/logo.png',
            'site_favicon' => 'uploads/favicon.png',
            'instagram_url' => 'https://www.instagram.com/alpha_gaming_shop/',
            'whatsapp_number' => '+213555123456',
            'payment_baridimob' => '00799999000123456789 (MENOUER MOHAMED EL AMINE)',
            'payment_ccp' => '12345678 Clé 90 (الجزائر)',
            'payment_paypal' => 'active',
            'payment_cash_on_delivery' => 'inactive',
        ];
        foreach ($settings as $k => $v) {
            Setting::set($k, $v);
        }

        // 12. Theme Settings (Cyan / Neon Gamer Theme)
        ThemeSetting::set('primary_color', '#00d2ff');
        ThemeSetting::set('secondary_color', '#00ff88');
        ThemeSetting::set('accent_color', '#ffaa00');
        ThemeSetting::set('dark_bg_color', '#0b0f19');
        ThemeSetting::set('font_family', 'Cairo');
        ThemeSetting::set('hero_banner_image', 'insta_19.png');
        ThemeSetting::set('hero_title_ar', 'Alpha Gaming Shop - كل ما يحتاجه اللاعب في مكان واحد');
        ThemeSetting::set('hero_subtitle_ar', 'أقوى ألعاب PC و PlayStation و Xbox، وبكجات Steam الأسطورية، واشتراكات الترفيه بأسعار حصرية بالدينار الجزائري وتسليم فوري!');

        // 13. Static Pages
        Page::create([
            'title_ar' => 'من نحن - Alpha Gaming Shop',
            'title_en' => 'About Alpha Gaming Shop',
            'slug' => 'about-us',
            'content_ar' => '<h3>مرحباً بكم في Alpha Gaming Shop</h3><p>المتجر الجزائري الرائد في توفير الحسابات الأصلية للألعاب، بكجات Steam الضخمة، والاشتراكات الرقمية (Netflix, Game Pass, Adobe, Spotify) بأفضل الأسعار مع ضمان كامل ودعم فني على مدار الساعة.</p>',
            'content_en' => 'Welcome to Alpha Gaming Shop, Algeria\'s premier destination for original gaming accounts and digital subscriptions.',
            'status' => 'active'
        ]);
        Page::create([
            'title_ar' => 'طرق الدفع والتسليم في الجزائر',
            'title_en' => 'Payment & Delivery Methods in Algeria',
            'slug' => 'payment-methods',
            'content_ar' => '<h3>طرق الدفع المعتمدة:</h3><ul><li><strong>تطبيق بريدي موب (BaridiMob):</strong> تحويل مباشر وسريع.</li><li><strong>الحساب البريدي الجاري (CCP):</strong> عبر أي مركز بريد في الجزائر.</li><li><strong>PayPal والبطاقات الدولية:</strong> للمقيمين والمهتمين بالدفع الإلكتروني.</li></ul><h3>طريقة التسليم:</h3><p>يتم تسليم الحسابات والأكواد فوراً عبر رسالة WhatsApp والبريد الإلكتروني مع كافة بيانات التفعيل وضمان كامل.</p>',
            'content_en' => 'We accept BaridiMob, CCP, and PayPal with instant delivery via WhatsApp and Email.',
            'status' => 'active'
        ]);
    }
}
