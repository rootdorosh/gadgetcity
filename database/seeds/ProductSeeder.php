<?php

use Illuminate\Database\Seeder;
use App\Modules\Product\Models\Product;
//use DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('DELETE FROM product');
        DB::statement('DELETE FROM product_providers_items');
        DB::statement('UPDATE product_providers SET last_guid = NULL');

        $file = base_path() . '/database/data/products.txt';
        $items = file($file);
        foreach ($items as $item) {
            $title = trim($item);
            if (empty($title)) {
                continue;
            }

            $is_active = 1;
            $product = Product::updateOrCreate(['title' => $title], compact('title', 'is_active'));
            echo $product->id . "\n";
        }
    }
}
