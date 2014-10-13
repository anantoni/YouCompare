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
        //check if edit var has been sent
        $edit = 0;
        if(isset($_REQUEST["edit"])){
            $edit = intval($_REQUEST["edit"]);
        }

////////////////////////////////////////////////////////////////////////////////
        //check if user has the correct privileges
        
        if(isset($_SESSION["username"]))
            $user=$_SESSION["username"];
        else{
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=1");
            return;
        }
            

        if($edit==1)
            $categ->can_access($user, EDITOR_MEMBER);
        else
            $categ->can_access($user, SUB_MODERATOR);
        
        if($categ->get_errno() != DB_OK ){
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=1");
            return;
        }
        
////////////////////////////////////////////////////////////////////////////////
        //check if entity id has been sent
        if(isset($_REQUEST["ent_id"])){
            $entid = intval($_REQUEST["ent_id"]);
        }
        else{
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=9");
            return;
        }

////////////////////////////////////////////////////////////////////////////////
        //check if entity name has been sent
        if(isset($_REQUEST["name"])){
            $name = sanitize_str_disp($_REQUEST["name"]);
            //if empty echo xml error
            if (check_string($_REQUEST["name"], 1, 25) != LOGIC_OK){
                header("Location: ../manageEntities.php?cat_id=".$catid."&mode=11");
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
            
            $chk=check_string($_REQUEST["desc"], 0, 200);
            if ($chk == BAD_INPUT || $chk==SHORT_INPUT){
                $desc = NULL;
            }
            else if($chk==LONG_INPUT)
                $desc = substr($desc, 0, 196)."...";
        }
        else{
            $desc = NULL;
        }

        //ean set send sto file system kai dwse link sto $image
////////////////////////////////////////////////////////////////////////////////
        //check if video has been sent
        if(isset($_REQUEST["vid"])){
            $video = sanitize_str_disp($_REQUEST["vid"]);
            
            if (check_string($_REQUEST["vid"], 0, 100) != LOGIC_OK){
                $video = NULL;
            }
        }
        else{
            $video = NULL;
        }

////////////////////////////////////////////////////////////////////////////////

        //Get number of attributes from db
        //$categ = new Category($catid);
//        if ($categ->get_errno() == CATEGORY_DONT_EXIST){
//            echo "Category doesn't exist";
//            return;
//        }

        $attrs_array = $categ->get_attributes(0);
        $attrnum = intval(count($attrs_array));
////////////////////////////////////////////////////////////////////////////////
     
        //Check if attribute numbers match
        if($attrnum!=intval(count($_REQUEST["attr"]))){
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=3");
            return;
        }

        //Check if all attributes have been sent
        for($i=0;$i<$attrnum;$i++){
            if(isset($_REQUEST["attr"][$i])){
                $chk=check_string($_REQUEST["attr"][$i], 0, 25);
                if($attrs_array[$i]->comparability == COUNTABLE){
                    $attrs[$attrs_array[$i]->id] = floatval($_REQUEST["attr"][$i]);
                    if($attrs[$attrs_array[$i]->id]<$attrs_array[$i]->type_elements["Lower Limit"] || $attrs[$attrs_array[$i]->id]>$attrs_array[$i]->type_elements["Upper Limit"]){
                        header("Location: ../manageEntities.php?cat_id=".$catid."&mode=4");
                        return;
                    }
                }
                //$value=sprintf("\"%s\"", $attrs_array[$i]->id);
                //$value=$attrs_array[$i]->id;
                else{
                    $attrs[$attrs_array[$i]->id] = sanitize_str_disp($_REQUEST["attr"][$i]);

                    if($chk!= LOGIC_OK && $chk!= BAD_INPUT){
                        header("Location: ../manageEntities.php?cat_id=".$catid."&mode=4");
                        return;
                    }
                }
                
                if($chk == BAD_INPUT){
                        $attrs[$attrs_array[$i]->id]=NULL;
                }
            }
            else{
                $attrs[$attrs_array[$i]->id]=NULL;
            }
        }

////////////////////////////////////////////////////////////////////////////////
        //check if image has been sent
   
        $attrs_values = $categ->get_specific_entity( $entid );
        if ($categ->get_errno() != DB_OK) {
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=15");
            return;
        }
        //$image=$attrs_values->entity_image;

        $image=addEntityImage("img", $_FILES);
        $image_set=0;
        if(empty($image))
            $image=$attrs_values->entity_image;
        /*else{
            $image_set=1;
            deleteImage($attrs_values->entity_image);
        }*/



////////////////////////////////////////////////////////////////////////////////
        //Send to database
        
        $info = new entity_info();
        if($categ->get_errno() != DB_OK){
            /*if($image_set==1)
                deleteImage($image);*/
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=15");
            return;
        }

        $info->entity_id=$entid;
        $info->entity_name=$name;
        $info->entity_description=$desc;
        $info->entity_attribute_values=$attrs;
        $info->entity_image=$image;
        $info->entity_video=$video;
        //$info_array[] = $info;
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
        $categ->set_specific_entity($info);
        
        if($categ->get_errno() != DB_OK){
            /*if($image_set==1)
                deleteImage($image);*/

            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=15");
            return;
        }
        else{
            
            $new_ent_id = $entid;
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
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=12");
            return;
        }

////////////////////////////////////////////////////////////////////////////////
?>