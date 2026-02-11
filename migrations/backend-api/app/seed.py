"""
Seeders para poblar la base de datos (desarrollo / entorno inicial).

Uso:
  cd migrations/backend-api && source .venv/bin/activate
  python -m app.seed

Contraseña por defecto de los usuarios creados: admin123
"""
import sys
from datetime import datetime, timezone

from app.database import SessionLocal
from app.core.security import get_password_hash
from app.models import (
    User,
    Cliente,
    TipoCalculo,
    Formulario,
    FormularioEtapa,
    Pergunta,
    Variavel,
    PerguntaVariavel,
)
from app.models.cliente import TipoCliente

# 99 perguntas do Burnout 99 (formulario_id=1), na ordem do PerguntasBurnOutSeeder PHP
PERGUNTAS_BURNOUT_99 = [
    "Recebo novas demandas antes de conseguir concluir as anteriores.",
    "Já me senti coagido(a) ou intimidado(a) a cumprir tarefas que não condizem com meu cargo ou que envolviam situações desconfortáveis.",
    "Me sinto sem energia para enfrentar mais um dia de trabalho.",
    "Tenho orgulho do que realizo profissionalmente.",
    "Tenho dificuldades para me recuperar emocionalmente após o expediente.",
    "Tenho clareza sobre minhas funções e responsabilidades.",
    "Já fui alvo de piadas, ironias ou críticas frequentes e desrespeitosas no ambiente de trabalho.",
    "Sinto medo ou insegurança ao expressar minha opinião no ambiente de trabalho.",
    "Sinto que sou ouvido(a) e respeitado(a) no ambiente de trabalho.",
    "Tenho dificuldade em tirar férias ou pausas regulares sem sentir culpa ou receio.",
    "Costumo me sentir indiferente em relação às demandas do meu trabalho.",
    "O volume de trabalho atual está além da minha capacidade de entrega.",
    "Frequentemente, trabalho além do meu horário regular para dar conta das demandas.",
    "Sinto que sou tratado(a) de maneira injusta ou desrespeitosa por colegas ou líderes.",
    "Tenho notado um distanciamento emocional das pessoas com quem trabalho.",
    "Já presenciei situações de humilhação ou constrangimento direcionadas a outros colegas.",
    "O tempo disponível para realizar minhas funções é insuficiente para executá-las com qualidade.",
    "Sinto que as exigências do trabalho ultrapassam minha capacidade.",
    "Tenho pouca autonomia para decidir o ritmo ou a organização das minhas tarefas.",
    "Já fui excluído(a) de reuniões ou decisões importantes, sem justificativa.",
    "Tenho apoio suficiente da liderança ou colegas quando enfrento dificuldades.",
    "Sinto que estou realizando menos do que sou capaz no meu trabalho.",
    "O tempo que tenho para cumprir minhas responsabilidades é limitado e não permite realiza-las com excelência.",
    "As metas e prazos estabelecidos são frequentemente inalcançáveis ou excessivamente pressionantes.",
    "Sinto que meu trabalho é importante e significativo.",
    "Sinto-me pouco envolvido(a) emocionalmente com as atividades que executo.",
    "Tenho que sacrificar compromissos pessoais ou horas de descanso em função do trabalho.",
    "Tenho dúvidas sobre minha competência para executar minhas tarefas.",
    "Sinto-me esgotado(a) física e emocionalmente no final do dia de trabalho.",
    "Já testemunhei ou sofri algum tipo de assédio moral ou pressão excessiva.",
    "As metas e prazos estabelecidos são realistas.",
    "Tenho que lidar com cobranças excessivas sem suporte adequado.",
    "Sinto que recebo tratamento desigual em relação aos demais colegas que exercem a mesma função.",
    "Já recebi ameaças veladas ou explícitas em função de desempenho ou opinião.",
    "Consigo manter equilíbrio entre vida pessoal e profissional.",
    "Já me senti pressionado(a) a sacrificar minha saúde física ou mental em função do trabalho.",
    # PESQUISA GERAL (37-52)
    "Estou me sentindo mentalmente exausto(a) devido às exigências do meu trabalho.",
    "Termino o dia mental e fisicamente esgotado(a).",
    "Sinto-me cansado(a) quando acordo de manhã e tenho que enfrentar outro dia de trabalho.",
    "Trabalhar o dia todo é um grande esforço para mim.",
    "Consigo resolver de forma eficiente os problemas que surgem no meu trabalho.",
    "Sinto-me completamente esgotado(a) pelo meu trabalho.",
    "Sinto que contribuo bastante com minha organização através do meu trabalho.",
    "Sinto-me menos interessado(a) pelo trabalho desde que comecei nesta atividade.",
    "Estou menos entusiasmado(a) com meu trabalho.",
    "Na minha opinião, sou bom(a) no meu trabalho.",
    "Sinto-me entusiasmado(a) quando realizo algo significativo no trabalho.",
    "Consigo fazer várias coisas importantes neste trabalho.",
    "Só quero fazer meu trabalho e não ser incomodado(a).",
    "Sou cético(a) sobre o quanto meu trabalho contribui para a empresa.",
    "Duvido do significado de meu trabalho.",
    "Em meu trabalho, sinto-me confiante sobre minha eficiência ao fazer as coisas.",
    # PESQUISA DE SERVIÇO (53-74)
    "Sinto-me mentalmente esgotado(a) pelas demandas do meu trabalho.",
    "Sinto-me exausto(a) ao final do dia.",
    "Sinto-me muito cansado(a) quando acordo de manhã e tenho que enfrentar outro dia de trabalho.",
    "Consigo facilmente entender como os receptores de meus serviços se sentem sobre as coisas.",
    "Percebo que trato alguns dos receptores de meus serviços como se fossem objetos impessoais.",
    "Trabalhar com pessoas o dia todo é um grande esforço para mim.",
    "Consigo lidar de forma eficiente com os problemas dos receptores de meus serviços.",
    "Sinto-me totalmente exaurido(a) pelas exigências do meu trabalho.",
    "Sinto que influencio de forma positiva as vidas das pessoas através de meu trabalho.",
    "Tornei-me mais indiferente com relação às pessoas desde que assumi este trabalho.",
    "Sinto que este trabalho está me deixando muito menos emocional.",
    "Sinto-me cheio(a) de energia.",
    "Sinto-me frustrado(a) com meu emprego.",
    "Sinto que estou trabalhando muito duro neste trabalho.",
    "Tenho certa dificuldade em me importar com todos os usuários que se beneficiam do meu trabalho.",
    "Trabalhar diretamente com pessoas coloca muita pressão sobre mim.",
    "Crio um ambiente acolhedor e tranquilo para as pessoas que atendo.",
    "Ganho ânimo e motivação ao interagir diretamente com as pessoas que se beneficiam do meu trabalho.",
    "Consegui fazer várias coisas importantes neste trabalho.",
    "Sinto que não tenho mais um pingo de criatividade ou imaginação.",
    "Em meu trabalho, lido com problemas emocionais de forma muito calma.",
    "Sinto que os usuários dos meus serviços às vezes projetam em mim a responsabilidade por seus problemas.",
    # Questionario II - Adriana (75-99)
    "Sinto-me esgotado(a) ao final do dia.",
    "Sinto-me sobrecarregado(a) com o volume de trabalho.",
    "Não tenho energia para enfrentar o dia seguinte.",
    "Tenho dificuldade para me recuperar emocionalmente.",
    "Sinto-me emocionalmente distante do que faço.",
    "Torno-me indiferente às demandas que recebo.",
    "Percebo que trato as pessoas de forma impessoal.",
    "Meu envolvimento emocional com o trabalho é baixo.",
    "Tenho a sensação de que realizo pouco no meu trabalho.",
    "Sinto dúvidas em relação à minha competência.",
    "Cobro demais de mim mesmo(a) o tempo todo.",
    "Sinto que estou sacrificando minha saúde em função do trabalho.",
    "Percebo que tenho pouca autonomia para tomar decisões.",
    "Já vivenciei situações de assédio ou pressão excessiva.",
    "Sou alvo de piadas ou críticas desrespeitosas.",
    "Sinto que sou tratado(a) de maneira injusta.",
    "Já testemunhei colegas sendo humilhados.",
    "Tenho medo de me expressar no ambiente de trabalho.",
    "Sinto-me excluído(a) de decisões importantes.",
    "Já recebi ameaças veladas ou explícitas.",
    "Costumo fazer horas extras com frequência.",
    "Sinto que as metas definidas são inalcançáveis.",
    "Tenho dificuldade para tirar férias ou fazer pausas regulares.",
    "Recebo novas demandas antes de conseguir concluir as anteriores.",
    "Percebo que minha vida pessoal tem sido comprometida pelo trabalho.",
]

