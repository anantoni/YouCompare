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
|   Description: LOGIC Part for page manage Users.                          **|
|        Lines : 152                                                        **|   
\*****************************************************************************/


class manageUsers{
    private $content;

        
    public function create_content() {
  

        include_once ("./db_includes/DB_DEFINES.php");
        include_once ("./db_includes/attribute.php");
        include_once ("./db_includes/user.php");
        include_once ("./db_includes/entity.php");     
        include_once ("./db_includes/category.php");


        include_once ("./logic_includes/LOGIC_DEFINES.php");
        include_once ("./logic_includes/logic_functions.php");
        include_once ("./logic_includes/renderFunctions.php");

        $username_ = "";
        $categ_id  = -1;
        
        if(!isset($_REQUEST["cat_id"]))
        {
            $this->content = createMgUsMessage("You are not allowed to be here", null, 1, 0);
            return;
        }
        else{
            $categ_id = intval($_REQUEST["cat_id"]);
        }
        

        if(!isset($_SESSION["username"]))
        {
            $this->content = createMgUsMessage("You are not allowed to be here", $categ_id, 1, 0);
            return;
        }
        else{
            $username_ = $_SESSION["username"];
        }


        $categ = new category($categ_id);
        if($categ->get_errno() != DB_OK){
            // error
            $this->content = createMgUsMessage("Category does not exists", null, 1, 0);
            return;
        }

        $can_manage = 0;
        $priv = -2;
        $ret = $categ->get_user_rights($username_);
        if($categ->get_errno() != DB_OK){
            $this->content = createMgUsMessage("Intenal DB Error happened sorry , try again", $categ_id, 1, 0);
            return;    
        }
        if (!is_null($ret)) {
            $priv = intval($ret);
            if($priv != intval(LOGIC_ADMINISTRATOR)){
                $priv = $priv+1; // ... db saves them from 0 to 3 i save from 1 to 4
            }
        }
        if($priv == LOGIC_ADMINISTRATOR){
            $can_manage = 1;   
        }
        else if($priv == LOGIC_MODERATOR)
        {
            $can_manage = 1;
        }
        if($can_manage == 0){
            $this->content = createMgUsMessage("You are not allowed to Manage users for this category", $categ_id, 1, 0);
            return;       
        }
       
        $categ_name ="-";
        $categ_name = $categ->get_name();
        if($categ->get_errno() != DB_OK){
            $this->content = createMgUsMessage("Intenal DB Error happened sorry , try again", $categ_id, 1, 0);
            return;      
        }
        
        /* fwrtwnw tous members autis tis katigorias */
        $category_members   = $categ->get_privileged_users(CATEGORY_MEMBER);
        if($categ->get_errno() != DB_OK){
            $this->content = createMgUsMessage("Intenal DB Error happened sorry , try again", $categ_id, 1, 0);
            return;    
        }
        if(empty($category_members))
            $category_members = array();
        
        $editor_members     = $categ->get_privileged_users(EDITOR_MEMBER);
        if($categ->get_errno() != DB_OK){
            $this->content = createMgUsMessage("Intenal DB Error happened sorry , try again", $categ_id, 1, 0);
            return;    
        }
        if(empty($editor_members))
            $editor_members = array();
        
        $sub_mods_members   = $categ->get_privileged_users(SUB_MODERATOR);
        if($categ->get_errno() != DB_OK){
            $this->content = createMgUsMessage("Intenal DB Error happened sorry , try again", $categ_id, 1, 0);
            return;    
        }
        if(empty($sub_mods_members))
            $sub_mods_members = array();
        
        $mods_members       = $categ->get_privileged_users(MODERATOR);
        if($categ->get_errno() != DB_OK){
            $this->content = createMgUsMessage("Intenal DB Error happened sorry , try again", $categ_id, 1, 0);
            return;    
        }
        if(empty($mods_members ))
            $mods_members  = array();
        
        $active_users = array();
        $active_users[] = $category_members;
        $active_users[] = $editor_members;
        $active_users[] = $sub_mods_members;
        $active_users[] = $mods_members;
        
        /* get all requested users */
        $pending_users = $categ->get_pending_members();
        if($categ->get_errno() != DB_OK){
            $this->content = createMgUsMessage("Intenal DB Error happened sorry , try again", $categ_id, 1, 0);
            return;     
        }
        if(empty($pending_users))
            $pending_users = array();
        
	
        $this->content = "<div id=\"mainContainer\" align=\"left\">";
        $this->content .= createMgUsMessage("Welcome ".$username_." , here you can Manage Users for category <b>".$categ_name."</b>", $categ_id, 0, 1);     
        $this->content .= createMgUsPanel($active_users,$pending_users,$categ_id);
        $this->content .="</div>";
        return;

    }

    public function get_content() {
        return $this->content;
    }
};
?>
