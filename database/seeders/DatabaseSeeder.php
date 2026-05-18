<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Membership;
use App\Models\LoyaltyPoint;
use App\Models\UserProfile;
use App\Models\MenuCategory;
use App\Models\Menu;
use App\Models\RestaurantTable;
use App\Models\Banner;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET session_replication_role = replica;'); // disable FK checks for postgres

        // =============================================
        // CREATE 4 DEMO ACCOUNTS (1 per role)
        // =============================================

        $users = [
            [
                'name'       => 'Super Admin',
                'email'      => 'superadmin@ras.com',
                'phone'      => '081234567890',
                'password'   => Hash::make('password'),
                'role'       => 'superadmin',
                'status'     => 'active',
                'avatar'     => null,
                'bio'        => 'Super Administrator Rumahnya Anak Sekolah',
            ],
            [
                'name'       => 'Kasir RAS',
                'email'      => 'kasir@ras.com',
                'phone'      => '081234567891',
                'password'   => Hash::make('password'),
                'role'       => 'kasir',
                'status'     => 'active',
                'avatar'     => null,
                'bio'        => 'Kasir Rumahnya Anak Sekolah',
            ],
            [
                'name'       => 'Kitchen RAS',
                'email'      => 'kitchen@ras.com',
                'phone'      => '081234567892',
                'password'   => Hash::make('password'),
                'role'       => 'kitchen',
                'status'     => 'active',
                'avatar'     => null,
                'bio'        => 'Chef Rumahnya Anak Sekolah',
            ],
            [
                'name'       => 'Budi Santoso',
                'email'      => 'customer@ras.com',
                'phone'      => '081234567893',
                'password'   => Hash::make('password'),
                'role'       => 'customer',
                'status'     => 'active',
                'avatar'     => null,
                'bio'        => 'Pelanggan setia Rumahnya Anak Sekolah',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(['email' => $userData['email']], $userData);

            // Create profile for each user
            UserProfile::updateOrCreate(['user_id' => $user->id], [
                'display_name' => $userData['name'],
                'profile_completion' => 60,
            ]);

            // Create wallet for each user
            $wallet = Wallet::updateOrCreate(['user_id' => $user->id], [
                'balance'       => $userData['role'] === 'customer' ? 150000 : 0,
                'total_topup'   => $userData['role'] === 'customer' ? 150000 : 0,
                'total_spent'   => 0,
                'is_active'     => true,
            ]);

            // Create membership for customer
            Membership::updateOrCreate(['user_id' => $user->id], [
                'tier'             => $userData['role'] === 'customer' ? 'silver' : 'none',
                'completed_orders' => $userData['role'] === 'customer' ? 12 : 0,
                'cashback_rate'    => $userData['role'] === 'customer' ? 2.0 : 0,
                'is_active'        => true,
            ]);

            // Create loyalty points
            LoyaltyPoint::updateOrCreate(['user_id' => $user->id], [
                'total_points'     => $userData['role'] === 'customer' ? 240 : 0,
                'used_points'      => 0,
                'available_points' => $userData['role'] === 'customer' ? 240 : 0,
            ]);
        }

        // =============================================
        // MENU CATEGORIES
        // =============================================
        $categories = [
            ['name' => 'Menu Utama',     'slug' => 'menu-utama',     'icon' => '🍱', 'sort_order' => 1],
            ['name' => 'Minuman',        'slug' => 'minuman',        'icon' => '☕', 'sort_order' => 2],
            ['name' => 'Gorengan',       'slug' => 'gorengan',       'icon' => '🥟', 'sort_order' => 3],
            ['name' => 'Dessert & Snack','slug' => 'dessert-snack',  'icon' => '🍮', 'sort_order' => 4],
            ['name' => 'Paket Hemat',    'slug' => 'paket-hemat',    'icon' => '💰', 'sort_order' => 5],
        ];

        foreach ($categories as $cat) {
            MenuCategory::updateOrCreate(['slug' => $cat['slug']], array_merge($cat, ['is_active' => true]));
        }

        $menuUtama     = MenuCategory::where('slug', 'menu-utama')->first();
        $menuMinuman   = MenuCategory::where('slug', 'minuman')->first();
        $menuGorengan  = MenuCategory::where('slug', 'gorengan')->first();
        $menuDessert   = MenuCategory::where('slug', 'dessert-snack')->first();
        $menuPaket     = MenuCategory::where('slug', 'paket-hemat')->first();

        // =============================================
        // MENUS
        // =============================================
        $menus = [
            // Menu Utama
            [
                'category_id'       => $menuUtama->id,
                'name'              => 'Nasi Ayam Geprek Anak Sekolah',
                'slug'              => 'nasi-ayam-geprek-anak-sekolah',
                'description'       => 'Ayam geprek crispy level 1-5, nasi putih pulen, lalapan segar, sambal matah khas anak sekolah. Menu andalan yang bikin nagih!',
                'price'             => 18000,
                'stock'             => 50,
                'is_available'      => true,
                'is_best_seller'    => true,
                'is_featured'       => true,
                'rating'            => 4.8,
                'review_count'      => 324,
                'order_count'       => 1248,
                'preparation_time'  => '10-15 menit',
                'calories'          => 520,
            ],
            [
                'category_id'       => $menuUtama->id,
                'name'              => 'Mie Instan Premium Kantin',
                'slug'              => 'mie-instan-premium-kantin',
                'description'       => 'Mie instan upgrade premium dengan topping telur, cakwe, sosis, dan sayuran segar. Bukan mie biasa!',
                'price'             => 14000,
                'stock'             => 40,
                'is_available'      => true,
                'is_best_seller'    => false,
                'is_featured'       => false,
                'rating'            => 4.6,
                'review_count'      => 218,
                'order_count'       => 867,
                'preparation_time'  => '7-10 menit',
                'calories'          => 380,
            ],
            [
                'category_id'       => $menuUtama->id,
                'name'              => 'Rice Bowl Hemat Pelajar',
                'slug'              => 'rice-bowl-hemat-pelajar',
                'description'       => 'Rice bowl premium dengan pilihan topping: ayam teriyaki, rendang, atau tongseng. Kenyang, enak, terjangkau!',
                'price'             => 16000,
                'stock'             => 35,
                'is_available'      => true,
                'is_best_seller'    => false,
                'is_featured'       => true,
                'rating'            => 4.5,
                'review_count'      => 156,
                'order_count'       => 634,
                'preparation_time'  => '8-12 menit',
                'calories'          => 450,
            ],
            [
                'category_id'       => $menuUtama->id,
                'name'              => 'Salad Wrap Sehat Anak Sekolah',
                'slug'              => 'salad-wrap-sehat-anak-sekolah',
                'description'       => 'Wrap tortilla premium isi salad segar, ayam panggang, keju, dan saus thousand island. Sehat dan mengenyangkan!',
                'price'             => 20000,
                'stock'             => 25,
                'is_available'      => true,
                'is_best_seller'    => true,
                'is_featured'       => true,
                'rating'            => 4.9,
                'review_count'      => 412,
                'order_count'       => 1567,
                'preparation_time'  => '5-8 menit',
                'calories'          => 340,
            ],

            // Minuman
            [
                'category_id'       => $menuMinuman->id,
                'name'              => 'Coffee Anak Sekolah - Kopi Susu',
                'slug'              => 'coffee-anak-sekolah-kopi-susu',
                'description'       => 'Kopi susu kekinian dengan biji kopi pilihan, susu fresh milk premium, dan gula aren asli. Bukan kopi biasa!',
                'price'             => 15000,
                'stock'             => 60,
                'is_available'      => true,
                'is_best_seller'    => true,
                'is_featured'       => true,
                'rating'            => 4.9,
                'review_count'      => 528,
                'order_count'       => 2134,
                'preparation_time'  => '5-7 menit',
                'calories'          => 180,
            ],
            [
                'category_id'       => $menuMinuman->id,
                'name'              => 'Coffee Anak Sekolah - Espresso',
                'slug'              => 'coffee-anak-sekolah-espresso',
                'description'       => 'Espresso double shot dengan crema sempurna. Untuk kamu yang butuh semangat ekstra buat belajar!',
                'price'             => 12000,
                'stock'             => 60,
                'is_available'      => true,
                'is_best_seller'    => false,
                'is_featured'       => false,
                'rating'            => 4.7,
                'review_count'      => 234,
                'order_count'       => 876,
                'preparation_time'  => '3-5 menit',
                'calories'          => 20,
            ],
            [
                'category_id'       => $menuMinuman->id,
                'name'              => 'Es Teh Manis Premium',
                'slug'              => 'es-teh-manis-premium',
                'description'       => 'Teh premium seduh panas, dinginkan dengan es batu kristal bersih. Seger banget cocok buat siang-siang!',
                'price'             => 6000,
                'stock'             => 80,
                'is_available'      => true,
                'is_best_seller'    => false,
                'is_featured'       => false,
                'rating'            => 4.4,
                'review_count'      => 189,
                'order_count'       => 1234,
                'preparation_time'  => '2-3 menit',
                'calories'          => 90,
            ],

            // Gorengan
            [
                'category_id'       => $menuGorengan->id,
                'name'              => 'Gorengan Kantin Premium',
                'slug'              => 'gorengan-kantin-premium',
                'description'       => 'Mix gorengan premium: bakwan jagung, tempe mendoan, tahu isi, pisang goreng coklat. 5 pcs crispy gurih!',
                'price'             => 10000,
                'stock'             => 45,
                'is_available'      => true,
                'is_best_seller'    => false,
                'is_featured'       => false,
                'rating'            => 4.5,
                'review_count'      => 267,
                'order_count'       => 987,
                'preparation_time'  => '5-10 menit',
                'calories'          => 320,
            ],

            // Dessert
            [
                'category_id'       => $menuDessert->id,
                'name'              => 'Dessert & Snack Box Sekolah',
                'slug'              => 'dessert-snack-box-sekolah',
                'description'       => 'Box dessert premium: pudding coklat, brownies fudge, dan roti bakar nutella. Manis banget buat reward belajar!',
                'price'             => 22000,
                'stock'             => 20,
                'is_available'      => true,
                'is_best_seller'    => false,
                'is_featured'       => true,
                'rating'            => 4.8,
                'review_count'      => 145,
                'order_count'       => 432,
                'preparation_time'  => '5-8 menit',
                'calories'          => 480,
            ],
        ];

        foreach ($menus as $menuData) {
            Menu::updateOrCreate(['slug' => $menuData['slug']], $menuData);
        }

        // =============================================
        // RESTAURANT TABLES
        // =============================================
        for ($i = 1; $i <= 12; $i++) {
            RestaurantTable::updateOrCreate(['table_number' => "T{$i}"], [
                'capacity' => in_array($i, [1,2,3,4]) ? 2 : (in_array($i, [5,6,7,8]) ? 4 : 6),
                'status'   => 'available',
                'location' => $i <= 4 ? 'Indoor Depan' : ($i <= 8 ? 'Indoor Belakang' : 'Outdoor'),
                'is_active' => true,
            ]);
        }

        // =============================================
        // BANNERS
        // =============================================
        $banners = [
            [
                'title'       => 'Promo Spesial Pelajar - Diskon 20%',
                'image'       => 'banners/banner1.jpg',
                'link'        => '/menu',
                'description' => 'Dapatkan diskon 20% untuk pembelian pertama!',
                'is_active'   => true,
                'sort_order'  => 1,
            ],
            [
                'title'       => 'Menu Baru! Salad Wrap Premium',
                'image'       => 'banners/banner2.jpg',
                'link'        => '/menu/salad-wrap-sehat-anak-sekolah',
                'description' => 'Coba menu sehat terbaru kami!',
                'is_active'   => true,
                'sort_order'  => 2,
            ],
            [
                'title'       => 'Rumah sekolah Wallet - Topup Sekarang!',
                'image'       => 'banners/banner3.jpg',
                'link'        => '/wallet',
                'description' => 'Topup wallet dan dapatkan bonus!',
                'is_active'   => true,
                'sort_order'  => 3,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(['title' => $banner['title']], $banner);
        }

        // =============================================
        // SYSTEM SETTINGS
        // =============================================
        $settings = [
            ['key' => 'restaurant_name',   'value' => 'Rumahnya Anak Sekolah', 'group' => 'general'],
            ['key' => 'restaurant_tagline', 'value' => 'Premium Student Culinary Experience', 'group' => 'general'],
            ['key' => 'restaurant_phone',   'value' => '081234567890', 'group' => 'contact'],
            ['key' => 'restaurant_email',   'value' => 'hello@ras.com', 'group' => 'contact'],
            ['key' => 'restaurant_address', 'value' => 'Jl. Pelajar No. 1, Jakarta', 'group' => 'contact'],
            ['key' => 'open_time',          'value' => '07:00', 'group' => 'operation'],
            ['key' => 'close_time',         'value' => '21:00', 'group' => 'operation'],
            ['key' => 'tax_rate',           'value' => '10', 'group' => 'payment'],
            ['key' => 'silver_min_orders',  'value' => '10', 'group' => 'membership'],
            ['key' => 'gold_min_orders',    'value' => '30', 'group' => 'membership'],
            ['key' => 'platinum_min_orders','value' => '100', 'group' => 'membership'],
            ['key' => 'silver_cashback',    'value' => '2', 'group' => 'membership'],
            ['key' => 'gold_cashback',      'value' => '5', 'group' => 'membership'],
            ['key' => 'platinum_cashback',  'value' => '10', 'group' => 'membership'],
            ['key' => 'points_per_order',   'value' => '20', 'group' => 'loyalty'],
            ['key' => 'whatsapp_number',    'value' => '6281234567890', 'group' => 'contact'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], array_merge($setting, ['type' => 'string']));
        }

        DB::statement('SET session_replication_role = DEFAULT;');

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('🔑 DEMO ACCOUNTS:');
        $this->command->info('┌─────────────┬──────────────────────┬──────────┐');
        $this->command->info('│ Role        │ Email                │ Password │');
        $this->command->info('├─────────────┼──────────────────────┼──────────┤');
        $this->command->info('│ Superadmin  │ superadmin@ras.com   │ password │');
        $this->command->info('│ Kasir       │ kasir@ras.com        │ password │');
        $this->command->info('│ Kitchen     │ kitchen@ras.com      │ password │');
        $this->command->info('│ Customer    │ customer@ras.com     │ password │');
        $this->command->info('└─────────────┴──────────────────────┴──────────┘');
    }
}