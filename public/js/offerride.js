var offerride={};
offerride.limitCity=4;
offerride.startCity=1;
offerride.placearray=new Array();
offerride.cityarray=new Array();
offerride.latarray=new Array();
offerride.lngarray=new Array();
offerride.originalplacearray=new Array();
offerride.waypoint=new Array();
offerride.markers=[];
$('#return,#daily').change(function() 
{
	$("#todate").val("");
	$("#returnHour").val("0");
	$("#returnMin").val("0");
	if($("#return").is(":checked") && $("#daily").is(":checked"))
	{
		$("#todate_error").text('');
		$("#returnDateDiv").css("display","none");
		$("#returnHourDiv").css("display","block");	
		$("#returnMinDiv").css("display","block");
	}
	if($("#return").is(":checked") && !($("#daily").is(":checked")))
	{
		$("#todate_error").text('');
		$("#returnDateDiv").css("display","block");
		$("#returnHourDiv").css("display","none");	
		$("#returnMinDiv").css("display","none");
	}
	if(!($("#return").is(":checked")) && $("#daily").is(":checked"))
	{
		$("#todate_error").text('');
		$("#returnDateDiv").css("display","none");
		$("#returnHourDiv").css("display","none");	
		$("#returnMinDiv").css("display","none");
	}
	if(!($("#return").is(":checked")) && !($("#daily").is(":checked")))
	{
		$("#todate_error").text('');
		$("#returnDateDiv").css("display","none");
		$("#returnHourDiv").css("display","none");	
		$("#returnMinDiv").css("display","none");
	}
});
//------------------------------------------------------------------------------------------------
offerride.ajaxcall=function(data,responsefunction,param){
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

offerride.fillInAddressFrom=function()
{
	if(autocompleteFrom.getPlace())
	{
		var place=autocompleteFrom.getPlace();
		offerride.createRide(place);
	}
	else
	{
		var place="";
	}
    offerride.areaselection(0,place,$("#from").val());	
}
offerride.fillInAddressTo=function()
{
	if(autocompleteTo.getPlace())
	{
		var place=autocompleteTo.getPlace();	
		offerride.createRide(place);
	}
	else
	{
		var place="";
	}
    offerride.areaselection(1,place,$("#to").val());	
}
offerride.fillInAddressArea0=function()
{
	if(autocompleteArea0.getPlace())
	{
		var place=autocompleteArea0.getPlace();
		offerride.createRide(place,0);
	}
	else
	{
		var place="";
	}
	offerride.areaselection(2,place,$("#areafrom_0").val());	
}
$(".addarea").click(function(){
	var flag=0;
	var options = {
            //types: ['(cities)'],
            componentRestrictions: {country: "ind"}
        };
	if(offerride.startCity==4)
	{
		$.toaster({ priority : 'danger', title : 'Title', message : 'You can add only 4 cities'});
	}
	else
	{
		for(var i=0;i<offerride.startCity;i++)
		{
			if($("#areafrom_"+i).val()=="")
			{
				flag=1;
			}
		}
		if(flag==0)
		{
			var html="";
			html+='<div class="form-group col-md-7 margin-top-10 margin-bottom-0 aa" id="areadiv_'+offerride.startCity+'">';
			html+='<div class="col-md-10">';
	        html+='<input type="text" id="areafrom_'+offerride.startCity+'" name="areafrom[]" class="form-control" placeholder="Enter City" required/>';
	        html+='</div><div class="col-sm-2 col-md-2">';
	        html+='<button type="button" class="btn btn-danger removeArea">';
	        html+='<i class="zmdi zmdi-minus"></i>';
	        html+='</button></div>';
	        html+='<p id="areafrom_'+(offerride.startCity+2)+'_error" class="validation_error PD20"></p>';
	        html+='<div class="clearfix"></div>';
	        html+='</div>';
	        $("#waypointdiv").append(html);
			offerride.startCity++;
		}
		else
		{
			$.toaster({ priority : 'danger', title : 'Title', message : 'Fill up all cities first'});   					
		}
	}
	offerride.areaFromLoad();
});
$('body').on('click','.removeArea',function(){
	var id=$(this).closest('.aa').attr("id");
	$("#"+id).remove();
	var rowNo=id.split("_");
	
	if(rowNo[1]==1)
	{
		$("#areafrom_3_error").text('');
		if ($('#areafrom_2').length) 
		{
			$("#areadiv_2").attr("id","areadiv_1");
			$("#areafrom_2").attr("id","areafrom_1");
			$("#areafrom_4_error").attr("id","areafrom_3_error");
		}
		if ($('#areafrom_3').length) 
		{
			$("#areadiv_3").attr("id","areadiv_2");
			$("#areafrom_3").attr("id","areafrom_2");
			$("#areafrom_5_error").attr("id","areafrom_4_error");
		}
		offerride.removeElement(3);
		offerride.areaFromLoad();
	}
	if(rowNo[1]==2)
	{
		$("#areafrom_4_error").text('');
		if ($('#areafrom_3').length) 
		{
			$("#areadiv_3").attr("id","areadiv_2");
			$("#areafrom_3").attr("id","areafrom_2");
			$("#areafrom_5_error").attr("id","areafrom_4_error");
		}
		offerride.removeElement(4);
		offerride.areaFromLoad();	
	}
	if(rowNo[1]==3)
	{
		$("#areafrom_5_error").text('');
		offerride.removeElement(5);
		offerride.areaFromLoad();		
	}
	offerride.startCity--;
});
offerride.getCityName=function(lat,lng,index,place_value,place_name)
{
    var geocoder,city;
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
                   	offerride.changeValue(city,index,place_value,place_name);
                }
                else  
                {
                    city="";
                    
                }
            }
            else 
            {
                city="";
            }
        }
    );
    return city;
}
offerride.areaselection=function(index,place_value,place_name)
{
    if (place_value=="")
    {
    	offerride.removeElement(index);
    }
    else
    {
    	var index1=offerride.originalplacearray.indexOf(place_name);
    	
    	if(index1!=-1)
    	{
    		if(index1!=index)
    		{
    			if(index==0)
	    		{
	    			$("#from").val("");
	    		}
	    		if(index==1)
	    		{
	    			$("#to").val("");
	    		}
	    		if(index==2)
	    		{
	    			$("#areafrom_0").val("");
	    		}
	    		if(index==3)
	    		{
	    			$("#areafrom_1").val("");
	    		}
	    		if(index==4)
	    		{
	    			$("#areafrom_2").val("");
	    		}
	    		if(index==5)
	    		{
	    			$("#areafrom_3").val("");
	    		}
	    		offerride.nullElement(index);
	 			$.toaster({ priority : 'danger', title : 'Title', message : 'You can not add same cities..'});   		
    		}
    	}
    	else
    	{
    		offerride.getCityName(place_value.geometry.location.lat(),place_value.geometry.location.lng(),index,place_value,place_name);
    	}
    }
}
offerride.areaFromLoad=function()
{
	var options = {
            //types: ['(cities)'],
        componentRestrictions: {country: "ind"}
    };
	
	if ($('#areafrom_1').length) {
		var inputto1 = document.getElementById('areafrom_1');
		var autocompleteto1 = new google.maps.places.Autocomplete(inputto1,options);
		autocompleteto1.addListener('place_changed', function() {
			if(autocompleteto1.getPlace())
			{
				var placeto1 = autocompleteto1.getPlace();
				offerride.createRide(placeto1);
			}
			else
			{
				var placeto1 = "";
			}
			
			
			offerride.areaselection(3,placeto1,$("#areafrom_1").val());
		});
	}
	if ($('#areafrom_2').length) {
		var inputto2 = document.getElementById('areafrom_2');
		var autocompleteto2 = new google.maps.places.Autocomplete(inputto2,options);
		autocompleteto2.addListener('place_changed', function() {
			if(autocompleteto2.getPlace())
			{
				var placeto2 = autocompleteto2.getPlace();	
				offerride.createRide(placeto2);
			}
			else
			{
				var placeto2="";
			}
			offerride.areaselection(4,placeto2,$("#areafrom_2").val());
		});
	}
	if ($('#areafrom_3').length) {
		var inputto3 = document.getElementById('areafrom_3');
		var autocompleteto3 = new google.maps.places.Autocomplete(inputto3,options);
		autocompleteto3.addListener('place_changed', function() {
			if(autocompleteto3.getPlace())
			{
				var placeto3 = autocompleteto3.getPlace();
				offerride.createRide(placeto3);
			}
			else
			{
				var placeto3="";
			}
			offerride.areaselection(5,placeto3,$("#areafrom_3").val());
		});
	}
}

