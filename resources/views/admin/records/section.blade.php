@extends('layout.layout')
@section('content')
    <div>
        <h1>STUDENT RECORDS</h1>
    </div>
    <div class="card-container">
        <a href="{{ url('records-data') }}" class="text-center">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">{SECTION_NAME}</h5>
                </div>
            </div>
        </a>
        <a href="{{ url('records-data') }}" class="text-center">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">{SECTION_NAME}</h5>
                </div>
            </div>
        </a>
        <a href="{{ url('records-data') }}" class="text-center">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">{SECTION_NAME}</h5>
                </div>
            </div>
        </a>
    </div>
@endsection
