<?php
namespace App\Repositories;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Validator; 
use DB;
use Mail;
use Response;
class rideRepository
{
    protected $request,$ip;
	public function __construct(Request $request)
	{
		$this->request=$request;
        $this->ip=$request->ip();
        //return redirect()->back()->withErrors(["error"=>"Could not add details! Please try again."]);
	}
	public function rideSearch()
	{
        try
		{
            if(\Session::has('userId'))
            {

                $request=$this->request->all();     
    			$fromplace=$request['fromplace'];//Shivranjani
    			$fromcity=$request['fromcity'];//Ahmedabad
    			$toplace=$request['toplace'];// Silvassa
    			$tocity=$request['tocity'];// Silvassa
    			$date=$request['fromdate'];//10-04-2016
                //set cookie


    			$messages=[
    				'fromplace.required'=>'Enter Correct From City..',
    				'fromcity.required'=>'Enter Correct From City..',
                    'toplace.required'=>'Enter Correct To City..',
                    'tocity.required'=>'Enter Correct To City..',
                    'fromdate.required'=>'Date Required..',
                    'fromdate.date'=>'Enter Correct Date..'
    			];
    			$validator=Validator::make($request,[
    				'fromplace'=>'required',
    				'fromcity'=>'required',
                    'toplace'=>'required',
                    'tocity'=>'required',
                    'fromdate'=>'required|date',
    				],$messages);
    			if($validator->fails())
    			{
            		return redirect()->back()->withErrors($validator)->withInput();
    			}
    			else
    		    {
                    $today_date=date("Y-m-d");
                    $searchDate=date("Y-m-d",strtotime($date));
                    if($searchDate<$today_date)
                    {
                        return redirect()->back()->withErrors(["error"=>"Date can not be less than current Date.."])->withInput();
                    }
                    $data['from']=$request['from'];
                    $data['to']=$request['to'];
                    $data['fromcity']=$fromcity;
                    $data['fromplace']=$fromplace;
    			    $data['tocity']=$tocity;
                    $data['toplace']=$toplace; 
                    $data['date']=$date;

                    /*if (isset($_SERVER['HTTP_COOKIE']))
                    {
                        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                        
                        foreach($cookies as $cookie)
                        {
                            $mainCookies = explode('=', $cookie);
                            $name = trim($mainCookies[0]);
                            unset($_COOKIE[$name]);
                            //setcookie($name, '', time()-1000);
                            //setcookie($name, '', time()-1000, '/');
                        }
                    }*/
                    /*if(isset($_COOKIE['fromplace']))
                            setcookie("fromplace",'', time()-1000,'/','www.smw.com');

                    if(isset($_COOKIE['fromcity']))
                        setcookie("fromcity",'', time()-1000,'/','www.smw.com');
                        

                    if(isset($_COOKIE['toplace']))
                        setcookie("toplace",'', time()-1000,'/','www.smw.com');

                    if(isset($_COOKIE['tocity']))
                        setcookie("tocity",'', time()-1000,'/','www.smw.com');

                    if(isset($_COOKIE['fromoriginal']))
                        setcookie("fromoriginal",'', time()-1000,'/','www.smw.com');
                        
                    if(isset($_COOKIE['tooriginal']))
                        setcookie("tooriginal",'', time()-1000,'/','www.smw.com');

                    if(isset($_COOKIE['ridedate']))
                        setcookie("ridedate",'', time()-1000,'/','www.smw.com');*/
                    
                    /*setcookie("fromplace", $fromplace, time()+3600,'/','www.smw.com');
                    setcookie("fromcity", $fromcity, time()+3600,'/','www.smw.com');
                    setcookie("toplace", $toplace, time()+3600,'/','www.smw.com');
                    setcookie("tocity", $tocity, time()+3600,'/','www.smw.com');
                    setcookie("fromoriginal",$request['from'],time()+3600,'/','www.smw.com');
                    setcookie("tooriginal",$request['to'],time()+3600,'/','www.smw.com');
                    setcookie("ridedate", $date, time()+3600,'/','www.smw.com');

                    if(isset($request['fromlat']))
                    {
                        setcookie("fromlat",$request['fromlat'],time()+3600,'/','www.smw.com');
                    }
                    if(isset($request['fromlng']))
                    {
                        setcookie("fromlng",$request['fromlng'],time()+3600,'/','www.smw.com');
                    }
                    if(isset($request['tolat']))
                    {
                        setcookie("tolat",$request['tolat'],time()+3600,'/','www.smw.com');
                    }
                    if(isset($request['tolng']))
                    {
                        setcookie("tolng",$request['tolng'],time()+3600,'/','www.smw.com');
                    }*/
                    
                    //select car comfort
                    $car_comfort=DB::table('comfort_master')->where('is_deleted',0)->get();

                    //$ridelist=$this->RideList($data);
                    return view('rideList',['ride'=>$data,'comfort'=>$car_comfort]);
    			}
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
    
    public function rideListSearch()
    {
        
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
            
                $fromplace=$request['fromplace'];
                $fromcity=$request['fromcity'];
                $toplace=$request['toplace'];
                $tocity=$request['tocity'];
                $date=date("d-m-Y");

                $messages=[
                    'fromplace.required'=>'Enter Correct From City..',
                    'fromcity.required'=>'Enter Correct From City..'
                ];
                $validator=Validator::make($request,[
                    'fromplace'=>'required',
                    'fromcity'=>'required', 
                    ],$messages);
                if($validator->fails())
                {
                    return redirect('/ridesearch')->withErrors($validator)->withInput();
                }
                else
                {
                    $data['from']=$request['from'];
                    $data['to']=$request['to'];
                    $data['fromcity']=$fromcity;
                    $data['fromplace']=$fromplace;
                    $data['tocity']=$tocity;
                    $data['toplace']=$toplace; 
                    $data['date']=$date;
                    $ridelist=$this->RideList($data);
                    return view('rideList',['ride'=>$data,'ridelist'=>$ridelist]);
                }
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
	public function RideList($data)
	{
		$userId=session('userId');
        $departPlace=$data['fromplace'];
        $city="";
        $departurecity=$data['fromcity'];
        $arrivalcity=$data['tocity'];
        $arrivalPlace=$data['toplace'];
        $date=date("Y-m-d",strtotime($data['date']));
        $ladies_only=0;
        $twowheeler="";     
        $fourwheeler=0;     
        $isdaily=0; 

        $today=date("Y-m-d H:i:s");    
    
        $rideQuery=DB::table('rides')
                    ->leftjoin('ride_via_points', 'rides.id', '=', 'ride_via_points.rideId')
                    ->leftjoin('car_details','rides.carId', '=', 'car_details.id')
                    ->leftjoin('users','rides.userId','=','users.id')
                    ->where('rides.userId','<>',$userId)
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
            $rideQuery=$rideQuery->whereRaw('(( departurecity = "'.strtolower(trim($departurecity)).'" and departure="'.$departPlace.'")'
                                             .' or ( is_round_trip=1 and arrivalCity="'.strtolower(trim($departurecity)).'" and arrival="'.$departPlace.'"))'
                                            );
                     
                    
        }
        
        if(!empty($arrivalcity)) 
        {
            $rideQuery=$rideQuery->whereRaw('('
                                                .'(('
                                                 .'arrivalCity = "'.strtolower(trim($arrivalcity))
                                                 .'" or ride_via_points.cityName = "'.strtolower(trim($arrivalcity)).'"' 
                                                .') and (arrival="'.$arrivalPlace.'" or ride_via_points.city="'.$arrivalPlace.'"))  or '
                                                 .'(is_round_trip=1 and  (departurecity="'.strtolower(trim($arrivalcity)).'"'
                                                  . ' or  ride_via_points.cityName = "'.strtolower(trim($arrivalcity)).'"'
                                                    .') or (departure="'.$arrivalPlace.'" or ride_via_points.city="'.$arrivalPlace.'"))'   
                                            .')');
        }
        
        if(!empty($ladies_only) && $ladies_only==1) 
        {
            $rideQuery=$rideQuery->where('rides.ladies_only','=',1);
        }      

        $rideQuery=$rideQuery->get(['rides.userId',
                                    'rides.departure as source',
                                    'rides.departureCity as sourcename',
                                    'rides.arrival as destination',
                                    'rides.arrivalCity as destinationname',
                                    'rides.offer_seat',
                                    'rides.available_seat',
                                    'rides.cost_per_seat',
                                    'rides.departure_date',
                                    'rides.return_date',
                                    'rides.is_round_trip',
                                    'rides.isDaily',
                                    'rides.ladies_only',
                                    'rides.view_count as view_count',
                                    'rides.licence_verified',
                                    'rides.comment',
                                    'users.rating',
                                    'users.first_name',
                                    'users.last_name',
                                    'users.profile_pic',
                                    'rides.created_date',
                                    'rides.id as rideid',
                                    'car_details.car_model',
                                    'car_details.car_make',
                                    'rides.carId'
                                    ]);
       
        $temprespo = array();
        $i=0;
        foreach($rideQuery as $rideKey => $rideval)
        {
            $tempdata=array();
            foreach($rideval as $key=>$value)
            {
                if($key=='departure_date' || $key=='is_round_trip')
                {
                    $tempdata[$key]=date("l d F - H:i",strtotime($value));
                }
                else
                {
                    $tempdata[$key]=$value;    
                }
                
            }    
            $temprespo[$i]['ridedetails']=$tempdata;      
            $i++;
        }
        return $temprespo;
	}
    //when ride list page loads this function will call
    public function carList()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                /*if(isset($_COOKIE["fromplace"]))
                {   
                    if (isset($_SERVER['HTTP_COOKIE'])) {
                        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                        foreach($cookies as $cookie) {
                            $parts = explode('=', $cookie);
                            $name = trim($parts[0]);
                            unset($_COOKIE[$name]);
                        }
                    }
                    echo $_COOKIE['fromplace'];
                    exit;
                }
                else
                {
                    echo "hello";
                }
                exit;*/
                $aColumns = array('rides.id');
                $sTable = 'rides';


                $userId=session('userId');
                $departPlace=$request['fromplace'];
                $departOrigin=$request['fromorigin'];
                $city="";
                $departurecity=$request['fromcity'];
                $arrivalcity=$request['tocity'];
                $arrivalPlace=$request['toplace'];
                $arrivalOrigin=$request['toorigin'];
                $photo="";
                $flag=0;
                if($request['date']!="")
                {
                    $date=date("Y-m-d",strtotime($request['date']));  
                    $current_date=date("Y-m-d");
                    if($current_date>$date)
                    {
                        $flag=1;
                        
                    }  
                    else
                    {
                        $flag=0;
                    }
                }
                else
                {
                    $date=date("Y-m-d");
                    $flag=0;
                }
               
                if($flag==1)
                {
                    $ridesData=array();
                    $sEcho = $request['sEcho'];
                    $iTotal=0;
                    $iDisplayStart = $request['iDisplayStart'];
                    $ridesDataCount=0;
                }
                else
                {
                        //unset all cookies
                        /*if (isset($_SERVER['HTTP_COOKIE']))
                        {
                            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                            print(arg)_r($cookies);
                            exit;                   
                            foreach($cookies as $cookie)
                            {
                                $mainCookies = explode('=', $cookie);
                                $name = trim($mainCookies[0]);
                                unset($_COOKIE[$name]);
                                //setcookie($name, '', time()-1000);
                                //setcookie($name, '', time()-1000, '/');
                            }
                        }*/
                        /*if(isset($_COOKIE['fromplace']))
                            setcookie("fromplace",'', time()-1000,'/','www.smw.com');

                        if(isset($_COOKIE['fromcity']))
                            setcookie("fromcity",'', time()-1000,'/','www.smw.com');
                            

                        if(isset($_COOKIE['toplace']))
                            setcookie("toplace",'', time()-1000,'/','www.smw.com');

                        if(isset($_COOKIE['tocity']))
                            setcookie("tocity",'', time()-1000,'/','www.smw.com');

                        if(isset($_COOKIE['fromoriginal']))
                            setcookie("fromoriginal",'', time()-1000,'/','www.smw.com');
                            
                        if(isset($_COOKIE['tooriginal']))
                            setcookie("tooriginal",'', time()-1000,'/','www.smw.com');

                        if(isset($_COOKIE['ridedate']))
                            setcookie("ridedate",'', time()-1000,'/','www.smw.com');*/
                        //set cookie

                        setcookie("fromplace", $departPlace, time()+3600,'/');
                        setcookie("fromcity", $departurecity, time()+3600,'/');
                        setcookie("toplace", $arrivalPlace, time()+3600,'/');
                        setcookie("tocity", $arrivalcity, time()+3600,'/');
                        setcookie("fromoriginal",$departOrigin,time()+3600,'/');
                        setcookie("tooriginal",$arrivalOrigin,time()+3600,'/');
                        setcookie("ridedate", date("d-m-Y",strtotime($date)), time()+3600,'/');

                        $rideType=$request['ridetype'];
                        if($rideType=="ladies")
                        {
                            $ladies_only=1;    
                            $isdaily=0; 
                        }
                        else if($rideType=="daily")
                        {
                            $isdaily=1;
                            $ladies_only=0;
                        }
                        else
                        {
                            $isdaily=0;
                            $ladies_only=0;
                        }

                        $vehicle_type=$request['vehicle_type'];
                        if($vehicle_type==2)
                        {
                            $twowheeler=0;
                            $fourwheeler=1;
                        }
                        else if($vehicle_type==1)
                        {
                            $twowheeler=1;
                            $fourwheeler=0;
                        }
                        else
                        {
                            $twowheeler=0;
                            $fourwheeler=0;
                        }
                        if(isset($request['comfort']))
                        {
                            $comfort=$request['comfort'];    
                        }
                        else
                        {
                            $comfort=array();
                        }


                        $iDisplayStart = $request['iDisplayStart'];
                        $iDisplayLength = $request['iDisplayLength'];
                        $iSortCol_0 = $request['iSortCol_0'];
                        $iSortingCols = $request['iSortingCols'];

                        $sSearch = $request['sSearch'];
                        $sEcho = $request['sEcho'];
                        //DB::enableQueryLog();
                        /*----------------------------------*/
                        $ridesData=DB::table('rides')->select('rides.userId',
                                                'rides.departure as source',
                                                'rides.departureCity as sourcename',
                                                'rides.departureOriginal',
                                                'rides.arrival as destination',
                                                'rides.arrivalCity as destinationname',
                                                'rides.arrivalOriginal',
                                                'rides.offer_seat',
                                                'rides.available_seat',
                                                'rides.cost_per_seat',
                                                'rides.departure_date',
                                                'rides.return_date',
                                                'rides.is_round_trip',
                                                'rides.isDaily',
                                                'rides.ladies_only',
                                                'rides.view_count as view_count',
                                                'rides.licence_verified',
                                                'rides.comment',
                                                'users.rating',
                                                'users.first_name',
                                                'users.last_name',
                                                'users.profile_pic',
                                                'rides.created_date',
                                                'rides.id as rideid',
                                                'car_details.car_model',
                                                'car_details.car_make',
                                                'rides.carId',
                                                'rides.id as rideId',
                                                'users.birthdate')
                                ->leftjoin('ride_via_points', 'rides.id', '=', 'ride_via_points.rideId')
                                ->leftjoin('car_details','rides.carId', '=', 'car_details.id')
                                ->leftjoin('users','rides.userId','=','users.id')
                                // ->where('rides.userId','<>',$userId)
                                ->where('rides.status','=',0)
                                /*->whereRaw('(departure_date >="'.$date.
                                             '" or (isDaily=1'. 
                                             ' and departure_date >="'.$date. 
                                             '" and return_date <="'.$date.'" ))'.
                                             '');*/
                                ->whereRaw('((is_round_trip=0 and isDaily=0 and departure_date >="'.$date.'") or (is_round_trip=1 and isDaily=0 and (departure_date>="'.$date.'" or return_date>="'.$date.'")) or (isDaily=1 and is_round_trip=1 and (date(departure_date)<="'.$date.'" or date(departure_date)>="'.$date.'")) or (is_round_trip=0 and isDaily=1 and (date(departure_date)<="'.$date.'" or (date(departure_date)>="'.$date.'"))))');

                       

                        if (!empty($sSearch)) {

                            $ridesData = $ridesData->whereRaw('(CONCAT(users.first_name, " ", users.last_name) LIKE "%' . $sSearch . '%" 
                                                            or rides.departure_date LIKE "%' . $sSearch . '%"
                                                            or rides.departure LIKE "%' . $sSearch . '%"
                                                            or rides.departureCity LIKE "%' . $sSearch . '%"
                                                            or rides.arrival LIKE "%' . $sSearch . '%"
                                                            or rides.arrivalCity LIKE "%' . $sSearch . '%"
                                                            or rides.available_seat LIKE "%' . $sSearch . '%"
                                                            or rides.cost_per_seat LIKE "%' . $sSearch . '%"
                                                            )');
                        }

                        if(!empty($city) && empty($departurecity)) 
                        {
                            $ridesData=$ridesData->whereRaw('(is_round_trip=0 and departureCity = "'.strtolower(trim($city)).'" or (is_round_trip=1 and (departureCity="'.$city.'" or arrivalCity="'.strtolower(trim($city)).'")))');
                                    
                        }
                        
                        if(count($comfort)>0)
                        {
                            $ridesData = $ridesData->whereIn('comfortId',$comfort);
                        }

                        if(!empty($isdaily)) 
                        {
                            $ridesData=$ridesData->where('rides.isDaily','=',$isdaily);
                        }
                    
                    
                    
                        if(!empty($twowheeler) && $twowheeler==1) 
                        {
                            $ridesData=$ridesData->where('car_details.car_type','=',1);
                        }
                    
                        if(!empty($fourwheeler) && $fourwheeler==1) 
                        {
                            $ridesData=$ridesData->where('car_details.car_type','=',2);
                        }
                    
                    
                        if(!empty($departurecity) && empty($arrivalcity)) 
                        {
                            $ridesData=$ridesData->whereRaw('((is_round_trip=0 and departureCity = "'.strtolower(trim($departurecity)).'" and departure="'.$departPlace.'") or (is_round_trip=1 and ((departureCity="'.strtolower(trim($departurecity)).'" and departure="'.$departPlace.'") or (arrivalCity="'.strtolower(trim($departurecity)).'" and arrival="'.$departPlace.'"))))');
                        }
                    
                        if(!empty($departurecity) && !empty($arrivalcity)) 
                        {

                            $ridesData=$ridesData->whereRaw('((is_round_trip=0 and departureCity = "'.strtolower(trim($departurecity)).'" and departure="'.$departPlace.'" and ((arrivalCity="'.strtolower(trim($arrivalcity)).'" or ride_via_points.cityName="'.strtolower(trim($arrivalcity)).'") and (arrival="'.$arrivalPlace.'" or ride_via_points.city="'.$arrivalPlace.'"))) or (is_round_trip=1 and ((departureCity="'.strtolower(trim($departurecity)).'" and departure="'.$departPlace.'" and ((arrivalCity="'.strtolower(trim($arrivalcity)).'" or ride_via_points.cityName="'.strtolower(trim($arrivalcity)).'") and (arrival="'.$arrivalPlace.'" or ride_via_points.city="'.$arrivalPlace.'"))) or ((departureCity="'.strtolower(trim($arrivalcity)).'" or ride_via_points.cityName="'.strtolower(trim($arrivalcity)).'") and (departure="'.$arrivalPlace.'" or ride_via_points.city="'.$arrivalPlace.'")) and (arrivalCity="'.strtolower(trim($departurecity)).'" and arrival="'.$departPlace.'"))))');

                            //$ridesData=$ridesData->whereRaw('(((arrivalCity = "'.strtolower(trim($arrivalcity)).'" or ride_via_points.cityName = "'.strtolower(trim($arrivalcity)).'") and (arrival="'.$arrivalPlace.'" or ride_via_points.city="'.$arrivalPlace.'"))  or (is_round_trip=1 and  (departurecity="'.strtolower(trim($arrivalcity)).'" or  ride_via_points.cityName = "'.strtolower(trim($arrivalcity)).'") or (departure="'.$arrivalPlace.'" or ride_via_points.city="'.$arrivalPlace.'")))');
                        }
                    
                        if(!empty($ladies_only) && $ladies_only==1) 
                        {
                            $ridesData=$ridesData->where('rides.ladies_only','=',1);
                        }      

                        if(isset($request['photo']))
                        {
                            if($request['photo']=='photo')
                            {
                               $ridesData=$ridesData->where('users.profile_pic','!=','default.png'); 
                            }
                        }
                        
                        //dd($ridesData->toSql());
                        $ridesData=$ridesData->groupBy('rides.id');
                        
                        
                        
                        if (isset($iSortCol_0)) {
                            for ($i = 0; $i < intval($iSortingCols); $i++) {
                                $iSortCol = $request['iSortCol_' . $i];
                                $bSortable = $request['bSortable_' . intval($iSortCol)];
                                $sSortDir = $request['sSortDir_' . $i];

                                if ($bSortable == 'true') {
                                    $ridesData = $ridesData->orderBy($aColumns[intval($iSortCol)], $sSortDir);
                                } else {
                                    $ridesData = $ridesData->orderBy('rides.departure_date', 'asc');
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
    //                    dd(
      //          DB::getQueryLog()
        //    );
                        //
                    }
               


                // Output
                $output = array(
                    'sEcho' => intval($sEcho),
                    'iTotalRecords' => intval($ridesDataCount),
                    'iTotalDisplayRecords' => intval($ridesDataCount),
                    'aaData' => array()
                );
                $lpath=env('APP_LOCAL_URL');
                //-----------------------------------
                $i = $iDisplayStart + 1;
                foreach ($ridesData as $key => $value) {
                    // dd($value);
                    
                    $newrideData = array();
                    $message='<a href="ridedetail/'.$value->sourcename.'_'.$value->destinationname.'_'.$value->rideId.'" target="_blank">';
                    $message.='<div style="overflow:hidden"><div class="col-md-4 no-padding margin-top-5">';
                    if($value->profile_pic=='default.png')
                    {
                        $path="/images/default.png";
                    }
                    else
                    {
                        $path="/images/profile/".$value->userId."/".$value->profile_pic;
                    }
                    
                    $message.='<img src="'.$lpath.$path.'" style="width:70px;height:70px;border-radius:46%">';
                    
                    $message.='</div>';
                    $message.='<div class="col-md-8 no-padding rideListName margin-top-5">';
                    $message.='<h3>'.ucwords($value->first_name." ".$value->last_name).'</h3>';
                    if($value->birthdate==""){
                        $message.='<h6>-</h6>';    
                    }
                    else
                    {
                        $year=date("Y")-$value->birthdate;
                        $message.='<h6>'.$year.' years old</h6>';    
                    }

                    $message.='<div class="stars1" style="padding:0px;margin:0px">';
                   
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
                    $message.='<div class="clearfix"></div></div>';

                    $message.='</div></div></a>';   
                    $newrideData[] = $message; 

                    $message1='<a href="ridedetail/'.$value->sourcename.'_'.$value->destinationname.'_'.$value->rideId.'" target="_blank">';
                    $message1.='<div style="overflow:hidden"><div class="col-md-12">';
                    $message1.='<h3><b><span>'.date("l d F Y - H:i",strtotime($value->departure_date)).'</span></b></h3>';
                    $message1.='<h5>';
                    $message1.='<span>'.$value->sourcename.'</span>';
                    $message1.='<i class="zmdi zmdi-long-arrow-right"></i>';
                    $message1.='<span>'.$value->destinationname.'</span>';
                    $message1.='</h5>';
                    $message1.='<div>';
                    $message1.='<i class="zmdi zmdi-pin zmdi-hc-lg zmdi-green"></i>';
                    $message1.='<span> '.$value->departureOriginal.'</span>';
                    $message1.='</div>';
                    $message1.='<div class="margin-top-5">';
                    $message1.='<i class="zmdi zmdi-pin zmdi-hc-lg zmdi-red"></i>';
                    $message1.='<span> '.$value->arrivalOriginal.'</span>';
                    $message1.='</div>';
                    $message1.='</div></div></a>';
                    $newrideData[]=$message1;

                    $message2='<a href="ridedetail/'.$value->sourcename.'_'.$value->destinationname.'_'.$value->rideId.'" target="_blank">';
                    $message2.='<div class="col-md-12">';
                    $message2.='<h4 class="MT0"><span class="price">&#8377;&nbsp;'.(int)$value->cost_per_seat.'</span></h4>';
                    $message2.='<div>';
                    $message2.='<span>per co-traveller</span>';
                    $message2.='</div>';
                    $message2.='<h3 class="margin-top-10">';
                    $message2.='<span>'.$value->available_seat.'</span><small class="smallnocolor"> Seats Left</small>';
                    $message2.='</h3>';
                    $message2.='</div></a>';
                    $newrideData[]=$message2;
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
            \Log::error('carList function error'.$e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }
    //this function is for getting ride details
    public function getRideDetail($id)
    {
        try
        {
            if(\Session::has('userId'))
            {
                $str=explode("_",$id);
                $departurecity=strtolower(trim($str[0]));
                $arrivalcity=strtolower(trim($str[1]));
                $id=(int)$str[2];
                if($departurecity=="" || $arrivalcity=="")
                    return view('errors.404');
               
                $findride=DB::table('rides')->where('departureCity',$departurecity)->where('arrivalCity',$arrivalcity)->where('id',$id)->get();
                
                if(count($findride)==0)
                    return view('errors.404');

                DB::table('rides')->where('rides.id',$id)->increment('view_count');
                $rideDetails=DB::table('rides')->select('rides.id as rideId','departure','departureCity','arrival','arrivalCity','offer_seat','available_seat','cost_per_seat','departure_date','return_date','return_time','is_round_trip','isDaily','ladies_only','view_count','comment','users.isverifyemail','users.id as userId','users.first_name','users.last_name','users.gender','users.isverifyphone','rating','description','profile_pic','created_at','luggage.name as luggage','leave_on.name as leave','detour.name as detour','rides.created_date as offerDate','users.created_at as userDate','users.birthdate','rides.departureOriginal','rides.arrivalOriginal','car_make','car_model','vehical_pic','color.color','comfort_master.name as comfort')
                            ->leftjoin('car_details','rides.carId','=','car_details.id')
                            ->leftjoin('color','car_details.colorId','=','color.id')
                            ->leftjoin('comfort_master','car_details.comfortId','=','comfort_master.id')
                            ->leftjoin('users', 'rides.userId', '=', 'users.id')
                            ->leftjoin('luggage', 'rides.luggage_size', '=', 'luggage.id')
                            ->leftjoin('leave_on', 'rides.leave_on', '=', 'leave_on.id')
                            ->leftjoin('detour', 'rides.can_detour', '=', 'detour.id')
                            ->where('rides.status',0)
                            ->where('rides.id',$id)->get();
                            //->where('rides.userId','<>',session('userId'))->get();

                if(count($rideDetails)>0)
                {
                    $waypoint=DB::table('ride_via_points')->select('cityOriginal')->where('rideId',$id)->get();

                    $ridePreference=DB::table('user_ride_preferences')->select('user_ride_preferences.preferenceId','user_ride_preferences.pref_optionId','preferences.preferences','preferences_option.options')
                        ->leftjoin('preferences_option','user_ride_preferences.pref_optionId','=','preferences_option.id')
                        ->leftjoin('preferences','user_ride_preferences.preferenceId','=','preferences.id')
                        ->where('user_ride_preferences.rideId',$id)
                        ->where('user_ride_preferences.userId',$rideDetails[0]->userId)
                        ->orderBy('user_ride_preferences.preferenceId','asc')
                        ->get();

                    
                    $totalRide=DB::table('rides')->where('userId',$rideDetails[0]->userId)->where('status',0)->count();
                    $login=DB::table('loginLog')->where('users_id',$rideDetails[0]->userId)->orderBy('id','desc')->take(1)->get();
                    if(count($login)>0)
                    {
                        $loginDate=date("l d F Y",strtotime($login[0]->created_at));
                    }
                    else
                    {
                        $loginDate='00-00-0000';
                    }
                }
                else
                {
                    return view('errors.404');
                    $totalRide=0;
                    $loginDate='00-00-0000';
                }
                return view('rideDetails',['rideDetail'=>$rideDetails,'totalRide'=>$totalRide,'loginDate'=>$loginDate,'ridePreference'=>$ridePreference,'waypoint'=>$waypoint]);
            }
            else
            {
                return redirect('/logout');
            }     
        }
        catch(\Exception $e)
        {
            \Log::error('carList function error'.$e->getMessage());
            return redirect('/ridelist');
        }
    }
   //function call when click on url of ridelist
    public function getRideList()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $data=array();
                $car_comfort=DB::table('comfort_master')->where('is_deleted',0)->get();
                if(isset($_COOKIE['fromoriginal']))
                {
                    $data['from']=$_COOKIE['fromoriginal'];    
                }
                else
                {
                    $data['from']="";
                }

                if(isset($_COOKIE['tooriginal']))
                {
                    $data['to']=$_COOKIE['tooriginal'];
                }
                else
                {
                    $data['to']="";
                }    
                if(isset($_COOKIE['fromcity']))
                {
                    $data['fromcity']=$_COOKIE['fromcity'];
                }
                else
                {
                    $data['fromcity']="";
                }
                if(isset($_COOKIE['fromplace']))
                {
                    $data['fromplace']=$_COOKIE['fromplace'];
                }
                else
                {
                    $data['fromplace']="";
                }
                if(isset($_COOKIE['tocity']))
                {
                    $data['tocity']=$_COOKIE['tocity'];
                }    
                else
                {
                    $data['tocity']="";
                }    
                if(isset($_COOKIE['toplace']))
                {
                    $data['toplace']=$_COOKIE['toplace']; 
                }
                else
                {
                    $data['toplace']="";
                }    
                if(isset($_COOKIE['ridedate']))
                {
                    $data['date']=$_COOKIE['ridedate'];
                }
                else
                {
                    $data['date']=date("d-m-Y");
                }    
                return view('rideList',['ride'=>$data,'comfort'=>$car_comfort]);
            }
            else
            {
                return redirect('/logout');
            }
        }
        catch(\Exception $e)
        {
            \Log::error('getRideList function error'.$e->getMessage());
            return redirect('/home');
        }
    }
    //function is for get ride offer details
    public function getRideOffer($id)
    {
        try
        {
            if(\Session::has('userId'))
            {
                $id=(int)$id;

                $findride=DB::table('rides')->where('id',$id)->where('userId',session('userId'))->get();

                if(count($findride)==0)
                    return view('errors.404');

                DB::table('rides')->where('rides.id',$id)->increment('view_count');

                
                $rideDetails=DB::table('rides')->select('rides.id as rideId','departure','departureCity','arrival','arrivalCity','offer_seat','available_seat','cost_per_seat','departure_date','return_date','return_time','is_round_trip','isDaily','ladies_only','view_count','comment','users.isverifyemail','users.id as userId','users.first_name','users.last_name','users.gender','users.isverifyphone','rating','description','profile_pic','created_at','luggage.name as luggage','leave_on.name as leave','detour.name as detour','rides.created_date as offerDate','users.created_at as userDate','users.birthdate','rides.departureOriginal','rides.arrivalOriginal','car_make','car_model','vehical_pic','color.color','comfort_master.name as comfort')
                            ->leftjoin('car_details','rides.carId','=','car_details.id')
                            ->leftjoin('color','car_details.colorId','=','color.id')
                            ->leftjoin('comfort_master','car_details.comfortId','=','comfort_master.id')
                            ->leftjoin('users', 'rides.userId', '=', 'users.id')
                            ->leftjoin('luggage', 'rides.luggage_size', '=', 'luggage.id')
                            ->leftjoin('leave_on', 'rides.leave_on', '=', 'leave_on.id')
                            ->leftjoin('detour', 'rides.can_detour', '=', 'detour.id')
                            ->where('rides.status',0)
                            ->where('rides.id',$id)->get();

                            //->where('rides.userId','<>',session('userId'))->get();
                if(count($rideDetails)>0)
                {
                    $waypoint=DB::table('ride_via_points')->select('cityOriginal')->where('rideId',$id)->get();
                   
                    $ridePreference=DB::table('user_ride_preferences')->select('user_ride_preferences.preferenceId','user_ride_preferences.pref_optionId','preferences.preferences','preferences_option.options')
                        ->leftjoin('preferences_option','user_ride_preferences.pref_optionId','=','preferences_option.id')
                        ->leftjoin('preferences','user_ride_preferences.preferenceId','=','preferences.id')
                        ->where('user_ride_preferences.rideId',$id)
                        ->where('user_ride_preferences.userId',$rideDetails[0]->userId)
                        ->orderBy('user_ride_preferences.preferenceId','asc')
                        ->get();
                    //booked ride user details
                    $bookedUserDetail=DB::table('ride_booking')->select('book_userId','users.first_name','users.last_name','birthdate','ride_booking.created_date','users.rating','no_of_seats','ride_booking.cost_per_seat','ride_booking.source','ride_booking.destination','profile_pic')
                        ->leftjoin('users','ride_booking.book_userId','=','users.id')
                        ->where('ride_booking.rideId',$id)
                        ->where('ride_booking.is_deleted',0)->get();
                    
                    $totalRide=DB::table('rides')->where('userId',$rideDetails[0]->userId)->where('status',0)->count();
                    $login=DB::table('loginLog')->where('users_id',$rideDetails[0]->userId)->orderBy('id','desc')->take(1)->get();
                    if(count($login)>0)
                    {
                        $loginDate=date("l d F Y",strtotime($login[0]->created_at));
                    }
                    else
                    {
                        $loginDate='00-00-0000';
                    }
                }
                else
                {
                    return view('errors.404');
                }

                return view('rideOfferOwner',['rideDetail'=>$rideDetails,'totalRide'=>$totalRide,'loginDate'=>$loginDate,'bookedUserDetail'=>$bookedUserDetail,'ridePreference'=>$ridePreference,'waypoint'=>$waypoint]);
            }
            else
            {
                return redirect('/logout');
            }
        }
        catch(\Exception $e)
        {
            \Log::error('getRideOffer function error'.$e->getMessage());
            return view('errors.404');
        }
    }
    //function for get ride details
    public function getRideInfo()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $param=$this->request->all();
                $rideid=$param['ride'];
                $rideinfo=DB::table('rides')->select('available_seat','departure_date','return_date','is_round_trip','isDaily','status','id','userId','cost_per_seat')
                    ->where('rides.id',$rideid)->get();

                $response['message']="Data fetch";
                $response['status']=true;
                $response['erromessage']=array();
                $response['data']=$rideinfo;
                return response($response,200);
            }
            else
            {
                \Log::error('getRideOffer function error'.$e->getMessage());
                $response['message'] = "Please try again";
                $response['status'] = false;
                $response['erromessage']=array();
                $response['data'] = array();
                return response($response,400);
            }
        }
        catch(\Exception $e)
        {
            \Log::error('getRideOffer function error'.$e->getMessage());
            $response['message'] = "Please try again";
            $response['status'] = false;
            $response['erromessage']=array();
            $response['data'] = array();
            return response($response,400);
        }
    }
    //function is for book ride
    public function bookRide()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $param=$this->request->all();
                $rideid=$param['ride'];
                $cost_seat=$param['cost_seat'];
                $seat=$param['No_seats'];
                $totalCost=$cost_seat*$seat;
                $tax=round(0.1*$totalCost);
                $totalFinal=$totalCost+$tax;
                $findride=DB::table('rides')->where('id',$rideid)->get();

                if(count($findride)>0)
                {
                    //remain select origin n destination according to ride type(daily,return date);
                    if($findride[0]->available_seat>=$seat)
                    {
                        $daily=0;
                        if($findride[0]->isDaily==1)
                        {
                            $cost_seat=0;
                            $totalCost=$cost_seat*$seat;
                            $tax=27.5;
                            $totalFinal=$totalCost+$tax;
                            $daily=1;
                        }
                        //check wallet amount enough in book rider account
                        $walletRecord=DB::table('payment_wallete')->where('userId',session('userId'))->get();
                        if(count($walletRecord)>0)
                        {
                            if($walletRecord[0]->amount>$totalFinal)
                            {
                                //you can book this ride 
                                //insert data in ride booking
                                DB::beginTransaction();
                                $insertData=array("offer_userId"=>$findride[0]->userId,"book_userId"=>session('userId'),"rideId"=>$rideid,"source"=>$findride[0]->departureOriginal,"destination"=>$findride[0]->arrivalOriginal,"no_of_seats"=>$seat,"cost_per_seat"=>$totalCost,"paymentType"=>"wallet");
                                $status=DB::table('ride_booking')->insert($insertData);
                                if($status>0)
                                {
                                    //tax amount add in admin account
                                    $insertAdminWallet=array("rideId"=>$rideid,"userId"=>session('userId'),"amount"=>$tax,"bookType"=>"book","isDaily"=>$daily);
                                    DB::table('admin_wallet')->insert($insertAdminWallet);

                                    //wallet amount transfer from rider account to offerrider account
                                    $rdd=DB::table('payment_wallete')->where('userId',session('userId'))->get();
                                    DB::table('payment_wallete')->where('userId',session('userId'))->update(['amount'=>$rdd[0]->amount-$totalFinal]);

                                    //check wallet amount exists for offer rider
                                    $che=DB::table('payment_wallete')->where('userId',$findride[0]->userId)->get();
                                    if(count($che)>0)
                                    {
                                        //update wallet amount... amount add in offer userid account
                                        DB::table('payment_wallete')->where('userId',$findride[0]->userId)->update(['amount'=>$che[0]->amount+$totalCost]);
                                    }
                                    else
                                    {
                                        //insert amount in offer userid account
                                        DB::table('payment_wallete')->insert(['userId'=>$findride[0]->userId,'amount'=>$totalCost]);
                                    }
                                    //minus available seat of a ride
                                    $st=DB::table('rides')->where('id',$rideid)->update(['available_seat'=>$findride[0]->available_seat-$seat]);
                                    if($st)
                                    {
                                        //send booked email
                                        //GET DETAILS OF OFFERED PERSON
                                        $offerPersonDetail=DB::table('users')->where('id',$findride[0]->userId)->get();
                                        //GET RIDE DETAILS
                                        //$offerRideDetail=DB::table('rides')->where('id',$bookRideArray['offerid'])->get();
                                        //GET DETAILS OF BOOKED PERSON
                                        $bookedPersonDetail=DB::table('users')->where('id',session('userId'))->get();

                                        if(count($offerPersonDetail)>0)
                                        {
                                            $dd['username']=$offerPersonDetail[0]->username;
                                            $dd['email']=$offerPersonDetail[0]->email;    

                                        }
                                        if(count($findride)>0)
                                        {
                                            $dd['date']=date("d-m-Y",strtotime($findride[0]->departure_date));
                                            $dd['time']=date("H:i:s",strtotime($findride[0]->departure_date));
                                            $dd['source']=$findride[0]->departureOriginal;
                                            $dd['destination']=$findride[0]->arrivalOriginal;
                                            $dd['seat']=$seat;
                                            if($findride[0]->isDaily==0)
                                            {
                                                $dd['amount']=$totalFinal;
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
                                        //ends
                                        //send default sms to user who has book this ride same as email
                                        $msg='Dear '.$dd['bookedname'].' ,<br>Your ride has been successfully booked.below are the offer details.<br/>';
                                        $msg.='Source :'.$dd['source'].'<br>';
                                        $msg.='Destination :'.$dd['destination'].'<br>';
                                        $msg.='Owner email :'.$dd['email'].'<br>';
                                        $msg.='Seats book :'.$dd['seat'].'<br>';
                                        $msg.='Time :'.$dd['time'].'<br>';
                                        $msg.='Date :'.$dd['date'].'<br><br>';
                                        $msg.='Enjoy your ride<br><br>';
                                        $msg.='Thank you';
                                        
                                        DB::table('user_chat_messages')->insert(['fromUserId'=>$findride[0]->userId,"toUserId"=>session('userId'),"message"=>$msg,"ip"=>$this->ip]);

                                        $this->request->session()->flash('status', 'Your ride has been booked successfully');
                                        DB::commit();
                                        $response['message']    =   "success";
                                        $response['status']     =   true;
                                        $response['erromessage']=   array();
                                        return response($response,200);       
                                    }
                                    else
                                    {
                                        //error
                                        DB::rollBack();
                                        $response['data'] = array();
                                        $response['message'] = "Please try again";
                                        $response['erromessage']=array();
                                        $response['status'] = false;
                                        return response($response,400);
                                    }
                                }
                                else
                                {
                                    //error
                                    DB::rollBack();
                                    $response['data'] = array();
                                    $response['message'] = "Please try again";
                                    $response['erromessage']=array();
                                    $response['status'] = false;
                                    return response($response,400);
                                }
                            }
                            else
                            {
                                //don't have enough balance in your wallet to book this ride..
                                $response['data'] = array();
                                $response['message'] = "you don't have enough balance in your wallet to book this ride.";
                                $response['erromessage']=array();
                                $response['status'] = false;
                                return response($response,400);
                            }
                        }
                        else
                        {
                            //don't have enough balance in your wallet to book this ride..
                            $response['data'] = array();
                            $response['message'] = "you don't have enough balance in your wallet to book this ride.";
                            $response['erromessage']=array();
                            $response['status'] = false;
                            return response($response,400);
                        }
                    }
                    else
                    {
                        //seat is not available
                        $response['data'] = array();
                        $response['message'] = "your requested seat is not available";
                        $response['erromessage']=array();
                        $response['status'] = false;
                        return response($response,400);
                    }
                }
                else
                {
                    //ride not found error
                    $response['data'] = array();
                    $response['message'] = "Please try again";
                    $response['erromessage']=array();
                    $response['status'] = false;
                    return response($response,400);
                }
            }
            else
            {
                $response['data'] = array();
                $response['message'] = "Please try again";
                $response['erromessage']=array();
                $response['status'] = false;
                return response($response,400);    
            }
        }
        catch(\Exception $e)    
        {
            //return redirect()->back()->withErrors(['error'=>true])->withInput();
            \Log::error('bookRide function error: ' . $e->getMessage());
            $response['data'] = array();
            $response['message'] = "Please try again";
            $response['erromessage']=array();
            $response['status'] = false;
            return response($response,400);
            //return Response::json(array('error'=>true), 400);
        }
    }
    //ride book using ccavenu
    public function bookCcavenu()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $param=$this->request->all();
                
