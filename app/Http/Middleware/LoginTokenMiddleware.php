<?php

namespace App\Http\Middleware;

use Closure;
use DB;
class LoginTokenMiddleware
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
        $userKey=  $request->input('apikey');
        $userData=DB::table('device_token')
                  ->where('apikey','=',trim((string)$userKey))
                  ->count();
         
         //   dd($userData);  
        if($token!="123456" || $userData!=1) {
            $data['status']=false;
            $data['message']="Invalid Token";
            $data['data']=array();
            return response($data, 401);
        }
        return $next($request);
    }
}
