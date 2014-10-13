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
        $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        
        if ( isset( $_REQUEST['email'] ) ) 
                $email = trim( $_REQUEST['email'] );
        else {
                $xml_output .= "<check_email><status>FAIL</status><error>BAD_REQUEST</error></check_email>";
		die( "$xml_output" );
        }

        $email_error_code = check_email( $email );
        
        if ( $email_error_code ==  FOUND ) {
                $xml_output .= "<check_email><status>FAIL</status><error>EMAIL_ALREADY_EXISTS</error></check_email>";
                die( "$xml_output" );
        }
        else if ( $username_error_code == NOT_FOUND ) {
                $xml_output .= "<check_email><status>SUCCESS</status></check_email>";
                die( "$xml_output" );
        }
        else {
                $xml_output .= "<check_email><status>FAIL</status><error>MYSQL_ERROR</error></check_emal>";
                die( "$xml_output" );
        }
 ?>
