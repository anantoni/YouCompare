<?php
        /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/

	include_once( "../db_includes/attribute.php" );
        include_once( "../db_includes/category.php" );
        include_once( "../db_includes/DB_DEFINES.php" );
        include_once( "logic_functions.php" );
        include_once( "LOGIC_DEFINES.php" );
        
        class attribute_value_range_prototype {
            
                public $attribute_type;
                public $min_Limit;
                public $max_Limit;
                public $comparison_type;
                public $distinct_values_array;
                public $distinct_value_weights_array;
                
        }

            $attribute_info_List[0] = NULL;
            $attribute_prototype_List = NULL;
	    $attribute_id = 0;
            $category_info = new category_info();
            $category_name = "";
            $category_keywords = "";
            $category_description = "";
            $category_type = -10;
            $attribute_info = NULL;
            $request_error_message = "";
            $form_error_message = "";
                             
            $keyword_pieces = NULL;
            $error_existence = 0;
            $xml_output =  '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            $type_elements;
            $error_code = -1;
            $min_Limit;
            $max_Limit;
            $comparison_type;
            $cat_id;
            
        if ( isset( $_POST["cat_id"] ) && isset( $_POST["attribute_info"] ) ) {
            print_r( $_REQUEST );
                $cat_id = intval( $_POST["cat_id"] );
                $attribute_info = $_POST["attribute_info"];
                
                $attribute_info_List[0] = new attribute_info();               
                $attribute_info_List[0]->name = sanitize_str( $attribute_info[0] );                                     
                $attribute_info_List[0]->description = sanitize_str( $attribute_info[1] );                                         
                $attribute_info_List[0]->comparability = intval( $attribute_info[2] );
                
                $attribute_prototype_List = new attribute_value_range_prototype();

                if ( $attribute_info[2] == 2 ) {                    
                    
                            $attribute_prototype_List->min_Limit = floatval( $attribute_info[3] );
                            $attribute_prototype_List->max_Limit = floatval( $attribute_info[4] );
                            $attribute_info_List[0]->is_filterable = intval( $attribute_info[6] );


                            if ( sanitize_str( $attribute_info[5] ) == "min" )
                                    $attribute_prototype_List->comparison_type = 1;

                            else if ( sanitize_str( $attribute_info[5] ) == "max" )
                                    $attribute_prototype_List->comparison_type = 2;

                            else
                                    $attribute_prototype_List->comparison_type = 3;
                }
                else if ( $attribute_info[2] == 1 ) {

                            $attribute_prototype_List->distinct_values_array = explode( ",", sanitize_str( $attribute_info[3] ) );
                            $attribute_prototype_List->distinct_value_weights_array = explode( ",", sanitize_str( $attribute_info[4] ) );

                            $attribute_info_List[0]->is_filterable = intval( $attribute_info[5] );
                }
                else 
                            $attribute_info_List[0]->is_filterable = intval( $attribute_info[3] );
                
              
                if ( $attribute_info_List[0]->comparability == COUNTABLE )  
                            $attribute_info_List[0]->type_elements = array( "Upper Limit" => $attribute_prototype_List->max_Limit, "Lower Limit"=> $attribute_prototype_List->min_Limit, "Comparison Type"=> $attribute_prototype_List->comparison_type );  //
                
                else if ( $attribute_info_List[0]->comparability == DISTINCT ) {
                    for ( $j = 0 ; $j < count( $attribute_prototype_List->distinct_value_weights_array ) ; $j++ )
                            $attribute_info_List[0]->type_elements[] = array( "Preference" => $attribute_prototype_List->distinct_value_weights_array[$j], "Value" => $attribute_prototype_List->distinct_values_array[$j] );
                }
                
                else
                            $attribute_info_List[0]->type_elements = NULL;
                
                
                $category = new category( $cat_id );
                $attribute_id = $category->add_attributes( $attribute_info_List );
                $error_code = $category->get_errno();
                
                if ( $error_code == DB_OK )                     
                    $xml_output .= "<add_attribute><status>SUCCESS</status><id>".$attribute_id."</id></add_attribute>";
                
                else 
                    $xml_output .= "<add_attribute><status>FAIL</status><error>WRONG_ID</error></add_attribute>";
                
                die( "$xml_output" );
       
        }
        else {
                $xml_output .= "<add_attribute><status>FAIL</status><error>BAD_REQUEST</error></add_attribute>";
                die( "$xml_output" );
            
        }
?>