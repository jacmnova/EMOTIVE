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
@endphp
<div class="section-pdf-footer page-a4-footer">
    <div style="display: flex; gap: {{ $isPdfMode ? '10pt' : '20px' }}; align-items: center;">
        <img src="{{ $imgPath('img/felipelli-logo.png') }}" alt="Fellipelli Consultoria" style="height: {{ $isPdfMode ? '20pt' : '30px' }}; max-height: 20pt;">
        <img src="{{ $imgPath('img/emotive-logo.png') }}" alt="E.MO.TI.VE" style="height: {{ $isPdfMode ? '20pt' : '30px' }}; max-height: 20pt;">
    </div>
    <div style="text-align: right; font-family: 'DejaVu Sans', sans-serif;">
        <p style="font-size: {{ $isPdfMode ? '6pt' : '8px' }}; color: #999; margin: 0; font-family: 'DejaVu Sans', sans-serif;">Todos os direitos reservados a Fellipelli Consultoria</p>
        <p style="font-size: {{ $isPdfMode ? '6pt' : '8px' }}; color: #999; margin: 2pt 0 0 0; font-family: 'DejaVu Sans', sans-serif;">Pág. {{ $pageNumber ?? '00' }}</p>
    </div>
</div>

