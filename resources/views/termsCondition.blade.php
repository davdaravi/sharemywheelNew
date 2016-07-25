@extends("master")

@section("head")
    <title>Share My Wheel - Terms & Condition</title>
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
<div class="container" style="background:white;margin-bottom:50px">
    <div class="col-md-12 margin-top-30">
    	<article>
    		<header><a href="/home"><- Back to Home</a></header>
    	
    	<h3 style="color:#2ABCA3">Terms and Conditions</h3>
    	<div class="clearfix"></div>
    	<p><span class="b1">(A) SCOPE AND DEFINITION</span></p>
    	
    	<p>-> <span class="b1">Share my wheel</span><span class="n1"> this service or site or company registered in India.</span></p>
    	<p>-> <span class="b1">Share my wheel</span><span class="n1"> is a (social) communications platform for members to transact with one another.</span></p>
    	<p>-> <span class="b1">Share my wheel</span><span class="n1"> provide communicational service for ride sharing.</span></p>
    	<p>-> <span class="n1">Vehicle for a trip by a vehicle owner or driver caring a co-traveler for that trip in exchange for a cost contribution</span></p>
    	<p>-> <span class="n1">Neither share my wheel  nor the site provides any transport services. Use of this site both the parties or members are communicate each other for share the journey or trip</span></p>
    	<p><span class="b1">*user:</span><p>
    	<p>-> <span class="n1">over the age of 18 years person who use this site for trip sharing</span></p>
    	<p><span class="b1">*account:</span><p>
    	<p>-> <span class="n1">user must create a user account and agree to provide correct personal information as requested.</span></p>
    	<p>-> <span class="b1">Share my wheel</span><span class="n1"> need users first name last name, valid age, valid telephone number, email address etc. user must give all the personal data true, complete, correct, and accurate.</span></p>
    	<p>-> <span class="n1">But when the person uses the site, this site not declares that all the users are accurate. May be possible users are false, inaccurate, misleading or fraudulent.</span> <span class="b1">Share my wheel</span> <span class="n1">advise that every user that they should not find a ride at the time of emergency as well as before taking any ride please try to know the person who is offering you ride from his face and atmosphere of vehicle.</span></p>
    	<p><span class="b1">*log in:</span></p>
    	<p>-> <span class="n1">Share my wheel creates a communicational platform who offers share journey with each others. Here members are mostly travel alone to their places so if we find a co traveler with him they also have company in their ride as well as it saves some cost of the vehicle owner. As well as the person who are regularly finding a transport, we can help them to find ride and they feel also much convenient than using a public transport.</span></p>
    	<p><span class="b1">*Vehicle sharing:</span></p>
    	<p>-> <span class="n1">vehicle means four-wheeler, two-wheeler three-wheeler etc. govt. approved vehicle as per country rule. Vehicle which offered by owner for ride.</span></p>
    	<p>-> <span class="n1">Vehicle sharing means trip by a vehicle owner or driver caring a co-traveler for that trip in exchange for a cost contribution.</span></p>

    	<p><span class="b1">*Condition:</span></p>
		<p>-> <span class="n1">means these general condition of use including good conduct charter and privacy policy of Some as notified on the site.</span></p>    	
		<p><span class="b1">*Cost contribution:</span></p>
		<p>-> <span class="n1">the amount agreed between the vehicle owner and the co-traveler in relation to the trip which is payable by the co traveler as their contribution towards the costs of the trip.</span></p>    	
		
		<p><span class="b1">*co-traveler-or passenger:</span></p>
		<p>-> <span class="n1">members who has accepted an offer to be transported by a vehicle owner and includes all other persons who accompany such member in the vehicle for trip.</span></p>    	
		<p><span class="b1">*car owner or driver:</span></p>
		<p>-> <span class="n1">member who trough the site offers to share a vehicle journey with a co-traveler in exchange for the cost contribution.</span></p>    	
		<p><span class="b1">*member:</span> <span class="n1">registered user</span></p>
		<p><span class="b1">*trip:</span></p>
		<p>-> <span class="n1">a given journey in relation to which a vehicle owner and co-traveler have agreed upon a transaction trough the site.</span></p>    	
		<p><span class="b1" style="font-size:16px">Liability:</span></p>
		<p><span class="b1">A. Co-travelers or ride partners liability.</span></p>
		<p><span class="n1">*Co-traveler give all the details are correct to car owner.</span></p>    	
		<p><span class="n1">*Co-traveler checks all the details that need for a good and valid journey.</span></p>
		<p><span class="n1">*Co-traveler should immediately inform incase of any change of trip.</span></p>
		<p><span class="n1">*Co-traveler can not any blame, any kind of refund against company.</span></p>
		<p><span class="b1">B. Vehicle owner's liability.</span></p>
		<p><span class="n1">* Car owner also should mention correct details.</span></p>
		<p><span class="n1">* Car owner should have valid driving license as well as valid insurance & valid documents.</span></p>
		<p><span class="n1">*Give the correct and accurate details (including smoking, pets, or luggage etc.)</span></p>
		<p><span class="n1">* Avoid any kind of fraudulent, criminal activity and unlawful activities.</span></p>
		<p><span class="n1">*Right time on pick up point. Wait at least 30mins.after agreement time.</span></p>
		<p><span class="n1">*owner give the details completely and correct to co traveler(s)</span></p>
		<p><span class="n1">*owner check all the details which are needed for good and valid journey.</span></p>
		<p><span class="n1">*owner should immediately inform in case of any change of trip.</span></p>
		<p><span class="n1">*driving license, PUC certificate insurance policy..</span></p>
		<p><span class="n1">*verify all the details which given by the co-traveler.</span></p>
		<p><span class="n1">*owner can not any kind of blame, any kind of refund against on company.</span></p>
		<p><span class="n1">*right time at place. wait on pick up point at least 30mins after agreement time.</span></p>
		<p><span class="b1">C. company liability.</span></p>
		<p><span class="n1">*there is no any kind of liability of this company  whether civil or criminal liabilities.</span></p>

		<p><span class="n1">*no warranty and limitation of liability share on my wheel</span></p>
		<p><span class="n1">*users understand and agree that we do not guarantee the any accuracy or legitimacy of any listing posting information by other users.</span></p>
		<p><span class="n1">*users use this site and service at your own discretion and risk and that will be solely responsible for any damages that arise from such use.</span></p>
		<p><span class="n1">*some is not member or element of users contract for travel.</span></p>
		<p><span class="n1">*some is not party to any agreement or transaction between members, nor is some liable in respect of any matter arising which relates to a booking between members.</span></p>
		<p><span class="n1">*some is nor the site provides any transport services.</span></p>
		<p><span class="n1">*company will not be liable to any member in the event that any information provided by another member is false, incomplete, insurance, misleading or fraudulent.</span></p>
		</article>
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