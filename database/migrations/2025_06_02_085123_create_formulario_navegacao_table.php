<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulario_etapas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('formulario_id');
            $table->integer('etapa');
            $table->integer('de');
            $table->integer('ate');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulario_etapas');
    }
};
