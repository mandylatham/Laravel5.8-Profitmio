<?php

namespace App\Http\Controllers;

use App\Models\CampaignScheduleTemplate;
use App\Models\Drop;
use App\Http\Requests\DeploymentRequest;
use App\Http\Requests\StoreMailerRequest;
use App\Http\Requests\BulkDeploymentRequest;
use App\Models\Recipient;
use App\Models\RecipientList;
use ProfitMiner\Base\Services\Drops\Processors\SMSDropProcessor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Company;
use App\Models\CampaignSchedule;
use Illuminate\Support\Facades\Log;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class DeploymentController extends Controller
{
    /**
     * @var SMSDropProcessor
     */
    protected $processor;

    public function __construct(SMSDropProcessor $processor)
    {
        parent::__construct();

        $this->processor = $processor;
    }

    /**
     * Send out the SMS message
     *
     * @param \App\Models\Campaign  $campaign
     * @param \App\Models\Drop      $drop
     * @param \App\Models\Recipient $recipient
     *
     * @return array
     * @throws \Exception
     */
    public function deploySms(
        Campaign $campaign,
        Drop $drop,
        Recipient $recipient
    ) {
        if ($campaign->isExpired) {
            return response()->json(['error' => ['error' => 'Illegal Request. This abuse of the system has been logged.']], 403);
        }

        try {
            $this->processor->processRecipient($drop, $recipient);
        }
        catch (\Throwable $e) {
            return ['success' => 0, 'There was a problem sending the sms message'];
        }

        return [
            'success' => 1,
            'message' => 'This recipient has been sent a customized copy of the sms message',
            'debug' => [
                'status' => $drop->status,
                'completed_at' => now('UTC'),
                'percentage_complete' => $drop->percentage_complete,
            ],
        ];
    }

    public function createNew(Campaign $campaign, Request $request)
    {
        if ($campaign->isExpired) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }

        $viewData['recipient_info'] = [];
        $viewData['campaign'] = $campaign;
        $viewData['templates'] = CampaignScheduleTemplate::all();
        $viewData['recipientLists'] = RecipientList::where('campaign_id', $campaign->id)->get();

        return view('campaigns.deployments.create', $viewData);
    }

    public function createNewMailer(Campaign $campaign, Request $request)
    {
        if ($campaign->isExpired()) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }

        $viewData['campaign'] = $campaign;

        return view('campaigns.deployments.create-mailer', $viewData);
    }

    public function createNewEmailDrop(Campaign $campaign, Request $request)
    {
        if ($campaign->isExpired) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }

        $viewData['recipient_info'] = [];
        $viewData['campaign'] = $campaign;
        $viewData['templates'] = CampaignScheduleTemplate::all();

        return view('campaigns.deployments.new-email-drop', $viewData);
    }

    public function createNewSmsDrop(Campaign $campaign, Request $request)
    {
        if ($campaign->isExpired) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }

        $viewData['recipient_info'] = [];
        $viewData['campaign'] = $campaign;
        $viewData['templates'] = CampaignScheduleTemplate::all();

        return view('campaigns.deployments.new-sms-drop', $viewData);
    }

    public function createNewMailerDrop(Campaign $campaign, Request $request)
    {
        if ($campaign->isExpired) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }

        $viewData['recipient_info'] = [];
        $viewData['campaign'] = $campaign;
        $viewData['templates'] = CampaignScheduleTemplate::all();

        return view('campaigns.deployments.new-mailer-drop', $viewData);
    }

    public function create(Campaign $campaign, BulkDeploymentRequest $request)
    {
        if ($campaign->isExpired) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }

        $info = $request->session()->get($campaign->id . '_recipient_info');

        $deployments = collect($this->createBulkDeployments($campaign, $request));
        $recipients = collect($this->getBulkRecipients($campaign, $request));
        $batches = $this->assembleRecipientBatches($info, $recipients, $deployments);

        \DB::table('deployment_recipients')->insert($batches);

        return response()->json(['message' => 'Resource created.']);

