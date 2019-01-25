<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MigratePasswordToNewHash
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Attempting  $event
     * @return void
     */
    public function handle(Attempting $event)
    {
        $login = 'email';
        $user = User::where($login, $event->credentials[$login])->get();
        $password = $event->credentials['password'];

        if ($user->count() == 0) {
            return;
        }

        $user = $user->makeVisible(['password', 'alt_password'])->first();

        if ($user->password == sha1($password) &&
            ! \Hash::check($password, $user->alt_password)) {
            $user->alt_password = \Hash::make($password);
            $user->save();
        }
    }
}
