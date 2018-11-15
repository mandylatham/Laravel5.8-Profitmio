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
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">

    <script src="{{ secure_url('/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ secure_url('/vendor/knockout/knockout.js') }}"></script>
    <script src="{{ secure_url('/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ secure_url('/vendor/jquery-ui/jquery-ui.touch-punch.min.js') }}"></script>
    <script src="{{ secure_url('/vendor/knockout/knockout-jqueryui.min.js') }}"></script>
    <script src="{{ secure_url('/js/load-image.all.min.js') }}"></script>
    <script src="{{ secure_url('/js/canvas-to-blob.min.js') }}"></script>
    <script src="{{ secure_url('/js/jquery.iframe-transport.js') }}"></script>
    <script src="{{ secure_url('/js/jquery.fileupload.js') }}"></script>
    <script src="{{ secure_url('/js/jquery.fileupload-process.js') }}"></script>
    <script src="{{ secure_url('/js/jquery.fileupload-image.js') }}"></script>
    <script src="{{ secure_url('/js/jquery.fileupload-validate.js') }}"></script>
    <script src="{{ secure_url('/js/tinymce.min.js') }}"></script>
    <script src="{{ secure_url('/js/themes/modern/index.js') }}"></script>
    <script src="{{ secure_url('/js/themes/modern/theme.min.js') }}"></script>

    <script src="{{ secure_url('/js/mosaico/mosaico.min.js') }}"></script>
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (!Mosaico.isCompatible()) {
                alert('This browser is not compatible with the email builder.  Consider updating your browser to the latest version!');
                return;
            }
            // var basePath = window.location.href.substr(0, window.location.href.lastIndexOf('/')).substr(window.location.href.indexOf('/','https://'.length));
            var basePath = window.location.href;
            if (basePath.lastIndexOf('#') > 0) basePath = basePath.substr(0, basePath.lastIndexOf('#'));
            if (basePath.lastIndexOf('?') > 0) basePath = basePath.substr(0, basePath.lastIndexOf('?'));
            if (basePath.lastIndexOf('/') > 0) basePath = basePath.substr(0, basePath.lastIndexOf('/'));

            plugins = [
    // plugin for integrating save button
    function (viewModel) {
        window.viewModel = viewModel;
        var saveCmd = {
            name: 'Save to new template',
            enabled: ko.observable(true)
        };

        var downloadCmd = {
            name: 'Get HTML',
            enabled: ko.observable(true)
        };

        saveCmd.execute = function () {
            console.log('SAVE FIRED');

            saveCmd.enabled(false);
            viewModel.metadata.changed = Date.now();
            if (typeof viewModel.metadata.key == 'undefined') {
                var rnd = Math.random().toString(36).substr(2, 7);
                viewModel.metadata.key = rnd;
            }

            // This is the simplest for sending it as POST
            var postData = {
                csrf_token: '{{ csrf_token() }}', // this is only required if your back-end requires csrf token
                metadata: viewModel.exportMetadata(),
                content: viewModel.exportJSON(),
                html: viewModel.exportHTML()
            };

            $.post('{{ secure_url('template-builder/create') }}', postData)
                .done(function () {
                    viewModel.notifier.success(viewModel.t('Successfully saved.'));
                    setTimeout(function(){ window.location = '{{ secure_url('templates/new') }}'; }, 2000);
                })
                .fail(function (jqXHR, textStatus, error) {
                    console.log(textStatus);
                    console.log(error);
                    console.log(jqXHR);
                    viewModel.notifier.error(viewModel.t('Saving failed. Please try again in a few moments or contact us.'));
                })
                .always(function () {
                    saveCmd.enabled(true);
                });
        };

        downloadCmd.execute = function () {
            downloadCmd.enabled = false;

            // This is the simplest for sending it as POST
            var postData = {
                csrf_token: '{{ csrf_token() }}', // this is only required if your back-end requires csrf token
                metadata: viewModel.exportMetadata(),
                content: viewModel.exportJSON(),
                html: viewModel.exportHTML()
            };

            $.post('{{ secure_url('template-builder/dl') }}', postData)
                .done(function (data) {

                    viewModel.notifier.success(viewModel.t('Successfully saved.'));
                    setTimeout(function(){ window.location = '{{ secure_url('templates/new') }}'; }, 2000);
                })
                .fail(function (jqXHR, textStatus, error) {
                    console.log(textStatus);
                    console.log(error);
                    console.log(jqXHR);
                    viewModel.notifier.error(viewModel.t('Saving failed. Please try again in a few moments or contact us.'));
                })
                .always(function () {
                    saveCmd.enabled(true);
                });
        };

        viewModel.save = saveCmd;
        // viewModel.download = downloadCmd;
    },
];

var ok = Mosaico.start({
    imgProcessorBackend: basePath + '/img/',
    emailProcessorBackend: basePath + '/dl/',
    titleToken: "MOSAICO Responsive Email Designer",
    //onSave: function (saveObject) { alert('hi'); },
    fileuploadConfig: {
        url: basePath + '/upload/'
    }
}, '/emailTemplates/versafix-1/template-versafix-1.html', undefined, undefined, plugins);

            if (!ok) {
                console.log("Missing initialization hash, redirecting to main entrypoint");
                sleep(3);
                document.location = ".";
            }
        });
    </script>

    <link rel="stylesheet" href="{{ secure_url('/css/mosaico-material.min.css') }}?v=0.10" />
    <link rel="stylesheet" href="{{ secure_url('/vendor/notoregular/stylesheet.css') }}" />

    <style type="text/css">
    body {
        background: #333;
    }
    #toolbar {
        background-color: #333;
    }
    #toolbar .ui-button,
    #preview-toolbar .ui-button {
        background-color: #333;
    }
    #main-edit-area:before, #main-edit-area:after, #frame-container:before, #frame-container:after {
        box-shadow: 2px 2px 5px #000;
    }
    </style>
</head>
<body class="mo-standalone" >
</body>
</html>
