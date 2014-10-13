/*****************************************************************************\
|        \  ,  /                                                            **|
|     ' ,___/_\___, '           *****         ******                        **|            
|        \ /o.o\ /              *    *          **                          **|
|    -=   > \_/ <   =-          *     *         **                          **|
|        /_\___/_\              *    *          **                          **|
|     . `   \ /   ` .           *****         ******                        **|
|         /  `  \                                                           **|
|_____________________________________________________________________________|
|   Created By : Antonopoulos Spyridon                                      **|
|   contact me : sdi0600048@di.uoa.gr                                       **|
|       Project: YouCompare Site - Software Engineering Course Spring 2011  **|
|   Description: GUI part Javascript for page browse Category               **|
|        Lines : 573                                                        **|   
\*****************************************************************************/

var filters_toggled;

$(document).ready(function(){
        // hide the extra filters to show them on expand click
        $(".filterRowHid").hide();
        $("#jumpToPageBox").hide(); 
	 $("#jumpToPageBox2").hide(); 
        filters_toggled = 0;
});


/* expanding attributes of entities*/
$(".expandAttrTable").live("click",(function(){
    
    var expand = $(this);
    if(expand.find(".texpand").is(":visible")){
        $(this).parent().children(".entityAttributes").find(".nonVisibleAttr").each(function(){
            $(this).fadeIn(400);

        });

        $(this).parent().children(".entityAttributes").find(".nonVisibleAttr").promise().done(function() {
            expand.find(".texpand").fadeOut(100,function(){expand.find(".cexpand").toggle();});
            

        });
    }
    else {
       $(this).parent().children(".entityAttributes").find(".nonVisibleAttr").each(function(){
            $(this).fadeOut(500);

        });

        $(this).parent().children(".entityAttributes").find(".nonVisibleAttr").promise().done(function() {
            expand.find(".cexpand").fadeOut(100,function(){expand.find(".texpand").toggle();});
            

        });
    }
}));

$("#jumpToPage").live("click",(function(){
      $("#jumpToPageBox").toggle();
}));     
$("#jumpToPage2").live("click",(function(){
      $("#jumpToPageBox2").toggle();
}));   

$(".expandFilterValsImage").live("click",function(){
    var $row = $(this).attr("rowexp");
    var $state = $(this).attr("stateexp");

    if($state == "close"){
        $(".filterValuesHid"+$row).fadeIn(1000);
        $(".expandAttribute:eq("+($row-1)+") img").attr("src","./images/collapse.gif");
        $(".expandAttribute:eq("+($row-1)+") img").attr("stateexp","expand");
    }
    else {
        $(".filterValuesHid"+$row).fadeOut(1000);
        $(".expandAttribute:eq("+($row-1)+") img").attr("src","./images/expand.gif");
        $(".expandAttribute:eq("+($row-1)+") img").attr("stateexp","close");
    }

});

$("#expandFilters").live("click",function(){

    
    var count = 0;
    var elen;
    
    if(filters_toggled == 0){
        $(".filterRowHid").animate({height:"100%"},0,function(){
            $(".filterRowHid:eq("+count/3+")").toggle();
            elen =".forslide:eq("+count+")";
            $(elen).fadeIn(900);
            elen =".forslide:eq("+(count+1)+")";
            $(elen).fadeIn(900);
            elen =".forslide:eq("+(count+2)+")";
            $(elen).fadeIn(900);
            count+=3;

        });
        $(".filterRowHid").promise().done(function(){
            $("#expandFiltersImage").attr("src","./images/arrow_up.png");
        });
        filters_toggled = 1;
    }
    else {
        
        $(".filterRowHid").animate({},0,function(){
           
            elen =".forslide:eq("+count+")";
            $(elen).fadeOut(600);
            elen =".forslide:eq("+(count+1)+")";
            $(elen).fadeOut(600);
            elen =".forslide:eq("+(count+2)+")";
            $(elen).fadeOut(600);
          
            count+=3;
        });
        
        $(".forslide").promise().done(function() {
            
            $(".filterRowHid").toggle();
            $("#expandFiltersImage").attr("src","./images/arrow_down.png");
        });
        
        filters_toggled = 0;

    } 
});


