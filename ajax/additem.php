<?php
        session_start();
        include_once ("../db_includes/DB_DEFINES.php");
        include_once ("../db_includes/attribute.php");
        include_once ("../db_includes/user.php");
        include_once ("../db_includes/entity.php");     
        include_once ("../db_includes/category.php");


        include_once ("../logic_includes/LOGIC_DEFINES.php");
        include_once ("../logic_includes/logic_functions.php");
        include_once ("../fileSystem2.php");
        
////////////////////////////////////////////////////////////////////////////////
        //check if category id has been sent
        if(isset($_REQUEST["cat_id"])){
            $catid = intval($_REQUEST["cat_id"]);
        }
        else{
            header("Location: ../index.php");
            return;
        }
        $categ = new Category($catid);
        if ($categ->get_errno() == CATEGORY_DONT_EXIST){
            header("Location: ../index.php");
            return;
        }
////////////////////////////////////////////////////////////////////////////////
        //check if user has the correct privileges

        if(!isset($_SESSION["username"])){
            header("Location: ../index.php");
            return;    
        }
        $user=$_SESSION["username"];

        $categ->can_access($user, EDITOR_MEMBER);
        if($categ->get_errno != DB_OK || !ISSET($_SESSION["username"])){
            header("Location: ../index.php");
            return;
        }
        

////////////////////////////////////////////////////////////////////////////////
        //check if entity name has been sent
        if(isset($_REQUEST["name"])){
            $name = sanitize_str_disp($_REQUEST["name"]);

            
            if (check_input($name) == EMPTY_QUERY){
                header("Location: ../manageEntities.php?cat_id=".$catid."&mode=2");
                return;
            }
        }
        else{
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=2");
            return;
        }

////////////////////////////////////////////////////////////////////////////////
        //check if description has been sent
        if(isset($_REQUEST["desc"])){
            $desc = sanitize_str_disp($_REQUEST["desc"]);

            if (check_input($desc) == EMPTY_QUERY){
                $desc = NULL;
            }
        }
        else{
            $desc = NULL;
        }


////////////////////////////////////////////////////////////////////////////////
        //check if video has been sent
        if(isset($_REQUEST["vid"])){
            $video = sanitize_str($_REQUEST["vid"]);
            //if empty echo xml error
            if (empty($video)){
                $video = NULL;
            }
        }
        else{
            $video = NULL;
        }

////////////////////////////////////////////////////////////////////////////////

        //Get number of attributes from db
        //$categ = new Category($catid);
        //if ($categ->get_errno() == CATEGORY_DONT_EXIST){
        //    echo "Category doesn't exist";
        //    return;
        //}

        $attrs_array = $categ->get_attributes(0);
        $attrnum = intval(count($attrs_array));
////////////////////////////////////////////////////////////////////////////////
        //na balw elegxo gia to range twn timwn!!!!!!!!!!!!!!!!!!!!!!!!!!
        //Check if attribute numbers match
        if($attrnum!=intval(count($_REQUEST["attr"]))){
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=3");
            return;
        }

        //Check if all attributes have been sent
        for($i=0;$i<$attrnum;$i++){
            if(isset($_REQUEST["attr"][$i])){
                if($attrs_array[$i]->comparability == COUNTABLE){
                    $attrs[$attrs_array[$i]->id] =floatval($_REQUEST["attr"][$i]);
                    if($attrs[$attrs_array[$i]->id]<$attrs_array[$i]->type_elements["Lower Limit"] || $attrs[$attrs_array[$i]->id]>$attrs_array[$i]->type_elements["Upper Limit"]){
                        header("Location: ../manageEntities.php?cat_id=".$catid."&mode=4");
                        return;
                    }
                }
                //$value=sprintf("\"%s\"", $attrs_array[$i]->id);
                //$value="\"".$attrs_array[$i]->id."\"";
                else
                    $attrs[$attrs_array[$i]->id] = sanitize_str_disp($_REQUEST["attr"][$i]);
                
                if (check_input($_REQUEST["attr"][$i]) == EMPTY_QUERY){
                    $attrs[$attrs_array[$i]->id]=NULL;
                }
            }
            else{
                $attrs[$attrs_array[$i]->id]=NULL;
            }
        }

////////////////////////////////////////////////////////////////////////////////
        //check if image has been sent

        $image=addEntityImage("img", $_FILES);
 	echo $image;die(1);
////////////////////////////////////////////////////////////////////////////////
        //Send to database

        $info = new entity_info();
        if($categ->get_errno() != DB_OK){
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=14");
            return;
        }

        $info->entity_name=$name;
        $info->entity_description=$desc;
        $info->entity_attribute_values=$attrs;
        $info->entity_image=$image;
        $info->entity_video=$video;
        $info_array[] = $info;
        /*
        echo $info->entity_name;
        echo"<br />";
        echo $info->entity_description;
        echo"<br />";
        for($i=0;$i<intval(count($info->entity_attribute_values));$i++){
            echo $info->entity_attribute_values[$i][0];
            echo $info->entity_attribute_values[$i][1];
            echo"<br />";
        }
        */
        /*mode = 0 -> empty name
         mode  = 1 -> logic error/dberror
         mode  = 2 -> out of limit 
         mode  = 3 -> entity exists
         */
        $categ->add_entities($info_array);
        if($categ->get_errno() == ENTITY_EXISTS){
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=16");
            return;
        }
        else if($categ->get_errno() != DB_OK){
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=14");
            return;
        }
        else{
            $new_ent_id = $categ->get_ent_id_by_name($name);
            if($categ->get_errno() != DB_OK){
                header("Location: ../manageEntities.php?cat_id=".$catid."&mode=14");
                return;    
            }
            if(!isset($_SESSION["added_ents"])){
                $_SESSION["added_ents"][$catid] = array($new_ent_id);
            }
            else {
                if(isset($_SESSION["added_ents"][$catid])){
                    $this_last = $_SESSION["added_ents"][$catid];
                    $this_last[]=$new_ent_id;
                    $_SESSION["added_ents"][$catid]=$this_last;
                }
                else{
                    $_SESSION["added_ents"][$catid] = array($new_ent_id);    
                }
            }
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=7");
            return;
        }
        
////////////////////////////////////////////////////////////////////////////////
?>
