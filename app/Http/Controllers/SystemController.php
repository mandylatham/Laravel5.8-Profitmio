<?php

namespace App\Http\Controllers;

use App\Models\Drop;
use \Carbon\Carbon;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    /**
     * Views all System Activity
     */
    public function index(Request $request)
    {
        $date = $request->date ? new Carbon($request->date) : Carbon::now();
        $startDate = (new Carbon($date))->startOfWeek()->subDay();
        $endDate = (new Carbon($date))->endOfWeek()->subDay();

        $scheduleQueue = Drop::with(['campaign'])
            ->withCount(['recipients'])
            ->where('send_at', '>=', $startDate)
            ->where('send_at', '<=', $endDate)
            ->paginate(25);

        $viewData['scheduleQueue'] = $scheduleQueue;
        $viewData['startDate'] = (new Carbon($startDate))->toFormattedDateString();
        $viewData['endDate'] = (new Carbon($endDate))->toFormattedDateString();

        return view('system.index', $viewData);
    }
}
