@extends("master")

@section("head")
    <title>Share My Wheel - offer ride</title>
@endsection
@section("nav")
    @include("includes.afterLoginSidebar")
@endsection
@section("content")
<div class="container sharingContentBody">
    <div class="col-md-12">
        <div class="col-md-8 sharingRideGrid">
            <div class="col-md-12 margin-top-15">
                <h4>Create Ride</h4>
                <hr/>

            </div>
            <div class="col-md-12">
                <form class="form-horizontal" role="form" method="POST" name="offerForm" id="offerForm" action="">
                    <div class="form-group col-md-6 margin-top-10 margin-bottom-0">
                        <label class="col-sm-12 col-md-12">Source :</label>
                        <div class="col-sm-12 col-md-12">
                            <input type="text" name="from" id="from" class="form-control search" placeholder="Enter Source" required/>
                        </div>
                        <p id="areafrom_0_error" class="validation_error PD20"></p>
                    </div>

                    <div class="form-group col-md-6 col-md-offset-1 margin-top-10 margin-bottom-0">
                        <label class="col-sm-12 col-md-12">Destination :</label>
                        <div class="col-sm-12 col-md-12">
                            <input type="text" class="form-control" name="to" id="to" placeholder="Enter Destination" required/>
                        </div>
                        <p id="areafrom_1_error" class="validation_error PD20"></p>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-6 margin-top-10 margin-bottom-0">
                        <label class="col-sm-12 col-md-12 addarea" style="color:#2ABCA3">+ Add more way points</label>
                    </div>
                    <div class="clearfix"></div>

                    <div id="waypointdiv" class="col-md-12 PD0">
                        <div class="form-group col-md-7 margin-bottom-0">
                            <div class="col-md-10">
                                <input type="text" id="areafrom_0" name="areafrom[]" class="form-control" placeholder="Enter City"/>
                            </div>      
                                     
                            <div class="clearfix"></div>
                            <p id="areafrom_2_error" class="validation_error PD20"></p>
                        </div>
                        
                    </div>
                    <div class="form-group col-md-6 margin-top-10 margin-bottom-0">
                        <div class="col-sm-12 col-md-12">Ladies Only : &nbsp;<input style="vertical-align:top" type="checkbox" name="ladies" value="1" id="ladies"></div>
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group col-md-6 margin-top-30 margin-bottom-0">
                        <div class="col-sm-12 col-md-4">Daily : &nbsp;<input style="vertical-align:top" type = "checkbox" name ="daily" value="1" id ="daily"></div>
                        <div class="col-sm-12 col-md-8">Return : &nbsp;<input style="vertical-align:top" type = "checkbox" name ="return" id ="return" value="1" checked/></div>
                    </div>
                    <div class="clearfix"></div>
                    
                    <div class="form-group col-md-6 margin-top-10 margin-bottom-0">
                        <div class="col-sm-12 col-md-12">Departure Date :</div>
                        <div class="col-md-12 col-sm-12">
                            <input type="text" id="fromdate" name="fromdate" class="form-control" placeholder="Enter Start Date" autocomplete="off" required/>
                        </div>
                        <p id="fromdate_error" class="validation_error PD20"></p>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group col-md-3 margin-top-10 margin-bottom-0" id="returnHourDiv" style="display:none">
                        <div class="col-sm-12 col-md-12">Return Hour :</div>
                        <div class="col-md-12 col-sm-12">
                            <select class="form-control" id="returnHour" name="returnHour">
                                @for($i=0;$i<24;$i++)
                                <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                        <p id="returnHour_error" class="validation_error PD20"></p>
                    </div>
                    <div class="form-group col-md-3 margin-top-10 margin-bottom-0" id="returnMinDiv" style="display:none">
                        <div class="col-sm-12 col-md-12">Return Min :</div>
                        <div class="col-md-12 col-sm-12">
                            <select class="form-control" id="returnMin" name="returnMin">
                                @for($i=0;$i<60;$i++)
                                <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-6 margin-top-10 margin-bottom-0" id="returnDateDiv">
                        <div class="col-sm-12 col-md-12">Return Date :</div>
                        <div class="col-md-12 col-sm-12">
                            <input type="text" id="todate" name="todate" class="form-control" placeholder="Enter Return Date"/>
                        </div>
                        <p id="todate_error" class="validation_error PD20"></p>
                    </div>

                    <div class="clearfix"></div>
                    
                    <div class="form-group col-md-6 margin-top-30 margin-bottom-0">
                        <div class="col-sm-12 col-md-12">Car :</div>
                        <div class="col-sm-12 col-md-12">
                            <select class="form-control" name="car" id="car" required>
                                <option value="">Select Car</option>
                                @for($i=0;$i<count($car);$i++)
                                    <option value="{{$car[$i]->id}}">{{ucwords($car[$i]->car_make." - ".$car[$i]->car_model)}}</option>
                                @endfor
                            </select>
                        </div>
                        <p id="car_error" class="validation_error PD20"></p>
                    </div>
                    <div class="form-group col-md-6 margin-top-30 margin-bottom-0">
                        <div class="col-sm-12 col-md-12">No. of Seats :</div>
                        <div class="col-sm-12 col-md-12">
                            <select class="form-control" name="seat" id="seat" required>
                                
                                @for($i=1;$i<8;$i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                        <p id="seat_error" class="validation_error PD20"></p>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-6 margin-top-10 margin-bottom-0">
                        <div class="col-sm-12 col-md-12">Cost Per Seat :</div>
                        <div class="col-sm-12 col-md-12">
                            <input type="text" maxlength="4" name="costseat" id="costseat" placeholder="Cost per seat" class="form-control" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" required/>
                        </div>
                        <p id="costseat_error" class="validation_error PD20"></p>
                    </div>
                    <div class="form-group col-md-6 margin-top-10 margin-bottom-0">
                        <div class="col-sm-12 col-md-12">Luggage :</div>
                        <div class="col-sm-12 col-md-12">
                            <select class="form-control" name="luggage" id="luggage" required> 
                                <option value="">Select luggage</option>
                                @for($i=0;$i<count($luggage);$i++)
                                    <option value="{{$luggage[$i]->id}}">{{$luggage[$i]->name}}</option>
                                @endfor
                            </select>
                        </div>
                        <p id="luggage_error" class="validation_error PD20"></p>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-6 margin-top-10 margin-bottom-0">
                        <div class="col-sm-12 col-md-12">Leave :</div>
                        <div class="col-sm-12 col-md-12">
                            <select class="form-control" name="leave" id="leave" required>
                                <option value="">Select Leave on</option>
                                @for($i=0;$i<count($leave);$i++)
                                <option value="{{$leave[$i]->id}}">{{$leave[$i]->name}}</option>
                                @endfor
                            </select>
                        </div>
                        <p id="leave_error" class="validation_error PD20"></p>
                    </div>

                    <div class="form-group col-md-6 margin-top-10 margin-bottom-0">
                        <div class="col-sm-12 col-md-12">Detour :</div>
                        <div class="col-sm-12 col-md-12">
                            <select class="form-control" name="detour" id="detour" required>
                                <option value="">Select Detour</option>
                                @for($i=0;$i<count($detour);$i++)
                                <option value="{{$detour[$i]->id}}">{{$detour[$i]->name}}</option>
                                @endfor
                            </select>
                        </div>
                        <p id="detour_error" class="validation_error PD20"></p>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-12 margin-top-10 margin-bottom-0">
                        <label class="col-sm-12 col-md-12">Comments :</label>
                        <div class="col-sm-12 col-md-12">
                            <textarea class="form-control" name="comments" id="comments" placeholder="Comment here"></textarea>
                        </div>
                        <p id="comments_error" class="validation_error PD20"></p>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-12 margin-top-10 margin-bottom-0">
                        <div class="col-md-12">
                            <label><input type="checkbox" value="licence" name="licence" id="licence" style="vertical-align:top" required/> I certify that I hold a driving Licence</label>
                        </div>
                        <p id="licence_error" class="validation_error PD20"></p>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-12 margin-top-10" style="margin-bottom:0px">
                        <div class="col-md-12">By clicking 'Submit' you agree to our <a target="_blank" href="/terms_condition">Terms & Conditions</a>.</div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-12 margin-top-10" style="margin-bottom:0px">
                        <div class="col-md-12"><span style="color:red">Note: You have to pay 25 Rs. to offer a daily ride.</span></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-12 margin-top-10">
                        <div class="col-sm-9">
                            <input type="submit" name="offerSubmitBtn" id="offerSubmitBtn" class="btn btn-primary" value="submit"/>
                        </div>
                    </div>


                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
        <div class="col-md-4 sharingRideFilter" style="display:none">
            <div class="col-md-12 margin-top-25">
                Map
            </div>
            <div class="col-md-12 margin-top-10" id="googleMap1" style="width:100%;height:400px">
            </div>
            <div class="clearfix"></div>
            <hr/>
        </div>
    </div>
</div>
<!--daily ride payment model-->
<div class="modal fade" id="dailyride" tabindex="-1" role="dialog" aria-labelledby="ModalTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ModalTitle">Offer Ride</h4>
            </div>
            <input type="hidden" id="token" name="_token" value="{{ csrf_token() }}">
            <hr/>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <h3>Wallet Balance &#8377; <span id="wallet_amount_balance">0</span></h3>
                        <div class="col-md-9 col-sm-9 col-xs-9">
                            <h5 id="ticket_name">Ride Charge:</h5>
                            <h5 id="tax_name">Serivce Charge:</h5>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3 text-right">
                            <h5><span id="cost_seat">25</span> Rs.</h5>
                            <h5><span id="tax_cost">2.5</span> Rs.</h5>
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
                            <h5 class="teal"><span id="total_cost">27.5</span> Rs.</h5>
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
                <input type="button" name="paybtn" id="paybtn" value="Pay" class="btn btn-primary"/>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- daily ride payment model ends-->
<form method="post" name="redirect" target="_blank" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction"> 
    <input type="hidden" name="encRequest" id="encRequest" value="">
    <input type="hidden" name="access_code" id="access_code" value="">
</form>
@endsection
@section("js")
<script type="text/javascript" src="{{URL::asset('js/jquery.toaster.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/offerride.js')}}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDzn37QRF8u0MBNXd2MGsTQs1IOBUXMH4Q&libraries=places&callback=initialize" async defer></script>   
<script type="text/ecmascript">
    var autocompleteFrom,autocompleteTo,autocompleteArea0,map;
    $(document).ready(function(){


        $('#fromdate').datetimepicker({
            lang:'ch',
            step:1,
            timepicker:true,
            closeOnDateSelect:true,
            //theme:'dark',
            format:'Y/m/d H:i',
            defaultDate:new Date(),
            validateOnBlur:true,
            onShow:function( ct ){
                    this.setOptions({
                    maxDate:jQuery('#todate').val()?jQuery('#todate').val():false
               })
            },
            minDate:new Date(), // yesterday is minimum date
        });
        $('#todate').datetimepicker({
            lang:'ch',
            step:1,
            timepicker:true,
            format:'Y/m/d H:i',
            defaultDate:new Date(),
            closeOnDateSelect:true,
            validateOnBlur:true,
            onShow:function( ct ){
                    this.setOptions({
                    minDate:jQuery('#fromdate').val()?jQuery('#fromdate').val():new Date()
                })
            }
            // yesterday is minimum date
        });
    });
    function initialize() 
    {
        //map js starts
        directionsDisplay = new google.maps.DirectionsRenderer;
        directionsService = new google.maps.DirectionsService;
        service = new google.maps.DistanceMatrixService();
        var mapProp = {
            center:new google.maps.LatLng(20.5937,78.9629),
            zoom:4,
            mapTypeId:google.maps.MapTypeId.ROADMAP
        };
        map=new google.maps.Map(document.getElementById("googleMap1"),mapProp);
        infowindow = new google.maps.InfoWindow();
        //map js ends
       
        var options = {
            //types: ['(cities)'],
            componentRestrictions: {country: "ind"}
        };

        var inputFrom = document.getElementById('from');
        var inputTo = document.getElementById('to');
        var areaFrom0 = document.getElementById('areafrom_0');
    
        autocompleteFrom = new google.maps.places.Autocomplete(inputFrom, options);
        autocompleteFrom.addListener('place_changed', offerride.fillInAddressFrom);

        autocompleteTo = new google.maps.places.Autocomplete(inputTo, options);
        autocompleteTo.addListener('place_changed', offerride.fillInAddressTo);

        autocompleteArea0 = new google.maps.places.Autocomplete(areaFrom0, options);
        autocompleteArea0.addListener('place_changed', offerride.fillInAddressArea0);
    }
    
</script>
@endsection