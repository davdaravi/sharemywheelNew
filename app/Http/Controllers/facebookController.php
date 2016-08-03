<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Socialite;
use Hash;
use App\Http\Controllers\HelperController;
use DB;

class facebookController extends Controller
{
    //
    protected $ip,$request;
    public function __construct(Socialite $socialite,Request $request){
           $this->socialite = $socialite;
           $this->ip=$request->ip();
           $this->request=$request;
    }
    public function facebook()
    {
        if(\Session::has('userId'))
        {
            return redirect('/ridelist');
        }
        return Socialite::driver('facebook')->redirect();
    }
    
    public function handleProviderCallback()
    {
        try 
        {
            if(\Session::has('userId'))
            {
                return redirect('/ridelist');
            }
            else
            {
                $user = Socialite::driver('facebook')->user();
                
                $name=$user->name;
                $fb_id=$user->id;
                $fb_token=$user->token;
                // print_r($fb_token);
                // exit;
                $email=$user->email;
                $pic=$user->avatar;
                $gender=$user->user['gender'];
                $image=$user->avatar;
                $content=file_get_contents($image);

                
                if($gender=='male')
                {
                	$gender='M';
                }
                else
                {
                	$gender='F';
                }

                $flag=0;
                if($email!="")
                {
                	$flag=$this->checkUser($email);
                }

            	if($flag==0)
            	{
                    //email not exists
            		//check facebook_id if exists then make entry in device token and login log else make new entry
            		$userdata=DB::table('users')->where('facebook_id',$fb_id)->get();
            		if(count($userdata)>0)
            		{
                        $content_type='jpg';
            			$userImage = 'profile'.rand(100,999).time().".".$content_type;
            			$user_id=$userdata[0]->id;
                        if( is_dir("public/images/profile/".$user_id) == false )
                        { 
                            $path = public_path().'/images/profile/'.$user_id .'/';
                            HelperController::makeDirectory($path, $mode = 0777, true, true);
                            //@chmod("public/images/users/".$userDetails['id'], 0755);
                        }
                        $directory=public_path().'/images/profile/'.$user_id.'/'.$userImage;
                        file_put_contents($directory,$content);
                        $new_path=$userImage;
                        DB::table('users')->where('id',$user_id)->update(['profile_pic'=>$new_path]);
                        $this->request->session()->put('profilePic',$new_path);

            			$apikey=HelperController::keygen();
            			$api_checker = array(
                            'source' => 1,//for web
                            'user_id' =>$user_id,
                            'deviceid' =>"",
                            'apikey' => $apikey,
                            'pushid'=> "",
                        );
                    	$ff=$this->api_checker($api_checker);
                 
    	                $this->request->session()->put('userId',$user_id);
    	                $this->request->session()->put('userName',$userdata[0]->username);
    	                //$this->request->session()->put('profilePic','default.png');
    	                $this->request->session()->put('email',$userdata[0]->email);
    	                return redirect('/ridelist');
            		}
            		else
            		{
            			
            			//make new user with facebook id and logintype 2
                        if($email!="")
                        {
                            $insertArray=array("email"=>$email,"username"=>$name,"facebook_token"=>$fb_token,"facebook_id"=>$fb_id,"typeoflogin"=>2,"gender"=>$gender,"profile_pic"=>"default.png","licence_pic"=>"no_licence.png");
                        }
                        else
                        {
                            $insertArray=array("username"=>$name,"facebook_token"=>$fb_token,"facebook_id"=>$fb_id,"typeoflogin"=>2,"gender"=>$gender,"profile_pic"=>"default.png","licence_pic"=>"no_licence.png");
                        }
            			
    	                //insert user data
    	                $insertUser=DB::table('users')->insertGetId($insertArray);
                        $content_type='jpg';
                        $userImage = 'profile'.rand(100,999).time().".".$content_type;
                        if( is_dir("public/images/profile/".$insertUser) == false )
                        { 
                            $path = public_path().'/images/profile/'.$insertUser .'/';
                            HelperController::makeDirectory($path, $mode = 0777, true, true);
                            //@chmod("public/images/users/".$userDetails['id'], 0755);
                        }
                        $directory=public_path().'/images/profile/'.$insertUser.'/'.$userImage;
                        $new_path=$userImage;
                        file_put_contents($directory,$content);

                        DB::table('users')->where('id',$insertUser)->update(['profile_pic'=>$new_path]);
                        $this->request->session()->put('profilePic',$new_path);
    	                //insert user preference data
    	                $getpreferencedefault=DB::table('preferences_option')->select('id','preference_id')->groupBy('preference_id')->get();
    	                if(count($getpreferencedefault)>0)
    	                {
    	                    for($i=0;$i<count($getpreferencedefault);$i++)
    	                    {
    	                        $ridePreference=array("userid"=>$insertUser,"preferenceId"=>$getpreferencedefault[$i]->preference_id,"pref_optionId"=>$getpreferencedefault[$i]->id);
    	                        $ridepreferenceinsert[]=$ridePreference;
    	                    }
    	                    $insertpreference=DB::table('user_preferences')->insert($ridepreferenceinsert);
    	                    if($insertpreference<0 || $insertpreference=="")
    	                    {
    	                        
    	                    }
    	                }
    	                $apikey=HelperController::keygen();
    	                $api_checker = array(
    	                        'source' => 1,//for web
    	                        'user_id' =>$insertUser,
    	                        'deviceid' =>"",
    	                        'apikey' => $apikey,
    	                        'pushid'=> "",
    	                    );
    	                $ff=$this->api_checker($api_checker);
    	               
    	                $this->request->session()->put('userId',$insertUser);
    	                $this->request->session()->put('userName',$name);
    	                //$this->request->session()->put('profilePic','default.png');
    	                $this->request->session()->put('email',$userdata[0]->email);
    	                return redirect('/ridelist');
            		}
            	}
            	else
            	{
            		//email exists
            		//set session and make entry in device token and login log
            		$user_id=$flag;
            		$userdata=DB::table('users')->where('id',$user_id)->get();

                    $content_type='jpg';
                    $userImage = 'profile'.rand(100,999).time().".".$content_type;
                    if( is_dir("public/images/profile/".$user_id) == false )
                    { 
                        $path = public_path().'/images/profile/'.$user_id .'/';
                        HelperController::makeDirectory($path, $mode = 0777, true, true);
                        //@chmod("public/images/users/".$userDetails['id'], 0755);
                    }
                    $directory=public_path().'/images/profile/'.$user_id.'/'.$userImage;
                    file_put_contents($directory,$content);
                    $new_path=$userImage;
                    DB::table('users')->where('id',$user_id)->update(['profile_pic'=>$new_path]);
                    $this->request->session()->put('profilePic',$new_path);

            		$apikey=HelperController::keygen();
            		
                    $api_checker = array(
                            'source' => 1,//for web
                            'user_id' =>$user_id,
                            'deviceid' =>"",
                            'apikey' => $apikey,
                            'pushid'=> "",
                        );
                    $ff=$this->api_checker($api_checker);
                    

                    $this->request->session()->put('userId',$user_id);
                    $this->request->session()->put('userName',$userdata[0]->username);
                    //$this->request->session()->put('profilePic','default.png');
                    $this->request->session()->put('email',$userdata[0]->email);
                    return redirect('/ridelist');
            	}
            }
        } 
        catch(\Exception $e) 
        {
            return redirect('/facebook');
        }
    }
    //this function is for checking user exists by his email id
    public function checkUser($email)
    {
    	$user=DB::table('users')->where('email',$email)->orWhere('phone_no',$email)->get();
        
    	if(count($user)>0)
    	{
    		return $user[0]->id;
    	}
    	else
    	{
    		return 0;
    	}
    }
    //api checker
    private function api_checker($api_checker)//done
    {
        //remove the currently login user  from all device 
        try
        {
            $onlyDevice = DB::table('device_token')->where('users_id','=',$api_checker['user_id'])
                                   ->get(['id']);
                                  
            if(count($onlyDevice)>0)
            {   
                for($i=0;$i<count($onlyDevice);$i++)
                { 
                    DB::table('device_token')->where('id',$onlyDevice[$i]->id)->delete();
                }    
            }  
             
            $insertUser=DB::table('device_token')->insertGetId(
                                                                array(
                                                                    'users_id'  =>  trim($api_checker['user_id']),
                                                                    'deviceid'  =>  $api_checker['deviceid'],
                                                                    'apikey'    =>  $api_checker['apikey'],
                                                                    'source'    =>  $api_checker['source'],
                                                                    'pushid'    =>  $api_checker['pushid'],
                                                                    'status'    =>  1
                                                                    )
                                                                );
            if($insertUser>0)
            {
            	$logArray=array(
                                'users_id'=>$api_checker['user_id'],
                                'ip'=>$this->ip,
                                'deviceid'=>$api_checker['deviceid'],
                                'source'=>$api_checker['source']
                            );  
            	$logInsert=DB::table('loginLog')->insert($logArray);
            }
            
            return 1;
        }
        catch(\Exception $e)
        {
            \Log::error('api_checker function error: ' . $e->getMessage());
            redirect('/');
        }
    }
}
