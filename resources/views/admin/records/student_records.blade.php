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
                <form id="filter-form2" class="d-flex justify-content-start">
                    <div class="col-4 ms-2">
                        <label for="academic-year-filter" class="form-label">Academic Year:</label>
                        <select name="academic_year" id="academic-year-filter2" class="form-select">
                            <option value="">Select AY</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-4 ms-2">
                        <label for="section-filter" class="form-label">Section:</label>
                        <select name="section" id="section-filter2" class="form-select">
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
        <table id="student-records2" class="table table-striped" style="width:100%;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Sex</th>
                    <th>Address</th>
                    <th>Guardian's Contact #</th>
                    <th>Section</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated by DataTables -->
            </tbody>
        </table>
        <!-- Placeholder for No Data Message -->
    </div>

    <div id="no-data-message2" class="alert alert-warning" style="display: none;">
        Please select an Academic Year or Section to view data.
    </div>
    
    <!-- View Student Modal -->
    <div class="modal fade" id="viewStudentModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Student Information</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Full Name:</strong> <span id="studentFullName_admin"></span></p>
                    <p><strong>Sex:</strong> <span id="studentSex_admin"></span></p>
                    <p><strong>Address:</strong> <span id="studentAddress_admin"></span></p>
                    <p><strong>Guardian's Name:</strong> <span id="studentGuardiansName_admin"></span></p>
                    <p><strong>Guardian's Contact:</strong> <span id="studentGuardiansContact_admin"></span></p>
                    <p><strong>Grade Level:</strong> <span id="studentLevelToBeEnrolled_admin"></span></p>
                    <p><strong>Adviser:</strong> <span id="studentAdviser_admin"></span></p>
                    <p><strong>Section:</strong> <span id="studentSection_admin"></span></p>
                    <p><strong>Mother's Name:</strong> <span id="studentMotherName_admin"></span></p>
                    <p><strong>Academic Year:</strong> <span id="studentAcademicYear_admin"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add subject and grades Modal -->
    <div class="modal fade" id="addGradesModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('store.grades.admin') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Add Subject and Grades</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="studentID" value=""> <!-- Hidden field for studentID -->

                        <div id="subjects-container"> <!-- Correct ID -->
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" name="subjects[0][name]" class="form-control"
                                        placeholder="Subject Name" required>
                                    <input type="text" name="subjects[0][grade]" class="form-control" placeholder="Grade"
                                        required>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="add-subject" class="btn btn-success">Add Another Subject</button>
                        <!-- Correct ID -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Grades</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
