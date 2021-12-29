<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;

class CheckActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        if (Auth::check()) {
            $User = Auth::user();
            $User->last_activity = date('Y-m-d H:i:s');
            $User->save();
        }

        return $next($request);
    }
}
