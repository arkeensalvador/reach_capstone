@extends('layout.layout')
@section('content')
    <section class="content">
        <div class="container-fluid p-0" style="overflow-y: auto">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="card text-bg-info mb-3">
                        <div class="card-body">
                            <h3 class="card-title">150</h3>
                            <p class="card-text">New Requests</p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <span>More info</span>
                            <i class="fas fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="card text-bg-success mb-3">
                        <div class="card-body">
                            <h3 class="card-title">53<sup style="font-size: 20px">%</sup></h3>
                            <p class="card-text">Students</p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <span>More info</span>
                            <i class="fas fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="card text-bg-warning mb-3">
                        <div class="card-body">
                            <h3 class="card-title">44</h3>
                            <p class="card-text">User Registrations</p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <span>More info</span>
                            <i class="fas fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="card text-bg-danger mb-3">
                        <div class="card-body">
                            <h3 class="card-title">65</h3>
                            <p class="card-text">Rejected Requests</p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <span>More info</span>
                            <i class="fas fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            {{-- CHARTS --}}
            <div class="row">
                <div class="col-lg-6">
                    Sample Chart
                    <div id="chart"></div>
                </div>

                <div class="col-lg-6">
                    <div id="chart2"></div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        var options = {
            series: [{
                name: 'Net Profit',
                data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
            }, {
                name: 'Revenue',
                data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
            }, {
                name: 'Free Cash Flow',
                data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            },
            yaxis: {
                title: {
                    text: '$ (thousands)'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "$ " + val + " thousands"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

        var options = {
          series: [44, 55, 13, 43, 22],
          chart: {
          width: 380,
          type: 'pie',
        },
        labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
        };

        var chart2 = new ApexCharts(document.querySelector("#chart2"), options);
        chart2.render();
    </script>
@endsection
