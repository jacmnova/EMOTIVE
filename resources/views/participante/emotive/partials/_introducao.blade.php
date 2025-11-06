@if(isset($isPdf) && $isPdf)
<div class="page-break" style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;page-break-after: always;page-break-inside: avoid;">
@else
<div style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;">
@endif
    <h1 class="section-title" style="color: #A4977F;font-size: 24px;font-style: normal;font-weight: 700;line-height: normal;">RELATÓRIO E.MO.TI.VE®</h1>
    
    <div style="margin-bottom: 30px;">
        <h2 class="section-subtitle" style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal;">Ferramenta de Autoconhecimento e Prevenção de Riscos Psicossociais</h2>
        <p style="color: #000; font-size: 10px; font-style: normal; font-weight: 400; line-height: normal;">
            O E.MO.TI.VE® é uma ferramenta de avaliação desenvolvida para identificar sinais de desequilíbrio emocional e ocupacional no ambiente de trabalho. Baseado em evidências científicas e normas regulatórias, este instrumento oferece uma análise personalizada do seu estado emocional e psicossocial.
        </p>
        <p style="color: #000; font-size: 10px; font-style: normal; font-weight: 400; line-height: normal;">
            <strong>Mais do que medir</strong> — este relatório busca promover reflexão e autoconhecimento sobre fatores que influenciam seu bem-estar profissional.
        </p>
        <p style="color: #000; font-size: 10px; font-style: normal; font-weight: 400; line-height: normal;">
            <strong>Mais do que um diagnóstico</strong> — oferece orientações práticas para fortalecer sua resiliência emocional e melhorar sua relação com o trabalho.
        </p>
        
        <div class="quote-box" style="border-radius: 10px;background: rgba(46, 145, 150, 0.20);color: #2E9196;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal;padding: 8px;text-align: center;margin: auto;display: flex;align-items: center;justify-content: center;">
            <p style="color: #2E9196; font-size: 10px; font-style: normal; font-weight: 400; line-height: normal; margin: 0;">
                "Saúde emocional não é ausência de estresse, mas a capacidade de reconhecê-lo e se fortalecer diante dele."
            </p>
        </div>
    </div>
    
    <div style="margin-bottom: 30px;">
        <h2 class="section-subtitle" style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal;">Finalidade do Instrumento</h2>
        <p style="color: #000; font-size: 10px; font-style: normal; font-weight: 400; line-height: normal;">
            Este relatório identifica dimensões relacionadas ao bem-estar emocional e ocupacional, permitindo que você:
        </p>
        <ul style="padding-left: 25px;color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal;">
            <li>Reconheça áreas de equilíbrio e vulnerabilidade emocional</li>
            <li>Identifique sinais precoces de desgaste ou sobrecarga</li>
            <li>Compreenda como fatores organizacionais e pessoais se interrelacionam</li>
            <li>Receba orientações práticas para promover autocuidado e desenvolvimento</li>
        </ul>
    </div>
    
    <div style="margin-bottom: 30px;">
        <h2 class="section-subtitle" style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal;">Base Normativa e Científica</h2>
        <ol style="padding-left: 25px;color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal;">
            <li><strong>NR-1 (Portaria 6.730/2020):</strong> Estabelece diretrizes para avaliação de riscos psicossociais no trabalho, exigindo que organizações identifiquem e previnam fatores que possam comprometer a saúde mental dos trabalhadores.</li>
            <li><strong>Modelo de Maslach (Burnout):</strong> Baseado em décadas de pesquisa científica, o modelo identifica três dimensões principais: exaustão emocional, despersonalização e redução da realização profissional.</li>
        </ol>
    </div>
    
    <div style="margin-bottom: 30px;">
        <h2 class="section-subtitle" style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal;">Ética, Confidencialidade e Responsabilidade Compartilhada</h2>
        <p style="color: #000; font-size: 10px; font-style: normal; font-weight: 400; line-height: normal;">
            Este relatório é confidencial e não deve ser utilizado como diagnóstico clínico. Os resultados refletem uma avaliação do momento atual e podem variar conforme mudanças no ambiente de trabalho e nas condições pessoais.
        </p>
        <ul style="padding-left: 25px;color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal;">
            <li>Você é responsável por refletir sobre os resultados e buscar apoio quando necessário</li>
            <li>A empresa deve assegurar condições saudáveis e equilibradas de trabalho</li>
            <li>Este instrumento visa promover prevenção e bem-estar, não substituir avaliações clínicas especializadas</li>
        </ul>
    </div>
    
    <!-- Footer -->
    @php
        $imgPath = function($path) {
            if (isset($isPdf) && $isPdf) {
                $fullPath = public_path($path);
                return file_exists($fullPath) ? $fullPath : '';
            }
            return asset($path);
        };
    @endphp
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 2vh;">
        <img src="{{ $imgPath('img/felipelli-logo.png') }}" alt="Fellipelli Consultoria">
        <img src="{{ $imgPath('img/emotive-logo.png') }}" alt="Descripción">
        </div>
        <div style="text-align: right;display: flex;gap: 2vh;align-items: baseline;">
            <p style="font-size: 0.8rem; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 01</p>
        </div>
    </div>
</div>

