<?php

namespace App\Http\Middleware;

use Closure;

class CheckForActiveCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        if ($user) {
            // Set active company to support company
            if ($user->isAdmin() && !session()->get('activeCompany')) {
                session([
                    'activeCompany' => $user->companies()
                        ->where('companies.type', 'support')
                        ->where('company_user.is_active', true)
                        ->first()
                        ->id
                ]);
            }
            if (!$user->isAdmin() && !session()->get('activeCompany')) {
                // If user has only 1 company, select that company by default
                if (count($user->getActiveCompanies()) == 1) {
                    session(['activeCompany' => $user->getActiveCompanies()[0]->id]);
                } else if (!$request->is('selector*')) {
                    return redirect()->route('selector.select-active-company');
                }
            }
        }
        return $next($request);
    }
}
