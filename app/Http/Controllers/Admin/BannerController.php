<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::withTrashed()->orderBy('sort_order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $path = $request->file('image')->store('banners','public');

        Banner::create([
            'title'       => $request->title,
            'image'       => $path,
            'link'        => $request->link,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
            'sort_order'  => Banner::max('sort_order') + 1,
            'start_at'    => $request->start_at ?: null,
            'end_at'      => $request->end_at ?: null,
        ]);

        return back()->with('success', 'Banner ditambahkan!');
    }

    public function update(Request $request, int $id)
    {
        $banner = Banner::withTrashed()->findOrFail($id);
        $data = $request->only('title','link','description','sort_order','start_at','end_at');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            \Storage::disk('public')->delete($banner->image);
            $data['image'] = $request->file('image')->store('banners','public');
        }

        $banner->update($data);
        return back()->with('success', 'Banner diperbarui!');
    }

    public function destroy(int $id)
    {
        Banner::findOrFail($id)->delete();
        return back()->with('success', 'Banner dihapus.');
    }

    public function create() { return $this->index(); }
    public function edit(int $id) { return $this->index(); }
}
