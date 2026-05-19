<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
private $cartService;
public function __construct(CartService $cartService)
{
    $this->cartService=$cartService;
}
    public function index()
    {
        $cart = $this->cartService->getCartWithItems(Auth::user());
        return view('customer.cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'menu_id'  => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1|max:20',
            'note'     => 'nullable|string|max:200',
        ]);

        try {
            $this->cartService->addItem(
                Auth::user(),
                $request->menu_id,
                $request->quantity,
                $request->note
            );

            $cart = $this->cartService->getCartWithItems(Auth::user());

            return response()->json([
                'success'     => true,
                'message'     => 'Menu berhasil ditambahkan ke keranjang! 🛒',
                'cart_count'  => $cart->total_items,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request, int $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:20']);

        try {
            $this->cartService->updateItem(Auth::user(), $itemId, $request->quantity);
            $cart = $this->cartService->getCartWithItems(Auth::user());

            return response()->json([
                'success'    => true,
                'cart_total' => number_format($cart->total, 0, ',', '.'),
                'cart_count' => $cart->total_items,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function remove(int $itemId)
    {
        $this->cartService->removeItem(Auth::user(), $itemId);
        $cart = $this->cartService->getCartWithItems(Auth::user());

        return response()->json([
            'success'    => true,
            'cart_count' => $cart->total_items,
        ]);
    }

    public function count()
    {
        $cart = $this->cartService->getOrCreate(Auth::user());
        return response()->json(['count' => $cart->items()->sum('quantity')]);
    }
}