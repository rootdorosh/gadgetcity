<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductProvider extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('product_providers', function (Blueprint $table) {
			$table->increments('id');
			$table->string('pid');
			$table->string('title');
			$table->boolean('is_active')->default('0');
			$table->integer('last_guid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::dropIfExists('product_providers');
    }
}
