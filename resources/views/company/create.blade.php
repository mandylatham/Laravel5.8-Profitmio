@extends('layouts.remark')

@section('header')
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/dropify/dropify.css') }}">
@endsection

@section('manualStyle')
    .company-image {
    width: 220px;
    margin: 0 auto;
    }
@endsection

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-md-6 offset-md-3">
                    <button type="button" role="button"
                            data-url="{{ route('company.index') }}"
                            class="btn btn-sm float-left btn-default waves-effect campaign-edit-button"
                            data-toggle="tooltip" data-original-title="Go Back"
                            style="margin-right: 15px; background: rgba(255, 255, 255, 0.2); border-size: 0.5px;">
                        <i class="icon fa-angle-left" style="color: #efefef" aria-hidden="true"></i>
                    </button>
                    <h3 class="page-title text-default">
                        New Company
                    </h3>
                    <div class="page-header-actions">
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="row-fluid" data-plugin="matchHeight" data-by-row="true">
                <div class="col-md-6 offset-md-3">
                    <div class="panel">
                        <div class="panel-body" data-fv-live="enabled">
                            @if ($errors->count() > 0)
                                <div class="alert alert-danger">
                                    <h3>There were some errors:</h3>
                                    <ul>
                                        @foreach ($errors->all() as $message)
                                            <li>{{ $message }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form class="form" method="post" action="{{ route('company.store') }}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <div class="company-image">
                                        <input type="file" name="image" data-plugin="dropify" class="dropify"
                                               data-height="200" data-width="200"
                                               data-allowed-file-extensions="jpg jpeg png gif"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="floating-label">Company Name</label>
                                    <input type="text" class="form-control empty" name="name" placeholder="Company Name"
                                           value="{{ old('name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="type" class="floating-label">Type</label>
                                    <select name="type" class="form-control" data-fv-field="type" required>
                                        <option value='support' {{ old('type') === 'support' ? 'selected' : '' }}>
                                            Support
                                        </option>
                                        <option value='agency' {{ old('type') == 'agency' ? 'selected' : '' }}>Agency
                                        </option>
                                        <option value='dealership' {{ old('type') == 'dealership' ? 'selected' : '' }}>
                                            Dealership
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="phone" class="floating-label">Phone</label>
                                    <input type="text" class="form-control empty" name="phone" autocomplete="off"
                                           value="{{ old('phone') }}" data-plugin="formatter"
                                           data-pattern="([[999]]) [[999]]-[[9999]]" required>
                                </div>
                                <div class="form-group">
                                    <label for="address" class="floating-label">Address 1</label>
                                    <textarea class="form-control empty" name="address" placeholder="Address 1"
                                              autocomplete="off">{{ old('address') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="address2" class="floating-label">Address 2</label>
                                    <textarea class="form-control empty" name="address2" placeholder="Address 2"
                                              autocomplete="off">{{ old('address2') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="city" class="floating-label">City</label>
                                    <input type="text" class="form-control empty" name="city" placeholder="City"
                                           value="{{ old('city') }}">
                                </div>
                                <div class="form-group">
                                    <label for="state" class="floating-label">State</label>
                                    <input type="text" class="form-control empty" name="state" placeholder="State"
                                           value="{{ old('state') }}">
                                </div>
                                <div class="form-group">
                                    <label for="country" class="floating-label">Country</label>
                                    <input type="text" class="form-control empty" name="country" placeholder="Country"
                                           value="{{ old('country') }}">
                                </div>
                                <div class="form-group">
                                    <label for="zip" class="floating-label">Zip</label>
                                    <input type="text" class="form-control empty" name="zip" placeholder="Zip"
                                           value="{{ old('zip') }}">
                                </div>
                                <div class="form-group">
                                    <label for="url" class="floating-label">Url</label>
                                    <input type="text" class="form-control empty" name="url" placeholder="Url"
                                           value="{{ old('url') }}">
                                </div>
                                <div class="form-group">
                                    <label for="facebook" class="floating-label">Facebook</label>
                                    <input type="text" class="form-control empty" name="facebook" placeholder="Facebook"
                                           value="{{ old('facebook') }}">
                                </div>
                                <div class="form-group">
                                    <label for="twitter" class="floating-label">Twitter</label>
                                    <input type="text" class="form-control empty" name="twitter" placeholder="Twitter"
                                           value="{{ old('twitter') }}">
                                </div>
                                <button type="submit" class="btn btn-success">Add Company</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptTags')
    <script src="{{ secure_url('vendor/dropify/dropify.js') }}"></script>
    <script src="{{ secure_url('js/Plugin/formatter.js') }}"></script>
@endsection

@section('scripts')
    $('.dropify').dropify({
    messages: {
    'default': 'Drag and drop a file here or click to upload an image'
    }
    });
@endsection
