@extends("master")

@section("head")
    <title>Share My Wheel - Refund & Cancellation</title>
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
<div class="container margin-top-10" style="background:white">
    <div class="row">
        <div class="col-md-12 margin-top-30">
            <article>
            <header><a href="/home"><- Back to Home</a></header>
            <h3 style="color:#2ABCA3">Refund & Cancellation</h3>
            </article>
        </div>
        <div class="col-sm-10  col-xs-12 col-md-7 col-md-offset-2 col-sm-offset-1">
            <div class="col-sm-4 col-md-4">
                <div class="refund-policy">
                <div class="steps-div">
                    <img src="{{asset('images/mail.png')}}" class="img-responsive">
                </div>
                <div class="number-pos">1</div>
                <br>
                <span>Raise request via mail</span>
            </div>
            </div>

            <div class="col-sm-4 col-md-4">
                <div class="refund-policy">
                <div class="steps-div">
                    <img src="{{asset('images/call.png')}}" class="img-responsive">
                </div>
                <div class="number-pos">2</div>
                <br>
                <span>Receive call</span>
            </div>
            </div>

            <div class="col-sm-4 col-md-4">
                <div class="refund-policy">
                <div class="steps-div">
                    <img src="{{asset('images/dollar.png')}}" class="img-responsive">
                </div>
                <div class="number-pos">3</div>
                <br>
                <span>Refund amount</span>
            </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 pictorial">
            <h3 class="color-2ABCA3">Steps:</h3>

            <ul>
                <li>1. Raise request via mail <a href="mailto:info@sharemywheel.com">info@sharemywheel.com</a> and mention the validate reason in the body.</li>
                <li>2. You will receive call from the help desk within 3-4 working days.</li>
                <li>3. Instructor will instruct you that you will receive the refund amount via wallet or cash by 10-15 working days.</li>
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