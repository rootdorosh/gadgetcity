<?php

use Illuminate\Database\Seeder;
use App\Modules\Product\Models\Provider;
use App\Modules\Color\Models\Color;

class ColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = base_path() . '/database/data/colors.txt';
        $items = file($file);
        foreach ($items as $item) {
            $title = trim($item);
            if (empty($title)) {
                continue;
            }
            $code = $title;
            $color = Color::updateOrCreate(['code' => $code], compact('code', 'title'));
            echo $color->id . "\n";
        }
    }
}