# 6 dimensiones (variaveis) do Burnout 99 - ordem = id 1..6 (ExEm, DeCi, RePr, FaPs, AsMo, ExTr)
VARIAVEIS_BURNOUT_99 = [
    {
        "id": 1,
        "nome": "Exaustão Emocional",
        "tag": "ExEm",
        "descricao": "Refere-se ao sentimento de estar emocionalmente sobrecarregado e esgotado pelo trabalho. Pode surgir da pressão constante, da carga excessiva de tarefas e da dificuldade de recuperação emocional.",
        "B": 52, "M": 104, "A": 105,
        "baixa": "O profissional demonstra um bom nível de energia e resiliência frente às exigências do trabalho. Há indícios de que consegue se recuperar emocionalmente após os expedientes e mantém o controle diante de pressões rotineiras. Essa condição favorece o bem-estar psicológico e a sustentabilidade no desempenho profissional.",
        "moderada": "Observa-se uma frequência crescente de cansaço emocional e perda de energia para lidar com as demandas do trabalho. A recuperação emocional pode estar comprometida em certos períodos, o que pode evoluir para quadros mais graves de estafa se não forem adotadas estratégias de manejo do estresse.",
        "alta": "Indica um estado avançado de desgaste psicológico. A pessoa sente-se constantemente esgotada, sem forças para enfrentar o dia de trabalho e com dificuldades para se reequilibrar. Trata-se de uma condição crítica, com alto potencial para desencadear transtornos como ansiedade, depressão e burnout.",
        "r_baixa": "Você apresenta um bom nível de energia e resiliência para lidar com as pressões e exigências do trabalho. Isso indica que, mesmo após dias intensos, você consegue se recuperar emocionalmente, manter o foco e cuidar do seu bem-estar. É essencial que você continue cultivando hábitos saudáveis, como manter uma rotina equilibrada entre trabalho e vida pessoal, praticar atividades relaxantes, cuidar do sono e buscar momentos de lazer. Aproveite para identificar o que contribui para sua recuperação emocional — seja esporte, hobbies, apoio social — e mantenha esses recursos ativos. Essa estabilidade emocional é uma das principais proteções contra o esgotamento futuro.",
        "r_moderada": "Seu resultado mostra sinais moderados de exaustão emocional, sugerindo que, em certos momentos, o estresse acumulado pode estar afetando sua capacidade de recuperação e seu humor. É um momento importante para refletir: você está conseguindo se desconectar do trabalho nas horas de descanso? Há espaço para pausas e lazer? Talvez seja hora de ajustar a carga de atividades, estabelecer limites claros entre trabalho e vida pessoal, priorizar autocuidado e conversar com colegas ou líderes sobre formas de reduzir pressões desnecessárias. Se notar persistência de cansaço, desânimo ou irritabilidade, considerar apoio psicológico pode ser um passo valioso.",
        "r_alta": "O nível de exaustão emocional identificado no seu resultado é preocupante. Ele aponta que as exigências emocionais do trabalho estão ultrapassando seus limites de recuperação, podendo gerar irritabilidade, desmotivação, sensação de vazio ou até sintomas físicos relacionados ao estresse. Este é um sinal claro de que você precisa intervir urgentemente: reveja suas responsabilidades, negocie prazos e tarefas, busque apoio da liderança para redistribuir demandas e, se possível, tire folgas ou férias. Além disso, considere fortemente buscar apoio psicológico ou terapêutico, que pode ajudá-lo a lidar com esse momento e a reconstruir estratégias de enfrentamento saudáveis. Cuidar de si agora é essencial para evitar o agravamento do quadro.",
    },
    {
        "id": 2,
        "nome": "Despersonalização / Cinismo",
        "tag": "DeCi",
        "descricao": "Refere-se ao desenvolvimento de uma atitude cínica, distante ou impessoal em relação ao trabalho ou às pessoas atendidas. Costuma ser uma forma de defesa frente ao estresse contínuo.",
        "B": 58, "M": 116, "A": 117,
        "baixa": "O indivíduo mantém vínculos saudáveis com seu trabalho e colegas. Mostra-se envolvido emocionalmente com suas tarefas, apresentando empatia, interesse e responsabilidade nas relações profissionais. Esse perfil está alinhado a ambientes de trabalho colaborativos e saudáveis.",
        "moderada": "Há sinais de frieza afetiva, distanciamento emocional ou indiferença em relação ao trabalho ou às pessoas ao redor. Embora não graves, esses comportamentos são alertas para possíveis estratégias inconscientes de defesa frente ao estresse crônico ou à insatisfação com o ambiente laboral.",
        "alta": "Forte evidência de desconexão emocional e comportamentos cínicos. O indivíduo tende a minimizar a importância das tarefas e das relações profissionais, podendo adotar uma postura apática ou até hostil. Essa condição é prejudicial para o clima organizacional e pode ser um sintoma central de burnout.",
        "r_baixa": "Você mantém vínculos saudáveis e positivos no ambiente de trabalho, demonstrando empatia, envolvimento e responsabilidade nas relações profissionais. Isso fortalece o clima organizacional e contribui para sua satisfação e bem-estar no dia a dia. Continue investindo em boas práticas de convivência: ofereça apoio aos colegas, compartilhe conquistas, participe ativamente das dinâmicas de equipe e celebre os resultados em conjunto. O engajamento emocional positivo é um fator protetivo importante, mantendo seu trabalho significativo e estimulante.",
        "r_moderada": "Há sinais de certo distanciamento emocional no trabalho, possivelmente como uma forma de proteção frente a pressões ou frustrações. Isso pode levar a um comportamento mais automático, frio ou cínico nas interações. Esteja atento: buscar um distanciamento constante pode prejudicar vínculos, gerar isolamento e reduzir o senso de propósito nas atividades. Reflita sobre o que está gerando esse afastamento — excesso de demandas, conflitos, falta de reconhecimento? Tente recuperar pequenas fontes de satisfação: alinhe expectativas, proponha momentos de interação positiva com colegas e, se necessário, dialogue com a liderança para reavaliar tarefas que estejam sobrecarregando ou frustrando você.",
        "r_alta": "Seu resultado aponta para um nível elevado de despersonalização ou cinismo, o que significa que você pode estar emocionalmente desconectado do trabalho e das pessoas, com tendência a tratar as atividades e os colegas de forma indiferente, crítica ou negativa. Esse é um alerta importante: além de impactar suas relações, essa postura aumenta significativamente o risco de esgotamento. Procure entender a raiz desse afastamento: frustração contínua? Falta de reconhecimento? Sobrecarga emocional? Busque apoio para reorganizar sua rotina, reaproximar-se de colegas confiáveis e, se necessário, considerar aconselhamento psicológico para reconstruir seu engajamento de forma saudável.",
    },
    {
        "id": 3,
        "nome": "Realização Profissional",
        "tag": "RePr",
        "descricao": "Diz respeito à percepção de ineficácia ou de não estar realizando algo significativo. Envolve sentimentos de baixa autoestima profissional e perda de propósito no trabalho.",
        "B": 52, "M": 104, "A": 105,
        "baixa": "O colaborador percebe sentido no trabalho, sente-se competente, valorizado e motivado. Demonstra autoconfiança, orgulho pelo que faz e envolvimento positivo com suas funções. Essa é uma condição de proteção contra o esgotamento profissional.",
        "moderada": "Indica flutuações na percepção de competência e na motivação profissional. A pessoa pode sentir que não está dando o seu melhor, ou que seu esforço não é reconhecido, o que pode comprometer a autoestima e o engajamento no médio prazo.",
        "alta": "Reflete um estado de insatisfação profunda com o próprio desempenho. O profissional sente que não realiza o que poderia ou que seu trabalho é irrelevante, o que compromete diretamente a autoestima, o senso de propósito e a motivação. Essa condição é um fator de risco importante para o adoecimento mental.",
        "r_baixa": "Você percebe sentido no seu trabalho, sente-se valorizado, competente e motivado, o que fortalece sua autoestima e seu bem-estar profissional. Esse senso de realização é um dos principais fatores de proteção contra o esgotamento e a desmotivação. Continue investindo no que alimenta sua autoconfiança: busque novos desafios, comemore conquistas, compartilhe aprendizados e mantenha o alinhamento com os objetivos da equipe e da organização. Aproveite para também apoiar colegas, promovendo um ambiente onde todos possam se sentir valorizados.",
        "r_moderada": "Seu resultado indica que há um senso moderado de realização profissional, com momentos de satisfação, mas também de insegurança ou dúvida quanto ao valor do seu trabalho. É um momento importante para refletir: você sente reconhecimento suficiente? Está conseguindo perceber seus avanços? Talvez seja útil revisar suas metas, pedir feedback construtivo à liderança, identificar oportunidades de desenvolvimento ou buscar atividades que lhe tragam mais propósito. Pequenas mudanças podem reativar seu entusiasmo e fortalecer sua sensação de competência.",
        "r_alta": "O resultado aponta para baixos níveis de realização profissional, o que pode significar sensação de desvalorização, baixa autoestima profissional ou falta de propósito no trabalho. Essa condição representa risco elevado de desmotivação e esgotamento. É essencial que você busque apoio: converse com a liderança sobre suas dificuldades, explore possibilidades de redirecionamento de tarefas, treinamento ou mudança de foco. Além disso, considere apoio psicológico ou de mentoria para ajudá-lo a reconstruir seu senso de propósito e autoestima no trabalho.",
    },
    {
        "id": 4,
        "nome": "Fatores Psicossociais",
        "tag": "FaPs",
        "descricao": "Engloba aspectos organizacionais e de suporte no ambiente de trabalho, como excesso de cobrança, falta de apoio, e clima de pressão. Estes fatores afetam diretamente a saúde mental e o bem-estar dos colaboradores.",
        "B": 20, "M": 40, "A": 41,
        "baixa": "O ambiente de trabalho oferece suporte adequado, clareza nas funções, boa comunicação e autonomia. Esses fatores favorecem a saúde mental e fortalecem o senso de pertencimento e bem-estar no contexto organizacional.",
        "moderada": "Há desequilíbrios nos fatores psicossociais, como ambiguidade de papéis, excesso de exigências, falhas na comunicação ou ausência de apoio. Tais condições afetam o rendimento e aumentam o risco de estresse ocupacional, exigindo monitoramento contínuo.",
        "alta": "O cenário psicossocial é crítico. Falta de autonomia, ausência de apoio da liderança, metas incoerentes ou conflitos interpessoais intensos tornam o ambiente tóxico. Essa situação representa risco iminente à saúde mental e deve ser objeto de ações corretivas institucionais.",
        "r_baixa": "Seu ambiente de trabalho parece oferecer suporte adequado, clareza de papéis, boa comunicação e autonomia. Esses fatores fortalecem seu senso de pertencimento e bem-estar, criando condições ideais para um desempenho saudável e sustentável. Continue valorizando esses aspectos, cultivando bons relacionamentos, mantendo canais abertos de diálogo e participando ativamente das discussões sobre melhorias no ambiente. Você também pode atuar como um agente positivo, ajudando colegas a se integrarem e reforçando uma cultura organizacional saudável.",
        "r_moderada": "Seu resultado mostra que há aspectos psicossociais que poderiam ser melhorados: talvez a comunicação esteja falhando, as funções estejam pouco claras ou o suporte da equipe não esteja fluindo bem. Esses fatores podem gerar insegurança, estresse ou sensação de isolamento. Esteja atento: procure identificar onde estão as maiores dificuldades e proponha pequenas ações de melhoria — seja solicitando reuniões de alinhamento, buscando feedbacks mais claros ou sugerindo ajustes na forma de trabalho em equipe. Um ambiente psicossocial equilibrado depende também da sua participação ativa nas soluções.",
        "r_alta": "O resultado indica que o ambiente psicossocial está desfavorável, com alta chance de problemas relacionados a falta de apoio, comunicação ineficaz, conflitos ou baixa autonomia. Esse cenário representa um risco importante para sua saúde emocional e para o desempenho profissional. Não hesite em buscar apoio institucional: acione lideranças, setores de RH ou espaços de mediação para relatar dificuldades e buscar soluções. Além disso, busque fortalecer sua rede de apoio pessoal, cuidando das emoções e buscando suporte externo, se necessário. Intervir cedo ajuda a prevenir impactos mais graves.",
    },
    {
        "id": 5,
        "nome": "Assédio Moral",
        "tag": "AsMo",
        "descricao": "Envolve condutas abusivas repetitivas que expõem o trabalhador a situações humilhantes, constrangedoras ou degradantes. Pode afetar profundamente a saúde mental e a autoestima da pessoa.",
        "B": 30, "M": 60, "A": 61,
        "baixa": "Não há relatos ou indícios de práticas abusivas. O ambiente é predominantemente respeitoso, ético e seguro, promovendo relações saudáveis entre pares e lideranças.",
        "moderada": "Há indícios pontuais de comportamentos inadequados, como piadas ofensivas, ironias ou críticas públicas. Embora não configurando assédio sistemático, esses episódios contribuem para um clima de insegurança e devem ser investigados com atenção.",
        "alta": "Forte presença de comportamentos abusivos, humilhantes ou discriminatórios, configurando assédio moral. Esse tipo de conduta compromete gravemente a saúde psíquica da vítima e a integridade do ambiente de trabalho. A situação exige apuração imediata e ações institucionais contundentes.",
        "r_baixa": "Seu ambiente de trabalho é percebido como respeitoso, ético e seguro, sem indícios de práticas abusivas. Esse é um indicador muito positivo, que favorece relações saudáveis, confiança e colaboração. Continue cultivando esse clima: pratique respeito mútuo, fortaleça os vínculos de confiança e participe ativamente de ações que promovam ética e inclusão. Além disso, mantenha-se atento a qualquer sinal de mudança no ambiente, para que esse padrão positivo seja preservado.",
        "r_moderada": "O resultado mostra que há sinais moderados de possíveis tensões ou situações desconfortáveis que podem ser percebidas como práticas abusivas, ainda que não constantes ou explícitas. É um momento importante para ficar atento: observe os comportamentos no ambiente, busque manter diálogo aberto com colegas e líderes e, caso perceba padrões inadequados, não hesite em buscar orientação ou apoio. Prevenir situações de assédio envolve não apenas proteger a si, mas também contribuir para um ambiente mais seguro e respeitoso para todos.",
        "r_alta": "O resultado aponta para indícios elevados de assédio moral no ambiente de trabalho, um fator extremamente preocupante, que exige atenção imediata. Situações como desrespeito constante, humilhações, isolamento ou ameaças precisam ser enfrentadas com apoio institucional. Busque ajuda: acione canais formais da empresa (RH, ouvidoria, liderança) e, se necessário, procure suporte externo (jurídico, psicológico). Lembre-se: ninguém deve enfrentar essas situações sozinho, e você tem direito a um ambiente de trabalho seguro e digno.",
    },
    {
        "id": 6,
        "nome": "Excesso de Trabalho",
        "tag": "ExTr",
        "descricao": "Diz respeito à sobrecarga de tarefas, metas inatingíveis, falta de pausas e desequilíbrio entre vida pessoal e profissional. É um dos principais preditores de burnout e adoecimento psíquico.",
        "B": 32, "M": 64, "A": 65,
        "baixa": "As demandas profissionais estão equilibradas com os recursos e capacidades do trabalhador. Há espaço para pausas, férias e manutenção de qualidade de vida. Esse equilíbrio é protetivo para a saúde física e mental.",
        "moderada": "Sinais de sobrecarga pontual, dificuldade em conciliar as tarefas com os prazos ou ausência de pausas adequadas. Ainda que a situação seja gerenciável, há risco de evolução para esgotamento se persistir por longos períodos.",
        "alta": "O trabalhador está submetido a uma carga excessiva de tarefas, frequentemente extrapolando horários, suprimindo momentos de descanso e convivendo com metas inalcançáveis. Essa realidade afeta diretamente a saúde física e psíquica, e é uma das principais causas de burnout.",
        "r_baixa": "Você mantém um bom equilíbrio entre demandas profissionais e recursos pessoais, conseguindo administrar bem seu tempo, priorizar tarefas e garantir momentos de descanso e lazer. Isso é essencial para manter a saúde mental e física no longo prazo. Continue atento: revise regularmente sua carga de trabalho, comunique-se abertamente sobre prazos e metas e mantenha hábitos de autocuidado. Pequenas rotinas saudáveis, como pausas durante o expediente, exercícios físicos e tempo com a família, ajudam a preservar esse equilíbrio.",
        "r_moderada": "Há sinais de que suas demandas profissionais podem estar se aproximando do limite saudável, gerando sensação de sobrecarga em alguns momentos. Isso exige atenção: revise seu planejamento, organize prioridades, avalie o que pode ser delegado e converse com sua liderança sobre formas de aliviar pontos críticos. Também é importante fortalecer práticas de recuperação: reserve tempo para descanso, atividades prazerosas e autocuidado. Uma rotina sustentável depende de equilíbrio entre produtividade e bem-estar.",
        "r_alta": "Seu resultado indica um nível preocupante de excesso de trabalho, com alta probabilidade de sobrecarga e impacto negativo na saúde física e emocional. É essencial intervir rapidamente: negocie redistribuição de tarefas, reveja prazos, busque apoio da liderança e, se possível, considere uma pausa para recuperação. Além disso, ative redes de apoio emocional e, se necessário, busque orientação psicológica. Lembre-se: reduzir a carga não é sinal de fraqueza, mas uma ação fundamental para proteger sua saúde e sua capacidade de continuar produzindo no longo prazo.",
    },
]

