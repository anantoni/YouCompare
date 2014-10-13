
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
|   Description: GUI Part renders HTML output from logic outputs to print   **|
|        Lines : 1540                                                       **|   
|                           ** renderEdit/Add Item is from Pan **           **|
\*****************************************************************************/
 
    
    function createCategories($search_results,$sq){
        // $sq is unsanitized
        //  0 -> id
        //  1 -> name
        //  2 -> type (CLOSE/OPEN)
        //  3 -> description
        //  4 -> rate
        //  5 -> search_rank
        //  6 -> number of entities
        //  7 -> image path

        $output = "<div id=\"searchResults\" align=\"center\"><ul>";
        foreach($search_results as $cat) {
            if ($cat[4] < 0)
                $rank_width = 0;
            else if ($cat[4] > 10)
                $rank_width = 125;
            else
                $rank_width = floatval(round($cat[4]*12.5,2));
            
            
                
            $output.="<li onclick=\"window.location='./browseCategory.php?cat_id=".$cat[0]."&sq=".urlencode($sq)."';\" class=\"mainCategory\"><a href=\"./browseCategory.php?cat_id=".$cat[0]."&sq=".urlencode($sq)."\"><div align=\"left\" class=\"mainFirstRow\">";
            if ($cat[2] == 0)
                $output.="<img src=\"./images/lock_4.png\" class=\"mainLockedImage\"/>";
            else
                $output.="<span class=\"mainLockedImage\"></span>";

	     if($cat[7] != NULL) 
	            $output.="<img class=\"mainCatImage\" src='".$cat[7]."'/></div>";
	     else
		    $output.="<img class=\"mainCatImage\" src=\"./cat_images/__not__.jpg\"/></div>";


            $output.="<div align=\"center\" class=\"mainCatInfo\">
                        <div class=\"mainOuterRate\">
                        <div style=\"width:".$rank_width."px\" class=\"mainInnerRate\"></div></div>   
                            <span class=\"mainCatName\">".sanitize_str_disp($cat[1])."</span>
                            <span class=\"mainCatNumEnts\">(".$cat[6]." Entities)</span>
                      </div></a>
                    </li>";
        }
        // spam 10 ghosts
        for($i = 0; $i < 10; $i++)
            $output.="<li class=\"mainGhosts\"></li>";
        $output.="</ul></div>";
        return $output;
        
        
    }
    function createMessage($sq,$str,$mode,$finalize){
        $output="";
        if($finalize == 1)
            $output.="<div id=\"mainContainer\" align=\"left\">";
        $output.="<div id=\"searchInfo\" align=\"left\">";
        if($mode == NO_RESULTS)
            $output.="<img src=\"./images/searchError.gif\" alt=\"error\" id=\"searchGif\"/>";
        else if($mode == LOGIC_OK)
            $output.="<img src=\"./images/okSearch.png\" alt=\"ok\" id=\"searchGif\"/>";
        $output.="<span id=\"searchMsg\">Searching for (<b>".sanitize_str_disp($sq)."</b>) <span style=\"margin-left:4px;margin-right:4px\">:</span> ".$str;
        $output.="</span></div>";
        if ($finalize == 1)
            $output.="</div>";
        return $output;
    }
    
    
    /* create sorting and results per page selector layout */
    function createLayout($nump,$maxnump,$sort_m,$sort_w,$sq,$rpp){
        $output = "<div class=\"searchPaging\" align=\"left\"><ul>";
        $pages = createPageArray($nump, $maxnump);

        $output .= "<li id=\"jumpToPage\"><img style=\"margin-right:5px\" src=\"./images/downArrowPaging.png\"/>
                   Page ".$nump." From ".$maxnump."</li>";
        if($nump > 1) {
                  $output .="<li onClick=\"ChangePage(".($nump-1).",".$sort_m.",".$sort_w.",'".urlencode($sq)."',".$rpp.");\" ><img style=\"margin-right:5px\" src=\"./images/leftArrowPaging.png\"/>Previous</li>";
        }
        
        foreach($pages as $page) {
            if ($page == -1) {
                $output.="<li id=\"nonHover\">..</li>";
            }
            else if ($page == $nump){
                $output.="<li onClick=\"ChangePage(".($nump).",".$sort_m.",".$sort_w.",'".urlencode($sq)."',".$rpp.");\" id=\"Active\">".$nump."</li>";
            }
            else {
                $output.="<li onClick=\"ChangePage(".$page.",".$sort_m.",".$sort_w.",'".urlencode($sq)."',".$rpp.");\" >".$page."</li>";
            }
                
        }
        if ($nump < $maxnump) {
            $output.="<li onClick=\"ChangePage(".($nump+1).",".$sort_m.",".$sort_w.",'".urlencode($sq)."',".$rpp.");\" >Next<img style=\"margin-left:5px\" src=\"./images/rightArrowPaging.png\"/></li>";     
        }

        
        // add sorting panel
       
        $output .="</br><li class=\"sortList\"><label for=\"sort_method_select\">Sort By:</label>
                    <select id=\"sort_method_select\" onchange=\"ChangePage(1,this.value,".$sort_w.",'".urlencode($sq)."',".$rpp.");\">";
        if ($sort_m == SORT_ALPHABETICAL)
            $output.="<option selected=\"selected\" value=".SORT_ALPHABETICAL.">Alphabetical</option>";
        else
            $output.="<option value=".SORT_ALPHABETICAL.">Alphabetical</option>";
        
        if ($sort_m == SORT_POPULARITY)
            $output.="<option selected=\"selected\" value=".SORT_POPULARITY.">Popularity</option>";
        else
            $output.="<option value=".SORT_POPULARITY.">Popularity</option>";
        
        if ($sort_m == SORT_NUM_ENTITIES)
            $output.="<option selected=\"selected\" value=".SORT_NUM_ENTITIES.">Number of Entities</option>";
        else
            $output.="<option value=".SORT_NUM_ENTITIES.">Number of Entities</option>";
        
        if ($sort_m == SORT_SEARCH_RANK)
            $output.="<option selected=\"selected\" value=".SORT_SEARCH_RANK.">Search Relevence</option>";
        else
            $output.="<option value=".SORT_SEARCH_RANK.">Search Relevence</option>";
        $output.="</select>";
        
        $output .="<select id=\"sort_way_selected\" onchange=\"ChangePage(1,".$sort_m.",this.value,'".urlencode($sq)."',".$rpp.");\">";
        if ($sort_w == SORT_ASCENDING)
            $output.="<option selected=\"selected\" value=".SORT_ASCENDING.">ASC</option>";
        else
            $output.="<option value=".SORT_ASCENDING.">ASC</option>";
        
        if ($sort_w == SORT_DESCENDING)
            $output.="<option selected=\"selected\" value=".SORT_DESCENDING.">DESC</option>";
        else
            $output.="<option value=".SORT_DESCENDING.">DESC</option>";
        $output.="</select>";
        $output .="<select id=\"rpp_selected\" onchange=\"ChangePage(1,".$sort_m.",".$sort_w.",'".urlencode($sq)."',this.value);\">";
        if ($rpp == 6)
            $output.="<option selected=\"selected\" value=6>6</option>";
        else
            $output.="<option value=6>6</option>";
        
        if ($rpp == 12)
            $output.="<option selected=\"selected\" value=12>12</option>";
        else
            $output.="<option value=12>12</option>";
        
        if ($rpp == 18)
            $output.="<option selected=\"selected\" value=18>18</option>";
        else
            $output.="<option value=18>18</option>";
        
        if ($rpp == -1 || ($rpp != 6 && $rpp != 12 && $rpp != 18))
            $output.="<option selected=\"selected\" value=-1>ALL</option>";
        else
            $output.="<option value=-1>ALL</option>";
   
        $output.="</select>";
        
        $output.="</li>";

        $output.="</ul>
            <div id=\"jumpToPageBox\">
            <form  name=\"jumpform\" method=\"GET\" action=\"./search.php\">
            <label>Jump to</label>
            <input type=\"hidden\" name=\"sort_m\" value=\"".$sort_m."\">
	     <input type=\"hidden\" name=\"sort_w\" value=\"".$sort_w."\">
	     <input type=\"hidden\" name=\"rpp\" value=\"".$rpp."\">
	     <input type=\"hidden\" name=\"sq\" value=\"".htmlspecialchars($sq)."\">
            <input type=\"text\" size=3 name=\"nump\" value=\"\"/>
            <input type=\"button\" onClick=\" var val = parseInt(document.jumpform.nump.value);ChangePage(val,".$sort_m.",".$sort_w.",'".urlencode($sq)."',".$rpp.");\" value=\"Go\"/>
            </div>";

        $output .= "</div>";
        return $output;
    }
   

    /* manage entities render functions*/
    function createMgEnMessage($msg,$cat_id,$finalize,$lmg){
        $output="";
        if($finalize == 1)
            $output.="<div id=\"mainContainer\" align=\"left\">";
        $output.="<div id=\"MgEnMessage\" align=\"left\">";
        
        if($lmg == 0)
            $output.="<img src=\"./images/searchError.gif\" alt=\"error\" id=\"MgEnGif\"/>";
        else if($lmg == 1)
            $output.="<img src=\"./images/okSearch.png\" alt=\"ok\" id=\"MgEnGif\"/>";
        
        $output.="<span id=\"MgEnMesgspan\">".$msg."</span>";
        
        $output.="<a href=\"./browseCategory.php?cat_id=".$cat_id."\"><span id=\"backToCategory\" class=\"MgEn_button\" onclick=\"window.location='./browseCategory.php";
        if (empty($cat_id))
            $output.="';\">";
        else
            $output.="?cat_id=".$cat_id."';\">";
        $output.="Visit Category</span></a>";
        $output.="</div>";
	if($finalize ==1)
		$output.="</div>"; 
        return $output;   
    }
    
    function createMgEnPanel($entities,$categ_id,$user_state,$added_ents,$ret){
        $counter = 0;
        
        $output = "<div id=\"MgEnPanel\" align=\"center\">";      
        $output.="<div id=\"MgEnCategoryId\" style=\"display:none;\">".$categ_id."</div>";
        $output.="<span id=\"MgEnAddButton\">Add New Entity</span>";
        if($ret >=0 && $ret<= 17){
            $output.="<p style=\"margin-left: 30%;text-align: center;vertical-align: middle;width: 35%;\" class=\"table_notoggle\">";
            if($ret == 7 || $ret == 10 || $ret == 12)
                $output.="<img src=\"./images/okSearch.png\" style=\"vertical-align:middle;margin-right:15px;\"/>";
            else
                $output.="<img src=\"./images/searchError.gif\" style=\"vertical-align:middle;margin-right:15px;\"/>";
            $output.=returnError($ret)."</p>";
        }
        if (empty($entities)){      
            $output.="<p id=\"AddEntInfo\" class=\"table_toggle\" style=\"display:none\"> Add New Entity Info: <span>[Hide]</span> </p>";
            $output.="<div id=\"AddEntDiv\" style=\"display:none\"></div>";
            $output .= "<p class=\"table_notoggle\">No Entities for this Category</p></div>";    
            return $output;
        }
        else {
            $output.="<p id=\"AddEntInfo\" class=\"table_toggle\" style=\"display:none\"> Add New Entity Info: <span>[Hide]</span> </p>";
            $output.="<div id=\"AddEntDiv\" style=\"display:none\"></div>";
            $output.="<p class=\"table_toggle\" id=\"enTabletoggle\">List of Entities for This Category<span>[Hide]</span></p>";
            $output.="<table class=\"MgEnTable\" cellpadding=\"0\" cellspacing=\"10\" style=\"display:table;\"><thead><tr><th class=\"headHover\" onClick=\"window.location='./manageEntities.php?cat_id=".$categ_id."&sort=0';\">Entity Name</th><th class=\"headHover\" onClick=\"window.location='./manageEntities.php?cat_id=".$categ_id."&sort=1';\">Popularity</th><th colspan=\"2\">Actions</th></tr>
                      </thead><tbody>";
            foreach($entities as $ent){
                $rank_width = 0;
                if ($ent->rate < 0)
                    $rank_width = 0;
                else if ($ent->rate > 10)
                    $rank_width = 125;
                else
                    $rank_width = floatval(round($ent->rate*12.5,2));
                if(in_array($ent->entity_id,$added_ents))
                    $output.="<tr class=\"entRow\"><th class=\"MgEnEntName\">".$ent->entity_name."<img src=\"./images/mini_new.gif\" style=\"vertical-align:middle;margin-left:10px;\"/></th><td>"; 
                else
                    $output.="<tr class=\"entRow\"><th class=\"MgEnEntName\">".$ent->entity_name."</th><td>";
                $output.="
                <div class=\"mainOuterRateEn\">
                        <div style=\"width:".$rank_width."px\" class=\"mainInnerRateEn\"></div></div>
                </td>
                <td class=\"MgEnButton\" colspan=\"2\">";
                if($user_state == LOGIC_EDITOR_MEMBER){
                    if(in_array($ent->entity_id,$added_ents)){
                        $output.="<span id=\"edit_".$ent->entity_id."_1\" class=\"MgEnEditButton\">Edit</span>
                        <span class=\"MgEnDeleteButton\" id=\"delete_".$ent->entity_id."_1\">Delete</span>";    
                    }
                    $output.="<span class=\"MgEnVisitButton\" onclick=\"window.location='./browseEntity.php?cat_id=".$categ_id."&ent_id=".$ent->entity_id."';\">Browse</span>";
                }
                else if($user_state == LOGIC_SUB_MODERATOR ||
                        $user_state == LOGIC_MODERATOR ||
                        $user_state == LOGIC_ADMINISTRATOR){
                  
                    $output.="<span class=\"MgEnEditButton\" id=\"edit_".$ent->entity_id."_0\">Edit</span>
                    <span class=\"MgEnDeleteButton\" id=\"delete_".$ent->entity_id."_0\">Delete</span>
                    <a href=\"./browseEntity.php?cat_id=".$categ_id."&ent_id=".$ent->entity_id."\"><span class=\"MgEnVisitButton\" onclick=\"window.location='./browseEntity.php?cat_id=".$categ_id."&ent_id=".$ent->entity_id."';\">Browse</span></a>";
                }
                $output.="</td></tr>";
            }
            $output.="</tbody></table></div>";
        }
        return $output;
    }
    
    /* manage users render functions*/
    function createMgUsMessage($msg,$cat_id,$finalize,$lmg){
        $output="";
        if($finalize == 1)
            $output.="<div id=\"mainContainer\" align=\"left\">";
        $output.="<div id=\"MgUsMessage\" align=\"left\">";
        
        if($lmg == 0)
            $output.="<img src=\"./images/searchError.gif\" alt=\"error\" id=\"MgUsGif\"/>";
        else if($lmg == 1)
            $output.="<img src=\"./images/okSearch.png\" alt=\"ok\" id=\"MgUsGif\"/>";
        
        $output.="<span id=\"MgUsMesgspan\">".$msg."</span>";
        
        $output.="<a href=\"./browseCategory.php?cat_id=".$cat_id."\"><span id=\"backToCategory\" class=\"MgUs_button\" onclick=\"window.location='./browseCategory.php";
        if (empty($cat_id))
            $output.="';\">";
        else
            $output.="?cat_id=".$cat_id."';\">";
        $output.="Visit Category</span></a>";
        $output.="</div>"; 
	if($finalize == 1)
		$output.="</div>";
        return $output;   
    }
    
    function createMgUsPanel($active_users,$requested_users,$categ_id){
        $members = $active_users[0];
        $editors = $active_users[1];
        $submods = $active_users[2];
        $mods    = $active_users[3];
        
        $counter = 0;
        
        $output = "<div id=\"MgUsPanel\" align=\"center\">";
        $output.="<div id=\"MgUsCategoryId\" style=\"display:none;\">".$categ_id."</div>";
        if (empty($members) && empty($editors) && empty($submods)
                && empty($mods) && empty($requested_users)){
            
            $output .= "<p class=\"table_notoggle\">No Members or Pending Membership Requests for this Category</p></div>";
            return $output;
        }
        if(empty($requested_users )){
            $output.="<p class=\"table_notoggle\"> No Pending membership requests for this category</span> </p>";    
        }
        else {
            
            $output.="<p id=\"mgur_".$counter."\" class=\"table_toggle\"> Member Requests: <span>[Hide]</span> </p>";
            $output.="<table class=\"MgUsTable\" cellpadding=\"0\" cellspacing=\"10\"><thead><tr><th>Username</th><th>Privileges</th><th colspan=\"2\">Actions</th></tr>
                      </thead><tbody>";
            $counter++;
            foreach($requested_users as $usr){
                        $output.="<tr class=\"userRow\"><th class=\"MgUrUserName\">".$usr."</th><td>
                        <select class=\"priv_select\">
                            <option selected=\"selected\" value=".CATEGORY_MEMBER.">Category Member</option>
                            <option value=".EDITOR_MEMBER.">Category Editor</option>
                            <option value=".SUB_MODERATOR.">Sub-Moderator</option>
                            <option value=".MODERATOR.">Moderator</option>
                        </select>
                        </td>
                        <td class=\"MgUsButton\" colspan=\"2\">
                            <span class=\"MgUsApproveButton\" id=\"accept_".$usr."\">Approve</span>
                            <span class=\"MgUsRejectButton\" id=\"reject_".$usr."\">Reject</span></td>
                        </tr>";
            }
            $output.="</tbody></table>";
        }
        
        if(empty($mods)){
            $output.="<p class=\"table_notoggle\"> No moderators for this category</span> </p>";    
        }
        else{
            $output.="<p id=\"mgur_".$counter."\" class=\"table_toggle\" style=\"margin-top:60px;\"> Active Moderators <span>[Hide]</span> </p>";
            $output.="<table class=\"MgUsTable\" cellpadding=\"0\" cellspacing=\"10\"><thead><tr><th>Username</th><th>Privileges</th><th colspan=\"2\">Actions</th></tr>
                      </thead><tbody>";
            $counter++;
            foreach($mods as $usr){
                $output.="<tr class=\"userRow\"><th class=\"MgUrUserName\">".$usr."</th><td>
                <select class=\"priv_select\">
                    <option value=".CATEGORY_MEMBER.">Category Member</option>
                    <option value=".EDITOR_MEMBER.">Category Editor</option>
                    <option value=".SUB_MODERATOR.">Sub-Moderator</option>
                    <option selected=\"selected\" value=".MODERATOR.">Moderator</option>
                </select>
                </td>
                <td class=\"MgUsButton\" colspan=\"2\">
                    <span class=\"MgUsUpdateButton\" id=\"accept_".$usr."\">Update</span>
                    <span class=\"MgUsDeleteButton\" id=\"reject_".$usr."\">Delete</span></td>
                </tr>";
            }
            $output.="</tbody></table>";   
        }
        
        if(empty($submods)){
            $output.="<p class=\"table_notoggle\"> No Sub-moderators for this category</span> </p>";    
        }
        else{
            $output.="<p id=\"mgur_".$counter."\" class=\"table_toggle\" style=\"margin-top:60px;\"> Active Sub-Moderators <span>[Hide]</span> </p>";
            $output.="<table class=\"MgUsTable\" cellpadding=\"0\" cellspacing=\"10\"><thead><tr><th>Username</th><th>Privileges</th><th colspan=\"2\">Actions</th></tr>
                      </thead><tbody>";
            $counter++;
            foreach($submods as $usr){
                $output.="<tr class=\"userRow\"><th class=\"MgUrUserName\">".$usr."</th><td>
                <select class=\"priv_select\">
                    <option value=".CATEGORY_MEMBER.">Category Member</option>
                    <option value=".EDITOR_MEMBER.">Category Editor</option>
                    <option selected=\"selected\"  value=".SUB_MODERATOR.">Sub-Moderator</option>
                    <option value=".MODERATOR.">Moderator</option>
                </select>
                </td>
                <td class=\"MgUsButton\" colspan=\"2\">
                    <span class=\"MgUsUpdateButton\" id=\"accept_".$usr."\">Update</span>
                    <span class=\"MgUsDeleteButton\" id=\"reject_".$usr."\">Delete</span></td>
                </tr>";
            }
            $output.="</tbody></table>";   
        }
        
        if(empty($editors)){
            $output.="<p class=\"table_notoggle\"> No Editors for this category</span> </p>";    
        }
        else{
            $output.="<p id=\"mgur_".$counter."\" class=\"table_toggle\" style=\"margin-top:60px;\"> Active Category Editors <span>[Hide]</span> </p>";
            $output.="<table class=\"MgUsTable\" cellpadding=\"0\" cellspacing=\"10\"><thead><tr><th>Username</th><th>Privileges</th><th colspan=\"2\">Actions</th></tr>
                      </thead><tbody>";
            $counter++;
            foreach($editors as $usr){
                $output.="<tr class=\"userRow\"><th class=\"MgUrUserName\">".$usr."</th><td>
                <select class=\"priv_select\">
                    <option value=".CATEGORY_MEMBER.">Category Member</option>
                    <option selected=\"selected\"value=".EDITOR_MEMBER.">Category Editor</option>
                    <option value=".SUB_MODERATOR.">Sub-Moderator</option>
                    <option value=".MODERATOR.">Moderator</option>
                </select>
                </td>
                <td class=\"MgUsButton\" colspan=\"2\">
                    <span class=\"MgUsUpdateButton\" id=\"accept_".$usr."\">Update</span>
                    <span class=\"MgUsDeleteButton\" id=\"reject_".$usr."\">Delete</span></td>
                </tr>";
            }
            $output.="</tbody></table>";   
        }
        
        if(empty($members)){
            $output.="<p class=\"table_notoggle\"> No Members for this category</span> </p>";    
        }
        else{
            $output.="<p id=\"mgur_".$counter."\" class=\"table_toggle\" style=\"margin-top:60px;\"> Active Category Members <span>[Hide]</span> </p>";
            $output.="<table class=\"MgUsTable\" cellpadding=\"0\" cellspacing=\"10\"><thead><tr><th>Username</th><th>Privileges</th><th colspan=\"2\">Actions</th></tr>
                      </thead><tbody>";
            $counter++;
            foreach($members as $usr){
                $output.="<tr class=\"userRow\"><th class=\"MgUrUserName\">".$usr."</th><td>
                <select class=\"priv_select\">
                    <option selected=\"selected\" value=".CATEGORY_MEMBER.">Category Member</option>
                    <option value=".EDITOR_MEMBER.">Category Editor</option>
                    <option value=".SUB_MODERATOR.">Sub-Moderator</option>
                    <option value=".MODERATOR.">Moderator</option>
                </select>
                </td>
                <td class=\"MgUsButton\" colspan=\"2\">
                    <span class=\"MgUsUpdateButton\" id=\"accept_".$usr."\">Update</span>
                    <span class=\"MgUsDeleteButton\" id=\"reject_".$usr."\">Delete</span></td>
                </tr>";
            }
            $output.="</tbody></table>";   
        }
        $output.="</div>";
        return $output;
        
        
        
        
        
        //$output.="<div id=\"MgUsPanel\"
        
        
    }
    /* become member render function*/
    function createMemberMessage($msg,$cat_id,$finalize,$lmg){
        $output="";
        if($finalize == 1)
            $output.="<div id=\"mainContainer\" align=\"left\">";
        $output.="<div id=\"memberMessage\" align=\"left\">";
        
        if($lmg == 0)
            $output.="<img src=\"./images/searchError.gif\" alt=\"error\" id=\"membGif\"/>";
        else if($lmg == 1)
            $output.="<img src=\"./images/okSearch.png\" alt=\"ok\" id=\"membGif\"/>";
        
        $output.="<span id=\"memberMesgspan\">".$msg."</span>";
        
        $output.="<span id=\"backToCategory\" class=\"memb_button\" onclick=\"window.location='./browseCategory.php";
        if (empty($cat_id))
            $output.="';\">";
        else
            $output.="?cat_id=".$cat_id."';\">";
        $output.="Back to Category</span>";
        $output.="</div>";
	if($finalize == 1)
		$output.="</div>"; 
        return $output;
    }
    /* Category browsing functions , creating html*/
    function createCategoryMessage($msg,$sq,$finalize,$lmg){
        $output="";
        if($finalize == 1)
            $output.="<div id=\"mainContainer\" align=\"left\">";
        $output.="<div id=\"categoryMessage\" align=\"left\">";
        
        if($lmg == 1)
            $output.="<img style=\"float:left;vertical-align:middle;\" src=\"./images/lock_closed.png\"/>";
        else if($lmg == 2)
            $output.="<img style=\"float:left;vertical-align:middle;\" src=\"./images/unlocked.png\"/>";
        else if($lmg == 3)
            $output.="<img style=\"float:left;vertical-align:middle;\" src=\"./images/cat_error.png\"/>";
        
        $output.="<span id=\"catMesgspan\">".$msg."</span>";
        
        if(!empty($sq)){
            $output.="<a href=\"./search.php?sq=".urlencode($sq)."\"><span id=\"backToSearch\" class=\"cat_button\" onclick=\"window.location='./search.php";
            $output.="?sq=".urlencode($sq)."';\">";
            $output.="Back to Search</span></a>";
        }
        $output.="</div>";

        if ($finalize == 1)
            $output.="</div>";
        return $output;
    }
        
    
   

        
    function createCategoryInfo($category_info, $state){
        // category info 0 name,1 rate,2 num entities, 3 description
        //               4 image, 5 video, 6 {can rate category 0 yes,1 has rated,2 no
        //               7 = id
        // $state = active user privilegdes
        $rank_width = 0;
        if ($category_info[1] < 0)
            $rank_width = 0;
        else if ($category_info[1] > 10)
            $rank_width = 125;
        else
            $rank_width = floatval(round($category_info[1]*12.5,2));
        
        
        $output = "<div id=\"categoryInfo\" align=\"left\">";
        
        if (empty($category_info[4]))
            $output .="<img id=\"categoryImage\" height=\"130px\" width=\"140px\" src=\"./cat_images/__not__.jpg\"/>";
        else
            $output .="<img id=\"categoryImage\" height=\"130px\" width=\"140px\" src=\"".$category_info[4]."\"/>";

        $output.=" <span id=\"categColumn2\">
            <span id=\"firstCatRow\">
            <span id=\"categoryName\">".sanitize_str_disp($category_info[0])."</span>";
            if ($category_info[6] == 0){
             // can rate
                $output.="<span id=\"ratecat_".$category_info[7]."__".$rank_width."\" class=\"rating1\"><div class=\"mainOuterRateYes\">
                        <div style=\"width:".$rank_width."px\" class=\"mainInnerRate\"></div></div>
                        </span>";
            }
            else {
                $output.="<span class=\"rating0\"><div class=\"mainOuterRateNo\">
                        <div style=\"width:".$rank_width."px\" class=\"mainInnerRate\"></div></div></span>";
            }
            
        if($category_info[6] == 0){
            $output.="<span id=\"ratecatmsg_".$category_info[7]."\" style=\"margin-left:20px;\">Rate it</span>";
        }
        else if ($category_info[6] == 1){
            $output.="<span style=\"margin-left:20px;\">Already Rated</span>";
        }
        else {
            $output.="<span style=\"margin-left:20px;\">You can't Rate</span>";
        }
        if(!empty($category_info[5])){  // An yparxei video kai i katigoria einai anoixti i , kleisti kai exei dikaiwmata member
		if(($category_info[8] == 1) || ($category_info[8] == 0  && $state != LOGIC_GUEST_VIEWER && $state != LOGIC_REGISTERED))
	 		$output.="<a style=\"margin-left:100px;\" href=\"$category_info[5]\">see video</a>";
	 }
        $output.="</span>
            <span id=\"numberEntities\">Category Contains ".$category_info[2]." Entities</span>
            <span id=\"description\">".sanitize_str_disp($category_info[3])."</span>";
        
        
        $output .="<span id=\"myButtons\">";
        
        if ($state == LOGIC_REGISTERED) {
            $output .="<a href=\"./becomeMember.php?cat_id=".$category_info[7]."\"><span  id=\"requestMember\" class=\"cat_button\">Become Member</span></a>";
        }
        else if($state == LOGIC_EDITOR_MEMBER){
            $output .="<a href=\"./manageEntities.php?cat_id=".$category_info[7]."\"><span class=\"cat_button\">Manage Entities</span></a>";
        }
        else if($state == LOGIC_SUB_MODERATOR){
            $output .="<a href=\"./manage_category.php?id=".$category_info[7]."\"><span  class=\"cat_button\">Manage Category</span></a>";
            $output .="<a href=\"./manageEntities.php?cat_id=".$category_info[7]."\"><span  class=\"cat_button\">Manage Entities</span></a>";

        }
        else if($state == LOGIC_MODERATOR ||
                $state == LOGIC_ADMINISTRATOR){
                $output .="<a href=\"./manage_category.php?id=".$category_info[7]."\"><span class=\"cat_button\">Manage Category</span></a>";
                $output .="<a href=\"./manageEntities.php?cat_id=".$category_info[7]."\"><span class=\"cat_button\">Manage Entities</span></a>";
                $output .="<a href=\"./manageUsers.php?cat_id=".$category_info[7]."\"><span class=\"cat_button\">Manage Users</span></a>";
        }

        $output.="</span></span><div class=\"clearBoth\"></div></div>";
        return $output;
        
    }
    
    function createCatCompLayout($cat_id,$num_e,$num_e_comp){
        /* cat_id = category id
         * num_e_comp = 0 print def, else custom
         * num_e   = active entities for message
         */
        $output="<div id=\"CatCompareLayout\" align=\"left\">";
        if($num_e_comp == 0){
            $output.= createCatCompDivDef($cat_id,1);
            $output.= createCatCompDiv($cat_id, $num_e_comp, 0);
        }
        else{
            $output.= createCatCompDivDef($cat_id,0);
            $output.= createCatCompDiv($cat_id, $num_e_comp, 1);    
        }
        
        $output.="<div id=\"categoryInCompMsg\"><span>".$num_e." Entities match your preferences</span></div>";
        $output.="<div class=\"clearBoth\"></div>";
        $output.="</div>";
        return $output;
            
        
    }
    
    function createCatCompDivDef($cat_id,$disp){
        $output="<div id=\"compareContainerDef\"";
        if($disp)
            $output.=" style=\"display:block;\">";
        else
            $output.=" style=\"display:none;\">";
        $output.="
        <span id=\"compareMsg\">Compare All Category's Entities</span>
        <div id=\"cmp_but_div\" align=\"center\">
            <span id=\"compare_".$cat_id."_0\" class=\"compareBtn\">Compare!!</span>
        </div></div>";
        return $output;
    }
    
    function createCatCompDiv($cat_id,$num_e,$disp){
        $output="<div id=\"compareContainer\"";
        if($disp)
            $output.=" style=\"display:block;\">";
        else
            $output.=" style=\"display:none;\">";
        $output.="
                <span id=\"compareMsg\">You have selected <span id=\"compareCount\">".$num_e."</span> entities to compare</span>
                </br><div style=\"margin-top:4px\"><span id=\"selectA\">select all</span> / <span id=\"uselectA\">unselect all</span></div>
                <div id=\"cmp_but_div\" align=\"center\">
                <span id=\"compare_".$cat_id."_1\" class=\"compareBtn\">Compare!!</span>
                </div></div>";
        return $output;
        
    }
    /*
    function createCatLink($page_info, $change, $value){
        $active_filters = $page_info[5];
        $rest = " ";
        if ($change == 0){
            // rpp
            $rest ="&nump=".$page_info[1]."&sort_m=".$page_info[2]."&sort_w=".$page_info[3]."&rpp=".$value."&attrid=".$page_info[7]; ;         
        }
        else if ($change == 1){
            // nump
            $rest ="&nump=".$value."&sort_m=".$page_info[2]."&sort_w=".$page_info[3]."&rpp=".$page_info[4]."&attrid=".$page_info[7]; 
        }
        else if ($change == 2){
            // sort_m
            $rest ="&nump=".$page_info[1]."&sort_m=".$value."&sort_w=".$page_info[3]."&rpp=".$page_info[4]."&attrid=".$page_info[7];  
        }
        else if($change == 3){
            // sort_w           
            $rest ="&nump=".$page_info[1]."&sort_m=".$page_info[2]."&sort_w=".$value."&rpp=".$page_info[4]."&attrid=".$page_info[7];  
        }
        else if($chage == 4){
            // attrid
            $rest ="&nump=".$page_info[1]."&sort_m=".$page_info[2]."&sort_w=".$page_info[3]."&rpp=".$page_info[4]."&attrid=".$value;  
        }
        
        $filter_num = count($active_filters);
        $url_link ="./browseCategory.php?cat_id=".$page_info[0].$rest."&afc=".$filter_num;
        $i= 0;
        foreach($active_filters as $na){
            $url_link.="&acf_id_".$i."=".$na["id"]."&acf_val_".$i."=".$na["pvalue"];
            $i++;
        }
        return $url_link;
           
    }*/
    
    function createCatFilterLink($page_info,$filter_id,$filter_pval,$mode){
        // e.g $filter_name = Horsepower
        //     $filter_val  = 100_200
        // mode 0 delete, mode 1 add , mode 2 just make link no add or delete
        
        $active_filters = $page_info[5]; // current active filters
        $new_active = array();
        $added = 0;
        if($mode != 2){
            foreach($active_filters as $acf){
           // if($acf["type"] == COUNTABLE){
                // * an patise to idio ksana 
                
                if ($filter_id == $acf["id"] &&$filter_pval == $acf["pvalue"]){
                    if($mode == 0)
                        $added = 1;
                }
                else if ($filter_id == $acf["id"] && $filter_pval != $acf["pvalue"]){
                    $new_active[] = array("id"=>$acf["id"],"pvalue"=>$acf["pvalue"]);
                }
                else{
                    $new_active[]= array("id"=>$acf["id"],"pvalue"=>$acf["pvalue"]);                                    
                }
                
            }
        }
        else
            $added =1;
       /*     else {
                if($filter_id == $acf["id"] ) { // an uparxei hdh  
                    if($mode == 1) {
                        $new_active[] = array("id"=>$acf["id"],"pvalue"=>$filter_pval);
                        $added = 1;
                    }
                    else // if mode delete
                        $added = 1;
                }
                else {
                    $new_active[] = array("id"=>$acf["id"],"pvalue"=>$acf["pvalue"]);
                }
            }
        }*/
        
        if($added == 0){
            $new_active[] = array("id"=>$filter_id,"pvalue"=>$filter_pval);
        }
        
        // exoume enimerwsei ta filters
        $filter_num = count($new_active);
        $url_link ="./browseCategory.php?cat_id=".$page_info[0]."&nump=".$page_info[1]."&sort_m=".$page_info[2]."&attrid=".$page_info[7]."&sort_w=".$page_info[3]."&rpp=".$page_info[4]."&afc=".$filter_num."&sq=".urlencode($page_info[9]);
        $i= 0;
        foreach($new_active as $na){
            $url_link.="&acf_id_".$i."=".$na["id"]."&acf_val_".$i."=".urlencode($na["pvalue"]);
            $i++;
        }
        return $url_link;
    }
    
    
    function createFilters($page_info,$filters){
        // category page client info 
            // 0 -> category id
            // 1 -> number of current page
            // 2 -> type of sorting
            // 3 -> way of sorting
            // 4 -> results per page
            // 5 -> active filters array
            //    5."id"
            //    5."name"   =>   
            //    5."pvalue" => 
            //    5."value"  => 
            // $filters
            //      "ID"
            //      "NAME"
            //      "TYPE" 
            //      "VALUES" => "value","pvalue","count"
            //              pvalue will be in url , link

        $output = "";
        
        if(!empty($filters)){
            $total_filters = count($filters);
            $count_shown = 0;
            $url_exec = createCatFilterLink($page_info, -1,-1,2);
            $output ="<div id=\"filterTable\">
                 <table cellpadding=\"0\" cellspacing=\"0\">
                 <thead><tr><th>
                    Active Filters<a id=\"clearFilters\" href=\"".$url_exec."\"> Clear All</a></th> <td colspan=\"2\">";
            $active_filters = $page_info[5];
            $mixed_filters  = $page_info[8];
            if(!empty($mixed_filters)) {
                //$output.="<span id=\"activeLabel\">Active Filters:</span>";
                foreach($mixed_filters as $flid=>$acf){
                    $output.="<span class=\"pathFilter\"><a>".sanitize_str_disp($acf[0]["name"])."<a class=\"vs\">:</a><a class=\"parenthesis\">(</a></a>";
                    for($u = 0; $u < count($acf) ;$u++){
                        $url_filter = createCatFilterLink($page_info,$flid,$acf[$u]["pvalue"],0);                
                        $output.="<span class=\"pathFilterVal\"><a>".sanitize_str_disp($acf[$u]["value"])."</a>";
                        if ($u != count($acf)-1)
                            $output.="<a href=\"".$url_filter."\" class=\"deleteFilter\"><img src=\"./images/close_attr.png\"/></a><a class=\"filval_delim\">,</a></span>";
                        else
                            $output.="<a href=\"".$url_filter."\" class=\"deleteFilter\"><img src=\"./images/close_attr.png\"/></a><a class=\"parenthesis\">)</a></span>";                    
                    }
                    $output.="</span><a class=\"fil_delim\"></a>";
                }
                
            }
            $output.="</td></tr></thead>";
            
        
            $output.="<tbody>";
            $count = 0 ;
            foreach($filters as $filter) {
                $count++;
                if($count <= 5){
                    $count_shown++;
                    $output.="<tr class=\"filterRow\">
                    <th><span class=\"filterName\">".sanitize_str_disp($filter["NAME"])."</span></th>
                    <td class=\"filterVals\">";
                    $ij = 0;
                    foreach($filter["VALUES"] as $fil_val){
                        $is_active = 0;
                        foreach($active_filters as $acf){
                            if($acf["value"] == $fil_val["value"] && $acf["id"]==$filter["ID"]){
                                $is_active = 1;
                            }
                        }

                        if($ij < 8) {
                            $url_filter = createCatFilterLink($page_info,$filter["ID"],$fil_val["pvalue"],1);
                            if($is_active)
                                $output.="<span class=\"filterValues\"><a class=\"activeFilterValue\" href=\"".$url_filter."\">".sanitize_str_disp($fil_val["value"])."<span class=\"filterCount\">(".$fil_val["count"].")</span> </a></span>";
                            else
                                $output.="<span class=\"filterValues\"><a href=\"".$url_filter."\">".sanitize_str_disp($fil_val["value"])."<span class=\"filterCount\">(".$fil_val["count"].")</span> </a></span>";
                        }
                        else {
                            $url_filter = createCatFilterLink($page_info,$filter["ID"],$fil_val["pvalue"],1);
                            if(!$is_active)
                                $output.="<span class=\"filterValuesHid".$count."\" style=\"display:none\"><a href=\"".$url_filter."\">".sanitize_str_disp($fil_val["value"])."<span class=\"filterCount\">(".$fil_val["count"].")</span> </a></span>";
                            else
                                $output.="<span class=\"filterValues\"><a class=\"activeFilterValue\" href=\"".$url_filter."\">".sanitize_str_disp($fil_val["value"])."<span class=\"filterCount\">(".$fil_val["count"].")</span> </a></span>";
                        }
                        $ij++;
                        
                    }
                    $output.="</td>";
                    if($ij >= 9){
                        $output.="<td class=\"expandAttribute\"><img stateexp=\"close\" rowexp=\"".$count."\" src=\"./images/expand.gif\" class=\"expandFilterValsImage\"/></td>";
                    }
                    else
                        $output.="<td class=\"expandAttribute\"><a></a></td>";
                    $output.="</tr>"; 
                }
                else {
                    $is_active = 0;
                    foreach($active_filters as $acf){
                        if($acf["id"] == $filter["ID"]){
                                $is_active = 1;
                        }
                    }
                    if(!$is_active){
                        $output.="<tr class=\"filterRowHid\">
                        <th>
                        <div class=\"forslide\">                    
                        <span class=\"filterName\">".sanitize_str_disp($filter["NAME"])."</span></div></th>
                        <td class=\"filterVals\"><div class=\"forslide\">";
                        $ij = 0;
                        foreach($filter["VALUES"] as $fil_val){

                            $is_active = 0;
                            foreach($active_filters as $acf){
                                if($acf["value"] == $fil_val["value"] && $acf["id"]==$filter["ID"]){
                                    $is_active = 1;
                                }
                            }

                            if($ij < 10) {
                                $url_filter = createCatFilterLink($page_info,$filter["ID"],$fil_val["pvalue"],1);
                                if(!$is_active) 
                                    $output.="<span class=\"filterValues\"><a href=\"".$url_filter."\">".sanitize_str_disp($fil_val["value"])."<span class=\"filterCount\">(".$fil_val["count"].")</span> </a></span>";
                                else
                                    $output.="<span class=\"filterValues\"><a class=\"activeFilterValue\" href=\"".$url_filter."\">".sanitize_str_disp($fil_val["value"])."<span class=\"filterCount\">(".$fil_val["count"].")</span> </a></span>";

                            }
                            else {
                                $url_filter = createCatFilterLink($page_info,$filter["ID"],$fil_val["pvalue"],1);
                                if(!$is_active)
                                    $output.="<span class=\"filterValuesHid".$count."\" style=\"display:none\"><a href=\"".$url_filter."\">".sanitize_str_disp($fil_val["value"])."<span class=\"filterCount\">(".$fil_val["count"].")</span> </a></span>";
                                else
                                    $output.="<span class=\"filterValues\"><a class=\"activeFilterValue\" href=\"".$url_filter."\">".sanitize_str_disp($fil_val["value"])."<span class=\"filterCount\">(".$fil_val["count"].")</span> </a></span>";      
                            }
                            $ij++;

                        }
                        $output.="</div></td>";
                        if($ij >= 11){
                            $output.="<td class=\"expandAttribute\"><div class=\"forslide\"><img stateexp=\"close\" rowexp=\"".$count."\" src=\"./images/expand.gif\" class=\"expandFilterValsImage\"/></div></td>";
                        }
                        else
                            $output.="<td class=\"expandAttribute\"><div class=\"forslide\"></div></td>";
                        $output.="</tr>"; 
                    }
                    else{  // if was hidden filter then spawn it
                        $count_shown++;
                        $output.="<tr class=\"filterRow\">
                        <th><span class=\"filterName\">".sanitize_str_disp($filter["NAME"])."</span></th>
                        <td class=\"filterVals\">";
                        $ij = 0;
                        foreach($filter["VALUES"] as $fil_val){
                            $is_active = 0;
                            foreach($active_filters as $acf){
                                if($acf["value"] == $fil_val["value"] && $acf["id"]==$filter["ID"]){
                                    $is_active = 1;
                                }
                            }

                            if($ij < 10) {
                                $url_filter = createCatFilterLink($page_info,$filter["ID"],$fil_val["pvalue"],1);
                                if($is_active)
                                    $output.="<span class=\"filterValues\"><a class=\"activeFilterValue\" href=\"".$url_filter."\">".sanitize_str_disp($fil_val["value"])."<span class=\"filterCount\">(".$fil_val["count"].")</span> </a></span>";
                                else
                                    $output.="<span class=\"filterValues\"><a href=\"".$url_filter."\">".sanitize_str_disp($fil_val["value"])."<span class=\"filterCount\">(".$fil_val["count"].")</span> </a></span>";
                            }
                            else {
                                $url_filter = createCatFilterLink($page_info,$filter["ID"],$fil_val["pvalue"],1);
                                if(!$is_active)
                                    $output.="<span class=\"filterValuesHid".$count."\" style=\"display:none\"><a href=\"".$url_filter."\">".sanitize_str_disp($fil_val["value"])."<span class=\"filterCount\">(".$fil_val["count"].")</span> </a></span>";
                                else
                                    $output.="<span class=\"filterValues\"><a class=\"activeFilterValue\" href=\"".$url_filter."\">".sanitize_str_disp($fil_val["value"])."<span class=\"filterCount\">(".$fil_val["count"].")</span> </a></span>";
                            }
                            $ij++;

                        }
                        $output.="</td>";
                        if($ij >= 11){
                            $output.="<td class=\"expandAttribute\"><img stateexp=\"close\" rowexp=\"".$count."\" src=\"./images/expand.gif\" class=\"expandFilterValsImage\"/></td>";
                        }
                        else
                            $output.="<td class=\"expandAttribute\"><a></a></td>";
                        $output.="</tr>";    
                    }
                }
            }
            if ($total_filters > $count_shown) {
                $output.="<tr style=\"text-align:center\">
                        <th id=\"showMoreFilters\">
                        <a id=\"expandFilters\">
                        <img id=\"expandFiltersImage\" src=\"./images/arrow_down.png\"/> Show All Filters
                        </a></th></tr></tbody></table></div>";
            }
            else {
                $output.="</tbody></table></div>";    
            }
            
        }
        return $output;
            
            

        
    }
    
    function createCatLinkExcept($page_info,$exc){
        $active_filters = $page_info[5];
        $sq = $page_info[9];
        $rest = " ";
        if ($exc == 0){
            // rpp
            $rest ="&nump=".$page_info[1]."&sort_m=".$page_info[2]."&sort_w=".$page_info[3]."&attrid=".$page_info[7]."&sq=".urlencode($sq);         
        }
        else if ($exc == 1){
            // nump
            $rest ="&sort_m=".$page_info[2]."&sort_w=".$page_info[3]."&rpp=".$page_info[4]."&attrid=".$page_info[7]."&sq=".urlencode($sq);  
        }
        else if ($exc == 2){
            // sort_m
            $rest ="&nump=".$page_info[1]."&sort_w=".$page_info[3]."&rpp=".$page_info[4]."&attrid=".$page_info[7]."&sq=".urlencode($sq);  
        }
        else if($exc == 3){
            // sort_w           
            $rest ="&nump=".$page_info[1]."&sort_m=".$page_info[2]."&rpp=".$page_info[4]."&attrid=".$page_info[7]."&sq=".urlencode($sq);  
        }
        else if($exc == 4){
            // attrid
            $rest ="&nump=".$page_info[1]."&sort_m=".$page_info[2]."&rpp=".$page_info[4]."&sort_w=".$page_info[3]."&sq=".urlencode($sq);
        }
        
        $filter_num = count($active_filters);
        $url_link ="./browseCategory.php?cat_id=".$page_info[0].$rest."&afc=".$filter_num;
        $i= 0;
        foreach($active_filters as $na){
            $url_link.="&acf_id_".$i."=".$na["id"]."&acf_val_".$i."=".urlencode($na["pvalue"]);
            $i++;
        }
        return $url_link;    
    }
    function createCatLayout($page_info,$maxnump){
        // category page client info 
        // 0 -> category id
        // 1 -> number of current page
        // 2 -> type of sorting
        // 3 -> way of sorting
        // 4 -> results per page
        $cat_id = $page_info[0];
        $nump = $page_info[1];
        $sort_m = $page_info[2];
        $sort_w = $page_info[3];
        $rpp = $page_info[4];
        $attr_id = $page_info[7];
        
        
        $output = "<div class=\"catPaging\" align=\"left\"><ul>";
        if ($maxnump > 0){
            $pages = createPageArray($nump, $maxnump);
            $url_exec = createCatLinkExcept($page_info, 1);
            $output .= "<li id=\"jumpToPage\"><img style=\"margin-right:5px\" src=\"./images/downArrowPaging.png\"/>
                       Page ".$nump." From ".$maxnump."</li>";
            if($nump > 1) {
                      $output .="<li onClick=\"window.location='".$url_exec."&nump=".($nump-1)."';\" ><img style=\"margin-right:5px\" src=\"./images/leftArrowPaging.png\"/>Previous</li>";
            }

            foreach($pages as $page) {
                if ($page == -1) {
                    $output.="<li id=\"nonHover\">..</li>";
                }
                else if ($page == $nump){
                    $output.="<li onClick=\"window.location='".$url_exec."&nump=".($nump)."';\" id=\"Active\">".$nump."</li>";
                }
                else {
                    $output.="<li onClick=\"window.location='".$url_exec."&nump=".$page."';\" >".$page."</li>";
                }

            }
            if ($nump < $maxnump) {
                $output.="<li onClick=\"window.location='".$url_exec."&nump=".($nump+1)."';\" >Next<img style=\"margin-left:5px\" src=\"./images/rightArrowPaging.png\"/></li>";     
            }
        }

        
        // add sorting panel

        $url_exc = createCatLinkExcept($page_info, 2);
        $output .="</br><li class=\"sortList\"><label for=\"sort_method_select\">Sort By:</label>
                    <select id=\"sort_method_select\" onchange=\"ChangeCatPage('".$url_exc."','sort_m',this.value);\">";
        if ($sort_m == SORT_ALPHABETICAL)
            $output.="<option selected=\"selected\" value=".SORT_ALPHABETICAL.">Alphabetical</option>";
        else
            $output.="<option value=".SORT_ALPHABETICAL.">Alphabetical</option>";
        
        if ($sort_m == SORT_POPULARITY)
            $output.="<option selected=\"selected\" value=".SORT_POPULARITY.">Popularity</option>";
        else
            $output.="<option value=".SORT_POPULARITY.">Popularity</option>";
        
        if ($sort_m == SORT_BY_ATTRIBUTE)
            $output.="<option selected=\"selected\" value=".SORT_BY_ATTRIBUTE.">By Attribute</option>";
        else
            $output.="<option value=".SORT_BY_ATTRIBUTE.">By Attribute</option>";
        
        $output.="</select>";
        
        if ($sort_m == SORT_BY_ATTRIBUTE){
            // TAKE numeric attributes 
            $count_attrs = $page_info[6];
            $url_exc = createCatLinkExcept($page_info, 4);
            $output .="<select id=\"attr_id_selected\" onchange=\"ChangeCatPage('".$url_exc."','attrid',this.value);\">";
            
            $exists = 0;
            foreach($count_attrs as $atr){
                if ($atr[0] == $attr_id)
                    $exists = 1;
            }
            $first_a = 1;
            foreach($count_attrs as $atr){
                // $atr[0] = id , $attr[1] = name
                if ($first_a == 1 && $exists == 0){
                    $output.="<option selected=\"selected\" value=".$atr[0].">".sanitize_str_disp($atr[1])."</option>";
                    $first_a = 0;
                }
                else {
                    if ($attr_id == $atr[0])
                        $output.="<option selected=\"selected\" value=".$atr[0].">".sanitize_str_disp($atr[1])."</option>";
                    else
                        $output.="<option value=".$atr[0].">".sanitize_str_disp($atr[1])."</option>";
                }
            }
            $output.="</select>";        
        }
        
        $url_exc = createCatLinkExcept($page_info, 3);
        $output .="<select id=\"sort_way_selected\" onchange=\"ChangeCatPage('".$url_exc."','sort_w',this.value);\">";
        if ($sort_w == SORT_ASCENDING)
            $output.="<option selected=\"selected\" value=".SORT_ASCENDING.">ASC</option>";
        else
            $output.="<option value=".SORT_ASCENDING.">ASC</option>";
        
        if ($sort_w == SORT_DESCENDING)
            $output.="<option selected=\"selected\" value=".SORT_DESCENDING.">DESC</option>";
        else
            $output.="<option value=".SORT_DESCENDING.">DESC</option>";
        $output.="</select>";
        
        $url_exc = createCatLinkExcept($page_info, 0);
        $output .="<select id=\"rpp_selected\" onchange=\"ChangeCatPage('".$url_exc."','rpp',this.value);\">";
        if ($rpp == 5)
            $output.="<option selected=\"selected\" value=5>5</option>";
        else
            $output.="<option value=5>5</option>";
        
        if ($rpp == 10 || ($rpp != 5 && $rpp != 10 && $rpp != 20 && $rpp != 30))
            $output.="<option selected=\"selected\" value=10>10</option>";
        else
            $output.="<option value=10>10</option>";
        
        if ($rpp == 20)
            $output.="<option selected=\"selected\" value=20>20</option>";
        else
            $output.="<option value=20>20</option>";
        
        if ($rpp == 30)
            $output.="<option selected=\"selected\" value=30>30</option>";
        else
            $output.="<option value=30>30</option>";
        
        if ($rpp == -1)
            $output.="<option selected=\"selected\" value=-1>ALL</option>";
        else
            $output.="<option value=-1>ALL</option>";
   
        $output.="</select>";
        
        $output.="</li>";
        $url_exc = createCatLinkExcept($page_info, 1);
        $output.="</ul>
            <div id=\"jumpToPageBox\">
            <form  id=\"jumpCatSub\" method=\"get\" name=\"jumpform\">
            <label>Jump to</label>
            <input type=\"hidden\" id=\"url_ex\" value=\"".$url_exc."\">
            <input type=\"text\" size=3 name=\"nump\" value=\"\"/>
            <input type=\"button\" onClick=\"var val = parseInt(document.jumpform.nump.value);ChangeCatPage('".$url_exc."','nump',val);\" value=\"Go\"/>
		</form>   
         </div>";
        $output .= "</div>";

        return $output;
    }
  
