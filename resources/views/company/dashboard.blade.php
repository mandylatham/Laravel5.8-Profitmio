@extends('layouts.app', ['companyId' => $company->id])

@section('content')
<div class="container">
    <p class="h2">{{$company->name}}</p>
    <div class="row justify-content-center">
        @if(!empty($campaigns))
        <div class="card">
            <div class="card-header">{{ __('Campaigns List') }}</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($campaigns as $campaign)
                    <li class="list-group-item">{{$campaign->name}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
