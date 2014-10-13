/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: user panel gui part		                            **|
|   									    **|                        
|   			                                                    **|   
\*****************************************************************************/

var userPanel_toggle = 0;
var openable = 0;
var content;


/*** preformatting userPanel content for guest ***/
var contentGuest = "<ul>"+"<li><a class=\"registerPrompt\"href=\"register.php\"><span>Dont have an account?</span><span class=\"registerPromptLink\">&nbsp;<u>Register</u></span></a></span></li>"+"<li><span class=\"credentialsPrompt\">Please supply your credentials:</span></li>"+"</ul>";
contentGuest += "<table>"+"<tr><td><label class=\"usernamePrompt\">Username:&nbsp;</span></td><td><input class=\"loginUsername\" type=\"text\"></td></tr>";
contentGuest += 	      "<tr><td><label class=\"passwordPrompt\">Password:&nbsp;</span></td><td><input class=\"loginPassword\" id=\"panelPassword\" type=\"password\"></td></tr>";
contentGuest += 	      "<tr>";
contentGuest += "</table><span id=\"buttonSection\" align=\"center\"><button id=\"loginButton\">Login</button></span> <span id=\"loginResult\"></span>";

/*** preformatting userPanel content for user ***/
var contentUser = "<ul id=\"logged_content\">"+"<li><img src='images/manageAccountInactive.png'><a href='./manage_account.php' title='manage account [alt-v]' accesskey='v'>Manage account</a></li>";
contentUser += 		"<li><img src='images/createCategoryInactive.png'><a href='./create_category.php' title='create category [alt-c]' accesskey='c'>Create category</a></li>";
contentUser +=		"<li><img src='images/editCategoryInactive.png'><a href='./my_categories.php' title='My categories [alt-e]' accesskey='e'>My Categories</a></li>";
contentUser +=		"<li><img src='images/logoutInactive.png'><a href=logout.php?redirect="+document.location+" title='Log out'>Log out</a></li>";
contentUser += "</ul>";


$(function() {
    
        $( "#panelPassword" ).live( "keyup", function(event){
            if ( event.keyCode == 13 ) 
                $("#loginButton").click();

        });
    
	$("#userPanel").live( "hover", function() {
            
                $("#userPanel").stop();
                $("#userPanel").animate({
                    height: '270px'

                });

                $("#userPanel div.content").css( "display", "block" );

                if( global_username == "" ) {
                        content = contentGuest;
                        $("#userPanel div.content").html(content);                
                        $("#loginButton").bind("click",login);
                }
                else {
                        content = contentUser;
                        $("#userPanel div.content").html(content);
                }            
            
        });        
        
        $( ".content" ).live( "mouseover", function(event) {
                event.stopPropagation();
                event.preventDefault();
        })
        
        $( ".userImg" ).live( "mouseover", function(event) {
                event.stopPropagation();
                event.preventDefault();
        })
        
         $( ".userInfo" ).live( "mouseover", function(event) {
                event.stopPropagation();
                event.preventDefault();
        })
        
        $( "#userPanel").live( "mouseleave", function() { 
            
            $("#userPanel").stop();
            $("#userPanel div.content").css( "display", "none" );
            $("#userPanel").animate({
                height: '70px'
            
            });
            
        })
        	
});
