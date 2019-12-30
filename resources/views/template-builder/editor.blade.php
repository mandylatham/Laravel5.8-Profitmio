<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>PM Template Builder</title>
    <meta name="viewport" content="width=1024, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="canonical" href="http://mosaico.io" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <link type="text/css" rel="stylesheet" href="{{ asset('fonts/font-awesome.css') }}">

    <script src="{{ asset('/js/plugins/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/plugins/knockout.js') }}"></script>
    <script src="{{ asset('/js/plugins/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/js/plugins/jquery-ui.touch-punch.min.js') }}"></script>
    <script src="{{ asset('/js/plugins/knockout-jqueryui.min.js') }}"></script>
    <script src="{{ asset('/js/plugins/load-image.all.min.js') }}"></script>
    <script src="{{ asset('/js/plugins/canvas-to-blob.min.js') }}"></script>
    <script src="{{ asset('/js/plugins/jquery.iframe-transport.js') }}"></script>
    <script src="{{ asset('/js/plugins/jquery.fileupload.js') }}"></script>
    <script src="{{ asset('/js/plugins/jquery.fileupload-process.js') }}"></script>
    <script src="{{ asset('/js/plugins/jquery.fileupload-image.js') }}"></script>
    <script src="{{ asset('/js/plugins/jquery.fileupload-validate.js') }}"></script>
    <script src="{{ asset('/js/plugins/tinymce.min.js') }}"></script>
    <script src="{{ asset('/js/plugins/modern-theme/index.js') }}"></script>
    <script src="{{ asset('/js/plugins/modern-theme/theme.min.js') }}"></script>

    <script src="{{ asset('/js/plugins/mosaico.min.js') }}"></script>

    <script>
        window.createTemplateUrl = @json(route('template-builder.store'));
        window.newTemplateUrl = @json(route('template.create-form'));
        window.listTemplateUrl = @json(route('template.index'));
        window.downloadTemplatePost = @json(route('template-builder.download-post'));
        window.downloadTemplateGet = @json(route('template-builder.download-get'));
    </script>
    <script src="{{ asset('js/template-builder-editor.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('/css/plugins/mosaico-material.min.css') }}?v=0.10" />
    <link rel="stylesheet" href="{{ asset('/css/plugins/notoregular/stylesheet.css') }}" />

    <link href="{{ asset('css/template-builder-editor.css') }}" rel="stylesheet">
</head>
<body class="mo-standalone" >
</body>
</html>
