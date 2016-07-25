@extends("master")

@section("head")
    <title>Share My Wheel - Contact us</title>
    <style type="text/css">
    .b1{font-weight: bold}
    .n1{font-weight: normal}
    .container article p{font-family: "Times New Roman", Georgia, Serif;word-wrap: break-word;}
    </style>
@endsection
@section("nav")
	@if(Session::has('userId'))
    	@include("includes.afterLoginSidebar")
    @else
    	@include("includes.beforeLoginSidebar")
    @endif
@endsection
@section("content")
<div class="container sharingContentBody">
    <div class="col-md-12">
        <div class="col-md-12 sharingRideGrid">
            
            <div class="col-md-12 margin-top-15">
                <a href="/"><- Back to home</a>
                <h4>Contact us</h4>
                <hr/>
            </div>
            <div class="col-md-6">
                <form class="form-horizontal" role="form" method="POST" name="contactform" id="contactform" action="{{route('post.contact')}}">
                    <div class="form-group col-md-12 margin-top-10 margin-bottom-0">
                        <label class="col-sm-12 col-md-12">From :</label>
                        <div class="col-sm-12 col-md-12">
                            <input type="email" name="from" id="from" class="form-control search" placeholder="Enter Your Email id" value="@if(old('from')){{ old('from') }}@else{{""}}@endif" required/>
                        </div>
                        @if($errors->has('from'))<p id="from_error" class="validation_error PD20">{{ $errors->first('from') }}</p>@endif
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-12 margin-top-10 margin-bottom-0">
                        <label class="col-sm-12 col-md-12">Subject :</label>
                        <div class="col-sm-12 col-md-12">
                            <input type="text" name="subject" id="subject" class="form-control search" placeholder="Enter Subject" value="@if(old('subject')){{ old('subject') }}@else{{""}}@endif" required/>
                        </div>
                        @if($errors->has('from'))<p id="subject_error" class="validation_error PD20">{{ $errors->first('subject') }}</p>@endif
                    </div>
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="clearfix"></div>
                    <div class="form-group col-md-12 margin-top-10 margin-bottom-0">
                        <label class="col-sm-12 col-md-12">Message :</label>
                        <div class="col-sm-12 col-md-12">
                            <textarea class="form-control" name="message" id="message" rows="6" minlength="50" maxlength="500" placeholder="Enter Year Message" required>@if(old('message')){{ old('message') }}@else{{""}}@endif</textarea>
                        </div>
                        @if($errors->has('from'))<p id="message_error" class="validation_error PD20">{{ $errors->first('message') }}</p>@endif
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-12 margin-top-10 margin-bottom-0">
                        <div class="col-sm-12 col-md-12">
                            <input type="submit" class="btn btn-info" name="contact_btn" id="contact_btn" value="Send"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 contactDetails">
                <div class="col-md-6 text-center">
                    <i class="zmdi zmdi-email"></i><br>
                    <b>Email</b><br> 
                    <span>info@sharemywheel.com</span>
                </div>
                <div class="col-md-6 text-center">
                    <i class="zmdi zmdi-pin zmdi-hc-fw"></i><br>
                    <b>Address</b><br>
                    <span>B-1/101, Bhulabhai Park,Opp. Swati-5 Residency,
                        Nr. K.B. Royal flat, Chandkeda,
                        Ahmedabad-382424
                        Gujarat, India.</span>
                </div>
            </div>
        </div>
    </div>
</div>
@if(Session::has('userId'))
@else
@include('loginModal')
@endif
@endsection
@section("js")
<script type="text/javascript" src="{{URL::asset('js/jquery.toaster.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
    @if(Session::has('userId'))
    @else
    <?php
    if(isset($errors))
    {
        if(count($errors)>0)
        {
            if($errors->has('username') || $errors->has('password') || $errors->has('error'))
            {
            ?>
                $("#loginModal").modal('show');
            <?php
            }
            else if($errors->has('signuser') || $errors->has('signpassword') || $errors->has('signconfirm') || $errors->has('signemail') || $errors->has('signcontact'))
            {?>
                $("#signUpModal").modal('show');
            <?php
            }
            else
            {?>
                $("#loginModal").modal('hide');
                $("#signUpModal").modal('hide');
            <?php
            }
        }    
    }
    ?>
    //login modal click clear value
    $(".login").click(function(){
        $("#username").val('');
        $("#password").val('');
        $(".loginerror").each(function(){
            $(this).text('');
            $(this).hide();
        });
    });
    //if signup modal click
    $(".signup").click(function(){
        $("#signuser").val('');
        $("#signpassword").val('');
        $("#signconfirm").val('');
        $("#signemail").val('');
        $("#signcontact").val('');
        $(".signerror").each(function(){
            $(this).text('');
            $(this).hide();
        });
    });
    $(document).ready(function(){
        $(".frgpassword").click(function(){
            $("#loginModal").modal('hide');
            $("#forgotPassword").modal('show');
        });

        $("#btn_forgot").click(function(){
            if($("#forgotemail").val()=="")
            {
                $("#forgotemail_msg").text('Enter Email');
            }
            else
            {
                $("#forgotemail_msg").text('');
                forgot_password();   
            }
        });
    });
    function forgot_password()
    {
        $.ajax({
            async:false,
            headers: { 'X-CSRF-Token' : $("#token").val() } ,
            type:'PUT',
            url:'{{route('forgot.password')}}',
            data:$("#forgotPasswordForm").serialize(),
            dataType:'json',
            beforeSend:function(){
                
            },
            success:function(resp){
                $(".validation_error").each(function(){
                    $(this).text('');
                });
                if(Object.keys(resp['error']).length>0)
                {
                    if(resp['error']['forgotemail'])
                    {
                        $("#forgotemail_msg").text(resp['error']['forgotemail'][0]);
                    }
                    if(resp['error']['error'])
                    {
                        $.toaster({ priority : 'danger', title : 'Title', message : resp['error']['error'][0]});
                    }
                }
                else
                {
                    $("#forgotemail").val("");
                    $("#forgotPassword").modal('hide');
                    $.toaster({ priority : 'success', title : 'Title', message :resp['message']});
                }
            },
            error:function(response)
            {
                $.toaster({ priority : 'danger', title : 'Title', message : 'Please try again'});
            },
            complete:function(){
                //removeOverlay();
            }
        });
    }
    @endif

    @if(Session::has('error_message'))
        $.toaster({ priority : 'danger', title : 'Title', message : '{{Session::get('error_message')}}'});
    @elseif(Session::has('success_message'))
        $("#from").val("");
        $("#subject").val("");
        $("#message").val("");
        $.toaster({ priority : 'success', title : 'Title', message : '{{Session::get('success_message')}}'});
    @endif
});
</script>
@endsection