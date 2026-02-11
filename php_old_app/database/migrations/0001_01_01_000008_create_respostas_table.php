<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('respostas', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('pergunta_id');
            $table->integer('valor_resposta');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('respostas');
    }
};

