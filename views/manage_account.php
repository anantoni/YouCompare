<?php
include_once( "db_includes/user.php");;
 
class manageAccountPage {
    
            private $content;

            public function create_content() {
                
                    session_start();
                    if ( !isset( $_SESSION['username'] ) ) 
                                header("Location: ./index.php");
                    
                    $this->content = "<div id=\"mainContainer\" align=\"center\">
                                        <div id=\"manageAccount\" align=\"left\">
                                            <div id=\"manageAccountHeader\" align=\"center\">
                                                    <h2 style=\"font-size: 30px; font-weight: bold;\">Change your account information</h2>
                                                    <hr>
                                            </div>
                                            <div id=\"optionPanel\">
                                                    <ul>
                                                            <li id=\"change_password_option\" class=\"option\"><span id=\"change_password_option\" class=\"option\"> Change Password </span></li>
                                                            <li id=\"change_email_option\" class=\"option\"><span id=\"change_email_option\" class=\"option\"> Change E-mail </span></li>
                                                            <li id=\"change_first_name_option\"  class=\"option\"><span id=\"change_first_name_option\"  class=\"option\"> Change First Name </span></li>
                                                            <li id=\"change_last_name_option\" class=\"option\"><span id=\"change_last_name_option\" class=\"option\"> Change Last Name </span></li>
                                                    </ul>
                                             </div> 
                                             
                                             <div id=\"changeForms\" align=\"center\">
                                                        <div id=\"general_info\" class=\"Form\">
                                                        Welcome ".$_SESSION['username'].",<br>
                                                        On this page you can change your account information

                                                        </div>
                                                        <div id=\"change_email\" class=\"Form\">
                                                        
                                                            <label>Please type your new e-mail:</label>
                                                            <input id=\"new_mail\" class=\"manage_account_input\" type=\"text\"/>
                                                            <span id=\"new_mail_error\"> </span>
                                                            <br>
                                                            
                                                            <div class=\"buttonSection\" align=\"center\"><button id=\"change_email_button\" class=\"Button\">Confirm</button></div>
                                                            <br>
                                                            <div id=\"change_mail_result\" align=\"center\"> </div>

                                                        </div>
                                                        <div id=\"change_password\" class=\"Form\">
                                                        
                                                            <label>Please type your old password:</label>
                                                            <input id=\"old_password\" class=\"manage_account_input\" type=\"password\"/>
                                                            <span id=\"old_password_error\"> </span>
                                                            <br>

                                                            <label>Please type your new password:</label>
                                                            <input id=\"new_password\" class=\"manage_account_input\" type=\"password\"/>
                                                            <span id=\"new_password_error\"> </span>
                                                            <br>

                                                            <label>Please re-type your new password:</label>
                                                            <input id=\"confirm_new_password\" class=\"manage_account_input\" type=\"password\"/>
                                                            <span id=\"confirm_new_password_error\"> </span>
                                                            <br>
                                                            <div class=\"buttonSection\" align=\"center\"><button id=\"change_password_button\" class=\"Button\">Confirm</button></div>                                                                                                    
                                                            <br>
                                                            <div id=\"change_password_result\" align=\"center\"> </div>
                                                            
                                                        </div>
                                                        <div id=\"change_first_name\" class=\"Form\">
                                                        
                                                            <label>Please type your new first name:</label>
                                                            <input id=\"new_first_name\" class=\"manage_account_input\" type=\"text\"/>
                                                            <span id=\"new_first_name_error\"> </span>
                                                            <br>
                                                            
                                                            <div class=\"buttonSection\" align=\"center\"><button id=\"change_first_name_button\" class=\"Button\">Confirm</button></div>                                                                                                    
                                                            <br>
                                                            <div id=\"change_first_name_result\" align=\"center\"> </div>
                                                            
                                                        </div>
                                                        <div id=\"change_last_name\" class=\"Form\">
                                                        
                                                            <label>Please type your new last name:</label>
                                                            <input id=\"new_last_name\" class=\"manage_account_input\" type=\"text\"/>
                                                            <span id=\"new_last_name_error\"> </span>
                                                            <br>

                                                            <div class=\"buttonSection\" align=\"center\"><button id=\"change_last_name_button\" class=\"Button\">Confirm</button></div>                                                                                                    
                                                            <br>
                                                            <div id=\"change_last_name_result\" align=\"center\"> </div>
                                                            
                                                        </div>
                                             </div>
                                         </div>
                                </div>";
            }
            
            public function get_content(){
                    return $this->content;
            }
}

?>
