<?php
	
/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: backend part of page generation                            **|
|                                                                           **|   
\*****************************************************************************/
	



        /**************************************** CLASS PAGE ********************************************************************************************************/
	class Page {
                    /*private variables*/
                    private $type;

                    /*dependencies*/
                    private $dependenciesCss; 
                    private $dependenciesJs;

                    /*markup*/
                    private $html;
                    private $head;
                    private $body;
					
					/*content*/
					private $content;
					
                    /*errors*/
                    private $error; //set to last error


                    /**************************************** GENERATE PRIMARY MARKUP FUNCTION ***************************************************************************************/
                    private function generatePrimaryMarkup() {

                            /*opening html tags here*/
                            $this->html = "<!DOCTYPE HTML><html>";

                            /*generating and adding head to the markup*/
                            $this->generateHead();
                            $this->html .= $this->head;

                            /*generating and adding body to the markup*/			
                            $this->generateBody();
                            $this->html .= $this->body;

                            /*closing html tags here*/
                            $this->html .= '</html>';

                    }
			/********** GENERATE ESSENTIAL JS *********/
			private function generate_essential_js() {
				/*** this function passes essential info to js vars ***/
					//$script = "";
					
					$script .= '<script type="text/javascript">';
				
					
					if( isset($_SESSION['username']) ) {
						$script .= "var global_username="."\"".$_SESSION['username']."\";";
					}
					else {
						$script .= "var global_username="."\"".""."\";";
					}
					$script .= "</script>";
					
					return $script;
			}

                    /********************************************* GENERATE HEAD FUNCTION ******************************************************************************************************************************/                
                    private function generateHead() {

                            $this->head .= file_get_contents('views/head.html');
				
				$this->head .= $this->generate_essential_js();

                            if($this->type != "empty" || "search" ){
                                    $this->import_css("$this->type"."Style");
                                    $this->import_js($this->type);
                            }	

                            /*fixing the page title*/
                            $this->head .= "<title>YouCompare! - $this->type</title>";
                            $this->head .= "</head>";
                    }                



                    /********************************************* GENERATE BODY FUNCTION ******************************************************************************************************************************/                
                    private function generateBody() {

                            /*opening body tags here*/
                            $this->body = '<body>';

                            $this->body .= $this->generateHeader();
                            $this->body .= $this->generateNavigation();
                            $this->body .= $this->generateMainContainer();
                            $this->body .= $this->generateFooter();

                            /*opening body tags here*/
                            $this->body .= '</body>';
                    }



                    /********************************************* GENERATE HEADER FUNCTION *******************************************************************************/               
                    private function generateHeader() {

                            $header = file_get_contents('views/header.html');
                            if ( isset( $_SESSION["username"] ) ) {
                                    $header .= "<div id=\"userPanel\"><div class=\"userPanel-staticInfo\"><span style=\"display: inline-block; height: 26px; vertical-align: middle;\"><img class=\"userImg\" src=\"images/gear.png\" alt=\"gear\"></span><span class=\"userInfo\">".$_SESSION['username']."</span></div><div class=\"content\"></div></div>";
                            }
                            else {
                                    $header .= "<div id=\"userPanel\"><div class=\"userPanel-staticInfo\"><span style=\"display: inline-block; height: 26px; vertical-align: middle;\"><img class=\"userImg\" src=\"images/user.png\" alt=\"gear\"></span><span class=\"userInfo\">Login or Create an account</span></div><div class=\"content\"></div></div>";
                            }

                            /*closing header div*/
                            $header .= '</div>';
                            return $header;
                    }



                    /*********************************************** GENERATE NAVIGATION ***********************************************************************************/               		
                    private function generateNavigation() {

                            $navigation = file_get_contents('views/navigation.html');
                            return $navigation;
                    }             



                    /************************************* GENERATE MAIN CONTAINER FUNCTION - START ************************************************************************/              
                    private function generateMainContainer() {

						/************* INDEX CONTENT ***************/	
						if($this->type == 'index'){
							require_once( "views/index.php" );
							$container = new Index();
							return "<div id=\"mainContainer\">".$container->get_content()."</div>";

						}	

                            /************* REGISTER CONTENT ***************/
                           else if ( $this->type == 'register' ) {

                                require_once ( "views/register.php" );
                                $container = new registerPage;
                                $container->create_content();

                                return $container->get_content();

                            }

                            /*************** LOGIN CONTENT *******************/
                            else if ( $this->type == 'login' ) {

                                require_once ( "views/login.php" );
                                $container = new loginPage;
                                $container->create_content();

                                return $container->get_content();

                            }

                            /************** SEARCH CONTENT *******************/
                           else if ( $this->type == 'search' ) {

                                require_once ( "views/search.php" );
                                $container = new searchPage;
                                $container->create_content();

                                return $container->get_content();

                           }
				/**************** ADD/EDIT/DELETE ATTRIBUTE CONTENT ****************/
                            else if ( $this->type == 'manage_category' ) {

                                require_once ( "views/manage_category.php" );
                                $container = new manage_categoryPage;
                                $container->create_content();

                                return $container->get_content();

                            }

				/**************** MY CATEGORIES CONTENT ****************/
                            else if ( $this->type == 'my_categories' ) {

                                require_once ( "views/my_categories.php" );
                                $container = new my_categoriesPage;
                                $container->create_content();

                                return $container->get_content();

                            }

                            /**************** CREATE CATEGORY ****************/
                           else if ( $this->type == 'create_category' ) {

                                require_once ( "views/create_category.php" );
                                $container = new create_categoryPage;
                                $container->create_content();

                                return $container->get_content();

                            }
                            /***********BROWSE CATEGORY***********/
                          else if($this->type=='browseCategory'){
			
						require_once ( "views/browseCategory.php" );
						$container = new browseCategoryPage;
						$container->create_content();
							
						return $container->get_content();
							
						   }
						
						/**********MANAGE ACCOUNT **********/
						else if($this->type=='manageAccount'){
							require_once ("views/manage_account.php");
							$container=new manageAccount;
							$container->create_Content();
							return $container->get_content();
							}
							
				/************* VERIFY CONTENT *******************/
                            else if ( $this->type == 'verify' ) {

                                require_once ( "views/verify.php" );
                                $container = new verifyPage;
                                $container->create_content();

                                return $container->get_content();

                            }
				/************* BROWSE ENTITY CONTENT *******************/
				else if ( $this->type == 'browseEntity' ) {

                                	require_once ( "views/browseEntity.php" );
                                	$container = new BrowseEntity;
                                	return "<div id=\"mainContainer\">".$container->get_content()."</div>";

                        	}		
	
				/************* ADD ENTITY CONTENT *******************/
				else if ( $this->type == 'addEntity' ) {

                                	require_once ( "views/addEntity.php" );
                                	$container = new addEntity;
                                	return "<div id=\"mainContainer\">".$container->get_content()."</div>";

                        	}
				/************* EDIT ENTITY CONTENT *******************/
				else if ( $this->type == 'editEntity' ) {
									require_once ( "views/editEntity.php" );
									$container = new editEntity;
									return "<div id=\"mainContainer\">".$container->get_content()."</div>";
							}
				/************* Manage Users Content*******************/
				else if ( $this->type == 'manageUsers' ) {
					require_once ( "views/manageUsers.php" );
					$container = new manageUsers;
					$container->create_content();
					return $container->get_content();
				}
				/************* Manage Entities Content*******************/
				else if ( $this->type == 'manageEntities' ) {
					require_once ( "views/manageEntities.php" );
					$container = new manageEntities;
					$container->create_content();
					return $container->get_content();
				}	
				/************* Become Member Content*******************/
				else if ( $this->type == 'becomeMember' ) {
					require_once ( "views/becomeMember.php" );
					$container = new becomeMember;
					$container->create_content();
					return $container->get_content();
				}		
				/**** COMPARE CONTENT ****/			
				else if($this->type == 'compare') {
						require_once( "views/compare.php" );
						$container = new Compare();
						return "<div id=\"mainContainer\">".$container->get_content()."</div>";
					
				}
				/*** COMPARISON RESULTS ***/
				else if($this->type == 'comparison_results') {
						require_once( "views/comparison_results.php" );
						$container = new ComparisonResults();
						return "<div id=\"mainContainer\">".$container->get_content()."</div>";
				}
								
				/**** MANAGE ACCOUNT CONTENT ****/			
				else if($this->type == 'manage_account') {
						require_once( "views/manage_account.php" );
						$container = new manageAccountPage();
						$container->create_content();

						return $container->get_content();					
				}
				/**** FAQ MODULE****/
				else if($this->type == 'faq')
				{
						require_once("views/faq.php");
						$container = new Faq();
						return "<div id=\"mainContainer\">".$container->get_content()."</div>";
				}
				/**** SITEMAP MODULE ****/
				else if($this->type == 'sitemap')
				{
						require_once("views/sitemap.php");
						$container = new Sitemap();
						return "<div id=\"mainContainer\">".$container->get_content()."</div>";
				}
				/**** DISCLAIMER MODULE ****/
				else if($this->type == 'disclaimer')
				{
						require_once("views/disclaimer.php");
						$container = new Disclaimer();
						return "<div id=\"mainContainer\">".$container->get_content()."</div>";				
				}
				/**** ABOUT MODULE ****/
				else if($this->type == 'about')
				{
						require_once("views/about.php");
						$container = new About();
						return "<div id=\"mainContainer\">".$container->get_content()."</div>";
				}
				/**** PRIVACY MODULE ****/
				else if($this->type == 'privacy')
				{
						require_once("views/privacy_policy.php");
						$container = new Privacy();
						return "<div id=\"mainContainer\">".$container->get_content()."</div>";				
				}
                        /************* BUG REPORT CONTENT *******************/
					else if($this->type == 'bugReport'){
						require_once( "views/bugReport.php" );
						$container = new BugReport();
						return "<div id=\"mainContainer\">".$container->get_content()."</div>";
					}
                
					

            }



                    /******************************** GENERATE MAIN CONTAINER FUNCTION - END **************************************************************************/           









                    /************************************** GENERATE FOOTER FUNCTION *********************************************************************************/
                    private function generateFooter() {
                            $footer = file_get_contents('views/footer.html');

                            return $footer;
                    }




                    /*********************************************** IMPORT CSS FILES ********************************************************************/
                    private function import_css( $cssToImport ) {

                            $this->head .= "<link rel='stylesheet' type='text/css' href='css/".$cssToImport.".css' />";
                    }




                    /*************************************** IMPORT JAVASCRIPT FILES **********************************************************************/
                    private function import_js( $jsToImport ) { 

                            $this->head .= "<script src='js/".$jsToImport.".js'></script>";
			 
                    }

		    /***************** HISTORY MANAGEMENT FUNCTION *************/
		    private function history($task){
			if($task == "append"){
			    $currentUrl = $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
  			    if (!isset($_SESSION['history'])) {
    		            	$_SESSION['history'] = array();
		            }
		            array_push($_SESSION['history'],$currentUrl);
			}
		    }
                    /*********************************** CONSTRUCTOR ************************************************************************************/
                    public function Page( $pageType ) {

                            /************ INITIALIZING VARIABLES **********************/
                            $this->dependenciesCss= "";
                            $this->dependenciesJs = "";
                            $this->html = "";
                            $this->head = "";
                            $this->body = "";
                            $this->error = "OK";
                            $this->type = $pageType;
							$this->content="";
                            /********* STARTING SESSION **************/
                            session_start();
							$this->history("append");
                            $this->generatePrimaryMarkup();

                    }





                    /************************************** DISPLAY FUNCTION ******************************************************************************************************************************************************/               
                    public function display($mode) {

                            /*we could integrate modes in this function*/
                            /*such as debug mode etc.*/
			    if($mode == "release"){		
 	                           print $this->html;
			    }
			    else if($mode == "debug"){
			           print "***Debug mode***</br>";
				   var_dump($this->type);
                    		   var_dump($this->dependenciesCss);
				   var_dump($this->dependenciesJs);
                                   var_dump($this->html);
				   var_dump($this->head);
				   var_dump($this->body);
				   var_dump($this->error);	
                    	    }
                    }	
			
	
	public function set_content($cont)
	{$this->content=$cont;}
	}
?>
