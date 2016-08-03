<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/ //<li><a href="{{url('/github')}}" class="dropdown-toggle">Github</a></li>-->
Route::group(['domain' => 'www.smw.com'], function () {
Route::group(['middleware' => ['web']], function () {

    Route::get('/getCity',[
        'as'    =>  'get.city.name',
        'uses'  =>  'userController@getCityName'
    ]);
    Route::get('/', [
        'as'    =>  'get.login',
        'uses'  =>  'loginController@getLogin'
    ])->middleware(['before']);
    //when click on login page
    Route::get('/login',[
        'as'    =>  'get.login',
        'uses'  =>  'loginController@getLogin'
    ]);

    Route::get('/signup',function(){
        return redirect('/');
    });
    
    Route::post('/login/',[
            'as'    =>  'post.login.check',
            'uses'  =>  'loginController@checkLogin'
    ])->middleware(['token']);

    Route::post('/signup/',[
            'as'    =>  'post.signup',
            'uses'  =>  'loginController@signup'
    ])->middleware(['token']);

    Route::get('/facebook',[
            'as'    =>  'get.facebook',
            'uses'  =>  'facebookController@facebook'
    ]);

    Route::get('/fb_callback',[
            'as'    =>  'get.facebook.callback',
            'uses'  =>  'facebookController@handleProviderCallback'
    ]);

    Route::get('/github',[
        'as'    =>  'get.github',
        'uses'  =>  'gitController@gitLogin'
    ]);

    Route::get('/githubresponse',[
        'as'    =>  'get.git.response',
        'uses'  =>  'gitController@githubresponse'
    ]);

    Route::get('/terms_condition',function(){
        return view('termsCondition');
    });

    Route::get('/privacy_policy',function(){
        return view('privacyPolicy');
    });

    Route::get('/contact',function(){
        return view('contact');
    });

    Route::get('/about',function(){
        return view('about');
    });

    Route::post('/contact',[
        'as'    =>  'post.contact',
        'uses'  =>  'contactController@contact'
    ]);

    Route::put('/forgotPassword',[
        'as'    =>  'forgot.password',
        'uses'  =>  'userController@forgotPassword'
    ]);

    Route::get('/latest_news',[
        'as'    =>  'get.latest.news',
        'uses'  =>  'userController@latest_news'
    ]);

    Route::get('/coupan',[
        'as'    =>  'get.coupan',
        'uses'  =>  'userController@coupanCode'
    ]);

    Route::get('/refund',function(){
        return view('returnCancellation');
    });

    Route::get('/pricing',function(){
        return view('pricing');
    });
});

Route::group(['middleware'=>['web','login','token']],function(){
   
    Route::post('/ridelist/',[
            'as'    => 'post.ride.search',
            'uses'  => 'rideController@rideSearch'
    ]);
    
    Route::post('/ridesearch/',[
            'as'    =>  'post.rideListSearch',
            'uses'  =>  'rideController@rideListSearch'
    ]);    

});

Route::group(['middleware'=>['web','login']],function(){

    Route::get('/findride', [
        'as'    =>  'get.find.ride',
        'uses'  =>  'rideController@findride'
    ]);

    Route::get('/userProfile/{id}/{rideid}',[
        'as'    =>  'get.profile',
        'uses'  =>  'userController@getProfile'
    ]);

    Route::get('/offerride',[
        'as'    =>  'get.view.offer',
        'uses'  =>  'offerRideController@viewOfferRide'
    ]);

    Route::get('/logout',function(){
        session()->flush();
        if (isset($_SERVER['HTTP_COOKIE']))
        {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie)
            {
                $mainCookies = explode('=', $cookie);
                $name = trim($mainCookies[0]);
                //setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
                unset($_COOKIE[$name]);
            }
        }
        return redirect('/');
    });

    Route::get('/ridedetail/{id}',[
        'as'    =>  'get.ride.details',
        'uses'  =>  'rideController@getRideDetail'
    ]);
    Route::get('/rideinfo',[
        'as'    =>  'get.ride.seat',
        'uses'  =>  'rideController@getRideInfo'
    ]);
    Route::get('/OfferRideDetail/{id}',[
        'as'    =>  'get.ride.offer',
        'uses'  =>  'rideController@getRideOffer'
    ]);

    /*Route::get('/carowner', function () {
        return view('carOwner');
    });*/

    Route::get('/dashboard', [
        'as'    =>  'get.dashboard',
        'uses'  =>  'dashboardController@viewDashboard'
    ]);
    Route::get('/amount',[
        'as'    =>  'get.wallet.amount',
        'uses'  =>  'dashboardController@amount'
    ]);

    Route::get('/home', [
        'as'    =>  'get.home',
        'uses'  =>  'loginController@loadhome'
    ]);
    //when click on url
    Route::get('/ridelist', [
        'as'    =>  'get.ride.list',
        'uses'  =>  'rideController@getRideList'
    ]);
    //when click on url
    /*Route::get('/ridelistsearch',function(){
        return view('rideList');
    });*/
    //get quantity
    Route::get('/userDetails',[
        'as'    =>  'get.user.details',
        'uses'  =>  'userController@getUserDetails'
    ]);

    Route::post('/userDetails',[
        'as'    =>  'post.user.details',
        'uses'  =>  'userController@updateUserDetails'
    ]);
    
    Route::post('/payment',[
        'as'    =>  'post.book.ccavenu.ride',
        'uses'  =>  'rideController@bookCcavenu'
    ]);
    Route::post('/emailConfirmation',[
        'as'    =>  'post.email.confirmation',
        'uses'  =>  'userController@emailConfirmation'
    ]);

    Route::post('/mobileConfirmation',[
        'as'    =>  'post.mobile.confirmation',
        'uses'  =>  'userController@mobileConfirmation' 
    ]);

    Route::post('/updateUserPreference',[
        'as'    =>  'post.user.preference',
        'uses'  =>  'userController@savePreference'
    ]);

    Route::post('/imagesubmit',[
        'as'    =>  'post.image.upload',
        'uses'  =>  'userController@imageUpload'
    ]);

    Route::post('/licencesubmit',[
        'as'    =>  'post.licence.image.upload',
        'uses'  =>  'userController@licenceUpload'
    ]);
    Route::post('/sendMobileCode',[
        'as'    =>  'post.send.mobile.code',
        'uses'  =>  'userController@sendMobileCode'
    ]);
    Route::post('/sendEmailCode',[
        'as'    =>  'post.send.email.code',
        'uses'  =>  'userController@sendEmailCode'
    ]);
    Route::post('/carAdd',[
        'as'    =>  'post.car.add',
        'uses'  =>  'carController@addCar'
    ]);

    Route::get('/getCarDetails',[
        'as'    =>  'get.car.detail',
        'uses'  =>  'carController@getCarDetails'
    ]);

    Route::post('/carUpdate',[
        'as'    =>  'post.car.update',
        'uses'  =>  'carController@updateCar'
    ]);

    //delete car
    Route::post('/deleteCar',[
        'as'    =>  'post.car.delete',
        'uses'  =>  'carController@deleteCar'
    ]);

    Route::get('/carList',[
        'as'    =>  'get.car.list',
        'uses'  =>  'carController@carList'
    ]);
    
    Route::get('/rideSearchList',[
        'as'    =>  'get.ride.list.data',
        'uses'  =>  'rideController@carList'
    ]);

    Route::get('/exchangeRating',[
        'as'    =>  'get.exchange.rating',
        'uses'  =>  'ratingController@getExchangeRating'
    ]);
    Route::post('/ratingExchange',[
        'as'    =>  'post.rating.exchange',
        'uses'  =>  'ratingController@ratingExchange'
    ]);

    //rating given list
    Route::get('/ratingGiven',[
        'as'    =>  'get.rating.given.list',
        'uses'  =>  'ratingController@ratingGiven'
    ]);
    //rating received list
    Route::get('/ratingReceived',[
        'as'    =>  'get.rating.received.list',
        'uses'  =>  'ratingController@ratingReceived'
    ]);

    Route::post('/offerride',[
        'as'    =>  'post.offer.ride',
        'uses'  =>  'offerRideController@createRide'
    ]);

    Route::get('/paidTransaction',[
        'as'    =>  'get.paid.transaction',
        'uses'  =>  'dashboardController@paidTransaction'
    ]);

    Route::get('/earnTransaction',[
        'as'    =>  'get.earn.transaction',
        'uses'  =>  'dashboardController@earnTransaction'
    ]);

    Route::get('/getMessage',[
        'as'    =>  'get.chat.message',
        'uses'  =>  'chatController@getMessage'
    ]);

    Route::post('/sendMessage',[
        'as'    =>  'post.send.message',
        'uses'  =>  'chatController@sendMessage'
    ]);

    Route::post('/isTyping',[
        'as'    =>  'post.is.typing',
        'uses'  =>  'chatController@isTyping'
    ]);

    Route::post('/isNotTyping',[
        'as'    =>  'post.not.typing',
        'uses'  =>  'chatController@isNotTyping'
    ]);

    Route::get('/retriveMessage',[
        'as'    =>  'get.retrive.message',
        'uses'  =>  'chatController@retriveMessage'
    ]);

    Route::post('/rideBook',[
        'as'    =>  'post.book.ride',
        'uses'  =>  'rideController@bookRide'
    ]);

    Route::get('/rideBookHistory',[
        'as'    =>  'get.ridebooked.history',
        'uses'  =>  'dashboardController@rideBookHistory'
    ]);

    Route::post('/addCoupan',[
        'as'    =>  'post.coupan.code',
        'uses'  =>  'dashboardController@addCoupan'
    ]);

    Route::post('/getsuccessPayment',[
        'as'    =>  'get.success.payment',
        'uses'  =>  'rideController@ccavenuFeedback'
    ]);

    Route::post('/withdrawAmount',[
        'as'    =>  'post.withdraw.amount',
        'uses'  =>  'userController@withdrawAmount'
    ]);

    Route::get('/bookedRidePaymentMessage',function () {
        return view('bookedRidePaymentMessage');
    });

    Route::put('/changePassword',[
        'as'    =>  'update.password',
        'uses'  =>  'userController@changePassword'
    ]);

    Route::post('/offerrideSuccess',[
        'as'    =>  'ccavenu.offerride.payment',
        'uses'  =>  'offerRideController@ccavenuPayment'
    ]);
});

});
//----------api which requried defult key for access. defultToken-----------------------
 Route::group(['prefix' => 'api/v1', 'middleware' => 'defultToken'], function(){
        Route::post('/login','Api@login');
        Route::get('/getPreference','Api@getPreferenceList');
        Route::post('/register','Api@register');
        Route::post('/users/forgotPassword','Api@forgot_password');
   });   


