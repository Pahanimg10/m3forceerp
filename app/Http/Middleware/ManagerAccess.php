<?php

namespace App\Http\Middleware;

use Closure;

class ManagerAccess
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
        if (!session()->get('LoggedIn') || (!in_array(1, session()->get('user_group')) && !in_array(2, session()->get('user_group')))) {
            return redirect('/home');
        }

        return $next($request);
    }

}