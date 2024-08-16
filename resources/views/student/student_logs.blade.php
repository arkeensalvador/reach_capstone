@extends('layout.layout')
@section('content')
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-md-6 p-0">
                <h1 class="">REQUEST LOGS</h1>
            </div>
            <div class="col-md-6 p-0">
                <form method="GET" action="{{ route('student.request_logs') }}" class="d-flex justify-content-end">
                    <label for="">Filter Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Approved</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Rejected</option>
                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>In Progress</option>
                        <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Completed</option>
                        <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </form>
            </div>
        </div>
        <table id="example" class="table table-striped" style="width:100%;">
            <thead>
                <tr>
                    <th>DOCUMENT REQUESTED</th>
                    <th>STATUS</th>
                    <th>DATE & TIME REQUESTED</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requestLogs as $logs)
                    @php
                        $document =
                            $logs->doc_requested == '1'
                                ? 'FORM 137'
                                : ($logs->doc_requested == '2'
                                    ? 'GOOD MORAL'
                                    : '');

                        $statusMessages = [
                            0 => 'Pending',
                            1 => 'Approved',
                            2 => 'Rejected',
                            3 => 'In Progress',
                            4 => 'Completed',
                            5 => 'On Hold',
                        ];

                        $statusMessage = $statusMessages[$logs->status] ?? 'Unknown Status';
                    @endphp
                    <tr>
                        <td>{{ $document }}</td>
                        <td>{{ $statusMessage }}</td>
                        <td>{{ date('F d, Y h:m:s A', strtotime($logs->created_at)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
