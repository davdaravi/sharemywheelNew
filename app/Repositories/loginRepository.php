<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use Validator; 
use DB;
use Hash;
use App\Http\Controllers\HelperController;
use Illuminate\Foundation\Bus\DispatchesJobs;

class loginRepository
{
    protected $request,$ip;
	public function __construct(Request $request)
	{
		$this->request=$request;
        $this->request->headers->set('Last-Modified', gmdate('D, d M Y H:i:s').'GMT');
        $this->request->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
        $this->request->headers->set('Cache-Control', 'post-check=0, pre-check=0');
        $this->request->headers->set('Pragma', 'no-cache');
        $this->ip=$request->ip();
        //return redirect()->back()->withErrors(["error"=>"Could not add details! Please try again."]);
	}
    //if any user directly enter on this url then he will redirect to login page
    public function getLogin()
    {   
        //session clear is remainning
        //get all advertisment
        if(\Session::has('userId'))
        {
            return redirect('/ridelist');
        }
        else
        {
            $date=date("Y-m-d");
            $selectAd=DB::table('advertisment')->where('start_date','<=',$date)->where('end_date','>=',$date)->where('is_deleted',0)->get();
        
            return view('login',['ad'=>$selectAd]);
        }
        
    }
	public function checkLogin()
	{
        //$route=\Request::getHost();
        $request=$this->request->all();	
        try
		{
            if(\Session::has('userId'))
                return redirect('/ridelist');

            if(isset($request['submit']))
            {
                if(isset($request['username']) && isset($request['password']))
                {
                    $messages=[
                                'username.required'=>'Username is required',
                                'password.required'=>'Password is required'
                            ];
                    $validator=Validator::make($request,[
                            'username'  => 'required',
                            'password'  =>  'required'
                        ],$messages);
                    if($validator->fails())
                    {                
                        //return \View::make('login')->with('data',$request);
                        return redirect()->back()->withErrors($validator)->withInput();
                    }
                    else
                    {
                        //success
                        $userdd=DB::table('users')->whereRaw('(email = "'.$request['username'].'" or username="'.$request['username'].'")')->where('typeoflogin',1)->get();
                        if(count($userdd)>0)
                        {
                            if($userdd[0]->is_deleted==1)
                            {
                                return redirect()->back()->withErrors(["error"=>"User is not allow to login in our system.."])->withInput();
                            }
                            else
                            {
                                $pass=$userdd[0]->password;
                                if (Hash::check($request['password'], $pass))
                                {
                                    $userid=$userdd[0]->id;
                                    $username=$userdd[0]->username;
                                    $profile_pic=$userdd[0]->profile_pic;
                                    $name=ucwords($userdd[0]->first_name." ".$userdd[0]->last_name);
                                    $email=$userdd[0]->email;

                                    //apikey
                                    $apikey=HelperController::keygen();
                                    $api_checker = array(
                                            'source' => 1,//for web
                                            'user_id' =>$userid,
                                            'deviceid' =>"",
                                            'apikey' => $apikey,
                                            'pushid'=> "",
                                        );
                                    $ff=$this->api_checker($api_checker);
                                    $this->request->session()->put('userId',$userid);
                                    $this->request->session()->put('userName',$username);
                                    //$this->request->session()->put('fullName',$name);
                                    $this->request->session()->put('profilePic',$profile_pic);
                                    $this->request->session()->put('email',$email);

                                    return redirect('/ridelist');
                                   
                                }
                                else
                                {
                                    //invalid username or password
                                    return redirect()->back()->withErrors(["error"=>"Username or Password is incorrect.."])->withInput();
                                }
                            }
                            
                        }
                        else
                        {
                            //invalid username or password
                            return redirect()->back()->withErrors(["error"=>"Username or Password is incorrect.."])->withInput();
                        }
                    }
                }
                else
                {
                    //if username and password not set
                    return redirect('/');
                }
            }
            else
            {
                //bad request redirect to login page
                return redirect('/');
            }
		}
		catch(\Exception $e)
		{
			\Log::error('checkLogin function error: ' . $e->getMessage());
			redirect('/');
		}
	}  
    //function call for any entry exists in device token if exists then delete
    //call from checkLogin function
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
            $logArray=array(
                                'users_id'=>$api_checker['user_id'],
                                'ip'=>$this->ip,
                                'deviceid'=>$api_checker['deviceid'],
                                'source'=>$api_checker['source']
                            );  
            $logInsert=DB::table('loginLog')->insert($logArray);
            
