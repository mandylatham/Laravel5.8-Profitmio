@extends('layouts.remark_campaign')

@section('header')
    <link type="text/css" rel="stylesheet" href="{{ secure_url('css/sweetalert.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/icheck/icheck.css') }}">
    <script type="text/javascript" src="{{ secure_url('/vendor/chart-js/Chart.js') }}"></script>
@endsection

@section('manualStyle')
    .upload-area {
        display: flex;
        align-items: center;
        width: 100%;
        min-height: 90px;
        border: 3px dashed #e0e0e0;
    }
    .upload-area > h4 {
        margin-left: auto;
        margin-right: auto;
        color: #888;
    }
    .jsgrid-grid-header.jsgrid-header-scrollbar {
        overflow: scroll;
    }
    #resumable-drop {
        height: 250px;
        width: 100%;
        border: 3px dashed #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        z-index: 3000;
    }
    .upload-zone div *{
        font-size: 2em;
        color: #e0e0e0;
        font-weight: bold;
        z-index: 500;
    }
    .upload-form-card {
        border: 1px solid #3f51b5;
        margin-top: 20px;
    }
    table#field-list thead th.header,
    table#field-list tbody th {
        width: 140px;
    }
    .list-details > div {
        display: flex;
        justify-content: flex-start;
        align-content: space-around;
    }
    .list-details > div > .list-count {
        width: 50%;
    }
    .list-details > div > .list-count.count {
        width: 50%;
    }
@endsection