function createBottomCatLayout($page_info,$maxnump){
        // category page client info 
        // 0 -> category id
        // 1 -> number of current page
        // 2 -> type of sorting
        // 3 -> way of sorting
        // 4 -> results per page
        $cat_id = $page_info[0];
        $nump = $page_info[1];
        $sort_m = $page_info[2];
        $sort_w = $page_info[3];
        $rpp = $page_info[4];
        $attr_id = $page_info[7];
        
        
        $output = "<div class=\"catPaging\" align=\"center\" style=\"margin-bottom:50px\"><ul>";
        if ($maxnump > 0){
            $pages = createPageArray($nump, $maxnump);
            $url_exec = createCatLinkExcept($page_info, 1);
            $output .= "<li id=\"jumpToPage2\"><img style=\"margin-right:5px\" src=\"./images/downArrowPaging.png\"/>
                       Page ".$nump." From ".$maxnump."</li>";
            if($nump > 1) {
                      $output .="<li onClick=\"window.location='".$url_exec."&nump=".($nump-1)."';\" ><img style=\"margin-right:5px\" src=\"./images/leftArrowPaging.png\"/>Previous</li>";
            }

            foreach($pages as $page) {
                if ($page == -1) {
                    $output.="<li id=\"nonHover\">..</li>";
                }
                else if ($page == $nump){
                    $output.="<li onClick=\"window.location='".$url_exec."&nump=".($nump)."';\" id=\"Active\">".$nump."</li>";
                }
                else {
                    $output.="<li onClick=\"window.location='".$url_exec."&nump=".$page."';\" >".$page."</li>";
                }

            }
            if ($nump < $maxnump) {
                $output.="<li onClick=\"window.location='".$url_exec."&nump=".($nump+1)."';\" >Next<img style=\"margin-left:5px\" src=\"./images/rightArrowPaging.png\"/></li>";     
            }
        }
	 $output.="<li style=\"float:right;\"><a style=\"padding-left:10px;padding-right:10px;\" id=\"backToTop\">Back to Top</a></li>";
        $url_exc = createCatLinkExcept($page_info, 1);
        $output.="</ul>
            <div id=\"jumpToPageBox2\">
            <form  id=\"jumpCatSub2\" method=\"get\" name=\"botjumpform2\">
            <label>Jump to</label>
            <input type=\"hidden\" id=\"url_ex2\" value=\"".$url_exc."\">
            <input type=\"text\" size=3 name=\"nump\" value=\"\"/>
            <input type=\"button\" onClick=\"var val = parseInt(document.botjumpform2.nump.value);ChangeCatPage('".$url_exc."','nump',val);\" value=\"Go\"/> 
		</form>  
         </div>";
        $output .= "</div>";

        return $output;
    }

    
    function createCatResultsEntities($entities,$category_id){
        // entities array of entities contains
        /*
         * [0] -> entity_id
         * [1] -> entity_name
         * [2] -> entity_desc
         * [3] -> entity_image
         * [4] -> entity_video
         * [5] -> entity_rate
         * [6] -> {array of values }
         * [7] -> user_can_rate ( 0 means no , 1 means yes)
         * [8] -> is_added_to_compare ( 0 means no , 1 means yes)
         */
        
       
        $output = "<div id=\"catEntResults\" align=\"left\"><ul class=\"entitiesList\">";
        foreach($entities as $entity) {
            
            if ($entity[5] < 0)
                $rank_width = 0;
            else if ($entity[5] > 10)
                $rank_width = 125;
            else
                $rank_width = floatval(round($entity[5]*12.5,2));
            
            $output.="<li class=\"entityBox\">
            <div class=\"entFirstCol\">
            <ul>
            <li class=\"firstColComp\">
                <input id=\"entity_".$entity[0]."_".$category_id."\" type=\"checkbox\" class=\"compCheck\" ";
            if($entity[8] == 1){
                $output.="checked ";
            }
            $output.="/> Add to Compare                
            </li>
            <li class=\"firstColImg\">";
            
            if(empty($entity[3]))
                $output.="<img class=\"imagelinkr\" onclick=\"window.location='./browseEntity.php?cat_id=".$category_id."&ent_id=".$entity[0]."';\" src=\"./cat_images/__not__.jpg\" width=\"125px\" height=\"110px\" />";
            else
                $output.="<img class=\"imagelinkr\" onclick=\"window.location='./browseEntity.php?cat_id=".$category_id."&ent_id=".$entity[0]."';\" src=\"".$entity[3]."\" width=\"125px\" height=\"110px\" />";
            
            $output.="</li>";
            
            if($entity[7] == 2){
                
                $output.="<li class=\"firstColRateYes\" >
                <div align=\"center\">
                    <div align=\"left\" id=\"rateent_".$entity[0]."_".$category_id."\" class=\"mainOuterRateInYes\">
                    <div style=\"width:".$rank_width."px;\" class=\"mainInnerRateIn\" id=\"innerwrap_".$entity[0]."_".$rank_width."\" ></div>
                    </div> 
                </div><span id=\"rate_".$entity[0]."\" class=\"rateEntity\">Rate it!</span>";
            }
            else if($entity[7] == 1){
                $output.="<li class=\"firstColRateNo\" >
                <div align=\"center\"><div align=\"left\" class=\"mainOuterRateInNo\">
                    <div style=\"width:".$rank_width."px;\" class=\"mainInnerRateIn\"></div>
                    </div> 
                    <span class=\"alreadyRated\">You have Rate it!</span></div>";
            }
            else if($entity[7] == 0){
                $output.="<li class=\"firstColRateNo\" >
                <div align=\"center\"><div align=\"left\" class=\"mainOuterRateInNo\">
                    <div style=\"width:".$rank_width."px;\" class=\"mainInnerRateIn\"></div>
                    </div> 
                    <span class=\"alreadyRated\">You can't rate</span></div>";
            }
            $output.="
            </li>
            </ul>	
            </div>

            <div id=\"nowrap_".$entity[0]."_".$rank_width."\" class=\"entSecCol\">
            <a href=\"./browseEntity.php?cat_id=".$category_id."&ent_id=".$entity[0]."\"><span class=\"secColName\">".sanitize_str_disp($entity[1])."</span></a>
            <div class=\"secColAttr\" align=\"left\">
            <div class=\"AttrDivTabl\" align=\"left\">";
            
            if(count($entity[6]) > 0) {
                $output.="<table class=\"entityAttributes\" cellpadding=\"2\" cellspacing=\"2\"><tbody>";
                $ij = 0;
                foreach($entity[6] as $entval){
                    $ent_value = $entval["value"];
                    if(empty($entval["value"]))
                        $ent_value = "No value given";
                    if($ij < 8) {
                        $output.="<tr class=\"VisibleAttr\"><td class=\"entAttrName\">".sanitize_str_disp($entval["name"])."</td>
                                  <td class=\"semiNamVal\">:</td>
                                  <td class=\"entAttrVal\">".sanitize_str_disp($ent_value)."</td>
                                  </tr>";
                    }
                    else{
                        $output.="<tr class=\"nonVisibleAttr\"><td class=\"entAttrName\">".sanitize_str_disp($entval["name"])."</td>
                                  <td class=\"semiNamVal\">:</td>
                                  <td class=\"entAttrVal\">".sanitize_str_disp($ent_value)."</td>
                                  </tr>";
                    }
                    $ij++;
                }
                $output.="</tbody></table>";
                if ($ij > 8){ /*has passed the limit of 6 attribute*/
                    $output.="<div class=\"expandAttrTable\">
                                <a class=\"texpand\">
                                    <img border=\"0\" src=\"./images/expand_arrow_down.png\" />
                                </a>
                                <a class=\"cexpand\">
                                    <img border=\"0\" src=\"./images/expand_arrow_up.png\" />
                                </a>
                               </div>";
                }
            }
            $desc = "No description given";
            if (!empty($entity[2])) {
                $desc = sanitize_str_disp($entity[2]);
            }
            $output.="</div>
                    <div class=\"entityDesc\">
                    <span>Description:</br>".$desc."</span>
                    </div>   
                    <div class=\"clearBoth\"></div>
                    </div>

                    <div class=\"clearBgr\"><a></a></div>
			</div>
                </li>";
            
        }
        $output.="</ul></div>";
        return $output;
    }
    
    
    
    function renderCategoryError($error){
        if($error == NO_CATEGORY_ID_GIVEN){
            return createCategoryMessage("You have not given a category ID","",1,3);
        }
        else if ($error == CATEGORY_DOES_NOT_EXIST){
            return createCategoryMessage ("This category does not exist","",1,3);
        }
        else if ($error == GUEST_VIEWER){
            return createCategoryMessage("You have to register first to become a member of this Category, Please Register Here","",0,3);
        }
        else if ($error == NOT_MEMBER_OF_CAT){
            return createCategoryMessage ("Please click on the Become Member button, to view this category","",0,3);
        }
        else if($error == WRONG_PAGE){
            return createCategoryMessage("Internal error happened,not such page","",1,3);
        }
    }


    
    
    /* pan add /edit item */
    ////////////////////////////////////////////////////////////////////////////////
    
    function renderAddEntityForm($catid, $attrs){
        $num=count($attrs);
        if($num==0)
            echo "Category has no attributes";

	 echo "<form id=\"addedititemform\" method=\"post\" action=\"./additem.php\" enctype=\"multipart/form-data\">";
        echo "<table id=\"AddItemInner\" cellspacing=5><tbody>";
        echo "<tr>";
        echo"<th>";
        echo"</th>";
        echo"<td>";
        echo"</td>";
        echo "</tr>";
        
        echo "<input name=\"cat_id\" type=\"hidden\" value=\"".$catid."\">";
        echo "<tr>";
        echo"<th><span class=\"overlay\">";
        echo "<label class=\"dialogLabel\">Name</label></span>";
        echo"</th>";
        echo"<td>";
        echo "<input class=\"resetInputAble\" class=\"name\" name=\"name\" onblur=\"entCheckName3(this.value);\" onkeyup=\"entCheckName(this.value);\" type=\"text\"><span class=\"entError\"></span>";
        echo"</td>";
        echo "</tr>";
        echo "<tr>";
        echo"<th><span class=\"overlay\">";
        echo "<label class=\"dialogLabel\">Description</label>";
        echo "<span class=\"dialogOptional\">[optional]</span></span>";
        echo"</th>";
        echo"<td>";
        echo "<textarea class=\"resetInputAble\" name=\"desc\" rows=5 onkeyup=\"entCheckDesc(this.value);\" style=\"vertical-align: middle;\"></textarea><span class=\"entError\"></span>";
        echo"</td>";
        echo "</tr>";
        echo "<tr>";
        echo"<th><span class=\"overlay\">";
        echo "<label class=\"dialogLabel\">Image</label>";
        echo "<span class=\"dialogOptional\">[optional]</span></span>";
        echo"</th>";
        echo"<td>";
        //echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"500\" />";
        echo "<input class=\"resetInputAble\" name=\"img\" type=\"file\">";
        echo"</td>";
        echo "</tr>";
        echo "<tr>";
        echo"<th><span class=\"overlay\">";
        echo "<label class=\"dialogLabel\">Video</label>";
        echo "<span class=\"dialogOptional\">[optional]</span></span>";
        echo"</th>";
        echo"<td>";
        echo "<input class=\"resetInputAble\" name=\"vid\" type=\"text\">";       
        echo "<span class=\"dialogOptional\">(youtube link)</span>";
        echo"</td>";
        echo "</tr>";
        echo "<tr><th></br></th><td></br></td></tr>";
        
        for ($i = 0; $i < $num; $i++ ){
            echo "<tr>";
            echo"<th><span class=\"overlay\">";
            echo "<label class=\"dialogLabel\">".sanitize_str_disp($attrs[$i]->name)."</label></span>";
            echo "</th>";
            //echo"<label for="name">Name</label>";
            
            //echo $attrs[$i]->comparability;
            echo"<td>";
            if($attrs[$i]->comparability == DISTINCT){
                $dnum=count($attrs[$i]->type_elements);
                //echo $dnum;
                echo "<select class=\"resetSelectInp\" name=\"attr[]\">";
                for($j=0;$j<$dnum;$j++){
                    echo "<option value=\"".sanitize_str_disp($attrs[$i]->type_elements[$j]["Value"])."\">".sanitize_str_disp($attrs[$i]->type_elements[$j]["Value"])."</option>";
                    //echo $attrs[$i]->type_elements[$j]["Value"];
                }
                echo "</select>";
            }
            else{
                
                if($attrs[$i]->comparability == COUNTABLE){
                        echo "<input class=\"resetInputAble\" name=\"attr[]\" type=\"text\" onkeyup=\"entCheckCountable(this.value,".floatval($attrs[$i]->type_elements["Lower Limit"]).",".floatval($attrs[$i]->type_elements["Upper Limit"]).",".$i.");\">";
                        echo "<span class=\"dialogOptional\">(numerical";
                        echo " from ".floatval($attrs[$i]->type_elements["Lower Limit"]);
                        echo " to ".floatval($attrs[$i]->type_elements["Upper Limit"]);
                        echo ")</span><span class=\"entError\"></span>";
                }
                else{
                    echo "<input class=\"resetInputAble\" name=\"attr[]\" type=\"text\" onkeyup=\"entCheckUncomparable(this.value,".$i.");\">";
                    echo "<span class=\"dialogOptional\">(Uncomparable Attribute)</span><span class=\"entError\"></span>";    
                }
            }
            echo"</td>";
            echo "</tr>";
        }
        echo "<tr><th></br></th><td></br></td></tr>";
        echo "<tr><th style=\"height: 68px;\" colspan=\"2\" id=\"addEnInBtns\">
                 <span class=\"MgEnInSubmitButton\">Submit</span>
                    <span class=\"MgEnInResetButton\">Reset</span>
                    <span class=\"MgEnInCloseButton\">Close</span>
                </th></tr>";
        echo "</tbody>";
        echo"</table>";
        echo "</form>";
    }

     
  
    function renderEditEntityForm($catid, $entid,$attrs, $values){
        $num=count($attrs);
        if($num==0)
            echo "<span>Category has no attributes";

        echo "<form id=\"addedititemform\" method=\"post\" action=\"./edititem.php\" enctype=\"multipart/form-data\">";
        echo "<input name=\"cat_id\" type=\"hidden\" value=\"".$catid."\">";
        echo "<input name=\"ent_id\" type=\"hidden\" value=\"".$entid."\">";

        echo "<table id=\"AddItemInner\" cellspacing=5><tbody>";
        echo "<tr>";
        echo"<th>";
        echo"</th>";
        echo"<td>";
        echo"</td>";
        echo "</tr>";


        echo "<tr>";
        echo"<th><span class=\"overlay\">";
        echo "<label class=\"dialogLabel\">Name</label></span>";
        echo"</th>";
        echo"<td>";
        echo "<input class=\"resetInputAble\" value=\"".sanitize_str_disp($values->entity_name)."\" class=\"name\" name=\"name\" type=\"text\" onblur=\"entCheckName4(this.value,'".sanitize_str_disp($values->entity_name)."');\" onkeyup=\"entCheckName2(this.value,'".sanitize_str_disp($values->entity_name)."');\"><span class=\"entError\"></span>";
        echo"</td>";
        echo "</tr>";
        echo "<tr>";
        echo"<th><span class=\"overlay\">";
        echo "<label class=\"dialogLabel\">Description</label>";
        echo "<span class=\"dialogOptional\">[optional]</span></span>";
        echo"</th>";
        echo"<td>";
        echo "<textarea class=\"resetInputAble\" name=\"desc\" rows=5 onkeyup=\"entCheckDesc(this.value);\">".sanitize_str_disp($values->entity_description)."</textarea><span class=\"entError\"></span>";
        echo"</td>";
        echo "</tr>";
        echo "<tr>";
        echo"<th><span class=\"overlay\">";
        echo "<label class=\"dialogLabel\">Image</label>";
        echo "<span class=\"dialogOptional\">[optional]</span></span>";
        echo"</th>";
        echo"<td>";
        //echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"500\" />";
        echo "<input class=\"resetInputAble\" name=\"img\" type=\"file\">";
        $image_path = $values->entity_image;
        if(empty($image_path))
            $image_path = "./cat_images/__not__.jpg";
        echo "<span id=\"oldImageMgEn\" style=\"margin-left:20px;vertical-align:middle;\">Old Image:<img src=\"".$image_path."\" id=\"imageEditEnt\"/></span>";
        echo"</td>";
        echo "</tr>";
        echo "<tr>";
        echo"<th><span class=\"overlay\">";
        $video_link = sanitize_str_disp($values->entity_video);
        if(empty($video_link))
            $video_link = " ";
        echo "<label class=\"dialogLabel\">Video</label>";
        echo "<span class=\"dialogOptional\">[optional]</span></span>";
        echo"</th>";
        echo"<td>";
        echo "<input class=\"resetInputAble\" value=\"".$video_link."\"name=\"vid\" type=\"text\">";       
        echo "<span class=\"dialogOptional\">(youtube link)</span>";
        echo"</td>";
        echo "</tr>";
        echo "<tr><th></br></th><td></br></td></tr>";
       
        for ($i = 0; $i < $num; $i++ ){
            echo "<tr>";
            echo"<th><span class=\"overlay\">";
            echo "<label class=\"dialogLabel\">".sanitize_str_disp($attrs[$i]->name)."</label></span>";
            echo "</th>";
            //echo"<label for="name">Name</label>";
            
            //echo $attrs[$i]->comparability;
            echo"<td>";
            if($attrs[$i]->comparability == DISTINCT){
                $dnum=count($attrs[$i]->type_elements);
                //echo $dnum;
                echo "<select class=\"resetSelectInp\" name=\"attr[]\">";
                for($j=0;$j<$dnum;$j++){
                    echo "<option value=\"".sanitize_str_disp($attrs[$i]->type_elements[$j]["Value"])."\"";
                    if($attrs[$i]->type_elements[$j]["Value"] == $values->entity_attribute_values[$attrs[$i]->id])
                            echo "selected";
                    echo ">".sanitize_str_disp($attrs[$i]->type_elements[$j]["Value"])."</option>";
                }
                echo "</select>";
            }
            else{
                
                if($attrs[$i]->comparability == COUNTABLE){
                    echo "<input class=\"resetInputAble\" name=\"attr[]\" type=\"text\" value=\"".sanitize_str_disp($values->entity_attribute_values[$attrs[$i]->id])."\" onkeyup=\"entCheckCountable(this.value,".floatval($attrs[$i]->type_elements["Lower Limit"]).",".floatval($attrs[$i]->type_elements["Upper Limit"]).",".$i.");\">";
                        echo "<span class=\"dialogOptional\">(numerical";
                        echo " from ".floatval($attrs[$i]->type_elements["Lower Limit"]);
                        echo " to ".floatval($attrs[$i]->type_elements["Upper Limit"]);
                        echo ")</span><span class=\"entError\"></span>";
                }
                else{
                    echo "<input class=\"resetInputAble\" name=\"attr[]\" type=\"text\" value=\"".sanitize_str_disp($values->entity_attribute_values[$attrs[$i]->id])."\" onkeyup=\"entCheckUncomparable(this.value,".$i.");\">";
                    echo "<span class=\"dialogOptional\">(Uncomparable Attribute)</span><span class=\"entError\"></span>";   
                }
            }
            echo"</td>";
            echo "</tr>";
        }
        echo "<tr><th></br></th><td></br></td></tr>";
        echo "<tr><th style=\"height: 68px;\" colspan=\"2\" id=\"addEnInBtns\">
                 <span class=\"MgEnInSubmitButton\">Submit</span>
                    <span class=\"MgEnInResetButton\">Reset</span>
                    <span class=\"MgEnInCloseButton\">Close</span>
                </th></tr>";
        echo "</tbody>";
        echo"</table>";
        echo "</form>";
    }
    
    function returnError($code){
        if($code == 0)
            return "No category selected";
        else if($code == 1)
            return "Not enough privileges";
        else if($code == 2)
            return "Name not set";
        else if($code == 3)
            return "Internal Error sorry";
        else if($code == 4)
            return "Attribute is out of bounds";
        else if($code == 6)
            return "Internal Error sorry";
        else if($code == 7)
            return "Entity added successfully!";
        else if($code == 8)
            return "No entity selected";
        else if($code == 9)
            return "Internal Error sorry";
        else if($code == 10)
            return "Entity deleted successfully!";
        else if($code == 11)
            return "Name out of bounds";
        else if($code == 12)
            return "Entity editted successfully!";
        else if($code == 13)
            return "Internal Error, entity not deleted";
        else if($code == 14)
            return "Internal Error, entity not added";
        else if($code == 15)
            return "Internal Error, entity not editted";
        else if($code == 16)
            return "This entity name already exists";
	else if($code == 17)
		return "Countable Attributes must contain numbers only";
    
    }


?>
