@if(count($perfis) > 0)
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="dataTables_length mb-1" id="DataTables_Table_0_length">

            </div>
            <div class="dataTables_filter" id="DataTables_Table_0_filter">
                <span class="d-flex align-items-center mb-0">
                    Pesquisar:
                    <input type="search" id="filtroUsuarios" class="form-control form-control-sm ml-2" placeholder="" aria-controls="DataTables_Table_0">
                </span>
            </div>
        </div>
        <hr class="w-100 my-2 mb-3">
        <div class="row" id="listaUsuarios">
            @foreach($perfis as $perfil)
                <div class="col-md-6 col-lg-4 mb-4 usuario-card" data-nome="{{ strtolower($perfil['name']) }}" data-email="{{ strtolower($perfil['email']) }}">
                    <div class="card h-100 shadow-sm d-flex flex-column">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ asset('storage/' . $perfil['avatar'] ) }}" class="rounded-circle mr-3" style="width: 45px; height: 45px; object-fit: cover;">
                                <div>
                                    <h5 class="mb-0">{{ $perfil['name'] }}</h5>
                                    <small class="text-muted">{{ $perfil['email'] }}</small>
                                </div>
                            </div>
                            <p class="mb-1">
                                @if($perfil['cliente_id'] != 0 && $perfil['cliente'])
                                    <strong>Cliente:</strong> {{ $perfil['cliente']['razao_social'] }}<br>
                                @endif
                            </p>
                            <div class="mb-2">
                                @if($perfil['sa'])
                                    <i class="fa-solid fa-user-secret text-dark mr-2" title="SA"></i>
                                @endif
                                @if($perfil['admin'])
                                    <i class="fa-solid fa-user-tie text-secondary mr-2" title="Administrador"></i>
                                @endif
                                @if($perfil['gestor'])
                                    <i class="fa-brands fa-black-tie text-violet mr-2" title="Gestor"></i>
                                @endif
                                @if($perfil['usuario'])
                                    <i class="fa-solid fa-user text-success mr-2" title="Usuário"></i>
                                @endif
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-end align-items-center bg-light border-top gap-2" style="min-height: 60px;">
                            <a href="{{ route('usuarios.show', $perfil['id']) }}" class="btn btn-tool d-flex align-items-center" title="Ver Detalhes">
                                <i class="fa-solid fa-eye text-success"></i>
                            </a>
                            <a href="{{ route('usuarios.edit', $perfil['id']) }}" class="btn btn-tool d-flex align-items-center" title="Editar">
                                <i class="fa-solid fa-pencil text-info"></i>
                            </a>
                            <form action="{{ route('usuarios.destroy', $perfil['id']) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="destroy_id" id="deleteIdInput">
                                <button type="button" class="btn btn-tool d-flex align-items-center" title="Remover" onclick="confirmDeletar({{ $perfil['id'] }})">
                                    <i class="fa-regular fa-trash-can text-danger"></i>
                                </button>
                            </form>
                            @if($perfil['id'] !== Auth::user()->id)
                                @if($perfil['sa'] !== true)
                                    <form action="{{ route('usuarios.status', $perfil['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status_id" id="statusIdInput">
                                        <button type="button" class="btn btn-tool d-flex align-items-center" title="@if($perfil['ativo']) Inativar @else Ativar @endif" onclick="confirmStatus({{ $perfil['id'] }})">
                                            @if($perfil['ativo'])
                                                <i class="fa-solid fa-toggle-on text-primary"></i>
                                            @else
                                                <i class="fa-solid fa-toggle-off text-muted"></i>
                                            @endif
                                        </button>
                                    </form>
                                    <form action="{{ route('impersonate.start', ['id' => $perfil['id']]) }}" method="POST" class="d-inline" id="startForm_{{ $perfil['id'] }}">
                                        @csrf
                                        <a class="btn btn-tool d-flex align-items-center" href="#" title="Impersonate" onclick="document.getElementById('startForm_{{ $perfil['id'] }}').submit()">
                                            <i class="fa-solid fa-user-gear text-danger"></i>
                                        </a>
                                    </form>
                                @endif    
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        const inputFiltro = document.getElementById('filtroUsuarios');
        inputFiltro.addEventListener('input', function () {
            const filtro = this.value.toLowerCase();
            document.querySelectorAll('.usuario-card').forEach(function (card) {
                const nome = card.dataset.nome;
                const email = card.dataset.email;
                card.style.display = (nome.includes(filtro) || email.includes(filtro)) ? 'block' : 'none';
            });
        });

        inputFiltro.addEventListener('search', function () {
            // Esse evento dispara ao clicar no 'X' do input em navegadores compatíveis
            this.dispatchEvent(new Event('input'));
        });
    </script>

@else
    <div class="row" style="margin: 20px;">
        <div class="callout callout-warning w-100">
            <h5><i class="fa-solid fa-circle-info"></i> Nenhum Perfil foi encontrado.</h5>
            <p>Cadastre seu perfil no botão <strong>"Incluir Perfil"</strong> no canto superior direito</p>
        </div>
    </div>
@endif
        