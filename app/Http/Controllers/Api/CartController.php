<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::with('product.category')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($cart);
    }

    public function add(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $userId = $request->user()->id;
        $results = [];

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);

            $cart = Cart::where('user_id', $userId)
                ->where('product_id', $item['product_id'])
                ->first();

            $currentCartQty = $cart ? $cart->quantity : 0;
            $requestedQty = (int) $item['quantity'];
            $remainingStock = $product->quantity - $currentCartQty;

            if ($requestedQty > $remainingStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requested quantity exceeds available stock for product ID ' . $item['product_id'],
                    'available_stock' => $remainingStock,
                    'total_stock' => $product->quantity,
                    'already_in_cart' => $currentCartQty
                ], 400);
            }

            if ($cart) {
                $cart->increment('quantity', $requestedQty);
                $cart->refresh();
            } else {
                $cart = Cart::create([
                    'user_id' => $userId,
                    'product_id' => $item['product_id'],
                    'quantity' => $requestedQty
                ]);
            }

            $results[] = $cart->fresh()->load('product.category');
        }

        return response()->json([
            'success' => true,
            'message' => 'Items added to cart',
            'data' => $results
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $userId = $request->user()->id;
        $results = [];

        foreach ($request->items as $item) {
            $cart = Cart::where('user_id', $userId)
                ->where('product_id', $item['product_id'])
                ->firstOrFail();

            $product = Product::findOrFail($item['product_id']);

            if ($item['quantity'] > $product->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requested quantity exceeds available stock for product ID ' . $item['product_id'],
                    'available_stock' => $product->quantity,
                    'total_stock' => $product->quantity,
                    'already_in_cart' => $cart->quantity
                ], 400);
            }

            $cart->update([
                'quantity' => $item['quantity']
            ]);

            $results[] = $cart->fresh()->load('product.category');
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'data' => $results
        ]);
    }

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        Cart::where('user_id', $request->user()->id)
            ->where('product_id', $validated['product_id'])
            ->delete();

        return response()->json([
            'message' => 'Item removed'
        ]);
    }
}