<?php

/*****************************************************************************\
|   Created By : Antonopoulos Spyridon  1115 2006 00048                     **|
|   contact me : sdi0600048@di.uoa.gr                                       **|
|       Project: YouCompare Site - Software Engineering Course Spring 2011  **|
|   Description: called to manipulate ratings to entity or category         **|
|        Lines : 189                                                        **|   
\*****************************************************************************/
    session_start();
  
    include_once ("../db_includes/DB_DEFINES.php");
    include_once ("../db_includes/attribute.php");
    include_once ("../db_includes/user.php");
    include_once ("../db_includes/entity.php");     
    include_once ("../db_includes/category.php");

    
    $cat_id_ = -1;
    $ent_id_ = -1;
    $mode_   = -1; /*0 means rate category, 1 means rate entity */
    $rank = 0;
    $username_ ="";
    
    header('Content-type: text/xml');
    $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    $xml .= "<result>";
    $status_err = "<status>-1</status></result>";
 
    if(isset($_REQUEST["rating"])){
        $rank = floatval($_REQUEST["rating"]);
        if ($rank < 0)
            $rank =0;
        if($rank > 10)
            $rank = 10;
    }
    else{
        $xml.=$status_err;
        echo $xml;
        return;
    }
    if(isset($_SESSION["username"])){
        $username_ = $_SESSION["username"];
    }
    else{
        $xml.=$status_err;
        echo $xml;
        return;
    }
    
    // create user
    if (isset($_REQUEST["mode"])){
        $mode_ = $_REQUEST["mode"];
    }
    else{
        $xml.=$status_err;
        echo $xml;
        return;
    }
    
    if (isset($_REQUEST["cat_id"])){
        $cat_id_ = $_REQUEST["cat_id"];
    }
    else{
        $xml.=$status_err;
        echo $xml;
        return;
    }
    
    if (isset($_REQUEST["ent_id"])){
        $ent_id_ = $_REQUEST["ent_id"];
    }
    else {
        if($mode != 0) {
            $xml.=$status_err;
            echo $xml;
            return;
        }
    }
    
    if($mode_ == 0) {
        // cat_id
        $categ = new category($cat_id_);
        if ($categ->get_errno() != DB_OK){
            $xml.=$status_err;
            echo $xml;
            return;
        }
        
        //
        if($categ->is_open() == 0){
            $categ->can_access($username_, CATEGORY_MEMBER);
            if($categ->get_errno() != DB_OK)
            {
                $xml.=$status_err;
                echo $xml;
                return;
            }
            
        }
        
        if($categ->has_category_been_rated($username_) != 0)
        {
            $xml.=$status_err;
            echo $xml;
            return;
        }
            
        // mporei na tin kanei rate
        $this_user = new user($username_,LOGGED_IN);
        if($this_user->get_errno() != DB_OK)
        {
            $xml.=$status_err;
            echo $xml;
            return;
        }
        
        $this_user->rate_category($cat_id_, $rank);
        if($this_user->get_errno() != DB_OK)
        {
            $xml.=$status_err;
            echo $xml;
            return;
        }
        
        $categ_new = new category($cat_id_);
        
        $xml.="<status>".floatval(round($categ_new->get_rating(),2))."</status></result>";
        echo $xml;
        return;
    }
    else if($mode == 1){
        // rates entity
        // cat_id
        // ent_id
        $categ = new category($cat_id_);
        if ($categ->get_errno() != DB_OK){
            $xml.=$status_err;
            echo $xml;
            return;
        }
        
        //
        if($categ->is_open() == 0){
            $categ->can_access($username_, CATEGORY_MEMBER);
            if($categ->get_errno() != DB_OK)
            {
                $xml.=$status_err;
                echo $xml;
                return;
            }
            
        }
        
        if($categ->has_entity_been_rated($ent_id_, $username_) != 0)
        {
            $xml.=$status_err;
            echo $xml;
            return;
        }
            
        // mporei na tin kanei rate
        $this_user = new user($username_,LOGGED_IN);
        if($this_user->get_errno() != DB_OK)
        {
            $xml.=$status_err;
            echo $xml;
            return;
        }
        
        $this_user->rate_entity($ent_id_, $rank);
        if($this_user->get_errno() != DB_OK)
        {
            $xml.=$status_err;
            echo $xml;
            return;
        }
        
        $new_ent = new category($cat_id_);
	 $xml.="<status>".floatval(round($new_ent->get_specific_entity($ent_id)->rate,2))."</status></result>";
        echo $xml;
        return;
    }
    else{
        $xml.=$status_err;
        echo $xml;
        return;   
    }
        
    

    
?>