offerride.removeElement=function(index)
{
	offerride.placearray.splice(index,1);
    offerride.latarray.splice(index,1);
    offerride.lngarray.splice(index,1);
    offerride.cityarray.splice(index,1);
    offerride.originalplacearray.splice(index,1);
}


offerride.responsOfferRide=function(resp,param)
{
	console.log(resp);
}
offerride.nullElement=function(index)
{
	offerride.placearray[index]=null;
    offerride.latarray[index]=null;
    offerride.lngarray[index]=null;
    offerride.cityarray[index]=null;
    offerride.originalplacearray[index]=null;
}

offerride.changeValue=function(city,index,place_value,place_name)
{
	if(city!="")
	{
		offerride.placearray[index]=place_value.name;
    	offerride.latarray[index]=place_value.geometry.location.lat();
    	offerride.lngarray[index]=place_value.geometry.location.lng();
    	offerride.cityarray[index]=city;
    	offerride.originalplacearray[index]=place_name;
	}
	else
	{
		offerride.removeElement(index);
		$.toaster({ priority : 'danger', title : 'Title', message : 'Enter Correct City'});   		
	}
}

//this function call when submit form
//----------------------------------------------------------------------------------
$("#offerForm").submit(function(e){
	offerride.status=true;
	$(".validation_error").each(function(){
		$(this).text('');
	});
	e.preventDefault();	

	//this validation is for check if areafrom_0 value enter then after submit if i change the value null of areafrom_0 then it will check if value in placearray exists or not

	if($("#areafrom_0").val()=="")
	{
		if($("#areafrom_1").length)
		{

		}
		else
		{
			offerride.removeElement(2);
		}
	}

	if(offerride.placearray.length>0)
	{
		for(var j=0;j<offerride.placearray.length;j++)
		{
			if(j==0)
			{
				if($("#from").val()!=offerride.originalplacearray[j])
				{
					offerride.nullElement(0);
					$("#areafrom_0_error").text('Enter Valid Location');
				}		
			}
			if(j==1)
			{
				if($("#to").val()!=offerride.originalplacearray[j])
				{
					offerride.nullElement(1);
					$("#areafrom_1_error").text('Enter Valid Location');	
				}
			}
			if(j==2)
			{
				if($("#areafrom_0").val()!=offerride.originalplacearray[j])
				{
					offerride.nullElement(2);
					$("#areafrom_2_error").text('Enter Valid Location');		
				}
			}
			if(j==3)
			{
				if($("#areafrom_1").val()!=offerride.originalplacearray[j])
				{
					offerride.nullElement(3);
					$("#areafrom_3_error").text('Enter Valid Location');		
				}
			}
			if(j==4)
			{
				if($("#areafrom_2").val()!=offerride.originalplacearray[j])
				{
					offerride.nullElement(4);
					$("#areafrom_4_error").text('Enter Valid Location');		
				}
			}
			if(j==5)
			{
				if($("#areafrom_3").val()!=offerride.originalplacearray[j])
				{
					offerride.nullElement(5);
					$("#areafrom_5_error").text('Enter Valid Location');		
				}
			}

			if(offerride.placearray[j]==null || typeof offerride.placearray[j]=='undefined')
			{
				$("#areafrom_"+j+"_error").text('Enter Valid Location');
				offerride.status=false;
			}
		}
	}
	else
	{
		for(j=0;j<6;j++)
		{
			if($("#areafrom_"+j+"_error").length)
			{
				$("#areafrom_"+j+"_error").text('Enter Valid Location');	
				offerride.status=false;
			}
		}
		
	}
	
	if($("#areafrom_1").length)
	{
		if(offerride.placearray[3]==null || typeof offerride.placearray[3]=='undefined')
		{
			$("#areafrom_3_error").text('Enter Valid Location');
			offerride.status=false;
		}
	}
	if($("#areafrom_2").length)
	{
		if(offerride.placearray[4]==null || typeof offerride.placearray[4]=='undefined')
		{
			$("#areafrom_4_error").text('Enter Valid Location');
			offerride.status=false;
		}
	}
	if($("#areafrom_3").length)
	{
		if(offerride.placearray[5]==null || typeof offerride.placearray[5]=='undefined')
		{
			$("#areafrom_5_error").text('Enter Valid Location');
			offerride.status=false;
		}
	}

	if($("#fromdate").val()=="")
	{
		$("#fromdate_error").text('Enter Departure Date time');
		offerride.status=false;
	}
	if($("#returnDateDiv:visible").length)
	{
		if($("#todate").val()=="")
		{
			$("#todate_error").text('Enter Return Date time');
			offerride.status=false;
		}
	}
	if($("#car").val()=="")
	{
		$("#car_error").text('Select Car');
		offerride.status=false;
	}
	if($("#luggage").val()=="")
	{
		$("#luggage_error").text('Select luggage');
		offerride.status=false;
	}
	if($("#leave").val()=="")
	{
		$("#leave_error").text('Select leave');
		offerride.status=false;
	}
	if($("#detour").val()=="")
	{
		$("#detour_error").text('Select detour');
		offerride.status=false;
	}
	
	if(offerride.status)
	{
		/*var $inputs = $('#offerForm :input');
		var values = {};
	    $inputs.each(function() {
	        values[this.name] = $(this).val();
	    });*/
		var ladies,daily,ret,way1,way2,way3,way4,licence;

		way1=$("#areafrom_0").val();

		if($("#areafrom_1").length)
		{
			way2=$("#areafrom_1").val();
		}
		else
		{
			way2=0;
		}

		if($("#areafrom_2").length)
		{
			way3=$("#areafrom_2").val();
		}
		else
		{
			way3=0;
		}

		if($("#areafrom_3").length)
		{
			way4=$("#areafrom_3").val();
		}
		else
		{
			way4=0;
		}

		if($("#ladies").is(":checked"))
		{
			ladies=1;
		}
		else
		{
			ladies=0;
		}

	    if($("#daily").is(":checked"))
		{
			daily=1;
		}
		else
		{
			daily=0;
		}

		if($("#return").is(":checked"))
		{
			ret=1;
		}
		else
		{
			ret=0;
		}

		if($("#licence").is(":checked"))
		{
			licence=1;
		}
		else
		{
			licence="";
		}
	    
	    payment_type="";
	    if(daily==1)
	    {
	    	getAmount();
	    	var am=$("#walletamount").text();
	    	$("#wallet_amount_balance").text(am);
	    	$("#dailyride").modal('show');

	    }
	    else
	    {
	    	var param={"place":offerride.placearray,"lat":offerride.latarray,"lng":offerride.lngarray,"city":offerride.cityarray,"original":offerride.originalplacearray,"fromdate":$("#fromdate").val(),"todate":$("#todate").val(),"returnHour":$("#returnHour").val(),"returnMin":$("#returnMin").val(),"car":$("#car").val(),"luggage":$("#luggage").val(),"leave":$("#leave").val(),"detour":$("#detour").val(),"comments":$("#comments").val(),"ladies":ladies,"return":ret,"daily":daily,"way1":way1,"way2":way2,"way3":way3,"way4":way4,"from":$("#from").val(),"to":$("#to").val(),"licence":licence,"seat":$("#seat").val(),"costseat":$("#costseat").val(),"payment_type":payment_type,"_token":$("input[name=_token]").val(),"licence":licence};

		    var data={"place":offerride.placearray,"lat":offerride.latarray,"lng":offerride.lngarray,"city":offerride.cityarray,"original":offerride.originalplacearray,"fromdate":$("#fromdate").val(),"todate":$("#todate").val(),"returnHour":$("#returnHour").val(),"returnMin":$("#returnMin").val(),"car":$("#car").val(),"luggage":$("#luggage").val(),"leave":$("#leave").val(),"detour":$("#detour").val(),"comments":$("#comments").val(),"ladies":ladies,"return":ret,"daily":daily,"way1":way1,"way2":way2,"way3":way3,"way4":way4,"from":$("#from").val(),"to":$("#to").val(),"licence":licence,"seat":$("#seat").val(),"costseat":$("#costseat").val(),"payment_type":payment_type};

		    var jsondata={"type":"POST","data":data,"url":"/offerride"};
		    offerride.ajaxcall(jsondata,offerride.responsOfferRide,param); 
	    }
	}	
});
//**********************************************************************************
$('#daily').change(function() {
	if($(this).is(":checked")) 
	{
		$("#costseat").val("0");
		$("#costseat").attr("readonly","readonly");
	}
	else
	{
		$("#costseat").val("");
		$("#costseat").removeAttr("readonly");
	}
});
//----------------------------------------------------------------------------------
$("#paybtn").click(function(){
	$("#dailyride").modal('hide');
	var payment_type="";                    
	if($("#pay_wallet").is(":checked"))
	{
		payment_type="wallet";
	}
	else
	{
		payment_type="ccavenu";
	}

	offerride.status=true;
	$(".validation_error").each(function(){
		$(this).text('');
	});	

	//this validation is for check if areafrom_0 value enter then after submit if i change the value null of areafrom_0 then it will check if value in placearray exists or not

	if($("#areafrom_0").val()=="")
	{
		if($("#areafrom_1").length)
		{

		}
		else
		{
			offerride.removeElement(2);
		}
	}

	if(offerride.placearray.length>0)
	{
		for(var j=0;j<offerride.placearray.length;j++)
		{
			if(j==0)
			{
				if($("#from").val()!=offerride.originalplacearray[j])
				{
					offerride.nullElement(0);
					$("#areafrom_0_error").text('Enter Valid Location');
				}		
			}
			if(j==1)
			{
				if($("#to").val()!=offerride.originalplacearray[j])
				{
					offerride.nullElement(1);
					$("#areafrom_1_error").text('Enter Valid Location');	
				}
			}
			if(j==2)
			{
				if($("#areafrom_0").val()!=offerride.originalplacearray[j])
				{
					offerride.nullElement(2);
					$("#areafrom_2_error").text('Enter Valid Location');		
				}
			}
			if(j==3)
			{
				if($("#areafrom_1").val()!=offerride.originalplacearray[j])
				{
					offerride.nullElement(3);
					$("#areafrom_3_error").text('Enter Valid Location');		
				}
			}
			if(j==4)
			{
				if($("#areafrom_2").val()!=offerride.originalplacearray[j])
				{
					offerride.nullElement(4);
					$("#areafrom_4_error").text('Enter Valid Location');		
				}
			}
			if(j==5)
			{
				if($("#areafrom_3").val()!=offerride.originalplacearray[j])
				{
					offerride.nullElement(5);
					$("#areafrom_5_error").text('Enter Valid Location');		
				}
			}

			if(offerride.placearray[j]==null || typeof offerride.placearray[j]=='undefined')
			{
				$("#areafrom_"+j+"_error").text('Enter Valid Location');
				offerride.status=false;
			}
		}
	}
	else
	{
		for(j=0;j<6;j++)
		{
			if($("#areafrom_"+j+"_error").length)
			{
				$("#areafrom_"+j+"_error").text('Enter Valid Location');	
				offerride.status=false;
			}
		}
		
	}
	
	if($("#areafrom_1").length)
	{
		if(offerride.placearray[3]==null || typeof offerride.placearray[3]=='undefined')
		{
			$("#areafrom_3_error").text('Enter Valid Location');
			offerride.status=false;
		}
	}
	if($("#areafrom_2").length)
	{
		if(offerride.placearray[4]==null || typeof offerride.placearray[4]=='undefined')
		{
			$("#areafrom_4_error").text('Enter Valid Location');
			offerride.status=false;
		}
	}
	if($("#areafrom_3").length)
	{
		if(offerride.placearray[5]==null || typeof offerride.placearray[5]=='undefined')
		{
			$("#areafrom_5_error").text('Enter Valid Location');
			offerride.status=false;
		}
	}

	if($("#fromdate").val()=="")
	{
		$("#fromdate_error").text('Enter Departure Date time');
		offerride.status=false;
	}
	if($("#returnDateDiv:visible").length)
	{
		if($("#todate").val()=="")
		{
			$("#todate_error").text('Enter Return Date time');
			offerride.status=false;
		}
	}
	if($("#car").val()=="")
	{
		$("#car_error").text('Select Car');
		offerride.status=false;
	}
	if($("#luggage").val()=="")
	{
		$("#luggage_error").text('Select luggage');
		offerride.status=false;
	}
	if($("#leave").val()=="")
	{
		$("#leave_error").text('Select leave');
		offerride.status=false;
	}
	if($("#detour").val()=="")
	{
		$("#detour_error").text('Select detour');
		offerride.status=false;
	}
	
	if(offerride.status)
	{
		/*var $inputs = $('#offerForm :input');
		var values = {};
	    $inputs.each(function() {
	        values[this.name] = $(this).val();
	    });*/
		var ladies,daily,ret,way1,way2,way3,way4,licence;

		way1=$("#areafrom_0").val();

		if($("#areafrom_1").length)
		{
			way2=$("#areafrom_1").val();
		}
		else
		{
			way2=0;
		}

		if($("#areafrom_2").length)
		{
			way3=$("#areafrom_2").val();
		}
		else
		{
			way3=0;
		}

		if($("#areafrom_3").length)
		{
			way4=$("#areafrom_3").val();
		}
		else
		{
			way4=0;
		}

		if($("#ladies").is(":checked"))
		{
			ladies=1;
		}
		else
		{
			ladies=0;
		}

	    if($("#daily").is(":checked"))
		{
			daily=1;
		}
		else
		{
			daily=0;
		}

		if($("#return").is(":checked"))
		{
			ret=1;
		}
		else
		{
			ret=0;
		}

		if($("#licence").is(":checked"))
		{
			licence=1;
		}
		else
		{
			licence="";
		}
	    
    	var param={"place":offerride.placearray,"lat":offerride.latarray,"lng":offerride.lngarray,"city":offerride.cityarray,"original":offerride.originalplacearray,"fromdate":$("#fromdate").val(),"todate":$("#todate").val(),"returnHour":$("#returnHour").val(),"returnMin":$("#returnMin").val(),"car":$("#car").val(),"luggage":$("#luggage").val(),"leave":$("#leave").val(),"detour":$("#detour").val(),"comments":$("#comments").val(),"ladies":ladies,"return":ret,"daily":daily,"way1":way1,"way2":way2,"way3":way3,"way4":way4,"from":$("#from").val(),"to":$("#to").val(),"licence":licence,"seat":$("#seat").val(),"costseat":$("#costseat").val(),"payment_type":payment_type,"_token":$("input[name=_token]").val(),"licence":licence};

	    var data={"place":offerride.placearray,"lat":offerride.latarray,"lng":offerride.lngarray,"city":offerride.cityarray,"original":offerride.originalplacearray,"fromdate":$("#fromdate").val(),"todate":$("#todate").val(),"returnHour":$("#returnHour").val(),"returnMin":$("#returnMin").val(),"car":$("#car").val(),"luggage":$("#luggage").val(),"leave":$("#leave").val(),"detour":$("#detour").val(),"comments":$("#comments").val(),"ladies":ladies,"return":ret,"daily":daily,"way1":way1,"way2":way2,"way3":way3,"way4":way4,"from":$("#from").val(),"to":$("#to").val(),"licence":licence,"seat":$("#seat").val(),"costseat":$("#costseat").val(),"payment_type":payment_type};

	    var jsondata={"type":"POST","data":data,"url":"/offerride"};
	    offerride.ajaxcall(jsondata,offerride.responsOfferRide,param); 
	}
});
//**********************************************************************************

