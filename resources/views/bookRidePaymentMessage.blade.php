@extends("master")

@section("head")
    <title>Share My Wheel - find ride</title>
@endsection
@section("nav")
    @include("includes.afterLoginSidebar")
@endsection
@section("content")

@if(Session::has('status'))
<div class="container">
    <div class="col-md-12">
        <h3 class="alert alert-success">{{session('status')}}</h3>
    </div>
</div>
@elseif(Session::has('failure'))
<div class="container">
    <div class="col-md-12">
        <h3 class="alert alert-danger">{{session('failure')}}</h3>
    </div>
</div>
@else
@endif
@endsection
@section("js")
<script type="text/javascript">
$(document).ready(function(){
    @if(Session::has('status'))
    @elseif(Session::has('failure'))
    @else
        window.location.href="www.sharemywheel.info/home";
    @endif
});
</script>
@endsection