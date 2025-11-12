@php
    $isPdfMode = isset($isPdf) && $isPdf;
    $padding = $isPdfMode ? '18pt' : '40px';
    $marginBottom = $isPdfMode ? '12pt' : '30px';
    $titleSize = $isPdfMode ? '18pt' : '24px';
    $subtitleSize = $isPdfMode ? '12pt' : '16px';
    $textSize = $isPdfMode ? '10pt' : '10px';
    $footerMargin = $isPdfMode ? '20pt' : '50px';
@endphp
@if($isPdfMode)
<div class="section-pdf" style="padding: {{ $padding }}; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box; font-family: 'DejaVu Sans', sans-serif;">
@else
<div class="section-pdf" style="padding: {{ $padding }}; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;">
@endif
    <h1 style="color: #A4977F; font-size: {{ $titleSize }}; font-style: normal; font-weight: 700; line-height: 1.2; margin: 0 0 {{ $marginBottom }} 0; font-family: 'DejaVu Sans', sans-serif;">RELATÓRIO E.MO.TI.VE®</h1>
    
    <div style="margin-bottom: {{ $marginBottom }};">
        <h2 style="color: #2E9196; font-size: {{ $subtitleSize }}; font-style: normal; font-weight: 400; line-height: 1.3; margin: 0 0 8pt 0; font-family: 'DejaVu Sans', sans-serif;">Ferramenta de Autoconhecimento e Prevenção de Riscos Psicossociais</h2>
        <p style="color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; margin: 0 0 {{ $isPdfMode ? '6pt' : '10px' }} 0; font-family: 'DejaVu Sans', sans-serif;">
            O E.MO.TI.VE® é uma ferramenta de avaliação desenvolvida para identificar sinais de desequilíbrio emocional e ocupacional no ambiente de trabalho. Baseado em evidências científicas e normas regulatórias, este instrumento oferece uma análise personalizada do seu estado emocional e psicossocial.
        </p>
        <p style="color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; margin: 0 0 {{ $isPdfMode ? '6pt' : '10px' }} 0; font-family: 'DejaVu Sans', sans-serif;">
            <strong>Mais do que medir</strong> — este relatório busca promover reflexão e autoconhecimento sobre fatores que influenciam seu bem-estar profissional.
        </p>
        <p style="color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; margin: 0 0 {{ $isPdfMode ? '10pt' : '15px' }} 0; font-family: 'DejaVu Sans', sans-serif;">
            <strong>Mais do que um diagnóstico</strong> — oferece orientações práticas para fortalecer sua resiliência emocional e melhorar sua relação com o trabalho.
        </p>
        
        <div style="border-radius: 10px; background: rgba(46, 145, 150, 0.20); color: #2E9196; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; padding: {{ $isPdfMode ? '8pt' : '8px' }}; text-align: center; margin: {{ $isPdfMode ? '10pt' : '15px' }} auto; display: flex; align-items: center; justify-content: center; page-break-inside: avoid; font-family: 'DejaVu Sans', sans-serif;">
            <p style="color: #2E9196; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; margin: 0; font-family: 'DejaVu Sans', sans-serif;">
                "Saúde emocional não é ausência de estresse, mas a capacidade de reconhecê-lo e se fortalecer diante dele."
            </p>
        </div>
    </div>
    
    <div style="margin-bottom: {{ $marginBottom }}; page-break-inside: avoid;">
        <h2 style="color: #2E9196; font-size: {{ $subtitleSize }}; font-style: normal; font-weight: 400; line-height: 1.3; margin: 0 0 8pt 0; font-family: 'DejaVu Sans', sans-serif;">Finalidade do Instrumento</h2>
        <p style="color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; margin: 0 0 6pt 0; font-family: 'DejaVu Sans', sans-serif;">
            Este relatório identifica dimensões relacionadas ao bem-estar emocional e ocupacional, permitindo que você:
        </p>
        <ul style="padding-left: 18pt; color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; margin: 0; list-style: disc; font-family: 'DejaVu Sans', sans-serif;">
            <li style="margin-bottom: 4pt; font-family: 'DejaVu Sans', sans-serif;">Reconheça áreas de equilíbrio e vulnerabilidade emocional</li>
            <li style="margin-bottom: 4pt; font-family: 'DejaVu Sans', sans-serif;">Identifique sinais precoces de desgaste ou sobrecarga</li>
            <li style="margin-bottom: 4pt; font-family: 'DejaVu Sans', sans-serif;">Compreenda como fatores organizacionais e pessoais se interrelacionam</li>
            <li style="margin-bottom: 4pt; font-family: 'DejaVu Sans', sans-serif;">Receba orientações práticas para promover autocuidado e desenvolvimento</li>
        </ul>
    </div>
    
    <div style="margin-bottom: {{ $marginBottom }}; page-break-inside: avoid;">
        <h2 style="color: #2E9196; font-size: {{ $subtitleSize }}; font-style: normal; font-weight: 400; line-height: 1.3; margin: 0 0 8pt 0; font-family: 'DejaVu Sans', sans-serif;">Base Normativa e Científica</h2>
        <ol style="padding-left: 18pt; color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; margin: 0; font-family: 'DejaVu Sans', sans-serif;">
            <li style="margin-bottom: 6pt; font-family: 'DejaVu Sans', sans-serif;"><strong>NR-1 (Portaria 6.730/2020):</strong> Estabelece diretrizes para avaliação de riscos psicossociais no trabalho, exigindo que organizações identifiquem e previnam fatores que possam comprometer a saúde mental dos trabalhadores.</li>
            <li style="margin-bottom: 6pt; font-family: 'DejaVu Sans', sans-serif;"><strong>Modelo de Maslach (Burnout):</strong> Baseado em décadas de pesquisa científica, o modelo identifica três dimensões principais: exaustão emocional, despersonalização e redução da realização profissional.</li>
        </ol>
    </div>
    
    <div style="margin-bottom: {{ $marginBottom }}; page-break-inside: avoid;">
        <h2 style="color: #2E9196; font-size: {{ $subtitleSize }}; font-style: normal; font-weight: 400; line-height: 1.3; margin: 0 0 8pt 0; font-family: 'DejaVu Sans', sans-serif;">Ética, Confidencialidade e Responsabilidade Compartilhada</h2>
        <p style="color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; margin: 0 0 6pt 0; font-family: 'DejaVu Sans', sans-serif;">
            Este relatório é confidencial e não deve ser utilizado como diagnóstico clínico. Os resultados refletem uma avaliação do momento atual e podem variar conforme mudanças no ambiente de trabalho e nas condições pessoais.
        </p>
        <ul style="padding-left: 18pt; color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; margin: 0; list-style: disc; font-family: 'DejaVu Sans', sans-serif;">
            <li style="margin-bottom: 4pt; font-family: 'DejaVu Sans', sans-serif;">Você é responsável por refletir sobre os resultados e buscar apoio quando necessário</li>
            <li style="margin-bottom: 4pt; font-family: 'DejaVu Sans', sans-serif;">A empresa deve assegurar condições saudáveis e equilibradas de trabalho</li>
            <li style="margin-bottom: 4pt; font-family: 'DejaVu Sans', sans-serif;">Este instrumento visa promover prevenção e bem-estar, não substituir avaliações clínicas especializadas</li>
        </ul>
    </div>
    
    <!-- Footer -->
    @php
        $imgPath = function($path) {
            if (isset($isPdf) && $isPdf) {
                $fullPath = public_path($path);
                if (file_exists($fullPath)) {
                    // Usar ruta relativa desde base_path() (chroot)
                    $basePath = str_replace('\\', '/', realpath(base_path()));
                    $fullPathNormalized = str_replace('\\', '/', realpath($fullPath));
                    return str_replace($basePath . '/', '', $fullPathNormalized);
                }
                return '';
            }
            return asset($path);
        };
    @endphp
    <div style="margin-top: {{ $footerMargin }}; padding-top: {{ $isPdfMode ? '12pt' : '20px' }}; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; page-break-inside: avoid; font-family: 'DejaVu Sans', sans-serif;">
        <div style="display: flex; gap: {{ $isPdfMode ? '10pt' : '2vh' }}; align-items: center;">
        <img src="{{ $imgPath('img/felipelli-logo.png') }}" alt="Fellipelli Consultoria" style="height: {{ $isPdfMode ? '20pt' : 'auto' }}; max-height: 20pt;">
        <img src="{{ $imgPath('img/emotive-logo.png') }}" alt="E.MO.TI.VE" style="height: {{ $isPdfMode ? '20pt' : 'auto' }}; max-height: 20pt;">
        </div>
        <div style="text-align: right; display: flex; gap: {{ $isPdfMode ? '10pt' : '2vh' }}; align-items: baseline; font-family: 'DejaVu Sans', sans-serif;">
            <p style="font-size: {{ $isPdfMode ? '6pt' : '0.8rem' }}; color: #999; margin: 0; font-family: 'DejaVu Sans', sans-serif;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: {{ $isPdfMode ? '6pt' : '0.8rem' }}; color: #999; margin: 0; font-family: 'DejaVu Sans', sans-serif;">Pág. 01</p>
        </div>
    </div>
</div>

