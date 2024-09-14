@extends('layout.layout')

@section('content')
    <section class="content">
        <div class="container-fluid p-0">
            <!-- Stat Boxes -->
            <div class="row mb-4">
                @foreach ([['info', 'totalTransaction', 'Total Request'], ['success', 'approvedTransaction', 'Total Approved Requests'], ['danger', 'rejectedTransaction', 'Total Rejected Requests'], ['warning', 'pendingTransaction', 'Total Pending Requests'], ['primary', 'totalStudents', 'Total Enrolled Students'], ['secondary', 'totalRegistrar', 'Total Registrar Accounts']] as $stat)
                    <div class="col-lg-3 col-6">
                        <div class="card text-bg-{{ $stat[0] }} mb-3">
                            <div class="card-body">
                                <h3 class="card-title">{{ ${$stat[1]} }}</h3>
                                <p class="card-text">{{ $stat[2] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Analytics Charts -->
            <hr>
            <h2>Analytics</h2>
            <div class="row">
                <div class="col-lg-6">
                    <div id="chart" class="pt-3"></div>
                </div>

                <div class="col-lg-6">
                    <form method="GET" action="{{ route('admin.index') }}">
                        <label for="academic_year">Select Academic Year:</label>
                        <select id="academic_year" class="form-control" name="academic_year" onchange="this.form.submit()">
                            <option value="" selected disabled>Select AY</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    <div id="chart2"></div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-6">
                    <div id="doc-requested"></div>
                </div>
                <div class="col-lg-6">
                    <div id="passed"></div>
                </div>
            </div>
        </div>
    </section>

    
    <script>
        // TOTAL NUMBER OF REQUEST PER MONTH
        document.addEventListener('DOMContentLoaded', function() {
            var options1 = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: 'Transactions',
                    data: @json(array_values($transactionsPerMonth))
                }],
                xaxis: {
                    categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
                        'September', 'October', 'November', 'December'
                    ]
                },
                title: {
                    text: 'Total Transactions Per Month',
                    align: 'center'
                }
            };

            var chart1 = new ApexCharts(document.querySelector("#chart"), options1);
            chart1.render();

            // TOTAL STUDENT PER SECTION AND ACADEMIC YEAR
            var options2 = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: 'Students',
                    data: @json(array_values($sectionCounts))
                }],
                xaxis: {
                    categories: @json(array_keys($sectionCounts))
                },
                title: {
                    text: 'Total Students Per Section for {{ $selectedYear }}',
                    align: 'center'
                }
            };

            var chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
            chart2.render();
        });

        // DOC REQUESTED
        var options = {
            chart: {
                type: 'donut'
            },
            series: [{{ $form137Count }}, {{ $goodMoralCount }}],
            labels: ['FORM 137', 'GOOD MORAL'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            title: {
                text: 'Total # of Document Requested',
                align: 'center'
            }
        }

        var doc_requested = new ApexCharts(document.querySelector("#doc-requested"), options);
        doc_requested.render();


        // Prepare the data from the PHP variable
        var passFailStats = @json($passFailStats);

        // Extract labels and series data
        var labels = passFailStats.map(function(item) {
            return item.year;
        });

        var totalStudents = passFailStats.map(function(item) {
            return item.total_count;
        });

        var passedStudents = passFailStats.map(function(item) {
            return item.passed_count;
        });

        var failedStudents = passFailStats.map(function(item) {
            return item.failed_count;
        });

        var options = {
            series: [{
                    name: 'Total Students',
                    data: totalStudents
                },
                {
                    name: 'Passed Students',
                    data: passedStudents
                },
                {
                    name: 'Failed Students',
                    data: failedStudents
                }
            ],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    stacked: true,
                    barHeight: '50%',
                }
            },
            dataLabels: {
                enabled: true,
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: labels
            },
            yaxis: {
                title: {
                    text: 'Number of Students'
                },
                labels: {
                    formatter: function(val) {
                        return parseInt(val);
                    }
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return parseInt(val);
                    }
                }
            }
        };

        var passed = new ApexCharts(document.querySelector("#passed"), options);
        passed.render();
    </script>
@endsection
