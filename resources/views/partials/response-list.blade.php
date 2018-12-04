<table id="mailboxTable" class="table" data-plugin="animateList" data-animate="fade"
       data-child="tr">
    <tbody>
@foreach ($recipients as $recipient)
    <tr data-url="{{ secure_url('campaign/'. $campaign->id . '/response/' . $recipient->id) }}" data-toggle="slidePanel" data-recipient="{{ $recipient->id }}">
        <td class="cell-60">
            <span class="checkbox-custom checkbox-primary checkbox-lg">
                <input type="checkbox" class="mailbox-checkbox selectable-item" id="mail_mid_1" />
                <label for="mail_mid_1"></label>
            </span>
        </td>
        <td class="cell-30 responsive-hide">
            <span class="checkbox-important checkbox-default">
                <input type="checkbox" class="mailbox-checkbox mailbox-important" id="mail_mid_1_important" />
                <label for="mail_mid_1_important"></label>
            </span>
        </td>
        <td class="cell-60 responsive-hide">
            <a class="avatar" href="javascript:void(0)">
                <img class="img-fluid" src="https://placehold.id/40x40" alt="...">
            </a>
        </td>
        <td>
            <div class="content">
                <div class="title">{{ $recipient->name }}</div>
                <div class="abstract">{{ $recipient->vehicle }}</div>
            </div>
        </td>
        <td class="cell-30 responsive-hide">
        </td>
        <td class="cell-130">
            <div class="time">2 hours ago</div>
            <div class="identity"><i class="md-circle red-600" aria-hidden="true"></i>Work</div>
        </td>
    </tr>
@endforeach
    </tbody>
</table>

{{ $recipients->links() }}