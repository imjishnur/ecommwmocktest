<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'cart';
    protected string $couponKey = 'coupon';

    public function add(int $productId, int $qty = 1): void
    {
        $cart = Session::get($this->sessionKey, []);
        if (isset($cart[$productId])) {
            $cart[$productId] += $qty;
        } else {
            $cart[$productId] = $qty;
        }
        Session::put($this->sessionKey, $cart);
    }

    public function remove(int $productId): void
    {
        $cart = Session::get($this->sessionKey, []);
        unset($cart[$productId]);
        Session::put($this->sessionKey, $cart);
    }

    public function getItems(): array
    {
        $cart = Session::get($this->sessionKey, []);
        $items = [];

        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);
            if (!$product) continue;

            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'subtotal' => $product->price * $qty,
            ];
        }

        return $items;
    }

    public function getSubtotal(): float
    {
        return collect($this->getItems())->sum('subtotal');
    }

    public function setCoupon(string $code, float $discount): void
    {
        Session::put($this->couponKey, [
            'code' => $code,
            'discount' => $discount,
        ]);
    }

    public function getDiscount(): float
    {
        return Session::get($this->couponKey.'.discount', 0);
    }

    public function getTotal(): float
    {
        return $this->getSubtotal() - $this->getDiscount();
    }

    public function clear(): void
    {
        Session::forget([$this->sessionKey, $this->couponKey]);
    }

    public function checkStock(): array
    {
        $cartItems = $this->getItems();
        foreach ($cartItems as $item) {
            $product = $item['product'];
            if ($product->qty < $item['qty']) {
                return [
                    'success' => false,
                    'message' => "Insufficient stock for {$product->name}. Available: {$product->qty}"
                ];
            }
        }

        return ['success' => true];
    }


    public function checkout(): array
    {
        $cartItems = $this->getItems();
        if (empty($cartItems)) {
            return ['success' => false, 'message' => 'Cart is empty.'];
        }

        
        $stockCheck = $this->checkStock();
        if (!$stockCheck['success']) {
            return $stockCheck; 
        }

        $subtotal = $this->getSubtotal();
        $discount = $this->getDiscount();
        $total = $this->getTotal();
        $coupon = Session::get('coupon.code', null);

        $order = Order::create([
            'customer_name' => 'Test Name',
            'customer_email' => 'test@example.com',
            'customer_address' => 'Sample Address',
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'coupon_code' => $coupon,
            'status' => 1,
        ]);

        foreach ($cartItems as $item) {
            $product = $item['product'];
            $product->decrement('qty', $item['qty']);

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $item['qty'],
                'price' => $product->price,
                'subtotal' => $item['subtotal'],
            ]);
        }

        $this->clear();

        return ['success' => true, 'message' => 'Checkout successful!', 'order_id' => $order->id];
    }

}
