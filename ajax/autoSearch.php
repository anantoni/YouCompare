<?php
        /* thomas karampelas*/

	include_once('../logic_includes/LOGIC_DEFINES.php');
        include_once('../logic_includes/logic_functions.php');
	include_once "../db_includes/DB_DEFINES.php" ;
	include_once "../db_includes/user.php" ;
	include_once "../db_includes/attribute.php" ;
	include_once "../db_includes/entity.php" ;
	include_once('../db_includes/autocomplete.php');

    
	function autoSearchCategories($name){
        	$results = array();
         	$con = new mysqli("edudb.di.uoa.gr", "soft_tech", "#s@f+#", "soft_tech_db");
            	$query="SELECT cat_id,cat_name from category WHERE cat_name LIKE \"".$name."%\" LIMIT 12;";

		if( !($res=$con->query($query) ) )
		{
			$con->close();
			return $results;
		}
		$i = 0;
		while( ($row=$res->fetch_row()) )
		{
              	$temp = array();
                     array_push($temp,$row[0]);
                     array_push($temp,$row[1]);
                     $fl = new category($row[0]);
			if($fl->get_errno() != DB_OK)
				continue;
                                    
			$rate = $fl->get_rating();
			if($fl->get_errno() != DB_OK)
				continue;
			array_push($temp,$rate);
                     array_push($results,$temp);
                     unset($temp);
                     $i++;
		}
		$con->close();
                        
              return $results;
    }

    $aid = " ";
    if (isset($_REQUEST["aid"])){
        $aid = sanitize_str($_REQUEST["aid"]);
	 
    }

    header('Content-type: text/xml');
    $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    //$res = autoSearchCategories($aid);
    $xml .= '<results>';
    if(empty($aid) || $aid[0]==" "){
	$xml .="</results>";
    	echo $xml;
    	return;
    }
    $auto = new categoryAuto();
    $res = $auto->get_categories_by_popularity($aid,12);
  
    if(!empty($res)){
    	foreach($res as $key) {
      		$xml.="<rs id=\"".$key[0]."\" rating=\"".$key[2]."\">".sanitize_str_disp($key[1])."</rs>";
    	}
    }
    $xml .="</results>";
    echo $xml;
    return;
?>
