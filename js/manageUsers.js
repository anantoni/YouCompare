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
|   Description: GUI part Javascript for page manage users                  **|
|        Lines : 339                                                        **|   
\*****************************************************************************/

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



$(".userRow").stop().live("mouseover",function(e){
    e.preventDefault();
    /* clear all */
    $(".MgUrUserName").each(function(){
        $(this).attr("id","emptyIdName");
    })
    $(this).find(".MgUrUserName").attr("id","highlightName");
    
});


$(".table_toggle").stop().live("click",function(e){
    e.preventDefault();
    var pointer= $(this);
    var table_st= $(this).attr("id");
    var pos = table_st.indexOf("_");
    var table_id = table_st.substr(pos+1);
    
    $("table").eq(table_id).find("tr").each(function(){
        $(this).animate({},0,function(){});
    });
    $("table").eq(table_id).promise().done(function() {
        $(this).find("tr").each(function(){
            $(this).toggle();
        })
        $(this).find("tr").promise().done(function(){
            $("table").eq(table_id).toggle();
            var innersp = pointer.children("span").text();
            if(innersp == "[Hide]")
                pointer.children("span").text("[Show]");
            else
                pointer.children("span").text("[Hide]");
        })
          
    });
});



/* approve pending member*/
$(".MgUsApproveButton").live("click",function(e){
    e.preventDefault();
    var cat_id = parseInt($("#MgUsCategoryId").text());
    var usrn = $(this).attr("id");
    var pos = usrn.indexOf("_");
    var username = usrn.substr(pos+1);
    
    
    var select = $(this).parent().parent(".userRow").children("td").children(".priv_select");
    var priviledges = select.val();
    /*0 ,1 ,2 ,3 */
    var usertype ="-";
    if(priviledges == 0){
        usertype = "Category Member";
    }
    else if(priviledges == 1){
        usertype = "Category Editor";
    }
    else if(priviledges == 2){
        usertype = "Sub-Moderator";
    }
    else if(priviledges == 3){
        usertype = "Moderator";
    }
    $.confirm({
            'title'		: 'Approve Member Confirmation',
            'message'	: 'Approve <b>'+username+'</b> to be '+usertype+'. <br />Please Confirm this action! Continue?',
            'buttons'	: {
                    'Yes'	: {
                            'class': 'blue',
                            'action': function(){
                                var xmlhttp3;
                                if (window.XMLHttpRequest)
                                {// code for IE7+, Firefox, Chrome, Opera, Safari
                                    xmlhttp3 =new XMLHttpRequest();
                                }
                                else
                                {// code for IE6, IE5
                                    xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
                                }


                                var url2 ="./logic_includes/logic.php";
                                xmlhttp3.open("POST",url2,false);
                                xmlhttp3.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                                xmlhttp3.send("call=manage_users&mode=0&cat_id="+cat_id+"&ch_username="+username+"&priv="+priviledges);
                                window.location='./manageUsers.php?cat_id='+cat_id;
                            }
                    },
                    'No'	: {
                            'class' : 'blue',
                            'action': function(){
                            }	
                    }
            }
    });
    
});



/* reject pending member*/
$(".MgUsRejectButton").live("click",function(e){
    e.preventDefault();
    var cat_id = parseInt($("#MgUsCategoryId").text());
    var usrn = $(this).attr("id");
    var pos = usrn.indexOf("_");
    var username = usrn.substr(pos+1);
    
    
    var select = $(this).parent().parent(".userRow").children("td").children(".priv_select");
    var priviledges = select.val();
    /*0 ,1 ,2 ,3 */
    
    $.confirm({
            'title'		: 'Reject Member Confirmation',
            'message'	: 'Reject <b>'+username+'</b> request. <br />Please Confirm this action! Continue?',
            'buttons'	: {
                    'Yes'	: {
                            'class': 'blue',
                            'action': function(){
                                var xmlhttp3;
                                if (window.XMLHttpRequest)
                                {// code for IE7+, Firefox, Chrome, Opera, Safari
                                    xmlhttp3 =new XMLHttpRequest();
                                }
                                else
                                {// code for IE6, IE5
                                    xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
                                }


                                var url2 ="./logic_includes/logic.php";
                                xmlhttp3.open("POST",url2,false);
                                xmlhttp3.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                                xmlhttp3.send("call=manage_users&mode=1&cat_id="+cat_id+"&ch_username="+username+"&priv="+priviledges);
                                window.location='./manageUsers.php?cat_id='+cat_id;    
                            }
                    },
                    'No'	: {
                            'class' : 'blue',
                            'action': function(){
                            }	
                    }
            }
    });
    
    
});



