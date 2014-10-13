<?php
     /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/

    include_once( "../db_includes/category.php" );
    include_once( "../db_includes/attribute.php" );
    include_once( "../db_includes/user.php" );
    include_once( "../db_includes/DB_DEFINES.php" );
    include_once( "logic_functions.php" );
    include_once( "LOGIC_DEFINES.php" );
     

     function check_category_name_validity( $category_name ) {                              //category_name validation
         
             $name_pattern = '/[^A-Za-z0-9().?!&*%-_ ]/';
             $error_message = "";

             if ( preg_match( $name_pattern, $category_name ) == true )
                 $error_message = "CATEGORY_NAME_INVALID";

             else if ( strlen( $category_name ) > 100 )
                 $error_message = "CATEGORY_NAME_TOO_LONG";

             else if ( strlen( $category_name ) < 2 )
                 $error_message = "CATEGORY_NAME_TOO_SHORT";

             return $error_message;
             
     }

     function check_category_keywords_validity( $category_keywords ) {                       //category_keywords validation
         
             $keywords_pattern = '/[^A-Za-z0-9, ]/';
             $error_message = "";

             if ( preg_match( $keywords_pattern, $category_keywords ) == true )
                 $error_message = "CATEGORY_KEYWORDS_INVALID";
             
             else if ( strlen( $category_keywords ) > 500 )
                 $error_message = "CATEGORY_KEYWORDS_TOO_LONG";
             
             return $error_message;
     }

     function check_category_description_validity( $category_description ) {                 //category_description validation
             
             $error_message = "";

             if ( strlen( $category_description ) > 500 )
                 $error_message = "CATEGORY_DESCRIPTION_TOO_LONG";
             
             return $error_message;
             
     }

     function check_category_type_validity( $category_type ) {                                //category_type validation
            $error_message = "";

            if ( $category_type != OPEN && $category_type != CLOSE )                             //Elegxw gia category type vadility
             $error_message = "CATEGORY_TYPE_INVALID";

            return $error_message;
     }

    function check_attribute_name_validity( $attribute_name ) {                                //attribute_name validation
        
            $name_pattern = '/[^A-Za-z0-9().?!&*%-_ ]/';
            $error_message = "";

            if ( preg_match( $name_pattern, $attribute_name ) == true )                           //Kanw checkarisma me to pattern tou attribute name
                $error_message = "ATTRIBUTE_NAME_INVALID";

            else if ( strlen( $attribute_name ) > 50  )                                           //Elegxw gia attribute name too long
                $error_message = "ATTRIBUTE_NAME_TOO_LONG";

            else if ( strlen( $attribute_name ) < 2 )
                $error_message = "ATTRIBUTE_NAME_TOO_SHORT";                                       //Elegxw gia attribute name too short

            return $error_message;
    
    }

    function check_attribute_comparability_validity( $attribute_comparability ) {              //attribute_comparability validation
        
            $error_message = "";

            if ( $attribute_comparability != UNCOMPARABLE && $attribute_comparability != DISTINCT && $attribute_comparability != COUNTABLE )
                $error_message = "ATTRIBUTE_TYPE_INVALID";

            return $error_message;
            
    }

    function check_attribute_description_validity( $attribute_description ) {                  //attribute_description validation
            
            $error_message = "";

            if ( strlen( $attribute_description ) > 500 )
                $error_message = "ATTRIBUTE_DESCRIPTION_TOO_LONG";

            return $error_message;
            
    }

    function check_attribute_value_range_validity( $attribute_values, $attribute_type, $min, $max ) {      //attribute_values validation
        
            $distinct_values_pattern = '/[^A-Za-z0-9?!+%().,-_ ]/';
            $distinct_weights_pattern = '/[^0-9,]/';
            $countable_pattern = '/[^0-9.,-]/';

            $error_message = "";

            if ( $attribute_type == DISTINCT ) {                                                               //DISTINCT CHECK
                
                if ( preg_match( $distinct_values_pattern, $attribute_values ) == true )                       //Check me to value pattern gia values
                    $error_message = "INVALID_DISTINCT_VALUES";
                
                else if ( preg_match( $distinct_weights_pattern, $min ) == true )                              //Check me to value pattern gia times
                    $error_message = "INVALID_DISTINCT_VALUE_WEIGHTS";
                
                else if ( count( $attribute_values ) != count ( $min ) )
                    $error_message = "VALUES_AND_WEIGHTS_NUMBERS_DONT_MATCH";
                
            }
            else {
                
                if ( preg_match( $countable_pattern,$min ) == true )
                    $error_message = "INVALID_COUNTABLE_VALUE_LIMIT";
                
                else if ( preg_match( $countable_pattern,$max ) == true )
                    $error_message = "INVALID_COUNTABLE_VALUE_LIMIT";
                
                else if ( $attribute_values != "min" && $attribute_values != "max" && $attribute_values != "average" )
                    $error_message = "INVALID_DEFAULT_COMPARE_PREFERENCE";
                
            }
            
            return $error_message;
    }

    class attribute_value_range_prototype {
        
            public $attribute_type;
            public $min_Limit;
            public $max_Limit;
            public $comparison_type;
            public $distinct_values_array;
            public $distinct_value_weights_array;
            
    }

    $attribute_info_List = NULL;
    $attribute_prototype_List = NULL;
    $category_info = new category_info();
    $category_name = "";
    $category_keywords = "";
    $category_description = "";
    $category_type = -10;
    $attributes_array = NULL;
    $request_error_message = "";
    $form_error_message = "";
    $category_name_error_message = "";
    $category_keywords_error_message = "";
    $category_description_error_message = "";
    $num_of_attributes_error_message = "";
    $category_type_error_message = "";
    $attribute_name_error_message = "";
    $attribute_comparability_error_message = "";
    $attribute_description_error_message = "";
    $attribute_value_range_error_message = "";
    $keyword_pieces = NULL;
    $error_existence = 0;
    $xml_output = "";
    $type_elements;
    $error_code = -1;
    $min_Limit;
    $max_Limit;
    $comparison_type;
    $cat_image = NULL;;

   
    if ( isset( $_POST["category_name"] ) && isset( $_POST["category_type"] ) && isset( $_POST["attributes_array"] ) ) {
        
                $category_name = sanitize_str( $_POST["category_name"] );

                if ( isset( $_POST["category_keywords"] ) ) {
                        if ( $_POST["category_keywords"] != "" )
                                $category_keywords = sanitize_str( $_POST["category_keywords"] );
                        else 
                                $category_keywords = NULL;
                }
                else
                                $category_keywords = NULL;
                
                if ( isset( $_POST["category_description"] ) ) {
                        if ( $_POST["category_description"] != "" )
                                $category_description = sanitize_str( $_POST["category_description"] );
                        else 
                                $category_description = NULL;
                }
                else
                                $category_description = NULL;
                
                if ( isset( $_POST["category_image"] ) ) {
                        if (  $_POST["category_image"] != "" )
                                        $cat_image = sanitize_str( $_POST["category_image"] );
                        else {
                                        $cat_image = NULL;
                }}
                
                    
                $category_type = intval( $_POST["category_type"] );
                $attributes_array = (array) $_POST["attributes_array"];

    }
    else
                $request_error_message = "BAD_REQUEST";
    


    /***************************************** Category Error Checking *****************************************************/
    if ( $category_name == "" ) {                                                                                        //Error check gia to form
        $form_error_message = "NOT_ALL_FORMS_FILLED";
        $error_existence = 1;
    }
    if ( ( $category_name_error_message = check_category_name_validity( $category_name ) ) != "" )                      //Error check gia to category_name
        $error_existence = 1;
    if ( ( $category_keywords_error_message = check_category_keywords_validity( $category_keywords ) )!= "" )           //Error check gia ta category_keywords
        $error_existence = 1;
    if ( ( $category_description_error_message = check_category_description_validity( $category_description ) ) != "" ) //Error check gia to category_description
        $error_existence = 1;
    if ( ( $category_type_error_message = check_category_type_validity( $category_type ) ) != "" )
        $error_existence = 1;
    if ( count( $attributes_array ) == 0 ) {
        $num_of_attributes_error_message = "NO_ATTRIBUTES";
        $error_existence = 1;
    }

    /***************************************** Dhmiourgia tou pinaka twn Attributes kai Attribute Error Checking ***********************************************/
    if ( $error_existence == 0 ) {
        
            for ( $i = 0 ; $i < count ( $attributes_array ) ; $i++ ) {                                                       //Analoga me ton ari8mo twn attributes pou mou esteile to GUI

                        if ( ( $attribute_name_error_message = check_attribute_name_validity( $attributes_array[$i][0] ) ) != "" )                         // Ean vre8ei kapoio la8os telos to loop
                                $error_existence = 1;

                        if ( ( $attribute_description_message = check_attribute_description_validity( $attributes_array[$i][1] ) ) != "" )
                                $error_existence = 1;

                        if ( ( $attribute_comparability_error_message = check_attribute_comparability_validity( $attributes_array[$i][2] ) ) != "" )
                                $error_existence = 1;

                        if ( $error_existence == 1 ) 
                                break;

                        $attribute_info_List[$i] = new attribute_info();               
                        $attribute_info_List[$i]->name = $attributes_array[$i][0];   

                        if ( intval( $attributes_array[$i][1] ) != "" )
                                $attribute_info_List[$i]->description = $attributes_array[$i][1]; 
                        else 
                                $attribute_info_List[$i]->description = NULL;
                        $attribute_info_List[$i]->comparability = intval( $attributes_array[$i][2] );
                        $attribute_prototype_List[$i] = new attribute_value_range_prototype();

                        if ( intval( $attributes_array[$i][2] ) == 2 ) {

                            if ( ( $attribute_value_range_error_message = check_attribute_value_range_validity( $attributes_array[$i][5], $attributes_array[$i][2], $attributes_array[$i][3], $attributes_array[$i][4] ) ) != "" ) {
                                    $error_existence = 1;
                                    break;
                            }

                            $attribute_prototype_List[$i]->min_Limit = floatval( $attributes_array[$i][3] );
                            $attribute_prototype_List[$i]->max_Limit = floatval( $attributes_array[$i][4] );
                            $attribute_info_List[$i]->is_filterable = intval( $attributes_array[$i][6] );


                            if ( sanitize_str( $attributes_array[$i][5] ) == "min" )
                                    $attribute_prototype_List[$i]->comparison_type = 1;

                            else if ( sanitize_str( $attributes_array[$i][5] ) == "max" )
                                    $attribute_prototype_List[$i]->comparison_type = 0;

                            else
                                    $attribute_prototype_List[$i]->comparison_type = 2;
                        }
                        else if ( intval( $attributes_array[$i][2] ) == 1 ) {

                            if ( ( $attribute_value_range_error_message = check_attribute_value_range_validity( $attributes_array[$i][3], $attributes_array[$i][2], $attributes_array[$i][4], 0 ) ) != "" ) {
                                $error_existence = 1;
                                break;
                            }

                            $attribute_prototype_List[$i]->distinct_values_array = explode( ",", sanitize_str( $attributes_array[$i][3] ) );
                            $attribute_prototype_List[$i]->distinct_value_weights_array = explode( ",", sanitize_str( $attributes_array[$i][4] ) );

                            $attribute_info_List[$i]->is_filterable = intval( $attributes_array[$i][5] );
                }
                else 
                    $attribute_info_List[$i]->is_filterable = intval( $attributes_array[$i][3] );
                
                
            }

            for ( $i = 0 ; $i < count( $attributes_array ) ; $i++ )
                for ( $j = 0 ; $j < count( $attributes_array) ; $j++ )
                    if ( $i != $j )
                        if ( $attributes_array[$i][0] == $attributes_array[$j][0] ) {
                            $attribute_name_error_message = "ATTRIBUTE_NAME_DUPLICATE_FOUND";
                            $error_existence = 1;
                            break;
                        }

    
            /********************************* Parsing twn keywords, kai tou Value Range an exw countable attribute h twn Distinct Values an exw Distinct attribute ********************************/           
            $keyword_pieces = explode( ',', $category_keywords );
            if ( $error_existence == 0 ) {
                        for ( $i = 0 ; $i < count( $attributes_array) ; $i++ ) {

                            if ( $attribute_info_List[$i]->comparability == COUNTABLE )  
                                $attribute_info_List[$i]->type_elements = array( "Upper Limit" => $attribute_prototype_List[$i]->max_Limit, "Lower Limit"=> $attribute_prototype_List[$i]->min_Limit, "Comparison Type"=> $attribute_prototype_List[$i]->comparison_type );  //

                            else if ( $attribute_info_List[$i]->comparability == DISTINCT ) {
                                for ( $j = 0 ; $j < count( $attribute_prototype_List[$i]->distinct_value_weights_array ) ; $j++ )
                                    $attribute_info_List[$i]->type_elements[] = array( "Preference" => $attribute_prototype_List[$i]->distinct_value_weights_array[$j], "Value" => $attribute_prototype_List[$i]->distinct_values_array[$j] );
                            }

                            else
                                $attribute_info_List[$i]->type_elements = NULL;

                        }

                        $category = new category(-1);
                        if ( $category->get_errno() != DB_OK ) {
                                $xml_output .= "<create_category><status>FAIL</status><error>MYSQL_ERROR</error></create_category>";
                                die( "$xml_output" );
                        }
                        /************************************** Gemisma tou object category_info ********************************/
                        $category_info->cat_name = $category_name;
                        $category_info->cat_keywords = $keyword_pieces;
                        $category_info->is_open = $category_type;
                        $category_info->cat_description = $category_description;
                        $category_info->attr_info_array = $attribute_info_List;
                        $category_info->cat_image = $cat_image;

                        session_start();
                        if ( isset( $_SESSION ) ) {                                                                                    //An uparxei SESSION
                              if ( isset( $_SESSION['username'] ) ) {                                                                      //An uparxei SESSION['username']
                                    if ( ( $error_code = $category->can_access( $_SESSION['username'], CATEGORY_MEMBER) ) == DB_OK )   {   //Elegxos an exei dikaiwma na dhmiourhgsei kathgoria
                                        $category->create_category( $category_info, $_SESSION['username'] );                               //Klhsh ths create_category gia th dhmiourgia kathgorias
                                        $error_code = $category->get_errno();

                                    }

                              }
                              else
                                    $error_code = NOT_LOGGED_IN;
                        }
                        else
                                $error_code = NOT_LOGGED_IN;



                        $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
                        if ( $error_code == DB_OK ) {
                            $category->set_image( $cat_image );
                            if ( $category->get_errno() != DB_OK ) 
                                $xml_output .= "<create_category><status>FAIL</status><error>SET_IMAGE_FAILED</error><id>".$category->get_id()."</id></create_category>";   
                            else
                                $xml_output .= "<create_category><status>SUCCESS</status><id>".$category->get_id()."</id></create_category>";
                        }
                        else if ( $error_code == NOT_LOGGED_IN )
                            $xml_output .= "<create_category><status>FAIL</status><error>NOT_LOGGED_IN</error></create_category>";
                        
                        else if ( $error_code == NOT_ENOUGH_ATTRIBUTES )
                            $xml_output .= "<create_category><status>FAIL</status><error>NO_ATTRIBUTES</error></create_category>";
                        
                        else if ( $error_code == MYSQL_CONNECT_ERROR )
                            $xml_output .= "<create_category><status>FAIL</status><error>MYSQL_CONNECT_ERROR</error></create_category>";
                        
                        else if ( $error_code == MYSQL_ERROR ) 
                            $xml_output .= "<create_category><status>FAIL</status><error>MYSQL_ERROR</error></create_category>";
                        
                        else if ( $error_code == CATEGORY_EXISTS )
                            $xml_output .= "<create_category><status>FAIL</status><error>CATEGORY_NAME_ALREADY_EXISTS</error></create_category>";
                        
                        else if ( $error_code == USERNAME_DONT_EXIST )
                            $xml_output .= "<create_category><status>FAIL</status><error>USERNAME_NOT_EXISTS</error></create_category>";
                        
                        else if ( $error_code == CATEGORY_DONT_EXIST )
                            $xml_output .= "<create_category><status>FAIL</status><error>CATEGORY_NOT_INSERTED</error></create_category>";
                        
                        die ( "$xml_output" );

            }
            else {

                
                         $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
                         $xml_output .= "<create_category><status>FAIL</status>";
                         if ( $attribute_name_error_message != "" )
                            $xml_output .= "<error>".$attribute_name_error_message."</error>";
                         
                         if ( $attribute_comparability_error_message != "" )
                            $xml_output .= "<error>".$attribute_comparability_error_message."</error>";
                         
                         if ( $attribute_description_error_message != "" )
                            $xml_output .= "<error>".$attribute_description_error_message."</error>";
                         
                         if ( $attribute_value_range_error_message != "" )
                            $xml_output .= "<error>".$attribute_value_range_error_message."</error>";
                         
                         $xml_output .= "</create_category>";
                         die ( "$xml_output" );

            }
    }
    else {

        
                        $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
                        $xml_output .= "<create_category><status>FAIL</status>";
                        if ( $request_error_message != "" )
                            $xml_output .= "<error>".$request_error_message."</error>";

                        if ( $form_error_message != "" )
                            $xml_output .= "<error>".$form_error_message."</error>";

                        if ( $category_name_error_message != "" )
                            $xml_output .= "<error>".$category_name_error_message."</error>";

                        if ( $category_keywords_error_message != "" )
                            $xml_output .= "<error>".$category_keywords_error_message."</error>";

                        if ( $category_type_error_message != "" )
                            $xml_output .= "<error>".$category_type_error_message."</error>";
                        if ( $category_description_error_message != "" )
                            $xml_output .= "<error>".$category_description_error_message."</error>";

                        if ( $num_of_attributes_error_message != "" )
                            $xml_output .= "<error>".$num_of_attributes_error_message."</error>";

                        $xml_output .= "</create_category>";
                        die ( "$xml_output" );

    }
?>
