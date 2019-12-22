const ko = window.ko;

$(function() {
    const crsfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': crsfToken
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
                    csrf_token: crsfToken,
                    metadata: viewModel.exportMetadata(),
                    content: viewModel.exportJSON(),
                    html: viewModel.exportHTML()
                };

                console.log('postData', postData);

                $.post(window.createTemplateUrl, postData)
                    .done(function () {
                        viewModel.notifier.success(viewModel.t('Successfully saved.'));
                        setTimeout(() => {
                            window.location = window.newTemplateUrl + '?type=email';
                        }, 2000);
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
                    csrf_token: csrfToken,
                    metadata: viewModel.exportMetadata(),
                    content: viewModel.exportJSON(),
                    html: viewModel.exportHTML()
                };

                $.post(window.downloadTemplatePost, postData)
                    .done(function (data) {
                        viewModel.notifier.success(viewModel.t('Successfully saved.'));
                        setTimeout(() => {
                            window.location = window.location = window.listTemplateUrl;
                        }, 2000);
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
        // setTimeout(() => {
        //     document.location = ".";
        // }, 3000);
    }
});
