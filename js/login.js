var toggle = 0;

function login(){
		var usern = $(".loginUsername").val();
		var pass = $(".loginPassword").val();
		

                $( "#loginResult" ).html("<img class='loading' src='images/loadingLogin9.gif'>");                  
                
                
		$.post( "logic_includes/logic.php", { call: "login", username: usern , password: pass }, function(xml) {
			if( xml.length > 0 ){
                            
                                var status = parse_status(xml);
                                if( status == 0 ) {
                                    
                                        $( "#loginResult" ).html( "<b>Login successful</b>" );   
                                        if ( location.pathname.substr( location.pathname.lastIndexOf("/") + 1) != "login.php" )
                                            window.location.reload();
                                        else
                                            window.location = "my_categories.php";
                                        
                                }
				else {
                                        parse_errors(xml);
                                        for ( var error in errorArray ) {

                                                        if ( errorArray[error] == "AUTHENTICATION_FAILED" ) 
                                                                $( "#loginResult" ).html( "<div id=\"loginError\">Invalid Credentials</div>" );                                                               

                                                        else if ( errorArray[error] == "UNVERIFIED_USER" ) 
                                                                $( "#loginResult" ).html( "<div id=\"loginError\">Unverified User</div>" );  

                                                        else if ( errorArray[error] == "BAD_REQUEST" ) 
                                                                $( "#loginResult" ).html( "<div id=\"loginError\">Unknown Error</div>" );

                                                        else if ( errorArray[error] == "MYSQL_CONNECT_ERROR" || errorArray[error] == "MYSQL_ERROR"  ) 
                                                                $( "#loginResult" ).html( "<div id=\"loginError\">Database Error</div>" );

                                        }
                                }
			}
			else 
                                        $( "#loginResult" ).html( "<div id=\"loginError\">An error has occured</div>" );
			
		});
}

$(function() {
    
    $( "#password_input" ).keyup( function(event){
            if ( event.keyCode == 13 ) 
                $("#loginButton").click();
            
    })


    $("#loginButton").bind("click",login);

})
          
    

