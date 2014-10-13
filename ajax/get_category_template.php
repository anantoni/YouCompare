<?php        
            /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/

            include_once( "../db_includes/user.php" );
            include_once( "../db_includes/DB_DEFINES.php" );
            include_once( "../db_includes/category.php" );
            include_once( "logic_functions.php" );
            include_once( "LOGIC_DEFINES.php" );

            $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            $name = "";
            $id;
            $category;
            $attribute_array = array();
            
            if ( isset( $_REQUEST["name"] ) ) {
                
                   $name = sanitize_str( $_REQUEST["name"] );
                   
                   session_start();
                   if ( !isset( $_SESSION['username'] ) ) 
                           header("Location: ./index.php");
                  

                   $category = new category( -1 );
                   $id = $category->get_id_by_name( $name );
                   

                   if ( $category->get_errno() == DB_OK ) {
                       
                            $category = new category( $id );
                            $category->can_access( $_SESSION['username'], CATEGORY_MEMBER );

                            if ( $category->get_errno() != DB_OK ) {
                                    $xml_output .= "<get_category_template><status>FAIL</status><error>NOT_ENOUGH_PRIVILEGES</error></get_category_template>";
                                    die( "$xml_output" );

                            }
                            $attribute_array = $category->get_attributes( 0 );


                            /********************************************* IF DB_OK CREATE XML *************************************************************************************/
                            if ( $category->get_errno() == DB_OK ) {

                                    $xml_output .= "<get_category_template><status>SUCCESS</status>";

                                    /**************************************************** COUNTABLE ATTRIBUTES ****************************************************************************/
                                    $xml_output .= "<Countable id=\"Countable\">";
                                    for ( $i = 0 ; $i < count( $attribute_array ) ; $i++ ) {
                                       
                                            if ( $attribute_array[$i]->comparability == COUNTABLE ) 
                                                $xml_output .= "<attribute><id>".$attribute_array[$i]->id."</id><name>".$attribute_array[$i]->name."</name><description> ".$attribute_array[$i]->description." </description><lower> ".$attribute_array[$i]->type_elements["Lower Limit"]." </lower><upper> ".$attribute_array[$i]->type_elements["Upper Limit"]." </upper><optimal> ".$attribute_array[$i]->type_elements["Comparison Type"]." </optimal><filterability> " .$attribute_array[$i]->is_filterable. "</filterability></attribute>";

                                    }
                                    $xml_output .= "</Countable>";

                                    /**************************************************** DISTINCT ATTRIBUTES ****************************************************************************/
                                    $xml_output .= "<Distinct id=\"Distinct\">";

                                    for ( $i = 0 ; $i < count( $attribute_array ) ; $i++ ) {

                                            if ( $attribute_array[$i]->comparability == DISTINCT ) {
                                                
                                                $values_string = "";
                                                $weights_string = "";
                                                
                                                $values_string = $attribute_array[$i]->type_elements[0]["Value"];
                                                $weights_string = $attribute_array[$i]->type_elements[0]["Preference"];

                                                for ( $j = 1 ; $j < count( $attribute_array[$i]->type_elements ) ; $j++ ) {
                                                    $values_string .= ",".$attribute_array[$i]->type_elements[$j]["Value"];
                                                    $weights_string .= ",".$attribute_array[$i]->type_elements[$j]["Preference"];
                                                }

                                                $xml_output .= "<attribute><id>".$attribute_array[$i]->id."</id><name>".$attribute_array[$i]->name."</name><description>".$attribute_array[$i]->description."</description><values>".$values_string." </values><weights> " .$weights_string. "</weights><filterability>".$attribute_array[$i]->is_filterable."</filerability></attribute>";

                                            }
                                    }
                                    $xml_output .= "</Distinct>";

                                    /**************************************************** UNCOMPARABLE ATTRIBUTES ****************************************************************************/
                                    $xml_output .= "<Uncomparable id=\"Uncomparable\">";
                                    for ( $i = 0 ; $i < count( $attribute_array ) ; $i++ ) {

                                            if ( $attribute_array[$i]->comparability == UNCOMPARABLE ) {

                                                    $xml_output .= "<attribute><id>".$attribute_array[$i]->id."</id><name>".$attribute_array[$i]->name."</name><description>".$attribute_array[$i]->description."</description><filterability>".$attribute_array[$i]->is_filterable."</filterability></attribute>";

                                                }
                                    }
                                    $xml_output .= "</Uncomparable></get_category_template>";

                                    die( "$xml_output" );
                            }
                            else if ( $category->get_errno() == WRONG_ID ) {  

                                    $xml_output .= "<get_category_template><status>FAIL</status><error>CATEGORY_NOT_EXISTS</error></get_category_template>";
                                    die( "$xml_output" );
                            }
                            else if ( $category->get_errno() == CATEGORY_CLOSED ) { 

                                    $xml_output .= "<get_category_template><status>FAIL</status><error>CATEGORY_CLOSED</error></get_category_template>";
                                    die( "$xml_output" );
                            }
                            
                   }
                   else if ( $category->get_errno() == CATEGORY_DONT_EXIST ) {
                            $xml_output .= "<get_category_template><status>FAIL</status><error>CATEGORY_NOT_EXISTS</error></get_category_template>";
                            die( "$xml_output" );
                   }
                   else {
                            $xml_output .= "<get_category_template><status>FAIL</status><error>MYSQL_ERROR</error></get_category_template>";
                            die( "$xml_output" );
                       
                   }
           
       }
       else {
           $xml_output .= "<get_category_template><status>FAIL</status><error>BAD_REQUEST</error></get_category_template>";
           die( "$xml_output" );
       }

?>