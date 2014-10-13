<?php

        include_once ("../db_includes/DB_DEFINES.php");
        include_once ("../db_includes/attribute.php");
        include_once ("../db_includes/user.php");
        include_once ("../db_includes/entity.php");     
        include_once ("../db_includes/category.php");
        
        include_once ('./LOGIC_DEFINES.php');
        include_once ('./logic_functions.php');


        
////////////////////////////////////////////////////////////////////////////////

        
        //check if category id has been sent
        if(isset($_REQUEST["cat_id"])){
            $catid = intval($_REQUEST["cat_id"]);
        }
        else{
            echo "0";
        }
        $categ = new Category($catid);
        if ($categ->get_errno() == CATEGORY_DONT_EXIST){
            echo "0";
        }

////////////////////////////////////////////////////////////////////////////////
        //check if name has been sent
        if(isset($_REQUEST["name"])){
            $name = sanitize_str_disp($_REQUEST["name"]);
        }
        else{
            echo "0";
        }

////////////////////////////////////////////////////////////////////////////////

        $categ->get_ent_id_by_name($name);
        if ($categ->get_errno() == DB_OK){
            echo "0";
        }
        else{
            echo "1";
        }
?>
