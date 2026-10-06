<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Translator;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\Compare;
use App\Models\Deal;
use App\Models\Page;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\CurrencyService;
use App\Services\PaymentService;
use App\Services\ShippingService;
use App\Services\SeoService;

class StoreController {
    public function index(Request $request) {
        $banners = Deal::where('status', 'active')->orderBy('id', 'DESC')->get();
        $categories = Category::where('status', 'active')->where('parent_id', null)->orderBy('sort_order', 'ASC')->get();
        $featuredCategories = Category::where('status', 'active')->where('is_featured', 1)->orderBy('sort_order', 'ASC')->limit(8)->get();
        
        $bestSellers = Product::where('status', 'active')->orderByDesc('sales_count')->limit(8)->get();
        $deals = Product::where('status', 'active')->where('is_deal', 1)->limit(6)->get();
        $brands = Brand::where('status', 'active')->limit(12)->get();
        $platforms = self::getHomePlatforms();

        return view('storefront.home', compact(
            'banners', 'categories', 'featuredCategories',
            'bestSellers', 'deals', 'brands', 'platforms'
        ));
    }

    public static function getHomePlatforms(): array {
        $customJson = \App\Models\ThemeSetting::get('home_platforms_json');
        if (!empty($customJson)) {
            $decoded = json_decode($customJson, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return array_map(function($p) {
                    if (isset($p['url']) && !str_starts_with($p['url'], 'http://') && !str_starts_with($p['url'], 'https://')) {
                        $p['url'] = url($p['url']);
                    }
                    return $p;
                }, $decoded);
            }
        }

        return [
            [
                'key' => 'xbox',
                'name_ar' => 'إكس بوكس (Xbox)',
                'name_en' => 'Xbox',
                'badge_ar' => 'كونسول & PC',
                'badge_en' => 'CONSOLE & PC',
                'tagline_ar' => 'ألعاب، حسابات واشتراكات Game Pass',
                'tagline_en' => 'Games, Accounts & Subscriptions',
                'image' => 'assets/images/platforms/platform_xbox.webp',
                'accent_color' => '#107c10',
                'icon' => '🎮',
                'url' => url('brand/xbox')
            ],
            [
                'key' => 'playstation',
                'name_ar' => 'بلايستيشن (PlayStation)',
                'name_en' => 'PlayStation',
                'badge_ar' => 'PS5 & PS4',
                'badge_en' => 'PS5 & PS4',
                'tagline_ar' => 'تفعيلات رسمية، ألعاب واشتراكات Plus',
                'tagline_en' => 'PS5/PS4 Activations & PS Plus',
                'image' => 'assets/images/platforms/platform_playstation.webp',
                'accent_color' => '#0070d1',
                'icon' => '🕹️',
                'url' => url('brand/playstation')
            ],
            [
                'key' => 'steam',
                'name_ar' => 'ستيم (Steam)',
                'name_en' => 'Steam',
                'badge_ar' => 'بكجات & أوفلاين',
                'badge_en' => 'PACKS & OFFLINE',
                'tagline_ar' => 'أضخم بكجات الألعاب وحسابات مفعلة',
                'tagline_en' => 'Massive Packs & Verified Accounts',
                'image' => 'assets/images/platforms/platform_steam.webp',
                'accent_color' => '#00d2ff',
                'icon' => '🚀',
                'url' => url('category/steam-mega-packs')
            ],
            [
                'key' => 'pc_gaming',
                'name_ar' => 'ألعاب الكمبيوتر (PC Gaming)',
                'name_en' => 'PC Gaming',
                'badge_ar' => 'ألعاب AAA أصلية',
                'badge_en' => 'AAA ORIGINAL GAMES',
                'tagline_ar' => 'أقوى العناوين العالمية جاهزة للتحميل',
                'tagline_en' => 'Top AAA PC Games Ready to Play',
                'image' => 'assets/images/platforms/platform_pc_gaming.webp',
                'accent_color' => '#e11d48',
                'icon' => '💻',
                'url' => url('category/pc-console-games')
            ],
            [
                'key' => 'ea_sports_fc',
                'name_ar' => 'سلسلة EA SPORTS FC',
                'name_en' => 'EA SPORTS FC',
                'badge_ar' => 'FC 27 / FC 26',
                'badge_en' => 'FC 27 & FC 26',
                'tagline_ar' => 'أحدث إصدارات كرة القدم وتفعيلات أونلاين',
                'tagline_en' => 'Football Editions & Online Access',
                'image' => 'assets/images/platforms/platform_ea_sports_fc.webp',
                'accent_color' => '#00ff88',
                'icon' => '⚽',
                'url' => url('category/ea-sports-fc')
            ],
            [
                'key' => 'minecraft',
                'name_ar' => 'ماين كرافت (Minecraft)',
                'name_en' => 'Minecraft Official',
                'badge_ar' => 'حسابات أصلية',
                'badge_en' => 'OFFICIAL LICENSE',
                'tagline_ar' => 'Java + Bedrock على حسابك مدى الحياة',
                'tagline_en' => 'Java & Bedrock Lifetime License',
                'image' => 'assets/images/platforms/platform_minecraft.webp',
                'accent_color' => '#22c55e',
                'icon' => '⛏️',
                'url' => url('category/minecraft')
            ],
            [
                'key' => 'ubisoft',
                'name_ar' => 'يوبي سوفت (Ubisoft)',
                'name_en' => 'Ubisoft',
                'badge_ar' => 'أساطير المغامرات',
                'badge_en' => 'ADVENTURE LEGENDS',
                'tagline_ar' => 'Assassin\'s Creed، Ghost Recon والمزيد',
                'tagline_en' => 'Assassin\'s Creed, Ghost Recon & More',
                'image' => 'assets/images/platforms/platform_ubisoft.webp',
                'accent_color' => '#0ea5e9',
                'icon' => '⚔️',
                'url' => url('brand/ubisoft')
            ],
            [
                'key' => 'rockstar_games',
                'name_ar' => 'روكستار جيمز (Rockstar)',
                'name_en' => 'Rockstar Games',
                'badge_ar' => 'عالم مفتوح',
                'badge_en' => 'OPEN WORLD HITS',
                'tagline_ar' => 'GTA 6، Red Dead Redemption 2',
                'tagline_en' => 'GTA 6 & Red Dead Redemption 2',
                'image' => 'assets/images/platforms/platform_rockstar_games.webp',
                'accent_color' => '#f59e0b',
                'icon' => '⭐',
                'url' => url('brand/rockstar-games')
            ],
            [
                'key' => 'game_pass',
                'name_ar' => 'إكس بوكس جيم باس (Game Pass)',
                'name_en' => 'Xbox Game Pass',
                'badge_ar' => '+450 لعبة مفتوحة',
                'badge_en' => '450+ GAMES INCLUDED',
                'tagline_ar' => 'اشتراكات التميت وسويتش مع EA Play',
                'tagline_en' => 'Ultimate & Switch with EA Play',
                'image' => 'assets/images/platforms/platform_game_pass.webp',
                'accent_color' => '#107c10',
                'icon' => '🟢',
                'url' => url('shop?q=Game+Pass')
            ],
            [
                'key' => 'software',
                'name_ar' => 'برامج وتطبيقات (Software)',
                'name_en' => 'Software & Design',
                'badge_ar' => 'أدوبي وتطبيقات رسمية',
                'badge_en' => 'ADOBE & PRO APPS',
                'tagline_ar' => 'Adobe Creative Cloud وتطبيقات الإنتاجية',
                'tagline_en' => 'Adobe Creative Cloud Suite',
                'image' => 'assets/images/platforms/platform_software.webp',
                'accent_color' => '#8b5cf6',
                'icon' => '🎨',
                'url' => url('brand/adobe')
            ],
            [
                'key' => 'iptv',
                'name_ar' => 'اشتراكات IPTV (Promax 4K)',
                'name_en' => 'Promax 4K IPTV',
                'badge_ar' => 'بث مباشر بدون تقطيع',
                'badge_en' => 'LIVE 4K STREAMING',
                'tagline_ar' => 'قنوات beIN الرياضية وأضخم مكتبة أفلام',
                'tagline_en' => 'Global Sports, Movies & Series',
                'image' => 'assets/images/platforms/platform_iptv.webp',
                'accent_color' => '#06b6d4',
                'icon' => '📺',
                'url' => url('product/promax-4k-ott-iptv-12m')
            ],
            [
                'key' => 'subscriptions',
                'name_ar' => 'اشتراكات الترفيه و AI',
                'name_en' => 'Entertainment & AI',
                'badge_ar' => 'تطبيقات مميزة',
                'badge_en' => 'PREMIUM SERVICES',
                'tagline_ar' => 'Netflix, Spotify, ChatGPT, Gemini, VPN',
                'tagline_en' => 'Netflix, Spotify, ChatGPT, Gemini, VPN',
                'image' => 'assets/images/platforms/platform_subscriptions.webp',
                'accent_color' => '#e50914',
                'icon' => '🍿',
                'url' => url('category/subscriptions')
            ]
        ];
    }


