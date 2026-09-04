<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
class CartController extends Controller {
    public function show(Cart $cart) { return $cart->load('items.product'); }
    public function add(Request $request) { $data = $request->validate(['cart_id'=>'nullable|exists:carts,id','product_id'=>'required|exists:products,id','quantity'=>'required|integer|min:1']); $cart = isset($data['cart_id']) ? Cart::findOrFail($data['cart_id']) : Cart::firstOrCreate(['session_id'=>$request->session()->getId()]); $product=Product::findOrFail($data['product_id']); $item=$cart->items()->updateOrCreate(['product_id'=>$product->id], ['quantity'=>$data['quantity'], 'unit_price'=>$product->price]); return response()->json($cart->load('items.product'), 201); }
}
