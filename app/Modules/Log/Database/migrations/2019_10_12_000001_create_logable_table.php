<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logable', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('logable_id')->nullable();
            $table->string('logable_type', 64)->nullable();
            $table->text('action');
			$table->unsignedBigInteger('user_id')->nullable();
			$table->text('properties')->nullable();
            $table->timestamp('created_at')->useCurrent();
			
			$table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logable');
    }
}
