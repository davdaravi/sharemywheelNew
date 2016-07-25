var smw={};

//------------------------------------------------------------------------------------------------
//this function is for making ajax call
smw.ajaxcall=function(data,responsefunction,param){
	$.ajax({
		async:false,
		headers: { 'X-CSRF-Token' : param._token } ,
		type:data.type,
		url:data.url,
		data:{json:JSON.stringify(data["data"])},
		dataType:'json',
		beforeSend:function(){
			
		},
		success:function(response){
			responsefunction(response,param);		
		},
		statusCode: {
		    400: function() {
		       // Only if your server returns a 403 status code can it come in this block. :-)
		       $.toaster({ priority : 'danger', title : 'Title', message : 'Bad Request'});
		    },
		    404: function() {
		    	$.toaster({ priority : 'danger', title : 'Title', message : 'Bad Request'});
		    },
		    501: function(){
		    	$.toaster({ priority : 'danger', title : 'Title', message : 'Please try again'});	
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
};
//************************************************************************************************
smw.ajaximagecall=function(data,responsefunction){
	$.ajax({
		async:false,
		headers: { 'X-CSRF-Token' : $("input[name=_token]").val() } ,
		type:data.type,
		url:data.url,
		data:data["data"],
		contentType : false,
        processData : false,
		beforeSend:function(){
			
		},
		success:function(response){
			responsefunction(response);		
		},
		statusCode: {
		    400: function() {
		       // Only if your server returns a 403 status code can it come in this block. :-)
		       $.toaster({ priority : 'danger', title : 'Title', message : 'Bad Request'});
		    },
		    404: function() {
		    	$.toaster({ priority : 'danger', title : 'Title', message : 'Bad Request'});
		    },
		    501: function(){
		    	$.toaster({ priority : 'danger', title : 'Title', message : 'Please try again'});	
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
};
//------------------------------------------------------------------------------------------------
//this function is for making ajax image upload

//************************************************************************************************

//------------------------------------------------------------------------------------------------
smw.getUserProfileDetails=function(userid)
{ 
    var param={"userid":userid,"_token":$("input[name=_token]").val()};
    var data={"userid":userid};
    var jsondata={"type":"GET","data":data,"url":"/userDetails"};
    smw.ajaxcall(jsondata,smw.responsGeteUserProfileDetails,param);    
}
smw.responsGeteUserProfileDetails=function(resp,param){
	
	if(resp['data'].length>0)
	{
		$("#confirmEmailCode").val('');
		$("#confirmMobileCode").val('');
		$("#username").val(resp['data'][0]['username']);
		$("#email").val(resp['data'][0]['email']);
		$("#firstName").val(resp['data'][0]['first_name']);
		$("#lastName").val(resp['data'][0]['last_name']);
		$("#bio").text(resp['data'][0]['description']);
		$("#mobile").val(resp['data'][0]['phone_no']);
		$('input[name="gender"][value="' + resp['data'][0]['gender'] + '"]').prop('checked', true);
		$("#birth").val(resp['data'][0]['birthdate']);
		if(resp['data'][0]['birthdate']!="")
		{
			$("#birth").attr("disabled",true);
		}
		else
		{
			$("#birth").attr("disabled",false);
		}

		if(resp['data'][0]['profile_pic']!="default.png")
		{
			$("#userimage").attr('src',lpath+'/images/profile/'+resp['data'][0]['id']+"/"+resp['data'][0]['profile_pic']);	
		}
		else
		{
			$("#userimage").attr("src",lpath+"/images/default.png");
		}

		if(resp['data'][0]['licence_pic']!="no_licence.png")
		{
			$("#licenceimage").attr('src',lpath+'/images/licence/'+resp['data'][0]['id']+"/"+resp['data'][0]['licence_pic']);	
		}
		else
		{
			$("#licenceimage").attr("src",lpath+"/images/no_licence.png");
		}
		//$("#userimage").attr('src',resp['data'][0]['profile_pic']);
		
		if(resp['data'][0]['isverifyphone']==1)
		{
			var html="";
			html+='<i class="zmdi zmdi-check-circle zmdi-hc-fw zmdi-green zmdi-hc-lg"></i>MOBILE VERIFIED';
			$("#mobileConfirmMsg").html(html);
		}
		else
		{
			var html="";
			html+='<i class="zmdi zmdi-close-circle zmdi-hc-fw zmdi-red zmdi-hc-lg"></i>MOBILE NOT VERIFIED';
			$("#mobileConfirmMsg").html(html);
		}

		if(resp['data'][0]['isverifyemail']==1)
		{
			var html="";
			html+='<i class="zmdi zmdi-check-circle zmdi-hc-fw zmdi-green zmdi-hc-lg"></i>EMAIL VERIFIED';
			$("#emailConfirmMsg").html(html);
		}
		else
		{
			var html="";
			html+='<i class="zmdi zmdi-close-circle zmdi-hc-fw zmdi-red zmdi-hc-lg"></i>EMAIL NOT VERIFIED';
			$("#emailConfirmMsg").html(html);
		}
		//$("#userPersonalInfoForm")[0].reset();
	}	
	
}
//************************************************************************************************

//------------------------------------------------------------------------------------------------
//this function is call for user personal information update
smw.formSubmit=function(userid){
	
	var param={"username":$("#username").val(),"gender":$("input[name=gender]:checked").val(),"first_name":$("#firstName").val(),"last_name":$("#lastName").val(),"birth":$("#birth").val(),"bio":$("#bio").val(),"userid":userid,"_token":$("input[name=_token]").val()};
    var data={"username":$("#username").val(),"gender":$("input[name=gender]:checked").val(),"first_name":$("#firstName").val(),"last_name":$("#lastName").val(),"birth":$("#birth").val(),"bio":$("#bio").val(),"userid":userid};
    var jsondata={"type":"POST","data":data,"url":"/userDetails"};
    smw.ajaxcall(jsondata,smw.responsPosteUserProfileDetails,param);    
}
//response function for user personal information
smw.responsPosteUserProfileDetails=function(resp,param){
	$(".validation_error").each(function(){
		$(this).text('');
	});
	if(Object.keys(resp['error']).length>0)
	{
		if(resp['error']['first_name'])
		{
			$("#firstNameError").text(resp['error']['first_name'][0]);
		}
		if(resp['error']['last_name'])
		{
			$("#lastNameError").text(resp['error']['last_name'][0]);
		}
		if(resp['error']['username'])
		{
			$("#userNameError").text(resp['error']['username'][0]);
		}	
		if(resp['error']['gender'])
		{
			$("#genderError").text(resp['error']['gender'][0]);
		}
		if(resp['error']['birth'])
		{
			$("#birthError").text(resp['error']['birth'][0]);
		}
	}
	else
	{
		$.toaster({ priority : 'success', title : 'Title', message : resp['message']});
		if($("#birth").val()=="")
		{
			$("#birth").attr("disabled",false);
		}
		else
		{
			$("#birth").attr("disabled",true);
		}
	}
}
//************************************************************************************************

//------------------------------------------------------------------------------------------------
smw.emailConfirmation=function(userid){
	var param={"email":$("#email").val(),"code":$("#confirmEmailCode").val(),"userid":userid,"_token":$("input[name=_token]").val()};
    var data={"email":$("#email").val(),"code":$("#confirmEmailCode").val(),"userid":userid};
    var jsondata={"type":"POST","data":data,"url":"/emailConfirmation"};
    smw.ajaxcall(jsondata,smw.responseEmailConfirmation,param);  
}
smw.responseEmailConfirmation=function(resp,param){
	var len=Object.keys(resp['error']).length;
	if(len>0 && resp['status']==false)
	{
		for(var i=0;i<len;i++)
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][i]});
		}
		$("#emailConfirmMsg").html('');
	}
	else if(resp['status']==true)
	{
		var html="";
		html+='<i class="zmdi zmdi-check-circle zmdi-hc-fw zmdi-green zmdi-hc-lg"></i>EMAIL VERIFIED';
		$("#emailConfirmMsg").html(html);
		$.toaster({ priority : resp['class'], title : 'Title', message : resp['message']});
		$("#confirmEmailCode").val('');
	}
	else
	{
		$("#confirmEmailCode").val('');
	}
}
//************************************************************************************************

//------------------------------------------------------------------------------------------------
smw.mobileConfirmation=function(userid){
	var param={"mobile":$("#mobile").val(),"code":$("#confirmMobileCode").val(),"userid":userid,"_token":$("input[name=_token]").val()};
    var data={"mobile":$("#mobile").val(),"code":$("#confirmMobileCode").val(),"userid":userid};
    var jsondata={"type":"POST","data":data,"url":"/mobileConfirmation"};
    smw.ajaxcall(jsondata,smw.responseMobileConfirmation,param);
}
smw.responseMobileConfirmation=function(resp,param){
	var len=Object.keys(resp['error']).length;
	if(len>0 && resp['status']==false)
	{
		for(var i=0;i<len;i++)
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][i]});
		}
		$("#mobileConfirmMsg").html('');
	}
	else if(resp['status']==true)
	{
		var html="";
		html+='<i class="zmdi zmdi-check-circle zmdi-hc-fw zmdi-green zmdi-hc-lg"></i>MOBILE VERIFIED';
		$("#mobileConfirmMsg").html(html);
		$.toaster({ priority : resp['class'], title : 'Title', message : resp['message']});
		$("#confirmMobileCode").val('');
	}
	else
	{
		$("#confirmMobileCode").val('');
	}
}
//************************************************************************************************

