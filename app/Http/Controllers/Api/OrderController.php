<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function show(Order $order)
    {
        return $order->load('items');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['cart_id' => 'required|exists:carts,id', 'shipping_address' => 'required|array']);
        $order = DB::transaction(function () use ($data) {
            $cart = Cart::with('items.product')->findOrFail($data['cart_id']);
            abort_if($cart->items->isEmpty(), 422, 'Cart is empty');
            $order = Order::create(['shipping_address' => $data['shipping_address'], 'total' => 0]);
            $total = 0;
            foreach ($cart->items as $item) {
                abort_if($item->product->stock < $item->quantity, 422, "Insufficient stock for {$item->product->name}");
                $subtotal = $item->unit_price * $item->quantity;
                $order->items()->create(['product_id' => $item->product_id, 'product_name' => $item->product->name, 'quantity' => $item->quantity, 'unit_price' => $item->unit_price, 'subtotal' => $subtotal]);
                $item->product->decrement('stock', $item->quantity);
                $total += $subtotal;
            } $order->update(['total' => $total]);
            $cart->items()->delete();

            return $order->load('items');
        });

        return response()->json($order, 201);
    }
}