# (pergunta_id, variavel_id) - VarPerguntaBurnOutSeeder PHP (formulario 1, perguntas 1-99, variaveis 1-6)
PERGUNTA_VARIAVEL_BURNOUT = [
    (1, 6), (2, 5), (3, 1), (4, 3), (5, 1), (6, 4), (7, 5), (8, 5), (9, 4), (10, 6), (11, 2), (12, 6), (13, 6), (14, 5), (15, 2), (16, 5), (17, 6), (18, 4), (19, 6), (20, 5), (21, 4), (22, 3), (23, 6), (24, 6), (25, 3), (26, 2), (27, 6), (28, 3), (29, 1), (30, 5), (31, 4), (32, 6), (33, 5), (34, 5), (35, 4), (36, 6),
    (37, 1), (38, 1), (39, 1), (40, 1), (41, 3), (42, 1), (43, 3), (44, 2), (45, 2), (46, 3), (47, 3), (48, 3), (49, 2), (50, 2), (51, 2), (52, 3),
    (53, 2), (54, 2), (55, 2), (56, 2), (58, 2), (59, 2), (60, 2), (61, 2), (64, 2), (65, 2), (66, 2), (68, 2), (69, 2), (70, 2), (71, 2), (72, 2), (73, 2),
    (53, 3), (54, 3), (55, 3), (57, 3), (58, 3), (60, 3), (62, 3), (63, 3), (65, 3), (66, 3), (67, 3), (68, 3), (72, 3), (74, 3),
    (55, 1), (56, 1), (57, 1), (59, 1), (61, 1), (62, 1), (63, 1), (64, 1), (67, 1), (69, 1), (70, 1), (71, 1), (73, 1), (74, 1),
    (75, 1), (76, 1), (77, 1), (78, 1), (79, 2), (80, 2), (81, 2), (82, 2), (83, 3), (84, 3), (85, 4), (86, 4), (87, 4), (88, 4), (89, 5), (90, 5), (91, 5), (92, 5), (93, 5), (94, 5), (95, 6), (96, 6), (97, 6), (98, 6), (99, 6),
]


