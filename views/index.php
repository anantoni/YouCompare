<?php
	 /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/

        include_once( "db_includes/category.php" );
        include_once( "db_includes/get_categories.php" );
        include_once( "db_includes/DB_DEFINES.php" );

	class Index {
            
		private $content;
		
		private function generate_content() {
                    
                        $get_categories = new get_categories;
                        $most_popular_array = $get_categories->get_most_popular(12);
                        $name_array = array();
                        $id_array = array();
                        $image_array = array();
                        $image_source = "";
                        
                        $i = 0;
                        foreach( $most_popular_array as $key=>$element )                             {
                            $name_array[$i] = $key;
                            $id_array[$i] = $element["id"];
                            $i++;
                            
                        }
                        $image_array = $get_categories->fetch( $id_array );

                        $this->content = "<div id=\"mainContainer\" align=\"center\">
                                                            <h2>Top 12 Most Popular Categories</h2>
                                                            <hr>
                                                            <ul id=\"categoryMemberList\">";
                                          
                                                            for ( $i = 0 ; $i < count( $id_array ) ; $i++ )     {                     
										for ( $j = 0 ; $j < count( $image_array ) ; $j++ ) {                                          
                                                                    		
									     		if ( $image_array[$j]["id"] == $id_array[$i] ) {
												$this->content .= "<li onclick=\"window.location='browseCategory.php?cat_id=".$id_array[$i]."';\"><div class=\"imageSection\" align=\"center\">";
                                                                    			if ( $image_array[$j]["image"] != NULL  )
                                                                        			$image_source = $image_array[$j]["image"];
                                                                    			else 
                                                                        			$image_source = "./cat_images/__not__.jpg";
                                                                        
                                                                    			$this->content .= "<img class=\"cat_image\" src=\"".$image_source."\"></div><div class=\"nameSection\" aling=\"center\"><span class=\"name\">";
                                                                    			$this->content .= $name_array[$i] ."</span></div></li>";
											}
											
										}
                                                                
                                                                }
                            
                        
                    
                        $this->content .= "</ul></div>";
                    			                        
		}
                
		public function Index(){
                    
			$this->content = "";
			$this->generate_content();
                        
		}
                
		public function get_content(){
                    
			return $this->content;
                        
		}
	}
?>
