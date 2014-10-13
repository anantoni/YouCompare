<?php
/*********************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             	**|
|   contact : zorbash@hotmail.com                                           	**|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      	**|
|   Description: backend part for adding weights information to session (php)   **|
|                                                                           	**|   
\*********************************************************************************/


if( isset($_POST["weights"]) && isset($_POST["cat_id"]) ) {
	session_start();
	$cat_id = intval($_POST["cat_id"]);
	$weights = array();
	$weights = $_POST["weights"];

	if( isset($_SESSION["comparisons"]) ) {
		$active_comparisons =array();
		$active_comparisons = $_SESSION["comparisons"];
		$active_comparisons[$cat_id]["weights"] = $weights; 
		
		
		$_SESSION["comparisons"] = $active_comparisons;
		var_dump($active_comparisons[$cat_id]);
        	
	}	
	

}
else {
		echo "invalid arguments";
}

?>
