@extends("master")

@section("head")
    <title>Share My Wheel - ride list</title>
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
    <style type="text/css">
    .PD10{padding-left: 10px !important;}
    div.stars,div.stars1
    {
        width: 92px;
        display: inline-block;
    }
    input.star,input.star1 { display: none; }
    label.star,label.star1 {
        float: right;
        padding: 2px;
        font-size: 15px;
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
<!-- main Content -->
<div class="container">
    <div class="col-md-12">
        <div class="sharingRideSearch">
            <form role="form">
                <div class="form-group col-lg-4 col-md-4 col-sm-5 margin-top-10">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="zmdi zmdi-pin zmdi-hc-lg zmdi-green"></i></span>
                        <input type="text" name="from" id="from" value="@if(isset($ride['from'])){{$ride['from']}}@elseif(isset($_COOKIE['fromoriginal'])){{$_COOKIE['fromoriginal']}}@else{{""}}@endif" class="form-control" placeholder="From" />   
                    </div>
                </div>
                <div class="col-lg-1 text-center margin-top-10">
                    <button type="button" class="btn btn-primary" id="reverseBtn" name="reverseBtn"><i class="zmdi zmdi-repeat zmdi-hc-lg"></i></button>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-5 margin-top-10">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="zmdi zmdi-pin zmdi-hc-lg zmdi-red"></i></span>
                        <input type="text" name="to" id="to" class="form-control" value="@if(isset($ride['to'])){{$ride['to']}}@elseif(isset($_COOKIE['tooriginal'])){{$_COOKIE['tooriginal']}}@else{{""}}@endif" placeholder="To" /> 
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 margin-top-10">
                    <input type="button" class="btn btn-primary" name="submitBtn" id="submitBtn" value="Search"/>
                </div>
                <input type="hidden" name="fromvalue" id="fromvalue" value="@if(isset($ride['from'])){{$ride['from']}}@elseif(isset($_COOKIE['fromoriginal'])){{$_COOKIE['fromoriginal']}}@else{{""}}@endif"/>
                <input type="hidden" name="tovalue" id="tovalue" value="@if(isset($ride['to'])){{$ride['to']}}@elseif(isset($_COOKIE['tooriginal'])){{$_COOKIE['tooriginal']}}@else{{""}}@endif"/>
                <input type="hidden" name="fromcity" id="fromcity" value="@if(isset($ride['fromcity'])){{$ride['fromcity']}}@elseif(isset($_COOKIE['fromcity'])){{$_COOKIE['fromcity']}}@else{{""}}@endif"/>
                <input type="hidden" name="fromplace" id="fromplace" value="@if(isset($ride['fromplace'])){{$ride['fromplace']}}@elseif(isset($_COOKIE['fromplace'])){{$_COOKIE['fromplace']}}@else{{""}}@endif"/>
                <input type="hidden" name="tocity" id="tocity" value="@if(isset($ride['tocity'])){{$ride['tocity']}}@elseif(isset($_COOKIE['tocity'])){{$_COOKIE['tocity']}}@else{{""}}@endif"/>
                <input type="hidden" name="toplace" id="toplace" value="@if(isset($ride['toplace'])){{$ride['toplace']}}@elseif(isset($_COOKIE['toplace'])){{$_COOKIE['toplace']}}@else{{""}}@endif"/>
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                <input type="hidden" name="token" value="{{config('app.token')}}"/>
            </form>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<!-- filter and Body -->
<div class="container sharingContentBody">
    <div class="col-md-12">
        <div class="col-md-3 sharingRideFilter">
            <div class="col-md-12">
                <label class="control-label">Date :</label>
                <input type="text" id="fromdate" name="fromdate" value="@if(isset($ride['date'])){{$ride['date']}}@elseif(isset($_COOKIE['ridedate'])){{$_COOKIE['ridedate']}}@else{{""}}@endif" placeholder="Select Date" class="form-control"/>
            </div>
            <div class="clearfix"></div><hr/>

            <div class="col-md-12 margin-top-5">
                <label class="control-label">Photo :</label>
                <div class="photoSelect">
                    <label><input type="radio" name="photo" value="photo"/><span class="PD10">With Photo Only</span></label>
                </div>
                <div class="photoSelect">
                    <label><input type="radio" name="photo" value="All" checked/><span class="PD10">All</span></label>
                </div>
            </div>
            <div class="clearfix"></div><hr/>

            <div class="col-md-12 margin-top-5">
                <label class="control-label">Ride Type :</label>
                <div class="photoSelect">
                    <label><input type="radio" name="ridetype" value="All" checked/><span class="PD10">All</span></label>
                </div>
                <div class="photoSelect">
                    <label><input type="radio" name="ridetype" value="daily"/><span class="PD10">Daily</span></label>
                </div>
                <div class="photoSelect">
                    <label><input type="radio" name="ridetype" value="ladies"/><span class="PD10">Ladies only</span></label>
                </div>
                
            </div>
            <div class="clearfix"></div><hr/>

            <div class="col-md-12 margin-top-5 filter_div_height">
                <label class="control-label">Car Comfort :</label>
                <div>
                    <label><input type="checkbox" class="comfortAll" style="vertical-align:inherit" name="carComfortall" value="all"/><span class="PD10">All</span></label>
                </div>
                @for($i=0;$i<count($comfort);$i++)
                <div>   
                    <label><input type="checkbox" class="comfort" style="vertical-align:inherit" name="carComfort[]" value="{{$comfort[$i]->id}}"/><span class="PD10">{{$comfort[$i]->name}}</span>
                </div>
                @endfor 
            </div>
            <div class="clearfix"></div><hr/>

            <div class="col-md-12 margin-top-5">
                <label class="control-label">Vehicle Type :</label>
                <div>
                    <select class="form-control" name="vehicle_type" id="vehicle_type">
                        <option value="all">All</option>
                        <option value="2">four wheeler</option>
                        <option value="1">two wheeler</option>
                    </select>
                </div>
            </div>
            <div class="clearfix"></div><hr/>

        </div>
        <div class="col-md-9 sharingRideGrid">
            <h5 class="text-center" style="font-family:times new Roman !important;font-weight:bold">
                <?php
                if(isset($ride) && isset($ridelist))
                {
                    echo count($ridelist)?> rides from <span style="color:green"><?php echo $ride['from']?></span>  To  <span style="color:red"><?php echo $ride['to']?></span>
                <?php
                } 
                ?>
            </h5>
            <div class="clearfix"></div><hr/>
            
           
            <table id="ridelist" class="table table-striped table-hover table-responsive" cellspacing="0" width="100%" style="border-bottom:1px solid #ccc">
            <thead>
                <tr>
                    <th>59  rides from Gujarat</th>
                    <th>Ahmedabad, Gujarat to Rajkot,</th>
                    <th>fgfg</th>
                </tr>
            </thead>
            <tbody>
                   
            </tbody>
        </table>
         
        </div>
    </div>
</div>

@endsection
@section("js")
<script type="text/javascript" src="{{URL::asset('js/jquery.toaster.js')}}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDzn37QRF8u0MBNXd2MGsTQs1IOBUXMH4Q&libraries=places&callback=initialize" async defer></script>
<script type="text/javascript">
var autocomplete,autocomplete1,rideTable;
var comfortArray=Array();
var numberNotChecked =$('input[name="carComfort[]"]').not(':checked').length;

$(document).ready(function() {
    @if(isset($_COOKIE['fromcity']))
        alert("fromcity"+'{{$_COOKIE['fromcity']}}');
    @endif

    @if(isset($_COOKIE['fromplace']))
        alert("fromplace"+'{{$_COOKIE['fromplace']}}');
    @endif

    @if(isset($_COOKIE['fromoriginal']))
        alert("fromoriginal"+'{{$_COOKIE['fromoriginal']}}');
    @endif

    @if(isset($_COOKIE['tocity']))
        alert("tocity"+'{{$_COOKIE['tocity']}}');
    @endif

    @if(isset($_COOKIE['toplace']))
        alert("toplace"+'{{$_COOKIE['toplace']}}');
    @endif

    @if(isset($_COOKIE['tooriginal']))
        alert("tooriginal"+'{{$_COOKIE['tooriginal']}}');
    @endif

    $("input[name='ridetype']").click(function(){
        rideTable.fnDraw();
    });
    $("input[name='photo']").click(function(){
        rideTable.fnDraw();
    });
    $("#vehicle_type").change(function(){
        rideTable.fnDraw();
    });
    $('#fromdate').datetimepicker({
            lang:'ch',
            timepicker:false,
            format:'d-m-Y',
            minDate:new Date(),
            onChangeDateTime:function(dp,$input){
                rideTable.fnDraw();
            },
            onSelectDate:function(ct,$i){
                rideTable.fnDraw();
            }
    });
    

    $(".comfortAll").click(function(){
        if($(this).is(':checked'))
        {
           $(".comfort").each(function(){
                this.checked=true;
                var a=$(this).val();
                if(jQuery.inArray(parseInt(a),comfortArray) !== -1)
                {

                }
                else
                {
                    comfortArray.push(parseInt(a));
                }
           });
        }
        else
        {
            comfortArray=[];
            $(".comfort").each(function(){
                this.checked=false;
            });
        }
        rideTable.fnDraw();
    });

    $(".comfort").click(function(){
        var a=$(this).val();
        if($(this).is(":checked"))
        {
            if(jQuery.inArray(parseInt(a),comfortArray) !== -1)
            {

            }
            else
            {
                comfortArray.push(parseInt(a));
            }
            
            if(comfortArray.length==numberNotChecked)
            {
                $(".comfortAll").prop("checked",true);
            }
        }
        else
        {
            var b = comfortArray.indexOf(parseInt(a));
            if(b>-1)
            {
                comfortArray.splice(b,1);
            }
            else
            {

            }
            if($(".comfortAll").is(':checked'))
            {
                $(".comfortAll").prop("checked",false);
            }
        }
        rideTable.fnDraw();
    });
    rideTable=$('#ridelist').dataTable({
                oLanguage: {
                            sProcessing: "<img src='/images/load.gif'>"
                },
                searchHighlight: true,
                /*"language": {
                    "url": "dataTables.gujarati.lang"
                },
                */ 
                scrollY:        "350px",
                scrollX:        true,
                
                "pagingType": "full_numbers",
                "bProcessing": true,
                "bServerSide": true,
                "sServerMethod": "GET",
                "sAjaxSource": '{!! route("get.ride.list.data") !!}',
                "iDisplayLength": 10,
                "fnServerParams": function(aoData) {
                    fromorigin = $("#from").val();
                    fromplace = $("#fromplace").val();
                    fromcity = $("#fromcity").val();
                    toplace = $("#toplace").val();
                    tocity = $("#tocity").val();
                    toorigin = $("#to").val();
                    fromdate=$("#fromdate").val();
                    rideType=$('input[name=ridetype]:checked').val();
                    photo=$('input[name=photo]:checked').val();
                    vehicle_type=$("#vehicle_type").val();
                    aoData.push({"name": "date", "value": fromdate},
                    {"name": "fromplace", "value": fromplace},
                    {"name": "fromorigin", "value": fromorigin},
                    {"name": "fromcity", "value": fromcity},
                    {"name": "toplace", "value": toplace},
                    {"name": "tocity", "value": tocity},
                    {"name": "toorigin", "value": toorigin},
                    {"name": "ridetype", "value": rideType},
                    {"name": "photo", "value": photo},
                    {"name": "vehicle_type", "value": vehicle_type},
                    {"name": "comfort", "value": comfortArray}
                    );
                },
                "searchHighlight": true,
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                /*"aaSorting": [[1, 'asc']],*/
                "aoColumns": [
                    {"bVisible": true, "bSearchable": true, "bSortable": false},
                    {"bVisible": true, "bSearchable": true, "bSortable": false},
                    {"bVisible": true, "bSearchable": true, "bSortable": false}
                ]
    });
    $("#submitBtn").click(function(){
        /*var date=$("#fromdate").val();
        var d=new Date();
        if(d.getMonth()+1<10)
        {
            var month="0"+(d.getMonth()+1);
        }
        else
        {
            var month=d.getMonth()+1;
        }
        var dd=d.getDate()+"-"+month+"-"+d.getFullYear().toString();
        
        if(date=="")
        {
            $.toaster({ priority : 'danger', title : 'Title', message : 'Select Date'});
            return false;
        }
        else if(dd>date)
        {
            $.toaster({ priority : 'danger', title : 'Title', message : 'Date must be greater or equals to current date'});
            return false;
        }
        else
        {*/
            searchTable();
        //}
        
    });
    $("#reverseBtn").click(function(){
        var from=$("#from").val();
        var fromplace=$("#fromplace").val();
        var fromcity=$("#fromcity").val();
        var fromvalue=$("#fromvalue").val();
        var to=$("#to").val();
        var toplace=$("#toplace").val();
        var tocity=$("#tocity").val();
        var tovalue=$("#tovalue").val();
        $("#from").val(to);
        $("#fromplace").val(toplace);
        $("#fromcity").val(tocity);
        $("#fromvalue").val(tovalue);
        $("#to").val(from);
        $("#toplace").val(fromplace);
        $("#tocity").val(fromcity);
        $("#tovalue").val(fromvalue);
        searchTable();
    });
});
function searchTable()
{
    var currentFrom=$("#from").val();
   

    var currentTo=$("#to").val();
    
    if(currentFrom=="")
    {
        $("#fromplace").val("");
        $("#fromcity").val("");
        $("#fromvalue").val("");
    }
    if(currentTo=="")
    {
        $("#toplace").val("");
        $("#tocity").val("");
        $("#tovalue").val("");
    }
    var oldFrom=$("#fromvalue").val();
    var oldTo=$("#tovalue").val();
    
    if(currentFrom!=oldFrom)
    {
        $.toaster({ priority : 'danger', title : 'Title', message : 'Invalid From City'});
    }
    else if(currentTo!=oldTo)
    {
        $.toaster({ priority : 'danger', title : 'Title', message : 'Invalid To City'});   
    }
    else if(currentFrom=="" && currentTo!="")
    {
        $.toaster({ priority : 'danger', title : 'Title', message : 'Enter From City'});   
    }
    else
    {
        rideTable.fnDraw();
    }
}
function change2(date) 
{
    rideTable.fnDraw();
}
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
    var fromLat=autocomplete.getPlace().geometry.location.lat();
    var toLat=autocomplete.getPlace().geometry.location.lng();
    $("#fromplace").val(place.name);
    $("#fromvalue").val($("#from").val());
    //get city name from lat lng
    getCityName(fromLat,toLat,1);
}
function fillInAddress1()
{
    // Get the place details from the autocomplete object.
    var place = autocomplete1.getPlace();
    var fromLat=autocomplete1.getPlace().geometry.location.lat();
    var toLat=autocomplete1.getPlace().geometry.location.lng();
    $("#toplace").val(place.name);
    $("#tovalue").val($("#to").val());
    //get city name from lat lng
    getCityName(fromLat,toLat,2);
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
                        
                    }
                    else if(part==2)
                    {
                        $("#tocity").val(city);
                        $("#to").removeClass('validation_error_border');
                    }
                    else
                    {
                        $("#fromcity").val('');
                        $("#tocity").val('');
                        $("#fromplace").val('');
                        $("#toplace").val('');
                        $("#from").val('');
                        $("#to").val('');   
                    }        
                }
                else  
                {
                    
                    if(part==1)
                    {
                        $("#fromcity").val('');
                        $("#fromplace").val('');
                        $("#from").val('');
                    }
                    else if(part==2)
                    {
                        $("#tocity").val('');
                        $("#toplace").val('');
                        $("#to").val('');   
                    }
                    else
                    {
                        $("#fromcity").val('');
                        $("#tocity").val('');
                        $("#fromplace").val('');
                        $("#toplace").val('');
                        $("#from").val('');
                        $("#to").val('');   
                    }
                }
            }
            else 
            {
                if(part==1)
                {
                    $("#fromcity").val('');
                    $("#fromplace").val('');
                    $("#from").val('');
                }
                else if(part==2)
                {
                    $("#tocity").val('');
                    $("#toplace").val('');
                    $("#to").val('');   
                }
                else
                {
                    $("#fromcity").val('');
                    $("#tocity").val('');
                    $("#fromplace").val('');
                    $("#toplace").val('');
                    $("#from").val('');
                    $("#to").val('');   
                }
            }
        }
    );
}
</script>
@endsection