//------------------------------------------------------------------------------------------------
//function is for update user preferences
smw.saveUserPreferences=function(userid){
	var chat=$("#chat").val();
	var smoke=$("#smoke").val();
	var pets=$("#pets").val();
	var music=$("#music").val();
	var param={"chat":chat,"smoke":smoke,"pets":pets,"music":music,"userid":userid,"_token":$("input[name=_token]").val()};
    var data={"chat":chat,"smoke":smoke,"pets":pets,"music":music,"userid":userid};
    var jsondata={"type":"POST","data":data,"url":"/updateUserPreference"};
    smw.ajaxcall(jsondata,smw.responseUserPreference,param);  
}
//function is for getting response of user preferences save after
smw.responseUserPreference=function(resp,param){
	var len=Object.keys(resp['error']).length;
	if(len>0 && resp['status']==false)
	{
		for(var i=0;i<len;i++)
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][i]});
		}
	}
	else if(resp['status']==true)
	{
		$.toaster({ priority : resp['class'], title : 'Title', message : resp['message']});
	}
	else
	{

	}
}
//************************************************************************************************
smw.responsUploadImage=function(resp){
	var len=Object.keys(resp['error']).length;
	if(len>0 && resp['status']==false)
	{
		for(var i=0;i<len;i++)
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][i]});
		}
	}
	else if(resp['status']==true)
	{
		$.toaster({ priority : resp['class'], title : 'Title', message : resp['message']});
		if(resp['path']=='default.png')
		{
			$("#userimage").attr("src",lpath+"/images/default.png");	
			$("#loginuserpic").attr("src",lpath+"/images/default.png");
		}
		else
		{
			$("#userimage").attr('src',lpath+"/images/profile/"+resp['userid']+"/"+resp['path']);
			$("#loginuserpic").attr("src",lpath+"/images/profile/"+resp['userid']+"/"+resp['path']);
		}
		//$("#userimage").attr("src",resp['path']);
		
		$("#upic").val(resp['path']);
	}
	else
	{
	
	}
}

