<?php
        /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/

        class loginPage {
            
                private $content;

                public function create_content() {
                    
                        $this->content = "<div id=\"mainContainer\" align=\"center\">
                                        <div id=\"login_panel\" align=\"center\">

                                            <h2 style=\"font-size: 30px; font-weight: bold;\">Log in</h2> 
                                            <hr>
                                            <br>
                                            <p>Don't have an account? <a href=\"register.php\">Create one</a>.</p>
                                            <br><br>

                                            <ul><li><label for='username'>Username: </label></li><li><input class=\"loginUsername\" name=\"username\"/></li></ul> 
                                            <ul><li><label for='password'>Password: </label></li><li><input id=\"password_input\" class=\"loginPassword\" type=\"password\" name=\"password\"/></li></ul>                 

                                            <br>

                                            <button type=\"submit\" id=\"loginButton\"><span>Log in<span></button>
                                            <br>
                                            <br>

                                            <div id=\"loginResult\"> <br> </div>
                                            </div>
                                            
                                        </div>";
                }

                public function get_content() {
                        return $this->content;
                }
        }
?>
