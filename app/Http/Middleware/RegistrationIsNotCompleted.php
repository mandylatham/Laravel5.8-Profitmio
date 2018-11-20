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
        if (!isset($query['id']) || !isset($query['company'])) {
            return redirect('login');
        }
        $user = User::find($query['id']);
        if (empty($user)) {
            return redirect('login');
        }
        $invitation = $user->companies()->where('companies.id', $query['company'])->first();
        if ($invitation->completed_at) {
            return redirect('login');
        }
        return $next($request);
    }
}
