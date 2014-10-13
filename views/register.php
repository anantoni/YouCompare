<?php
        /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/

        class registerPage { 
                    private $content;

                    public function create_content() {
                        
                                require_once('recaptchalib.php');
                                $publickey = "6LfwncQSAAAAAOQDKkfsa-JJdY5lq8tR0avhN5Ki"; // you got this from the signup page
                                $privatekey = "6LfwncQSAAAAAJbHzTZSoNXbXiU683U6qVUS6OKZ";
                                
                                $this->content = "<div id=\"mainContainer\" align=\"center\" >
                                                     <div id=\"register_panel\" align=\"center\">  
                                                        <h2 style=\"font-size: 30px; font-weight: bold;\"> Create YouCompare account </h2>
                                                        <hr>
                                                            <div id=\"register\">
                                                                
                                                                <br>
                                                                <div style=\"display:block ; width: 600px; text-align: left;\">
                                                                    <p>Already have an account? <a href=\"login.php\"> Log in </a>.</p> 
                                                                    <p>Please fill all the following mandatory fields with your information</p>     
                                                                </div>
                                                                <br>
                                                             
                                                                <label> Username: </label>                         
                                                                <input class=\"registerUsername\" type=\"text\" title=\"please specify your desired username\"  placeholder=\"e.g MyUsername56\" value=\"\" />
                                                                <div class=\"registerUsernameError\"> </div>
                                                                <br>
                                                                <label> First name (optional): </label>  
                                                                <input class=\"registerFname\" type=\"text\" title=\"please specify your first name\" placeholder=\"e.g John\" value=\"\" /> 
                                                                <div class=\"registerFnameError\"> </div>	
                                                                <br>
                                                                <label> Last name (optional): </label>  
                                                                <input class=\"registerLname\" type=\"text\" title=\"please specify your last name\" placeholder=\"e.g Rambo\" value=\"\" /> 
                                                                <div class=\"registerLnameError\"> </div>
                                                                <br>
                                                                <label> E-mail: </label> 
                                                                <input class=\"registerEmail\" type=\"text\" title=\"please specify your email account you want to register with\" placeholder=\"e.g mymail@gmail.com\" value=\"\" />
                                                                <div class=\"registerEmailError\"> </div>
                                                                <br>
                                                                <label> Retype e-mail: </label> 
                                                                <input class=\"registerEmailConfirm\"  type=\"text\" title=\"please re-type your e-mail address\" value=\"\" />
                                                                <div class=\"registerEmailConfirmError\"> </div>
                                                                <br>
                                                                <label> Password: </label> 
                                                                <input class=\"registerPassword\" type=\"password\" title=\"please specify a password for your account (we suggest using both uppercase letters and numbers)\" placeholder=\"e.g 89My17Password13\"value=\"\" />
                                                                <div class=\"registerPasswordError\"> </div>
                                                                <br>
                                                                <label> Retype password: </label> 
                                                                <input class=\"registerPasswordConfirm\" type=\"password\" title=\"please re-type your password\" value=\"\" /> 
                                                                <div class=\"registerPasswordConfirmError\"> </div>
                                                                
                                                               
                                                                <div style=\"display:block ; width: 600px; text-align: left;\"><br>Please enter the following words, <b><u>without</u></b> any spaces: </div>
                                                </div>
                                                <br>    
                                                <br>".recaptcha_get_html($publickey)."<p class=\"recaptchaError\"> <p>
                                                <br>
                                                <button id=\"registerButton\" class=\"registerButtonDisabled\"> <span> Register </span> </button>
                                                <br>
                                                <div id=\"registrationResult\"> </div>
                                                <br><br>
                                                </div>
                                </div>";
                    }
                    
                    public function get_content() {
                                return $this->content;
                    }
        }
?>