# Contraseña para todos los usuarios de seed (igual que Laravel)
SEED_PASSWORD = "admin123"


def seed_tipo_calculo(db):
    """Tipos de cálculo (SOMATORIO, MEDIA, MÁXIMO, etc.)."""
    if db.query(TipoCalculo).count() > 0:
        print("  tipo_calculo ya tiene datos, se omite.")
        return
    items = [
        ("SOMATORIO", "Soma dos valores das variáveis"),
        ("MEDIA", "Soma e divisão pela quantidade de perguntas"),
        ("MÁXIMO", "Maior valor entre as respostas"),
        ("MÍNIMO", "Menor valor entre as respostas"),
        ("PORCENTAGEM DE ACERTOS", "Percentual de respostas corretas"),
        ("PONTUAÇÃO COM PESO", "Soma das respostas multiplicadas pelos pesos"),
        ("MÉDIA POR BLOCO", "Média separada por grupo de perguntas"),
        ("DELTA ENTRE GRUPOS", "Diferença entre médias de grupos"),
        ("PADRÃO BINÁRIO", "Contagem de acertos (0 ou 1)"),
        ("DESVIO PADRÃO", "Variação das respostas em relação à média"),
        ("MODA", "Valor mais frequente nas respostas"),
        ("EXPRESSÃO PERSONALIZADA", "Cálculo definido por expressão customizada"),
    ]
    for i, (nome, descricao) in enumerate(items, start=1):
        db.add(TipoCalculo(id=i, nome=nome, descricao=descricao))
    db.commit()
    print("  tipo_calculo: %d registros." % len(items))


