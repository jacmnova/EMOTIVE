<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VariaveisSeeder extends Seeder
{

    public function run()
    {
        DB::table('variaveis')->truncate();

        DB::table('variaveis')->insert([
            [
                'formulario_id' => 1,
                'nome' => 'Exaustão Emocional',
                'tag' => 'ExEm',
                'descricao' => 'Refere-se ao sentimento de estar emocionalmente sobrecarregado e esgotado pelo trabalho. Pode surgir da pressão constante, da carga excessiva de tarefas e da dificuldade de recuperação emocional.',
                'B' => 8,
                'M' => 16,
                'A' => 17,
                'baixa' => 'Baixo nível de exaustão emocional. Situação sob controle.',
                'moderada' => 'Nível moderado de exaustão. Atenção às condições de trabalho.',
                'alta' => 'Alto nível de exaustão. Indica risco elevado de burnout.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'nome' => 'Despersonalização / Cinismo',
                'tag' => 'DeCi',
                'descricao' => 'Refere-se ao desenvolvimento de uma atitude cínica, distante ou impessoal em relação ao trabalho ou às pessoas atendidas. Costuma ser uma forma de defesa frente ao estresse contínuo.',
                'B' => 6,
                'M' => 10,
                'A' => 11,
                'baixa' => 'Baixo nível de despersonalização. Bom envolvimento interpessoal.',
                'moderada' => 'Sinais de distanciamento emocional. Observar interações sociais.',
                'alta' => 'Alto nível de cinismo. Risco de impacto negativo nas relações e desempenho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'nome' => 'Realização Profissional',
                'tag' => 'RePr',
                'descricao' => 'Diz respeito à percepção de ineficácia ou de não estar realizando algo significativo. Envolve sentimentos de baixa autoestima profissional e perda de propósito no trabalho.',
                'B' => 4,
                'M' => 8,
                'A' => 9,
                'baixa' => 'Alto senso de realização profissional.',
                'moderada' => 'Moderado senso de eficácia no trabalho.',
                'alta' => 'Sensação intensa de baixa realização. Pode afetar autoestima e motivação.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'nome' => 'Fatores Psicossociais',
                'tag' => 'FaPs',
                'descricao' => 'Engloba aspectos organizacionais e de suporte no ambiente de trabalho, como excesso de cobrança, falta de apoio, e clima de pressão. Estes fatores afetam diretamente a saúde mental e o bem-estar dos colaboradores.',
                'B' => 6,
                'M' => 10,
                'A' => 11,
                'baixa' => 'Baixo impacto psicossocial identificado.',
                'moderada' => 'Impacto psicossocial moderado. Avaliar carga de trabalho e suporte.',
                'alta' => 'Alto impacto psicossocial. Risco elevado para saúde mental.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'nome' => 'Assédio Moral',
                'tag' => 'AsMo',
                'descricao' => 'Envolve condutas abusivas repetitivas que expõem o trabalhador a situações humilhantes, constrangedoras ou degradantes. Pode afetar profundamente a saúde mental e a autoestima da pessoa.',
                'B' => 6,
                'M' => 12,
                'A' => 13,
                'baixa' => 'Baixo risco de assédio identificado.',
                'moderada' => 'Indícios moderados de comportamentos abusivos. Atenção recomendada.',
                'alta' => 'Forte indício de assédio moral. Necessidade de intervenção urgente.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'nome' => 'Excesso de Trabalho',
                'tag' => 'ExTr',
                'descricao' => 'Diz respeito à sobrecarga de tarefas, metas inatingíveis, falta de pausas e desequilíbrio entre vida pessoal e profissional. É um dos principais preditores de burnout e adoecimento psíquico.',
                'B' => 6,
                'M' => 10,
                'A' => 11,
                'baixa' => 'Carga de trabalho dentro de limites saudáveis.',
                'moderada' => 'Carga de trabalho moderada. Monitoramento necessário.',
                'alta' => 'Sobrecarga de trabalho evidente. Alto risco para saúde física e mental.',
                'created_at' => now(),
            ],







            [
                'formulario_id' => 2,
                'nome' => 'Exaustão Emocional',
                'tag' => 'EX',
                'descricao' => 'Descrição de Exaustão Emocional',
                'B' => 5,
                'M' => 10,
                'A' => 11,
                'baixa' => 'Faixa Baixa',
                'moderada' => 'Faixa Moderada',
                'alta' => 'Faixa Alta',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 2,
                'nome' => 'Cinismo',
                'tag' => 'CY',
                'descricao' => 'Descrição de Cinismo',
                'B' => 5,
                'M' => 10,
                'A' => 11,
                'baixa' => 'Faixa Baixa',
                'moderada' => 'Faixa Moderada',
                'alta' => 'Faixa Alta',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 2,
                'nome' => 'Eficiencia Profissional',
                'tag' => 'PE',
                'descricao' => 'Descrição Eficiencia Profissional',
                'B' => 5,
                'M' => 10,
                'A' => 11,
                'baixa' => 'Faixa Baixa',
                'moderada' => 'Faixa Moderada',
                'alta' => 'Faixa Alta',
                'created_at' => now(),
            ],






            [
                'formulario_id' => 3,
                'nome' => 'Exaustão Emocional', // 10
                'tag' => 'EE',
                'descricao' => 'Descrição de Exaustão Emocional',
                'B' => 5,
                'M' => 10,
                'A' => 11,
                'baixa' => 'Faixa Baixa',
                'moderada' => 'Faixa Moderada',
                'alta' => 'Faixa Alta',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 3,
                'nome' => 'Despersonalização', // 11
                'tag' => 'DP',
                'descricao' => 'Descrição de Despersonalização',
                'B' => 5,
                'M' => 10,
                'A' => 11,
                'baixa' => 'Faixa Baixa',
                'moderada' => 'Faixa Moderada',
                'alta' => 'Faixa Alta',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 3,
                'nome' => 'Realização Pessoal', // 12
                'tag' => 'PA',
                'descricao' => 'Descrição Realização Pessoal',
                'B' => 5,
                'M' => 10,
                'A' => 11,
                'baixa' => 'Faixa Baixa',
                'moderada' => 'Faixa Moderada',
                'alta' => 'Faixa Alta',
                'created_at' => now(),
            ],






            [
                'formulario_id' => 4,
                'nome' => 'Exaustão Emocional', // 13
                'tag' => 'ExEm',
                'descricao' => 'Refere-se ao sentimento de estar emocionalmente sobrecarregado e esgotado pelo trabalho. Pode surgir da pressão constante, da carga excessiva de tarefas e da dificuldade de recuperação emocional.',
                'B' => 8,
                'M' => 16,
                'A' => 17,
                'baixa' => 'Baixo nível de exaustão emocional. Situação sob controle.',
                'moderada' => 'Nível moderado de exaustão. Atenção às condições de trabalho.',
                'alta' => 'Alto nível de exaustão. Indica risco elevado de burnout.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 4,
                'nome' => 'Despersonalização', // 14
                'tag' => 'Desp',
                'descricao' => 'Refere-se ao desenvolvimento de uma atitude cínica, distante ou impessoal em relação ao trabalho ou às pessoas atendidas. Costuma ser uma forma de defesa frente ao estresse contínuo.',
                'B' => 6,
                'M' => 10,
                'A' => 11,
                'baixa' => 'Baixo nível de despersonalização. Bom envolvimento interpessoal.',
                'moderada' => 'Sinais de distanciamento emocional. Observar interações sociais.',
                'alta' => 'Alto nível de cinismo. Risco de impacto negativo nas relações e desempenho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 4,
                'nome' => 'Baixa Realização',  // 15
                'tag' => 'BaRe',
                'descricao' => 'Diz respeito à percepção de ineficácia ou de não estar realizando algo significativo. Envolve sentimentos de baixa autoestima profissional e perda de propósito no trabalho.',
                'B' => 4,
                'M' => 8,
                'A' => 9,
                'baixa' => 'Alto senso de realização profissional.',
                'moderada' => 'Moderado senso de eficácia no trabalho.',
                'alta' => 'Sensação intensa de baixa realização. Pode afetar autoestima e motivação.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 4,
                'nome' => 'Fatores Psicossociais',  // 16
                'tag' => 'FaPs',
                'descricao' => 'Engloba aspectos organizacionais e de suporte no ambiente de trabalho, como excesso de cobrança, falta de apoio, e clima de pressão. Estes fatores afetam diretamente a saúde mental e o bem-estar dos colaboradores.',
                'B' => 6,
                'M' => 10,
                'A' => 11,
                'baixa' => 'Baixo impacto psicossocial identificado.',
                'moderada' => 'Impacto psicossocial moderado. Avaliar carga de trabalho e suporte.',
                'alta' => 'Alto impacto psicossocial. Risco elevado para saúde mental.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 4,
                'nome' => 'Assédio Moral', // 17
                'tag' => 'AsMo',
                'descricao' => 'Envolve condutas abusivas repetitivas que expõem o trabalhador a situações humilhantes, constrangedoras ou degradantes. Pode afetar profundamente a saúde mental e a autoestima da pessoa.',
                'B' => 6,
                'M' => 12,
                'A' => 13,
                'baixa' => 'Baixo risco de assédio identificado.',
                'moderada' => 'Indícios moderados de comportamentos abusivos. Atenção recomendada.',
                'alta' => 'Forte indício de assédio moral. Necessidade de intervenção urgente.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 4,
                'nome' => 'Excesso de Trabalho',  //18
                'tag' => 'ExTr',
                'descricao' => 'Diz respeito à sobrecarga de tarefas, metas inatingíveis, falta de pausas e desequilíbrio entre vida pessoal e profissional. É um dos principais preditores de burnout e adoecimento psíquico.',
                'B' => 6,
                'M' => 10,
                'A' => 11,
                'baixa' => 'Carga de trabalho dentro de limites saudáveis.',
                'moderada' => 'Carga de trabalho moderada. Monitoramento necessário.',
                'alta' => 'Sobrecarga de trabalho evidente. Alto risco para saúde física e mental.',
                'created_at' => now(),
            ],






        ]);
    }
}