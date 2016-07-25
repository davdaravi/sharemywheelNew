@extends("master")

@section("head")
    <title>Share My Wheel - find ride</title>
@endsection
@section("nav")
    @include("includes.afterLoginSidebar")
@endsection
@section("content")

<div class="container">
            <div class="col-md-12">
                <div class="sharingRideSearch">
                    <form role="form" method="POST" action="{{route('post.ride.search')}}" onsubmit="return checkRide()">
                        <div class="col-md-12">
                            <h4>Find a ride</h4>
                        </div>
                        
                        <div class="form-group col-lg-3 col-md-4 col-sm-5">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1"><i class="zmdi zmdi-pin zmdi-hc-lg zmdi-green"></i></span>
                                <input type="text" id="from" name="from" class="form-control" placeholder="From" value=""/>   
                            </div>
                            @if ($errors->has('fromcity'))<label id="fromerror" class="validation_error"><b>{{ $errors->first('fromcity') }}</b></label>@endif                
                        </div>
                        <!--<div class="col-lg-1 text-center">
                            <button type="button" class="btn" id="reverse" name="reverse"><i class="zmdi zmdi-repeat zmdi-hc-lg"></i></button>
                        </div>-->
                        <div class="form-group col-lg-3 col-md-4 col-sm-5">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1"><i class="zmdi zmdi-pin zmdi-hc-lg zmdi-red"></i></span>
                                <input type="text" name="to" id="to" class="form-control" placeholder="To" value=""/> 
                            </div>
                            @if ($errors->has('tocity'))<label id="toerror" class="validation_error"><b>{{ $errors->first('tocity') }}</b></label>@endif                
                        </div>
                        <div class="form-group col-lg-3 col-md-3 col-sm-2">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1"><i class="zmdi zmdi-calendar-note zmdi-hc-lg"></i></span>
                                <input type="text" name="fromdate" id="fromdate" class="form-control" placeholder="select Date" value="" autocomplete="off"/>
                            </div>
                            @if ($errors->has('fromdate'))<label id="dateerror" class="validation_error"><b>{{ $errors->first('fromdate') }}</b></label>@endif                
                            @if ($errors->has('error'))<label class="validation_error"><b>{{ $errors->first('error') }}</b></label>@endif
                        </div>
                        <input type="hidden" name="fromlat" id="fromlat" value=""/>
                        <input type="hidden" name="tolat" id="tolat" value=""/>
                        <input type="hidden" name="fromlng" id="fromlng" value=""/>
                        <input type="hidden" name="tolng" id="tolng" value=""/>
                        <input type="hidden" name="fromcity" id="fromcity" value=""/>
                        <input type="hidden" name="fromplace" id="fromplace" value=""/>
                        <input type="hidden" name="fromvalue" id="fromvalue" value=""/>
                        <input type="hidden" name="tocity" id="tocity" value=""/>
                        <input type="hidden" name="toplace" id="toplace" value=""/>
                        <input type="hidden" name="tovalue" id="tovalue" value=""/>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                        <input type="hidden" name="token" value="{{config('app.token')}}"/>
                        <!--<div class="form-group col-lg-3 col-md-4 col-sm-5">
                            <div class="input-group">
                                
                                <input type="text" name="rideFindDatepicker" id="rideFindDatepicker" class="form-control" placeholder="Date" /> 
                            </div>
                        </div>-->
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <input type="submit" name="rideSearch" id="rideSearch" class="btn btn-primary" value="Search"/>
                        </div>
                    </form>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="col-md-12">
                <div class="col-md-12 findRideMapBlock" id="googleMap">
                    
                </div>
            </div>
        </div>

