<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VarBurnOutSeeder extends Seeder
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
                'B' => 43,
                'M' => 86,
                'A' => 130,
                'baixa' => 'O profissional demonstra um bom nível de energia e resiliência frente às exigências do trabalho. Há indícios de que consegue se recuperar emocionalmente após os expedientes e mantém o controle diante de pressões rotineiras. Essa condição favorece o bem-estar psicológico e a sustentabilidade no desempenho profissional.',
                'moderada' => 'Observa-se uma frequência crescente de cansaço emocional e perda de energia para lidar com as demandas do trabalho. A recuperação emocional pode estar comprometida em certos períodos, o que pode evoluir para quadros mais graves de estafa se não forem adotadas estratégias de manejo do estresse.',
                'alta' => 'Indica um estado avançado de desgaste psicológico. A pessoa sente-se constantemente esgotada, sem forças para enfrentar o dia de trabalho e com dificuldades para se reequilibrar. Trata-se de uma condição crítica, com alto potencial para desencadear transtornos como ansiedade, depressão e burnout.',
                'r_baixa' => 'Você apresenta um bom nível de energia e resiliência para lidar com as pressões e exigências do trabalho. Isso indica que, mesmo após dias intensos, você consegue se recuperar emocionalmente, manter o foco e cuidar do seu bem-estar. É essencial que você continue cultivando hábitos saudáveis, como manter uma rotina equilibrada entre trabalho e vida pessoal, praticar atividades relaxantes, cuidar do sono e buscar momentos de lazer. Aproveite para identificar o que contribui para sua recuperação emocional — seja esporte, hobbies, apoio social — e mantenha esses recursos ativos. Essa estabilidade emocional é uma das principais proteções contra o esgotamento futuro.',
                'r_moderada' => 'Seu resultado mostra sinais moderados de exaustão emocional, sugerindo que, em certos momentos, o estresse acumulado pode estar afetando sua capacidade de recuperação e seu humor. É um momento importante para refletir: você está conseguindo se desconectar do trabalho nas horas de descanso? Há espaço para pausas e lazer? Talvez seja hora de ajustar a carga de atividades, estabelecer limites claros entre trabalho e vida pessoal, priorizar autocuidado e conversar com colegas ou líderes sobre formas de reduzir pressões desnecessárias. Se notar persistência de cansaço, desânimo ou irritabilidade, considerar apoio psicológico pode ser um passo valioso.',
                'r_alta' => 'O nível de exaustão emocional identificado no seu resultado é preocupante. Ele aponta que as exigências emocionais do trabalho estão ultrapassando seus limites de recuperação, podendo gerar irritabilidade, desmotivação, sensação de vazio ou até sintomas físicos relacionados ao estresse. Este é um sinal claro de que você precisa intervir urgentemente: reveja suas responsabilidades, negocie prazos e tarefas, busque apoio da liderança para redistribuir demandas e, se possível, tire folgas ou férias. Além disso, considere fortemente buscar apoio psicológico ou terapêutico, que pode ajudá-lo a lidar com esse momento e a reconstruir estratégias de enfrentamento saudáveis. Cuidar de si agora é essencial para evitar o agravamento do quadro.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'nome' => 'Despersonalização / Cinismo',
                'tag' => 'DeCi',
                'descricao' => 'Refere-se ao desenvolvimento de uma atitude cínica, distante ou impessoal em relação ao trabalho ou às pessoas atendidas. Costuma ser uma forma de defesa frente ao estresse contínuo.',
                'B' => 48,
                'M' => 96,
                'A' => 145,
                'baixa' => 'O indivíduo mantém vínculos saudáveis com seu trabalho e colegas. Mostra-se envolvido emocionalmente com suas tarefas, apresentando empatia, interesse e responsabilidade nas relações profissionais. Esse perfil está alinhado a ambientes de trabalho colaborativos e saudáveis.',
                'moderada' => 'Há sinais de frieza afetiva, distanciamento emocional ou indiferença em relação ao trabalho ou às pessoas ao redor. Embora não graves, esses comportamentos são alertas para possíveis estratégias inconscientes de defesa frente ao estresse crônico ou à insatisfação com o ambiente laboral.',
                'alta' => 'Forte evidência de desconexão emocional e comportamentos cínicos. O indivíduo tende a minimizar a importância das tarefas e das relações profissionais, podendo adotar uma postura apática ou até hostil. Essa condição é prejudicial para o clima organizacional e pode ser um sintoma central de burnout.',
                'r_baixa' => 'Você mantém vínculos saudáveis e positivos no ambiente de trabalho, demonstrando empatia, envolvimento e responsabilidade nas relações profissionais. Isso fortalece o clima organizacional e contribui para sua satisfação e bem-estar no dia a dia. Continue investindo em boas práticas de convivência: ofereça apoio aos colegas, compartilhe conquistas, participe ativamente das dinâmicas de equipe e celebre os resultados em conjunto. O engajamento emocional positivo é um fator protetivo importante, mantendo seu trabalho significativo e estimulante.',
                'r_moderada' => 'Há sinais de certo distanciamento emocional no trabalho, possivelmente como uma forma de proteção frente a pressões ou frustrações. Isso pode levar a um comportamento mais automático, frio ou cínico nas interações. Esteja atento: buscar um distanciamento constante pode prejudicar vínculos, gerar isolamento e reduzir o senso de propósito nas atividades. Reflita sobre o que está gerando esse afastamento — excesso de demandas, conflitos, falta de reconhecimento? Tente recuperar pequenas fontes de satisfação: alinhe expectativas, proponha momentos de interação positiva com colegas e, se necessário, dialogue com a liderança para reavaliar tarefas que estejam sobrecarregando ou frustrando você.',
                'r_alta' => 'Seu resultado aponta para um nível elevado de despersonalização ou cinismo, o que significa que você pode estar emocionalmente desconectado do trabalho e das pessoas, com tendência a tratar as atividades e os colegas de forma indiferente, crítica ou negativa. Esse é um alerta importante: além de impactar suas relações, essa postura aumenta significativamente o risco de esgotamento. Procure entender a raiz desse afastamento: frustração contínua? Falta de reconhecimento? Sobrecarga emocional? Busque apoio para reorganizar sua rotina, reaproximar-se de colegas confiáveis e, se necessário, considerar aconselhamento psicológico para reconstruir seu engajamento de forma saudável.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'nome' => 'Realização Profissional',
                'tag' => 'RePr',
                'descricao' => 'Diz respeito à percepção de ineficácia ou de não estar realizando algo significativo. Envolve sentimentos de baixa autoestima profissional e perda de propósito no trabalho.',
                'B' => 43,
                'M' => 86,
                'A' => 130,
                'baixa' => 'O colaborador percebe sentido no trabalho, sente-se competente, valorizado e motivado. Demonstra autoconfiança, orgulho pelo que faz e envolvimento positivo com suas funções. Essa é uma condição de proteção contra o esgotamento profissional.',
                'moderada' => 'Indica flutuações na percepção de competência e na motivação profissional. A pessoa pode sentir que não está dando o seu melhor, ou que seu esforço não é reconhecido, o que pode comprometer a autoestima e o engajamento no médio prazo.',
                'alta' => 'Reflete um estado de insatisfação profunda com o próprio desempenho. O profissional sente que não realiza o que poderia ou que seu trabalho é irrelevante, o que compromete diretamente a autoestima, o senso de propósito e a motivação. Essa condição é um fator de risco importante para o adoecimento mental.',
                'r_baixa' => 'Você percebe sentido no seu trabalho, sente-se valorizado, competente e motivado, o que fortalece sua autoestima e seu bem-estar profissional. Esse senso de realização é um dos principais fatores de proteção contra o esgotamento e a desmotivação. Continue investindo no que alimenta sua autoconfiança: busque novos desafios, comemore conquistas, compartilhe aprendizados e mantenha o alinhamento com os objetivos da equipe e da organização. Aproveite para também apoiar colegas, promovendo um ambiente onde todos possam se sentir valorizados.',
                'r_moderada' => 'Seu resultado indica que há um senso moderado de realização profissional, com momentos de satisfação, mas também de insegurança ou dúvida quanto ao valor do seu trabalho. É um momento importante para refletir: você sente reconhecimento suficiente? Está conseguindo perceber seus avanços? Talvez seja útil revisar suas metas, pedir feedback construtivo à liderança, identificar oportunidades de desenvolvimento ou buscar atividades que lhe tragam mais propósito. Pequenas mudanças podem reativar seu entusiasmo e fortalecer sua sensação de competência.',
                'r_alta' => 'O resultado aponta para baixos níveis de realização profissional, o que pode significar sensação de desvalorização, baixa autoestima profissional ou falta de propósito no trabalho. Essa condição representa risco elevado de desmotivação e esgotamento. É essencial que você busque apoio: converse com a liderança sobre suas dificuldades, explore possibilidades de redirecionamento de tarefas, treinamento ou mudança de foco. Além disso, considere apoio psicológico ou de mentoria para ajudá-lo a reconstruir seu senso de propósito e autoestima no trabalho.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'nome' => 'Fatores Psicossociais',
                'tag' => 'FaPs',
                'descricao' => 'Engloba aspectos organizacionais e de suporte no ambiente de trabalho, como excesso de cobrança, falta de apoio, e clima de pressão. Estes fatores afetam diretamente a saúde mental e o bem-estar dos colaboradores.',
                'B' => 20,
                'M' => 40,
                'A' => 60,
                'baixa' => 'O ambiente de trabalho oferece suporte adequado, clareza nas funções, boa comunicação e autonomia. Esses fatores favorecem a saúde mental e fortalecem o senso de pertencimento e bem-estar no contexto organizacional.',
                'moderada' => 'Há desequilíbrios nos fatores psicossociais, como ambiguidade de papéis, excesso de exigências, falhas na comunicação ou ausência de apoio. Tais condições afetam o rendimento e aumentam o risco de estresse ocupacional, exigindo monitoramento contínuo.',
                'alta' => 'O cenário psicossocial é crítico. Falta de autonomia, ausência de apoio da liderança, metas incoerentes ou conflitos interpessoais intensos tornam o ambiente tóxico. Essa situação representa risco iminente à saúde mental e deve ser objeto de ações corretivas institucionais.',
                'r_baixa' => 'Seu ambiente de trabalho parece oferecer suporte adequado, clareza de papéis, boa comunicação e autonomia. Esses fatores fortalecem seu senso de pertencimento e bem-estar, criando condições ideais para um desempenho saudável e sustentável. Continue valorizando esses aspectos, cultivando bons relacionamentos, mantendo canais abertos de diálogo e participando ativamente das discussões sobre melhorias no ambiente. Você também pode atuar como um agente positivo, ajudando colegas a se integrarem e reforçando uma cultura organizacional saudável.',
                'r_moderada' => 'Seu resultado mostra que há aspectos psicossociais que poderiam ser melhorados: talvez a comunicação esteja falhando, as funções estejam pouco claras ou o suporte da equipe não esteja fluindo bem. Esses fatores podem gerar insegurança, estresse ou sensação de isolamento. Esteja atento: procure identificar onde estão as maiores dificuldades e proponha pequenas ações de melhoria — seja solicitando reuniões de alinhamento, buscando feedbacks mais claros ou sugerindo ajustes na forma de trabalho em equipe. Um ambiente psicossocial equilibrado depende também da sua participação ativa nas soluções.',
                'r_alta' => 'O resultado indica que o ambiente psicossocial está desfavorável, com alta chance de problemas relacionados a falta de apoio, comunicação ineficaz, conflitos ou baixa autonomia. Esse cenário representa um risco importante para sua saúde emocional e para o desempenho profissional. Não hesite em buscar apoio institucional: acione lideranças, setores de RH ou espaços de mediação para relatar dificuldades e buscar soluções. Além disso, busque fortalecer sua rede de apoio pessoal, cuidando das emoções e buscando suporte externo, se necessário. Intervir cedo ajuda a prevenir impactos mais graves.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'nome' => 'Assédio Moral',
                'tag' => 'AsMo',
                'descricao' => 'Envolve condutas abusivas repetitivas que expõem o trabalhador a situações humilhantes, constrangedoras ou degradantes. Pode afetar profundamente a saúde mental e a autoestima da pessoa.',
                'B' => 25,
                'M' => 50,
                'A' => 75,
                'baixa' => 'Não há relatos ou indícios de práticas abusivas. O ambiente é predominantemente respeitoso, ético e seguro, promovendo relações saudáveis entre pares e lideranças.',
                'moderada' => 'Há indícios pontuais de comportamentos inadequados, como piadas ofensivas, ironias ou críticas públicas. Embora não configurando assédio sistemático, esses episódios contribuem para um clima de insegurança e devem ser investigados com atenção.',
                'alta' => 'Forte presença de comportamentos abusivos, humilhantes ou discriminatórios, configurando assédio moral. Esse tipo de conduta compromete gravemente a saúde psíquica da vítima e a integridade do ambiente de trabalho. A situação exige apuração imediata e ações institucionais contundentes.',
                'r_baixa' => 'Seu ambiente de trabalho é percebido como respeitoso, ético e seguro, sem indícios de práticas abusivas. Esse é um indicador muito positivo, que favorece relações saudáveis, confiança e colaboração. Continue cultivando esse clima: pratique respeito mútuo, fortaleça os vínculos de confiança e participe ativamente de ações que promovam ética e inclusão. Além disso, mantenha-se atento a qualquer sinal de mudança no ambiente, para que esse padrão positivo seja preservado.',
                'r_moderada' => 'O resultado mostra que há sinais moderados de possíveis tensões ou situações desconfortáveis que podem ser percebidas como práticas abusivas, ainda que não constantes ou explícitas. É um momento importante para ficar atento: observe os comportamentos no ambiente, busque manter diálogo aberto com colegas e líderes e, caso perceba padrões inadequados, não hesite em buscar orientação ou apoio. Prevenir situações de assédio envolve não apenas proteger a si, mas também contribuir para um ambiente mais seguro e respeitoso para todos.',
                'r_alta' => 'O resultado aponta para indícios elevados de assédio moral no ambiente de trabalho, um fator extremamente preocupante, que exige atenção imediata. Situações como desrespeito constante, humilhações, isolamento ou ameaças precisam ser enfrentadas com apoio institucional. Busque ajuda: acione canais formais da empresa (RH, ouvidoria, liderança) e, se necessário, procure suporte externo (jurídico, psicológico). Lembre-se: ninguém deve enfrentar essas situações sozinho, e você tem direito a um ambiente de trabalho seguro e digno.',
                'created_at' => now(),
            ],
            [
                'formulario_id' => 1,
                'nome' => 'Excesso de Trabalho',
                'tag' => 'ExTr',
                'descricao' => 'Diz respeito à sobrecarga de tarefas, metas inatingíveis, falta de pausas e desequilíbrio entre vida pessoal e profissional. É um dos principais preditores de burnout e adoecimento psíquico.',
                'B' => 26,
                'M' => 52,
                'A' => 80,
                'baixa' => 'As demandas profissionais estão equilibradas com os recursos e capacidades do trabalhador. Há espaço para pausas, férias e manutenção de qualidade de vida. Esse equilíbrio é protetivo para a saúde física e mental.',
                'moderada' => 'Sinais de sobrecarga pontual, dificuldade em conciliar as tarefas com os prazos ou ausência de pausas adequadas. Ainda que a situação seja gerenciável, há risco de evolução para esgotamento se persistir por longos períodos.',
                'alta' => 'O trabalhador está submetido a uma carga excessiva de tarefas, frequentemente extrapolando horários, suprimindo momentos de descanso e convivendo com metas inalcançáveis. Essa realidade afeta diretamente a saúde física e psíquica, e é uma das principais causas de burnout.',
                'r_baixa' => 'Você mantém um bom equilíbrio entre demandas profissionais e recursos pessoais, conseguindo administrar bem seu tempo, priorizar tarefas e garantir momentos de descanso e lazer. Isso é essencial para manter a saúde mental e física no longo prazo. Continue atento: revise regularmente sua carga de trabalho, comunique-se abertamente sobre prazos e metas e mantenha hábitos de autocuidado. Pequenas rotinas saudáveis, como pausas durante o expediente, exercícios físicos e tempo com a família, ajudam a preservar esse equilíbrio.',
                'r_moderada' => 'Há sinais de que suas demandas profissionais podem estar se aproximando do limite saudável, gerando sensação de sobrecarga em alguns momentos. Isso exige atenção: revise seu planejamento, organize prioridades, avalie o que pode ser delegado e converse com sua liderança sobre formas de aliviar pontos críticos. Também é importante fortalecer práticas de recuperação: reserve tempo para descanso, atividades prazerosas e autocuidado. Uma rotina sustentável depende de equilíbrio entre produtividade e bem-estar.',
                'r_alta' => 'Seu resultado indica um nível preocupante de excesso de trabalho, com alta probabilidade de sobrecarga e impacto negativo na saúde física e emocional. É essencial intervir rapidamente: negocie redistribuição de tarefas, reveja prazos, busque apoio da liderança e, se possível, considere uma pausa para recuperação. Além disso, ative redes de apoio emocional e, se necessário, busque orientação psicológica. Lembre-se: reduzir a carga não é sinal de fraqueza, mas uma ação fundamental para proteger sua saúde e sua capacidade de continuar produzindo no longo prazo.',
                'created_at' => now(),
            ],
        ]);
    }
}