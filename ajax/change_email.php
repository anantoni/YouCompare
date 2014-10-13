<?php
             /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/

            include_once( "../db_includes/DB_DEFINES.php" );
            include_once( "../db_includes/user.php" );
            include_once( "logic_functions.php" );
            include_once( "LOGIC_DEFINES.php" );

            $email = "";
            $user = NULL;
            $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";

            if ( isset( $_REQUEST["email"] ) ) {
                
                    $email = sanitize_str( $_REQUEST["email"] );
                    session_start();
                    
                    if ( isset( $_SESSION["username"] ) ) {
                            
                            $user = new user( $_SESSION["username"], LOGGED_IN );
                            if ( $user->get_errno() == DB_OK ) {
                                
                                    $user->set_email( $email );
                                    if ( $user->get_errno() == DB_OK ) {
                                            
                                            $xml_output .= "<change_email><status>SUCCESS</status></change_email>";
                                            die( "$xml_output" );
                                        
                                    }
                                    else {
                                            $xml_output .= "<change_email><status>FAIL</status><error>MYSQL_ERROR</error></change_email>";
                                            die( "$xml_output" );
                                            
                                    }
                            }
                            else {
                                    $xml_output .= "<change_email><status>FAIL</status><error>MYSQL_ERROR</error></change_email>";
                                    die( "$xml_output" );
                                            
                            }
                        
                    }
                    else 
                                header("Location: ./index.php");
                
            }
            else {
                    $xml_output .= "<change_email><status>FAIL</status><error>BAD_REQUEST</error></change_email>";
                    die( "$xml_output" );
            }

?>

