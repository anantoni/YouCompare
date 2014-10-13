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
|   Description: LOGIC Part for page become Member.                         **|
|        Lines : 115                                                        **|   
\*****************************************************************************/



class becomeMember{
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
            $this->content = createMemberMessage("You are not allowed to be here", null, 1, 0);
            return;
        }
        else{
            $categ_id = intval($_REQUEST["cat_id"]);
        }

        if(!isset($_SESSION["username"]))
        {
            $this->content = createMemberMessage("You are not allowed to be here", $categ_id, 1, 0);
            return;
        }
        else{
            $username_ = $_SESSION["username"];
        }

 

        // create user 
        $user = new user($username_,LOGGED_IN);
        if($user->get_errno() != DB_OK){
            // error
            $this->content = createMemberMessage("You are not allowed to be here", $categ_id, 1, 0);
            return;
        }

        $categ = new category($categ_id);
        if($categ->get_errno() != DB_OK){
            // error
            $this->content = createMemberMessage("You are not allowed to be here", $categ_id, 1, 0);
            return;
        }

        $priv =-2;
        $ret = $categ->get_user_rights($username_);
        if($categ->get_errno() != DB_OK){
            $this->content = createMemberMessage("Intenal DB Error happened sorry , try again", $categ_id, 1, 0);
            return;    
        }
        if (!is_null($ret)) {
            $priv = intval($ret);
            if($priv != intval(LOGIC_ADMINISTRATOR)){
                $priv = $priv+1; // ... db saves them from 0 to 3 i save from 1 to 4
            }
        }
        
        if($priv == LOGIC_ADMINISTRATOR){
            $this->content = createMemberMessage("You are THE Administrator!!", $categ_id, 1, 1);
            return;    
        }
        else if($priv == LOGIC_CATEGORY_MEMBER || $priv== LOGIC_EDITOR_MEMBER || $priv == LOGIC_SUB_MODERATOR
                || $priv == LOGIC_MODERATOR)
        {
            $this->content = createMemberMessage("You are already Member of this Category!!", $categ_id, 1, 1);
            return;   
        }
        

        // ean den einai member alla hdh exei pending
        if($user->pendingMembReq($categ_id)){
            $this->content = createMemberMessage("Your Request is not accepted yet", $categ_id, 1, 1);
            return;
        }

        $user->becomeMemberReq($categ_id); 
        if($user->get_errno() == DB_OK){
            $this->content = createMemberMessage("Your Request is submited, in a while a moderator will approve you !!!", $categ_id, 1, 1);
            return;    
        }
        else{
            $this->content = createMemberMessage("Intenal Error happened sorry , try again", $categ_id, 1, 0);
            return;
        }
    }

    public function get_content() {
        return $this->content;
    }
};
?>
