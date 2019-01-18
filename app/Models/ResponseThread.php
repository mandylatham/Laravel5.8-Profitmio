<?php

namespace App\Models;

class ResponseThread
{
    public $threads;
    protected $recipient;
    protected $campaign;
    protected $appointments;
    protected $responses;

    public function __construct(Campaign $campaign, Recipient $recipient)
    {
        $this->campaign = $campaign;
        $this->recipient = $recipient;
        $this->appointments = Appointment::where('recipient_id', $recipient->id)->get();
        $this->responses = Response::where('campaign_id', $campaign->id)
            ->where('recipient_id', $recipient->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $this->threads = collect([
            'email' => Response::where('campaign_id', $campaign->id)
                ->where('recipient_id', $recipient->id)
                ->where('type', 'email')
                ->get(),
            'text' => Response::where('campaign_id', $campaign->id)
                ->where('recipient_id', $recipient->id)
                ->where('type', 'text')
                ->get(),
            'phone' => Response::where('campaign_id', $campaign->id)
                ->where('recipient_id', $recipient->id)
                ->where('type', 'phone')
                ->get(),
        ]);
    }

    public function sms()
    {
        return $this->threads->get('text');
    }

    public function email()
    {
        return $this->threads->get('email');
    }

    public function phone()
    {
        return $this->threads->get('phone');
    }

    public function all()
    {
        return $this->responses;
    }

    public function getForm()
    {
        $viewData = [
            'appointments' => $this->appointments,
            'campaign' => $this->campaign,
            'recipient' => $this->recipient,
            'threads' => $this->threads,
        ];
        $viewData['emailDrop'] = $viewData['recipient']->drops()
            ->whereType('email')
            ->whereStatus('Completed')
            ->orderBy('send_at', 'desc')
            ->first();

        $viewData['textDrop'] = $this->recipient->drops()
            ->whereType('sms')
            ->whereStatus('Completed')
            ->orderBy('send_at', 'desc')
            ->first();
        // TODO: Get actually time the sms was sent!

        \Log::debug(json_encode($viewData['textDrop']) . "\n" . json_encode($this->recipient));

        return view('partials.response-thread', $viewData);
    }

    public function latest()
    {
        return $this->responses->last()->created_at->diffForHumans();
    }
}
