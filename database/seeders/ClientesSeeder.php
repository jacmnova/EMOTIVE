<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('clientes')->truncate();

        $clientes = [
            [
                'usuario_id' => 3, 
                'tipo' => 'cnpj',
                'cpf_cnpj' => '07792897000182',
                'nome_fantasia' => 'FELLIPELLI',
                'razao_social' => 'FELLIPELLI INSTRUMENTOS DE DIAGNOSTICO LTDA.',
                'email' => 'adriana.fellipelli@fellipelli.com.br',
                'contato' => 'Adriana Fellipelli',
                'telefone' => '1142807100', 
                'created_at' => now(), 
                'ativo' => 1
            ],
        ];

        DB::table('clientes')->insert($clientes);
    }
}