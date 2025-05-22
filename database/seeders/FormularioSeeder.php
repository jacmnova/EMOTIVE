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
                'instrucoes' => '<p style="line-height: 1;"><h4 style="text-align: center; "><b>MBI - Pesquisa Geral</b></h4><h6 style="text-align: center; "><i>O Objetivo desta pesquisa é identificar como você percebe seu trabalho e como são suas reações a ele.</i></h6><div style="text-align: justify;"><span style="font-size: 1rem;">No questionário você encontra 16 frases sobre seus sentimentos relacionados ao trabalho. Leia cada frase cuidadosamente e decida se alguma vez se sentiu daquela maneira sobre seu trabalho. Se nunca se sentiu assim, marque zero (0) no espaço a frente da frase. Se já se sentiu assim, marque um numero de 1 a 6 que melhor descreva a frequencia de seu sentimento.</span></div></p><p>Para cada afirmação, marque a resposta que melhor representa sua percepção, usando a escala abaixo:</p><li>0 - Nunca</li><li>1 - Algumas poucas vezes por ano ou menos ainda.</li><li>2 - Uma vez por mês ou menos.</li><li>3 - Algumas poucas vezes por mês</li><li>4 - Uma vez por semana.</li><li>5 - Algumas vezes por semana.</li><li>6 - Todos os dias.</li>',
                'score_ini' => 0,
                'score_fim' => 6,
                'calculo_id' => 1,
                'status' => 0,
                'created_at' => now(),
            ],
            [
                'nome' => 'Mashlach Burnout Inventory PS',
                'label' => 'MBI-PS',
                'descricao' => '<p>Pesquisa de Serviço (<span style="font-size: 1rem;">Síndrome de Esgotamento Profissional)</span></p><p>O Maslach Burnout Inventory (MBI) é uma ferramenta para medir o burnout através da avaliação da exaustão emocional, despersonalização e realização profissional. É usado para identificar indivíduos em risco de esgotamento profissional.</p>',
                'instrucoes' => '<h5 style="text-align: center;"><b>MBI Pesquisa de Serviços</b></h5><p style="text-align: center;"><i>O Objetivo desta pesquisa é identificar como pessoas que trabalham em atendimento ou ajudando profissionais, enxergam suas funções e as pessoas com as quais trabalham. Como pessoas de diferentes áreas irão responder a este questionário, usou-se o termo "receptor" para designar as pessoas que são objeto de seu serviço, atendimento, tratamento ou ensino. Ao responder esta pesquisa lembre-se de que são pessoas que recebem o serviço que você presta, apesar do uso de um outro termo em seu trabalho.</i></p><p><span style="font-size: 1rem; text-align: justify;">No questionário você encontra 22 frases sobre seus sentimentos relacionados ao trabalho. Leia cada frase cuidadosamente e decida se alguma vez se sentiu daquela maneira sobre seu trabalho. Se nunca se sentiu assim, marque zero (0) no espaço a frente da frase. Se já se sentiu assim, marque um numero de 1 a 6 que melhor descreva a frequencia de seu sentimento.</span><i></i></p><p>Para cada afirmação, marque a resposta que melhor representa sua percepção, usando a escala abaixo:</p><li>0 - Nunca</li><li>1 - Algumas poucas vezes por ano ou menos ainda.</li><li>2 - Uma vez por mês ou menos.</li><li>3 - Algumas poucas vezes por mês</li><li>4 - Uma vez por semana.</li><li>5 - Algumas vezes por semana.</li><li>6 - Todos os dias.</li>',
                'score_ini' => 0,
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
