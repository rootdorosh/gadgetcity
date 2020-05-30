<?php 
use Illuminate\Support\Str;
use App\Services\ModuleGenerator\ModuleGeneratorService;

$tab5 = "                    ";
$tab4 = "                ";
$tab3 = "            ";
$tab2 = "        ";
$tab1 = "    ";
?>
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Create{{ ucfirst(Str::camel($moduleName)) }}{{ ucfirst(Str::camel($model['name'])) }} extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('{{ $model['table'] }}', function (Blueprint $table) {
{!! ModuleGeneratorService::migration($model['fields'], $model) !!}
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{{ $model['table'] }}');
    }
}
