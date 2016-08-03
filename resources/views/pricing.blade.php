@extends("master")

@section("head")
    <title>Share My Wheel - Pricing</title>
    <style type="text/css">
    .b1{font-weight: bold}
    .n1{font-weight: normal}
    .container article p{font-family: "Times New Roman", Georgia, Serif;word-wrap: break-word;}

    .refund-policy{ 
        padding: 15px;
        text-align: center;
        margin: 0 auto;
    }
    .refund-policy .steps-div{ 
        padding: 30px;
        border: 2px solid #2ABCA3;
        border-radius: 50%;
        width: 170px;
        text-align: center;
        margin: 0 auto;
    }
    .refund-policy span{ font-weight: bold; font-size: 16px;}
    .refund-policy .number-pos{ padding: 7px; border-radius: 50%; background: #F9B316; position: absolute; top: 16%; height: 25px; width: 25px; line-height: 12px; }
    .color-2ABCA3{ color: #2ABCA3; }
    .pictorial ul{ padding-bottom: 50px;}
    .pictorial ul li{ list-style: none; padding-left: 10px; margin-bottom: 10px; }
    .refund-policy h4{ font-size: 14px; line-height: 22px;}

    @media only screen and (max-width: 767px){
        .refund-policy{ width: 250px; margin: 0 auto}
        .refund-policy img{ width: 100px !important; margin: 0 auto; height: 100px;}
        .refund-policy .number-pos{ top: 20%;}
     }
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
<div class="container margin-top-10" style="background:white;">
    <div class="col-md-12 margin-top-30">
    	<article>
    		<header><a href="/home"><- Back to Home</a></header>
    	
    	<h3 style="color:#2ABCA3">Pricing</h3>
    	<div class="clearfix"></div>
    	   <div class="row">
                <div class="col-sm-10 col-xs-12 col-md-6 col-md-offset-3 col-sm-offset-1">
                    <div class="col-sm-6 col-md-6">
                        <div class="refund-policy">
                            <div class="steps-div">
                                <img src="{{asset('images/dailyRide.png')}}" class="img-responsive">
                            </div>
                            <br>
                            <span>Daily Ride</span>
                            <h4>25 Rs. + tax + Charges(+5%)  </h4>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-6">
                        <div class="refund-policy">
                        <div class="steps-div">
                            <img src="{{asset('images/regularRide.png')}}" class="img-responsive">
                        </div>
                        <br>
                        <span>Regular Ride</span>
                        <h4>Ride amount +  5% + tax </h4>
                    </div>
                    </div>
                </div>
           </div>

		</article>
    </div>
    <div class="row">
        <div class="col-sm-12 pictorial">
            <div class="col-md-12">
                <h4 style="color:rgb(42, 188, 163)">Below are the details of the pricing structure.</h4>
            </div>
            <ul>
                <li>1. For the daily ride create you have to pay (25 Rs. + tax + Charges(+5%)).</li>
                <li>2. For the daily ride book you have to pay (25 Rs. + tax + Charges(+5%)).</li>
                <li>3. For the regular ride book you have to pay (Ride amount + 5% + tax ).</li>
            </ul>
        </div>
    </div>
</div>
@if(Session::has('userId'))
@else
@include('loginModal')
@endif

@endsection
@section("js")
@if(Session::has('userId'))
@else
    <script type="text/javascript" src="{{URL::asset('js/jquery.toaster.js')}}"></script>
    <script type="text/javascript">
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
    </script>
@endif
@endsection