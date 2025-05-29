<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('midias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->enum('tipo', ['video', 'url']);
            $table->string('url')->nullable();
            $table->string('arquivo')->nullable();
            $table->unsignedBigInteger('formulario_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('midias');
    }
};
