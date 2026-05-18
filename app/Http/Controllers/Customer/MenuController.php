<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with(['category'])->available();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('order_count', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('rating', 'desc');
                    break;
                default:
                    $query->orderBy('is_best_seller', 'desc')->orderBy('order_count', 'desc');
                    break;
            }
        } else {
            $query->orderBy('is_best_seller', 'desc')->orderBy('order_count', 'desc');
        }

        $menus      = $query->paginate(12)->withQueryString();
        $categories = MenuCategory::active()->orderBy('sort_order')->get();

        // Favorites
        $favoriteIds = [];
        if (Auth::check()) {
            $favoriteIds = Favorite::where('user_id', Auth::id())->pluck('menu_id')->toArray();
        }

        if ($request->ajax()) {
            return response()->json([
                'html'    => view('customer.menu.partials.grid', compact('menus', 'favoriteIds'))->render(),
                'hasMore' => $menus->hasMorePages(),
            ]);
        }

        return view('customer.menu.index', compact('menus', 'categories', 'favoriteIds'));
    }

    public function show(string $slug)
    {
        $menu    = Menu::where('slug', $slug)->with(['category', 'reviews.user', 'images'])->firstOrFail();
        $related = Menu::where('category_id', $menu->category_id)
            ->where('id', '!=', $menu->id)
            ->available()
            ->take(4)
            ->get();

        $isFavorite = Auth::check()
            ? Favorite::where('user_id', Auth::id())->where('menu_id', $menu->id)->exists()
            : false;

        return view('customer.menu.show', compact('menu', 'related', 'isFavorite'));
    }

    public function toggleFavorite(int $menuId)
    {
        $existing = Favorite::where('user_id', Auth::id())->where('menu_id', $menuId)->first();

        if ($existing) {
            $existing->delete();
            $status = false;
        } else {
            Favorite::create(['user_id' => Auth::id(), 'menu_id' => $menuId]);
            $status = true;
        }

        return response()->json(['favorited' => $status]);
    }
}