@extends('adminlte::page')

@section('title', 'Fellipelli')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <p class="lead mb-0" style="font-size: 18px;">FAQ - Dúvidas NR-1</p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <!-- Breadcrumbs -->
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title"> <i class="fa-solid fa-sitemap mr-2"></i>FAQ - Dúvidas NR-1</h3>
    </div>

    <div class="card-body">
        <div>
            <h5>O que é a NR-1?</h5>
            <p style="text-align: justify;">
                É a norma que define os princípios e diretrizes do Gerenciamento de Riscos Ocupacionais (GRO) e do Programa de Gerenciamento de Riscos (PGR), além de tratar da capacitação em Saúde e Segurança Trabalhista (SST) e das responsabilidades de empregadores e empregados.
            </p> 
        </div>
        <div>
            <h5>Quem precisa cumprir a NR-1? </h5>
            <p style="text-align: justify;">
                Todas as empresas que contratam trabalhadores sob o regime da CLT, independentemente do porte ou setor. Pequenas empresas podem ter algumas flexibilizações, dependendo do grau de risco.
            </p> 
        </div>
        <div>
            <h5>Quais são as principais mudanças em 2025?</h5>
            <p style="text-align: justify;">
                A nova versão reforça a gestão de riscos psicossociais, como estresse e ansiedade, e exige que as empresas adotem medidas preventivas.  
            </p> 
        </div>
        <div>
            <h5>Empresas pequenas também precisam se adequar? </h5>
            <p style="text-align: justify;">
                Sim, com poucas exceções, como MEIs, MEs e EPPs com grau de risco 1 ou 2 e sem exposição a agentes nocivos.  
            </p> 
        </div>
        <div>
            <h5>Quando entram em vigor as mudanças?</h5>
            <p style="text-align: justify;">
                As alterações promovidas pela Portaria MTE nº 1.419 entraram em vigor em 26 de maio de 2025. 
            </p> 
        </div>
        <div>
            <h5>Quais são os riscos se minha empresa não se adequar à NR-1?  </h5>
            <p style="text-align: justify;">
                O descumprimento da NR-1 gera sérias consequências para as empresas em vários âmbitos: legal, financeiro, comercial e psicológico. Dentre eles destacam-se:  

Multas e sanções administrativas - A fiscalização do trabalho pode aplicar multas que variam conforme a gravidade da infração e o porte da empresa. Em casos mais graves, pode haver interdição de atividades. 

Ações judiciais e indenizações - Se for comprovado que o descumprimento da NR-1 contribuiu para o adoecimento físico ou mental de um trabalhador, a empresa pode ser responsabilizada judicialmente e obrigada a pagar indenizações por danos morais, materiais ou até pensão vitalícia. 

Danos à imagem e clima organizacional Ambientes de trabalho inseguros ou negligentes afetam a confiança dos colaboradores, elevam a rotatividade e prejudicam a performance. 
            </p> 
        </div>
        <div>
            <h5>O que são riscos psicossociais?</h5>
            <p style="text-align: justify;">
                Os riscos psicossociais, segundo a nova redação da NR-1, são fatores do ambiente de trabalho que podem afetar negativamente a saúde mental e emocional dos trabalhadores. Eles incluem situações como: 

Sobrecarga de trabalho 

Assédio moral ou sexual 

Ambiente tóxico 

Metas abusivas ou inatingíveis 

Isolamento social 

Falta de suporte da liderança 

Pressão constante por resultados e hiperconectividade 

Esses riscos passaram a ser formalmente reconhecidos como parte do Programa de Gerenciamento de Riscos (PGR), e sua avaliação será obrigatória a partir de maio de 2026. 
            </p> 
        </div>
        <div>
            <h5>O que é um Programa de Gerenciamento de Riscos (PGR)?  </h5>
            <p style="text-align: justify;">
                O PGR funciona como um raio-X corporativo, iniciando com um levantamento de riscos (“prazos apertados estão pressionando o time”, por exemplo), passando por uma avaliação da sua gravidade e probabilidade (“elevado risco de burnout”), e, por fim, implementando um plano de ação (ex.: “Perfil de Resiliência ao Estresse e revisão do plano de metas). Trata-se de um documento vivo, que deve ser continuamente revisado e que atesta que a empresa está cuidando da segurança e saúde dos colaboradores – inclusive no campo mental. 
            </p> 
        </div>
        <div>
            <h5>É possível blindar minha empresa de todos esses problemas e garantir a conformidade à NR-1?  </h5>
            <p style="text-align: justify;">
                SIM! 	Não arrisque a saúde emocional, financeira e jurídica da sua empresa. Conte com 	toda a expertise da FELLIPELLI para assegurar a conformidade à NR-1 da sua organização: caminhamos lado a lado com o seu time por toda 	essa trilha, desde o diagnóstico, passando pelo treinamento e até a elaboração de um Programa de Gerenciamento de Riscos (PGR) sólido e seguro. Invista no internacionalmente reconhecido poder da FELLIPELLI de promover saúde mental e proteger sua empresa. Vamos lá? INSERIR HIPERLINK APP HOME
            </p> 
        </div>
    
    <div class="card-footer text-right">
    </div>

</div>


    @include('layouts.partials.whatsapp')

@stop


@section('css')
    <style>
        .main-sidebar {
            min-height: 100vh;
        }

        ::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }

        body {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        body::-webkit-scrollbar {
            display: none;
        }
    </style>
@stop

@section('js')
    <script> console.log("Usando Script de JS"); </script>
@stop