def seed_users(db):
    """Usuarios iniciales: SA/Admin, Administrador, Gestor."""
    emails = [
        "wheelkorner@gmail.com",
        "desenvolvedor@fellipelli.com.br",
        "arley.rincon@fellipelli.com.br",
    ]
    existing = db.query(User).filter(User.email.in_(emails)).count()
    if existing > 0:
        print("  users (seed) ya existen, se omiten.")
        return
    password_hash = get_password_hash(SEED_PASSWORD)
    now = datetime.now(timezone.utc)
    users_data = [
        {
            "name": "Arley Humberto Rueda Rincon",
            "email": "wheelkorner@gmail.com",
            "sa": True,
            "admin": True,
            "usuario": True,
            "gestor": True,
            "cliente_id": None,
        },
        {
            "name": "Administrador",
            "email": "desenvolvedor@fellipelli.com.br",
            "sa": False,
            "admin": True,
            "usuario": True,
            "gestor": True,
            "cliente_id": None,
        },
        {
            "name": "Gestor",
            "email": "arley.rincon@fellipelli.com.br",
            "sa": False,
            "admin": False,
            "usuario": True,
            "gestor": True,
            "cliente_id": None,  # se actualiza después de crear cliente
        },
    ]
    for u in users_data:
        user = User(
            name=u["name"],
            email=u["email"],
            password=password_hash,
            email_verified_at=now,
            sa=u["sa"],
            admin=u["admin"],
            usuario=u["usuario"],
            gestor=u["gestor"],
            ativo=True,
            cliente_id=u["cliente_id"],
        )
        db.add(user)
    db.commit()
    print("  users: 3 registros (contraseña: %s)." % SEED_PASSWORD)