//------------------------------------------------------------------------------------------------
//************************************************************************************************
smw.responsUploadLicenceImage=function(resp){
	var len=Object.keys(resp['error']).length;
	if(len>0 && resp['status']==false)
	{
		for(var i=0;i<len;i++)
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][i]});
		}
	}
	else if(resp['status']==true)
	{
		$.toaster({ priority : resp['class'], title : 'Title', message : resp['message']});
		if(resp['path']=='no_licence.png')
		{
			$("#licenceimage").attr("src",lpath+"/images/no_licence.png");	
		}
		else
		{
			$("#licenceimage").attr('src',lpath+"/images/licence/"+resp['userid']+"/"+resp['path']);
		}
	}
	else
	{
	
	}
}

//------------------------------------------------------------------------------------------------
smw.sendEmailCode=function(userid)
{
	var email=$("#email").val();
	var param={"email":email,"userid":userid,"_token":$("input[name=_token]").val()};
    var data={"email":email,"userid":userid};
    var jsondata={"type":"POST","data":data,"url":"/sendEmailCode"};
    smw.ajaxcall(jsondata,smw.responseSendEmailCode,param); 
}
smw.responseSendEmailCode=function(resp,param)
{
	var len=Object.keys(resp['error']).length;
	if(len>0 && resp['status']==false)
	{
		for(var i=0;i<len;i++)
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][i]});
		}
		$("#emailConfirmMsg").html('');
	}
	else if(resp['status']==true)
	{
		var html="";
		html+='<i class="zmdi zmdi-check-circle zmdi-hc-fw zmdi-green zmdi-hc-lg"></i>EMAIL VERIFIED';
		$("#emailConfirmMsg").html(html);
		$.toaster({ priority : resp['class'], title : 'Title', message : resp['message']});
	}
	else
	{
	
	}
}
//************************************************************************************************

