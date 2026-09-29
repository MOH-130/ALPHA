<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductAttribute;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderHistory;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Deal;
use App\Models\Review;
use App\Models\ReturnRequest;
use App\Models\AffiliateCommission;
use App\Models\Currency;
use App\Models\Language;
use App\Models\ShippingMethod;
use App\Models\Page;
use App\Models\Setting;
use App\Models\ThemeSetting;
use App\Models\Visitor;
use App\Models\ActivityLog;
use App\Services\OrderService;
use App\Services\ImageOptimizer;
use App\Services\VisitorTrackerService;
use App\Services\ActivityLogger;

class AdminController {
    public function dashboard(Request $request) {
        $totalSales = Order::whereIn('status', ['paid', 'shipped', 'completed'])->sum('total_amount');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalProducts = Product::count();
        $estimatedProfit = round($totalSales * 0.35, 2); // 35% estimated gross margin

        $recentOrders = Order::orderByDesc('id')->limit(8)->get();
        $topProducts = Product::orderByDesc('sales_count')->limit(5)->get();
        $lowStockProducts = Product::whereRaw('stock_quantity <= low_stock_threshold')->limit(5)->get();
        $onlineVisitors = VisitorTrackerService::getActiveOnlineCount();

        // Chart Data for last 7 days
        $chartLabels = [];
        $chartSales = [];
        $chartOrders = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-{$i} days"));
            $chartLabels[] = date('d M', strtotime($day));
            
            $dayOrders = Order::whereRaw("DATE(created_at) = ?", [$day])->get();
            $chartOrders[] = count($dayOrders);
            
            $dayTotal = 0.0;
            foreach ($dayOrders as $o) {
                if (in_array($o->status, ['paid', 'shipped', 'completed', 'pending'])) {
                    $dayTotal += (float)$o->total_amount;
                }
            }
            $chartSales[] = $dayTotal;
        }

