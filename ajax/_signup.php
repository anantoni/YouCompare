<?php
        include_once( "../db_includes/user.php" );
        include_once( "../db_includes/DB_DEFINES.php" );
        include_once( "logic_functions.php" );
        include_once( "LOGIC_DEFINES.php" );

        $request_error_message = "";
        $form_error_message = "";
        $username_error_message = "";
        $password_error_message = "";
        $email_error_message = "";
        $firstname_error_message = "";
        $lastname_error_message = "";
        $register_error_message = "";
        $xml_output = "";
        $username = "";
        $password = "";
        $email = "";
        $firstname = "";
        $lastname = "";


        $profile = new profile_info();                                                              //dhmiourgw to profile
        $name_pattern = '/[^A-Za-z0-9-_]/';                                                         //regular expression gia to username validation
        $password_pattern = '/[^A-Za-z0-9]/';                                                       //regular expression gia to password validation
        $email_pattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' .'(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';  //regular expression gia to e-mail validation

        if ( isset( $_POST["username"] ) && isset( $_POST["password"] ) && isset( $_POST["email"] )  ) {
            
                    $username = sanitize_str( $_POST["username"] );
                    $password = sanitize_str( $_POST["password"] );
                    $email = sanitize_str( $_POST["email"] );

                    if ( isset( $_POST["firstname"] ) )
                        $firstname = sanitize_str( $_POST["firstname"] );
                    else
                        $firstname = "";

                    if ( isset( $_POST["lastname" ] ) )
                        $lastname = sanitize_str( $_POST["lastname"] );
                    else
                        $lastname = "";
        }
        else                                                                                                             //Error: Bad Request
                    $request_error_message = "BAD_REQUEST";



        if ( $username == "" || $password == "" || $email == "" )     //Error: Not all forms filled
                $form_error_message = "NOT_ALL_FORMS_FILLED";

        /*************************************************** Username Error Checking ************************************************/
        if ( preg_match( $name_pattern, $username ) == true )                                                             //Error: Invalid Username
                $username_error_message = "USERNAME_INVALID";
        
       else if ( strlen( $username ) > 20 )                                                                             //Error: Username too long
                $username_error_message = "USERNAME_TOO_LONG";
        
        else if ( strlen( $username ) < 5 )                                                                              //Error: Username too short
                $username_error_message = "USERNAME_TOO_SHORT";


        /****************************************************** Password Error Checking *********************************************/
        if ( preg_match( $password_pattern, $password ) == true )                                                        //Error: Password Invalid
                 $password_error_message = "PASSWORD_INVALID";
        
        else if ( strlen( $password ) < 5 )                                                                              //Error: Password too short
                 $password_error_message = "PASSWORD_TOO_SHORT";

        /**************************************************** E-mail Error Checking **********************************************/
        if ( preg_match( $email_pattern, $email ) == false )                                                             //Error: E-mail Invalid
                 $email_error_message = "EMAIL_INVALID";
        
        else if ( strlen( $email ) > 40 )                                                                                //Error: E-mail too long
                 $email_error_message = "EMAIL_TOO_LONG";
        
        else if ( strlen( $email ) < 7 )
                 $email_error_message = "EMAIL_TOO_SHORT";

        /*************************************************** Firstname Error Checking *******************************************/
        if ( $firstname != "" ) {
                 if ( preg_match( $name_pattern, $firstname ) == true )                                                           //Error: Firstname Invalid
                        $firstname_error_message = "FIRSTNAME_INVALID";
                 
                 else if ( strlen( $firstname ) > 20 )                                                                            //Error: Firstname too long
                        $firstname_error_message = "FIRSTNAME_TOO_LONG";
        }

        /*************************************************** Firstname Error Checking *******************************************/
         if ( $lastname != "" ) {
             
                    if ( preg_match( $name_pattern, $lastname ) == true )                                                            //Error: Lastname Invalid
                            $lastname_error_message = "LASTNAME_INVALID";
                    
                    else if ( strlen( $lastname ) > 20 )                                                                             //Error: Lastname too long
                            $lastname_error_message = "LASTNAME_TOO_LONG";
         }

        /******************* Gemizw to profile_info profile ************************/
        if ( $username_error_message == "" && $password_error_message == "" && $email_error_message == "" && $firstname_error_message == "" && $lastname_error_message == "" ) {
            
                    $profile->username = $username;
                    $profile->password = $password;
                    $profile->email = $email;
                    $profile->name = $firstname;
                    $profile->surname = $lastname;

                    $user = new user( $username, -1 );
                    $user->register( $profile, $password );                                                                       //Kalw th register function tou user object
                    $register_error_code = $user->get_errno();


                    $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";

                    if ( $register_error_code == DB_OK )                                                                          //Ean epestrepse DB_OK
                        $xml_output .= "<register><status>SUCCESS</status></register>";        
                    else if ( $register_error_code == USERNAME_ALLREADY_EXISTS )                                                  //Ean uparxei hdh to Username
                        $xml_output .= "<register><status>FAIL</status><error>USERNAME_ALREADY_EXISTS</error></register>";
                    else if ( $register_error_code == EMAIL_ALLREADY_EXISTS )                                                     //Ean uparxei hdh to E-Mail
                        $xml_output .= "<register><status>FAIL</status><error>EMAIL_ALREADY_EXISTS</error></register>";
                    else                                                                                                          //Alliws MySQL Error
                        $xml_output .= "<register><status>FAIL</status><error>MYSQL_ERROR</error></register>";
                    
                    die("$xml_output");

        }
        else {


                    $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
                    $xml_output .= "<register><status>FAIL</status>";

                    if ( $request_error_message != "" )
                        $xml_output .= "<error>".$request_error_message."</error>";
                    if ( $form_error_message != "" )
                        $xml_output .= "<error>".$form_error_message."</error>";
                    if ( $username_error_message != "" )
                        $xml_output .= "<error>".$username_error_message."</error>";
                    if ( $password_error_message != "" )
                        $xml_output .= "<error>".$password_error_message."</error>";
                    if ( $email_error_message != "" )
                        $xml_output .= "<error>".$email_error_message."</error>";
                    if ( $firstname_error_message != "" )
                        $xml_output .= "<error>".$firstname_error_message."</error>";
                    if ( $lastname_error_message != "" )
                        $xml_output .= "<error>".$lastname_error_message."</error>";
                    $xml_output .= "</register>";
                    
                    die("$xml_output");

        }
    ?>
