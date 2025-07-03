<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerguntasBurnOutSeeder extends Seeder
{

    public function run()
    {
        DB::table('perguntas')->truncate();

        DB::table('perguntas')->insert([

        // Q36 - Adriana
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 1,
                'pergunta' => 'Recebo novas demandas antes de conseguir concluir as anteriores.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 2,
                'pergunta' => 'Já me senti coagido(a) ou intimidado(a) a cumprir tarefas que não condizem com meu cargo ou que envolviam situações desconfortáveis.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 3,
                'pergunta' => 'Me sinto sem energia para enfrentar mais um dia de trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 4,
                'pergunta' => 'Tenho orgulho do que realizo profissionalmente.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 5,
                'pergunta' => 'Tenho dificuldades para me recuperar emocionalmente após o expediente.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 6,
                'pergunta' => 'Tenho clareza sobre minhas funções e responsabilidades.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 7,
                'pergunta' => 'Já fui alvo de piadas, ironias ou críticas frequentes e desrespeitosas no ambiente de trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 8,
                'pergunta' => 'Sinto medo ou insegurança ao expressar minha opinião no ambiente de trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 9,
                'pergunta' => 'Sinto que sou ouvido(a) e respeitado(a) no ambiente de trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 10,
                'pergunta' => 'Tenho dificuldade em tirar férias ou pausas regulares sem sentir culpa ou receio.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 11,
                'pergunta' => 'Costumo me sentir indiferente em relação às demandas do meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 12,
                'pergunta' => 'O volume de trabalho atual está além da minha capacidade de entrega.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 13,
                'pergunta' => 'Frequentemente, trabalho além do meu horário regular para dar conta das demandas.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 14,
                'pergunta' => 'Sinto que sou tratado(a) de maneira injusta ou desrespeitosa por colegas ou líderes.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 15,
                'pergunta' => 'Tenho notado um distanciamento emocional das pessoas com quem trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 16,
                'pergunta' => 'Já presenciei situações de humilhação ou constrangimento direcionadas a outros colegas.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 17,
                'pergunta' => 'O tempo disponível para realizar minhas funções é insuficiente para executá-las com qualidade.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 18,
                'pergunta' => 'Sinto que as exigências do trabalho ultrapassam minha capacidade.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 19,
                'pergunta' => 'Tenho pouca autonomia para decidir o ritmo ou a organização das minhas tarefas.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 20,
                'pergunta' => 'Já fui excluído(a) de reuniões ou decisões importantes, sem justificativa.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 21,
                'pergunta' => 'Tenho apoio suficiente da liderança ou colegas quando enfrento dificuldades.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 22,
                'pergunta' => 'Sinto que estou realizando menos do que sou capaz no meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 23,
                'pergunta' => 'O tempo que tenho para cumprir minhas responsabilidades é limitado e não permite realiza-las com excelência.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 24,
                'pergunta' => 'As metas e prazos estabelecidos são frequentemente inalcançáveis ou excessivamente pressionantes.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 25,
                'pergunta' => 'Sinto que meu trabalho é importante e significativo.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 26,
                'pergunta' => 'Sinto-me pouco envolvido(a) emocionalmente com as atividades que executo.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 27,
                'pergunta' => 'Tenho que sacrificar compromissos pessoais ou horas de descanso em função do trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 28,
                'pergunta' => 'Tenho dúvidas sobre minha competência para executar minhas tarefas.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 29,
                'pergunta' => 'Sinto-me esgotado(a) física e emocionalmente no final do dia de trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 30,
                'pergunta' => 'Já testemunhei ou sofri algum tipo de assédio moral ou pressão excessiva.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 31,
                'pergunta' => 'As metas e prazos estabelecidos são realistas.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 32,
                'pergunta' => 'Tenho que lidar com cobranças excessivas sem suporte adequado.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 33,
                'pergunta' => 'Sinto que recebo tratamento desigual em relação aos demais colegas que exercem a mesma função.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 34,
                'pergunta' => 'Já recebi ameaças veladas ou explícitas em função de desempenho ou opinião.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 35,
                'pergunta' => 'Consigo manter equilíbrio entre vida pessoal e profissional.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 36,
                'pergunta' => 'Já me senti pressionado(a) a sacrificar minha saúde física ou mental em função do trabalho.',
                'created_at' => now(),
            ],


        // PESQUISA GERAL 

            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 1,
                'pergunta' => 'Sinto-me emocionalmente sugado(a) pelo meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 2,
                'pergunta' => 'Termino o dia mental e fisicamente esgotado(a).',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 3,
                'pergunta' => 'Sinto-me cansado(a) quando acordo de manhã e tenho que enfrentar outro dia de trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 4,
                'pergunta' => 'Trabalhar o dia todo é um grande esforço para mim.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 5,
                'pergunta' => 'Consigo resolver de forma eficiente os problemas que surgem no meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 6,
                'pergunta' => 'Sinto-me completamente esgotado(a) pelo meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 7,
                'pergunta' => 'Sinto que contribuo bastante com minha organização através do meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 8,
                'pergunta' => 'Sinto-me menos interessado(a) pelo trabalho desde que comecei nesta atividade.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 9,
                'pergunta' => 'Estou menos entusiasmado(a) com meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 10,
                'pergunta' => 'Na minha opinião, sou bom(a) no meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 11,
                'pergunta' => 'Sinto-me entusiasmado(a) quando realizo algo significativo no trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 12,
                'pergunta' => 'Consigo fazer várias coisas importantes neste trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 13,
                'pergunta' => 'Só quero fazer meu trabalho e não ser incomodado(a).',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 14,
                'pergunta' => 'Sou cético(a) sobre o quanto meu trabalho contribui para a empresa.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 15,
                'pergunta' => 'Duvido do significado de meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 16,
                'pergunta' => 'Em meu trabalho, sinto-me confiante sobre minha eficiência ao fazer as coisas.',
                'created_at' => now(),
            ],

        // PESQUISA DE SERVIÇO 

            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 1,
                'pergunta' => 'Sinto-me mentalmente esgotado(a) pelas demandas do meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 2,
                'pergunta' => 'Sinto-me exausto(a) ao final do dia.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 3,
                'pergunta' => 'Sinto-me muito cansado(a) quando acordo de manhã e tenho que enfrentar outro dia de trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 4,
                'pergunta' => 'Consigo facilmente entender como os receptores de meus serviços se sentem sobre as coisas.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 5,
                'pergunta' => 'Percebo que trato alguns dos receptores de meus serviços como se fossem objetos impessoais.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 6,
                'pergunta' => 'Trabalhar com pessoas o dia todo é um grande esforço para mim.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 7,
                'pergunta' => 'Consigo lidar de forma eficiente com os problemas dos receptores de meus serviços.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 8,
                'pergunta' => 'Sinto-me totalmente exaurido(a) pelas exigências do meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 9,
                'pergunta' => 'Sinto que influencio de forma positiva as vidas das pessoas através de meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 10,
                'pergunta' => 'Tornei-me mais indiferente com relação às pessoas desde que assumi este trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 11,
                'pergunta' => 'Sinto que este trabalho está me deixando muito menos emocional.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 12,
                'pergunta' => 'Sinto-me cheio(a) de energia.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 13,
                'pergunta' => 'Sinto-me frustrado(a) com meu emprego.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 14,
                'pergunta' => 'Sinto que estou trabalhando muito duro neste trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 15,
                'pergunta' => 'Na verdade, não me importo com o que acontece a alguns dos receptores de meu trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 16,
                'pergunta' => 'Trabalhar diretamente com pessoas coloca muita pressão sobre mim.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 17,
                'pergunta' => 'Consigo criar uma atmosfera relaxada com meus receptores.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 18,
                'pergunta' => 'Sinto-me entusiasmado(a) após trabalhar diretamente com os receptores de meus serviços.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 19,
                'pergunta' => 'Consegui fazer várias coisas importantes neste trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 20,
                'pergunta' => 'Sinto que não tenho mais um pingo de criatividade ou imaginação.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 21,
                'pergunta' => 'Em meu trabalho, lido com problemas emocionais de forma muito calma.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 22,
                'pergunta' => 'Sinto que os receptores de meus serviços às vezes me culpam por seus problemas.',
                'created_at' => now(),
            ],

        // Questionario II - Adriana

            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 1,
                'pergunta' => 'Esgotamento ao final do dia',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 2,
                'pergunta' => 'Sobrecarga de trabalho',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 3,
                'pergunta' => 'Sem energia para o dia seguinte',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 4,
                'pergunta' => 'Dificuldade de recuperação emocional',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 5,
                'pergunta' => 'Distanciamento emocional',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 6,
                'pergunta' => 'Indiferença às demandas',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 7,
                'pergunta' => 'Tratamento impessoal',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 8,
                'pergunta' => 'Baixo envolvimento emocional',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 9,
                'pergunta' => 'Baixa realização pessoal',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 10,
                'pergunta' => 'Dúvidas sobre competência',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 11,
                'pergunta' => 'Exigência excessiva',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 12,
                'pergunta' => 'Sacrifício da saúde',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 13,
                'pergunta' => 'Pouca autonomia',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 14,
                'pergunta' => 'Vivência de assédio/pressão',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 15,
                'pergunta' => 'Piadas ou críticas desrespeitosas',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 16,
                'pergunta' => 'Tratamento injusto',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 17,
                'pergunta' => 'Testemunha de humilhação',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 18,
                'pergunta' => 'Medo de se expressar',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 19,
                'pergunta' => 'Exclusão de decisões',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 20,
                'pergunta' => 'Ameaças veladas ou explícitas',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 21,
                'pergunta' => 'Horas extras frequentes',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 22,
                'pergunta' => 'Metas inalcançáveis',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 23,
                'pergunta' => 'Dificuldade para férias ou pausas',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 24,
                'pergunta' => 'Novas demandas sem concluir anteriores',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'numero_da_pergunta' => 25,
                'pergunta' => 'Sacrifício da vida pessoal',
                'created_at' => now(),
            ],

        ]);
    }
}