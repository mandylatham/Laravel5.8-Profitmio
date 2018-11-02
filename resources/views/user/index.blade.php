@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">

        @if(count($users) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{$user->id}}</td>
                    <td>{{$user->name}}</td>
                    <td><a class="btn btn-link" href="{{route('users.edit', ['user' => $user->id])}}">{{ __('Edit') }}</a> </td>
                    <td><a class="btn btn-link" href="{{route('auth.impersonate', ['user' => $user->id])}}">{{ __('Log in as') }}</a> </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif
        <div class="form-group row mb-0">
            <div class="col-md-8 offset-md-4">
                <a class="btn btn-link" href="{{ route('users.create') }}">
                    {{ __('Create new user') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
