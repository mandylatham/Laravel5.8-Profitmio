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
        if (! $request->has('params')) {
            abort(422, 'Invalid Parameters');
        }

        $params = collect($request->input('params'));
        $data = $params->only(['name', 'type', 'email_subject', 'email_text',
            'email_html', 'text_message', 'text_message_image', 'send_vehicle_image']);

        $template = new CampaignScheduleTemplate([
            'name' => $params->get('name'),
            'type' => $params->get('type'),
            'email_subject' => $params->get('email_subject'),
            'email_text' => $params->get('email_text'),
            'email_html' => $params->get('email_html'),
            'text_message' => $params->get('text_message'),
        ]);

        // dd($template);

        $template->save();

        return redirect()->route('template.index');
    }

    public function editForm(CampaignScheduleTemplate $template)
    {
        $viewData['template'] = $template;

        return view('template.details', $viewData);
    }

    public function update(CampaignScheduleTemplate $template, Request $request)
    {
        $template->fill($request->input('params'));

        $template->save();

        return redirect()->route('template.edit', ['template' => $template->id]);
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
            'companySelected' => Company::find(session('filters.template.index.company')),
            'q' => session('filters.template.index.q')
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
