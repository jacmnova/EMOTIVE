<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_calculo')->truncate();

        $calculo = [
            [
                'nome' => 'SOMATORIO', 
                'descricao' => 'Soma dos Valores das Variaveis',
                'operador' => 'soma',
                'formula' => 'total da soma',
                'created_at' => now()
            ],
            [
                'nome' => 'MEDIA', 
                'descricao' => 'Soma e Divisão pela quantidade de perguntas',
                'operador' => 'media',
                'formula' => 'soma dividido pela quantidade',
                'created_at' => now()
            ],
        ];

        DB::table('tipo_calculo')->insert($calculo);
    }
}