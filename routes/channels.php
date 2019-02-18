<?php

use App\Models\Campaign;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('campaign.{campaign}', function ($user, $campaign) {
    if ($user->is_admin) {
        return true;
    }
    $campaign = Campaign::find($campaign);
    if (empty($campaign)) {
        return false;
    }

    return $campaign->users()->where('users.id', $user->id)->count();
});
