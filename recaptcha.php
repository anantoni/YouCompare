<?php

    require_once('recaptchalib.php');
    $publickey = "6LfwncQSAAAAAOQDKkfsa-JJdY5lq8tR0avhN5Ki"; // you got this from the signup page
    $privatekey = "6LfwncQSAAAAAJbHzTZSoNXbXiU683U6qVUS6OKZ";

    $resp = recaptcha_check_answer ($privatekey,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);
    
   
    if ( $resp->is_valid ) {
         // Your code here to handle a successful verification
	 die ("success");
    } else {
         // What happens when the CAPTCHA was entered incorrectly
         die ( $_POST["recaptcha_challenge_field"]."The reCAPTCHA wasn't entered correctly. Go back and try it again." ."(reCAPTCHA said: " . $resp->error . ")" . $_POST["recaptcha_response_field"]   );
    }
?>
