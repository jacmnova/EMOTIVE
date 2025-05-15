<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DadosController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\VariavelController;
use App\Http\Controllers\PerguntasController;
use App\Http\Controllers\FormulariosController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\QuestionariosController;
use App\Http\Controllers\UsuarioFormularioController;
use App\Http\Controllers\GestorController;
use App\Http\Controllers\CalculosController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

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

Route::resource('usuario_formulario', UsuarioFormularioController::class);

Route::get('/meusquestionarios', [DadosController::class, 'questionariosUsuario'])->name('questionarios.usuario');
Route::get('/meusquestionarios/editar/{id}', [DadosController::class, 'questionarioEditar'])->name('questionarios.editar');
Route::post('/respostas/salvar', [DadosController::class, 'salvarRespostas'])->name('respostas.salvar');

Route::post('/usuario-formulario/finalizar', [DadosController::class, 'finalizar'])->name('usuarioFormulario.finalizar');

Route::get('/meurelatorio/show/{id}', [DadosController::class, 'relatorioShow'])->name('relatorio.show');

Route::get('/variaveis/formulario/{id}', [VariavelController::class, 'getPorFormulario']);

Route::put('/calculos/status/{id}', [CalculosController::class, 'status'])->name('calculos.status');
Route::resource('calculos', CalculosController::class);