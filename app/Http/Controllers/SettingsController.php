<?php

namespace App\Http\Controllers;

use App\Models\GlobalSettings;
use Illuminate\Http\Request;
use App\Services\FacebookService;
use PhpParser\Node\Stmt\Global_;

class SettingsController extends Controller
{
    private $settings;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(401);
        }

        $facebookService = new FacebookService();

        return view('settings.index', [
            'settings' => (object) [
                'facebook' => (object) [
                    'app_id' => $facebookService->getAppId(),
                    'graph_version' => $facebookService->getGraphVersion()
                ]
            ]
        ]);
    }

    public function facebookAccessTokenStore(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(401);
        }

        $facebookService = new FacebookService();
        $accessToken = $facebookService->getExtendedAccessToken($request->input('accessToken'));

        $globalSettings = GlobalSettings::where('name', 'facebook_access_token')->first();
        if (! $globalSettings) $globalSettings = new GlobalSettings;
        $globalSettings->name = 'facebook_access_token';
        $globalSettings->value = $accessToken;
        $globalSettings->save();

        return response()->json(['message' => 'Resource saved']);
    }
}