        return view('admin.dashboard', compact(
            'totalSales', 'totalOrders', 'pendingOrders', 'totalCustomers',
            'totalProducts', 'estimatedProfit', 'recentOrders', 'topProducts',
            'lowStockProducts', 'onlineVisitors', 'chartLabels', 'chartSales', 'chartOrders'
        ));
    }

    // ================= PRODUCT MANAGEMENT =================
    public function products(Request $request) {
        $query = Product::query();
        $q = $request->getQuery('q');
        if ($q) {
            $query->whereRaw("(name_ar LIKE ? OR name_en LIKE ? OR sku LIKE ?)", ["%{$q}%", "%{$q}%", "%{$q}%"]);
        }
        $catId = $request->getQuery('category_id');
        if ($catId) {
            $query->where('category_id', $catId);
        }
        $status = $request->getQuery('status');
        if ($status) {
            $query->where('status', $status);
        }

        $products = $query->orderByDesc('id')->paginate(15);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories', 'q', 'catId', 'status'));
    }

    public function createProduct() {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function storeProduct(Request $request) {
        $data = $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|numeric'
        ]);

        $slug = slugify($request->input('name_en') ?: $request->input('name_ar'));
        $existingSlug = Product::where('slug', $slug)->first();
        if ($existingSlug) {
            $slug .= '-' . time();
        }

        $primaryImage = 'default-product.png';
        if ($request->hasFile('primary_image')) {
            $uploaded = ImageOptimizer::processAndSave($request->file('primary_image'), __DIR__ . '/../../public/uploads/products');
            if ($uploaded) {
                $primaryImage = $uploaded;
            }
        }

        $product = Product::create([
            'category_id' => $request->input('category_id') ?: null,
            'brand_id' => $request->input('brand_id') ?: null,
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'slug' => $slug,
            'sku' => $request->input('sku') ?: ('SKU-' . strtoupper(substr(uniqid(), -6))),
            'barcode' => $request->input('barcode', ''),
            'short_description_ar' => $request->input('short_description_ar', ''),
            'short_description_en' => $request->input('short_description_en', ''),
            'description_ar' => $request->input('description_ar', ''),
            'description_en' => $request->input('description_en', ''),
            'price' => (float)$data['price'],
            'compare_price' => $request->input('compare_price') ? (float)$request->input('compare_price') : null,
            'cost_price' => $request->input('cost_price') ? (float)$request->input('cost_price') : null,
            'stock_quantity' => (int)$data['stock_quantity'],
            'low_stock_threshold' => (int)($request->input('low_stock_threshold') ?: 5),
            'primary_image' => $primaryImage,
            'video_url' => $request->input('video_url', ''),
            'is_featured' => $request->input('is_featured') ? 1 : 0,
            'is_best_seller' => $request->input('is_best_seller') ? 1 : 0,
            'is_deal' => $request->input('is_deal') ? 1 : 0,
            'deal_price' => $request->input('deal_price') ? (float)$request->input('deal_price') : null,
            'deal_start' => $request->input('deal_start') ?: null,
            'deal_end' => $request->input('deal_end') ?: null,
            'meta_title' => $request->input('meta_title', ''),
            'meta_description' => $request->input('meta_description', ''),
            'meta_keywords' => $request->input('meta_keywords', ''),
            'weight' => (float)($request->input('weight') ?: 0.5),
            'status' => $request->input('status', 'active')
        ]);

        // Multiple gallery images
        if ($request->hasFile('gallery_images')) {
            $files = $request->file('gallery_images');
            if (isset($files['name']) && is_array($files['name'])) {
                for ($i = 0; $i < count($files['name']); $i++) {
                    $err = $files['error'][$i] ?? UPLOAD_ERR_NO_FILE;
                    $tmpName = $files['tmp_name'][$i] ?? '';
                    if ($err !== UPLOAD_ERR_OK || empty($tmpName) || !is_string($tmpName) || !is_uploaded_file($tmpName)) {
                        continue;
                    }
                    $singleFile = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i] ?? '',
                        'tmp_name' => $tmpName,
                        'error' => $err,
                        'size' => $files['size'][$i] ?? 0,
                    ];
                    $uploadedGall = ImageOptimizer::processAndSave($singleFile, __DIR__ . '/../../public/uploads/products');
                    if ($uploadedGall) {
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $uploadedGall,
                            'sort_order' => $i
                        ]);
                    }
                }
            }
        }

        ActivityLogger::log('Create Product', 'Products', "Created product: {$product->name_en} (ID: {$product->id})");
        Session::flash('success', __('admin.product_created_success'));
        return redirect(url('admin/products'));
    }

    public function editProduct(Request $request, int $id) {
        $product = Product::find($id);
        if (!$product) return redirect(url('admin/products'));
        $categories = Category::all();
        $brands = Brand::all();
        $images = $product->images();
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'images'));
    }

    public function updateProduct(Request $request, int $id) {
        $product = Product::find($id);
        if (!$product) return redirect(url('admin/products'));

        $data = $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|numeric'
        ]);

        $updateData = [
            'category_id' => $request->input('category_id') ?: null,
            'brand_id' => $request->input('brand_id') ?: null,
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'sku' => $request->input('sku', $product->sku),
            'barcode' => $request->input('barcode', ''),
            'short_description_ar' => $request->input('short_description_ar', ''),
            'short_description_en' => $request->input('short_description_en', ''),
            'description_ar' => $request->input('description_ar', ''),
            'description_en' => $request->input('description_en', ''),
            'price' => (float)$data['price'],
            'compare_price' => $request->input('compare_price') ? (float)$request->input('compare_price') : null,
            'cost_price' => $request->input('cost_price') ? (float)$request->input('cost_price') : null,
            'stock_quantity' => (int)$data['stock_quantity'],
            'low_stock_threshold' => (int)($request->input('low_stock_threshold') ?: 5),
            'video_url' => $request->input('video_url', ''),
            'is_featured' => $request->input('is_featured') ? 1 : 0,
            'is_best_seller' => $request->input('is_best_seller') ? 1 : 0,
            'is_deal' => $request->input('is_deal') ? 1 : 0,
            'deal_price' => $request->input('deal_price') ? (float)$request->input('deal_price') : null,
            'deal_start' => $request->input('deal_start') ?: null,
            'deal_end' => $request->input('deal_end') ?: null,
            'meta_title' => $request->input('meta_title', ''),
            'meta_description' => $request->input('meta_description', ''),
            'meta_keywords' => $request->input('meta_keywords', ''),
            'weight' => (float)($request->input('weight') ?: 0.5),
            'status' => $request->input('status', 'active')
        ];

        if ($request->hasFile('primary_image')) {
            $uploaded = ImageOptimizer::processAndSave($request->file('primary_image'), __DIR__ . '/../../public/uploads/products');
            if ($uploaded) {
                $updateData['primary_image'] = $uploaded;
            }
        }

        $product->update($updateData);
        ActivityLogger::log('Update Product', 'Products', "Updated product: {$product->name_en} (ID: {$product->id})");
        Session::flash('success', __('admin.product_updated_success'));
        return redirect(url('admin/products'));
    }

    public function deleteProduct(Request $request, int $id) {
        $product = Product::find($id);
        if ($product) {
            ActivityLogger::log('Delete Product', 'Products', "Deleted product: {$product->name_en} (ID: {$product->id})");
            $product->delete();
            Session::flash('success', __('admin.product_deleted_success'));
        }
        return redirect(url('admin/products'));
    }

    // ================= CATEGORIES & BRANDS =================
    public function categories(Request $request) {
        $categories = Category::orderBy('sort_order', 'ASC')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request) {
        $data = $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required'
        ]);

        $slug = slugify($data['name_en'] ?: $data['name_ar']);
        $image = null;
        if ($request->hasFile('image')) {
            $image = ImageOptimizer::processAndSave($request->file('image'), __DIR__ . '/../../public/uploads/categories');
        }

        Category::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'slug' => $slug,
            'parent_id' => $request->input('parent_id') ?: null,
            'description_ar' => $request->input('description_ar', ''),
            'description_en' => $request->input('description_en', ''),
            'image' => $image,
            'icon' => $request->input('icon', 'tag'),
            'sort_order' => (int)($request->input('sort_order') ?: 0),
            'is_featured' => $request->input('is_featured') ? 1 : 0,
            'status' => $request->input('status', 'active')
        ]);

        Session::flash('success', __('admin.category_saved_success'));
        return redirect(url('admin/categories'));
    }

    public function deleteCategory(Request $request, int $id) {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            Session::flash('success', __('admin.category_deleted_success'));
        }
        return redirect(url('admin/categories'));
    }

    public function brands(Request $request) {
        $brands = Brand::all();
        return view('admin.brands.index', compact('brands'));
    }

    public function storeBrand(Request $request) {
        $data = $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required'
        ]);

        $logo = null;
        if ($request->hasFile('logo')) {
            $logo = ImageOptimizer::processAndSave($request->file('logo'), __DIR__ . '/../../public/uploads/brands');
        }

        Brand::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'slug' => slugify($data['name_en']),
            'logo' => $logo,
            'description_ar' => $request->input('description_ar', ''),
            'description_en' => $request->input('description_en', ''),
            'is_featured' => $request->input('is_featured') ? 1 : 0,
            'status' => 'active'
        ]);

        Session::flash('success', __('admin.brand_saved_success'));
        return redirect(url('admin/brands'));
    }

    public function deleteBrand(Request $request, int $id) {
        $brand = Brand::find($id);
        if ($brand) {
            $brand->delete();
            Session::flash('success', __('admin.brand_deleted_success'));
        }
        return redirect(url('admin/brands'));
    }

    // ================= ORDERS & SALES =================
    public function orders(Request $request) {
        $query = Order::query();
        $status = $request->getQuery('status');
        if ($status) {
            $query->where('status', $status);
        }
        $paymentStatus = $request->getQuery('payment_status');
        if ($paymentStatus) {
            $query->where('payment_status', $paymentStatus);
        }
        $q = $request->getQuery('q');
        if ($q) {
            $query->whereRaw("(order_number LIKE ? OR customer_name LIKE ? OR customer_email LIKE ? OR customer_phone LIKE ?)", [
                "%{$q}%", "%{$q}%", "%{$q}%", "%{$q}%"
            ]);
        }

        $orders = $query->orderByDesc('id')->paginate(15);
        return view('admin.orders.index', compact('orders', 'status', 'paymentStatus', 'q'));
    }

    public function showOrder(Request $request, int $id) {
        $order = Order::find($id);
        if (!$order) return redirect(url('admin/orders'));

        $items = $order->items();
        $history = $order->history();

        return view('admin.orders.show', compact('order', 'items', 'history'));
    }

    public function updateOrderStatus(Request $request, int $id) {
        $order = Order::find($id);
        if (!$order) return redirect(url('admin/orders'));

        $status = $request->input('status');
        $paymentStatus = $request->input('payment_status');
        $comment = $request->input('comment', '');

        if ($status) {
            OrderService::updateStatus($order, $status, $comment, true);
        }
        if ($paymentStatus) {
            $order->update(['payment_status' => $paymentStatus]);
        }
        if ($request->input('tracking_number')) {
            $order->update(['tracking_number' => $request->input('tracking_number')]);
        }

        ActivityLogger::log('Update Order Status', 'Orders', "Updated order {$order->order_number} to status: {$status}");
        Session::flash('success', __('admin.order_updated_success'));
        return redirect(url('admin/orders/' . $id));
    }

    public function printInvoice(Request $request, int $id) {
        $order = Order::find($id);
        if (!$order) return redirect(url('admin/orders'));
        $items = $order->items();
        return view('admin.orders.invoice', compact('order', 'items'));
    }

    // ================= RETURNS / REFUNDS =================
    public function returns(Request $request) {
        $returns = ReturnRequest::orderByDesc('id')->paginate(15);
        return view('admin.returns.index', compact('returns'));
    }

    public function updateReturnStatus(Request $request, int $id) {
        $return = ReturnRequest::find($id);
        if ($return) {
            $status = $request->input('status');
            $adminNotes = $request->input('admin_notes', '');
            $return->update([
                'status' => $status,
                'admin_notes' => $adminNotes
            ]);

            if ($status === 'refunded' && $return->order()) {
                $return->order()->update(['status' => 'refunded', 'payment_status' => 'refunded']);
            }

            ActivityLogger::log('Update Return Request', 'Returns', "Updated return request #{$return->id} to {$status}");
            Session::flash('success', __('admin.return_request_updated'));
        }
        return redirect(url('admin/returns'));
    }

    // ================= CUSTOMERS =================
    public function customers(Request $request) {
        $query = User::where('role', 'customer');
        $q = $request->getQuery('q');
        if ($q) {
            $query->whereRaw("(name LIKE ? OR email LIKE ? OR phone LIKE ?)", ["%{$q}%", "%{$q}%", "%{$q}%"]);
        }
        $customers = $query->orderByDesc('id')->paginate(15);
        return view('admin.customers.index', compact('customers', 'q'));
    }

    public function updateCustomerStatus(Request $request, int $id) {
        $customer = User::find($id);
        if ($customer) {
            $isApproved = $request->input('is_approved') !== null ? (int)$request->input('is_approved') : $customer->is_approved;
            $status = $request->input('status', $customer->status);
            $customer->update(['is_approved' => $isApproved, 'status' => $status]);
            Session::flash('success', __('admin.customer_updated_success'));
        }
        return redirect(url('admin/customers'));
    }

    // ================= MARKETING & COUPONS & DEALS =================
    public function coupons(Request $request) {
        $coupons = Coupon::orderByDesc('id')->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    public function storeCoupon(Request $request) {
        $data = $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric'
        ]);

        Coupon::create([
            'code' => strtoupper(trim($data['code'])),
            'type' => $data['type'],
            'value' => (float)$data['value'],
            'min_order_amount' => $request->input('min_order_amount') ? (float)$request->input('min_order_amount') : null,
            'max_discount_amount' => $request->input('max_discount_amount') ? (float)$request->input('max_discount_amount') : null,
            'usage_limit' => $request->input('usage_limit') ? (int)$request->input('usage_limit') : null,
            'end_date' => $request->input('end_date') ?: null,
            'status' => 'active'
        ]);

        Session::flash('success', __('admin.coupon_created_success'));
        return redirect(url('admin/coupons'));
    }

    public function deleteCoupon(Request $request, int $id) {
        $coupon = Coupon::find($id);
        if ($coupon) $coupon->delete();
        Session::flash('success', __('admin.coupon_deleted_success'));
        return redirect(url('admin/coupons'));
    }

    // ================= AFFILIATE MANAGEMENT =================
    public function affiliates(Request $request) {
        $commissions = AffiliateCommission::orderByDesc('id')->paginate(15);
        $totalCommissions = AffiliateCommission::sum('commission_amount');
        $paidCommissions = AffiliateCommission::where('status', 'paid')->sum('commission_amount');
        $pendingCommissions = AffiliateCommission::where('status', 'pending')->sum('commission_amount');

        return view('admin.affiliates.index', compact('commissions', 'totalCommissions', 'paidCommissions', 'pendingCommissions'));
    }

    public function updateAffiliateCommission(Request $request, int $id) {
        $commission = AffiliateCommission::find($id);
        if ($commission) {
            $status = $request->input('status');
            $commission->update([
                'status' => $status,
                'payout_date' => $status === 'paid' ? date('Y-m-d H:i:s') : null,
                'notes' => $request->input('notes', $commission->notes)
            ]);
            Session::flash('success', __('admin.commission_updated_success'));
        }
        return redirect(url('admin/affiliates'));
    }

    // ================= REVIEWS MODERATION =================
    public function reviews(Request $request) {
        $reviews = Review::orderByDesc('id')->paginate(15);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleReview(Request $request, int $id) {
        $review = Review::find($id);
        if ($review) {
            $review->update(['is_approved' => $review->is_approved ? 0 : 1]);
            Session::flash('success', __('admin.review_status_updated'));
        }
        return redirect(url('admin/reviews'));
    }

    public function deleteReview(Request $request, int $id) {
        $review = Review::find($id);
        if ($review) $review->delete();
        Session::flash('success', __('admin.review_deleted_success'));
        return redirect(url('admin/reviews'));
    }

    // ================= THEME & DESIGN CUSTOMIZER =================
    public function theme(Request $request) {
        if ($request->isPost()) {
            $fields = [
                'primary_color', 'secondary_color', 'accent_color', 'font_family',
                'button_radius', 'hero_headline_ar', 'hero_headline_en',
                'hero_subheadline_ar', 'hero_subheadline_en', 'footer_text_ar',
                'footer_text_en', 'show_brands_section', 'show_flash_deals',
                'show_newsletter', 'header_style', 'bg_preset', 'bg_main_color',
                'bg_card_color', 'enable_custom_cursor', 'cursor_glow_color',
                'enable_welcome_modal'
            ];

            foreach ($fields as $field) {
                if ($request->input($field) !== null) {
                    ThemeSetting::set($field, $request->input($field));
                }
            }

            if ($request->hasFile('store_logo')) {
                $logo = ImageOptimizer::processAndSave($request->file('store_logo'), __DIR__ . '/../../public/assets/images');
                if ($logo) {
                    ThemeSetting::set('store_logo', $logo);
                }
            }

            ActivityLogger::log('Update Theme', 'Theme', 'Updated store theme colors, cursor effects, and layout customizer');
            Session::flash('success', __('admin.theme_saved_success'));
            return redirect(url('admin/theme'));
        }

        return view('admin.theme.index');
    }

    // ================= SETTINGS =================
    public function settings(Request $request) {
        if ($request->isPost()) {
            $settings = $request->all();
            foreach ($settings as $k => $v) {
                if ($k !== '_token') {
                    Setting::set($k, is_array($v) ? json_encode($v) : $v);
                }
            }
            ActivityLogger::log('Update Settings', 'Settings', 'Updated system settings');
            Session::flash('success', __('admin.settings_saved_success'));
            return redirect(url('admin/settings'));
        }

        return view('admin.settings.index');
    }

    // ================= SHIPPING & PAYMENTS =================
    public function shipping(Request $request) {
        $methods = ShippingMethod::orderBy('sort_order', 'ASC')->get();
        return view('admin.shipping.index', compact('methods'));
    }

    public function updateShippingMethod(Request $request, int $id) {
        $method = ShippingMethod::find($id);
        if ($method) {
            $method->update([
                'cost' => (float)$request->input('cost', 0),
                'free_threshold' => $request->input('free_threshold') ? (float)$request->input('free_threshold') : null,
                'status' => $request->input('status', 'active')
            ]);
            Session::flash('success', __('admin.shipping_updated_success'));
        }
        return redirect(url('admin/shipping'));
    }

    public function payments(Request $request) {
        return view('admin.payments.index');
    }

    public function updatePayments(Request $request) {
        Setting::set('payment_cod_enabled', $request->input('payment_cod_enabled', '0'));
        Setting::set('payment_bank_enabled', $request->input('payment_bank_enabled', '0'));
        Setting::set('payment_card_enabled', $request->input('payment_card_enabled', '0'));
        Setting::set('payment_paypal_enabled', $request->input('payment_paypal_enabled', '0'));
        Setting::set('bank_account_details', $request->input('bank_account_details', ''));

        Session::flash('success', __('admin.payments_saved_success'));
        return redirect(url('admin/payments'));
    }

    // ================= CURRENCIES & LANGUAGES =================
    public function currencies(Request $request) {
        $currencies = Currency::all();
        return view('admin.currencies.index', compact('currencies'));
    }

    public function storeCurrency(Request $request) {
        $data = $request->validate([
            'code' => 'required',
            'name_ar' => 'required',
            'name_en' => 'required',
            'exchange_rate' => 'required|numeric'
        ]);

        Currency::create([
            'code' => strtoupper($data['code']),
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'symbol_ar' => $request->input('symbol_ar', $data['code']),
            'symbol_en' => $request->input('symbol_en', $data['code']),
            'exchange_rate' => (float)$data['exchange_rate'],
            'status' => 'active'
        ]);

        Session::flash('success', __('admin.currency_saved_success'));
        return redirect(url('admin/currencies'));
    }

    public function languages(Request $request) {
        $languages = Language::all();
        return view('admin.languages.index', compact('languages'));
    }

    // ================= PAGES =================
    public function pages(Request $request) {
        $pages = Page::all();
        return view('admin.pages.index', compact('pages'));
    }

    public function storePage(Request $request) {
        $data = $request->validate([
            'title_ar' => 'required',
            'title_en' => 'required',
            'slug' => 'required'
        ]);

        Page::create([
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'],
            'slug' => slugify($data['slug']),
            'content_ar' => $request->input('content_ar', ''),
            'content_en' => $request->input('content_en', ''),
            'status' => 'published'
        ]);

        Session::flash('success', __('admin.page_saved_success'));
        return redirect(url('admin/pages'));
    }

    // ================= VISITORS & AUDIT LOGS =================
    public function visitors(Request $request) {
        $visitors = Visitor::orderByDesc('last_activity')->limit(50)->get();
        $onlineCount = VisitorTrackerService::getActiveOnlineCount();
        return view('admin.visitors.index', compact('visitors', 'onlineCount'));
    }

    public function logs(Request $request) {
        $logs = ActivityLog::orderByDesc('id')->paginate(20);
        return view('admin.logs.index', compact('logs'));
    }

    // ================= ADMIN PROFILE & CREDENTIALS SECURITY =================
    public function profile(Request $request) {
        $admin = Auth::getInstance()->user();
        if (!$admin) {
            return redirect(url('admin/login'));
        }
        $enableDemoMode = Setting::get('enable_demo_mode', '0');
        $googleClientId = Setting::get('google_client_id', '');
        $googleClientSecret = Setting::get('google_client_secret', '');
        $googleRedirectUri = \App\Services\GoogleAuthService::getRedirectUri();

        return view('admin.profile.index', compact('admin', 'enableDemoMode', 'googleClientId', 'googleClientSecret', 'googleRedirectUri'));
    }

    public function updateProfile(Request $request) {
        $admin = Auth::getInstance()->user();
        if (!$admin) {
            return redirect(url('admin/login'));
        }

        $data = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email'
        ]);

        // Check if email changed and is taken by another account
        if ($data['email'] !== $admin->email) {
            $existing = User::where('email', $data['email'])->first();
            if ($existing && $existing->id !== $admin->id) {
                Session::flash('error', 'البريد الإلكتروني المدخل مستخدم بالفعل من قبل حساب آخر. يرجى اختيار بريد مختلف.');
                return redirect(url('admin/profile'));
            }
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $request->input('phone', $admin->phone),
            'two_factor_enabled' => $request->input('two_factor_enabled') ? 1 : 0
        ];

        // Check password change if provided
        $currentPassword = $request->input('current_password');
        $newPassword = $request->input('new_password');
        $confirmPassword = $request->input('confirm_password');

        if (!empty($newPassword)) {
            if (mb_strlen($newPassword) < 6) {
                Session::flash('error', 'كلمة المرور الجديدة يجب ألا تقل عن 6 خانات.');
                return redirect(url('admin/profile'));
            }

            if ($newPassword !== $confirmPassword) {
                Session::flash('error', 'تأكيد كلمة المرور الجديدة غير متطابق.');
                return redirect(url('admin/profile'));
            }

            if (!empty($currentPassword)) {
                if (!password_verify($currentPassword, $admin->password)) {
                    Session::flash('error', 'كلمة المرور الحالية غير صحيحة.');
                    return redirect(url('admin/profile'));
                }
            }

            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $admin->update($updateData);

        // Update Session info
        Session::set('user_name', $admin->name);
        Session::set('user_email', $admin->email);

        // Update Demo Mode setting (whether to show or hide demo auto-fill on login)
        $enableDemo = $request->input('enable_demo_mode', '0') === '1' ? '1' : '0';
        Setting::set('enable_demo_mode', $enableDemo, 'security');

        // Update Google OAuth credentials
        if ($request->input('google_client_id') !== null) {
            Setting::set('google_client_id', trim((string)$request->input('google_client_id')), 'oauth');
        }
        if ($request->input('google_client_secret') !== null) {
            Setting::set('google_client_secret', trim((string)$request->input('google_client_secret')), 'oauth');
        }

        ActivityLogger::log('Update Admin Profile', 'Admin', "Admin updated profile credentials & security: {$admin->email}");
        Session::flash('success', 'تم تحديث بيانات المشرف وكلمة المرور وإعدادات الأمان والتحقق المزدوج بنجاح! 🔒');
        return redirect(url('admin/profile'));
    }
}

