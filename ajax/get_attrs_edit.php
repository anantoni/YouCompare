<?php
	session_start();
        include_once ("../db_includes/DB_DEFINES.php");
        include_once ("../db_includes/attribute.php");
        include_once ("../db_includes/user.php");
        include_once ("../db_includes/entity.php");     
        include_once ("../db_includes/category.php");
        
        include_once ('./LOGIC_DEFINES.php');
        include_once ('./logic_functions.php');
        include_once ('./renderFunctions.php');

        
        if(isset($_REQUEST["cat_id"])){
            $catid = intval($_REQUEST["cat_id"]); //check to be added
            //if empty echo error
        }
        else{
            echo "No category given</br>";
            return;
        }

        if(isset($_REQUEST["ent_id"])){
            $entid = intval($_REQUEST["ent_id"]); //check to be added
            //if empty echo error
        }
        else{
            echo "No entity to edit</br>";
            return;
        }
        
        $mode = 0;
        if(isset($_REQUEST["qedit"])){
            $mode = intval($_REQUEST["qedit"]);
        }
        
        $categ = new Category($catid);
        if ($categ->get_errno() != DB_OK) {
            //error;
            echo "Category does not exist</br>";
            return;
        }

        $user = " ";
        if(isset ($_SESSION["username"])){
            $user = $_SESSION["username"];
        }
        
        if($mode == 1){
            $categ->can_access($user, EDITOR_MEMBER);
            if($categ->get_errno() != DB_OK){
                echo "You are not allowed here</br>";
                return;
            }
        }
        else{
           $categ->can_access($user, SUB_MODERATOR);
            if($categ->get_errno() != DB_OK){
                echo "You are not allowed here</br>";
                return;
            } 
        }
        
        $attrs_array = $categ->get_attributes(0);
        if ($categ->get_errno() != DB_OK) {
            echo "Internal db error</br>";
            return;
        }

        $attrs_values = $categ->get_specific_entity( $entid );
        if ($categ->get_errno() != DB_OK) {
            echo "Internal db error</br>";
            return;
        }
        renderEditEntityForm($catid, $entid, $attrs_array, $attrs_values);
?>
