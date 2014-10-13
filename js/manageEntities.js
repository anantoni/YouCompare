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
|   Description: GUI part Javascript for page manage entities               **|
|        Lines : 555                                                        **|   
\*****************************************************************************/


function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
	});
	return vars;
}


/*
var addEntity_cat_id;
var entity_name;
var entity_desc;
var entity_img;
var entity_vid;
var attributes = {};
var object = new Object();
var i=0;


*/




/*
$("#enTabletoggle").live("click",function(e){
    e.preventDefault();
    var pointer = $(this);
    $(".MgEnTable").slideToggle(1000,function(){
            var innersp = pointer.children("span").text();
            if(innersp == "[Hide]")
                pointer.children("span").text("[Show]");
            else
                pointer.children("span").text("[Hide]");
    });
})
*/


$(".resetInputAble").live("focus",function(){

    $(".overlay").each(function(){
        $(this).attr("id","emptyIdName");
    });
    
    $(this).parents("tr").find(".overlay").attr("id","highlightName2");
});
/*
$("#AddItemInner tr").stop().live("mouseover",function(e){
    e.preventDefault();
  
    
    $(".overlay").each(function(){
        $(this).attr("id","emptyIdName");
    });
    
    $(this).find(".overlay").attr("id","highlightName2");
   
});*/

$("#enTabletoggle").stop().live("click",function(e){
    e.preventDefault();
    var pointer= $(this);
    
    $(".MgEnTable").find("tr").each(function(){
        $(this).animate({},0,function(){});
    });
    $(".MgEnTable").promise().done(function() {
        $(this).find("tr").each(function(){
            $(this).toggle();
        })
        $(this).find("tr").promise().done(function(){
            $(".MgEnTable").toggle();
            var innersp = pointer.children("span").text();
            if(innersp == "[Hide]")
                pointer.children("span").text("[Show]");
            else
                pointer.children("span").text("[Hide]");
        })
          
    });
});

$("#AddEntInfo").live("click",function(e){
    e.preventDefault();
    var pointer = $(this);
    if($("#AddEntDiv").is(":visible")){
        $("#AddEntDiv").slideUp(1000, function(){
            var innersp = pointer.children("span").text();
            if(innersp == "[Hide]")
                pointer.children("span").text("[Show]");
            else
                pointer.children("span").text("[Hide]");
        });   
    }
    else{
        $("#AddEntDiv").slideDown(1000, function(){
            var innersp = pointer.children("span").text();
            if(innersp == "[Hide]")
                pointer.children("span").text("[Show]");
            else
                pointer.children("span").text("[Hide]");
        });    
    }
    
})

$("#MgEnAddButton").live("click",function(e){
    e.preventDefault();
    var id = getUrlVars()["cat_id"];
    
    $.post("./logic_includes/logic.php", {call: "show_category_attrs" , cat_id: id},function(xml){
    if(xml.length > 0){	
        if(!$("#AddEntDiv").is(':visible')){
            if(!$("#AddEntInfo").is(':visible')){
                $("#AddEntInfo").fadeIn(0, function(){
                    $("#AddEntDiv").text("");
                    $("#AddEntDiv").append(xml);
                    $("#AddEntDiv").slideDown(1000, function(){

                    });
                });
            }
            else{
               $("#AddEntInfo").children("span").text("[Hide]"); 
               $("#AddEntDiv").text("");
                $("#AddEntDiv").append(xml);
                $("#AddEntDiv").slideDown(1000, function(){

                });
            }
        }
        else{
            /*already pressed add item*/
            $("#AddEntDiv").text("");
            $("#AddEntDiv").append(xml);
        }
    }
    else{
        alert("oops , empty response.sorry.");	
    }   
    
    }); 
});
   
   
$(".MgEnInResetButton").live("click",function(e){
    e.preventDefault();
    $("#AddEntDiv").find(".resetInputAble").each(function(){
        $(this).val('');
    });
    $("#AddEntDiv").find(".resetSelectInp").each(function(){
        $(this).val($(this).find("option:first").val());
    })
    
    $(".entError").each(function(){
        $(this).text("");
    })
});

$(".MgEnInCloseButton").live("click",function(e){
    e.preventDefault();
    $("#AddEntDiv").slideUp(800, function(){
            $("#AddEntInfo").fadeOut(0, function(){
            
            });
    });
});


$(".MgEnInSubmitButton").live("click",function(e){
    e.preventDefault();
    var mode_n = $(this).attr("id");
    if(typeof mode_n != "undefined"){
        var pos = mode_n.indexOf("_");
        var mode = mode_n.substr(0,pos);
        var edit = mode_n.substr(pos+1);
   
        if(typeof mode != "undefined"){
            if(mode == "edit"){
                if(edit == 0){
                    /*only by moderators*/
                    $('#addedititemform').append('<input type="hidden" name="edit" value="0" />');
                }
                else{
                    /*new entity of him*/
                    $('#addedititemform').append('<input type="hidden" name="edit" value="1" />');
                }
            }
        }
        else
            $('#addedititemform').append('<input type="hidden" name="edit" value="0" />'); 
    }
        
    $("#addedititemform").submit();
});




