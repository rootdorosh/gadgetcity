<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductProviderLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('product_provider_log', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('provider_id');
			$table->longText('content');
			$table->unsignedInteger('message_time');
			$table->unsignedInteger('create_time');

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
        Schema::dropIfExists('product_provider_log');
    }
}
