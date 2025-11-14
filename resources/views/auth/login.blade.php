@extends('adminlte::auth.login')

@section('auth_header')
    Entre para iniciar uma nova sessão
@stop

@section('auth_footer')
    {{-- Mostrar solo "Recuperar senha", ocultar "Registrar um novo membro" --}}
    @php
        $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');
        if (config('adminlte.use_route_url', false)) {
            $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
        } else {
            $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
        }
    @endphp
    @if($passResetUrl)
        <p class="my-0">
            <a href="{{ $passResetUrl }}">
                {{ __('adminlte::adminlte.i_forgot_my_password') }}
            </a>
        </p>
    @endif
@stop

@section('auth_body')

    {{-- SweetAlert de sessão expirada --}}
    @if($errors->has('message'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sessão Expirada',
                    text: '{{ $errors->first('message') }}',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    {{-- Formulário de login padrão --}}
    <form action="{{ route('login') }}" method="post">
        @csrf

        {{-- Email --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="Email" autofocus required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback d-block">{{ $message }}</span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Senha" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback d-block">{{ $message }}</span>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">
                        Lembrar-me
                    </label>
                </div>
            </div>

            <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </div>
        </div>
    </form>

@endsection

@section('css')
    <style>
        .login-logo img,
        .login-box .login-logo img {
            width: 360px !important;
            height: 135px !important;
            object-fit: contain;
        }
    </style>
@stop
