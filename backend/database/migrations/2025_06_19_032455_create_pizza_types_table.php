<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pizza_types', function (Blueprint $table) {
            $table->id();
            $table->string('pizza_type_id')->unique();
            $table->string('name');
            $table->string('category');
            $table->text('ingredients');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pizza_types');
    }
};