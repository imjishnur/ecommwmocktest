<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductImport
{
    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function import()
    {
        $rows = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->file)
                    ->getActiveSheet()
                    ->toArray();

        foreach (array_slice($rows, 1) as $row) {
            [$name, $categoryName, $colorName, $sizeName, $qty, $price, $imageUrl] = $row;

            $category = Category::firstOrCreate(['name' => $categoryName]);
            $color = Color::firstOrCreate(['name' => $colorName]);
            $size = Size::firstOrCreate(['name' => $sizeName]);

            $productData = [
                'name' => $name,
                'category_id' => $category->id,
                'color_id' => $color->id,
                'size_id' => $size->id,
                'qty' => (int) $qty,
                'price' => (float) $price,
                'image' => null,
            ];

            if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                try {
                    $contents = file_get_contents($imageUrl);
                    if ($contents) {
                        $originalName = 'products/original/' . Str::random(10) . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);
                        Storage::disk('public')->put($originalName, $contents);

                        $img = Image::make($contents)->encode('webp', 90);
                        $webpName = 'products/webp/' . Str::random(10) . '.webp';
                        Storage::disk('public')->put($webpName, $img);

                        $productData['image'] = $webpName;
                    }
                } catch (\Exception $e) {
                    \Log::error('Image download failed for ' . $imageUrl . ': ' . $e->getMessage());
                }
            }

            Product::create($productData);
        }
    }
}
