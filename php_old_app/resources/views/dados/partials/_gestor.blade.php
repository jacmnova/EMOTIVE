
@if(isset($cliente->razao_social))
<div class="card card-secondary card-outline widget-user-2">

    <div class="widget-user-header d-flex justify-content-center">
        <div class="widget-user-image">
            <img src="{{ Storage::url($cliente->logo_url) }}" alt="Logo Cliente" style="width: 400px; height: 400px; object-fit: cover; border-radius: 8px;">
        </div>
    </div>

    <h3 class="profile-username text-center">GESTOR DE CONTA</h3>

    <h3 class="profile-username text-center">{{ $cliente->razao_social }}</h3>

    @if($cliente->tipo === 'cpf')
        <p class="text-muted text-center">{{ $cliente->formatted_cpf }} </p>
    @elseif(Auth::user()->cliente->tipo === 'cnpj')
        <p class="text-muted text-center">{{ $cliente->formatted_cnpj }}</p>
    @else
        <p class="text-muted text-center">{{ $cliente->cpf_cnpj }}</p>
    @endif

    <div class="card-footer">

    </div>
</div>
@endif

