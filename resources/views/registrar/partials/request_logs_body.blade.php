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
        <td>{{$logs->rejection_reason}}</td>
        <td>
            <button class="btn btn-primary update-status-btn" data-log-id="{{ $logs->transaction_ID }}">
                Update Status
            </button>
        </td>
    </tr>
@endforeach
