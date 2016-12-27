<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckStadium
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    // public function handle($request, Closure $next)
    // {


    //     return $next($request);
    // }

    public function handle($request, Closure $next)
    {
        //if (Auth::check()) {
            $user = Auth::user();
            $stadium = $user->stadium;
            $tmpStadium = null;
            if ($request->stadium != null) {
                $tmpStadium = $request->stadium;

                if ($tmpStadium != $stadium) {
                    return "You don't have privileges to access this resource";
                }
                
            }
        //}


        return $next($request);
    }
}
