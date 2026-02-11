@extends('adminlte::page')

@section('title', 'Análise Gerada')

@section('content_header')
    <h1>Análise Gerada com IA</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <pre style="white-space: pre-wrap;">{{ $output }}</pre>
        </div>
    </div>
@stop