<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests;
use App\Users;
use App\DeviceToken;
use App\LoginLog;
use App\Http\Controllers\HelperController;
use Validator;    
use DB;
use Hash;
use Illuminate\Contracts\Auth\Authenticatable;
use Auth;

class Api extends BaseController
{
    public $ip;
    /*---------- User Login  -------------------*/ 
    public function login(Request $request)//done
    {   
        try
        {
            $type=$request->input('loginType');
            if ($type =='1')
            {
                $this->ip=$request->ip();

                $userdetail = array(
                                    'email' => $request->input('email'),
                                    'password' => $request->input('password'),
                                    'deviceid' => $request->input('deviceid'),
                                    'source'=>$request->input('source'),
                                    'pushid'=>$request->input('pushid')    
                                    );
                $validator = Validator::make($request->all(), [
                            'email' => 'required',
                            'password' => 'required',
                            'loginType'=>'required',
                            'token'=>'required',
                            'source'=>'required',
                            'pushid'=>'required'
                        ]);
                 
                if ($validator->fails()) 
                {
                    $messages = $validator->messages();             
                    foreach ($messages->all() as $key=>$value) 
                    {
                        $errors[$key]= $value;
                    }
                            
                    $response['message'] = "Opps something worng";
                    $response['errormessage'] = $errors;
                    $response['status'] = false;
                    $response['data'] = array();
                } 
                else
                { 

                    $response = $this->authcheckNormalLogin($userdetail);//custome function call for check user authication

                }
            } 
            else if($type =='2')
            {
                
                $userdetail = array(
                                     'email' => $request->input('email'), 
                                    'deviceid' => $request->input('deviceid'),
                                    'source'=>$request->input('source'),
                                    'fbtoken'=>$request->input('fbtoken'),
                                    'pushid'=>$request->input('pushid')
                                    );
                $validator = Validator::make($request->all(), [
                            'email' => 'required',
                            'loginType'=>'required',
                            'token'=>'required',
                            'source'=>'required',
                            'fbtoken'=>'required',
                            'pushid'=>'required'
                        ]);
                $response = $this->authcheckNormalFbLogin($userdetail);
                
            }    
            else
            {
                $response['message'] = "Opps something worng";
                $response['status'] = false;
                $response['erromessage']=array();
                $response['data'] = array();
            }    
            return response($response,200);   
        }
        catch (\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        } 
    }
    public function authcheckNormalLogin($userinfo)//done
    {
        // dd($userinfo);  
        $response = array();
        $validator = Validator::make($userinfo, [
                           'email' => 'email',
                        ]);
        $new_password=$userinfo['password'];
        $userdd=DB::table('users')->whereRaw('(email = "'.$userinfo['email'].'" or username="'.$userinfo['email'].'")')->get();
        if(count($userdd)>0)
        {
            $pass=$userdd[0]->password;
            if (Hash::check($new_password, $pass))
            {
                //   dd($user);
                $userdata['userId']=$userdd[0]->id;
                $userdata['email']=$userdd[0]->email;
                $userdata['username']=$userdd[0]->username;
                $userdata['isverifyemail']=$userdd[0]->isverifyemail;
                $userdata['isverifyphone']=$userdd[0]->isverifyphone;
                $userdata['rideoffers']=$userdd[0]->rideOffer;
                $userdata['first_name']=$userdd[0]->first_name;
                $userdata['last_name']=$userdd[0]->last_name;
                $userdata['gender']=$userdd[0]->gender;
                $userdata['birthdate']=$userdd[0]->birthdate;
                $userdata['phone_no']=$userdd[0]->phone_no;
                $userdata['description']=$userdd[0]->description;
                $userdata['profile_pic']=$userdd[0]->profile_pic;
                $userdata['licence_pic']=$userdd[0]->licence_pic;
                if($userdd[0]->profile_pic=='default.png')
                {
                    $userdata['profile_pic_full_path']="/images/default.png";
                }
                else
                {
                    $userdata['profile_pic_full_path']="/images/profile/".$userdd[0]->id."/".$userdd[0]->profile_pic;
                }

                if($userdd[0]->licence_pic=='no_licence.png')
                {
                    $userdata['licence_pic_full_path']="/images/no_licence.png";
                }
                else
                {
                    $userdata['licence_pic_full_path']="/images/licence/".$userdd[0]->id."/".$userdd[0]->licence_pic;
                }
                $userdata['created_at']=$userdd[0]->created_at;
                $userdata['email_token']="";
                $userdata['mobile_token']="";
                
                $userdata['apikey'] = HelperController::keygen1($userinfo['email'], $userinfo['deviceid']);
          
                $api_checker = array(
                                        'source' => $userinfo['source'],
                                        'user_id' =>$userdd[0]->id,
                                        'deviceid' => $userinfo['deviceid'],
                                        'apikey' => $userdata['apikey'],
                                        'pushid'=> $userinfo['pushid'],
                                    );
                $userdata['deviceinfo'] = $this->apichecker($api_checker);
                $response['data'] = $userdata;
                $response['erromessage']=array();
                $response['message'] = "Login success";
                $response['status'] = true;
            }
            else
            {
                $response['message'] = "Please enter correct email and password.";
                $response['status'] = false;
                $response['erromessage']=array();
                $response['data'] = array();
            }
        }   
        else
        {
            $response['message'] = "Please enter correct email and password.";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
        }            
        return $response;
    }
    public function authcheckNormalFbLogin($userinfo)//done
    {

        $response = array();
        try 
        {   
            // Get facebook profile using access token
            $fbProfile = new \Facebook\Facebook([
                            'app_id' => '1617698085210568',
                            'app_secret' => '12ee6ef7ac3b69d370f1edc3c0c2ad4b',
                            'default_graph_version' => 'v2.6',
                    ]);
         
            $fbtoken=(string)$userinfo['fbtoken'];
            $fbProfile->setDefaultAccessToken($fbtoken);
            $responseData= $fbProfile->get('/me?fields=email,first_name,last_name,gender,birthday,picture');
            $userData = $responseData->getGraphUser();

            // validate email address
            if(! filter_var($userData->getEmail(),FILTER_VALIDATE_EMAIL))
                return "error";
                    
            //dd($userCount);
            $gender = ($userData->getGender() == 'male') ? 'm' : 'f';
             
            // get user image and save on the server
            $userImage = '';
         
            $userdata=array();
            $userdata['email']=$userData->getEmail();
            $userdata['first_name']=$userData->getFirstName();
            $userdata['last_name']=$userData->getLastName();
            $userdata['birthdate']=($userData->getBirthday())?$userData->getBirthday():'';
            $userdata['gender']=$gender;
            $userdata['isverifyemail']=1;
            $userdata['typeoflogin']=2;
            $userdata['facebook_token']=$userinfo['fbtoken'];
            $userdata['licence_pic']='no_licence.png';      
            $userCount=Users::where('email',$userData->getEmail())->count();
            
            if($userCount> 0) 
            {
                $userDataFlag=Users::where(array('email'=>$userdata['email']))->update(array('facebook_token'=>$fbtoken));
            }
            else 
            {
                //$insertedId=Users::create($userData);
                $userDataFlag=DB::table('users')->insert($userdata);
            }
        
            $userDetails=Users::where(array('email'=>$userdata['email']))
                         ->first();
       
         
            if($userData->getPicture()->getUrl() != '') 
            {
                //echo $userData->getPicture();exit;
                // CURL request to get image propertires
              
                $ch = curl_init ($userData->getPicture()->getUrl());
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_exec ($ch);
              
                // Get image type
                $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                if($content_type == 'image/jpeg' || $content_type == 'image/jpg') $userImage = 'user'.rand(100,999).time().'.jpg';
                else if($content_type == 'image/png') $userImage = 'user'.rand(100,999).time().'.png';
                else $userImage = 'user'.rand(100,999).time().'.jpg';
              
                //Get the file
                $image = file_get_contents($userData->getPicture()->getUrl());
                if( is_dir("public/images/users".$userDetails['id']) == false )
                {
                    //  mkdir("public".$userDetails['id']."/",0755);
                    // chmod("public/images", 0755);
                    $path = public_path().'/images/users/'.$userDetails['id'].'/';
                    HelperController::makeDirectory($path, $mode = 0755, true, true);
                   //@chmod("public/images/users/".$userDetails['id'], 0755);
                }           							//Store in the filesystem.
                
                $imageFile = fopen(base_path()."/public/images/users/".$userDetails['id'].'/'.$userImage, "w");
                fwrite($imageFile, $image);
                fclose($imageFile);
                //   resizeImage(base_path()."/public/images/users/".$userImage,base_path()."/public/images/users/".$userImage,300,NULL);
            }
            
            $userdata['profile_pic']=$userImage; 
            $userData=Users::where(array('email'=>$userdata['email']))->update(array('profile_pic'=>$userImage));
           
            $userdata['profile_pic']=url("/images/users/".$userDetails['id'], $parameters = [], $secure = null)."/".$userImage; 
            $userdata['licence_pic']=$userDetails['licence_pic'];
            $userdata['username']=$userDetails['username'];
            $userdata['isverifyemail']=$userDetails['isverifyemail'];
            $userdata['isverifyphone']=$userDetails['isverifyphone'];
            $userdata['rideoffers']=$userDetails['rideOffer'];
            $userdata['phone_no']=$userDetails['phone_no'];
            $userdata['description']=$userDetails['description'];
            $dd=(array)$userDetails['created_at'];
            $ee=$dd['date'];

            $userdata['created_at']=$ee;
            $userdata['mobile_token']="";
            $userdata['email_token']="";
            $userdata['userId']=$userDetails['id'];
            $userdata['apikey'] = HelperController::keygen1($userdata['email'], $userinfo['deviceid']);
            $api_checker = array(
                                'source' => $userinfo['source'],
                                'user_id' =>$userDetails->id,
                                'deviceid' => $userinfo['deviceid'],
                                'apikey' => $userdata['apikey'],
                                'pushid'=> $userinfo['pushid']
                                 
                                );
            $userdata['deviceinfo']=$this->apichecker($api_checker); 
              // dd($userinfo); 
            
            unset($userdata['facebook_token']);
            $response['data'] = $userdata;
            $response['message'] = "Login success";
            $response['erromessage']=array();
            $response['status'] = true;
        }
        catch(\Exception $e)
        {
            // print_r($e);exit;
            $response['data'] = array();
            //  $response['errormessage'] = $e;
            $response['message'] = "Opps something worng";
            $response['erromessage']=array();
            $response['status'] = false;
        }
        return $response;
    }
     
    private function apichecker($api_checker)//done
    {

        ///remove the currently login user
        $userDevice = DeviceToken::where('deviceid',trim($api_checker['deviceid']))
                       ->get(['id']);
         
        if($userDevice)
        {   
            foreach($userDevice as $device)
            { 
                //  DB::table('users')->where('id', '<', 100)->delete();
                DeviceToken::destroy($device->id);
            } 
        }   
  
        //remove the currently login user  from all device 
        
        $onlyDevice = DeviceToken::where('users_id','=',$api_checker['user_id'])
                                   ->get(['id']);
        if($onlyDevice)
        {   
            foreach($onlyDevice as $device)
            { 
                DeviceToken::destroy($device->id);
            }    
        }  
         
//           DeviceToken::create(array(
//                    'users_id' =>trim($api_checker['user_id']),
//                    'deviceid' =>$api_checker['deviceid'],
//                    'apikey' =>$api_checker['apikey'],
//                    'source' =>$api_checker['source'],
//                    'pushid'=>$api_checker['pushid'],
//                    'status' => 1
//                ));
        $insertUser=DB::table('device_token')->insertGetId(
                                                        array(
                                                        'users_id' =>trim($api_checker['user_id']),
                                                        'deviceid' =>$api_checker['deviceid'],
                                                        'apikey' =>$api_checker['apikey'],
                                                        'source' =>$api_checker['source'],
                                                        'pushid'=>$api_checker['pushid'],
                                                        'status' => 1
                                                        )
                                                    );
              // dd('ss');  
        $this->loginStroe(array(
                                   'user_id'=>$api_checker['user_id'],
                                   'deviceid'=>$api_checker['deviceid'],
                                   'source'=>$api_checker['source']
                                ));
        $response['devicestatus'] = '1';
        $response['devicemessage'] = 'record  insert successfully';
        return $response;
    }
    private function loginStroe($loginUserLog)//done
    {

        LoginLog::create(array(
                    'users_id' => $loginUserLog['user_id'],
                    'ip ' =>  $this->ip,
                    'deviceId' => $loginUserLog['deviceid'],
                    'source' => $loginUserLog['source'],
                ));
     }
    /*-----------------------------------------------*/
    
