var emailPattern = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
var passwordPattern = /[^A-Za-z0-9-_]/;
var usernamePattern = /[^A-Za-z0-9-_]/;

var emailErrorFound = true;
var firstnameErrorFound = true;
var lastnameErrorFound = true;
var passwordErrorFound = true;
var passwordConfirmErrorFound = true;

function checkEmail() {
                
            $("#new_mail_error").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
            if ( $("#new_mail").val() == "" ) {
                    $("#new_mail_error").html("");
                    emailErrorFound = true;                      
            }            
            else if ( $("#new_mail").val().length > 40 ) {
                    $("#new_mail_error").html("<img src='images/invalid_input.png'> Too long").show('slow');
                    emailErrorFound = true;
            }

            else if ( !( emailPattern.test( $("#new_mail").val() ) ) ) {
                    $("#new_mail_error").html("<img src='images/invalid_input.png'> Invalid e-mail address").show('slow');
                    emailErrorFound = true;
            }

            else {
                    /*checking email availability*/
                    $.post("logic_includes/logic.php", { call: "check_email" , email: $("#new_mail").val() },function(xml){
                            if( xml.length > 0 ) {

                                    var status = parse_status(xml);
                                    if ( status == 0 ) {
                                            $("#new_mail_error").html("<img src='images/valid_input.png'>").show('slow');
                                            emailErrorFound = false;

                                    }
                                    else {
                                            parse_errors(xml);
                                            for ( var error in errorArray ) {
                                                
                                                    if ( errorArray[error] == "EMAIL_ALREADY_EXISTS" ) 
                                                            $("#new_mail_error").html("<img src='images/invalid_input.png'> Email taken");

                                                    else if( errorArray[error] == "BAD_REQUEST" ) 
                                                            $("#new_mail_error").html("<img src='images/invalid_input.png'> An Error has occured").show('slow');

                                                    else 
                                                            $("#new_mail_error").html("<img src='images/invalid_input.png'>Internal Error").show('slow');	

                                                    emailErrorFound = true;
                                                    
                                            }
                                    }
                            }
                            else 
                                     $("#new_mail_error").html("AJAX Error");
                            
                    });                
            }

            if ( !emailErrorFound ) {
                    $("#change_email_button").removeAttr( "disabled" );              
                    $("#change_email_button").addClass( "ButtonEnabled" ).show('show');
            }
            else {
                    $("#change_email_button").attr( "disabled", "enabled" );
                    $("#change_email_button").removeClass( "ButtonEnabled" ).show('slow');                     
            }

}

function checkFirstName() {

            $("#new_first_name_error").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');

            if ( $("#new_first_name").val().length > 20 ) {
                    $("#new_first_name_error").html("<img src='images/invalid_input.png'> Too long").show('slow');
                    firstnameErrorFound = true;
            }
            else if ( usernamePattern.test( $("#new_first_name").val() ) ) {
                    $("#new_first_name_error").html("<img src='images/invalid_input.png'> Invalid").show('slow');
                    firstnameErrorFound = true;
            }
            else {
                    $("#new_first_name_error").html("<img src='images/valid_input.png'>").show('slow');
                    firstnameErrorFound = false;
            }

            if ( !firstnameErrorFound ) {
                    $("#change_first_name_button").removeAttr("disabled");              
                    $("#change_first_name_button").addClass("ButtonEnabled").show('slow');
            }
            else {
                    $("#change_first_name_button").attr( "disabled", "enabled" );
                    $("#change_first_name_button").removeClass("ButtonEnabled").show('slow');  
            }

}

function checkLastName() {

            $("#new_last_name_error").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');

            if ( $("#new_last_name").val().length > 20 ) {
                    $("#new_last_name_error").html("<img src='images/invalid_input.png'> Too long").show('slow');
                    lastnameErrorFound = true;
            }
            else if ( usernamePattern.test( $("#new_last_name").val() ) ) {
                    $("#new_last_name_error").html("<img src='images/invalid_input.png'> Invalid").show('slow');
                    lastnameErrorFound = true;
            }
            else {
                    $("#new_last_name_error").html("<img src='images/valid_input.png'>").show('slow');
                    lastnameErrorFound = false;
            }

            if ( !lastnameErrorFound ) {
                    $("#change_last_name_button").removeAttr("disabled");              
                    $("#change_last_name_button").addClass("ButtonEnabled").show('slow');
            }
            else {
                    $("#change_last_name_button").attr( "disabled", "enabled" );
                    $("#change_last_name_button").removeClass("ButtonEnabled").show('slow');  
            }

}

