<?php

namespace App\Http\Controllers;

use App\Models\CampaignScheduleTemplate;
use App\Models\Drop;
use App\Http\Requests\DeploymentRequest;
use App\Http\Requests\BulkDeploymentRequest;
use App\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\CampaignSchedule;
use Illuminate\Support\Facades\Log;

class DeploymentController extends Controller
{
    /**
     * Send out the SMS message
     *
     * TODO: Clean this up
     *
     * @param \App\Models\Campaign  $campaign
     * @param \App\Models\Drop      $drop
     * @param \App\Models\Recipient $recipient
     *
     * @return array
     * @throws \Exception
     */
    public function deploySms(Campaign $campaign, Drop $drop, Recipient $recipient)
    {
        if ($campaign->isExpired) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }
        if ($drop->system_id == 2) {
            $unsent = \DB::table('deployment_recipients')
                    ->where('deployment_id', $drop->id)
                    ->where('recipient_id', $recipient->id)
                    ->whereNotNull('sent_at')
                    ->get();
        } else {
            $unsent = \DB::table('campaign_schedule_lists')
                    ->where('campaign_schedule_id', $drop->id)
                    ->where('recipient_id', $recipient->id)
                    ->whereNotNull('sent_at')
                    ->get();
        }

        if ($unsent->count() > 0) {
            return ['success' => 1, 'message' => 'This recipient has already been sent an sms message'];
        }

        $loader = new \Twig_Loader_Array([
            'text_message' => $drop->text_message,
        ]);

        $twig = new \Twig_Environment($loader);

        $templateVars = collect($recipient->toArray())->except(['pivot'])->toArray();

        if (! $text = $twig->render('text_message', $templateVars)) {
            throw new \Exception("Unable to parse message template");
        }

        $from = $campaign->phone->phone_number;
        $to = $recipient->phone;
        $message = $text;
        $mediaUrl = null;

        //if we want to send an MMS, we want $mms set to true
        if ($drop->send_vehicle_image) {
            $year = 99999999; //will never exist in this image list
            if ((int)$recipient->year > 2000) {
                $year = (int)$recipient->year - 2000;
            }

            $filename = strtolower("{$recipient->make}_{$year}{$recipient->model}.png");

            if (\Storage::disk('s3')->exists($filename)) {
                $mediaUrl = 'https://s3.amazonaws.com/profitminer/vehicles/'.$filename;
            }
        } else {
            // we might want to send an image attached to the campaign rather than an image of their vehicle.
            if (! empty(trim($drop->text_message_image))) {
                $mediaUrl = $drop->text_message_image;
            }
        }


        try {
            $updateField = 'failed_at';

            if ($recipient->suppressions->count() > 0) {
                throw new \Exception("Recipient is suppressed from SMS communication");
            }

            if ($recipient->last_responded_at == null) {
                //Log::create(['message'=>'sending text to ' . $recipient->phone, 'code'=>'phone', 'file'=>$recipient->phone, 'line_number'=>$recipient->campaign_id]);
                \Twilio::sendSms($from, $to, $message, $mediaUrl);

                $updateField = 'sent_at';
            }

            /*  Mark DropRecipient as Sent  */
            if ($drop->system_id == 2) {
                \DB::table('deployment_recipients')
                    ->where('deployment_id', $drop->id)
                    ->where('recipient_id', $recipient->id)
                    ->update([$updateField => Carbon::now()]);
                $stats = \DB::table('deployment_recipients')->where('deployment_id', $drop->id)->selectRaw("sum(case when sent_at is null and failed_at is null then 1 else 0 end) as pending, sum(case when sent_at is not null or failed_at is not null then 1 else 0 end) as sent")->first();
            } else {
                \DB::table('campaign_schedule_lists')
                    ->where('campaign_schedule_id', $drop->id)
                    ->where('recipient_id', $recipient->id)
                    ->update([$updateField => Carbon::now()]);
                $stats = \DB::table('campaign_schedule_lists')->where('campaign_schedule_id', $drop->id)->selectRaw("sum(case when sent_at is null and failed_at is null then 1 else 0 end) as pending, sum(case when sent_at is not null or failed_at is not null then 1 else 0 end) as sent")->first();
            }

            $percent = $stats->pending / ($stats->pending + $stats->sent);
            $filler = [
                'status' => $stats->pending == 0 ? 'Completed' : 'Processing',
                'percentage_complete' => $percent * 100,
            ];

            $drop->fill($filler)->save();

            return [
                'success' => 1,
                'message' => 'This recipient has been sent a customized copy of the sms message',
                'debug' => json_encode($filler),
            ];
        } catch (\Exception $e) {
            /*  Mark DropRecipient as Sent  */
            if ($drop->system_id == 2) {
                \DB::table('deployment_recipients')
                    ->where('deployment_id', $drop->id)
                    ->where('recipient_id', $recipient->id)
                    ->update(['failed_at' => Carbon::now()]);
            } else {
                \DB::table('campaign_schedule_lists')
                    ->where('campaign_schedule_id', $drop->id)
                    ->where('recipient_id', $recipient->id)
                    ->update(['failed_at' => Carbon::now()]);
            }
            \Log::error("There was an error sending SMS to recipient #{$recipient->id}: " . $e->getMessage());
        }

