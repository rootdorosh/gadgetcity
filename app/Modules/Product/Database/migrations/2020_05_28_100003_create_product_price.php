<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('product_prices', function (Blueprint $table) {
			$table->unsignedInteger('product_id');
			$table->unsignedInteger('provider_id');
			$table->double('price', 10, 2);
			$table->unique(['product_id', 'provider_id'], 'product_prices_unique');
			
			$table->foreign('product_id')->references('id')->on('product')->onDelete('CASCADE');
			$table->foreign('provider_id')->references('id')->on('product_providers')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::dropIfExists('product_prices');
    }
}
