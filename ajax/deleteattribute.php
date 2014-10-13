<?php
        include_once( "../db_includes/DB_DEFINES.php" );
        include_once( "../db_includes/category.php" );
	include_once( "../db_includes/attribute.php" );
        include_once( "logic_functions.php" );
        include_once( "LOGIC_DEFINES.php" );
        
////////////////////////////////////////////////////////////////////////////////
	$xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";

        //check if category id has been sent
        if( isset( $_REQUEST["cat_id"])){
            $catid = intval( $_REQUEST["cat_id"] );

            if ( check_input($catid) == EMPTY_QUERY ){
                $xml_output .= "<delete_attribute><status>FAIL</status><error>NO_CATEGORY_SELECTED</error></delete_attribute>";
                die( "$xml_output" );

            }
        }
        else{
                $xml_output .= "<delete_attribute><status>FAIL</status><error>NO_CATEGORY_SELECTED</error></delete_attribute>";
            	  die( "$xml_output" );
        }

////////////////////////////////////////////////////////////////////////////////
        //check if attribute id has been sent
        if( isset( $_REQUEST["attr_id"] ) ) {
            $attrid[0] = intval($_REQUEST["attr_id"]);
            if (check_input($attrid[0]) == EMPTY_QUERY){
                  $xml_output .= "<delete_attribute><status>FAIL</status><error>NO_ATTRIBUTE_SELECTED</error></delete_attribute>";
            	  die( "$xml_output" );
            }
        }
        else {
            	  $xml_output .= "<delete_attribute><status>FAIL</status><error>NO_ATTRIBUTE_SELECTED</error></delete_attribute>";
            	  die( "$xml_output" );
        }
////////////////////////////////////////////////////////////////////////////////

        $categ = new Category($catid);
        if ( $categ->get_errno() == CATEGORY_DONT_EXIST ) {
            	  $xml_output .= "<delete_attribute><status>FAIL</status><error>CATEGORY_NOT_EXISTS</error></delete_attribute>";
            	  die( "$xml_output" );        
	 }

        $categ->remove_attributes($attrid);
        if ( $categ->get_errno() != DB_OK ) {
                  $xml_output .= "<delete_attribute><status>FAIL</status><error>MYSQL_ERROR</error></delete_attribute>";
            	  die( "$xml_output" );        
	}
        else {
            	  $xml_output .= "<delete_attribute><status>SUCCESS</status></delete_attribute>";
            	  die( "$xml_output" );
	}
?>