//------------------------------------------------------------------------------------------------
smw.sendMobileCode=function(userid)
{
	var mobile=$("#mobile").val();
	var param={"mobile":mobile,"userid":userid,"_token":$("input[name=_token]").val()};
    var data={"mobile":mobile,"userid":userid};
    var jsondata={"type":"POST","data":data,"url":"/sendMobileCode"};
    smw.ajaxcall(jsondata,smw.responseSendMobileCode,param); 
}
smw.responseSendMobileCode=function(resp,param)
{
	var len=Object.keys(resp['error']).length;
	if(len>0 && resp['status']==false)
	{
		for(var i=0;i<len;i++)
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][i]});
		}
		$("#mobileConfirmMsg").html('');
	}
	else if(resp['status']==true)
	{
		var html="";
		html+='<i class="zmdi zmdi-check-circle zmdi-hc-fw zmdi-green zmdi-hc-lg"></i>MOBILE VERIFIED';
		$("#mobileConfirmMsg").html(html);
		$.toaster({ priority : resp['class'], title : 'Title', message : resp['message']});
	}
	else
	{
	
	}
}
//************************************************************************************************

//------------------------------------------------------------------------------------------------
//this function is for add car
smw.addCar=function(userid)
{
	var formData=new FormData(),
    files    = $('#carImage').get(0).files;
    $.each(files, function(i, file) {
        formData.append("carImage", file); 
    });
    formData.append("vehicle_type",$("#vehicle_type").val());
    formData.append("carMake",$("#carMake").val());
    formData.append("carModel",$("#carModel").val());
    formData.append("carComfort",$("#carComfort").val());
    formData.append("carColour",$("#carColour").val());
    formData.append("carSeat",$("#carSeat").val());
    formData.append("userid",userid);
	
    var jsondata={"type":"POST","data":formData,"url":"/carAdd"};
    smw.ajaximagecall(jsondata,smw.responseAddCar); 
}
smw.responseAddCar=function(resp)
{
	console.log(resp);
	$(".validation_error").each(function(){
		$(this).text('');
	});
	if(Object.keys(resp['error']).length>0)
	{
		if(resp['error']['vehicle_type'])
		{
			$("#vehicleTypeError").text(resp['error']['vehicle_type'][0]);
		}
		if(resp['error']['carMake'])
		{
			$("#carMakeError").text(resp['error']['carMake'][0]);
		}
		if(resp['error']['carModel'])
		{
			$("#carModelError").text(resp['error']['carModel'][0]);
		}	
		if(resp['error']['carComfort'])
		{
			$("#carComfortError").text(resp['error']['carComfort'][0]);
		}
		if(resp['error']['carColour'])
		{
			$("#carColourError").text(resp['error']['carColour'][0]);
		}
		if(resp['error']['carSeat'])
		{
			$("#carSeatError").text(resp['error']['carSeat'][0]);
		}
		if(resp['error']['carImage'])
		{
			$("#carImageError").text(resp['error']['carImage'][0]);
		}
	}
	else
	{
		$.toaster({ priority : 'success', title : 'Title', message : resp['message']});
		$("#addCar").modal('hide');
		oTable.fnDraw();
	}
}
//************************************************************************************************

//------------------------------------------------------------------------------------------------
smw.editCar=function(carId,userId)
{
	var param={"car":carId,"userid":userId,"_token":$("input[name=_token]").val()};
    var data={"car":carId,"userid":userId};
    var jsondata={"type":"GET","data":data,"url":"/getCarDetails"};
    smw.ajaxcall(jsondata,smw.responseEditCar,param);
}
smw.responseEditCar=function(resp,param)
{
	$(".validation_error").each(function(){
		$(this).text('');
	});
	var len=Object.keys(resp['error']).length;
	if(len>0 && resp['status']==false)
	{
		for(var i=0;i<len;i++)
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][i]});
		}
		$("#mobileConfirmMsg").html('');
	}
	else if(resp['status']==true)
	{
		if(Object.keys(resp['data']).length>0)
		{
			var pathl="";
			if(resp['data'][0]['vehical_pic']=='car_default.png')
			{
				pathl='/images/car_default.png';
			}
			else
			{
				pathl='/images/cars/'+resp['data'][0]['userId']+'/'+resp['data'][0]['vehical_pic'];
			}
			$("#editcarid").val(resp['data'][0]['id']);
			$("#editVehicleType").val(resp['data'][0]['car_type']);
			$("#editCarMake").val(resp['data'][0]['car_make']);
			$("#editCarModel").val(resp['data'][0]['car_model']);
			$("#editCarComfort").val(resp['data'][0]['comfortId']);
			$("#editCarColour").val(resp['data'][0]['colorId']);
			$("#editCarSeat").val(resp['data'][0]['no_of_seats']);
			$("#edit_car_image").attr("src",lpath+pathl);
			
		}
		else
		{}
	}
	else
	{

	}
}
//************************************************************************************************

