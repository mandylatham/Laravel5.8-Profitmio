@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/deployments-details.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.campaign = @json($campaign);
        window.drop = @json($drop);
        window.recipients = @json($recipients);
        window.sendSms = "{{ route('campaigns.drops.send-sms', [
            'campaign' => $campaign->id,
            'drop' => $drop->id,
            'recipient' => ':recipientId'
        ]) }}";
    </script>
    <script src="{{ asset('js/deployments-details.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="deployments-details" v-cloak>
        <a class="btn pm-btn pm-btn-blue go-back mb-3 mt-3" href="{{ route('campaigns.drops.index', ['campaign' => $campaign->id, 'drop' => $drop->id]) }}">
            <i class="fas fa-arrow-circle-left mr-2"></i> Go Back
        </a>
        <div class="details-container">
            @if ($campaign->phones()->whereCallSourceName('sms')->count() == 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Cannot send SMS
                    </h3>
                </div>
                <div class="card-body">
                    <p>This campaign does not have a phone number from which to send SMS messages!</p>
                    <a href="{{ route('campaigns.edit', ['campaign' => $campaign->id]) }}" class="btn btn-success">Click here to add one</a>
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-12 col-md-5">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="counter">@{{ pendingSmsCounter }}</div>
                            <div class="counter-label">Pending SMS Messages</div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="counter">@{{ sentSmsCounter }}</div>
                            <div class="counter-label">Sent SMS Messages</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7">
                    <div class="card card-message-container">
                        <div class="card-header">
                            <div class="card-title mb-0">Message</div>
                        </div>
                        <div class="card-body">
                            <div>{!! $drop->text_message !!}</div>
                            @if ($drop->send_vehicle_image && $drop->text_vehicle_image)
                                <img src="{{ $drop->text_vehicle_image }}">
                            @endif
                        </div>
                        <div class="card-footer">
                            <div>Scheduled to drop at {{ $drop->send_at->format("m/d/Y g:i A") }}</div>
                        </div>
                    </div>
                    <button :disabled="loading" type="button" @click="sendMessage" v-if="currentRecipient" class="btn btn-block pm-btn pm-btn-purple pm-btn-send">
                        <span v-if="!loading">Send an SMS message to <b>@{{ currentRecipient.first_name }} @{{ currentRecipient.last_name }}</b></span>
                        <spinner-icon class="white" :size="'xs'" v-if="loading"></spinner-icon>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
