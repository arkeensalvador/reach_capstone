@extends('layout.layout')

@section('content')
    <div>
        <h1>Student Grades</h1>
    </div>
    <hr class="custom-hr">

    <!-- Display Student Information -->
    <div class="student-info">
        <h3>{{ $studentName }}</h3>
        <p><strong>School Year:</strong> {{ $student->academic_year }}</p>
        <p><strong>Section:</strong> {{ $student->section }}</p>
    </div>

    <div class="card-container">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Subject</th>
                    <th scope="col">Grades</th>
                    <th scope="col" colspan="2">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($student->grades as $grade)
                    <tr>
                        <td>{{ $grade->subject }}</td>
                        <form action="{{ route('update_grades_admin', ['id' => $grade->id]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <td>
                                <input type="text" name="grade" value="{{ $grade->grades }}" class="form-control">
                            </td>
                            <td>
                                <button type="submit" class="btn btn-info btn-sm">Update</button>
                        </form>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteAdmin('{{ $grade->id }}', '{{ $grade->subject }}')">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="col-3">
            <a href="{{ url('admin/student-records') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
@endsection
