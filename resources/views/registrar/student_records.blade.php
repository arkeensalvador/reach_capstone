@extends('layout.layout')
@section('content')
    <div>
        <h1>{{ $section }} {{ $year }} STUDENT RECORDS</h1>
    </div>

    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>Sex</th>
                <th>Address</th>
                {{-- <th>Section</th> --}}
                <th>Guardian's Name</th>
                <th>Contact</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td>{{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</td>
                    <td>{{ $student->sex }}</td>
                    <td>{{ $student->address }}</td>
                    {{-- <td>{{ $student->section }}</td> --}}
                    <td>{{ $student->guardians_name }}</td>
                    <td>{{ $student->guardians_contact }}</td>
                    <td>
                        <a href="#" class="btn btn-info">Edit</a>
                        <a href="#" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
