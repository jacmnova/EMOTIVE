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

Template teste


@stop

@section('right_sidebar')
    @include('partials._right_sidebar')
@stop

@section('css')

@stop

@section('js')
    <script> console.log("Usando Script de JS"); </script>
@stop

