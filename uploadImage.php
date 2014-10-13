<?php
    include_once './fileSystem.php';
    
    $err_msg ="<form name=\"photoUpload\" action=\"./uploadImage.php\" method=\"post\" enctype=\"multipart/form-data\">
    <input id=\"file\" type=\"file\" name=\"image\" onChange=\"document.photoUpload.submit();\"/>
    <input type=\"hidden\" name=\"typeImg\" value=\"0\"/></form>";
    $suc_msg = "SUCCESS";
    
    
    if(!isset($_FILES["image"])){      
        echo $err_msg;
        return;
    }
    
    if(!isset($_POST["typeImg"])){
        echo $err_msg;
        return;
    }
    $mode = intval($_POST["typeImg"]);
    if($mode != 0 && $mode !=1){
        echo $err_msg;
        return;
    }
    
    $err_msg ="<form name=\"photoUpload\" action=\"./uploadImage.php\" method=\"post\" enctype=\"multipart/form-data\">
    <input id=\"file\" type=\"file\" name=\"image\" onChange=\"document.photoUpload.submit();\"/>
    <input type=\"hidden\" value=\"".$mode."\"/></form>";
    
    if($mode == 0){
        $path = addCategoryImage("image", $_FILES);
        if(!empty($path)){
            echo "<span style=\"color: green;\">Image uploaded succesfully</span><span class=\"image_path\" id=\"".$path."\"></span>";
            return;
        }
        else{
            echo $err_msg;
            return;           
        }
            
    }
    else{
        $path = addEntityImage("image", $_FILES);
        if(!empty($path)){
            echo $path;
            return;
        }
        else{
            echo $err_msg;
            return;           
        }
            
    }
?>
