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
|   Description: LOGIC functions for all pages                              **|
|        Lines : 578                                                        **|   
\*****************************************************************************/

   
// creates an array with the shown page numbers , depends on current + max page
// -1 is set before and after a page number which is not after and before of the prev-next page
    function createPageArray($nump,$maxnump){
            $pages = array();


            $from_left  = $nump-1;
            $from_right = $maxnump - $nump;
            $nump += 0;

            if ($from_left >=2 && $from_right >=2){
                // append 2 and 2
                array_push($pages,$nump-2);
                array_push($pages,$nump-1);
                array_push($pages,$nump+0);
                array_push($pages,$nump+1);
                array_push($pages,$nump+2);

            }
            else if ($from_right >= 2 && $from_left == 0) {
                for ($i= $nump; $i <= $nump+4; $i++){
                    if ($i > 0 && $i <= $maxnump )
                        array_push($pages,$i);
                }
            }
            else if ($from_right >= 2 && $from_left == 1) {

                for ($i= $nump-1; $i <= $nump+3; $i++){
                    if ($i > 0 && $i <= $maxnump )
                        array_push($pages,$i);
                }
            }
            else if ($from_right == 0 && $from_left >= 2) {
                for ($i= $nump-4; $i <= $nump; $i++){
                    if ($i > 0 && $i <= $maxnump )
                        array_push($pages,$i);
                }
            }
            else if ($from_right == 1 && $from_left >= 2) {
                for ($i= $nump-3; $i <= $nump+1; $i++){
                    if ($i > 0 && $i <= $maxnump )
                        array_push($pages,$i);
                }
            }
            else if ($from_right < 2 && $from_left < 2 ){
               
                for ($i= $nump-$from_left; $i <= $nump-$from_left+4; $i++){
                    if ($i > 0 && $i <= $maxnump)
                        array_push($pages,$i);
                }
            }
            
            $new_pages = array();
            // insert meso oro 
            if ($from_left >= 10) {
                array_unshift($pages,-1);
                array_unshift($pages,intval(ceil($nump/2)));
                array_unshift($pages,-1);
            }
            if ($from_right >= 10) {
                array_push($pages,-1);
                array_push($pages,intval(ceil(($maxnump+$nump)/2)));
                array_push($pages,-1);
            }
            
            $ins_start = array();
           
            if ($pages[0] != 1)
                $ins_start[] = 1;

            if ($pages[0] > 2 || $pages[0] == -1)
                $ins_start[] = 2;
            
            if ($pages[0] > 3 && $pages[0] != -1)
                $ins_start[] = -1;
            $pages=array_merge($ins_start,$pages);

            $ins_end = array();
            if ($pages[count($pages)-1] < $maxnump-2 && $pages[count($pages)-1] != -1)
                $ins_end[] = -1;
            if ($pages[count($pages)-1] < ($maxnump-1)){
                $ins_end[] = intval($maxnump-1);
            }
            if ($pages[count($pages)-1] < $maxnump)
                $ins_end[] = intval($maxnump);
  
            $pages = array_merge($pages,$ins_end);

            return $pages;
        }
        
        
		
    function check_string($input, $min, $max){
        if (($length = strlen($input)) == 0)
            return BAD_INPUT;
        else if($length > $max)
            return LONG_INPUT;
        else if($length < $min)
            return SHORT_INPUT;
        return LOGIC_OK;
    }
	
	function sanitize_str($input) {
	
		$result46 = $input;
		$sanitize1 = "/<script[^>]*>[^<]+<\/script[^>]*>/is";
		$sanitize2 = "/<\/?(!doctype|a|abbr|address|applet|area|article|aside|audio|b|base|bb|bdo|bgsound|blockquote|body|br|button|"
				."canvas|caption|cite|code|col|colgroup|command|datagrid|datalist|dd|del|details|dialog|dfn|div|dl|"
				."dt|dynsrc|em|embed|eventsource|fieldset|figure|footer|form|h1|h2|h3|h4|h5|h6|head|header|hr|html|i|ilayer|"
				."iframe|frame|frameset|img|input|ins|kbd|label|layer|legend|li|link|lowsrc|mark|map|menu|meta|meter|nav|noscript|object|ol|"
				."optgroup|option|output|p|param|pre|progress|q|ruby|rp|rt|samp|script|section|select|set|small|source|src|"
				."span|strong|style|sub|sup|table|tbody|td|textarea|tfoot|th|thead|time|title|tr|ul|var|video|xml)[^>]*>/is";
		
               /* $sanitize3 = "/(src|method|enctype|accept-charset|accept|name|value|lang|dir|style|title|target|onsubmit|onreset|onclick|ondblclick|"
				."onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup|onload|checked|disabled|readonly|"
				."size|maxlength|alt|usemap|ismap|tabindex|accesskey|onfocus|onblur|onselect|onchange|checkbox|radio|submit|image|"
				."reset|button|hidden|file|label|selected|onreadystatechange|multiple)[^=]*=/is";*/


		while ( 
			preg_match($sanitize1, $result46)	||
			preg_match($sanitize2, $result46)	
			
		)

		{
			$result46 = preg_replace($sanitize1, "",$result46);
			$result46 = preg_replace($sanitize2, "",$result46);
			//$result46 = preg_replace($sanitize3, "",$result46);
			//$result46 = preg_replace($sanitize4, "",$result46);
		}

		$result46 = addslashes($result46);//mysql_real_escape_string($result46);
		return $result46;

    }

    function sanitize_str_disp($input) {
	
		$result46 = $input;
		$sanitize1 = "/<script[^>]*>[^<]+<\/script[^>]*>/is";
		$sanitize2 = "/<\/?(!doctype|a|abbr|address|applet|area|article|aside|audio|b|base|bb|bdo|bgsound|blockquote|body|br|button|"
				."canvas|caption|cite|code|col|colgroup|command|datagrid|datalist|dd|del|details|dialog|dfn|div|dl|"
				."dt|dynsrc|em|embed|eventsource|fieldset|figure|footer|form|h1|h2|h3|h4|h5|h6|head|header|hr|html|i|ilayer|"
				."iframe|frame|frameset|img|input|ins|kbd|label|layer|legend|li|link|lowsrc|mark|map|menu|meta|meter|nav|noscript|object|ol|"
				."optgroup|option|output|p|param|pre|progress|q|ruby|rp|rt|samp|script|section|select|set|small|source|src|"
				."span|strong|style|sub|sup|table|tbody|td|textarea|tfoot|th|thead|time|title|tr|ul|var|video|xml)[^>]*>/is";
		
               /* $sanitize3 = "/(src|method|enctype|accept-charset|accept|name|value|lang|dir|style|title|target|onsubmit|onreset|onclick|ondblclick|"
				."onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup|onload|checked|disabled|readonly|"
				."size|maxlength|alt|usemap|ismap|tabindex|accesskey|onfocus|onblur|onselect|onchange|checkbox|radio|submit|image|"
				."reset|button|hidden|file|label|selected|onreadystatechange|multiple)[^=]*=/is";*/


		while ( 
			preg_match($sanitize1, $result46)	||
			preg_match($sanitize2, $result46)	
			
		)

		{
			$result46 = preg_replace($sanitize1, "",$result46);
			$result46 = preg_replace($sanitize2, "",$result46);
			//$result46 = preg_replace($sanitize3, "",$result46);
			//$result46 = preg_replace($sanitize4, "",$result46);
		}

		return $result46;

    }


    function check_input($input){ // tsekarei to megethos tis eisodou
        if (($num = strlen($input)) == 0)
            return EMPTY_QUERY;
        else if($num > SEARCH_MAX_INPUT)
            return LONG_QUERY;
        else if($num < SEARCH_MIN_INPUT)
            return SHORT_QUERY;
        return LOGIC_OK;
    }

    function filter_sq($words) {
        // sto words exoume se array ola ta separate words poy tha xrisimopoithoun
        // stin anazitisi
       // global $stop_words;
        $final_words = array();

        foreach($words as $w) {
            if (strlen($w) < SEARCH_WORD_MIN_LENGTH)
                continue;
            // an thewreite common i leksi tote den tin eisagoume
 
           // if(in_array($w, $stop_words))
            //    continue;

            // an exei eisaxthei hdh
            if (count($final_words) > 0) {
                if(in_array($w, $final_words))
                    continue;
            }
            array_push($final_words, $w);
        }
        return $final_words;
    }

    //sort a,b by popularity /* array's index = 3
    function cmp_pop_a($a, $b)
    {
        if ($a[3] == $b[3]) {
            $a_nm = strtolower($a[1]);
            $b_nm = strtolower($b[1]);

            if ($a_nm == $b_nm) {
                return 0;
            }
            return ($a_nm > $b_nm) ? +1 : -1;
        }
        return ($a[3] > $b[3]) ? +1 : -1;
    }

    function cmp_pop_d($a, $b)
    {

        if ($a[3] == $b[3]) {
            $a_nm = strtolower($a[1]);
            $b_nm = strtolower($b[1]);

            if ($a_nm == $b_nm) {
                return 0;
            }
            return ($a_nm > $b_nm) ? +1 : -1;
        }
        return ($a[3] > $b[3]) ? -1 : +1;
    }

    //sort a,b by number of producets /* array's index = 2
    function cmp_num_prods_a($a, $b)
    {
        if ($a[2] == $b[2]) {
            if ($a[3] == $b[3]) {
                return 0;
            }
            return ($a[3] > $b[3]) ? -1 : +1;
        }
        return ($a[2] > $b[2]) ? +1 : -1;
    }

    function cmp_num_prods_d($a, $b)
    {
        if ($a[2] == $b[2]) {
            if ($a[3] == $b[3]) {
                return 0;
            }
            return ($a[3] > $b[3]) ? -1 : +1;
        }
        return ($a[2] > $b[2]) ? -1 : +1;
    }

    //sort a,b by search rank /* array's index = 4
    function cmp_search_rank_a($a, $b)
    {
        if ($a[4] == $b[4]) {
            if ($a[3] == $b[3]) {
                return 0;
            }
            return ($a[3] > $b[3]) ? -1 : +1;
        }
        return ($a[4] > $b[4]) ? +1 : -1;
    }

    function cmp_search_rank_d($a, $b)
    {
        if ($a[4] == $b[4]) {
            if ($a[3] == $b[3]) {
                return 0;
            }
            return ($a[3] > $b[3]) ? -1 : +1;
        }
        return ($a[4] > $b[4]) ? -1 : +1;
    }

    // comparison function for ascending nm sorting (z to a )
    function cmp_name_a($a, $b)
    {
        $a_nm = strtolower($a[1]);
        $b_nm = strtolower($b[1]);
        if ($a_nm == $b_nm) {
            if ($a[3] == $b[3]) {
                return 0;
            }
            return ($a[3] > $b[3]) ? -1 : +1;
        }

        return ($a_nm > $b_nm) ? -1 : +1;
    }

    // comparison function for descending nm sorting (a to z)
    function cmp_name_d($a, $b)
    {
        $a_nm = strtolower($a[1]);
        $b_nm = strtolower($b[1]);
        if ($a_nm == $b_nm) {
            if ($a[3] == $b[3]) {
                return 0;
            }
            return ($a[3] > $b[3]) ? -1 : +1;
        }
        return ($a_nm > $b_nm) ? +1 : -1;
    }


    function existsInStorage($key){
        if(isSet($_SESSION[$key]))
            return true;
        return false;

    }

    function saveToStorage($key,$value){
        $_SESSION[$key] = $value;
        return;
    }

    function deleteFromStorage($key){
        if(isSet($_SESSION[$key])){
            unset($_SESSION[$key]);
        }
        return;
    }

    function loadFromStorage($key){
        if(isSet($_SESSION[$key]))
            return $_SESSION[$key];
        else
            return NULL;
    }

    
    /* filtering functions */
    function kmeans($data, $k)
{
        // dimiourgei ta arxika kentra (means) ton ranges , me to stepping paragonta px
        // 0-50 me k = 5 , exoume  (5),(15),(25),(35),(45)
        $cPositions = assign_initial_positions($data, $k);
        $clusters = array();
 
        while(true)
        {
                //gia kathe timi mesa sta data upologizw tin elaxisti apostasi
                // apo kathe uparxwn mean kai tin bazw mesa stin omada ekeinei
                
                $changes = kmeans_clustering($data, $cPositions, $clusters);
                if(!$changes)
                {
                        return kmeans_get_cluster_values($clusters, $data);
                }
                // an exw allages upologizw ta nea kentra
                $cPositions = kmeans_recalculate_cpositions($cPositions, $data, $clusters);
        }
}
 
