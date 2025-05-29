<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuario_formulario', function (Blueprint $table) {
            $table->id();
            $table->integer('usuario_id')->nullable();
            $table->integer('formulario_id')->nullable();
            $table->timestamps();
            $table->enum('status', ['novo', 'pendente', 'completo'])->default('novo');
            $table->date('data_limite')->nullable();
            $table->boolean('video_assistido')->default(false);
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario_formulario');
    }
};