    public function catalog(Request $request) {
        $query = Product::where('status', 'active');

        // Search Keyword
        $q = $request->getQuery('q');
        if (!empty($q)) {
            $query->whereRaw("(name_ar LIKE ? OR name_en LIKE ? OR sku LIKE ? OR short_description_ar LIKE ?)", [
                "%{$q}%", "%{$q}%", "%{$q}%", "%{$q}%"
            ]);
        }

        // Category Filter
        $categorySlug = $request->getQuery('category');
        $activeCategory = null;
        if (!empty($categorySlug)) {
            $activeCategory = Category::where('slug', $categorySlug)->first();
            if ($activeCategory) {
                $childIds = array_map(fn($c) => $c->id, $activeCategory->children());
                $allCatIds = array_merge([$activeCategory->id], $childIds);
                $query->whereIn('category_id', $allCatIds);
            }
        }

        // Brand Filter
        $brandSlug = $request->getQuery('brand');
        $activeBrand = null;
        if (!empty($brandSlug)) {
            $activeBrand = Brand::where('slug', $brandSlug)->first();
            if ($activeBrand) {
                $query->where('brand_id', $activeBrand->id);
            }
        }

        // Price Range Filter
        $minPrice = $request->getQuery('min_price');
        $maxPrice = $request->getQuery('max_price');
        if ($minPrice !== null && is_numeric($minPrice)) {
            $query->where('price', '>=', (float)$minPrice);
        }
        if ($maxPrice !== null && is_numeric($maxPrice)) {
            $query->where('price', '<=', (float)$maxPrice);
        }

        // Stock Filter
        if ($request->getQuery('in_stock') === '1') {
            $query->where('stock_quantity', '>', 0);
        }

        // Deals Filter
        if ($request->getQuery('deals_only') === '1') {
            $query->where('is_deal', 1);
        }

        // Sorting
        $sort = $request->getQuery('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'ASC');
                break;
            case 'price_high':
                $query->orderBy('price', 'DESC');
                break;
            case 'popular':
            case 'best_seller':
                $query->orderByDesc('sales_count');
                break;
            case 'rating':
                $query->orderByDesc('views_count');
                break;
            case 'newest':
            default:
                $query->orderByDesc('id');
                break;
        }

        $viewMode = $request->getQuery('view', 'grid'); // 'grid' or 'list'
        $products = $query->paginate(12);

        $categories = Category::where('status', 'active')->where('parent_id', null)->orderBy('sort_order', 'ASC')->get();
        $brands = Brand::where('status', 'active')->get();

        return view('storefront.catalog', compact(
            'products', 'categories', 'brands', 'activeCategory', 'activeBrand', 'sort', 'viewMode', 'q', 'minPrice', 'maxPrice'
        ));
    }

