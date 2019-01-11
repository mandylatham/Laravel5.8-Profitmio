
@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/media-template-create.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.createUrl = "{{ route('template.create') }}";
    </script>
    <script src="{{ asset('js/media-template-create.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="template-create">
        <div class="row no-gutters mt-3">
            <div class="col-12 no-gutters">
                <a href="{{ route('template.index') }}" class="btn btn-outline-primary mb-4">
                    <i class="fas fa-chevron-left mr-1"></i>
                    Back
                </a>
                <media-template-create :url="createUrl"></media-template-create>
            </div>
        </div>
    </div>
@endsection
