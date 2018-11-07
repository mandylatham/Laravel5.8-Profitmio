@extends('layouts.app', ['companyId' => $company->id])

@section('content')
<div class="container">
    <p class="h2">{{$company->name}}</p>
    <div class="row ">
        @if(!empty($company->users))
        <div class="card">
            <div class="card-header">{{ __('Users List') }}</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($company->users as $user)
                    <li class="list-group-item">
                        {{$user->name}} ({{$user->pivot->role}})
                        <a href="{{route('companies.useraccess', ['company' => $company->id, 'user' => $user->id])}}">Access</a>
                    </li>
                    @endforeach
                </ul>
                <a class="btn btn-link" href="{{ route('companies.createuser', ['company' => $company->id]) }}">
                    {{ __('Add new user') }}
                </a>
            </div>
        </div>

        @endif
    </div>
    <div class="row">
        @if(!empty($campaigns))
        <div class="card">
            <div class="card-header">{{ __('Campaigns List') }}</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($campaigns as $campaign)
                        <li class="list-group-item">
                            {{$campaign->name}}
                            <a href="{{route('companies.campaignaccess', ['company' => $company->id, 'campaign' => $campaign->id])}}">Access</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
