<div class="card card-secondary card-outline">
    <div class="card-body box-profile">

        <div class="text-center">
            <img class="profile-user-img img-fluid img-circle" src="{{ Auth::user()->adminlte_image() }}" alt="User profile picture">
        </div>

        <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>
        <p class="text-muted text-center">{{ Auth::user()->email }}</p>

        <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
                <b>Data de Cadastro</b> <a class="float-right">{{ Auth::user()->created_at->format('d/m/Y H:m') }}</a>
            </li>
        </ul>

        <div class="card collapsed-card p-0 mt-3 mb-3 bg-light">
            <div class="card-header">
                <h3 class="card-title"> <i class="fa-solid fa-camera-retro" style="margin-right: 8px; color:silver;"></i> Alterar imagem do avatar</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('upload.image.usuario') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ auth()->user()->id }}"> <!-- Adicione esta linha -->
                    <div class="form-group">
                        <label for="avatar">Nova Imagem de Perfil:</label>
                        <div class="input-group mb-3">
                            <input type="file" name="image" id="image" class="form-control rounded-0" required>
                            <span class="input-group-append">
                                <button type="submit" class="btn btn-info btn-flat">Enviar!</button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <div class="card-footer">
    
    </div>
</div>

<div class="card card-secondary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-lock" style="margin-right: 8px;"></i> Alteração de Senha</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('senha.update', Auth::user()->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="input-group mb-3">
                <input type="password" name="current_password" class="form-control" placeholder="Senha Atual" required>
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                </div>
            </div>

            <div class="input-group mb-3">
                <input type="password" name="new_password" class="form-control" placeholder="Nova Senha" required>
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-check"></i></span>
                </div>
            </div>

            <div class="input-group mb-3">
                <input type="password" name="new_password_confirmation" class="form-control" placeholder="Confirmar Nova Senha" required>
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-check"></i></span>
                </div>
            </div>

            <button type="submit" class="btn btn-dark">Alterar Senha</button>
        </form>
    </div>

    <div class="card-footer">
    </div>
</div>
