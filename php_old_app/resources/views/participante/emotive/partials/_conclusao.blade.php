@php
    $isPdfMode = isset($isPdf) && $isPdf;
@endphp
@if($isPdfMode)
<div class="page-a4">
    <div class="page-a4-content">
    <h1 class="section-title" style="color: #A4977F;font-size: 18pt;font-style: normal;font-weight: 700;line-height: normal; margin-bottom: 15pt; font-family: 'DejaVu Sans', sans-serif;">CONCLUSÃO GERAL</h1>
@else
<div class="section-pdf" style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;">
    <h1 class="section-title" style="color: #A4977F;font-size: 24px;font-style: normal;font-weight: 700;line-height: normal; margin-bottom: 30px;">CONCLUSÃO GERAL</h1>
@endif
    
    <!-- Autodesenvolvimento -->
    <div style="margin-bottom: 40px;">
        <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin-bottom: 20px;">
            O autodesenvolvimento é um processo contínuo e intencional. O E.MO.TI.VE oferece insights sobre fatores que influenciam seu equilíbrio emocional no trabalho, destacando áreas de força e vulnerabilidade. Ao construir uma rotina mais autoconhecedora, saudável e produtiva, você fortalece seu bem-estar e performance.
        </p>
        
        <div style="background-color: #E8F4F8; border-radius: 8px; padding: 20px; margin: 30px 0;">
            <p style="color: #2E9196;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                "Cuidar de si é um ato de liderança silenciosa: quando você se equilibra, o ambiente ao seu redor também muda."
            </p>
        </div>
    </div>
    
    <!-- Passo Importante -->
    <div style="margin-bottom: 40px;">
        <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin-bottom: 20px;">
            Pausar, observar e compreender suas emoções é um passo importante para o crescimento. O equilíbrio emocional é uma prática contínua que requer atenção, respeito por si mesmo e cuidado ativo. Ao desenvolver essas habilidades, você constrói uma base sólida para melhor performance e bem-estar sustentável.
        </p>
        
        <div style="background-color: #F5F5F5; border-radius: 8px; padding: 20px; margin: 30px 0;">
            <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                "A mudança começa quando nos olhamos com gentileza."
            </p>
        </div>
    </div>
    
    <!-- Consultoria -->
    <div style="margin-top: 50px; padding-top: 30px; border-top: 2px solid #2E9196;">
        <h2 class="section-subtitle" style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 10px;">Fellipelli Consultoria</h2>
        <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; margin: 0;">
            Transformando autoconhecimento em desenvolvimento humano.
        </p>
    </div>
    @if($isPdfMode)
    </div>
    @include('participante.emotive.partials._footer_pdf', ['pageNumber' => '11'])
    @else
    <!-- Footer -->
    <div class="section-pdf-footer" style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 20px; align-items: center;">
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
            <img src="{{ $imgPath('img/felipelli-logo.png') }}" alt="Fellipelli Consultoria" style="height: 30px;">
            <img src="{{ $imgPath('img/emotive-logo.png') }}" alt="E.MO.TI.VE" style="height: 30px;">
        </div>
        <div style="text-align: right;">
            <p style="font-size: 8px; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 8px; color: #999; margin: 5px 0 0 0;">Pág. 11</p>
        </div>
    </div>
    @endif
</div>

