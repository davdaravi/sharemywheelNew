@extends("master")

@section("head")
    <title>Share My Wheel - Status</title>
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
<div class="container margin-top-10" style="background:white;">
    <div class="row">
    <div class="col-md-12 article paymentStatusPage text-center margin-top-30">
        <!-- <div class="paymentStatusSuccess">
            <i class="zmdi zmdi-check"></i>
            <h2>Thank You.</h2>
            <p>You are now signed out of Zoho Mail.</p>
            <a href="#">Go to Home</a>
        </div> -->
        @if(Session::has('bookingsuccess'))
            <div class="paymentStatusSuccess">
                <i class="zmdi zmdi-check"></i>
                <h2>Thank You.</h2>
                <p>{{session('bookingsuccess')}}</p>
                <a href="{{route('get.login')}}">Go to Home</a>
            </div>
        @elseif(Session::has('bookingfailure'))
            <div class="paymentStatusUnsuccess">
                <i class="zmdi zmdi-close"></i>
                <p class="margin-top-30">{{session('bookingfailure')}}</p>
                <a href="{{route('get.login')}}">Go to Home</a>
            </div>
        @endif

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