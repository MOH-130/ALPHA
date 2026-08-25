<?php

namespace Database\Migrations;

use App\Core\Database;

class CreateAllTables {
    public static function fresh(): void {
        $pdo = Database::getPdo();
        $tables = [
            'activity_logs', 'affiliate_commissions', 'categories', 'brands',
            'coupons', 'currencies', 'deals', 'languages', 'order_histories',
            'order_items', 'orders', 'pages', 'payments', 'product_attributes',
            'product_images', 'products', 'return_requests', 'reviews',
            'roles', 'settings', 'shipping_methods', 'shipping_rates',
            'theme_settings', 'users', 'visitors', 'wishlists', 'compares'
        ];
        $pdo->exec("PRAGMA foreign_keys = OFF;");
        foreach ($tables as $tbl) {
            $pdo->exec("DROP TABLE IF EXISTS `{$tbl}`;");
        }
        $pdo->exec("PRAGMA foreign_keys = ON;");
        self::up();
    }

    public static function up(): void {
        $pdo = Database::getPdo();

        $statements = [
            // Users table
            "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(191) NOT NULL,
                email VARCHAR(191) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                phone VARCHAR(50) NULL,
                role VARCHAR(50) DEFAULT 'customer',
                status VARCHAR(50) DEFAULT 'active',
                avatar VARCHAR(255) NULL,
                is_approved INTEGER DEFAULT 1,
                affiliate_code VARCHAR(100) UNIQUE NULL,
                email_verified_at DATETIME NULL,
                address TEXT NULL,
                city VARCHAR(100) NULL,
                country VARCHAR(100) DEFAULT 'Saudi Arabia',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Roles table
            "CREATE TABLE IF NOT EXISTS roles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) UNIQUE NOT NULL,
                description TEXT NULL,
                permissions TEXT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Categories table
            "CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                parent_id INTEGER NULL,
                name_ar VARCHAR(191) NOT NULL,
                name_en VARCHAR(191) NOT NULL,
                slug VARCHAR(191) UNIQUE NOT NULL,
                description_ar TEXT NULL,
                description_en TEXT NULL,
                image VARCHAR(255) NULL,
                icon VARCHAR(100) DEFAULT 'tag',
                sort_order INTEGER DEFAULT 0,
                is_featured INTEGER DEFAULT 0,
                status VARCHAR(50) DEFAULT 'active',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
            );",

            // Brands table
            "CREATE TABLE IF NOT EXISTS brands (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name_ar VARCHAR(191) NOT NULL,
                name_en VARCHAR(191) NOT NULL,
                slug VARCHAR(191) UNIQUE NOT NULL,
                logo VARCHAR(255) NULL,
                description_ar TEXT NULL,
                description_en TEXT NULL,
                is_featured INTEGER DEFAULT 0,
                status VARCHAR(50) DEFAULT 'active',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Products table
            "CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category_id INTEGER NULL,
                brand_id INTEGER NULL,
                name_ar VARCHAR(255) NOT NULL,
                name_en VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                sku VARCHAR(100) UNIQUE NOT NULL,
                barcode VARCHAR(100) NULL,
                short_description_ar TEXT NULL,
                short_description_en TEXT NULL,
                description_ar TEXT NULL,
                description_en TEXT NULL,
                price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                compare_price DECIMAL(10,2) NULL,
                cost_price DECIMAL(10,2) NULL,
                stock_quantity INTEGER NOT NULL DEFAULT 0,
                low_stock_threshold INTEGER DEFAULT 5,
                primary_image VARCHAR(255) NULL,
                video_url VARCHAR(255) NULL,
                is_featured INTEGER DEFAULT 0,
                is_best_seller INTEGER DEFAULT 0,
                is_deal INTEGER DEFAULT 0,
                deal_price DECIMAL(10,2) NULL,
                deal_start DATETIME NULL,
                deal_end DATETIME NULL,
                meta_title VARCHAR(255) NULL,
                meta_description TEXT NULL,
                meta_keywords VARCHAR(255) NULL,
                status VARCHAR(50) DEFAULT 'active',
                views_count INTEGER DEFAULT 0,
                sales_count INTEGER DEFAULT 0,
                weight DECIMAL(8,2) DEFAULT 0.50,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
                FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL
            );",

