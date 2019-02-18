<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Campaign;
use App\Models\Company;
use App\Models\Drop;
use App\Models\Response;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $appointment;

    private $campaign;

    private $company;

    private $drop;

    public function __construct(Appointment $appointment, Campaign $campaign, Company $company, Drop $drop)
    {
        $this->middleware('auth');

        $this->appointment = $appointment;
        $this->campaign = $campaign;
        $this->company = $company;
        $this->drop = $drop;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('campaigns.index');
        }

        return view('dashboard.index', [
            'q' => '',
        ]);
    }

    protected function getStats($ids)
    {
        $stats = [
            'responses' => Response::where('incoming', 1),
            'calls' => Response::where('type', 'phone')->where('incoming', 1),
            'sms' => Response::where('type', 'text')->where('incoming', 1),
            'emails' => Response::where('type', 'email')->where('incoming', 1),
            'appointments' => Appointment::where('type', 'appointment'),
            'callbacks' => Appointment::where('type', 'callback'),
        ];

        foreach ($stats as &$stat) {
            $stat->whereIn('campaign_id', $ids);
        }

        foreach ($stats as &$stat) {
            $stat = $stat->count();
        }

        return (object) $stats;
    }
}
