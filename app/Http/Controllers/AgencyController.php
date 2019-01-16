<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgencyRequest;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class AgencyController
 *
 * TODO: extend a UserController with custom properties to identify the class type
 *
 * @package App\Http\Controllers
 */
class AgencyController extends Controller
{
    public function index()
    {
        $agencies = User::where('access', 'Agency')->get();

        $viewData['agencies'] = $agencies;

        return view('agencies.index', $viewData);
    }

    public function show(User $agency)
    {
        $viewData['agency'] = $agency;
        $viewData['campaigns'] = $agency->client_campaigns()->get();

        return view('agencies.details', $viewData);
    }

    public function createForm()
    {
        $viewData = [];

        return view('agencies.new', $viewData);
    }

    public function create(AgencyRequest $request)
    {
        $agency = new User([
            'organization' => $request->organization,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'access' => 'Agency',
            'password' => sha1($request->password),
            'alt_password' => bcrypt($request->password),
            'phone_number' => $request->phone_number,
            'timezone' => $request->timezone
        ]);

        $agency->save();

        return redirect()->route('agency.edit', ['agency' => $agency->id]);
    }

    public function updateForm(User $agency)
    {
        $viewData['agency'] = $agency;

        return view('agencies.edit', $viewData);
    }

    public function update(User $agency, AgencyRequest $request)
    {
        $agency->fill($request);

        dd($agency);
    }
}
