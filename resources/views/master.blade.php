<!DOCTYPE html>
<html class="no-js">
	<head>
		@yield('head')
		@include("includes.header")
	</head>
	<body>
		<div class="se-pre-con"></div>
		{{--for sidebar--}}
		@section('sidebar')
		@show
		{{-- for navigation--}}
		@yield('nav')
		{{--for body content--}}
		@yield('content')
		{{--for particular page js --}}
		@yield('js')
		
		@include("includes.footer")
	</body>
</html>
