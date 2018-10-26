@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">

        @if(count($companies) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($companies as $company)
                <tr>
                    <td>{{$company->id}}</td>
                    <td>{{$company->name}}</td>
                    <td><a class="btn btn-link" href="{{route('companies.edit', ['company' => $company->id])}}">{{ __('Edit') }}</a> </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif
        <div class="form-group row mb-0">
            <div class="col-md-8 offset-md-4">
                <a class="btn btn-link" href="{{ route('companies.create') }}">
                    {{ __('Create new company') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