@section('campaign_content')
        <div class="col-md-12">
            @if (!$campaign->isExpired())
            <button class="pull-right btn btn-success upload-list-button mb-3" style="margin-top: -10px;">Upload New List</button>
            @endif
            <div style="margin-bottom: 20px;">
                <p class="h3">
                    Recipient Lists
                </p>
            </div>
            @if (!$campaign->isExpired())
            <div class="card border border-primary upload-form-card">
                <div class="card-block">
                    <h4 class="card-title">New Recipient List</h4>
                    <div class="card-text container">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div id="resumable-error" style="display:none;">
                            Upload Library not supported
                        </div>
                        <div id="resumable-drop" style="display:none; margin-bottom: 14px;">
                            <p>
                                <button id="resumable-browse"
                                        style="margin-right: 4px;"
                                        data-url="{{ url('/campaign/'.$campaign->id.'/recipient-list/upload') }}" }}>
                                    Browse for the file
                                </button>
                            </p>
                            <p> or Drag and Drop the file here</p>
                        </div>
                        <ul id="file-upload-list" class="list-unstyled" style="display:none">
                            @if (old('uploaded_file_name'))
                                <li>{{ old('uploaded_file_name') }}</li>
                            @endif
                        </ul>
                        <form action="{{ secure_url('campaign/' . $campaign->id . '/recipients/upload') }}"
                              method="post" id="file-attributes-form">
                            <input type="hidden" name="uploaded_file_name" value="{{ old('uploaded_file_name') }}">
                            <input type="hidden" name="uploaded_file_headers" value="{{ old('uploaded_file_headers') }}">
                            <input type="hidden" name="uploaded_file_fieldmap" value="{{ old('uploaded_file_fieldmap') }}">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-12">
                                    <input type="text" name="pm_list_name" class="form-control" placeholder="List Name"
                                        value="{{ old('pm_list_name') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="pm_list_type" class="" value="all_conquest"
                                                  {{ (old('pm_list_type') == 'all_conquest' ? 'checked="checked"' : '') }}>
                                            All Conquest
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="pm_list_type" class="" value="all_database"
                                                    {{ (old('pm_list_type') == 'all_database' ? 'checked="checked"' : '') }}>
                                            All Database
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="pm_list_type" class="" value="use_recipient_field"
                                            {{ ((old('pm_list_type') == 'use_recipient_field' || old('pm_list_type') == null) ? 'checked="checked"' : '') }}>
                                            Mix - Use CSV Field
                                        </label>
                                    </div>
                                    <div class="alert alert-info">
                                        <h3>Notice</h3>
                                        <p>When using the CSV Field, that field must have a letter "D" to signify the row is sourced from the dealership's database.  Any other value will cause the row to be considered as a Conquest sourced row.</p>
                                    </div>
                                </div>
                                <div class="col-lg-9 col-md-6 col-sm-12">
                                    <h4>Map Your Fields</h4>
                                    <table id="field-list" class="table" style="border:0" cellpadding="0" cellspacing="0">
                                        <thead>
                                        <tr>
                                            <th class="header">PM Field</th>
                                            <th>File Field</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <th>First Name</th>
                                            <td><select name="first_name" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        <tr>
                                            <th>Last Name</th>
                                            <td><select name="last_name" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td><select name="email" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td><select name="phone" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td><select name="address1" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        <tr>
                                            <th>City</th>
                                            <td><select name="city" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        <tr>
                                            <th>State</th>
                                            <td><select name="state" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        <tr>
                                            <th>Zip</th>
                                            <td><select name="zip" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        <tr>
                                            <th>Auto Year</th>
                                            <td><select name="year" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        <tr>
                                            <th>Auto Make</th>
                                            <td><select name="make" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        <tr>
                                            <th>Auto Model</th>
                                            <td><select name="model" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        <tr>
                                            <th>Auto VIN</th>
                                            <td><select name="vin" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        <tr id="use_csv_database_field">
                                            <th>Is From Database</th>
                                            <td><select name="is_database" class="fieldmap-fields"><option></option></select></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <button class="btn btn-primary disabled submit-new-list-button" disabled="disabled">Submit List</button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            @if ($lists->count() > 0)
                @foreach ($lists as $list)
                <div class="panel" style="margin-top: 4px;">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-4">
                                <h3 class="mb-0">
                                    <i class="icon fa fa-list"></i>
                                    {{ $list->name ?: "&lt;Unnamed&gt;" }} List
                                </h3>
                                <p class="mt-0"
                                   style="margin-left:42px;
                                   font-family: monospace;">
                                    List File Id: {{ $list->id }}
                                </p>
                            @if ($list->recipients_added)
                                <a href="{{ route("recipient-list.show", [$campaign->id, $list->id]) }}" class="btn btn-outline btn-outline-info">Modify Members</a>
                            @endif
                            @if ($list->recipients_added or $list->failed_at)
                                <button data-url="{{ route("recipient-list.delete-stats", [$campaign->id, $list->id]) }}" class="delete-this-recipient-list btn btn-outline btn-outline-danger">Delete List</button>
                            @endif
                            </div>
                            @if ($list->recipients_added)
                            <div class="col-md-3 col-sm-4 list-details">
                                <div class="text-primary">
                                    <div class="list-count"><i class="ml-2 mr-2 icon fa fa-users"></i> Total</div>
                                    <div class="list-count count">{{ $list->recipients->count() }}</div>
                                </div>
                                <div>
                                    <div class="list-count"><i class="ml-2 mr-2 icon fa fa-envelope"></i> Emails</div>
                                    <div class="list-count count">{{ $list->withEmails() }}</div>
                                </div>
                                <div>
                                    <div class="list-count"><i class="ml-2 mr-2 icon fa fa-phone"></i> Phones</div>
                                    <div class="list-count count">{{ $list->withPhones() }}</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-4 list-details">
                                <div>
                                    <div class="list-count"><i class="ml-2 mr-2 icon fa fa-map"></i> Conquest</div>
                                    <div class="list-count count">{{ $list->fromConquest(false) }}</div>
                                </div>
                                <div>
                                    <div class="list-count"><i class="ml-2 mr-2 icon fa fa-database"></i> Dealer DB</div>
                                    <div class="list-count count">{{ $list->fromDealerDb(true) }}</div>
                                </div>
                            </div>
                            @else
                            <div class="col-md-6 col-sm-8">
                                @if ($list->failed_at)
                                    <div><h4 class="text-danger">List failed to load: </h4><p class="text-danger">{{ $list->failed_reason }}</p></div>
                                @else
                                    <div class="alert"><i class="icon fa-spinner fa-spin "></i> <strong>Loading Recipients...</strong> <i>(refresh to update)</i></div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="panel-footer">
                    </div>
                </div>
                @endforeach
            @endif
        </div>
@endsection

