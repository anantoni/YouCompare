<?php
/*****************************************************************************\
|        \  ,  /                                                            **|
|     ' ,___/_\___, '           *****         ******                        **|            
|        \ /o.o\ /              *    *          **                          **|
|    -=   > \_/ <   =-          *     *         **                          **|
|        /_\___/_\              *    *          **                          **|
|     . `   \ /   ` .           *****         ******                        **|
|         /  `  \                                                           **|
|_____________________________________________________________________________|
|   Created By : Antonopoulos Spyridon  1115 2006 00048                     **|
|   contact me : sdi0600048@di.uoa.gr                                       **|
|       Project: YouCompare Site - Software Engineering Course Spring 2011  **|
|   Description: LOGIC Part for change users priviledges                    **|
|        Lines : 159                                                        **|   
\*****************************************************************************/

    session_start();
    
    include_once ("../db_includes/DB_DEFINES.php");
    include_once ("../db_includes/attribute.php");
    include_once ("../db_includes/user.php");
    include_once ("../db_includes/entity.php");     
    include_once ("../db_includes/category.php");


    $mode = -1;
    $cat_id = -1;
    $username_ = " ";
    $priv = -1;
    $mod_username = " ";
    
    if(isset($_REQUEST["mode"])){
        $mode = intval($_REQUEST["mode"]);
    }
    
    if(isset($_REQUEST["cat_id"])){
        $cat_id = intval($_REQUEST["cat_id"]);
    }
    
    if(isset($_REQUEST["ch_username"])){
        $username_ = $_REQUEST["ch_username"];
    }
    
    if(isset($_REQUEST["priv"])){
        $priv = intval($_REQUEST["priv"]);
        if($priv < 0 || $priv > 3)
            return;
    }
    
    if(isset($_SESSION["username"])){
        $mod_username = $_SESSION["username"];
    }
    else{
        return;
    }
    
    if($mode == -1 || $cat_id == -1)
        return;
    
    
     // create user 
    $mod_user = new user($mod_username,LOGGED_IN);
    if($mod_user->get_errno() != DB_OK){
        // error
        return;
    }
    
    $categ = new category($cat_id);
    if($categ->get_errno() != DB_OK){
        // error
        return;
    }
   

    $categ->can_access($mod_username, MODERATOR);
    if($categ->get_errno() != DB_OK)
            return;
    
    // o moderator user einai verified , i katigoria uparxei 
    // opote analoga me to mode kanw tis energeies
    // approve pending prepei na exoun oristei priv,username_
    if ($mode == 0){
        if($priv == -1 || $username_ == " ")
            return;
        
        // create user mod obj
        $ch_user = new user($username_,LOGGED_IN);
        if($ch_user->get_errno() != DB_OK)
                return;
        
        $uid = $ch_user->get_id();
        
  
        // delete from pending
        // insert in user rights
        $mod_user->approveMemberReq($uid,$cat_id,$priv);
        if($mod_user->get_errno() != DB_OK)
                return;
        return;
    }
    else if($mode == 1){
        // delete pending membership
        if($username_ == " ")
            return;
        
        // create user mod obj
        $ch_user = new user($username_,LOGGED_IN);
        if($ch_user->get_errno() != DB_OK)
                return;
        
        $uid = $ch_user->get_id();
        
  
        // delete from pending
        $mod_user->deleteFromPending($uid,$cat_id);
        if($mod_user->get_errno() != DB_OK)
                return;
        return;   
    }
    else if($mode == 2){
        /* update member*/
        if($priv == -1 || $username_ == " ")
            return;
        
        // create user mod obj
        $ch_user = new user($username_,LOGGED_IN);
        if($ch_user->get_errno() != DB_OK)
                return;
        
        $uid = $ch_user->get_id();
        
  
        // delete from pending
        // insert in user rights
        $mod_user->updateMemberPriv($uid, $cat_id, $priv);
        if($mod_user->get_errno() != DB_OK)
                return;
        return;
    }
    else if($mode == 3){
        // delete member
        if($username_ == " ")
            return;
        
        // create user mod obj
        $ch_user = new user($username_,LOGGED_IN);
        if($ch_user->get_errno() != DB_OK)
                return;
        
        $uid = $ch_user->get_id();
        
  
        // delete from pending
        $mod_user->deleteMember($uid, $cat_id);
        if($mod_user->get_errno() != DB_OK)
                return;
        return;  
    }
    
    return;
   
    
?>
