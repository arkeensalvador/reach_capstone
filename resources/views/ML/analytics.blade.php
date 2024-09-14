<!DOCTYPE html>
<html>
<head>
    <title>Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>
    <h1>Analytics Dashboard</h1>
    <div id="prediction-chart"></div>

    <script>
        // Example data - Replace this with actual prediction data if needed
        var options = {
            chart: {
                type: 'bar'
            },
            series: [{
                name: 'Predicted Grade',
                data: [{{ $prediction }}]
            }],
            xaxis: {
                categories: ['Predicted Grade']
            }
        };

        var chart = new ApexCharts(document.querySelector("#prediction-chart"), options);
        chart.render();
    </script>
</body>
</html>
