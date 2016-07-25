<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use Hash;
use App\Http\Requests;
use DB;

class gitController extends Controller
{
    //
    protected $ip,$request;
    public function __construct(Socialite $socialite,Request $request){
        $this->socialite = $socialite;
        $this->ip=$request->ip();
        $this->request=$request;
    }
    public function gitLogin()
    {
    	return Socialite::driver('github')->redirect();
    	//print_r("hi");
    	//exit;
    }
    public function githubresponse()
    {
        try
        {
            $request=$this->request->all();
    
            if($request['code'])
            {
                $user=Socialite::driver('github')->user();
                print_r($user);
                exit;
            
                $token=$user->token;
                $id=$user->id;
                $name=$user->name;
                $email=$user->email;
                $avater=$user->avatar;
                
            }
            else
            {
                return Socialite::driver('github')->redirect();
            }
        }
        catch(\Exception $e)
        {
            \Log::error('githubresponse function error: ' . $e->getMessage());
            return Socialite::driver('github')->redirect();   
        }
    	
    }
}
