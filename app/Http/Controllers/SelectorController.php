<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateActiveCompanyRequest;
use Illuminate\Http\Request;

class SelectorController extends Controller
{
    /**
     * Return the view to select a company
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        return view('selector.select-company', [
            'activeCompanyId' => get_active_company()
        ]);
    }

    public function updateActiveCompany(UpdateActiveCompanyRequest $request)
    {
        session(['activeCompany' => $request->input('company')]);
        return response()->json(['redirect_url' => route('dashboard')]);
    }
}
