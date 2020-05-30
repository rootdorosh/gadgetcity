<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductProviderItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('product_providers_items', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('provider_id');
			$table->string('title');
			$table->unsignedInteger('product_id')->nullable();
			$table->boolean('status')->default('1');
			$table->double('price', 10, 2)->nullable();
			
			$table->foreign('provider_id')->references('id')->on('product_providers')->onDelete('CASCADE');
			$table->foreign('product_id')->references('id')->on('product')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::dropIfExists('product_providers_items');
    }
}
