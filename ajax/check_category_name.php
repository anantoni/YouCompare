<?php
         /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/
        
        include_once( "../db_includes/DB_DEFINES.php" );
        include_once( "../db_includes/category.php" );
        include_once( "logic_functions.php" );
        include_once( "LOGIC_DEFINES.php" );
        
        $category_name = "";
        $category_name_error_code = 0;
        $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";


        if ( isset( $_REQUEST['category_name'] ) )
                $category_name = trim( $_REQUEST['category_name'] );
        else {
                $xml_output .= "<check_category_name><status>FAIL</status><error>BAD_REQUEST</error></check_category_name>";
                die( "$xml_output" );
        }


       
        $category = new category(-1);
        $category_name_error_code = $category->category_exists( $category_name );
        
        if ( $category_name_error_code ==  CATEGORY_EXISTS) {
                $xml_output .= "<check_category_name><status>FAIL</status><error>CATEGORY_NAME_ALREADY_EXISTS</error></check_category_name>";
                die( "$xml_output" );
        }
        else if ( $category_name_error_code == CATEGORY_DONT_EXIST ) {
                $xml_output .= "<check_category_name><status>SUCCESS</status></check_category_name>";
                die( "$xml_output" );
        }
        else {
                if ( $category->get_errno() == MYSQL_ERROR ) {
                        $xml_output .= "<check_category_name><status>FAIL</status><error>MYSQL_ERROR</error></check_category_name>";
                        die( "$xml_output" );
                    
                }
                else if ( $category->get_errno() == WRONG_ID )	{																		//	We don't refer to a specific category y{
			$xml_output .= "<check_category_name><status>FAIL</status><error>INVALID_INPUT</error></check_category_name>";
                        die( "$xml_output" );
		}
            
        }

 ?>
