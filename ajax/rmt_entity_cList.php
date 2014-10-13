<?php

	/*** file: rmt_entity_cList.php ***/
	/*
	  Created on : 04/05/2011, 1:22:46 AM
	  Author : zorbash
          Description: circular list implementation for browse entities  
	  contact: zorbash@hotmail.com 
	*/

if( isset( $_POST['cat_id'] ) && isset( $_POST['entid'] ) && isset( $_POST['mode'] ) ) {
	require_once "./../db_includes/category.php"; 
	$cat_id = $_POST['cat_id'];
	$entid = $_POST['entid'];
	$mode = $_POST['mode'];
	$category = new category($cat_id);
	$id = "";
	if($mode == "next") {
		$id = $category->get_next_entity_id($entid);
		echo $id;
	}
	else if($mode == "previous") {
		$id = $category->get_prev_entity_id($entid);
		echo $id;
	}
}
else {
	echo "invalid access to the script";
}

?>
