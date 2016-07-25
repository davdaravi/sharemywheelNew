@extends("master")

@section("head")
    <title>Share My Wheel - ride details</title>
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
    </style>
@endsection
@section("nav")
    @include("includes.afterLoginSidebar")
@endsection
@section("content")
<!-- filter and Body -->
<div class="container sharingContentBody">
    <div class="col-md-12">
        @if(count($rideDetail)>0)
        <div class="col-md-8 sharingRideGrid">
            <div class="col-md-12 margin-top-15">
                <div><a href="{{route('get.dashboard')}}"><i class="zmdi zmdi-arrow-left"></i> Back to Dashboard</a></div><hr/>
                <h4 class="margin-top-10">
                    <span>{{$rideDetail[0]->departureOriginal}}</span> <i class="zmdi zmdi-arrow-right"></i> <span>{{$rideDetail[0]->arrivalOriginal}}</span>
                </h4><hr/>
            </div>

            <div class="col-md-12">
                <div class="col-md-12 detailBlock">
                    <table class="table table-hover table-bordered margin-top-10">
                        <tbody>
                            <tr>
                                <th>Departure point</th>
                                <td><i class="zmdi zmdi-pin zmdi-green"></i> {{$rideDetail[0]->departureOriginal}}</td>
                            </tr>

                            <tr>
                                <th>Arrival point</th>
                                <td><i class="zmdi zmdi-pin zmdi-red"></i> {{$rideDetail[0]->arrivalOriginal}}</td>
                            </tr>
                             <tr>
                                <th>WayPoints</th>
                                <?php
                                $point="";
                                if(count($waypoint)>0)
                                {
                                    for($i=0;$i<count($waypoint);$i++)
                                    {
                                        if($i==0)
                                        {
                                            $point=$waypoint[$i]->cityOriginal;
                                        }
                                        else
                                        {
                                            $point.=" | ".$waypoint[$i]->cityOriginal;
                                        }
                                    }
                                }
                                else
                                {
                                    $point="-";
                                }
                                ?>
                                <td><?php echo $point?></td>
                            </tr>
                            <tr>
                                <th>Departure date</th>
                                <td><i class="zmdi zmdi-calendar-note"></i> <?php echo date("l d F Y - H:i",strtotime($rideDetail[0]->departure_date))?></td>
                            </tr>
                            <?php
                            if($rideDetail[0]->is_round_trip==1 && $rideDetail[0]->isDaily==0 && $rideDetail[0]->return_date>'1971-01-01') 
                            {?>
                            <tr>
                                <th>Return date</th>
                                <td><i class="zmdi zmdi-calendar-note"></i> 
                                <?php     
                                    echo date("l d F Y - H:i",strtotime($rideDetail[0]->return_date));    
                                ?>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>   
                            @if($rideDetail[0]->is_round_trip==1 && $rideDetail[0]->isDaily==1)
                            <tr>
                                <th>Return Time</th>
                                <td><i class="zmdi zmdi-calendar-note"></i> <?php echo date("H:i:s",strtotime($rideDetail[0]->return_time))?></td>
                            </tr>
                            @endif
                            <tr>
                                <th>Details</th>
                                <td>
                                    <span><i class="zmdi zmdi-alarm-check"></i> {{$rideDetail[0]->leave}}</span>
                                </td>
                            </tr>
                            <tr>
                                <th></th>
                                <td><span><i class="zmdi zmdi-shopping-basket"></i> {{$rideDetail[0]->luggage}} (travel bag)</span></td>
                            </tr>
                            <tr>
                                <th></th>
                                <td><span><i class="zmdi zmdi-crop"></i> {{$rideDetail[0]->detour}}</span></td>
                            </tr>
                            @if(count($ridePreference)>0)
                            <tr>
                                <th>Chattiness</th>
                                <td>{{$ridePreference[0]->options}}</td>
                            </tr>
                            <tr>
                                <th>Smoking</th>
                                <td>{{$ridePreference[1]->options}}</td>
                            </tr>
                            <tr>
                                <th>Pets</th>
                                <td>{{$ridePreference[2]->options}}</td>
                            </tr>
                            <tr>    
                                <th>Music</th>
                                <td>{{$ridePreference[3]->options}}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    @if(count($bookedUserDetail)>0)
                    @for($i=0;$i<count($bookedUserDetail);$i++)
                    <div class="panel panel-default" style="margin-bottom:10px">
                        <div class="panel-heading">
                            <h5>
                                <span>{{$bookedUserDetail[$i]->source}}</span>&nbsp;<i class="zmdi zmdi-arrow-right"></i>&nbsp;
                                <span>{{$bookedUserDetail[$i]->destination}}</span>
                            </h5>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div>
                                    <i class="zmdi zmdi-calendar-alt zmdi-hc-lg"></i>&nbsp;&nbsp;<span><?php echo date("l d F Y - h:i A",strtotime($bookedUserDetail[$i]->created_date))?></span>
                                </div>
                                <div class="margin-top-10">
                                    <?php
                                    if($bookedUserDetail[$i]->profile_pic=='default.png')
                                    {
                                        $path='/images/default.png';
                                    }
                                    else
                                    {
                                        $path='/images/profile/'.$bookedUserDetail[$i]->book_userId.'/'.$bookedUserDetail[$i]->profile_pic;
                                    }
                                    ?>
                                    <img src="{{asset($path)}}" height="50" width="50" style="border-radius:50%;border:1px solid #eee;box-shadow:0px 1px 1px 1px #ccc"/>&nbsp;&nbsp;{{$bookedUserDetail[$i]->first_name." ".$bookedUserDetail[$i]->last_name}}
                                </div>
                                <div class="margin-top-10">
                                    <div>
                                        <label>Rating:</label>
                                    </div>
                                    <div style="color:#999" class="stars1">
                                        <?php
                                        $rating=$bookedUserDetail[$i]->rating;                                   
                                        if($rating==5)
                                        {?>
                                            <input type="radio" class="star1 star1-5" value="5" checked disabled="disabled"/><label class="star1 star1-5"></label>
                                        <?php
                                        }
                                        else
                                        {?>
                                            <input type="radio" class="star1 star1-5" value="5" disabled="disabled"/><label class="star1 star1-5"></label>
                                        <?php
                                        }
                                        if($rating==4)
                                        {?>
                                            <input type="radio" class="star1 star1-4" value="4" checked disabled="disabled"/><label class="star1 star1-4"></label>
                                        <?php
                                        }
                                        else
                                        {?>
                                            <input type="radio" class="star1 star1-4" value="4" disabled="disabled"/><label class="star1 star1-4"></label>
                                        <?php
                                        }
                                        if($rating==3)
                                        {?>
                                            <input type="radio" class="star1 star1-3" value="3" checked disabled="disabled"/><label class="star1 star1-3"></label>
                                        <?php
                                        }
                                        else
                                        {?>
                                            <input type="radio" class="star1 star1-3" value="3" disabled="disabled"/><label class="star1 star1-3"></label>
                                        <?php
                                        }
                                        if($rating==2)
                                        {?>
                                            <input type="radio" class="star1 star1-2" value="2" checked disabled="disabled"/><label class="star1 star1-2"></label>
                                        <?php
                                        }
                                        else
                                        {?>
                                            <input type="radio" class="star1 star1-2" value="2" disabled="disabled"/><label class="star1 star1-2"></label>
                                        <?php
                                        }                
                                        if($rating==1)
                                        {?>
                                            <input type="radio" class="star1 star1-1" value="1" checked disabled="disabled"/><label class="star1 star1-1"></label>
                                        <?php
                                        }                    
                                        else
                                        {?>
                                            <input type="radio" class="star1 star1-1" value="1" disabled="disabled"/><label class="star1 star1-1"></label>
                                        <?php
                                        }?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <div>
                                    <span style="color:#777">{{$bookedUserDetail[$i]->no_of_seats}} seats booked</span>
                                </div>
                                <div class="margin-top-10">
                                    <span style="color:red;font-size:16px">Paid {{number_format((float)$bookedUserDetail[$i]->cost_per_seat, 2, '.', '')}} &#8377;</span>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    @endfor
                    @endif

                    <div class="cabOwnerNotes">
                        <div class="col-md-1" style="margin-top:7px;">
                            <?php
                            if($rideDetail[0]->profile_pic=='default.png')
                            {
                                $npath='/images/default.png';
                            }
                            else
                            {
                                $npath='/images/profile/'.$rideDetail[0]->userId.'/'.$rideDetail[0]->profile_pic;
                            }
                            ?>
                            <img src="{{asset($npath)}}" width="50" style="border-radius:50%" height="50">
                        </div>
                        <div class="col-md-10">
                            <h4>{{ucwords($rideDetail[0]->first_name." ".$rideDetail[0]->last_name)}}</h4>
                            <h5>{{$rideDetail[0]->comment}}</h5>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>              
            </div>
        </div>
        <div class="col-md-4 sharingRideFilter">
            <div class="col-md-12 margin-top-25">
                <div>Offer published: {{date("d/m/Y",strtotime($rideDetail[0]->offerDate))}} - Seen {{$rideDetail[0]->view_count}} times</div>
            </div>
            <div class="clearfix"></div><hr/>

            <div class="col-md-12 margin-top-10">
                <div class="col-md-6">
                    <h2>{{(int)$rideDetail[0]->cost_per_seat}} Rs.</h2>
                    <h6>per co-traveller</h6>
                </div>

                <div class="col-md-6">
                    <h2>{{$rideDetail[0]->available_seat}} L</h2>
                    <h6>Seat Left</h6>
                </div>
            </div>
            <div class="clearfix"></div><hr/>
            <!------>
            <div class="col-md-12" style="background:#efefef">
                <h5 style="padding:0px 0px">Car Owner</h5>
            </div>
            <div class="clearfix"></div><hr/>

            <div class="col-md-12 margin-top-10">
                <div class="col-md-4" style="margin:0px;padding:0px">
                    <?php
                        if($rideDetail[0]->profile_pic=='default.png')
                        {
                            $path="/images/default.png";
                        }
                        else
                        {   
                            $path="/images/profile/".$rideDetail[0]->userId."/".$rideDetail[0]->profile_pic;
                        }
                    ?>
                    <img src="{{asset($path)}}" height="80" width="80" style="border-radius:50%;padding:0px;margin:0px;float:left">
                </div>
                <div class="col-md-8">
                    <label style="font-size:14px;font-weight:bold"><a href="{{route('get.profile',[$rideDetail[0]->userId,$rideDetail[0]->rideId])}}">{{ucwords($rideDetail[0]->first_name." ".$rideDetail[0]->last_name)}}</a></label><br/>
                    <label style="font-size:12px;font-weight:normal">@if($rideDetail[0]->birthdate=="")@else{{date_diff(date_create($rideDetail[0]->birthdate), date_create('today'))->y." Years old"}}@endif</label><br/>
                    <div class="stars1" style="padding:0px;margin:0px">
                        @if($rideDetail[0]->rating==5)
                        <input type="radio" class="star1 star-5" name="rating" value="5" checked="checked"/><label class="star1 star-5"></label>                        
                        <input type="radio" class="star1 star-4" name="rating" value="4"/><label class="star1 star-4"></label>
                        <input type="radio" class="star1 star-3" name="rating" value="3"/><label class="star1 star-3"></label>
                        <input type="radio" class="star1 star-2" name="rating" value="2"/><label class="star1 star-2"></label>
                        <input type="radio" class="star1 star-1" name="rating" value="1"/><label class="star1 star-1"></label>
                        @elseif($rideDetail[0]->rating==4)
                        <input type="radio" class="star1 star-5" name="rating" value="5"/><label class="star1 star-5"></label>                        
                        <input type="radio" class="star1 star-4" name="rating" value="4" checked="checked"/><label class="star1 star-4"></label>
                        <input type="radio" class="star1 star-3" name="rating" value="3"/><label class="star1 star-3"></label>
                        <input type="radio" class="star1 star-2" name="rating" value="2"/><label class="star1 star-2"></label>
                        <input type="radio" class="star1 star-1" name="rating" value="1"/><label class="star1 star-1"></label>
                        @elseif($rideDetail[0]->rating==3)
                        <input type="radio" class="star1 star-5" name="rating" value="5"/><label class="star1 star-5"></label>                        
                        <input type="radio" class="star1 star-4" name="rating" value="4"/><label class="star1 star-4"></label>
                        <input type="radio" class="star1 star-3" name="rating" value="3" checked="checked"/><label class="star1 star-3"></label>
                        <input type="radio" class="star1 star-2" name="rating" value="2"/><label class="star1 star-2"></label>
                        <input type="radio" class="star1 star-1" name="rating" value="1"/><label class="star1 star-1"></label>
                        @elseif($rideDetail[0]->rating==2)
                        <input type="radio" class="star1 star-5" name="rating" value="5"/><label class="star1 star-5"></label>                        
                        <input type="radio" class="star1 star-4" name="rating" value="4"/><label class="star1 star-4"></label>
                        <input type="radio" class="star1 star-3" name="rating" value="3"/><label class="star1 star-3"></label>
                        <input type="radio" class="star1 star-2" name="rating" value="2" checked="checked"/><label class="star1 star-2"></label>
                        <input type="radio" class="star1 star-1" name="rating" value="1"/><label class="star1 star-1"></label>
                        @elseif($rideDetail[0]->rating==1)
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
                </div>
            </div>
            <div class="clearfix"></div><hr/>

            <div class="col-md-12 margin-top-10">
                @if($rideDetail[0]->isverifyphone==1)
                <h5><i class="zmdi zmdi-check-circle zmdi-hc-fw zmdi-green zmdi-hc-lg"></i> Phone Verified</h5>
                @else
                <h5><i class="zmdi zmdi-close-circle zmdi-hc-fw zmdi-red zmdi-hc-lg"></i> Phone Not Verified</h5>
                @endif
                @if($rideDetail[0]->isverifyemail==1)
                <h5><i class="zmdi zmdi-check-circle zmdi-hc-fw zmdi-green zmdi-hc-lg"></i> Mobile Verified</h5>
                @else
                <h5><i class="zmdi zmdi-close-circle zmdi-hc-fw zmdi-red zmdi-hc-lg"></i> Mobile Not Verified</h5>
                @endif
            </div>
            <div class="clearfix"></div><hr/>


            <div class="col-md-12" style="margin:0px">
                <div class="col-md-12" style="padding:0px">
                    <h4>Car</h4>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4" style="margin:0px;padding:0px">
                    <?php
                    if($rideDetail[0]->vehical_pic=='car_default.png')
                    {
                        $ff='/images/car_default.png';
                    }
                    else
                    {
                        $ff='/images/cars/'.$rideDetail[0]->userId.'/'.$rideDetail[0]->vehical_pic;
                    }
                    ?>
                    <img src="{{asset($ff)}}" height="80" width="80" style="padding:0px;margin:0px;float:left">
                </div>
                <div class="col-md-8" style="margin:0px;padding:0px">
                    <div class="col-md-12">{{$rideDetail[0]->car_make." ".$rideDetail[0]->car_model}}</div>
                    <div class="col-md-12">Color : <span>{{$rideDetail[0]->color}}</span></div>
                    <div class="col-md-12">Comfort : <span>{{$rideDetail[0]->comfort}}</span></div>
                    
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div><hr/>
            <!------>
            <div class="col-md-12 margin-top-10">
                <h4><b>Member activity</b></h4>
                <h5>{{$totalRide}} ride offered</h5>
                <h5>Last online: {{$loginDate}}</h5>
                <h5>Member since: {{date("d-m-Y",strtotime($rideDetail[0]->userDate))}}</h5>
            </div>
            <div class="clearfix"></div><hr/>
        </div>
        @else
        <div>
            <h4>Sorry No Data Found</h4>
        </div>
        @endif
    </div>
</div>


@endsection
@section("js")
   
@endsection