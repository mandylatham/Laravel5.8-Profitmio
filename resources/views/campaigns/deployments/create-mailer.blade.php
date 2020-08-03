@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/mailer-create.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.dropsIndexUrl = "{{ route('campaigns.drops.index', ['campaign' => $campaign->id]) }}";
        window.saveMailerDropUrl = "{{ route('campaigns.drops.store-mailer', ['campaign' => $campaign->id]) }}";
    </script>
    <script src="{{ asset('js/mailer-create.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="mailer-create" v-cloak>
        <div class="row">
            <div class="col-12 col-md-6 offset-md-3">
                <a class="btn pm-btn pm-btn-blue mb-3 mt-4" href="{{ route('campaigns.drops.index', ['campaign' => $campaign->id]) }}">
                    <i class="fas fa-arrow-circle-left mr-2"></i> Go Back
                </a>
                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-4">New Mailer</h2>
                        <label>Mailer Image:</label>
                        <resumable :target-url="uploadImageUrl" :file-type="fileTypes" ref="resumable" @file-added="onImageSelected" @file-error="onFileError" @file-success="onFileSuccess" :hide-progress="true" :class="{'is-invalid': dropForm.errors.has('image')}">
                            <template slot="message">
                                Choose an image
                            </template>
                        </resumable>
                        <input-errors :error-bag="dropForm.errors" :field="'image'"></input-errors>
                        <form action="" @submit.prevent="saveDrop">
                            <div class="form-group row mt-4">
                                <label for="email" class="col-xs-12 col-md-3 col-form-label">In-home Date: </label>
                                <div class="col-xs-12 col-md-8">
                                    <date-picker v-model="dropForm.send_at" @change="clearError('send_at')" lang="en" type="date" format="MM/DD/YYYY" :value-type="'format'" :class="{'is-invalid': dropForm.errors.has('send_at')}"></date-picker>
                                    <input-errors :error-bag="dropForm.errors" :field="'send_at'"></input-errors>
                                </div>
                            </div>
                            <button type="submit" :disabled="loading" class="btn pm-btn-submit pm-btn pm-btn-purple mt-2">
                                <span v-if="!loading"><i class="fas fa-save mr-2"></i>Save</span>
                                <spinner-icon :size="'sm'" class="white" v-if="loading"></spinner-icon>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
