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
    <div class="container" id="company-create" v-cloak>
        <div class="row">
            <div class="col-12 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2 mb-3">
                <a class="btn pm-btn pm-btn-blue go-back" href="{{ route('company.index') }}">
                    <i class="fas fa-arrow-circle-left mr-2"></i> Go Back
                </a>
            </div>
            <div class="col-12 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2">
                <div class="wizard-container">
                    <form-wizard :title="''" :subtitle="''" :step-size="'sm'" :color="'#572E8D'" @on-complete="saveCompany">
                        <tab-content title="Description" icon="fas fa-list-ul" :before-change="validateBasicTab">
                            <div class="image-selector mb-3">
                                <div class="image-preview" v-if="imagePreviewUrl" :style="{backgroundImage: 'url(\'' + imagePreviewUrl + '\')'}">
                                    <span class="image-preview--cancel" @click="removeSelectedImage">
                                        <i class="fas fa-times"></i>
                                    </span>
                                </div>
                                <resumable :target-url="''" v-if="!imagePreviewUrl" ref="resumable" :hide-progress="true" @file-added="onFileAdded">
                                    <template slot="message">Select Image</template>
                                </resumable>
                            </div>
                            <div class="form-group">
                                <label for="name" class="form-label">Company Name</label>
                                <input id="name" type="text" v-model="createForm.name" :class="{ 'form-control': true,  'is-invalid': $v.createForm.name.$error }" name="name" placeholder="Company Name" required>
                                <div class="text-sm mt-2 ml-2 invalid-feedback" v-if="$v.createForm.name.$error">
                                    <div v-if="!$v.createForm.name.required">Name is required</div>
                                    <div v-if="!$v.createForm.name.minLength">Name must be more than 1 character long</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" v-model="createForm.type" :class="{ 'form-control': true, 'is-invalid': $v.createForm.type.$error }" required>
                                    <option value='agency'>Agency</option>
                                    <option value='dealership'>Dealership</option>
                                </select>
                                <div class="text-sm mt-2 ml-2 invalid-feedback" v-if="$v.createForm.type.$error">
                                    <div v-if="!$v.createForm.type.required">Name is required</div>
                                </div>
                            </div>
                        </tab-content>
                        <tab-content title="Contact" icon="fas fa-phone" :before-change="validateContactTab">
                            <div class="form-group">
                                <label for="country" class="form-label">Country</label>
                                <select v-model="createForm.country" :class="{ 'form-control': true, 'is-invalid': $v.createForm.country.$error }" name="country">
                                    <option value="us">United States</option>
                                    <option value="ca">Canada</option>
                                </select>
                                <div class="text-sm mt-2 ml-2 invalid-feedback" v-if="$v.createForm.country.$error">
                                    <div v-if="!$v.createForm.country.required">Country is required</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" v-model="createForm.phone" :class="{ 'form-control': true, 'is-invalid': $v.createForm.phone.$error }" name="phone" required>
                                <div class="text-sm mt-2 ml-2 invalid-feedback" v-if="$v.createForm.phone.$error">
                                    <div v-if="!$v.createForm.phone.required">Phone Number is required</div>
                                    <div v-if="!$v.createForm.phone.isNorthAmericanPhoneNumber">Phone Number must be a valid North American number</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address" class="floating-label">Address</label>
                                <input v-model="createForm.address" :class="{ 'form-control': true, 'is-invalid': $v.createForm.address.$error }" name="address" placeholder="Address 1">
                                <div class="text-sm mt-2 ml-2 invalid-feedback" v-if="$v.createForm.address.$error">
                                    <div v-if="!$v.createForm.address.required">Address is required</div>
                                    <div v-if="!$v.createForm.address.looseAddressMatch">Must be a valid address</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address2" class="form-label">Address 2</label>
                                <input class="form-control" v-model="createForm.address2" name="address2" placeholder="Address 2">
                            </div>
                            <div class="form-group">
                                <label for="city" class="form-label">City</label>
                                <input type="text" v-model="createForm.city" :class="{ 'form-control': true, 'is-invalid': $v.createForm.city.$error }" name="city" placeholder="City">
                                <div class="text-sm mt-2 ml-2 invalid-feedback" v-if="$v.createForm.city.$error">
                                    <div v-if="!$v.createForm.city.required">City is required</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="state" class="form-label">State</label>
                                <input type="text" v-model="createForm.state" :class="{ 'form-control': true, 'is-invalid': $v.createForm.state.$error }" name="state" placeholder="State">
                                <div class="text-sm mt-2 ml-2 invalid-feedback" v-if="$v.createForm.state.$error">
                                    <div v-if="!$v.createForm.state.required">State is required</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="zip" class="form-label">Postal Code</label>
                                <input type="text" v-model="createForm.zip" :class="{ 'form-control': true, 'is-invalid': $v.createForm.zip.$error }" name="zip" placeholder="Zip">
                                <div class="text-sm mt-2 ml-2 invalid-feedback" v-if="$v.createForm.zip.$error">
                                    <div v-if="!$v.createForm.zip.required">Postal Code is required</div>
                                    <div v-if="$v.createForm.zip.isUnitedStatesPostalCode !== undefined && !$v.createForm.zip.isUnitedStatesPostalCode">Must be valid US Postal Code</div>
                                    <div v-if="$v.createForm.zip.isCanadianPostalCode !== undefined && !$v.createForm.zip.isCanadianPostalCode">Must be valid CA Postal Code</div>
                                </div>
                            </div>
                        </tab-content>
                        <tab-content title="Social" icon="fas fa-globe-americas" :before-change="validateSocialTab">
                            <div class="form-group">
                                <label for="url" class="form-label">Url</label>
                                <input type="text" v-model="createForm.url" :class="{ 'form-control': true, 'is-invalid': $v.createForm.url.$error }" name="url" placeholder="Url">
                                <div class="text-sm mt-2 ml-2 invalid-feedback" v-if="$v.createForm.url.$error">
                                    <div v-if="!$v.createForm.url.required">Url is required</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="facebook" class="form-label">Facebook</label>
                                <input type="text" v-model="createForm.facebook" :class="{ 'form-control': true, 'is-invalid': $v.createForm.facebook.$error }" name="facebook" placeholder="Facebook">
                                <div class="text-sm mt-2 ml-2 invalid-feedback" v-if="$v.createForm.facebook.$error">
                                    <div v-if="!$v.createForm.facebook.required">Must be a full Url</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="twitter" class="form-label">Twitter</label>
                                <input type="text" v-model="createForm.twitter" :class="{ 'form-control': true, 'is-invalid': $v.createForm.twitter.$error }" name="twitter" placeholder="Twitter">
                                <div class="text-sm mt-2 ml-2 invalid-feedback" v-if="$v.createForm.twitter.$error">
                                    <div v-if="!$v.createForm.twitter.required">Must be a full Url</div>
                                </div>
                            </div>
                        </tab-content>
                        <template slot="finish">
                            <button type="button" class="wizard-btn" :disabled="isLoading">
                                <span v-if="!isLoading">Finish</span>
                                <spinner-icon :size="'sm'" class="white" v-if="isLoading"></spinner-icon>
                            </button>
                        </template>
                    </form-wizard>
                </div>
           </div>
        </div>
    </div>
@endsection
