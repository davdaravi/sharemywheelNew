@extends("master")

@section("head")
    <title>Share My Wheel - About</title>
    <style type="text/css">
    .b1{font-weight: bold}
    .n1{font-weight: normal}
    .container .article p{font-family: "Times New Roman", Georgia, Serif;word-wrap: break-word;}
    .custom-panel .panel{ border:1px solid #ddd; box-shadow: none;}
    .custom-panel .panel-heading{ background: #03a9f4 !important; color: #fff !important;}
    .custom-panel .panel-heading p{ margin-bottom: 0px; overflow: hidden; text-overflow:ellipsis; white-space: nowrap;}
    .custom-panel .coupan-code{ border: 1px solid #ddd; color: #333; background: #ddd; padding: 7px; width: 100px;}
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
    	<h3 style="color:#2ABCA3">Coupan code</h3>
    	<div class="clearfix"></div>
    </div>
    @if(count($coupan)>0)
    <div class="col-md-12 margin-top-10">
        @for($i=0;$i<count($coupan);$i++)
        <div class="col-md-6 custom-panel" style="padding:0px">
            <div class="panel">
                <div class="panel-heading">
                    <p class="iffyTip">{{$coupan[$i]->title}}</p>
                </div>
                <div class="panel-body">
                    <p>{{$coupan[$i]->description}}</p>
                    <div class="coupan-code">
                        {{$coupan[$i]->coupan_code}}
                    </div>
                    <br>
                    <span>Expires on {{date("j-M-Y",strtotime($coupan[$i]->end_date))}}</span>
                </div>
            </div>  
        </div>
        <div class="clearfix"></div>
        @endfor   
    </div>
    @endif
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
        $(document).on('mouseenter', ".iffyTip", function () {
             var $this = $(this);
             
                if(this.offsetWidth < this.scrollWidth && !$this.attr('title')){
                    $(".iffyTip").attr('data-toggle','tooltip');
                    $(".iffyTip").attr('placement','bottom');
                    $(".iffyTip").attr('title',$this.text());
                }
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