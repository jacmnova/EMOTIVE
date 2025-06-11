@if(count($perfis) > 0)
    <div class="card-body">

        {{-- TABELA (para telas médias e grandes) --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th style="width: 20%">Nome</th>
                        <th style="width: 25%">Email</th>
                        <th style="width: 15%">Perfil</th>
                        <th style="width: 25%">Cliente</th>
                        <th style="width: 15%"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($perfis as $perfil)
                        <tr>
                            <td>
                                <img src="{{ asset('storage/' . $perfil['avatar']) }}" class="rounded-circle mr-2" style="width: 35px; height: 35px; object-fit: cover;">
                                {{ $perfil['name'] }}
                            </td>
                            <td>{{ $perfil['email'] }}</td>
                            <td>
                                @if($perfil['sa']) <i class="fa-solid fa-user-secret" style="margin-right: 8px; color: black; font-size: 22px;" title="SA"></i> @endif
                                @if($perfil['admin']) <i class="fa-solid fa-user-tie" style="margin-right: 8px; color: gray; font-size: 22px;" title="Administrador"></i> @endif
                                @if($perfil['gestor']) <i class="fa-brands fa-black-tie" style="margin-right: 8px; color: violet; font-size: 22px;" title="Gestor"></i> @endif
                                @if($perfil['usuario']) <i class="fa-solid fa-user" style="margin-right: 8px; color: green; font-size: 22px;" title="Usuário"></i> @endif
                            </td>
                            <td>
                                @if($perfil['cliente_id'] && $perfil['cliente'])
                                    {{ $perfil['cliente']['razao_social'] }}
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('usuarios.show', $perfil['id']) }}" class="btn btn-sm btn-tool d-sm-inline-block" title="Ver Detalhes">
                                    <i class="fa-solid fa-eye" style="color: green;"></i>
                                </a>
                                <a href="{{ route('usuarios.edit', $perfil['id']) }}" class="btn btn-sm btn-tool d-sm-inline-block" title="Editar">
                                    <i class="fa-solid fa-pencil" style="color: #008ca5;"></i>
                                </a>
                                <form action="{{ route('usuarios.destroy', $perfil['id']) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-tool d-sm-inline-block" onclick="confirmDeletar({{ $perfil['id'] }})" title="Remover">
                                        <i class="fa-regular fa-trash-can" style="color: darkred;"></i>
                                    </button>
                                </form>
                                @if($perfil['id'] !== Auth::user()->id)
                                    @if($perfil['sa'] !== true)
                                        <form action="{{ route('usuarios.status', $perfil['id']) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="button" class="btn btn-sm btn-tool d-sm-inline-block" onclick="confirmStatus({{ $perfil['id'] }})" title="@if($perfil['ativo']) Inativar @else Ativar @endif">
                                                @if($perfil['ativo'])
                                                    <i class="fa-solid fa-toggle-on" style="color: #233750;"></i>
                                                @else
                                                    <i class="fa-solid fa-toggle-off" style="color: #5fc3b4;"></i>
                                                @endif
                                            </button>
                                        </form>
                                    
                                        <form action="{{ route('impersonate.start', ['id' => $perfil['id']]) }}" method="POST" class="d-inline" id="startForm_{{ $perfil['id'] }}">
                                            @csrf
                                            <a class="btn btn-sm btn-tool d-sm-inline-block" href="#" onclick="document.getElementById('startForm_{{ $perfil['id'] }}').submit()" title="Impersonar">
                                                <i class="fa-solid fa-user-gear" style="color: red;"></i>
                                            </a>
                                        </form>
                                    @endif

                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- BLOCOS (para telas pequenas) --}}
        <div class="d-block d-md-none">
            @foreach($perfis as $perfil)
                <div class="d-flex border rounded p-3 mb-3 shadow-sm align-items-start">
                    <div class="mr-3">
                        <img src="{{ asset('storage/' . $perfil['avatar']) }}" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                    </div>
                    <div class="flex-grow-1">
                        <div class="mb-1"><strong>Nome:</strong> {{ $perfil['name'] }}</div>
                        <div class="mb-1"><strong>Email:</strong> {{ $perfil['email'] }}</div>
                        <div class="mb-1">
                            <strong>Perfil:</strong>
                            @if($perfil['sa']) <i class="fa-solid fa-user-secret" style="margin-right: 8px; color: black; font-size: 22px;" title="SA"></i> @endif
                            @if($perfil['admin']) <i class="fa-solid fa-user-tie" style="margin-right: 8px; color: gray; font-size: 22px;" title="Administrador"></i> @endif
                            @if($perfil['gestor']) <i class="fa-brands fa-black-tie" style="margin-right: 8px; color: violet; font-size: 22px;" title="Gestor"></i> @endif
                            @if($perfil['usuario']) <i class="fa-solid fa-user" style="margin-right: 8px; color: green; font-size: 22px;" title="Usuário"></i> @endif
                        </div>
                        @if($perfil['cliente_id'] && $perfil['cliente'])
                            <div class="mb-1"><strong>Cliente:</strong> {{ $perfil['cliente']['razao_social'] }}</div>
                        @endif

                        <div class="mt-2 d-flex flex-wrap gap-2">
                            
                            <a href="{{ route('usuarios.show', $perfil['id']) }}" class="btn btn-sm btn-tool d-flex align-items-center" title="Ver Detalhes">
                                <i class="fa-solid fa-eye" style="color: green;"></i>
                            </a>

                            <a href="{{ route('usuarios.edit', $perfil['id']) }}" class="btn btn-sm btn-tool d-flex align-items-center" title="Editar">
                                <i class="fa-solid fa-pencil" style="color: #008ca5;"></i>
                            </a>

                            <form action="{{ route('usuarios.destroy', $perfil['id']) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-tool d-sm-inline-block" onclick="confirmDeletar({{ $perfil['id'] }})" title="Remover">
                                    <i class="fa-regular fa-trash-can" style="color: darkred;"></i>
                                </button>
                            </form>
                            @if($perfil['id'] !== Auth::user()->id)
                                <form action="{{ route('usuarios.status', $perfil['id']) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="button" class="btn btn-sm btn-tool d-sm-inline-block" onclick="confirmStatus({{ $perfil['id'] }})" title="@if($perfil['ativo']) Inativar @else Ativar @endif">
                                        @if($perfil['ativo'])
                                            <i class="fa-solid fa-toggle-on" style="color: #233750;"></i>
                                        @else
                                            <i class="fa-solid fa-toggle-off" style="color: #5fc3b4;"></i>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('impersonate.start', ['id' => $perfil['id']]) }}" method="POST" class="d-inline" id="startForm_mobile_{{ $perfil['id'] }}">
                                    @csrf
                                    <a class="btn btn-sm btn-tool d-sm-inline-block" href="#" onclick="document.getElementById('startForm_mobile_{{ $perfil['id'] }}').submit()" title="Impersonar">
                                        <i class="fa-solid fa-user-gear" style="color: red;"></i>
                                    </a>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@else
    <div class="row" style="margin: 20px;">
        <div class="callout callout-warning w-100">
            <h5><i class="fa-solid fa-circle-info"></i> Nenhum Perfil foi encontrado.</h5>
            <p>Cadastre seu perfil no botão <strong>"Incluir Perfil"</strong> no canto superior direito</p>
        </div>
    </div>
@endif
