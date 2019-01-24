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
    </div>
    <div class="container" id="company-create" v-cloak>
        <div class="row">
            <div class="col-12 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2 wizard-container">
                <form-wizard :title="''" :subtitle="''" :step-size="'sm'" :color="'#572E8D'" @on-complete="saveCompany">
                    <tab-content title="Description" icon="fas fa-list-ul" :before-change="validateBasicTab">
                        <div class="form-group">
                            <div class="company-image">
                                <input type="file" name="image">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="form-label">Company Name</label>
                            <input type="text" v-model="createForm.name" class="form-control" name="name" placeholder="Company Name" required>
                            <input-errors :error-bag="createForm.errors" :field="'name'"></input-errors>
                        </div>
                        <div class="form-group">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" v-model="createForm.type" class="form-control" required>
                                <option value='support'>Support</option>
                                <option value='agency'>Agency</option>
                                <option value='dealership'>Dealership</option>
                            </select>
                        </div>
                    </tab-content>
                    <tab-content title="Contact" icon="fas fa-phone" :before-change="validateContactTab">
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" v-model="createForm.phone" class="form-control" name="phone" required>
                            <input-errors :error-bag="createForm.errors" :field="'phone'"></input-errors>
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
                            <input class="form-control" v-model="createForm.address" name="address" placeholder="Address 1">
                            <input-errors :error-bag="createForm.errors" :field="'address'"></input-errors>
                        </div>
                        <div class="form-group">
                            <label for="address2" class="form-label">Address 2</label>
                            <input class="form-control" v-model="createForm.address2" name="address2" placeholder="Address 2">
                            <input-errors :error-bag="createForm.errors" :field="'address2'"></input-errors>
                        </div>
                        <div class="form-group">
                            <label for="city" class="form-label">City</label>
                            <input type="text" v-model="createForm.city" class="form-control" name="city" placeholder="City">
                            <input-errors :error-bag="createForm.errors" :field="'city'"></input-errors>
                        </div>
                        <div class="form-group">
                            <label for="state" class="form-label">State</label>
                            <input type="text" v-model="createForm.state" class="form-control" name="state" placeholder="State">
                            <input-errors :error-bag="createForm.errors" :field="'state'"></input-errors>
                        </div>
                        <div class="form-group">
                            <label for="zip" class="form-label">Zip</label>
                            <input type="text" v-model="createForm.zip" class="form-control" name="zip" placeholder="Zip">
                            <input-errors :error-bag="createForm.errors" :field="'zip'"></input-errors>
                        </div>
                    </tab-content>
                    <tab-content title="Social" icon="fas fa-globe-americas" :before-change="validateSocialTab">
                        <div class="form-group">
                            <label for="url" class="form-label">Url</label>
                            <input type="text" v-model="createForm.url" class="form-control" name="url" placeholder="Url">
                            <input-errors :error-bag="createForm.errors" :field="'url'"></input-errors>
                        </div>
                        <div class="form-group">
                            <label for="facebook" class="form-label">Facebook</label>
                            <input type="text" v-model="createForm.facebook" class="form-control" name="facebook" placeholder="Facebook">
                            <input-errors :error-bag="createForm.errors" :field="'facebook'"></input-errors>
                        </div>
                        <div class="form-group">
                            <label for="twitter" class="form-label">Twitter</label>
                            <input type="text" v-model="createForm.twitter" class="form-control" name="twitter" placeholder="Twitter">
                            <input-errors :error-bag="createForm.errors" :field="'twitter'"></input-errors>
                        </div>
                    </tab-content>
                </form-wizard>
           </div>
        </div>
    </div>
@endsection