<?php
        session_start();
        include_once ("../db_includes/DB_DEFINES.php");
        include_once ("../db_includes/attribute.php");
        include_once ("../db_includes/user.php");
        include_once ("../db_includes/entity.php");     
        include_once ("../db_includes/category.php");


        include_once ("../logic_includes/LOGIC_DEFINES.php");
        include_once ("../logic_includes/logic_functions.php");


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
        //check if delete var has been sent
        if(isset($_REQUEST["delete"])){
            $del = intval($_REQUEST["delete"]);
        }
        else{
            header("Location: ../index.php");
            return;
        }

////////////////////////////////////////////////////////////////////////////////
        //check if user has the correct privileges

        $user=$_SESSION["username"];
        if(!ISSET($_SESSION["username"])){
            header("Location: ../index.php");
            return;
        }
        if($del==1)
            $categ->can_access($user, EDITOR_MEMBER);
        else
            $categ->can_access($user, SUB_MODERATOR);
        if($categ->get_errno() != DB_OK){
            header("Location: ../index.php");
            return;
        }
        
////////////////////////////////////////////////////////////////////////////////
        //check if entity id has been sent
        if(isset($_REQUEST["ent_id"])){
            $entid = intval($_REQUEST["ent_id"]);
        }
        else{
            header("Location: ../index.php");
            return;
        }

////////////////////////////////////////////////////////////////////////////////

        //$categ = new Category($catid);
//        if ($categ->get_errno() == CATEGORY_DONT_EXIST){
//            echo "Category doesn't exist";
//            return;
//        }
        $tmp = array();
        $tmp[] = $entid;
        $categ->remove_entities($tmp);
        if (($ret = $categ->get_errno()) != DB_OK){
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=13");
            return;
        }
        else{
            if(isset($_SESSION["added_ents"])){
                if(isset($_SESSION["added_ents"][$catid])){
                    $this_last = $_SESSION["added_ents"][$catid];
                    if(in_array($entid,$this_last)){
                        $key = array_search($entid,$this_last);
                            unset($this_last[$key]);
                    }
                    if(!empty($this_last))
                        $_SESSION["added_ents"][$catid] = $this_last;
                }
            }
                       
            header("Location: ../manageEntities.php?cat_id=".$catid."&mode=10");
            return;
        }
?>