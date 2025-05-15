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

                </ol>
            </div>
        </div>
    </div>

@stop

@section('content')

    @if(Auth::user()->admin === true)
        @include('intro.partials._admin')
    @endif

    @if(Auth::user()->gestor === true)
        @include('intro.partials._gestor')
    @endif

    @if(Auth::user()->usuario === true)
        @include('intro.partials._usuario')
    @else
        @include('intro.partials._novo')
    @endif

@stop


@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Usando Script de JS"); </script>
@stop