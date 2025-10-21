<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\Session;

class CouponService
{
    public function validate(string $code, float $cartTotal): array
    {
        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) return ['success' => false, 'message' => 'Invalid coupon code'];
        if ($coupon->min_order && $cartTotal < $coupon->min_order) {
            return ['success' => false, 'message' => 'Cart total too low for this coupon'];
        }

        $discount = $coupon->type === 'fixed'
            ? $coupon->value
            : $cartTotal * ($coupon->value / 100);

        return ['success' => true, 'discount' => $discount, 'message' => 'Coupon applied'];
    }
}
