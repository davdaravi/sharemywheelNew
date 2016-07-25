<?php

namespace App\Http\Middleware;

use Closure;

class requestCheck
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
        if($request->has('token'))
        {

            if($request['token']=='SMW16CAR')
            {
               
            }
            else
            {
                $request->session()->flush();
                return redirect('/');
            }
        }
        else
        {
            $request->session()->flush();
            return redirect('/');
        }
        return $next($request);
    }
}
