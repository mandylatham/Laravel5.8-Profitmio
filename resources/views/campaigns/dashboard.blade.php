@extends('layouts.remark_campaign')

@section('header')
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
@endsection

@section('campaign_content')
    <div class="row">
        <div class="col-md-6">
            @if ( isset($emailStats->total) && $emailStats->total > 0 )
            <div class="example-wrap">
                <h4 class="example-title">Email</h4>
                <p>This shows the emails sent over time.</p>
                <div class="example text-center"><iframe class="chartjs-hidden-iframe" tabindex="-1" style="display: block; overflow: hidden; border: 0px; margin: 0px; top: 0px; left: 0px; bottom: 0px; right: 0px; height: 100%; width: 100%; position: absolute; pointer-events: none; z-index: -1;"></iframe>
                    <canvas id="emailChart" height="252" width="379" style="display: block; width: 379px; height: 252px;"></canvas>
                </div>
            </div>
            @else
            <div class="alert alert-info"><p>No email statistics are available</p></div>
            @endif
        </div>
        <div class="col-md-6">
            @if ( isset($responseStats->total) && $responseStats->total > 0 )
            <div class="example-wrap">
                <h4 class="example-title">Responses</h4>
                <p>A display of the number of SMSs sent over time.</p>
                <div class="example text-center"><iframe class="chartjs-hidden-iframe" tabindex="-1" style="display: block; overflow: hidden; border: 0px; margin: 0px; top: 0px; left: 0px; bottom: 0px; right: 0px; height: 100%; width: 100%; position: absolute; pointer-events: none; z-index: -1;"></iframe>
                    <canvas id="responseChart" height="252" width="379" style="display: block; width: 379px; height: 252px;"></canvas>
                </div>
            </div>
            @else
                <div class="alert alert-info"><p>No response statistics are available</p></div>
            @endif
        </div>
    </div>
@endsection

@section('scriptTags')
    <script type="text/javascript" src="{{ secure_url('js/Plugin/panel.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('/vendor/chart-js/Chart.js') }}"></script>
@endsection

@section('scripts')
@if ($emailCount > 0)
var emailStatsCanvas = document.getElementById("emailChart");
var emailStats = new Chart(emailStatsCanvas, {
    type: 'bar',
    data: {
        labels: [
            "Sent ({{ $emailStats->sent }})",
            "Delivered ({{ $emailStats->delivered }})",
            "Opened ({{ $emailStats->opened }})",
            "Clicked ({{ $emailStats->clicked }})",
            "Bounced ({{ $emailStats->bounced }})",
            "Dropped ({{ $emailStats->dropped }})",
            "Unsubscribed ({{ $emailStats->unsubscribed }})"
        ],
        datasets: [{
            label: '# of Emails ({{ $emailStats->total }})',
            data: [
                {{ $emailStats->sent }},
                {{ $emailStats->delivered }},
                {{ $emailStats->opened }},
                {{ $emailStats->clicked }},
                {{ $emailStats->bounced }},
                {{ $emailStats->dropped }},
                {{ $emailStats->unsubscribed }}
            ],
            backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(55, 192, 192, 0.2)',
        ],
        borderColor: [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(55, 192, 192, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
@endif

@if ($responseCount > 0)
var responseStatsCanvas = document.getElementById("responseChart");
var responseStats = new Chart(responseStatsCanvas, {
    type: 'bar',
    data: {
        labels: [
            "Service ({{ $responseStats->service }})",
            "Appointment ({{ $responseStats->appointment }})",
            "Heat ({{ $responseStats->heat }})",
            "Interested ({{ $responseStats->interested }})",
            "Not Interested ({{ $responseStats->not_interested }})",
            "Wrong Number ({{ $responseStats->wrong_number }})",
            "Car Sold ({{ $responseStats->car_sold }})"
        ],
        datasets: [{
            label: '# of Responses ({{ $responseStats->total }})',
            data: [
                {{ $responseStats->service }},
                {{ $responseStats->appointment }},
                {{ $responseStats->heat }},
                {{ $responseStats->interested }},
                {{ $responseStats->not_interested }},
                {{ $responseStats->wrong_number }},
                {{ $responseStats->car_sold }}
            ],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(55, 192, 192, 0.2)',
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(55, 192, 192, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
@endif
@endsection
