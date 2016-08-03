<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use Validator; 
use DB;
use App\Http\Controllers\HelperController;
use Response;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ratingRepository
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
    
    //this function is for fetch data of exchange rating
    public function getExchangeRating()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                if(session()->has('userId'))
                {
                    $fetchUser=$this->giveRatedUserList();
                    $error=array();
                    return Response::json(array('status'=>true,'error'=>$error,'message'=>'success','data'=>$fetchUser,'class'=>'success'),200);
                }
                else
                {
                    \Log::error('userid not set getExchangeRating  function error: ');
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
            \Log::error('getExchangeRating function error: ' . $e->getMessage());
            return Response::json(array('error'=>true), 400); 
        }
    }
    public function ratingExchange()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $parameter=json_decode($request['json'],true);
                $requiredFieldArray=array("touser","rating");
                $check=HelperController::checkParameter($requiredFieldArray,$parameter);
                if($check==1)
                {
                    $errors=array();
                    $errors[]="Your request is incorrect";
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                }
                $message=[
                    'touser.required'   =>  'User is required',
                    'rating.required'   =>  'Rating is required',
                    'rating.integer'    =>  'Rating must be an integer'
                ];
                $validator=Validator::make($parameter,[
                        'touser'    =>  'required',
                        'rating'    =>  'required|integer'
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
                    $insertArray=array("fromUser"=>session('userId'),"toUser"=>$parameter['touser'],"ratting"=>$parameter['rating']);
                    $insert=DB::table('user_ratting')->insert($insertArray);
                    if($insert)
                    {
                        $avgRating=DB::table('user_ratting')->where('fromUser',session('userId'))->avg('ratting');
                        $updateRating=DB::table('users')->where('id',$parameter['touser'])->update(['rating'=>$avgRating]);
                        $fetchUser=$this->giveRatedUserList();
                        $errors=array();
                        return Response::json(array('status'=>true,'error'=>$errors,'message'=>'rating given successfully','data'=>$fetchUser,'class'=>'success'),200);
                    }  
                    else
                    {
                        $errors=array();
                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
                    }
                }
            }
            else
            {
                return Response::json(array('error'=>true),400);       
            }
        }
        catch(\Exception $e)
        {
            \Log::error('ratingExchange function error'.$e->getMessage());
            return Response::json(array('error'=>true),400);
        }
    }
    public function giveRatedUserList()
    {
        $ratedUser=array();
        $alreadyRatedUserList=DB::table('user_ratting')->select('toUser')->where('fromUser',session('userId'))->get();
        if(count($alreadyRatedUserList)>0)
        {
            for($i=0;$i<count($alreadyRatedUserList);$i++)
            {
                $ratedUser[]=$alreadyRatedUserList[$i]->toUser;
            }
        }

        $fetchUser=DB::table('ride_booking')->select('users.id',DB::raw('CONCAT(first_name," ",last_name) as name'))
                    ->leftJoin('users','ride_booking.offer_userId','=','users.id')
                    ->where('ride_booking.book_userId','=',session('userId'));
        if(count($ratedUser)>0)
        {
            $fetchUser=$fetchUser->whereNotIn('offer_userId',$ratedUser);
        }
        $fetchUser=$fetchUser->distinct()->get();
        return $fetchUser;
    }

    public function ratingGiven()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                
                $aColumns = array('user_ratting.id');
                $sTable = 'user_ratting';

              

                $iDisplayStart = $request['iDisplayStart'];
                $iDisplayLength = $request['iDisplayLength'];
                $iSortCol_0 = $request['iSortCol_0'];
                $iSortingCols = $request['iSortingCols'];

                $sSearch = $request['sSearch'];
                $sEcho = $request['sEcho'];

                $ridesData=DB::table('user_ratting')->select('profile_pic',DB::raw('CONCAT(first_name," ",last_name) as name'),'user_ratting.system_datetime','ratting','users.id as userId')
                        ->leftJoin('users','user_ratting.toUser','=','users.id')
                        ->where('user_ratting.fromUser','=',session('userId'));

                
                $ridesDataCount = $ridesData->count();
                // dd($ridesData);
                if (!empty($sSearch)) {

                    $ridesData = $ridesData->whereRaw('(CONCAT(first_name," ",last_name) LIKE "%' . $sSearch . '%" or date_format(user_ratting.system_datetime,"%d-%m-%Y")  LIKE "%' . $sSearch . '%"  or ratting LIKE "%' . $sSearch . '%")');
                }

                $iTotal = $ridesData->count();
                //dd($ridesData->toSql());

                if (isset($iSortCol_0)) {
                    for ($i = 0; $i < intval($iSortingCols); $i++) {
                        $iSortCol = $request['iSortCol_' . $i];
                        $bSortable = $request['bSortable_' . intval($iSortCol)];
                        $sSortDir = $request['sSortDir_' . $i];

                        if ($bSortable == 'true') {
                            $ridesData = $ridesData->orderBy($aColumns[intval($iSortCol)], $sSortDir);
                        } else {
                            $ridesData = $ridesData->orderBy('user_ratting.system_datetime', 'desc');
                        }
                    }
                }
                if (isset($iDisplayStart) && $iDisplayLength != '-1') {
                    $ridesData = $ridesData->skip($iDisplayStart)
                            ->take($iDisplayLength);
                }
                $ridesData = $ridesData->get();

                // Output
                $output = array(
                    'sEcho' => intval($sEcho),
                    'iTotalRecords' => intval($iTotal),
                    'iTotalDisplayRecords' => intval($iTotal),
                    'aaData' => array()
                );
                $lpath=env('APP_LOCAL_URL');
                $i = $iDisplayStart + 1;
                foreach ($ridesData as $key => $value) {
                    // dd($value);
                    
                    
                    $newrideData = array();
                    $message='<div class="col-md-3 col-sm-3 text-center">';
                    if($value->profile_pic!='default.png')
                    {
                        $path="/images/profile/".$value->userId."/".$value->profile_pic;
                    }
                    else
                    {
                        $path="/images/default.png";
                    }
                    $message.='<img src="'.$lpath.$path.'" height="80px" width="80px" style="border-radius:5px"/>';
                    $message.='</div><div class="col-md-6 col-sm-5 xs-text-center">';
                    $message.='<div><h5>'.ucwords($value->name).'</h5></div>';
                    $message.='<div></div></div>';
                    $message.='<div class="col-md-3 text-right xs-text-center PLR0 col-sm-4">';
                    $message.='<div>';
                    $message.='<i class="zmdi zmdi-calendar-alt zmdi-hc-lg"></i> <span>'.date("d-F-Y",strtotime($value->system_datetime)).'</span>';
                    $message.='</div>';
                    $message.='<div class="stars1">';

                    if($value->ratting==5)
                    {
                        $message.='<input type="radio" class="star1 star1-5" id="star-d5" value="5" checked disabled="disabled"/><label for="star-d5" class="star1 star1-5"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-5" id="star-c5" value="5"/><label for="star-d5" class="star1 star1-5"></label>';
                    }

                    if($value->ratting==4)
                    {
                        $message.='<input type="radio" class="star1 star1-4" id="star-d4" value="4" checked disabled="disabled"/><label for="star-d4" class="star1 star1-4"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-4" id="star-d4" value="4" disabled="disabled"/><label for="star-d4" class="star1 star1-4"></label>';
                    }
                    if($value->ratting==3)
                    {
                        $message.='<input type="radio" class="star1 star1-3" id="star-d3" value="3" checked disabled="disabled"/><label for="star-d3" class="star1 star1-3"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-3" id="star-d3" value="3" disabled="disabled"/><label for="star-d3" class="star1 star1-3"></label>';
                    }
                    
                    if($value->ratting==2)
                    {
                        $message.='<input type="radio" class="star1 star1-2" id="star-d2" value="2" checked disabled="disabled"/><label for="star-d2" class="star1 star1-2"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-2" id="star-d2" value="2" disabled="disabled"/><label for="star-d2" class="star1 star1-2"></label>';
                    }
                                        
                    if($value->ratting==1)
                    {
                        $message.='<input type="radio" class="star1 star1-1" id="star-d1" value="1" checked disabled="disabled"/><label for="star-d1" class="star1 star1-1"></label>';    
                    }                    
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-1" id="star-d1" value="1" disabled="disabled"/><label for="star-d1" class="star1 star1-1"></label>';
                    }
                    $message.='<div class="clearfix"></div></div>';
                    $newrideData[] = $message; 
                    
                    $output['aaData'][] = $newrideData;
                    $i++;
                }
                
                echo json_encode($output);
            }
            else
            {
                return Response::json(array('error'=>true), 400);
            }
        }
        catch(\Exception $e)
        {
            \Log::error('ratingGiven function error'.$e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }
    //this function is for rating received users
    public function ratingReceived()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                
                $aColumns = array('user_ratting.id');
                $sTable = 'user_ratting';

                $iDisplayStart = $request['iDisplayStart'];
                $iDisplayLength = $request['iDisplayLength'];
                $iSortCol_0 = $request['iSortCol_0'];
                $iSortingCols = $request['iSortingCols'];

                $sSearch = $request['sSearch'];
                $sEcho = $request['sEcho'];

                $ridesData=DB::table('user_ratting')->select('profile_pic',DB::raw('CONCAT(first_name," ",last_name) as name'),'user_ratting.system_datetime','ratting','users.id as userId')
                        ->leftJoin('users','user_ratting.fromUser','=','users.id')
                        ->where('user_ratting.toUser','=',session('userId'));

                
                $ridesDataCount = $ridesData->count();
                // dd($ridesData);
                if (!empty($sSearch)) {

                    $ridesData = $ridesData->whereRaw('(CONCAT(first_name," ",last_name) LIKE "%' . $sSearch . '%" or date_format(user_ratting.system_datetime,"%d-%m-%Y")  LIKE "%' . $sSearch . '%"  or ratting LIKE "%' . $sSearch . '%")');
                }

                $iTotal = $ridesData->count();
                //dd($ridesData->toSql());

                if (isset($iSortCol_0)) {
                    for ($i = 0; $i < intval($iSortingCols); $i++) {
                        $iSortCol = $request['iSortCol_' . $i];
                        $bSortable = $request['bSortable_' . intval($iSortCol)];
                        $sSortDir = $request['sSortDir_' . $i];

                        if ($bSortable == 'true') {
                            $ridesData = $ridesData->orderBy($aColumns[intval($iSortCol)], $sSortDir);
                        } else {
                            $ridesData = $ridesData->orderBy('user_ratting.system_datetime', 'desc');
                        }
                    }
                }
                if (isset($iDisplayStart) && $iDisplayLength != '-1') {
                    $ridesData = $ridesData->skip($iDisplayStart)
                            ->take($iDisplayLength);
                }
                $ridesData = $ridesData->get();

                // Output
                $output = array(
                    'sEcho' => intval($sEcho),
                    'iTotalRecords' => intval($iTotal),
                    'iTotalDisplayRecords' => intval($iTotal),
                    'aaData' => array()
                );
                $lpath=env('APP_LOCAL_URL');
                $i = $iDisplayStart + 1;
                foreach ($ridesData as $key => $value) {
                    // dd($value);
                    
                    
                    $newrideData = array();
                    $message='<div class="col-md-3 col-sm-3 col-sm-6 text-center">';
                    if($value->profile_pic=="default.png")
                    {
                        $path="/images/default.png";
                    }
                    else
                    {
                        $path="/images/profile/".$value->userId."/".$value->profile_pic;
                    }
                    $message.='<img src="'.$lpath.$path.'" height="80px" width="80px" style="border-radius:5px"/>';
                    $message.='</div><div class="col-md-6 col-sm-5 xs-text-center">';
                    $message.='<div><h5>'.ucwords($value->name).'</h5></div>';
                    $message.='<div></div></div>';
                    $message.='<div class="col-md-3 text-right xs-text-center PLR0 col-sm-4">';
                    $message.='<div>';
                    $message.='<i class="zmdi zmdi-calendar-alt zmdi-hc-lg"></i> <span>'.date("d-F-Y",strtotime($value->system_datetime)).'</span>';
                    $message.='</div>';
                    $message.='<div class="stars1">';

                    if($value->ratting==5)
                    {
                        $message.='<input type="radio" class="star1 star1-5" id="star-c5" value="5" checked disabled="disabled"/><label for="star-c5" class="star1 star1-5"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-5" id="star-c5" value="5" disabled="disabled"/><label for="star-c5" class="star1 star1-5"></label>';
                    }

                    if($value->ratting==4)
                    {
                        $message.='<input type="radio" class="star1 star1-4" id="star-c4" value="4" checked disabled="disabled"/><label for="star-c4" class="star1 star1-4"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-4" id="star-c4" value="4" disabled="disabled"/><label for="star-c4" class="star1 star1-4"></label>';
                    }
                    if($value->ratting==3)
                    {
                        $message.='<input type="radio" class="star1 star1-3" id="star-c3" value="3" checked disabled="disabled"/><label for="star-c3" class="star1 star1-3"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-3" id="star-c3" value="3" disabled="disabled"/><label for="star-c3" class="star1 star1-3"></label>';
                    }
                    
                    if($value->ratting==2)
                    {
                        $message.='<input type="radio" class="star1 star1-2" id="star-c2" value="2" checked disabled="disabled"/><label for="star-c2" class="star1 star1-2"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-2" id="star-c2" value="2" disabled="disabled"/><label for="star-c2" class="star1 star1-2"></label>';
                    }
                                        
                    if($value->ratting==1)
                    {
                        $message.='<input type="radio" class="star1 star1-1" id="star-c1" value="1" checked disabled="disabled"/><label for="star-c1" class="star1 star1-1"></label>';    
                    }                    
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-1" id="star-c1" value="1" disabled="disabled"/><label for="star-c1" class="star1 star1-1"></label>';
                    }
                    $message.='<div class="clearfix"></div></div>';
                    $newrideData[] = $message; 
                    
                    $output['aaData'][] = $newrideData;
                    $i++;
                }
                
                echo json_encode($output);
            }
            else
            {
                return Response::json(array('error'=>true), 400);
            }
        }
        catch(\Exception $e)
        {
            \Log::error('ratingReceived function error'.$e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }
}
