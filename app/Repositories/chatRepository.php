<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use Validator; 
use DB;
use Hash;
use App\Http\Controllers\HelperController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Response;

class chatRepository
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
    //function
    public function getMessage()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $param=json_decode($request['json'],true);
                $userid1=$param['user'];
                $errors=array();
                $uid=session('userId');
               
                $message=[
                    'user.required'  =>  'incorrect request',
                    'user.integer'   =>  'incorrect request'
                ];
                $validator=Validator::make($param,[
                        'user'    =>  'required|integer'
                    ],$message);
                if($validator->fails())
                { 
                    $errors[0]="incorrect request";
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                }
                else
                {
                    if($userid1<=0)
                    {
                        $errors[0]="incorrect request";
                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                    }
                    else
                    {
                        $data=array();
                        $flag=false;
                        $data[0]=session('userId');

                        $allMessage=DB::table('user_chat_messages')->select('chatMessageId','fromUserId','toUserId','message','createdDate','readMsg')
                            ->whereRaw('(fromUserId="'.$userid1.'" or fromUserId="'.$uid.'") and (toUserId="'.$userid1.'" or toUserId="'.$uid.'")')->get();
                        $data[1]=$allMessage;

                        $userProfile=DB::table('users')->select('username','profile_pic','isverifyemail','isverifyphone')->where('id',$userid1)->get();
                        $data[2]=$userProfile;

                        $checkcount=DB::table('ride_booking')->select('departure_date')
                                    ->leftJoin('rides','ride_booking.rideId','=','rides.id')
                                    ->whereRaw('(offer_userId="'.$userid1.'" and book_userId="'.$uid.'") or (offer_userId="'.$uid.'" and book_userId="'.$userid1.'")')
                                    ->where('ride_booking.is_deleted',0)
                                    ->orderBy('departure_date','desc')
                                    ->first();
                                    
                        if(count($checkcount)>0)
                        {
                            $depdate=date("Y-m-d H:i:s",strtotime($checkcount->departure_date));
                            $currentDate=date("Y-m-d H:i:s");
                            if($currentDate>=$depdate)
                            {
                                $flag=false;
                            }
                            else
                            {
                                $flag=true;
                            }
                        }
                        else
                        {
                            $flag=false;
                        }
                        $data[3]=$flag;
                        return Response::json(array('status'=>true,'error'=>$errors,'message'=>'success','data'=>$data,'class'=>'success'),200);
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
            return Response::json(array('error'=>true), 400);
        }
    }

    //function for sending msg
    public function sendMessage()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $errors=array();
                $param=json_decode($request['json'],true);
                $insertArray=array("fromUserId"=>session('userId'),"toUserId"=>$param['user'],"message"=>$param['msg'],"ip"=>$this->ip);
                $status=DB::table('user_chat_messages')->insertGetId($insertArray);
                if($status>0)
                {
                    //inserted successfully
                    $data=array();
                    return Response::json(array('status'=>true,'error'=>$errors,'message'=>'success','data'=>$data,'class'=>'success'),200);
                }
                else
                {
                    //something error
                    $errors[0]="incorrect request";
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
                }
            }
            else
            {
                return Response::json(array('error'=>true), 400);
            }
        }
        catch(\Exception $e)
        {
            return Response::json(array('error'=>true), 400);
        }
    }
    //function call if user is not typing
    public function isNotTyping()
    {
        try
        {
            $request=$this->request->all();
            $param=json_decode($request['json'],true);
            $userid=$param['user'];
            $record=DB::table('user_chat')->where('fromUser',session('userId'))->where('toUser',$userid)->first();
            if(count($record)>0)
            {
                $stat=DB::table('user_chat')->where('fromUser',session('userId'))->where('toUser',$userid)->update(['fromUserTyping'=>0]);
                print_r($stat);
            }
        }
        catch(\Exception $e)
        {

        }
    }
    public function isTyping()
    {
        try
        {
            $request=$this->request->all();
            $param=json_decode($request['json'],true);
            $userid=$param['user'];
            $record=DB::table('user_chat')->where('fromUser',session('userId'))->where('toUser',$userid)->first();
            if(count($record)>0)
            {
                $stat=DB::table('user_chat')->where('fromUser',session('userId'))->where('toUser',$userid)->update(['fromUserTyping'=>1]);
                print_r($stat);
            }
        }
        catch(\Exception $e)
        {

        }
    }
    public function retriveMessage()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $param=json_decode($request['json'],true);
                $userid=$param['user'];
                $msgid=$param['msgid'];
                $uid=session('userId');
                $chatData=DB::table('user_chat_messages')->select('chatMessageId','fromUserId','toUserId','message','createdDate','readMsg')
                            ->whereRaw('(fromUserId="'.$userid.'" and toUserId="'.$uid.'") and chatMessageId >= '.$msgid.'')->get();


                $profilepic=DB::table('users')->select('profile_pic')->where('id',$userid)->get();
                $data=array();
                $data[0]=$profilepic;
                $data[1]=$chatData;             
                //$data[]=$allMessage;
                $errors=array();
                return Response::json(array('status'=>true,'error'=>$errors,'message'=>'success','data'=>$data,'class'=>'success'),200);
            }
            else
            {
                return Response::json(array('error'=>true), 400);
            }
        }
        catch(\Exception $e)
        {
            return Response::json(array('error'=>true), 400);
        }
    }
}
