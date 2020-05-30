<?php

use Illuminate\Database\Seeder;
use App\Modules\Product\Models\Provider;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = base_path() . '/database/data/providers.txt';
        $items = file($file);
        foreach ($items as $item) {
            list($title, $pid) = explode('|', $item);
            $title = trim($title);
            $pid = trim($pid);
            $is_active = 1;
            $provider = Provider::updateOrCreate(['pid' => $pid], compact('pid', 'title', 'is_active'));
            echo $provider->id . "\n";
        }
    }
}
