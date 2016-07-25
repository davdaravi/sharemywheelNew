$("#chat_div").empty();
$(".profile-main").empty();
var udd=0;
var msgid=0;
var settimeoutid=0;
//--------------------------------------------------------------------------------------
$("body").on('click','.user-chat-messages',function(){
    //clear settimeout
    clearTimeout(settimeoutid);

    var id=$(this).attr("data-id");
    udd=id;
    var pic=$(this).attr("data-pic");
    $(".user-chat-messages").each(function(){
        $(this).css("background","#fff");
        $(this).attr("data-flag",false);
    });
    $(this).css("background","#eee");
    $(this).attr("data-flag",true);
    for(i=0;i<chatUser.length;i++)
    {
        if(chatUser[i]['id']==id)
        {
            chatUser[i]['flag']=true;
        }
        else
        {
            chatUser[i]['flag']=false;
        }
    }
    smw.setOverlay();
    smw.getAllMessage(id,pic);    
});
//**************************************************************************************
//when user click on messages tab
$(".messages").click(function(){

});

if(udd>0)
{
    pullData();
}

//get all message 
smw.getAllMessage=function(userid,pic)
{
	var param={"user":userid,"pic":pic,"_token":$("input[name=_token]").val()};
	var data={"user":userid};
	var jsondata={"type":"GET","data":data,"url":"/getMessage"};
	smw.ajaxcall(jsondata,smw.responsGetMessage,param);
}

smw.responsGetMessage=function(resp,param)
{
    console.log(resp);
    var pic=param.pic;

    if(Object.keys(resp['error']).length>0 && resp['status']==false)
    {
        smw.removeOverlay();
        $.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][0]});
    }
    else
    {
        //chat fill start
        $("#chat_div").empty();
        var html="";
        var last_date="";
        for(var i=0;i<resp['data'][1].length;i++)
        {
            var datesplit=resp['data'][1][i]['createdDate'].split(" ");
            if(i==0)
            {
                last_date=datesplit[0];
                html+='<div class="row">';
                html+='<div class="col-md-12 col-xs-12 date-time-inbox text-center">';
                html+='<span>'+resp['data'][1][i]['createdDate']+'</span>';
                html+='</div>';
                html+='</div>';
            }
            else
            {
                if(last_date!=datesplit[0])
                {
                    last_date=datesplit[0];
                    html+='<div class="row">';
                    html+='<div class="col-md-12 col-xs-12 date-time-inbox text-center">';
                    html+='<span>'+resp['data'][1][i]['createdDate']+'</span>';
                    html+='</div>';    
                    html+='</div>';   
                }
            }

            //msg bind check if he is receiver or sender
            if(resp['data'][0]==resp['data'][1][i]['fromUserId'])
            {
                var upic=$("#upic").val();

                if(upic=='default.png')
                {
                    upic='/images/default.png';
                }
                else
                {
                    upic='/images/profile/'+resp['data'][1][i]['fromUserId']+'/'+upic;
                }
                html+='<div class="row">';
                html+='<div class="col-md-12 col-xs-12 col-sm-12 sender">';
                html+='<div class="col-md-10 col-sm-10 col-xs-10">';
                html+='<div class="message-bubble">';
                html+='<span>'+resp['data'][1][i]['message']+'</span>';
                html+='</div>';
                html+='</div>';
                html+='<div class="col-md-2 col-sm-2 col-xs-2">';
                html+='<img src="'+lpath+upic+'" />';
                html+='</div>';
                html+='</div>';
                html+='</div>';
            }
            else
            {

                html+='<div class="row">';
                html+='<div class="col-md-12 receiver col-sm-12 col-xs-12">';
                html+='<div class="col-md-2 col-sm-2 col-xs-2">';
                html+='<img src="'+pic+'" />';
                html+='</div>';
                html+='<div class="col-md-10 col-sm-10 col-xs-10">';
                html+='<div class="message-bubble">';
                html+='<span>'+resp['data'][1][i]['message']+'</span>';
                html+='</div>';
                html+='</div>';
                html+='</div>';
                html+='</div>';
                msgid=resp['data'][1][i]['chatMessageId'];
            }
        }
        $("#chat_div").html(html);
        var a=$("#chat_div");
        var h=a[0].scrollHeight;
        a.scrollTop(h);
        smw.removeOverlay();
        //chat fill ends

        //fill userprofile
        $(".profile-main").empty();
        var ht="";
        ht+='<div class="left-message-box" id="chatUserProfile">';
        ht+='<div class="message-profile">';
        ht+='<img class="img-responsive" src="'+pic+'" />';
        ht+='</div>';
        ht+='<div class="text-center">';
        ht+='<h3>'+resp['data'][2][0]['username']+'</h3>';
        ht+='<br>';
        if(resp['data'][2][0]['isverifyphone']==0)
        {
            ht+='<i class="zmdi zmdi-close-circle zmdi-hc-fw"></i>';
            ht+='<span>Mobile Number not Verified</span>';
        }
        else
        {
            ht+='<i class="zmdi zmdi-check-circle zmdi-hc-fw"></i>';
            ht+='<span>Mobile Number Verified</span>';
        }
        ht+='<br><br>';
        if(resp['data'][2][0]['isverifyemail']==0)
        {
            ht+='<i class="zmdi zmdi-close-circle zmdi-hc-fw"></i>';
            ht+='<span>Email not Verified</span>';
        }
        else
        {
            ht+='<i class="zmdi zmdi-check-circle zmdi-hc-fw"></i>';
            ht+='<span>Email Verified</span>';    
        }
        
        ht+='<br><br>';
        ht+='</div>';
        ht+='</div>';
        $(".profile-main").html(ht);
        //userprofile ends

        if(resp['data'][3]==false)//when departure date is gone at that time not available for chat
        {
            $(".send-message").hide();
        }
        else
        {
            $(".send-message").show();
             pullData();
        }
       
    }

}                    
//--------------------------------------------------------------------------------------
//when user click on send button
$("#sendMsgBtn").mousedown(function(){
    
    if(udd>0)
    {
        var msg=$("#userMsg").val();
        if(msg!="")
        {
            var param={"user":udd,"msg":msg,"_token":$("input[name=_token]").val()};
            var data={"user":udd,"msg":msg};
            var jsondata={"type":"POST","data":data,"url":"/sendMessage"};
            smw.ajaxcall(jsondata,smw.responseSendMessage,param);       
        }
        else
        {
            $.toaster({ priority : 'danger', title : 'Title', message : 'Please enter message'});
        }
        
    }
    else
    {
        $.toaster({ priority : 'danger', title : 'Title', message : 'Please select user first'});
    }
});
smw.responseSendMessage=function(resp,param)
{
    if(Object.keys(resp['error']).length>0 && resp['status']==false)
    {
        smw.removeOverlay();
        $.toaster({ priority : resp['class'], title : 'Title', message : resp['error'][0]});
    }
    else if(resp['status']==true)
    {
        $("#userMsg").val('');
        var upic=$("#upic").val();
        html="";
        html+='<div class="row">';
        html+='<div class="col-md-12 col-xs-12 col-sm-12 sender">';
        html+='<div class="col-md-10 col-sm-10 col-xs-10">';
        html+='<div class="message-bubble">';
        html+='<span>'+param.msg+'</span>';
        html+='</div>';
        html+='</div>';
        html+='<div class="col-md-2 col-sm-2 col-xs-2">';
        html+='<img src="'+upic+'" />';
        html+='</div>';
        html+='</div>';
        html+='</div>';
        $("#chat_div").append(html);
        var a=$("#chat_div");
        var h=a[0].scrollHeight;
        a.scrollTop(h);
    }
    else
    {
        $("#userMsg").val('');
    }
}
//**************************************************************************************
//when user is typing
/*$("#userMsg").keyup(function(e){
    smw.isTyping(udd);
});*/