//----------------------------------------------------------------------------------
offerride.responsOfferRide=function(resp,param)
{
	
	$(".validation_error").each(function(){
		$(this).text('');
	});
	if(Object.keys(resp['error']).length>0)
	{
		if(resp['error']['error'])
		{
			$.toaster({ priority :resp['class'], title : 'Title', message : resp['error']['error']});
		}
		if(resp['error']['from'])
		{
			$("#areafrom_0_error").text(resp['error']['from'][0]);
		}
		if(resp['error']['to'])
		{
			$("#areafrom_1_error").text(resp['error']['to'][0]);
		}
		if(resp['error']['areafrom_0'])
		{
			$("#areafrom_2_error").text(resp['error']['areafrom_0'][0]);
		}	
		if(resp['error']['areafrom_1'])
		{
			$("#areafrom_3_error").text(resp['error']['areafrom_1'][0]);
		}
		if(resp['error']['areafrom_2'])
		{
			$("#areafrom_4_error").text(resp['error']['areafrom_2'][0]);
		}
		if(resp['error']['areafrom_3'])
		{
			$("#areafrom_5_error").text(resp['error']['areafrom_3'][0]);
		}
		if(resp['error']['car'])
		{
			$("#car_error").text(resp['error']['car'][0]);
		}
		if(resp['error']['luggage'])
		{
			$("#luggage_error").text(resp['error']['luggage'][0]);
		}
		if(resp['error']['leave'])
		{
			$("#leave_error").text(resp['error']['leave'][0]);
		}
		if(resp['error']['detour'])
		{
			$("#detour_error").text(resp['error']['detour'][0]);
		}
		if(resp['error']['licence'])
		{
			$("#licence_error").text(resp['error']['licence'][0]);
		}
		if(resp['error']['fromdate'])
		{
			$("#fromdate_error").text(resp['error']['fromdate'][0]);
		}
		if(resp['error']['todate'])
		{
			$("#todate_error").text(resp['error']['todate'][0]);
		}
		if(resp['error']['returnHour'])
		{
			$("#returnHour_error").text(resp['error']['returnHour'][0]);
		}
		if(resp['error']['seat'])
		{
			$("#seat_error").text(resp['error']['seat'][0]);
		}
		if(resp['error']['costseat'])
		{
			$("#costseat_error").text(resp['error']['costseat'][0]);
		}
		if(resp['error']['wallet'])
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['error']['wallet'][0]});
		}
	}
	else
	{
		if(typeof resp['data']!="undefined" && typeof resp['data']!="undefined")
		{	
			$("#access_code").val(resp['data']['access']);
			$("#encRequest").val(resp['data']['dd']);
			document.redirect.submit();
		}
		else
		{
			$.toaster({ priority : resp['class'], title : 'Title', message : resp['message']});
			$("#offerForm")[0].reset();
			$("#returnDateDiv").css("display","block");
			offerride.startCity=1;
			offerride.placearray=[];
			offerride.latarray=[];
			offerride.lngarray=[];
			offerride.cityarray=[];
			offerride.originalplacearray=[];

			$(".aa").each(function(){
				$(this).remove();
			});

			getAmount();
		}
	}
}

