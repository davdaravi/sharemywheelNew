@extends("master")

@section("head")
    <title>Share My Wheel - home</title>
@endsection
@section("nav")
    @include("includes.afterLoginSidebar")
@endsection
@section("content")
<!-- main Content -->
<div class="container-fluid">
    <div class="row">
        <div class="bannerImage">
            <img src="images/slider1.png" class="bannerImg"/>
            <div id="slider-pattern"></div>
        </div>
    </div>
</div>

<div class="container">

    <div class="col-xs-12 col-sm-6 col-md-4 searchForm">

        
        <div class="col-md-12">
            <h3>Book a Car</h3>
        </div>
        <div class="clearfix"></div><hr/>
        <form role="form" method="POST" action="{{route('post.ride.search')}}">
            <div class="form-group col-md-12">
                @if ($errors->has('error'))<label class="validation_error"><b>{{ $errors->first('error') }}</b></label>@endif
            </div>
            <div class="form-group col-md-12">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><i class="zmdi zmdi-pin zmdi-hc-lg zmdi-green"></i></span>
                    <input type="text" name="from" id="from" class="form-control" placeholder="From" value="" required/> 
                </div>
                @if ($errors->has('fromcity'))<label id="fromerror" class="validation_error"><b>{{ $errors->first('fromcity') }}</b></label>@endif                
            </div>
            <div class="form-group col-md-12">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><i class="zmdi zmdi-pin zmdi-hc-lg zmdi-red"></i></span>
                    <input type="text" name="to" id="to" class="form-control" placeholder="To" value="" required/>   
                </div>
                @if ($errors->has('tocity'))<label id="toerror" class="validation_error"><b>{{ $errors->first('tocity') }}</b></label>@endif                
            </div>

            <div class="form-group col-md-12">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><i class="zmdi zmdi-calendar-note zmdi-hc-lg"></i></span>
                    <input type="text" class="form-control" name="fromdate" id="fromdate" placeholder="Date Picker" value="" required readonly/>    
                </div>
                @if ($errors->has('fromdate'))<label id="dateerror" class="validation_error"><b>{{ $errors->first('fromdate') }}</b></label>@endif                
            </div>
            <input type="hidden" name="fromcity" id="fromcity" value=""/>
            <input type="hidden" name="fromplace" id="fromplace" value=""/>
            <input type="hidden" name="tocity" id="tocity" value=""/>
            <input type="hidden" name="toplace" id="toplace" value=""/>
            <input type="hidden" name="fromvalue" id="fromvalue" value=""/>
            <input type="hidden" name="tovalue" id="tovalue" value=""/>
            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
            <input type="hidden" name="token" value="{{config('app.token')}}"/>
            <div class="col-md-12 text-right">
                <input type="submit" name="submit" id="submit" value="Search" class="btn btn-primary">
            </div>
        </form>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-4 carImages">
        <img src="images/slider_front_img.png">
    </div>
</div>
<div class="clearfix"></div>

<div class="container margin-top-25 blocks">
    <div class="col-md-12 text-center">
        <div class="col-xs-12 col-sm-3 col-md-3 block">
            <i class="zmdi zmdi-movie-alt zmdi-hc-2x"></i>
            <p class="margin-top-10">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's </p>
        </div>

        <div class="col-xs-12 col-sm-3 col-md-3 block">
            <i class="zmdi zmdi-label zmdi-hc-2x"></i>
            <p class="margin-top-10">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's </p>
        </div>

        <div class="col-xs-12 col-sm-3 col-md-3 block">
            <i class="zmdi zmdi-car-taxi zmdi-hc-2x"></i>
            <p class="margin-top-10">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's </p>
        </div>

        <div class="col-xs-12 col-sm-3 col-md-3 block">
            <i class="zmdi zmdi-camera zmdi-hc-2x"></i>
            <p class="margin-top-10">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's </p>
        </div>
    </div>
</div>
<div class="clearfix"></div>