/*$("#userMsg").on('blur',function(e){
    smw.isNotTyping(udd);
});*/

//--------------------------------------------------------------------------------------
/*smw.isTyping=function(uid)
{
    var param={"user":uid,"_token":$("input[name=_token]").val()};
    var data={"user":uid};
    var jsondata={"type":"POST","data":data,"url":"/isTyping"};
    smw.ajaxcall(jsondata,smw.responseIsTyping,param); 
}
smw.responseIsTyping=function(resp,param)
{

}*/
//**************************************************************************************

/*smw.isNotTyping=function(uid){
    var param={"user":uid,"_token":$("input[name=_token]").val()};
    var data={"user":uid};
    var jsondata={"type":"POST","data":data,"url":"/isNotTyping"};
    smw.ajaxcall(jsondata,smw.responseIsNotTyping,param);    
}
smw.responseIsNotTyping=function(resp,param)
{
    
}*/
//**************************************************************************************

function pullData()
{
    retrieveChatMessages();
    //retrieveTypingStatus();
    settimeoutid=setTimeout(pullData,5000);
}

function retrieveChatMessages()
{
    var param={"user":udd,"msgid":msgid,"_token":$("input[name=_token]").val()};
    var data={"user":udd,"msgid":msgid};
    var jsondata={"type":"GET","data":data,"url":"/retriveMessage"};
    smw.ajaxcall(jsondata,smw.responseRetriveMessage,param); 
}
smw.responseRetriveMessage=function(resp,param)
{
    if(Object.keys(resp['error']).length>0 && resp['status']==false)
    {
        $.toaster({ priority : resp['class'], title : 'Title', message : resp['message']});
    }
    else if(resp['status']==true)
    {
        if(resp['data'][1].length>1)
        {
            var pic= resp['data'][0][0]['profile_pic'];
            html="";
            var datesplit=resp['data'][1][0]['createdDate'].split(" ");
            var last_date=datesplit[0];
            for(var i=1;i<resp['data'][1].length;i++)
            {
                var datesplit1=resp['data'][1][0]['createdDate'].split(" ");
                if(last_date!=datesplit1[0])
                {
                    last_date=datesplit1[0];
                    html+='<div class="row">';
                    html+='<div class="col-md-12 col-xs-12 date-time-inbox text-center">';
                    html+='<span>'+resp['data'][1][i]['createdDate']+'</span>';
                    html+='</div>';    
                    html+='</div>';   
                }
                //msg bind check if he is receiver or sender
                
               
                html+='<div class="row">';
                html+='<div class="col-md-12 receiver col-sm-12 col-xs-12">';
                html+='<div class="col-md-2 col-sm-2 col-xs-2">';
                html+='<img src="'+pic+'" />';
                html+='</div>';
                html+='<div class="col-md-10 col-sm-10 col-xs-10">';
                html+='<div class="message-bubble">';
                html+='<span>'+resp['data'][1][i]['message']+'</span>';
                html+='</div>';
                html+='</div>';
                html+='</div>';
                html+='</div>';
                msgid=resp['data'][1][i]['chatMessageId'];
                
            }
            $("#chat_div").append(html);
            var a=$("#chat_div");
            var h=a[0].scrollHeight;
            a.scrollTop(h);
        }
    }
    else
    {

    }
}
smw.setOverlay=function()
{
    $('.main').append('<div class="overlay"><div class="overlayImage"></div></div>');
}
smw.removeOverlay=function()
{
    $('.overlay').remove();
}