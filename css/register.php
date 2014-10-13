<?php
        class registerPage { 
                    private $content;

                    public function create_content() {
                                require_once('recaptchalib.php');
                                $publickey = "6LfwncQSAAAAAOQDKkfsa-JJdY5lq8tR0avhN5Ki"; // you got this from the signup page
                                $privatekey = "6LfwncQSAAAAAJbHzTZSoNXbXiU683U6qVUS6OKZ";
                                
                                $this->content = "<div id=\"mainContainer\" align=\"center\" >
                                                   <div id=\"register\" >
                                                                <h2 style=\"font-size: 30px; font-weight: bold;\"> Create YouCompare account </h2>
                                                                <hr>
                                                                <br>
                                                                <div style=\"display:block ; width: 600px; text-align: left;\">
                                                                    <p>Already have an account? <a href=\"login.php\"> Log in </a>.</p> 
                                                                    <p>Please fill all the following mandatory fields with your information</p>     
                                                                </div>
                                                                <br>

                                                                
                                                                                <label> Username: </label>                         
                                                                                <input class=\"registerUsername\" type=\"text\" placeholder=\"e.g MyUsername56\" value=\"\" />
                                                                                <div class=\"registerUsernameError\"> </div>
                                                                        
                                                                                <label> First name (optional): </label>  
                                                                                <input class=\"registerFname\" type=\"text\" placeholder=\"e.g John\" value=\"\" /> 
                                                                                <div class=\"registerFnameError\"> </div>	
                                                                       
                                                                                <label> Last name (optional): </label> </td> 
                                                                                <input class=\"registerLname\" type=\"text\" placeholder=\"e.g Rambo\" value=\"\" />  </td>
                                                                                <div class=\"registerLnameError\"> </div>
                                                                        
                                                                                <label> E-mail: </label> 
                                                                                <input class=\"registerEmail\" type=\"text\" placeholder=\"e.g mymail@gmail.com\" value=\"\" />
                                                                                <div class=\"registerEmailError\"> </div>
                                                                        
                                                                                <label> <label> Retype e-mail: </label> 
                                                                                <input class=\"registerEmailConfirm\" type=\"text\" value=\"\" />
                                                                                <div class=\"registerEmailConfirmError\"> </div>
                                                                       
                                                                                <label> Password: </label> 
                                                                                <input class=\"registerPassword\"  type=\"password\" placeholder=\"e.g 89My17Password13\"value=\"\" />
                                                                                <div class=\"registerPasswordError\"> </div>
                                                                        
                                                                                <label> Retype password: </label> 
                                                                                <input class=\"registerPasswordConfirm\" type=\"password\" value=\"\" /> 
                                                                                <div class=\"registerPasswordConfirmError\"> </div>
                                                                        
                                                                        <br>    
                                                                        <div style=\"display:block ; width: 600px; text-align: left;\">Please enter the following words, without any spaces:</div>
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