function checkPassword() {

           if ( $("#new_password").val().length > 40 ) {

                    $("#new_password_error").html("<img src='images/invalid_input.png'> Too long").show('slow');
                    passwordErrorFound = true;
            }
            else if ( $("#new_password").val().length < 5 ) {
                    if ( $("#new_password").val() != "" )
                        $("#new_password_error").html("<img src='images/invalid_input.png'> Too short").show('slow');
                    passwordConfirmErrorFound = true;
            }
            else if ( usernamePattern.test( $("#new_password").val() ) ) {
                    $("#new_password_error").html("<img src='images/invalid_input.png'> Invalid characters found").show('slow');    
                    passwordErrorFound = true;
            }
            else { 
                    $("#new_password_error").html("<img src='images/valid_input.png'>");
                    passwordErrorFound = false;
                    if ( $("#new_password").val() != $("#confirm_new_password").val() && $("#new_password").val().length > 0 && $("#confirm_new_password").val().length > 0 ) 
                            $("#confirm_new_password_error").html("<img src='images/invalid_input.png'> Passwords don't match").show('slow');

                    else if ( $("#new_password").val() == $("#confirm_new_password").val() && $("#new_password").val().length > 0 && $("#confirm_new_password").val().length > 0 ) 
                            $("#confirm_new_password_error").html("<img src='images/valid_input.png'>").show('slow');

            }

            if ( !passwordErrorFound && !passwordConfirmErrorFound ) {
                    $("#change_password_button").removeAttr("disabled");              
                    $("#change_password_button").addClass("ButtonEnabled");
            }
            else {
                    $("#change_password_button").attr( "disabled", "enabled" );
                    $("#change_password_button").removeClass("ButtonEnabled");                     
            }

}

function checkConfirmPassword() {

            $("#confirm_new_password").html("<img class='loading' src='images/loadingLogin9.gif'>");
            if ( $("#confirm_new_password").val().length > 40 ) {
                    $("#confirm_new_password_error").html("<img src='images/invalid_input.png'> Too long").show('slow');
                    passwordConfirmErrorFound = true;
            }
            else if ( $("#confirm_new_password").val().length < 5 ) {
                    if ( $("#confirm_new_password").val() != "" )
                        $("#confirm_new_password_error").html("<img src='images/invalid_input.png'> Too short").show('slow');
                    passwordConfirmErrorFound = true;
            }		
            else if ( $("#new_password").val() != $("#confirm_new_password").val() && $("#new_password").val().length > 0 && $("#confirm_new_password").val().length > 0 ) {
                    $("#confirm_new_password_error").html("<img src='images/invalid_input.png'> Passwords don't match").show('slow');
                    passwordConfirmErrorFound = true;
            }
            else if ( $("#new_password").val() == $("#confirm_new_password").val() && $("#new_password").val().length > 0 && $("#confirm_new_password").val().length > 0  ) {
                    $("#confirm_new_password_error").html("<img src='images/valid_input.png'>").show('slow');
                    passwordConfirmErrorFound = false;
            }

            if (  !passwordConfirmErrorFound && !passwordErrorFound ) {
                    $("#change_password_button").removeAttr("disabled");              
                    $("#change_password_button").addClass("ButtonEnabled");
            }
            else {
                    $("#change_password_button").attr( "disabled", "enabled" );
                    $("#change_password_utton").removeClass("ButtonEnabled");                     
            }
}

function changeEmail() {
            
            $.post("logic_includes/logic.php", { call: "change_email" , email: $("#new_mail").val() },function(xml) {
                           
                            if( xml.length > 0 ) {

                                    var status = parse_status(xml);

                                    if ( status == 0 ) 
                                            $("change_mail_result").html( "<span style=\"color: green;\">E-mail changed successfully</span>" );

                                    else 
                                            $("change_mail_result").html( "<span style=\"color: red;\">An error has occured</span>" );
                                    
                            }
                            else 
                                     $("change_mail_result").html( "AJAX Error" );
                            
            });                
    
}

