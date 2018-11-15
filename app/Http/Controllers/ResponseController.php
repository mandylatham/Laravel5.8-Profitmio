<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Classes\MailgunService;
use App\Models\Recipient;
use App\Models\Response;
use App\Models\ResponseThread;
use Illuminate\Http\Request;
use League\Csv\Writer;


class ResponseController extends Controller
{
    /**
     * @var \App\Classes\MailgunService
     */
    protected $mailgun;

    public function __construct(MailgunService $mailgun)
    {
        $this->mailgun = $mailgun;
    }

    public function updateReadStatus(Response $response, Request $request)
    {
        $response->fill(['read' => (int) $request->read]);

        $response->save();

        return $response->toJson();
    }

    public function getCampaignResponses(Campaign $campaign)
    {
        $rawResponses = \DB::select(\DB::raw("
            SELECT
                t.id ,
                t.first_name ,
                t.last_name ,
                t.email ,
                replace(phone, '+1', '') as phone,
                t.year ,
                t.make ,
                t.model ,
                r.id ,
                r.type ,
                r.message ,
                r.incoming ,
                r.created_at,
                r.read as seen
            FROM
                recipients AS t
            JOIN responses AS r ON t.id = r.recipient_id
            WHERE
                t.campaign_id = {$campaign->id}
                AND r.type IN('email' , 'text')
                AND t.deleted_at IS NULL
                AND r.deleted_at IS NULL
            ORDER BY
                t.id ,
                r.type"));

        $responses = [];
        $recipientIds = [];

        foreach ($rawResponses as $raw) {
            if (!in_array($raw->recipient_id, $recipientIds)) {
                $responses[$raw->recipient_id]['meta'] = (object) [
                    'id' => $raw->response_id ?: 0,
                    'name' => ucwords(strtolower($raw->first_name) . " " . strtolower($raw->last_name)),
                    'recipient_id' => $raw->recipient_id,
                    'email' => strtolower($raw->email),
                    'phone' => str_replace('+1', '', $raw->phone),
                    'year' => $raw->year,
                    'make' => $raw->make,
                    'model' => $raw->model,
                ];

                $recipientIds[] = $raw->recipient_id;
            }

            $responses[$raw->recipient_id][$raw->type] = (object) [
                'sent' => $raw->created_at,
                'message' => $raw->message,
                'incoming' => $raw->incoming,
                'seen' => $raw->seen,
            ];
        }

        $viewData['responses'] = $responses;
        $viewData['campaign'] = $campaign;

        return view('campaigns.responses', $viewData);
    }

    public function getResponsesHash(Campaign $campaign)
    {
        $output = \DB::table('responses')
            ->select([\DB::raw("sha2(group_concat(id separator ' '), 256) as checksum, count(*) as response_count")])
            ->where('campaign_id', $campaign->id)
            ->groupBy('campaign_id')
            ->get();
        return $output->toJson();
    }

    public function getEmailHash(Campaign $campaign, Recipient $recipient)
    {
        $output = \DB::table('responses')
            ->select([\DB::raw("sha2(group_concat(id separator ' '), 256) as checksum, count(*) as digest")])
            ->where('campaign_id', $campaign->id)
            ->where('recipient_id', $recipient->id)
            ->where('type', 'email')
            ->groupBy('campaign_id')
            ->get();
        return $output->toJson();
    }

    public function getTextHash(Campaign $campaign, Recipient $recipient)
    {
        $output = \DB::table('responses')
            ->select([\DB::raw("sha2(group_concat(id separator ' '), 256) as checksum, count(*) as digest")])
            ->where('campaign_id', $campaign->id)
            ->where('recipient_id', $recipient->id)
            ->where('type', 'text')
            ->groupBy('campaign_id')
            ->get();
        return $output->toJson();
    }

    public function getListData(Campaign $campaign)
    {
    }

    public function getResponse(Campaign $campaign, Recipient $recipient)
    {
        /*
        $responses = \DB::table('responses')
        ->where('campaign_id', $campaign->id)
        ->where('recipient_id', $recipient->id)
        ->get();
         */

        $response = new ResponseThread($campaign, $recipient);

        return $response->getForm();
    }

    public function getResponseList(Campaign $campaign, Request $request)
    {
        $page = $request->page ?: 1;
        $recipients = Recipient::whereIn('id', \DB::table('responses')->where('campaign_id', $campaign->id)
                ->whereNull('deleted_at')
                ->get()
                ->pluck('recipient_id'))
            ->paginate(15);

        $recipients->withPath('/campaign/' . $campaign->id . '/response-console');

        $viewData['campaign'] = $campaign;
        $viewData['recipients'] = $recipients;

        return view('partials.response-list', $viewData);
    }

    public function getTextThread(Campaign $campaign, Recipient $recipient)
    {
        $responses = new ResponseThread($campaign, $recipient);
        $lastDrop = $recipient->drops()
            ->whereType('sms')
            ->whereNotNull('completed_at')
            ->orderBy('send_at', 'desc')
            ->first();

        $viewData['campaign'] = $campaign;
        $viewData['recipient'] = $recipient;
        $viewData['messages'] = $responses->sms();
        $viewData['textDrop'] = $lastDrop;
        $viewData['type'] = 'sms';

        return view('partials.response-thread-body', $viewData);
    }

    public function getEmailThread(Campaign $campaign, Recipient $recipient)
    {
        $responses = new ResponseThread($campaign, $recipient);
        $lastDrop = $recipient->drops()
            ->whereType('email')
            ->whereNotNull('completed_at')
            ->orderBy('send_at', 'desc')
            ->first();

        $viewData['campaign'] = $campaign;
        $viewData['recipient'] = $recipient;
        $viewData['messages'] = $responses->email();
        $viewData['emailDrop'] = $lastDrop;
        $viewData['type'] = 'email';

        return view('partials.response-thread-body', $viewData);
    }

    /**
     * Get Responders for this campaign
     * @param \App\Models\Campaign $campaign
     */
    public function getAllResponders(Campaign $campaign)
    {
        $responders = \DB::select(\DB::raw("
select recipients.*, replace(recipients.phone, '+1', '') as clean_phone, responses.type, responses.messageCount
from recipients
join (
    select a.recipient_id, a.type, count(*) as messageCount
    from (
       select recipient_id, type
       from responses
       where campaign_id = ?
       order by response_id asc) a
   group by a.recipient_id, a.type ) as responses
  on recipients.recipient_id = responses.recipient_id
where campaign_id = ?
and deleted_at is null;
        "), [$campaign->id, $campaign->id]);

        $responders = array_map(function($data){
            return (array) $data;
        }, $responders);

        $csv = Writer::createFromString('');

        if (count($responders) > 0) {
            $headers = array_keys($responders[0]);
            $rows = array_map(function($data) {
                return array_values($data);
            }, $responders);
            $csv->insertOne($headers);
            $csv->insertAll($rows);
        }

        return response($csv)->header("Content-type", "text/csv")
            ->header("Content-disposition", 'attachment; filename="Campaign_' . $campaign->id . '_Responders.csv"');
    }

    /**
     * Get Non Responders for this campaign
     * @param \App\Models\Campaign $campaign
     */
    public function getNonResponders(Campaign $campaign)
    {
        $responders = \DB::select(\DB::raw("
            select recipients.*, replace(recipients.phone, '+1', '') as clean_phone
            from recipients
            where campaign_id = ?
            and recipient_id not in (select recipient_id from responses where campaign_id = ?)
            and deleted_at is null;
        "), [$campaign->id, $campaign->id]);

        $responders = array_map(function($data){
            return (array) $data;
        }, $responders);

        $csv = Writer::createFromString('');

        if (count($responders) > 0)
        {
            $headers = array_keys($responders[0]);
            $rows = array_map(function($data) {
                return array_values($data);
            }, $responders);

            $csv->insertOne($headers);
            $csv->insertAll($rows);
        }

        return response($csv)->header("Content-type", "text/csv")
            ->header("Content-disposition", 'attachment; filename="Campaign_' . $campaign->id . '_NonResponders.csv"');
    }
}