$(".rating1").stop().live("mousedown",function(e){
    e.preventDefault();
    var rank_width =parseInt($(".mainInnerRate").css("width"));
    var str_id = $(this).attr("id"); 
    var pos = str_id.indexOf("__");
    var startr = str_id.substr(pos+2); 
    var pos2 = str_id.indexOf("_");
    var cat_id_s = str_id.substr(pos2+1); 
    var pos3 = cat_id_s.indexOf("_");
    var cat_id = cat_id_s.substr(0,pos3);

    if(rank_width > 125)
	rank_width = 125;
    if(rank_width< 0)
	rank_width = 0;
    var rank = ((rank_width/125)*10).toFixed(2);
    var frank = (Math.ceil(rank*10)/10).toFixed(1);
    if(frank > 10)
        frank = 10;

    $(".rating1").attr("class","rating0");
    $(".mainOuterRateYes").attr("class","mainOuterRateNo");

    var xmlhttp3;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp3 =new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp3.onreadystatechange=function()
    {
        if (xmlhttp3.readyState==4){
            if(xmlhttp3.status==200){
                // check status
                var xml = xmlhttp3.responseXML;
                var status_ = xml.getElementsByTagName('result')[0].childNodes;
                var status = status_[0].childNodes[0].nodeValue;
                if(status == -1){
                    // error action
                    $(".mainInnerRate").css("width",startr+"px");
                    $("#ratecatmsg_"+cat_id).text("Error happened");
                    $(".rating1").attr("class","rating0");
                    $(".mainOuterRateYes").attr("class","mainOuterRateNo");
                }
                else{ 
                    var newr = parseFloat(status);
                    var newwidth = newr*12.5;
                    $(".mainInnerRate").css("width",newwidth+"px");
                    $("#ratecatmsg_"+cat_id).text("Your rating ("+frank+") has been saved");
                    $(".rating1").attr("class","rating0");
                    $(".mainOuterRateYes").attr("class","mainOuterRateNo");
                }
            }
            else {
                // error action
                $(".mainInnerRate").css("width",startr+"px");
                $("#ratecatmsg_"+cat_id).text("Error happened");
                $(".rating1").attr("class","rating0");
                $(".mainOuterRateYes").attr("class","mainOuterRateNo");
                
            }
        }

    }
    var url2 ="./logic_includes/logic.php";
    xmlhttp3.open("POST",url2,true);
    xmlhttp3.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp3.send("call=user_rates&mode=0&cat_id="+cat_id+"&rating="+rank);
});



/*rate entity*/
$(".firstColRateYes").live("mousedown",function(e){
    e.preventDefault();
  
    var mainO = $(this).find(".mainOuterRateInYes");
    var mainI = mainO.find(".mainInnerRateIn");
    var spanm = $(this).find(".rateEntity");
    
    
    spanm.attr("class","alreadyRated");
    mainO.attr("class","mainOuterRateInNo");
    
    var rank_width =parseInt(mainI.css("width"));
    var str_id = mainO.attr("id"); 
    var pos = str_id.indexOf("_");
    var startr = str_id.substr(pos+1); 
    var pos2 = startr.indexOf("_");
    var ent_id = startr.substr(0,pos2);
    var cat_id = startr.substr(pos2+1);
    if(rank_width > 125)
	rank_width = 125;
    if(rank_width< 0)
	rank_width = 0;
    var rank = ((rank_width/125)*10).toFixed(2);
    var frank = (Math.ceil(rank*10)/10).toFixed(1);
    if(frank > 10)
        frank = 10;

   
    
    var innerId = mainI.attr("id");
    var pos3 = innerId.indexOf("_");
    var startr2 = innerId.substr(pos3+1); 
    var pos4 = startr2.indexOf("_");
    var ent_id2 = startr2.substr(0,pos4);
    var prev_rate = startr2.substr(pos4+1);
    
    var wrapper = $("#ratewrap_"+ent_id+"_"+prev_rate);
    if (wrapper)
        wrapper.attr("id","nowrap_"+ent_id+"_"+prev_rate);
    
    var xmlhttp3;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp3 =new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp3.onreadystatechange=function()
    {
        if (xmlhttp3.readyState==4){
            if(xmlhttp3.status==200){
                // check status
                var xml = xmlhttp3.responseXML;
                var status_ = xml.getElementsByTagName('result')[0].childNodes;
                var status = status_[0].childNodes[0].nodeValue;
                if(status == -1){
                    // error action
                    spanm.text("Error happened");
                    spanm.attr("class","alreadyRated");
                    mainO.attr("class","mainOuterRateInNo");
                }
                else{ 
                    var newr = parseFloat(status);
                    var newwidth = newr*12.5;

                    mainI.css("width",newwidth+"px");
                    spanm.text("Your rating ("+frank+") has been saved");
                    spanm.attr("class","alreadyRated");
                    mainO.attr("class","mainOuterRateInNo");
                }
            }
            else {
                // error action
                spanm.text("Error happened");
                spanm.attr("class","alreadyRated");
                mainO.attr("class","mainOuterRateInNo");
                
            }
        }

    }

   // var url2 ="./logic_includes/logic.php";
//	var url2 = "./ajax/userRates.php";
var url2 ="./logic_includes/logic.php";
    xmlhttp3.open("POST",url2,true);
    xmlhttp3.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp3.send("call=user_rates&mode=1&cat_id="+cat_id+"&rating="+rank+"&ent_id="+ent_id);
});



