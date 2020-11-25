<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedPatterns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "INSERT INTO `pattern` (`id`, `example`, `value`, `rank`) VALUES
                    (1, '7 Plus 32 - 280$', '/([0-9]{1,10})\\$/', 1),
                    (2, '7 Plus 32 - $ 280', '/\\$\\s([0-9]{1,10})/', 2),
                    (3, '7 Plus 32 - USD 280', '/\\U\\S\\D\\s([0-9]{1,10})/', 3),
                    (4, '7 Plus 32 - $280', '/\\$([0-9]{1,10})/', 4),
                    (5, '7 Plus 32 - $280.00', '/\\$(([0-9]{1,10})\\.00)/', 1),
                    (6, '7 Plus 32 - USD 280.00', '/\\U\\S\\D\\s(([0-9]{1,10})\\.00)/', 0),
                    (7, '7 Plus 32 - USD280.00', '/\\U\\S\\D(([0-9]{1,10})\\.00)/', 0),
                    (8, 'SE 16  - 100', '/\\s\\-\\s([0-9]{1,10})$/', 100),
                    (9, 'Used 7 32 Black - 170-180$', '/(\\s\\-\\s[0-9]{1,10}\\-([0-9]{1,10}))\\$$/', 0),
                    (10, '12 64 GB - 1300 $', '/\\s\\-\\s([0-9]{1,10})\\s\\$/', 0),
                    (11, '# Apple Pencil (MK0C2) 110 $', '/\\s([0-9]{1,10})\\s\\$/', 1),
                    (12, 'MacBook Pro 13 space  -1530 $', '/\\s\\-([0-9]{1,10})\\s\\$/', 1),
                    (13, 'Ipad Pro 11 256 silver 930', '/\\s([0-9]{1,10})$/', 99),
                    (14, 'Xs Max 256 gold -760', '/\\s\\-([0-9]{1,10})$/', 98);
                    ";
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
