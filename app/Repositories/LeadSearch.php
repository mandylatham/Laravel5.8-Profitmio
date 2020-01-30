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

    /**
     * @var QueryBuilder
     */
    private $leads;

    /**
     * @var Campaign
     */
    private $campaign;

    /**
     * @var int
     */
    private $per_page = 30;

    /**
     * Allow this to be used for a specific campaign
     *
     * @param Campaign $campaign
     */
    public function forCampaign(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->leads = $this->campaign->leads();

        return $this;
    }

    public function byRequest(Request $request)
    {
        if (!$this->campaign) {
            throw new \Exception("No campaign specified");
        }

        $this->per_page = $request->input('per_page', $this->per_page);
        $statuses = (array) $request->input('status', []);
        $search = $request->input('search');
        $media = (array) $request->input('media', []);
        $labels = (array) $request->input('labels', []);

        $leadSearch = $this;

        if ($statuses) { $leadSearch = $leadSearch->byStatus($statuses); }
        if ($search) { $leadSearch = $leadSearch->byKeyword($search); }
        if ($media) { $leadSearch = $leadSearch->byMedia($media); }
        if ($labels) { $leadSearch = $leadSearch->byLabel($labels); }

        return $leadSearch->results();

    }

    public function results()
    {
        return $this->leads->orderBy('last_responded_at', 'DESC')
            ->orderBy('last_status_changed_at', 'DESC')
            ->paginate($this->per_page);
    }

    public function byStatus($statuses)
    {
        $statuses = (array) $statuses;

        foreach ($statuses as $status) {
            if (in_array($status, $this->statuses)) {
                $this->leads->$status();
            } else {
                throw new \Exception('invalid parameter for status');
            }
        }

        return $this;
    }

    public function byMedia($media)
    {
        $media = (array) $media;

        foreach ($media as $medium) {
            if (in_array($medium, $this->media)) {
                $this->leads->whereHas(
                    'responses', function ($query) use ($medium) {
                        $query->whereType($medium);
                    }
                );
            } else {
                throw new \Exception('invalid parameter for media');
            }
        }

        return $this;
    }

    public function byHealth($healths)
    {
        $healths = (array) $healths;

        foreach ($healths as $health) {
            if (in_array($health, $this->healths)) {
                $this->leads->healthIs($health);
            } else {
                throw new \Exception('invalid parameter for health');
            }
        }

        return $this;
    }

    public function byLabel($labels)
    {
        $labels = (array) $labels;

        $this->leads->labelled($labels);

        return $this;
    }

    public function byKeyword($keywords)
    {
        $this->leads->search($keywords);

        return $this;
    }
}