offerride.createRide=function(place)
{

    /*origin={lat:21.9619, lng: 70.7923};
    destination={lat:23.0225,lng:72.5714};*/
    var placeLoc = place.geometry.location;
    
    var marker = new google.maps.Marker({
        map: map,
        position: placeLoc
    });

    google.maps.event.addListener(marker, 'click', function() {
        infowindow.setContent(place.name);
        infowindow.open(map, this);
    });
    /*directionsService.route({
        origin: "Gondal, Gujarat, India",
      destination: "Ahmedabad, Gujarat, India",
      waypoints: [
        {
          location:"Rajkot, Gujarat, India",
          stopover:false
        },{
          location:"Junagadh, Gujarat, India",
          stopover:true
        }],
        // Note that Javascript allows us to access the constant
        // using square brackets and a string value as its
        // "property."
        travelMode: google.maps.TravelMode['DRIVING']
    }, function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
           offerride.distanceRoute(service);
        } 
        else 
        {
            window.alert('Directions request failed due to ' + status);
        }
    }); */
}

offerride.distanceRoute=function(service)
{
    //find distance
        
    //var origin1 = new google.maps.LatLng(from_lat,to_lat);
    var origin2 = "Gondal, Gujarat, India";
    var destinationA = "Ahmedabad, Gujarat, India";
    //var destinationB = new google.maps.LatLng(dfrom_lat,dto_lat);
    
    service.getDistanceMatrix({
        origins: [origin2],
        destinations: [destinationA],
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.METRIC,
        avoidHighways: false,
        avoidTolls: false
    }, function (response, status) {
        if (status == google.maps.DistanceMatrixStatus.OK && response.rows[0].elements[0].status != "ZERO_RESULTS") {
            var distance = response.rows[0].elements[0].distance.text;
            var duration = response.rows[0].elements[0].duration.text;
            console.log(distance);
            console.log(duration);
 
        } else {
            alert("Unable to find the distance via road.");
        }
    });

    //distance ends

}
