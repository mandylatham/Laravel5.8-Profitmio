@extends('layouts.app', ['companyId' => $company->id])

@section('content')
<div class="container">
    <p class="h2">{{$company->name}}</p>
    <p class="h3">{{$campaign->name}}</p>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('User access') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('companies.setcampaignaccess', ['company' => $company->id, 'campaign' => $campaign->id]) }}">
                        @csrf
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Allow</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($company->users as $user)
                                <tr>
                                    <td>{{$user->name}}</td>
                                    <td><input type="checkbox" name="allowedusers[]" {{$user->hasAccessToCampaign($campaign->id) ? 'checked' : ''}} value="{{$user->id}}" /></td>
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
                                    {{ __('Back to campaign') }}
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
