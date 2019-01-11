@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/media-template-details.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.updateUrl = "{{ route('template.update', ['template' => $template->id]) }}";
        window.deleteUrl = "{{ route('template.delete', ['template' => $template->id]) }}";
        window.template = @json($template);
    </script>
    <script src="{{ asset('js/media-template-details.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="template-details">
        <div class="row no-gutters mt-3">
            <div class="col-12 no-gutters">
                <a href="{{ route('template.index') }}" class="btn btn-outline-primary mb-4">
                    <i class="fas fa-chevron-left mr-1"></i>
                    Back
                </a>
                <media-template-detail :template="template" :url="updateUrl"></media-template-detail>
            </div>
        </div>
    </div>
@endsection
