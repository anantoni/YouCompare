<?php
    session_start();

    if(isset($_REQUEST["cat_id"])){
        $cat_id = intval($_REQUEST["cat_id"]);
    }
    
    if(isset($_SESSION["comparisons"])){
        $active_comparisons = $_SESSION["comparisons"];
    }
    if(isset($active_comparisons[$cat_id]))
        var_dump($active_comparisons[$cat_id]);
    return;
    
    
?>
