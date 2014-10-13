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
|   Description: LOGIC Part for page manage Entities.                       **|
|        Lines : 128                                                        **|   
\*****************************************************************************/


class manageEntities{
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
        $sort_by = SORT_ALPHABETICAL;
        $ret_code = -10;
        $added_ents = array();

        if(isset($_REQUEST["sort"])){
            $sort_by = intval($_REQUEST["sort"]);
            if($sort_by != 0 && $sort_by != 1)
                $sort_by = SORT_ALPHABETICAL;
        }
        if(!isset($_REQUEST["cat_id"]))
        {
            $this->content = createMgEnMessage("You are not allowed to be here", null, 1, 0);
            return;
        }
        else{
            $categ_id = intval($_REQUEST["cat_id"]);
        }
        
        if(isset($_REQUEST["mode"])){
            $ret_code = intval($_REQUEST["mode"]);
        }

        
        if(isset($_SESSION["added_ents"])){
            if(isset($_SESSION["added_ents"][$categ_id]))
                $added_ents = $_SESSION["added_ents"][$categ_id];
        }

        if(!isset($_SESSION["username"]))
        {
            $this->content = createMgEnMessage("You are not allowed to be here", $categ_id, 1, 0);
            return;
        }
        else{
            $username_ = $_SESSION["username"];
        }


        $categ = new category($categ_id);
        if($categ->get_errno() != DB_OK){
            // error
            $this->content = createMgEnMessage("Category does not exists", null, 1, 0);
            return;
        }
        
        $categ_name ="-";
        $categ_name = $categ->get_name();
        if($categ->get_errno() != DB_OK){
            $this->content = createMgEnMessage("Intenal DB Error happened sorry , try again", $categ_id, 1, 0);
            return;      
        }

        $can_manage = 0;
        $priv = -2;
        $ret = $categ->get_user_rights($username_);
        if($categ->get_errno() != DB_OK){
            $this->content = createMgEnMessage("Intenal DB Error happened sorry , try again", $categ_id, 1, 0);
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
        else if($priv == LOGIC_EDITOR_MEMBER ||
                $priv == LOGIC_SUB_MODERATOR ||
                $priv == LOGIC_MODERATOR)
        {
            $can_manage = 1;
        }
        if($can_manage == 0){
            $this->content = createMgEnMessage("You are not allowed to Manage Entities for category <b>".$categ_name."</b>", $categ_id, 1, 0);
            return;       
        }
       
        /* fortwnw ola ta entities pou exei i katigoria*/
        if($sort_by == 0)
            $entities = $categ->get_N_entities(1, -1, SORT_ALPHABETICAL+1, "ASC", null, -1);
        else if($sort_by == 1)
            $entities = $categ->get_N_entities(1, -1, SORT_POPULARITY+1, "DESC", null, -1);
        
        $this->content  = "<div id=\"mainContainer\" align=\"left\">";
        $this->content .= createMgEnMessage("Welcome ".$username_." , here you can manage entities for category ".$categ_name, $categ_id, 0, 1);     
        $this->content .= createMgEnPanel($entities,$categ_id,$priv,$added_ents,$ret_code);
        $this->content .= "</div>";

    }

    public function get_content() {
        return $this->content;
    }
};
?>
