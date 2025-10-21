<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Services\CouponService;

class CartController extends Controller
{
    protected CartService $cart;
    protected CouponService $couponService;

    public function __construct(CartService $cart, CouponService $couponService)
    {
        $this->cart = $cart;
        $this->couponService = $couponService;
    }

    public function index()
    {
        $cartItems = $this->cart->getItems();
        $total = $this->cart->getSubtotal();
        $discount = $this->cart->getDiscount();

        return view('frontend.cart.index', compact('cartItems', 'total', 'discount'));
    }

    public function add(Request $request)
    {
        $this->cart->add($request->product_id, $request->qty ?? 1);
        return response()->json(['success' => true, 'message' => 'Product added to cart']);
    }

    public function remove(int $id)
    {
        $this->cart->remove($id);
        return response()->json(['success' => true, 'message' => 'Product removed from cart']);
    }

    public function applyCoupon(Request $request)
    {
          $request->validate([
        'code' => 'required|string|max:50',
    ]);

        $code = $request->code;
        $subtotal = $this->cart->getSubtotal();

        $result = $this->couponService->validate($code, $subtotal);

        if ($result['success']) {
            $this->cart->setCoupon($code, $result['discount']);
        }

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'total' => number_format($this->cart->getTotal(), 2),
            'discount' => number_format($this->cart->getDiscount(), 2)
        ]);
    }

public function checkout()
{
    $result = $this->cart->checkout(); 

    return response()->json([
        'success' => $result['success'],
        'message' => $result['message'],
        'order_id' => $result['order_id'] ?? null
    ]);
}
public function orderSuccess($orderId)
{
    $order = \App\Models\Order::with('items.product')->findOrFail($orderId);

    return view('frontend.order.success', compact('order'));
}

}
