@extends('layout.layout')
@section('content')
    <div>
        <h1>STUDENT RECORDS</h1>
    </div>

    <div class="card-container">
        @if ($academic_years->isEmpty())
            <div class="col-md-12 text-center">
                <p>No academic years found.</p>
            </div>
        @else
            @foreach ($academic_years as $year)
                <div class="col-md-4">
                    <a href="{{ route('section-select', ['year' => $year->academic_year]) }}" class="text-center">
                        <div class="card" style="width: 18rem;">
                            <div class="card-body">
                                <h5 class="card-title">{{ $year->academic_year }}</h5>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        @endif
    </div>
@endsection