/* update member */
$(".MgUsUpdateButton").live("click",function(e){
    e.preventDefault();
    var cat_id = parseInt($("#MgUsCategoryId").text());
    var usrn = $(this).attr("id");
    var pos = usrn.indexOf("_");
    var username = usrn.substr(pos+1);
    
    
    var select = $(this).parent().parent(".userRow").children("td").children(".priv_select");
    var priviledges = select.val();
    /*0 ,1 ,2 ,3 */
    var usertype ="-";
    if(priviledges == 0){
        usertype = "Category Member";
    }
    else if(priviledges == 1){
        usertype = "Category Editor";
    }
    else if(priviledges == 2){
        usertype = "Sub-Moderator";
    }
    else if(priviledges == 3){
        usertype = "Moderator";
    }
    
    $.confirm({
            'title'		: 'Change Privilege Confirmation',
            'message'	: 'Update <b>'+username+'</b> to be '+usertype+'. <br />Please Confirm the Update! Continue?',
            'buttons'	: {
                    'Yes'	: {
                            'class': 'blue',
                            'action': function(){
                                var xmlhttp3;
                                if (window.XMLHttpRequest)
                                {// code for IE7+, Firefox, Chrome, Opera, Safari
                                    xmlhttp3 =new XMLHttpRequest();
                                }
                                else
                                {// code for IE6, IE5
                                    xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
                                }


                                var url2 ="./logic_includes/logic.php";
                                xmlhttp3.open("POST",url2,false);
                                xmlhttp3.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                                xmlhttp3.send("call=manage_users&mode=2&cat_id="+cat_id+"&ch_username="+username+"&priv="+priviledges);
                                window.location='./manageUsers.php?cat_id='+cat_id;
                            }
                    },
                    'No'	: {
                            'class' : 'blue',
                            'action': function(){
                            }	
                    }
            }
    });

    
    
});



/*delete member*/
$(".MgUsDeleteButton").live("click",function(e){
    e.preventDefault();
    var cat_id = parseInt($("#MgUsCategoryId").text());
    var usrn = $(this).attr("id");
    var pos = usrn.indexOf("_");
    var username = usrn.substr(pos+1);
    
    
    var select = $(this).parent().parent(".userRow").children("td").children(".priv_select");
    var priviledges = select.val();
    /*0 ,1 ,2 ,3 */


    $.confirm({
            'title'		: 'Delete Confirmation',
            'message'	: 'Delete Member <b>'+username+'</b>. <br />Please Confirm the deletion! Continue?',
            'buttons'	: {
                    'Yes'	: {
                            'class': 'blue',
                            'action': function(){
                                    var xmlhttp3;
                                    if (window.XMLHttpRequest)
                                    {// code for IE7+, Firefox, Chrome, Opera, Safari
                                        xmlhttp3 =new XMLHttpRequest();
                                    }
                                    else
                                    {// code for IE6, IE5
                                        xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
                                    }


                                    var url2 ="./logic_includes/logic.php";
                                    xmlhttp3.open("POST",url2,false);
                                    xmlhttp3.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                                    xmlhttp3.send("call=manage_users&mode=3&cat_id="+cat_id+"&ch_username="+username+"&priv="+priviledges);
                                    window.location='./manageUsers.php?cat_id='+cat_id;   
                            }
                    },
                    'No'	: {
                            'class' : 'blue',
                            'action': function(){
                            }	
                    }
            }
    });
	
});