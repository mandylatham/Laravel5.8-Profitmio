<?php
namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Http\Resources\LeadCollection;
use App\Repositories\Contracts\SearchableRepositoryContract;

class LeadSearch implements SearchableRepositoryContract
{

    /**
     * @var array
     */
    public $healths = ['good', 'ok', 'bad'];

    /**
     * @var array
     */
    public $labels = ['none', 'interested', 'appointment', 'callback', 'service', 'not_interested', 'wrong_number', 'car_sold', 'heat'];

    /**
     * @var $media
     */
    public $media = ['email', 'text', 'phone'];

    /**
     * @var array
     */
    public $statuses = ['new', 'open', 'closed'];

    public function byRequest(Campaign $campaign, Request $request)
    {
        $healths = (array) $request->input('health', []);
        $labels = (array) $request->input('labels', []);
        $media = (array) $request->input('media', []);
        $statuses = (array) $request->input('filter', []);

        $leads = $campaign->leads();

        foreach ($healths as $health) { //$this->healths as $health) {
            if (in_array($health, $this->healths)) {
                $leads->healthIs($health);
            } else {
                throw new \Exception('invalid parameter for health');
            }
        }

        foreach ($statuses as $status) {
            if (in_array($status, $this->statuses)) {
                $leads->$status();
            } else {
                throw new \Exception('invalid parameter for status');
            }
        }

        foreach ($media as $medium) {
            if (in_array($medium, $this->media)) {
                $leads->whereHas(
                    'responses', function ($query) use ($medium) {
                        $query->whereType($medium);
                    }
                );
            } else {
                throw new \Exception('invalid parameter for media');
            }
        }

        foreach ($labels as $label) {
            if (in_array($label, $this->labels)) {
                $leads->labelled($label, $campaign->id);
            } else {
                throw new \Exception('invalid parameter for labels');
            }

        }

        if ($request->filled('search')) {
            $leads->search($request->input('search'));
        }

        return $leads->orderBy('last_responded_at', 'DESC')
            ->orderBy('last_status_change_at', 'DESC')
            ->paginate($request->input('per_page', 30));

    }
}
