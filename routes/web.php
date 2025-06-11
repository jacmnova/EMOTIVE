<?php

use App\Models\Midia;
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

use App\Http\Controllers\FormularioEtapaController;
use App\Http\Controllers\UsuarioFormularioController;

use App\Http\Controllers\AnaliseController;
use App\Http\Controllers\Auth\EmailVerificacaoController;


use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\NovoUsuarioCadastrado;


Route::get('/', function () {
    return view('welcome');
});


Route::view('/termos', 'termos')->name('termos');
Route::view('/tutorial', 'tutorial')->name('tutorial');


Auth::routes();


Route::get('/template', function () {
    return view('template.index');
})->name('template.index');



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// GESTAO
Route::get('/gestao-usuarios', [GestorController::class, 'usuariosCliente'])->name('usuarios.cliente');
Route::get('/editar-usuario/{id}', [GestorController::class, 'usuariosEditar'])->name('usuarios.editar');
Route::get('/lista-formularios', [GestorController::class, 'listaFormularios'])->name('formularios.cliente');



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


// Route::get('/meurelatorio/show/{id}', [DadosController::class, 'relatorioShow'])->name('relatorio.show');
Route::get('/meurelatorio/show', [DadosController::class, 'relatorioShow'])->name('relatorio.show');




Route::get('/variaveis/formulario/{id}', [VariavelController::class, 'getPorFormulario']);

Route::put('/calculos/status/{id}', [CalculosController::class, 'status'])->name('calculos.status');
Route::resource('calculos', CalculosController::class);


Route::get('/relatorio/pdf', [RelatorioController::class, 'gerarPDF'])->name('relatorio.pdf');


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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



// Route::get('/teste-email', function () {
//     $admin = User::find(1);
//     $falso = new \App\Models\User([
//         'name' => 'Teste',
//         'email' => 'wheelkorner@gmail.com'
//     ]);

//     $admin->notify(new NovoUsuarioCadastrado($falso));

//     return 'Notificação enviada';
// });

Route::get('/verificar-email/{token}', [EmailVerificacaoController::class, 'verificar'])
    ->name('verificar.email');