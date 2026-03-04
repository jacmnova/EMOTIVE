@extends('adminlte::page')

@section('title', 'Visualizar Usuário')

@section('content_header')
    @if(Session::has('msgSuccess'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa-regular fa-bell" style="margin-right: 5px"></i> {!! Session::get('msgSuccess') !!}
        </div>
    @elseif(Session::has('msgError'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa-solid fa-triangle-exclamation"></i> {!! Session::get('msgError') !!}
        </div>
    @endif

    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Início</a></li>
                    <li class="breadcrumb-item"><a href="#">Usuários</a></li>
                    <li class="breadcrumb-item active">Visualizar</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card card-widget widget-user shadow">

                <div class="widget-user-header bg-info">
                    <h3 class="widget-user-username">{{$usuario->name}}</h3>
                    <h5 class="widget-user-desc">{{$usuario->email}}</h5>
                </div>

                <div class="widget-user-image">
                    <img class="img-circle" src="{{ asset('storage/' . $usuario->avatar) }}" alt="User Avatar">
                </div>

                <div class="card-footer">
                    <div class="row">

                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <h5 class="description-header">{{ $quantidadeFormularios }}</h5>
                                <span class="description-text">Liberados</span>
                            </div>
                        </div>

                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <h5 class="description-header">{{ $quantidadePendente }}</h5>
                                <span class="description-text">Pendentes</span>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="description-block">
                                <h5 class="description-header">{{ $quantidadeFinalizado }}</h5>
                                <span class="description-text">Finalizados</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        @if($cliente)
            <div class="col-md-6">
                <div class="card card-widget widget-user shadow">
                    <div class="widget-user-header bg-dark">
                        <h3 class="widget-user-username">{{ strtoupper($cliente->razao_social) }}</h3>
                        <h5 class="widget-user-desc">{{ $cliente->email }}</h5>
                        @if($cliente->tipo === 'cpf')
                            <p><i class="fa-solid fa-person" style="color: rgb(206, 206, 206); margin-right: 7px;" title="CPF"></i> {{ $cliente->formatted_cpf }} </p>
                        @elseif($cliente->tipo === 'cnpj')
                            <p><i class="fa-solid fa-building" style="color: rgb(206, 206, 206); margin-right: 7px;" title="CNPJ"></i> {{ $cliente->formatted_cnpj }} </p>
                        @else
                            <p><i class="fa-solid fa-globe" style="color: #008ca5; margin-right: 7px;" title="Internacional"></i> {{ $cliente->cpf_cnpj }} </p>
                        @endif
                    </div>
                    <div class="card-footer">
                    </div>
                </div>
            </div>
        @endif

    </div>

    @include('usuarios.partials._formularios')

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function gerarPDF(userId, formularioId) {
        // 1. Mostrar loading y deshabilitar botón
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        // 2. Generar la URL del informe que será convertida (usar el dominio actual para asegurar URL pública)
        const baseUrl = window.location.origin;
        const informeUrl = baseUrl + '/meurelatorio/pdf?formulario_id=' + formularioId + '&usuario_id=' + userId;

        // 3. Configuración de la petición POST al servicio de conversión de Railway
        fetch('https://api-convet-pdf-g3nia.up.railway.app/convert-url-paginated', {
            method: 'POST',
            mode: 'cors', // Permitir CORS explícitamente
            credentials: 'omit', // No enviar cookies
            headers: {
                'Accept': 'application/pdf', // Indicamos que esperamos un PDF
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                "url": informeUrl
            })
        })
        .then(response => {
            if (response.ok) {
                // El servicio respondió exitosamente.
                // Verificamos si realmente recibimos un PDF.
                const contentType = response.headers.get('Content-Type');

                if (contentType && contentType.includes('application/json')) {
                    // Si el servicio responde OK pero devuelve JSON (ej. un link de descarga), 
                    // maneja esa lógica aquí.
                    return response.json().then(data => {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Conversión OK, pero no es un PDF',
                            text: 'El servicio externo devolvió JSON. Si esperabas el archivo, revisa el endpoint.',
                            confirmButtonText: 'OK'
                        });
                        throw new Error('Respuesta JSON en lugar de PDF');
                    });
                }

                // 4. Si la respuesta es un archivo, la procesamos como BLOB.
                return response.blob(); 
            } else {
                // Manejar errores de respuesta HTTP (4xx, 5xx)
                return response.text().then(text => {
                    let errorMsg = `Error ${response.status}: Error en el servicio de conversión.`;
                    try {
                        const jsonError = JSON.parse(text);
                        errorMsg = jsonError.detail || jsonError.message || errorMsg;
                    } catch (e) {
                        // Si no es JSON, usar el texto tal cual
                        if (text) errorMsg += ' ' + text.substring(0, 200);
                    }
                    throw new Error(errorMsg);
                });
            }
        })
        .then(pdfBlob => {
            // 5. Crear el enlace de descarga y simular el clic.
            if (pdfBlob) {
                // Creamos una URL temporal para el Blob
                const url = window.URL.createObjectURL(pdfBlob);
                
                // Creamos un elemento <a> oculto para forzar la descarga
                const a = document.createElement('a');
                a.href = url;
                a.download = `Relatorio_${userId}_${formularioId}.pdf`; // Nombre del archivo
                document.body.appendChild(a);
                
                // Simulamos el clic
                a.click();
                
                // Limpiamos la URL temporal y el elemento <a>
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                Swal.fire({
                    icon: 'success',
                    title: 'PDF Descargado',
                    text: 'El informe se ha descargado correctamente.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        })
        .catch(error => {
            // 6. Manejo de errores de red o del throw anterior
            let errorMessage = error.message || 'Verifica la conexión o el servicio.';
            
            // Detectar errores de CORS específicamente
            if (error.message.includes('CORS') || error.message.includes('Failed to fetch') || error.name === 'TypeError') {
                errorMessage = 'Error de CORS: El servidor de Railway no permite peticiones desde este dominio. Contacta al administrador para configurar CORS en el servidor.';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error de Descarga',
                text: `No fue posible descargar el PDF: ${errorMessage}`,
                confirmButtonText: 'OK'
            });
            console.error('Error al generar y descargar PDF:', error);
        })
        .finally(() => {
            // Restaurar el botón siempre
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        });
    }
</script>
@stop