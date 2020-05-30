<?php

use Illuminate\Database\Seeder;
use App\Modules\Product\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = base_path() . '/database/data/products.txt';
        $items = file($file);
        foreach ($items as $item) {
            $title = trim($item);
            $is_active = 1;
            $product = Product::updateOrCreate(['title' => $title], compact('title', 'is_active'));
            echo $product->id . "\n";
        }
    }
}