    public function category(Request $request, string $slug) {
        $category = Category::where('slug', $slug)->first();
        if (!$category) {
            return Response::html(view('storefront.404'), 404);
        }

        $childIds = array_map(fn($c) => $c->id, $category->children());
        $allCatIds = array_merge([$category->id], $childIds);

        $products = Product::where('status', 'active')->whereIn('category_id', $allCatIds)->orderByDesc('id')->paginate(12);
        $categories = Category::where('status', 'active')->where('parent_id', null)->get();
        $brands = Brand::where('status', 'active')->get();
        $activeCategory = $category;

        return view('storefront.catalog', compact('products', 'categories', 'brands', 'activeCategory'));
    }

    public function brand(Request $request, string $slug) {
        $brand = Brand::where('slug', $slug)->first();
        if (!$brand) {
            return Response::html(view('storefront.404'), 404);
        }

        $products = Product::where('status', 'active')->where('brand_id', $brand->id)->orderByDesc('id')->paginate(12);
        $categories = Category::where('status', 'active')->where('parent_id', null)->get();
        $brands = Brand::where('status', 'active')->get();
        $activeBrand = $brand;

        return view('storefront.catalog', compact('products', 'categories', 'brands', 'activeBrand'));
    }

    public function product(Request $request, string $slug) {
        $product = Product::where('slug', $slug)->first();
        if (!$product) {
            return Response::html(view('storefront.404'), 404);
        }

        // Increment Views
        $product->update(['views_count' => (int)$product->views_count + 1]);

        $images = $product->images();
        $attributes = $product->attributes();
        $reviews = $product->reviews();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();

        $schemaJson = SeoService::generateProductSchema($product);

        return view('storefront.product', compact('product', 'images', 'attributes', 'reviews', 'relatedProducts', 'schemaJson'));
    }

