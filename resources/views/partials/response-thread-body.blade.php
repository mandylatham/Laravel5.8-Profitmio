@foreach ($messages as $message)
<div class="message-wrapper {{ $message->incoming ? 'inbound-message' : 'outbound-message' }}">
    <div class="message-time">{{ $message->created_at->timezone(\Auth::user()->timezone)->format('Y-m-d g:i A T') }} ({{ $message->created_at->timezone(\Auth::user()->timezone)->diffForHumans() }})</div>
    <div class="message unread">{{ $message->message }}</div>
    @if ($message->incoming)
    <div class="checkbox">
        <label>
            <input type="checkbox"
                class="message-read"
                data-postback='{{ secure_url("response/{$message->id}/update-read-status") }}'
                data-response_id="{{ $message->id }}"
                data-response_time="{{ $message->created_at->format('m/d/Y g:i A') }}"
            {{ $message->read ? 'checked="checked"' : '' }}>
            Read
        </label>
    </div>
    @endif
</div>
@endforeach
