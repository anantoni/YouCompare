<?php
        include_once( '../db_includes/attribute.php' );
        include_once( '../db_includes/DB_DEFINES.php' );
        include_once( '../db_includes/category.php' );
        include_once( 'logic_functions.php' );
        include_once( "LOGIC_DEFINES.php" );


        $error=0;
        $xml_output = '<?xml version="1.0" encoding="utf-8"?>' . "\n"; print_r( $_REQUEST);
////////////////////////////////////////////////////////////////////////////////
        //check if category id has been sent
        if(isset($_REQUEST["cat_id"])){
            $catid = intval($_REQUEST["cat_id"]);
            if (check_input($catid) == EMPTY_QUERY){
                $xml_output .= "<edit_attribute><status>FAIL</status><error>NO_CATEGORY_SELECTED</error></edit_attribute>";
                die( "$xml_output" );            }
        }
        else{
            $xml_output .= "<edit_attribute><status>FAIL</status><error>NO_CATEGORY_SELECTED</error></edit_attribute>";
            die( "$xml_output" );
        }

////////////////////////////////////////////////////////////////////////////////
        //check if attribute id has been sent
        if(isset($_REQUEST["attr_id"])){
            $attrid = intval($_REQUEST["attr_id"]);
            if (check_input($attrid) == EMPTY_QUERY){
                $xml_output .= "<edit_attribute><status>FAIL</status><error>NO_ATTRIBUTE_SELECTED</error></edit_attribute>";
            	  die( "$xml_output" );
            }
        }
        else{
            $xml_output .= "<edit_attribute><status>FAIL</status><error>NO_ATTRIBUTE_SELECTED</error></edit_attribute>";
            die( "$xml_output" );
        }

////////////////////////////////////////////////////////////////////////////////
        //check if entity name has been sent
        if(isset($_REQUEST["name"])){
            $name = sanitize_str($_REQUEST["name"]);
            
            if (check_input($name) == EMPTY_QUERY){
                $xml_output .= "<edit_attribute><status>FAIL</status><error>NAME_NOT_SET</error></edit_attribute>";
            	  die( "$xml_output" );           
	     }
        }
        else{
            echo "Name not set";
            return;
        }

////////////////////////////////////////////////////////////////////////////////
        //check if description has been sent
        if(isset($_REQUEST["desc"])){
            $desc = sanitize_str($_REQUEST["desc"]);
            
            if (check_input($desc) == EMPTY_QUERY){
                $desc = NULL;
            }
        }
        else{
            $desc = NULL;
        }

////////////////////////////////////////////////////////////////////////////////
        //check if filterability name has been sent
        if(isset($_REQUEST["filterable"])){
            $filt = intval($_REQUEST["filterable"]);
            //if empty echo xml error
            if (check_input($filt) == EMPTY_QUERY){
                $xml_output .= "<edit_attribute><status>FAIL</status><error>FILTERABILITY_NOT_SET</error></edit_attribute>";
            	  die( "$xml_output" );            
	     }
        }
        else{
            $xml_output .= "<edit_attribute><status>FAIL</status><error>FILTERABILITY_NOT_SET</error></edit_attribute>";
            	  die( "$xml_output" );
        }
        
////////////////////////////////////////////////////////////////////////////////
        //check if comparability name has been sent
        if(isset($_REQUEST["type"])){
            $comp = intval($_REQUEST["type"]);
            //if empty echo xml error
            if (check_input($comp) == EMPTY_QUERY || ($comp!=UNCOMPARABLE && $comp!=DISTINCT && $comp!=COUNTABLE)){
                $xml_output .= "<edit_attribute><status>FAIL</status><error>COMPARABILITY_NOT_SET</error></edit_attribute>";
            	  die( "$xml_output" );
            }
        }
        else{
            $xml_output .= "<edit_attribute><status>FAIL</status><error>COMPARABILITY_NOT_SET</error></edit_attribute>";
            	  die( "$xml_output" );
        }

