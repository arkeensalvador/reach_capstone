@extends('layout.layout')

@section('content')
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-md-6 p-0">
                <h1>Import Student Records</h1>
            </div>
        </div>

        <!-- Filter Form Below the Heading -->
        <div class="row mb-3 p-0">
            <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                <div class="col-md-6 p-0 mb-2">
                    @csrf
                    <input type="file" name="file" class="form-control mb-2" required>
                    <!-- Note with icon -->
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> File import only accepts .xlsx and .xls files.
                    </small>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success">Import</button>
                </div>
            </form>
        </div>
    </div>
@endsection
