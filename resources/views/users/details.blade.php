@extends('layouts.users')

@section('user_content')
    <h5>Total: {{ $campaigns->count() }}</h5>
    @if ($campaigns->count() > 0)
    <table class="table table-condensed table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Client</th>
            <th>Dates</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            @foreach ($campaigns as $campaign)
            <td>
                <a href="{{ secure_url('/campaign/' . $campaign->id) }}" class="btn btn-primary">
                    # {{ $campaign->id }}
                </a>
            </td>
            <td>{{ $campaign->name }}</td>
            <td>{{ $campaign->client->organization }}</td>
            <td>
                @if (! empty($campaign->starts_at))
                    {{ $campaign->starts_at->toFormattedDateString() }} &rarr; {{ $campaign->ends_at->toFormattedDateString() }}
                @else
                    N/a
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
@endsection

@section('foot')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.js"></script>
@endsection
