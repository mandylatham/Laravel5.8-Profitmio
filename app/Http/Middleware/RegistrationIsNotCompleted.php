<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class RegistrationIsNotCompleted
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
        $query = $request->query();
        if (!isset($query['id'])) {
            return redirect()->route('login');
        }
        $user = User::find($query['id']);
        if (empty($user)) {
            return redirect()->route('login');
        }
        if ($user->isAdmin()) {
            return $next($request);
        }
        $invitation = $user->companies()->where('companies.id', $query['company'])->first();
        if ($invitation->completed_at) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
