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
|   Description: LOGIC Part for page browse Category. algorithms calls to   **|
|                my render functions                                        **|
|        Lines : 633                                                        **|   
\*****************************************************************************/



class browseCategoryPage{
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
            
            $nump = 1;
            $rpp  = 10;
            $sq2   = "";
            $sort_m = 0; /* default by alpha
                            0 alphabetical
                            1 By popularity
                            2 By attribute (later)  */
            $sort_w = 0; /* 0 descending 1 ascending */
            $active_attr_id = -1; /* attribute id when sorting by attribute*/
            $active_filters = array(); /*active filters*/
            $active_values = array(); /* value for each filter*/
            $filters_count = 0; 
            
            // retrive category id
            $category_id = -1;
            if (isset($_REQUEST["cat_id"])) {
                $category_id = intval($_REQUEST["cat_id"]);
            }
            else {
                $this->content = renderCategoryError(NO_CATEGORY_ID_GIVEN);
                return;
            }
            
            
            /* sort_way 0 descending 1 ascending */
            if (isset ($_REQUEST["sort_w"])) {
                $sort_w = intval($_REQUEST["sort_w"]);
                if ($sort_w != 0 && $sort_w != 1) {
                    $sort_w = 0;
                }
            }


            /* results per page */
            if (isset ($_REQUEST["rpp"])) {
                $rpp = intval($_REQUEST["rpp"]);
                if (($rpp != -1) && ($rpp != 5) && ($rpp != 10) && ($rpp != 20) && ($rpp != 30)){
                    $rpp = 10;
                }
            }

            /* number of page */
            if (isset($_REQUEST["nump"])) {
                $nump = intval($_REQUEST["nump"]);
                if ($nump <= 0) {
                    $nump = 1;
                }
            }

            /* sorting method 0,1,2 */

            if (isset($_REQUEST["sort_m"])) {
                $sort_m = intval($_REQUEST["sort_m"]);

                if ($sort_m < 0 || $sort_m > 2){
                    $sort_m = 0;
                }
            }
            
            /* attr id */
            if (isset($_REQUEST["attrid"])) {
                $active_attr_id = intval($_REQUEST["attrid"]);
            }
            
            if (isset($_REQUEST["sq"])){
                $sq2 = sanitize_str_disp(stripslashes($_REQUEST["sq"]));
            }

            $username_ = "";
            $user_state = LOGIC_GUEST_VIEWER;
            if (isset($_SESSION["username"])){
                $username_ = $_SESSION["username"];
                $user_state = LOGIC_REGISTERED;
            }
 
            
            $categ = new Category($category_id);
            if ($categ->get_errno() != DB_OK || $category_id == -1) {
                // category does not exist
                $msg_content ="This category does not exist";               
                $this->content = createCategoryMessage ($msg_content,$sq2,1,3);
                return;
            }
            
           if($user_state == LOGIC_REGISTERED){
                $ret = $categ->get_user_rights($username_);
                if (!is_null($ret)) {
                    $user_state = intval($ret);
                    if($user_state != intval(LOGIC_ADMINISTRATOR)){
                        $user_state = $user_state+1; // ... db saves them from 0 to 3 i save from 1 to 4
                    }
                }
           }
                
           //$user_state = LOGIC_MODERATOR;
           /*$ent = $categ->get_specific_entity(1);
           $attr_id = 22;
           $vl = $ent->entity_attribute_values;
           echo($vl[$attr_id]);*/
           
            $is_open = $categ->is_open();
            
            // category info 0 name,1 rate,2 num entities, 3 description
            //               4 image, 5 video, 6 {can rate category 0 yes,1 has rated,2 no
            $category_info = array();
            $category_info[]  = $categ->get_name();
            $category_info[]  = $categ->get_rating();
            $category_info[]  = $categ->get_number_of_entities();
            $category_info[]  = $categ->get_description();
            $category_info[]  = $categ->get_image();
            $category_info[]  = $categ->get_video();
            
            // 2 means cannot rate, 1 means has already rate it, 0 means can rate
            // ean einai viewer den mporei na kanei rate
            if($user_state == LOGIC_GUEST_VIEWER)
                $category_info[] = 2;
            // ean einai registered se kleisti katigoria den mporei na kanei rate
            if($user_state == LOGIC_REGISTERED && $is_open == 0)
                $category_info[] = 2;
            // ean den einai guest  se anoixti katigoria elegxetai an exei ksanakanei rate
            // ean den einai guest/registered se kleisti katigoria pali elegxetai an exei kanei rate
            if(($user_state != LOGIC_GUEST_VIEWER && $is_open == 1) ||(
                ($user_state != LOGIC_GUEST_VIEWER && $user_state != LOGIC_REGISTERED) && $is_open==0))
                $category_info[] = $categ->has_category_been_rated($username_);
            $category_info[]  = $category_id;
	     $category_info[]  = $is_open;
  
            
            