    /*---------------- Registe ----------------------*/
    public function register(Request $request)//done
    {  
        try
        {
            $errors=array();
            $parameter=$request->all();
            $userdetail = array(
                                'email' => $request->input('email'),
                                'password' => $request->input('password'),
                                'deviceid' => $request->input('deviceid'),
                                'source'=>$request->input('source'),
                                'username'=>$request->input('username'),
                                'source'=>$request->input('source'),
                                'loginType'=>$request->input('loginType')
                            );
            $validator = Validator::make($userdetail, [
                        'email' => 'required | email | unique:users,email',
                        'password' => 'required',
                        'loginType'=>'required',
                        'source'=>'required',
                        'username'=>'required | unique:users,username'
                    ]);
                     //dd($validator->fails());
            if ($validator->fails()) 
            {
                $messages = $validator->messages();             
                foreach ($messages->all() as $key=>$value) 
                {
                    $errors[$key]= $value;
                }
                            
                $response['message'] = "Opps something worng";
                $response['errormessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);   
            } 
            else
            {   
                $flg=0;
                $userData['email']=$request->input('email');
                $userData['password']=Hash::make($request->input('password'));
                $userData['username']=$request->input('username');
                $userData['typeoflogin']=$request->input('loginType'); 
                $userData['licence_pic']='no_licence.png';
                $userData['profile_pic']='default.png'; 
                if(isset($parameter['mobile']))
                {
                    $userData['phone_no']=$parameter['mobile'];
                    $flg=1;
                    $ff=array('phone_no'=>$parameter['mobile']);
                    $validator1= Validator::make($ff, [
                         'phone_no' => 'min:9|max:12|unique:users,phone_no',
                     ]);
                     if($validator1->fails())
                     {
                        $messages = $validator1->messages();             
                        foreach ($messages->all() as $key=>$value) 
                        {
                            $errors[$key]= $value;
                        }
                                    
                        $response['message'] = "Opps something worng";
                        $response['errormessage'] = $errors;
                        $response['status'] = false;
                        $response['data'] = array();
                        return response($response,401);  
                    }
                }
                $email_random=$this->random_password(6);
                $mobile_random=$this->random_password(6);

                //   $userData['status']=1;
                //$insertedId=Users::create($userData);
                $insertUser=DB::table('users')->insertGetId($userData);
                if($insertUser>0)
                {
                    $userNew1=DB::table('users')->select('id as userId','email','username','isverifyemail','isverifyphone','rideOffer as rideoffers','first_name','last_name','gender','birthdate','phone_no','description','created_at','profile_pic','licence_pic')->where('id',$insertUser)->get();
                    $userNew['userId']=$userNew1[0]->userId;
                    $userNew['email']=$userNew1[0]->email;
                    $userNew['username']=$userNew1[0]->username;
                    $userNew['isverifyemail']=$userNew1[0]->isverifyemail;
                    $userNew['isverifyphone']=$userNew1[0]->isverifyphone;
                    $userNew['rideoffers']=$userNew1[0]->rideoffers;
                    $userNew['rideoffers']=$userNew1[0]->rideoffers;
                    $userNew['first_name']=$userNew1[0]->first_name;
                    $userNew['last_name']=$userNew1[0]->last_name;
                    $userNew['gender']=$userNew1[0]->gender;
                    $userNew['birthdate']=$userNew1[0]->birthdate;
                    $userNew['phone_no']=$userNew1[0]->phone_no;
                    $userNew['description']=$userNew1[0]->description;
                    $userNew['created_at']=$userNew1[0]->created_at;
                    $userNew['profile_pic']=$userNew1[0]->profile_pic;
                    $userNew['licence_pic']=$userNew1[0]->licence_pic;

                    if($userNew1[0]->profile_pic=='default.png')
                    {
                        $userNew['profile_pic_full_path']="/images/default.png";
                    }
                    else
                    {
                        $userNew['profile_pic_full_path']="/images/profile/".$userNew['userId']."/".$userNew1[0]->profile_pic;
                    }

                    if($userNew1[0]->licence_pic=='no_licence.png')
                    {
                        $userNew['licence_pic_full_path']="/images/no_licence.png";
                    }
                    else
                    {
                        $userNew['licence_pic_full_path']="/images/licence/".$userNew['userId']."/".$userNew1[0]->licence_pic;
                    }
                    //$userNew=Users::where('id',$insertUser)
                       //     ->first();
                    //Token Table userdata
                    if($flg==1)
                    {
                        $userNew['mobile_token']=$mobile_random;    
                    }
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

                    $userNew['email_token']=$email_random;

                    //sending mail
                    $this->send_email($userData['email'],$email_random);
                    //sending sms
                    if($flg==1)
                    {
                        \Queue::push(function($job) use($parameter,$mobile_random){
                        $this->send_sms($parameter['mobile'],$mobile_random);
                            $job->delete();
                        });
                        
                    }
                    $api_checker['user_id'] =$insertUser;
                    $api_checker['deviceid']=$request->input('deviceid');;
                    $api_checker['apikey']=HelperController::keygen1($userData['email'], $request->input('deviceid'));
                    $userNew['apikey']=$api_checker['apikey'];
                    $api_checker['source']=$request->input('source');
                    $api_checker['pushid']=$request->input('pushid');

                    $response['data'] =$userNew;
                    $response['message'] = "Login success";
                    $response['status'] = true;
                    $response['erromessage']=array();
                    $response['deviceinfo'] = $this->apichecker($api_checker);    
                    return response($response,200);                 
                   //  $response = $this->authcheck($userdetail);//custome function call for check user authication
                }
                else
                {
                    $response['data'] = array();
                    //  $response['errormessage'] = $e;
                    $response['message'] = "register not successfully";
                    $response['erromessage']=array();
                    $response['status'] = false;
                    return response($response,401);
                }
            }
        }
        catch(\Exception $e)
        {
            // print_r($e);exit;
            $response['data'] = array();
            //  $response['errormessage'] = $e;
            $response['message'] = "Opps something worng";
            $response['erromessage']=array();
            $response['status'] = false;
            return response($response,401);
        }
    }
    
     /*----------------End Registe ----------------------*/
    public function editBasicProfile(Request $request)//done
    {   
        try
        {
            $getArray=$request->all();
        
            $validator = Validator::make($getArray, [
                                'username' => 'required',
                                'gender' => 'required',
                                'birthdate'=>'required'
                            ]);
                     
            if ($validator->fails()) 
            {
                
                $messages = $validator->messages();             
                foreach ($messages->all() as $key=>$value) 
                {
                    $errors[$key]= $value;
                }   
                $response['message'] = "Opps something worng";
                $response['status'] = false;
                $response['erromessage']=$errors;
                $response['data'] = array();
                return response($response,401);   
            } 
            else
            {   
                $userId = $this->userDetails($getArray['apikey']);
                $userId=$userId->id;            
                
                $checkUserName=DB::table('users')
                                ->where('username',$getArray['username'])
                                ->where('id','!=',$userId)
                                ->get();
                if(count($checkUserName)>0)
                {
                    $response['message'] = "Username already exits";
                    $response['status'] = false;
                    $response['data'] = array();
                    $response['erromessage']=array();
                    return response($response,401); 
                }
                else
                {
                    if(isset($getArray['firstname']))
                    {
                        $firstname=$getArray['firstname'];
                    }
                    else
                    {
                        $firstname="";
                    }

                    if(isset($getArray['lastname']))
                    {
                        $lastname=$getArray['lastname'];
                    }
                    else
                    {
                        $lastname="";
                    }

                    if(isset($getArray['about']))
                    {
                        $about=$getArray['about'];
                    }
                    else
                    {
                        $about="";
                    }
                    $upadtaArray=array("username"=>$getArray['username'],"gender"=>$getArray['gender'],"first_name"=>$firstname,"last_name"=>$lastname,"birthdate"=>$getArray['birthdate'],"description"=>$about);
                    $upquery=DB::table('users')->where('id',$userId)->update($upadtaArray);
                    if($upquery>=0)
                    {
                        $userData=DB::table('users')
                                        ->select('email','username','first_name as firstName','last_name as lastName','gender','birthdate','phone_no as Mobile','description as about','profile_pic as pic')
                                        ->where('id',$userId)
                                        ->get();
                        if($userData[0]->pic=='default.png')
                        {
                            $userData[0]->profile_pic_full_path="/images/".$userData[0]->pic;    
                        }
                        else
                        {
                            $userData[0]->profile_pic_full_path="/images/profile/".$userData[0]->id."/".$userData[0]->pic;
                        }
                        $response['message'] = "Profile Updated Successfully";
                        $response['status'] = true;
                        $response['erromessage']=array();
                        $response['data'] =$userData;
                        return response($response,200);
                    }
                    else
                    {
                        $response['message'] = "Opps something worng";
                        $response['status'] = false;
                        $response['data'] = array();
                        $response['erromessage']=array();
                        return response($response,401); 
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $response['data'] = array();
            //  $response['errormessage'] = $e;
            $response['message'] = "Opps something worng";
            $response['erromessage']=array();
            $response['status'] = false;
            return response($response,401);
        }
    }
    
    public function editEmail(Request $request)//done
    {   
        try
        {
            
            $userId = $this->userDetails($request->input('apikey'));
            $userId=$userId->id;
            $userdetail = array(
                                'email' => $request->input('email'),
                            );
            $validator = Validator::make($userdetail, [
                                'email' => 'required|email|unique:users,email'
                        ]);
                     
            if ($validator->fails())
            {               
                $messages = $validator->messages();             
                foreach ($messages->all() as $key=>$value) 
                {
                    $errors[$key]= $value;
                }
                            
                $response['message'] = "Opps something worng";
                $response['errormessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);
            } 
            else
            {   
                $userData['email']=$request->input('email');

                $userEmail=Users::where('id',$userId)
                                ->update(array('email'=>$userdetail['email']));
                $response['message'] = "Your Email updated successfully";
                $response['status'] = true;
                $response['errormessage'] = array();
                $response['data'] = array();
                return response($response,200);
            }    
            //  $response = $this->authcheck($userdetail);//custome function call for check user authication
               
        }
        catch(\Exception $e)
        {
            // print_r($e);exit;
            $response['data'] = array();
            //  $response['errormessage'] = $e;
            $response['message'] = "Opps something worng";
            $response['erromessage']=array();
            $response['status'] = false;
            return response($response,401);
        }
    }
    
    /*---------------------------------------------------------
     * Change password
     */
    public function changePassword(Request $request)//done
    {   
        try
        {
            $errors=array();
            $userdetail = array(
                                   
                                    'currentpassword' => $request->input('currentpassword'),
                                    'apikey' => $request->input('apikey'),
                                    'newpassword'=>$request->input('newpassword'),
                                    'token'=>$request->input('token')
                                );
            $userId = $this->userDetails($request->input('apikey'));
            $userId=$userId->id;
            $validator = Validator::make($userdetail, [
                                'currentpassword' => 'required',
                                'newpassword'=>'required',
                                'apikey'=>'required',
                                'token'=>'required'
                            ]);
                     
            if ($validator->fails()) 
            {
               
                $messages = $validator->messages();             
                foreach ($messages->all() as $key=>$value) 
                {
                    $errors[$key]= $value;
                }
                $response['message'] = "Opps something worng";
                $response['status'] = false;
                $response['data'] = array();
                $response['errormessage']=$errors;
                return response($response,401);
            } 
            else
            {   
                $selectUserData=DB::table('users')->where('id',$userId)->get();
                if(count($selectUserData)>0)
                {
                    $currentpassword=Hash::make($userdetail['currentpassword']);
                    $selectPassword=DB::table('users')->select('password')->where('id',$userId)->get();
                    if(Hash::check($userdetail['currentpassword'], $selectPassword[0]->password))
                    {
                        $newpassword=Hash::make($userdetail['newpassword']);
                        $updatePassword=DB::table('users')->where('id',$userId)->update(['password'=>$newpassword]);
                        if($updatePassword>=0)
                        {
                            $response['message'] = "Your password has been changed successfully";
                            $response['status'] = true;
                            $response['data'] = array();
                            $response['errormessage']=array();
                            return response($response,200);
                        }
                        else
                        {
                            $response['message'] = "Opps something worng";
                            $response['status'] = false;
                            $response['data'] = array();
                            $response['errormessage']=$errors;
                            return response($response,401);
                        }
                    }
                    else
                    {
                        $response['message'] = "your current password is wrong.";
                        $response['status'] = false;
                        $response['data'] = array();
                        $response['errormessage']=array();
                        return response($response,401);
                    }
                }
                else
                {
                    $response['message'] = "User is not exists..";
                    $response['status'] = false;
                    $response['data'] = array();
                    $response['errormessage']=array();
                    return response($response,401);
                }
            }
        }
        catch(\Exception $e)
        {
            $response['data'] = array();
            //  $response['errormessage'] = $e;
            $response['message'] = "Opps something worng";
            $response['erromessage']=array();
            $response['status'] = false;
            return response($response,401);
        }
        
    }
    
    
    /* offers ride ****/
    public function offerAridepost(Request $request)//done
    { 
        try
        {
            $offerRideArray=$request->all();
            $validator = Validator::make($offerRideArray, [  
                                                                    "departure"=>'required',
                                                                    "departurecity"=>'required',
                                                                    "departure_lat_long"=>'required',
                                                                    "arrival"=>'required',
                                                                    "arrivalcity"=>'required',
                                                                    "arrival_lat_long"=>'required',
                                                                    "ladies_only"=>'required',
                                                                    "is_round_trip"=>'required',
                                                                    "isDaily"=>'required',
                                                                    "departure_date"=>'required',
                                                                    "carId"=>'required',
                                                                    "luggage_size"=>'required',
                                                                    "can_detour"=>'required',
                                                                    "leave_on"=>'required',
                                                                    "cost_per_seat"=>'required',
                                                                    "offer_seat"=>'required',
                                                                    "licence_verified"=>'required'
      //                                                              "comment"=>'required'
                                                               ]);
            if ($validator->fails()) 
            {
                $errors=array();
                $messages = $validator->messages();
                foreach ($messages->all() as $key=>$value) {
                $errors[$key]= $value;
                }
                $response['message'] = "Something went wrong";
                $response['erromessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);
            }
            else
            {
                $userId = $this->userDetails($request->input('apikey'));
                $userId=$userId->id;
                if(isset($offerRideArray['return_date']))
                {
                    if(!empty($offerRideArray['return_date']))
                    {
                        $return_date=date("Y-m-d H:i:s",strtotime($offerRideArray['return_date']));    
                    }
                    else
                    {
                        $return_date="0000-00-00 00:00:00";
                    }
                }
                else
                {
                    $return_date="0000-00-00 00:00:00";
                }

                if(isset($offerRideArray['return_time']))
                {
                    if(!empty($offerRideArray['return_time']))
                    {
                        $return_time=date("H:i:s",strtotime($offerRideArray['return_time']));
                    }
                    else
                    {
                        $return_time="00:00:00";
                    }
                }
                else
                {
                    $return_time="00:00:00";
                }
                /*if($offerRideArray['is_round_trip']==1 && $offerRideArray['isDaily']==0)
                {
                    $cost_of_seat=$offerRideArray['cost_per_seat']*2;
                }
                else
                {*/
                $cost_of_seat=$offerRideArray['cost_per_seat'];
                //}

                if(isset($offerRideArray['departureFullAddress']) && $offerRideArray['departureFullAddress']!="")
                {
                    $departureFullAddress=$offerRideArray['departureFullAddress'];
                }
                else
                {
                    $departureFullAddress=$offerRideArray['departure'].", ".$offerRideArray['departurecity'];
                }

                if(isset($offerRideArray['arrivalFullAddress']) && $offerRideArray['arrivalFullAddress']!="")
                {
                    $arrivalFullAddress=$offerRideArray['arrivalFullAddress'];
                }
                else
                {
                    $arrivalFullAddress=$offerRideArray['arrival'].", ".$offerRideArray['arrivalcity'];
                }
                $offerParams = array(
                                        "departure"=>$offerRideArray['departure'],
                                        "departureCity"=>strtolower(trim($offerRideArray['departurecity'])),
                                        "departureOriginal"=>$departureFullAddress,
                                        "departure_lat_long"=>$offerRideArray['departure_lat_long'],
                                        "arrival"=>$offerRideArray['arrival'],
                                        "arrivalCity"=>strtolower(trim($offerRideArray['arrivalcity'])),
                                        "arrivalOriginal"=>$arrivalFullAddress,
                                        "arrival_lat_long"=>$offerRideArray['arrival_lat_long'],
                                        "ladies_only"=>$offerRideArray['ladies_only'],
                                        "is_round_trip"=>$offerRideArray['is_round_trip'],
                                        "isDaily"=>$offerRideArray['isDaily'],
                                        "departure_date"=>date("Y-m-d H:i:s",strtotime($offerRideArray['departure_date'])),
                                        "return_date"=>$return_date,
                                        "return_time"=>$return_time,
                                        "carId"=>$offerRideArray['carId'],
                                        "luggage_size"=>$offerRideArray['luggage_size'],
                                        "can_detour"=>$offerRideArray['can_detour'],
                                        "leave_on"=>$offerRideArray['leave_on'],
                                        "userId"=>$userId,
                                        "cost_per_seat"=>$cost_of_seat,
                                        "offer_seat"=>$offerRideArray['offer_seat'],
                                        "available_seat"=>$offerRideArray['offer_seat'],
                                        "licence_verified"=>$offerRideArray['licence_verified'],
                                        "comment"=>$offerRideArray['comment'],
                                    );
                
                //begin transaction
                DB::beginTransaction();
                //dd($offerParams);
                $insertRide=DB::table('rides')->insertGetId($offerParams);

               
                if($insertRide>0)
                {  
                    $d=$this->check_uniq_city(strtolower(trim($offerRideArray['departurecity'])));
                    $e=$this->check_uniq_city(strtolower(trim($offerRideArray['arrivalcity'])));
                    if($d==0)
                    {
                      $x=array("city_name"=>strtolower(trim($offerRideArray['departurecity'])));
                        DB::table('city_master')->insert($x);
                    }
                    if($e==0)
                    {
                       $x=array("city_name"=>strtolower(trim($offerRideArray['arrivalcity'])));
                        DB::table('city_master')->insert($x);
                    }
                    
                    if(isset($offerRideArray['waypointname']))
                    {
                        $waypointname=$offerRideArray['waypointname'];
                        $waypoint_lat_lng=$offerRideArray['waypoint_lat_lng'];
                        $waypointcity=$offerRideArray['waypointcity'];
                        $countViaPoint=count($waypointname);
                        if(isset($offerRideArray['waypointFullAddress']))
                        {
                            $waypointFullAddress=$offerRideArray['waypointFullAddress'];
                        }
                        else
                        {
                            $waypointFullAddress=array();
                        }
                        if($countViaPoint>0)
                        {        
                            for($i=0;$i<$countViaPoint;$i++)    
                            {
                                $g=$this->check_uniq_city(strtolower(trim($waypointcity[$i])));
                                if($g==0)
                                {
                                    $x=array("city_name"=>strtolower(trim($waypointcity[$i])));
                                    DB::table('city_master')->insert($x);
                                }

                                if(isset($waypointFullAddress[$i]) && $waypointFullAddress[$i]!="")
                                {
                                    $wayfulladdress=$waypointFullAddress[$i];
                                }
                                else
                                {
                                    $wayfulladdress=$waypointname[$i].", ".$waypointcity[$i];
                                }
                                $rideViaParams = array(
                                            "rideId"=>$insertRide,
                                            "city"=>$waypointname[$i],
                                            "city_lat_long"=>$waypoint_lat_lng[$i],
                                            "cityOriginal"=>$wayfulladdress,
                                            "cityName"=>strtolower(trim($waypointcity[$i]))
                                        );   
                                $insertViaRide=DB::table('ride_via_points')->insertGetId($rideViaParams);
                                if($insertViaRide<=0 || $insertViaRide=="")
                                {
                                    DB::rollback();
                                    $response['message'] = "Opps something wrong";
                                    $response['status'] = false;
                                    $response['data'] = array();
                                    $response['erromessage']=array();
                                    return response($response,400);
                                }
                            }  
                        }
                    }
                  
                    $ridepreferenceinsert=array();
                    $getpreferenceList=DB::table('user_preferences')->select('preferenceId','pref_optionId')->where('userid',$userId)->where('isDeleted',0)->get();
                    if(count($getpreferenceList)>0)
                    {
                        for($i=0;$i<count($getpreferenceList);$i++)
                        {
                            $ridePreference=array("userId"=>$userId,"rideId"=>$insertRide,"preferenceId"=>$getpreferenceList[$i]->preferenceId,"pref_optionId"=>$getpreferenceList[$i]->pref_optionId);
                            $ridepreferenceinsert[]=$ridePreference;
                        }
                        $insertpreference=DB::table('user_ride_preferences')->insert($ridepreferenceinsert);
                        if($insertpreference<0 || $insertpreference=="")
                        {
                            DB::rollback();
                            $response['message'] = "Opps something wrong";
                            $response['status'] = false;
                            $response['data'] = array();
                            $response['erromessage']=array();
                            return response($response,400);
                        }
                    }
                    else
                    {
                        $getpreferencedefault=DB::table('preferences_option')->select('id','preference_id')->groupBy('preference_id')->get();
                        if(count($getpreferencedefault)>0)
                        {
                            for($i=0;$i<count($getpreferencedefault);$i++)
                            {
                                $ridePreference=array("userId"=>$userId,"rideId"=>$insertRide,"preferenceId"=>$getpreferencedefault[$i]->preference_id,"pref_optionId"=>$getpreferencedefault[$i]->id);
                                $ridepreferenceinsert[]=$ridePreference;
                            }
                            $insertpreference=DB::table('user_ride_preferences')->insert($ridepreferenceinsert);
                            if($insertpreference<0 || $insertpreference=="")
                            {
                                DB::rollback();
                                $response['message'] = "Opps something wrong";
                                $response['status'] = false;
                                $response['data'] = array();
                                $response['erromessage']=array();
                                return response($response,400);
                            }
                        }
                    }
                    DB::commit();
                    $response['message'] = "Your Ride has been offered successfully..";
                    $response['status'] = true;
                    $response['data'] = array();
                    $response['erromessage']=array();
                    return response($response,200);
                }
                else
                {
                    $response['message'] = "Opps something wrong";
                    $response['status'] = false;
                    $response['data'] = array();
                    $response['erromessage']=array();
                    return response($response,400);
                }        
            }
        }
        catch(\Exception $e)
        {
            $response['data'] = array();
            //  $response['errormessage'] = $e;
            $response['message'] = "Opps something worng";
            $response['erromessage']=array();
            $response['status'] = false;
            return response($response,401);
        }
    }
        
    public function AddCar(Request $request)//done
    { 

        try
        {
            $CarArray=$request->all();
           
            $validator = Validator::make($CarArray, [
                                                            "brand"=>'required',
                                                            "model"=>'required',
                                                            "noofseats"=>'required|integer',
                                                            "vehicaltype"=>'required|integer',
                                                            "comfort"=>'required|integer',
                                                            "color"=>'required|integer',
                                                            "apikey"=>'required'
                                                    ]);
            if ($validator->fails())
            {
                $errors=array();
                $messages = $validator->messages();
                foreach ($messages->all() as $key=>$value) {
                $errors[$key]= $value;
                }
                $response['message'] = "Something went wrong";
                $response['erromessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);
            }
            else
            {       
                $CarArray['userId'] =  $this->userDetails($request->input('apikey'));
                $CarArray['userId']=$CarArray['userId']->id;
                //dd($CarArray['userId']);
                $carParams = array(
                                    "userId"=> $CarArray['userId'],
                                    "car_make"=>$request->input('brand'),
                                    "car_type"=>$request->input('vehicaltype'),
                                    "car_model"=>$request->input('model'),
                                    "comfortId"=>$request->input('comfort'),
                                    "colorId"=>$request->input('color'),
                                    "no_of_seats"=>$request->input('noofseats'),
                                    "is_deleted"=>'0'
                                   );
                $carPhoto=$request->file('vehical_pic');
                $userImage='car_default.png';
                if($request->hasFile('vehical_pic')) 
                {
                    $content_type=$carPhoto->getClientOriginalExtension();
                    $nameImage=$carPhoto->getClientOriginalName();
                    //   dd($content_type);
                    // Get image type
                    $userImage = 'cars'.rand(100,999).time().".".$content_type;

                     //Get the file
                     
                    if( is_dir("public/images/cars/".$CarArray['userId']) == false )
                    {
                        $path = public_path().'/images/cars/'.$CarArray['userId'] .'/';
                         HelperController::makeDirectory($path, $mode = 0755, true, true);
                          //@chmod("public/images/users/".$userDetails['id'], 0755);
                    }     
                    $destinationPath=  public_path()."/images/cars/".$CarArray['userId'].'/';
                       //Store in the filesystem.
                    $data=$request->file('vehical_pic')->move($destinationPath, $userImage);
    //                     
                         //   resizeImage(base_path()."/public/images/users/".$userImage,base_path()."/public/images/users/".$userImage,300,NULL);
                }
        
                $carParams['vehical_pic']=$userImage;        							//Store in the filesystem.
                 
                $insertCar=DB::table('car_details')->insert($carParams);
                if($insertCar>0)
                {
                    $response['message'] = "Your Car has been Added successfully..";
                    $response['status'] = true;
                    $response['erromessage']=array();
                    $response['data'] = array();
                    return response($response,200);
                }
                else
                {
                    $response['message'] = "Opps something wrong";
                    $response['status'] = false;
                    $response['erromessage']=array();
                    $response['data'] = array();
                    return response($response,400);
                }        
            }
        }
        catch (\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }   
    }
    
    public function UpdateCar(Request $request)//done
    {
            $CarArray = $request->all();
            $validator = Validator::make($CarArray, [       "carid" => "required",
                                                            "brand"=>'required',
                                                            "model"=>'required',
                                                            "noofseats"=>'required|integer',
                                                            "vehicaltype"=>'required|integer',
                                                            "comfort"=>'required|integer',
                                                            "color"=>'required|integer',
                                                            "apikey"=>'required'
                                                    ]);
            if ($validator->fails()) 
            {
                $errors = array();
                $messages = $validator->messages();
                foreach ($messages->all() as $key => $value) {
                    $errors[$key] = $value;
                }
                $response['message'] = "Something went wrong";
                $response['erromessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response, 401);
            }
            else
            {

                $CarArray['userId'] =  $this->userDetails($request->input('apikey'));
                $CarArray['userId']= $CarArray['userId']->id;
               
                $carParams = array(
                                        "userId"=> $CarArray['userId'],
                                        "car_make"=>$request->input('brand'),
                                        "car_type"=>$request->input('vehicaltype'),
                                        "car_model"=>$request->input('model'),
                                        "comfortId"=>$request->input('comfort'),
                                        "colorId"=>$request->input('color'),
                                        "no_of_seats"=>$request->input('noofseats'),
                                        "is_deleted"=>'0'
                                   );
                
                $carPhoto=$request->file('vehical_pic');
                $userImage='';
                //   $image = fopen("php://input", "w");
                //dd($image);
                if($request->hasFile('vehical_pic')) 
                {
                    $content_type=$carPhoto->getClientOriginalExtension();
                    $nameImage=$carPhoto->getClientOriginalName();
                    //   dd($content_type);
                    // Get image type
                    $userImage = 'cars'.rand(100,999).time().".".$content_type;

                    //Get the file
                     
                    if( is_dir("public/images/cars/".$CarArray['userId']) == false )
                    {
                        $path = public_path().'/images/cars/'.$CarArray['userId'] .'/';
                        HelperController::makeDirectory($path, $mode = 0755, true, true);
                          //@chmod("public/images/users/".$userDetails['id'], 0755);
                    }     
                    $destinationPath=  public_path()."/images/cars/".$CarArray['userId'].'/';
                    //Store in the filesystem.
                    $data=$request->file('vehical_pic')->move($destinationPath, $userImage);
//                     
                             //   resizeImage(base_path()."/public/images/users/".$userImage,base_path()."/public/images/users/".$userImage,300,NULL);
                }
            
                // dd($userImage);
                if($userImage!='')
                {       
                    $carParams['vehical_pic']=$userImage;        							//Store in the filesystem.
                }

                $updateCar = DB::table('car_details')
                        ->where('userId', $CarArray['userId'])
                        ->where('id', $request->input('carid'))
                        ->update($carParams); 
                        
                if ($updateCar >=0) 
                {
                    $response['message'] = "Your Car has been updated successfully..";
                    $response['status'] = true;
                    $response['erromessage']=array();
                    $response['data'] = array();
                    return response($response, 200);
                } 
                else 
                {
                    $response['message'] = "Opps something wrong";
                    $response['status'] = false;
                    $response['erromessage']=array();
                    $response['data'] = array();
                    
                    return response($response, 400);
                }
            }
    }
    
    public function myCar(Request $request)//done
    { 
        try
        {
            $CarArray=$request->all();
        
            $CarArray['userId'] = $this->userDetails($request->input('apikey'));
            $CarArray['userId']=$CarArray['userId']->id; 
            
            
            $data= DB::table('car_details')
                    ->select('car_details.id as carId','car_details.userId as UserId','car_make as make','car_details.car_type as typeId','vehical_type.name as type','car_model as model','vehical_pic as image','car_details.comfortId','car_details.colorId','comfort_master.name as comfort','color.color','no_of_seats as seats','car_details.created_date as date')
                    ->leftJoin('vehical_type','car_details.car_type','=','vehical_type.id')
                    ->leftJoin('comfort_master','car_details.comfortId','=','comfort_master.id')
                    ->leftJoin('color','car_details.colorId','=','color.id')
                    ->where('userId',$CarArray['userId'])
                    ->where('car_details.is_deleted',0)
                    ->get();   
            foreach ($data as $key => $value) {
                # code...
                if($data[$key]->image=="car_default.png")
                {
                    $data[$key]->car_image_full_path="/images/car_default.png";
                }
                else
                {
                    $data[$key]->car_image_full_path="/images/cars/".$data[$key]->UserId."/".$data[$key]->image;
                }
            }
            $response['message'] ="success";
            $response['status'] = true;
            $response['erromessage']=array();
            $response['data'] = $data;
            return response($response, 200);
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
        
    }
   
   
    public function deleteCar(Request $request)//done
    { 
        try
        {
            $CarArray = $request->all();
       
            $validator = Validator::make($CarArray, [
                        "car_id" => "required",
                        "apikey"=>"required"
                       ]);
            if ($validator->fails()) 
            {
                $errors = array();
                $messages = $validator->messages();
                foreach ($messages->all() as $key => $value) {
                    $errors[$key] = $value;
                }
                $response['message'] = "Something went wrong";
                $response['erromessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response, 401);
            }
            else
            {   
                $CarArray['userId'] = $this->userDetails($request->input('apikey'));
                $CarArray['userId']=$CarArray['userId']->id; 
                
                $selectCar=DB::table('car_details')
                                ->where('id',$CarArray['car_id'])
                                ->where('userId',$CarArray['userId'])
                                ->get();
                if(count($selectCar)>0)
                {
                    $isDeleted=$selectCar[0]->is_deleted;
                    if($isDeleted==1)
                    {
                        $response['message'] = "car has been already deleted..";
                        $response['erromessage'] = array();
                        $response['status'] = true;
                        $response['data'] = array();
                        return response($response, 200);
                    }
                    else
                    {
                        DB::table('car_details')->where('id',$CarArray['car_id'])
                                        ->where('userId',$CarArray['userId'])
                                        ->update(array('is_deleted'=>1));
                        $response['message'] = "Your Car has been deleted successfully..";
                        $response['status'] = true;
                        $response['data'] = array();
                        return response($response, 200);
                    }
                }
                else
                {
                    $response['message'] = "Something went wrong";
                    $response['erromessage'] = array();
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response, 401);
                }
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    }
   //search ride
   /* public function searchRide(Request $request)
    { 
        $serachArray = $request->all();
       
        $validator = Validator::make($serachArray, [
                    "departurecity" => "required",
                    "arrivalcity"=>"required"
                   ]);
        if ($validator->fails()) 
        {
            $errors = array();
            $messages = $validator->messages();
            foreach ($messages->all() as $key => $value) {
                $errors[$key] = $value;
            }
            $response['message'] = "Something went wrong";
            $response['erromessage'] = $errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response, 401);
        }
        else
        {
            $userId = $this->userDetails($request->input('apikey'));
            $userId=$userId->id;
            $departurecity=$request->input('departurecity');
            $arrivalcity=$request->input('arrivalcity');
            $ladies_only=$request->input('ladies_only');



            echo $today=date("Y-m-d H:i:s");    
        
            $rideQuery=DB::table('rides')
                        ->leftjoin('ride_via_points', 'rides.id', '=', 'ride_via_points.rideId')
                        ->where('userId','<>',$userId)
                        ->where('departurecity','=',strtolower(trim($departurecity)))
                        ->where('arrivalcity','=',strtolower(trim($arrivalcity)))
                         ->whereOr('ride_via_points.cityName','=',strtolower(trim($arrivalcity)))
                        ->where('departure_date','>=',$today);
                        
            if(!empty($ladies_only)) 
            {
                $rideQuery=$rideQuery->where('ladies_only','=',1);
            }           
            $rideQuery=$rideQuery->toSql();
        
           // DB::enableQueryLog();
           // dd(DB::getQueryLog());
            dd($rideQuery);
            $temprespo = array();
            foreach($ridedata as $rideKey => $rideval){
                $temprespo[] = array("userId"=>$rideval->userId,
                             "Departure"=>$rideval->departure,
                             "Arrival"=>$rideval->arrival,
                             "OfferSeat"=>$rideval->offer_seat,
                             "AvailableSeat"=>$rideval->available_seat,
                             "CostPerSeat"=>$rideval->cost_per_seat,
                             "IsRoundTrip"=>$rideval->is_round_trip,
                             "LaidesOnly"=>$rideval->ladies_only,
                             "Ratting"=>$rideval->ratting,
                             "Created"=>$rideval->created_date);

            }
            $response['message'] = "search successfully..";
            $response['status'] = true;
            $response['erromessage']=array();
            $response['data'] = $temprespo;
		    return response($response,400);	
       }
    }  */
    public function searchRide(Request $request)
    { 
        try
        {

        $serachArray = $request->all();
       
        $validator = Validator::make($serachArray, [
                    "city" => "required",
                    "date"=>"required"
                   ]);
        if ($validator->fails()) 
        {
            $errors = array();
            $messages = $validator->messages();
            foreach ($messages->all() as $key => $value) {
                $errors[$key] = $value;
            }
            $response['message'] = "Something went wrong";
            $response['erromessage'] = $errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response, 401);
        }
        else
        {
            $userId = $this->userDetails($request->input('apikey'));
            $userId=$userId->id;
            $departurecity=$request->input('source');
            $arrivalcity=$request->input('destination');
            $city=$request->input('city');
            $date=$request->input('date');
            $ladies_only=$request->input('ladies_only');
            $twowheeler=$request->input('twowheeler');     
            $fourwheeler=$request->input('fourwheeler');     
            $isdaily=$request->input('isdaily'); 

            $today=date("Y-m-d H:i:s");    
        
            $rideQuery=DB::table('rides')
                        ->leftjoin('users','rides.userId','=','users.id')
                        ->leftjoin('ride_via_points', 'rides.id', '=', 'ride_via_points.rideId')
                        ->leftjoin('luggage', 'rides.luggage_size', '=', 'luggage.id')
                        ->leftjoin('leave_on', 'rides.leave_on', '=', 'leave_on.id')
                        ->leftjoin('detour', 'rides.can_detour', '=', 'detour.id')
                        ->leftjoin('car_details','rides.carId', '=', 'car_details.id')
                        //->where('rides.userId','<>',$userId)
                        ->where('rides.status','=',0)
                        ->whereRaw('(departure_date >="'.$date.
                                     '" or (isDaily=1'. 
                                     ' and departure_date >="'.$date. 
                                     '" and return_date <="'.$date.'" ))'.
                                     '');
            if(!empty($city) && empty($departurecity)) 
            {
                 $rideQuery=$rideQuery->whereRaw('departurecity = "'.strtolower(trim($city)).'"'
                                                 .'or ( is_round_trip=1 and arrivalCity="'.strtolower(trim($city)).'") 
                                                ');
                        
            }
            
            if(!empty($isdaily)) 
            {
                 $rideQuery=$rideQuery->where('rides.isDaily','=',$isdaily);
            }
            
            
            
            if(!empty($twowheeler) ) 
            {
                 $rideQuery=$rideQuery->where('car_details.car_type','=',1);
            }
            
            if(!empty($fourwheeler)) 
            {
                 $rideQuery=$rideQuery->where('car_details.car_type','=',2);
            }
            
            
            if(!empty($departurecity)) 
            {
                 $rideQuery=$rideQuery->whereRaw('( departurecity = "'.strtolower(trim($departurecity)).'"'
                                                 .' or ( is_round_trip=1 and arrivalCity="'.strtolower(trim($departurecity)).'"))'
                                                );
                         
                        
            }
            
            if(!empty($arrivalcity)) 
            {
                 $rideQuery=$rideQuery->whereRaw('('
                                                    .'('
                                                     .'arrivalCity = "'.strtolower(trim($arrivalcity))
                                                     .'" or ride_via_points.cityName = "'.strtolower(trim($arrivalcity)).'"' 
                                                    .')  or '
                                                     .'(is_round_trip=1 and  departurecity="'.strtolower(trim($arrivalcity)).'"'
                                                      . ' or  ride_via_points.cityName = "'.strtolower(trim($arrivalcity)).'"'
                                                        .')'   
                                                .')');
            }
            
            if(!empty($ladies_only) && $ladies_only==1) 
            {
                $rideQuery=$rideQuery->where('rides.ladies_only','=',1);
            }      
            
            $rideQuery=$rideQuery->get(['rides.userId',
                                        'rides.carId',
                                        'rides.departure as source',
                                        'rides.departure_lat_long as sourcename_lat_long',
                                        'rides.departureCity as sourcename',
                                        'rides.arrival as destination',
                                        'rides.arrival_lat_long as destination_lat_long',
                                        'rides.arrivalCity as destinationname',
                                        'rides.offer_seat',
                                        'rides.available_seat',
                                        'rides.cost_per_seat',
                                        'rides.departure_date',
                                        'rides.return_date',
                                        'rides.is_round_trip',
                                        'rides.isDaily',
                                        'rides.ladies_only',
                                        'luggage.name as luggage_size',
                                        'leave_on.name as leave_on',
                                        'detour.name as can_detour',
                                        'rides.view_count as view_count',
                                        'rides.licence_verified',
                                        'rides.comment',
                                        'users.rating as ratting', 
                                        'rides.created_date',
                                        'rides.id as rideid',
                                        
             
                                        ]);
            
            $temprespo = array();
            $i=0;
            foreach($rideQuery as $rideKey => $rideval){
                 $tempdata=array();
                  foreach($rideval as $key=>$value)
                  {
                      $tempdata[$key]=$value;
                  }    
                  $temprespo[$i]['ridedetails']=$tempdata;
//            
               
                $i++;
            }
            $response['message'] = "search successfully..";
            $response['status'] = true;
            $response['erromessage']=array();
            $response['data'] = $temprespo;
            return response($response,200); 
       }

        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);   
        }
    } 

   /* public function searchList(Request $request)
    { 
        try
        {
            $serachArray = $request->all();
            $validator = Validator::make($serachArray, [
                        "city" => "required",
                        "date"=>"required|date_format:Y-m-d H:i:s"
                       ]);
            if ($validator->fails()) 
            {
                $errors = array();
                $messages = $validator->messages();
                foreach ($messages->all() as $key => $value) {
                    $errors[$key] = $value;
                }
                $response['message'] = "Something went wrong";
                $response['erromessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response, 401);
            }
            else
            {
                $userId = $this->userDetails($request->input('apikey'));
                $userId=$userId->id;
                $city=$request->input('city');
                $date=$request->input('date');
                
                $today=date("Y-m-d H:i:s");    
            
                $rideQuery=DB::table('rides')->select('rides.id as rideId','rides.userId','departure as source','arrival as destination','offer_seat as offerSeat','available_seat as availableSeat','cost_per_seat as seatPrice','departure_date as departureDate','return_date as returnDate','is_round_trip as roundTrip','isDaily as Daily','ladies_only as ladiesOnly','status','view_count as viewOffer','comment','ratting as rating',DB::raw('CONCAT(first_name, " ", last_name) AS userName'))
                            ->leftjoin('users', 'rides.userId', '=', 'users.id')
                            ->where('rides.userId','<>',$userId)
                            ->where('rides.status','=',0)
                            ->whereRaw('(departure_date >="'.$date.'" or (isDaily=1 and departure_date >="'.$date.'"))');
                if(!empty($city)) 
                {
                     $rideQuery=$rideQuery->whereRaw('departurecity = "'.strtolower(trim($city)).'"'
                                                     .'or ( is_round_trip=1 and arrivalCity="'.strtolower(trim($city)).'" and return_date>="'.$date.'") 
                                                    ');
                }

                $rideQuery=$rideQuery->get();

                $temprespo = array();
                $i=0;
                foreach($rideQuery as $rideKey => $rideval)
                {
                        $tempdata=array();
                        $wayarray=array();
                        $ff=array();
                        foreach($rideval as $key=>$value)
                        {
                            $tempdata[$key]=$value;
                        }    
                        $temprespo[$i]['ridedetails']=$tempdata;
                        $rideid=$rideval->rideId;
                        $waypoints=DB::table('ride_via_points')->select('city','cityName','rideId')->where('is_deleted',0)->where('rideId',$rideid)->get();
                       
                        foreach($waypoints as $wayKey => $wayval){
                            foreach($wayval as $key=>$value)
                            {
                                $wayarray[$key]=$value;
                            } 
                            $ff[]=$wayarray;
                            
                        }
                        $temprespo[$i]['waypoints']=$ff;
                        $i++;
                }
                $response['message'] = "search successfully..";
                $response['status'] = true;
                $response['erromessage']=array();
                $response['data'] = $temprespo;
                return response($response,200); 
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    } 
    */
    public function searchList(Request $request)
    {   
        try
        {
            $serachArray = $request->all();
            $validator = Validator::make($serachArray, [
                            "date"=>"required|date"
                        ]);
            if ($validator->fails()) 
            {
                $errors = array();
                $messages = $validator->messages();
                foreach ($messages->all() as $key => $value) {
                    $errors[$key] = $value;
                }
                $response['message'] = "Something went wrong";
                $response['erromessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response, 401);
            }
            else
            {
                $userId = $this->userDetails($request->input('apikey'));
                $userId=$userId->id;
                $city=$request->input('city');
                $date=$request->input('date');
                $source=$request->input('source');
                $destination=$request->input('destination');
                $twowheeler=$request->input('twowheeler');
                $fourwheeler=$request->input('fourwheeler');
                $ladies_only=$request->input('ladiesonly');
                $daily=$request->input('daily');
                if($request->input('departurePlace') && $request->input('departurePlace')!="")
                {
                    $departurePlace=trim($request->input('departurePlace'));
                }
                else
                {
                    $departurePlace="";
                }


                if($request->input('arrivalPlace') && $request->input('arrivalPlace')!="")
                {
                    $arrivalPlace=trim($request->input('arrivalPlace'));
                }
                else
                {
                    $arrivalPlace="";
                }


                $today=date("Y-m-d H:i:s");    
               
                $rideQuery=DB::table('rides')->select('rides.id as rideId','rides.userId','departureOriginal as source','arrivalOriginal as destination','offer_seat as offerSeat','available_seat as availableSeat','cost_per_seat as seatPrice','departure_date as departureDate','return_date as returnDate','return_time as ReturnTime','is_round_trip as roundTrip','isDaily as Daily','ladies_only as ladiesOnly','status','view_count as viewOffer','comment','users.rating as rating',DB::raw('CONCAT(first_name, " ", last_name) AS userName'),'users.profile_pic as pic','users.birthdate as age')
                            ->leftJoin('users', 'rides.userId', '=', 'users.id')
                            ->leftJoin('ride_via_points', 'rides.id', '=', 'ride_via_points.rideId')
                            ->leftJoin('car_details','rides.carId','=','car_details.id')
                            //->where('rides.userId','<>',$userId)
                            ->where('rides.status','=',0)
                            //->whereRaw('(departure_date >="'.$date.'" or (isDaily=1 and departure_date <="'.$date.'") or (isDaily=0 and is_round_trip=1 and return_date>="'.$date.'"))');
                            ->whereRaw('((is_round_trip=0 and isDaily=0 and departure_date >="'.$date.'") or (is_round_trip=1 and isDaily=0 and (departure_date>="'.$date.'" or return_date>="'.$date.'")) or (isDaily=1 and is_round_trip=1 and (date(departure_date)<="'.$date.'" or date(departure_date)>="'.$date.'")) or (is_round_trip=0 and isDaily=1 and (date(departure_date)<="'.$date.'" or (date(departure_date)>="'.$date.'"))))');

                if(!empty($destination) && empty($source))
                {
                    $response['message'] = "Source is required";
                    $response['status'] = false;
                    $response['erromessage']=array();
                    $response['data'] = array();
                    return response($response,400);
                }
                else if(!empty($destination) && !empty($source))
                {
                    //search by destination and source both
                    if(empty($arrivalPlace) && !empty($departurePlace))
                    {
                        $rideQuery=$rideQuery->whereRaw('((is_round_trip=0 and departure="'.$departurePlace.'" and departureCity="'.$source.'" and (arrivalCity="'.$destination.'" or ride_via_points.cityName="'.$destination.'")) or (is_round_trip=1 and ((departureCity="'.$source.'" and departure="'.$departurePlace.'" and (arrivalCity="'.$destination.'" or ride_via_points.cityName="'.$destination.'")) or (arrivalCity="'.$source.'" and arrival="'.$departurePlace.'" and (departureCity="'.$destination.'" or ride_via_points.cityName="'.$destination.'")))))');
                    }
                    else if(empty($departurePlace) && !empty($arrivalPlace))
                    {
                        $rideQuery=$rideQuery->whereRaw('((is_round_trip=0 and departureCity="'.$source.'" and ((arrivalCity="'.$destination.'" and arrival="'.$arrivalPlace.'") or (ride_via_points.cityName="'.$destination.'" and ride_via_points.city="'.$arrivalPlace.'"))) or (is_round_trip=1 and ((departureCity="'.$source.'" and ((arrivalCity="'.$destination.'" and arrival="'.$arrivalPlace.'") or (ride_via_points.cityName="'.$destination.'" and ride_via_points.city="'.$arrivalPlace.'"))) or (arrivalCity="'.$source.'" and ((departureCity="'.$destination.'" and departure="'.$arrivalPlace.'") or (ride_via_points.cityName="'.$destination.'" and ride_via_points.city="'.$arrivalPlace.'"))))))'); 
                    }
                    else if(!empty($departurePlace) && !empty($arrivalPlace))
                    {
                        $rideQuery=$rideQuery->whereRaw('((is_round_trip=0 and departureCity="'.$source.'" and departure="'.$departurePlace.'" and ((arrivalCity="'.$destination.'" and arrival="'.$arrivalPlace.'") or (ride_via_points.cityName="'.$destination.'" and ride_via_points.city="'.$arrivalPlace.'"))) or (is_round_trip=1 and ((departureCity="'.$source.'" and departure="'.$departurePlace.'" and ((arrivalCity="'.$destination.'" and arrival="'.$arrivalPlace.'") or (ride_via_points.cityName="'.$destination.'" and ride_via_points.city="'.$arrivalPlace.'"))) or (arrivalCity="'.$source.'" and arrival="'.$departurePlace.'" and ((departureCity="'.$destination.'" and departure="'.$arrivalPlace.'") or (ride_via_points.cityName="'.$destination.'" and ride_via_points.city="'.$arrivalPlace.'"))))))');  
                    }
                    else
                    {
                        $rideQuery=$rideQuery->whereRaw('((is_round_trip=0 and departureCity="'.$source.'" and (arrivalCity="'.$destination.'" or ride_via_points.cityName="'.$destination.'")) or (is_round_trip=1 and ((departureCity="'.$source.'" and (arrivalCity="'.$destination.'" or ride_via_points.cityName="'.$destination.'")) or (arrivalCity="'.$source.'" and (departureCity="'.$destination.'" or ride_via_points.cityName="'.$destination.'")))))');  
                    }
                }
                else if(empty($destination) && empty($source))
                {
                    //search by city
                    if(empty($city))
                    {
                        $response['message'] = "City is required";
                        $response['status'] = false;
                        $response['erromessage']=array();
                        $response['data'] = array();
                        return response($response,400);
                    }
                    else
                    {
                        $rideQuery=$rideQuery->whereRaw('((is_round_trip=0 and departureCity="'.$city.'") or (is_round_trip=1 and (departureCity="'.$city.'" or arrivalCity="'.$city.'")))');
                    }
                }
                else
                {
                    //search by source
                    if($departurePlace=="")
                    {
                        $rideQuery=$rideQuery->whereRaw('((is_round_trip=0 and departureCity="'.$source.'") or (is_round_trip=1 and (departureCity="'.$source.'" or arrivalCity="'.$source.'")))');    
                    }
                    else
                    {
                        $rideQuery=$rideQuery->whereRaw('((is_round_trip=0 and departureCity="'.$source.'" and departure="'.$departurePlace.'") or (is_round_trip=1 and ((departureCity="'.$source.'" and  departure="'.$departurePlace.'") or (arrivalCity="'.$source.'" and arrival="'.$departurePlace.'"))))');
                    }
                }
                if(!empty($twowheeler) && $twowheeler==1)
                {
                    if(!empty($fourwheeler) && $fourwheeler==1)
                    {
                        $rideQuery=$rideQuery->whereRaw('(car_details.car_type=1 or car_details.car_type=2)');
                    }
                    else
                    {
                        $rideQuery=$rideQuery->where('car_details.car_type',1);    
                    }
                }
                else if(!empty($fourwheeler) && $fourwheeler==1)
                {
                    if(!empty($twowheeler) && $twowheeler==1)
                    {
                        $rideQuery=$rideQuery->whereRaw('(car_details.car_type=1 or car_details.car_type==2)');
                    }
                    else
                    {
                        $rideQuery=$rideQuery->where('car_details.car_type',2);       
                    }
                }
                else
                {

                }
                if(!empty($ladies_only) && $ladies_only==1)
                {
                      $rideQuery=$rideQuery->where('rides.ladies_only',1);      
                }
                if(!empty($daily) && $daily==1)
                {
                    $rideQuery=$rideQuery->where('isDaily',1);
                }
               /* if(!empty($city)) 
                {
                     $rideQuery=$rideQuery->whereRaw('departurecity = "'.strtolower(trim($city)).'"'
                                                     .'or ( is_round_trip=1 and arrivalCity="'.strtolower(trim($city)).'" and return_date>="'.$date.'") 
                                                    ');
                }*/
                
                $rideQuery=$rideQuery->groupBy('rides.id');
                $rideQuery=$rideQuery->orderBy('rides.departure_date','asc');
                $rideQuery=$rideQuery->get();

                $temprespo = array();
                $i=0;
                foreach($rideQuery as $rideKey => $rideval)
                {
                    $tempdata=array();
                    $wayarray=array();
                    $ff=array();
                    foreach($rideval as $key=>$value)
                    {
                        if($key=="age")
                        {
                            if($value!="")
                            {
                                $value=date_diff(date_create($value), date_create('today'))->y;
                            }
                            else
                            {
                                $value=$value;
                            }
                        }
                        $tempdata[$key]=$value;
                    }  
                    if($rideQuery[$rideKey]->pic=="default.png")
                    {
                        $tempdata['profile_pic_full_path']="/images/".$rideQuery[$rideKey]->pic;      
                    }
                    else
                    {
                        $tempdata['profile_pic_full_path']="/images/profile/".$rideQuery[$rideKey]->userId."/".$rideQuery[$rideKey]->pic;
                    }
                    $temprespo[$i]['ridedetails']=$tempdata;
                    $rideid=$rideval->rideId;
                    $waypoints=DB::table('ride_via_points')->select('city','cityName','rideId')->where('is_deleted',0)->where('rideId',$rideid)->get();
                   
                    foreach($waypoints as $wayKey => $wayval)
                    {
                        foreach($wayval as $key=>$value)
                        {
                            $wayarray[$key]=$value;
                        } 
                        $ff[]=$wayarray;   
                    }
                    $temprespo[$i]['waypoints']=$ff;
                    $i++;
                }
                $response['message'] = "search successfully..";
                $response['status'] = true;
                $response['erromessage']=array();
                $response['data'] = $temprespo;
                return response($response,200); 
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    } 
   /**
    *----------------- Save Preference Details --------------------------------------
    */
    public function SavePreference(Request $request)//done
    { 
        $PrefArray=$request->all();
        $flag=0;
        $errors=array();
        try
        {
            if(isset($PrefArray['preference']) && isset($PrefArray['options']))
            {
                $insertArray=array();
                $mainArray=array();
                $mainPreferenceArray=$PrefArray['preference'];
                $subPreferenceArray=$PrefArray['options'];    
                $userId = $this->userDetails($request->input('apikey'));
                $userId=$userId->id;
                $dataflg = false;
               
                for($i=0;$i<count($mainPreferenceArray);$i++)
                {
                    $mainPreferenceId=$mainPreferenceArray[$i];
                    $subPreferenceId=$subPreferenceArray[$i];
                    $checkarray=array("mainid"=>$mainPreferenceId,"subid"=>$subPreferenceId);
                    $validator = \Validator::make($checkarray, [
                        'mainid'=>'numeric',
                        'subid'=>'numeric',
                    ]);

                    if ($validator->fails())
                    {
                        $flag=1;
                        $messages = $validator->messages();
                        foreach ($messages->all() as $key=>$value) 
                        {
                            $errors[]= $value;
                        }
                    }
                    else
                    {
                        $insertArray=array("userid"=>$userId,"preferenceId"=>$mainPreferenceId,"pref_optionId"=>$subPreferenceId);
                    }
                    $mainArray[]=$insertArray;
                }

                if($flag==1)
                {

                    $response['message'] = "Something went wrong";
                    $response['erromessage']=$errors;
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,400);
                }
                else
                {
                    $flg=DB::table('user_preferences')->where('userid',$userId)->update(['isDeleted'=>1]);
                    if($flg>=0)
                    {
                       
                        $dataflg=DB::table('user_preferences')->insert($mainArray);
                       
                        if($dataflg>0)
                        {
                            $response['message'] = "Your preferences has been Added successfully..";
                            $response['status'] = true;
                            $response['erromessage']=$errors;
                            $response['data'] = array();
                            return response($response,200);
                        }
                        else
                        {
                            $response['message'] = "Opps something wrong";
                            $response['status'] = false;
                            $response['erromessage']=$errors;
                            $response['data'] = array();
                            return response($response,400);
                        }
                    }
                    else
                    {
                        $response['message'] = "Opps something wrong";
                        $response['status'] = false;
                        $response['erromessage']=$errors;
                        $response['data'] = array();
                        return response($response,400);
                    }
                }
            }
            else
            {
                $response['message'] = "Opps something wrong";
                $response['status'] = false;
                $response['erromessage']=$errors;
                $response['data'] = array();
                return response($response,400);
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['errormessage']=$errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,400);
        }
    }
        /* -----------------------------------------*/
        
        /**
            *----------------- My Rides Details --------------------------------------
            */
    public function MyRide(Request $request)//done
    { 
            //        $CarArray=$request->all();
        try
        {
            $RideArray=$request->all();
            $userId = $this->userDetails($request->input('apikey'));
            $userId=$userId->id;

            $validator = Validator::make($RideArray, [
                        "apikey"=>'required',
                        "token"=>'required',
                    ]);

            if ($validator->fails()) 
            {
                $errors=array();
                $messages = $validation->messages();
                foreach ($messages->all() as $key=>$value) 
                {
                        $errors[$key]= $value;
                }
                $response['message'] = "Something went wrong";
                $response['erromessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);
            }
            else
            {

                $ridedata=DB::table('rides')->select('rides.id','rides.userId','rides.departureOriginal','rides.arrivalOriginal','rides.offer_seat','rides.available_seat','rides.cost_per_seat','rides.is_round_trip','rides.isDaily','rides.ladies_only','rides.departure_date','rides.return_date','rides.return_time','users.rating','rides.created_date')
                ->leftjoin('users','rides.userId','=','users.id')
                ->where('userId',$userId)->orderBy('departure_date','desc')->get();
                $temprespo = array();
                foreach($ridedata as $rideKey => $rideval)
                {
                    $temprespo[] = array("rideId"=>$rideval->id,"userId"=>$rideval->userId,
                                 "Departure"=>$rideval->departureOriginal,
                                 "Arrival"=>$rideval->arrivalOriginal,
                                 "OfferSeat"=>$rideval->offer_seat,
                                 "AvailableSeat"=>$rideval->available_seat,
                                 "CostPerSeat"=>$rideval->cost_per_seat,
                                 "IsRoundTrip"=>$rideval->is_round_trip,
                                 "IsDaily"=>$rideval->isDaily,
                                 "LaidesOnly"=>$rideval->ladies_only,
                                 "departure_date"=>$rideval->departure_date,
                                 "returnDate"=>$rideval->return_date,
                                 "ReturnTime"=>$rideval->return_time,
                                 "Ratting"=>$rideval->rating,
                                 "Created"=>$rideval->created_date);
                }
                
                $response['message'] = "success";
                $response['status'] = true;
                $response['data'] = $temprespo;
                $response['errormessage']=array();
                return response($response,200);
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['errormessage']=array();
            $response['status'] = false;
            $response['data'] = array();
            return response($response,400);
        }        
    }
     
    protected function userDetails($apiey)
    {
        $userData=DB::table('device_token')
          ->where('apikey','=',trim($apiey))
          ->first(['users_id as id']);
          return $userData;
    }    

    //
    public function editPhone(Request $request)//done
    {   
         
        try 
        {
            $errors=array();
            $userdetail = array(
                                    'phone' => $request->input('phone'),
                                );
            $userId = $this->userDetails($request->input('apikey'));
            $userId=$userId->id;
            $validator = Validator::make($userdetail, [
                                'phone' => 'required|'
                        ]);
                     
            if ($validator->fails())
            {
                
                $messages = $validator->messages();             
                foreach ($messages->all() as $key=>$value) 
                {
                    $errors[$key]= $value;
                }
                            
                $response['message'] = "Opps something worng";
                $response['errormessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401); 
            } 
            else
            {   
                $userData['phone']=$request->input('phone');

                $userPhone=Users::where('id',$userId)
                                ->update(array('phone_no'=>$userdetail['phone']));
                $response['message'] = "Your phone number updated successfully";
                $response['errormessage'] = $errors;
                $response['status'] = true;
                $response['data'] = array();
                return response($response,200); 
            }     
        } 
        catch (\Exception $e) 
        {
            $response['message'] = "Opps something wrong";
            $response['errormessage']=array();
            $response['status'] = false;
            $response['data'] = array();
            return response($response,400);
        } 
            
        //  $response = $this->authcheck($userdetail);//custome function call for check user authication
          
    }  

    public function userRatting(Request $request)//done
    {   
        try
        {
            $userdetail = array(
                               'touser' => $request->input('touser'),
                               'ratting' => $request->input('ratting'),
                            );
            $userId = $this->userDetails($request->input('apikey'));
            $userId=$userId->id;
            $validator = Validator::make($userdetail, [
                                'touser' => 'required',
                                'ratting' => 'required'
                        ]);
                     
            if ($validator->fails())
            {
                
                $messages = $validator->messages();             
                foreach ($messages->all() as $key=>$value) 
                {
                    $errors[$key]= $value;
                }
                            
                $response['message'] = "Opps something worng";
                $response['errormessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);
            } 
            else
            {   
                $userData['fromUser']=$userId;
                $userData['toUser']=$request->input('touser');
                $userData['ratting']=$request->input('ratting');

                $userDataFlag=DB::table('user_ratting')->insert($userData);
        
                $userRatting = DB::table('user_ratting')->where('toUser',$userData['toUser'])->avg('ratting');
                if($userRatting)
                {
                    $userratting=Users::where('id',$userData['toUser'])
                                ->update(array('rating'=>$userRatting));
                }

                $response['message'] = "Your ratting added successfully";
                $response['status'] = true;
                $response['errormessage'] = array();
                $response['data'] = array();
                return response($response,200);
            }  
        }
        catch (\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        } 
        //  $response = $this->authcheck($userdetail);//custome function call for check user authication
    }  
    //rating user list
    public function ratingUserList(Request $request)//done
    {
        try
        {
            $userid=array();
            $paramArray=$request->all();
            $userId = $this->userDetails($request->input('apikey'));
            $userId=$userId->id;

            $ratingUser=DB::table('user_ratting')->select('toUser')
                            ->where('fromUser',$userId)
                            ->distinct()
                            ->get();

            for($i=0;$i<count($ratingUser);$i++)
            {
                $userid[]=$ratingUser[$i]->toUser;
            }

            $ratingUserArray=DB::table('ride_booking')->select('offer_userId as userId',DB::raw('CONCAT(users.first_name, " ", users.last_name) AS name'))
                            ->join('users','ride_booking.offer_userId','=','users.id');
                            if(count($userid)>0)
                            {
                               $ratingUserArray=$ratingUserArray->whereNotIn('offer_userId',$userid)
                                                ->where('book_userId',$userId)
                                                ->distinct()
                                                ->get();
                            }
                            else
                            {
                                $ratingUserArray=$ratingUserArray->where('book_userId',$userId)->distinct()->get();
                            }
            $response['message'] = "Success";
            $response['status'] = true;
            $response['errormessage'] = array();
            $response['data'] = $ratingUserArray;
            return response($response,200);
        }
        catch (\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        } 
    }

    public function OfferRideGetByID(Request $request)//done
    {
        try
        {
            //get request
            //required parameter
            //offerId
            $offerRideGet=$request->all();
            $errors=array();
            $validation=\Validator::make($offerRideGet,[
                    'offer'=>'required',
                ]);
            if($validation->fails())
            {
                $messages = $validation->messages();                
                foreach ($messages->all() as $key=>$value) {
                    $errors[$key]= $value;
                }
                
                $response['message'] = "Opps something wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,404);
            }
            else
            {
                //update view offers
                $viewoffer=DB::table('rides')->select('view_count')->where('id',$offerRideGet['offer'])->get();
                if(count($viewoffer)>0)
                {
                    $viewcount=$viewoffer[0]->view_count+1;
                    DB::table('rides')->where('id',$offerRideGet['offer'])->update(['view_count'=>$viewcount]);
                }
                
                $getArray=DB::table('rides')->select('rides.id as OfferId','rides.userId as userid','departure as Source','arrival as Destination','rides.departure_lat_long as SourceLatLng','arrival_lat_long as DestinationLatLng','departureCity as SourceCity','arrivalCity as DestinationCity','departure_date','return_date','return_time','is_round_trip as RoundTrip','isDaily as IsDaily','ladies_only as LadiesOnly',
                    'available_seat as availableSeats','offer_seat as offerSeats','cost_per_seat as Price','detour.name as detourName','leave_on.name as leaveName','view_count','luggage.name as luggageName','rating as Rating','comment','isverifyemail as Email_Verified','isverifyphone as Phone_Verified','users.created_at as member_date','car_make as Brand','car_model as Model','vehical_pic','vehical_type.name as vehicle_type','comfort_master.name as Comfort','color.color as Colour','users.profile_pic as profile_pic',DB::raw('CONCAT(first_name, " ",last_name) AS full_name'),'birthdate')
                    ->leftJoin('car_details','rides.carId','=','car_details.id')
                    ->leftJoin('comfort_master','car_details.comfortId','=','comfort_master.id')
                    ->leftJoin('color','car_details.colorId','=','color.id')
                    ->leftJoin('users','rides.userId','=','users.id')
                    ->leftJoin('vehical_type','car_details.car_type','=','vehical_type.id')
                    ->leftJoin('luggage','rides.luggage_size','=','luggage.id')
                    ->leftJoin('leave_on','rides.leave_on','=','leave_on.id')
                    ->leftJoin('detour','rides.can_detour','=','detour.id')
                    ->where('rides.id',$offerRideGet['offer'])
                    ->get();
                    
                //get preference data   
                $getPreference=DB::table('user_ride_preferences')->select('preferences.preferences as preference_name','preferences_option.options as option')
                    ->leftJoin('preferences','user_ride_preferences.preferenceId','=','preferences.id')
                    ->leftJoin('preferences_option','user_ride_preferences.pref_optionId','=','preferences_option.id')
                    ->where('user_ride_preferences.rideId',$offerRideGet['offer'])
                    ->get();
                
                //how many times offer created by this user
                $userId=$getArray[0]->userid;
                $offerCreated=DB::table('rides')->select('id')->where('userId',$userId)->get();
                if(count($offerCreated)>0)
                {
                    $offerCreate=count($offerCreated);
                }
                else
                {
                    $offerCreate=0;
                }

                $age="";
                if($getArray[0]->birthdate!="")
                {
                    $age=date_diff(date_create($getArray[0]->birthdate), date_create('today'))->y;
                }

                if($getArray[0]->profile_pic=="default.png")
                {
                    $profile_pic_full_path="/images/default.png";
                }
                else
                {
                    $profile_pic_full_path="/images/profile/".$getArray[0]->userid."/".$getArray[0]->profile_pic;
                }

                if($getArray[0]->vehical_pic=="car_default.png")
                {
                    $car_image_full_path="/images/car_default.png";
                }
                else
                {
                    $car_image_full_path="/images/cars/".$getArray[0]->userid."/".$getArray[0]->vehical_pic;
                }
                //select waypoints
                $wayPointArray=DB::table('ride_via_points')->select('city as WayPointsName','city_lat_long as WayPointsLatLng','cityName as WayPointCity')->where('is_deleted',0)->where('rideId',$getArray[0]->OfferId)->get();
                
                $userinfo=array("UserID"=>$getArray[0]->userid,"Name"=>$getArray[0]->full_name,"Rating"=>$getArray[0]->Rating,"Phone_Verified"=>$getArray[0]->Phone_Verified,"Email_Verified"=>$getArray[0]->Email_Verified,"Member_Since"=>$getArray[0]->member_date,"Ride_Offer"=>$offerCreate,"profile_pic"=>$getArray[0]->profile_pic,"profile_pic_full_path"=>$profile_pic_full_path,"age"=>$age);
                //put blank space instead of null
                $userinfo = array_map(function($value) {
                   return $value === NULL ? "" : $value;
                }, $userinfo);


                $cardetail=array("Brand"=>$getArray[0]->Brand,"Model"=>$getArray[0]->Model,"Comfort"=>$getArray[0]->Comfort,"Colour"=>$getArray[0]->Colour,"VehiclePic"=>$getArray[0]->vehical_pic,"car_image_full_path"=>$car_image_full_path,"VehicleType"=>$getArray[0]->vehicle_type);
                //put blank space instead of null
                $cardetail = array_map(function($value) {
                   return $value === NULL ? "" : $value;
                }, $cardetail);
                $mm=array("OfferId"=>$getArray[0]->OfferId,"Source"=>$getArray[0]->Source,"Destination"=>$getArray[0]->Destination,"SourceLatLng"=>$getArray[0]->SourceLatLng,"DestinationLatLng"=>$getArray[0]->DestinationLatLng,"SourceCity"=>$getArray[0]->SourceCity,"DestinationCity"=>$getArray[0]->DestinationCity,"DateTime"=>$getArray[0]->departure_date,"ReturnDate"=>$getArray[0]->return_date,"ReturnTime"=>$getArray[0]->return_time,"offerSeats"=>$getArray[0]->offerSeats,"availableSeats"=>$getArray[0]->availableSeats,"Price"=>$getArray[0]->Price,"Daily"=>$getArray[0]->IsDaily,"RoundTrip"=>$getArray[0]->RoundTrip,"LadiesOnly"=>$getArray[0]->LadiesOnly,"Comment"=>$getArray[0]->comment,"Detore"=>$getArray[0]->detourName,"Flexibility"=>$getArray[0]->leaveName,"OfferView"=>$getArray[0]->view_count,"Luggage"=>$getArray[0]->luggageName,"UserInfo"=>$userinfo,"preference"=>$getPreference,"car_Detail"=>$cardetail,"WayPoints"=>$wayPointArray);

                $response['message'] = "Success";
                $response['errormessage']=$errors;
                $response['status'] = true;
                $response['RideDetail']=$mm;
                return response($response,200);
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    } 
    public function earnHistory(Request $request)//done
    {
        $errors=array();
        $parameterArray=$request->all();
        
        try
        {
            $userId = $this->userDetails($request->input('apikey'));
            $userId=$userId->id;
            $validation=\Validator::make($parameterArray,[
                    'type'=>'required',
                ]);

            if($validation->fails())
            {   
                $messages = $validation->messages();                
                foreach ($messages->all() as $key=>$value) {
                    $errors[$key]= $value;
                }
                
                $response['message'] = "Something went wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);
            }
            else
            {
                $data=array();
                $type=$parameterArray['type'];
                $ridedata=DB::table('ride_booking')->select(DB::raw('CONCAT(u1.first_name, " ", u1.last_name) AS full_name'),'ride_booking.id as BookId','ride_booking.source as userSource','ride_booking.destination as userDestination','ride_booking.no_of_seats','ride_booking.cost_per_seat','u1.rating as riderRating','u1.id as u1user_id','u1.profile_pic as u1profile_pic','u1.username as u1username','ride_booking.rideId as OfferId','departure','departure_lat_long','departureCity','arrival','arrival_lat_long','arrivalCity','offer_seat','available_seat','rides.cost_per_seat as perseat','departure_date','return_date','return_time','is_round_trip','isDaily','ladies_only','luggage.name as Luggage','leave_on.name as Flexibility','detour.name as Detore','view_count','licence_verified','comment','rides.userId',DB::raw('CONCAT(u2.first_name, " ", u2.last_name) AS full_name1'),'u2.id as u2user_id','u2.rating as userRating','u2.isverifyemail','u2.isverifyphone','u2.profile_pic as u2profile_pic','u2.created_at as createddate','car_make','car_model','car_details.vehical_pic','vehical_type.name as vehicle_type','color.color','comfort_master.name as comfort')
                    ->leftJoin('users as u1','ride_booking.book_userId','=','u1.id')
                    ->leftJoin('rides','ride_booking.rideId','=','rides.id')
                    ->leftJoin('car_details','rides.carId','=','car_details.id')
                    ->leftJoin('vehical_type','car_details.car_type','=','vehical_type.id')
                    ->leftJoin('comfort_master','car_details.comfortId','=','comfort_master.id')
                    ->leftJoin('color','car_details.colorId','=','color.id')
                    ->leftJoin('users as u2','rides.userId','=','u2.id')
                    ->leftJoin('luggage','rides.luggage_size','=','luggage.id')
                    ->leftJoin('leave_on','rides.leave_on','=','leave_on.id')
                    ->leftJoin('detour','rides.can_detour','=','detour.id');
                    if($type=='earn')
                    {
                        //earn
                        $result=$ridedata->where('ride_booking.offer_userId',$userId)->get();
                    }
                    else if($type=='paid')
                    {
                        //paid
                        $result=$ridedata->where('ride_booking.book_userId',$userId)->get();
                    }
                    else
                    {
                        $result=array();
                    }
                    if(count($result)>0)
                    {
                        for($i=0;$i<count($result);$i++)
                        {
                            $rideid=$result[$i]->OfferId;
                            $userId=$result[$i]->userId;
                            $offerCreated=DB::table('rides')->select('id')->where('userId',$userId)->get();
                            if(count($offerCreated)>0)
                            {
                                $offerCreate=count($offerCreated);
                            }
                            else
                            {
                                $offerCreate=0;
                            }

                            if($result[$i]->u1profile_pic=="default.png")
                            {
                                $u1profile_pic_full_path="/images/default.png";
                            }
                            else
                            {
                                $u1profile_pic_full_path="/images/profile/".$result[$i]->u1user_id."/".$result[$i]->u1profile_pic;
                            }

                            if($result[$i]->vehical_pic=="car_default.png")
                            {
                                $car_image_full_path="/images/car_default.png";
                            }
                            else
                            {
                                $car_image_full_path="/images/cars/".$result[$i]->u2user_id."/".$result[$i]->vehical_pic;
                            }

                            if($result[$i]->u2profile_pic=="default.png")
                            {
                                $u2profile_pic_full_path="/images/default.png";
                            }
                            else
                            {
                                $u2profile_pic_full_path="/images/profile/".$result[$i]->u2user_id."/".$result[$i]->u2profile_pic;
                            }

                            $rideuserinfo=array("userid"=>$result[$i]->u1user_id,"Name"=>$result[$i]->full_name,"Rating"=>$result[$i]->riderRating,"username"=>$result[$i]->u1username,"profile_pic"=>$result[$i]->u1profile_pic,"profile_pic_full_path"=>$u1profile_pic_full_path);

                            $ridedetails=array("OfferId"=>$result[$i]->OfferId,"Source"=>$result[$i]->departure,"SourceLatLng"=>$result[$i]->departure_lat_long,"SourceCity"=>$result[$i]->departureCity,"Destination"=>$result[$i]->arrival,"DestinationLatLng"=>$result[$i]->arrival_lat_long,"DestinationCity"=>$result[$i]->arrivalCity,"DepartureDate"=>$result[$i]->departure_date,"ReturnDate"=>$result[$i]->return_date,"ReturnTime"=>$result[$i]->return_time,"isRoundTrip"=>$result[$i]->is_round_trip,"isDaily"=>$result[$i]->isDaily,"ladies_only"=>$result[$i]->ladies_only,"Luggage"=>$result[$i]->Luggage,"Flexibility"=>$result[$i]->Flexibility,"Detore"=>$result[$i]->Detore,"Comment"=>$result[$i]->comment,"OfferSeat"=>$result[$i]->offer_seat,"AvailableSeat"=>$result[$i]->available_seat,"SeatPrice"=>$result[$i]->cost_per_seat,"OfferView"=>$result[$i]->view_count);

                            $cardetail=array("Brand"=>$result[$i]->car_make,"Model"=>$result[$i]->car_model,"Comfort"=>$result[$i]->comfort,"Colour"=>$result[$i]->color,"VehiclePic"=>$result[$i]->vehical_pic,"car_image_full_path"=>$car_image_full_path,"VehicleType"=>$result[$i]->vehicle_type);
                            //get preference data   
                            $getPreference=DB::table('rides')->select('preferences.preferences as preference_name','preferences_option.options as option')
                                ->leftJoin('user_ride_preferences','rides.id','=','user_ride_preferences.rideId')
                                ->leftJoin('preferences','user_ride_preferences.preferenceId','=','preferences.id')
                                ->leftJoin('preferences_option','user_ride_preferences.pref_optionId','=','preferences_option.id')
                                ->where('user_ride_preferences.rideId',$rideid)
                                ->get();
                            
                            $userInfo=array("user_id"=>$result[$i]->u2user_id,"name"=>$result[$i]->full_name1,"Rating"=>$result[$i]->userRating,"Phone_Verified"=>$result[$i]->isverifyphone,"Email_Verified"=>$result[$i]->isverifyemail,"Member_Since"=>date("Y-m-d",strtotime($result[$i]->createddate)),"Ride_Offer"=>$offerCreate,"profile_pic"=>$result[$i]->u2profile_pic,"profile_pic_full_path"=>$u2profile_pic_full_path);

                            $earnTransactionHistory=array("BookId"=>$result[$i]->BookId,"Source"=>$result[$i]->userSource,"Destination"=>$result[$i]->userDestination,"SeatsBooked"=>$result[$i]->no_of_seats,"perSeatCostUser"=>$result[$i]->cost_per_seat,"RideUserInfo"=>$rideuserinfo,"RideDetail"=>$ridedetails,"UserInfo"=>$userInfo,"Car_Detail"=>$cardetail,"Preference"=>$getPreference);
                            $data[]=$earnTransactionHistory;
                        }
                    }
                    $response['message'] = "Success";
                    $response['errormessage']=$errors;
                    $response['status'] = true;
                    $response['TransactionHistory']=$data;
                    return response($response,200);
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['errormessage']=$errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,400);
        }
    }
    public function bookRide(Request $request)//done
    {
        //we have to check if ride id daily or not if ride is daily then we have to pay not to user .. we have to pay to the soc and also at the time of offer if user creats daily ride then he has to ay 25 rs to the soc first.. and in the paid list of the transaction it also comes
        //required parameter
        //RiderId, OfferId, Seats, Total, Source, Destination, OfferPersonId
        $userId = $this->userDetails($request->input('apikey'));
        $userId=$userId->id;
        $bookRideArray=$request->all();
        $errors=array();
        $validation=\Validator::make($bookRideArray,[
                'offerid'=>'required',
                'riderid'=>'required',
                'seats'=>'required',
                'total'=>'required',
                'source'=>'required',
                'destination'=>'required',
                'offerpersonid'=>'required',
            ]);
        if($validation->fails())
        {
            $messages = $validation->messages();                
            foreach ($messages->all() as $key=>$value) {
                $errors[$key]= $value;
            }
            
            $response['message'] = "Something went wrong";
            $response['errormessage']=$errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,401);
        }
        else
        {
            try
            {
                //check if requested seat is available in ride or not
                $seatArray=DB::table('rides')->select('id','offer_seat','available_seat')
                                ->where('id',$bookRideArray['offerid'])
                                ->where('available_seat','>=',$bookRideArray['seats'])
                                ->where('status',0)->get();

                if(count($seatArray)>0)
                {
                    DB::beginTransaction();
                    $bookRideInsertArray=array("offer_userId"=>$bookRideArray['offerpersonid'],"book_userId"=>$bookRideArray['riderid'],
                                        "rideId"=>$bookRideArray['offerid'],"source"=>$bookRideArray['source'],
                                        "destination"=>$bookRideArray['destination'],"no_of_seats"=>$bookRideArray['seats'],
                                        "cost_per_seat"=>$bookRideArray['total'],
                                        "created_date"=>date("Y-m-d H:i:s"));
                      
                    $insertRide=DB::table('ride_booking')->insertGetId($bookRideInsertArray);
                
                    if($insertRide>0)
                    {
                        $available_seat=$seatArray[0]->available_seat;
                        $offer_seat=$bookRideArray['seats'];
                        $remainig_seat=$available_seat-$offer_seat;
                        if($remainig_seat<0)
                        {
                            DB::rollback();
                            $response['message'] = "Opps something wrong";
                            $response['errormessage']=$errors;
                            $response['status'] = false;
                            $response['data'] = array();
                            return response($response,400);
                        }
                        else
                        {
                            $up=DB::table('rides')->where('id',$bookRideArray['offerid'])->update(['available_seat'=>$remainig_seat]);
                            if($up>=0)
                            {
                                $getwallet=DB::table('payment_wallete')->select('amount')->where('userId',$bookRideArray['offerpersonid'])->get();

                                if(count($getwallet)>0)
                                {
                                    $final_amount=$getwallet[0]->amount+$bookRideArray['total'];
                                    $update=DB::table('payment_wallete')->where('userId',$bookRideArray['offerpersonid'])->update(['amount'=>$final_amount]);
                                    if($update>=0)
                                    {
                                        DB::commit();
                                        //GET DETAILS OF OFFERED PERSON
                                        $offerPersonDetail=DB::table('users')->where('id',$bookRideArray['offerpersonid'])->get();
                                        //GET RIDE DETAILS
                                        $offerRideDetail=DB::table('rides')->where('id',$bookRideArray['offerid'])->get();
                                        //GET DETAILS OF BOOKED PERSON
                                        $bookedPersonDetail=DB::table('users')->where('id',$userId)->get();

                                        if(count($offerPersonDetail)>0)
                                        {
                                            $dd['username']=$offerPersonDetail[0]->username;
                                            $dd['email']=$offerPersonDetail[0]->email;    

                                        }
                                        if(count($offerRideDetail)>0)
                                        {
                                            $dd['date']=date("d-m-Y",strtotime($offerRideDetail[0]->departure_date));
                                            $dd['time']=date("H:i:s",strtotime($offerRideDetail[0]->departure_date));
                                            $dd['source']=$bookRideArray['source'];
                                            $dd['destination']=$bookRideArray['destination'];
                                            $dd['seat']=$bookRideArray['seats'];
                                            if($offerRideDetail[0]->isDaily==0)
                                            {
                                                $dd['amount']=$bookRideArray['total'];
                                            }
                                        }
                                        if(count($bookedPersonDetail)>0)
                                        {
                                            $dd['bookedname']=$bookedPersonDetail[0]->username;
                                            $dd['bookedemail']=$bookedPersonDetail[0]->email;
                                        }
                                        if(isset($dd['email']))
                                        {
                                            if($dd['email']!="")
                                            {
                                                $this->send_ride_email($dd['email'],$dd);
                                            }
                                        }
                                        if(isset($dd['bookedemail']))
                                        {
                                            if($dd['bookedemail']!="")
                                            {
                                                $this->send_book_ride_email($dd['bookedemail'],$dd);
                                            }
                                        }
                                        
                                        $response['message'] = "Your Ride has been booked successfully..";
                                        $response['errormessage']=$errors;
                                        $response['status'] = true;
                                        $response['data'] = array();
                                        return response($response,200);
                                    }
                                    else
                                    {
                                        DB::rollback();
                                        $response['message'] = "Opps something wrong";
                                        $response['errormessage']=$errors;
                                        $response['status'] = false;
                                        $response['data'] = array();
                                        return response($response,400);
                                    }                           
                                }
                                else
                                {
                                    $insert_array=array("userId"=>$bookRideArray['offerpersonid'],"amount"=>$bookRideArray['total']);
                                    $update=DB::table('payment_wallete')->insert($insert_array);

                                    if($update>=0)
                                    {
                                        DB::commit();
                                        //GET DETAILS OF OFFERED PERSON
                                        $offerPersonDetail=DB::table('users')->where('id',$bookRideArray['offerpersonid'])->get();
                                        //GET RIDE DETAILS
                                        $offerRideDetail=DB::table('rides')->where('id',$bookRideArray['offerid'])->get();
                                        //GET DETAILS OF BOOKED PERSON
                                        $bookedPersonDetail=DB::table('users')->where('id',$userId)->get();

                                        if(count($offerPersonDetail)>0)
                                        {
                                            $dd['username']=$offerPersonDetail[0]->username;
                                            $dd['email']=$offerPersonDetail[0]->email;    

                                        }
                                        if(count($offerRideDetail)>0)
                                        {
                                            $dd['date']=date("d-m-Y",strtotime($offerRideDetail[0]->departure_date));
                                            $dd['time']=date("H:i:s",strtotime($offerRideDetail[0]->departure_date));
                                            $dd['source']=$bookRideArray['source'];
                                            $dd['destination']=$bookRideArray['destination'];
                                            $dd['seat']=$bookRideArray['seats'];
                                            if($offerRideDetail[0]->isDaily==0)
                                            {
                                                $dd['amount']=$bookRideArray['total'];
                                            }
                                        }
                                        if(count($bookedPersonDetail)>0)
                                        {
                                            $dd['bookedname']=$bookedPersonDetail[0]->username;
                                            $dd['bookedemail']=$bookedPersonDetail[0]->email;
                                        }
                                        if(isset($dd['email']))
                                        {
                                            if($dd['email']!="")
                                            {
                                                $this->send_ride_email($dd['email'],$dd);
                                            }
                                        }
                                        if(isset($dd['bookedemail']))
                                        {
                                            if($dd['bookedemail']!="")
                                            {
                                                $this->send_book_ride_email($dd['bookedemail'],$dd);
                                            }
                                        }
                                        $response['message'] = "Your Ride has been booked successfully..";
                                        $response['errormessage']=$errors;
                                        $response['status'] = true;
                                        $response['data'] = array();
                                        return response($response,200);
                                    }
                                    else
                                    {
                                        DB::rollback();
                                        $response['message'] = "Opps something wrong";
                                        $response['errormessage']=$errors;
                                        $response['status'] = false;
                                        $response['data'] = array();
                                        return response($response,400);
                                    }
                                }
                            }
                            else
                            {
                                DB::rollback();
                                $response['message'] = "Opps something wrong";
                                $response['errormessage']=$errors;
                                $response['status'] = false;
                                $response['data'] = array();
                                return response($response,400);
                            }
                        }
                    }
                    else
                    {
                        $response['message'] = "Opps something wrong";
                        $response['errormessage']=$errors;
                        $response['status'] = false;
                        $response['data'] = array();
                        return response($response,400);
                    }                  
                }
                else
                {
                    $response['message'] = "requested seat is not available";
                    $response['errormessage']=$errors;
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,400);
                }
            }
            catch(\Exception $e)
            {
                $response['message'] = "Opps something wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,400);
            }
        }   
    }
    public function deleteRide(Request $request)
    {
        try
        {
            $parameter=$request->all();
            $validator=\Validator::make($parameter,[
                    'rideid'=>'required|integer'
                ]);
            if($validator->fails())
            {
                $messages = $validator->messages();                
                foreach ($messages->all() as $key=>$value) {
                    $errors[$key]= $value;
                }
                
                $response['message'] = "Something went wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);
            }
            else
            {
                
                $userId = $this->userDetails($request->input('apikey'));
                $userId=$userId->id;
                $checkDelete=DB::table('rides')->select('id','status')->where('userId',$userId)->where('id',$parameter['rideid'])->get();
                if(count($checkDelete)>0)
                {
                    $status=$checkDelete[0]->status;
                    if($status==2)
                    {
                        $response['message'] = "Ride has been already deleted";
                        $response['errormessage']=array();
                        $response['status'] = false;
                        $response['data'] = array();
                        return response($response,401);
                    }
                    else
                    {
                        //check if ride is booked by other rider
                        $checkridebook=DB::table('ride_booking')->select('id')->where('rideId',$parameter['rideid'])->where('is_deleted',0)->get();
                        if(count($checkridebook)>0)
                        {
                            $total=count($checkridebook);
                            $response['message'] = "You can not delete this ride.This ride has been booked by ".$total." person.";
                            $response['errormessage']=array();
                            $response['status'] = false;
                            $response['data'] = array();
                            return response($response,401);
                        }
                        else
                        {
                            //update ride with status delete (2)
                            $deletestatus=DB::table('rides')->where('id',$parameter['rideid'])->update(['status'=>2]);
                            if($deletestatus>=0)
                            {
                                //success
                                $response['message'] = "Your Ride has been deleted successfully..";
                                $response['errormessage']=array();
                                $response['status'] = true;
                                $response['data'] = array();
                                return response($response,200);
                            }
                            else
                            {
                                //something wrong
                                $response['message'] = "Opps something wrong";
                                $response['errormessage']=array();
                                $response['status'] = false;
                                $response['data'] = array();
                                return response($response,400);
                            }
                        }
                    }
                }
                else
                {
                    //no ride found for request user
                    $response['message'] = "Ride not found";
                    $response['errormessage']=array();
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,401);
                }
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['errormessage']=array();
            $response['status'] = false;
            $response['data'] = array();
            return response($response,400);
        }
    }
    public function addRideMaster(Request $request)
    {
        
        try
        {
            $parameter=$request->all();
            $userId = $this->userDetails($request->input('apikey'));
            $userId=$userId->id;
            //fetch car of login user
            $carArray=DB::table('car_details')->select('id as carId',DB::raw('CONCAT(car_make, " ",car_model) AS carName'))->where('userId',$userId)->where('is_deleted',0)->get();

            //fetch luggage
            $luggageMaster=DB::table('luggage')->select('id','name')->where('is_deleted',0)->get();
            //fetch luggage
            $leaveMaster=DB::table('leave_on')->select('id','name')->where('is_deleted',0)->get();
            //fetch luggage
            $detourMaster=DB::table('detour')->select('id','name')->where('is_deleted',0)->get();
            $data=array("car"=>$carArray,"luggage"=>$luggageMaster,"leave"=>$leaveMaster,"detour"=>$detourMaster);
            $response['message'] = "success";
            $response['errormessage']=array();
            $response['status'] = true;
            $response['data'] = $data;
            return response($response,200);

        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['errormessage']=array();
            $response['status'] = false;
            $response['data'] = array();
            return response($response,400);
        }
    }
    public function addCarMaster(Request $request)
    {
        try
        {
            $parameter=$request->all();
            //fetch vehical type
            $vehical=DB::table('vehical_type')->select('id','name')->where('is_deleted',0)->get();
            //fetch comfort
            $comfort=DB::table('comfort_master')->select('id','name')->where('is_deleted',0)->get();
            //fetch color
            $colour=DB::table('color')->select('id','color')->where('isDeleted',0)->get();
            $data=array("vehical_type"=>$vehical,"comfort"=>$comfort,"colour"=>$colour);
            $response['message'] = "success";
            $response['errormessage']=array();
            $response['status'] = true;
            $response['data'] = $data;
            return response($response,200);

        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['errormessage']=array();
            $response['status'] = false;
            $response['data'] = array();
            return response($response,400);
        }
    }
    //get user preference
    public function getUserPreference(Request $request)
    {
        try
        {
            $parameter=$request->all();
            $userId = $this->userDetails($request->input('apikey'));
            $userId=$userId->id;

            $userpreference=DB::table('user_preferences')->select('preferenceId','pref_optionId','preferences_option.options')
            ->leftjoin('preferences_option','user_preferences.pref_optionId','=','preferences_option.id')
            ->where('userId',$userId)->where('isDeleted',0)->get();
            if(count($userpreference)>0)
            {
                $response['message'] = "success";
                $response['errormessage']=array();
                $response['status'] = true;
                $response['data'] = $userpreference;
                return response($response,200);
            }
            else
            {
                $getpreferencedefault=DB::table('preferences_option')->select('id as pref_optionId','preference_id as preferenceId')->groupBy('preference_id')->get();
                $response['message'] = "success";
                $response['errormessage']=array();
                $response['status'] = true;
                $response['data'] = $getpreferencedefault;
                return response($response,200);
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['errormessage']=array();
            $response['status'] = false;
            $response['data'] = array();
            return response($response,400);
        }
    }

    //profile pic upload with base64//
    /*public function AddProfilepic(Request $request)//done
    { 
        try
        {
            $parameter=$request->all();
            
            $ReqArray=$request->all();
            $data = false;
            $UserArray['userId'] =  $this->userDetails($request->input('apikey'));
            $UserArray['userId'] =  $UserArray['userId']->id;
            $ss='data:image/jpeg;base64,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';
            $dd=explode(",", $ss);
            $imgdata = base64_decode($dd[1]);
            if( is_dir("public/images/profile/".$UserArray['userId']) == false ){ 
                        $path = public_path().'/images/profile/'.$UserArray['userId'] .'/';
                        HelperController::makeDirectory($path, $mode = 0755, true, true);
                        //@chmod("public/images/users/".$userDetails['id'], 0755);
            } 
            $file=public_path()."/images/profile/".$UserArray['userId'].'/abc1.jpg';
            $success=file_put_contents($file,$imgdata);
            print_r($success);
            exit;
            $source=imagecreatefromstring($imgdata);
            $angle = 90;
            $rotate = imagerotate($source, $angle, 0); // if want to rotate the image
            $imageName = "hello1.png";
            $imageSave = imagejpeg($rotate,$imageName,100);
            $f = finfo_open();
            $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
            print_r($mime_type);
            exit;
            //echo "<pre>"; print_r($UserPhoto); exit;

            //$userImage='default.png';
            

                $content_type=$UserPhoto->getClientOriginalExtension();
                $nameImage=$UserPhoto->getClientOriginalName();
                    //   dd($content_type);
                    // Get image type
                $userImage = 'profile'.rand(100,999).time().".".$content_type;

                     //Get the file
                     
                if( is_dir("public/images/profile/".$UserArray['userId']) == false ){ 
                    $path = public_path().'/images/profile/'.$UserArray['userId'] .'/';
                    HelperController::makeDirectory($path, $mode = 0755, true, true);
                    //@chmod("public/images/users/".$userDetails['id'], 0755);
                }     
                $destinationPath=  public_path()."/images/profile/".$UserArray['userId'].'/';
                //Store in the filesystem.
                $data=$request->file('profile_pic')->move($destinationPath, $userImage);       
                //   resizeImage(base_path()."/public/images/users/".$userImage,base_path()."/public/images/users/".$userImage,300,NULL);
            
            $UserArray['profile_pic']=$userImage;                                   //Store in the filesystem.
            if($data)
            {
                $userDataFlag=Users::where(array('id'=>$UserArray['userId']))->update(array('profile_pic'=>$userImage));
                $response['message'] = "Your profile picture has been updated successfully..";
                $response['status'] = true;
                $response['erromessage']=array();
                $response['data'] = array("profile_pic"=>$userImage);
                return response($response,200);
            }
            else
            {
                $response['message'] = "Opps something wrong";
                $response['status'] = false;
                $response['erromessage']=array();
                $response['data'] = array();
                return response($response,400);
            }        
            
        }
        catch (Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }   
    }*/
    public function AddProfilepic(Request $request)//done
    { 

        try
        {
            $parameter=$request->all();
            $validator=\Validator::make($parameter,[
                    'profile_pic'=>'required|image|max:2048'
                ]);
            if($validator->fails())
            {
                $messages = $validator->messages();                
                foreach ($messages->all() as $key=>$value) {
                    $errors[$key]= $value;
                }
                
                $response['message'] = "Something went wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);
            }
            else
            {            
                $ReqArray=$request->all();
                $data = false;
                $UserArray['userId'] =  $this->userDetails($request->input('apikey'));
                $UserArray['userId']=$UserArray['userId']->id;
                $UserPhoto=$request->file('profile_pic');
                //echo "<pre>"; print_r($UserPhoto); exit;

                $userImage='default.png';
                if($request->hasFile('profile_pic')){

                    $content_type=$UserPhoto->getClientOriginalExtension();
                    $nameImage=$UserPhoto->getClientOriginalName();
                        //   dd($content_type);
                        // Get image type
                    $userImage = 'profile'.rand(100,999).time().".".$content_type;

                         //Get the file
                         
                    if( is_dir("public/images/profile/".$UserArray['userId']) == false ){ 
                        $path = public_path().'/images/profile/'.$UserArray['userId'] .'/';
                        HelperController::makeDirectory($path, $mode = 0755, true, true);
                        //@chmod("public/images/users/".$userDetails['id'], 0755);
                    }     
                    $destinationPath=  public_path()."/images/profile/".$UserArray['userId'].'/';
                    //Store in the filesystem.
                    $data=$request->file('profile_pic')->move($destinationPath, $userImage);       
                    //   resizeImage(base_path()."/public/images/users/".$userImage,base_path()."/public/images/users/".$userImage,300,NULL);
                }
            //echo "dsfsd".$data; exit;
                $UserArray['profile_pic']=$userImage;                                   //Store in the filesystem.
                if($userImage=='default.png')
                {
                    $profile_pic_full_path="/images/default.png";
                }
                else
                {
                    $profile_pic_full_path="/images/profile/".$UserArray['userId']."/".$userImage;
                }

                if($data)
                {
                    $userDataFlag=Users::where(array('id'=>$UserArray['userId']))->update(array('profile_pic'=>$userImage));
                    $response['message'] = "Your profile picture has been updated successfully..";
                    $response['status'] = true;
                    $response['erromessage']=array();
                    $response['data'] = array("profile_pic"=>$userImage,"profile_pic_full_path"=>$profile_pic_full_path);
                    return response($response,200);
                }
                else
                {
                    $response['message'] = "Opps something wrong";
                    $response['status'] = false;
                    $response['erromessage']=array();
                    $response['data'] = array();
                    return response($response,400);
                }        
            }
        }
        catch (\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }   
    }
    public function changeNotification(Request $request)
    {
        try
        {
            $parameter=$request->all();
            $userId = $this->userDetails($request->input('apikey'));
            $userId = $userId->id;
            $validator=\Validator::make($parameter,[
                    'notify'=>'required'
                ]);
            if($validator->fails())
            {
                $messages = $validator->messages();                
                foreach ($messages->all() as $key=>$value) {
                    $errors[$key]= $value;
                }
                
                $response['message'] = "Something went wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);
            }
            else
            {
                $update=DB::table('users')->where('id',$userId)->update(['notification_alert'=>$parameter['notify']]);
                if($update>=0)
                {
                    $response['message'] = "Success";
                    $response['status'] = true;
                    $response['erromessage']=array();
                    $response['data'] = array();
                    return response($response,200);
                }
                else
                {
                    $response['message'] = "Opps something wrong";
                    $response['status'] = false;
                    $response['erromessage']=array();
                    $response['data'] = array();
                    return response($response,400);
                }
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    }
    public function withdrawMoney(Request $request)
    {
        try
        {
            $parameter=$request->all();

            $userId = $this->userDetails($request->input('apikey'));
            $userId = $userId->id;
            $validator=\Validator::make($parameter,[
                'amount'=>'required',
                'datetime'=>'required',
                'bank'=>'required',
                'username'=>'required',
                'accountno'=>'required',
                'ifsc'=>'required'
            ]);
            if($validator->fails())
            {
                $messages = $validator->messages();                
                foreach ($messages->all() as $key=>$value) {
                    $errors[$key]= $value;
                }
                
                $response['message'] = "Something went wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401); 
            }
            else
            {
                $withdrawamount=$parameter['amount'];
                $walletamount=DB::table('payment_wallete')->select('amount')->where('userId',$userId)->get();
                if(count($walletamount)>0)
                {
                    //
                    $amount=$walletamount[0]->amount;
                    if($amount>$withdrawamount)
                    {
                        DB::beginTransaction();
                        $array=array("userId"=>$userId,"amount"=>$parameter['amount'],"bank"=>$parameter['bank'],"username"=>$parameter['username'],"accountNumber"=>$parameter['accountno'],"ifscCode"=>$parameter['ifsc'],"transferDate"=>date("Y-m-d H:i:s",strtotime($parameter['datetime'])));
                        $insert=DB::table('payment_withdraw')->insert($array);
                        if($insert>0)
                        {
                            $final_amount=$amount-$withdrawamount;
                            $update=DB::table('payment_wallete')->where('userId',$userId)->update(['amount'=>$final_amount]);
                            if($update>=0)
                            {
                                DB::commit();
                                $response['message'] = "amount withdraw successfully";
                                $response['status'] = true;
                                $response['erromessage']=array();
                                $response['data'] = array();
                                return response($response,200);
                            }
                            else
                            {
                                DB::rollback();
                                $response['message'] = "Something went wrong";
                                $response['errormessage']=array();
                                $response['status'] = false;
                                $response['data'] = array();
                                return response($response,401); 
                            }
                        }
                        else
                        {
                            DB::commit();
                            $response['message'] = "Something went wrong";
                            $response['errormessage']=array();
                            $response['status'] = false;
                            $response['data'] = array();
                            return response($response,401); 
                        }
                    }
                    else
                    {
                        $response['message'] = "withdraw amount should be less than wallet amount";
                        $response['status'] = false;
                        $response['erromessage']=array();
                        $response['data'] = array();
                        return response($response,400);
                    }
                }
                else
                {
                    $response['message'] = "You have no amount in your wallet";
                    $response['status'] = false;
                    $response['erromessage']=array();
                    $response['data'] = array();
                    return response($response,400);
                }
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    }
    public function getWallet(Request $request)
    {
        try
        {
            $parameter=$request->all();
            $userId = $this->userDetails($request->input('apikey'));
            $userId = $userId->id;
            $amount=DB::table('payment_wallete')->select('amount')->where('userId',$userId)->get();
            if(count($amount)>0)
            {
                $walletamount=$amount[0]->amount;

            }
            else
            {
                $walletamount=0;
            }
            $response['message'] = "success";
            $response['status'] = true;
            $response['erromessage']=array();
            $response['data'] = array("amount"=>$walletamount);
            return response($response,200);
        }   
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    }
    public function send_book_ride_email($email1,$data)
    {
        if(Mail::later(5,'emails.sendbookperson',['name'=>$data],function ($message) use ($email1){
        //
            $message->from('info@sharemywheel.com', 'ShareMyWheel');

            $message->to($email1);
            $message->subject("Your ride booked successfully in Share My Wheels");
            //$message->attach(public_path().'/images/users/8/abc.jpg');
        }))
        {
            //echo "success";
        }
        else
        {
            //echo "error";
            \Log::info('Showing error in send_ride_email function');
        }
    }
    //this function is for send mail after successfully ride booked
    public function send_ride_email($email1,$data)
    {
        if(Mail::later(5,'emails.sendrideemail',['name'=>$data],function ($message) use ($email1){
        //
            $message->from('info@sharemywheel.com', 'ShareMyWheel');

            $message->to($email1);
            $message->subject("Your ride booked successfully in Share My Wheels");
            //$message->attach(public_path().'/images/users/8/abc.jpg');
        }))
        {
            //echo "success";
        }
        else
        {
            //echo "error";
            \Log::info('Showing error in send_ride_email function');
        }
    }
    public function send_email($email1,$token)
    {
        
        // $filename=public_path().'/images/users/8/aa.jpg';
        // if(file_exists($filename))
        // {
        //     echo "if";
        // }
        // else
        // {
        //     echo "else";
        // }
        
        if(Mail::later(5,'emails.sendmail',['name'=>$token],function ($message) use ($email1){
        //
            $message->from('info@sharemywheel.com', 'ShareMyWheel');

            $message->to($email1);
            $message->subject("Verify Email");
            //$message->attach(public_path().'/images/users/8/abc.jpg');
        }))
        {
            //echo "success";
        }
        else
        {
            //echo "error";
            \Log::info('Showing error in mail send profile for user');
        }
       /* \Queue::push(function($job){
            $this->send_sms();
             $job->delete();
        });
        */
        //return 1;
    }
    public function send_sms($mobile,$token)
    {
        
    //Your authentication key
        $authKey = "58615AJFx3ubzm5wp52afca33";

        //Multiple mobiles numbers separated by comma
        $mobileNumber = $mobile;

        //Sender ID,While using route4 sender id should be 6 characters long.
        $senderId = "JSNJSN";
        $message1="Hello user,\n your verification code for mobile is: ".$token;
        //Your message to send, Add URL encoding here.
        $message = urlencode($message1);

        //Define route 
        $route = "template";
        //Prepare you post parameters
        $postData = array(
            'authkey' => $authKey,
            'mobiles' => $mobileNumber,
            'message' => $message,
            'sender' => $senderId,
            'route' => $route
        );

        //API URL
        $url="https://control.msg91.com/sendhttp.php";

        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
            //,CURLOPT_FOLLOWLOCATION => true
        ));


        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        //get response
        $output = curl_exec($ch);

        //Print error if any
        if(curl_errno($ch))
        {
            echo 'error:' . curl_error($ch);
        }

        curl_close($ch);

//          echo $output;
    }
    function random_password( $length = 4 ) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
    }
    public function check_mobile_verification(Request $request)
    {
        try
        {
            $parameter=$request->all();
            $errors=array();
            $userId = $this->userDetails($request->input('apikey'));
            $userId = $userId->id;
    
            $update=DB::table('users')->where('id',$userId)->update(['isverifyphone'=>1]);
            if($update>=0)
            {
                $response['message'] = "Your Mobile has been verified successfully";
                $response['errormessage']=array();
                $response['status'] = true;
                $response['data'] = array();
                return response($response,200); 
            }
            else
            {
                $response['message'] = "Opps something wrong";
                $response['status'] = false;
                $response['erromessage']=array();
                $response['data'] = array();
                return response($response,400);
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    }
    public function check_email_verification(Request $request)
    {
        try
        {
            $parameter=$request->all();
            $errors=array();
            $userId = $this->userDetails($request->input('apikey'));
            $userId = $userId->id;
    
            $update=DB::table('users')->where('id',$userId)->update(['isverifyemail'=>1]);
            if($update>=0)
            {
                $response['message'] = "Your email has been verified successfully";
                $response['errormessage']=array();
                $response['status'] = true;
                $response['data'] = array();
                return response($response,200); 
            }
            else
            {
                $response['message'] = "Opps something wrong";
                $response['status'] = false;
                $response['erromessage']=array();
                $response['data'] = array();
                return response($response,400);
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    }
    public function change_contact(Request $request)
    {
        try
        {
            $parameter=$request->all();
            $userdetail=array("phone_no"=>$parameter['phone_no']);
            $errors=array();
            $userId = $this->userDetails($request->input('apikey'));
            $userId = $userId->id;
            $validator = Validator::make($userdetail, [
                        'phone_no' => 'required | unique:users,phone_no|min:9|max:12',
                    ]);
            if($validator->fails())
            {
                $messages = $validator->messages();                
                foreach ($messages->all() as $key=>$value) {
                    $errors[$key]= $value;
                }
                
                $response['message'] = "Something went wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401); 
            }
            else
            {
                $mobile_random=$this->random_password(6);
                \Queue::push(function($job) use($parameter,$mobile_random){
                    $this->send_sms($parameter['phone_no'],$mobile_random);
                    $job->delete();
                });
                
                $response['message'] = "send token";
                $response['status'] = true;
                $response['erromessage']=array();
                $response['data'] = array("mobile_token"=>$mobile_random);
                return response($response,200);
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    }
    public function verify_update_contact(Request $request)
    {
        try
        {
            $parameter=$request->all();
            $errors=array();
            $userId = $this->userDetails($request->input('apikey'));
            $userId = $userId->id;
            $ff=array("phone_no"=>$parameter['mobile']);
            $validator = Validator::make($ff, [
                        'phone_no' => 'required | unique:users,phone_no|min:9|max:12',
                    ]);
            if($validator->fails())
            {
                $messages = $validator->messages();                
                foreach ($messages->all() as $key=>$value) {
                    $errors[$key]= $value;
                }
                
                $response['message'] = "Something went wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401); 
            }
            else
            {
                $update=DB::table('users')->where('id',$userId)->update(["phone_no"=>$parameter['mobile'],"isverifyphone"=>1]);
                if($update>=0)
                {
                    $response['message'] = "Contact Number has been updated successfully";
                    $response['errormessage']=array();
                    $response['status'] = true;
                    $response['data'] = array("mobile"=>$parameter['mobile']);
                    return response($response,200); 
                }
                else
                {
                    $response['message'] = "Something went wrong";
                    $response['errormessage']=array();
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,401); 
                }
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    }
    public function getMyBookedRide(Request $request)
    {
        $errors=array();
        $parameterArray=$request->all();
        try
        {
            $data=array();
            $userId = $this->userDetails($request->input('apikey'));
            $userId = $userId->id;

            $ridedata=DB::table('ride_booking')->select(DB::raw('CONCAT(u1.first_name, " ", u1.last_name) AS full_name'),'ride_booking.id as BookId','ride_booking.source as userSource','ride_booking.destination as userDestination','ride_booking.no_of_seats','ride_booking.cost_per_seat','u1.rating as riderRating','u1.profile_pic as u1profile_pic','u1.username as u1username','u1.id as u1user_id','ride_booking.rideId as OfferId','departure','departure_lat_long','departureCity','arrival','arrival_lat_long','arrivalCity','offer_seat','available_seat','rides.cost_per_seat as perseat','departure_date','return_date','return_time','is_round_trip','isDaily','ladies_only','luggage.name as Luggage','leave_on.name as Flexibility','detour.name as Detore','view_count','licence_verified','comment','rides.userId',DB::raw('CONCAT(u2.first_name, " ", u2.last_name) AS full_name1'),'u2.id as u2user_id','u2.profile_pic as u2profile_pic','u2.rating as userRating','u2.isverifyemail','u2.isverifyphone','u2.created_at','car_make','car_model','car_details.vehical_pic','vehical_type.name as vehicle_type','color.color','comfort_master.name as comfort')
                ->leftJoin('users as u1','ride_booking.book_userId','=','u1.id')
                ->leftJoin('rides','ride_booking.rideId','=','rides.id')
                ->leftJoin('car_details','rides.carId','=','car_details.id')
                ->leftJoin('vehical_type','car_details.car_type','=','vehical_type.id')
                ->leftJoin('comfort_master','car_details.comfortId','=','comfort_master.id')
                ->leftJoin('color','car_details.colorId','=','color.id')
                ->leftJoin('users as u2','rides.userId','=','u2.id')
                ->leftJoin('luggage','rides.luggage_size','=','luggage.id')
                ->leftJoin('leave_on','rides.leave_on','=','leave_on.id')
                ->leftJoin('detour','rides.can_detour','=','detour.id')
                ->where('ride_booking.book_userId',$userId)
                ->get();
               
            if(count($ridedata)>0)
            {
                for($i=0;$i<count($ridedata);$i++)
                {
                    $rideid=$ridedata[$i]->OfferId;
                    $userId=$ridedata[$i]->userId;
                    $offerCreated=DB::table('rides')->select('id')->where('userId',$userId)->get();
                    if(count($offerCreated)>0)
                    {
                        $offerCreate=count($offerCreated);
                    }
                    else
                    {
                        $offerCreate=0;
                    }

                    if($ridedata[$i]->u1profile_pic=="default.png")
                    {
                        $u1profile_pic_full_path="/images/default.png";
                    }
                    else
                    {
                        $u1profile_pic_full_path="/images/profile/".$ridedata[$i]->u1user_id."/".$ridedata[$i]->u1profile_pic;
                    }

                    if($ridedata[$i]->vehical_pic=="car_default.png")
                    {
                        $car_image_full_path="/images/car_default.png";
                    }
                    else
                    {
                        $car_image_full_path="/images/cars/".$ridedata[$i]->u2user_id."/".$ridedata[$i]->vehical_pic;
                    }

                    if($ridedata[$i]->u2profile_pic=="default.png")
                    {
                        $u2profile_pic_full_path="/images/default.png";
                    }
                    else
                    {
                        $u2profile_pic_full_path="/images/profile/".$ridedata[$i]->u2user_id."/".$ridedata[$i]->u2profile_pic;
                    }

                    $rideuserinfo=array("userid"=>$ridedata[$i]->u1user_id,"Name"=>$ridedata[$i]->full_name,"Rating"=>$ridedata[$i]->riderRating,"username"=>$ridedata[$i]->u1username,"profile_pic"=>$ridedata[$i]->u1profile_pic,"profile_pic_full_path"=>$u1profile_pic_full_path);

                    $ridedetails=array("OfferId"=>$ridedata[$i]->OfferId,"Source"=>$ridedata[$i]->departure,"SourceLatLng"=>$ridedata[$i]->departure_lat_long,"SourceCity"=>$ridedata[$i]->departureCity,"Destination"=>$ridedata[$i]->arrival,"DestinationLatLng"=>$ridedata[$i]->arrival_lat_long,"DestinationCity"=>$ridedata[$i]->arrivalCity,"DepartureDate"=>$ridedata[$i]->departure_date,"ReturnDate"=>$ridedata[$i]->return_date,"ReturnTime"=>$ridedata[$i]->return_time,"isRoundTrip"=>$ridedata[$i]->is_round_trip,"isDaily"=>$ridedata[$i]->isDaily,"ladies_only"=>$ridedata[$i]->ladies_only,"Luggage"=>$ridedata[$i]->Luggage,"Flexibility"=>$ridedata[$i]->Flexibility,"Detore"=>$ridedata[$i]->Detore,"Comment"=>$ridedata[$i]->comment,"OfferSeat"=>$ridedata[$i]->offer_seat,"AvailableSeat"=>$ridedata[$i]->available_seat,"SeatPrice"=>$ridedata[$i]->cost_per_seat,"OfferView"=>$ridedata[$i]->view_count);

                    $cardetail=array("Brand"=>$ridedata[$i]->car_make,"Model"=>$ridedata[$i]->car_model,"Comfort"=>$ridedata[$i]->comfort,"Colour"=>$ridedata[$i]->color,"VehiclePic"=>$ridedata[$i]->vehical_pic,"car_image_full_path"=>$car_image_full_path,"VehicleType"=>$ridedata[$i]->vehicle_type);
                    //get preference data   
                    $getPreference=DB::table('rides')->select('preferences.preferences as preference_name','preferences_option.options as option')
                        ->leftJoin('user_ride_preferences','rides.id','=','user_ride_preferences.rideId')
                        ->leftJoin('preferences','user_ride_preferences.preferenceId','=','preferences.id')
                        ->leftJoin('preferences_option','user_ride_preferences.pref_optionId','=','preferences_option.id')
                        ->where('user_ride_preferences.rideId',$rideid)
                        ->get();
                    
                    $userInfo=array("userid"=>$ridedata[$i]->u2user_id,"name"=>$ridedata[$i]->full_name1,"Rating"=>$ridedata[$i]->userRating,"Phone_Verified"=>$ridedata[$i]->isverifyphone,"Email_Verified"=>$ridedata[$i]->isverifyemail,"Member_Since"=>date("Y-m-d",strtotime($ridedata[$i]->created_at)),"Ride_Offer"=>$offerCreate,"profile_pic"=>$ridedata[$i]->u2profile_pic,"profile_pic_full_path"=>$u2profile_pic_full_path);

                    $earnTransactionHistory=array("BookId"=>$ridedata[$i]->BookId,"Source"=>$ridedata[$i]->userSource,"Destination"=>$ridedata[$i]->userDestination,"SeatsBooked"=>$ridedata[$i]->no_of_seats,"perSeatCostUser"=>$ridedata[$i]->cost_per_seat,"RideUserInfo"=>$rideuserinfo,"RideDetail"=>$ridedetails,"UserInfo"=>$userInfo,"Car_Detail"=>$cardetail,"Preference"=>$getPreference);
                    $data[]=$earnTransactionHistory;
                }
            }
            $response['message'] = "Success";
            $response['errormessage']=$errors;
            $response['status'] = true;
            $response['data']=$data;
            return response($response,200);
            
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['errormessage']=$errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,400);
        }
    }
    public function uploadLicenceImage(Request $request)//done
    { 

        try
        {
            $parameter=$request->all();
            $validator=\Validator::make($parameter,[
                    'image'=>'required|image'
                ]);
            if($validator->fails())
            {
                $messages = $validator->messages();                
                foreach ($messages->all() as $key=>$value) {
                    $errors[$key]= $value;
                }
                
                $response['message'] = "Something went wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);
            }
            else
            {            
                $ReqArray=$request->all();
                $data = false;
                $UserArray['userId'] = $this->userDetails($request->input('apikey'));
                $UserArray['userId']=$UserArray['userId']->id;
                $UserPhoto=$request->file('image');
                //echo "<pre>"; print_r($UserPhoto); exit;

                $userImage='no_licence.png';
                if($request->hasFile('image')){

                    $content_type=$UserPhoto->getClientOriginalExtension();
                    $nameImage=$UserPhoto->getClientOriginalName();
                        //   dd($content_type);
                        // Get image type
                    $userImage = 'licence'.rand(100,999).time().".".$content_type;

                         //Get the file
                         
                    if( is_dir("public/images/licence/".$UserArray['userId']) == false ){ 
                        $path = public_path().'/images/licence/'.$UserArray['userId'] .'/';
                        HelperController::makeDirectory($path, $mode = 0755, true, true);
                        //@chmod("public/images/users/".$userDetails['id'], 0755);
                    }     
                    $destinationPath=  public_path()."/images/licence/".$UserArray['userId'].'/';
                    //Store in the filesystem.
                    $data=$request->file('image')->move($destinationPath, $userImage);       
                    //   resizeImage(base_path()."/public/images/users/".$userImage,base_path()."/public/images/users/".$userImage,300,NULL);
                }
            //echo "dsfsd".$data; exit;
                $UserArray['image']=$userImage;                                   //Store in the filesystem.
                if($userImage=='no_licence.png')
                {
                    $licence_pic_full_path="/images/no_licence.png";
                }
                else
                {
                    $licence_pic_full_path="/images/licence/".$UserArray['userId']."/".$userImage;
                }

                if($data)
                {
                    $userDataFlag=Users::where(array('id'=>$UserArray['userId']))->update(array('licence_pic'=>$userImage));
                    $response['message'] = "Your licence picture has been updated successfully..";
                    $response['status'] = true;
                    $response['erromessage']=array();
                    $response['data'] = array("licence_pic"=>$userImage,"licence_pic_full_path"=>$licence_pic_full_path);
                    return response($response,200);
                }
                else
                {
                    $response['message'] = "Opps something wrong";
                    $response['status'] = false;
                    $response['erromessage']=array();
                    $response['data'] = array();
                    return response($response,400);
                }        
            }
        }
        catch (\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }   
    }
    public function panicEmergency(Request $request)
    {
        try
        {
            $parameter=$request->all();
            $validator=\Validator::make($parameter,[
                    'location'=>'required',
                    'address'=>'required',
                    'mobile'=>'required'
                ]);
            if($validator->fails())
            {
                $messages = $validator->messages();                
                foreach ($messages->all() as $key=>$value) {
                    $errors[$key]= $value;
                }
                
                $response['message'] = "Something went wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);
            }
            else
            {            
                $ReqArray=$request->all();
                $UserArray['userId'] = $this->userDetails($request->input('apikey'));
                $UserArray['userId']=$UserArray['userId']->id;
                $username=DB::table('users')->select('first_name','last_name')->where('id',$UserArray['userId'])->get();
                $user['username']=$username[0]->first_name." ".$userName[0]->last_name;
                $user['mobile']=$ReqArray['mobile'];
                $user['address']=$ReqArray['address'];
                $user['location']=$ReqArray['location'];

                \Queue::push(function($job) use($user){
                    $mobile=$user['mobile'];
                    $address=$user['address'];
                    $location=$user['location'];
                    $username=$user['username'];
                    $msg=$username." need some emergency and stuck in ".$location." : \n Username=".$username." which is stuck.\n\n Location = ".$location."\n address:".$address;
                    $this->send_sms1($msg,$mobile);
                        $job->delete();
                    }); 
                $response['message'] = "Your request has been send successfully..";
                $response['status'] = true;
                $response['erromessage']=array();
                $response['data'] = array();
                return response($response,200);
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }   
    }
    public function send_sms1($msg,$mobile)
    {
        
    //Your authentication key
        $authKey = "58615AJFx3ubzm5wp52afca33";

        //Multiple mobiles numbers separated by comma
        $mobileNumber = $mobile;

        //Sender ID,While using route4 sender id should be 6 characters long.
        $senderId = "JSNJSN";
        //Your message to send, Add URL encoding here.
        $message = urlencode($msg);

        //Define route 
        $route = "template";
        //Prepare you post parameters
        $postData = array(
            'authkey' => $authKey,
            'mobiles' => $mobileNumber,
            'message' => $message,
            'sender' => $senderId,
            'route' => $route
        );

        //API URL
        $url="https://control.msg91.com/sendhttp.php";

        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
            //,CURLOPT_FOLLOWLOCATION => true
        ));


        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        //get response
        $output = curl_exec($ch);

        //Print error if any
        if(curl_errno($ch))
        {
            echo 'error:' . curl_error($ch);
        }

        curl_close($ch);

//          echo $output;
    }
    public function check_uniq_city($cityname)
    {
        $city=DB::table('city_master')->select('id')->where('city_name',$cityname)->get();
        if(count($city)>0)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }
    //this function is for forgot password
    public function forgot_password(Request $request)
    {
        try
        {
            $parameter=$request->all();
            $validator=\Validator::make($parameter,[
                    'email'=>'required|email'
                ]);
            if($validator->fails())
            {
                $messages = $validator->messages();                
                foreach ($messages->all() as $key=>$value) {
                    $errors[$key]= $value;
                }
                
                $response['message'] = "Something went wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);
            }
            else
            {
                $userData=DB::table('users')->where('email',$parameter['email'])->get();
                if(count($userData)>0)
                {
                    $uid=$userData[0]->id;
                    $new_password=$this->random_password(8);
                    $password_new=Hash::make($new_password);
                    $username=$userData[0]->username;
                    $emailidsend=$userData[0]->email;
                    $darray=array();
                    $darray["username"]=$username;
                    $darray["password"]=$new_password;

                    $stat=DB::table('users')->where('id',$uid)->update(['password'=>$password_new]);
                    if($stat>0)
                    {
                        $this->send_forgot_password_email($emailidsend,$darray);
                        $response['message'] = "Login details has been send on your emailid";
                        $response['errormessage']=array();
                        $response['status'] = true;
                        $response['data'] = array();
                        return response($response,200); 
                    }
                    else
                    {
                        $response['message'] = "Opps something wrong";
                        $response['status'] = false;
                        $response['erromessage']=array();
                        $response['data'] = array();
                        return response($response,400);
                    }
                }
                else
                {
                    $response['message'] = "email id does not exists";
                    $response['errormessage']=array();
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,401); 
                }
            }
        }
        catch(\Exception $e)
        {
            $response['message'] = "Opps something wrong";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    }

    //this function is for send mail after successfully ride booked
    public function send_forgot_password_email($email1,$data)
    {
        if(Mail::later(5,'emails.sendpasswordemail',['name'=>$data],function ($message) use ($email1){
        //
            $message->from('info@sharemywheel.com', 'ShareMyWheel');

            $message->to($email1);
            $message->subject("Forgot password details of ShareMyWheel");
            //$message->attach(public_path().'/images/users/8/abc.jpg');
        }))
        {
            //echo "success";
        }
        else
        {
            //echo "error";
            \Log::info('Showing error in send_ride_email function');
        }
    }

    //---------send message ----------------------------
    public function sendMessage(Request $request)
    {
        try
        {
            $param=$request->all();
            $errors=array();
            $validator = \Validator::make($param, [
                        'user_id' => 'required | numeric',
                        'receiver_id' => 'required | numeric',
                        'message'=>'required',
                        'apikey'=>'required'
                    ]);

            if($validator->fails())
            {
                $messages = $validator->messages();             
                foreach ($messages->all() as $key=>$value) 
                {
                    $errors[$key]= $value;
                }
                            
                $response['message'] = "Opps something worng";
                $response['errormessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);  
            }
            else
            {
                //check userid exists or not
                $UserArray['userId'] = $this->userDetails($param['apikey']);
                $UserArray['userId']=$UserArray['userId']->id;
                $senderCheck=$this->checkid($param['user_id']);
                if(!$senderCheck)
                {
                    $errors[]="User is not exists..";
                    $response['message'] = "Opps something worng";
                    $response['errormessage'] = $errors;
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,401);  
                }
                //check receiver id exists or not
                $receiveCheck=$this->checkid($param['receiver_id']);
                if(!$receiveCheck)
                {
                    $errors[]="Receiver is not exists..";
                    $response['message'] = "Opps something worng";
                    $response['errormessage'] = $errors;
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,401); 
                }

                $insertArray=array("fromUserId"=>$param['user_id'],"toUserId"=>$param['receiver_id'],"message"=>$param['message']);
                $insertCheck=DB::table('user_chat_messages')->insert($insertArray);
                if($insertCheck)
                {
                    $response['data'] =array();
                    $response['message'] = "message send successfully";
                    $response['status'] = true;
                    $response['erromessage']=$errors;
                    return response($response,200);
                }
                else
                {
                    $errors[]="Please try again";
                    $response['message'] = "Opps something worng";
                    $response['errormessage'] = $errors;
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,401); 
                }
            }
        }
        catch(\Exception $e)
        {
            $errors[]="Opps something worng";
            $response['message'] = "Opps something worng";
            $response['errormessage'] = $errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,401); 
        }
    }
    //*******************send msg api ends*******************************
    public function checkid($id)
    {
        $status=DB::table('users')->where('id',$id)->count();
        if($status>0)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    //------------------------receive message-----------------------------
    public function receiveMessage(Request $request)
    {
        //parameters user_id,receiver_id
        try
        {
            $param=$request->all();
            $errors=array();
            $validator = \Validator::make($param, [
                        'user_id' => 'required | numeric',
                        'receiver_id' => 'required | numeric',
                        'apikey'=>'required'
                    ]);

            if($validator->fails())
            {
                $messages = $validator->messages();             
                foreach ($messages->all() as $key=>$value) 
                {
                    $errors[$key]= $value;
                }
                            
                $response['message'] = "Opps something worng";
                $response['errormessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);  
            }
            else
            {
                //check userid exists or not
                $senderCheck=$this->checkid($param['user_id']);
                if(!$senderCheck)
                {
                    $errors[]="User is not exists..";
                    $response['message'] = "Opps something worng";
                    $response['errormessage'] = $errors;
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,401);  
                }
                //check receiver id exists or not
                $receiveCheck=$this->checkid($param['receiver_id']);
                if(!$receiveCheck)
                {
                    $errors[]="Receiver is not exists..";
                    $response['message'] = "Opps something worng";
                    $response['errormessage'] = $errors;
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,401); 
                }

                $getMessage=DB::table('user_chat_messages')->whereRaw('(fromUserId="'.$param['user_id'].'" and toUserId="'.$param['receiver_id'].'") or (fromUserId="'.$param['receiver_id'].'" and toUserId="'.$param['user_id'].'")')->get();
                $response['data'] =$getMessage;
                $response['message'] = "all messages";
                $response['status'] = true;
                $response['erromessage']=$errors;
                return response($response,200);
            }
        }
        catch(\Exception $e)
        {
            $errors[]="Opps something worng";
            $response['message'] = "Opps something worng";
            $response['errormessage'] = $errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,401); 
        }

    }
    //*************receive message function ends**************************

    //-----------------get chat contacts----------------------------------
    public function getChatContact(Request $request)
    {
        try
        {
            $param=$request->all();
            $errors=array();
            $validator = \Validator::make($param, [
                        'user_id' => 'required | numeric',
                        'apikey'=>'required'
                    ]);

            if($validator->fails())
            {
                $messages = $validator->messages();             
                foreach ($messages->all() as $key=>$value) 
                {
                    $errors[$key]= $value;
                }
                            
                $response['message'] = "Opps something worng";
                $response['errormessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);  
            }
            else
            {
                //check userid exists or not
                $senderCheck=$this->checkid($param['user_id']);
                if(!$senderCheck)
                {
                    $errors[]="User is not exists..";
                    $response['message'] = "Opps something worng";
                    $response['errormessage'] = $errors;
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,401);  
                }
               
                $fromuser=DB::table('user_chat_messages')->select('users.id','username','profile_pic','first_name','last_name')
                ->leftJoin('users','user_chat_messages.toUserId','=','users.id')
                ->where('fromUserId',$param['user_id'])->distinct();
        
                $msgUser=DB::table('user_chat_messages')->select('users.id','username','profile_pic','first_name','last_name')
                ->leftJoin('users','user_chat_messages.fromUserId','=','users.id')
                ->where('toUserId',$param['user_id'])->union($fromuser)->distinct()->get();
        
                foreach ($msgUser as $key => $value) {
                    # code...
                    if($msgUser[$key]->profile_pic=="default.png")
                    {
                        $msgUser[$key]->profile_pic_full_path="/images/default.png";    
                    }
                    else
                    {
                        $msgUser[$key]->profile_pic_full_path="/images/profile/".$msgUser[$key]->id."/".$msgUser[$key]->profile_pic;
                    }
                }
                $response['data'] =$msgUser;
                $response['message'] = "all chat contacts";
                $response['status'] = true;
                $response['erromessage']=$errors;
                return response($response,200);
            }
        }
        catch(\Exception $e)
        {
            $errors[]="Opps something worng";
            $response['message'] = "Opps something worng";
            $response['errormessage'] = $errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,401); 
        }
    }
    //****************chat contacts ends**********************************

    //-----------------get preference list--------------------------------
    public function getPreferenceList()
    {
        try
        {
            $finalArray=array();
            $preference=DB::table('preferences')->select('id','preferences')->where('is_deleted',0)->orderBy('id','asc')->get();
            for($i=0;$i<count($preference);$i++)
            {
                $optionArray=array();
                $getPreferenceOption=DB::table('preferences_option')->select('id','options')->where('preference_id',$preference[$i]->id)->orderBy('id','asc')->get();
                for($j=0;$j<count($getPreferenceOption);$j++)
                {
                    $n=array("pref_optionId"=>$getPreferenceOption[$j]->id,"pref_optionname"=>$getPreferenceOption[$j]->options);
                    $optionArray[]=$n;
                }
                $x=array("preferenceId"=>$preference[$i]->id,"options"=>$optionArray);
                $finalArray[]=$x;
            }
            $response['data'] =$finalArray;
            $response['message'] = "success";
            $response['status'] = true;
            $response['erromessage']=array();
            return response($response,200);
        }
        catch(\Exception $e)
        {
            $errors[]="Opps something worng";
            $response['message'] = "Opps something worng";
            $response['errormessage'] = $errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,401); 
        }   
    }
    //**************** getPreferenceList ends*********************************

    //-----------------get profile api----------------------------------------
    public function getProfile(Request $request)
    {
        try
        {
            $userId = $this->userDetails($request->input('apikey'));
            $userId = $userId->id;

            $userdd=DB::table('users')->select('id','email','username','isverifyemail','isverifyphone','first_name','last_name','gender','birthdate','phone_no','description','profile_pic','licence_pic','created_at')->where('id',$userId)->get();
            
            if(count($userdd)>0)
            {
                $userdata['userId']=$userdd[0]->id;
                $userdata['email']=$userdd[0]->email;
                $userdata['username']=$userdd[0]->username;
                $userdata['isverifyemail']=$userdd[0]->isverifyemail;
                $userdata['isverifyphone']=$userdd[0]->isverifyphone;
                $userdata['first_name']=$userdd[0]->first_name;
                $userdata['last_name']=$userdd[0]->last_name;
                $userdata['gender']=$userdd[0]->gender;
                $userdata['birthdate']=$userdd[0]->birthdate;
                $userdata['phone_no']=$userdd[0]->phone_no;
                $userdata['description']=$userdd[0]->description;
                $userdata['profile_pic']=$userdd[0]->profile_pic;
                $userdata['licence_pic']=$userdd[0]->licence_pic;

                if($userdd[0]->profile_pic=="default.png")
                {
                    $userdata['profile_pic_full_path']="/images/default.png";
                }
                else
                {
                    $userdata['profile_pic_full_path']="/images/profile/".$userdd[0]->id."/".$userdd[0]->profile_pic;
                }

                if($userdd[0]->licence_pic=="no_licence.png")
                {
                    $userdata['licence_pic_full_path']="/images/no_licence.png";
                }
                else
                {
                    $userdata['licence_pic_full_path']="/images/licence/".$userdd[0]->id."/".$userdd[0]->licence_pic;
                }

                $userdata['created_at']=$userdd[0]->created_at;
                $response['data'] = $userdata;
                $response['erromessage']=array();
                $response['message'] = "Login success";
                $response['status'] = true;
                return response($response,200);
            }
            else
            {
                $response['message'] = "Userid is wrong";
                $response['errormessage'] =array();
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401); 
            }
        }
        catch(\Exception $e)
        {
            $errors[]="Opps something worng";
            $response['message'] = "Opps something worng";
            $response['errormessage'] = $errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,401); 
        }
    }
    //**************** get profile ends***************************************

    //----------------- get coupan offer -------------------------------------
    public function addCoupanAmount(Request $request)
    {
        try
        {
            $param=$request->all();
            $errors=array();
            $validator = Validator::make($param, [
                        'userid'    => 'required | numeric',
                        'coupancode'=> 'required | alpha_num',
                    ],
                    [
                        'userid.required'       =>  'Userid is required',
                        'coupancode.required'   =>  'Coupancode is required',
                        'userid.numeric'        =>  'Userid must be numeric',
                        'coupancode.alpha_num'  =>  'Coupancode is wrong'
                    ]);
                     //dd($validator->fails());
            if ($validator->fails()) 
            {
                $messages = $validator->messages();             
                foreach ($messages->all() as $key=>$value) 
                {
                    $errors[$key]= $value;
                } 
                $response['message'] = "Opps something worng";
                $response['errormessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);   
            } 
            else
            {
                $date=date("Y-m-d H:i:s");
                $userId = $this->userDetails($request->input('apikey'));
                $userId = $userId->id;  

                $paramUserId=$param['userid'];
                if($userId!=$paramUserId)
                {
                    $errors[]="Userid is wrong";
                    $response['message'] = "Userid is wrong";
                    $response['errormessage'] = $errors;
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,401);
                }
                //check if coupan code is exists or not
                $findcoupan=DB::table('coupan_code')
                            ->where('coupan_code',$param['coupancode'])
                            ->where('is_deleted',0)
                            ->get();
                            
                if(count($findcoupan)>0)
                {
                    //check weather coupan is expired or not
                    if($findcoupan[0]->start_date<=$date && $findcoupan[0]->end_date>=$date)
                    {
                        //check if user has already avail this coupan code or not
                        $checkUserCoupan=DB::table('user_coupan_code')->where('userId',$userId)->where('coupanId',$findcoupan[0]->id)->get();
                        if(count($checkUserCoupan)>0)
                        {
                            $errors[]="you have already avail this coupancode";
                            $response['message'] = "you have already avail this offer";
                            $response['errormessage'] = $errors;
                            $response['status'] = false;
                            $response['data'] = array();
                            return response($response,401);
                        }
                        else
                        {
                            DB::beginTransaction();
                            $insert_user_coupan=array("userId"=>$userId,"coupanId"=>$findcoupan[0]->id,"amount"=>$findcoupan[0]->amount);
                            $insert=DB::table('user_coupan_code')->insert($insert_user_coupan);
                            if($insert)
                            {
                                $selectUserWallet=DB::table('payment_wallete')->where('userId',$userId)->get();
                                if(count($selectUserWallet)>0)
                                {
                                    $newAmount=$findcoupan[0]->amount+$selectUserWallet[0]->amount;
                                    $updateAmount=DB::table('payment_wallete')->where('userId',$userId)->update(['amount'=>$newAmount]);
                                    if($updateAmount)
                                    {
                                        DB::commit();
                                        $response['data'] = array();
                                        $response['erromessage']=array();
                                        $response['message'] = "Congratulations.You won ".$findcoupan[0]->amount." Rs. in your wallet.";
                                        $response['status'] = true;
                                        return response($response,200);
                                    }
                                    else
                                    {
                                        //error
                                        DB::rollback();
                                        $errors[]="Please try again";
                                        $response['message'] = "Please try again";
                                        $response['errormessage'] = $errors;
                                        $response['status'] = false;
                                        $response['data'] = array();
                                        return response($response,401);
                                    }
                                }
                                else
                                {
                                    $insertAmount=DB::table('payment_wallete')->insert(['userId'=>$userId,'amount'=>$findcoupan[0]->amount]);    
                                    if($insertAmount)
                                    {
                                        DB::commit();
                                        $response['data'] = array();
                                        $response['erromessage']=array();
                                        $response['message'] = "Congratulations.You won ".$findcoupan[0]->amount." Amount in your wallet.";
                                        $response['status'] = true;
                                        return response($response,200);
                                    }
                                    else
                                    {
                                        //error
                                        DB::rollback();
                                        $errors[]="Please try again";
                                        $response['message'] = "Please try again";
                                        $response['errormessage'] = $errors;
                                        $response['status'] = false;
                                        $response['data'] = array();
                                        return response($response,401);
                                    }
                                }
                            }
                            else
                            {
                                DB::commit();
                                $errors[]="Please try again";
                                $response['message'] = "Please try again";
                                $response['errormessage'] = $errors;
                                $response['status'] = false;
                                $response['data'] = array();
                                return response($response,401);
                            }
                        }
                    }
                    else
                    {
                        //coupan expired
                        $errors[]="coupan is expired";
                        $response['message'] = "coupan is expired";
                        $response['errormessage'] = $errors;
                        $response['status'] = false;
                        $response['data'] = array();
                        return response($response,401);
                    }
                }
                else
                {
                    $errors[]="Coupancode is wrong";
                    $response['message'] = "Coupancode is wrong";
                    $response['errormessage'] = $errors;
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,401);
                }
            }
        }
        catch(\Exception $e)
        {
            $errors[]="Opps something worng";
            $response['message'] = "Opps something worng";
            $response['errormessage'] = $errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,401);
        }
    }
    //**************** get coupan offer ends**********************************   

    //------------------ ride book function new ------------------------------
    public function rideBook(Request $request)//done
    {
        //we have to check if ride id daily or not if ride is daily then we have to pay not to user .. we have to pay to the soc and also at the time of offer if user creats daily ride then he has to ay 25 rs to the soc first.. and in the paid list of the transaction it also comes
        //required parameterll
        //RiderId, OfferId, Seats, Total, Source, Destination, OfferPersonId

        //if daily ride then don't deduct any amount from the total .. add total amount in admin wallet and if not daily ride then deduct 10% amont from the total amount and add that 10% amount in admin wallet and remaining amount in ride booking amount.

        $userId = $this->userDetails($request->input('apikey'));
        $userId=$userId->id;
        $bookRideArray=$request->all();
        $errors=array();
        $validation=\Validator::make($bookRideArray,[
                'offerid'       =>'required',
                'riderid'       =>'required',
                'seats'         =>'required',
                'total'         =>'required',
                'source'        =>'required',
                'destination'   =>'required',
                'offerpersonid' =>'required',
                'paymenttype'   =>'required|in:wallet,ccavenue'
            ]);
        if($validation->fails())
        {
            $messages = $validation->messages();                
            foreach ($messages->all() as $key=>$value) {
                $errors[$key]= $value;
            }
            
            $response['message'] = "Something went wrong";
            $response['errormessage']=$errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,401);
        }
        else
        {
            try
            {
                //check if requested seat is available in ride or not
                $seatArray=DB::table('rides')->select('id','offer_seat','available_seat','isDaily')
                                ->where('id',$bookRideArray['offerid'])
                                ->where('available_seat','>=',$bookRideArray['seats'])
                                ->where('status',0)->get();

                if(count($seatArray)>0)
                {
                    DB::beginTransaction();
                    $paymenttype=$bookRideArray['paymenttype'];
                    $isDaily=$seatArray[0]->isDaily;

                    if($paymenttype=='wallet')
                    {
                        //booked user
                        $fetchWalletAmount=DB::table('payment_wallete')->where('userId',$userId)->get();
                        if(count($fetchWalletAmount)>0)
                        {
                            if($fetchWalletAmount[0]->amount<$bookRideArray['total'])
                            {
                                DB::commit();
                                $errors[]="you don't have sufficient balance in your wallet..please book your ride by ccavenue..";
                                $response['message'] = "you don't have sufficient balance in your wallet..please book your ride by ccavenue..";
                                $response['errormessage']=$errors;
                                $response['status'] = false;
                                $response['data'] = array();
                                return response($response,400);
                            }
                            else
                            {
                                //deduct amount from booked person wallet
                                $finalNewAmount=$fetchWalletAmount[0]->amount-$bookRideArray['total'];
                                $updateBookUserWallet=DB::table('payment_wallete')->where('userId',$bookRideArray['riderid'])->update(['amount'=>$finalNewAmount]);
                                if($updateBookUserWallet)
                                {
                                    //
                                }
                                else
                                {
                                    //error
                                    DB::commit();
                                    $errors[]="Please try again";
                                    $response['message'] = "Please try again";
                                    $response['errormessage']=$errors;
                                    $response['status'] = false;
                                    $response['data'] = array();
                                    return response($response,400);
                                }
                            }
                        }
                        else
                        {
                            DB::commit();
                            $errors[]="you don't have sufficient balance in your wallet..please book your ride by ccavenue..";
                            $response['message'] = "you don't have sufficient balance in your wallet..please book your ride by ccavenue..";
                            $response['errormessage']=$errors;
                            $response['status'] = false;
                            $response['data'] = array();
                            return response($response,400);
                        }
                    }
                    
                        if($isDaily==1)
                        {
                            $costSeat=0;
                            $taxamount=$bookRideArray['total'];
                        }
                        else
                        {
                            $tt=$bookRideArray['total'];
                            $costSeat=(100*$tt)/(110);//110 means 10%service tax 
                            $taxamount=$bookRideArray['total']-$costSeat;
                        }
                    $bookRideInsertArray=array("offer_userId"=>$bookRideArray['offerpersonid'],"book_userId"=>$bookRideArray['riderid'],
                        "rideId"=>$bookRideArray['offerid'],"source"=>$bookRideArray['source'],"destination"=>$bookRideArray['destination'],"no_of_seats"=>$bookRideArray['seats'],"cost_per_seat"=>$costSeat,"created_date"=>date("Y-m-d H:i:s"),"paymentType"=>$paymenttype);
                      
                    $insertRide=DB::table('ride_booking')->insertGetId($bookRideInsertArray);
                
                    if($insertRide>0)
                    {
                        $available_seat=$seatArray[0]->available_seat;
                        $offer_seat=$bookRideArray['seats'];
                        $remainig_seat=$available_seat-$offer_seat;
                        if($remainig_seat<0)
                        {
                            DB::rollback();
                            $response['message'] = "Opps something wrong";
                            $response['errormessage']=$errors;
                            $response['status'] = false;
                            $response['data'] = array();
                            return response($response,400);
                        }
                        else
                        {
                            $up=DB::table('rides')->where('id',$bookRideArray['offerid'])->update(['available_seat'=>$remainig_seat]);
                            if($up>=0)
                            {
                                //offer person wallet
                                $getwallet=DB::table('payment_wallete')->select('amount')->where('userId',$bookRideArray['offerpersonid'])->get();

                                if(count($getwallet)>0)
                                {
                                    //update wallet amount in offer person wallet
                                    $final_amount=$getwallet[0]->amount+$costSeat;
                                    $update=DB::table('payment_wallete')->where('userId',$bookRideArray['offerpersonid'])->update(['amount'=>$final_amount]);

                                    //add service charge in admin wallet
                                    $in=DB::table('admin_wallet')->insert(['rideId'=>$bookRideArray['offerid'],"userId"=>$bookRideArray['riderid'],"amount"=>$taxamount,"bookType"=>"book","isDaily"=>$isDaily]);

                                    if($in>=0)
                                    {
                                        DB::commit();
                                        //GET DETAILS OF OFFERED PERSON
                                        $offerPersonDetail=DB::table('users')->where('id',$bookRideArray['offerpersonid'])->get();
                                        //GET RIDE DETAILS
                                        $offerRideDetail=DB::table('rides')->where('id',$bookRideArray['offerid'])->get();
                                        //GET DETAILS OF BOOKED PERSON
                                        $bookedPersonDetail=DB::table('users')->where('id',$userId)->get();

                                        if(count($offerPersonDetail)>0)
                                        {
                                            $dd['username']=$offerPersonDetail[0]->username;
                                            $dd['email']=$offerPersonDetail[0]->email;    

                                        }
                                        if(count($offerRideDetail)>0)
                                        {
                                            $dd['date']=date("d-m-Y",strtotime($offerRideDetail[0]->departure_date));
                                            $dd['time']=date("H:i:s",strtotime($offerRideDetail[0]->departure_date));
                                            $dd['source']=$bookRideArray['source'];
                                            $dd['destination']=$bookRideArray['destination'];
                                            $dd['seat']=$bookRideArray['seats'];
                                            if($offerRideDetail[0]->isDaily==0)
                                            {
                                                $dd['amount']=$bookRideArray['total'];
                                            }
                                        }
                                        if(count($bookedPersonDetail)>0)
                                        {
                                            $dd['bookedname']=$bookedPersonDetail[0]->username;
                                            $dd['bookedemail']=$bookedPersonDetail[0]->email;
                                        }
                                        if(isset($dd['email']))
                                        {
                                            if($dd['email']!="")
                                            {
                                                $this->send_ride_email($dd['email'],$dd);
                                            }
                                        }
                                        if(isset($dd['bookedemail']))
                                        {
                                            if($dd['bookedemail']!="")
                                            {
                                                $this->send_book_ride_email($dd['bookedemail'],$dd);
                                            }
                                        }
                                        
                                        $response['message'] = "Your Ride has been booked successfully..";
                                        $response['errormessage']=$errors;
                                        $response['status'] = true;
                                        $response['data'] = array();
                                        return response($response,200);
                                    }
                                    else
                                    {
                                        DB::rollback();
                                        $response['message'] = "Opps something wrong";
                                        $response['errormessage']=$errors;
                                        $response['status'] = false;
                                        $response['data'] = array();
                                        return response($response,400);
                                    }                           
                                }
                                else
                                {
                                    $insert_array=array("userId"=>$bookRideArray['offerpersonid'],"amount"=>$costSeat);
                                    $update=DB::table('payment_wallete')->insert($insert_array);
                                    //add service charge in admin wallet
                                    $in=DB::table('admin_wallet')->insert(['rideId'=>$bookRideArray['offerid'],"userId"=>$bookRideArray['riderid'],"amount"=>$taxamount,"bookType"=>"book","isDaily"=>$isDaily]);
                                    if($in>=0)
                                    {
                                        DB::commit();
                                        //GET DETAILS OF OFFERED PERSON
                                        $offerPersonDetail=DB::table('users')->where('id',$bookRideArray['offerpersonid'])->get();
                                        //GET RIDE DETAILS
                                        $offerRideDetail=DB::table('rides')->where('id',$bookRideArray['offerid'])->get();
                                        //GET DETAILS OF BOOKED PERSON
                                        $bookedPersonDetail=DB::table('users')->where('id',$userId)->get();

                                        if(count($offerPersonDetail)>0)
                                        {
                                            $dd['username']=$offerPersonDetail[0]->username;
                                            $dd['email']=$offerPersonDetail[0]->email;    

                                        }
                                        if(count($offerRideDetail)>0)
                                        {
                                            $dd['date']=date("d-m-Y",strtotime($offerRideDetail[0]->departure_date));
                                            $dd['time']=date("H:i:s",strtotime($offerRideDetail[0]->departure_date));
                                            $dd['source']=$bookRideArray['source'];
                                            $dd['destination']=$bookRideArray['destination'];
                                            $dd['seat']=$bookRideArray['seats'];
                                            if($offerRideDetail[0]->isDaily==0)
                                            {
                                                $dd['amount']=$bookRideArray['total'];
                                            }
                                        }
                                        if(count($bookedPersonDetail)>0)
                                        {
                                            $dd['bookedname']=$bookedPersonDetail[0]->username;
                                            $dd['bookedemail']=$bookedPersonDetail[0]->email;
                                        }
                                        if(isset($dd['email']))
                                        {
                                            if($dd['email']!="")
                                            {
                                                $this->send_ride_email($dd['email'],$dd);
                                            }
                                        }
                                        if(isset($dd['bookedemail']))
                                        {
                                            if($dd['bookedemail']!="")
                                            {
                                                $this->send_book_ride_email($dd['bookedemail'],$dd);
                                            }
                                        }
                                        $response['message'] = "Your Ride has been booked successfully..";
                                        $response['errormessage']=$errors;
                                        $response['status'] = true;
                                        $response['data'] = array();
                                        return response($response,200);
                                    }
                                    else
                                    {
                                        DB::rollback();
                                        $response['message'] = "Opps something wrong";
                                        $response['errormessage']=$errors;
                                        $response['status'] = false;
                                        $response['data'] = array();
                                        return response($response,400);
                                    }
                                }
                            }
                            else
                            {
                                DB::rollback();
                                $response['message'] = "Opps something wrong";
                                $response['errormessage']=$errors;
                                $response['status'] = false;
                                $response['data'] = array();
                                return response($response,400);
                            }
                        }
                    }
                    else
                    {
                        $response['message'] = "Opps something wrong";
                        $response['errormessage']=$errors;
                        $response['status'] = false;
                        $response['data'] = array();
                        return response($response,400);
                    }                  
                }
                else
                {
                    $response['message'] = "requested seat is not available";
                    $response['errormessage']=$errors;
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,400);
                }
            }
            catch(\Exception $e)
            {
                $response['message'] = "Opps something wrong";
                $response['errormessage']=$errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,400);
            }
        }   
    }
    //********************** ride book ends **********************************

    //---------------------- get ad ------------------------------------------
    public function getAd(Request $request)
    {
        try
        {
            $param=$request->all();
            $date=date("Y-m-d");
            $errors=array();
            $fetchAd=DB::table('advertisment')
                        ->where('is_deleted',0)
                        ->where('start_date','<=',$date)
                        ->where('end_date','>=',$date)
                        ->get();
            if(count($fetchAd)>0)
            {
                $response['message'] = "advertisment found";
                $response['errormessage']=$errors;
                $response['status'] = true;
                $response['data'] = $fetchAd;
                return response($response,200);
            }
            else
            {
                $response['message'] = "No advertisment found";
                $response['errormessage']=$errors;
                $response['status'] = true;
                $response['data'] = array();
                return response($response,200);
            }
        }
        catch(\Exception $e)
        {
            $errors[]="Please try again";
            $response['message'] = "Please try again";
            $response['errormessage']=$errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,400);
        }
    }          
    //********************** get ad ends *************************************
}
