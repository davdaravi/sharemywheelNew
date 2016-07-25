@extends("master")

@section("head")
    <title>Share My Wheel - car owner</title>
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
    <style type="text/css"> 
    div.stars,div.stars1
    {
        width: 92px;
        display: inline-block;
    }
    input.star,input.star1 { display: none; }
    label.star,label.star1 {
        float: right;
        padding: 0px 2px;
        font-size: 15px;
        color: #444;
        transition: all .2s;
        margin: 0px;
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
    </style>
@endsection
@section("nav")
    @include("includes.afterLoginSidebar")
@endsection
@section("content")
 <!-- filter and Body -->
<div class="container sharingContentBody">
    <div class="col-md-12">
        <div class="col-md-8 sharingRideGrid">
            <!-- <div class="col-md-12 margin-top-15">
                <div><a href="#"><i class="zmdi zmdi-arrow-left"></i> Back to Search Result</a></div><hr/>
            </div> -->
            <div class="col-md-12">
                <br/><div><a href="{{route('get.ride.list')}}"><i class="zmdi zmdi-arrow-left"></i> Back to Search Result</a></div><br/>
            </div>
            <div class="col-md-12">
                <div class="col-md-12 detailBlock">
                    <table class="table table-hover margin-top-10 table-td-border-none">
                        <tbody>
                            <tr>
                                <td class="col-md-2 col-sm-2 col-xs-2">
                                    <?php
                                        if($userDetail[0]->profile_pic=='default.png')
                                        {
                                            $path="/images/default.png";
                                        }
                                        else
                                        {   
                                            $path="/images/profile/".$userDetail[0]->userId."/".$userDetail[0]->profile_pic;
                                        }
                                    ?>
                                    <img src="<?php echo asset($path)?>" width="80px" height="80px">
                                </td>
                                <td class="col-md-10 col-sm-10 col-xs-10">
                                    <h4>{{ucwords($userDetail[0]->first_name." ".$userDetail[0]->last_name)}} <small><br>@if($userDetail[0]->birthdate=="")@else{{date_diff(date_create($userDetail[0]->birthdate), date_create('today'))->y." Years old"}}@endif</small></h4>
                                    <div class="stars1" style="padding:0px;margin:0px">
                                        @if($userDetail[0]->rating==5)
                                        <input type="radio" class="star1 star-5" name="rating" value="5" checked="checked"/><label class="star1 star-5"></label>                        
                                        <input type="radio" class="star1 star-4" name="rating" value="4"/><label class="star1 star-4"></label>
                                        <input type="radio" class="star1 star-3" name="rating" value="3"/><label class="star1 star-3"></label>
                                        <input type="radio" class="star1 star-2" name="rating" value="2"/><label class="star1 star-2"></label>
                                        <input type="radio" class="star1 star-1" name="rating" value="1"/><label class="star1 star-1"></label>
                                        @elseif($userDetail[0]->rating==4)
                                        <input type="radio" class="star1 star-5" name="rating" value="5"/><label class="star1 star-5"></label>                        
                                        <input type="radio" class="star1 star-4" name="rating" value="4" checked="checked"/><label class="star1 star-4"></label>
                                        <input type="radio" class="star1 star-3" name="rating" value="3"/><label class="star1 star-3"></label>
                                        <input type="radio" class="star1 star-2" name="rating" value="2"/><label class="star1 star-2"></label>
                                        <input type="radio" class="star1 star-1" name="rating" value="1"/><label class="star1 star-1"></label>
                                        @elseif($userDetail[0]->rating==3)
                                        <input type="radio" class="star1 star-5" name="rating" value="5"/><label class="star1 star-5"></label>                        
                                        <input type="radio" class="star1 star-4" name="rating" value="4"/><label class="star1 star-4"></label>
                                        <input type="radio" class="star1 star-3" name="rating" value="3" checked="checked"/><label class="star1 star-3"></label>
                                        <input type="radio" class="star1 star-2" name="rating" value="2"/><label class="star1 star-2"></label>
                                        <input type="radio" class="star1 star-1" name="rating" value="1"/><label class="star1 star-1"></label>
                                        @elseif($userDetail[0]->rating==2)
                                        <input type="radio" class="star1 star-5" name="rating" value="5"/><label class="star1 star-5"></label>                        
                                        <input type="radio" class="star1 star-4" name="rating" value="4"/><label class="star1 star-4"></label>
                                        <input type="radio" class="star1 star-3" name="rating" value="3"/><label class="star1 star-3"></label>
                                        <input type="radio" class="star1 star-2" name="rating" value="2" checked="checked"/><label class="star1 star-2"></label>
                                        <input type="radio" class="star1 star-1" name="rating" value="1"/><label class="star1 star-1"></label>
                                        @elseif($userDetail[0]->rating==1)
                                        <input type="radio" class="star1 star-5" name="rating" value="5"/><label class="star1 star-5"></label>                        
                                        <input type="radio" class="star1 star-4" name="rating" value="4"/><label class="star1 star-4"></label>
                                        <input type="radio" class="star1 star-3" name="rating" value="3"/><label class="star1 star-3"></label>
                                        <input type="radio" class="star1 star-2" name="rating" value="2"/><label class="star1 star-2"></label>
                                        <input type="radio" class="star1 star-1" name="rating" value="1" checked="checked"/><label class="star1 star-1"></label>
                                        @else
                                        <input type="radio" class="star1 star-5" name="rating" value="5"/><label class="star1 star-5"></label>                        
                                        <input type="radio" class="star1 star-4" name="rating" value="4"/><label class="star1 star-4"></label>
                                        <input type="radio" class="star1 star-3" name="rating" value="3"/><label class="star1 star-3"></label>
                                        <input type="radio" class="star1 star-2" name="rating" value="2"/><label class="star1 star-2"></label>
                                        <input type="radio" class="star1 star-1" name="rating" value="1"/><label class="star1 star-1"></label>
                                        @endif
                                        <div class="clearfix"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="cabOwnerNotes">
                        <div class="col-md-1">
                            <i class="zmdi zmdi-pin-account zmdi-hc-2x margin-top-20 text-center"></i>
                        </div>
                        <div class="col-md-10">
                            <h4>Why travel with me ?</h4>
                            <h5>
                            @if($userDetail[0]->description!="")
                                {{$userDetail[0]->description}}
                            @else
                                -
                            @endif
                            </h5>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>              
            </div>
        </div>
        <div class="col-md-4 sharingRideFilter">
            <div class="col-md-12 margin-top-25">
                <h3>Verification</h3><hr/>
                @if($userDetail[0]->isverifyphone==1)
                <h5><i class="zmdi zmdi-check-circle zmdi-hc-fw zmdi-green zmdi-hc-lg"></i> Phone Verified</h5>
                @else
                <h5><i class="zmdi zmdi-close-circle zmdi-hc-fw zmdi-red zmdi-hc-lg"></i> Phone Not Verified</h5>
                @endif
                @if($userDetail[0]->isverifyemail==1)
                <h5><i class="zmdi zmdi-check-circle zmdi-hc-fw zmdi-green zmdi-hc-lg"></i> Mobile Verified</h5>
                @else
                <h5><i class="zmdi zmdi-close-circle zmdi-hc-fw zmdi-red zmdi-hc-lg"></i> Mobile Not Verified</h5>
                @endif
            </div>
            <div class="clearfix"></div><hr/>

            <div class="col-md-12 margin-top-10">
                <h4><b>Member activity</b></h4>
                <h5>{{$totalRide}} ride offered</h5>
                <h5>Last online: {{$loginDate}}</h5>
                <h5>Member since: {{date("d-m-Y",strtotime($userDetail[0]->created_at))}}</h5>
            </div>
            <div class="clearfix"></div><hr/>

            <div class="col-md-12" style="margin:0px">
                <div class="col-md-12" style="padding:0px">
                    <h4>Car</h4>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4 col-sm-2 col-xs-3" style="margin:0px;padding:0px">
                <?php
                if($carDetails[0]->vehical_pic=='car_default.png')
                {
                    $gg='/images/car_default.png';
                }
                else
                {
                    $gg='/images/cars/'.$carDetails[0]->userId.'/'.$carDetails[0]->vehical_pic;
                }
                ?>
                <img src="{{asset($gg)}}" height="80" width="80"/>
                </div>
                <div class="col-md-8 col-sm-10 col-xs-9">
                    <div>{{$carDetails[0]->car_make." ".$carDetails[0]->car_model}}</div>
                    <div>Color : <span>{{$carDetails[0]->color}}</span></div>
                    <div>Comfort : <span>{{$carDetails[0]->comfort}}</span></div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div><hr/>                   
        </div>
    </div>
</div>

@endsection
@section("js")
    
@endsection