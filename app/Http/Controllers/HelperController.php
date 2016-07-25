<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use Mail;
class HelperController extends Controller
{
    //
    public static function keygen() 
    {
        // Generates a random string of ten digits
        $salt =mt_rand();
        //check already exists or not
        $check=DB::table('device_token')->select('id')->where('apikey',$salt)->get();
        if(count($check)>0)
        {
        	$this->keygen();
        }
        return $salt;
    }
    public static function keygen1($username, $deviceid) {
        
       $secretKey = $username + $deviceid;

        // Generates a random string of ten digits
        $salt = mt_rand();
        

        // Computes the signature by hashing the salt with the secret key as the key
        //$signature = hash_hmac('sha256', $salt, $secretKey, true);

        // base64 encode...
        //$encodedSignature = base64_encode($signature);

        // urlencode...
        //$encodedSignature = urlencode($encodedSignature);
        return $salt;
    }
    public static function makeDirectory($path, $mode = 0777, $recursive = false, $force = false)
    {
        if ($force)
        {
            return @mkdir($path, $mode, $recursive);
        }
        else
        {
            return mkdir($path, $mode, $recursive);
        }
    }
    //for check isset parameter
    public static function checkParameter($requiredparameter,$parameter)
    {
        for($i=0;$i<count($requiredparameter);$i++)
        {
            $match=$requiredparameter[$i];   
            if(isset($parameter[$requiredparameter[$i]]))
            {

            }
            else
            {
                return 1;
            }
        }
        return 0;
    }
    //for generate random password
    public static function random_password( $length = 4 ) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
    }
    //sending email
    public static function send_email($email1,$token)
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
            Log::info('Showing error in mail send profile for user');
        }
       /* \Queue::push(function($job){
            $this->send_sms();
             $job->delete();
        });
        */
        //return 1;
    }
    //sending sms
    public static function send_sms($mobile,$token)
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

        //echo $output;
    }
}