                $data['tid']=time();
                $seat=$param['No_seats'];
                $rideid=$param['ride'];
                $fetchRide=DB::table('rides')->select('id','userId','offer_seat','available_seat','cost_per_seat','isDaily')->where('id',$rideid)->get();
                if(count($fetchRide)>0)
                {
                    if($seat==0)
                    {
                        //if requested seat is zero
                        $response['data'] = array();
                        $response['message'] = "Please try again";
                        $response['erromessage']=array();
                        $response['status'] = false;
                        return response($response,200);
                    }
                    else
                    {
                        if($fetchRide[0]->available_seat<$seat)
                        {
                            //error(requested seat not available)
                            $response['data'] = array();
                            $response['message'] = "Requested seat is not available";
                            $response['erromessage']=array();
                            $response['status'] = false;
                            return response($response,200);
                        }
                        else
                        {
                            //
                            if($fetchRide[0]->isDaily==1)
                            {
                                $total_price=27.5;
                            }
                            else
                            {
                                $seat_price=$fetchRide[0]->cost_per_seat*$seat;
                                $tax=$seat_price*0.1;
                                $total_price=$seat_price+$tax;
                            }
                        }
                    }
                }
                else
                {
                    //if ride not found
                    $response['data'] = array();
                    $response['message'] = "Please try again";
                    $response['erromessage']=array();
                    $response['status'] = false;
                    return response($response,200);
                }

