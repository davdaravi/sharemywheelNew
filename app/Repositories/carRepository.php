<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use Validator; 
use DB;
use Hash;
use App\Http\Controllers\HelperController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Response;

class carRepository
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
    //this function is for add new car details
    public function addCar()
    {   
        try
        {
            if(\Session::has('userId'))
            {
                $parameter=$this->request->all();

                $requiredFieldArray=array("vehicle_type","carMake","carModel","carComfort","carColour","carSeat");
                $check=HelperController::checkParameter($requiredFieldArray,$parameter);
                if($check==1)
                {
                    $errors=array();
                    $errors[]="Your request is incorrect";
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                }
                $messages=[
                    'vehicle_type.required' =>  'Vehicle type is required',
                    'vehicle_type.integer'  =>  'Vehicle type value is wrong',

                    'carMake.required'      =>  'Car Make is required',

                    'carModel.required'     =>  'Car Model is required',

                    'carComfort.required'   =>  'Comfort is required',
                    'carComfort.integer'    =>  'Comfort value is wrong',

                    'carColour.required'    =>  'Color is required',
                    'carColour.integer'     =>  'Color value is wrong',
                    
                    'carSeat.required'      =>  'Seats are required',
                    'carSeat.integer'       =>  'Seats value are wrong'
                ];
                $validator=Validator::make($parameter,[
                        'vehicle_type'  =>  'required|integer',
                        'carMake'       =>  'required',
                        'carModel'      =>  'required',
                        'carComfort'    =>  'required|integer',
                        'carColour'     =>  'required|integer',
                        'carSeat'       =>  'required|integer'
                    ],$messages);
                if($validator->fails())
                {
                    return Response::json(array('success'=>false,'error'=>$validator->getMessageBag()->toArray(),'message'=>''),200);
                }
                else
                {
                    if(isset($parameter['carImage']))
                    {
                        $messages=[
                            'carImage.required'    =>  'car image is required',
                            'carImage.image'       =>  'image format is wrong',
                            'carImage.max'         =>  'Car image can not be more than 2 MB'
                        ];
                        $validator=Validator::make($parameter,[
                            'carImage'    =>  'required|image|max:2048'
                        ],$messages);
                        if($validator->fails())
                        {
                            //image error
                            return Response::json(array('success'=>false,'error'=>$validator->getMessageBag()->toArray(),'message'=>''),200);
                        }
                        else
                        {
                            //insert car
                            //image upload of car
                            $UserPhoto=$parameter['carImage'];
                            $content_type=$UserPhoto->getClientOriginalExtension();
                            $nameImage=$UserPhoto->getClientOriginalName();

                            $userImage = 'cars'.rand(100,999).time().".".$content_type;

                         //Get the file
                         
                            if( is_dir("public/images/cars/".session('userId')) == false )
                            {
                                $path = public_path().'/images/cars/'.session('userId') .'/';
                                HelperController::makeDirectory($path, $mode = 0777, true, true);
                              //@chmod("public/images/users/".$userDetails['id'], 0755);
                            }     
                            $destinationPath=  public_path()."/images/cars/".session('userId').'/';
                            //Store in the filesystem.
                            $new_image_path=$userImage;
                            $data=$UserPhoto->move($destinationPath, $userImage);
                            $insertArray=array("userId"=>session('userId'),"car_make"=>$parameter['carMake'],"car_type"=>$parameter['vehicle_type'],"car_model"=>$parameter['carModel'],"vehical_pic"=>$new_image_path,"comfortId"=>$parameter['carComfort'],"colorId"=>$parameter['carColour'],"no_of_seats"=>$parameter['carSeat']);
                            $insert=DB::table('car_details')->insertGetId($insertArray);
                            if($insert>0)
                            {
                                //inserted successfully
                                $newArray=array("car_id"=>$insert,"car_make"=>$parameter['carMake'],"car_model"=>$parameter['carModel'],"vehical_pic"=>$new_image_path,"no_of_seats"=>$parameter['carSeat'],"userid"=>session('userId'));
                                $errors=array();
                                return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Your car has been added successfully','class'=>'success','data'=>$newArray),200);
                            }
                            else
                            {
                                //not inserted successfully
                                $errors=array();
                                $errors[]="Please try again for add new car";
                                return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
                                //something went wrong with insertion
                            }
                        }
                    }
                    else
                    {
                        $insertArray=array("userId"=>session('userId'),"car_make"=>$parameter['carMake'],"car_type"=>$parameter['vehicle_type'],"car_model"=>$parameter['carModel'],"vehical_pic"=>"car_default.png","comfortId"=>$parameter['carComfort'],"colorId"=>$parameter['carColour'],"no_of_seats"=>$parameter['carSeat']);
                        $insert=DB::table('car_details')->insertGetId($insertArray);
                        if($insert>0)
                        {
                            //inserted successfully
                            $newArray=array("car_id"=>$insert,"car_make"=>$parameter['carMake'],"car_model"=>$parameter['carModel'],"vehical_pic"=>"/images/car_default.png","no_of_seats"=>$parameter['carSeat'],"userid"=>session('userId'));
                            $errors=array();
                            return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Your car has been added successfully','class'=>'success','data'=>$newArray),200);
                        }
                        else
                        {
                            //something went wrong with insertion
                            $errors=array();
                            $errors[]="Please try again for add new car";
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
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
            \Log::error('add car function error'.$e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }

    //function is for get car details
    public function getCarDetails()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $parameter=json_decode($request['json'],true);

                $requiredFieldArray=array("car","userid");
                $check=HelperController::checkParameter($requiredFieldArray,$parameter);
                if($check==1)
                {
                    $errors=array();
                    $errors[]="Your request is incorrect";
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                }
                $validator=Validator::make($parameter,[
                        'car'   =>  'required|integer',
                        'userid'=>  'required|integer'    
                    ]);
                if($validator->fails())
                {
                    //validation error
                    $errors=array();
                    $errors[]="Your request is incorrect";
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                }
                else
                {
                    //get car details
                    $carDetail=DB::table('car_details')->where('id',$parameter['car'])->get();
                    $errors=array();
                    return Response::json(array('status'=>true,'error'=>$errors,'message'=>'','class'=>'success','data'=>$carDetail),200);
                }
            }
            else
            {
                return Response::json(array('error'=>true), 400);    
            }
        }
        catch(\Exception $e)
        {
            \Log::error('getCarDetails function error'.$e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }
    //function is for update car details
    public function updateCar()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $parameter=$this->request->all();
                $requiredFieldArray=array("editCarId","editVehicleType","editCarMake","editCarModel","editCarComfort","editCarColour","editCarSeat","userid");
                $check=HelperController::checkParameter($requiredFieldArray,$parameter);
                if($check==1)
                {
                    $errors=array();
                    $errors[]="Your request is incorrect";
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                }

                $message=[
                    'editVehicleType.required'  =>  'Vehicle Type is required',
                    'editVehicleType.integer'   =>  'Select correct vehicle type',
                    'editCarMake.required'      =>  'Car make is required',
                    'editCarModel.required'     =>  'Car model is required',
                    'editCarComfort.required'   =>  'Comfort is required',
                    'editCarComfort.integer'    =>  'Select correct comfort',
                    'editCarColour.required'    =>  'Color is required',
                    'editCarColour.integer'     =>  'Select correct color',
                    'editCarSeat.required'      =>  'Car seat is required',
                    'editCarSeat.integer'       =>  'Select correct car seat'
                ];
                $validator=Validator::make($parameter,[
                        'editVehicleType'   =>  'required|integer',
                        'editCarMake'       =>  'required',
                        'editCarModel'      =>  'required',
                        'editCarComfort'    =>  'required|integer',
                        'editCarColour'     =>  'required|integer',
                        'editCarSeat'       =>  'required|integer'
                    ],$message);
                if($validator->fails())
                {
                    return Response::json(array('success'=>false,'error'=>$validator->getMessageBag()->toArray(),'message'=>''),200);
                }
                else
                {
                    if(isset($parameter['editCarImage']))
                    {
                        $messages=[
                            'editCarImage.required'=>  'car image is required',
                            'editCarImage.image'   =>  'image format is wrong',
                            'editCarImage.max'     =>  'Car image can not be more than 2 MB'
                        ];
                        $validator=Validator::make($parameter,[
                            'editCarImage'    =>  'required|image|max:2048'
                        ],$messages);
                        if($validator->fails())
                        {
                            //image error
                            return Response::json(array('success'=>false,'error'=>$validator->getMessageBag()->toArray(),'message'=>''),200);
                        }
                        else
                        {
                            $UserPhoto=$parameter['editCarImage'];
                            $content_type=$UserPhoto->getClientOriginalExtension();
                            $nameImage=$UserPhoto->getClientOriginalName();

                            $userImage = 'cars'.rand(100,999).time().".".$content_type;

                         //Get the file
                         
                            if( is_dir("public/images/cars/".session('userId')) == false )
                            {
                                $path = public_path().'/images/cars/'.session('userId') .'/';
                                HelperController::makeDirectory($path, $mode = 0777, true, true);
                              //@chmod("public/images/users/".$userDetails['id'], 0755);
                            }     
                            $destinationPath=  public_path()."/images/cars/".session('userId').'/';
                            //Store in the filesystem.
                            $data=$UserPhoto->move($destinationPath, $userImage);
                            $new_image_path=$userImage;
                            $updateArray=array("userId"=>session('userId'),"car_make"=>$parameter['editCarMake'],"car_type"=>$parameter['editVehicleType'],"car_model"=>$parameter['editCarModel'],"vehical_pic"=>$new_image_path,"comfortId"=>$parameter['editCarComfort'],"colorId"=>$parameter['editCarColour'],"no_of_seats"=>$parameter['editCarSeat']);
                            $update=DB::table('car_details')->where('id',$parameter['editCarId'])->update($updateArray);
                            if($update>=0)
                            {
                                //updated successfully
                                $errors=array();
                                return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Your car has been updated successfully','class'=>'success'),200);
                            }
                            else
                            {
                                //not updated successfully
                                $errors=array();
                                $errors[]="Please try again for update this car";
                                return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
                                //something went wrong with updation
                            }
                        }
                    }
                    else
                    {
                        //if car image is not set
                        $updateArray=array("userId"=>session('userId'),"car_make"=>$parameter['editCarMake'],"car_type"=>$parameter['editVehicleType'],"car_model"=>$parameter['editCarModel'],"comfortId"=>$parameter['editCarComfort'],"colorId"=>$parameter['editCarColour'],"no_of_seats"=>$parameter['editCarSeat']);
                        $update=DB::table('car_details')->where('id',$parameter['editCarId'])->update($updateArray);
                        if($update>=0)
                        {
                            //updated successfully
                            $errors=array();
                            return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Your car has been updated successfully','class'=>'success'),200);
                        }
                        else
                        {
                            //not updated successfully
                            $errors=array();
                            $errors[]="Please try again for update this car";
                            return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
                            //something went wrong with updation
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
            \Log::error('updateCar function error'.$e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }

    //function for delete car
    public function deleteCar()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                $parameter=json_decode($request['json'],true);
                $requiredFieldArray=array("car","userid");
                $check=HelperController::checkParameter($requiredFieldArray,$parameter);
                if($check==1)
                {
                    $errors=array();
                    $errors[]="Your request is incorrect";
                    return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),200);
                }
                $message=[
                    'car.required'  =>  'car is required',
                    'car.integer'   =>  'car value is wrong'
                ];
                $validator=Validator::make($parameter,[
                        'car'   =>  'required|integer'
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
                    $stat=DB::table('car_details')->where('id',$parameter['car'])->update(['is_deleted'=>1]);
                    if($stat>=0)
                    {
                        $errors=array();
                        return Response::json(array('status'=>true,'error'=>$errors,'message'=>'Your car has been deleted successfully','class'=>'success'),200);
                    }
                    else
                    {
                        $errors=array();
                        $errors[]="Please try again for delete this car";
                        return Response::json(array('status'=>false,'error'=>$errors,'message'=>'','class'=>'danger'),501);
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
            \Log::error('deleteCar function error'.$e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }
    //function for get all car
    public function carList()
    {
        try
        {
            if(\Session::has('userId'))
            {
                $request=$this->request->all();
                
                $aColumns = array('car_details.id');
                $sTable = 'car_details';

                $fromdate = $request['fromDate'];
                $todate = $request['toDate'];
                $city = $request['city'];

                $iDisplayStart = $request['iDisplayStart'];
                $iDisplayLength = $request['iDisplayLength'];
                $iSortCol_0 = $request['iSortCol_0'];
                $iSortingCols = $request['iSortingCols'];

                $sSearch = $request['sSearch'];
                $sEcho = $request['sEcho'];

                $ridesData=DB::table('car_details')->select('id','userId','car_make','car_model','vehical_pic','no_of_seats','created_date')->where('userId',session('userId'))->where('is_deleted',0);


                $ridesDataCount = $ridesData->count();
                // dd($ridesData);
                if (!empty($sSearch)) {

                    $ridesData = $ridesData->whereRaw('(car_details.car_make LIKE "%' . $sSearch . '%"
                                                    or car_details.car_model LIKE "%' . $sSearch . '%"  
                                                    or car_details.no_of_seats LIKE "%' . $sSearch . '%"
                                                    )');
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
                            $ridesData = $ridesData->orderBy('car_details.id', 'desc');
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
                    
                    if($value->vehical_pic=='car_default.png')
                    {
                        $pt='/images/car_default.png';
                    }
                    else
                    {
                        $pt='/images/cars/'.session('userId').'/'.$value->vehical_pic;
                    }
                    $newrideData = array();
                    $message='<div class="col-md-3 col-sm-4 col-xs-5">';
                    $message.='<img src="'.$lpath.$pt.'" width="80" height="80">';
                    $message.='</div>';

                    $message.='<div class="col-md-6 col-sm-5 col-xs-5">';
                    $message.='<div>'.strtoupper($value->car_make." ".$value->car_model).'</div>';
                    $message.='<div>'.$value->no_of_seats.' Seats</div>';
                    $message.='</div>';

                    $message.='<div class="col-md-2 col-sm-3 col-xs-2">';
                    $message.='<div class="col-sm-6 col-xs-6">';
                    $message.='<i class="zmdi zmdi-edit zmdi-hc-lg fa-green editCar" id="edit_'.$value->id.'" data-toggle="modal" data-target="#editCar"></i>';
                    $message.='</div>';
                    $message.='<div class="col-sm-6 col-xs-6">';
                    $message.='<i class="zmdi zmdi-delete zmdi-hc-lg fa-red car_delete" id="delete_'.$value->id.'"></i>';
                    $message.='</div>';
                    $message.='</div>';


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
            \Log::error('carList function error'.$e->getMessage());
            return Response::json(array('error'=>true), 400);
        }
    }
}
