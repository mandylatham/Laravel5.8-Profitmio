<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Http\Requests\NewClientRequest;
use App\Models\User;
use App\Models\Campaign;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Index of all Clients
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $clients = User::where('access', 'Client')->get();

        $viewData['clients'] = $clients;

        return view('remark_clients.index', $viewData);
    }

    /**
     * Details of one Client
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        $campaigns = Campaign::where('client_id', $user->id)->with('schedules')->get();

        $viewData['client'] = $user;
        $viewData['campaigns'] = $campaigns;

        return view('remark_clients.details', $viewData);
    }

    public function createNew()
    {
        $viewData = [];

        return view('remark_clients.new', $viewData);
    }

    public function create(ClientRequest $request)
    {
        $client = new User([
            'organization' => $request->organization,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'access' => 'Client',
            'password' => sha1($request->password),
            'alt_password' => bcrypt($request->password),
            'phone_number' => $request->phone_number,
            'timezone' => $request->timezone
        ]);

        $client->save();

        return redirect()->route('client.edit', ['client' => $client->id]);
    }

    public function edit(User $client)
    {
        $viewData['client'] = $client;

        return view('remark_clients.edit', $viewData);
    }

    public function update(ClientRequest $request)
    {
        $client = User::findOrFail($request->id);

        if (empty($request->password)) {
            $request->request->remove('password');
            $request->request->remove('verify_password');
        }

        $client->fill($request->toArray());

        $client->save();

        return redirect()->route('client.edit', ['client' => $client->id]);
    }
}
