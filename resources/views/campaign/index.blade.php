@extends('layouts.app')

@section('content')
    <div class="container">
    <div class="row justify-content-center">

        @if(count($campaigns) > 0)
            <table class="table">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Agency</th>
                    <th scope="col">Dealership</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
            @foreach($campaigns as $campaign)
                <tr>
                    <td>{{$campaign->id}}</td>
                    <td>{{$campaign->name}}</td>
                    <td>{{$campaign->agency->name}}</td>
                    <td>{{$campaign->dealership->name}}</td>
                    <td><a class="btn btn-link" href="{{route('campaigns.edit', ['campaign' => $campaign->id])}}">{{ __('Edit') }}</a> </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif
        <div class="form-group row mb-0">
            <div class="col-md-8 offset-md-4">
                <a class="btn btn-link" href="{{ route('campaigns.create') }}">
                    {{ __('Create new campaign') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