@endsection
@section("js")
<script type="text/javascript" src="{{URL::asset('js/jquery.toaster.js')}}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDzn37QRF8u0MBNXd2MGsTQs1IOBUXMH4Q&libraries=places&callback=initialize" async defer></script>
<script type="text/ecmascript">
    var autocomplete,autocomplete1,directionsDisplay,directionsService,service,origin,destination;
    

    $(document).ready(function(){
        
        @if(count($errors)>0)
        $("#from").val('{{old('from')}}');
        $("#to").val('{{old('to')}}');
        $("#fromdate").val('{{old('fromdate')}}');
        $("#fromcity").val('{{old('fromcity')}}');
        $("#fromplace").val('{{old('fromplace')}}');
        $("#toplace").val('{{old('toplace')}}');
        $("#tocity").val('{{old('tocity')}}');
        $("#fromvalue").val('{{old('fromvalue')}}');
        $("#tovalue").val('{{old('tovalue')}}');
        $("#fromlat").val('{{old('fromlat')}}');
        $("#tolat").val('{{old('tolat')}}');
        $("#fromlng").val('{{old('fromlng')}}');
        $("#tolng").val('{{old('tolng')}}');
        @else
        $("#from").val('');
        $("#to").val('');
        $("#fromdate").val('');
        $("#fromcity").val('');
        $("#fromplace").val('');
        $("#toplace").val('');
        $("#tocity").val('');
        $("#fromvalue").val('');
        $("#tovalue").val('');
        $("#fromlat").val('');
        $("#tolat").val('');
        $("#fromlng").val('');
        $("#tolng").val('');
        @endif
        origin={};
        destination={};
        //   initialize();
        $('#fromdate').datetimepicker({
            lang:'ch',
            timepicker:false,
            format:'d-m-Y',
            minDate:'01-01-1970', // yesterday is minimum date
        });
        $("#reverse").click(function(){
            var from=$("#from").val();
            var to=$("#to").val();
            $("#to").val(from);
            $("#from").val(to);  
            var from_lat=$("#fromlat").val();
            var from_lng=$("#fromlng").val();
            var to_lat=$("#tolat").val(); 
            var to_lng=$("#tolng").val();
            $("#tolat").val(from_lat);
            $("#tolng").val(from_lng);
            $("#fromlat").val(to_lat);
            $("#fromlng").val(to_lng);
            origin={lat: parseFloat(to_lat), lng: parseFloat(to_lng)};
            destination={lat: parseFloat(from_lat), lng: parseFloat(from_lng)};
           
            calculateAndDisplayRoute(directionsService, directionsDisplay,origin,destination);   
        });
    });
    function initialize() 
    {
        //load map
        var from_lat=$("#fromlat").val();
        var from_lng=$("#fromlng").val();
        var to_lat=$("#tolat").val(); 
        var to_lng=$("#tolng").val();
        if(from_lat=="" && from_lng=="" && to_lat=="" && to_lng=="")
        {

        }
        else
        {
            origin={lat: parseFloat(from_lat), lng: parseFloat(from_lng)};
            destination={lat: parseFloat(to_lat), lng: parseFloat(to_lng)};
        }

        directionsDisplay = new google.maps.DirectionsRenderer;
        directionsService = new google.maps.DirectionsService;
        service = new google.maps.DistanceMatrixService();
        var mapProp = {
            center:new google.maps.LatLng(23.00,72.00),
            zoom:8,
            mapTypeId:google.maps.MapTypeId.ROADMAP
        };
        var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);
        directionsDisplay.setMap(map);
        calculateAndDisplayRoute(directionsService, directionsDisplay,origin,destination);
        /*var flightPlanCoordinates = [
            {lat: 22.306124, lng: 70.820068},
            {lat: 23.033863, lng: 72.585022}
            ];
        //var flightPath = new google.maps.Polyline({
            path: flightPlanCoordinates,
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
          });*/

        //flightPath.setMap(map);
        //start autocomplete
        var options = {
            //types: ['(cities)'],
            componentRestrictions: {country: "ind"}
        };

        var input = document.getElementById('from');
        var input1 = document.getElementById('to');
        autocomplete = new google.maps.places.Autocomplete(input, options);
        autocomplete.addListener('place_changed', fillInAddress);
        autocomplete1 = new google.maps.places.Autocomplete(input1, options);
        autocomplete1.addListener('place_changed', fillInAddress1);

    }
    function fillInAddress() 
    {
        // Get the place details from the autocomplete object.
        var place=autocomplete.getPlace();
        if(place.geometry)
        {
            var from_lat=autocomplete.getPlace().geometry.location.lat();
            var to_lat=autocomplete.getPlace().geometry.location.lng();
            origin =  {lat: from_lat, lng: to_lat};
            $("#fromlat").val(from_lat);
            $("#fromplace").val(place.name);
            $("#fromlng").val(to_lat);
            $("#fromvalue").val($("#from").val());
            getCityName(from_lat,to_lat,1);
        }
        else
        {
            origin={};
            $("#fromlat").val("");
            $("#fromplace").val("");
            $("#fromlng").val("");
            $("#fromvalue").val("");
            $("#fromcity").val("");
        }
        
        calculateAndDisplayRoute(directionsService, directionsDisplay,origin,destination);
    }
    function fillInAddress1()
    {
        // Get the place details from the autocomplete object.
        var place=autocomplete1.getPlace();
        if(place.geometry)
        {
            var dfrom_lat=autocomplete1.getPlace().geometry.location.lat();
            var dto_lat=autocomplete1.getPlace().geometry.location.lng();
            destination =  {lat: dfrom_lat, lng: dto_lat};
            $("#tolat").val(dfrom_lat);
            $("#tolng").val(dto_lat);
            $("#toplace").val(place.name);
            $("#tovalue").val($("#to").val());
            getCityName(dfrom_lat,dto_lat,2);
        }
        else
        {
            destination={};
            $("#tolat").val("");
            $("#tolng").val("");
            $("#toplace").val("");
            $("#tovalue").val("");
            $("#tocity").val("");
        }
        calculateAndDisplayRoute(directionsService, directionsDisplay,origin,destination);
    }
    function calculateAndDisplayRoute(directionsService, directionsDisplay,origin,destination) {
        
      //  var selectedMode = document.getElementById('mode').value;
        var from=document.getElementById('from').value;
        var to=document.getElementById('to').value;
        
        if(from!="" && to!="")
        {
            if(Object.keys(origin).length>0 && Object.keys(destination).length>0)
            {
                directionsService.route({
                    origin: origin,  // Haight.
                    destination: destination,  // Ocean Beach.
                    // Note that Javascript allows us to access the constant
                    // using square brackets and a string value as its
                    // "property."
                    travelMode: google.maps.TravelMode['DRIVING']
                }, function(response, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setDirections(response);
                        distanceRoute(service,origin,destination);
                    } 
                    else 
                    {
                        window.alert('Directions request failed due to ' + status);
                    }
                });  
            }
              
           
        }
        
    }
    function distanceRoute(service,origin,destination)
    {
        //find distance
            
        //var origin1 = new google.maps.LatLng(from_lat,to_lat);
        var origin2 = document.getElementById('from').value;
        var destinationA = document.getElementById('to').value;
        //var destinationB = new google.maps.LatLng(dfrom_lat,dto_lat);
        
        service.getDistanceMatrix({
            origins: [origin2],
            destinations: [destinationA],
            travelMode: google.maps.TravelMode.DRIVING,
            unitSystem: google.maps.UnitSystem.METRIC,
            avoidHighways: false,
            avoidTolls: false
        }, function (response, status) {
            if (status == google.maps.DistanceMatrixStatus.OK && response.rows[0].elements[0].status != "ZERO_RESULTS") {
                var distance = response.rows[0].elements[0].distance.text;
                var duration = response.rows[0].elements[0].duration.text;
                console.log(distance);
                console.log(duration);
     
            } else {
                alert("Unable to find the distance via road.");
            }
        });

        //distance ends

    }
    function getCityName(lat,lng,part)
    {
        var geocoder;
        geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(lat, lng);

        geocoder.geocode(
            {'latLng': latlng}, 
            function(results, status) 
            {
                if (status == google.maps.GeocoderStatus.OK) 
                {
                    if (results[0]) 
                    {
                        var add= results[0].formatted_address ;
                        var  value=add.split(",");

                        count=value.length;
                        country=value[count-1];
                        state=value[count-2];
                        city=value[count-3];
                        if(part==1)
                        {
                            $("#fromcity").val(city);
                        }
                        else if(part==2)
                        {
                            $("#tocity").val(city);
                        }
                        else
                        {
                            $("#fromcity").val('');
                            $("#tocity").val('');
                        }        
                    }
                    else  
                    {
                        
                        if(part==1)
                        {
                            $("#fromcity").val('');
                        }
                        else if(part==2)
                        {
                            $("#tocity").val('');
                        }
                        else
                        {
                            $("#fromcity").val('');
                            $("#tocity").val('');
                        }
                    }
                }
                else 
                {
                    if(part==1)
                    {
                        $("#fromcity").val('');
                    }
                    else if(part==2)
                    {
                        $("#tocity").val('');
                    }
                    else
                    {
                        $("#fromcity").val('');
                        $("#tocity").val(''); 
                    }
                }
            }
        );
    }
    function checkRide()
    {
        var from=$("#from").val();
        var to=$("#to").val();
        var fromvalue=$("#fromvalue").val();
        var tovalue=$("#tovalue").val();
        var date=$("#fromdate").val();
        var d=new Date();
        var day;
        
        if(d.getMonth()+1<10)
        {
            var month="0"+(d.getMonth()+1);
        }
        else
        {
            var month=d.getMonth()+1;
        }
        if(d.getDate()<10)
        {
            day="0"+d.getDate();
        }
        else
        {
            day=d.getDate();
        }
        
        dd=new Date(d.getFullYear(),d.getMonth(),day);
        var ff=date.split("-");
        date=new Date(ff[2],ff[1]-1,ff[0]);

        if(from=="")
        {
            $.toaster({ priority : 'danger', title : 'Title', message : 'Select From place'});
            return false;
        }
        else if(to=="")
        {
            $.toaster({ priority : 'danger', title : 'Title', message : 'Select To place'});
            return false;
        }
        else if(from!=fromvalue)
        {
            $.toaster({ priority : 'danger', title : 'Title', message : 'Select correct from place'});
            return false;   
        }
        else if(to!=tovalue)
        {
            $.toaster({ priority : 'danger', title : 'Title', message : 'Select correct to place'});
            return false;
        }
        else if(date=="")
        {
            $.toaster({ priority : 'danger', title : 'Title', message : 'Select Date'});
            return false;
        }
        else if(dd>date)
        {
            $.toaster({ priority : 'danger', title : 'Title', message : 'Select Correct Date'});
            return false;
        }
        else
        {
            $(".validation_error").each(function(){
                $(this).text('');
            });
            return true;
        }
    }
</script>
@endsection