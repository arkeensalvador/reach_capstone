@extends('layout.layout')
@section('content')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
            });
        </script>
    @endif
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-md-12 p-0">
                <h1 class="">Edit Registrar Account</h1>
            </div>
        </div>
        <form class="row g-3" action="{{ route('update.registrar', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <label for="name" class="form-label">Name</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                    value="{{ old('name', $user->name) }}" required autocomplete="name" autofocus>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="username" class="form-label">Username</label>
                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                    name="username" value="{{ old('username', $user->username) }}" required autocomplete="username">
                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-12">
                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="col-6">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" autocomplete="new-password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="col-6">
                <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                    autocomplete="new-password">
            </div>

            <div class="col-6">
                <label for="userType" class="form-label">{{ __('User type') }}</label>
                <select name="userType" id="userType" class="form-control @error('userType') is-invalid @enderror"
                    required>
                    <option value="" disabled>Select User Type</option>
                    <option value="student" {{ $user->userType == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="registrar" {{ $user->userType == 'registrar' ? 'selected' : '' }}>Registrar</option>
                    <option value="admin" {{ $user->userType == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('userType')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="col-12">
                <a href="{{ url('admin/registrar-accounts') }}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Update Account') }}
                </button>
            </div>
        </form>
    </div>
@endsection
