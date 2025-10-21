<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
        public function allActive()
    {
        return Product::with(['category','color','size'])
                    ->latest()
                    ->paginate(10);
    }

    public function all()
    {
       return Product::with(['category', 'color', 'size'])
              ->latest()
              ->paginate(10);

    }

    public function find(int $id): ?Product
    {
        return Product::withTrashed()->find($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->find($id);
        $product->update($data);
        return $product;
    }

    public function delete(int $id): bool
    {
        $product = $this->find($id);
        return $product->delete();
    }
}