    public function storeReview(Request $request, string $slug) {
        $product = Product::where('slug', $slug)->first();
        if (!$product) {
            return redirect(url('/'));
        }

        $data = $request->validate([
            'rating' => 'required|numeric',
            'comment' => 'required|min:3'
        ]);

        $rawAuthorName = Auth::getInstance()->check() ? Auth::getInstance()->user()->name : ($request->input('name') ?: 'عميل');
        $authorName = htmlspecialchars(strip_tags(trim((string)$rawAuthorName)), ENT_QUOTES, 'UTF-8');

        $rawEmail = Auth::getInstance()->check() ? Auth::getInstance()->user()->email : ($request->input('email') ?: 'guest@store.com');
        $authorEmail = filter_var($rawEmail, FILTER_VALIDATE_EMAIL) ? $rawEmail : 'guest@store.com';

        Review::create([
            'product_id' => $product->id,
            'user_id' => Auth::getInstance()->id(),
            'author_name' => mb_substr($authorName, 0, 100),
            'author_email' => mb_substr($authorEmail, 0, 150),
            'rating' => max(1, min(5, (int)$data['rating'])),
            'comment' => htmlspecialchars(strip_tags(trim((string)$data['comment'])), ENT_QUOTES, 'UTF-8'),
            'is_verified_purchase' => Auth::getInstance()->check() ? 1 : 0,
            'is_approved' => 1 // auto-approve or moderate based on admin setting
        ]);

        Session::flash('success', __('messages.review_submitted_success'));
        return redirect(url('product/' . $slug . '#reviews'));
    }

    public function cart(Request $request) {
        $cart = CartService::getCart();
        $subtotal = CartService::getSubtotal();
        $discount = CartService::getDiscountAmount();
        $tax = CartService::getTaxAmount();
        $total = CartService::getTotal();
        $coupon = CartService::getAppliedCoupon();
        $shippingMethods = ShippingService::getAvailableMethods($subtotal);

        return view('storefront.cart', compact('cart', 'subtotal', 'discount', 'tax', 'total', 'coupon', 'shippingMethods'));
    }

    public function checkout(Request $request) {
        $cart = CartService::getCart();
        if (empty($cart)) {
            Session::flash('error', __('messages.cart_is_empty'));
            return redirect(url('cart'));
        }

        $subtotal = CartService::getSubtotal();
        $discount = CartService::getDiscountAmount();
        $tax = CartService::getTaxAmount();
        $shippingMethods = ShippingService::getAvailableMethods($subtotal);
        $defaultShippingCost = $shippingMethods[0]['cost'] ?? 0.0;
        $total = CartService::getTotal($defaultShippingCost);

        $paymentMethods = PaymentService::getActiveMethods();
        $user = Auth::getInstance()->user();

        return view('storefront.checkout', compact(
            'cart', 'subtotal', 'discount', 'tax', 'shippingMethods', 'total', 'paymentMethods', 'user'
        ));
    }

