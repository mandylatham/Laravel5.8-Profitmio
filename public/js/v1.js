/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2017 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */
$(document).ready(function($) {
  Site.run();

  Waves.attach('.page-content .btn-floating', ['waves-light']);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("button.button-link").click(function(ev) {
        ev.preventDefault();

        if ($(this).data('url')) {
            window.location.href = $(this).data('url');
        }
    });

    $(".campaign-edit-button").click(function() {
        var url = $(this).data("url");
        window.location.href = url;
    });
});
