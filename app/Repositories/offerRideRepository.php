<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use Validator; 
use DB;
use App\Http\Controllers\HelperController;
use Response;
use Illuminate\Foundation\Bus\DispatchesJobs;

class offerRideRepository
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
    
    //this function is for load offerride
    public function viewOfferRide()
    {
        //select all car
        try
        {
            if(\Session::get('userId'))
            {
                $carList=DB::table('car_details')->where('userId',session('userId'))->where('is_deleted',0)->get();
                //leave
                $leave=DB::table('leave_on')->where('is_deleted',0)->get();
                //detour
                $detour=DB::table('detour')->where('is_deleted',0)->get();
                //luggage
                $luggage=DB::table('luggage')->where('is_deleted',0)->get();
                return view('offerRide',['car'=>$carList,'leave'=>$leave,'detour'=>$detour,'luggage'=>$luggage]);
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
    //function for create ride
    public function createRide()
    {
        //for create ride if ride is daily then check payment type if payment type is wallet then check if user having amount in their wallet if not then throw error otherwise move forward and if payment type is ccavenu then encrypt whole data and send encrypetion data and key..
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $param=json_decode($request['json'],true);
                
                //check if field exists or not
                $requiredFieldArray=array("place","lat","lng","city","original","fromdate","todate","returnHour","returnMin","car","luggage","leave","detour","comments","ladies","return","daily","way1","way2","way3","way4","from","to","licence","seat","costseat","payment_type");
                $check=HelperController::checkParameter($requiredFieldArray,$param);
                if($check==1)
                {
                    $errors=array();
                    $errors['error']="Your request is incorrect";
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                }

                $place=$param['place'];
                $lat=$param['lat'];
                $lng=$param['lng'];
                $city=$param['city'];
                $original=$param['original'];
                $fromdate=$param['fromdate'];
                $todate=$param['todate'];
                $returnHour=$param['returnHour'];
                $returnMin=$param['returnMin'];
                $car=$param['car'];
                $luggage=$param['luggage'];
                $leave=$param['leave'];
                $detour=$param['detour'];
                $comments=$param['comments'];
                $ladies=$param['ladies'];
                $return=$param['return'];
                $daily=$param['daily'];
                $from=$param['from'];//source place
                $to=$param['to'];//destination place
                $licence=$param['licence'];
                $seat=$param['seat'];
                $costseat=$param['costseat'];
                $payment_type=$param['payment_type'];
                $errors=array();
                $flg=0;
                if(count($place)>0)
                {
                    if(isset($place[0]))
                    {
                        if($place[0]=="")
                        {
                            $errors['from'][0]="Enter Valid location";
                            $flg=1;    
                        }
                        else
                        {
                            if($original[0]!=$from)
                            {
                                $errors['from'][0]="Enter Valid location";
                                $flg=1;
                            }
                        }
                    }   
                    else
                    {
                        $errors['from'][0]="Enter Valid location";
                        $flg=1;
                    }

                    if(isset($place[1]))
                    {
                        if($place[1]=="")
                        {
                            $errors['to'][0]="Enter Valid location";       
                            $flg=1;
                        }
                        else
                        {
                            if($original[1]!=$to)
                            {
                                $errors['to'][0]="Enter Valid location";       
                                $flg=1;       
                            }
                        }
                    }
                    else
                    {
                        $errors['to'][0]="Enter Valid location";
                        $flg=1;
                    }

                    if(isset($place[2]))
                    {
                        if(isset($place[3]))
                        {
                            if(($place[2]=="" && $param['way1']!="") || ($original[2]!=$param['way1']) || ($place[2]!="" && $param['way1']==""))
                            {
                                $errors['areafrom_0'][0]="Enter Valid location";    
                                $flg=1;
                            }
                            if(($place[3]=="" && $param['way2']!="") || ($place[3]!="" && $param['way2']=="") || ($original[3]!=$param['way2']))
                            {
                                $errors['areafrom_1'][0]="Enter Valid location";
                                $flg=1;
                            }
                        }
                        else
                        {
                            if($param['way1']!="")
                            {
                                if(($place[2]=="" && $param['way1']!="") || ($original[2]!=$param['way1']))
                                {
                                    $errors['areafrom_0'][0]="Enter Valid location";    
                                    $flg=1;
                                }

                                if($param['way2']!='0')
                                {
                                    $errors['areafrom_1'][0]="Enter Valid location";
                                    $flg=1;
                                }
                            }
                        }
                    }
                    else
                    {
                        if($param['way1']!="")
                        {
                            $errors['areafrom_0'][0]="Enter Valid location";
                            $flg=1;
                        }
                        if($param['way2']!='0')
                        {
                            if(isset($place[3]))
                            {
                                if($original[3]!=$param['way2'])
                                {
                                    $errors['areafrom_1'][0]="Enter Valid location";
                                    $flg=1;
                                }
                            }
                            else
                            {
                                $errors['areafrom_1'][0]="Enter Valid location";
                                $flg=1;
                            }
                        }
                    }

                    if(isset($place[4]))
                    {
                        if(($place[4]=="") || ($place[4]!="" && $param['way3']=="") || ($original[4]!=$param['way3']))
                        {
                            $errors['areafrom_2'][0]="Enter Valid location";
                            $flg=1;    
                        }
                    }
                    else
                    {
                        if($param['way3']!='0')
                        {
                            $errors['areafrom_2'][0]="Enter Valid location";
                            $flg=1;
                        }
                    }

                    if(isset($place[5]))
                    {
                        if(($place[5]=="") || ($place[5]!="" && $param['way4']=="") || ($original[5]!=$param['way4']))
                        {
                            $errors['areafrom_3'][0]="Enter Valid location";
                            $flg=1;
                        }
                    }
                    else
                    {
                        if($param['way4']!='0')
                        {
                            $errors['areafrom_3'][0]="Enter Valid location";
                            $flg=1;
                        }
                    }

                    if($flg==1)
                    {
                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);       
                    }
                }
                else
                {
                    $errors['from'][0]="Enter Valid location";
                    $errors['to'][0]="Enter Valid location";
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                }



                $checkArray=array("car"=>$car,"luggage"=>$luggage,"leave"=>$leave,"detour"=>$detour,"comments"=>$comments,"licence"=>$licence,"fromdate"=>$fromdate,"seat"=>$seat,"costseat"=>$costseat);
                $message=[
                        'car.required'      =>  'Select car',
                        'luggage.required'  =>  'Select luggage',
                        'leave.required'    =>  'Select leave',
                        'detour.required'   =>  'Select detour',
                        'licence.required'  =>  'Select licence',
                        'fromdate.required' =>  'Select departure date',
                        'fromdate.date'     =>  'Select correct departure date',
                        'seat.required'     =>  'Select no of seats',
                        'seat.integer'      =>  'Wrong value select',
                        'costseat.required' =>  'Cost per seat is required',
                        'costseat.integer'  =>  'Cost must be in integer'
                    ];
                $Validator=Validator::make($checkArray,[
                        'car'       =>  'required',
                        'luggage'   =>  'required',
                        'leave'     =>  'required',
                        'detour'    =>  'required',
                        'licence'   =>  'required',
                        'fromdate'  =>  'required|date',
                        'seat'      =>  'required|integer',
                        'costseat'  =>  'required|integer'
                    ],$message);

                if($Validator->fails())
                {
                    return Response::json(array('status'=>false,'error'=>$Validator->getMessageBag()->toArray(),'message'=>'','class'=>'danger'),200);
                }
                else
                {
                    //check return date if not daily and return date
                    if($daily==0 && $return==1)
                    {
                        if($todate!="")
                        {
                            $message1=[
                                'fromdate.required' =>  'Select departure date',
                                'fromdate.date'     =>  'Select correct departure date',
                                'todate.required'   =>  'Select return date',
                                'todate.date'       =>  'Select correct return date',
                                'todate.after'      =>  'Return date must be greater than departure date'
                            ];
                            $checkRetunDate=array("fromdate"=>$fromdate,"todate"=>$todate);
                            $Validator1=Validator::make($checkRetunDate,[
                                    'fromdate'  =>  'required|date',
                                    'todate'    =>  'required|date|after:fromdate'
                                ],$message1);
                            if($Validator1->fails())
                            {
                                return Response::json(array('status'=>false,'error'=>$Validator1->getMessageBag()->toArray(),'message'=>'','class'=>'danger'),200);
                            }
                        }
                        else
                        {
                            //if return date is null
                            $errors['todate'][0]="Select Return Date";
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                        }
                    }
                    else
                    {
                        if($daily==1 && $return==1)
                        {
                            $fromhour=date("H",strtotime($fromdate));
                            $frommin=date("i",strtotime($fromdate));
                            $fromtime=$fromhour.":".$frommin;
                            $endtime=$returnHour.":".$returnMin;
                            $strfromtime=strtotime($fromtime);
                            $strendtime=strtotime($endtime);
                            if($strfromtime>=$strendtime)
                            {
                                //departure date time grater than return hour and min
                                $errors['returnHour'][0]="Return time should be greater than departure time";
                                return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                            }
                        }
                    }
                    $ride_status=0;
                    //------
                    //check if daily ride if it is then check payment type
                    if($daily==1)
                    {
                        if($payment_type=="wallet")
                        {
                            $se=DB::table('payment_wallete')->where('userId',session('userId'))->where('amount','>',30)->get();
                            if(count($se)>0)
                            {
                                //wallet amount transfer from user account to admin wallet    
                                $famount=$se[0]->amount-27.5;
                                        
                            }
                            else
                            {
                                //error does not have sufficient balance
                                $errors['wallet'][0]="You Don't have sufficient balance to offer this ride";
                                return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                            }
                        }
                        else
                        {
                            $ride_status=1;
                        }
                    }
                    //------
                    if($costseat<=0 && $daily==0)
                    {
                        $errors['costseat'][0]="Cost of seat must be greater than zero..";
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                    }
                    else
                    {   
                        if($todate!="")
                        {
                            $todate=date("Y-m-d H:i:s",strtotime($todate));
                        }
                        else
                        {
                            $todate="0000-00-00 00:00:00";
                        }

                        if($daily==1)
                        {
                            $costseat=0;
                        }
                        $return_time=$returnHour.":".$returnMin;
                        $insertRideArray=array("userId"=>session('userId'),"carId"=>$car,"departure"=>$place[0],"departure_lat_long"=>$lat[0].",".$lng[0],"departureCity"=>strtolower(trim($city[0])),"departureOriginal"=>$original[0],"arrival"=>$place[1],"arrival_lat_long"=>$lat[1].",".$lng[1],"arrivalCity"=>strtolower(trim($city[1])),"arrivalOriginal"=>$original[1],"offer_seat"=>$seat,"available_seat"=>$seat,"cost_per_seat"=>$costseat,"departure_date"=>date("Y-m-d H:i:s",strtotime($fromdate)),"return_date"=>$todate,"return_time"=>$return_time,"is_round_trip"=>$return,"isDaily"=>$daily,"ladies_only"=>$ladies,"luggage_size"=>$luggage,"leave_on"=>$leave,"can_detour"=>$detour,"status"=>$ride_status,"view_count"=>0,"licence_verified"=>$licence,"comment"=>$comments,"ratting"=>0);

                        //transaction
                        DB::beginTransaction();
                        //
                        $rideID=DB::table('rides')->insertGetId($insertRideArray);
                        if($rideID>0)
                        {
                            //insert waypoints
                            if(count($place)>2)
                            {
                                for($i=2;$i<count($place);$i++)
                                {
                                    $ff=array("rideId"=>$rideID,"city"=>$place[$i],"cityName"=>strtolower(trim($city[$i])),"cityOriginal"=>$original[$i],"city_lat_long"=>$lat[$i].",".$lng[$i]);
                                    $new[]=$ff;
                                }
                                $rideViaInsert=DB::table('ride_via_points')->insert($new);
                                if($rideViaInsert>0)
                                {
                                    //add preference
                                    $ridepreferenceinsert=array();
                                    $getpreferenceList=DB::table('user_preferences')->select('preferenceId','pref_optionId')->where('userid',session('userId'))->where('isDeleted',0)->get();
                                    if(count($getpreferenceList)>0)
                                    {
                                        for($i=0;$i<count($getpreferenceList);$i++)
                                        {
                                            $ridePreference=array("userId"=>session('userId'),"rideId"=>$rideID,"preferenceId"=>$getpreferenceList[$i]->preferenceId,"pref_optionId"=>$getpreferenceList[$i]->pref_optionId);
                                            $ridepreferenceinsert[]=$ridePreference;
                                        }
                                        $insertpreference=DB::table('user_ride_preferences')->insert($ridepreferenceinsert);
                                        if($insertpreference<0 || $insertpreference=="")
                                        {
                                            DB::rollback();
                                            $errors['error']="Please try again";
                                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
                                        }
                                    }
                                    else
                                    {
                                        $getpreferencedefault=DB::table('preferences_option')->select('id','preference_id')->groupBy('preference_id')->get();
                                        if(count($getpreferencedefault)>0)
                                        {
                                            for($i=0;$i<count($getpreferencedefault);$i++)
                                            {
                                                $ridePreference=array("userId"=>session('userId'),"rideId"=>$rideID,"preferenceId"=>$getpreferencedefault[$i]->preference_id,"pref_optionId"=>$getpreferencedefault[$i]->id);
                                                $ridepreferenceinsert[]=$ridePreference;
                                            }
                                            $insertpreference=DB::table('user_ride_preferences')->insert($ridepreferenceinsert);
                                            if($insertpreference<0 || $insertpreference=="")
                                            {
                                                DB::rollback();
                                                $errors['error']="Please try again";
                                                return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
                                            }
                                        }
                                    }
                                    //add preference ends
                                    if($daily==1 && $payment_type=="wallet")
                                    {
                                        DB::table('payment_wallete')->where('userId',session('userId'))->update(['amount'=>$famount]);
                                        DB::table('admin_wallet')->insert(['rideId'=>$rideID,"userId"=>session('userId'),"amount"=>27.5,"bookType"=>"create","isDaily"=>1]);
                                    }
                                    else if($daily==1 && $payment_type=="ccavenu")
                                    {
                                        //create data for ccavenu
                                        DB::commit();
                                        $cd['tid']=time();
                                        $cd['merchant_id']=89903;
                                        $cd['order_id']=$cd['tid'];
                                        $cd['amount']=27.5;
                                        $cd['currency']="INR";
                                        $cd['redirect_url']="http://sharemywheel.info/offerrideSuccess";
                                        $cd['cancel_url']="http://sharemywheel.info/offerrideSuccess";
                                        $cd['language']="EN";
                                        $cd['merchant_param1']=$rideID;
                                        $cd['merchant_param2']="create";
                                        $cd['merchant_param3']=session('userId');
                                        $merchant_data='';
                                        $working_key='A087123D0EA8318575EA3EDDDF177F7E';//Shared by CCAVENUES
                                        $access_code='AVUD66DH35AD37DUDA';//Shared by CCAVENUES
                                        
                                        foreach ($cd as $key => $value){
                                            $merchant_data.=$key.'='.$value.'&';
                                        }

                                        $encrypted_data=encrypt($merchant_data,$working_key); 
                                        $data['access']='AVUD66DH35AD37DUDA';
                                        $data['dd']=$encrypted_data;




                                        return Response::json(array('status'=>true,'error'=>$errors,'message'=>'','class'=>'success','data'=>$data),200);
                                        //update ride status from 1 to 0 when response back from ccavenu
                                    }   
                                    else
                                    {
                                        
                                    }
                                    DB::commit();
                                    //data inserted successfully
                                    return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Ride has been offered successfully','class'=>'success'),200);
                                }   
                                else
                                {
                                    DB::rollBack();
                                    //something wrong
                                    $errors['error']="Please try again for creating ride..";
                                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
                                }
                            }
                            else
                            {
                                //add preference
                                $ridepreferenceinsert=array();
                                $getpreferenceList=DB::table('user_preferences')->select('preferenceId','pref_optionId')->where('userid',session('userId'))->where('isDeleted',0)->get();
                                if(count($getpreferenceList)>0)
                                {
                                    for($i=0;$i<count($getpreferenceList);$i++)
                                    {
                                        $ridePreference=array("userId"=>session('userId'),"rideId"=>$rideID,"preferenceId"=>$getpreferenceList[$i]->preferenceId,"pref_optionId"=>$getpreferenceList[$i]->pref_optionId);
                                        $ridepreferenceinsert[]=$ridePreference;
                                    }
                                    $insertpreference=DB::table('user_ride_preferences')->insert($ridepreferenceinsert);
                                    if($insertpreference<0 || $insertpreference=="")
                                    {
                                        DB::rollback();
                                        $errors['error']="Please try again";
                                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
                                    }
                                }
                                else
                                {
                                    $getpreferencedefault=DB::table('preferences_option')->select('id','preference_id')->groupBy('preference_id')->get();
                                    if(count($getpreferencedefault)>0)
                                    {
                                        for($i=0;$i<count($getpreferencedefault);$i++)
                                        {
                                            $ridePreference=array("userId"=>session('userId'),"rideId"=>$rideID,"preferenceId"=>$getpreferencedefault[$i]->preference_id,"pref_optionId"=>$getpreferencedefault[$i]->id);
                                            $ridepreferenceinsert[]=$ridePreference;
                                        }
                                        $insertpreference=DB::table('user_ride_preferences')->insert($ridepreferenceinsert);
                                        if($insertpreference<0 || $insertpreference=="")
                                        {
                                            DB::rollback();
                                            $errors['error']="Please try again";
                                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
                                        }
                                    }
                                }
                                //add preference ends
                                if($daily==1 && $payment_type=="wallet")
                                {
                                    DB::table('payment_wallete')->where('userId',session('userId'))->update(['amount'=>$famount]);
                                    DB::table('admin_wallet')->insert(['rideId'=>$rideID,"userId"=>session('userId'),"amount"=>27.5,"bookType"=>"create","isDaily"=>1]);
                                }
                                else if($daily==1 && $payment_type="ccavenu")
                                {
                                    //create data for ccavenu
                                    DB::commit();
                                    $cd['tid']=time();
                                    $cd['merchant_id']=89903;
                                    $cd['order_id']=$cd['tid'];
                                    $cd['amount']=27.5;
                                    $cd['currency']="INR";
                                    $cd['redirect_url']="http://sharemywheel.info/offerrideSuccess";
                                    $cd['cancel_url']="http://sharemywheel.info/offerrideSuccess";
                                    $cd['language']="EN";
                                    $cd['merchant_param1']=$rideID;
                                    $cd['merchant_param2']="create";
                                    $cd['merchant_param3']=session('userId');
                                    $merchant_data='';
                                    $working_key='A087123D0EA8318575EA3EDDDF177F7E';//Shared by CCAVENUES
                                    $access_code='AVUD66DH35AD37DUDA';//Shared by CCAVENUES
                                    
                                    foreach ($cd as $key => $value){
                                        $merchant_data.=$key.'='.$value.'&';
                                    }

                                    $encrypted_data=encrypt($merchant_data,$working_key); 
                                    $data['access']='AVUD66DH35AD37DUDA';
                                    $data['dd']=$encrypted_data;


                                    /*$workingKey='A087123D0EA8318575EA3EDDDF177F7E';     //Working Key should be provided here.
                                    $encResponse="eyJpdiI6IkoyeVpoWndEZ0hoNWFNVEN5XC9uQ3lRPT0iLCJ2YWx1ZSI6IjNEVnZsZmpubU5qaENaUlJrZXk2Wnp5bFF6Q0YyQkgwQkh4aHhxVjRjU1JrbWp1MFV0ckdpTVBRbXk0T2N6ZlVmVkJmTjY4WWdGbElESlVzS0ZZcFo4QVBLdzdcLzQwZGpsVmxTU21jN0hLempEY3hINDF6a3pmNEFaMlpsK3N5Qzc4OHMwd1ZtTEpxd09OeTNyM0puSVwvZUI0VzVJV2c0ZVYzbkVrbE13WVwvbkVyVTNRYlduMm5LOVhPWkRUcVlkeHFQdU95QkVwOWZTUWZQZ25YSGRrNEdsYlRzYnRqKzAwRnluOW5xVnF0MzFUWmdSZWlXSHpjaGJWU290ZU5PbFp6M3hZNVwvXC9jWStibTZwNFZOZWZ4Z2tPM09DTFVRU3N6bFwvK2dEanJtV1NjPSIsIm1hYyI6ImIyZWNmYWVjZDZiZmNiNTNjNDhiYmYzZGVkZjM2N2QzMzJiNzJiMjdjZGM4ZmU3MGI3NTBkZjQ4MzI1Yzc2NmIifQ==";         //This is the response sent by the CCAvenue Server
                                    $rcvdString=decrypt($encResponse,$workingKey);   
                                    print_r($rcvdString);
                                    exit;*/
                                    return Response::json(array('status'=>true,'error'=>$errors,'message'=>'','class'=>'success','data'=>$data),200);
                                    //update ride status from 1 to 0 when response back from ccavenu
                                }
                                else
                                {

                                }
                                DB::commit();
                                //ride created without waypoints
                                return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Ride has been offered successfully','class'=>'success'),200);
                            }
                        }
                        else
                        {
                            DB::rollBack();
                            //something went wrong while inserting rides
                            $errors['error']="Please try again for creating ride..";
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
                        }
                    }
                    
                }
            }
            else
            {
                $errors['error']="Please try again for creating ride..";
                return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
            }
        }
        catch(\Exception $e)
        {
            \Log::error('createRide function error: ' . $e->getMessage());
            $errors['error']="Please try again for creating ride..";
            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
        }
    }
    //ccavenu functions
    function encrypt($plainText,$key)
    {
        $secretKey = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
        $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
        $plainPad = $this->pkcs5_pad($plainText, $blockSize);
        if (mcrypt_generic_init($openMode, $secretKey, $initVector) != -1) 
        {
              $encryptedText = mcrypt_generic($openMode, $plainPad);
                  mcrypt_generic_deinit($openMode);
                        
        } 
        return bin2hex($encryptedText);
    }

    function decrypt($encryptedText,$key)
    {
        $secretKey = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText=$this->hextobin($encryptedText);
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

    //function for generate encrypte data
    function encrypteData($dd)
    {
        $merchant_data='';
        $working_key='A087123D0EA8318575EA3EDDDF177F7E';//Shared by CCAVENUES
        $access_code='AVUD66DH35AD37DUDA';//Shared by CCAVENUES
        foreach ($dd as $key => $value){
            $merchant_data.=$key.'='.$value.'&';
        }

        $encrypted_data=encrypt($merchant_data,$working_key); // Method for encrypting the data.
        return $encrypted_data;
    }
    //function for done ccavenu payment response
    public function ccavenuPayment()
    {
        try
        {
            $workingKey='A087123D0EA8318575EA3EDDDF177F7E';     //Working Key should be provided here.
            $encResponse=$_POST["encResp"];         //This is the response sent by the CCAvenue Server
            $rcvdString=$this->decrypt($encResponse,$workingKey);      //Crypto Decryption used as per the specified working key.
            $order_status="";
            $decryptValues=explode('&', $rcvdString);
            $dataSize=sizeof($decryptValues);
            //echo "<center>";
            $data=array();
            
            for($i = 0; $i < $dataSize; $i++) 
            {
                $information=explode('=',$decryptValues[$i]);
                $data[$information[0]]=$information[1];
                if($i==3)   $order_status=$information[1];
            }

            if($order_status==="Success")
            {
                //echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.";
                $paid_amount=$data['amount'];
                $order_id=$data['order_id'];
                $tracking_id=$data['tracking_id'];
                $rideid=$data['merchant_param1'];
                $offerType="create";
                $userid=$data['merchant_param3'];

                //change status of ride from 1 to 0..
                $updateStatus=DB::table('rides')->where('id',$rideid)->where('userId',$userid)->update(['status'=>0,'order_id'=>$order_id,'tracking_id'=>$tracking_id,'paid_amount'=>$paid_amount]);
                
                //add amount 27.5 in admin wallet...
                DB::table('admin_wallet')->insert(['rideId'=>$rideid,"userId"=>$userid,"amount"=>$paid_amount,"bookType"=>$offerType,"isDaily"=>1]);
                //flash message set
                $message="Your transaction has been successfully completed.Your ride offered successfully.";
                session()->flash('offersuccess',$message);
                //return redirect('/');

            }
            else if($order_status==="Aborted")
            {
                //echo "<br>Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail";
                $message="Your transaction has been Aborted.Please Try again to offer daily ride.";
                session()->flash('offerfailure',$message);
            }
            else if($order_status==="Failure")
            {
                $message="Your transaction has been declined.Please Try again to offer daily ride.";
                session()->flash('offerfailure',$message);
                //echo "<br>Thank you for shopping with us.However,the transaction has been declined.";
            }
            else
            {
                //echo "<br>Security Error. Illegal access detected";
                $message="Your transaction has been declined.Please Try again to offer daily ride.";
                session()->flash('offerfailure',$message);
            }
            return redirect('/rideStatus');
            /*echo "<br><br>";

            echo "<table cellspacing=4 cellpadding=4>";
            for($i = 0; $i < $dataSize; $i++) 
            {
                $information=explode('=',$decryptValues[$i]);
                    echo '<tr><td>'.$information[0].'</td><td>'.$information[1].'</td></tr>';
            }

            echo "</table><br>";
            echo "</center>";*/
        }
        catch(\Exception $e)
        {
            \Log::error('createRide function error: ' . $e->getMessage());
            return view('errors.404');
        }
    }
}
