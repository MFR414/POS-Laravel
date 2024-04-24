@extends('layouts.master')

@section('content')
   <!-- Main content -->
    <section class="content">
        <div class="main-container">
            <section>
                <div class="d-flex justify-content-start">
                    <div class="title">
                        <br>
                        <h3>Selamat Datang {{ auth()->user()->name}}</h3>
                    </div>
                </div>
            </section>
            <br>
            <section class="section">
                <div class="card">
                    <div class="card-title p-3">
                        <h4>Dashboard</h4>
                    </div>
                </div>
            </section>

            <section class="section">
                <div class="card">
                    <div class="card-title p-3">
                        <h4>Kendaraan Paling Sering Digunakan</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="donutChart" style="max-height: 700px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </section>
        </div>
    </section>
    <script>

        //data from controller
        var vehicle_name = JSON.parse('{!! $vehicleNames !!}');
        var vehicle_order_count = JSON.parse('{!! $vehicleOrderCount !!}');

        console.log(vehicle_name);
        console.log(vehicle_order_count);

        //-------------
        //- DONUT CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
        var donutData = {
            labels: vehicle_name,
            datasets: [
                {
                data: vehicle_order_count,
                backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
                }
            ]
        }
        var donutOptions = {
            maintainAspectRatio : false,
            responsive : true,
        }
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        new Chart(donutChartCanvas, {
            type: 'doughnut',
            data: donutData,
            options: donutOptions
        })
    </script>
@endsection