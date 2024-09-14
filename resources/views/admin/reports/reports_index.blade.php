@extends('layout.layout')

@section('content')
    <div class="container">
        <h1>Statistics Report</h1>

        <div class="row">
            <div class="col-md-6">
                <h3>Total Students: {{ $totalStudents }}</h3>
            </div>
            <div class="col-md-6">
                <h3>Total Transactions: {{ $totalTransactions }}</h3>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <h4>Students by Academic Year and Section</h4>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('export.student.total.csv') }}" class="btn btn-primary">Download CSV</a>
                    </div>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Academic Year</th>
                            @foreach ($sections as $section)
                                <th>{{ $section }}</th>
                            @endforeach
                            <th>Total Enrolled</th> <!-- Column for row totals -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentsByYear as $year => $sectionsData)
                            <tr>
                                <td>{{ $year }}</td>
                                @php
                                    $totalForYear = 0;
                                @endphp
                                @foreach ($sections as $section)
                                    @php
                                        $count = $sectionsData->get($section)->total ?? 0;
                                        $totalForYear += $count;
                                    @endphp
                                    <td>{{ $count }}</td>
                                @endforeach
                                <td>{{ $totalForYear }}</td> <!-- Total per row -->
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            @foreach ($sections as $section)
                                @php
                                    $totalForSection = $studentsByYear->reduce(function ($carry, $items) use (
                                        $section,
                                    ) {
                                        return $carry + ($items->get($section)->total ?? 0);
                                    }, 0);
                                @endphp
                                <th>{{ $totalForSection }}</th>
                            @endforeach
                            <th>
                                @php
                                    $totalOverall = $studentsByYear->reduce(function ($carry, $items) use ($sections) {
                                        return $carry +
                                            $sections->reduce(function ($subCarry, $section) use ($items) {
                                                return $subCarry + ($items->get($section)->total ?? 0);
                                            }, 0);
                                    }, 0);
                                @endphp
                                {{ $totalOverall }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- New section for Pass/Fail Statistics -->
            <div class="col-md-12 mt-4">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <h4>Student Grades Pass/Fail Status by Academic Year with >=75 Final Grades Avg</h4>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('export.csv') }}" class="btn btn-primary">Download CSV</a>
                    </div>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Academic Year</th>
                            <th>Total Students</th>
                            <th>Total Passed</th>
                            <th>Total Failed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($passFailStats as $stats)
                            <tr>
                                <td>{{ $stats['year'] }}</td>
                                <td>{{ $stats['total_count'] }}</td>
                                <td>{{ $stats['passed_count'] }}</td>
                                <td>{{ $stats['failed_count'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="col-md-12 mt-4">
                <h4>Total Transactions by Status</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Total Transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactionsByStatus as $status)
                            @php
                                $statusMessages = [
                                    0 => 'Pending',
                                    1 => 'Approved',
                                    2 => 'Rejected',
                                ];

                                $statusMessage = $statusMessages[$status->status] ?? 'Unknown Status';
                            @endphp
                            <tr>
                                <td>{{ $statusMessage }}</td>
                                <td>{{ $status->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        <div class="col-md-12 mt-4">
            <div class="row mb-3">
                <div class="col-md-8">
                    <h4>Grades Analysis</h4>
                </div>
                <div class="col-md-4 text-end">
                    <button id="analyze-grades-btn" class="btn btn-primary">Analyze Grades</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div id="grades-chart" class="mt-4"></div>
                </div>
                <div class="col-md-6">
                    <div id="mean-grades-chart" class="mt-4"></div>
                </div>
                <div class="col-md-12">
                    <div id="analysis-result" class="mt-2"></div>
                </div>
            </div>

        </div>

        <script>
            $(document).ready(function() {
                $('#analyze-grades-btn').on('click', function() {
                    $.ajax({
                        url: '{{ route('analyze.grades') }}',
                        method: 'GET',
                        success: function(data) {
                            let resultHtml = '';
                            resultHtml += '<div class="row">';

                            // Column 1: Aggregate Grades by Subject
                            resultHtml += '<div class="col-md-6">';
                            resultHtml += '<h4>Aggregate Grades by Subject:</h4>';
                            $.each(data.analysis, function(subject, stats) {
                                resultHtml +=
                                    `<p><strong>${subject}</strong>: ${stats.grades.join(', ')}</p>`;
                            });
                            resultHtml += '<h4>Conclusion:</h4>';
                            resultHtml +=
                                `<p>Subject with the Lowest Mean Grade: <strong>${data.conclusion.lowest_mean_subject}</strong> (${data.conclusion.lowest_mean_grade ? data.conclusion.lowest_mean_grade.toFixed(2) : 'N/A'})</p>`;
                            resultHtml +=
                                `<p>Subject with the Highest Mean Grade: <strong>${data.conclusion.highest_mean_subject}</strong> (${data.conclusion.highest_mean_grade ? data.conclusion.highest_mean_grade.toFixed(2) : 'N/A'})</p>`;
                            resultHtml +=
                                `<p>Subjects Needing More Focus:<br> <strong>${data.conclusion.lowest_mean_subject}</strong> has the lowest mean grade and may need more focus in teaching.</p>`;
                            resultHtml +=
                                `<p>Subjects with Better Performance:<br> <strong>${data.conclusion.highest_mean_subject}</strong> has the highest mean grade, suggesting better performance.</p>`;


                            resultHtml += '</div>';

                            // Column 2: Determine Lowest and Highest Grades, Conclusion
                            resultHtml += '<div class="col-md-6">';
                            resultHtml +=
                                '<h4>Determine the Lowest and Highest Grades by Subject:</h4>';

                            // Create a container for the details of each subject
                            $.each(data.analysis, function(subject, stats) {
                                resultHtml +=
                                    `<div class="row">
                                    <div class="col-md-6">
                                        <p><strong>${subject}</strong><br></p>
                                    
                                    </div>
                                    <div class="col-md-6">
                                        <p>Lowest Grade: ${stats.min}<br>
                                        Highest Grade: ${stats.max}<br>
                                        Mean: ${stats.mean ? stats.mean.toFixed(2) : 'N/A'}</p>
                                    </div>
                                </div>`;
                            });

                            resultHtml += '</div>'; // Close row div

                            $('#analysis-result').html(resultHtml);

                            // Render ApexCharts
                            renderCharts(data.chartData);
                        },
                        error: function() {
                            $('#analysis-result').html(
                                '<p>An error occurred while analyzing grades.</p>');
                        }
                    });
                });
            });


            function renderCharts(chartData) {
                var subjects = chartData.subjects;
                var gradesBySubject = chartData.gradesBySubject;
                var meanGradesBySubject = chartData.meanGradesBySubject;
                var sections = chartData.sections; // Get sections for x-axis

                // Render Grades by Subject Line Chart
                var gradesChartOptions = {
                    series: subjects.map((subject, index) => ({
                        name: subject,
                        data: gradesBySubject[index]
                    })),
                    chart: {
                        type: 'line',
                        height: 350,
                        animations: {
                            enabled: true,
                            easing: 'linear',
                            dynamicAnimation: {
                                speed: 1000
                            }
                        },
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    title: {
                        text: 'Grades by Subject',
                        align: 'left'
                    },
                    markers: {
                        size: 0
                    },
                    xaxis: {
                        categories: sections, // Use sections for x-axis categories
                    },
                    yaxis: {
                        max: 100
                    },
                    legend: {
                        show: true
                    },
                };

                new ApexCharts(document.querySelector('#grades-chart'), gradesChartOptions).render();

                // Render Mean Grades by Subject Pie Chart
                var meanGradesChartOptions = {
                    series: meanGradesBySubject,
                    chart: {
                        type: 'pie',
                        height: 350
                    },
                    labels: subjects,
                    title: {
                        text: 'Mean Grades by Subject'
                    },
                    legend: {
                        show: true
                    }
                };

                new ApexCharts(document.querySelector('#mean-grades-chart'), meanGradesChartOptions).render();
            }
        </script>

    </div>
@endsection
