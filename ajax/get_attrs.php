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
        }
        else{
            echo "No Category Selected";
            return;
        }
        
        
	 
        $categ = new Category($catid);
        if ($categ->get_errno() != DB_OK) {
            echo "Database Error";
            return;
        }
        
        $user = " ";
        if(isset($_SESSION["username"])){
            $user = $_SESSION["username"];
        }

        $categ->can_access($user, EDITOR_MEMBER);
        if($categ->get_errno() != DB_OK){
            echo "You are not allowed here</br>";
            return;
        }

        $attrs_array = $categ->get_attributes(0);
        if ($categ->get_errno() != DB_OK) {
            echo "Database Error";
            return;
        }
        
        renderAddEntityForm($catid, $attrs_array);
?>
