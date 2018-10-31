@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create Campaign') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('campaigns.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-sm-4 col-form-label text-md-right">{{ __('Campaign name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required>

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="type" class="col-md-4 col-form-label text-md-right">{{ __('Agency') }}</label>

                            <div class="col-md-6">
                                <select id="agency_id" class="form-control{{ $errors->has('agency_id') ? ' is-invalid' : '' }}" name="agency_id" required>
                                    @foreach($agencies as $agency)
                                    <option value="{{$agency->id}}" {{ old('$agency')==$agency->id ? 'selected' : '' }}>{{$agency->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('agency_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('agency_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="type" class="col-md-4 col-form-label text-md-right">{{ __('Dealership') }}</label>

                            <div class="col-md-6">
                                <select id="dealership_id" class="form-control{{ $errors->has('dealership_id') ? ' is-invalid' : '' }}" name="dealership_id" required>
                                    @foreach($dealerships as $dealership)
                                    <option value="{{$dealership->id}}" {{ old('$agency')==$dealership->id ? 'selected' : '' }}>{{$dealership->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('dealership_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('dealership_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Create') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
