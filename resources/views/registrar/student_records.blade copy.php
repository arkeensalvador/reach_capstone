@extends('layout.layout')

@section('content')
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-md-6 p-0">
                <h1>STUDENT RECORDS</h1>
            </div>
        </div>

        <!-- Filter Form Below the Heading -->
        <div class="row mb-3">
            <div class="col-md-12 p-0">
                <form id="filter-form" class="d-flex justify-content-start">
                    <div class="ms-2">
                        <label for="academic-year-filter" class="form-label">Academic Year:</label>
                        <select name="academic_year" id="academic-year-filter" class="form-select">
                            <option value="">Select AY</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ms-2">
                        <label for="section-filter" class="form-label">Section:</label>
                        <select name="section" id="section-filter" class="form-select">
                            <option value="">Select Section</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section }}">{{ $section }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Student Records Table -->
        <table id="student-records" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Sex</th>
                    <th>Address</th>
                    <th>Guardian's Contact #</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated by DataTables -->
            </tbody>
        </table>
    </div>
@endsection
