
{{-- resources/views/tutorial.blade.php --}}
@extends('layouts.public')

@section('title', 'Tutoriais do Sistema')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Tutoriais do Sistema</h1>
    <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] space-y-4">
        <p>Assista abaixo aos tutoriais de uso do sistema e entenda como aplicar corretamente os instrumentos de avaliação psicossocial.</p>

        <ul class="list-disc ml-5 space-y-2">
            <li><strong>Cadastro de usuários:</strong> vídeo explicativo sobre como registrar novos participantes.</li>
            <li><strong>Atribuição de formulários:</strong> como vincular o QRP-36 aos usuários.</li>
            <li><strong>Acompanhamento de resultados:</strong> onde e como visualizar os diagnósticos.</li>
            <li><strong>Geração de relatórios:</strong> orientações para exportar relatórios em PDF.</li>
        </ul>

        <p>Caso tenha dúvidas adicionais, entre em contato com o suporte Fellipelli.</p>
    </div>
@endsection
