
    <form action="{{ route('usuarioscli.update', $usuario->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar Informações do Perfil</h3>
        </div>

        <div class="card-body">

            <div class="card card-widget widget-user">
                <div class="widget-user-header text-white" style="background: url('{{ asset('img/panel_user.png') }}') center center;">
                    <h3 class="widget-user-username text-right">{{$usuario->name}}</h3>
                    <h5 class="widget-user-desc text-right">{{$usuario->email}}</h5>
                </div>

                <div class="widget-user-image">
                    <img class="img-circle" src="{{ asset('storage/' . $usuario->avatar) }}" alt="User Avatar">
                </div>

                <div class="card-footer">

                    <div class="row">

                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                {{-- <h5 class="description-header">EMPRESA</h5>
                                <span class="description-text">0</span> --}}
                                <i class="fa-solid fa-trophy"></i>
                            </div>
                        </div>
                    
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                {{-- <h5 class="description-header">PROJETO</h5>
                                <span class="description-text">0</span> --}}
                                {{-- <i class="fa-solid fa-certificate"></i> --}}
                                <i class="fa-solid fa-award"></i>
                            </div>
                        </div>
                    
                        <div class="col-sm-4">
                            <div class="description-block">
                                {{-- <h5 class="description-header">INSTRUMENTOS</h5>
                                <span class="description-text">0</span> --}}
                                <i class="fa-solid fa-ranking-star"></i>
                            </div>
                        </div>
                    
                    </div>
                
                </div>
            </div>

            <input hidden type="text" name="id" class="form-control" value="{{ $usuario->id}}" required>
                                
            <div class="form-group">
                <label for="name">Nome:</label>
                <input type="text" name="name" class="form-control" value="{{ $usuario->name}}" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control" value="{{ $usuario->email }}" readonly required>
            </div>

            <table class="table">
                <tbody>
                    <tr>
                        <th style="width:50%">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="gestor" id="gestorCheckbox" value="1" {{ $usuario->gestor ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="gestorCheckbox">Gestor</label><input type="hidden" name="gestor_hidden" id="gestorHidden" value="{{ $usuario->gestor ? '1' : '0' }}" >
                                </div>
                            </div>                                        
                        </th>
                    </tr>
                    <tr>
                        <th style="width:50%">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="usuario" id="usuarioCheckbox" value="1" {{ $usuario->usuario ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="usuarioCheckbox">Usuário</label><input type="hidden" name="usuario_hidden" id="usuarioHidden" value="{{ $usuario->usuario ? '1' : '0' }}">
                                </div>
                            </div>                                        
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-default" style="width: 150px;">Salvar</button>
            <a href="{{ route('usuarios.cliente') }}" class="btn btn-default" style="width: 150px;">Cancelar</a>
        </div>
    </div>
</form>