function entCheckUncomparable(val,count){

    var error = $("input[type=text][name^=attr]").eq(count).parent("td").find(".entError");
    error.css("margin-left", "20px");
    if(val.length > 45){
        error.css("color","#D61D1D");
        error.text("Value too Long");
    }
    else if(val.length > 0){
        error.css("color","#36A810");  
        error.text("Valid Value"); 
    }
    else
        error.text("");
}

function entCheckCountable(val,low,high,count){
     var lowlimit   = parseFloat(low);
     var highlimit  = parseFloat(high);
     var value      = parseFloat(val);
     var error = $("input[type=text][name^=attr]").eq(count).parent("td").find(".entError");
     error.css("margin-left", "20px");
        
     if(isNaN(val)){
        error.css("color","#D61D1D");
        error.text("Please Enter a number");    
     }
     else if(value > highlimit || value < lowlimit){
        error.css("color","#D61D1D");
        error.text("Out of Bounds");    
     }
     else if(val.length>0){
        error.css("color","#36A810");  
        error.text("Valid Value"); 
     }
     else
         error.text("");
}

function entCheckName2(val,old_name){

    var error =$("input[type=text][name=name]").parent("td").find(".entError");
    error.css("margin-left", "20px");

    if(val.length > 50){
        error.css("color","#D61D1D");
        error.text("Name too Long");
    }
    else if(val.length == 0){
        error.css("color","#D61D1D");
        error.text("You must enter a name");  
    }
    else if(val.length < 4){
        error.css("color","#D61D1D");
        error.text("Name too short");
    }
    else if(val == old_name){
        error.css("color","#36A810");  
        error.text(val+" is Available");   
    }
    else{
        error.css("color","#36A810");  
        error.text(val+" is Valid");  
    }
}

function entCheckName3(val){
    var error =$("input[type=text][name=name]").parent("td").find(".entError");
    error.css("margin-left", "20px"); 
    var cid = getUrlVars()["cat_id"];
    $.post("./logic_includes/logic.php", {call: "check_entity_name",cat_id: cid, name:val},function(xml){
        if(xml.length > 0){	
            if(xml == "0"){
                // not available
                error.css("color","#D61D1D");
                error.text(val+" already Exists");
            }
            else if(xml == "1"){
                error.css("color","#36A810");  
                error.text(val+" is Available");
            }
        }
        else{

        }
    });
}

function entCheckName4(val,old_name){
    var error =$("input[type=text][name=name]").parent("td").find(".entError");
    error.css("margin-left", "20px"); 
    if(val == old_name){
        error.css("color","#36A810");  
        error.text(val+" is Available");   
    }
    else{
            var cid = getUrlVars()["cat_id"];
         $.post("./logic_includes/logic.php", {call: "check_entity_name",cat_id: cid, name:val},function(xml){
                if(xml.length > 0){	
                    if(xml == "0"){
                        // not available
                        error.css("color","#D61D1D");
                        error.text(val+" already Exists");
                    }
                    else if(xml == "1"){
                        error.css("color","#36A810");  
                        error.text(val+" is Available");
                    }
                }
                else{
                    
                }
         });
    }
}

function entCheckName(val){

    var error =$("input[type=text][name=name]").parent("td").find(".entError");
    error.css("margin-left", "20px");

    if(val.length > 50){
        error.css("color","#D61D1D");
        error.text("Name too Long");
    }
    else if(val.length == 0){
        error.css("color","#D61D1D");
        error.text("You must enter a name");  
    }
    else if(val.length < 4){
        error.css("color","#D61D1D");
        error.text("Name too short");
    }
    else{
        error.css("color","#36A810");  
        error.text(val+" is Valid");  
    } 
}
function entCheckDesc(val){
    var error =$("textarea").parent("td").find(".entError");
    error.css("margin-left", "20px");

    if(val.length > 500){
         error.css("color","#D61D1D");
         error.text("Description too Long");
    }
    else if(val.length > 1){
        error.css("color","#36A810");  
        error.text("Valid Description");
    }
    else
        error.text("");
}


var run_slide = false;
/*
function scrollToElement(selector, callback){
    var animation = {scrollTop: $(selector).offset().top};
    alert(1);
    $('html,body').animate(animation, 'slow', 'swing', function(){
	if(typeof callback == 'function' && run_slide){
		callback();
	}
        if(typeof callback == 'function' && !run_slide){
            run_slide = true;
        }
    });
}*/



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


