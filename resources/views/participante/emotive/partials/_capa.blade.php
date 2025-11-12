
@php
    $imgPath = function($path) {
        if (isset($isPdf) && $isPdf) {
            // Para PDF, usar ruta relativa desde base_path() (chroot)
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                // DomPDF con chroot necesita rutas relativas desde base_path()
                $basePath = str_replace('\\', '/', realpath(base_path()));
                $fullPathNormalized = str_replace('\\', '/', realpath($fullPath));
                $relativePath = str_replace($basePath . '/', '', $fullPathNormalized);
                return $relativePath;
            }
            return '';
        }
        return asset($path);
    };
@endphp
@php
    $isPdfMode = isset($isPdf) && $isPdf;
@endphp
@if($isPdfMode)
{{-- Versión optimizada para PDF - A4 --}}
<div class="section-container" style="background: #333; position: relative; display: flex; flex-direction: column; justify-content: space-between; width: 100%; max-width: 595.28pt; margin: 0 auto; box-sizing: border-box; padding: 30pt 15pt; font-family: 'DejaVu Sans', sans-serif; min-height: 842pt; page-break-after: always; page-break-inside: avoid;">
@else
<div class="container container-portada" style="background: #333;background-size: cover;display: flex;flex-direction: column;gap: 30vh;background-image: url('{{ asset('img/Emotive-bg.png') }}');background-position: bottom;background-repeat: no-repeat;width: 100%;max-width: 595.28pt;margin: 0 auto;min-height: 100vh;box-sizing: border-box;">
@endif
    @if($isPdfMode)
        {{-- Para PDF: solo mostrar imagen de fondo si existe y es válida --}}
        @php
            $bgFullPath = public_path('img/Emotive-bg.png');
            $bgExists = file_exists($bgFullPath) && filesize($bgFullPath) > 0;
            if ($bgExists) {
                // Usar ruta relativa desde base_path() (chroot)
                $basePath = str_replace('\\', '/', realpath(base_path()));
                $bgFullPathNormalized = str_replace('\\', '/', realpath($bgFullPath));
                $bgPath = str_replace($basePath . '/', '', $bgFullPathNormalized);
            } else {
                $bgPath = '';
            }

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
        @if($bgExists && !empty($bgPath))
            <img src="{{ $bgPath }}" alt="" style="position: absolute; bottom: 0; left: 0; width: 100%; max-width: 595.28pt; height: auto; max-height: 150pt; object-fit: contain; object-position: bottom; opacity: 0.15; z-index: 0; pointer-events: none; page-break-inside: avoid;">
        @endif
    @endif


    <!-- LOGO FELIPELLI -->
    <img src="{{ $imgPath('img/emotive-header.png') }}" alt="Estrutura do Modelo E.MO.TI.VE" style="width: 258px; margin: auto;">
    
     


    <!-- LOGO EMOTIVE -->
    <img src="{{ $imgPath('img/emotive-central.png') }}" alt="Estrutura do Modelo E.MO.TI.VE" style="width: 258px; margin: auto;">
    

    <div style="position: relative; z-index: 2; margin-top: {{ $isPdfMode ? 'auto' : '40px' }}; margin-bottom: {{ $isPdfMode ? '30pt' : '0' }}; font-family: 'DejaVu Sans', sans-serif;">
        <div style="text-align: center; background: white; padding: {{ $isPdfMode ? '15pt' : '25px' }}; color: #333; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative; z-index: 1; width: {{ $isPdfMode ? '75%' : '50%' }}; margin: auto; border-top-left-radius: 12px; border-top-right-radius: 12px; font-family: 'DejaVu Sans', sans-serif;">
            <h3 style="font-size: {{ $isPdfMode ? '14pt' : '1.3rem' }}; font-weight: bold; margin: 0 0 6pt 0; color: #A59880; text-align: center; font-family: 'DejaVu Sans', sans-serif;">{{ $user->name }}</h3>
            <p style="font-size: {{ $isPdfMode ? '10pt' : '0.95rem' }}; margin: 3pt 0; color: #666; font-family: 'DejaVu Sans', sans-serif;">Relatório Questionário de Riscos Psicossociais</p>
            <p style="font-size: {{ $isPdfMode ? '9pt' : '0.9rem' }}; margin: 3pt 0; color: #888; font-family: 'DejaVu Sans', sans-serif;">Respondido em {{ \Carbon\Carbon::parse($respostasUsuario->first()->created_at ?? now())->format('d/m/Y') }}</p>
        </div>
    </div>
</div>