//        return redirect()->route('campaign.drop.index', ['campaign' => $campaign->id]);
    }

    public function forCampaign(Campaign $campaign)
    {
        return view('campaigns.deployments.index', [
            'campaign' => $campaign,
        ]);
    }

    public function getForUserDisplay(Campaign $campaign, Request $request)
    {
        $drops = Drop::searchByRequest($request, $campaign)
            ->orderBy('campaign_schedules.id', 'desc')
            ->paginate(15);

        $drops->each(function ($item) {
            return $item->text_message = str_replace('}}', '</span>', str_replace('{{', '<span class="badge badge-outline badge-primary">', $item->text_message));
        });

        return $drops;
    }

    public function saveGroups(Campaign $campaign, Request $request)
    {
        $info = [
            'contact_filter' => $request->contact,
            'group_using' => $request->group_using,
            'max' => $request->max,
            'total' => $request->total,
            'group_count' => $request->group_count,
            'groups' => $request->groups,
            'lists' => $request->lists,
            'sources' => $request->sources,
        ];

        $request->session()->put($campaign->id . "_recipient_info", $info);

        return response()->json(['code' => 200, 'message' => $info]);
    }

    public function scopeFilterByQuery($query, $q)
    {
        return $query->search($q);
    }

    public function show(Campaign $campaign, Drop $drop)
    {
        $drop->text_message = str_replace('{{', '<span class="badge badge-outline badge-primary">',
            str_replace('}}', '</span>', $drop->text_message));

        return view('campaigns.deployments.details', [
            'campaign' => $campaign,
            'recipients' => $drop->recipients()->withPivot('sent_at', 'failed_at')->get(),
            'drop' => $drop,
        ]);
    }

    public function storeMailer(Campaign $campaign, StoreMailerRequest $request)
    {
        if ($campaign->isExpired()) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }

        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }
        // receive the file
        $save = $receiver->receive();
        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            $drop = new Drop();
            $drop->campaign_id = $campaign->id;
            $drop->type = 'mailer';
            $drop->send_at = (new Carbon($request->input('send_at')))->toDateTimeString();
            $drop->save();
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            $drop->addMedia($save->getFile())
                ->toMediaCollection('image', env('MEDIA_LIBRARY_DEFAULT_PUBLIC_FILESYSTEM'));
            return response()->json([
                'message' => 'Resource created.',
                'resource' => $drop
            ]);
        }
        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            "done"   => $handler->getPercentageDone(),
            'status' => true,
        ]);
    }

    public function update(Campaign $campaign, Drop $drop, DeploymentRequest $request)
    {
        $timezone = auth()->user()->getTimezone(Company::findOrFail(get_active_company()));
        $sendAtDatetime = (new Carbon($request->send_at_date . " " . $request->send_at_time, $timezone))->timezone('UTC');
        $date = $sendAtDatetime->toDateString();
        $time = $sendAtDatetime->format('H:i:s');
        $send_at = $sendAtDatetime->toDateTimeString();
        \Log::debug($timezone);
		$requestDrop = $request->all();
		$requestDrop['send_at'] = $send_at;

        $drop->fill($requestDrop);

        $drop->save();

        return response()->json(['message' => 'Resource Updated.']);
    }

    public function updateImage(Campaign $campaign, Drop $drop, Request $request)
    {
        if ($campaign->isExpired()) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }

        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }
        // receive the file
        $save = $receiver->receive();
        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            $drop->addMedia($save->getFile())
                ->toMediaCollection('image', env('MEDIA_LIBRARY_DEFAULT_PUBLIC_FILESYSTEM'));
            return response()->json([
                'message' => 'Resource created.',
                'image_url' => $drop->image_url
            ]);
        }
        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            "done"   => $handler->getPercentageDone(),
            'status' => true,
        ]);
    }


    public function updateForm(Campaign $campaign, Drop $drop)
    {
        if ($campaign->isExpired()) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }
        $viewData['campaign'] = $campaign;
        $viewData['drop'] = $drop;

        return view('campaigns.deployments.edit', $viewData);
    }


    public function delete(Campaign $campaign, Drop $drop)
    {
        try {
            $drop->status = "Deleted";
            $drop->save();
            $drop->delete();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'The drop has been deleted'
        ]);
    }

    public function resume(Drop $deployment)
    {
        try {
            $deployment->status = "Pending";
            $deployment->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'The campaign has been resumed'
        ]);
    }

    public function pause(Drop $deployment)
    {
        try {
            $deployment->status = "Paused";
            $deployment->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'The campaign has been paused'
        ]);
    }


    public function start(Drop $deployment)
    {
        try {
            $this->processor->launch($deployment);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'The drop has been started'
        ]);
    }

    /**
     * @param \App\Models\Campaign                            $campaign
     * @param \App\Http\Requests\BulkDeploymentRequest $request
     *
     * @return array
     */
    protected function createBulkDeployments(Campaign $campaign, BulkDeploymentRequest $request)
    {
        $userTimezone = auth()->user()->getTimezone(Company::findOrFail(get_active_company()));
        $base = [
            'campaign_id' => $campaign->id,
            'type' => $request->type,
            'email_subject' => $request->email_subject,
            'email_text' => $request->email_text,
            'email_html' => $request->email_html,
            'text_message' => $request->text_message,
            'text_vehicle_image' => $request->text_vehicle_image,
            'send_vehicle_image' => (int)$request->send_vehicle_image,
            'recipient_group' => 0,
            'system_id' => 2
        ];

        $deployments = [];

        $x = 0;
        if ($request->has('Group0_date')) {
            $x = 1;
        }
        for ($i = 0; $i < $x; $i++) {
            $deployment = $base;

            $deployment['send_at'] = (new Carbon(
                    $request->get('Group' . $i . '_date') . ' ' . $request->get('Group' . $i . '_time'),
                    $userTimezone))
                ->timezone('UTC');
            \Log::debug("time is ".$request->get('Group' . $i . '_time') . " and send at is ". $deployment['send_at']);

            $deployment = new Drop($deployment);

            $deployment->save();

            $deployments[] = $deployment;

            if ($request->has('Group' . ($i + 1) . '_date')) {
                $x++;
            }
        }

        return $deployments;
    }

    /**
     * Gets the QueryBuilder for Recipients
     *
     * @param \App\Models\Campaign $campaign
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getBulkRecipients(Campaign $campaign, $request)
    {
        $info = $request->session()->get($campaign->id . '_recipient_info');
        $lists = $info['lists'];
        $contact = $info['contact_filter'];
        Log::debug('bulk recipient filters: ' . json_encode($info));

        $recipients = \DB::table('recipients')
            ->whereNull('deleted_at')
            ->whereNull('last_responded_at')
            ->where('campaign_id', $campaign->id);

        if ($contact == 'all-sms' or $contact == 'no-resp-sms') {
            $recipients->whereRaw("length(phone) > 9");
        }
        if ($contact == 'all-email' or $contact == 'no-resp-email') {
            $recipients->where('email', '<>', '')
                ->where('email_valid', 1);
        }
        if ($contact == 'sms-only') {
            $recipients->where('email', '=', '')->where(function ($q) {
                $q->orWhere('phone', '<>', '+1')
                    ->orWhere('phone', '<>', '');
                })
                ->whereRaw("length(phone) > 9");
        }
        if ($contact == 'email-only') {
            $recipients->where('email', '<>', '')
                ->where(function($q) {
                    $q->orWhere('phone', '=', '+1')
                      ->orWhere('phone', '=', '');
                })
                ->where('email_valid', 1);
        }
        if ($contact == 'no-resp-email') {
            $recipients->whereNotIn('id',
                \DB::table('responses')->where('campaign_id',
                    $campaign->id)->select('recipient_id')->get()->pluck('recipient_id')->toArray())
                ->where('email_valid', 1);
        }
        if ($contact == 'no-resp-sms') {
            $recipients->whereNotIn('id',
                \DB::table('responses')->where('campaign_id',
                    $campaign->id)->select('recipient_id')->get()->pluck('recipient_id')->toArray())
                ->whereRaw("length(phone) > 9");
        }

        if (is_array($lists) && !empty($lists) && $lists[0] != null) {
            if (!in_array('all', $lists)) {
                $recipients->whereIn('recipient_list_id', (array)$lists);
            }
        }

        if (count($info['sources']) == 1) {
            if ($info['sources'][0] == 'database') {
                $recipients->whereFromDealerDb(true);
            }
            if ($info['sources'][0] == 'conquest') {
                $recipients->whereFromDealerDb(false);
            }
        }
        Log::debug("recipient count: " . $recipients->select('id')->count());

        return $recipients->select('id')->get();
    }

    /**
     * @param $info
     * @param $recipients
     * @param $deployments
     *
     * @return array
     */
    protected function assembleRecipientBatches($info, $recipients, $deployments)
    {
        $i = 1;
        $batch = 0;
        $batches = [];
        \Log::debug("assemble recipient batches (info): " . json_encode($info));
        \Log::debug("assemble recipient batches (deployments): " . json_encode($deployments));

        try {
            if ($info['max']) {
                foreach ($recipients as $recipient) {
                    if ( ! isset($deployments[$batch])) {
                        Throw new \Exception("More recipients than batches");
                    }
                    $batches[] = ['deployment_id' => $deployments[$batch]->id, 'recipient_id' => $recipient->id];

                    if ($i != 0 && $i % $info['max'] == 0) {
                        $batch++;
                    }
                    $i++;
                }
            } else {
                $batches = $recipients->map(function ($recip) use ($deployments) {
                    return ['deployment_id' => $deployments->first()->id, 'recipient_id' => $recip->id];
                })->toArray();
            }
        } catch (\Exception $e) {
            \Log::error('DeploymentController@assembleRecipientBatches(): cannot assemble batches => ' . $e->getMessage());
            dd($e->getMessage());
        }

        return $batches;
    }
}
