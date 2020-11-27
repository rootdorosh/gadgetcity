<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedPattern extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("DELETE FROM pattern");
        
        $sql = "INSERT INTO `pattern` (`id`, `example`, `value`, `rank`) VALUES
(16, '7 Plus 32 - 2 800.00$', '[space]-[space][price]$', 1),
(17, '12 64 GB - 1300 $', '([space]-[space][price][space]$)', 2),
(18, '7 Plus 32 - $280', '[space]-[space]$[price]', 1),
(19, '7 Plus 32 - $ 280', '[space]-[space]$[space][price]', 1),
(20, '7 Plus 32 - 280 USD', '[space]-[space][price][space]USD', 1),
(21, '7 Plus 32 - 800-900$', '[space]-[space][price]-[price]$', 0),
(22, '7 Plus 32 - 800/900$', '[space]-[space][price]/[price]$', 0),
(23, 'Huawei P40 Pro 8/256GB Black EU 810', '[space][price_end]', 100),
(24, '7+ 128 Silver - 300/280', '[space]-[space][price]/[price]', 99),
(25, 'Xs Max 256 gold -720', '[space]-[price_end]', 101),
(26, 'X 64 space - 460:450', '[space]-[space][price]:[price]', 102),
(27, '8 plus 64 silver black gold- 360/340', '-[space][price]/[price]', 102),
(28, 'Space Gray 2019 (MV972)—1480', '—[price_end]', 102),
(29, 'Space Gray (MVH22) 2020--1320', '--[price_end]', 102),
(30, 'Space Gray (MVH22) 2020-1320', '-[price_end]', 103),
(31, 'Apple iMac Pro (MHLV3)-- 585', '--[space][price]', 104);";
        \Illuminate\Support\Facades\DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
