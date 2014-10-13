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
|   Description: LOGIC Part for page Search.                                **|
|        Lines : 382                                                        **|   
\*****************************************************************************/


    class searchPage { 
        private $content;

        public function create_content() {
            include_once ("./db_includes/DB_DEFINES.php");
            include_once ("./db_includes/attribute.php");
            include_once ("./db_includes/user.php");
            include_once ("./db_includes/entity.php");     
            include_once ("./db_includes/category.php");
            include_once ("./db_includes/search.php");
                   
            
            include_once ("./logic_includes/LOGIC_DEFINES.php");
            include_once ("./logic_includes/logic_functions.php");
            include_once ("./logic_includes/renderFunctions.php");
   
   

           // Se ola ta links tha exw sort_w,sort_m pou tha einai
           // pithanws apo to proigoumeno save search

            /* initializing search variables */
            $nump = 1;
            $rpp  = 6;
            $sq   = " ";
            $sort_m = -1; /*-1 non set
                            0 alphabetical
                            1 By popularity
                            2 By num of prods
                            3 By search ranking  */
            $sort_w = -1; /*-1 non set 0 descending 1 ascending */
            $num_results = 0;
            $num_all_res = 0;
            $from_storage = false;
            $search_content = " ";
            $sq3= " ";
            /**************************************/



            /************  retrive input **************/
            /* search query */
            if (isset ($_REQUEST["sq"])) {
                $sq_ = sanitize_str(stripslashes($_REQUEST["sq"]));
                $sq3 = sanitize_str_disp(stripslashes($_REQUEST["sq"]));
            }
            else {
                $this->content = createMessage($sq3,"Please enter your search key-words first",NO_RESULTS,1);
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
                if (($rpp != -1) && ($rpp != 6) && ($rpp != 12) && ($rpp != 18)){
                    $rpp = 6;
                }
            }

            /* number of page */
            if (isset($_REQUEST["nump"])) {
                $nump = intval($_REQUEST["nump"]);
                if ($nump <= 0) {
                    $nump = 1;
                }
            }

            /* sorting method 0,1,2,3 */

            if (isset($_REQUEST["sort_m"])) {
                $sort_m = intval($_REQUEST["sort_m"]);

                if ($sort_m < 0 || $sort_m > 3){
                    $sort_m = 0;
                }
            }


            // edw tha apothikeuthoun ta apotelesmata tis anazitisis
            // eite apo to storage eite apo to sessions
            $results = array();

            if (existsInStorage($sq_)){
                $from_storage = true;
                // exw apotelesmata hdh gi auto to search sto session
                $results =loadFromStorage($sq_); 
                // sq will be used as variable name in session
                // cut whitespace from start and from end
                $sq3     =   trim($sq3);
                // replace 2 or more whitespaces in a row  with a whitespace , when inside the string
                $sq3     =   preg_replace('/\s\s+/', ' ', $sq3);
            }
            else {// search db
                $from_storage = false;
                // an den dilwse rita tropo kai methodo taksinomisis default action
                if ($sort_m == -1)
                    $sort_m = SORT_SEARCH_RANK;
                if ($sort_w == -1)
                    $sort_w = SORT_DESCENDING;

                // sq will be used as variable name in session
                // cut whitespace from start and from end
                $sq2     =   trim($sq_);
                // replace 2 or more whitespaces in a row  with a whitespace , when inside the string
                $sq2     =   preg_replace('/\s\s+/', ' ', $sq2);


                if (($ret=check_input($sq2))!= LOGIC_OK) {
                    $this->content = createMessage($sq,"Your search key-words are not Valid",NO_RESULTS,1);
                    return;
                }

                // split search query;
                $search_array = explode(" ", $sq2);

                // filter search query cut words if too many,
                // remove dublicates ,common words,
                $search_array = filter_sq($search_array);

                if (empty($search_array)) {
                    $this->content = createMessage($sq3,"Your search key-words are not Valid",NO_RESULTS,1);
                    return;
                }
                $search_mod = new search();
                $results= $search_mod->execute($search_array);
                if ($search_mod->get_errno() != DB_OK){
                    $this->content = createMessage($sq3,"Your search did not match any categories",NO_RESULTS,1);
                    return;
                }
                if (empty($results)){
                    $this->content = createMessage($sq3,"Your search did not match any categories",NO_RESULTS,1);
                    return;
                }
            }

            $prev_sort_m = -1;
            $prev_sort_w = -1;
            $re_sort     = false; // if re_sort is true then re-sort session's results

            if ($from_storage == true) {
                $prev_sort_m = loadFromStorage($sq_."_sort_type");
                $prev_sort_w = loadFromStorage($sq_."_sort_way");

                if ($sort_m != -1) {
                    if ($prev_sort_m != $sort_m)
                        $re_sort = true; // change $sort_m
                }
                else
                    $sort_m = $prev_sort_m; // ean allaksei i kateuthinsi taksinomisis
                                            // tha xreiazomaste kai ton apothikeumeno tupo
                if ($sort_w != -1) {
                    if ($prev_sort_w != $sort_w)
                        $re_sort = true; // change $sort_w
                }
                else
                    $sort_w = $prev_sort_w; // ean allaksei o tupos  taksinomisis
                                            // tha xreiazomaste kai tin kateuthinsi
            }
            else
                $re_sort = true;

            // An exoume sto storage proiougmeno tupo kai
            if( $re_sort == true) {
             // re-sort results
                if ($sort_m == SORT_ALPHABETICAL) {

                    if ($sort_w == SORT_ASCENDING)
                        usort($results,"cmp_name_a");

                    else if ($sort_w == SORT_DESCENDING)
                        usort($results,"cmp_name_d");

                }
                else if ($sort_m == SORT_POPULARITY) {

                    if ($sort_w == SORT_ASCENDING)
                        usort($results,"cmp_pop_a");

                    else if ($sort_w == SORT_DESCENDING)
                        usort($results,"cmp_pop_d");

                }
                else if ($sort_m == SORT_NUM_ENTITIES){

                    if ($sort_w == SORT_ASCENDING)
                        usort($results,"cmp_num_prods_a");

                    else if ($sort_w == SORT_DESCENDING)
                        usort($results,"cmp_num_prods_d");

                }
                else if ($sort_m == SORT_SEARCH_RANK) {

                    if ($sort_w == SORT_ASCENDING)
                        usort($results,"cmp_search_rank_a");

                    else if ($sort_w == SORT_DESCENDING)
                        usort($results,"cmp_search_rank_d");

                }
            }


            // save sorted results in session ,sort_type,sort_way, last_touch
            if (($re_sort == true && $from_storage == true)|| $from_storage == false) {
                //ean egine nea taksinomisi se hdh apothikeumena apotelesmata tote
                // swse ta panta + ananewsw ton xrono search_touch
                // ean diavase apo tin vasi swse ta panta

                saveToStorage($sq_,$results);
                saveToStorage($sq_."_sort_type",$sort_m);
                saveToStorage($sq_."_sort_way",$sort_w);

                if ($from_storage == false){
                    // ean einai i prwti fora pou ginetai save auto to search
                    // swse to sq sto saved_searches pou krataei ola ta sq twn apothikeumenwn
                    $saved_searches = loadFromStorage("saved_searches");
                    $saved_searches[] = $sq_;
                    saveToStorage("saved_searches", $saved_searches);

                }

            }
            saveToStorage($sq_."_search_touch",time());

            // load array of saved searches
            $saved_searches = array();

            if (existsInStorage("saved_searches"))  {
                $saved_searches = loadFromStorage("saved_searches");


                if (count($saved_searches) > MAX_SAVED_SEARCHES) {
                    // An kseperastike to orio apothikeumenwn anazitisewn
                    $min = -1;          // to mikrotero time tha svistei
                    $min_index = -1;    // tha krataei to index sto telos tou loop me to mikrotero time
                    $first     = true;

                    foreach ($saved_searches as $key=>$ssq) {
                        $stime = loadFromStorage($ssq."_search_touch");
                        if ($first == true) {
                            $first = false;
                            $min = $stime;
                            $min_index = $key;
                        }
                        else {
                            if ($stime < $min) {
                                $min = $stime;
                                $min_index = $key;
                            }
                        }
                    }
                    // Brika tin anazitisi pou xrisimopoiithike pio palia
                    // Svinw ta panta gia autin tin anazitisi
                    // kai ksanaswzw to saved_searches

                    deleteFromStorage($saved_searches[$min_index]);
                    deleteFromStorage($saved_searches[$min_index]."_sort_type");
                    deleteFromStorage($saved_searches[$min_index]."_sort_way");
                    deleteFromStorage($saved_searches[$min_index]."_search_touch");
                    unset($saved_searches[$min_index]);
                    saveToStorage("saved_searches", $saved_searches);
                }
            }

            $num_all_res = count($results);
            $start_c = 0;
            $end_c   = $num_all_res-1;
            if ($rpp == -1) {// show all
                $maxnump = 1;
            }
            else
                $maxnump = ceil($num_all_res / $rpp);
             
	    if($nump >= $maxnump)
		$nump = $maxnump;

		// paginate
            if ($rpp == -1) {
                $start_c = 0;
                $end_c   = $num_all_res-1;
            }
            else {
                $start_c    =   ($nump-1)*$rpp;
                $end_c      =   ($nump*$rpp)-1;
            }

	    if($num_all_res == 0){
                $nump=1;
                $maxnump = 1;
            }
	     
            if ($start_c >= $num_all_res && $num_all_res != 0) {
                // exception out of page
                $this->content = createMessage($sq3,"Internal Error in paging",NO_RESULTS,1);
                return;
            }

            if ($end_c >= $num_all_res) {
                $end_c = $num_all_res-1;
            }

            $results_to_print = array();
            for ($i = $start_c ; $i <= $end_c; $i++)
                array_push($results_to_print,$results[$i]);


            unset($results);

            // retrive info for the results
            $num_results = count($results_to_print);

            // create a 2-d array for printing
            //  0 -> id
            //  1 -> name
            //  2 -> type (CLOSE/OPEN)
            //  3 -> description
            //  4 -> rate
            //  5 -> search_rank
            //  6 -> number of entities
            //  7 -> image path

            $search_results = array();
            for ($i = 0 ; $i < $num_results; $i++){
                $tmp = array();
                $cat = new category($results_to_print[$i][0]);
                if($cat->get_errno() != DB_OK){
                    $this->content = createMessage($sq3,"Your search did not match any categories",NO_RESULTS,1);
                    return;   
                }
                $tmp[0] = $results_to_print[$i][0];
                $tmp[1] = $results_to_print[$i][1];
                $tmp[2] = $cat->is_open();
                $tmp[3] = $cat->get_description();
                $tmp[4] = $results_to_print[$i][3];
                $tmp[5] = $results_to_print[$i][4];
                $tmp[6] = $results_to_print[$i][2];
                $tmp[7] = $cat->get_image();

                array_push($search_results,$tmp);
                unset($cat);
                unset($tmp);
            }

            // exw toulaxisto 1 apotelesma , kai den exei sumvei error opote sunexizw
            $this->content="<div id=\"mainContainer\" align=\"left\">";
            $info_content = createMessage($sq3,"Found ".$num_all_res." categories",LOGIC_OK,0);
            $layout_content = createLayout($nump, $maxnump, $sort_m, $sort_w, $sq3, $rpp);
            $main_content = createCategories($search_results,$sq3);
            $this->content .= $info_content;
            $this->content .= $layout_content;
            $this->content .= $main_content;
            $this->content .="</div>";
        }

        public function get_content() {
            return $this->content;
        }
    }
?>
