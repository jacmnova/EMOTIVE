<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DadosController;
use App\Http\Controllers\MidiaController;
use App\Http\Controllers\GestorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CalculosController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\VariavelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerguntasController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\FormulariosController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\QuestionariosController;
use App\Http\Controllers\ImportarUsuariosController;
use App\Http\Controllers\FormularioEtapaController;
use App\Http\Controllers\UsuarioFormularioController;

use App\Http\Controllers\PasswordController;

use App\Http\Controllers\AnaliseController;

use App\Models\User;

use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('welcome');
});


Route::view('/termos', 'termos')->name('termos');
Route::view('/tutorial', 'tutorial')->name('tutorial');


Auth::routes();


Route::get('/template', function () {
    return view('template.index');
})->name('template.index');

Route::get('/faqs', function () {
    return view('faqs.index');
})->name('faqs.index');



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');


// GESTAO
Route::get('/gestao-usuarios', [GestorController::class, 'usuariosCliente'])->name('usuarios.cliente');
Route::get('/editar-usuario/{id}', [GestorController::class, 'usuariosEditar'])->name('usuarios.editar');
Route::get('/lista-formularios', [GestorController::class, 'listaFormularios'])->name('formularios.cliente');
Route::get('/gestor/create-cliente', [GestorController::class, 'create_cliente'])->name('gestor.create.cliente');
Route::post('/gestor/store-cliente', [GestorController::class, 'store'])->name('gestor.cliente.store');


Route::put('/meusuario/{id}', [DadosController::class, 'updateUsuario'])->name('usuarioscli.update');




Route::get('/dados', [DadosController::class, 'index'])->name('dados.index');
Route::post('/changepass/{id}', [DadosController::class, 'updatePassword'])->name('senha.update');
Route::post('/upload/imagem/usuario', [ImageUploadController::class, 'uploadimagemusuario'])->name('upload.image.usuario');
Route::post('/upload/imagem/cliente', [ImageUploadController::class, 'uploadimagemcliente'])->name('upload.image.cliente');



Route::put('/formularios/status/{id}', [FormulariosController::class, 'status'])->name('formularios.status');
Route::resource('formularios', FormulariosController::class);
Route::resource('variaveis', VariavelController::class)->parameters([
    'variaveis' => 'variavel'
]);

Route::resource('perguntas', PerguntasController::class);
Route::resource('questionarios', QuestionariosController::class);



Route::put('/usuarios/status/{id}', [UsuariosController::class, 'status'])->name('usuarios.status');
Route::post('/impersonate/start/{id}', [ImpersonateController::class, 'start'])->name('impersonate.start');

Route::resource('usuarios', UsuariosController::class);


Route::put('/clientes/status/{id}', [ClienteController::class, 'status'])->name('clientes.status');
Route::resource('clientes', ClienteController::class);

Route::post('/questionarios/incluir', [ClienteController::class, 'incluir'])->name('questionarios.incluir');


Route::post('/usuario_formulario_admin', [UsuarioFormularioController::class, 'storeAdmin'])->name('usuario_formulario_admin.store');

Route::resource('usuario_formulario', UsuarioFormularioController::class);



Route::get('/meus-questionarios', [DadosController::class, 'questionariosUsuario'])->name('questionarios.usuario');




Route::get('/meusquestionarios/editar/{id}', [DadosController::class, 'questionarioEditar'])->name('questionarios.editar');
Route::post('/respostas/salvar', [DadosController::class, 'salvarRespostas'])->name('respostas.salvar');

Route::post('/usuario-formulario/finalizar', [DadosController::class, 'finalizar'])->name('usuarioFormulario.finalizar');

Route::post('/relatorio/generar-api', [DadosController::class, 'generarRelatorioAPI'])->name('relatorio.generar.api')->middleware('auth');


Route::get('/meurelatorio/show', [DadosController::class, 'relatorioShow'])->name('relatorio.show');


Route::get('/variaveis/formulario/{id}', [VariavelController::class, 'getPorFormulario']);

Route::put('/calculos/status/{id}', [CalculosController::class, 'status'])->name('calculos.status');
Route::resource('calculos', CalculosController::class);


Route::get('/relatorio/pdf', [RelatorioController::class, 'gerarPDF'])->name('relatorio.pdf');


Route::get('/dashadmin', [DashboardController::class, 'index'])->name('dashboard.admin');

Route::resource('midias', MidiaController::class);


Route::post('/usuario-formulario/{id}/assistido', [UsuarioFormularioController::class, 'marcarAssistido']);


Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat', [ChatController::class, 'store'])->name('chat.ask');


Route::post('/etapas/adicionar', [FormularioEtapaController::class, 'adicionar'])->name('etapas.adicionar');
Route::delete('/etapas/{id}/remover', [FormularioEtapaController::class, 'remover'])->name('etapas.remover');


Route::post('/relatorio/analise/{usuarioId}', [AnaliseController::class, 'gerarAnalise'])->name('relatorio.analise');

Route::post('/relatorio/regenerar', [RelatorioController::class, 'regenerarAnalise'])
    ->name('relatorio.regenerar')
    ->middleware('auth');


Route::get('/notificacoes/marcar-todas', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return back();
})->name('notificacoes.marcarLidas');

Route::get('/verificar-email', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');

    $usuario = User::where('verification_token', $token)->first();

    if (!$usuario) {
        return redirect('/login')->withErrors(['message' => 'Token inválido ou expirado.']);
    }

    $usuario->email_verified_at = now();
    $usuario->verification_token = null;
    $usuario->save();

    return redirect('/login')->with('status', 'E-mail verificado com sucesso. Agora você pode acessar o sistema.');
})->name('verificar.email');

Route::post('/verificar-reativar', function () {
    $user = Auth::user();

    if (!$user->email_verified_at) {
        $token = \Illuminate\Support\Str::random(64);
        $user->verification_token = $token;
        $user->save();

        $user->notify(new \App\Notifications\VerificarEmail($token));
    }

    return redirect()->back()->with('status', 'E-mail de verificação reenviado com sucesso!');
})->name('verificar.email.reativar')->middleware('auth');


Route::get('/gestor/importar', [ImportarUsuariosController::class, 'form'])->name('gestor.importar.form');
Route::post('/gestor/importar', [ImportarUsuariosController::class, 'importar'])->name('gestor.importar');


Route::middleware(['auth', 'can:admin'])->group(function () {
    Route::post('/admin/password/initiate/{id}', [PasswordController::class, 'initiateChange'])
        ->name('password.change.initiate');

    Route::post('/admin/password/update/{id}', [PasswordController::class, 'updatePassword'])
        ->name('password.change.update');
});