$(".MgEnEditButton").live("click",function(e){
    e.preventDefault();
    var cid = getUrlVars()["cat_id"];
    var enrn = $(this).attr("id");
    var pos = enrn.indexOf("_");
    var ent_id_ = enrn.substr(pos+1);
    var pos2 = ent_id_.indexOf("_");
    var ent_idn = ent_id_.substr(0,pos2);
    var mode = ent_id_.substr(pos2+1);

    var bodyelem;
    if($.browser.safari) 
	bodyelem = $("body")
    else 
	bodyelem = $("html,body")

    $.post("./logic_includes/logic.php", {call: "get_attrs_edit" , cat_id: cid, ent_id:ent_idn,"qedit":mode},function(xml){
    if(xml.length > 0){	
        if(!$("#AddEntDiv").is(':visible')){
            if(!$("#AddEntInfo").is(':visible')){
                $("#AddEntInfo").fadeIn(0, function(){
                    $("#AddEntDiv").text("");
                    $("#AddEntDiv").append(xml);
                    bodyelem.scrollToElement('#MgEnAddButton', function() {
   
   	           	 $("#AddEntDiv").slideDown(1000, function(){
       	        	$(".MgEnInSubmitButton").attr("id","edit_"+mode);
                   	});
		
			});
                });
            }
            else{
               $("#AddEntInfo").children("span").text("[Hide]"); 
               $("#AddEntDiv").text("");
                $("#AddEntDiv").append(xml);
		
		bodyelem.scrollToElement('#MgEnAddButton', function() {
   
   	            $("#AddEntDiv").slideDown(1000, function(){
       	        $(".MgEnInSubmitButton").attr("id","edit_"+mode);
                   });
		
		});
		
	     }
        }
        else{
            /*already pressed add item*/
            $("#AddEntDiv").text("");
            $("#AddEntDiv").append(xml);
            $(".MgEnInSubmitButton").attr("id","edit_"+mode);
            bodyelem.animate({scrollTop:$("#MgEnAddButton").offset().top}, 'slow');
        }
        
    }
    else{
        alert("oops , empty response.sorry.");	
    } 
    });
    
   
    
    
});














/* MANAGE ENTITIES*/
(function($){
	
	$.confirm = function(params){
		if($('#confirmOverlay').length){
			// A confirm is already shown on the page:
			return false;
		}
		
		var buttonHTML = '';
		$.each(params.buttons,function(name,obj){
			
			// Generating the markup for the buttons:
			
			buttonHTML += '<a href="#" class="button '+obj['class']+'">'+name+'<span></span></a>';
			
			if(!obj.action){
				obj.action = function(){};
			}
		});
		
		var markup = [
			'<div id="confirmOverlay">',
			'<div id="confirmBox">',
			'<h1>',params.title,'</h1>',
			'<p>',params.message,'</p>',
			'<div id="confirmButtons">',
			buttonHTML,
			'</div></div></div>'
		].join('');
		
		$(markup).hide().appendTo('body').fadeIn();
		
		var buttons = $('#confirmBox .button'),
			i = 0;

		$.each(params.buttons,function(name,obj){
			buttons.eq(i++).click(function(){
				
				// Calling the action attribute when a
				// click occurs, and hiding the confirm.
				
				obj.action();
				$.confirm.hide();
				return false;
			});
		});
	};

	$.confirm.hide = function(){
		$('#confirmOverlay').fadeOut(function(){
			$(this).remove();
		});
	};
	
})(jQuery);


/* delete entity */

$(".MgEnDeleteButton").live("click",function(e){
    e.preventDefault();

    var cat_id = getUrlVars()["cat_id"];
    var enrn = $(this).attr("id");
    var pos = enrn.indexOf("_");
    var ent_id_ = enrn.substr(pos+1);
    var pos2 = ent_id_.indexOf("_");
    var ent_id = ent_id_.substr(0,pos2);
    var mode = ent_id_.substr(pos2+1);
    
    var ent_name = $(this).parent("td").parent("tr").find(".MgEnEntName").text();

   
    $.confirm({
            'title'     : 'Delete Entity Confirmation',
            'message'	: 'You are about to Delete <b>'+ent_name+'</b><br />Please Confirm this action! Continue?',
            'buttons'	: {
                    'Yes'   : {
                            'class': 'blue',
                            'action': function(){
                                window.location='./ajax/deleteentity.php?cat_id='+cat_id+"&delete="+mode+"&ent_id="+ent_id;
                            }
                    },
                    'No'    : {
                            'class' : 'blue',
                            'action': function(){
                            }	
                    }
            }
    });
    
});






$(".entRow").stop().live("mouseover",function(e){
    e.preventDefault();

    $(".MgEnEntName").each(function(){
        $(this).attr("id","emptyIdName");
    })
    $(this).find(".MgEnEntName").attr("id","highlightName");
    
});