$(".mainOuterRateInYes").stop().live("mousemove",function(e){
    e.preventDefault();
    var x = e.pageX - this.offsetLeft;  
    if(x>125)
	x = 125;
    if(x< 0)
	x = 0;
    var rank = ((x/125)*10).toFixed(2);
    var frank = (Math.ceil(rank*10)/10).toFixed(1);
    if(frank > 10)
        frank = 10;
    
    var str_id = $(this).attr("id"); /*rateent_entid_catid*/
    var pos = str_id.indexOf("_");
    var startr = str_id.substr(pos+1); 
    var pos2 = startr.indexOf("_");
    var ent_id = startr.substr(0,pos2);
    var cat_id = startr.substr(pos2+1);
    
    var wrap = $(this).children(".mainInnerRateIn");
    var str2_id = wrap.attr("id");
    var pos3 = str2_id.indexOf("_");
    var startr2 = str2_id.substr(pos3+1); 
    var pos4 = startr2.indexOf("_");
    var ent_id2 = startr2.substr(0,pos4);
    var prev_rate = startr2.substr(pos4+1);
    
    var wrapper = $("#nowrap_"+ent_id+"_"+prev_rate);
    if (wrapper)
        wrapper.attr("id","ratewrap_"+ent_id+"_"+prev_rate);
    
    
    $(this).children(".mainInnerRateIn").css("width",x+"px");
    $("#rate_"+ent_id).text("Your Rating is("+frank+") Click to send!");
 
});

$(".entSecCol").live("mouseover",function(){
    var str_id = $(this).attr("id");
    var pos = str_id.indexOf("_");
    var startr = str_id.substr(0,pos);
    var rest = str_id.substr(pos+1); /*ent_id _ rate */
    var pos2 = rest.indexOf("_");
    var ent_id = rest.substr(0,pos2);
    var rate   = rest.substr(pos2+1);
    
    if(startr == "ratewrap"){
        $(this).attr("id","nowrap_"+ent_id+"_"+rate);
        $("#innerwrap_"+ent_id+"_"+rate).css("width",rate+"px");
        $("#rate_"+ent_id).text("Rate it!");
    }
});

$(".mainOuterRateYes").stop().live("mousemove",function(e){
    e.preventDefault();
    var x = e.pageX - this.offsetLeft; 
    if(x > 125)
	x = 125;
    if(x< 0)
	x = 0; 
    var rank = ((x/125)*10).toFixed(2);
    var frank = (Math.ceil(rank*10)/10).toFixed(1);
    if(frank > 10)
        frank = 10;
    
    var span_ = $(this).parents();
    

    var str_id = span_.attr("id"); 
    var pos = str_id.indexOf("__");
    var pos2 = str_id.indexOf("_");
    var cat_id_s = str_id.substr(pos2+1); 
    var pos3 = cat_id_s.indexOf("_");
    var cat_id = cat_id_s.substr(0,pos3);
   
    $(".mainInnerRate").css("width",x+"px");
    $("#ratecatmsg_"+cat_id).text("Your Rating is ("+frank+") Click to send!!");
});

$('#jumpCatSub').live('submit',function(e) {

    e.preventDefault();
    var rest = $("#url_ex").val();
    var valu = parseInt(document.jumpform.nump.value);
    
    
    window.location=rest+"&nump="+valu;

});


function ChangeCatPage(rest,change,value){
    var url = rest+"&"+change+"="+value;
    window.location=url;
}


$('#selectA').live("click",function(){
    var list ="";
    var new_c = 0;
    
    $('.compCheck').each(function(){ 
       if(!$(this).is(':checked')){
           new_c++;
           var ele_id = $(this).attr("id");
           var str_1 = ele_id.substr(7); /*333_22 (333 is entity id) (22 is cat id)*/
           var pos = str_1.indexOf("_");
           var ent_id = str_1.substr(0,pos);
           $(this).attr('checked',true);             
           list+=("_"+ent_id);
       }
    });
    
    $('.compCheck').promise().done(function(){
        var ele_id = $(this).attr("id");
        var str_1 = ele_id.substr(7); /*333_22 (333 is entity id) (22 is cat id)*/
        var pos = str_1.indexOf("_");
        var cat_id = str_1.substr(pos+1);
        
        var xmlhttp3;
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp3 =new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp3.onreadystatechange=function()
        {
            if (xmlhttp3.readyState==4 && xmlhttp3.status==200){
                // added an entity
                var count = parseInt($("#compareCount").text());
                $("#compareCount").text(count+new_c);
            }
        }
        var url2 ="./logic_includes/logic.php";
        xmlhttp3.open("POST",url2,true);
        xmlhttp3.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        var ent_list = list.substr(1);
        xmlhttp3.send("call=addtocomp&mode=3&cat_id="+cat_id+"&ent_ids="+ent_list);
    });
});

