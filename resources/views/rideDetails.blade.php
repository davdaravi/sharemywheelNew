@extends("master")

@section("head")
    <title>Share My Wheel - ride details</title>
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
        @if(count($rideDetail)>0)
        <div class="col-md-8 sharingRideGrid">
            <div class="col-md-12 margin-top-15">
                <div><a href="{{route('get.ride.list')}}"><i class="zmdi zmdi-arrow-left"></i> Go to Search Result</a></div><hr/>
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

                    <div class="cabOwnerNotes">
                        <div class="col-md-1" style="margin-top:7px;">
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
                            <img src="{{asset($path)}}" width="50" style="border-radius:50%" height="50">
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

            <div class="col-md-12 margin-top-10">
                
                <button type="button" class="btn btn-primary btn-block contact-modal">
                    Click to book your seat
                </button>
                <form name="paymentForm" id="paymentForm" method="POST" action="{{route('post.book.ccavenu.ride')}}">
                <div class="modal fade" id="ContactCarModal" tabindex="-1" role="dialog" aria-labelledby="ModalTitle">
                    <div class="modal-dialog" role="document">
                        <input type="hidden" name="daily" id="daily" value="0">
                        <input type="hidden" name="paymentride" id="paymentride" value="{{$rideDetail[0]->rideId}}">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="ModalTitle">Book Ride</h4>
                            </div>
                            <input type="hidden" id="token" name="_token" value="{{ csrf_token() }}">
                            <hr/>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="col-md-4 col-sm-6 col-xs-8">
                                            <h5>Number of Seats :</h5>
                                        </div>
                                        <div class="col-md-2 col-sm-4 col-xs-4">
                                            <select class="form-control" id="seats" name="seats">
                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="col-md-9 col-sm-9 col-xs-9">
                                            <h5 id="ticketName">Ticket Price :</h5>
                                            <h5 id="taxName">Serivce Charge :</h5>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-3 text-right">
                                            <h5><span id="cost_seat"></span> Rs.</h5>
                                            <h5><span id="tax_cost"></span> Rs.</h5>
                                            <hr/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="col-md-9 col-sm-9 col-xs-9">
                                            <h5 class="teal">Total :</h5>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-3 text-right">
                                            <h5 class="teal"><span id="total_cost"></span> Rs.</h5>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class ="col-md-12 col-sm-12 col-xs-12">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="pay_wallet" id="pay_wallet" value="1"> Want to pay using wallet
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" name="paybtn" id="paybtn" value="Pay" class="btn btn-primary"/>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            </div>
            <div class="clearfix"></div><hr/>

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


            <div class="col-md-12 margin-top-10">
                <h4><b>Member activity</b></h4>
                <h5>{{$totalRide}} ride offered</h5>
                <h5>Last online: {{$loginDate}}</h5>
                <h5>Member since: {{date("d-m-Y",strtotime($rideDetail[0]->userDate))}}</h5>
                <a href="{{route('get.profile',[$rideDetail[0]->userId,$rideDetail[0]->rideId])}}">View Profile</a>
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

<form method="post" name="redirect" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction"> 
        <input type="hidden" name="encRequest" id="encRequest" value="">
        <input type="hidden" name="access_code" id="access_code" value="">
</form>
@endsection
@section("js")
<script type="text/javascript" src="{{URL::asset('js/jquery.toaster.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
    @if(Session::has('status'))
        $.toaster({ priority : 'success', title : 'Title', message : '{{ Session::get('status') }}'});
    @endif
    $(".contact-modal").click(function(){
        $("#seats").empty();
        $("#cost_seat").text(0);
        $("#tax_cost").text(0);
        $("#total_cost").text(0);
        if({{$rideDetail[0]->available_seat}}==0)
        {
            $.toaster({ priority : 'danger', title : 'Title', message : 'Seat is not available'});
        }
        else
        {
            $.ajax({
                async:false,
                headers: { 'X-CSRF-Token' : $("#token").val() } ,
                type:'GET',
                url:'{{route('get.ride.seat')}}',
                data:{"ride":{{$rideDetail[0]->rideId}}},
                dataType:'json',
                beforeSend:function(){
                    
                },
                success:function(response){
                    $("#ContactCarModal").modal('show');
                    if(response['data'].length==1)
                    {
                        if(response['data'][0]['available_seat']==0)
                        {
                            $.toaster({ priority : 'danger', title : 'Title', message : 'Seat is not available'});
                        }
                        else
                        {
                            var html="";
                            for(var k=1;k<=response['data'][0]['available_seat'];k++)
                            {
                                html+='<option value='+k+'>'+k+'</option>';
                            }    
                            $("#seats").append(html);
                            if(response['data'][0]['isDaily']==1)
                            {
                                $("#ticketName").text('Daily Ride Charge:');
                                $("#daily").val(1);
                                calculateRates(25);
                            }
                            else
                            {
                                $("#daily").val(0);
                                calculateRates(response['data'][0]['cost_per_seat']);
                            }
                        }
                    }
                    else
                    {
                        $.toaster({ priority : 'danger', title : 'Title', message : 'Please try again'});
                        $("#ContactCarModal").modal('hide');
                    }
                },
                error:function(response)
                {
                    $.toaster({ priority : 'danger', title : 'Title', message : 'Please try again'});
                    $("#ContactCarModal").modal('hide');
                },
                complete:function(){
                    //removeOverlay();
                }
            });
        }   
    });
    $("#seats").change(function(){
        if($("#daily").val()==0)
        {
            calculateRates({{$rideDetail[0]->cost_per_seat}});    
        }
        else
        {
            $("#cost_seat").val("25.00");
            $("#tax_cost").val("2.5");
            $("#total_cost").val("27.5"); 
        }
    });
    $("#paymentForm").submit(function(e){
        var dd=$("#pay_wallet").is(':checked');
        if(dd==false)
        {
            e.preventDefault(); 
            $.ajax({
                async:false,
                headers: { 'X-CSRF-Token' : $("#token").val() } ,
                type:'POST',
                url:'{{route('post.book.ccavenu.ride')}}',
                data:{"ride":{{$rideDetail[0]->rideId}},"cost_seat":{{$rideDetail[0]->cost_per_seat}},"No_seats":$("#seats").val()},
                dataType:'json',
                beforeSend:function(){
                    
                },
                success:function(response){
                    if(response['status']==false)
                    {
                        $.toaster({ priority : 'danger', title : 'Title', message :response['message']});
                    }
                    else
                    {
                        $("#encRequest").val(response['data']['encrypt']);
                        $("#access_code").val(response['data']['access']);
                        document.redirect.submit();
                    }
                },
                error:function(response)
                {
            
                },
                complete:function(){
                    //removeOverlay();
                }
            });
        }
        else
        {
            e.preventDefault(); 
            //wallet
            $.ajax({
                async:false,
                headers: { 'X-CSRF-Token' : $("#token").val() } ,
                type:'POST',
                url:'{{route('post.book.ride')}}',
                data:{"ride":{{$rideDetail[0]->rideId}},"cost_seat":{{$rideDetail[0]->cost_per_seat}},"No_seats":$("#seats").val()},
                dataType:'json',
                beforeSend:function(){
                    
                },
                success:function(response){
                   window.location.reload();
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
});
function calculateRates(rate)
{
    var seat=$("#seats").val();
    var ticket_price=seat*rate;
    var tax=(0.1*ticket_price);
    $("#tax_cost").text(parseFloat(tax).toFixed(2));
    $("#cost_seat").text(parseFloat(ticket_price).toFixed(2));
    $("#total_cost").text(ticket_price+tax);
}
</script>
@endsection