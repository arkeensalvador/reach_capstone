@extends('layout.layout')
@section('content')
    <div>
        <h1>STUDENT RECORDS</h1>
    </div>
    <div class="card-container">
        <a href="{{ url('section') }}" class="text-center">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">{YEAR}</h5>
                </div>
            </div>
        </a>
        <a href="{{ url('section') }}" class="text-center">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">{YEAR}</h5>
                </div>
            </div>
        </a>
        <a href="{{ url('section') }}" class="text-center">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">{YEAR}</h5>
                </div>
            </div>
        </a>
    </div>
@endsection
