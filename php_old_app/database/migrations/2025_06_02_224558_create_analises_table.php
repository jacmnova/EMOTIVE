<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('formulario_id');
            $table->longText('texto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analises');
    }
};
