@extends('layouts.app', ['companyId' => $company->id])

@section('content')
<div class="container">
    <p class="h2">{{$company->name}}</p>
    <p class="h3">{{$user->name}}</p>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Company Access for user') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('companies.setuseraccess', ['company' => $company->id, 'user' => $user->id]) }}">
                        @csrf
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Allow</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($campaigns as $campaign)
                                <tr>
                                    <td>{{$campaign->name}}</td>
                                    <td><input type="checkbox" name="allowedcampaigns[]" {{$user->hasAccessToCampaign($campaign->id) ? 'checked' : ''}} value="{{$campaign->id}}" /></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update') }}
                                </button>
                                <a href="{{route('companies.dashboard', ['company' => $company->id])}}" class="btn btn-dark">
                                    {{ __('Back to compangy') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
