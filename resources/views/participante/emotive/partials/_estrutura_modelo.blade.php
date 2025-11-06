@php
    $imgPath = function($path) {
        if (isset($isPdf) && $isPdf) {
            $fullPath = public_path($path);
            return file_exists($fullPath) ? $fullPath : '';
        }
        return asset($path);
    };
@endphp
@if(isset($isPdf) && $isPdf)
<div class="page-break" style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; background: white; box-sizing: border-box;page-break-after: always;page-break-inside: avoid;">
@else
<div style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; background: white; box-sizing: border-box;">
@endif
    <h1 class="section-title" style="color: #A4977F;font-size: 24px;font-style: normal;font-weight: 700;line-height: normal;">Estrutura do Modelo E.MO.TI.VE</h1>
    
    <p style=" color: #2E9196; font-size: 16px; font-style: normal; font-weight: 400; line-height: normal;">
        O instrumento avalia seis dimensões principais que, juntas, formam um <strong>retrato do seu equilíbrio psicossocial</strong>:
    </p>
    
    <div class="estrutura-modelo" style="margin-bottom: 5vh; margin-top: 5vh;">
        <img src="{{ $imgPath('img/estrutura-modelo.png') }}" style="margin: auto;display: flex;" alt="Estrutura do Modelo E.MO.TI.VE">
    </div>
    <!-- Footer -->
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 2vh;">
        <img src="{{ $imgPath('img/felipelli-logo.png') }}" alt="Fellipelli Consultoria">
        <img src="{{ $imgPath('img/emotive-logo.png') }}" alt="Descripción">
        </div>
        <div style="text-align: right;display: flex;gap: 2vh;align-items: baseline;">
            <p style="font-size: 0.8rem; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 02</p>
        </div>
    </div>
    
</div>