            if($is_open == 0){ // if private category
                if ($user_state == LOGIC_GUEST_VIEWER){
                    $this->content = "<div id=\"mainContainer\" align=\"left\">";
                    $msg_content ="Hello Viewer You have to Login / Register First to view this category:".sanitize_str_disp($categ->get_name());              
                    $this->content .= createCategoryMessage($msg_content,$sq2,0,1);
                    $this->content .= createCategoryInfo($category_info,LOGIC_GUEST_VIEWER);
                    $this->content .= "</div>";
                    return;
                }
                else if($user_state == LOGIC_REGISTERED){
                    // ean den exei prosvasi
                    $this->content = "<div id=\"mainContainer\" align=\"left\"></br>";
                    $msg_content ="Hello ".$username_." ,You have to Become Member First to view category:".sanitize_str_disp($categ->get_name());     
                    $this->content .= createCategoryMessage($msg_content,$sq2,0,1);
                    $this->content .= createCategoryInfo($category_info,LOGIC_REGISTERED);
                    $this->content .= "</div>";
                    return;
                }
            }
            // open category or user has rights >= CATEGORY_MEMBER
            
            /* logic part of filters */
            $filters    = $categ->get_filters(-1); /* get filters of category*/
            // * afc = active filters count
            // number of active filters used
            if (isset($_REQUEST["afc"])){
                $filters_count = intval($_REQUEST["afc"]);
                if ($filters_count > 200){
                    $filters_count = 0;
                }
            }
     
       
            // get active filters
            $i = 0;
            for($i = 0 ; $i < $filters_count; $i++){
                // search for active filter
                if (isset($_REQUEST["acf_id_".$i]) && isset($_REQUEST["acf_val_".$i])){
                    $fil_id = intval($_REQUEST["acf_id_".$i]);
                    $fil_val  = sanitize_str_disp(stripslashes($_REQUEST["acf_val_".$i]));
                    $active_filters[] = array( "id" => $fil_id, "value" => $fil_val );    
                }
                
            }
            // $active_filters[] contains , GET info of current active filters
            // i must check for consistency and construct , sh_value,ac_value
            // $filters from db contains ID,NAME,COMPARABILITY
            $mod_filters = array();  // name,value e.g name = price , 
            //$enf_filters = array(); // this will contain all the needed , to pass filters impact to the entities shown
            
            if(!empty($filters)){
                foreach($filters as $filter){
                    $filter_id   = $filter["ID"];
                    $filter_name = $filter["NAME"];
                    $filter_comp = $filter["COMPARABILITY"];       
                    $filter_value =array();
                    $filter_values = $categ->get_specific_filter($filter_id, -1, $filter_comp);

                    if (empty($filter_values))
                        continue;

                    if ($filter_comp == DISTINCT || $filter_comp == UNCOMPARABLE){
                        // take distinct value, check if name,value exists
                        // sort filter values by count
                        $filter_values_sort = sortDistinctFilter($filter_values);
                        foreach($filter_values_sort as $val){
                            $fil_val = $val["value"];
                            $fil_count = $val["count"];
                            if ($fil_count > 0 && !empty($fil_val))
                                $filter_value[] = array("value"=>$fil_val,"pvalue"=>$fil_val,"count"=>$fil_count);
                        }

                    }
                    else if($filter_comp == COUNTABLE){
                        /*filter values "value" , "count"*/
                        $exploded_values = array();
                        $count_distinct_filt_values=0;
                        $count_all_filt_values =0;

                        foreach($filter_values as $val){
                            $fil_val = $val["value"];
                            $fil_count = $val["count"];
                            if ($fil_count > 0 && !empty($fil_val)){
                                $count_distinct_filt_values++;
                                $count_all_filt_values +=$val["count"];

                                for($ij = 0; $ij <$fil_count;$ij++)
                                    $exploded_values[] = floatval ($fil_val); /*sec argument posa dekadika*/
                            }
                        }
                        if(empty($exploded_values))
				continue;
                        $k_forkmeans = ceil(sqrt($count_all_filt_values/2));
                        if($k_forkmeans <=3)
                            $k_forkmeans = 2;
                        if($k_forkmeans >= 15)
                            $k_forkmeans = 10;
                        //$k_forkmeans = ceil(sqrt($count_distinct_filt_values/2));
 //                       $k_forkmeans = 26;

                       // echo "k:".$k_forkmeans."all:".$count_all_filt_values."dis:".$count_distinct_filt_values."</br>";
                        $clusters_vals  = kmeans($exploded_values, $k_forkmeans);
                       // echo "Total score:".kmeans_score($clusters_vals)."</br>";
                       // foreach($clusters_vals as $cl){
                       //     echo "cl (".min($cl)."-".max($cl).") score:".clusters_score($cl)."</br>";
                       // }
                        $init_ranges    = parse_ranges($clusters_vals,$filter_values);

                        foreach($init_ranges as $cur_range){
                            $min = $cur_range["min"];
                            $max = $cur_range["max"];
                            $cnt = $cur_range["count"];
                            $prange = fixFloat($min,3)."<span class=\"vs\">-</span>".fixFloat($max,3);
                            $range = $min."_".$max;
                            $filter_value[]=array("value"=>$prange ,"pvalue"=>$range,"count"=>$cnt);
                        }
                    }
                    // add modified filter
			if(!empty($filter_value))
	                    $mod_filters[]=array("ID"=>$filter_id,"NAME"=>$filter_name ,"TYPE"=>$filter_comp, "VALUES"=>$filter_value);


                }
            }
         