function changePassword() {
    
            $.post("logic_includes/logic.php", { call: "change_password" , old_password: $("#old_password").val(), new_password: $("#new_password").val() },function(xml) {
                
                            if( xml.length > 0 ) {
                                  
                                    var status = parse_status(xml);

                                    if ( status == 0 ) 
                                            $("#change_password_result").html( "<span style=\"color: green;\">Password changed successfully</span>" );
                                        
                                    else 
                                            $("#change_password_result").html( "<span style=\"color: red;\">An error has occured</span>" );
                            }
                            else 
                                     $("#change_password_result").html( "AJAX Error" );
                            
                    });                
    
}

function changeFirstName() {
    
            $.post("logic_includes/logic.php", { call: "change_first_name" , firstname: $("#new_first_name").val() },function(xml) {
                
                            if( xml.length > 0 ) {
                                 
                                    var status = parse_status(xml);

                                    if ( status == 0 ) 
                                            $("#change_first_name_result").html( "<span style=\"color: green;\">First name changed successfully</span>" );
                                       
                                    else 
                                            $("#change_first_name_result").html( "<span style=\"color: red;\">An error has occured</span>" );
                            }
                            else 
                                     $("#change_first_name_result").html("AJAX Error");
                            
            });                
    
}

function changeLastName() {
    
            $.post("logic_includes/logic.php", { call: "change_last_name" , lastname: $("#new_last_name").val() },function(xml) {
                          
                            if( xml.length > 0 ) {

                                    var status = parse_status(xml);

                                    if ( status == 0 ) 
                                            $("#change_last_name_result").html( "<span style=\"color: green;\">Surname changed successfully</span>" );
                                       
                                    else 
                                            $("#change_last_name_result").html("<span style=\"color: red;\">An error has occured</span>" );
                            }
                            else 
                                     $("#change_last_name_result").html("AJAX Error");
                            
            });                
    
}

$(document).ready( function() {
            
            $("#change_email_button").attr("disabled", "enabled");
            $("#change_password_button").attr("disabled", "enabled");
            $("#change_first_name_button").attr("disabled", "enabled");
            $("#change_last_name_button").attr("disabled", "enabled");
            
            $("#change_email_button").bind( "click", changeEmail );
            $("#change_password_button").bind( "click", changePassword );
            $("#change_first_name_button").bind( "click", changeFirstName );
            $("#change_last_name_button").bind( "click", changeLastName );
            
            $("#new_mail").bind( "blur", checkEmail );
            $("#new_first_name").bind( "blur", checkFirstName );
            $("#new_last_name").bind( "blur", checkLastName );
            $("#old_password").bind( "blur", checkPassword ); 
            $("#new_password").bind( "blur", checkPassword );
            $("#confirm_new_password").bind( "blur", checkConfirmPassword );            
            
            
            $(".option").bind( "click", function( event ) {
                    
                    event.stopPropagation();
                    var id = $(this).attr('id');
                    
                    if ( id == "change_password_option" ) {
                            
                            $("#general_info").css( "display", "none");
                            $("#change_email").css( "display", "none" );
                            $("#change_first_name").css( "display", "none" );
                            $("#change_last_name").css( "display", "none" );
                            $("#change_password").css( "display", "block" );
                        
                    }
                    else if ( id == "change_email_option" ) {
                        
                            $("#general_info").css( "display", "none");
                            $("#change_first_name").css( "display", "none" );
                            $("#change_last_name").css( "display", "none" );
                            $("#change_password").css( "display", "none" );
                            $("#change_email").css( "display", "block" );
                        
                    }
                    else if ( id == "change_first_name_option" ) {
                        
                            $("#general_info").css( "display", "none");
                            $("#change_last_name").css( "display", "none" );
                            $("#change_password").css( "display", "none" );
                            $("#change_email").css( "display", "none" );
                            $("#change_first_name").css( "display", "block" );
                        
                    }
                    else {
                        
                            $("#general_info").css( "display", "none");
                            $("#change_first_name").css( "display", "none" );
                            $("#change_password").css( "display", "none" );
                            $("#change_email").css( "display", "none" );
                            $("#change_last_name").css( "display", "block" );

                    }
                                    
            })    
            
            $("#change_email_button").ajaxSuccess(function(e, xhr, settings) {
                           
                            if ( !emailErrorFound ) {
                                    $("#change_email_button").removeAttr( "disabled" );              
                                    $("#change_email_button").addClass( "ButtonEnabled" ).show('show');
                            }
                            else {
                                    $("#change_email_button").attr( "disabled", "enabled" );
                                    $("#change_email_button").removeClass( "ButtonEnabled" ).show('slow');                     
                            }
            });
    
})