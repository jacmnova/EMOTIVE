<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class FormBurnOutSeeder extends Seeder
{

    public function run()
    {
        DB::table('formularios')->truncate();

        DB::table('formularios')->insert([
            [
                'nome' => 'Burnout 99',
                'label' => 'BO99',
                'descricao' => '<p>Questionário de Riscos Psicossociais (Base NR-1 e MBI Adaptado)</p><p>Avaliação Individual</p><p>Este relatório apresenta a análise das dimensões psicossociais avaliadas conforme os parâmetros da <b>NR-1</b> e do inventário <b>MBI</b> adaptado. </p><p>As faixas são classificadas como Baixa, Moderada ou Alta, com base nas pontuações obtidas.</p>',
                'instrucoes' => '<p>Responda com toda a sinceridade, sabendo que o sigilo da sua identidade é absoluto e garantido por nós, e que seu feedback é indispensável para construir um ambiente de trabalho mais sustentável e positivo para todos. Você está em um espaço 100% seguro, e cada resposta é valiosa para elevar sua qualidade de vida. </p><p>Use a escala de 1 a 5, onde:</p><ul><li>1 - Uma vez por mês ou nenhuma</li><li>2 - Algumas vezes por mês</li><li>3 - Uma vez por semana</li><li>4 - Algumas vezes por semana</li><li>5 - Todos os dias</li></ul>',
                'score_ini' => 1,
                'score_fim' => 5,
                'calculo_id' => 1,
                'status' => 0,
                'created_at' => now(),
            ],
        ]);
    }
}
