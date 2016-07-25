<nav class="navbar navbar-default">
	<div class="container">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">
                	<div class="col-md-3 col-sm-3 col-xs-3">
                		<img src="images/logo.png" width="50" class="mainLogo">
                	</div>
                	<div class="col-md-9 col-sm-9 col-xs-9">
                		<p class="logoContent">ShareMyWheel</p>
                	</div>
                </a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                	<li><a href="#" class="dropdown-toggle login" data-toggle="modal" data-target="#loginModal">Login</a></li>
                    <li><a href="#" class="dropdown-toggle signup" data-toggle="modal" data-target="#signUpModal">Sign Up</a></li>
                    <li><a href="{{url('/facebook')}}" class="dropdown-toggle">Connect with facebook</a></li>
                   
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
        <!--/.container-fluid -->
    </div>
</nav>