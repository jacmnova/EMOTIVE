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
                'descricao' => 'Soma dos valores das variáveis',
                'operador' => 'soma',
                'formula' => 'total da soma',
                'created_at' => now()
            ],
            [
                'nome' => 'MEDIA',
                'descricao' => 'Soma e divisão pela quantidade de perguntas',
                'operador' => 'media',
                'formula' => 'soma dividido pela quantidade',
                'created_at' => now()
            ],
            [
                'nome' => 'MÁXIMO',
                'descricao' => 'Maior valor entre as respostas',
                'operador' => 'max',
                'formula' => 'valor máximo das respostas',
                'created_at' => now()
            ],
            [
                'nome' => 'MÍNIMO',
                'descricao' => 'Menor valor entre as respostas',
                'operador' => 'min',
                'formula' => 'valor mínimo das respostas',
                'created_at' => now()
            ],
            [
                'nome' => 'PORCENTAGEM DE ACERTOS',
                'descricao' => 'Percentual de respostas corretas',
                'operador' => 'porcentagem',
                'formula' => '(respostas certas / total de perguntas) * 100',
                'created_at' => now()
            ],
            [
                'nome' => 'PONTUAÇÃO COM PESO',
                'descricao' => 'Soma das respostas multiplicadas pelos pesos',
                'operador' => 'soma_peso',
                'formula' => 'soma(resposta[i] * peso[i])',
                'created_at' => now()
            ],
            [
                'nome' => 'MÉDIA POR BLOCO',
                'descricao' => 'Média separada por grupo de perguntas',
                'operador' => 'media_grupo',
                'formula' => 'soma(grupo) / quantidade(grupo)',
                'created_at' => now()
            ],
            [
                'nome' => 'DELTA ENTRE GRUPOS',
                'descricao' => 'Diferença entre médias de grupos',
                'operador' => 'diferenca',
                'formula' => 'media(grupo1) - media(grupo2)',
                'created_at' => now()
            ],
            [
                'nome' => 'PADRÃO BINÁRIO',
                'descricao' => 'Contagem de acertos (0 ou 1)',
                'operador' => 'binario',
                'formula' => 'soma(respostas certas)',
                'created_at' => now()
            ],
            [
                'nome' => 'DESVIO PADRÃO',
                'descricao' => 'Variação das respostas em relação à média',
                'operador' => 'desvio',
                'formula' => 'sqrt(soma((x - média)^2) / n)',
                'created_at' => now()
            ],
            [
                'nome' => 'MODA',
                'descricao' => 'Valor mais frequente nas respostas',
                'operador' => 'moda',
                'formula' => 'moda das respostas',
                'created_at' => now()
            ],
            [
                'nome' => 'EXPRESSÃO PERSONALIZADA',
                'descricao' => 'Cálculo definido por expressão customizada',
                'operador' => 'custom',
                'formula' => 'ex: ((resp1 + resp2) / 2) * pesoA - resp3',
                'created_at' => now()
            ],
        ];

        DB::table('tipo_calculo')->insert($calculo);
    }
}