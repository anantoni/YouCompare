<?php
	session_start();
	session_destroy();
	//$redirect .= $_GET["redirect"];
	//foreach($_GET as $key=>$argument) {
	//	if($key == "redirect") continue;
	//	$redirect .= "&".$key."=".$argument;
	//}
	header("Location: ./index.php");
?>

