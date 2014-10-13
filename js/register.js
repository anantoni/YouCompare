var toggle = [];

var usern = "";
var pass = "";
var mail = "";
var fName = "";
var lName = "";
var mailConfirm = "";
var passConfirm = "";
var usernamePattern = /[^A-Za-z0-9-_]/;
var passwordPattern = /[^A-Za-z0-9-_]/;
var emailPattern = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
var usernameErrorFound = true;
var firstnameErrorFound = true;
var lastnameErrorFound = true;
var emailErrorFound = true;
var emailConfirmErrorFound = true;
var passwordErrorFound = true;
var passwordConfirmErrorFound = true;

$(document).ready(function() {
	/*** enabling tooltips ***/
	/*$("#register [title]").tooltip({
				position: "center right",
				offset: [12, 10],
				effect: "fade",
				opacity: 0.7
	});*/
        $("#registerButton").attr("disabled", "enabled");
});

function checkValidity(event){
	/*fetching input values*/
	var className = event.target.className;
	event.stopPropagation();
        
       
        /************************************ Username Check ************************************/
	if ( className == "registerUsername" ) {
		if ( $(".registerUsername").val().length <= 5 ) { 
                        if ( $(".registerUsername").val() != "" )
                            $(".registerUsernameError").html("<img src='images/invalid_input.png'> Too short").show('slow');
                        usernameErrorFound = true;
                }
	        else if ( $(".registerPasswordConfirm").val().length > 40 ) {
                        $(".registerPasswordConfirmError").html( "<img src='images/invalid_input.png'> Too long" ).show('slow');
                        passwordConfirmErrorFound = true;
                }
                else if ( usernamePattern.test( $(".registerUsername").val() ) ) {
                        $(".registerUsernameError").html("<img src='images/invalid_input.png'> Invalid username").show('slow');
                        usernameErrorFound = true;
                }
		else {  
                    
			$(".registerUsernameError").html("<img class='loading' src='images/loadingLogin9.gif'>");
			/*checking username availability*/
			$.post("logic_includes/logic.php", { call: "check_username" , username: $(".registerUsername").val() },function(xml){
				if ( xml.length > 0 ) {
					var status = parse_status(xml);
					if ( status == 0 ) {
						$(".registerUsernameError").html("<img src='images/valid_input.png'>").show('slow');
                                                usernameErrorFound = false;
                                        }
					else {
						parse_errors(xml);
						for ( var error in errorArray ) {
							if ( errorArray[error] == "USERNAME_ALREADY_EXISTS" ) {
								$(".registerUsernameError").html("<img src='images/invalid_input.png'> Username taken").show('slow');
                                                                usernameErrorFound = true;
                                                        }
							else if ( errorArray[error] == "BAD_REQUEST" ) {
								$(".registerUsernameError").html("<img src='images/invalid_input.png'> Unknown Error").show('slow');
                                                                usernameErrorFound = true;
                                                        }
                                                        else {
                                                                $(".registerUsernameError").html("<img src='images/invalid_input.png'> Internal Error").show('slow');
                                                                usernameErrorFound = true;
                                                        }
						}
					}
				}
				else {
					 $(".registerUsernameError").html("AJAX Error");
                                         usernameErrorFound = true;
                                }
			});
		}
                
                if ( !usernameErrorFound && !passwordErrorFound && !firstnameErrorFound && !lastnameErrorFound && !emailErrorFound && !passwordConfirmErrorFound && !emailConfirmErrorFound ) {
                        $("#registerButton").removeAttr("disabled");              
                        $("#registerButton").addClass("registerButtonEnabled");
                }
                else {
                        $("#registerButton").attr( "disabled", "enabled" );
                        $("#registerButton").removeClass("registerButtonEnabled");                     
                }
                        
	}
        /************************************ First Name Check ************************************/
	else if ( className == "registerFname" ) {
            
                $(".registerFnameError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                
		if ( $(".registerFname").val().length > 20 ) {
			$(".registerFnameError").html("<img src='images/invalid_input.png'> Too long").show('slow');
                        firstnameErrorFound = true;
                }
                else if ( usernamePattern.test( $(".registerFname").val() ) ) {
                        $(".registerFnameError").html("<img src='images/invalid_input.png'> Invalid").show('slow');
                        firstnameErrorFound = true;
                }
                else {
                        $(".registerFnameError").html("<img src='images/valid_input.png'>").show('slow');
                        firstnameErrorFound = false;
                }
                    
                if ( !usernameErrorFound && !passwordErrorFound && !firstnameErrorFound && !lastnameErrorFound && !emailErrorFound && !passwordConfirmErrorFound && !emailConfirmErrorFound ) {
                        $("#registerButton").removeAttr("disabled");              
                        $("#registerButton").addClass("registerButtonEnabled").show('slow');
                }
                else {
                        $("#registerButton").attr( "disabled", "enabled" );
                        $("#registerButton").removeClass("registerButtonEnabled").show('slow');                     
                }
                    
	}
        /************************************ Last Name Check ************************************/
	else if ( className == "registerLname" ) {
            
                $(".registerLnamelError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                
		if ( $(".registerLname").val().length > 20 ) {
			$(".registerLnameError").html("<img src='images/invalid_input.png'> Too long").show('slow');
                        lastnameErrorFound = true;
                }
                else if ( usernamePattern.test( $(".registerLname").val() ) ) {
                        $(".registerLnameError").html("<img src='images/invalid_input.png'> Invalid").show('slow');
                        lastnameErrorFound = true;
                }
                else {
                        $(".registerLnameError").html("<img src='images/valid_input.png'>").show('slow');
                        lastnameErrorFound = false;
                }
                
                if ( !usernameErrorFound && !passwordErrorFound && !firstnameErrorFound && !lastnameErrorFound && !emailErrorFound && !passwordConfirmErrorFound && !emailConfirmErrorFound ) {
                        $("#registerButton").removeAttr("disabled");              
                        $("#registerButton").addClass("registerButtonEnabled").show('slow');
                }
                else {
                        $("#registerButton").attr( "disabled", "enabled" );
                        $("#registerButton").removeClass("registerButtonEnabled").show('slow');                     
                }
                    
	}
        /************************************ Email Check ************************************/
	else if ( className == "registerEmail" ) {
            
                $(".registerEmailError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
		if ( $(".registerEmail").val() == "" ) {
                        $(".registerEmailError").html("");
                        emailErrorFound = true;                      
                }            
                else if ( $(".registerEmail").val().length > 40 ) {
                        $(".registerEmailError").html("<img src='images/invalid_input.png'> Too long").show('slow');
                        emailErrorFound = true;
                }
                
                else if ( !( emailPattern.test( $(".registerEmail").val() ) ) ) {
                        $(".registerEmailError").html("<img src='images/invalid_input.png'> Invalid e-mail address").show('slow');
                        emailErrorFound = true;
                }
                
                else {
			/*checking email availability*/
			$.post("logic_includes/logic.php", { call: "check_email" , email: $(".registerEmail").val() },function(xml){
				if( xml.length > 0 ) {
					var status = parse_status(xml);
					if ( status == 0 ) {
						$(".registerEmailError").html("<img src='images/valid_input.png'>").show('slow');
                                                emailErrorFound = false;
                                                if ( $(".registerEmail").val() != $(".registerEmailConfirm").val() && $(".registerEmail").val().length > 0 && $(".registerEmailConfirm").val().length > 0 ) 
                                                        $(".registerEmailConfirmError").html("<img src='images/invalid_input.png'> Emails don't match").show('slow');
                                                              
                                                       
                                                else if ( $(".registerEmail").val() == $(".registerEmailConfirm").val() && $(".registerEmail").val().length > 0 && $(".registerEmailConfirm").val().length > 0  ) 
                                                        $(".registerEmailConfirmError").html("<img src='images/valid_input.png'>").show('slow');                                                        
                                                
                                        }
					else {
						parse_errors(xml);
						for ( var error in errorArray ) {
							if ( errorArray[error] == "EMAIL_ALREADY_EXISTS" ) {
								$(".registerEmailError").html("<img src='images/invalid_input.png'> Email taken");
                                                                if ( $(".registerEmail").val() != $(".registerEmailConfirm").val() && $(".registerEmail").val().length > 0 && $(".registerEmailConfirm").val().length > 0 )
                                                                        $(".registerEmailConfirmError").html("<img src='images/invalid_input.png'> Emails don't match").show('slow');
                                                                else if ( $(".registerEmail").val() == $(".registerEmailConfirm").val() && $(".registerEmail").val().length > 0 && $(".registerEmailConfirm").val().length > 0  )
                                                                        $(".registerEmailConfirmError").html("<img src='images/valid_input.png'>").show('slow');
                                                                emailErrorFound = true;
                                                                
                                                        }
							else if( errorArray[error] == "BAD_REQUEST" ) {
								$(".registerEmailError").html("<img src='images/invalid_input.png'> An Error has occured").show('slow');
                                                                emailErrorFound = true;
                                                        }
                                                        else {
                                                                $(".registerEmailError").html("<img src='images/invalid_input.png'>Internal Error").show('slow');	
                                                                emailErrorFound = true;
                                                        }
						}
					}
				}
				else {
					 $(".registerEmailError").html("AJAX Error");
				}
			});                
                }
                
                if ( !usernameErrorFound && !passwordErrorFound && !firstnameErrorFound && !lastnameErrorFound && !emailErrorFound && !passwordConfirmErrorFound && !emailConfirmErrorFound ) {
                        $("#registerButton").removeAttr("disabled");              
                        $("#registerButton").addClass("registerButtonEnabled");
                }
                else {
                        $("#registerButton").attr( "disabled", "enabled" );
                        $("#registerButton").removeClass("registerButtonEnabled");                     
                }
                    
	}
        /************************************ Email Confirm Check ************************************/
        else if ( className == "registerEmailConfirm" ) {
                $(".registerEmailConfirm").html("<img class='loading' src='images/loadingLogin9.gif'>");
                
                if ( $(".registerEmailConfirm").val().length > 40 ) {
                        $(".registerEmailConfirmError").html("<img src='images/invalid_input.png'> Too long").show('slow');
                        emailConfirmErrorFound = true;
                }
                else if ( $(".registerEmailConfirm").val() == "" ) {
                        $(".registerEmailConfirmError").html("");
                        emailConfirmErrorFound = true;
                }
                else if ( $(".registerEmail").val() != $(".registerEmailConfirm").val() && $(".registerEmail").val().length > 0 && $(".registerEmailConfirm").val().length > 0 ) {
                        $(".registerEmailConfirmError").html("<img src='images/invalid_input.png'> Emails don't match").show('slow');
                        emailConfirmErrorFound = true;
                }
                else if ( $(".registerEmail").val() == $(".registerEmailConfirm").val() && $(".registerEmail").val().length > 0 && $(".registerEmailConfirm").val().length > 0  ) {
                        $(".registerEmailConfirmError").html("<img src='images/valid_input.png'>").show('slow');
                        emailConfirmErrorFound = false;                        
                }
                    
                if ( !usernameErrorFound && !passwordErrorFound && !firstnameErrorFound && !lastnameErrorFound && !emailErrorFound && !passwordConfirmErrorFound && !emailConfirmErrorFound ) 
                        $("#registerButton").removeAttr("disabled");              
                else 
                        $("#registerButton").attr( "disabled", "enabled" );
                
	}
        /************************************ Password Check ************************************/
        else if ( className == "registerPassword" ) {     
            
		if ( $(".registerPassword").val().length > 40 ) {
                        $(".registerPasswordError").html("<img src='images/invalid_input.png'> Too long").show('slow');
                        passwordErrorFound = true;
                }
		else if ( $(".registerPassword").val().length < 5 ) {
                        if ( $(".registerPassword").val() != "" )
                            $(".registerPasswordError").html("<img src='images/invalid_input.png'> Too short").show('slow');
                        passwordConfirmErrorFound = true;
                }
                else if ( usernamePattern.test( $(".registerPassword").val() ) ) {
                        $(".registerPasswordError").html("<img src='images/invalid_input.png'> Invalid characters found").show('slow');    
                        passwordErrorFound = true;
                }
                else { 
                        $(".registerPasswordError").html("<img src='images/valid_input.png'>");
                        passwordErrorFound = false;
                        if ( $(".registerPassword").val() != $(".registerPasswordConfirm").val() && $(".registerPassword").val().length > 0 && $(".registerPasswordConfirm").val().length > 0 ) 
                                $(".registerPasswordConfirmError").html("<img src='images/invalid_input.png'> Passwords don't match").show('slow');
                                                       
                        else if ( $(".registerPassword").val() == $(".registerPasswordConfirm").val() && $(".registerPassword").val().length > 0 && $(".registerPasswordConfirm").val().length > 0  ) 
                                $(".registerPasswordConfirmError").html("<img src='images/valid_input.png'>").show('slow');
                                
                }
                
                if ( !usernameErrorFound && !passwordErrorFound && !firstnameErrorFound && !lastnameErrorFound && !emailErrorFound && !passwordConfirmErrorFound && !emailConfirmErrorFound ) {
                        $("#registerButton").removeAttr("disabled");              
                        $("#registerButton").addClass("registerButtonEnabled");
                }
                else {
                        $("#registerButton").attr( "disabled", "enabled" );
                        $("#registerButton").removeClass("registerButtonEnabled");                     
                }
	}
        /************************************ Password Confirm Check ************************************/
        else if ( className == "registerPasswordConfirm" ) {
            
                $(".registerPasswordConfirm").html("<img class='loading' src='images/loadingLogin9.gif'>");
		if ( $(".registerPasswordConfirm").val().length > 40 ) {
                        $(".registerPasswordConfirmError").html("<img src='images/invalid_input.png'> Too long").show('slow');
                        passwordConfirmErrorFound = true;
                }
		else if ( $(".registerPasswordConfirm").val().length < 5 ) {
                        if ( $(".registerPasswordConfirm").val() != "" )
                            $(".registerPasswordConfirmError").html("<img src='images/invalid_input.png'> Too short").show('slow');
                        passwordConfirmErrorFound = true;
                }		
                else if ( $(".registerPassword").val() != $(".registerPasswordConfirm").val() && $(".registerPassword").val().length > 0 && $(".registerPasswordConfirm").val().length > 0 ) {
                        $(".registerPasswordConfirmError").html("<img src='images/invalid_input.png'> Passwords don't match").show('slow');
                        passwordConfirmErrorFound = true;
                }
                else if ( $(".registerPassword").val() == $(".registerPasswordConfirm").val() && $(".registerPassword").val().length > 0 && $(".registerPassword").val().length > 0  ) {
                        $(".registerPasswordConfirmError").html("<img src='images/valid_input.png'>").show('slow');
                        passwordConfirmErrorFound = false;
                }
                
                if ( !usernameErrorFound && !passwordErrorFound && !firstnameErrorFound && !lastnameErrorFound && !emailErrorFound && !passwordConfirmErrorFound && !emailConfirmErrorFound ) {
                        $("#registerButton").removeAttr("disabled");              
                        $("#registerButton").addClass("registerButtonEnabled");
                }
                else {
                        $("#registerButton").attr( "disabled", "enabled" );
                        $("#registerButton").removeClass("registerButtonEnabled");                     
                }
                
	}
		
}

function delayedRedirect(){
        window.location = "./login.php";
}

        
function register(){

	usern = $(".registerUsername").val();
	pass = $(".registerPassword").val();
	mail = $(".registerEmail").val();
        fName = $(".registerFname").val();
        lName = $(".registerLname").val();

	$.post( "logic_includes/logic.php", { call: "register" , username: usern , password: pass , email: mail, firstname: fName, lastname: lName },function(xml){
            
                        $(".recaptchaError").html("");
			if ( xml.length > 0 ){
				var status = parse_status(xml);

				if ( status == 0 ) {
                                    
					$("#registrationResult").html( "<br><p style=\"font-weight: bold;\">Registration Successfull!<br>Activation e-mail sent to "+mail+"</p>");
                                        setTimeout('delayedRedirect()', 3000);
                                        
				}
				else {
					$("#registrationResult").html( "<br><p style=\"font-weight: bold; color: red; \">An error has occured</p>");
				}
			}
			else {
				$("#registrationResult").html( "<br><p style=\"font-weight: bold; color: red; \">AJAX error</p>");
			}
                        
        });
        
}

/************************************** Validate Captcha Function*******************************************/
function validateCaptcha()
{
            challengeField = $("input#recaptcha_challenge_field").val();
            responseField = $("input#recaptcha_response_field").val();
            
            
            $(".recaptchaError").html("<img class='loading' src='images/loadingLogin9.gif'>");
            $.post( "recaptcha.php", { recaptcha_challenge_field: challengeField, recaptcha_response_field: responseField }, function(html) {           
                    if ( html.replace(/^\s+|\s+$/, '') == "success" ) {
                            register();
                            
                    }
                    else {
                            $(".recaptchaError").html("<p style=\"color: red;\">Your captcha is incorrect. Please try again</p>");                          
                            Recaptcha.reload();
                    }       
            });
}


$(function() {
	
	$("#registerButton").bind( "click", validateCaptcha );
	$("#register input").bind( "blur" ,checkValidity );
        
});