//api which requried defult key for access. loginToken
 Route::group(['prefix' => 'api/v1', 'middleware' => 'loginToken'], function(){
           
            /* ride route  */
            Route::post('/rides/add','Api@offerAridepost');
            Route::get('/rides/users','Api@MyRide');
            Route::get('/rides','Api@searchRide');
            Route::get('/rides/getmybookride','Api@getMyBookedRide');
            //-----------------------------------
            Route::get('/mail','Api@send_email');
            Route::get('/sms','Api@send_sms');
            //************************************
            //Route::post('/rides','Api@searchRide');//remning  api
            Route::delete('/rides/delete','Api@deleteRide'); //remning  api
            Route::get('/ridemasters','Api@addRideMaster');
            Route::get('/carmasters','Api@addCarMaster');
            Route::get('/rides/searchList','Api@searchList');
            Route::get('/rides/searchRaviList','Api@searchRaviList');
            /* car route  */
            Route::post('/cars/addCar','Api@AddCar');  
            Route::post('/cars/update','Api@UpdateCar');  
            Route::get('/cars/myCar','Api@myCar');//remning  api 
            Route::get('/cars/show','Api@showCar');//remning  api
            Route::delete('/cars/','Api@deleteCar');
            Route::get('/cars/rideDetailsById','Api@OfferRideGetByID');
            Route::get('/cars/RideHistory','Api@earnHistory');
            Route::post('/cars/bookRide','Api@bookRide');
            Route::post('/cars/rideBook','Api@rideBook');
            Route::post('/users/licencepic','Api@uploadLicenceImage');
            /* user profile */
            Route::post('/users/editprofile/','Api@editBasicProfile');
            Route::post('/users/email/','Api@editEmail'); 
            Route::post('/users/changePassword/','Api@changePassword');
            Route::post('/users/savePreference/','Api@SavePreference');
            Route::post('/users/notification/','Api@changeNotification');
            Route::post('/users/photo/','Api@AddProfilepic');
            Route::post('/users/withdrawMoney','Api@withdrawMoney');
            Route::post('/users/phone/','Api@showPhoto');//remning  api
            Route::post('/users/update/','Api@showPhoto');//remning  api
            Route::post('/users/setting/','Api@editEmail'); //remning  api 
            Route::post('/users/ratting/','Api@editEmail'); //remning  api  or table  need to create
            Route::post('/users/phone/','Api@editPhone');
            Route::post('/users/rating/','Api@userRatting');
            Route::get('/users/ratingList/','Api@ratingUserList');
            Route::get('/users/photo/','Api@showPhoto');//remning  api
            Route::get('/users/getpreference','Api@getUserPreference'); 
            Route::get('/users/getWallet','Api@getWallet');
            Route::get('/users/mail','Api@send_email');
            Route::post('/users/change_number','Api@change_contact');
            Route::post('/users/verify_update_contact','Api@verify_update_contact');
            Route::get('/users/panic','Api@panicEmergency');
            Route::get('/users/getProfile','Api@getProfile');

            Route::post('/addCoupanAmount','Api@addCoupanAmount');

            /* user chat*/
            Route::post('/users/sendMessage','Api@sendMessage');
            Route::get('/users/getMessage','Api@receiveMessage');
            Route::get('/users/getChatContact','Api@getChatContact');
              
            Route::post('/check_mobile/','Api@check_mobile_verification');
            Route::post('/check_email/','Api@check_email_verification');
            Route::get('/chat/user','Api@editEmail');  //remning  api
            Route::get('/chat','Api@editEmail');  //remning  api
            Route::post('/chat','Api@editEmail');  //remning  api

            Route::get('/getAd','Api@getAd');
            //reports
            Route::post('/report/rideReport','rideReportController@getData');
                         
             
 });   
