@extends('adminlte::page')

@section('title', 'Fellipelli')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <p class="lead mb-0" style="font-size: 18px;">Boas vindas, {{ Auth::user()->name }}!</p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <!-- Breadcrumbs -->
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    @if(Auth::check() && is_null(Auth::user()->email_verified_at))
        <div class="card bg-danger text-white">
            <div class="card-header">
                <h3 class="card-title">Confirmação de E-mail Necessária</h3>
            </div>
            <div class="card-body">
                <p>Oi, {{ Auth::user()->name }}!</p>
                <p style="text-align: justify;">Ainda não conseguimos confirmar o seu e-mail. Para liberar o acesso completo à nossa plataforma, dá uma olhadinha na sua caixa de entrada (ou até na de spam).</p>
                <p style="text-align: justify;">Não achou nada? Sem problemas! É só clicar no botão abaixo para reenviar o e-mail de verificação.</p>

                <form action="{{ route('verificar.email.reativar') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light">
                        Reenviar E-mail de Verificação
                    </button>
                </form>
            </div>
        </div>
    @endif

    @include('layouts.partials.whatsapp')

    @if(Auth::user()->admin === true)
        @include('intro.partials._admin')
    @endif

    @if(Auth::user()->gestor === true)
        @include('intro.partials._gestor')
    @endif

    @if(Auth::user()->usuario === true)
        @include('dicas.partials._dica_acesso')
        @include('intro.partials._usuario')
    @else
        @include('intro.partials._novo')
    @endif

    <!-- @if(Auth::user()->usuario === true)
        @include('dicas.partials._dica_video')
        <div class="row">
            @include('infos.partials._status')
            @include('infos.partials._libera_video')
        </div>
    @endif -->


@stop

@section('footer')
    <div class="float-right d-none d-sm-block">
    <b>Version</b> {{ config('app.version') }}
    </div>
    <strong>Copyright © 2000-2025 <a href="https://www.fellipelli.com.br">Fellipelli</a>.</strong> All rights reserved.
@stop


@section('css')
    <style>
        .main-sidebar {
            min-height: 100vh;
        }

        ::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }

        body {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        body::-webkit-scrollbar {
            display: none;
        }
    </style>
@stop

@section('js')
    <script> console.log("Usando Script de JS"); </script>
@stop

