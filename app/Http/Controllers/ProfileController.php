<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Contracts\Hashing\Hasher;

class ProfileController extends Controller
{
    protected $hash;
    public function __construct(Hasher $hash)
    {
        $this->hash = $hash;
    }

    public function index()
    {
        return view('profile.index', [
            'user' => auth()->user()
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        if ($request->filled('first_name')) {
            $user->first_name = $request->input('first_name');
        }
        if ($request->filled('last_name')) {
            $user->last_name = $request->input('last_name');
        }
        if ($request->filled('email')) {
            $user->email = $request->input('email');
        }
        $user->save();

        return redirect()->back();
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = auth()->user();
        if ($this->hash->check($request->input('password'), $user->password)) {
            $user->password = bcrypt($request->input('password'));
            $user->save();
        } else {
            return redirect()->back()->withErrors(['Current password doesn\'t match password stored in database.']);
        }

        return redirect()->back();
    }
}
