<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório E.MO.TI.VE - Captura</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Quicksand', sans-serif;
            background: white;
            color: #333;
            width: 100%;
            overflow-x: hidden;
        }
        
        .relatorio-emotive {
            font-family: 'Quicksand', sans-serif;
            background: white;
            color: #333;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        .relatorio-emotive,
        .relatorio-emotive *,
        .relatorio-emotive h1,
        .relatorio-emotive h2,
        .relatorio-emotive h3,
        .relatorio-emotive h4,
        .relatorio-emotive p,
        .relatorio-emotive span,
        .relatorio-emotive div,
        .relatorio-emotive li,
        .relatorio-emotive td,
        .relatorio-emotive th {
            font-family: 'Quicksand', sans-serif !important;
        }
        
        .page-break {
            page-break-after: always;
            margin-bottom: 50px;
            padding-bottom: 50px;
            border-bottom: 2px dashed #ddd;
            width: 100%;
            box-sizing: border-box;
            font-family: 'Quicksand', sans-serif !important;
        }
        
        .section-title {
            color: #A4977F;
            font-size: 24px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            font-family: 'Quicksand', sans-serif !important;
        }
        
        .section-subtitle {
            color: #2E9196;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            font-family: 'Quicksand', sans-serif !important;
        }
        
        .highlight-box {
            background-color: #e8f4f8;
            border-left: 4px solid #008ca5;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-family: 'Quicksand', sans-serif !important;
        }
        
        .quote-box {
            background-color: #e8f4f8;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            font-style: italic;
            font-family: 'Quicksand', sans-serif !important;
        }
        
        .faixa-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            margin: 5px;
            font-family: 'Quicksand', sans-serif !important;
        }
        
        .faixa-baixa {
            background-color: #d4edda;
            color: #155724;
        }
        
        .faixa-moderada {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .faixa-alta {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="relatorio-emotive">
        @include('participante.emotive.partials._capa')
        @include('participante.emotive.partials._introducao')
        @include('participante.emotive.partials._estrutura_modelo')
        @include('participante.emotive.partials._resultado_emotive')
        @include('participante.emotive.partials._estado_emocional')
        @include('participante.emotive.partials._eixos_analiticos')
        @include('participante.emotive.partials._risco_descarrilamento')
        @include('participante.emotive.partials._saude_emocional')
        @include('participante.emotive.partials._plano_desenvolvimento')
        @include('participante.emotive.partials._conclusao')
    </div>
    
    <script src="{{ asset('../js/utils.js') }}"></script>
    @include('participante.emotive.partials._scripts')
    
    <script>
        // Esperar a que todos los gráficos se carguen antes de que Browsershot capture
        let chartsLoaded = 0;
        let totalCharts = 0;
        
        // Contar cuántos gráficos hay en la página
        window.addEventListener('load', function() {
            // Buscar todos los canvas que son gráficos
            const canvases = document.querySelectorAll('canvas');
            totalCharts = canvases.length;
            
            if (totalCharts === 0) {
                // Si no hay gráficos, marcar como listo inmediatamente
                document.body.setAttribute('data-ready', 'true');
                return;
            }
            
            // Esperar a que Chart.js renderice todos los gráficos
            // Verificar cada segundo si los gráficos están listos
            const checkCharts = setInterval(function() {
                let readyCharts = 0;
                canvases.forEach(function(canvas) {
                    // Verificar si el canvas tiene contenido (no está vacío)
                    const ctx = canvas.getContext('2d');
                    if (ctx) {
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const hasContent = imageData.data.some(function(channel) {
                            return channel !== 0;
                        });
                        if (hasContent) {
                            readyCharts++;
                        }
                    }
                });
                
                // Si todos los gráficos están listos o han pasado 10 segundos, marcar como listo
                if (readyCharts >= totalCharts || Date.now() - window.loadStartTime > 10000) {
                    clearInterval(checkCharts);
                    document.body.setAttribute('data-ready', 'true');
                }
            }, 500);
            
            window.loadStartTime = Date.now();
            
            // Timeout de seguridad: marcar como listo después de 15 segundos máximo
            setTimeout(function() {
                clearInterval(checkCharts);
                document.body.setAttribute('data-ready', 'true');
            }, 15000);
        });
    </script>
</body>
</html>



