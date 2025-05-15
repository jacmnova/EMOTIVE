<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->integer('usuario_id')->nullable();
            $table->enum('tipo', ['cnpj', 'cpf', 'internacional']);
            $table->string('cpf_cnpj')->unique();
            $table->string('nome_fantasia')->nullable();
            $table->string('razao_social')->nullable();
            $table->string('logo_url')->default('../vendor/adminlte/dist/img/client.png')->nullable(false);
            $table->string('email')->nullable();
            $table->string('contato')->nullable();
            $table->string('telefone')->nullable();
            $table->timestamps();
            $table->boolean('ativo')->default(true);
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};

