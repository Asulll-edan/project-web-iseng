<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Menu;
use App\Models\User;

class CartService
{
    public function getOrCreate(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    public function addItem(User $user, int $menuId, int $quantity, ?string $note = null): CartItem
    {
        $menu = Menu::findOrFail($menuId);

        if (!$menu->is_available || $menu->stock < $quantity) {
            throw new \Exception('Menu tidak tersedia atau stok tidak mencukupi.');
        }

        $cart = $this->getOrCreate($user);

        $existing = CartItem::where('cart_id', $cart->id)
            ->where('menu_id', $menuId)
            ->first();

        if ($existing) {
            $newQty = $existing->quantity + $quantity;
            if ($menu->stock < $newQty) {
                throw new \Exception('Stok tidak mencukupi.');
            }
            $existing->update(['quantity' => $newQty]);
            return $existing;
        }

        return CartItem::create([
            'cart_id'  => $cart->id,
            'menu_id'  => $menuId,
            'quantity' => $quantity,
            'note'     => $note,
            'price'    => $menu->effective_price,
        ]);
    }

    public function updateItem(User $user, int $cartItemId, int $quantity): CartItem
{
    $cart = $this->getOrCreate($user);
    $item = CartItem::where('cart_id', $cart->id)->where('id', $cartItemId)->firstOrFail();

    if ($quantity <= 0) {
        $item->delete();
        return $item;
    }

    // Tambah batas max dari menu stock atau config
    if ($item->menu->stock < $quantity) {
        throw new \Exception('Stok tidak mencukupi.');
    }

    $item->update(['quantity' => $quantity]);
    return $item;
}

    public function removeItem(User $user, int $cartItemId): void
    {
        $cart = $this->getOrCreate($user);
        CartItem::where('cart_id', $cart->id)->where('id', $cartItemId)->delete();
    }

    public function clear(User $user): void
    {
        $cart = $this->getOrCreate($user);
        $cart->items()->delete();
    }

    public function getCartWithItems(User $user): Cart
    {
        return Cart::where('user_id', $user->id)
            ->with(['items.menu.category'])
            ->firstOrCreate(['user_id' => $user->id]);
    }
}