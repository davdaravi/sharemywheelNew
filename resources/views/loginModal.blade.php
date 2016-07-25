{{--login modal--}}
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" name="loginform" id="loginform" action="{{route('post.login.check')}}">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="zmdi zmdi-close"></i>
                    </button>
                    <h4 class="modal-title">Login</h4>
                </div>
                <div class="modal-body">                       
                        <label id="commonlogin" class="validation_error loginerror" style="padding-left:15px"><b>@if($errors->has('error')){{ $errors->first('error') }}@endif</b></label>                
                        <div class="col-md-12">
                            <label class="control-label">Username</label>
                            <input type="text" name="username" id="username" value="@if(old('username')){{ old('username') }}@else{{""}}@endif" placeholder="Enter Username/Email Id" class="form-control" required/>
                            <label id="unameerror" class="validation_error loginerror" style="padding:0px"><b>@if($errors->has('username')){{ $errors->first('username') }}@endif</b></label>                
                        </div>

                        <div class="col-md-12 margin-top-10">
                            <label class="control-label">Password</label>
                            <input type="password" name="password" id="password" value="@if(old('password')){{ old('password') }}@else{{""}}@endif" placeholder="Enter Password" class="form-control" required/> 
                            <label id="passworderror" class="validation_error loginerror" style="padding:0px"><b>@if($errors->has('password')){{ $errors->first('password') }}@endif</b></label>                
                        </div>
                        <div class="col-md-12 margin-top-10">
                            <a href="#" class="frgpassword">Forgot Password?</a>
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                        <input type="hidden" name="token" value="{{config('app.token')}}"/>
                        <div class="clearfix"></div>
                    
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" name="submit" id="submit" class="btn btn-primary" value="Login" />
                </div>
            </div>
        </form>
    </div>
    <div class="clearfix"></div>
</div>
{{--sign up modal--}}
<div class="modal fade" id="signUpModal" tabindex="-1">
    <div class="modal-dialog">
        <form name="signupform" method="POST" action="{{route('post.signup')}}">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="zmdi zmdi-close"></i>
                    </button>
                    <h4 class="modal-title">Sign Up</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="col-md-12">
                            <label class="control-label">Username</label>
                            <input type="text" name="signuser" id="signuser" placeholder="Enter Username" value="@if(old('signuser')){{ old('signuser') }}@else{{""}}@endif" class="form-control" required/>
                            @if($errors->has('signuser'))<label class="validation_error signerror" style="padding:0px"><b>{{ $errors->first('signuser') }}</b></label>@endif                
                        </div>

                        <div class="col-md-12 margin-top-10">
                            <label class="control-label">Password</label>
                            <input type="password" placeholder="Enter Password" name="signpassword" id="signpassword" value="@if(old('signpassword')){{ old('signpassword') }}@else{{""}}@endif" class="form-control" required/>
                            @if($errors->has('signpassword'))<label class="validation_error signerror" style="padding:0px"><b>{{ $errors->first('signpassword') }}</b></label>@endif                
                        </div>

                        <div class="col-md-12 margin-top-10">
                            <label class="control-label">Confirm Password</label>
                            <input type="password" placeholder="Enter Confirm Password" value="@if(old('signconfirm')){{ old('signconfirm') }}@else{{""}}@endif" name="signconfirm" id="signconfirm" class="form-control" required/>
                            @if($errors->has('signconfirm'))<label class="validation_error signerror" style="padding:0px"><b>{{ $errors->first('signconfirm') }}</b></label>@endif
                        </div>

                        <div class="col-md-12 margin-top-10">
                            <label class="control-label">Email Address</label>
                            <input type="email" placeholder="Enter Email Address" name="signemail" value="@if(old('signemail')){{ old('signemail') }}@else{{""}}@endif" id="signemail" class="form-control" required/>
                            @if($errors->has('signemail'))<label class="validation_error signerror" style="padding:0px"><b>{{ $errors->first('signemail') }}</b></label>@endif
                        </div>

                        <div class="col-md-12 margin-top-10">
                            <label class="control-label">Contact Number</label>
                            <input type="number" placeholder="Enter Contact Number" min="1111111111" max="9999999999" name="signcontact" value="@if(old('signcontact')){{ old('signcontact') }}@else{{""}}@endif" id="signcontact" class="form-control" required/>
                            @if($errors->has('signcontact'))<label class="validation_error signerror" style="padding:0px"><b>{{ $errors->first('signcontact') }}</b></label>@endif
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                        <input type="hidden" name="token" value="{{config('app.token')}}"/>
                        <div class="clearfix"></div>
                    </form>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" name="signupSubmit" id="signupSubmit" class="btn btn-primary" value="Sign Up" />
                </div>
            </div>
            
        </form>
    </div>
    <div class="clearfix"></div>
</div>
{{--forgot password modal--}}
<div class="modal fade" id="forgotPassword" tabindex="-1">
    <div class="modal-dialog">
        <form name="forgotPasswordForm" id="forgotPasswordForm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="zmdi zmdi-close"></i>
                    </button>
                    <h4 class="modal-title">Forgot Password</h4>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                <div class="modal-body">
                        <div class="col-md-12">
                            <label class="control-label">Enter Email</label>
                            <input type="text" name="forgotemail" id="forgotemail" value="" placeholder="Enter Email Id" class="form-control"/>
                            <label id="forgotemail_msg" class="validation_error" style="padding:0px"></label>                
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" name="btn_forgot" id="btn_forgot" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <div class="clearfix"></div>
</div>