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
    public function handle($request, Closure $next)
    {
        if($request->user()->hasStadium($request->route('stadium')))
        {
			return $next($request);
		}
        return response()->view('errors.401' , array('error' => 
                array( 'code' => 'INSUFFICIENT ROLE',
                    'description' => 'คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้'))
                , 401);

        return $next($request);
    }

}
