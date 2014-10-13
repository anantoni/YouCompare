<?php
            /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/

            include_once "db_includes/DB_DEFINES.php";
            include_once "db_includes/user.php";
            include_once "logic_includes/logic_functions.php";
            include_once "logic_includes/LOGIC_DEFINES.php";
            
            class verifyPage {         
                
                            private $content;

                            public function create_content() {
                                
                                    $md5username = "";
                                    $verify_error_code = 0;
                                    $this->content = "<div id=\"mainContainer\" align=\"center\"><br><br><br><br><br><br><p>";
                                    
                                
                                    if ( !isset( $_GET['user'] ) ) 
                                            $this->content.="An error has occured (Bad Request)";
                                    
                                    else {
                                            
                                            $md5username = sanitize_str( $_GET['user'] );                                  
                                            $user = new user( "", -1 );  
                                            
                                            if ( $user->get_errno() != DB_OK )
                                                    $this->content.="An error has occured";
                                            
                                            $user->verify_user( $md5username );
                                            $verify_error_code = $user->get_errno();

                                            if ( $verify_error_code == DB_OK ) 
                                                    $this->content.="Your YouCompare account has been activated successfully!";

                                            if ( $verify_error_code == USERNAME_DONT_EXIST ) 
                                                    $this->content.= "No YouCompare account found for this user";

                                            if ( $verify_error_code == USER_ALLREADY_VERIFIED ) 
                                                    $this->content.= "Your YouCompare account has already been activated!";

                                            if ( $verify_error_code == MYSQL_ERROR ) 
                                                    $this->content.= "An error has occured";
                                    }    
                                    $this->content.= "</p></div>
                                        <script type=\"text/javascript\">
                                            function delayedRedirect(){
                                                window.location = \"./login.php\";
                                            }
                                            setTimeout( 'delayedRedirect()', 3000 );

                                        </script>";
                            }

                            public function get_content() {
                                        return $this->content;
                                        
                            }   
                            
                }
?>