                $data['merchant_id']=89593;
                $data['order_id']=$data['tid'];
                $data['amount']=$total_price;
                $data['currency']="INR";
                $data['redirect_url']="http://www.sharemywheel.info/getsuccessPayment";
                $data['cancel_url']="http://www.sharemywheel.info/getsuccessPayment";
                $data['language']="EN";
                $data['merchant_param1']=$rideid;//ride id
                $data['merchant_param2']=$total_price;//total amount
                $data['merchant_param3']=session('userId');//boooked user id
                $data['merchant_param4']=$seat;//no of seats
                $data['merchant_param5']=$fetchRide[0]->isDaily;//is daily 
                $working_key='D8D17336E34ECFE458CCE9D13EE7E640';
                $access_code='AVKF65DF69BB16FKBB';
                $merchant_data="";
                
                foreach ($data as $key => $value){
                    $merchant_data.=$key.'='.$value.'&';
                }

                $encrypted_data=encrypt($merchant_data,$working_key);

               /* $rcvdString=decrypt($encrypted_data,$working_key);      //Crypto Decryption used as per the specified working key.
                $order_status="";
                $decryptValues=explode('&', $rcvdString);
                $dataSize=sizeof($decryptValues);

                for($i = 0; $i < $dataSize; $i++) 
                {
                    $information=explode('=',$decryptValues[$i]);
                        echo '<tr><td>'.$information[0].'</td><td>'.$information[1].'</td></tr>';
                }

                exit;   */

