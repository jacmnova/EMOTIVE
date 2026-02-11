<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pergunta_variavel', function (Blueprint $table) {
            $table->id();
            $table->integer('pergunta_id');
            $table->integer('variavel_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pergunta_variavel');
    }
};