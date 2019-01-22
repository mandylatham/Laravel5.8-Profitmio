@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/campaigns-stats.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script src="{{ asset('js/campaigns-stats.js') }}"></script>
@endsection

@section('main-content')
    <div id="campaign-stats"></div>
@endsection
