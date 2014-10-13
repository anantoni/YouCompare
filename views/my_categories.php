<?php
        include_once( "db_includes/DB_DEFINES.php");
        include_once( "db_includes/user.php");
        include_once( "db_includes/category.php");

        /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/

        class my_categoriesPage {
            
                private $content;

                public function create_content() {
                    
                        $categories_array = array();
                        $images_array = array();
                        $image_source = "";
                        $category = NULL;
                        $countable_counter = 0;
                        $distinct_counter = 0;
                        $uncomparable_counter = 0;
                    
                        session_start();
                        if ( !isset( $_SESSION['username'] ) ) 
                                header("Location: ./index.php");
                        
                        else {
                                $user = new user( $_SESSION['username'], LOGGED_IN );
                                if ( $user->get_errno() == DB_OK ) {
                                    
                                    $categories_array = $user->getYourCategories();
                                    
                                    if ( $user->get_errno() == DB_OK ) {
                                        
                                          for ( $i = 0 ; $i < count( $categories_array ) ; $i++ ) {
                                                $category = new category(  $categories_array[$i][0] );
                                                $images_array[] = $category->get_image();
                                                
                                          }
                                        
                                          /********************************************* CATEGORY MEMBER **************************************************************/
                                          $this->content = "<div id=\"mainContainer\" align=\"center\">
                                                            <h2 style=\"font-size: 30px; font-weight: bold;\"> My Categories </h2>
                                                            <hr>
                                                            <p id=\"category_member_toggle\" class=\"table_toggle\"> Category Member rights: <span id=\"category_member_visible\">[Hide]</span> </p>
                                                            <ul id=\"categoryMemberList\">";
                                          
                                                            for ( $i = 0 ; $i < count( $categories_array ) ; $i++ )
                                                                if ( $categories_array[$i][2] == 0 ) {
                                                                    
                                                                    $this->content .= "<li onclick=\"window.location='browseCategory.php?cat_id=".$categories_array[$i][0]."';\"><div class=\"imageSection\" align=\"center\">";
                                                                    if ( $images_array[$i] != NULL  )
                                                                        $image_source = $images_array[$i];
                                                                    else 
                                                                        $image_source = "cat_images/__not__.jpg";
                                                                        
                                                                    $this->content .= "<img class=\"cat_image\" src=\"".$image_source."\"></div><div class=\"nameSection\" aling=\"center\"><span class=\"name\">";
                                                                    $this->content .=$categories_array[$i][1] ."</span></div></li>";
                                                                
                                                                }
                                                            for ( $i = 0 ; $i < 10 ; $i++ )
                                                                    $this->content .= "<li class=\"ghost_cat\"></li>";
                                                                   
                                                        
                                           /********************************************* EDITOR MEMBER **************************************************************/
                                           $this->content .= "</ul></table>";
                                                        for ( $i = 0 ; $i < count( $categories_array ) ; $i++ )
                                                                    if ( $categories_array[$i][2] == 1 ) {
                                                                        $countable_counter++;
                                                                        if ( $countable_counter == 1 )
                                                                            $this->content .= "<p id=\"editor_member_toggle\" class=\"table_toggle\"> Editor Member rights: <span id=\"editor_member_visible\">[Hide]</span> </p>
                                                                                <table id=\"editorMemberTable\" class=\"Table\">
                                                                                <tr>
                                                                                <th scope=\"col\" style=\"width: 350px;\">Name</th>
                                                                                <th scope=\"col\">Manage Entities</th>
                                                                                </tr>";  
                                                                            $this->content .= "<tr id=\"category".$categories_array[$i][0] ."\" class=\"alt\"><th scope=\"row\"><a href=\"./browseCategory.php?cat_id=".$categories_array[$i][0]."\">".$categories_array[$i][1] ."</a></th><td> <span id=\"".$categories_array[$i][0]."\" class=\"manage_entities\">Manage Entities</span></tr>";

                                                                    }
                                                                        
                                                                

                                                                
                                            
                                            /********************************************* SUB MODERATOR **************************************************************/                
                                            $this->content .= " </table>";
                                            
                                                        for ( $i = 0 ; $i < count( $categories_array ) ; $i++ )
                                                            if ( $categories_array[$i][2] == 2 ) {
                                                                $distinct_counter++;
                                                                if ( $distinct_counter == 1 )
                                                                    $this->content .= "<p id=\"sub_moderator_toggle\" class=\"table_toggle\"> Sub Moderator rights: <span id=\"sub_moderator_visible\">[Hide]</span> </p>
                                                                            <table id=\"subModeratorTable\" class=\"Table\">
                                                                            <tr>
                                                                            <th scope=\"col\" style=\"width: 350px;\">Name</th>
                                                                            <th scope=\"col\">Manage Category</th>
                                                                            <th scope=\"col\">Manage Entities</th>
                                                                            </tr>";  
                                                                    
                                                                $this->content .= "<tr id=\"category".$categories_array[$i][0] ."\" class=\"alt\"> <th scope=\"row\"><a href=\"./browseCategory.php?cat_id=".$categories_array[$i][0]."\">".$categories_array[$i][1] ."</a></th> <td> <span id=\"".$categories_array[$i][0]."\" class=\"manage_category\">Manage Category</span> </td> <td> <span id=\"".$categories_array[$i][0]."\" class=\"manage_entities\">Manage Entities</span> </td> </tr>";
                                            
                                                
                                                                
                                                            }

                                            /************************************************* MODERATOR **************************************************************/                
                                            $this->content .= "</table>";
                                                         for ( $i = 0 ; $i < count( $categories_array ) ; $i++ )
                                                                    if ( $categories_array[$i][2] == 3 ) {
                                                                        $uncomparable_counter++;
                                                                        if ( $uncomparable_counter == 1 )
                                                                           $this->content .= "<p id=\"moderator_toggle\"class=\"table_toggle\"> Moderator rights: <span id=\"moderator_visible\">[Hide]</span> </p>
                                                                            <table id=\"moderatorTable\" class=\"Table\">
                                                                            <tr>
                                                                            <th scope=\"col\" style=\"width: 350px;\">Name</th>
                                                                            <th scope=\"col\">Manage Category</th>
                                                                            <th scope=\"col\">Manage Entities</th>
                                                                            <th scope=\"col\">Manage Users</th>
                                                                            <th scope=\"col\">Delete Category</th>
                                                                            </tr>";  
                                                                        $this->content .= "<tr id=\"category".$categories_array[$i][0] ."\"  class=\"alt\"> <th scope=\"row\"><a href=\"./browseCategory.php?cat_id=".$categories_array[$i][0]."\">".$categories_array[$i][1] ."</a></th> <td> <span id=\"".$categories_array[$i][0]."\" class=\"manage_category\">Manage Category</span> </td> <td> <span id=\"".$categories_array[$i][0]."\" class=\"manage_entities\">Manage Entities</span> </td> <td> <span id=\"".$categories_array[$i][0]."\" class=\"manage_users\">Manage Users</span> </td> <td> <span id=\"".$categories_array[$i][0]."\" class=\"delete_category\"><img class=\"remove_image\" src=\"images/close.png\">Delete Category</span> </td> </tr>";

                                                                    }
                                                               

                                            $this->content .= "</table></div><br><div id=\"deleteCategoryResult\"> </div>";
                                    }
                                    
                                }
                        }
                        
                }

                public function get_content() {
                        return $this->content;
                }
        }
?>
