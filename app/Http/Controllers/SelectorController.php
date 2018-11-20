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
            'companies' => auth()->user()->companies
        ]);
    }

    public function updateActiveCompany(UpdateActiveCompanyRequest $request)
    {
        session(['activeCompany' => $request->input('company')]);
        return redirect()->route('dashboard');
    }
}