@endsection
@section("js")
<script type="text/javascript" src="{{URL::asset('js/jquery.toaster.js')}}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDzn37QRF8u0MBNXd2MGsTQs1IOBUXMH4Q&libraries=places&callback=initialize" async defer></script>
<script type="text/ecmascript">
    var autocomplete,autocomplete1;
    $(document).ready(function(){
       @if(count($errors)>0)
            $("#from").val('{{old('from')}}');
            $("#to").val('{{old('to')}}');
            $("#fromdate").val('{{old('fromdate')}}');
            $("#fromcity").val('{{old('fromcity')}}');
            $("#fromplace").val('{{old('fromplace')}}');
            $("#tocity").val('{{old('tocity')}}');
            $("#toplace").val('{{old('toplace')}}');
            $("#fromvalue").val('{{old('fromvalue')}}');
            $("#tovalue").val('{{old('tovalue')}}');
        @else
            $("#from").val('');
            $("#to").val('');
            $("#fromdate").val('');
            $("#fromcity").val('');
            $("#fromplace").val('');
            $("#tocity").val('');
            $("#toplace").val('');
            $("#fromvalue").val('');
            $("#tovalue").val('');
        @endif
        //   initialize();
        $('#fromdate').datetimepicker({
            lang:'ch',
            timepicker:false,
            format:'d-m-Y',
            minDate:new Date()// yesterday is minimum date
        });

        /*$("#message").fadeTo(4000, 500).slideUp(500, function(){
            $("#message").alert('close');
        });*/
        if($("#fromerror").length>0)
        {
            $("#from").addClass('validation_error_border');
        }
        if($("#toerror").length>0)
        {
            $("#to").addClass('validation_error_border');
        }
        if($("#dateerror").length>0)
        {
            $("#fromdate").addClass('validation_error_border');
        }

        $("#submit").click(function(){
            var from=$("#from").val();
            var to=$("#to").val();
            var fromvalue=$("#fromvalue").val();
            var tovalue=$("#tovalue").val();
            if(from!=fromvalue)
            {
                $.toaster({ priority : 'danger', title : 'Title', message : 'Enter correct from place'});
                return false;
            }
            else if(to!=tovalue)
            {
                $.toaster({ priority : 'danger', title : 'Title', message : 'Enter correct to place'});
                return false;
            }
            else
            {
                return true;
            }
        });

    });
    function initialize() 
    {
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
        var place = autocomplete.getPlace();
        if(place.geometry)
        {
            var fromLat=autocomplete.getPlace().geometry.location.lat();
            var toLat=autocomplete.getPlace().geometry.location.lng();
            $("#fromplace").val(place.name); 
            $("#fromvalue").val($("#from").val());   
            getCityName(fromLat,toLat,1);
        }
        else
        {
            $("#fromplace").val("");
            $("#fromcity").val("");
            $("#fromvalue").val("");
        }
        
        //get city name from lat lng
        
    }
    function fillInAddress1()
    {
        // Get the place details from the autocomplete object.
        var place = autocomplete1.getPlace();
        if(place.geometry)
        {
            var fromLat=autocomplete1.getPlace().geometry.location.lat();
            var toLat=autocomplete1.getPlace().geometry.location.lng();
            $("#toplace").val(place.name);
            $("#tovalue").val($("#to").val());
            //get city name from lat lng
            getCityName(fromLat,toLat,2);
        }
        else
        {
            $("#toplace").val("");
            $("#tocity").val("");
            $("#tovalue").val("");
        }
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
                            $("#from").removeClass('validation_error_border');
                            $("#fromerror").text('');
                            $("#fromerror").hide();
                        }
                        else if(part==2)
                        {
                            $("#tocity").val(city);
                            $("#to").removeClass('validation_error_border');
                            $("#toerror").text('');
                            $("#toerror").hide();
                        }
                        else
                        {
                            $("#fromcity").val('');
                            $("#tocity").val('');
                            $("#fromplace").val('');
                            $("#toplace").val('');
                            $("#fromvalue").val('');
                            $("#tovalue").val('');  
                        }        
                    }
                    else  
                    {
                        
                        if(part==1)
                        {
                            $("#fromcity").val('');
                            $("#fromplace").val('');
                            $("#fromvalue").val('');
                        }
                        else if(part==2)
                        {
                            $("#tocity").val('');
                            $("#toplace").val('');
                            $("#tovalue").val('');   
                        }
                        else
                        {
                            $("#fromcity").val('');
                            $("#tocity").val('');
                            $("#fromplace").val('');
                            $("#toplace").val('');
                            $("#fromvalue").val('');
                            $("#tovalue").val('');   
                        }
                    }
                }
                else 
                {
                    if(part==1)
                    {
                        $("#fromcity").val('');
                        $("#fromplace").val('');
                        $("#fromvalue").val('');
                    }
                    else if(part==2)
                    {
                        $("#tocity").val('');
                        $("#toplace").val('');
                        $("#tovalue").val('');   
                    }
                    else
                    {
                        $("#fromcity").val('');
                        $("#tocity").val('');
                        $("#fromplace").val('');
                        $("#toplace").val('');
                        $("#fromvalue").val('');
                        $("#tovalue").val('');   
                    }
                }
            }
        );
    }
    /*function getAddressFromPlace()
    {
        for (var component in componentForm) 
        {
            document.getElementById(component).value = '';
            document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for(var i = 0; i < place.address_components.length; i++) 
        {
            var addressType = place.address_components[i].types[0];
            if(componentForm[addressType]) 
            {
                var val = place.address_components[i][componentForm[addressType]];
                document.getElementById(addressType).value = val;
            }
        }
    }*/
</script>
@endsection