            // check if active filters are ok within $mod_filters
            $ver_active_filters = array(); // periexei ta pragmatika matched active filters
            // gia kathe active filter des an uparxei
  
            foreach($active_filters as $acf){
                
                $acf_id = $acf["id"];
                $acf_val  = $acf["value"];
                $insert_fil = 0;
                $acf_pval = " ";
                $acf_name = " ";
                $acf_type = " ";
                foreach($mod_filters as $modf){
                    if($modf["ID"] == $acf_id){
                        $is_last_vl = 0;
                        for($ij = 0; $ij < count($modf["VALUES"]); $ij++){
                            if($modf["VALUES"][$ij]["pvalue"] == $acf_val){
                                if($ij == (count($modf["VALUES"])-1))
                                    $is_last_vl = 1;
                                $insert_fil = 1;
                                $acf_pval = $modf["VALUES"][$ij]["value"];
                                $acf_name = $modf["NAME"];
                                $acf_type = $modf["TYPE"];
                            }
                        }
                    }
                }
                if($insert_fil)
                    $ver_active_filters[] = array("id"=>$acf_id,"type"=>$acf_type,"name"=>$acf_name,"pvalue"=>$acf_val,"value"=>$acf_pval,"islast"=>$is_last_vl);
            }

            // ver_active_filters contains => id type name , value (100-200) pvalue(100_200)
            // mod_filters  {VALUES} contains => id  name , value(100-200), pvalue(100_200)

            
            // $mod_filters[] contains info about the filters of category

            // category page client info 
            // 0 -> category id
            // 1 -> number of current page
            // 2 -> type of sorting
            // 3 -> way of sorting
            // 4 -> results per page
            // 5 -> active filters array
            //    5."name"   =>   
            //    5."pvalue" => 
            //    5."value"  => 
            // $mod_filters
            //      "NAME"
            //      "TYPE" 
            //      "VALUES" => "value","pvalue","count"
  

