<?php

namespace App\Services;

use App\Interfaces\ProductRepositoryInterface;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected $repo;

    public function __construct(ProductRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }
        public function all()
    {
        return $this->repo->all();
    }
        public function getActive()
    {
        return $this->repo->allActive(); 
    }

    public function create(array $data)
    {
        $data = $this->handleImage($data);
        return $this->repo->create($data);
    }

    public function update(int $id, array $data)
    {
        $data = $this->handleImage($data, $id);
        return $this->repo->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->repo->delete($id);
    }

    private function handleImage(array $data, int $productId = null)
{
    if (isset($data['image'])) {
        if ($productId) {
            $oldProduct = $this->repo->find($productId);
            if ($oldProduct) {
                if ($oldProduct->image && Storage::disk('public')->exists($oldProduct->image)) {
                    Storage::disk('public')->delete($oldProduct->image);
                }
                $oldWebpPath = str_replace('products/', 'products/webp/', $oldProduct->image);
                if (Storage::disk('public')->exists($oldWebpPath)) {
                    Storage::disk('public')->delete($oldWebpPath);
                }
            }
        }

        $originalFolder = 'products/original/';
        $originalName = $originalFolder . uniqid() . '.' . $data['image']->getClientOriginalExtension();
        Storage::disk('public')->putFileAs($originalFolder, $data['image'], basename($originalName));

        $webpFolder = 'products/webp/';
        $webpName = $webpFolder . uniqid() . '.webp';
        $img = Image::make($data['image'])->encode('webp', 90);
        Storage::disk('public')->put($webpName, $img);

        $data['image'] = $webpName;
        $data['original_image'] = $originalName;
    }

    return $data;
}

}
