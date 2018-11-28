<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;

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
            'user' => auth()->user(),
            'companies' => auth()->user()->companies()->orderBy('companies.name', 'asc')->get()
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
            $user->password = bcrypt($request->input('new_password'));
            $user->save();
            return redirect()->back()->with(['success_message' => 'Password updated!']);
        } else {
            return redirect()->back()->withErrors(['password' => 'Current password doesn\'t match password stored in database.']);
        }
    }

    public function updateCompanyData(Request $request)
    {
        $invitation = auth()->user()->invitations()->where('company_id', $request->input('company'))->firstOrFail();

        $config = $invitation->config;
        $config['timezone'] = $request->input('timezone');

        $invitation->config = $config;
        $invitation->save();

        return response()->json('Resource updated.');
    }
}
