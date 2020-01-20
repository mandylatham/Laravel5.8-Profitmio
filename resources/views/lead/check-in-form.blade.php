@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/check-in-form.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.lead = @json($lead);
        window.checkedIn = @json($lead->checkedIn());
        window.saveCheckInFormUrl = @json(route('lead.store-check-in', ['lead' => $lead->id]));
    </script>
    <script src="{{ asset('js/check-in-form.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="check-in" v-cloak>
        <div class="row">
            <div class="col-12 col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <form action="" @submit.prevent="saveForm" novalidate>
                            <h3>Confirm Contact Info</h3>
                            <div class="form-group">
                                <label for="first_name">First name</label>
                                <input type="text" name="first_name" class="form-control"  v-model="leadForm.first_name">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last name</label>
                                <input type="text" name="last_name" class="form-control"  v-model="leadForm.last_name">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control"  v-model="leadForm.email">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" class="form-control"  v-model="leadForm.phone">
                            </div>
                            <hr>
                            <h5>Vehicle</h5>
                            <div class="form-group">
                                <label for="make">Make</label>
                                <input type="text" name="make" class="form-control"  v-model="leadForm.make">
                            </div>
                            <div class="form-group">
                                <label for="year">Year</label>
                                <input type="text" name="year" class="form-control"  v-model="leadForm.year">
                            </div>
                            <div class="form-group">
                                <label for="model">Model</label>
                                <input type="text" name="model" class="form-control"  v-model="leadForm.model">
                            </div>
                            <button type="submit" :disabled="loading" class="btn pm-btn-submit pm-btn pm-btn-purple pm-btn-md mt-4">
                                <span v-if="!loading">Save</span>
                                <div class="loader-spinner" v-if="loading">
                                    <spinner-icon></spinner-icon>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
