<?php
        include_once '../db_includes/DB_DEFINES.php';
        include_once '../db_includes/category.php';
        include_once 'logic_functions.php';
        include_once( "LOGIC_DEFINES.php" );
	
        $image = "";
	$xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
////////////////////////////////////////////////////////////////////////////////
        //check if category id has been sent
        if(isset($_REQUEST["cat_id"])){
            $catid = intval($_REQUEST["cat_id"]);
            if (check_input($catid) == EMPTY_QUERY){
                $xml_output .= "<delete_attribute><status>FAIL</status><error>NO_CATEGORY_SELECTED</error></delete_attribute>";
                die( "$xml_output" );            
	     }
        }
        else{
                $xml_output .= "<delete_attribute><status>FAIL</status><error>NO_CATEGORY_SELECTED</error></delete_attribute>";
                die( "$xml_output" );        
	 }

////////////////////////////////////////////////////////////////////////////////
        //check if entity name has been sent
        if(isset($_REQUEST["name"])){
            $name = sanitize_str($_REQUEST["name"]);
            
            if (check_input($name) == EMPTY_QUERY){
                $xml_output .= "<delete_attribute><status>FAIL</status><error>NAME_NOT_SET</error></delete_attribute>";
                die( "$xml_output" );            }
        }
        else{
                $xml_output .= "<delete_attribute><status>FAIL</status><error>NAME_NOT_SET</error></delete_attribute>";
                die( "$xml_output" );
        }

////////////////////////////////////////////////////////////////////////////////
        //check if keywords have been sent
        if(isset($_REQUEST["keywords"])){
            $kws = sanitize_str($_REQUEST["keywords"]);
            
            if (check_input($kws) == EMPTY_QUERY){
                $kws = NULL;
            }
        }
        else{
            $kws = NULL;
        }
        if($kws!=NULL)
            $kw=preg_split("/[\s]*[,][\s]*/", $kws);
        else
            $kw=NULL;
////////////////////////////////////////////////////////////////////////////////
        //check if description has been sent
        if(isset($_REQUEST["desc"])){
            $desc = sanitize_str($_REQUEST["desc"]);
            //if empty echo xml error
            if (check_input($desc) == EMPTY_QUERY){
                $desc = NULL;
            }
        }
        else{
            $desc = NULL;
        }

////////////////////////////////////////////////////////////////////////////////
        //check if image has been sent
   //to be added
        if( isset( $_REQUEST["category_image"] ) ) 	
            if ( $_REQUEST["category_image"] != "" ) 
                $image = $_REQUEST["category_image"];
	
print_r($_REQUEST);
        //ean den alla3e krata tin proigoumeni
        //ean egine empty bale tin default
        //ean set send sto file system kai dwse link sto $image
////////////////////////////////////////////////////////////////////////////////
        //check if video has been sent
        if(isset($_REQUEST["vid"])){
            $video = sanitize_str($_REQUEST["vid"]);
            //if empty echo xml error
            if (check_input($video) == EMPTY_QUERY){
                $video = NULL;
            }
        }
        else{
            $video = NULL;
        }

////////////////////////////////////////////////////////////////////////////////
        //check if openness has been sent
        if(isset($_REQUEST["openness"])){
            $open = intval($_REQUEST["openness"]);
            
            if (check_input($open) == EMPTY_QUERY){
                $xml_output .= "<delete_attribute><status>FAIL</status><error>CATEGORY_TYPE_NOT_SET</error></delete_attribute>";
                die( "$xml_output" );
            }
        }
        else{
            	  $xml_output .= "<delete_attribute><status>FAIL</status><error>CATEGORY_TYPE_NOT_SET</error></delete_attribute>";
                die( "$xml_output" );
        }

////////////////////////////////////////////////////////////////////////////////
        
        $categ = new Category($catid);
        if ($categ->get_errno() == CATEGORY_DONT_EXIST){
                $xml_output .= "<delete_attribute><status>FAIL</status><error>CATEGORY_NOT_EXISTS</error></delete_attribute>";
                die( "$xml_output" );
        }

////////////////////////////////////////////////////////////////////////////////
        //Send to database
        

        if($categ->get_name()!=$name){
            $categ->set_name($name);
            if($categ->get_errno() != DB_OK){
                $xml_output .= "<delete_attribute><status>FAIL</status><error>CATEGORY_TYPE_NOT_SET</error></delete_attribute>";
                die( "$xml_output" );
            }
        }

        if($categ->get_description()!=$desc){
            $categ->set_description($desc);
            if($categ->get_errno() != DB_OK){
                $xml_output .= "<delete_attribute><status>FAIL</status><error>MYSQL_ERROR</error></delete_attribute>";
                die( "$xml_output" );
            }
        }

        if($categ->get_keywords()!=$kw){
            $categ->set_keywords($kw);
            if($categ->get_errno() != DB_OK){
                $xml_output .= "<delete_attribute><status>FAIL</status><error>MYSQL_ERROR</error></delete_attribute>";
                die( "$xml_output" );
            }
        }
        ///////////////////////////////////////////image to be added

        if ( $image != NULL ) { 
                if( $categ->get_image() != $image ){

                    $categ->set_image( $image);
                    if ( $categ->get_errno() != DB_OK ) {
                        $xml_output .= "<delete_attribute><status>FAIL</status><error>MYSQL_ERROR</error></delete_attribute>";
                        die( "$xml_output" );
                    }
                }
        }
        
        if($categ->get_video()!=$video){
            $categ->set_video($video);
            if($categ->get_errno() != DB_OK){
                $xml_output .= "<delete_attribute><status>FAIL</status><error>MYSQL_ERROR</error></delete_attribute>";
                die( "$xml_output" );
            }
        }

        if($categ->is_open()!=$open){
            $categ->set_openness($open);
            if($categ->get_errno() != DB_OK){
                $xml_output .= "<delete_attribute><status>FAIL</status><error>MYSQL_ERROR</error></delete_attribute>";
                die( "$xml_output" );
            }
        }

        $xml_output .= "<delete_attribute><status>SUCCESS</status></delete_attribute>";
        die( "$xml_output" );
////////////////////////////////////////////////////////////////////////////////
?>