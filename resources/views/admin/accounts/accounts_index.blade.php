@extends('layout.layout')
@section('content')
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-md-6 p-0">
                <h1 class="">REGISTRAR ACCOUNTS</h1>
            </div>
            <div class="col-md-6 p-0 text-end">
                <a href="{{ url('/create-registrar-account') }}" class="btn btn-success">Create Registrar Account</a>
            </div>
        </div>
        <table id="registrar-accounts" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Account Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registrarAccounts as $account)
                    <tr>
                        <td>{{ $account->name }}</td>
                        <td>{{ $account->username }}</td>
                        <td>{{ $account->email }}</td>
                        <td>
                            @if ($account->acc_status == 1)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>

                        <td>
                            <!-- Add actions here (e.g., Edit, Delete) -->
                            <a href="{{ route('edit.registrar', $account->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('update.acc_status', $account->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger btn-sm">Update status</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
