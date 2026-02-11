<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formularios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('label');
            $table->text('descricao');
            $table->text('instrucoes');
            $table->integer('score_ini');
            $table->integer('score_fim');
            $table->integer('calculo_id');
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formularios');
    }
};
