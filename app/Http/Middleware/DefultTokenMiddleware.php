<?php

namespace App\Http\Middleware;
use App;
use Closure;

class DefultTokenMiddleware
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
      $token = $request->input('token');
        if($token!="123456") {
            $data['status']=false;
            $data['message']="Invalid Token";
            $data['data']=array();
            return response($data, 401);
        }
        
        return $next($request);
    }
}