/**
*
*/
    function kmeans_clustering($data, $cPositions, &$clusters)
    {
            $nChanges = 0;
            foreach($data as $dataKey => $value)
            {
                    $minDistance = null;
                    $cluster = null;
                    foreach($cPositions as $k => $position)
                    {
                            $distance = distance($value, $position);
                            if(is_null($minDistance) || $minDistance > $distance)
                            {
                                    $minDistance = $distance;
                                    $cluster = $k;
                            }
                    }
                    if(!isset($clusters[$dataKey]) || $clusters[$dataKey] != $cluster)
                    {
                            $nChanges++;
                    }
                    $clusters[$dataKey] = $cluster;
            }

            return $nChanges;
    }

    function kmeans_recalculate_cpositions($cPositions, $data, $clusters)
    {
            $kValues = kmeans_get_cluster_values($clusters, $data);
            foreach($cPositions as $k => $position)
            {
                    $cPositions[$k] = empty($kValues[$k]) ? 0 : kmeans_avg($kValues[$k]);
            }
            return $cPositions;
    }

    function kmeans_get_cluster_values($clusters, $data)
    {
            $values = array();
            foreach($clusters as $dataKey => $cluster)
            {
                    $values[$cluster][] = $data[$dataKey];
            }
            return $values;
    }


    function kmeans_avg($values)
    {
            $n = count($values);
            $sum = array_sum($values);
            return ($n == 0) ? 0 : $sum / $n;
    }


    function distance($v1, $v2)
    {
        $abc_ = abs($v1-$v2);
        return $abc_;
    }


    function assign_initial_positions($data, $k)
    {
            // creating initial ranges
            $min = min($data);
            $max = max($data);

            $step = abs($max - $min) / $k;
            while($k-- > 0)
            {
                    $cPositions[$k] = $min + $step * $k;
            }
            return $cPositions;
    }

    function kmeans_score($array){
        $total = 0;
        foreach($array as $ar){
            $total += clusters_score($ar);
        }
        return $total;
    }
    function clusters_score($array){
        $score = 0;
        $median = kmeans_avg($array);
        foreach($array as $vl){
            $dist = pow(abs($vl-$median),2);
            $score += $dist;
        }
        return $score;
    }
    function parse_ranges($karray,$val_array){
        $ranges = array();
        $distinct_vals = array();
        foreach($karray as $ark){
            // gia kathe sustada
            $min = min($ark);
            $max = max($ark);

            if($min == $max){
                $distinct_vals[] = $min;
            }

            else{
                $distinct_vals[] = $min;
                $distinct_vals[] = $max;

            }
        }
        
        $ptr = 0; /* pointer in ranges array */
        if(count($distinct_vals) == 1){
            $ranges[] = array("min"=>$distinct_vals[0],"max"=>($distinct_vals[0]+1));
        }
        else{
            $prev_min = $distinct_vals[0];
            for($ptr = 1; $ptr < count($distinct_vals); $ptr++){
                $ranges[] = array("min"=>$prev_min,"max"=>$distinct_vals[$ptr]);
                $prev_min = $distinct_vals[$ptr];

            }
        }
        $cranges = array();
        for($ij = 0 ; $ij < count($ranges); $ij++){
            $min = $ranges[$ij]["min"];
            $max = $ranges[$ij]["max"];
            $count = 0;
            $is_last = 0;
            if($ij == (count($ranges)-1))
                $is_last = 1;
            
            foreach($val_array as $value){
                if(!empty($value["value"]) && $value["count"] > 0) {
                    $fil_val = floatval($value["value"]);
                    $fil_cnt = intval($value["count"]);
                    if ($is_last == 0) {
                        if($fil_val >= $max)
                            break;
                        else if ($fil_val >= $min){
                            $count += $fil_cnt;
                        }
                    }
                    else{
                        if($fil_val > $max)
                            break;
                        else if ($fil_val >= $min){
                            $count += $fil_cnt;
                        }
                    }
                }
            }
            $cranges[] = array("min"=>$min,"max"=>$max,"count"=>$count);
        }
  
        return $cranges;

    }

    
    function fixFloat($val,$dec){
        /* val = 1.23141232 -> 1.3 mode = 1*/
        /* val = 1.23123213 -> 1.2 mode = 0*/
            return intval(pow(10,$dec)*$val)/pow(10,$dec);
    }
    
    function cmpFilters($a, $b)
    {
        $cnta = intval($a["count"]);
        $cntb = intval($b["count"]);
    
        if ($cnta == $cntb) {
            $vala = strtoupper($a["value"]);
            $valb = strtoupper($b["value"]);
            if($vala == $valb)
                return 0;
            return ($vala > $valb) ? 1:-1;
        }
        return ($cnta < $cntb) ? 1 : -1;
    }

    
    function sortDistinctFilter($filter_values){
        $new_vals = array();
        if(empty($filter_values))
            return $new_vals;
        
        usort($filter_values,"cmpFilters");
        return $filter_values;
    }
                        
    
?>
