<?php
namespace App\Repositories;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Validator; 
use Illuminate\Support\Facades\Mail;
use DB;
use Hash;
use Response;
use App\Http\Controllers\HelperController;
class userRepository
{
    protected $request;
    protected $errors = array();
    protected $service = 'api.ipinfodb.com';
    protected $version = 'v3';
    protected $apiKey = '';

	public function __construct(Request $request)
	{
		$this->request=$request;
        //return redirect()->back()->withErrors(["error"=>"Could not add details! Please try again."]);
	}
    //this is for fetching user details
    public function getUserDetails()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $parameter=$this->request->all();   
                $finalParameter=json_decode($parameter['json'],true);
                if(isset($finalParameter['userid']))
                {
                    $userid=$finalParameter['userid'];
                    if(session('userId')==$userid)
                    {
                        //fet information of user
                        $userdata=DB::table('users')->where('id',$userid)->get();
                        return Response::json(array('error'=>false,'data'=>$userdata),200);
                    }
                    else
                    {
                        //user id not match generate bad request
                        return Response::json(array('error'=>true), 400);
                    }
                }
                else
                {
                    //400 status code for bad request
                    return Response::json(array('error'=>true), 400);  
                }
            }
            else
            {
                return Response::json(array('error'=>true), 400);  
            }
        }
        catch(\Exception $e)
        {
            \Log::error('getUserDetails function error: ' . $e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }
    //this function is for updating user personal information 
    public function updateUserDetails()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $parameter=json_decode($request['json'],true);
                $message=[
                    'username.required'     =>  'username is required',
                    'username.max'          =>  'Maximum 35 characters are allowed',
                    'gender.required'       =>  'gender is required',
                    'first_name.required'   =>  'Firstname is required',
                    'first_name.max'        =>  'Maximum 35 characters are allowed',
                    'last_name.required'    =>  'Lastname is required',
                    'last_name.max'         =>  'Maximum 35 characters are allowed',
                    'birth.required'        =>  'Birth is required'    
                ];
                $validator=Validator::make($parameter,[
                        'username'  =>  'required|max:35',
                        'gender'    =>  'required',
                        'first_name'=>  'required|max:35',
                        'last_name' =>  'required|max:35',
                        'birth'     =>  'required'
                    ],$message);
                if($validator->fails())
                {
                    return Response::json(array('success'=>false,'error'=>$validator->getMessageBag()->toArray(),'message'=>''),200);
                }
                else
                {
                    //check if username is already exists or not
                    $checkUsername=DB::table('users')->where('id','<>',session('userId'))->where('username',$parameter['username'])->get();
                    if(count($checkUsername)>0)
                    {
                        //username is already exists..
                        $error=array();
                        $error['username'][]="Enter unique username";
                        return Response::json(array('success'=>false,'error'=>$error,'message'=>''),200);
                    }//if close
                    else
                    {
                        //update user information
                        $updateUserArray=array("username"=>$parameter['username'],"gender"=>$parameter['gender'],"first_name"=>$parameter['first_name'],"last_name"=>$parameter['last_name'],"description"=>$parameter['bio'],"birthdate"=>$parameter['birth']);

                        $update=DB::table('users')->where('id',session('userId'))->update($updateUserArray);
                        if($update>=0)
                        {
                            $error=array();
                            return Response::json(array('success'=>true,'error'=>$error,'message'=>'Profile updated successfully'),200);
                        }
                        else
                        {
                            //something erro occur
                            return Response::json(array('error'=>true), 400);
                        }
                    }
                }
            }
            else
            {
                return Response::json(array('error'=>true), 400);    
            }
        }
        catch(\Exception $e)
        {
            \Log::error('updateUserDetails function error: ' . $e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
        
    }//function close

    //this function is for email confirmation 
    public function emailConfirmation()
    {
        try
        {   
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $parameter=json_decode($request['json'],true);
                if(isset($parameter['userid']))
                {
                    if($parameter['userid']==session('userId'))
                    {
                        $message=[
                            'email.required'    =>  'Email is required',
                            'email.email'       =>  'Email is not valid',
                            'email.max'         =>  'Email is not allow more than 50 characters',
                            'code.required'     =>  'Code is required',
                            'code.max'          =>  'Code must be of 6 characters',
                            'code.min'          =>  'Code must be of 6 characters'
                        ];
                        $validator=Validator::make($parameter,[
                                'email' =>  'required|email|max:50',
                                'code'  =>  'required|max:6|min:6'
                            ],$message);
                        if($validator->fails())
                        {
                            //validation fails
                            $errors=array();
                            $messages = $validator->messages();             
                            foreach ($messages->all() as $key=>$value) 
                            {
                                $errors[$key]= $value;
                            }
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                        }
                        else
                        {
                            //check if email is change or not and already is verify or not
                            $checkemail=DB::table('users')->where('email',$parameter['email'])->where('id',$parameter['userid'])->get();
                            if(count($checkemail)>0)
                            {
                                //user has not changed his email address
                                //now check if email is already verify or not
                                $isverify=$checkemail[0]->isverifyemail;
                                if($isverify==0)
                                {
                                    //email is not verified
                                    if($checkemail[0]->email_random==$parameter['code'])
                                    {
                                        $stat=DB::table('users')->where('id',session('userId'))->update(['isverifyemail'=>1,"email_random"=>""]);
                                        if($stat>=0)
                                        {
                                            $errors=array();
                                            return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Email has been verified successfully','class'=>'success'),200);
                                        }
                                        else
                                        {
                                            //some error occur
                                            return Response::json(array('error'=>true), 400);
                                        }
                                    }
                                    else
                                    {
                                        //enter wrong code
                                        $errors=array();
                                        $errors[]='Your verification code is wrong';
                                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                                    }
                                }
                                else
                                {
                                    //email is already verified
                                    $errors=array();
                                    return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Email has been already verified','class'=>'info'),200);
                                }
                            }
                            else
                            {
                                //check if email is unique or not
                                $checkemail=DB::table('users')->where('email',$parameter['email'])->where('id','<>',session('userId'))->get();
                                if(count($checkemail)>0)
                                {
                                    //email is already taken by other user so enter unique email id
                                    $errors=array();
                                    $errors[]='Enter unique email';
                                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                                }
                                else
                                {
                                    //email not exists so update email and send verification code to user
                                    $email_random=HelperController::random_password(6);
                                    $stat=DB::table('users')->where('id',session('userId'))->update(['email'=>$parameter['email'],"email_random"=>$email_random,"isverifyemail"=>0]);

                                    if($stat>=0)
                                    {
                                        //sent verification code on email address
                                        HelperController::send_email($parameter['email'],$email_random);
                                        $errors=array();
                                        $errors[]='We have sent you verification code on your email';
                                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'info'),200);
                                    }
                                    else
                                    {
                                        //something error occur
                                        return Response::json(array('error'=>true), 400);
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        return Response::json(array('error'=>true), 400);
                    }
                }
                else
                {
                    return Response::json(array('error'=>true), 400);
                }
            }
            else
            {
                return Response::json(array('error'=>true), 400);   
            }
        }
        catch(\Exception $e)
        {
            \Log::error('emailConfirmation function error: ' . $e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }

    //function is for mobile confirmation
    public function mobileConfirmation()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $parameter=json_decode($request['json'],true);
                if(isset($parameter['userid']))
                {
                    if($parameter['userid']==session('userId'))
                    {
                        $message=[
                            'mobile.required'   =>  'Mobile number is required',
                            'mobile.numeric'    =>  'Mobile number must be numeric',
                            'code.required'     =>  'Code is required',
                            'code.max'          =>  'Code must be of 6 characters',
                            'code.min'          =>  'Code must be of 6 characters'
                        ];
                        $validator=Validator::make($parameter,[
                                'mobile' =>  'required|numeric',
                                'code'   =>  'required|max:6|min:6'
                            ],$message);
                        if($validator->fails())
                        {
                            //validation fails
                            $errors=array();
                            $messages = $validator->messages();             
                            foreach ($messages->all() as $key=>$value) 
                            {
                                $errors[$key]= $value;
                            }
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                        }
                        else
                        {
                            //check if mobile is change or not and already is verify or not
                            $checkmobile=DB::table('users')->where('phone_no',$parameter['mobile'])->where('id',$parameter['userid'])->get();
                            if(count($checkmobile)>0)
                            {
                                //user has not changed his mobile number
                                //now check if mobile is already verify or not
                                $isverify=$checkmobile[0]->isverifyphone;
                                if($isverify==0)
                                {
                                    //mobile is not verified
                                    if($checkmobile[0]->mobile_random==$parameter['code'])
                                    {
                                        $stat=DB::table('users')->where('id',session('userId'))->update(['isverifyphone'=>1,"mobile_random"=>""]);
                                        if($stat>=0)
                                        {
                                            $errors=array();
                                            return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Mobile number has been verified successfully','class'=>'success'),200);
                                        }
                                        else
                                        {
                                            //some error occur
                                            return Response::json(array('error'=>true), 400);
                                        }
                                    }
                                    else
                                    {
                                        //enter wrong code
                                        $errors=array();
                                        $errors[]='Your verification code is wrong';
                                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                                    }
                                }
                                else
                                {
                                    //mobile is already verified
                                    $errors=array();
                                    return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Mobile number has been already verified','class'=>'info'),200);
                                }
                            }
                            else
                            {
                                //check if mobile is unique or not
                                $checkmobile=DB::table('users')->where('phone_no',$parameter['mobile'])->where('id','<>',session('userId'))->get();
                                if(count($checkmobile)>0)
                                {
                                    //mobile is already taken by other user so enter unique mobile id
                                    $errors=array();
                                    $errors[]='Enter unique mobile';
                                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                                }
                                else
                                {
                                    //mobile not exists so update mobile and send verification code to user
                                    $mobile_random=HelperController::random_password(6);
                                    $stat=DB::table('users')->where('id',session('userId'))->update(['phone_no'=>$parameter['mobile'],"mobile_random"=>$mobile_random,"isverifyphone"=>0]);

                                    if($stat>=0)
                                    {
                                        //sent verification code on mobile
                                        \Queue::push(function($job) use($parameter,$mobile_random){
                                        HelperController::send_sms($parameter['mobile'],$mobile_random);
                                            $job->delete();
                                        });
                                        $errors=array();
                                        $errors[]='We have sent you verification code on your mobile';
                                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'info'),200);
                                    }
                                    else
                                    {
                                        //something error occur
                                        return Response::json(array('error'=>true), 400);
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        return Response::json(array('error'=>true), 400);
                    }
                }
                else
                {
                    return Response::json(array('error'=>true), 400);
                }
            }
            else
            {
                return Response::json(array('error'=>true), 400);
            }
        }
        catch(\Exception $e)
        {
            \Log::error('mobileConfirmation function error: ' . $e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
        
    }

    //function is for update user preferences
    public function savePreference()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $parameter=json_decode($request['json'],true);
                $chat=$parameter['chat'];
                $smoke=$parameter['smoke'];
                $pets=$parameter['pets'];
                $music=$parameter['music'];
                if(isset($parameter['userid']))
                {
                    if($parameter['userid']==session('userId'))
                    {

                        //insert preferences into array
                        $userPreference[]=array("userid"=>session('userId'),"preferenceId"=>1,"pref_optionId"=>$chat);
                        $userPreference[]=array("userid"=>session('userId'),"preferenceId"=>2,"pref_optionId"=>$smoke);
                        $userPreference[]=array("userid"=>session('userId'),"preferenceId"=>3,"pref_optionId"=>$pets);
                        $userPreference[]=array("userid"=>session('userId'),"preferenceId"=>4,"pref_optionId"=>$music);
                        //first delete old preferences
                        $stat=DB::table('user_preferences')->where('userid',session('userId'))->update(['isDeleted'=>1]);
                        $insert=DB::table('user_preferences')->insert($userPreference);
                        if($insert>=0)
                        {
                            $errors=array();
                            return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Preferences updated successfully','class'=>'success'),200);
                        }
                        else
                        {
                            //not inserted successfully
                            $errors=array();
                            $errors[]="Please try again for update preferences";
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
                        }
                    }
                    else
                    {
                        //bad request
                        return Response::json(array('error'=>true), 400);
                    }
                }
                else
                {
                    //bad request
                    return Response::json(array('error'=>true), 400);
                }
            }
            else
            {
                return Response::json(array('error'=>true), 400);   
            }
        }
        catch(\Exception $e)
        {
            \Log::error('savePreference function error: ' . $e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }
    //function is for upload image of user using ajax
    public function imageUpload()
    {
        try
        {
            $request=$this->request->all();

            if(session('userId'))
            {
                if(count($request)>0)
                {
                    
                    //image is there
                    $message=[
                        'pics_0.required'   =>  'Image is required',
                        'Pics_0.max'        =>  'Filesize can not be more than 2 MB'
                    ];
                    $validator=Validator::make($request,[
                        'pics_0'    =>  'required|min:1|max:2048'
                    ],$message);
                    if($validator->fails())
                    {
                        $errors=array();
                        $messages = $validator->messages();             
                        foreach ($messages->all() as $key=>$value) 
                        {
                            $errors[$key]= $value;
                        }
                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                    }
                    else
                    {
                        $UserPhoto=$request['pics_0'];
                        $mime_type=$UserPhoto->getClientMimeType();
                        $mime_array=array('image/gif','image/jpeg','image/png','image/bmp','application/octet-stream','image/bmp');
                        if(in_array($mime_type, $mime_array))
                        {
                            $content_type=$UserPhoto->getClientOriginalExtension();
                            $nameImage=$UserPhoto->getClientOriginalName();

                            //   dd($content_type);
                            // Get image type
                            $userImage = 'profile'.rand(100,999).time().".".$content_type;

                            //Get the file

                            if( is_dir("public/images/profile/".session('userId')) == false ){ 
                            $path = public_path().'/images/profile/'.session('userId') .'/';
                            HelperController::makeDirectory($path, $mode = 0777, true, true);
                            //@chmod("public/images/users/".$userDetails['id'], 0755);
                            }     
                            $destinationPath=  public_path()."/images/profile/".session('userId').'/';
                            //Store in the filesystem.
                            $pathn=$userImage;
                            $data=$UserPhoto->move($destinationPath, $userImage);  
                            DB::table('users')->where('id',session('userId'))->update(['profile_pic'=>$pathn]);
                            $this->request->session()->put('profilePic',$pathn);
                            $errors=array();
                            return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Image Uploaded successfully','class'=>'success','path'=>$pathn,'userid'=>session('userId')),200);
                        }
                        else
                        {
                            $errors=array();
                            $errors[0]="File type is not valid";
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                        }
                    }
                }
                else
                {
                    //select at least one image
                    $errors=array();
                    $errors[]='Select Image';
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                }
            }
            else
            {
                //bad request(session expires)
                return Response::json(array('error'=>true), 400);
            }
        }
        catch(\Exception $e)
        {
            \Log::error('image upload function error ' . $e->getMessage().session('userId'));
            return Response::json(array('error'=>true), 400);            
        }
    }
    //function is for upload licence image of user using ajax
    public function licenceUpload()
    {
        try
        {
            $request=$this->request->all();
           
            if(session('userId'))
            {
                if(count($request)>0)
                {
                    //image is there
                    $message=[
                        'pics_0.required'   =>  'Image is required',
                        'Pics_0.max'        =>  'Filesize can not be more than 2 MB'
                    ];
                    $validator=Validator::make($request,[
                        'pics_0'    =>  'required|image|max:2048'
                    ],$message);
                    if($validator->fails())
                    {
                        $errors=array();
                        $messages = $validator->messages();             
                        foreach ($messages->all() as $key=>$value) 
                        {
                            $errors[$key]= $value;
                        }
                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                    }
                    else
                    {
                        $UserPhoto=$request['pics_0'];
                        $mime_type=$UserPhoto->getClientMimeType();
                        $mime_array=array('image/gif','image/jpeg','image/png','image/bmp','application/octet-stream','image/bmp');
                        if(in_array($mime_type, $mime_array))
                        {
                        
                            $content_type=$UserPhoto->getClientOriginalExtension();
                            $nameImage=$UserPhoto->getClientOriginalName();

                            //   dd($content_type);
                            // Get image type
                            $userImage = 'profile'.rand(100,999).time().".".$content_type;

                            //Get the file

                            if( is_dir("public/images/licence/".session('userId')) == false ){ 
                            $path = public_path().'/images/licence/'.session('userId') .'/';
                            HelperController::makeDirectory($path, $mode = 0777, true, true);
                            //@chmod("public/images/users/".$userDetails['id'], 0755);
                            }     
                            $destinationPath=  public_path()."/images/licence/".session('userId').'/';
                            //Store in the filesystem.
                            $pathn=$userImage;
                            $data=$UserPhoto->move($destinationPath, $userImage);  
                            DB::table('users')->where('id',session('userId'))->update(['licence_pic'=>$pathn]);
                            //$this->request->session()->put('profilePic',$pathn);
                            $errors=array();
                            return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Image Uploaded successfully','class'=>'success','path'=>$pathn,'userid'=>session('userId')),200);
                        }
                        else
                        {
                            $errors=array();
                            $errors[0]="File type is not valid";
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                        }
                    }
                }
                else
                {
                    //select at least one image
                    $errors=array();
                    $errors[]='Select Image';
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                }
            }
            else
            {
                //bad request(session expires)
                return Response::json(array('error'=>true), 400);
            }
        }
        catch(\Exception $e)
        {
            \Log::error('image upload function error ' . $e->getMessage().session('userId'));
            return Response::json(array('error'=>true), 400);            
        }
    }
    //function for send email code
    public function sendEmailCode()
    {
        try
        {
            $request=$this->request->all();
            $parameter=json_decode($request['json'],true);
            if(isset($parameter['userid']))
            {
                if($parameter['userid']==session('userId'))
                {
                    $email=$parameter['email'];
                    $message=[
                        'email.required'    =>  'Email is required',
                        'email.email'       =>  'Email is not valid',
                        'email.max'         =>  'Email is not allow more than 50 characters'
                    ];
                    $validator=Validator::make($parameter,[
                            'email' =>  'required|email|max:50'
                        ],$message);
                    if($validator->fails())
                    {
                        //validation fails
                        $errors=array();
                        $messages = $validator->messages();             
                        foreach ($messages->all() as $key=>$value) 
                        {
                            $errors[$key]= $value;
                        }
                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                    }
                    else
                    {
                        //check if email is change or not and already is verify or not
                        $checkemail=DB::table('users')->where('email',$parameter['email'])->where('id',$parameter['userid'])->get();
                        if(count($checkemail)>0)
                        {
                            //user has not changed his email address
                            //now check if email is already verify or not
                            $isverify=$checkemail[0]->isverifyemail;
                            if($isverify==0)
                            {
                                //email is not verified so send verification code
                                $email_random=HelperController::random_password(6);
                                $stat=DB::table('users')->where('id',session('userId'))->update(['email'=>$parameter['email'],"email_random"=>$email_random,"isverifyemail"=>0]);

                                if($stat>=0)
                                {
                                    //sent verification code on email address
                                    HelperController::send_email($parameter['email'],$email_random);
                                    $errors=array();
                                    $errors[]='We have sent you verification code on your email';
                                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'info'),200);
                                }
                                else
                                {
                                    //something error occur
                                    return Response::json(array('error'=>true), 400);
                                }
                            }
                            else
                            {
                                //email is already verified
                                $errors=array();
                                return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Email has been already verified','class'=>'info'),200);
                            }
                        }
                        else
                        {
                            //check if email is unique or not
                            $checkemail=DB::table('users')->where('email',$parameter['email'])->where('id','<>',session('userId'))->get();
                            if(count($checkemail)>0)
                            {
                                //email is already taken by other user so enter unique email id
                                $errors=array();
                                $errors[]='Enter unique email';
                                return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                            }
                            else
                            {
                                //email not exists so update email and send verification code to user
                                $email_random=HelperController::random_password(6);
                                $stat=DB::table('users')->where('id',session('userId'))->update(['email'=>$parameter['email'],"email_random"=>$email_random,"isverifyemail"=>0]);

                                if($stat>=0)
                                {
                                    //sent verification code on email address
                                    HelperController::send_email($parameter['email'],$email_random);
                                    $errors=array();
                                    $errors[]='We have sent you verification code on your email';
                                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'info'),200);
                                }
                                else
                                {
                                    //something error occur
                                    \Log::error('sendEmailCode function error');
                                    return Response::json(array('error'=>true), 400);
                                }
                            }
                        }
                    }
                }
                else
                {
                    //bad request
                    return Response::json(array('error'=>true), 400);
                }
            }
            else
            {
                //bad request
                return Response::json(array('error'=>true), 400);
            }
        }
        catch(\Exception $e)
        {
            \Log::error('sendEmailCode function error: ' . $e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }
    //function for send mobile verification code
    public function sendMobileCode()
    {
        try
        {
            $request=$this->request->all();
            $parameter=json_decode($request['json'],true);
            if(isset($parameter['userid']))
            {
                if($parameter['userid']==session('userId'))
                {
                    $message=[
                        'mobile.required'   =>  'Mobile number is required',
                        'mobile.numeric'    =>  'Mobile number must be numeric'
                    ];
                    $validator=Validator::make($parameter,[
                            'mobile' =>  'required|numeric'
                        ],$message);
                    if($validator->fails())
                    {
                        //validation fails
                        $errors=array();
                        $messages = $validator->messages();             
                        foreach ($messages->all() as $key=>$value) 
                        {
                            $errors[$key]= $value;
                        }
                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                    }
                    else
                    {
                        //check if mobile is change or not and already is verify or not
                        $checkmobile=DB::table('users')->where('phone_no',$parameter['mobile'])->where('id',$parameter['userid'])->get();
                        if(count($checkmobile)>0)
                        {
                            //user has not changed his mobile number
                            //now check if mobile is already verify or not
                            $isverify=$checkmobile[0]->isverifyphone;
                            if($isverify==0)
                            {
                                //mobile is not verified send code
                                $mobile_random=HelperController::random_password(6);
                                $stat=DB::table('users')->where('id',session('userId'))->update(['phone_no'=>$parameter['mobile'],"mobile_random"=>$mobile_random,"isverifyphone"=>0]);

                                if($stat>=0)
                                {
                                    //sent verification code on mobile
                                    \Queue::push(function($job) use($parameter,$mobile_random){
                                    HelperController::send_sms($parameter['mobile'],$mobile_random);
                                        $job->delete();
                                    });
                                    $errors=array();
                                    $errors[]='We have sent you verification code on your mobile';
                                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'info'),200);
                                }
                                else
                                {
                                    //something error occur
                                    return Response::json(array('error'=>true), 400);
                                }
                            }
                            else
                            {
                                //mobile is already verified
                                $errors=array();
                                return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Mobile number has been already verified','class'=>'info'),200);
                            }
                        }
                        else
                        {
                            //check if mobile is unique or not
                            $checkmobile=DB::table('users')->where('phone_no',$parameter['mobile'])->where('id','<>',session('userId'))->get();
                            if(count($checkmobile)>0)
                            {
                                //mobile is already taken by other user so enter unique mobile id
                                $errors=array();
                                $errors[]='Enter unique mobile';
                                return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                            }
                            else
                            {
                                //mobile not exists so update mobile and send verification code to user
                                $mobile_random=HelperController::random_password(6);
                                $stat=DB::table('users')->where('id',session('userId'))->update(['phone_no'=>$parameter['mobile'],"mobile_random"=>$mobile_random,"isverifyphone"=>0]);

                                if($stat>=0)
                                {
                                    //sent verification code on mobile
                                    \Queue::push(function($job) use($parameter,$mobile_random){
                                    HelperController::send_sms($parameter['mobile'],$mobile_random);
                                        $job->delete();
                                    });
                                    $errors=array();
                                    $errors[]='We have sent you verification code on your mobile';
                                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'info'),200);
                                }
                                else
                                {
                                    //something error occur
                                    return Response::json(array('error'=>true), 400);
                                }
                            }
                        }
                    }
                }
                else
                {
                    return Response::json(array('error'=>true), 400);
                }
            }
            else
            {
                return Response::json(array('error'=>true), 400);
            }
        }
        catch(\Exception $e)
        {
            \Log::error('sendMobileCode function error: ' . $e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }
    //function is for get details of user
    public function getProfile($id,$rideid)
    {
        try
        {
            if(\Session::has('userId'))
            {
                $userCheck=DB::table('rides')->where('userId',$id)->where('id',$rideid)->first();
                if(count($userCheck)==0)
                    return view('errors.404');

                $userDetails=DB::table('users')->select('users.isverifyemail','users.first_name','users.last_name','users.gender','users.isverifyphone','rating','description','profile_pic','created_at','users.created_at as userDate','users.id as userId','birthdate')->where('users.id',$id)->get();

                if(count($userDetails)>0)
                {
                    $totalRide=DB::table('rides')->where('userId',$id)->where('status',0)->count();
                    $login=DB::table('loginLog')->where('users_id',$id)->orderBy('id','desc')->take(1)->get();

                    if(count($login)>0)
                    {
                        $loginDate=date("d-m-Y h:i:s A",strtotime($login[0]->created_at));
                    }
                    else
                    {
                        $loginDate='00-00-0000';
                    }

                    $carDetails=DB::table('rides')->select('car_details.userId','car_make','car_model','vehical_pic','comfort_master.name as comfort','color.color as color')
                        ->leftJoin('car_details','rides.carId','=','car_details.id')
                        ->leftJoin('comfort_master','car_details.comfortId','=','comfort_master.id')
                        ->leftJoin('color','car_details.colorId','=','color.id')
                        ->where('rides.id',$rideid)
                        ->get();
                }
                else
                {
                    return view('errors.404');
                }
                return view('carOwner',['userDetail'=>$userDetails,'totalRide'=>$totalRide,'loginDate'=>$loginDate,'carDetails'=>$carDetails]);
            }
            else
            {
                return redirect('/logout');
            }
        }
        catch(\Exception $e)
        {
            return view('errors.404');
        }
    }
    //function for request of withdrawAmount
    public function withdrawAmount()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $param=$this->request->all();
                $parameter=json_decode($param['json'],true);
                $validator=Validator::make($parameter,[
                        'account_holder'    =>  'required',
                        'bank_name'         =>  'required',
                        'account_no'        =>  'required|numeric',
                        'ifsc_code'         =>  'required',
                        'withdraw_amount'   =>  'required|numeric'
                    ],
                    [
                        'account_holder.required'   =>  'Enter Account holder name',
                        'bank_name.required'        =>  'Enter bank name',
                        'account_no.required'       =>  'Enter Account number',
                        'account_no.numeric'        =>  'Account number allow only numbers',
                        'ifsc_code.required'        =>  'Enter IFSC Code',
                        'withdraw_amount.required'  =>  'Enter Amount',
                        'withdraw_amount.numeric'   =>  'Amount must be numeric'
                    ]);

                if($validator->fails())
                {
                    return Response::json(array('status'=>false,'error'=>$validator->getMessageBag()->toArray(),'message'=>'','class'=>'danger'),200);
                }
                else
                {
                    //check if amount is in multiple of 100 or not
                    if($parameter['withdraw_amount']%100!=0)
                    {
                        $errors['withdraw_amount'][0]="Amount must be in multiple of 100";
                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);       
                    }
                    else
                    {
                        //check requested amount is available in wallet or not
                        $paymentInfo=DB::table('payment_wallete')->where('userId',session('userId'))->get();    
                        if(count($paymentInfo)==0)
                        {
                            $errors['withdraw_amount'][0]="You don't have enough balance to make this request";
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);              
                        }
                        else
                        {
                            if($paymentInfo[0]->amount<$parameter['withdraw_amount'])
                            {
                                $errors['withdraw_amount'][0]="You don't have enough balance to make this request";
                                return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200); 
                            }
                            else
                            {
                                //make request
                                $email='info@sharemywheel.com';
                                $userDetail=DB::table('users')->select('username','email','first_name','last_name','phone_no')->where('id',session('userId'))->get();
                                $dd['account_holder']=$parameter['account_holder'];
                                $dd['bank_name']=$parameter['bank_name'];
                                $dd['account_no']=$parameter['account_no'];
                                $dd['ifsc_code']=$parameter['ifsc_code'];
                                $dd['withdraw_amount']=$parameter['withdraw_amount'];
                                $dd['username']=$userDetail[0]->username;
                                $dd['email']=$userDetail[0]->email;
                                $dd['full_name']=$userDetail[0]->first_name." ".$userDetail[0]->last_name;
                                $dd['phone_no']=$userDetail[0]->phone_no;
                                $this->send_request_withdraw_email($email,$dd);
                                return Response::json(array('status'=>true,'error'=>array(),'message'=>'Your request has been successfully send..we will contact you in short time.','class'=>'success'),200);
                            }
                        }
                    }   
                }
            }
            else
            {
                return Response::json(array('status'=>false,'error'=>array(),'message'=>'Please try again','class'=>'danger'), 400);    
            }
        }
        catch(\Exception $e)
        {
            \Log::info('Showing error in withdrawAmount function');
            return Response::json(array('status'=>false,'error'=>array(),'message'=>'Please try again','class'=>'danger'), 400);
        }
    }

    public function send_request_withdraw_email($email1,$data)
    {
        if(Mail::later(5,'emails.sendrequestwithdrawemail',['name'=>$data],function ($message) use ($email1){
        //
            $message->from('info@sharemywheel.com', 'ShareMyWheel');

            $message->to($email1);
            $message->subject("request for withdraw amount");
            //$message->attach(public_path().'/images/users/8/abc.jpg');
        }))
        {
            //echo "success";
        }
        else
        {
            //echo "error";
            \Log::info('Showing error in send_request_withdraw_email function');
        }
    }

    //function for change password
    public function changePassword()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $param=$this->request->all();
                $validator=Validator::make($param,[
                        'cpassword'         =>  'required',
                        'newPassword'       =>  'required',
                        'confirmPassword'   =>  'required|same:newPassword'  
                    ],[
                        'cpassword.required'        =>  'Enter Current Password',
                        'newPassword.required'      =>  'Enter New Password',
                        'confirmPassword.required'  =>  'Enter Confirm Password',
                        'confirmPassword.same'      =>  'Confirm Password must match with new Password'
                    ]);
                if($validator->fails())
                {
                    return Response::json(array('status'=>false,'error'=>$validator->getMessageBag()->toArray(),'message'=>'','data'=>array()),200);
                }
                else
                {
                    //check password match with the current password
                    $currentpassword=Hash::make($param['cpassword']);
                    $selectPassword=DB::table('users')->where('id',session('userId'))->get();
                    //check if user is facebook login or normal
                    if(Hash::check($param['cpassword'], $selectPassword[0]->password))
                    {
                        $newpassword=Hash::make($param['newPassword']);
                        $updatePassword=DB::table('users')->where('id',session('userId'))->update(['password'=>$newpassword]);
                        if($updatePassword>=0)
                        {
                            return Response::json(array('status'=>true,'error'=>array(),'message'=>'Your password has been changed successfully','data'=>array()),200);
                        }
                        else
                        {
                            $errors=array();
                            $errors['error'][0]="Please try again";
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','data'=>array()),200);
                        }
                    }
                    else
                    {
                        $errors=array();
                        $errors['error'][0]="your current password is wrong.";
                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','data'=>array()),200);
                    }
                }
            }
            else
            {
                $errors=array();
                $errors['error'][0]="Please try again";
                return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','data'=>array()),200);
            }
        }
        catch(\Exception $e)
        {
            \Log::info('Showing error in changePassword'.$e->getMessage());   
            $errors=array();
            $errors['error'][0]="Please try again";
            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','data'=>array()),200);
        }
    }

    public function forgotPassword()
    {
        try
        {
            if(\Session::has('userId'))
                return redirect('/ridelist');

            $param=$this->request->all();
            $validator=Validator::make($param,[
                    'forgotemail'       =>  'required|email'
                ],[
                    'forgotemail.required'      =>  'Enter Email',
                    'forgotemail.email'         =>  'Enter Valid Email Format'
                ]);
            if($validator->fails())
            {
                return Response::json(array('status'=>false,'error'=>$validator->getMessageBag()->toArray(),'message'=>'','data'=>array()),200);
            }
            else
            {
                $find=DB::table('users')->where('email',$param['forgotemail'])->get();
                if(count($find)>0)
                {
                    //check if password null then user can not do forgot password
                    if($find[0]->password=="")
                    {
                        $errors=array();
                        $errors['error'][0]="You can not do forgot password";
                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','data'=>array()),200);
                    }
                    else
                    {
                        //send new password email to users

                        $uid=$find[0]->id;
                        $new_password=HelperController::random_password(8);
                        $password_new=Hash::make($new_password);
                        $username=$find[0]->username;
                        $emailidsend=$find[0]->email;
                        $darray=array();
                        $darray["username"]=$username;
                        $darray["password"]=$new_password;

                        $stat=DB::table('users')->where('id',$uid)->update(['password'=>$password_new]);
                        if($stat>0)
                        {
                            $this->send_forgot_password_email($emailidsend,$darray); 
                            return Response::json(array('status'=>true,'error'=>array(),'message'=>'Login details has been send on your emailid','data'=>array()),200);
                        }
                        else
                        {
                            $errors=array();
                            $errors['error'][0]="Please try again";
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','data'=>array()),200);
                        }
                    }
                }
                else
                {
                    $errors=array();
                    $errors['error'][0]="Emailid is wrong";
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','data'=>array()),200);
                }
            }
        }
        catch(\Exception $e)
        {
            \Log::info('Showing error in forgotPassword'.$e->getMessage());   
            $errors=array();
            $errors['error'][0]="Please try again";
            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','data'=>array()),200);
        }
    }
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
    public function getCityName()
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];

    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];

    $remote = $_SERVER['SERVER_ADDR'];
    print_r($remote);
    exit;
    //$remote  = '198.211.116.19';

    $result  = array('country'=>'', 'city'=>'');
    if(filter_var($client, FILTER_VALIDATE_IP)){
        $ip = $client;
    }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
        $ip = $forward;
    }else{
        $ip = $remote;
    }
    $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));    
    if($ip_data && $ip_data->geoplugin_countryName != null){
        $result['country'] = $ip_data->geoplugin_countryCode;
        $result['city'] = $ip_data->geoplugin_city;
    }
    return $result;
    }
    public function setKey($key){
        if(!empty($key)) $this->apiKey = $key;
    }

    public function getError(){
        return implode("\n", $this->errors);
    }

    public function getCountry($host){
        return $this->getResult($host, 'ip-country');
    }

    public function getCity($host){
        return $this->getResult($host, 'ip-city');
    }

    private function getResult($host, $name){
        $ip = @gethostbyname($host);

        // if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
        if(filter_var($ip, FILTER_VALIDATE_IP)){
            $xml = @file_get_contents('http://' . $this->service . '/' . $this->version . '/' . $name . '/?key=' . $this->apiKey . '&ip=' . $ip . '&format=xml');


            if (get_magic_quotes_runtime()){
                $xml = stripslashes($xml);
            }

            try{
                $response = @new SimpleXMLElement($xml);

                foreach($response as $field=>$value){
                    $result[(string)$field] = (string)$value;
                }

                return $result;
            }
            catch(Exception $e){
                $this->errors[] = $e->getMessage();
                return;
            }
        }

        $this->errors[] = '"' . $host . '" is not a valid IP address or hostname.';
        return;
    }
    //function for fetching lates new of sharemywheel
    public function latest_news()
    {
        try
        {
            $date=date("Y-m-d");
            $allLatestNews=DB::table('latest_news')->where('start_date','<=',$date)->where('end_date','>=',$date)->where('is_deleted',0)->orderBy('start_date', 'desc')->get();
            return view('latestNews',['news'=>$allLatestNews]);
        }
        catch(Exception $e)
        {
            \Log::info('Showing error in latest_news function');
            return view('latestNews');
        }
    }
    //function for fetch coupan code
    public function coupanCode()
    {
        try
        {
            $date=date("Y-m-d");
            $allCoupan=DB::table('coupan_code')->where('start_date','<=',$date)->where('end_date','>=',$date)->where('is_deleted',0)->orderBy('start_date', 'desc')->get();
            return view('coupan',['coupan'=>$allCoupan]);
        }
        catch(\Exception $e)
        {
            \Log::info('Showing error in latest_news function');
            return view('coupan');   
        }
    }
}
