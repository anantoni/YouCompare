<?php

    /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/

            session_start();
            include_once( "../db_includes/user.php" );
            include_once( "../db_includes/DB_DEFINES.php" );
            include_once( "logic_functions.php" );
            include_once( "LOGIC_DEFINES.php" );

            $request_error_message = "";
            $login_error_code = 0;
            $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            $username = "";
            $password = "";

            $name_pattern = '/[^A-Za-z0-9-_@]/';                                 //regular expression gia to username validation
            $password_pattern = '/[^A-Za-z0-9-_@]/';                               //regular expression gia to password validation

            if ( isset( $_POST["username"] ) && isset( $_POST["password"] ) ) {
                    $username = sanitize_str( $_POST["username"] );
                    $password = sanitize_str( $_POST["password"] );
            }
            else
                     $request_error_message = "BAD_REQUEST";                        //Error: Bad Request


            if ( $username == "" || $password == "" )                                                                         //Error: Not all forms filled
                    $login_error_code = "NOT_FILLED";

            /************************************************* Username Error Checking ***************************************************/
            if ( preg_match( $name_pattern, $username ) == true )                                                             //Error: Invalid Username
                    $login_error_code = 1;
            else if ( strlen( $username ) > 20 )                                                                              //Error: Username too long
                    $login_error_code = 1;
            else if ( strlen( $username ) < 3 )                                                                               //Error: Username too short
                    $login_error_code = 1;

            /****************************************************** Password Error Checking *********************************************/
            if ( preg_match( $password_pattern, $password ) == true )                                                         //Error: Invalid Password
                     $login_error_code = 1;
            else if ( strlen( $password ) < 3 )                                                                               //Error: Password too short
                     $login_error_code = 1;



            if ( $login_error_code == 0 ) {

                        $user = new user( $username, -1 );
                        if( $user->get_errno()!=DB_OK ) {

                                $xml_output .= "<login><status>FAIL</status><error>MYSQL_ERROR</error></login>";
                                die("$xml_output");

                        }        
                        $user->login( $password );       
                        $login_error_code=$user->get_errno();        //Kalw th login function tou user object


                        if ( $login_error_code == LOGGED_IN ) {                                                         //Successfull log in

                                $xml_output .= "<login><status>SUCCESS</status><user>".$username."</user></login>";
                                $_SESSION['username'] = $username;
                                $_SESSION['privileges'] = $user->get_privileges();

                                die("$xml_output");
                        }
                        else if ( $login_error_code == WRONG_USERNAME_OR_PASSWORD ) 
                                $xml_output .= "<login><status>FAIL</status><error>AUTHENTICATION_FAILED</error></login>";                  //Authentication Failed

                        else if ( $login_error_code == UNVERIFIED_USER )
                                $xml_output .= "<login><status>FAIL</status><error>UNVERIFIED_USER</error></login>";                       //Authentication Failed

                        else if ( $register_error_code == MYSQL_CONNECT_ERROR )                                                       //MYSQL_CONNECT_ERROR
                                $xml_output .= "<login><status>FAIL</status><error>MYSQL_CONNECT_ERROR</error></login>";

                        else                                                                                                          //MYSQL_ERROR
                                $xml_output .= "<login><status>FAIL</status><error>MYSQL_ERROR</error></login>";

                        die("$xml_output");
            }
            else {

                        if ( $request_error_message != "" )
                                $xml_output .= "<login><status>FAIL</status><error>BAD_REQUEST</error></login>";

                        else
                                $xml_output .= "<login><status>FAIL</status><error>AUTHENTICATION_FAILED</error></login>";                 //Authentication Failed

                        die("$xml_output");

            }
?>
