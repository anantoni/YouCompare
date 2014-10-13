<?php
	
/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: Gui Part for page browse entity                            **|
|                                                                           **|   
\*****************************************************************************/

	
	require_once './db_includes/DB_DEFINES.php';
	require_once './db_includes/category.php';
	require_once './db_includes/entity.php';
	require_once './db_includes/user.php';
	require_once './db_includes/attribute.php';

	class BrowseEntity{
		private $content;
		private $cat_id;
		private $ent_id;
		private $error;		
		
		private $category;
		private $entity;
		private $modPanelAccess;
		private $viewAccess;
		
		private $username;
		private $privileges;		
	
		private function generate_content($mode){
			$this->content .= "<div id=\"browseEntity\">";
			if($mode == "invalid ids"){
				$this->content .= "<span class=\"invalidIds\">Invalid category/entity id</span>";
			}
			else{
				$this->generate_entity_message();
				$this->generate_entity_info();
				$this->generate_media_gallery();
				$this->generate_entity_attributes();
				$this->generate_buttons();
				$this->generate_dialog();
			}	
			$this->content .= "</div>";	
		}


		private function generate_entity_message() {

			$entityMessage .= "<div id=\"entityMessage\">";
			$entityMessage .= "<h1 class=\"tabHeader\" name=\"entityMessageInner\">Category info</h1>";
			$entityMessage .= "<div id=\"entityMessageInner\">";
			$entityMessage .= "<span class=\"categoryIndicator\">Currently in category:</span><span class=\"categoryName\">".$this->category->get_name()."</span><a href=\"browseCategory.php?cat_id=".$this->cat_id."\"><button class=\"backToCategory\" title=\"browse category [alt-s]\" accesskey=\"s\"><span>Browse category &nbsp;".$this->category->get_name()."</span></button></a>";	
			$entityMessage .= "</div>";
			$entityMessage .= "</div>";
			$this->content .= $entityMessage;
		}


		private function generate_entity_info(){
			$name = $this->entity->entity_name;
			$description = $this->entity->entity_description;
			$image = $this->entity->entity_image;
			$rate  = $this->entity->rate;			
			
			$entityInfo .= "<div id=\"entityInfo\">";
			$entityInfo .= "<h1 class=\"tabHeader\" name=\"entityInfoInner\">Entity Info</h1>";
			$entityInfo .= "<div id=\"entityInfoInner\">";
			$entityInfo .= "<div id=\"basicInfoWrapper\">";
			if(!empty($image)){
				$entityInfo .= "<img class=\"image\" src=\"$image\" alt=\"could not load image\">";
				$entityInfo .= "<div class=\"imageBox\"></div>";
			}
			else{
				$entityInfo .=	"<img class=\"image\" src=\"cat_images/__not__.jpg\">";
			}
			
			$entityInfo .= "<div id=\"basicInfoRight\">";
			$entityInfo .=		"<h2 class=\"name\">$name</h2>";
			$stars = floatval(round($rate*12.5,2));
			$entityInfo .=		"<div class=\"mainOuterRate\"><div class=\"mainInnerRate\" style=\"width: $stars"."px\"></div></div>";
			if( !empty($description) ) {
				$entityInfo .=		"<pre class=\"description\" title=\"$description\">$description</pre>";
			}
			else {
				$entityInfo .= "<pre class=\"description\" title=\"$description\">Description not available.</pre>";
			}		
			
			if( isset($_SESSION["comparisons"][$this->cat_id]["entities"]) ) {
				
			
				if( in_array($this->ent_id, $_SESSION["comparisons"][$this->cat_id]["entities"]) ) {
					$entityInfo .=  	"<button class=\"toCompare\"><span name=\"remove\">Remove from comparison</span></button>";
				}
				else {
					$entityInfo .=  	"<button class=\"toCompare\"><span name=\"add\">Add to comparison</span></button>";
				}
			}
			else {
				$entityInfo .=  	"<button class=\"toCompare\"><span name=\"add\">Add to comparison</span></button>";
				
			}
			$entityInfo .= "</div>";
						
			if( $this->modPanelAccess == 1 ) {
				$entityInfo .= "<div id=\"modPanel\">";
				$entityInfo .= "<h1 class=\"tabHeader\">Moderation Panel</h1>";
				$entityInfo .=	"<a href=\"manageEntities.php?cat_id=".$this->cat_id."\"><button class=\"manage-entities\"><span>Manage entities</span></button></a>";
				$entityInfo .= "</div>";
			}
			$entityInfo .= "</div>";	/*** closing div#basicInfo ***/
			$entityInfo .= "</div>";        /*** closing div#entityInfoInner ***/
			$entityInfo .= "</div>";	/*** closing div#entityInfo ***/ 
			
			$this->content .= $entityInfo;
		}


		private function generate_media_gallery() {
			$video = $this->entity->entity_video;

			$mediaGallery .= "<div id=\"mediaGallery\">";
			$mediaGallery .= "<h1 class=\"tabHeader\" name=\"mediaGalleryInner\">Media gallery</h1>";
			$mediaGallery .= "<div id=\"mediaGalleryInner\">";
			
			if(empty($video)){
				$mediaGallery .= "<a href=\"$video\" class=\"video\">video</a>";
			}
			else {
				$mediaGallery .= "<span>The entity has no media</span>";
			}
			$mediaGallery .= "</div>";
			$mediaGallery .= "</div>";

			$this->content .= $mediaGallery;
		}


		private function generate_entity_attributes(){



			$attributes = $this->category->get_attributes(0);
			
			//$values = $this->entity->entity_attribute_values;
			

			$entityAttributes .= "<div id=\"entityAttributes\">";
			$entityAttributes .= "<h1 class=\"tabHeader\" name=\"entityAttributesInner\">Attributes listing</h1>";
			$entityAttributes .= "<div id=\"entityAttributesInner\">";
			$entityAttributes .=	"<table>";

			echo "<script>var entity = ".json_encode($this->entity).";</script>";
			$i = 0;
			foreach($this->entity->entity_attribute_values as $attr_id=>$attr_val){
				$attr_info = $this->category->get_specific_attribute($attr_id);
				$value= "";
				if($attr_val == "") $value = "undefined";
				else $value = $attr_val;
				$entityAttributes .= "<tr><td><span>".$attr_info->name."</span></td><td><span>:&nbsp;".$value."</span></td></tr>";
				$i += 1;		
			}
			$entityAttributes .= 	"</table>";
			$entityAttributes .= "</div>";	
			$entityAttributes .= "</div>";
			$this->content .= $entityAttributes;
		}


		private function generate_buttons(){
			$this->content .= "<button class=\"browse-entity-previous\" title=\"browse previus entity [alt-z]\" accesskey=\"z\"><span>Previous entity</span></button><button class=\"browse-entity-next\" title=\"browse next entity [alt-c]\" accesskey=\"c\"><span>Next entity</span></button>";
		}

		private function generate_dialog() {
			$this->content .= "<div id=\"dialog-form\"></div>";
		}


		private function passToJs(){
			$nEntities = $this->category->get_number_of_entities();
			$this->content .= "<script>";
			$this->content .= 	"var browseEntity_nEntities = $nEntities; ";
			$this->content .=	"var browseEntity_currentCategoryId = ".$this->cat_id."; ";
			$this->content .=	"var browseEntity_currentEntityId = ".$this->ent_id."; ";
			$this->content .= "</script>";
		}


		private function fetch_arguments(){
			if( isset($_REQUEST['cat_id']) && isset($_REQUEST['ent_id']) ){
				$this->cat_id = intval($_REQUEST['cat_id']);
				$this->ent_id = intval($_REQUEST['ent_id']);
			}
			else{
				$this->error = "MISSING_PARAMETERS";
			}	
		}


		public function BrowseEntity(){
			$this->content = "";
			$this->cat_it = "";
			$this->ent_id = "";
			$this->error = "OK";
			$this->info = "";
			$this->modPanelAccess = "";
			$this->viewAccess = "";
			$this->username = "";
			$this->privileges = "";
			$this->fetch_arguments();
			if($this->error == "OK"){
					
					$this->category = new Category($this->cat_id);
					$this->entity = $this->category->get_specific_entity($this->ent_id);
					/*** CHECKING cat_id , ent_id VALIDITY ***/
					if( $this->category->get_errno() != DB_OK ) {
						$this->generate_content("invalid ids");
						return ;
					}

					/*** CHECKING PRIVILEGES FOR VIEW ACCESS ***/
					if( $this->category->is_open() == OPEN ) {
						$this->viewAccess = 1;
					}
					else {
						if( !isset( $_SESSION['username']) ) {
							$this->viewAccess = -1;
						}
						else {
							$this->category->can_access($this->username,CATEGORY_MEMBER);
							if($this->category->get_errno() == DB_OK) {
								$this->viewAccess = 1;
							}
							else {
								$this->viewAccess = -1;
							}
						}
					}					

					/*** CHECKING PRIVILEGES FOR ENTITY MODERATION***/
					if( isset( $_SESSION['username']) ) {
						$this->username = $_SESSION['username'];
						$this->category->can_access($this->username,EDITOR_MEMBER);
						
						if($this->category->get_errno() == DB_OK) {
							$this->modPanelAccess = 1;
						
						}
						else {
							$this->modPanelAccess = -1;
						}
						//echo "<script>console.log(".json_encode($this->modPanelAccess).");</script>";
					}

					
					
					$this->passToJs();
					$this->generate_content("ids ok");
					
			}	
			else{
				$this->content = $this->error;
			}
		}
		public function get_content(){
			return $this->content;
		}
	}
?>
