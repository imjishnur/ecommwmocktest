<?php

namespace App\Interfaces;

interface CartServiceInterface {
    public function add(int $productId, int $qty);
    public function remove(int $productId);
    public function getCart(): array;
    public function applyCoupon(string $code): string;
    public function getTotal(): float;
}
