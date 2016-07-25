<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use Validator; 
use DB;
use Mail;
use Hash;
use Response;
use App\Http\Controllers\HelperController;
class contactRepository
{
    protected $request,$ip;
	public function __construct(Request $request)
	{
		$this->request=$request;
        $this->ip=$request->ip();
        //return redirect()->back()->withErrors(["error"=>"Could not add details! Please try again."]);
	}
    //
    public function contact()
    {
        try
        {
            $parameter=$this->request->all();
            
            $requiredFieldArray=array("from","subject","message");
            $check=HelperController::checkParameter($requiredFieldArray,$parameter);
            if($check==1)
            {
                $this->request->session()->flash('error_message', "Your request is incorrect");
                return redirect()->back()->withInput();
            }
            $messages=[
                'from.required'         =>  'Enter Your email id',
                'from.email'            =>  'Enter Valid email',
                'subject.required'      =>  'Enter Subject',
                'message.required'      =>  'Enter Message',
                'message.min'           =>  'Message length should be minimum 50 characters',
                'message.max'           =>  'Message length can not be more than 500 characters',
            ];
            $validator=Validator::make($parameter,[
                    'from'          =>  'required|email',
                    'subject'       =>  'required',
                    'message'       =>  'required|min:50|max:500',
                ],$messages);
            if($validator->fails())
            {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            else
            {
                //send mail
                $email="info@sharemywheel.com";
                $this->send_contact_email($email,$parameter,$parameter['subject']);
                $this->request->session()->flash('success_message', "Thank you for contact us..we will get back to you shortly");
                return redirect()->back()->withInput();
            }
        }
        catch(\Exception $e)
        {
            \Log::error('contact function error'.$e->getMessage());
            $this->request->session()->flash('error_message', "Your request is incorrect");
            return redirect()->back()->withInput();
        }
    }
    public function send_contact_email($email1,$data,$subject)
    {
        if(Mail::later(5,'emails.sendContactEmail',['name'=>$data],function ($message) use ($email1,$subject){
        //
            $message->from('info@sharemywheel.com', 'ShareMyWheel');

            $message->to($email1);
            $message->subject($subject);
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
}
