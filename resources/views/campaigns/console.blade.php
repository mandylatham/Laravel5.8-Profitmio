@extends('layouts.base')

@section('content')
    <campaign-console :campaign="{{ $campaign->toJson() }}" :recipients="{{ $recipients->toJson() }}"
                      :filter="{{ json_encode($filter) }}" :label="{{ json_encode($label) }}"
                      :counters="{{ json_encode($counters) }}"></campaign-console>
@endsection