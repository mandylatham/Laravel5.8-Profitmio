<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CampaignScheduleTemplate;
use App\Http\Requests\NewTemplateRequest;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function createForm()
    {
        $viewData = [];

        return view('template.create', $viewData);
    }

    public function create(Request $request)
    {
        $data = $request->only(['name', 'type', 'email_subject', 'email_text',
            'email_html', 'text_message', 'text_message_image', 'send_vehicle_image']);

        $template = new CampaignScheduleTemplate([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'email_subject' => $request->input('email_subject'),
            'email_text' => $request->input('email_text'),
            'email_html' => $request->input('email_html'),
            'text_message' => $request->input('text_message'),
        ]);

        session()->forget('email_html');

        $template->save();

        return redirect()->route('template.index');
    }

    public function editForm(CampaignScheduleTemplate $template)
    {
        $viewData['template'] = $template;

        return view('template.details', $viewData);
    }

    public function update(Request $request, CampaignScheduleTemplate $template)
    {
        $template->fill($request->only('name', 'text_message', 'email_subject', 'email_text', 'email_html'));

        $template->save();

        return response()->json();
//
//        return redirect()->route('template.edit', ['template' => $template->id]);
    }

    public function show(CampaignScheduleTemplate $template)
    {
        dd($template);
    }

    public function showJson(CampaignScheduleTemplate $template)
    {
        return $template->toJson();
    }

    public function index()
    {
        return view('template.index', [
            'companySelected' => null,
            'q' => '',
        ]);
    }

    public function getForUserDisplay(Request $request)
    {
        $templates = CampaignScheduleTemplate::searchByRequest($request)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $templates;
    }

    public function delete(CampaignScheduleTemplate $template)
    {
        $template->delete();
        return response()->json(['status' => 'success']);
    }
}
