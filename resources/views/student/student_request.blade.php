@extends('layout.layout')
@section('content')
    <div>
        <h1>REQUEST FORM</h1>
    </div>
    <hr class="custom-hr">
    <div class="card-container">
        <form class="row g-3" action="{{ route('student.request') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="text" class="form-control" name="user_id" id="" value="{{ Auth::user()->id }}" hidden>
            <div class="col-md-4">
                <label for="" class="form-label">Last Name</label>
                <input type="text" class="form-control" name="user_lname" id="" required>
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Middle Name</label>
                <input type="text" class="form-control" name="user_mname" id="" required>
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">First Name</label>
                <input type="text" class="form-control" name="user_fname" id="" required>
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Sex</label>
                <input type="text" class="form-control" name="user_sex" id="" required>
            </div>
            <div class="col-md-8">
                <label for="inputPassword4" class="form-label">Address</label>
                <input type="text" class="form-control" name="user_address" id="">
            </div>

            <div class="col-md-12">
                <label for="inputState" class="form-label">Request for</label>
                <select id="inputState" class="form-select" name="user_doc_requested" required>
                    <option selected>Choose...</option>
                    <option value="1">FORM 137</option>
                    <option value="2">GOOD MORAL</option>
                </select>
            </div>
            <div class="col-md-12">
                <label for="inputZip" class="form-label">E-Signature</label>
                <input type="file" class="form-control mb-1" name="user_signature" accept="image/*" required>
                <small>
                    <i class="fa-solid fa-circle-info"></i>
                    <i>
                        No E-signature? We got you! You can click the <a href="https://signaturely.com/online-signature/" target="_blank">link</a> to generate your E-Signature
                    </i>
                </small>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    {{ __('Request') }}
                </button>
            </div>
        </form>
    </div>
@endsection