            //prepei na omadopoiisw ta filtra me to idio id 
            $mixed_filters = array();
            foreach($ver_active_filters as $filt){
                $filt_id = intval($filt["id"]);
                $mixed_filters[$filt_id][] = $filt;
            }
 
            
            $filtered = array();
            foreach($mixed_filters as $filt_id=>$filt){
                // file_id id of filter
                $filter_type = -1;
                $filter_hold_keys = array();
                $filter_hold_vals = array();
                $filter_cnt = 0;
                foreach($filt as $next_filter){
                    $filter_type = $next_filter["type"];

                    if($filter_cnt == 0){
                        $filter_hold_keys[] = "Id";
                        $filter_hold_vals[] = $filt_id;
                        $filter_hold_keys[] = "Comp";
                        $filter_hold_vals[] = $filter_type;
                    }



                    $filter_hold_keys[] ="Op0.".$filter_cnt;  // low op
                    if ($filter_type == COUNTABLE){
                        $pos = strpos($next_filter["pvalue"], "_");
                        $low = floatval(substr($next_filter["pvalue"],0,$pos));
                        $high = floatval(substr($next_filter["pvalue"],$pos+1));
                        $is_last = intval($next_filter["islast"]);
                        $filter_hold_keys[] ="Op1.".$filter_cnt;  // hihg op
                        $filter_hold_vals[] = ">=";
                        if($is_last == 0) {
                            $filter_hold_vals[] = "<";
                        }
                        else
                            $filter_hold_vals[] = "<=";
                        // add values
                        $filter_hold_keys[] ="Value0.".$filter_cnt;
                        $filter_hold_keys[] ="Value1.".$filter_cnt;
                        $filter_hold_vals[] = $low;
                        $filter_hold_vals[] = $high;
                    }
                    else{
                        $filter_hold_vals[] = "=";
                        $filter_hold_keys[] ="Value0.".$filter_cnt;
                        $filter_hold_vals[] = $next_filter["pvalue"];               
                    }

                    $filter_cnt++;

                }
                $filtered[] = array_combine($filter_hold_keys,$filter_hold_vals);
            }
    
            if ($sort_w == 0){/*mysql alphabetical sort fix*/
                if($sort_m == SORT_ALPHABETICAL)
                    $sort_fw = "ASC";
                else
                    $sort_fw = "DESC";
            }
            else if($sort_w == 1){
                if($sort_m == SORT_ALPHABETICAL)
                    $sort_fw = "DESC";
                else
                    $sort_fw = "ASC";
                    
            }
            $countable_attrs = array();
            //mesa sto page_info vazw k ta countable attributes
            $attributes = $categ->get_attributes(0);
            
           
            $active_att_exists = 0;
            if(!empty($attributes)){
                foreach($attributes as $attrib){
                    if($attrib->comparability == COUNTABLE){
                        $tmp = array();
                        $tmp[] = $attrib->id;
                        $tmp[] = $attrib->name;
                        array_push($countable_attrs,$tmp);
                        if($active_attr_id != -1){
                            if($active_attr_id == $attrib->id)
                                    $active_att_exists = 1;
                        }
                        unset($tmp);
                    }
                }
            }
            
            if(empty($countable_attrs)){
                $sort_m = 0;
            }
            else {
                if($active_attr_id == -1 || $active_att_exists == 0) {
                    // assign first id
                    $active_attr_id = $countable_attrs[0][0];
                }
            }

            // retrive entities
            $entities = array();
            if(count($filtered) == 0 ){
                $entities   = $categ->get_N_entities(1, -1, $sort_m+1, $sort_fw, null, $active_attr_id);
            }     
            else {
                $entities   = $categ->get_N_entities(1, -1, $sort_m+1, $sort_fw, $filtered, $active_attr_id);    
            }
            if(empty($entities))
                $entities=array();
            

            $num_all_res = count($entities);     
            /* take all entities and select the needed*/
            $from = 0;              // take all
            $to   = $num_all_res; 
            $max_page = 1;
            
            if ($rpp == -1) {// show all
                $max_page = 1;
            }
            else
                $max_page = ceil($num_all_res / $rpp);
             
	    if($nump >= $max_page){
                $nump = $max_page;
            }

		// paginate
            if ($rpp == -1) {
                $from = 0;
                $to   = $num_all_res;
            }
            else {
                $from    =   ($nump-1)*$rpp;
                $to      =   ($nump*$rpp)-1;
            }
            if($num_all_res == 0){
                $nump=1;
                $max_page = 1;
            }
	     
            if ($from >= $num_all_res && $num_all_res != 0) {
                // exception out of page
                $this->content = renderCategoryError(WRONG_PAGE);
                return;
            }

            if ($to > $num_all_res) {
                $to = $num_all_res;
            }

            
            $entities_to_comp = array();
            if(isset($_SESSION["comparisons"])){
                $active = $_SESSION["comparisons"];
                if (array_key_exists($category_id, $active)){
                    $tmp_rmt = $active[$category_id];
                    if(array_key_exists("entities",$tmp_rmt)){
                        $entities_to_comp = $active[$category_id]["entities"];
                    }
                }
            }
            
            $num_to_comp = count($entities_to_comp);
            
