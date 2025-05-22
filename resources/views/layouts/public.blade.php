<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Fellipelli - CORE' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">

    <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-block px-5 py-1.5 border text-sm rounded-sm dark:text-[#EDEDEC] text-[#1b1b18] border-[#19140035] hover:border-[#1915014a]">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 text-sm rounded-sm border border-transparent dark:text-[#EDEDEC] text-[#1b1b18] hover:border-[#19140035]">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 text-sm border rounded-sm dark:text-[#EDEDEC] text-[#1b1b18] border-[#19140035] hover:border-[#1915014a]">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
            
            {{-- Conteúdo --}}
            <div class="text-sm leading-relaxed flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
                @yield('content')
            </div>

            {{-- Imagem / Estilo da direita --}}
            <div class="bg-[#fff2f2] dark:bg-[#1D0002] relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden flex items-center justify-center p-4">
                <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"></div>
                <img src="{{ asset('img/logo_burnout.png') }}" alt="Logo Fellipelli Burnout" class="relative z-10 max-w-full h-auto">
            </div>

        </main>
    </div>

</body>
</html>
