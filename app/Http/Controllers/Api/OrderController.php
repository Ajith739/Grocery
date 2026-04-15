<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // 📦 POST /api/orders
    public function store(Request $request)
    {
        $user = $request->user();

        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $total = 0;

            // ✅ calculate total + stock check
            foreach ($cartItems as $item) {
                if ($item->quantity > $item->product->quantity) {
                    throw new \Exception("Stock not available for {$item->product->name}");
                }

                $total += $item->quantity * $item->product->price;
            }

            // ✅ create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(6)),
                'total_amount' => $total,
                'status' => 'pending'
            ]);

            // ✅ create order items + reduce stock
            foreach ($cartItems as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price
                ]);

                // 🔥 reduce stock
                $item->product->decrement('quantity', $item->quantity);
            }

            // ✅ clear cart
            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => $order->load('items.product.category')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Order failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 📦 GET /api/orders
    public function index(Request $request)
    {
        $orders = Order::with('items.product.category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // 📦 GET /api/orders/:id
    public function show($id, Request $request)
    {
        $order = Order::with('items.product.category')
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json($order);
    }
}
