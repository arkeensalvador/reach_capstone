@extends('layout.layout')
@section('content')
    <div>
        <h1>ENROLL STUDENT</h1>
    </div>
    <hr class="custom-hr">
    <div class="card-container">
        <form class="row g-3" action="{{ route('enroll.student') }}" method="POST">
            @csrf <!-- CSRF Token to protect against Cross-Site Request Forgery -->
            <div class="col-md-4">
                <label for="lastname" class="form-label">Last Name</label>
                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Last name" >
            </div>
            <div class="col-md-4">
                <label for="middlename" class="form-label">Middle Name</label>
                <input type="text" class="form-control" name="middlename" id="middlename" placeholder="Middle name"
                    >
            </div>
            <div class="col-md-4">
                <label for="firstname" class="form-label">First Name</label>
                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="First name" >
            </div>
            <div class="col-md-6">
                <label for="sex" class="form-label">Sex</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sex" id="male" value="Male" >
                    <label class="form-check-label" for="male">Male</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sex" id="female" value="Female" >
                    <label class="form-check-label" for="female">Female</label>
                </div>
            </div>
            <div class="col-md-12">
                <label for="level_to_be_enrolled" class="form-label">Mother's maiden name</i></label>
                <input type="text" class="form-control" name="mother_name" id="mother_name"
                    placeholder="Mother's maiden name" >
            </div>
            <div class="col-md-12">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" name="address" id="address" placeholder="Complete address"
                    >
            </div>

            <div class="col-md-6">
                <label for="level_to_be_enrolled" class="form-label">Level to be enrolled</label>
                <input type="text" class="form-control" name="level_to_be_enrolled" id="level_to_be_enrolled"
                    value="Grade 10" readonly>
            </div>
            <div class="col-md-6">
                <label for="academic_year" class="form-label">Academic Year <i>(e.g 2020-2021, 2021-2022)</i></label>
                {{-- <input type="text" class="form-control" name="academic_year" id="academic_year" placeholder="" > --}}
                <select class="form-control" name="academic_year" id="academic_year">
                    <option selected disabled>Select academic year</option>
                    @foreach ($academic_years as $ay)
                        <option value="{{$ay->academic_year}}">{{$ay->academic_year}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="guardians_name" class="form-label">Guardian's Name</label>
                <input type="text" class="form-control" name="guardians_name" id="guardians_name"
                    placeholder="Guardian's name" >
            </div>
            <div class="col-md-6">
                <label for="guardians_contact" class="form-label">Guardian's Contact no.</label>
                <input type="text" class="form-control" name="guardians_contact" id="guardians_contact"
                    placeholder="Guardian's contact number" >
            </div>
            <div class="col-md-6">
                <label for="adviser" class="form-label">Adviser</label>
                <input type="text" class="form-control" name="adviser" id="adviser" placeholder="Name of adviser"
                    >
            </div>
            <div class="col-md-6">
                <label for="section" class="form-label">Section</label>
                <input type="text" class="form-control" name="section" id="section" placeholder="Section"
                    >
            </div>

            <div class="col-12 mb-5">
                <a href="{{ url('student-records') }}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
@endsection
