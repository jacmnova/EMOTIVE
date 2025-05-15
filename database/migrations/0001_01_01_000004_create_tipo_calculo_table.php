<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tipo_calculo', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50);
            $table->string('descricao', 200);
            $table->string('operador', 20);
            $table->text('formula');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipo_calculo');
    }
};