def seed_clientes(db):
    """Cliente inicial FELLIPELLI (gestor = usuario id 3)."""
    if db.query(Cliente).count() > 0:
        print("  clientes ya tiene datos, se omite.")
        return
    db.add(
        Cliente(
            id=1,
            usuario_id=3,
            tipo=TipoCliente.CNPJ,
            cpf_cnpj="07792897000182",
            nome_fantasia="FELLIPELLI",
            razao_social="FELLIPELLI INSTRUMENTOS DE DIAGNOSTICO LTDA.",
            email="adriana.fellipelli@fellipelli.com.br",
            contato="Adriana Fellipelli",
            telefone="1142807100",
            ativo=True,
        )
    )
    db.commit()
    # Asignar cliente_id=1 al gestor (usuario id 3)
    gestor = db.query(User).filter(User.email == "arley.rincon@fellipelli.com.br").first()
    if gestor:
        gestor.cliente_id = 1
        db.commit()
    print("  clientes: 1 registro (FELLIPELLI).")


def seed_formulario_burnout(db):
    """Formulario Burnout 99 y sus etapas (opcional)."""
    if db.query(Formulario).filter(Formulario.id == 1).first():
        print("  formulario Burnout 99 ya existe, se omite.")
        return
    db.add(
        Formulario(
            id=1,
            nome="Burnout 99",
            label="",
            descricao="<p>Questionário de Riscos Psicossociais (Base NR-1 e MBI Adaptado)</p><p>Avaliação Individual</p><p>Este relatório apresenta a análise das dimensões psicossociais avaliadas conforme os parâmetros da <b>NR-1</b> e do inventário <b>MBI</b> adaptado. </p><p>As faixas são classificadas como Baixa, Moderada ou Alta, com base nas pontuações obtidas.</p>",
            instrucoes="<p>Responda com toda a sinceridade, sabendo que o sigilo da sua identidade é absoluto e garantido por nós, e que seu feedback é indispensável para construir um ambiente de trabalho mais sustentável e positivo para todos. Você está em um espaço 100% seguro, e cada resposta é valiosa para elevar sua qualidade de vida. </p><p>Use a escala de 1 a 5, onde:</p><ul><li>1 - Uma vez por mês ou nenhuma</li><li>2 - Algumas vezes por mês</li><li>3 - Uma vez por semana</li><li>4 - Algumas vezes por semana</li><li>5 - Todos os dias</li></ul>",
            score_ini=1,
            score_fim=5,
            calculo_id=1,
            status=False,
        )
    )
    db.commit()
    # Etapas do formulário 1
    etapas = [
        (1, 1, 36),
        (2, 37, 52),
        (3, 53, 74),
        (4, 75, 99),
    ]
    for etapa, de, ate in etapas:
        db.add(
            FormularioEtapa(
                formulario_id=1,
                etapa=etapa,
                de=de,
                ate=ate,
            )
        )
    db.commit()
    print("  formulario Burnout 99 + 4 etapas creados.")


