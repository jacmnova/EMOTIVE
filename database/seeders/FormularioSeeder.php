<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class FormularioSeeder extends Seeder
{

    public function run()
    {
        DB::table('formularios')->truncate();

        DB::table('formularios')->insert([
            [
                'nome' => 'Questionário de Riscos Psicossociais',
                'label' => 'QRP-36',
                'descricao' => '<p>Questionário de Riscos Psicossociais (Base NR-1 e MBI Adaptado)</p><p>Avaliação Individual</p><p>Este relatório apresenta a análise das dimensões psicossociais avaliadas conforme os parâmetros da <b>NR-1</b> e do inventário <b>MBI</b> adaptado. </p><p>As faixas são classificadas como Baixa, Moderada ou Alta, com base nas pontuações obtidas.</p>',
                'instrucoes' => '<p>Responda cada uma das afirmações abaixo de acordo com a frequência com que você se sente assim no ambiente de trabalho. </p><p>Use a escala de 1 a 5, onde:</p><ul><li>1 - Uma vez por mês ou menos</li><li>2 - Algumas vezes por mês</li><li>3 - Uma vez por semana</li><li>4 - Algumas vezes por semana</li><li>5 - Todos os dias</li></ul>',
                'score_ini' => 1,
                'score_fim' => 5,
                'calculo_id' => 1,
                'status' => 0,
                'created_at' => now(),
            ],
            [
                'nome' => 'Mashlach Burnout Inventory PG',
                'label' => 'MBI-PG',
                'descricao' => '<p>Pesquisa Geral (<span style="font-size: 1rem;">Síndrome de Esgotamento Profissional)</span></p><p>O Maslach Burnout Inventory (MBI) é uma ferramenta para medir o burnout através da avaliação da exaustão emocional, despersonalização e realização profissional. É usado para identificar indivíduos em risco de esgotamento profissional.</p>',
                'instrucoes' => '<p>Para cada afirmação, marque a resposta que melhor representa sua percepção, usando a escala abaixo:</p><li>0 - Nunca</li><li>1 - Algumas poucas vezes por ano ou menos ainda.</li><li>2 - Uma vez por mês ou menos.</li><li>3 - Algumas poucas vezes por mês</li><li>4 - Uma vez por semana.</li><li>5 - Algumas vezes por semana.</li><li>6 - Todos os dias.</li>',
                'score_ini' => 1,
                'score_fim' => 6,
                'calculo_id' => 1,
                'status' => 0,
                'created_at' => now(),
            ],
            [
                'nome' => 'Mashlach Burnout Inventory PS',
                'label' => 'MBI-PS',
                'descricao' => '<p>Pesquisa de Serviço (<span style="font-size: 1rem;">Síndrome de Esgotamento Profissional)</span></p><p>O Maslach Burnout Inventory (MBI) é uma ferramenta para medir o burnout através da avaliação da exaustão emocional, despersonalização e realização profissional. É usado para identificar indivíduos em risco de esgotamento profissional.</p>',
                'instrucoes' => '<p>Para cada afirmação, marque a resposta que melhor representa sua percepção, usando a escala abaixo:</p><li>0 - Nunca</li><li>1 - Algumas poucas vezes por ano ou menos ainda.</li><li>2 - Uma vez por mês ou menos.</li><li>3 - Algumas poucas vezes por mês</li><li>4 - Uma vez por semana.</li><li>5 - Algumas vezes por semana.</li><li>6 - Todos os dias.</li>',
                'score_ini' => 1,
                'score_fim' => 6,
                'calculo_id' => 1,
                'status' => 0,
                'created_at' => now(),
            ],
            [
                'nome' => 'Questionário de Riscos Psicossociais II',
                'label' => 'QRP-25',
                'descricao' => '<p>Questionário de Riscos Psicossociais (Base NR-1 e MBI Adaptado)</p><p>Avaliação Individual</p><p>Este relatório apresenta a análise das dimensões psicossociais avaliadas conforme os parâmetros da <b>NR-1</b> e do inventário <b>MBI</b> adaptado. </p><p>As faixas são classificadas como Baixa, Moderada ou Alta, com base nas pontuações obtidas.</p>',
                'instrucoes' => '<p>Responda cada uma das afirmações abaixo de acordo com a frequência com que você se sente assim no ambiente de trabalho. </p><p>Use a escala de 1 a 5, onde:</p><ul><li>1 - Uma vez por mês ou menos</li><li>2 - Algumas vezes por mês</li><li>3 - Uma vez por semana</li><li>4 - Algumas vezes por semana</li><li>5 - Todos os dias</li></ul>',
                'score_ini' => 1,
                'score_fim' => 5,
                'calculo_id' => 1,
                'status' => 0,
                'created_at' => now(),
            ],
        ]);
    }
}