            // Product Images
            "CREATE TABLE IF NOT EXISTS product_images (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                product_id INTEGER NOT NULL,
                image_path VARCHAR(255) NOT NULL,
                alt_text VARCHAR(255) NULL,
                sort_order INTEGER DEFAULT 0,
                is_primary INTEGER DEFAULT 0,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            );",

            // Product Attributes
            "CREATE TABLE IF NOT EXISTS product_attributes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                product_id INTEGER NOT NULL,
                name_ar VARCHAR(100) NOT NULL,
                name_en VARCHAR(100) NOT NULL,
                values_json TEXT NOT NULL,
                price_modifier DECIMAL(10,2) DEFAULT 0.00,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            );",

            // Orders table
            "CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_number VARCHAR(100) UNIQUE NOT NULL,
                user_id INTEGER NULL,
                customer_name VARCHAR(191) NOT NULL,
                customer_email VARCHAR(191) NOT NULL,
                customer_phone VARCHAR(50) NOT NULL,
                shipping_address TEXT NOT NULL,
                shipping_city VARCHAR(100) NOT NULL,
                shipping_country VARCHAR(100) DEFAULT 'Saudi Arabia',
                shipping_postal_code VARCHAR(50) NULL,
                subtotal DECIMAL(10,2) NOT NULL,
                discount_amount DECIMAL(10,2) DEFAULT 0.00,
                coupon_code VARCHAR(100) NULL,
                tax_amount DECIMAL(10,2) DEFAULT 0.00,
                shipping_fee DECIMAL(10,2) DEFAULT 0.00,
                total_amount DECIMAL(10,2) NOT NULL,
                currency_code VARCHAR(10) DEFAULT 'SAR',
                currency_rate DECIMAL(10,4) DEFAULT 1.0000,
                status VARCHAR(50) DEFAULT 'pending',
                payment_status VARCHAR(50) DEFAULT 'pending',
                payment_method VARCHAR(50) NOT NULL,
                shipping_method VARCHAR(100) DEFAULT 'Standard Shipping',
                tracking_number VARCHAR(100) NULL,
                notes TEXT NULL,
                affiliate_id INTEGER NULL,
                affiliate_commission DECIMAL(10,2) DEFAULT 0.00,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            );",

            // Order Items table
            "CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                product_id INTEGER NULL,
                product_name VARCHAR(255) NOT NULL,
                product_image VARCHAR(255) NULL,
                sku VARCHAR(100) NULL,
                unit_price DECIMAL(10,2) NOT NULL,
                quantity INTEGER NOT NULL DEFAULT 1,
                subtotal DECIMAL(10,2) NOT NULL,
                attributes_json TEXT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            );",

            // Order Histories
            "CREATE TABLE IF NOT EXISTS order_histories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                status VARCHAR(50) NOT NULL,
                comment TEXT NULL,
                notified_customer INTEGER DEFAULT 1,
                created_by VARCHAR(100) DEFAULT 'System',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            );",

            // Payments table
            "CREATE TABLE IF NOT EXISTS payments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                gateway VARCHAR(50) NOT NULL,
                transaction_id VARCHAR(100) NULL,
                amount DECIMAL(10,2) NOT NULL,
                currency VARCHAR(10) DEFAULT 'SAR',
                status VARCHAR(50) DEFAULT 'pending',
                receipt_image VARCHAR(255) NULL,
                payload_json TEXT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            );",

            // Shipping Methods
            "CREATE TABLE IF NOT EXISTS shipping_methods (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code VARCHAR(50) UNIQUE NOT NULL,
                name_ar VARCHAR(100) NOT NULL,
                name_en VARCHAR(100) NOT NULL,
                description_ar TEXT NULL,
                description_en TEXT NULL,
                cost DECIMAL(10,2) DEFAULT 0.00,
                free_threshold DECIMAL(10,2) NULL,
                status VARCHAR(50) DEFAULT 'active',
                sort_order INTEGER DEFAULT 0,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Shipping Rates
            "CREATE TABLE IF NOT EXISTS shipping_rates (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                shipping_method_id INTEGER NOT NULL,
                country VARCHAR(100) NOT NULL,
                city VARCHAR(100) NULL,
                cost DECIMAL(10,2) NOT NULL,
                min_weight DECIMAL(8,2) DEFAULT 0.00,
                max_weight DECIMAL(8,2) DEFAULT 100.00,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Currencies table
            "CREATE TABLE IF NOT EXISTS currencies (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code VARCHAR(10) UNIQUE NOT NULL,
                name_ar VARCHAR(100) NOT NULL,
                name_en VARCHAR(100) NOT NULL,
                symbol_ar VARCHAR(20) NOT NULL,
                symbol_en VARCHAR(20) NOT NULL,
                exchange_rate DECIMAL(10,4) NOT NULL DEFAULT 1.0000,
                is_default INTEGER DEFAULT 0,
                status VARCHAR(50) DEFAULT 'active',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Languages table
            "CREATE TABLE IF NOT EXISTS languages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code VARCHAR(10) UNIQUE NOT NULL,
                name VARCHAR(100) NOT NULL,
                direction VARCHAR(10) DEFAULT 'ltr',
                flag VARCHAR(50) NULL,
                is_default INTEGER DEFAULT 0,
                status VARCHAR(50) DEFAULT 'active',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Coupons table
            "CREATE TABLE IF NOT EXISTS coupons (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code VARCHAR(50) UNIQUE NOT NULL,
                type VARCHAR(20) NOT NULL DEFAULT 'percentage',
                value DECIMAL(10,2) NOT NULL,
                min_order_amount DECIMAL(10,2) NULL,
                max_discount_amount DECIMAL(10,2) NULL,
                usage_limit INTEGER NULL,
                used_count INTEGER DEFAULT 0,
                start_date DATETIME NULL,
                end_date DATETIME NULL,
                status VARCHAR(50) DEFAULT 'active',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Deals table
            "CREATE TABLE IF NOT EXISTS deals (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title_ar VARCHAR(255) NOT NULL,
                title_en VARCHAR(255) NOT NULL,
                banner VARCHAR(255) NULL,
                discount_percentage INTEGER DEFAULT 0,
                start_date DATETIME NULL,
                end_date DATETIME NULL,
                status VARCHAR(50) DEFAULT 'active',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Reviews table
            "CREATE TABLE IF NOT EXISTS reviews (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                product_id INTEGER NOT NULL,
                user_id INTEGER NULL,
                author_name VARCHAR(100) NOT NULL,
                author_email VARCHAR(191) NOT NULL,
                rating INTEGER NOT NULL DEFAULT 5,
                comment TEXT NOT NULL,
                is_verified_purchase INTEGER DEFAULT 0,
                is_approved INTEGER DEFAULT 1,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            );",

            // Wishlists table
            "CREATE TABLE IF NOT EXISTS wishlists (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NULL,
                session_id VARCHAR(100) NULL,
                product_id INTEGER NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            );",

            // Compares table
            "CREATE TABLE IF NOT EXISTS compares (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NULL,
                session_id VARCHAR(100) NULL,
                product_id INTEGER NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            );",

            // Affiliate Commissions
            "CREATE TABLE IF NOT EXISTS affiliate_commissions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                affiliate_id INTEGER NOT NULL,
                order_id INTEGER NOT NULL,
                order_total DECIMAL(10,2) NOT NULL,
                commission_rate DECIMAL(5,2) NOT NULL,
                commission_amount DECIMAL(10,2) NOT NULL,
                status VARCHAR(50) DEFAULT 'pending',
                payout_date DATETIME NULL,
                notes TEXT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (affiliate_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            );",

            // Return Requests
            "CREATE TABLE IF NOT EXISTS return_requests (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                product_id INTEGER NULL,
                reason VARCHAR(255) NOT NULL,
                details TEXT NOT NULL,
                images_json TEXT NULL,
                status VARCHAR(50) DEFAULT 'pending',
                admin_notes TEXT NULL,
                refund_amount DECIMAL(10,2) NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );",

            // Pages table
            "CREATE TABLE IF NOT EXISTS pages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                slug VARCHAR(100) UNIQUE NOT NULL,
                title_ar VARCHAR(255) NOT NULL,
                title_en VARCHAR(255) NOT NULL,
                content_ar TEXT NOT NULL,
                content_en TEXT NOT NULL,
                meta_title VARCHAR(255) NULL,
                meta_description TEXT NULL,
                status VARCHAR(50) DEFAULT 'published',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Settings table
            "CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                `key` VARCHAR(100) UNIQUE NOT NULL,
                value TEXT NULL,
                `group` VARCHAR(50) DEFAULT 'general',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Theme Settings table
            "CREATE TABLE IF NOT EXISTS theme_settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                `key` VARCHAR(100) UNIQUE NOT NULL,
                value TEXT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Visitors table
            "CREATE TABLE IF NOT EXISTS visitors (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip_address VARCHAR(100) NOT NULL,
                current_url VARCHAR(255) NOT NULL,
                referrer VARCHAR(255) NULL,
                user_agent VARCHAR(255) NULL,
                device VARCHAR(50) DEFAULT 'Desktop',
                browser VARCHAR(50) DEFAULT 'Chrome',
                last_activity DATETIME NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );",

            // Activity Logs table
            "CREATE TABLE IF NOT EXISTS activity_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NULL,
                user_name VARCHAR(100) NULL,
                action VARCHAR(100) NOT NULL,
                module VARCHAR(100) NOT NULL,
                details TEXT NULL,
                ip_address VARCHAR(100) NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            );"
        ];

        foreach ($statements as $sql) {
            $pdo->exec($sql);
        }
    }
}
