<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('perguntas', function (Blueprint $table) {
            $table->id();
            $table->string('formulario_id');
            $table->integer('numero_da_pergunta');
            $table->string('pergunta');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('perguntas');
    }
};