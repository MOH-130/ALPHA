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
        
        $featuredProducts = Product::where('status', 'active')->where('is_featured', 1)->limit(8)->get();
        $bestSellers = Product::where('status', 'active')->orderByDesc('sales_count')->limit(8)->get();
        $newArrivals = Product::where('status', 'active')->orderByDesc('id')->limit(8)->get();
        $deals = Product::where('status', 'active')->where('is_deal', 1)->limit(6)->get();
        $brands = Brand::where('status', 'active')->limit(12)->get();

        return view('storefront.home', compact(
            'banners', 'categories', 'featuredCategories', 'featuredProducts',
            'bestSellers', 'newArrivals', 'deals', 'brands'
        ));
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

        $authorName = Auth::getInstance()->check() ? Auth::getInstance()->user()->name : ($request->input('name') ?: 'عميل');
        $authorEmail = Auth::getInstance()->check() ? Auth::getInstance()->user()->email : ($request->input('email') ?: 'guest@store.com');

        Review::create([
            'product_id' => $product->id,
            'user_id' => Auth::getInstance()->id(),
            'author_name' => $authorName,
            'author_email' => $authorEmail,
            'rating' => (int)$data['rating'],
            'comment' => htmlspecialchars($data['comment']),
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
        $data['country'] = $request->input('country', 'Saudi Arabia');
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
        $orderNumber = $request->input('order_number');
        $email = $request->input('email');
        $order = null;

        if ($orderNumber) {
            $query = Order::where('order_number', trim($orderNumber));
            if ($email) {
                $query->where('customer_email', trim($email));
            }
            $order = $query->first();
            if (!$order) {
                Session::flash('error', __('messages.order_not_found'));
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
