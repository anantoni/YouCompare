<?php
        include_once( '../db_includes/DB_DEFINES.php' );
        include_once( '../db_includes/category.php' );
        include_once( 'logic_functions.php' );
        include_once( "LOGIC_DEFINES.php" );
        
        $xml_output =  '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        
        
        if ( isset( $_REQUEST["cat_id"] ) )                                  //check if category id has been sent
                $catid = intval( $_REQUEST["cat_id"] );
            
        else {
                $xml_output .= "<delete_category><status>FAIL</status><error>NO_CATEGORY_SELECTED</error></delete_category>";
                die( "$xml_output" );
        }



        $categ = new Category( $catid );
        if ( $categ->get_errno() == CATEGORY_DONT_EXIST ){
                $xml_output .= "<delete_category><status>FAIL</status><error>CATEGORY_NOT_EXISTS</error></delete_category>";
                die( "$xml_output" );
        }

        $categ->delete_category();
        if ( $categ->get_errno() != DB_OK )
                $xml_output .= "<delete_category><status>FAIL</status><error>MYSQL_ERROR</error></delete_category>";
        else
                $xml_output .= "<delete_category><status>SUCCESS</status></delete_category>";
        
        die( "$xml_output" );
?>