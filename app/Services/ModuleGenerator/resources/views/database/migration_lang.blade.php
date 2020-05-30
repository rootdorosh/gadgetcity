<?php 
use Illuminate\Support\Str;
use App\Services\ModuleGenerator\ModuleGeneratorService;

$uniqueName = !empty($model['translatable']['unique_name']) ? $model['translatable']['unique_name'] : $model['table'] . '_unique';
$ownerForName = !empty($model['translatable']['foreign_name']) ? $model['translatable']['foreign_name'] : $model['table'] . '_owner';

?>
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Create{{ ucfirst(Str::camel($moduleName)) }}{{ ucfirst(Str::camel($model['name'])) }}Lang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('{{ $model['table'] }}_lang', function (Blueprint $table) {
{!! ModuleGeneratorService::migration_lang($model) !!}        
          
            $table->unique(['{{ $model['translatable']['owner_id'] }}', 'locale'], '{{ $uniqueName }}');
            $table->foreign('{{ $model['translatable']['owner_id'] }}', '{{ $ownerForName }}')->references('id')->on('{{ $model['table'] }}')->onDelete('cascade');        
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{{ $model['table'] }}_lang');
    }
}
