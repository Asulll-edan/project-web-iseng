<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\MenuImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with('category')->withTrashed();

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%'.$request->search.'%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $menus      = $query->latest()->paginate(15)->withQueryString();
        $categories = MenuCategory::active()->get();

        return view('admin.menus.index', compact('menus', 'categories'));
    }

    public function create()
    {
        $categories = MenuCategory::active()->get();
        return view('admin.menus.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'      => 'required|exists:menu_categories,id',
            'name'             => 'required|string|max:200',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'preparation_time' => 'nullable|string',
            'calories'         => 'nullable|integer',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $data = $request->except('image');
        $data['slug'] = Str::slug($request->name) . '-' . Str::random(4);
        $data['is_available']   = $request->boolean('is_available');
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['is_featured']    = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        Menu::create($data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(int $id)
    {
        $menu       = Menu::withTrashed()->findOrFail($id);
        $categories = MenuCategory::active()->get();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $menu = Menu::withTrashed()->findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'name'        => 'required|string|max:200',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $data = $request->except('image');
        $data['is_available']   = $request->boolean('is_available');
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['is_featured']    = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            if ($menu->image) Storage::disk('public')->delete($menu->image);
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy(int $id)
    {
        Menu::findOrFail($id)->delete();
        return back()->with('success', 'Menu dihapus.');
    }

    public function toggle(int $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update(['is_available' => !$menu->is_available]);
        return response()->json(['is_available' => $menu->is_available]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate(['image' => 'required|image|mimes:jpg,jpeg,png,webp|max:3072']);
        $path = $request->file('image')->store('menus', 'public');
        return response()->json(['path' => $path, 'url' => asset('storage/'.$path)]);
    }
}
