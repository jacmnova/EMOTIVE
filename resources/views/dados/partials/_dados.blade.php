

<!-- Cropper.js CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<!-- Cropper.js JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>



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
                <form id="formCropUpload" method="POST" action="{{ route('upload.image.usuario') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ auth()->user()->id }}">
                    <input type="hidden" name="imagem_base64" id="imagem_base64">

                    <div class="form-group">
                        <label for="imagemInput">Nova Imagem de Perfil:</label>
                        <input type="file" id="imagemInput" class="form-control" accept="image/*">
                    </div>

                    <div class="mt-3 mb-3 text-center">
                        <img id="imagemPreview" style="max-width: 300px; display:none;" class="img-fluid rounded">
                    </div>

                    <button type="button" id="recortarBtn" class="btn btn-warning" style="display:none;">Recortar e Enviar</button>
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


<script>
let cropper;

document.getElementById('imagemInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (event) {
        const img = document.getElementById('imagemPreview');
        img.src = event.target.result;
        img.style.display = 'block';

        if (cropper) {
            cropper.destroy();
        }

        cropper = new Cropper(img, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
        });

        document.getElementById('recortarBtn').style.display = 'inline-block';
    };

    reader.readAsDataURL(file);
});

document.getElementById('recortarBtn').addEventListener('click', function () {
    if (!cropper) return;

    const canvas = cropper.getCroppedCanvas({ width: 300, height: 300 });
    canvas.toBlob(function (blob) {
        const reader = new FileReader();
        reader.onloadend = function () {
            document.getElementById('imagem_base64').value = reader.result;
            document.getElementById('formCropUpload').submit();
        };
        reader.readAsDataURL(blob);
    }, 'image/jpeg');
});
</script>
