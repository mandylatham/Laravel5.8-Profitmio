@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/company-create.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.createUrl = "{{ route('company.store') }}";
        window.indexUrl = "{{ route('company.index') }}";
    </script>
    <script src="{{ asset('js/company-create.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="company-create">
        <div class="row mb-3">
            <div class="col-6">
                <a href="{{ route('company.index') }}" type="button" role="button" class="btn pm-btn pm-outline-purple">
                    <i class="fa fa-chevron-left"></i>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="card">
                    <div class="card-header">New Company</div>
                    <div class="card-body">
                        <div class="alert alert-default" v-if="createForm.errors.any()">
                            <p class="text-danger">There are errors!</p>
                        </div>
                        <div class="form-group">
                            <div class="company-image">
                                <input type="file" name="image">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="form-label">Company Name</label>
                            <input type="text" v-model="createForm.name" class="form-control" name="name" placeholder="Company Name" required>
                        </div>
                        <div class="form-group">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" v-model="createForm.type" class="form-control" required>
                                <option value='support'>Support</option>
                                <option value='agency'>Agency</option>
                                <option value='dealership'>Dealership</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" v-model="createForm.phone" class="form-control" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="country" class="form-label">Country</label>
                            <select v-model="createForm.country" class="form-control" name="country">
                                <option value="us">United States</option>
                                <option value="ca">Canada</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="address" class="floating-label">Address</label>
                            <textarea class="form-control" v-model="createForm.address" name="address" placeholder="Address 1"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="address2" class="form-label">Address 2</label>
                            <textarea class="form-control" v-model="createForm.address2" name="address2" placeholder="Address 2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="city" class="form-label">City</label>
                            <input type="text" v-model="createForm.city" class="form-control" name="city" placeholder="City">
                        </div>
                        <div class="form-group">
                            <label for="state" class="form-label">State</label>
                            <input type="text" v-model="createForm.state" class="form-control" name="state" placeholder="State">
                        </div>
                        <div class="form-group">
                            <label for="zip" class="form-label">Zip</label>
                            <input type="text" v-model="createForm.zip" class="form-control" name="zip" placeholder="Zip">
                        </div>
                        <div class="form-group">
                            <label for="url" class="form-label">Url</label>
                            <input type="text" v-model="createForm.url" class="form-control" name="url" placeholder="Url">
                        </div>
                        <div class="form-group">
                            <label for="facebook" class="form-label">Facebook</label>
                            <input type="text" v-model="createForm.facebook" class="form-control" name="facebook" placeholder="Facebook">
                        </div>
                        <div class="form-group">
                            <label for="twitter" class="form-label">Twitter</label>
                            <input type="text" v-model="createForm.twitter" class="form-control" name="twitter" placeholder="Twitter">
                        </div>
                        <button @click="onSubmit()" class="btn pm-btn btn-success">Add Company</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection