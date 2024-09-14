@extends('layout.layout')

@section('content')
    <section class="content">
        <div class="container-fluid p-0">
            <!-- Stat Boxes -->
            <div class="row mb-4">
                @foreach([
                    ['info', 'totalTransaction', 'Total Request'],
                    ['success', 'approvedTransaction', 'Total Approved Requests'],
                    ['danger', 'rejectedTransaction', 'Total Rejected Requests'],
                    ['warning', 'pendingTransaction', 'Total Pending Requests'],
                    ['primary', 'totalStudents', 'Total Students'],
                ] as $stat)
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
                    <div id="chart"></div>
                </div>

                <div class="col-lg-6">
                    <form method="GET" action="{{ route('student.section.counts') }}">
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
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
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
                    categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                },
                title: {
                    text: 'Total Transactions Per Month',
                    align: 'center'
                }
            };

            var chart1 = new ApexCharts(document.querySelector("#chart"), options1);
            chart1.render();

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
    </script>
@endsection
