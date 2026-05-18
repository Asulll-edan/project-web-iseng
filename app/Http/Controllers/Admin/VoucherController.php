<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::withTrashed()->latest()->paginate(20);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create() { return $this->index(); }
    public function edit(int $id) { return $this->index(); }

    public function store(Request $request)
    {
        $request->validate([
            'code'      => 'required|string|unique:vouchers,code|max:30',
            'name'      => 'required|string|max:200',
            'type'      => 'required|in:percent,fixed',
            'value'     => 'required|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
        ]);

        Voucher::create(array_merge(
            $request->only('code','name','description','type','value','min_order','max_discount','max_usage','start_at','end_at'),
            ['code' => strtoupper($request->code), 'is_active' => $request->boolean('is_active', true)]
        ));

        return back()->with('success', 'Voucher berhasil dibuat!');
    }

    public function update(Request $request, int $id)
    {
        $voucher = Voucher::withTrashed()->findOrFail($id);
        $data = $request->only('name','description','type','value','min_order','max_discount','max_usage','start_at','end_at');
        $data['is_active'] = $request->boolean('is_active');
        $voucher->update($data);
        return back()->with('success', 'Voucher diperbarui!');
    }

    public function destroy(int $id)
    {
        Voucher::findOrFail($id)->delete();
        return back()->with('success', 'Voucher dihapus.');
    }

    public function toggle(int $id)
    {
        $v = Voucher::findOrFail($id);
        $v->update(['is_active' => !$v->is_active]);
        return response()->json(['is_active' => $v->is_active]);
    }
}
