<?php
/*****************************************************************************\
|   Created By : Antonopoulos Spyridon  1115 2006 00048                     **|
|   contact me : sdi0600048@di.uoa.gr                                       **|
|       Project: YouCompare Site - Software Engineering Course Spring 2011  **|
|   Description: called when add/remove entities from comparison            **|
|        Lines : 151                                                        **|   
\*****************************************************************************/
    session_start();
    
    include_once ("../db_includes/DB_DEFINES.php");
    include_once ("../db_includes/attribute.php");
    include_once ("../db_includes/user.php");
    include_once ("../db_includes/entity.php");     
    include_once ("../db_includes/category.php");


    $ent_id = "";
    $cat_id = "";
    $ent_ids = ""; // 3_4_5 /* for select all call mode = 3*/
    $mode   = -1;/*mode can be 0 add specific entity, 1 delete specific
                 *  2 delete all   ,3 select all,4 select all category's entities  
                 */

    
    if(isset($_REQUEST["ent_id"])){
        $ent_id = intval($_REQUEST["ent_id"]);   
    }
    
    if(isset($_REQUEST["cat_id"])){
        $cat_id = intval($_REQUEST["cat_id"]);
    }
    
    if(isset($_REQUEST["ent_ids"])){
        $ent_ids = $_REQUEST["ent_ids"];
    }
    
    if(isset($_REQUEST["mode"])){
        $mode   = intval($_REQUEST["mode"]);
    }
    else
        return;
   
    if(empty($cat_id))
        return;
    
    
    $categ = new Category($cat_id);
    if(!$categ->is_open()) { // if closed
        if(isset($_SESSION["username"])){
            $categ->can_access($_SESSION["username"], CATEGORY_MEMBER);
	     if($categ->get_errno() != DB_OK)
                return;
        }
        else
            return;
    }
    
    $active_comparisons = array();
    
    if(isset($_SESSION["comparisons"])){
        $active_comparisons = $_SESSION["comparisons"];
    }
    
 
    
    $this_comp = array();
    
    if(array_key_exists($cat_id, $active_comparisons)) { // an exoume active comparison gia autin tn katigoria
        $this_comp_2 = $active_comparisons[$cat_id];
        if(array_key_exists("entities", $this_comp_2)){
            $this_comp = $active_comparisons[$cat_id]["entities"];
            unset($active_comparisons[$cat_id]["entities"]); // remove element from comparisons array to edit it
        }
    }
    
   
    if($mode == 0){
        if(empty($ent_id))
            return;
        
        if(!in_array($ent_id,$this_comp)){
            //if($categ->)
            array_push($this_comp,$ent_id);
        }
        $active_comparisons[$cat_id]["entities"] =$this_comp;  
    }
    else if($mode == 1){
        if(empty($ent_id))
            return;

        if(in_array($ent_id,$this_comp)){
            $key = array_search($ent_id,$this_comp);
            unset($this_comp[$key]);
        }
        if (!empty($this_comp))
            $active_comparisons[$cat_id]["entities"] = $this_comp;
    }
    else if($mode ==2 ){
        /*unselect all*/
        if(isset($active_comparisons[$cat_id]))
            unset($active_comparisons[$cat_id]);
    }
    else if($mode == 3){
        /* select all */
        $ents = explode("_", $ent_ids);
        foreach($ents as $ent){
            $code = intval($ent);
            if(!in_array($code,$this_comp)){
                array_push($this_comp,$code);
            }
        }
        $active_comparisons[$cat_id]["entities"] =$this_comp;
        /*array of ids */
        
    }
    else if($mode == 4){
        /*select all category's entities*/
        $new_comp = array();
        $entities_all_ids = $categ->get_all_entities_ids();
        foreach($entities_all_ids as $ent){
            $new_comp[] = intval($ent);
        }
        $active_comparisons[$cat_id]["entities"] =$new_comp;
        
    }
    

    $_SESSION["comparisons"] = $active_comparisons;
    return;
    /*
    
    if( isset($_REQUEST['cat_id']) && isset($_REQUEST['entid']) ) {
	$cat_id = $_REQUEST['cat_id'];
	$entid = $_REQUEST['entid'];
	session_start();
	if (!isset($_SESSION['comparison'])) {
		$_SESSION['comparison'] = array();
	}
	if (!isset($_SESSION['comparison'][$cat_id])) {
		$_SESSION['comparison'][$cat_id] = array();
	}
	$exists = false;
	for( $i = 0; $i < count( $_SESSION['comparison'][$cat_id] ) ; $i++ ) {
		if( $_SESSION['comparison'][$cat_id][$i] == $entid ) {
			$exists = true;
			break;
		}
	}
	if( $exists == false ) { 
		array_push($_SESSION['comparison'][$cat_id],$entid);
	}
	var_dump($_SESSION['comparison']);
}
else{
	echo "invalid arguments";
}
*/
?>