                $response['data'] = array("encrypt"=>$encrypted_data,"access"=>$access_code);
                $response['message'] = "success";
                $response['erromessage']=array();
                $response['status'] = true;
                return response($response,200);
            }
            else
            {
                $response['data'] = array();
                $response['message'] = "Please try again";
                $response['erromessage']=array();
                $response['status'] = false;
                return response($response,200);
            }
        }
        catch(\Exception $e)
        {
            $response['data'] = array();
            $response['message'] = "Please try again";
            $response['erromessage']=array();
            $response['status'] = false;
            return response($response,200);
        }
    }
    function encrypt($plainText,$key)
    {
        $secretKey = hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
        $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
        $plainPad = pkcs5_pad($plainText, $blockSize);
        if (mcrypt_generic_init($openMode, $secretKey, $initVector) != -1) 
        {
              $encryptedText = mcrypt_generic($openMode, $plainPad);
                  mcrypt_generic_deinit($openMode);
                        
        } 
        return bin2hex($encryptedText);
    }

    function decrypt($encryptedText,$key)
    {
        $secretKey = hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText=hextobin($encryptedText);
        $openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
        mcrypt_generic_init($openMode, $secretKey, $initVector);
        $decryptedText = mdecrypt_generic($openMode, $encryptedText);
        $decryptedText = rtrim($decryptedText, "\0");
        mcrypt_generic_deinit($openMode);
        return $decryptedText;
        
    }
    //*********** Padding Function *********************

    function pkcs5_pad ($plainText, $blockSize)
    {
        $pad = $blockSize - (strlen($plainText) % $blockSize);
        return $plainText . str_repeat(chr($pad), $pad);
    }

    //********** Hexadecimal to Binary function for php 4.0 version ********

    function hextobin($hexString) 
    { 
        $length = strlen($hexString); 
        $binString="";   
        $count=0; 
        while($count<$length) 
        {       
            $subString =substr($hexString,$count,2);           
            $packedString = pack("H*",$subString); 
            if ($count==0)
        {
            $binString=$packedString;
        } 
            
        else 
        {
            $binString.=$packedString;
        } 
            
        $count+=2; 
        } 
        return $binString; 
    } 

    function ccavenuFeedback()
    {
        try
        {
            $workingKey='D8D17336E34ECFE458CCE9D13EE7E640';     //Working Key should be provided here.
            $encResponse=$_POST["encResp"];         //This is the response sent by the CCAvenue Server
            $rcvdString=decrypt($encResponse,$workingKey);      //Crypto Decryption used as per the specified working key.
            $order_status="";
            $decryptValues=explode('&', $rcvdString);
            $dataSize=sizeof($decryptValues);

            for($i = 0; $i < $dataSize; $i++) 
            {
                $information=explode('=',$decryptValues[$i]);
                $data[$information[0]]=$information[1];
            }

            for($i = 0; $i < $dataSize; $i++) 
            {
                $information=explode('=',$decryptValues[$i]);
                if($i==3)   $order_status=$information[1];
            }

            if($order_status==="Success")
            {
                $ridedata=DB::table('rides')->where('id',$data['merchant_param1'])->get();
                if($data['merchant_param5']==1)
                {
                    $amount=0;
                    $tax=27.5;
                    $daily=1;
                }
                else
                {
                    $amount=(100*$data['merchant_param2'])/110;
                    $tax=$data['merchant_param2']-$amount;
                    $daily=0;
                }
                $insertArray=array("offer_userId"=>$ridedata[0]->userId,"book_userId"=>$data['merchant_param3'],"rideId"=>$data['merchant_param1'],"source"=>$ridedata[0]->departureOriginal,"destination"=>$ridedata[0]->arrivalOriginal,"no_of_seats"=>$data['merchant_param4'],"cost_per_seat"=>$amount,"paymentType"=>"ccavenu");
                $insertAdminWallet=array("rideId"=>$data['merchant_param1'],"userId"=>$data['merchant_param3'],"amount"=>$tax,"bookType"=>"book","isDaily"=>$daily);          

                DB::table('ride_booking')->insert($insertArray);
                DB::table('admin_wallet')->insert($insertAdminWallet);
                $this->request->session()->flash('status', 'Your ride has been successfully booked');
                return redirect('/bookedRidePaymentMessage');
                //echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.";
                
            }
            else if($order_status==="Aborted")
            {
                //echo "<br>Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail";
                $this->request->session()->flash('failure', 'Your ride is not booked');
                return redirect('/bookedRidePaymentMessage');
            }
            else if($order_status==="Failure")
            {
                $this->request->session()->flash('failure', 'Your ride is not booked');
                return redirect('/bookedRidePaymentMessage');
                //echo "<br>Thank you for shopping with us.However,the transaction has been declined.";
            }
            else
            {
                //echo "<br>Security Error. Illegal access detected";
                $this->request->session()->flash('failure', 'Your ride is not booked');
                return redirect('/bookedRidePaymentMessage');
            }
        }
        catch(\Exception $e)
        {
            return view('errors.404');
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

    //function call when get request of findride
    public function findride()
    {
        try
        {
            if(\Session::has('userId'))
            {
                return view('findRide');
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