////////////////////////////////////////////////////////////////////////////////

        $categ = new Category($catid);
        if ($categ->get_errno() == CATEGORY_DONT_EXIST){
            $xml_output .= "<edit_attribute><status>FAIL</status><error>CATEGORY_NOT_EXISTS</error></edit_attribute>";
            	  die( "$xml_output" );        
	 }

        $info = new attribute_info();
        if($categ->get_errno() != DB_OK){
            $xml_output .= "<edit_attribute><status>FAIL</status><error>MYSQL_ERROR</error></edit_attribute>";
            	  die( "$xml_output" );        
	 }

        $info->id=$attrid;
        $info->name=$name;
        $info->description=$desc;
        $info->comparability=$comp;
        $info->is_filterable=$filt;

        if($comp == DISTINCT){
            
            $cnt=count($_REQUEST["old_value"]);
            if($cnt<=0) {
                $xml_output .= "<edit_attribute><status>FAIL</status><error>OLD_VALUES_EMPTY</error></edit_attribute>";
            	  die( "$xml_output" );
	     }

            for($i=0;$i<$cnt;$i++){
                $old_val[$i]=sanitize_str($_REQUEST["old_value"][$i]);
                $old_prefer[$i]=floatval($_REQUEST["old_preference"][$i]);
            }
            
            $newcnt=count($_REQUEST["value"]);

            for($i=0;$i<$newcnt;$i++){
                $new_val[$i]=sanitize_str($_REQUEST["value"][$i]);
                $deleted[$i]=intval($_REQUEST["chk"][$i]);
                $prefer[$i]=floatval($_REQUEST["preference"][$i]);
            }
            
            $j=0;
            for($i=0;$i<$cnt;$i++){
                if(!isset($prefer[$i]) || check_input($prefer[$i]) == EMPTY_QUERY){
                    $error=1;
                    $xml_output .= "<edit_attribute><status>FAIL</status><error>PREFERENCES_EMPTY</error></edit_attribute>";
            	      die( "$xml_output" );
                }
                if($prefer[$i]<0 || $prefer[$i]>100){
                    $error=1;
                    $xml_output .= "<edit_attribute><status>FAIL</status><error>PREFERENCE_OUT_OF_BOUNDS</error></edit_attribute>";
            	      die( "$xml_output" );
                }
                if(check_input($new_val[$i]) == EMPTY_QUERY){
                    $error=1;
                    $xml_output .= "<edit_attribute><status>FAIL</status><error>VALUES_EMPTY</error></edit_attribute>";
            	      die( "$xml_output" );

                }
                if((strcmp($old_val[$i], $new_val[$i])!=0) || $old_prefer[$i]!=$prefer[$i] || $deleted[$i]==1){
                    $info->type_elements[$j]["Old"]=$old_val[$i];
                    $info->type_elements[$j]["Pref"]=$prefer[$i];

                    //check if distinct value has been deleted
                    if($deleted[$i]==1)
                        $info->type_elements[$j]["New"]=NULL;
                    //check if distinct value has been edited
                    else if(strcmp($new_val[$i],$old_val[$i])!=0)
                        $info->type_elements[$j]["New"]=$new_val[$i];
                    //check if only preference has been changed
                    else if($old_prefer[$i] != $prefer[$i])
                        $info->type_elements[$j]["New"]=$old_val[$i];

                    $j+=1;
                }
            }

            if($error==0){
                if($newcnt>$cnt){
                    for($i=$cnt;$i<$newcnt;$i++){
                        if(!isset($prefer[$i]) || check_input($prefer[$i]) == EMPTY_QUERY){
                            $error=1;
                            $xml_output .= "<edit_attribute><status>FAIL</status><error>PREFERENCES_EMPTY</error></edit_attribute>";
            	      		die( "$xml_output" );                        
			   }
                        if($prefer[$i]<0 || $prefer[$i]>100){
                            $error=1;
                            $xml_output .= "<edit_attribute><status>FAIL</status><error>PREFERENCE_OUT_OF_BOUNDS</error></edit_attribute>";
            	      		die( "$xml_output" );

                        }
                        if(check_input($new_val[$i]) == EMPTY_QUERY){
                            $error=1;
                            $xml_output .= "<edit_attribute><status>FAIL</status><error>VALUES_EMPTY</error></edit_attribute>";
            	      		die( "$xml_output" );                        
		          }
                        $info->type_elements[$j]["Old"]=NULL;
                        $info->type_elements[$j]["New"]=$new_val[$i];
                        $info->type_elements[$j]["Pref"]=$prefer[$i];

                        $j+=1;
                    }
                }

            }
            
        }

        else if($comp == COUNTABLE){

            //check if Upper Limit has been sent
            if(isset($_REQUEST["upper_limit"])){
                $ul = floatval($_REQUEST["upper_limit"]);
                //if empty echo xml error
                if (check_input($ul) == EMPTY_QUERY){
                    $xml_output .= "<edit_attribute><status>FAIL</status><error>UPPER_LIMIT_NOT_SET</error></edit_attribute>";
            	      		die( "$xml_output" );
                }
            }
            else{
                $xml_output .= "<edit_attribute><status>FAIL</status><error>UPPER_LIMIT_NOT_SET</error></edit_attribute>";
            	      		die( "$xml_output" );

            }

            //check if Lower Limit has been sent
            if(isset($_REQUEST["lower_limit"])){
                $ll = floatval($_REQUEST["lower_limit"]);
                //if empty echo xml error
                if (check_input($ll) == EMPTY_QUERY){
                    $xml_output .= "<edit_attribute><status>FAIL</status><error>LOWER_LIMIT_NOT_SET</error></edit_attribute>";
            	      		die( "$xml_output" );
;
                }
            }
            else{
                $xml_output .= "<edit_attribute><status>FAIL</status><error>LOWER_LIMIT_NOT_SET</error></edit_attribute>";
            	      		die( "$xml_output" );
            }
            if($ll>=$ul){
                $xml_output .= "<edit_attribute><status>FAIL</status><error>LOWER_LIMIT_NOT_BIGGER_THAN_UPPER_LIMIT</error></edit_attribute>";
            	      		die( "$xml_output" );
            }

            //check if comparison has been sent
            if(isset($_REQUEST["comparison"])){
                $comparison = intval($_REQUEST["comparison"]);
                //if empty echo xml error
                if (check_input($comparison) == EMPTY_QUERY || ($comparison!=BIG_IS_BETTER && $comparison!=LOWER_IS_BETTER && $comparison!=MIDDLE_VALUE)){
                    $xml_output .= "<edit_attribute><status>FAIL</status><error>COMPARISON_NOT_SET</error></edit_attribute>";
            	      		die( "$xml_output" );

                }
            }
            else{
                $xml_output .= "<edit_attribute><status>FAIL</status><error>COMPARISON_NOT_SET</error></edit_attribute>";
            	      		die( "$xml_output" );
            }

            $info->type_elements["Upper Limit"]=$ul;
            $info->type_elements["Lower Limit"]=$ll;
            $info->type_elements["Comparison Type"]=$comparison;
        }
        
        if($error==0){
            $categ->set_specific_attribute($info);
            if($categ->get_errno() != DB_OK){
                $xml_output .= "<edit_attribute><status>FAIL</status><error>MYSQL_ERROR</error></edit_attribute>";
            	  die( "$xml_output" );
            }
            else {
                $xml_output .= "<edit_attribute><status>SUCCESS</status></edit_attribute>";
            	  die( "$xml_output" );
	     }
        }
?>
