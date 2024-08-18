@extends('layout.layout')
@section('content')
    <div>
        <h1>STUDENT RECORDS</h1>
    </div>
    <div class="card-container">
        @foreach ($section as $sec)
            <div class="col-md-4">
                <a href="{{ route('registrar-section', ['year' => request()->year, 'section' => $sec->section]) }}" class="text-center">
                    <div class="card" style="width: 18rem;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $sec->section }}</h5>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
