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
                'usuario_id' => 1, 
                'tipo' => 'cpf',
                'cpf_cnpj' => '01148890904',
                'nome_fantasia' => 'DESENVOLVIMENTO DE SISTEMAS',
                'razao_social' => 'ARLEY HUMBERTO RUEDA RINCON LTDA.',
                'email' => 'arley@gmail.com',
                'contato' => 'Arley',
                'telefone' => '41978542635', 
                'created_at' => now(), 
                'ativo' => 1
            ],
            [
                'usuario_id' => 2, 
                'tipo' => 'cnpj',
                'cpf_cnpj' => '26789226000107',
                'nome_fantasia' => 'HAGUEN SYSTEM DESENVOLVIMENTO',
                'razao_social' => 'HAGUEN SYSTEM LTDA.',
                'email' => 'haguen@gmail.com',
                'contato' => 'Amuneth',
                'telefone' => '419989998996', 
                'created_at' => now(), 
                'ativo' => 1
            ],
            [
                'usuario_id' => 3, 
                'tipo' => 'internacional',
                'cpf_cnpj' => '123456',
                'nome_fantasia' => 'GOOGLE.COM',
                'razao_social' => 'GOOGLE SA.',
                'email' => 'google@gmail.com',
                'contato' => 'Alikson',
                'telefone' => '41978542635', 
                'created_at' => now(), 
                'ativo' => 1
            ],
        ];

        DB::table('clientes')->insert($clientes);
    }
}