@section('scriptTags')
    <script type="text/javascript" src="{{ secure_url('js/Plugin/panel.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('js/Plugin/sweetalert.min.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('js/Plugin/papaparse.min.js') }}"></script>
    <script src="{{ secure_url('js/Plugin/icheck.js') }}"></script>
    <script src="{{ secure_url('vendor/icheck/icheck.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/resumable/resumable.js') }}"></script>
    <script type="text/html" id="fieldmap-table-group">
        <table id="fieldmap-table" class="table">
            <thead></thead>
            <tbody></tbody>
        </table>
    </script>
    <script type="text/html" id="field-select">
        <select name="__NAME__" class="form-control map-field">
            <option></option>
            @foreach (\App\Models\Recipient::$mappable as $field)
                <option value="{{ $field }}">{{ \Illuminate\Support\Str::studly($field) }}</option>
            @endforeach
            <option value="is_database">from Dealer Database</option>
        </select>
    </script>
    <script type="text/html" id="delete-form-partial">
        <div class="delete-form card card-bordered">
            <div class="card-block">
                __ALERT__
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <th>Total recipients</th>
                        <td>__TOTAL__</td>
                    </tr>
                    <tr>
                        <th>In Drops</th>
                        <td>__IN_DROPS__</td>
                    </tr>
                    <tr>
                        <th>Sent media from drop<br><i>(can't be deleted)</i></th>
                        <td>__DROPPED__</td>
                    </tr>
                    <tr>
                        <th>Total to be deleted</th>
                        <td>__DELETABLE__</td>
                    </tr>
                    </tbody>
                </table>
                <a href="__URL__" class="btn btn-raised btn-danger">I Accept</a>
                <button onClick="removeDeleteForm(this)" class="btn btn-raised waves-button delete-cancel">I Decline</button>
            </div>
        </div>
    </script>
    <script type="text/javascript">
    var removeDeleteForm = function (elem) {
        $(elem).parent().parent().slideUp();
        setTimeout(function (elem) {
            $(elem).parent().parent().remove();
        }, 500);
    };

    $(document).ready(function() {
        $(".switch-to-search-mode").hide();
        @if (! $errors->any())
        $(".upload-form-card").hide();
        $("#resumable-drop").hide();
        $("#file-upload-list").show();
        $("#file-attributes-form").hide();
        @endif
        $(".upload-list-button").on('click', function(e) {
            $(".upload-form-card").slideToggle();
        });
        $(".upload-zone").on('click', function(e) {
            $("[name=recipient_csv]").click();
        });

        $(".panel-footer").hide();
        $(".delete-cancel").on('click', function () {
            $(this).parent().empty().hide();
        });

        var getFieldmapFields = function () {
            var fields = new Object();
            $("select.fieldmap-fields:visible").each(function (ord, field) {
               if ($(field).children("option:selected").val() != "") {
                   var keyname = $(field).attr("name");
                   var value = $(field).children("option:selected").val();
                   fields[keyname] = value;
               }
            });
            return JSON.stringify(fields);
        };

        $(".submit-new-list-button").click(function (e) {
            e.preventDefault();
            $("[name=uploaded_file_fieldmap]").val(getFieldmapFields());
            $("#field-list").remove();
            $(this).parent("form").submit();
        });

        $(".delete-this-recipient-list").on('click', function () {
            var $footer = $(this).parent().parent().parent().parent().children('.panel-footer');

            $.post($(this).data('url'),
                function (response) {
                    console.log(response);
                    var template = $("#delete-form-partial").html();
                    template = template.replace('__TOTAL__', response.total);
                    template = template.replace('__IN_DROPS__', response.inDrops);
                    template = template.replace('__DROPPED__', response.dropped);
                    template = template.replace('__DELETABLE__', response.deletable);
                    template = template.replace('__URL__', response.delete_url);
                    if (response.inDrops > 0) {
                        template = template.replace('__ALERT__',
                            '<div class="alert alert-alt alert-danger">' +
                            '<p><span class="mr-2"><strong>STOP:</strong></span> There are recipients in this list' +
                            "who cannot be removed because they've already been sent a drop.</p></div>");
                    } else {
                        template = template.replace('__ALERT__', '');
                    }
                    $footer.empty();
                    $footer.html(template);
                },
                'json'
            );

            $footer.show();
        });

        var populateFieldmapSelects = function (headers) {
            $("select.fieldmap-fields").html("<option></option>");
            var template = $('#field-select').html();
            $("select.fieldmap-fields").each(function (ord, field) {
                $(headers).each(function (ord, header) {
                    var options = '';
                    if (field.name == header) {
                        options += '<option selected="selected">';
                    } else {
                        options += '<option>';
                    }
                    options += header + "</option>";
                    $("select[name="+field.name+"]").append(options);
                });
            });
        };

        if ("{{ old('uploaded_file_name') }}".length > 0) {
            $('#resumable-drop').hide();
            // Re-populate the fieldmap dropdowns
            var headers = "{{ old('uploaded_file_headers') }}".split(',');
            populateFieldmapSelects(headers);
        }

        if ($("[name=pm_list_type]:checked").val() == "use_recipient_field") {
            $("#use_csv_database_field").show();
        }

        $("[name=pm_list_type]").on('change', function (e) {
            if ($("[name=pm_list_type]:checked").val() == "use_recipient_field") {
                $("#use_csv_database_field").show();
            } else {
                $("#use_csv_database_field").hide();
            }
        });

        /**
         * Setup a check for whether the form is ready to be submitted
         */
        var formButtonToggle = function () {
            console.log('checking form elements');
            var f = !! $("[name=uploaded_file_name]").val().length;
            var n = !! $("[name=pm_list_name]").val().length;
            var t = !! $("[name=pm_list_type]:checked").val().length;

            if (f && n && t) {
                $('.submit-new-list-button').removeClass('disabled');
                $('.submit-new-list-button').attr('disabled', '');
                $('.submit-new-list-button').removeAttr('disabled');
            } else {
                if (! $('.submit-new-list-button').hasClass('disabled')) {
                    $('.submit-new-list-button').addClass('disabled');
                    $('.submit-new-list-button').attr('disabled', 'disabled');
                }
            }
        };
        $("form *").on('focusout', function () {
            formButtonToggle();
        });
        $("form input").on('keyup', function () {
            formButtonToggle();
        });

        var uploadCompleteActions = function (file, response) {
            $("#file-attributes-form").show();
            $("[name=uploaded_file_name]").val(response.name);
            $("[name=uploaded_file_headers]").val(response.headers);
            $('#resumable-drop').hide();
            populateFieldmapSelects(response.headers);
        };

        @if (! $errors->any())
        var $fileUpload = $('#resumable-browse');
        var $fileUploadDrop = $('#resumable-drop');
        var $uploadList = $("#file-upload-list");

        if ($fileUpload.length > 0 && $fileUploadDrop.length > 0) {
            var resumable = new Resumable({
                // Use chunk size that is smaller than your maximum limit due a resumable issue
                // https://github.com/23/resumable.js/issues/51
                chunkSize: 1 * 1024 * 1024, // 1MB
                simultaneousUploads: 3,
                testChunks: false,
                throttleProgressCallbacks: 1,
                // Get the url from data-url tag
                target: $fileUpload.data('url'),
                // Append token to the request - required for web routes
                query:{_token : $('input[name=_token]').val()}
            });

            // Resumable.js isn't supported, fall back on a different method
            if (!resumable.support) {
                $('#resumable-error').show();
            } else {
                // Show a place for dropping/selecting files
                $fileUploadDrop.show();
                resumable.assignDrop($fileUpload[0]);
                resumable.assignBrowse($fileUploadDrop[0]);

                // Handle file add event
                resumable.on('fileAdded', function (file) {
                    // Show progress bar
                    $uploadList.show();
                    // Show pause, hide resume
                    $('.resumable-progress .progress-resume-link').hide();
                    $('.resumable-progress .progress-pause-link').show();
                    // Add the file to the list
                    $uploadList.append('<li class="resumable-file-' + file.uniqueIdentifier + '">Uploading <span class="resumable-file-name"></span> <span class="resumable-file-progress"></span>');
                    $('.resumable-file-' + file.uniqueIdentifier + ' .resumable-file-name').html(file.fileName);
                    // Actually start the upload
                    resumable.upload();
                });
                resumable.on('fileSuccess', function (file, message) {
                    // Reflect that the file upload has completed
                    $('.resumable-file-' + file.uniqueIdentifier + ' .resumable-file-progress').html('(completed)');
                    var response = JSON.parse(message);
                    uploadCompleteActions(file, response);
                    formButtonToggle();
                });
                resumable.on('fileError', function (file, message) {
                    // Reflect that the file upload has resulted in error
                    $('.resumable-file-' + file.uniqueIdentifier + ' .resumable-file-progress').html('(file could not be uploaded: ' + message + ')');
                });
                resumable.on('fileProgress', function (file) {
                    // Handle progress for both the file and the overall upload
                    $('.resumable-file-' + file.uniqueIdentifier + ' .resumable-file-progress').html(Math.floor(file.progress() * 100) + '%');
                    $('.progress-bar').css({width: Math.floor(resumable.progress() * 100) + '%'});
                });
            }
        }
        @endif

    });
    </script>
@endsection

@section('scripts')

@endsection
