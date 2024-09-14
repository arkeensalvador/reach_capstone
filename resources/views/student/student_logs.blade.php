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
        <table id="student-logs" class="table table-striped" style="width:100%;">
            <thead>
                <tr>
                    <th>DOCUMENT REQUESTED</th>
                    <th>STATUS</th>
                    <th>DATE & TIME REQUESTED</th>
                    <th>REMARKS</th>
                    <th>UPLOADED DOCUMENT</th>
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
                        ];

                        $statusMessage = $statusMessages[$logs->status] ?? 'Unknown Status';
                    @endphp
                    <tr>
                        <td>{{ $document }}</td>
                        <td>{{ $statusMessage }}</td>
                        <td>{{ date('F d, Y h:m:s A', strtotime($logs->created_at)) }}</td>
                        <td>{{ $logs->rejection_reason != null ? $logs->rejection_reason : 'N/A' }}</td>
                        <!-- Display documents for each transaction -->
                        <td>
                            @if ($logs->documents && !$logs->documents->isEmpty())
                                @foreach ($logs->documents as $document)
                                    <a href="{{ route('student.documents.download', ['id' => $document->id]) }}"
                                        target="_blank" class="btn btn-success">
                                        Download File
                                    </a>
                                    <!-- Add a button to view the document -->
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                        data-bs-target="#viewModal"
                                        data-file-url="{{ asset('storage/' . $document->file_path) }}">
                                        View
                                    </button>
                                @endforeach
                            @else
                                No documents uploaded
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">View Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="fileViewer" src="" style="width: 100%; height: 90vh;" frameborder="0"></iframe>
                    <!-- You can also use an <img> tag for image files if necessary -->
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var viewModal = document.getElementById('viewModal');
            viewModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget; // Button that triggered the modal
                var fileUrl = button.getAttribute('data-file-url'); // Extract info from data-* attributes

                var modalBody = viewModal.querySelector('.modal-body');
                var fileViewer = modalBody.querySelector('#fileViewer');
                fileViewer.src = fileUrl;
            });
        });
    </script>
@endsection