def seed_perguntas_burnout(db):
    """99 perguntas do formulário Burnout 99 (formulario_id=1). Só insere se ainda não houver perguntas para esse formulário."""
    existing = db.query(Pergunta).filter(Pergunta.formulario_id == 1).count()
    if existing > 0:
        print("  perguntas (Burnout 99) ya existen (%d), se omite." % existing)
        return
    for i, texto in enumerate(PERGUNTAS_BURNOUT_99, start=1):
        db.add(
            Pergunta(
                formulario_id=1,
                numero_da_pergunta=i,
                pergunta=texto[:500],
            )
        )
    db.commit()
    print("  perguntas Burnout 99: %d creadas." % len(PERGUNTAS_BURNOUT_99))


def seed_variaveis_burnout(db):
    """6 dimensiones (variaveis) do Burnout 99. Insere em ordem para que ids fiquem 1-6 (ExEm, DeCi, RePr, FaPs, AsMo, ExTr)."""
    existing = db.query(Variavel).filter(Variavel.formulario_id == 1).count()
    if existing > 0:
        print("  variaveis (Burnout 99) ya existen (%d), se omite." % existing)
        return
    for v in VARIAVEIS_BURNOUT_99:
        data = {k: v[k] for k in v if k != "id"}
        data["formulario_id"] = 1
        db.add(Variavel(**data))
    db.commit()
    print("  variaveis Burnout 99: 6 dimensiones creadas.")


