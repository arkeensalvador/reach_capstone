<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pass/Fail Stats</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>
    <div id="chart"></div>

    <script>
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
            series: [
                {
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
                enabled: false
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
                    formatter: function (val) {
                        return parseInt(val);
                    }
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return parseInt(val);
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
</body>
</html>
