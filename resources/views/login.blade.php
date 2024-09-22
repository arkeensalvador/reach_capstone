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
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
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
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-title">
                        <h1>HELLO!</h1>
                        <p>Login to your account</p>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="floatingInput" placeholder="name@example.com">
                        <label for="floatingInput">Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
                        <label for="floatingPassword">Password</label>
                        <small class="capslock-notif" id="capslockNotif">Caps Lock is ON</small>
                    </div>
                    <div class="form-button">
                        <button type="submit" class="btn btn-form-login">Login</button>
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
    </script>
</body>

</html>
