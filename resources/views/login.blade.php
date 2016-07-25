@extends("master")

@section("head")
    <title>Share My Wheel - login</title>
    <link href="{{URL::asset('css/jquery.bxslider.css')}}" rel="stylesheet">
@endsection
@section("nav")
    @include("includes.beforeLoginSidebar")
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
    <div class="col-xs-12 col-sm-6 col-md-4 carImages">
        <img src="images/slider_front_img.png">
    </div>
</div>
<div class="clearfix"></div>
<!-- <div class="container">
    <div class="col-xs-12 col-sm-6 col-md-4 searchForm">
        <div class="col-md-12">
            <h3>Book a Car</h3>
        </div>
        <div class="clearfix"></div><hr/>
        <form role="form" method="POST" action="{{route('post.ride.search')}}">
            <div class="form-group col-md-12">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><i class="zmdi zmdi-pin zmdi-hc-lg"></i></span>
                    <input type="text" name="from" id="from" class="form-control" placeholder="From" />
                    <input type="hidden" name="fromcity" id="fromcity"/>
                </div>
            </div>
            <div class="form-group col-md-12">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><i class="zmdi zmdi-pin zmdi-hc-lg"></i></span>
                    <input type="text" name="to" id="to" class="form-control" placeholder="To" />
                    <input type="hidden" name="tocity" id="tocity"/> 
                </div>
            </div>

            <div class="form-group col-md-12">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><i class="zmdi zmdi-calendar-note zmdi-hc-lg"></i></span>
                    <input type="text" class="form-control" name="rideFindDatepicker" id="rideFindDatepicker" placeholder="Date Picker" />    
                </div>
            </div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="col-md-12 text-right">
                <input type="submit" name="submit" id="submit" value="Search" class="btn btn-primary">
            </div>
        </form>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-4 carImages">
        <img src="images/slider_front_img.png">
    </div>
</div>
<div class="clearfix"></div> -->
<div class="container margin-top-25 blocks" style="margin-bottom:0px">
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

@if(count($ad)>0)
<div class="container" style="margin-bottom:50px">
    <div class="col-md-12">
        <div class="slider2">
            @for($i=0;$i<count($ad);$i++)
                <div class="slide"><a href="{{$ad[$i]->url}}" target="_blank"><img src="{{asset($ad[0]->image_path)}}"></a></div>
            @endfor
        </div>
    </div>
</div>
@endif
<div class="clearfix"></div>
@include('loginModal')
@endsection
@section("js")
    <script type="text/javascript" src="{{URL::asset('js/jquery.bxslider.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/jquery.toaster.js')}}"></script>
    <!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDzn37QRF8u0MBNXd2MGsTQs1IOBUXMH4Q&libraries=places&callback=initialize" async defer></script>-->
    <script type="text/ecmascript">
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

        var autocomplete,autocomplete1;
        $(document).ready(function(){
         //   initialize();
            $('#rideFindDatepicker').datetimepicker({
                lang:'ch',
                timepicker:false,
                format:'d-m-Y',
                minDate:'-1970/01/01', // yesterday is minimum date
            });

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

            $('.slider2').bxSlider({
                slideWidth: 220,
                minSlides: 5,
                maxSlides:5,
                slideMargin: 10,
                auto: true,
                autoControls: true
              });

            $("#submit").click(function(){
                $(".loginerror").each(function(){
                    $(this).text('');
                });
            });
            $("#signupSubmit").click(function(){
                $(".signerror").each(function(){
                    $(this).text('');
                });
            });
        });
        /*function initialize() 
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
          
            $("#fromcity").val(place.name);
            console.log(place.name);
            var from_lat=autocomplete.getPlace().geometry.location.lat();
            var to_lat=autocomplete.getPlace().geometry.location.lng();
            //****************************
            get_city_name(from_lat,to_lat);
            //****************************
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
        }
        function fillInAddress1()
        {
            // Get the place details from the autocomplete object.
            var place = autocomplete1.getPlace();
            console.log(place);
            $("#tocity").val(place.name);
            var from_lat=autocomplete1.getPlace().geometry.location.lat();
            var to_lat=autocomplete1.getPlace().geometry.location.lng();
             //****************************
            get_city_name(from_lat,to_lat);
            //****************************
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
        }
        function get_city_name(lat,lng)
        {

            var geocoder;
            geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(lat, lng);

            geocoder.geocode(
                {'latLng': latlng}, 
                function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                var add= results[0].formatted_address ;
                                var  value=add.split(",");

                                count=value.length;
                                country=value[count-1];
                                state=value[count-2];
                                city=value[count-3];
                                alert("city name is: " + city);
                            }
                            else  {
                                alert("address not found");
                            }
                    }
                     else {
                        alert("Geocoder failed due to: " + status);
                    }
                }
            );
        }*/
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
@endsection