def seed_pergunta_variavel_burnout(db):
    """Associa perguntas 1-99 às dimensiones (variaveis 1-6). Só insere se ainda não houver registros."""
    existing = db.query(PerguntaVariavel).filter(PerguntaVariavel.variavel_id <= 6).count()
    if existing > 0:
        print("  pergunta_variavel (Burnout 99) ya existen (%d), se omite." % existing)
        return
    for pergunta_id, variavel_id in PERGUNTA_VARIAVEL_BURNOUT:
        db.add(PerguntaVariavel(pergunta_id=pergunta_id, variavel_id=variavel_id))
    db.commit()
    print("  pergunta_variavel Burnout 99: %d asociaciones creadas." % len(PERGUNTA_VARIAVEL_BURNOUT))


def run_seed(include_formulario: bool = True):
    """Ejecuta todos los seeders en orden."""
    db = SessionLocal()
    try:
        print("Ejecutando seeders...")
        seed_tipo_calculo(db)
        seed_users(db)
        seed_clientes(db)
        if include_formulario:
            seed_formulario_burnout(db)
            seed_perguntas_burnout(db)
            seed_variaveis_burnout(db)
            seed_pergunta_variavel_burnout(db)
        print("Listo.")
    except Exception as e:
        print("Error:", e, file=sys.stderr)
        db.rollback()
        raise
    finally:
        db.close()


if __name__ == "__main__":
    import argparse
    parser = argparse.ArgumentParser(description="Seed database (users, clientes, tipo_calculo, opcional formulario Burnout).")
    parser.add_argument("--no-formulario", action="store_true", help="No crear formulario Burnout 99 ni etapas")
    args = parser.parse_args()
    run_seed(include_formulario=not args.no_formulario)
