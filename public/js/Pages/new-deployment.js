/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2017 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */
(function(document, window, $) {
    'use strict';

    var Site = window.Site;

    $(document).ready(function($) {
        Site.run();
    });


    // Example Wizard Form Container
    // -----------------------------
    // http://formvalidation.io/api/#is-valid-container
    (function() {
        var defaults = Plugin.getDefaults("wizard");
        var options = $.extend(true, {}, defaults, {
            onInit: function() {
                $('#exampleFormContainer').formValidation({
                    framework: 'bootstrap',
                    fields: {
                        username: {
                            validators: {
                                notEmpty: {
                                    message: 'The username is required'
                                }
                            }
                        },
                        password: {
                            validators: {
                                notEmpty: {
                                    message: 'The password is required'
                                }
                            }
                        },
                        number: {
                            validators: {
                                notEmpty: {
                                    message: 'The credit card number is not valid'
                                }
                            }
                        },
                        cvv: {
                            validators: {
                                notEmpty: {
                                    message: 'The CVV number is required'
                                }
                            }
                        }
                    },
                    err: {
                        clazz: 'text-help'
                    },
                    row: {
                        invalid: 'has-danger'
                    }
                });
            },
            validator: function() {
                var fv = $('#exampleFormContainer').data('formValidation');

                var $this = $(this);

                // Validate the container
                fv.validateContainer($this);

                var isValidStep = fv.isValidContainer($this);
                if (isValidStep === false || isValidStep === null) {
                    return false;
                }

                return true;
            },
            onFinish: function() {
                // $('#exampleFormContainer').submit();
            },
            buttonsAppendTo: '.panel-body'
        });

        $("#exampleWizardFormContainer").wizard(options);
    })();

})(document, window, jQuery);
