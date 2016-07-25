@extends("master")

@section("head")
    <title>Share My Wheel - dashboard</title>
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
    <style type="text/css"> 
    div.stars,div.stars1
    {
        width: 145px;
        display: inline-block;
    }
    input.star,input.star1 { display: none; }
    label.star,label.star1 {
        float: right;
        padding: 2px;
        font-size: 26px;
        color: #444;
        transition: all .2s;
    }
    input.star:checked ~ label.star:before,input.star1:checked ~ label.star1:before {
        content: '\f005';
        color: #FD4;
        transition: all .25s;
    }
   
    input.star-5:checked ~ label.star:before{
      color: #FE7;
      text-shadow: 0 0 20px #952;
    }
    input.star-1:checked ~ label.star:before{ color: #F62; }
    label.star:before,label.star1:before {
        content: '\f006';
        font-family: FontAwesome;
    }
    .overlay
    {
        width: 100%;
        height: 100%;
        position: absolute;
        background: rgba(215, 213, 213, 0.49);
        opacity: 1;
        margin: 0;
        padding: 0;
        top: 0;
        left: 0;
        z-index: 9999999999;
    }
    .overlayImage
    {
        background: url('images/loader/loader3.gif') no-repeat;
        background-size: 100% 100%;
        width: 30px;
        height: 30px;
        margin: 0 auto;
        top: 200px;
        position: relative;
    }
    .panel-heading{padding: 1px 15px!important;}
    </style>
@endsection
@section("nav")
    @include("includes.afterLoginSidebar")
@endsection
@section("content")
<!-- main Content -->
<div class="container dashboardTabs">
<!--begin tabs going in wide content -->
<ul class="nav nav-tabs margin-top-10" id="maincontent" role="tablist">
    <li class="active"><a href="#rideOffered" role="tab" data-toggle="tab">Rides Offered</a>
    </li>
    <li class="messages"><a href="#messages" role="tab" data-toggle="tab">Messages</a>
    </li>
    <li><a href="#transactionHistory" class="transactionHistory" role="tab" data-toggle="tab">Transaction History</a>
    </li>
    <li><a href="#booking" class="booking" role="tab" data-toggle="tab">Booking</a>
    </li>
    <li><a href="#ratings" role="tab" class="ratingMain" data-toggle="tab">Ratings</a>
    </li>
    <li><a href="#profile" role="tab" class="profile" data-toggle="tab">Profile</a>
    </li>
    <li><a href="#coupan_Code" role="tab" class="coupan_Code" data-toggle="tab">Coupan code</a>
    </li>
    <li><a href="#withdraw" role="tab" class="withdraw" data-toggle="tab">Withdraw Amount</a>
    </li>

</ul>
<!--/.nav-tabs.content-tabs -->


<div class="tab-content">
    <div class="tab-pane fade active in sharingRideGrid" id="rideOffered">
        @for($i=0;$i<count($rideOffered);$i++)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>
                <span>{{$rideOffered[$i]->departureOriginal}}</span>
                <i class="zmdi zmdi-arrow-right"></i> 
                <span>{{$rideOffered[$i]->arrivalOriginal}}</span>
                </h4>
            </div>
            <div class="panel-body">
                <div class="col-md-6">
                    <div>
                        <i class="zmdi zmdi-calendar-alt zmdi-hc-lg"></i>&nbsp;&nbsp;<span>{{date("l d F Y - h:i A",strtotime($rideOffered[$i]->departure_date))}}</span>
                    </div>
                    <div class="margin-top-10">
                        <i class="zmdi zmdi-filter-list zmdi-hc-lg"></i> <span>{{$rideOffered[$i]->view_count}} visit(s)</span>
                    </div>
                </div>

                <div class="col-md-6 text-right">
                    <div>
                        <span>{{$rideOffered[$i]->available_seat}}</span> <small>seats left</small>
                    </div>
                    <div class="margin-top-10">
                        <h3>{{(int)$rideOffered[$i]->cost_per_seat}} &#8377; <small>Per co-traveller</small></h3>
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr/>
                <div class="col-md-6">
                    <!--<span><a href="#"><i class="zmdi zmdi-copy zmdi-hc-lg"></i> Duplicate</a></span>-->
                </div>

                <div class="col-md-6 text-right">
                    <span><a href="{{route('get.ride.offer',$rideOffered[$i]->rideId)}}"><i class="zmdi zmdi-eye zmdi-hc-lg"></i> See ride offer  </a></span> 
                    <!--<span><a href="#"><i class="zmdi zmdi-filter-list zmdi-hc-lg"></i> See visitors</a></span>-->
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        @endfor
        
    </div>

    <div class="tab-pane fade" id="messages">
        
        <div class="row">
            <div class="col-md-12 col-sm-12 chat-box">
                @if(count($msgUser)>0)
                <div class="col-md-3 col-sm-3 left-message-box">
                    <div class="search-bar">
                        <input type="text" class="form-control" name="searchUser" id="searchUser" placeholder="Search"/> 
                    </div>
                    <div class="chat-list">
                        @for($i=0;$i<count($msgUser);$i++)
                        <a class="user-chat-messages" data-id="{{$msgUser[$i]->id}}" data-pic="@if($msgUser[$i]->profile_pic=='default.png'){{asset('/images/default.png')}}@else{{asset('/images/profile/'.$msgUser[$i]->id.'/'.$msgUser[$i]->profile_pic)}}@endif" data-flag="false">
                            <div class="avatar">
                                <img class="img-responsive" src="@if($msgUser[$i]->profile_pic=='default.png'){{asset('/images/default.png')}}@else{{asset('/images/profile/'.$msgUser[$i]->id.'/'.$msgUser[$i]->profile_pic)}}@endif" />
                            </div>
                            <div class="name-message">
                                <h5>{{$msgUser[$i]->username}}</h5>
                                <p></p>
                            </div>
                        </a>
                        @endfor
                        
                    </div>
                </div>
                
                <div class="col-md-6 col-sm-6 xs-PLR">
                    <div class="left-message-box">
                        <div class="main">
                        <div class="message-inbox-height" id="chat_div">

                            <div class="row">
                                <div class="col-md-12 col-xs-12 date-time-inbox text-center">
                                    <span>Wednesday, 25 April 2016</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12 col-sm-12 sender">
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        <div class="message-bubble">
                                            <span>Hello fwhiej oejwoifjiwejfoi jweoifjoiwe </span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-2">
                                        <img src="https://media.licdn.com/mpr/mprx/0_0tjxpPcg_B6IAzDmDchVy5Fg-4wmAvDXjUhjWSVRl4QaAMaeem3jdEF41III8U_IymhJDRQghmEwCk7DJJurIe6UBmEICk0weJugYPVRGNRGC1mGegyyZe7r2C" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12 col-sm-12 sender">
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        <div class="message-bubble">
                                            <span>Hello fweif iojweoifj oiwejfoi jweoif owei iquhwe fwhiej oejwoifjiwejfoi jweoifjoiwe </span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-2">
                                        <img src="https://media.licdn.com/mpr/mprx/0_0tjxpPcg_B6IAzDmDchVy5Fg-4wmAvDXjUhjWSVRl4QaAMaeem3jdEF41III8U_IymhJDRQghmEwCk7DJJurIe6UBmEICk0weJugYPVRGNRGC1mGegyyZe7r2C" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 receiver col-sm-12 col-xs-12">
                                    <div class="col-md-2 col-sm-2 col-xs-2">
                                        <img src="https://media.licdn.com/mpr/mprx/0_09uOYTnN-QWb1KTdeklJWZBtiJHGCr8EyrlyExEqAVWF1pDQY0lUOv9t1sebCta2erKJwABc87XF83rdEgjlWscNc7Xb83pdDgjppTIqCvvm8nS8DJDj9_ZO-M" />
                                    </div>
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        <div class="message-bubble">
                                            <span>Hi</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 sender col-xs-12">
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        <div class="message-bubble">
                                            <span>How are you ?</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-2">
                                        <img src="https://media.licdn.com/mpr/mprx/0_0tjxpPcg_B6IAzDmDchVy5Fg-4wmAvDXjUhjWSVRl4QaAMaeem3jdEF41III8U_IymhJDRQghmEwCk7DJJurIe6UBmEICk0weJugYPVRGNRGC1mGegyyZe7r2C" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-12 col-sm-12 receiver">
                                    <div class="col-md-2 col-xs-2 col-sm-2">
                                        <img src="https://media.licdn.com/mpr/mprx/0_09uOYTnN-QWb1KTdeklJWZBtiJHGCr8EyrlyExEqAVWF1pDQY0lUOv9t1sebCta2erKJwABc87XF83rdEgjlWscNc7Xb83pdDgjppTIqCvvm8nS8DJDj9_ZO-M" />
                                    </div>
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        <div class="message-bubble">
                                            <span>I am Fine...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12  date-time-inbox text-center">
                                    <span>Thusday, 26 April 2016</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-12 col-sm-12 receiver">
                                    <div class="col-md-2 col-xs-2 col-sm-2">
                                        <img src="https://media.licdn.com/mpr/mprx/0_09uOYTnN-QWb1KTdeklJWZBtiJHGCr8EyrlyExEqAVWF1pDQY0lUOv9t1sebCta2erKJwABc87XF83rdEgjlWscNc7Xb83pdDgjppTIqCvvm8nS8DJDj9_ZO-M" />
                                    </div>
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        <div class="message-bubble">
                                            <span>I am Fine...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-12 col-sm-12 receiver">
                                    <div class="col-md-2 col-xs-2 col-sm-2">
                                        <img src="https://media.licdn.com/mpr/mprx/0_09uOYTnN-QWb1KTdeklJWZBtiJHGCr8EyrlyExEqAVWF1pDQY0lUOv9t1sebCta2erKJwABc87XF83rdEgjlWscNc7Xb83pdDgjppTIqCvvm8nS8DJDj9_ZO-M" />
                                    </div>
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        <div class="message-bubble">
                                            <span>I am Fine...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-12 col-sm-12 receiver">
                                    <div class="col-md-2 col-xs-2 col-sm-2">
                                        <img src="https://media.licdn.com/mpr/mprx/0_09uOYTnN-QWb1KTdeklJWZBtiJHGCr8EyrlyExEqAVWF1pDQY0lUOv9t1sebCta2erKJwABc87XF83rdEgjlWscNc7Xb83pdDgjppTIqCvvm8nS8DJDj9_ZO-M" />
                                    </div>
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        <div class="message-bubble">
                                            <span>I am Fine...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="send-message">
                            <form>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 PLR0 col-xs-12">
                                        <div class="col-md-9 col-sm-9 PLR0 col-xs-12">
                                            <textarea class="form-control" id="userMsg" name="userMsg" placeholder="Write your message here"></textarea>
                                        </div>
                                        <div class="col-md-3 col-sm-3 PLR0 col-xs-12">
                                            <input type="button" value="SEND" class="btn btn-primary" name="sendMsgBtn" id="sendMsgBtn"/>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-3 PL0 profile-main">
                    <div class="left-message-box" id="chatUserProfile">
                        <div class="message-profile">
                            <img class="img-responsive" src="https://media.licdn.com/mpr/mprx/0_0tjxpPcg_B6IAzDmDchVy5Fg-4wmAvDXjUhjWSVRl4QaAMaeem3jdEF41III8U_IymhJDRQghmEwCk7DJJurIe6UBmEICk0weJugYPVRGNRGC1mGegyyZe7r2C" />
                        </div>
                        <div class="text-center">
                            <h3>Nitin</h3>
                            <br>
                            <i class="zmdi zmdi-check-circle zmdi-hc-fw"></i>
                            <span>Mobile Number Verified</span>
                            <br><br>
                            <i class="zmdi zmdi-close-circle zmdi-hc-fw"></i>
                            <span>Email not Verified</span>
                            <br><br>
                        </div>
                    </div>
                </div>
                @else
                No Chat found
                @endif
            </div>
        </div>
    </div>
    <input type="hidden" id="upic" value="<?php echo session('profilePic')?>"/>
    <div class="tab-pane fade" id="transactionHistory">
        <div class="tabbable tabs-left">
            <ul class="nav nav-tabs">
                <li class="active paidTransaction"><a href="#paidTransaction" data-toggle="tab">Paid Transaction</a>
                </li>
                <li class="earnTransaction"><a href="#earnTransaction" data-toggle="tab">Earn Transaction</a>
                </li>
            </ul>
            <div class="tab-content col-md-8 col-sm-8 col-xs-12">
                <div class="tab-pane active" id="paidTransaction" style="padding-top:0px">
                    <table id="paidTransactionTable" class="table table-bordered table-hover" style="width:100%;">
                        <thead>
                            <tr>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                        </tbody>
                    </table>
                    
                </div>
                <div class="tab-pane" id="earnTransaction" style="padding-top:0px">
                    <table id="earnTransactionTable" class="table table-bordered table-hover" style="width:100%;">
                        <thead>
                            <tr>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                        
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <!--div start for booking-->
    <div class="tab-pane fade" id="booking">
        <div class="tabbable tabs-left">
            <ul class="nav nav-tabs">
                <!-- <li class="bookingHistory"><a href="#bookingHistory" data-toggle="tab">Booking history</a>
                </li> -->
                <li class="active ridebooked"><a href="#ridebooked" data-toggle="tab">Ride Booked</a>
                </li>
            </ul>
            <div class="tab-content col-md-8 col-sm-8 col-xs-12">
                <div class="tab-pane active" id="bookingHistory" style="padding-top:0px">
                    <table id="bookingHistoryTable" class="table table-bordered table-hover" style="width:100%;">
                        <thead>
                            <tr>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                        </tbody>
                    </table>
                    
                </div>
                <div class="tab-pane" id="ridebooked" style="padding-top:0px">
                    <table id="ridebookedTable" class="table table-bordered table-hover" style="width:100%;">
                        <thead>
                            <tr>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                        
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <!--div ends of booking-->
        <div class="tab-pane fade" id="ratings">
            <div class="tabbable tabs-left">
                <ul class="nav nav-tabs">
                    <li class="active ratingMain"><a href="#exchangeRatings" data-toggle="tab">Exchange Ratings</a>
                    </li>
                    <li class="ratingsReceived"><a href="#ratingsReceived" data-toggle="tab">Ratings Received</a>
                    </li>
                    <li class="ratingsGiven"><a href="#ratingsGiven" data-toggle="tab">Ratings Given</a>
                    </li>
                </ul>
                <div class="tab-content col-md-8">
                    <div class="tab-pane active" id="exchangeRatings">
                        <div class="col-md-12">
                            <h4>Exchange ratings with another member</h4>
                            <h5>Select a member you travelled with by selecting their name </h5>
                        </div>

                        <form>
                            <div class="col-md-5 margin-top-20">
                                <label class="control-label">User</label>
                                <select class="form-control" name="ratingUser" id="ratingUser">
                                    <option value="">Select User</option>
                                </select>
                            </div>
                            <div class="col-md-4 margin-top-20">
                                <label class="control-label">Rating</label>
                                <div class="clearfix"></div>
                                <div class="stars">
                                    <input type="radio" class="star star-5" id="star-5" name="rating" value="5" /><label for="star-5" class="star star-5"></label>
                                    <input type="radio" class="star star-4" id="star-4" name="rating" value="4" /><label for="star-4" class="star star-4"></label>
                                    <input type="radio" class="star star-3" id="star-3" name="rating" value="3" /><label for="star-3" class="star star-3"></label>
                                    <input type="radio" class="star star-2" id="star-2" name="rating" value="2" /><label for="star-2" class="star star-2"></label>
                                    <input type="radio" class="star star-1" id="star-1" name="rating" value="1" /><label for="star-1" class="star star-1"></label>
                                    <div class="clearfix"></div>
                                </div>
                            </div>

                            <div class="margin-top-45 col-md-2">
                                <input type="button" class="btn btn-primary" name="ratingSubmitBtn" id="ratingSubmitBtn" value="Submit" />
                            </div>
                        </form>
                        
                    </div>
                    <div class="tab-pane" id="ratingsReceived">
                        <div class="col-md-12">
                            <h4>List of rating received users</h4>
                            <table id="ratingReceivedTable" class="table table-bordered table-hover" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        
                                    </tbody>
                                </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="ratingsGiven">
                        <div class="col-md-12">
                            <h4>List of rating given users</h4>
                            <table id="ratingGivenTable" class="table table-bordered table-hover" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                       
                                    </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="tab-pane fade" id="profile">
            <div class="col-md-12">
                <!-- tabs left -->
                <div class="tabbable tabs-left">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#personalInfo" data-toggle="tab">Personal Information</a>
                        </li>
                        <li><a href="#preference" class="preferences_tab" data-toggle="tab">preference</a>
                        </li>
                        <li><a href="#car" data-toggle="tab">car</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="personalInfo">
                            <div class="col-md-9">
                                <h3>Personal Information</h3>
                                <hr/>
                                <!--<form class="form-horizontal" name="userPersonalInfoForm" id="userPersonalInfoForm" role="form">-->
                                    <label id="formerror" style="vertical-align:bottom" class="validation_error"></label>
                                    <form action="" method="POST" name="imagesubmitform" id="imagesubmitform" enctype="multipart/form-data">
                                    <div class="form-group margin-top-10">
                                        <label class="control-label col-xs-12 col-md-12 margin-top-10">Profile pic:</label>
                                        <div class="col-xs-2 col-md-2">
                                            <img id="userimage" src="/images/default.png" width="100px" height="100px">
                                        </div>
                                        <div class="col-xs-5 col-md-5" style="margin-top:40px">
                                            <input type="file" name="profile_pic" id="profile_pic" accept="image/gif, image/jpeg, image/png, image/gif"/>
                                        </div>
                                        <div class="col-xs-2 col-md-2" style="margin-top:40px">
                                            <button type="submit" class="btn btn-primary" name="uploadImage" id="uploadImage">Upload</button>
                                        </div>
                                    </div>
                                    </form>
                                    <div class="clearfix"></div>
                                    <form action="" method="POST" name="licencesubmitform" id="licencesubmitform" enctype="multipart/form-data">
                                    <div class="form-group margin-top-10">
                                        <label class="control-label col-xs-12 col-md-12 margin-top-10">Licence pic:</label>
                                        <div class="col-xs-2 col-md-2">
                                            <img id="licenceimage" src="/images/no_licence.png" width="100px" height="100px">
                                        </div>
                                        <div class="col-xs-5 col-md-5" style="margin-top:40px">
                                            <input type="file" name="licence_pic" id="licence_pic" accept="image/gif, image/jpeg, image/png, image/gif"/>
                                        </div>
                                        <div class="col-xs-2 col-md-2" style="margin-top:40px">
                                            <button type="submit" class="btn btn-primary" name="uploadLicence" id="uploadLicence">Upload</button>
                                        </div>
                                    </div>
                                    </form>
                                    <div class="clearfix"></div>
                                    <div class="form-group margin-top-10">
                                        <label class="control-label col-xs-2 col-md-2 margin-top-10">Username :</label>
                                        <div class="col-xs-5 col-md-5">
                                            <input type="text" class="form-control" maxlength="35" name="username" id="username" placeholder="Enter Username"/>
                                        </div>
                                        <label id="userNameError" style="vertical-align:bottom" class="validation_error"></label>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group margin-top-10">
                                        <label class="control-label col-xs-2 col-md-2 margin-top-10">Gender :</label>
                                        <div class="col-xs-5 col-md-5">
                                            <div class="btn-group" data-toggle="buttons">
                                                <input type="radio" name="gender" id="male" value="M"> Male
                                                <input type="radio" name="gender" id="female" value="F"> Female
                                            </div>
                                        </div>
                                        <label id="genderError" style="vertical-align:bottom" class="validation_error"></label>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group margin-top-10">
                                        <label class="control-label col-xs-2 col-md-2 margin-top-10">First Name :</label>
                                        <div class="col-xs-5 col-md-5">
                                            <input type="text" name="firstName" id="firstName" maxlength="35" class="form-control" placeholder="Enter First Name"/>
                                        </div>
                                        <label id="firstNameError" style="vertical-align:bottom" class="validation_error"></label>
                                    </div>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="clearfix"></div>

                                    <div class="form-group margin-top-10">
                                        <label class="control-label col-xs-2 col-md-2 margin-top-10">Last Name :</label>
                                        <div class="col-xs-5 col-md-5">
                                            <input type="text" name="lastName" id="lastName" maxlength="35" class="form-control" placeholder="Enter Last Name"/>
                                        </div>
                                        <label id="lastNameError" style="vertical-align:bottom" class="validation_error"></label>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group margin-top-10">
                                        <label class="control-label col-xs-2 col-md-2 margin-top-10">Birth Year :</label>
                                        <div class="col-xs-5 col-md-5">
                                            <select name="birth" id="birth" class="form-control">
                                                <option value="">Select Year</option>
                                                @for($i=1950;$i<2001;$i++)
                                                <option value="{{$i}}">{{$i}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <label id="birthError" style="vertical-align:bottom" class="validation_error"></label>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group margin-top-10">
                                        <label class="control-label col-xs-2 col-md-2 margin-top-10">Bio :</label>
                                        <div class="col-xs-5 col-md-5">
                                            <textarea class="form-control" name="bio" id="bio" maxlength="250" placeholder="Enter Year Bio"></textarea>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group margin-top-10">
                                        <div class="col-xs-offset-2 col-xs-10">
                                            <input type="button" name="submit" id="submit" class="btn btn-primary" value="Save Profile Info"/>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group" style="margin-top:60px">
                                        <label class="control-label col-xs-2 col-md-2 margin-top-10">Email :</label>
                                        <div class="col-xs-5 col-md-5">
                                            <input type="email" name="email" maxlength="50" id="email" class="form-control" placeholder="Enter Email"/>
                                            <div class="margin-top-10" id="emailConfirmMsg">
                                                
                                            </div>
                                            <div class="margin-top-10">
                                                <label class="emailcode" style="color:#009688;cursor:pointer">Send Verification Code</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3">
                                            <input type="text" name="confirmEmailCode" id="confirmEmailCode" minlength="6" maxlength="6" class="form-control" placeholder="Confirmation Code"/>
                                            <div class="margin-top-10">
                                                Already received a confirmation code?
                                            </div>
                                        </div> 
                                        <div class="col-xs-2 col-md-2">
                                            <button type="button" name="confirmemail" id="confirmemail" onclick="checkEmailConfirmation()" class="btn btn-primary">Confirm</button>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group margin-top-10">
                                        <label class="control-label col-xs-2 col-md-2 margin-top-10">Mobile :</label>
                                        <div class="col-xs-5 col-md-5">
                                            <input type="text" name="mobile" id="mobile" minlength="10" maxlength="10" class="form-control" placeholder="Enter Mobile"/>
                                            <div class="margin-top-10" id="mobileConfirmMsg">
                                                
                                            </div>
                                            <div class="margin-top-10">
                                                <label class="mobilecode" style="color:#009688;cursor:pointer">Send Verification Code</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3">
                                            <input type="text" name="confirmMobileCode" id="confirmMobileCode" minlength="6" maxlength="6" class="form-control" placeholder="Confirmation Code"/>
                                            <div class="margin-top-10">
                                                Already received a confirmation code?
                                            </div>
                                        </div> 
                                        <div class="col-xs-2 col-md-2">
                                            <button type="button" id="confirmmobile" name="confirmmobile" onclick="checkMobileConfirmation()" class="btn btn-primary">Confirm</button>
                                        </div>
                                    </div>
                                 
                               <!-- </form>-->
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="tab-pane" id="preference">
                            <div class="col-md-8">
                                <h3>Preference</h3>
                                <hr/>
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <label class="control-label col-xs-2">Chattiness :</label>
                                        <div class="col-xs-10">
                                            <select class="form-control" name="chat" id="chat">
                                                @for($i=0;$i<count($preference_option);$i++)
                                                    <?php $flag=0;?>
                                                    @if($preference_option[$i]->preference_id==1)
                                                        @for($j=0;$j<count($user_preference);$j++)
                                                            @if($user_preference[$j]->preferenceId==1 && $user_preference[$j]->pref_optionId==$preference_option[$i]->id)
                                                                <option value="{{$preference_option[$i]->id}}" selected>{{$preference_option[$i]->options}}</option>
                                                            <?php $flag=1?>
                                                            @endif
                                                        @endfor
                                                        @if($flag==0)
                                                            <option value="{{$preference_option[$i]->id}}">{{$preference_option[$i]->options}}</option>
                                                        @endif    
                                                    @endif
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group">
                                        <label class="control-label col-xs-2">Smoking :</label>
                                        <div class="col-xs-10">
                                            <select class="form-control" name="smoke" id="smoke">
                                                @for($i=0;$i<count($preference_option);$i++)
                                                    <?php $flag=0;?>
                                                    @if($preference_option[$i]->preference_id==2)
                                                        @for($j=0;$j<count($user_preference);$j++)
                                                            @if($user_preference[$j]->preferenceId==2 && $user_preference[$j]->pref_optionId==$preference_option[$i]->id)
                                                                <option value="{{$preference_option[$i]->id}}" selected>{{$preference_option[$i]->options}}</option>
                                                            <?php $flag=1?>
                                                            @endif
                                                        @endfor
                                                        @if($flag==0)
                                                            <option value="{{$preference_option[$i]->id}}">{{$preference_option[$i]->options}}</option>
                                                        @endif    
                                                    @endif
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group">
                                        <label class="control-label col-xs-2">Pets :</label>
                                        <div class="col-xs-10">
                                            <select class="form-control" name="pets" id="pets">
                                                @for($i=0;$i<count($preference_option);$i++)
                                                    <?php $flag=0;?>
                                                    @if($preference_option[$i]->preference_id==3)
                                                        @for($j=0;$j<count($user_preference);$j++)
                                                            @if($user_preference[$j]->preferenceId==3 && $user_preference[$j]->pref_optionId==$preference_option[$i]->id)
                                                                <option value="{{$preference_option[$i]->id}}" selected>{{$preference_option[$i]->options}}</option>
                                                            <?php $flag=1?>
                                                            @endif
                                                        @endfor
                                                        @if($flag==0)
                                                            <option value="{{$preference_option[$i]->id}}">{{$preference_option[$i]->options}}</option>
                                                        @endif    
                                                    @endif
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group">
                                        <label class="control-label col-xs-2">Music :</label>
                                        <div class="col-xs-10">
                                            <select class="form-control" name="music" id="music">
                                                @for($i=0;$i<count($preference_option);$i++)
                                                    <?php $flag=0;?>
                                                    @if($preference_option[$i]->preference_id==4)
                                                        @for($j=0;$j<count($user_preference);$j++)
                                                            @if($user_preference[$j]->preferenceId==4 && $user_preference[$j]->pref_optionId==$preference_option[$i]->id)
                                                                <option value="{{$preference_option[$i]->id}}" selected>{{$preference_option[$i]->options}}</option>
                                                            <?php $flag=1?>
                                                            @endif
                                                        @endfor
                                                        @if($flag==0)
                                                            <option value="{{$preference_option[$i]->id}}">{{$preference_option[$i]->options}}</option>
                                                        @endif    
                                                    @endif
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group margin-top-10">
                                        <div class="col-xs-offset-2 col-xs-10">
                                            <button type="button" name="preference_btn" id="preference_btn" class="btn btn-primary">Save Preference</button>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane" id="car">
                            <div class="col-md-8">
                                <div class="col-md-12">
                                    <button type="button" data-toggle="modal" data-target="#addCar" class="btn btn-primary addCar">Add Car</button>
                                </div>
                                <div class="clearfix"></div>
                                <hr/>
                                
                                <table id="carTable" class="table table-bordered table-hover" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        
                                    </tbody>
                                </table>
                                
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /tabs -->

            </div>

        </div>
        <!--coupan code start-->
        <div class="tab-pane fade coupan-code" id="coupan_Code">
            <div class="tabbable tabs-left">
                <h3>Balance &#8377; <span id="wallet_amount_balance">0</span></h3>
                <h4>Add Money</h4>

                <div class="row amount-div">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <input type="text" name="ccode" id="ccode" class="form-control input-sm" placeholder="Enter coupan code"/>
                        </div>
                        <div class="col-md-2">
                            <input type="button" id="coupan_apply_btn" name="coupan_apply_btn" class="btn btn-primary" value="Apply"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <!--ends coupan code  ends-->

        <!--withdraw amount start-->
        <div class="tab-pane fade coupan-code" id="withdraw">
            <form name="withdraw_form" id="withdraw_form" method="POST" action="{{route('post.withdraw.amount')}}">
            <div class="tabbable tabs-left">
                <h3>Balance &#8377; <span id="withdraw_amount_balance">0</span></h3>
            
                <div class="row amount-div">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <input type="text" name="account_holder" id="account_holder"  minlength="10" class="form-control input-sm" placeholder="Account Holder Name" required/>
                            <span class="validation_error" style="padding-left:0px" id="account_holder_error"></span>
                        </div>
                    </div>
                    <div class="col-md-12 margin-top-10">
                        <div class="col-md-4">
                            <input type="text" name="bank_name" id="bank_name" class="form-control input-sm" placeholder="Bank Name" required/>
                            <span class="validation_error" style="padding-left:0px" id="bank_name_error"></span>
                        </div>
                    </div>
                    <div class="col-md-12 margin-top-10">
                        <div class="col-md-4">
                            <input type="text" name="account_no" id="account_no" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" class="form-control input-sm" placeholder="Account Number" required/>
                            <span class="validation_error" style="padding-left:0px" id="account_no_error"></span>
                        </div>
                    </div>
                    <div class="col-md-12 margin-top-10">
                        <div class="col-md-4">
                            <input type="text" name="ifsc_code" id="ifsc_code" class="form-control input-sm" placeholder="IFSC Code" required/>
                            <span class="validation_error" style="padding-left:0px" id="ifsc_code_error"></span>
                        </div>
                    </div>
                    <div class="col-md-12 margin-top-10">
                        <div class="col-md-4">
                            <input type="text" name="withdraw_amount" id="withdraw_amount" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" class="form-control input-sm" placeholder="Amount to be withdraw(in multiple of 100)" required/>
                            <span class="validation_error" style="padding-left:0px" id="withdraw_amount_error"></span>
                        </div>
                    </div>
                    <div class="col-md-12 margin-top-10">
                        <span style="color:red">Note : You can withdraw the amount with the multiple of 100.</span>
                    </div>
                    <div class="col-md-12 margin-top-20">
                        <div class="col-md-2" style="padding:0px">
                            <input type="submit" id="withdraw_btn" name="withdraw_btn" class="btn btn-primary" value="Withdraw"/>
                        </div>
                    </div>

                </div>
            </div>
            </form>
            <div class="clearfix"></div>
        </div>
        <!--withdraw amount code ends -->
    </div>
    <div class="clearfix"></div>
</div>
<!--add car modal start-->
<div class="modal fade" tabindex="-1" id="addCar">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form name="AddCarForm" id="AddCarForm" enctype= "multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">X</button>
                    <h4 class="title">Add Car</h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-6 margin-top-10">
                        <label>Vehicle Type</label>
                        <select class="form-control" name="vehicle_type" id="vehicle_type">
                            <option value="2">four wheeler</option>
                            <option value="1">two wheeler</option>
                            
                        </select>
                        <label id="vehicleTypeError" class="validation_error PD0"></label>
                    </div>

                    <div class="col-md-6 margin-top-10">
                        <label>Make</label>
                        <input type="text" class="form-control" name="carMake" id="carMake" placeholder="Enter Brand Name" required/>
                        <label id="carMakeError" class="validation_error PD0"></label>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-md-6 margin-top-10">
                        <label>Model</label>
                        <input type="text" class="form-control" name="carModel" id="carModel" placeholder="Enter Model" required/>
                        <label id="carModelError" class="validation_error PD0"></label>
                    </div>

                    <div class="col-md-6 margin-top-10">
                        <label>Comfort</label>
                        <select class="form-control" id="carComfort" name="carComfort">
                            @for($i=0;$i<count($comfort);$i++)
                                <option value="{{$comfort[$i]->id}}">{{$comfort[$i]->name}}</option>
                            @endfor
                        </select>
                        <label id="carComfortError" class="validation_error PD0"></label>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-md-6 margin-top-10">
                        <label>Color</label>
                        <select class="form-control" id="carColour" name="carColour">
                            @for($i=0;$i<count($color);$i++)
                                <option value="{{$color[$i]->id}}">{{$color[$i]->color}}</option>
                            @endfor
                        </select>
                        <label id="carColourError" class="validation_error PD0"></label>
                    </div>

                    <div class="col-md-6 margin-top-10">
                        <label>No. of Seat</label>
                        <select class="form-control" name="carSeat" id="carSeat">
                            @for($i=1;$i<8;$i++)
                                <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                        <label id="carSeat" class="validation_error PD0"></label>
                    </div>
                    <div class="clearfix"></div>
                    
                   
                    <div class="col-md-6 margin-top-10">
                        <label>Car Image</label>
                        <input type="file" name="carImage" id="carImage" class="form-control" accept="image/gif, image/jpeg, image/png, image/gif" />
                        <label id="carImage" class="validation_error PD0"></label>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <div class="margin-top-10">
                        <div class="col-xs-offset-2 col-xs-10">
                            <button type="button" name="addCarBtn" id="addCarBtn" class="btn btn-primary">Save Car</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--add car modal ends-->

<!--edit car modal start-->
<div class="modal fade" tabindex="-1" id="editCar">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form name="EditCarForm" id="EditCarForm" enctype= "multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">X</button>
                    <h4 class="title">Update Car</h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-6 margin-top-10">
                        <label>Vehicle Type</label>
                        <select class="form-control" name="editVehicleType" id="editVehicleType">
                            <option value="2">four wheeler</option>
                            <option value="1">two wheeler</option>
                        </select>
                        <label id="editVehicleTypeError" class="validation_error PD0"></label>
                    </div>

                    <div class="col-md-6 margin-top-10">
                        <label>Make</label>
                        <input type="text" class="form-control" name="editCarMake" id="editCarMake" placeholder="Enter Brand Name" required/>
                        <label id="editCarMakeError" class="validation_error PD0"></label>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-md-6 margin-top-10">
                        <label>Model</label>
                        <input type="text" class="form-control" name="editCarModel" id="editCarModel" placeholder="Enter Model" required/>
                        <label id="editCarModelError" class="validation_error PD0"></label>
                    </div>

                    <div class="col-md-6 margin-top-10">
                        <label>Comfort</label>
                        <select class="form-control" id="editCarComfort" name="editCarComfort">
                            @for($i=0;$i<count($comfort);$i++)
                                <option value="{{$comfort[$i]->id}}">{{$comfort[$i]->name}}</option>
                            @endfor
                        </select>
                        <label id="editCarComfortError" class="validation_error PD0"></label>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-md-6 margin-top-10">
                        <label>Color</label>
                        <select class="form-control" id="editCarColour" name="editCarColour">
                            @for($i=0;$i<count($color);$i++)
                                <option value="{{$color[$i]->id}}">{{$color[$i]->color}}</option>
                            @endfor
                        </select>
                        <label id="editCarColourError" class="validation_error PD0"></label>
                    </div>

                    <div class="col-md-6 margin-top-10">
                        <label>No. of Seat</label>
                        <select class="form-control" name="editCarSeat" id="editCarSeat">
                            @for($i=1;$i<8;$i++)
                                <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                        <label id="editCarSeatError" class="validation_error PD0"></label>
                    </div>
                    <div class="clearfix"></div>
                    <input type="hidden" name="editcarid" id="editcarid"/> 
                   
                    <div class="col-md-6 margin-top-10">
                        <label>Car Image</label>
                        <input type="file" name="editCarImage" id="editCarImage" class="form-control" accept="image/gif, image/jpeg, image/png, image/gif" />
                        <label id="editCarImageError" class="validation_error PD0"></label>
                    </div>
                    <div class="col-md-6 margin-top-10">
                        <img src="/images/car_default.png" width="100" height="100" id="edit_car_image" name="edit_car_image"/>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <div class="margin-top-10">
                        <div class="col-xs-offset-2 col-xs-10">
                            <button type="button" name="editCarBtn" id="editCarBtn" class="btn btn-primary">Update Car</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--edit car modal ends-->
@endsection
@section("js")
    <script type="text/javascript" src="{{URL::asset('js/jquery.toaster.js')}}"></script>
    
    <script type="text/javascript">
    var oTable,ratingGivenTable,ratingReceivedTable,paidTransactionTable,earnTransactionTable,bookingHistoryTable,ridebookedTable;
    var chatUser=[];
    var lpath='{{getenv('APP_LOCAL_URL')}}';
        $(document).ready(function(){
            
            paidTans();
            earnTans();
            ridebooked();
            bookedHistory();
            $(".bookingHistory").click(function(){
                bookingHistoryTable.fnDraw();
            });
            $(".ridebooked").click(function(){
                ridebookedTable.fnDraw();
            });

            $(".paidTransaction").click(function(){
                paidTransactionTable.fnDraw();
            });
            $(".earnTransaction").click(function(){
                earnTransactionTable.fnDraw();
            });

            ratingReceivedTable=$("#ratingReceivedTable").dataTable({
                oLanguage: {
                          sProcessing: "<img src='/images/load.gif'>"
                },
                searchHighlight: true,
                /*"language": {
                "url": "dataTables.gujarati.lang"
                },
                */
                "pagingType": "full_numbers",
                "bProcessing": true,
                "bServerSide": true,
                "sServerMethod": "GET",
                "sAjaxSource": '{!! route("get.rating.received.list") !!}',
                "iDisplayLength": 10,
                "searchHighlight": true,
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                /*"aaSorting": [[1, 'asc']],*/
                "aoColumns": [
                    {"bVisible": true, "bSearchable": true, "bSortable": false}
                ]
            });
            //rating given data fetch
            ratingGivenTable=$("#ratingGivenTable").dataTable({
                oLanguage: {
                          sProcessing: "<img src='/images/load.gif'>"
                },
                searchHighlight: true,
                /*"language": {
                "url": "dataTables.gujarati.lang"
                },
                */
                "pagingType": "full_numbers",
                "bProcessing": true,
                "bServerSide": true,
                "sServerMethod": "GET",
                "sAjaxSource": '{!! route("get.rating.given.list") !!}',
                "iDisplayLength": 10,
                "searchHighlight": true,
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                /*"aaSorting": [[1, 'asc']],*/
                "aoColumns": [
                    {"bVisible": true, "bSearchable": true, "bSortable": false}
                ]
            });

            //car table data fetch
            oTable=$('#carTable').dataTable({
                oLanguage: {
                          sProcessing: "<img src='/images/load.gif'>"
                },
                searchHighlight: true,
                /*"language": {
                "url": "dataTables.gujarati.lang"
                },
                */
                "pagingType": "full_numbers",
                "bProcessing": true,
                "bServerSide": true,
                "sServerMethod": "GET",
                "sAjaxSource": '{!! route("get.car.list") !!}',
                "iDisplayLength": 10,
                "fnServerParams": function(aoData) {
                    fromDate = '2016-04-02';
                    toDate = '2017-04-02';
                    city = ['Ahmedabad','Surat'];
                    aoData.push({"name": "fromDate", "value": fromDate},
                    {"name": "toDate", "value": toDate},
                    {"name": "city", "value": city});
                },
                "searchHighlight": true,
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                /*"aaSorting": [[1, 'asc']],*/
                "aoColumns": [
                    {"bVisible": true, "bSearchable": true, "bSortable": false}
                ]
            });
            $(".profile").click(function(){
                smw.getUserProfileDetails(<?php echo session('userId')?>);
            });

            $("#submit").click(function(){
                smw.formSubmit(<?php echo session('userId')?>);
            });

            $("#preference_btn").click(function(){
                smw.saveUserPreferences(<?php echo session('userId')?>);
            });

            $("#imagesubmitform").submit(function(e){
                e.preventDefault();
                var formData=new FormData(),
                    files    = $('#profile_pic').get(0).files;
                    $.each(files, function(i, file) {
                        formData.append("pics_"  + i, file); 
                    });
                var jsondata={"type":"POST","data":formData,"url":"/imagesubmit"};
                smw.ajaximagecall(jsondata,smw.responsUploadImage);  
            });
            //upload licence image
            $("#licencesubmitform").submit(function(e){
                e.preventDefault();
                var formData=new FormData(),
                    files    = $('#licence_pic').get(0).files;
                    $.each(files, function(i, file) {
                        formData.append("pics_"  + i, file); 
                    });
                var jsondata={"type":"POST","data":formData,"url":"/licencesubmit"};
                smw.ajaximagecall(jsondata,smw.responsUploadLicenceImage);  
            });

            //licence image ends
            $(".emailcode").click(function(){
                var email=$("#email").val();
                if(email.length>0)
                {
                    var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
                    if (filter.test(email)) 
                    {
                        //call ajax for confirmation
                        smw.sendEmailCode(<?php echo session('userId')?>);
                    }
                    else 
                    {
                        $.toaster({ priority : 'danger', title : 'Title', message : 'Please Enter Correct Email'});
                        return false;
                    } 
                }
                else
                {
                    $.toaster({ priority : 'danger', title : 'Title', message : 'Please Enter Email'});
                    return false;
                }
            });

            $(".mobilecode").click(function(){
                var mobileval=$("#mobile").val();
            
                if(mobileval.length!=10)//check mobile length is 10 digit or not
                {
                    $.toaster({ priority : 'danger', title : 'Title', message : 'Please Enter 10 digit Mobile Number'});
                }
                else 
                {
                    //if mobile is 10 digit then
                    if(!isNaN(parseInt(mobileval)))
                    {
                        smw.sendMobileCode(<?php echo session('userId')?>);
                    }
                    else
                    {
                        //mobile number is not numeric
                        $.toaster({ priority : 'danger', title : 'Title', message : 'Please Enter digits only in mobile number'});
                    }
                }
            });

            $("#addCarBtn").click(function(){
                smw.addCar(<?php echo session('userId')?>);
            });

            $(".addCar").click(function(){
                $("#AddCarForm")[0].reset();
                $(".validation_error").each(function(){
                    $(this).text('');
                });
            });

            $('body').on('click','.editCar',function(){

                $("#EditCarForm")[0].reset();
                $(".validation_error").each(function(){
                    $(this).text('');
                });
                var editId=$(this).attr("id").split("_");
                smw.editCar(editId[1],<?php echo session('userId')?>);
            });

            $("#editCarBtn").click(function(){
                smw.updateCar($("#editcarid").val(),<?php echo session('userId')?>);
            });

            $('body').on('click','.car_delete',function(){

                var id=$(this).attr("id").split("_");
                
                var r = confirm("Are you sure you want to delete this car!");
                if(r==true)
                {
                    smw.deleteCar(id[1],<?php echo session('userId')?>);
                }
            });

            $(".ratingMain").click(function(){
                smw.getExchangeRating();
            });

            $(".ratingsReceived").click(function(){
                ratingReceivedTable.fnDraw();
            });

            $(".ratingsGiven").click(function(){
                ratingGivenTable.fnDraw();
            });
            $("#ratingSubmitBtn").click(function(){
                if($("#ratingUser").val()=="")
                {
                    $.toaster({ priority : 'danger', title : 'Title', message : 'please select User'});   
                }
                else if ($('input[name=rating]:checked').length > 0)
                {
                    var rate=$('input[name=rating]:checked').val();
                    var param={"touser":$("#ratingUser").val(),"rating":rate,"_token":$("input[name=_token]").val()};
                    var data={"touser":$("#ratingUser").val(),"rating":rate};
                    var jsondata={"type":"POST","data":data,"url":"/ratingExchange"};
                    smw.ajaxcall(jsondata,smw.responseRatingExchange,param);
                }
                else
                {
                    $.toaster({ priority : 'danger', title : 'Title', message : 'please select rating'});
                }
            });
            //chat user push to array as object
           
            @if(count($msgUser)>0)
                @for($i=0;$i<count($msgUser);$i++)
                    var onj={"name":"{{$msgUser[$i]->username}}","profilepic":"{{$msgUser[$i]->profile_pic}}","id":"{{$msgUser[$i]->id}}","flag":false};
                    chatUser.push(onj);
                @endfor
            @endif

            //if chat user search
            $("#searchUser").keyup(function(){
                if($("#searchUser").val().length>0)
                {
                    var u=$("#searchUser").val();
                    var search = new RegExp("(^)"+u, "i");
                    var found_names = $.grep(chatUser, function(v) {
                        return search.test(v.name);
                    });
                    //console.log(found_names);
                    $(".chat-list").empty();
                    if(found_names.length>0)
                    {  
                        var html="";
                        for(var i=0;i<found_names.length;i++)
                        {
                            var path="";
                            if(found_names[i]['profilepic']=='default.png')
                            {
                                path=lpath+"/images/default.png";
                            }
                            else
                            {
                                path=lpath+"/images/profile/"+found_names[i]['id']+"/"+found_names[i]['profilepic'];
                            }
                            if(found_names[i]['flag']==false)
                            {
                                html+='<a class="user-chat-messages" style="background:#fff" data-id="'+found_names[i]['id']+'" data-pic="'+path+'" data-flag="false">';    
                            }
                            else
                            {
                                html+='<a class="user-chat-messages" style="background:#eee" data-id="'+found_names[i]['id']+'" data-pic="'+path+'" data-flag="false">';
                            }
                            html+='<div class="avatar">';
                            html+='<img class="img-responsive" src="'+path+'" />';
                            html+='</div>';
                            html+='<div class="name-message">';
                            html+='<h5>'+found_names[i]['name']+'</h5>';
                            html+='<p></p>';
                            html+='</div>';
                            html+='</a>';

                        }
                        $(".chat-list").html(html);
                    }
                }
                else
                {
                    //bind all chat user
                    $(".chat-list").empty();
                    if(chatUser.length>0)
                    {
                        var html="";
                        for(var i=0;i<chatUser.length;i++)
                        {
                            var path="";
                            if(chatUser[i]['profilepic']=='default.png')
                            {
                                path=lpath+"/images/default.png";
                            }
                            else
                            {
                                path=lpath+"/images/profile/"+chatUser[i]['id']+"/"+chatUser[i]['profilepic'];
                            }
                            if(chatUser[i]['flag']==false)
                            {
                                html+='<a class="user-chat-messages" style="background:#fff" data-id="'+chatUser[i]['id']+'" data-pic="'+path+'" data-flag="false">';
                            }
                            else
                            {
                                html+='<a class="user-chat-messages" style="background:#eee" data-id="'+chatUser[i]['id']+'" data-pic="'+path+'" data-flag="false">';
                            }
                            
                            html+='<div class="avatar">';
                            html+='<img class="img-responsive" src="'+path+'" />';
                            html+='</div>';
                            html+='<div class="name-message">';
                            html+='<h5>'+chatUser[i]['name']+'</h5>';
                            html+='<p></p>';
                            html+='</div>';
                            html+='</a>';
                        }
                        $(".chat-list").html(html);
                    }

                }
            });
            //coupan code click
            $(".coupan_Code").click(function(){
                //get wallet amount
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
                            $("#wallet_amount_balance").text(response);
                        }
                        else
                        {
                            $("#walletamount").text(0);
                            $("#wallet_amount_balance").text(0);
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
                //get wallet amount ends
            });
            //coupan code click ends

            $("#coupan_apply_btn").click(function()
            {
                if($("#ccode").val()=="")
                {
                    $.toaster({ priority : 'danger', title : 'Title', message : 'Please Enter Coupan code'});
                }
                else
                {
                    $.ajax({
                        async:false,
                        headers: { 'X-CSRF-Token' : $("#token").val() } ,
                        type:'POST',
                        url:'{{route('post.coupan.code')}}',
                        data:{"code":$("#ccode").val()},
                    beforeSend:function(){
                        
                    },
                    success:function(response){
                        $("#walletamount").text(response['data']['amount']);
                        $("#wallet_amount_balance").text(response['data']['amount']);
                        $("#ccode").val('');
                        $.toaster({ priority : 'success', title : 'Title', message :response['message']});
                    },
                    error:function(response)
                    {
                        var a=$.parseJSON(response['responseText']);
                        $.toaster({ priority : 'danger', title : 'Title', message :a['message']});
                    },
                    complete:function(){
                        //removeOverlay();
                    }
                    });
                }
            });
        
            //withdraw amount click
            $(".withdraw").click(function(){
                getAmount();
                var a=$("#walletamount").text();
                $("#withdraw_amount_balance").text(a);
            });
        });
        function bookedHistory()
        {
            bookingHistoryTable=$("#bookingHistoryTable").dataTable({
                    oLanguage: {
                              sProcessing: "<img src='/images/load.gif'>"
                    },
                    searchHighlight: true,
                    /*"language": {
                    "url": "dataTables.gujarati.lang"
                    },
                    */
                    "pagingType": "full_numbers",
                    "bProcessing": true,
                    "bServerSide": true,
                    "sServerMethod": "GET",
                    "sAjaxSource": '{!! route("get.paid.transaction") !!}',
                    "iDisplayLength": 10,
                    "searchHighlight": true,
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    /*"aaSorting": [[1, 'asc']],*/
                    "aoColumns": [
                        {"bVisible": true, "bSearchable": true, "bSortable": false}
                    ]
                });
        }
        function ridebooked()
        {
            ridebookedTable=$("#ridebookedTable").dataTable({
                    oLanguage: {
                              sProcessing: "<img src='/images/load.gif'>"
                    },
                    searchHighlight: true,
                    /*"language": {
                    "url": "dataTables.gujarati.lang"
                    },
                    */
                    "pagingType": "full_numbers",
                    "bProcessing": true,
                    "bServerSide": true,
                    "sServerMethod": "GET",
                    "sAjaxSource": '{!! route("get.ridebooked.history") !!}',
                    "iDisplayLength": 10,
                    "searchHighlight": true,
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    /*"aaSorting": [[1, 'asc']],*/
                    "aoColumns": [
                        {"bVisible": true, "bSearchable": true, "bSortable": false}
                    ]
            });
        }
        function paidTans()
        {
            paidTransactionTable=$("#paidTransactionTable").dataTable({
                    oLanguage: {
                              sProcessing: "<img src='/images/load.gif'>"
                    },
                    searchHighlight: true,
                    /*"language": {
                    "url": "dataTables.gujarati.lang"
                    },
                    */
                    "pagingType": "full_numbers",
                    "bProcessing": true,
                    "bServerSide": true,
                    "sServerMethod": "GET",
                    "sAjaxSource": '{!! route("get.paid.transaction") !!}',
                    "iDisplayLength": 10,
                    "searchHighlight": true,
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    /*"aaSorting": [[1, 'asc']],*/
                    "aoColumns": [
                        {"bVisible": true, "bSearchable": true, "bSortable": false}
                    ]
                });
        }
        function earnTans()
        {
            earnTransactionTable=$("#earnTransactionTable").dataTable({
                    oLanguage: {
                              sProcessing: "<img src='/images/load.gif'>"
                    },
                    searchHighlight: true,
                    /*"language": {
                    "url": "dataTables.gujarati.lang"
                    },
                    */
                    "pagingType": "full_numbers",
                    "bProcessing": true,
                    "bServerSide": true,
                    "sServerMethod": "GET",
                    "sAjaxSource": '{!! route("get.earn.transaction") !!}',
                    "iDisplayLength": 10,
                    "searchHighlight": true,
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    /*"aaSorting": [[1, 'asc']],*/
                    "aoColumns": [
                        {"bVisible": true, "bSearchable": true, "bSortable": false}
                    ]
                });
        }
        function checkMobileConfirmation()
        {
            var mobileval=$("#mobile").val();
            
            if(mobileval.length!=10)//check mobile length is 10 digit or not
            {
                $.toaster({ priority : 'danger', title : 'Title', message : 'Please Enter 10 digit Mobile Number'});
            }
            else 
            {
                //if mobile is 10 digit then
                if(!isNaN(parseInt(mobileval)))
                {
                    //if mobile number is numeric
                    var confirmCode=$("#confirmMobileCode").val();
                   
                    if(confirmCode.length!=6)
                    {
                        //confirmation code is not 6 digit
                        $.toaster({ priority : 'danger', title : 'Title', message : 'Please Enter 6 digit code'});
                    }
                    else
                    {
                        //call ajax for confirmation
                        smw.mobileConfirmation(<?php echo session('userId')?>);
                    }
                }
                else
                {
                    //mobile number is not numeric
                    $.toaster({ priority : 'danger', title : 'Title', message : 'Please Enter digits only in mobile number'});
                }
            }
        }
        function checkEmailConfirmation() 
        {
            var email=$("#email").val();
            if(email.length>0)
            {
                var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
                if (filter.test(email)) 
                {
                    
                    var confirmCode=$("#confirmEmailCode").val();
                    if(confirmCode.length!=6)
                    {
                        //confirmation code is not 6 digit
                        $.toaster({ priority : 'danger', title : 'Title', message : 'Please Enter 6 digit confirmation code'});
                        return false;
                    }
                    else
                    {
                        //call ajax for confirmation
                        smw.emailConfirmation(<?php echo session('userId')?>);
                    }
                }
                else 
                {
                   $.toaster({ priority : 'danger', title : 'Title', message : 'Please Enter Correct Email'});
                    return false;
                } 
            }
            else
            {
                $.toaster({ priority : 'danger', title : 'Title', message : 'Please Enter Email'});
                return false;
            }
        }
    </script>
    <script type="text/javascript" src="{{URL::asset('js/dashboard.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/chat.js')}}"></script>
@endsection