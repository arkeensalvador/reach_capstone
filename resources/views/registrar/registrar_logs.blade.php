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
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Approved</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>
            </div>
        </div>
        <table id="registrar-logs" class="table table-striped" style="width:100%;">
            <thead>
                <tr>
                    <th>STUDENT</th>
                    <th>DOCUMENT REQUESTED</th>
                    <th>STATUS</th>
                    <th>DATE & TIME REQUESTED</th>
                    <th>RELEASE DATE</th>
                    <th>REMARKS</th>
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
                        ];

                        $statusMessage = $statusMessages[$logs->status] ?? 'Unknown Status';
                        $studentName = $logs->fname . ' ' . $logs->mname . ' ' . $logs->lname;
                    @endphp
                    <tr>
                        <td>{{ $studentName }}</td>
                        <td>{{ $document }}</td>
                        <td style="width: 10%;">
                            <select class="form-control update-status" data-log-id="{{ $logs->transaction_ID }}">
                                @foreach ($statusMessages as $key => $value)
                                    <option value="{{ $key }}" {{ $logs->status == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>{{ date('M d, Y h:i:s A', strtotime($logs->created_at)) }}</td>
                        <td>
                            <input type="text" class="form-control release-date-picker"
                                value="{{ $logs->date_released != null ? date('M d, Y', strtotime($logs->date_released)) : '' }}"
                                data-log-id="{{ $logs->transaction_ID }}" placeholder="Select Date" readonly>
                        </td>
                        <td>{{ $logs->rejection_reason }}</td>
                        <td>
                            <button class="btn btn-primary update-status-btn mb-1" data-log-id="{{ $logs->transaction_ID }}">
                                Update Status
                            </button>
                            @if ($logs->status == 1)
                                @php
                                    // Check if there are any documents uploaded
                                    $hasDocuments = $logs->documents->count() > 0;
                                @endphp

                                <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#uploadModal" data-transaction-id="{{ $logs->transaction_ID }}"
                                    @if ($hasDocuments) disabled @endif>
                                    Upload Document
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Document upload</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('document.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Hidden input to store the transaction ID -->
                        <input type="hidden" name="transaction_id" id="transaction_id">
                        <div class="col-md-12">
                            <label for="file" class="form-label">Upload Document</label>
                            <input type="file" class="form-control" name="file" id="file"
                                accept=".pdf, .png, .jpg">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