//------------------------------------------------------------------------------------------------
smw.updateCar=function(carid,userId)
{
	var formData=new FormData(),
    files    = $('#editCarImage').get(0).files;
    $.each(files, function(i, file) {
        formData.append("editCarImage", file); 
    });
    formData.append("editCarId",$("#editcarid").val());
    formData.append("editVehicleType",$("#editVehicleType").val());
    formData.append("editCarMake",$("#editCarMake").val());
    formData.append("editCarModel",$("#editCarModel").val());
    formData.append("editCarComfort",$("#editCarComfort").val());
    formData.append("editCarColour",$("#editCarColour").val());
    formData.append("editCarSeat",$("#editCarSeat").val());
    formData.append("userid",userId);
	
    var jsondata={"type":"POST","data":formData,"url":"/carUpdate"};
    smw.ajaximagecall(jsondata,smw.responseUpdateCar); 
}
smw.responseUpdateCar=function(resp)
{
	$(".validation_error").each(function(){
		$(this).text('');
	});
	if(Object.keys(resp['error']).length>0)
	{
		if(resp['error']['editVehicleType'])
		{
			$("#editVehicleTypeError").text(resp['error']['editVehicleType'][0]);
		}
		if(resp['error']['editCarMake'])
		{
			$("#editCarMakeError").text(resp['error']['editCarMake'][0]);
		}
		if(resp['error']['editCarModel'])
		{
			$("#editCarModelError").text(resp['error']['editCarModel'][0]);
		}	
		if(resp['error']['editCarComfort'])
		{
			$("#editCarComfortError").text(resp['error']['editCarComfort'][0]);
		}
		if(resp['error']['editCarColour'])
		{
			$("#editCarColourError").text(resp['error']['editCarColour'][0]);
		}
		if(resp['error']['editCarSeat'])
		{
			$("#editCarSeatError").text(resp['error']['editCarSeat'][0]);
		}
		if(resp['error']['editCarImage'])
		{
			$("#editCarImageError").text(resp['error']['editCarImage'][0]);
		}
	}
	else
	{
		$.toaster({ priority : 'success', title : 'Title', message : resp['message']});
		$("#editCar").modal('hide');
		oTable.fnDraw();
	}
}
//************************************************************************************************

//------------------------------------------------------------------------------------------------
smw.deleteCar=function(carid,userid)
{
	var param={"car":carid,"userid":userid,"_token":$("input[name=_token]").val()};
    var data={"car":carid,"userid":userid};
    var jsondata={"type":"POST","data":data,"url":"/deleteCar"};
    smw.ajaxcall(jsondata,smw.responseDeleteCar,param);
}
smw.responseDeleteCar=function(resp,param)
{
	$(".validation_error").each(function(){
		$(this).text('');
	});
	var len=Object.keys(resp['error']).length;
	if(len>0 && resp['status']==false)
	{
		for(var i=0;i<len;i++)
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][i]});
		}
	}
	else if(resp['status']==true)
	{
		$.toaster({ priority : resp['class'], title : 'Title', message : resp['message']});
		oTable.fnDraw();
	}
	else
	{

	}
}
//************************************************************************************************

//------------------------------------------------------------------------------------------------
smw.getExchangeRating=function()
{
	var param={"_token":$("input[name=_token]").val()};
    var data={"_token":$("input[name=_token]").val()};
    var jsondata={"type":"GET","data":data,"url":"/exchangeRating"};
    smw.ajaxcall(jsondata,smw.responsGetExchangeRating,param);   
}
smw.responsGetExchangeRating=function(resp,param)
{
	
	var len=Object.keys(resp['error']).length;
	
	if(len>0 && resp['status']==false)
	{
		for(var i=0;i<len;i++)
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][i]});
		}
	}
	else if(resp['status']==true)
	{
		if(resp['data'].length>0)
		{
			var html="";
			html+='<option value="">Select User</option>';
			for(var i=0;i<resp['data'].length;i++)
			{
				html+='<option value='+resp['data'][i]['id']+'>'+resp['data'][i]['name']+'</option>';
			}
			$("#ratingUser").html(html);
		}
		
	}
	else
	{

	}
}
//************************************************************************************************