        return ['success' => 0, 'There was a problem sending the sms message'];
    }

    public function createNew(Campaign $campaign, Request $request)
    {
        if ($campaign->isExpired) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }

        $viewData['recipient_info'] = [];
        $viewData['campaign'] = $campaign;
        $viewData['templates'] = CampaignScheduleTemplate::all();

        /*
        if ($request->session()->has($campaign->id . '_recipient_info')) {
            $viewData['recipient_info'] = $request->session()->get($campaign->id . '_recipient_info');
        }
        */

        return view('campaigns.deployments.new', $viewData);
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

        return redirect()->route('campaign.drop.index', ['campaign' => $campaign->id]);
    }

    public function forCampaign(Campaign $campaign)
    {
        // $schedules = CampaignSchedule::where('campaign_id', $campaign->id)->get();
        $drops = \DB::table('campaign_schedules')
            ->select([
                'send_at', 'type', 'started_at', 'recipient_group', 'status', 'text_message', 'percentage_complete', 'completed_at', 'campaign_schedules.id',
                \DB::raw("case when type in ('email', 'sms') then
                (select count(*) from deployment_recipients where deployment_id = campaign_schedules.id)
                else
                (select count(*) from recipients where campaign_id = " . $campaign->id . " and subgroup = recipient_group)
                end as recipients")
            ])
            ->where('campaign_id', $campaign->id)
            ->whereNull('deleted_at')
            ->orderBy('campaign_schedules.id', 'desc')
            ->get();

        $drops->each(function ($item) {
            return $item->text_message = str_replace('}}', '</span>', str_replace('{{', '<span class="badge badge-outline badge-primary">', $item->text_message));
        });

        $viewData['drops'] = $drops;
        $viewData['campaign'] = $campaign;

        return view('campaigns.deployments.index', $viewData);
    }

    public function saveGroups(Campaign $campaign, Request $request)
    {
        $info = [
            'contact_filter' => $request->contact_filter,
            'group_using' => $request->group_using,
            'max' => $request->max,
            'total' => $request->total,
            'group_count' => $request->group_count,
            'groups' => $request->groups,
            'lists' => $request->lists,
            'sources' => $request->sources,
        ];

        $request->session()->put($campaign->id . "_recipient_info", $info);

        return json_encode(['code' => 200, 'message' => $info]);
    }

    public function show(Campaign $campaign, Drop $drop)
    {
        $viewData['campaign'] = $campaign;
        if ($drop->system_id == 2) {
            $viewData['recipients'] = Recipient::whereIn('recipient_id',
                result_array_values(\DB::table('deployment_recipients')
                    ->where('deployment_id', $drop->id)
                    ->whereNull('sent_at')
                    ->whereNull('failed_at')
                    ->select('recipient_id')
                    ->get()))
                ->get();

            $viewData['sentRecipients'] = Recipient::whereIn('recipient_id',
                result_array_values(\DB::table('deployment_recipients')
                    ->where('deployment_id', $drop->id)
                    ->where(function ($query) {
                        $query->whereNotNull('sent_at')
                            ->orWhereNotNull('failed_at');
                    })
                    ->select('recipient_id')
                    ->get()))
                ->count();
            $viewData['recipientCount'] = \DB::table('deployment_recipients')->where('deployment_id', $drop->id)->count();
        } else {
            $viewData['recipients'] = Recipient::whereIn('recipient_id',
                result_array_values(\DB::table('campaign_schedule_lists')
                    ->where('campaign_schedule_id', $drop->id)
                    ->whereNull('sent_at')
                    ->whereNull('failed_at')
                    ->select('recipient_id')
                    ->get()))
                ->get();

            $viewData['sentRecipients'] = Recipient::whereIn('recipient_id',
                result_array_values(\DB::table('campaign_schedule_lists')
                    ->where('campaign_schedule_id', $drop->id)
                    ->where(function ($query) {
                        $query->whereNotNull('sent_at')
                            ->orWhereNotNull('failed_at');
                    })
                    ->select('recipient_id')
                    ->get()))
                ->count();
            $viewData['recipientCount'] = \DB::table('campaign_schedule_lists')->where('campaign_schedule_id', $drop->id)->count();
        }

        $drop->text_message = str_replace('{{', '<span class="badge badge-outline badge-primary">',
            str_replace('}}', '</span>', $drop->text_message));

        $viewData['drop'] = $drop;

        return view('campaigns.deployments.details', $viewData);
    }

    public function update(Campaign $campaign, CampaignSchedule $deployment, DeploymentRequest $request)
    {
        $date = new Carbon($request->send_at_date);
        $time = new Carbon($request->send_at_time);
        $send_at = (new Carbon($date->toDateString() . ' ' . $time->format('H:i:s'), \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();

        $request->request->set('send_at', $send_at);

        $deployment->fill($request->all());

        $deployment->save();

        return redirect()->route('campaign.drop.index', ['campaign' => $campaign->id]);
    }

    public function updateForm(Campaign $campaign, CampaignSchedule $drop)
    {
        if ($campaign->isExpired) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }
        $viewData['campaign'] = $campaign;
        $viewData['drop'] = $drop;

        return view('campaigns.deployments.edit', $viewData);
    }


    public function delete(CampaignSchedule $deployment)
    {
        try {
            $deployment->status = "Deleted";
            $deployment->save();
            $deployment->delete();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'The drop has been deleted'
        ]);
    }

    public function resume(CampaignSchedule $deployment)
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

    public function pause(CampaignSchedule $deployment)
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

    /**
     * @param \App\Models\Campaign                            $campaign
     * @param \App\Http\Requests\BulkDeploymentRequest $request
     *
     * @return array
     */
    protected function createBulkDeployments(Campaign $campaign, BulkDeploymentRequest $request)
    {
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

            $deployment['send_at'] = (new Carbon($request->get('Group' . $i . '_date') . ' ' . $request->get('Group' . $i . '_time'), \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();

            $deployment = new CampaignSchedule($deployment);

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
            $recipients->where('email', '=', '')
                ->where('phone', '<>', '+1')
                ->whereRaw("length(phone) > 9");
        }
        if ($contact == 'email-only') {
            $recipients->where('email', '<>', '')
                ->where('phone', '=', '+1')
                ->where('email_valid', 1);
        }
        if ($contact == 'no-resp-email') {
            $recipients->whereNotIn('id',
                \DB::table('responses')->where('campaign_id', $campaign->id)->select('recipient_id')->get()->pluck('recipient_id')->toArray())
                ->where('email_valid', 1);
        }
        if ($contact == 'no-resp-sms') {
            $recipients->whereNotIn('id',
                \DB::table('responses')->where('campaign_id', $campaign->id)->select('recipient_id')->get()->pluck('recipient_id')->toArray())
                ->whereRaw("length(phone) > 9");
        }

        if ($info['lists']) {
            $recipients->whereIn('recipient_list_id', $info['lists']);
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
        $i = 0;
        $batch = 0;
        $batches = [];

        try {
            if ($info['max']) {
                foreach ($recipients as $recipient) {
                    if ( ! isset($deployments[$batch])) {
                        Throw new \Exception("More recipients than batches");
                    }
                    $batches[] = ['deployment_id' => $deployments[$batch]->campaign_schedule_id, 'recipient_id' => $recipient->recipient_id];

                    if ($i != 0 && $i % $info['max'] == 0) {
                        $batch++;
                    }
                    $i++;
                }
            } else {
                $batches = $recipients->map(function ($recip) use ($deployments) {
                    return ['deployment_id' => $deployments->first()->id, 'recipient_id' => $recip->recipient_id];
                })->toArray();
            }
        } catch (\Exception $e) {
            \Log::error('DeploymentController@assembleRecipientBatches(): cannot assemble batches => ' . $e->getMessage());
            dd($e->getMessage());
        }

        return $batches;
    }
}
