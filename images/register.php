<!DOCTYPE HTML>
<?php
	session_start();
?>
<html>
        <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

                <link rel="shortcut icon" href="images/favicon.png">

                <!-- including all .js script here -->
                <!-- the following includes google's .js api -->
                <!-- note that the "key" is valid only for the current domain -->
                <script type="text/javascript" src="https://www.google.com/jsapi?key=ABQIAAAAXd--lQV4PTZa15vSS1WJwhSk7ylEV62EKrGciXTS5v_3b7qr3BRL5VFoZFzv6fGKFnp_c-LkXsI_6g"></script>
                <script type="text/javascript" src="js/user_input_module.js"></script>
                        
                <!-- the following script loads up some extra fonts -->
                <script type="text/javascript">
                        WebFont.load({
                            google: {
                                families: [ 'Ubuntu', 'Cantarell', 'Just Another Hand' ,'Lobster' ,'Lekton' ,'Cabin']
                                }
                        });
                </script>
                <script>
                        var category_id;
                        var entities_to_compare = "";
                        var errorArray = [];
                </script>
                <script type="text/javascript" src="js/navigation.js"></script>
                <script type="text/javascript" src="js/xml_parser.js"></script>
                <script src="js/userPanel.js"></script>
                <script src="js/search.js"></script>
                <script src="js/login.js"></script>
                <script src="js/register.js"></script>

                <!-- importing all .css files here -->
                <link rel="stylesheet" type="text/css" href="css/resetStyle.css" />
                <link rel="stylesheet" type="text/css" href="css/genericStyleB.css" />
                <link rel="stylesheet" type="text/css" href="css/searchStyle.css" />
                
                <title>YouCompare! - Register</title>
                
                <style type="text/css">
                        hr { 
                            color: black;
                            width: 50%;
                        }
                        
                        #userInfoTable td {
                            margin-right: 30px;
                            width: 200px;                        
                        }
                        
                        #userInfoTable td img {
                            position: relative;
                            bottom: -6px;
                            left: 6px;
                            height: 25px;
                            width: 25px;
                            margin-right: 5px;
                        }  
                        
                        #captchaTable td {
                            width: 600px;
                            text-align: center;
                        }
                        
                        #userInfoTable td input {
                            width: 100%;
                        }
                        
                        #mainContainer button:enabled {
                            border-radius: 20px;
                            background-color: #00cc33;
                            color: white;
                            border-width:0;
                            background-image: -webkit-gradient( linear, left bottom, left top, color-stop(0.75, rgb(83,201,24)), color-stop(0.29, rgb(34,122,72)) );
                            padding-left: 10px;
                            padding-right: 10px;
                            font-weight: bold;
                        }

                        #mainContainer .registerButtonEnabled:hover {
                            border-radius: 20px;
                            background-color: #00cc33;
                            color: black;
                            border-width:0;
                            background-image: -webkit-gradient( linear, left bottom,left top, color-stop(0.75, rgb(116,194,0)), color-stop(0.2, rgb(151,255,15)));
                            padding-left: 10px;
                            padding-right: 10px;
                            font-weight: bold;
                        }

                        button:disabled {
                            border-radius: 20px;
                            border-width: 0;
                            padding-left: 10px;
                            padding-right: 10px;
                            font-weight: bold;
                            border: 2px ButtonFace;
                            color: GrayText;
                            cursor: no-drop;
                        }
                        
                        input:focus {  
                            border:3px solid #EF4F7A;  
                        }    
                        
                        #mainContainer :link {
                            color: blue;
                            font-weight: bold;
                        }
                        
                        #mainContainer :visited {
                            color: blue;
                        }
                        
                </style>
                
        </head>

        <body>
                <!-- header elements -->
                <div id="header">
                        <a href="./index.php" title="Visit the main page [alt-m])" accesskey="m"><img src="images/scalesLogo3.png"></a>
                        <h1 title="Click to enter search query [alt-s])" accesskey="s" contenteditable=true><b>You</b>Compare!</h1>
                        <?php
                                if( isset($_SESSION["username"]) ){
                                        echo "<div id='userPanel'><img src='images/userPanel_dropDown.png'><span class='username'>".$_SESSION['username']."</span></hr><ul></ul></div>";		
                                }
                                else{
                                        echo '<div id="userPanelGuest"><img src="images/user.png"><span>Login or Create an account</span><ul class="loginContent"></ul><ul class="loginErrors"></ul></div>';

                                }
                        ?>
                </div>
                
                <!-- navigation -->
                <div id="navigation">
                        <ul>
                        <li><img src="images/homeInactive.png"><a href="./index.php" title="Visit the main page [alt-m])" accesskey="m">Main Page</a></li>
                        <li><img src="images/browseCategoryInactive.png"><a href="./browse_categories.php" title="Browse categories [alt-b]" accesskey="b">Browse Categories</a></li>
                        <li><img src="images/advancedSearch.png"><a href="./advanced_search.php" title="Advanced search">Advanced search</a></li>
                        <li><img src="images/helpInactive.png"><a href="./help.php" title="Get help">Help</a></li>
                        <li></li>
                        <li></br></li>	
                        <li></br></li>
                        <li></br></li>
                    </ul>
                </div>
                

                <!-- mainContainer elements -->
                <div id="mainContainer" align="center" >
                       <div id="register" >
                            
                                <h2 style="font-size: 30px; font-weight: bold;"> Create YouCompare account </h2>
                                <hr>
                                <br>
                                <div style="display:block ; width: 600px; text-align: left;">
                                    <p>Already have an account? <a href="login.php"> Log in </a>.</p> 
                                    <p>Please fill all the following mandatory fields with your information</p>     
                                </div>
                                <br>
                                
                                <table id="userInfoTable"> 
                                        <tr> 
                                                <td> <label> Username: </label> </td>                            
                                                <td> <input class="registerUsername" type="text" placeholder="e.g MyUsername56" value="" /> </td>
                                                <td class="registerUsernameError"> </td>
                                        </tr>
                                        <tr> 
                                                <td> <label> First name (optional): </label> </td> 
                                                <td> <input class="registerFname" type="text" placeholder="e.g John" value="" /> 
                                                </td> <td class="registerFnameError"></td>	
                                        </tr>
                                        <tr> 
                                                <td> <label> Last name (optional): </label> </td> 
                                                <td> <input class="registerLname" type="text" placeholder="e.g Rambo" value="" />  </td>
                                                <td class="registerLnameError"> </td>
                                        </tr> 
                                        <tr> 
                                                <td> <label> E-mail: </label> </td> 
                                                <td> <input class="registerEmail" type="mail" placeholder="e.g mymail@gmail.com" value="" /> </td>
                                                <td class="registerEmailError"> </td>
                                        </tr>
                                        <tr> 
                                                <td> <label> Retype e-mail: </label> </td> 
                                                <td> <input class="registerEmailConfirm" type="mail" value="" /> </td>
                                                <td class="registerEmailConfirmError"> </td>
                                        </tr>
                                        <tr> 
                                                <td> <label> Password: </label> </td> 
                                                <td> <input class="registerPassword"  type="password" placeholder="e.g 89My17Password13"value="" /> </td> 
                                                <td class="registerPasswordError"> </td>
                                        </tr>
                                        <tr> 
                                                <td> <label> Retype password: </label> </td> 
                                                <td> <input class="registerPasswordConfirm" type="password" value="" /> 
                                                </td> <td class="registerPasswordConfirmError"> </td>
                                        </tr>
                            </table>
                                
                            <br><br>    
                                        <div style="display:block ; width: 600px; text-align: left;">Please enter the following words, without any spaces:</div>
                                        <br>
                                        <?php require_once('recaptchalib.php');
                                                  $publickey = "6LfwncQSAAAAAOQDKkfsa-JJdY5lq8tR0avhN5Ki"; // you got this from the signup page
                                                  $privatekey = "6LfwncQSAAAAAJbHzTZSoNXbXiU683U6qVUS6OKZ";
                                                  echo recaptcha_get_html($publickey);                     
                                        ?> 
                                        <p class="recaptchaError"> <p>
                                        <br>
                                        <button id="registerButton" class="registerButtonDisabled"> <span> Register </span> </button>
                                        <br>
                                        <div id="registrationResult"> </div>
                                        <br><br><br>
                        </div>
                </div>
                
                
                
                
<!-----------------------------------------------------------------FOOTER ELEMENTS-------------------------------------------------------------------->
                <div id="footer">
                        <table>
                                <tr>
                                        <td><a href="./privacy_policy.php" title="Privacy policy of the page">Privacy policy</a></td>
                                        <td><a href="./about.php" title="about this page">About</a></td>
                                        <td><a href="./community.php" title="Meet the community of YouCompare!">Community</a></td>
                                </tr>
                                <tr>
                                        <td><a href="./disclaimer.php" title="Disclaimer page">Disclaimer</a></td>
                                        <td><a href="./faq.php" title="frequently asked questions concerning YouCompare!">F.A.Q</a></td>
                                        <td><a href="./news.php" title="new and updates of YouCompare!">News</a></td>

                                </tr>
                                <tr>
                                        <td></td>
                                        <td><a href="./introduction.php" title="Introduction to the main features of YouCompare!">Introduction</a></td>
                                        <td><a href="./contact.php" title="Contact YouCompare!">Contact</a></td>
                                </tr>
                                <tr>
                                        <td></td>
                                        <td><a href="./help.php" title="Get help">Help</a></td>

                                </tr>
                        </table>
                </div>
        </body>
</html>