    public function placeOrder(Request $request) {
        $data = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'phone' => 'required|min:8',
            'address' => 'required|min:5',
            'city' => 'required',
            'payment_method' => 'required'
        ]);

        $shippingMethods = ShippingService::getAvailableMethods(CartService::getSubtotal());
        $selectedShippingCode = $request->input('shipping_method', 'standard');
        $shippingCost = 0.0;
        $shippingName = 'Express Home Delivery';

        foreach ($shippingMethods as $sm) {
            if ($sm['code'] === $selectedShippingCode) {
                $shippingCost = $sm['cost'];
                $shippingName = is_rtl() ? $sm['name_ar'] : $sm['name_en'];
                break;
            }
        }

        $data['shipping_cost'] = $shippingCost;
        $data['shipping_method_name'] = $shippingName;
        $data['country'] = $request->input('country', 'Algeria');
        $data['postal_code'] = $request->input('postal_code', '');
        $data['notes'] = $request->input('notes', '');

        try {
            $order = OrderService::createOrder($data);
            Session::flash('success', __('messages.order_placed_successfully'));
            return redirect(url('order/success/' . $order->order_number));
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(url('checkout'));
        }
    }

    public function orderSuccess(Request $request, string $orderNumber) {
        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            return redirect(url('/'));
        }

        return view('storefront.order-success', compact('order'));
    }

    public function wishlist(Request $request) {
        $userId = Auth::getInstance()->id();
        $sessionId = session_id();

        $query = Wishlist::query();
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $wishlistItems = $query->get();
        return view('storefront.wishlist', compact('wishlistItems'));
    }

    public function compare(Request $request) {
        $userId = Auth::getInstance()->id();
        $sessionId = session_id();

        $query = Compare::query();
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $compareItems = $query->get();
        return view('storefront.compare', compact('compareItems'));
    }

    public function page(Request $request, string $slug) {
        $page = Page::where('slug', $slug)->where('status', 'published')->first();
        if (!$page) {
            return Response::html(view('storefront.404'), 404);
        }

        return view('storefront.page', compact('page'));
    }

    public function contact(Request $request) {
        if ($request->isPost()) {
            $data = $request->validate([
                'name' => 'required|min:3',
                'email' => 'required|email',
                'subject' => 'required',
                'message' => 'required|min:5'
            ]);

            Session::flash('success', __('messages.contact_message_sent_success'));
            return redirect(url('contact'));
        }

        return view('storefront.contact');
    }

    public function trackOrder(Request $request) {
        $orderNumber = trim((string)$request->input('order_number', ''));
        $email = strtolower(trim((string)$request->input('email', '')));
        $order = null;

        if (!empty($orderNumber)) {
            if (empty($email)) {
                Session::flash('error', 'يرجى إدخال البريد الإلكتروني المقترن بالطلب للتحقق من هويتك وحماية خصوصية بياناتك.');
            } else {
                $order = Order::where('order_number', $orderNumber)
                              ->where('customer_email', $email)
                              ->first();
                if (!$order) {
                    Session::flash('error', __('messages.order_not_found'));
                }
            }
        }

        return view('storefront.track-order', compact('order', 'orderNumber', 'email'));
    }

    public function setLocale(Request $request, string $locale) {
        Translator::setLocale($locale);
        $referer = $request->referrer() ?: url('/');
        return redirect($referer);
    }

    public function setCurrency(Request $request, string $currency) {
        CurrencyService::setCurrency($currency);
        $referer = $request->referrer() ?: url('/');
        return redirect($referer);
    }

    public function sitemap() {
        $xml = SeoService::renderSitemapXml();
        return new Response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    public function robots() {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /account/\nSitemap: " . url('sitemap.xml');
        return new Response($content, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}
