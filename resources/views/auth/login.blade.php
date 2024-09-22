<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'REACH: RECORD ENQUIRY ACCESS CONTROL HUB') }}</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo-school.png') }}">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Styles -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <style>
        :root {
            --background-image-url: url('{{ asset('img/pic-school.jpg') }}');
        }
    </style> --}}

</head>

<body>
    <div class="container-fluid center-card">
        <div class="card login-form">
            <div class="card-body">
                <div class="form-logo">
                    <img src="{{ asset('img/logo-school.png') }}" alt="School Logo" width="35">
                    <p>REACH</p>
                </div>
                @if (session('error'))
                    <script>
                        Swal.fire({
                            text: {{ session('error') }},
                            icon: "warning"
                        });
                    </script>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-title">
                        <h1>HELLO!</h1>
                        <p>Login to your account</p>
                        <a href="{{ route('auth.google') }}">
                            <button type="button" class="login-with-google-btn">
                                Sign in with Google
                            </button>
                        </a>

                        <p>or</p>
                    </div>



                    <div class="form-floating mb-3">
                        <input id="login" type="text" class="form-control @error('login') is-invalid @enderror"
                            name="login" value="{{ old('login') }}" required autocomplete="login" autofocus>
                        <label for="floatingInput">Username / Email</label>
                        @error('login')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password" required
                            autocomplete="current-password">
                        <label for="floatingPassword">Password</label>
                        <small class="capslock-notif" id="capslockNotif">Caps Lock is ON</small>

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-button">
                        <button type="submit" class="btn btn-form-login">
                            {{ __('Login') }}
                        </button>

                        {{-- @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('floatingPassword');
            const capslockNotif = document.getElementById('capslockNotif');

            passwordInput.addEventListener('keydown', function(event) {
                if (event.getModifierState('CapsLock')) {
                    capslockNotif.style.display = 'block';
                } else {
                    capslockNotif.style.display = 'none';
                }
            });

            passwordInput.addEventListener('keyup', function(event) {
                if (!event.getModifierState('CapsLock')) {
                    capslockNotif.style.display = 'none';
                }
            });
        });

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
            });
        @endif
    </script>
</body>

</html>
