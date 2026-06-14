@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Storage;
    $configData = Helper::appClasses();
    $customizerHidden = 'customizer-hide';
    try {
        $ci = \App\Models\ConfiguracionInstitucional::cached();
    } catch (\Exception $e) {
        $ci = null;
    }
@endphp

@extends('layouts/blankLayout')

@section('title', 'Iniciar Sesión - ' . ($ci?->sigla ?? ($ci?->nombre_institucion ?? 'PULSO UGEL')))

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover">

        <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
            @if (!empty($ci?->logo_ruta))
                <span class="app-brand-logo demo">
                    <img src="{{ Storage::url($ci->logo_ruta) }}" height="28" alt="logo" class="rounded">
                </span>
            @endif
            <span class="app-brand-text demo text-heading fw-bold">
                {{ $ci?->sigla ?? ($ci?->nombre_institucion ?? 'PULSO UGEL') }}
            </span>
        </a>

        <div class="authentication-inner row m-0">
            <!-- Ilustración lateral -->
            <div class="d-none d-xl-flex col-xl-8 p-0">
                <div class="auth-cover-bg d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/illustrations/auth-login-illustration-' . $configData['theme'] . '.png') }}"
                        alt="login" class="my-5 auth-illustration"
                        data-app-light-img="illustrations/auth-login-illustration-light.png"
                        data-app-dark-img="illustrations/auth-login-illustration-dark.png" />
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}"
                        alt="bg" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png"
                        data-app-dark-img="illustrations/bg-shape-image-dark.png" />
                </div>
            </div>

            <!-- Formulario -->
            <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
                <div class="w-px-400 mx-auto mt-12 pt-5">

                    <h4 class="mb-1">Bienvenido a {{ $ci?->sigla ?? ($ci?->nombre_institucion ?? 'PULSO UGEL') }} 👋</h4>
                    <p class="mb-6 text-muted">Ingresa tus credenciales para acceder al sistema</p>

                    @if (session('status'))
                        <div class="alert alert-success mb-4" role="alert">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label for="login-email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="login-email"
                                name="email"
                                placeholder="{{ $ci?->correo_institucional ? 'usuario@' . explode('@', $ci->correo_institucional)[1] : 'tu.correo@ugel.gob.pe' }}"
                                autofocus value="{{ old('email') }}" />
                            @error('email')
                                <span class="invalid-feedback" role="alert"><span
                                        class="fw-medium">{{ $message }}</span></span>
                            @enderror
                        </div>

                        <div class="mb-6 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="login-password">Contraseña</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-sm">
                                        <small>¿Olvidaste tu contraseña?</small>
                                    </a>
                                @endif
                            </div>
                            <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                                <input type="password" id="login-password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="············" aria-describedby="password" />
                                <span class="input-group-text cursor-pointer">
                                    <i class="icon-base ti tabler-eye-off"></i>
                                </span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert"><span
                                        class="fw-medium">{{ $message }}</span></span>
                            @enderror
                        </div>

                        <div class="my-8">
                            <div class="form-check mb-0 ms-2">
                                <input class="form-check-input" type="checkbox" id="remember-me" name="remember"
                                    {{ old('remember') ? 'checked' : '' }} />
                                <label class="form-check-label" for="remember-me">Recordarme</label>
                            </div>
                        </div>

                        <button class="btn btn-primary d-grid w-100" type="submit">Iniciar Sesión</button>
                    </form>

                    @if (Route::has('register'))
                        <p class="text-center">
                            <span>¿No tienes cuenta?</span>
                            <a href="{{ route('register') }}"> Regístrate</a>
                        </p>
                    @endif

                    <div class="divider my-6">
                        <div class="divider-text">
                            {{ $ci?->nombre_institucion ?? 'PULSO UGEL' }}
                            @if ($ci?->provincia || $ci?->departamento)
                                &bull; {{ implode(', ', array_filter([$ci->provincia, $ci->departamento])) }}
                            @endif
                        </div>
                    </div>

                    <p class="text-center text-muted small mb-0">
                        <i class="ti tabler-shield-check me-1 text-success"></i>
                        Sistema de Monitoreo de Control Interno e Integridad Institucional
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
