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
    $isPdfMode = isset($isPdf) && $isPdf;
    $padding = $isPdfMode ? '18pt' : '40px';
    $titleSize = $isPdfMode ? '18pt' : '24px';
    $subtitleSize = $isPdfMode ? '12pt' : '16px';
    $footerMargin = $isPdfMode ? '20pt' : '50px';
    $imgMargin = $isPdfMode ? '15pt' : '5vh';
@endphp
<div class="section-pdf" style="padding: {{ $padding }}; max-width: 595.28pt; width: 100%; margin: 0 auto; background: white; box-sizing: border-box;">
    <h1 style="color: #A4977F; font-size: {{ $titleSize }}; font-style: normal; font-weight: 700; line-height: 1.2; margin: 0 0 12pt 0; font-family: 'DejaVu Sans', sans-serif;">Estrutura do Modelo E.MO.TI.VE</h1>
    
    <p style="color: #2E9196; font-size: {{ $subtitleSize }}; font-style: normal; font-weight: 400; line-height: 1.3; margin: 0 0 50px 0; font-family: 'DejaVu Sans', sans-serif;">
        O instrumento avalia seis dimensões principais que, juntas, formam um <strong>retrato do seu equilíbrio psicossocial</strong>:
    </p>
    
    <div style="text-align: center; page-break-inside: avoid;">
        <img src="{{ $imgPath('img/estrutura-modelo.png') }}" style="max-width: 100%; height: 553px; margin: 0 auto; display: block;" alt="Estrutura do Modelo E.MO.TI.VE">
    </div>
    <!-- Footer -->
    <div style="margin-top: {{ $footerMargin }}; padding-top: {{ $isPdfMode ? '12pt' : '20px' }}; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; page-break-inside: avoid; font-family: 'DejaVu Sans', sans-serif;">
        <div style="display: flex; gap: {{ $isPdfMode ? '10pt' : '2vh' }}; align-items: center;">
        <img src="{{ $imgPath('img/felipelli-logo.png') }}" alt="Fellipelli Consultoria" style="height: {{ $isPdfMode ? '20pt' : 'auto' }}; max-height: 20pt;">
        <img src="{{ $imgPath('img/emotive-logo.png') }}" alt="E.MO.TI.VE" style="height: {{ $isPdfMode ? '20pt' : 'auto' }}; max-height: 20pt;">
        </div>
        <div style="text-align: right; display: flex; gap: {{ $isPdfMode ? '10pt' : '2vh' }}; align-items: baseline; font-family: 'DejaVu Sans', sans-serif;">
            <p style="font-size: {{ $isPdfMode ? '6pt' : '0.8rem' }}; color: #999; margin: 0; font-family: 'DejaVu Sans', sans-serif;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: {{ $isPdfMode ? '6pt' : '0.8rem' }}; color: #999; margin: 0; font-family: 'DejaVu Sans', sans-serif;">Pág. 02</p>
        </div>
    </div>
    
</div>
