<?php
             /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/

            include_once( "../db_includes/DB_DEFINES.php" );
            include_once( "../db_includes/user.php" );
            include_once( "logic_functions.php" );
            include_once "LOGIC_DEFINES.php";

            $firstname = "";
            $user = NULL;
            $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";

            if ( isset( $_REQUEST["firstname"] ) ) {
                
                    $firstname = sanitize_str( $_REQUEST["firstname"] );
                    session_start();
                    
                    if ( isset( $_SESSION["username"] ) ) {
                            
                            $user = new user( $_SESSION["username"], LOGGED_IN );
                            if ( $user->get_errno() == DB_OK ) {
                                
                                    $user->set_name( $firstname );
                                    if ( $user->get_errno() == DB_OK ) {
                                            
                                            $xml_output .= "<change_firstname><status>SUCCESS</status></change_firstname>";
                                            die( "$xml_output" );
                                        
                                    }
                                    else {
                                            $xml_output .= "<change_firstname><status>FAIL</status><error>MYSQL_ERROR</error></change_firstname>";
                                            die( "$xml_output" );
                                            
                                    }
                            }
                            else {
                                    $xml_output .= "<change_firstname><status>FAIL</status><error>MYSQL_ERROR</error></change_firstname>";
                                    die( "$xml_output" );
                                            
                            }
                                    
                    }
                    else 
                                header("./index.php");
                
            }
            else {
                    $xml_output .= "<change_firstname><status>FAIL</status><error>BAD_REQUEST</error></change_firstname>";
                    die( "$xml_output" );
            }

?>
