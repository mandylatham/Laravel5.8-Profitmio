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
        if (!$user) {
            return redirect('login');
        }
        if (!$user->isAdmin() && !session()->get('activeCompany')) {
            // If user has only 1 company, select that company by default
            if ($user->companies()->count() == 1) {
                session(['activeCompany' => $user->companies()->first()->id]);
            } else {
                return redirect()->route('selector.select-active-company');
            }
        }
        return $next($request);
    }
}
