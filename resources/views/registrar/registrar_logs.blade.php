@extends('layout.layout')
@section('content')
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-md-6 p-0">
                <h1 class="">REQUEST LOGS</h1>
            </div>
            <div class="col-md-6 p-0">
                <form method="GET" action="{{ route('registrar.request_logs') }}" class="d-flex justify-content-end">
                    <label for="">Filter Status</label>
                    <select name="status" class="form-select me-2" onchange="this.form.submit()">
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
                    <th>STUDENT</th>
                    <th>DOCUMENT REQUESTED</th>
                    <th>STATUS</th>
                    <th>DATE & TIME REQUESTED</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody id="request-logs-body">
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
                        $studentName = $logs->fname . ' ' . $logs->mname . ' ' . $logs->lname;
                    @endphp
                    <tr>
                        <td>{{ $studentName }}</td>
                        <td>{{ $document }}</td>
                        <td>
                            <select class="form-control update-status" data-log-id="{{ $logs->transaction_ID }}">
                                @foreach ($statusMessages as $key => $value)
                                    <option value="{{ $key }}" {{ $logs->status == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>{{ date('F d, Y h:i:s A', strtotime($logs->created_at)) }}</td>
                        <td>
                            <button class="btn btn-primary update-status-btn" data-log-id="{{ $logs->transaction_ID }}">
                                Update Status
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            // Handle status update
            $('.update-status-btn').click(function() {
                var logId = $(this).data('log-id');
                var newStatus = $(this).closest('tr').find('.update-status').val();
                var statusText = $(this).closest('tr').find('.update-status option:selected').text();

                $.ajax({
                    url: '{{ route('updateStatus') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        transaction_ID: logId,
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                            });
                            // Update the status text in the table without reloading
                            $(`button[data-log-id="${logId}"]`).closest('tr').find(
                                'td:nth-child(3) select').val(newStatus);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating the status.',
                        });
                    }
                });
            });
        });
    </script>
@endsection