            $entities_info_final = array();
            /* [0] -> entity_id
             * [1] -> entity_name
             * [2] -> entity_desc
             * [3] -> entity_image
             * [4] -> entity_video
             * [5] -> entity_rate
             * [6] -> {array of values }
             * [7] -> user_can_rate ( 0 means no , 1 means yes)
             * [8] -> is_added_to_compare ( 0 means no , 1 means yes)
             */
            $ent_cnt = -1;
            foreach($entities as $ent){
                $ent_cnt++;
                if($ent_cnt < $from)
                    continue;
                if($ent_cnt > $to)
                    break;
                
                
                $tmp = array();
                $tmp[] = $ent->entity_id;
                $tmp[] = $ent->entity_name;
                $tmp[] = $ent->entity_description;
                $tmp[] = $ent->entity_image;
                $tmp[] = $ent->entity_video;
                $tmp[] = $ent->rate;
                $tmp[] = $ent->entity_attribute_values;
                
                if(empty($username_)){
                    $tmp[] = 0; /* viewer cannot rate entities*/
                }
                else {
                    if($categ->has_entity_been_rated($ent->entity_id, $username_))
                        $tmp[] = 1; /* already rated */
                    else
                        $tmp[] = 2; /* can rate */
                }
                if(!empty($entities_to_comp)){
                    if(in_array($ent->entity_id,$entities_to_comp))
                        $tmp[] = 1;
                    else
                        $tmp[] = 0;
                }
                else
                    $tmp[] = 0;
                array_push($entities_info_final,$tmp);
                unset($tmp);
            }

            
            $page_info = array();
            array_push($page_info,$category_id);
            array_push($page_info,$nump);
            array_push($page_info,$sort_m);
            array_push($page_info,$sort_w);
            array_push($page_info,$rpp);
            array_push($page_info,$ver_active_filters);
            array_push($page_info,$countable_attrs);
            array_push($page_info,$active_attr_id);
            array_push($page_info,$mixed_filters);
            array_push($page_info,$sq2);

 
    
            $this->content="<div id=\"mainContainer\" align=\"left\">";
            $msg_content = " ";
            $categ_name2 = sanitize_str_disp($categ->get_name());
            if ($user_state == LOGIC_GUEST_VIEWER)
                $msg_content = "Hello Viewer, Welcome to Category:".$categ_name2;
            else if ($user_state == LOGIC_REGISTERED)
                $msg_content = "Hello ".$username_.", Welcome to Category:".$categ_name2;
            else if ($user_state == LOGIC_CATEGORY_MEMBER)
                $msg_content = "Hello Category Member ".$username_.", Welcome to Category:".$categ_name2;
            else if ($user_state == LOGIC_EDITOR_MEMBER)
                $msg_content = "Hello Editor Member ".$username_.", Welcome to Category:".$categ_name2;
            else if ($user_state == LOGIC_SUB_MODERATOR)
                $msg_content = "Hello Sub-Moderator ".$username_.", Welcome to Category:".$categ_name2;
            else if ($user_state == LOGIC_MODERATOR)
                $msg_content = "Hello Moderator ".$username_.", Welcome to Category:".$categ_name2;
            else if ($user_state == LOGIC_ADMINISTRATOR)
                $msg_content = "Hello Administrator ".$username_.", Welcome to Category:".$categ_name2;
            
            //if($user_state == LOGIC_REGISTERED)
              //  $user_state = LOGIC_CATEGORY_MEMBER;
            
            $img_ulock = 0;
            if($categ->is_open() == 0) // closed  but has access
                    $img_ulock = 2;

            $cat_page_info      = createCategoryMessage($msg_content,$sq2,0,$img_ulock);
            $cat_layout         = createCategoryInfo($category_info, $user_state);
            $this->content .=$cat_page_info;
            $this->content .=$cat_layout;

            if(!empty($mod_filters))
                $filters_layout     = createFilters($page_info,$mod_filters);
            
            
            if(count($entities_info_final) > 0){
		  $comp_layout        = createCatCompLayout($category_id, $num_all_res, $num_to_comp);
                $entities_layout    = createCatResultsEntities($entities_info_final,$category_id);            
                $pag_layout         = createCatLayout($page_info,$max_page);
	         $bot_pag_layout     = createBottomCatLayout($page_info,$max_page);
            }
                
            if(!empty($mod_filters))
                $this->content .=$filters_layout;
           
            
            
            if(count($entities_info_final) > 0) {
		  $this->content .=$comp_layout;
                $this->content .=$pag_layout;
                $this->content .=$entities_layout;
		  $this->content .=$bot_pag_layout;
            }

            $this->content .="</div>";

            
            
            
        }  
        public function get_content() {
            return $this->content;
        }
    
    
	
};
?>
