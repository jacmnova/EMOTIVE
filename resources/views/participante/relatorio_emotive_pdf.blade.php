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
        /* Configuración de página A4 */
        @page {
            size: A4 portrait;
            margin: 0;
        }
        
        @page :first {
            margin: 0;
        }
        
        @page :not(:first) {
            margin: 0;
        }
        
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
            max-width: 595.28pt; /* Ancho exacto de A4 en portrait */
            margin: 0 auto;
            overflow-x: hidden;
        }
        
        .relatorio-emotive {
            font-family: 'Quicksand', sans-serif;
            background: white;
            color: #333;
            width: 100%;
            max-width: 595.28pt; /* Ancho exacto de A4 en portrait */
            margin: 0 auto;
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
        
        /* Permitir que las secciones se dividan automáticamente en múltiples páginas */
        .relatorio-emotive > div {
            page-break-inside: auto;
            min-height: 0;
        }
        
        /* Evitar cortes en elementos críticos */
        h1, h2, h3 {
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        
        img, table, canvas {
            page-break-inside: avoid;
            max-width: 100%;
            height: auto;
        }
        
        .page-break {
            page-break-after: always;
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: 2px dashed #ddd;
            width: 100%;
            max-width: 595.28pt;
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
            page-break-inside: avoid;
        }
        
        .quote-box {
            background-color: #e8f4f8;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            font-style: italic;
            font-family: 'Quicksand', sans-serif !important;
            page-break-inside: avoid;
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
        
        /* Clase base para páginas A4 - cada sección es una página completa */
        .page-a4 {
            width: 100%;
            max-width: 595.28pt; /* Ancho exacto de A4 */
            height: 842pt; /* Altura exacta de A4 */
            min-height: 842pt;
            max-height: 842pt;
            margin: 0 auto;
            padding: 18pt;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            page-break-after: always;
            page-break-inside: avoid;
            font-family: 'DejaVu Sans', sans-serif;
            position: relative;
        }
        
        .page-a4-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .page-a4-footer {
            margin-top: auto;
            flex-shrink: 0;
            padding-top: 12pt;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            page-break-inside: avoid;
        }

        .container-portada {
            height: 842pt;
            min-height: 842pt;
            max-height: 842pt;
            gap: 10% !important;
            page-break-after: always;
            display: flex;
            flex-direction: column;
        }

        .section-pdf {
            min-height: 842pt !important;
            max-height: 842pt !important;
            display: block;
          
            height: 842pt;
            position: relative;
        }

        .section-pdf-large {
            / min-height: 842pt !important;
            max-height: 842pt !important;
            display: block;
            
            height: 842pt;
            position: relative;
        }

        .section-pdf-footer {
            position: absolute;
            bottom: 20px;
            width: 90%;
        }

        .fail-pdf {
            width: 80% !important;
        }

        .grafico-radar-emotive-pdf {
            width: 100%; 
            max-height: 350px; 
            height: auto;
            page-break-inside: avoid;
        }
        
        /* Evitar cortes en contenedores de gráficos */
        .graficos-container,
        .chart-container {
            page-break-inside: avoid;
        }
        
        /* Mejorar el espaciado entre secciones */
        .relatorio-emotive > div + div {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="relatorio-emotive">
        @include('participante.emotive.partials._capa')
        @include('participante.emotive.partials._introducao')
        @include('participante.emotive.partials._estrutura_modelo')
        @include('participante.emotive.partials._resultado_emotive')
        @include('participante.emotive.partials._estado_emocional_parte1')
        @include('participante.emotive.partials._estado_emocional_parte2')
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



