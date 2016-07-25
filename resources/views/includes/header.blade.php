<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<meta Http-Equiv="Cache-Control" Content="no-cache">
<meta Http-Equiv="Pragma" Content="no-cache">
<meta Http-Equiv="Expires" Content="0"> 

<link href="{{URL::asset('css/bootstrap.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('css/roboto.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{URL::asset('css/material.css')}}" type="text/css"/>
<link href="{{URL::asset('css/ripples.css')}}" rel="stylesheet" type="text/css">
<link href="{{URL::asset('css/roboto.css')}}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{URL::asset('css/main.css')}}">
<link rel="stylesheet" href="{{URL::asset('fonts/material-design-iconic-font/css/material-design-iconic-font.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{URL::asset('css/jquery.datetimepicker.css')}}" rel="stylesheet">

<script src="{{URL::asset('js/jquery-1.12.0.min.js')}}"></script> 
<script src="{{URL::asset('js/ripples.js')}}"></script> 

<script type="text/javascript" src="{{URL::asset('js/material.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/dataTables.bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/jquery.datetimepicker.full.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/loading.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function() {
	   // $('#example1').DataTable();
        $.material.init();
        //hide inspect element
        /*$(document).bind("contextmenu",function(e) {
            e.preventDefault();
        });*/
    });
    $(window).load(function() {
		// Animate loader off screen
		$(".se-pre-con").fadeOut("slow");
	});
</script>

