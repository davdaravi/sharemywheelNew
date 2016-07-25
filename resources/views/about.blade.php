@extends("master")

@section("head")
    <title>Share My Wheel - About</title>
    <style type="text/css">
    .b1{font-weight: bold}
    .n1{font-weight: normal}
    .container .article p{font-family: "Times New Roman", Georgia, Serif;word-wrap: break-word;}
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
<div class="container" style="background:white;margin-bottom:50px">
    <div class="col-md-12 article margin-top-30">
    	
    		<header><a href="/home"><- Back to Home</a></header>
	    	<h3 style="color:#2ABCA3">About us</h3>
	    	<div class="clearfix"></div>
	    	<p><span>Frustrated by experiencing daily traffic jams in urban cities ?</span></p>
	    	
	    	<p><span>Frustrated by exponentially increasing fuel prices ?</span></p>
	    	<p><span class="b1">****MUST HAVE TRAVEL APP TO SAVE MONEY & EARN MONEY ****</span></p>
	    	<p><span>Avoid Driving alone | Pay fraction of the total fuel cost | Interact with like-minded people | Save Environment using ShareMyWheel.Carpooling as an activity can save you a lot of money besides getting you more socialize and meeting new people. This app can help you find out daily carpool. 
	ShareMyWheel creates a way out for your daily commute while cutting down your expenses. Find ride in and around your city, just enter source and destination and wait for users to share the ride.</span></p>
	    	<p><span class="b1">Features:</span></p>
	    	
	    	<p><span class="b1">*Chat with the people before sharing the ride.</span><p>
	    	<p><span class="b1">*ladies only facility to find ride only for ladies.</span><p>
	    	<p><span class="b1">*ladies only facility to find ride only for ladies.</span></p>
	    	<p><span class="b1">*Create or join a carpool anywhere in the world.</span></p>
	    	<p><span class="b1">*Check transaction history</span></p>
			
			<p><span class="b1">Benefits:</span></p>
			<p><span class="b1">*Use technology to make earth and home happy.</span></p>
			<p><span class="b1">*Play your role in society.</span></p>
			<p><span class="b1">*Connect to more people.</span></p>
			<p><span class="b1">*Save Money</span></p>
			<p><span class="b1">*Save Environment</span></p>
		
    </div>
    <div class="col-md-12 margin-top-30">
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