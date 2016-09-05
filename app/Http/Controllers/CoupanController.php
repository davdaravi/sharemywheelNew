<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;

class CoupanController extends Controller
{
    //
    public function addCoupanCode(Request $request)
    {
    	try
    	{
    		$param=$request->all();
    		if(isset($param['amount']) && isset($param['totalcoupan']) && isset($param['length']))
    		{
    			if($param['amount']==250)
    			{
    				for($i=0;$i<$param['totalcoupan'];$i++)
    				{	
    					$promocode=$this->getPromocode($param['length']);	
    					$insertCoupan=array("coupan_code"=>$promocode,"amount"=>$param['amount']);
    					DB::table('coupan_code')->insert($insertCoupan);
    				}
    			}
    			else if($param['amount']==500)
    			{
    				for($i=0;$i<$param['totalcoupan'];$i++)
    				{	
    					$promocode=$this->getPromocode($param['length']);	
    					$insertCoupan=array("coupan_code"=>$promocode,"amount"=>$param['amount']);
    					DB::table('coupan_code')->insert($insertCoupan);
    				}
    			}
    			else
    			{
    				for($i=0;$i<$param['totalcoupan'];$i++)
    				{	
    					$promocode=$this->getPromocode($param['length']);	
    					$insertCoupan=array("coupan_code"=>$promocode,"amount"=>$param['amount']);
    					DB::table('coupan_code')->insert($insertCoupan);
    				}
    			}
    		}
    		else
    		{
    			abort(404);
    		}
    	}
    	catch(\Exception $e)
    	{
    		\Log::error($e->getMessage());
    		abort(404);
    	}
    }
    public function getPromocode($length=5)
    {
    	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $password = substr( str_shuffle( $chars ), 0, $length );
        $check=DB::table('coupan_code')->select('id')->where('coupan_code','like',$password)->get();
        if(count($check)>0)
        {
        	$this->getPromocode($length);
        }
        return $password;
    }
}
