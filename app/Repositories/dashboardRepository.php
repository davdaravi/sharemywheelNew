<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use Validator; 
use DB;
use Hash;
use App\Http\Controllers\HelperController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Response;

class dashboardRepository
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
    public function viewDashboard()
    {   
        //give list of user for messages tab
        try
        {
            if(\Session::has('userId'))
            {
                $uid=array();
                $fromuser=DB::table('user_chat_messages')->select('users.id','username','profile_pic')
                        ->leftJoin('users','user_chat_messages.toUserId','=','users.id')
                        ->where('fromUserId',session('userId'))->distinct();
                
                $msgUser=DB::table('user_chat_messages')->select('users.id','username','profile_pic')
                        ->leftJoin('users','user_chat_messages.fromUserId','=','users.id')
                        ->where('toUserId',session('userId'))->union($fromuser)->distinct()->get();
                
                //messages tab ends

                //rides offered
                $rideOffered=DB::table('rides')->select('rides.id as rideId','departureOriginal','arrivalOriginal','offer_seat','available_seat','cost_per_seat','departure_date','return_date','return_time','is_round_trip','isDaily','ladies_only','view_count')
                            ->where('userId',session('userId'))->where('status',0)->orderBy('departure_date','desc')->get();

                //all car details
                $car_details=DB::table('car_details')->select('id','car_make','car_model','vehical_pic','no_of_seats','created_date')->where('userId',session('userId'))->where('is_deleted',0)->get();
                //select color
                $color=DB::table('color')->where('isDeleted',0)->get();
                //select comfort type
                $comfort_type=DB::table('comfort_master')->where('is_deleted',0)->get();
                //select vehicle type
                $vehicle_type=DB::table('vehical_type')->where('is_deleted',0)->get();
                //select preferences options
                $preference_option=DB::table('preferences_option')->get();
                //select user preferences
                $user_preferences_option=DB::table('user_preferences')->where('isDeleted',0)->where('userid',session('userId'))->get();
                
                return view('dashboard',['preference_option'=>$preference_option,'user_preference'=>$user_preferences_option,'vehicle_type'=>$vehicle_type,'comfort'=>$comfort_type,'color'=>$color,'car_details'=>$car_details,'rideOffered'=>$rideOffered,'msgUser'=>$msgUser]);
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
    //function for paid transaction
    public function paidTransaction()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                
                $aColumns = array('rides.departure_date');
                $sTable = 'rides';
                $iDisplayStart = $request['iDisplayStart'];
                $iDisplayLength = $request['iDisplayLength'];
                $iSortCol_0 = $request['iSortCol_0'];
                $iSortingCols = $request['iSortingCols'];

                $sSearch = $request['sSearch'];
                $sEcho = $request['sEcho'];

                $ridesData=DB::table('ride_booking')->select('ride_booking.rideId','ride_booking.no_of_seats as seats','ride_booking.cost_per_seat','ride_booking.source','ride_booking.destination','rides.departure_date',DB::raw('CONCAT(first_name," ",last_name) as name'),'rating','profile_pic','rides.departureCity','rides.arrivalCity','users.id as userId')
                        ->leftJoin('rides','ride_booking.rideId','=','rides.id')
                        ->leftJoin('users','ride_booking.offer_userId','=','users.id')
                        ->where('ride_booking.book_userId','=',session('userId'))
                        ->where('ride_booking.is_deleted','=',0);

                
                $ridesDataCount = $ridesData->count();
                // dd($ridesData);
                if (!empty($sSearch)) {

                    $ridesData = $ridesData->whereRaw('(CONCAT(first_name," ",last_name) LIKE "%' . $sSearch . '%" or date_format(rides.departure_date,"%d-%m-%Y")  LIKE "%' . $sSearch . '%"  or rating LIKE "%' . $sSearch . '%" or ride_booking.destination LIKE "%'.$sSearch.'%" or ride_booking.source LIKE "%'.$sSearch.'%" or ride_booking.no_of_seats LIKE "%'.$sSearch.'%" or ride_booking.cost_per_seat LIKE "%'.$sSearch.'%")');
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
                            $ridesData = $ridesData->orderBy('rides.departure_date', 'desc');
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
                    
                    $newrideData = array();
                    $message='<div class="panel panel-default" style="margin-bottom:10px">';
                    $message.='<div class="panel-heading"><h5>';
                    $message.='<span>'.$value->source.'</span>';
                    $message.='&nbsp;<i class="zmdi zmdi-arrow-right"></i>&nbsp;'; 
                    $message.='<span>'.$value->destination.'</span>';
                    $message.='</h5></div><div class="panel-body">';
                    $message.='<div class="col-md-6 col-sm-6"><div>';
                    $message.='<i class="zmdi zmdi-calendar-alt zmdi-hc-lg"></i>&nbsp;&nbsp;<span>'.date("l d F Y - h:i A",strtotime($value->departure_date)).'</span>';
                    $message.='</div><div class="margin-top-10">';
                    if($value->profile_pic=='default.png')
                    {
                        $path="/images/default.png";
                    }
                    else
                    {
                        $path="/images/profile/".$value->userId."/".$value->profile_pic;
                    }
                    $message.='<img src="'.$lpath.$path.'" height="50" width="50" style="border-radius:50%;border:1px solid #eee;box-shadow:0px 1px 1px 1px #ccc"/>&nbsp;&nbsp;'.ucwords($value->name);
                    $message.='</div>';
                    $message.='<div class="margin-top-10">';
                    $message.='<div><label class="control-label">Rating:</label></div>';
                    $message.='<div style="color:#999" class="stars1">';
                                                                   
                    if($value->rating==5)
                    {
                        $message.='<input type="radio" class="star1 star1-5" id="star-a5" value="5" checked disabled="disabled"/><label for="star-a5" class="star1 star1-5"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-5" id="star-a5" value="5" disabled="disabled"/><label for="star-a5" class="star1 star1-5"></label>';         
                    }
                    if($value->rating==4)
                    {
                        $message.='<input type="radio" class="star1 star1-4" id="star-a4" value="4" checked disabled="disabled"/><label for="star-a4" class="star1 star1-4"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-4" id="star-a4" value="4" disabled="disabled"/><label for="star-a4" class="star1 star1-4"></label>';            
                    }
                    if($value->rating==3)
                    {
                        $message.='<input type="radio" class="star1 star1-3" id="star-a3" value="3" checked disabled="disabled"/><label for="star-a3" class="star1 star1-3"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-3" id="star-a3" value="3" disabled="disabled"/><label for="star-a3" class="star1 star1-3"></label>';   
                    }
                    if($value->rating==2)
                    {
                        $message.='<input type="radio" class="star1 star1-2" id="star-a2" value="2" checked disabled="disabled"/><label for="star-a2" class="star1 star1-2"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-2" id="star-a2" value="2" disabled="disabled"/><label for="star-a2" class="star1 star1-2"></label>';
                    }                
                    if($value->rating==1)
                    {
                        $message.='<input type="radio" class="star1 star1-1" id="star-a1" value="1" checked disabled="disabled"/><label for="star-a1" class="star1 star1-1"></label>';    
                    }                    
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-1" id="star-a1" value="1" disabled="disabled"/><label for="star-a1" class="star1 star1-1"></label>';
                    }
                    $message.='</div></div></div>';
                    $message.='<div class="col-md-6 col-sm-6 text-right">';
                    $message.='<div>';
                    $message.='<span style="color:#777">'.$value->seats.' seats booked</span>';
                    $message.='</div>';
                    $message.='<div class="margin-top-10">';
                    $message.='<span style="color:red;font-size:16px">Paid '.(int)$value->cost_per_seat.' &#8377;</span>';
                    $message.='</div>';
                    $message.='</div>';
                    $message.='<div class="clearfix"></div>';
                    $message.='<hr/>';
                    $message.='<div class="col-md-6 col-sm-6">';
                    $message.='</div>';
                    $message.='<div class="col-md-6 col-sm-6 text-right">';
                    $message.='<span><a href="ridedetail/'.$value->departureCity.'_'.$value->arrivalCity.'_'.$value->rideId.'"><i class="zmdi zmdi-eye zmdi-hc-lg"></i> See ride Detail  </a></span> ';
                    $message.='</div>';
                    $message.='</div>';
                    $message.='</div>';
                    $message.='<div class="clearfix"></div>';
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
    //function is for earned transaction
    public function earnTransaction()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $aColumns = array('rides.departure_date');
                $sTable = 'rides';
                $iDisplayStart = $request['iDisplayStart'];
                $iDisplayLength = $request['iDisplayLength'];
                $iSortCol_0 = $request['iSortCol_0'];
                $iSortingCols = $request['iSortingCols'];

                $sSearch = $request['sSearch'];
                $sEcho = $request['sEcho'];

                $ridesData=DB::table('ride_booking')->select('ride_booking.rideId','ride_booking.no_of_seats as seats','ride_booking.cost_per_seat','ride_booking.source','ride_booking.destination','rides.departure_date',DB::raw('CONCAT(first_name," ",last_name) as name'),'rating','profile_pic','rides.departureCity','rides.arrivalCity','users.id as userId')
                        ->leftJoin('rides','ride_booking.rideId','=','rides.id')
                        ->leftJoin('users','ride_booking.book_userId','=','users.id')
                        ->where('ride_booking.offer_userId','=',session('userId'))
                        ->where('ride_booking.is_deleted','=',0);

                $ridesDataCount = $ridesData->count();
                // dd($ridesData);
                if (!empty($sSearch)) {

                    $ridesData = $ridesData->whereRaw('(CONCAT(first_name," ",last_name) LIKE "%' . $sSearch . '%" or date_format(rides.departure_date,"%d-%m-%Y")  LIKE "%' . $sSearch . '%"  or rating LIKE "%' . $sSearch . '%" or ride_booking.destination LIKE "%'.$sSearch.'%" or ride_booking.source LIKE "%'.$sSearch.'%" or ride_booking.no_of_seats LIKE "%'.$sSearch.'%" or ride_booking.cost_per_seat LIKE "%'.$sSearch.'%")');
                }

                $iTotal = $ridesData->count();
                if (isset($iSortCol_0)) {
                    for ($i = 0; $i < intval($iSortingCols); $i++) {
                        $iSortCol = $request['iSortCol_' . $i];
                        $bSortable = $request['bSortable_' . intval($iSortCol)];
                        $sSortDir = $request['sSortDir_' . $i];

                        if ($bSortable == 'true') {
                            $ridesData = $ridesData->orderBy($aColumns[intval($iSortCol)], $sSortDir);
                        } else {
                            $ridesData = $ridesData->orderBy('rides.departure_date', 'desc');
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
                    $newrideData = array();
                    $message='<div class="panel panel-default" style="margin-bottom:10px">';
                    $message.='<div class="panel-heading"><h5>';
                    $message.='<span>'.$value->source.'</span>';
                    $message.='&nbsp;<i class="zmdi zmdi-arrow-right"></i>&nbsp;'; 
                    $message.='<span>'.$value->destination.'</span>';
                    $message.='</h5></div><div class="panel-body">';
                    $message.='<div class="col-md-6 col-sm-6"><div>';
                    $message.='<i class="zmdi zmdi-calendar-alt zmdi-hc-lg"></i>&nbsp;&nbsp;<span>'.date("l d F Y - h:i A",strtotime($value->departure_date)).'</span>';
                    $message.='</div><div class="margin-top-10">';

                    if($value->profile_pic=='default.png')
                    {
                        $path="/images/default.png";
                    }
                    else
                    {
                        $path="/images/profile/".$value->userId."/".$value->profile_pic;
                    }
                    $message.='<img src="'.$lpath.$path.'" height="50" width="50" style="border-radius:50%;border:1px solid #eee;box-shadow:0px 1px 1px 1px #ccc"/>&nbsp;&nbsp;'.ucwords($value->name);
                    $message.='</div>';
                    $message.='<div class="margin-top-10">';
                    $message.='<div><label class="control-label">Rating:</label></div>';
                    $message.='<div style="color:#999" class="stars1">';
                                                        
                    
                    if($value->rating==5)
                    {
                        $message.='<input type="radio" class="star1 star1-5" id="star-b5" value="5" checked disabled="disabled"/><label for="star-b5" class="star1 star1-5"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-5" id="star-b5" value="5" disabled="disabled"/><label for="star-b5" class="star1 star1-5"></label>';         
                    }
                    if($value->rating==4)
                    {
                        $message.='<input type="radio" class="star1 star1-4" id="star-b4" value="4" checked disabled="disabled"/><label for="star-b4" class="star1 star1-4"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-4" id="star-b4" value="4" disabled="disabled"/><label for="star-b4" class="star1 star1-4"></label>';            
                    }
                    if($value->rating==3)
                    {
                        $message.='<input type="radio" class="star1 star1-3" id="star-b3" value="3" checked disabled="disabled"/><label for="star-b3" class="star1 star1-3"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-3" id="star-b3" value="3" disabled="disabled"/><label for="star-b3" class="star1 star1-3"></label>';   
                    }
                    if($value->rating==2)
                    {
                        $message.='<input type="radio" class="star1 star1-2" id="star-b2" value="2" checked disabled="disabled"/><label for="star-b2" class="star1 star1-2"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-2" id="star-b2" value="2" disabled="disabled"/><label for="star-b2" class="star1 star1-2"></label>';
                    }                
                    if($value->rating==1)
                    {
                        $message.='<input type="radio" class="star1 star1-1" id="star-b1" value="1" checked disabled="disabled"/><label for="star-b1" class="star1 star1-1"></label>';    
                    }                    
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-1" id="star-b1" value="1" disabled="disabled"/><label for="star-b1" class="star1 star1-1"></label>';
                    }
                    $message.='</div></div></div>';
                    $message.='<div class="col-md-6 col-sm-6 text-right">';
                    $message.='<div>';
                    $message.='<span style="color:#777">'.$value->seats.' seats booked</span>';
                    $message.='</div>';
                    $message.='<div class="margin-top-10">';
                    $message.='<span style="color:red;font-size:16px">Earned '.(int)$value->cost_per_seat.' &#8377;</span>';
                    $message.='</div>';
                    $message.='</div>';
                    $message.='<div class="clearfix"></div>';
                    $message.='<hr/>';
                    $message.='<div class="col-md-6 col-sm-6">';
                    $message.='</div>';
                    $message.='<div class="col-md-6 col-sm-6 text-right">';
                    $message.='<span><a href="ridedetail/'.$value->departureCity.'_'.$value->arrivalCity.'_'.$value->rideId.'"><i class="zmdi zmdi-eye zmdi-hc-lg"></i> See ride offer  </a></span> ';
                    $message.='</div>';
                    $message.='</div>';
                    $message.='</div>';
                    $message.='<div class="clearfix"></div>';
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
            \Log::error('earntransaction function error'.$e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }
    //function for get wallet amount
    public function getAmount()
    {
        try
        {
            if(session('userId'))
            {
                $getAmount=DB::table('payment_wallete')->select('amount')->where('userId',session('userId'))->get();
                if(count($getAmount)>0)
                {
                    return $getAmount[0]->amount;
                }
                else
                {
                    return 0;
                }
            }
            else
            {
                return -1;
            }
        }
        catch(\Exception $e)
        {
            return 0;
        }
    }
    //function call for getting ride book details
    public function rideBookHistory()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $aColumns = array('rides.departure_date');
                $sTable = 'rides';
                $iDisplayStart = $request['iDisplayStart'];
                $iDisplayLength = $request['iDisplayLength'];
                $iSortCol_0 = $request['iSortCol_0'];
                $iSortingCols = $request['iSortingCols'];

                $sSearch = $request['sSearch'];
                $sEcho = $request['sEcho'];

                $ridesData=DB::table('ride_booking')->select('ride_booking.rideId','rides.offer_seat','rides.available_seat','rides.cost_per_seat','rides.departure_date','rides.return_date','rides.return_time','rides.departureOriginal','rides.arrivalOriginal',DB::raw('CONCAT(first_name," ",last_name) as name'),'rating','profile_pic','rides.departureCity','rides.arrivalCity','users.id as userID')
                        ->leftJoin('rides','ride_booking.rideId','=','rides.id')
                        ->leftJoin('users','ride_booking.offer_userId','=','users.id')
                        ->where('ride_booking.offer_userId','=',session('userId'))
                        ->where('ride_booking.is_deleted','=',0);

                $ridesDataCount = $ridesData->count();
                // dd($ridesData);
                if (!empty($sSearch)) {

                    $ridesData = $ridesData->whereRaw('(CONCAT(first_name," ",last_name) LIKE "%' . $sSearch . '%" or date_format(rides.departure_date,"%d-%m-%Y")  LIKE "%' . $sSearch . '%" or rides.departureOriginal LIKE "%'.$sSearch.'%" or rides.arrivalOriginal LIKE "%'.$sSearch.'%" or rides.offer_seat LIKE "%'.$sSearch.'%" or rides.available_seat LIKE "%'.$sSearch.'%"  or rides.cost_per_seat LIKE "%'.$sSearch.'%")');
                }

               
                $ridesData=$ridesData->groupBy('ride_booking.rideId');
                if (isset($iSortCol_0)) {
                    for ($i = 0; $i < intval($iSortingCols); $i++) {
                        $iSortCol = $request['iSortCol_' . $i];
                        $bSortable = $request['bSortable_' . intval($iSortCol)];
                        $sSortDir = $request['sSortDir_' . $i];

                        if ($bSortable == 'true') {
                            $ridesData = $ridesData->orderBy($aColumns[intval($iSortCol)], $sSortDir);
                        } else {
                            $ridesData = $ridesData->orderBy('rides.departure_date', 'desc');
                        }
                    }
                }
                $ridesDataCount = $ridesData->get();
                if (isset($iDisplayStart) && $iDisplayLength != '-1') {
                    $ridesData = $ridesData->skip($iDisplayStart)
                            ->take($iDisplayLength);
                }

                $ridesData = $ridesData->get();
                
                $iTotal = count($ridesData);
                $ridesDataCount=count($ridesDataCount);
                // Output
                $output = array(
                    'sEcho' => intval($sEcho),
                    'iTotalRecords' => intval($ridesDataCount),
                    'iTotalDisplayRecords' => intval($iTotal),
                    'aaData' => array()
                );
                $lpath=env('APP_LOCAL_URL');
                $i = $iDisplayStart + 1;
                foreach ($ridesData as $key => $value) {                
                    $newrideData = array();
                    $message='<div class="panel panel-default" style="margin-bottom:10px">';
                    $message.='<div class="panel-heading"><h5>';
                    $message.='<span>'.$value->departureOriginal.'</span>';
                    $message.='&nbsp;<i class="zmdi zmdi-arrow-right"></i>&nbsp;'; 
                    $message.='<span>'.$value->arrivalOriginal.'</span>';
                    $message.='</h5></div><div class="panel-body">';
                    $message.='<div class="col-md-6 col-sm-6"><div>';
                    $message.='<i class="zmdi zmdi-calendar-alt zmdi-hc-lg"></i>&nbsp;&nbsp;<span>'.date("l d F Y - h:i A",strtotime($value->departure_date)).'</span>';
                    $message.='</div><div class="margin-top-10">';
                    if($value->profile_pic=='default.png')
                    {
                        $path="/images/default.png";
                    }
                    else
                    {
                        $path="/images/profile/".$value->userID."/".$value->profile_pic;
                    }
                    $message.='<img src="'.$lpath.$path.'" height="50" width="50" style="border-radius:50%;border:1px solid #eee;box-shadow:0px 1px 1px 1px #ccc"/>&nbsp;&nbsp;'.ucwords($value->name);
                    $message.='</div>';
                    $message.='<div class="margin-top-10">';
                    $message.='<div><label>Rating:</label></div>';
                    $message.='<div style="color:#999" class="stars1">';
                                                        
                    
                    if($value->rating==5)
                    {
                        $message.='<input type="radio" class="star1 star1-5" id="star-b5" value="5" checked disabled="disabled"/><label for="star-b5" class="star1 star1-5"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-5" id="star-b5" value="5" disabled="disabled"/><label for="star-b5" class="star1 star1-5"></label>';         
                    }
                    if($value->rating==4)
                    {
                        $message.='<input type="radio" class="star1 star1-4" id="star-b4" value="4" checked disabled="disabled"/><label for="star-b4" class="star1 star1-4"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-4" id="star-b4" value="4" disabled="disabled"/><label for="star-b4" class="star1 star1-4"></label>';            
                    }
                    if($value->rating==3)
                    {
                        $message.='<input type="radio" class="star1 star1-3" id="star-b3" value="3" checked disabled="disabled"/><label for="star-b3" class="star1 star1-3"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-3" id="star-b3" value="3" disabled="disabled"/><label for="star-b3" class="star1 star1-3"></label>';   
                    }
                    if($value->rating==2)
                    {
                        $message.='<input type="radio" class="star1 star1-2" id="star-b2" value="2" checked disabled="disabled"/><label for="star-b2" class="star1 star1-2"></label>';
                    }
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-2" id="star-b2" value="2" disabled="disabled"/><label for="star-b2" class="star1 star1-2"></label>';
                    }                
                    if($value->rating==1)
                    {
                        $message.='<input type="radio" class="star1 star1-1" id="star-b1" value="1" checked disabled="disabled"/><label for="star-b1" class="star1 star1-1"></label>';    
                    }                    
                    else
                    {
                        $message.='<input type="radio" class="star1 star1-1" id="star-b1" value="1" disabled="disabled"/><label for="star-b1" class="star1 star1-1"></label>';
                    }
                    $message.='</div></div></div>';
                    $message.='<div class="col-md-6 col-sm-6 text-right">';
                    $message.='<div>';
                    $message.='<span style="color:#777">Offered Seats - '.$value->offer_seat.'</span>';
                    $message.='</div>';
                    $message.='<div>';
                    $message.='<span style="color:#777">Available Seats - '.$value->available_seat.'</span>';
                    $message.='</div>';
                    $message.='<div class="margin-top-10">';
                    $message.='<span style="color:red;font-size:16px">'.(int)$value->cost_per_seat.' &#8377; /seat</span>';
                    $message.='</div>';
                    $message.='</div>';
                    $message.='<div class="clearfix"></div>';
                    $message.='<hr/>';
                    $message.='<div class="col-md-6 col-sm-6">';
                    $message.='</div>';
                    $message.='<div class="col-md-6 col-sm-6 text-right">';
                    $message.='<span><a href="'.route('get.ride.offer',$value->rideId).'"><i class="zmdi zmdi-eye zmdi-hc-lg"></i> See ride offer  </a></span> ';
                    $message.='</div>';
                    $message.='</div>';
                    $message.='</div>';
                    $message.='<div class="clearfix"></div>';
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
            \Log::error('earntransaction function error'.$e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }
    //function for add coupan code
    public function addCoupan()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $param=$this->request->all();
                $date=date("Y-m-d");
                if(isset($param['code']))
                {
                    if($param['code']=="")
                    {
                        //error
                        $errors[]="Coupancode is required";
                        $response['message'] = "Coupancode is required";
                        $response['errormessage'] = $errors;
                        $response['status'] = false;
                        $response['data'] = array();
                        return response($response,401);
                    }
                    else
                    {
                        $findcoupan=DB::table('coupan_code')
                                ->where('coupan_code','LIKE',$param['code'])
                                ->where('is_deleted',0)
                                ->get();
                                
                        if(count($findcoupan)>0)
                        {
                            //check weather coupan is expired or not
                            if($findcoupan[0]->start_date<=$date && $findcoupan[0]->end_date>=$date)
                            {
                                //check if user has already avail this coupan code or not
                                $checkUserCoupan=DB::table('user_coupan_code')->where('userId',session('userId'))->where('coupanId',$findcoupan[0]->id)->get();
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
                                    $insert_user_coupan=array("userId"=>session('userId'),"coupanId"=>$findcoupan[0]->id,"amount"=>$findcoupan[0]->amount);
                                    $insert=DB::table('user_coupan_code')->insert($insert_user_coupan);
                                    if($insert)
                                    {
                                        $selectUserWallet=DB::table('payment_wallete')->where('userId',session('userId'))->get();
                                        if(count($selectUserWallet)>0)
                                        {
                                            $newAmount=$findcoupan[0]->amount+$selectUserWallet[0]->amount;
                                            $updateAmount=DB::table('payment_wallete')->where('userId',session('userId'))->update(['amount'=>$newAmount]);
                                            if($updateAmount)
                                            {
                                                DB::commit();
                                                $response['data'] = array("amount"=>$newAmount);
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
                                            $insertAmount=DB::table('payment_wallete')->insert(['userId'=>session('userId'),'amount'=>$findcoupan[0]->amount]);    
                                            if($insertAmount)
                                            {
                                                DB::commit();
                                                $response['data'] = array("amount"=>$findcoupan[0]->amount);
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
                else
                {
                    //error
                    $errors[]="Coupancode is required";
                    $response['message'] = "Coupancode is required";
                    $response['errormessage'] = $errors;
                    $response['status'] = false;
                    $response['data'] = array();
                    return response($response,401);
                }
            }
            else
            {
                $errors[]="Please try again";
                $response['message'] = "Please try again";
                $response['errormessage'] = $errors;
                $response['status'] = false;
                $response['data'] = array();
                return response($response,401);    
            }
        }
        catch(\Exception $e)
        {
            \Log::error('addCoupan function error'.$e->getMessage());
            $errors[]="Please try again";
            $response['message'] = "Please try again";
            $response['errormessage'] = $errors;
            $response['status'] = false;
            $response['data'] = array();
            return response($response,401);
        }
    }
}
