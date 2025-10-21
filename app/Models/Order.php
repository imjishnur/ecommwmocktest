<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_name', 'customer_phone', 'customer_address',
        'subtotal', 'discount', 'total', 'coupon_code', 'status'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
