<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->string('order_details_id')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->string('pizza_id');
            $table->integer('quantity');
            $table->timestamps();

            $table->unique(['order_id', 'pizza_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_details');
    }
};
