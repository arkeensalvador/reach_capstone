@foreach ($studentsWithGrades as $student)
    <tr>
        <td>{{ $student['name'] }}</td>
        <td>{{ $student['section'] }}</td>
        <td>{{ number_format($student['average_grade'], 2) }}</td>
        <td>{{ $student['status'] }}</td>
    </tr>
@endforeach
