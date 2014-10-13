<?php
    /* by Antonopoulos Spyridon - Thomas Karampelas*/

    session_start();
    include_once('../logic_includes/LOGIC_DEFINES.php');
    include_once('../logic_includes/logic_functions.php');
    include_once ("../db_includes/DB_DEFINES.php");
    include_once ("../db_includes/attribute.php");
    include_once ("../db_includes/user.php");
    include_once ("../db_includes/entity.php");     
    include_once ("../db_includes/category.php");

    
    header("Content-type: text/xml");
    $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    $xml .= "<results>";
    
    $cid = " ";
    if (isset($_REQUEST["cid"])){
        $cid = intval($_REQUEST["cid"]);
    }
    else{
        $xml.="</results>";
        echo $xml;
        return;
    }


    

  
    $fl = new category($cid);
    if($fl->get_errno() != DB_OK){
        $xml.="</results>";
        echo $xml;
        return;   
    }
   
    $xml .= "<id>".$cid."</id>";
    $num_e = $fl->get_number_of_entities();
    if(empty($num_e)){
        $num_e = 0;
    }
    
    $xml .= "<number_entities>".$num_e."</number_entities>";
    $cat_name = $fl->get_name();
    if(empty($cat_name)){
        $cat_name ="--";
    }
    $cat_name = htmlentities(sanitize_str_disp($cat_name));
    $xml .= "<name>".$cat_name."</name>";
    
    $output = "";
    $tmp = $fl->get_keywords();
    
    if(empty($tmp))
           $output = "-"; 
    else {
        $cntr= 0;
        $tmp2= array();
        foreach($tmp as $kwt){
            if($cntr < 6){
                $tmp2[] = $kwt;
                $cntr++;
            }
            else
                break;
        }
        $output = htmlentities(sanitize_str_disp(implode(" , ",$tmp2)));
    }

    
    $xml .= "<keywords>".$output."</keywords>";
    $xml .= "<type>".$fl->is_open()."</type>";
    $rate_c = $fl->get_rating();
    if($rate_c > 10)
        $rate_c = 10;
    if($rate_c < 0)
        $rate_c = 0;
   
    $xml .= "<rate>".floatval(round($rate_c,2))."</rate>";
    
    $tmp = $fl->get_image();
    if(empty($tmp))
             $output = "./cat_images/__not__.jpg"; 
    else 
        $output = $tmp;
    
    $xml .= "<image>".$output."</image>";
    
    $tmp = $fl->get_description();
    if(empty($tmp))
        $output = " . . . . "; 
    else 
        $output = htmlentities(sanitize_str_disp($tmp));
    $xml .= "<desc>".$output."</desc>";
    
    $show = true;
    $username_ = -1;
    if(isset($_SESSION["username"])) {
        $username_ = $_SESSION["username"];
    }
    
    if ($fl->is_open() == 0 ) {//if closed
        
        if ($username_ != -1){
            $fl->can_access($username_, CATEGORY_MEMBER);
            if($fl->get_errno() == DB_OK)
                $show = true;
            else
                $show = false;
        }
        else
            $show = false;
    }
    $xml .= "<populars>";
    if ($show == true) {
        $pope = $fl->get_most_popular_entities(5);
        if(!empty($pope)){
            foreach ($pope as $pe){
                $pop_name = $pe["Name"];
                if(empty($pop_name))
                    $pop_name = "-";
                
                $pop_id = $pe["Id"];
                if(empty($pop_id))
                    $pop_id = -1;

                $xml .="<popular><name>".htmlentities(sanitize_str_disp($pop_name))."</name>";
                $xml .="<id>".$pop_id."</id></popular>";
            }
        }
    }
    $xml .="</populars>";
    $xml .="</results>";
    

    echo $xml;
    return;
?>
