<?php
        /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/

        include_once( "../db_includes/user.php" );
        include_once( "../db_includes/DB_DEFINES.php" );
        include_once( "logic_functions.php" );
        include_once( "LOGIC_DEFINES.php" );

        $username = "";
        $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";


        if ( isset( $_REQUEST['username'] ) )
                $username = sanitize_str( trim( $_REQUEST['username'] ) );
        else {
                $xml_output .= "<check_username><status>FAIL</status><error>BAD_REQUEST</error></check_username>";
                die( "$xml_output" );
        }


        $username_error_code = check_username( $username );
        
        if ( $username_error_code ==  FOUND ) {
                $xml_output .= "<check_username><status>FAIL</status><error>USERNAME_ALREADY_EXISTS</error></check_username>";
                die( "$xml_output" );
        }
        else if ( $username_error_code == NOT_FOUND ) {
                $xml_output .= "<check_username><status>SUCCESS</status></check_username>";
                die( "$xml_output" );
        }
        else {
                $xml_output .= "<check_username><status>FAIL</status><error>MYSQL_ERROR</error></check_username>";
                die( "$xml_output" );
        }
 ?>