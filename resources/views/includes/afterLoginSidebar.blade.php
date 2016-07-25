<nav class="navbar navbar-default">
    <input type="hidden" id="token" name="_token" value="{{ csrf_token() }}">
	<div class="container">
        <div class="container-fluid xs-PLR0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{url('/home')}}">
                	<div class="col-md-3 col-sm-3 col-xs-3">
                		<img src="/images/logo.png" width="50" class="mainLogo">
                	</div>
                	<div class="col-md-9 col-sm-9 col-xs-9">
                		<p class="logoContent">ShareMyWheel</p>
                	</div>
                </a>
                <a href="{{url('/findride')}}" class="btn btn-info margin-top-5">Find a ride</a>&nbsp;<label style="color:black">or&nbsp;</label> 
                <a href="{{url('/offerride')}}" class="btn btn-success margin-top-5">Offer a ride</a>
            </div>

            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class="wallet text-center">
                        <i class="zmdi zmdi-card zmdi-hc-fw"></i>
                        <div>
                            <span>My Wallet</span>
                            <br>
                            <span><b>Rs. <span id="walletamount">25.00</span></b></span>
                        </div>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" style="padding-top: 0px;padding-bottom: 0px;line-height: 49px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <img id="loginuserpic" src="@if(session('profilePic')=='default.png'){{asset('/images/default.png')}}@else{{asset('/images/profile/'.session('userId').'/'.session('profilePic'))}}@endif" style="padding: 4px;margin-right: 10px;border-radius: 8px;width: 35px;height: 35px;">@if(session('userName')){{ session('userName') }}@else{{""}}@endif<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{url('/dashboard')}}">Dashboard</a></li>
                            <li role="separator" class="divider" style="border:1px solid #efefef"></li>
                            <li><a href="#" data-target="#changePassword" data-toggle="modal">Change Password</a></li>
                            <li role="separator" class="divider" style="border:1px solid #efefef"></li>
                            <li><a href="{{url('/logout')}}">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
        <!--/.container-fluid -->
    </div>
</nav>

<div class="modal fade" id="changePassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <form name="passwordForm" id="passwordForm">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Change Password</h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <input type="password" class="form-control" name="cpassword" id="cpassword" placeholder="Enter Current Password"/>
                        <span id="cpassword_msg" class="validation_error PD0"></span>
                    </div>
                    <div class="col-md-12 margin-top-10">
                        <input type="password" class="form-control" name="newPassword" id="newPassword" placeholder="Enter New Password"/>
                        <span id="newPassword_msg" class="validation_error PD0"></span>
                    </div>
                    <div class="col-md-12 margin-top-10">
                        <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Enter Confirm Password"/>
                        <span id="confirmPassword_msg" class="validation_error PD0"></span>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="modal-footer margin-top-30">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save_btn" name="save_btn">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        getAmount();
        $("#save_btn").click(function(){
            var field_id=["cpassword","newPassword","confirmPassword"];
            var msg_text=["Enter Current Password","Enter New Password","Enter Confirm Password"];
            var error_ids=["cpassword_msg","newPassword_msg","confirmPassword_msg"];
            var stat=check_required_validation(field_id,error_ids,msg_text);
            if(stat)
            {
                //var check_password=check_confirm_password();
                var check_password=true;
                if(check_password)
                {
                    update_password();
                }
            }
        });
    });
    function check_required_validation(id,error_id,msg)
    {
        var k=0;
        for(var i=0;i<id.length;i++)
        {
            $("#"+error_id[i]).text('');
            if($("#"+id[i]).val()=="")
            {
                $("#"+error_id[i]).text(msg[i]);
                k=1;
            }
        }
        if(k==1)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    function check_confirm_password()
    {
        if($("#newPassword").val()==$("#confirmPassword").val())
        {
            $("#confirmPassword_msg").text('');
            return true;
        }
        else
        {
            $("#confirmPassword_msg").text('Confirm Password must match with new Password');
            return false;
        }
    }
    function getAmount()
    {
        $.ajax({
            async:false,
            headers: { 'X-CSRF-Token' : $("#token").val() } ,
            type:'GET',
            url:'{{route('get.wallet.amount')}}',
            dataType:'json',
            beforeSend:function(){
                
            },
            success:function(response){
                if(response!=-1)
                {
                    $("#walletamount").text(response);
                }
                else
                {
                    $("#walletamount").text(0);
                }
            },
            error:function(response)
            {
                console.log(response);
                $("#walletamount").text(0);
                //$.toaster({ priority : 'danger', title : 'Title', message : 'Please try again'});
            },
            complete:function(){
                //removeOverlay();
            }
        });
    }
    function update_password()
    {
        $.ajax({
            async:false,
            headers: { 'X-CSRF-Token' : $("#token").val() } ,
            type:'PUT',
            url:'{{route('update.password')}}',
            data:$("#passwordForm").serialize(),
            dataType:'json',
            beforeSend:function(){
                
            },
            success:function(resp){
                $(".validation_error").each(function(){
                    $(this).text('');
                });
                if(Object.keys(resp['error']).length>0)
                {
                    if(resp['error']['cpassword'])
                    {
                        $("#cpassword_msg").text(resp['error']['cpassword'][0]);
                    }
                    if(resp['error']['newPassword'])
                    {
                        $("#newPassword_msg").text(resp['error']['newPassword'][0]);
                    }
                    if(resp['error']['confirmPassword'])
                    {
                        $("#confirmPassword_msg").text(resp['error']['confirmPassword'][0]);
                    }   
                    if(resp['error']['error'])
                    {
                        $.toaster({ priority : 'danger', title : 'Title', message : resp['error']['error'][0]});
                    }
                }
                else
                {
                    $("#cpassword").val("");
                    $("#newPassword").val("");
                    $("#confirmPassword").val("");
                    $("#changePassword").modal('hide');
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