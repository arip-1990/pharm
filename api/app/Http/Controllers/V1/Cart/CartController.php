<?php

namespace App\Http\Controllers\V1\Cart;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(User $user)
    {
        $cart = $user->cart()->with('items.product')->firstOrCreate([]);
        return response()->json($cart);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $cart = $user->cart()->firstOrCreate([]);

        $product = Product::findOrFail($request->product_id);

        // Если уже есть — увеличиваем количество
        $item = $cart->items()->where('product_id', $product->id)->first();
        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'price'      => $product->price,
            ]);
        }

        return response()->json(['message' => 'Product added to cart']);
    }

    public function removeFromCart(Request $request, $productId)
    {
        $cart = $request->user()->cart;

        if (!$cart) return response()->json(['message' => 'Cart not found'], 404);

        $cart->items()->where('product_id', $productId)->delete();

        return response()->json(['message' => 'Product removed from cart']);
    }

}
