@extends('layout.layout')
@section('content')
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="row mb-3 mt-5">
                <!-- Left Column for Text (Aligned to Top) -->
                <div class="col-md-8 text-center text-md-left">
                    <h3 class="request-text mt-5">REQUEST YOUR DOCUMENT / REQUIREMENTS</h3>
                    <h1 class="online-text mb-5" style="color: #f85b04;">VIA ONLINE</h1>

                    <p class="mb-5">
                        This system allows students to conveniently request official documents such as
                        Form 137, and Good Moral records online. Simply fill out the
                        required forms, submit your request, and track the status in real-time. The online request process
                        is designed to be user-friendly and efficient, reducing the need for in-person visits and long wait
                        times.
                    </p>

                    <a href="{{ url('student-requests') }}">
                        <button class="request-button mt-2">Request Now</button>
                    </a>

                </div>
                <!-- Right Column for the Box -->
                <div class="col-md-4 text-center">
                    <div class="student-home-box">
                        <!-- You can add content here if needed -->
                        <img src="{{ asset('img/logo-school.png') }}" alt="School Logo">
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
