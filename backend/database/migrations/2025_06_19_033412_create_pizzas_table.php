<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pizzas', function (Blueprint $table) {
            $table->id();
            $table->string('pizza_id')->unique()->nullable();
            $table->string('pizza_type_id')->nullable();
            $table->string('size')->nullable();
            $table->float('price')->nullable();
            $table->timestamps();

            $table->foreign('pizza_type_id')->references('pizza_type_id')->on('pizza_types')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pizzas');
    }
};
