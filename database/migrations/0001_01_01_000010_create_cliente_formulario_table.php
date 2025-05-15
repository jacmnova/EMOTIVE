<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cliente_formulario', function (Blueprint $table) {
            $table->id();
            $table->integer('cliente_id')->nullable();
            $table->integer('formulario_id')->nullable();
            $table->integer('quantidade')->nullable();
            $table->timestamps();
            $table->boolean('ativo')->default(true);
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cliente_formulario');
    }
};

