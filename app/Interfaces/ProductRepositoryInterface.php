<?php

namespace App\Interfaces;

use App\Models\Product;

interface ProductRepositoryInterface
{
     public function allActive();
    public function all();
    public function find(int $id): ?Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): Product;
    public function delete(int $id): bool;
}