$('#uselectA').live("click",function(){
     $('.compCheck').each(function(){ 
       if($(this).is(':checked')){
           $(this).attr('checked',false);
           // xml output data
       }
    });
    
    $('.compCheck').promise().done(function(){
        var ele_id = $(this).attr("id");
        var str_1 = ele_id.substr(7); /*333_22 (333 is entity id) (22 is cat id)*/
        var pos = str_1.indexOf("_");
        var ent_id = str_1.substr(0,pos);
        var cat_id = str_1.substr(pos+1);
        var xmlhttp3;
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp3 =new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp3.onreadystatechange=function()
        {
            if (xmlhttp3.readyState==4 && xmlhttp3.status==200){
                // added an entity
                $("#compareCount").text(0);
                $("#compareContainer").fadeOut(200, function(){
                    $("#compareContainerDef").fadeIn(200);
                });

            }
        }
        var url2 ="./logic_includes/logic.php";
        xmlhttp3.open("POST",url2,true);
        xmlhttp3.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp3.send("call=addtocomp&mode=2&cat_id="+cat_id);
       
    })
});

/*add or delete one entity from comparison*/
$('.compCheck').live("click",function() {

    var ele_id = $(this).attr("id");
    var str_1 = ele_id.substr(7); /*333_22 (333 is entity id) (22 is cat id)*/
    var pos = str_1.indexOf("_");
    var ent_id = str_1.substr(0,pos);
    var cat_id = str_1.substr(pos+1);
    var count = 0;
    
    // xml output data for add entity to compare
    var xmlhttp3;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp3 =new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp3.onreadystatechange=function()
    {
        if (xmlhttp3.readyState==4 && xmlhttp3.status==200){
            // added an entity
            count = parseInt($("#compareCount").text());
            $("#compareCount").text(count+1);
            count++;
            if (count == 1){
                $("#compareContainerDef").fadeOut(200, function(){
                    $("#compareContainer").fadeIn(200);
                });
            }
        }
    }
    
    var xmlhttp4;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp4 =new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp4=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp4.onreadystatechange=function()
    {
        if (xmlhttp4.readyState==4 && xmlhttp4.status==200){
            count = parseInt($("#compareCount").text());
            $("#compareCount").text(count-1);
            count--;
 
            if(count == 0){
                $("#compareContainer").fadeOut(200, function(){
                    $("#compareContainerDef").fadeIn(200);
                });   
            }
        }
    }
    
  
    if ($(this).is(':checked')){
        var url2 ="./logic_includes/logic.php";
        xmlhttp3.open("POST",url2,true);
        xmlhttp3.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp3.send("call=addtocomp&mode=0&cat_id="+cat_id+"&ent_id="+ent_id);

    }
    else{
        var url3 ="./logic_includes/logic.php";
        xmlhttp4.open("POST",url3,true);
        xmlhttp4.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp4.send("call=addtocomp&mode=1&cat_id="+cat_id+"&ent_id="+ent_id);
   
    }
    
    
    
});

$.fn.scrollToElement = function(selector, callback) {
    var def = new $.Deferred(),
        el = this;

    $('html, body').animate({scrollTop: $(selector).offset().top}, 'slow', 'swing', def.resolve);

    if (callback) {
        def.promise().done(function(){
            callback.call(el);
        });
    }
};

$("#backToTop").live("click",function(){
    var bodyelem;
    if($.browser.safari) 
	bodyelem = $("body")
    else 
	bodyelem = $("html,body")

    bodyelem.scrollToElement('#catEntResults', function() {
	});

});

$(".compareBtn").live("click",function(){
    var str_id = $(this).attr("id");
    var pos = str_id.indexOf("_");

    var rest = str_id.substr(pos+1); /*cat_id_mode */
    var pos2 = rest.indexOf("_");
    var cat_id = rest.substr(0,pos2);
    var mode   = rest.substr(pos2+1);   
 
    if(mode == 0){
        /*compare all first refresh session */
        var xmlhttp4;
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp4 =new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp4=new ActiveXObject("Microsoft.XMLHTTP");
        }


        var url3 ="./logic_includes/logic.php";
        xmlhttp4.open("POST",url3,false);
        xmlhttp4.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp4.send("call=addtocomp&mode=4&cat_id="+cat_id);
        
    }
    window.location='./comparison.php?cat_id='+cat_id;
})