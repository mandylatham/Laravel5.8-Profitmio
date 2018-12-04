@extends('layouts.console')

@section('header')
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
    <style type="text/css">
        .list-group-item > i.md-circle {
            padding-right: 10px;
        }
        #mailContent {
            min-height: 400px;
        }
    </style>
@endsection

@section('content')
<div class="page bg-white">
    <!-- Mailbox Sidebar -->
    <div class="page-aside">
        <div class="page-aside-switch">
            <i class="icon md-chevron-left" aria-hidden="true"></i>
            <i class="icon md-chevron-right" aria-hidden="true"></i>
        </div>
        <div class="page-aside-inner page-aside-scroll" style="position:relative; background-color: #fff">
            <div data-role="container">
                <div data-role="content">
                    <div class="page-aside-section">
                        <div class="list-group">
                            <a class="list-group-item list-group-item-action
                                {{ $filter == 'all' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/') }}">
                                <span class="badge badge-pill badge-default">{{ $recipients->totalCount }}</span>
                                <i class="icon md-inbox" aria-hidden="true"></i> All
                            </a>
                            <a class="list-group-item
                                {{ $filter == 'unread' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/unread') }}">
                                <span class="badge badge-pill badge-warning">{{ $recipients->unread }}</span>
                                <i class="icon fa-flag" aria-hidden="true"></i> Unread
                            </a>
                            <a class="list-group-item
                                {{ $filter == 'idle' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/idle') }}">
                                <span class="badge badge-pill badge-info">{{ $recipients->idle }}</span>
                                <i class="icon fa-hourglass-half" aria-hidden="true"></i> Idle
                            </a>
                            <!--
                            <a class="list-group-item
                                {{ $filter == 'archived' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/archived') }}">
                                <span class="badge badge-pill badge-success">{{ $recipients->archived }}</span>
                                <i class="icon fa-archive" aria-hidden="true"></i> Archive
                            </a>
                        -->
                        </div>
                    </div>
                    <div class="page-aside-section">
                        <h5 class="page-aside-title">MEDIA</h5>
                        <div class="list-group">
                            <a class="list-group-item
                                {{ $filter == 'calls' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/calls') }}">
                                <span class="badge badge-pill badge-success">{{ $recipients->calls }}</span>
                                <i class="icon fa-phone" aria-hidden="true"></i> Calls
                            </a>
                            <a class="list-group-item
                                {{ $filter == 'email' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/email') }}">
                                <span class="badge badge-pill badge-success">{{ $recipients->email }}</span>
                                <i class="icon fa-envelope" aria-hidden="true"></i> Email
                            </a>
                            <a class="list-group-item
                                {{ $filter == 'sms' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/sms') }}">
                                <span class="badge badge-pill badge-success">{{ $recipients->sms }}</span>
                                <i class="icon fa-comments" aria-hidden="true"></i> SMS
                            </a>
                        </div>
                    </div>
                    <div class="page-aside-section">
                        <h5 class="page-aside-title">LABELS</h5>
                        <div class="list-group">
                            <a class="list-group-item
                                {{ $label == 'none' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/none') }}">
                                No Label
                                <span class="badge badge-pill badge-danger">{{ $recipients->labelCounts->not_labelled }}</span>
                            </a>
                            <a class="list-group-item
                                {{ $label == 'interested' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/interested') }}">
                                <i class="md-circle green-600" aria-hidden="true"></i>
                                Interested
                                <span class="badge badge-pill badge-success">{{ $recipients->labelCounts->interested }}</span>
                            </a>
                            <a class="list-group-item
                                {{ $label == 'appointment' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/appointment') }}">
                                <i class="md-circle green-600" aria-hidden="true"></i>
                                Appointment
                                <span class="badge badge-pill badge-success">{{ $recipients->labelCounts->appointment }}</span>
                            </a>
                            <a class="list-group-item
                                {{ $label == 'service' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/service') }}">
                                <i class="md-circle green-600" aria-hidden="true"></i>
                                Service Dept
                                <span class="badge badge-pill badge-success">{{ $recipients->labelCounts->service }}</span>
                            </a>
                            <a class="list-group-item
                                {{ $label == 'not_interested' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/not_interested') }}">
                                <i class="md-circle red-600" aria-hidden="true"></i>
                                Not Interested
                                <span class="badge badge-pill badge-default">{{ $recipients->labelCounts->not_interested }}</span>
                            </a>
                            <a class="list-group-item
                                {{ $label == 'wrong_number' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/wrong_number') }}">
                                <i class="md-circle red-600" aria-hidden="true"></i>
                                Wrong #
                                <span class="badge badge-pill badge-default">{{ $recipients->labelCounts->wrong_number }}</span>
                            </a>
                            <a class="list-group-item
                                {{ $label == 'car_sold' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/car_sold') }}">
                                <i class="md-circle red-600" aria-hidden="true"></i>
                                Car Sold
                                <span class="badge badge-pill badge-default">{{ $recipients->labelCounts->car_sold }}</span>
                            </a>
                            <a class="list-group-item
                                {{ $label == 'heat' ? 'active' : '' }}"
                               href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/heat') }}">
                                <i class="md-circle red-600" aria-hidden="true"></i>
                                Heat Case
                                <span class="badge badge-pill badge-default">{{ $recipients->labelCounts->heat_case }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mailbox Content -->
    <div class="page-main">
        <!-- Mailbox Header -->
        <div class="page-header">
            <div style="display: flex">
                <button type="button"
                        role="button"
                        data-url="{{ in_array(\Auth::user()->access, ['Agency', 'Client']) ? secure_url('dashboard') : secure_url('/campaign/' . $campaign->id) }}"
                        class="btn btn-sm btn-default waves-effect campaign-edit-button"
                        data-toggle="tooltip"
                        @if ( in_array(\Auth::user()->access, ['Agency', 'Client']))
                        data-original-title="Back to Dashboard"
                        @else
                        data-original-title="Back to Campaign"
                        @endif
                        style="margin-right: 15px; background: rgba(255, 255, 255, 0.2); border-size: 0.5px;">
                    <i class="icon fa-angle-left" style="color: #444" aria-hidden="true"></i>
                </button>
                <h1 class="page-title">Responses</h1>
            </div>
            <div class="page-header-actions">
                <form>
                    <div class="input-search input-search-dark">
                        <i class="input-search-icon md-search" aria-hidden="true"></i>
                        <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ old('search') ?: request('search') }}">
                        @if (old('search') || request('search'))
                        <button type="button" class="input-search-close icon md-close" aria-label="Close"></button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <!-- Mailbox Content -->
        <div id="mailContent" class="page-content page-content-table" data-plugin="selectable">
            <!-- Actions -->
            <div class="page-content-actions">
                <div class="float-right filter">
                    <div class="text-center">{{ $recipients->links(' vendor/pagination/abbreviated') }}</div>
                </div>
                <div class="actions-main">
                    <!--
                    <span class="checkbox-custom checkbox-primary checkbox-lg inline-block vertical-align-bottom">
                        <input type="checkbox" class="mailbox-checkbox selectable-all" id="select_all"
                        />
                        <label for="select_all"></label>
                    </span>
                    -->
                    <div class="btn-group btn-group-flat">
                        <div class="dropdown">
                            <button class="btn btn-icon btn-pure btn-default" data-toggle="dropdown" aria-expanded="false"
                                    type="button"><i class="icon md-folder" aria-hidden="true" data-toggle="tooltip"
                                                     data-original-title="Folder" data-container="body" title=""></i></button>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/') }}">All</a>
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/unread') }}">Unread</a>
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/idle') }}">Idle</a>
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/calls') }}">Calls</a>
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/email') }}">Emails</a>
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/sms') }}">SMS</a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-icon btn-pure btn-default" data-toggle="dropdown" aria-expanded="false"
                                    type="button"><i class="icon md-tag" aria-hidden="true" data-toggle="tooltip"
                                                     data-original-title="Tag" data-container="body" title=""></i></button>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/none') }}">No Label</a>
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/interested') }}">Interested</a>
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/appointment') }}">Appointment</a>
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/service') }}">Service Department</a>
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/not_interested') }}">Not Interested</a>
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/wrong_number') }}">Wrong Number</a>
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/car_sold') }}">Car Sold</a>
                                <a class="dropdown-item" href="{{ secure_url('campaign/' . $campaign->id . '/response-console/labelled/heat') }}">Heat Case</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Mailbox -->
            <div class="mailboxtable-container">
                <table id="mailboxTable" class="table" data-plugin="animateLdata-animate="fade"
                       data-child="tr">
                    <tbody>
                    @if ($recipients->count() > 0)
                    @foreach ($recipients as $recipient)
                        <tr data-url="{{ secure_url('campaign/'. $campaign->id . '/response/' . $recipient->id) }}" data-toggle="slidePanel" data-recipient="{{ $recipient->id }}">
                            <td>
                                <div class="content">
                                    <div class="title">{{ $recipient->name }}</div>
                                    <div class="abstract">{{ $recipient->vehicle }}</div>
                                </div>
                            </td>
                            <td class="responsive-hide">
                                <div class="content">
                                    <div class="">{{ strtolower($recipient->email) }}</div>
                                    <div class="">{{ $recipient->phone }}</div>
                                </div>
                            </td>
                            <td class="cell-30 responsive-hide">
                            </td>
                            <td class="cell-130">
                                <div class="time">{{ $recipient->last_seen ? (new \Carbon\Carbon($recipient->last_seen))->timezone(\Auth::user()->timezone)->diffForHumans(\Carbon\Carbon::now(), true) . ' ago' : '' }}</div>
                                @if ($recipient->interested)
                                <div class="identity">
                                    <span class="badge badge-success">
                                        Interested
                                    </span>
                                </div>
                                @endif
                                @if ($recipient->not_interested)
                                <div class="identity">
                                    <span class="badge badge-danger">
                                        Not Interested
                                    </span>
                                </div>
                                @endif
                                @if ($recipient->appointment)
                                    <div class="identity">
                                    <span class="badge badge-success">
                                        Appointment
                                    </span>
                                    </div>
                                @endif
                                @if ($recipient->wrong_number)
                                <div class="identity">
                                    <span class="badge badge-danger">
                                        Wrong Number
                                    </span>
                                </div>
                                @endif
                                @if ($recipient->service)
                                <div class="identity">
                                    <span class="badge badge-success">
                                        Service Department
                                    </span>
                                </div>
                                @endif
                                @if ($recipient->heat)
                                <div class="identity">
                                    <span class="badge badge-danger">
                                        Heat Case
                                    </span>
                                </div>
                                @endif
                                @if ($recipient->car_sold)
                                <div class="identity">
                                    <span class="badge badge-danger">
                                        Car Sold
                                    </span>
                                </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @else
                        <tr>
                            <td>
                                    <i class="icon fa-fw fa-info-circle" style="margin-right: 10px;"></i>
                                    No Responses Found
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="text-center">{{ $recipients->links(' vendor/pagination/abbreviated') }}</div>
            </div>
        </div>
    </div>
</div>
<!-- Create New Messages Modal -->
<div class="modal fade" id="addMailForm" aria-hidden="true" aria-labelledby="addMailForm"
     role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-hidden="true" data-dismiss="modal">×</button>
                <h4 class="modal-title">Create New Messages</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <select id="topicTo" class="form-control" data-plugin="select2" multiple="multiple"
                                data-placeholder="To:">
                            <optgroup label="">
                                <option value="AK">Alaska</option>
                                <option value="HI">Hawaii</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-group">
                        <select id="topicSubject" class="form-control" data-plugin="select2" multiple="multiple"
                                data-placeholder="Subject:">
                            <optgroup label="">
                                <option value="AK">Alaska</option>
                                <option value="HI">Hawaii</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea name="content" data-provide="markdown" data-iconlibrary="fa" rows="10"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer text-left">
                <button class="btn btn-primary" data-dismiss="modal" type="submit">Send</button>
                <a class="btn btn-sm btn-white btn-pure" data-dismiss="modal" href="javascript:void(0)">Cancel</a>
            </div>
        </div>
    </div>
</div>
<!-- End Create New Messages Modal -->
<!-- Add Label Form -->
<div class="modal fade" id="addLabelForm" aria-hidden="true" aria-labelledby="addLabelForm"
     role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-hidden="true" data-dismiss="modal">×</button>
                <h4 class="modal-title">Add New Label</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <input type="text" class="form-control" name="lablename" placeholder="Label Name"
                        />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-dismiss="modal" type="submit">Save</button>
                <a class="btn btn-sm btn-white btn-pure" data-dismiss="modal" href="javascript:void(0)">Cancel</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptTags')
<script type="text/javascript">
var debug = false;
var current_recipient = 0;
var email_hash = '';
var text_hash = '';

/** Track Selected Recipient **/
$(function() {
    $("tr").click(function() {
        if ($(this).data('recipient')) {
            current_recipient = $(this).data('recipient');
        }
    });

    $(document).on('slidePanel::afterHide', function() {
        current_recipient = 0;
    });

    var refresh_email_messages = function() {
        if (debug) console.log('Updating emails for ' + current_recipient);
        $.post(
            '{{ secure_url("/campaign/{$campaign->id}/responses/") }}' + '/' +
                current_recipient + '/get-email-thread',
            function(data) {
                $(".email-message-container").html(data);
            },
            'html'
        );
    };

    var refresh_text_messages = function() {
        if (debug) console.log('Updating texts for ' + current_recipient);
        $.post(
            '{{ secure_url("/campaign/{$campaign->id}/responses/") }}' + '/' +
                current_recipient + '/get-text-thread',
            function(data) {
                $(".sms-message-container").html(data);
            },
            'html'
        );
    };

    setInterval(function() {
        if (current_recipient != 0) {
            if (debug) console.log('Current recipient is ' + current_recipient);

            if ($(".email-messages").html() !== undefined) {
                $.post(
                    '{{ secure_url("/campaign/{$campaign->id}/responses/") }}' +
                        '/' + current_recipient + '/get-email-hash',
                    function(data) {
                        if (data[0].checksum != email_hash) {
                            email_hash = data[0].checksum;
                            refresh_email_messages();
                        }
                    },
                    'json'
                );
                if (debug) console.log("checking email messages for  " + current_recipient);
            }

            if ($(".sms-messages").html() !== undefined) {
                $.post(
                    '{{ secure_url("/campaign/{$campaign->id}/responses/") }}' +
                        '/' + current_recipient + '/get-text-hash',
                    function(data) {
                        if (data[0].checksum != text_hash) {
                            text_hash = data[0].checksum;
                            refresh_text_messages();
                        }
                    },
                    'json'
                );
                if (debug) console.log("checking sms messages for " + current_recipient);
            }
        }
    }, 2000);

/** Handle Communication Forms **/

    // Disable enter key form submission
    $("body").on('keydown', ".message-field", function(event) {
        var x = event.which;
        if (x === 13) {
            event.preventDefault();
            return false;
        }
    });

    $("body").on('click', ".send-email", function (e) {
        $(".send-email").attr('disabled', 'disabled').addClass('disabled');
        e.preventDefault();
        var postback = $(this).data('postback');

        $.ajax({
            "url": postback,
            "data": {"message": $("#email-message").val()},
            "dataType": "json",
            "method": "post"
        }).done(function (data) {
            refresh_email_messages();
            $(".send-email").attr('disabled', '').removeClass('disabled');
        }).error(function() {
            $(".sms-message-container").append(
                '<div class="alert alert-info"><i class="icon fa-warning"></i> Lost Connection.  Refresh to Retry</div>'
            );
        });
    });

    $("body").on('click', ".send-sms", function (e) {
        $(".send-sms").attr('disabled', 'disabled').addClass('disabled');
        e.preventDefault();
        var postback = $(this).data('postback');

        $.ajax({
            "url": postback,
            "data": {"message": $("#sms-message").val()},
            "dataType": "json",
            "method": "post"
        }).done(function (data) {
            refresh_text_messages();
            $(".send-sms").attr('disabled', '').removeClass('disabled');
        }).error(function() {
            $(".sms-message-container").append(
                '<div class="alert alert-info"><i class="icon fa-warning"></i> Lost Connection.  Refresh to Retry</div>'
            );
        });
    });
});

/** Handle Recipient Labels **/
$(function() {
    $("body").on('click', '.add-label', function (ev) {
        var postback = $(this).parent().data('postback');
        var label_text = $(this).text().trim();

        $.post(
            postback,
            {'label': $(this).data('label')},
            function (data) {
                $(".labels").append(data);
                toastr.success(
                    '"' + label_text + '" label added for ' + $("#recipient-data").data('name'),
                    "Label Added",
                    { positionClass: "toast-bottom-left"}
                );
            }, 'html'
        );
    });

    $("body").on('click', "button.remove-label", function (ev) {
        var postback = $(this).data('postback');
        var label_text = $(this).parent().text().trim();

        $.post(
            postback,
            {'label': $(this).data('label')},
            function (data) {
                $(this).closest("span").remove();
                toastr.success(
                    '"' + label_text + '" label removed for ' + $("#recipient-data").data('name'),
                    "Label Removed",
                    { positionClass: "toast-bottom-left"}
                );
            }
        );

        $(this).closest("span").remove();
    });
});

/** Handle Recipient Notes Form **/
$(function() {
    $("body").on('change', "textarea[name=notes]", function() {
        var postback = $(this).data('postback');

        $.post(
            postback,
            { 'notes': $(this).val() },
            function(data) {
                toastr.success(
                    "Notes for " + $("#recipient-data").data('name') + " updated",
                    "Updated!",
                    { positionClass: "toast-bottom-left"}
                );
            },'json'
        );
    });
});

/** Handle Read Status Form **/
$(function() {
    $("body").on("change", "input.message-read", function() {
        console.log("Read status changed on response " + $(this).data('response_id') + " to " + $(this).prop('checked'));
        var postback = $(this).data('postback');

        $.post(
            postback,
            { 'read': $(this).prop('checked') ? 1 : 0},
            function(data) {
                var text = data.read ? "Read" : "Unread";

                toastr.success(
                    "Read status changed to <em>" +
                        text +
                        "</em> for " +
                        $('#recipient-data').data('name') +
                        "'s response from " +
                        data.created_at,
                    "Marked " + text,
                    { positionClass: "toast-bottom-left"}
                );
            },
            'json'
        );
    });
});

/** Handle Called Status Form **/
$(function() {
    $("body").on("change", ".toggle-called", function(e) {
        var postback = $(this).data('postback');

        $.post(
            postback,
            { 'called_back': $(this).prop('checked') ? 1 : 0},
            function(data) {
                var text = data.read ? "Called" : "Not Called";

                toastr.success(
                    "Appointment status changed to <em>" +
                        text +
                        "</em> for " +
                        $('#response-data').data('name') +
                        "'s via " +
                        $(this).data('phone'),
                    "Marked " + text,
                    { positionClass: "toast-bottom-left"}
                );
            },
            'json'
        );
    });
});

</script>
@endsection