//------------------------------------------------------------------------------------------------
smw.responseRatingExchange=function(resp,param)
{
	var len=Object.keys(resp['error']).length;
	if(len>0 && resp['status']==false)
	{
		for(var i=0;i<len;i++)
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][i]});
		}
	}
	else if(resp['status']==true)
	{
		$.toaster({ priority : resp['class'], title : 'Title', message : resp['message']});

		if(resp['data'].length>0)
		{
			var html="";
			html+='<option value="">Select User</option>';
			for(var i=0;i<resp['data'].length;i++)
			{
				html+='<option value='+resp['data'][i]['id']+'>'+resp['data'][i]['name']+'</option>';
			}
			$("#ratingUser").html(html);
		}
		else
		{
			var html="";
			html+='<option value="">Select User</option>';
			$("#ratingUser").html(html);
		}
		$("input[name=rating]").prop('checked',false);
	}
	else
	{

	}
}
//**************************************************************************************

$("#withdraw_form").submit(function(e){
	$(".validation_error").each(function(){
		$(this).text('');
	});
	var error_status=0;
	if($("#account_holder").val()=="")
	{
		$("#account_holder_error").text('Enter Account holder name');
		error_status=1;
	}
	if($("#bank_name").val()=="")
	{
		$("#bank_name_error").text('Enter bank name');
		error_status=1;
	}
	if($("#account_no").val()=="")
	{
		$("#account_no_error").text('Enter Account number');
		error_status=1;
	}
	if($("#ifsc_code").val()=="")
	{
		$("#ifsc_code_error").text('Enter IFSC Code');
		error_status=1;
	}
	if($("#withdraw_amount").val()=="")
	{
		$("#withdraw_amount_error").text('Enter Amount');
		error_status=1;
	}
	else
	{
		var amou=$("#withdraw_amount").val();
		if(amou%100!=0)
		{
			$("#withdraw_amount_error").text('Amount must be in multiple of 100');	
			error_status=1;
		}
	}
	if(error_status==1)
	{
		return false;
	}
	else
	{
		e.preventDefault();
		var rate=$('input[name=rating]:checked').val();
        var param={"account_holder":$("#account_holder").val(),"bank_name":$("#bank_name").val(),"account_no":$("#account_no").val(),"ifsc_code":$("#ifsc_code").val(),"withdraw_amount":$("#withdraw_amount").val(),"_token":$("input[name=_token]").val()};
        var data={"account_holder":$("#account_holder").val(),"bank_name":$("#bank_name").val(),"account_no":$("#account_no").val(),"ifsc_code":$("#ifsc_code").val(),"withdraw_amount":$("#withdraw_amount").val()};
        var jsondata={"type":"POST","data":data,"url":"/withdrawAmount"};
        smw.ajaxcall(jsondata,smw.responseWithdrawAmount,param);
	}
});

smw.responseWithdrawAmount=function(resp,param)
{
	$(".validation_error").each(function(){
		$(this).text('');
	});
	if(Object.keys(resp['error']).length>0)
	{
		if(resp['error']['account_holder'])
		{
			$("#account_holder_error").text(resp['error']['account_holder'][0]);
		}
		if(resp['error']['bank_name'])
		{
			$("#bank_name_error").text(resp['error']['bank_name'][0]);
		}
		if(resp['error']['account_no'])
		{
			$("#account_no_error").text(resp['error']['account_no'][0]);
		}	
		if(resp['error']['ifsc_code'])
		{
			$("#ifsc_code_error").text(resp['error']['ifsc_code'][0]);
		}
		if(resp['error']['withdraw_amount'])
		{
			$("#withdraw_amount_error").text(resp['error']['withdraw_amount'][0]);
		}
	}
	else
	{
		$.toaster({ priority : 'success', title : 'Title', message : resp['message']});
		$("#account_holder").val('');
		$("#bank_name").val('');
		$("#account_no").val('');
		$("#ifsc_code").val('');
		$("#withdraw_amount").val('');
	}
}