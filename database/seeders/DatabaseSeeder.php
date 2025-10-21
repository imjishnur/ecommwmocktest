<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use App\Models\Product;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
         User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
          
        ]);
        $category = Category::create(['name' => 'Electronics']);
        $color = Color::create(['name' => 'Red']);
        $size = Size::create(['name' => 'M']);

        Product::create([
            'name' => 'Sample Product',
            'category_id' => $category->id,
            'color_id' => $color->id,
            'size_id' => $size->id,
            'qty' => 10,
            'price' => 199.99,
            'image' => null, 
        ]);
        
    \App\Models\Coupon::create([
        'code' => 'FIXED50',
        'type' => 'fixed',
        'value' => 50,
    ]);
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
