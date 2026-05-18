<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $banners    = Banner::active()->get();
        $bestSeller = Menu::available()->where('is_best_seller', true)->with('category')->take(6)->get();
        $featured   = Menu::available()->where('is_featured', true)->with('category')->take(8)->get();
        $categories = MenuCategory::active()->withCount('activeMenus')->orderBy('sort_order')->get();

        $stats = [
            'total_menu'     => Menu::where('is_available', true)->count(),
            'total_orders'   => \App\Models\Order::where('status', 'completed')->count(),
            'happy_customers'=> \App\Models\User::where('role', 'customer')->count(),
            'rating_avg'     => round(Menu::avg('rating'), 1),
        ];
    
        return view('customer.home.index', compact(
            'banners', 'bestSeller', 'featured', 'categories', 'stats'
        ));
    }
}