            return 1;
        }
        catch(\Exception $e)
        {
            \Log::error('api_checker function error: ' . $e->getMessage());
            redirect('/');
        }
    }
    //for register new user
    public function signup()
    {
        $parameter=$this->request->all();
        
        
        try
        {
            if(\Session::has('userId'))
                return redirect('/ridelist');

            $requiredFieldArray=array("signuser","signpassword","signconfirm","signemail","signcontact","signupSubmit");
            $redirect="/";
            $check=HelperController::checkParameter($requiredFieldArray,$parameter);
            if($check==1)
            {
                session()->flush();
                return redirect('/');
            }
            $messages=[
                'signuser.required'      =>  'Username is required',
                'signuser.unique'        =>  'Username must be unique',
                'signuser.max'           =>  'Username allows only 35 characters',

                'signpassword.required'  =>  'Password is required',

                'signconfirm.required'   =>  'Confirm Password is required',
                'signconfirm.same'       =>  'Confirm Password must match with password',

                'signemail.required'     =>  'Email is required',
                'signemail.unique'       =>  'Email must be unique',
                'signemail.email'        =>  'Email must be in email format',

                'signcontact.required'   =>  'Contact Number is required',
                'signcontact.numeric'    =>  'Contact Number should be numeric',
                'signcontact.min'        =>  'Minimum 10 digits required for contact number',
                'signcontact.max'        =>  'Maximum 10 digits allow for contact number',
                'signcontact.unique'     =>  'Contact number must be unique',
            ];
            $validator=Validator::make($parameter,[
                    'signuser'      =>  'required|unique:users,username|max:35',
                    'signpassword'  =>  'required',
                    'signconfirm'   =>  'required|same:signpassword',
                    'signemail'     =>  'required|email|unique:users,email',
                    'signcontact'   =>  'required|numeric|min:1111111111|max:9999999999|unique:users,phone_no'
                ],$messages);
            if($validator->fails())
            {
                //validation fails
                return redirect()->back()->withErrors($validator)->withInput();
            }
            else
            {
                //success
                $newPassword=Hash::make($parameter['signpassword']);
                $email_random=HelperController::random_password(6);
                $mobile_random=HelperController::random_password(6);
                $image="default.png";
                $insertArray=array("email"=>$parameter['signemail'],"username"=>$parameter['signuser'],"password"=>$newPassword,"typeoflogin"=>1,"phone_no"=>$parameter['signcontact'],"profile_pic"=>"default.png","licence_pic"=>"no_licence.png","mobile_random"=>$mobile_random,"email_random"=>$email_random);
                //insert user data
                $insertUser=DB::table('users')->insertGetId($insertArray);
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

                //sending email
                HelperController::send_email($parameter['signemail'],$email_random);
                //sending sms
                \Queue::push(function($job) use($parameter,$mobile_random){
                    HelperController::send_sms($parameter['signcontact'],$mobile_random);
                        $job->delete();
                    });

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
                $this->request->session()->put('userName',$parameter['signuser']);
                $this->request->session()->put('profilePic',$image);
                $this->request->session()->put('email',$parameter['signemail']);
                return redirect('/ridelist');
            }
        }
        catch(\Exception $e)
        {
            \Log::error('signup function error: ' . $e->getMessage());
            redirect('/');
        }
    }
    public function loadhome()
    {
        try
        {
            if(\Session::has('userId'))
            {
                return view('home');
            }
            else
            {
                redirect('/logout');
            }
        }
        catch(\Exception $e)
        {
            return view('errors.404');
        }
    }
}
