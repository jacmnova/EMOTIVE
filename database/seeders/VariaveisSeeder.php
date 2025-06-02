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
                'baixa' => 'O profissional demonstra um bom nível de energia e resiliência frente às exigências do trabalho. Há indícios de que consegue se recuperar emocionalmente após os expedientes e mantém o controle diante de pressões rotineiras. Essa condição favorece o bem-estar psicológico e a sustentabilidade no desempenho profissional.',
                'moderada' => 'Observa-se uma frequência crescente de cansaço emocional e perda de energia para lidar com as demandas do trabalho. A recuperação emocional pode estar comprometida em certos períodos, o que pode evoluir para quadros mais graves de estafa se não forem adotadas estratégias de manejo do estresse.',
                'alta' => 'Indica um estado avançado de desgaste psicológico. A pessoa sente-se constantemente esgotada, sem forças para enfrentar o dia de trabalho e com dificuldades para se reequilibrar. Trata-se de uma condição crítica, com alto potencial para desencadear transtornos como ansiedade, depressão e burnout.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'O indivíduo mantém vínculos saudáveis com seu trabalho e colegas. Mostra-se envolvido emocionalmente com suas tarefas, apresentando empatia, interesse e responsabilidade nas relações profissionais. Esse perfil está alinhado a ambientes de trabalho colaborativos e saudáveis.',
                'moderada' => 'Há sinais de frieza afetiva, distanciamento emocional ou indiferença em relação ao trabalho ou às pessoas ao redor. Embora não graves, esses comportamentos são alertas para possíveis estratégias inconscientes de defesa frente ao estresse crônico ou à insatisfação com o ambiente laboral.',
                'alta' => 'Forte evidência de desconexão emocional e comportamentos cínicos. O indivíduo tende a minimizar a importância das tarefas e das relações profissionais, podendo adotar uma postura apática ou até hostil. Essa condição é prejudicial para o clima organizacional e pode ser um sintoma central de burnout.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'O colaborador percebe sentido no trabalho, sente-se competente, valorizado e motivado. Demonstra autoconfiança, orgulho pelo que faz e envolvimento positivo com suas funções. Essa é uma condição de proteção contra o esgotamento profissional.',
                'moderada' => 'Indica flutuações na percepção de competência e na motivação profissional. A pessoa pode sentir que não está dando o seu melhor, ou que seu esforço não é reconhecido, o que pode comprometer a autoestima e o engajamento no médio prazo.',
                'alta' => 'Reflete um estado de insatisfação profunda com o próprio desempenho. O profissional sente que não realiza o que poderia ou que seu trabalho é irrelevante, o que compromete diretamente a autoestima, o senso de propósito e a motivação. Essa condição é um fator de risco importante para o adoecimento mental.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'O ambiente de trabalho oferece suporte adequado, clareza nas funções, boa comunicação e autonomia. Esses fatores favorecem a saúde mental e fortalecem o senso de pertencimento e bem-estar no contexto organizacional.',
                'moderada' => 'Há desequilíbrios nos fatores psicossociais, como ambiguidade de papéis, excesso de exigências, falhas na comunicação ou ausência de apoio. Tais condições afetam o rendimento e aumentam o risco de estresse ocupacional, exigindo monitoramento contínuo.',
                'alta' => 'O cenário psicossocial é crítico. Falta de autonomia, ausência de apoio da liderança, metas incoerentes ou conflitos interpessoais intensos tornam o ambiente tóxico. Essa situação representa risco iminente à saúde mental e deve ser objeto de ações corretivas institucionais.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'Não há relatos ou indícios de práticas abusivas. O ambiente é predominantemente respeitoso, ético e seguro, promovendo relações saudáveis entre pares e lideranças.',
                'moderada' => 'Há indícios pontuais de comportamentos inadequados, como piadas ofensivas, ironias ou críticas públicas. Embora não configurando assédio sistemático, esses episódios contribuem para um clima de insegurança e devem ser investigados com atenção.',
                'alta' => 'Forte presença de comportamentos abusivos, humilhantes ou discriminatórios, configurando assédio moral. Esse tipo de conduta compromete gravemente a saúde psíquica da vítima e a integridade do ambiente de trabalho. A situação exige apuração imediata e ações institucionais contundentes.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'As demandas profissionais estão equilibradas com os recursos e capacidades do trabalhador. Há espaço para pausas, férias e manutenção de qualidade de vida. Esse equilíbrio é protetivo para a saúde física e mental.',
                'moderada' => 'Sinais de sobrecarga pontual, dificuldade em conciliar as tarefas com os prazos ou ausência de pausas adequadas. Ainda que a situação seja gerenciável, há risco de evolução para esgotamento se persistir por longos períodos.',
                'alta' => 'O trabalhador está submetido a uma carga excessiva de tarefas, frequentemente extrapolando horários, suprimindo momentos de descanso e convivendo com metas inalcançáveis. Essa realidade afeta diretamente a saúde física e psíquica, e é uma das principais causas de burnout.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'Indica ausência de desgaste emocional significativo, sugerindo que o profissional mantém energia e recursos internos para enfrentar as demandas de maneira adequada.',
                'moderada' => 'O profissional apresenta um nível moderado de exaustão emocional, evidenciando desgaste, mas ainda com capacidade de administrar as demandas do ambiente de trabalho.',
                'alta' => 'O resultado obtido nesta faixa demonstra que o profissional está emocionalmente sobrecarregado e apresenta elevado nível de desgaste. Essa condição impacta diretamente a disposição e a capacidade de lidar com as exigências diárias.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'Mostra que o profissional mantém uma atitude positiva, engajada e comprometida com suas responsabilidades e com o ambiente de trabalho.',
                'moderada' => 'Revela presença de cinismo em grau moderado, mas ainda sem ruptura completa no vínculo com as demandas e as relações de trabalho.',
                'alta' => 'Aponta que o profissional adota atitudes distantes e cínicas em relação ao trabalho e às pessoas com quem interage, o que pode comprometer seu engajamento e relações profissionais.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'Indica que o profissional reconhece um bom desempenho no trabalho, sentindo-se eficaz e confiante para atender às demandas e se realizar profissionalmente.',
                'moderada' => 'Aponta que o profissional avalia seu desempenho como razoável, demonstrando percepção de competência moderada.',
                'alta' => 'Sugere que o profissional percebe dificuldades em atender às demandas, com sentimento de ineficácia e dúvidas sobre suas habilidades.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'Indica ausência de desgaste emocional significativo, sugerindo que o profissional mantém energia e recursos internos para enfrentar as demandas de maneira adequada.',
                'moderada' => 'O profissional apresenta um nível moderado de exaustão emocional, evidenciando desgaste, mas ainda com capacidade de administrar as demandas do ambiente de trabalho.',
                'alta' => 'O resultado obtido nesta faixa demonstra que o profissional está emocionalmente sobrecarregado e apresenta elevado nível de desgaste. Essa condição impacta diretamente a disposição e a capacidade de lidar com as exigências diárias.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'Sugere que o profissional mantém empatia e conexão emocional com as pessoas e as atividades desenvolvidas, apresentando um relacionamento saudável e humanizado com o trabalho.',
                'moderada' => 'Revela que o profissional apresenta um grau moderado de despersonalização, caracterizado por certa indiferença ou falta de empatia, mas sem comprometimento severo das relações profissionais.',
                'alta' => 'Indica que o profissional adota um distanciamento emocional significativo em relação ao trabalho e às pessoas atendidas, demonstrando atitudes frias ou impessoais que podem prejudicar o relacionamento e o desempenho.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'Sugere que o profissional percebe baixa realização pessoal, sentindo-se pouco produtivo e pouco eficaz, o que pode impactar diretamente sua motivação e engajamento.',
                'moderada' => 'Aponta para uma percepção moderada de realização pessoal, com sentimentos de competência e satisfação presentes, mas não de forma intensa ou constante.',
                'alta' => 'Indica que o profissional percebe um alto nível de realização e competência no trabalho, sentindo-se motivado, produtivo e satisfeito com suas atividades e resultados.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'O profissional demonstra um bom nível de energia e resiliência frente às exigências do trabalho. Há indícios de que consegue se recuperar emocionalmente após os expedientes e mantém o controle diante de pressões rotineiras. Essa condição favorece o bem-estar psicológico e a sustentabilidade no desempenho profissional.',
                'moderada' => 'Observa-se uma frequência crescente de cansaço emocional e perda de energia para lidar com as demandas do trabalho. A recuperação emocional pode estar comprometida em certos períodos, o que pode evoluir para quadros mais graves de estafa se não forem adotadas estratégias de manejo do estresse.',
                'alta' => 'Indica um estado avançado de desgaste psicológico. A pessoa sente-se constantemente esgotada, sem forças para enfrentar o dia de trabalho e com dificuldades para se reequilibrar. Trata-se de uma condição crítica, com alto potencial para desencadear transtornos como ansiedade, depressão e burnout.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'O indivíduo mantém vínculos saudáveis com seu trabalho e colegas. Mostra-se envolvido emocionalmente com suas tarefas, apresentando empatia, interesse e responsabilidade nas relações profissionais. Esse perfil está alinhado a ambientes de trabalho colaborativos e saudáveis.',
                'moderada' => 'Há sinais de frieza afetiva, distanciamento emocional ou indiferença em relação ao trabalho ou às pessoas ao redor. Embora não graves, esses comportamentos são alertas para possíveis estratégias inconscientes de defesa frente ao estresse crônico ou à insatisfação com o ambiente laboral.',
                'alta' => 'Forte evidência de desconexão emocional e comportamentos cínicos. O indivíduo tende a minimizar a importância das tarefas e das relações profissionais, podendo adotar uma postura apática ou até hostil. Essa condição é prejudicial para o clima organizacional e pode ser um sintoma central de burnout.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'O colaborador percebe sentido no trabalho, sente-se competente, valorizado e motivado. Demonstra autoconfiança, orgulho pelo que faz e envolvimento positivo com suas funções. Essa é uma condição de proteção contra o esgotamento profissional.',
                'moderada' => 'Indica flutuações na percepção de competência e na motivação profissional. A pessoa pode sentir que não está dando o seu melhor, ou que seu esforço não é reconhecido, o que pode comprometer a autoestima e o engajamento no médio prazo.',
                'alta' => 'Reflete um estado de insatisfação profunda com o próprio desempenho. O profissional sente que não realiza o que poderia ou que seu trabalho é irrelevante, o que compromete diretamente a autoestima, o senso de propósito e a motivação. Essa condição é um fator de risco importante para o adoecimento mental.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'O ambiente de trabalho oferece suporte adequado, clareza nas funções, boa comunicação e autonomia. Esses fatores favorecem a saúde mental e fortalecem o senso de pertencimento e bem-estar no contexto organizacional.',
                'moderada' => 'Há desequilíbrios nos fatores psicossociais, como ambiguidade de papéis, excesso de exigências, falhas na comunicação ou ausência de apoio. Tais condições afetam o rendimento e aumentam o risco de estresse ocupacional, exigindo monitoramento contínuo.',
                'alta' => 'O cenário psicossocial é crítico. Falta de autonomia, ausência de apoio da liderança, metas incoerentes ou conflitos interpessoais intensos tornam o ambiente tóxico. Essa situação representa risco iminente à saúde mental e deve ser objeto de ações corretivas institucionais.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'Não há relatos ou indícios de práticas abusivas. O ambiente é predominantemente respeitoso, ético e seguro, promovendo relações saudáveis entre pares e lideranças.',
                'moderada' => 'Há indícios pontuais de comportamentos inadequados, como piadas ofensivas, ironias ou críticas públicas. Embora não configurando assédio sistemático, esses episódios contribuem para um clima de insegurança e devem ser investigados com atenção.',
                'alta' => 'Forte presença de comportamentos abusivos, humilhantes ou discriminatórios, configurando assédio moral. Esse tipo de conduta compromete gravemente a saúde psíquica da vítima e a integridade do ambiente de trabalho. A situação exige apuração imediata e ações institucionais contundentes.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
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
                'baixa' => 'As demandas profissionais estão equilibradas com os recursos e capacidades do trabalhador. Há espaço para pausas, férias e manutenção de qualidade de vida. Esse equilíbrio é protetivo para a saúde física e mental.',
                'moderada' => 'Sinais de sobrecarga pontual, dificuldade em conciliar as tarefas com os prazos ou ausência de pausas adequadas. Ainda que a situação seja gerenciável, há risco de evolução para esgotamento se persistir por longos períodos.',
                'alta' => 'O trabalhador está submetido a uma carga excessiva de tarefas, frequentemente extrapolando horários, suprimindo momentos de descanso e convivendo com metas inalcançáveis. Essa realidade afeta diretamente a saúde física e psíquica, e é uma das principais causas de burnout.',
                'r_baixa' => '*',
                'r_moderada' => '*',
                'r_alta' => '*',
                'created_at' => now(),
            ],

        ]);
    }
}