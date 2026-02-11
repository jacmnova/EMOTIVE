@php
    $isPdfMode = isset($isPdf) && $isPdf;
@endphp
@if($isPdfMode)
<div class="page-a4">
    <div class="page-a4-content">
    <h1 class="section-title" style="color: #A4977F;font-size: 18pt;font-style: normal;font-weight: 700;line-height: normal; margin-bottom: 15pt; font-family: 'DejaVu Sans', sans-serif;">PLANO DE DESENVOLVIMENTO PESSOAL</h1>
@else
<div class="section-pdf" style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;">
    <h1 class="section-title" style="color: #A4977F;font-size: 24px;font-style: normal;font-weight: 700;line-height: normal; margin-bottom: 30px;">PLANO DE DESENVOLVIMENTO PESSOAL</h1>
@endif
    
    <!-- Zona de Risco -->
    <div style="margin-bottom: 40px;">
        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <div style="width: 12px; height: 12px; background: {{ $nivelRisco['cor_hex'] }}; border-radius: 50%; margin-right: 10px;"></div>
            <h2 class="section-subtitle" style="color: {{ $nivelRisco['cor_hex'] }};font-size: 16px;font-style: normal;font-weight: 400;line-height: normal; margin: 0;">{{ $nivelRisco['zona'] }}</h2>
        </div>
        
        <div style="margin-bottom: 30px;">
            <h3 style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 15px;">Objetivo:</h3>
            <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                {{ $planDesenvolvimento['objetivo'] }}
            </p>
        </div>
        
        <div style="margin-bottom: 30px;">
            <h3 style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 15px;">Ações sugeridas:</h3>
            <ol style="line-height: 1.8; padding-left: 25px; margin: 0; list-style: decimal;">
                @foreach($planDesenvolvimento['acoes'] as $acao)
                    <li style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 8px; text-align: justify;">{{ $acao }}</li>
                @endforeach
            </ol>
        </div>
        
        <div>
            <h3 style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 15px;">Indicador de progresso:</h3>
            <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                {{ $planDesenvolvimento['indicador'] }}
            </p>
        </div>
    </div>
    
    <!-- Citação -->
    <div style="background-color: #E8F4F8; border-radius: 8px; padding: 20px; margin: 40px 0;">
        <p style="color: #2E9196;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
            "O equilíbrio não é ausência de desafios, mas a capacidade de se manter inteiro diante deles."
        </p>
    </div>
    @if($isPdfMode)
    </div>
    @include('participante.emotive.partials._footer_pdf', ['pageNumber' => '10'])
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
            <p style="font-size: 8px; color: #999; margin: 5px 0 0 0;">Pág. 10</p>
        </div>
    </div>
    @endif
</div>

