<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variaveis', function (Blueprint $table) {
            $table->id();
            $table->integer('formulario_id');
            $table->string('nome');
            $table->text('descricao');
            $table->string('tag');
            $table->integer('B');
            $table->integer('M');
            $table->integer('A');
            $table->text('baixa');
            $table->text('moderada');
            $table->text('alta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variaveis');
    }
};
