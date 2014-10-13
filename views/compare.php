<?php
/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: view part for page compare                                 **|
|                                                                           **|   
\*****************************************************************************/
	require_once './db_includes/category.php';

	class Compare{
		private $content;
		private $set_cat_id;
		private $set_category_name;
		
		private function generate_category_selector(){
			if ( isset( $_SESSION['comparisons'] ) ) {
				$this->content .= "<select id=\"category_select\" class=\"category_select\">";
				$this->content .= "<option name=\"default\">See all available categories for comparison</option>";
				foreach( $_SESSION["comparisons"] as $key=>$cmp_category ) {
					
					$categ = new Category($key);
					$categ_name = $categ->get_name();
					$this->content .= "<option name=\"$key\">$categ_name</option>";
				}
				$this->content .= "</select>";
			}
			else {
				$this->content .= "<span>please add entities to compare first!</span>";
			}
			
		}
		private function generate_content(){
			$this->content .= "<div id=\"compare\">";
			$this->content .= "<h1 class=\"tabHeader\" name=\"div.compare-inner\">Compare</h1>";

			$this->content .= 	"<div class=\"compare-inner\">";

			$this->content .=		"<div class=\"comparison-selection-wrapper\">";
			$this->content .=			"<h1 class=\"tabHeader\" name=\"div.comparison-selection-inner\">Comparison selection</h1>";
			$this->content .=			"<div class=\"comparison-selection-inner\">";
			$this->content .=				"<div =\"comparison-selection-currently-set\">";
			$this->content .=					"<span>Currenty set for category: </span><span class=\"comparison-selection-indicator\" name=\"$this->cat_id\">".$this->set_category_name."</span>";
			$this->content .=				"</div>";	
			$this->content .=				"<div class=\"comparison-selection-available-selections\">";

			$this->generate_category_selector();

			$this->content .=				"</div>";
			$this->content .=			"</div>";
			$this->content .=		"</div>";
			
			$this->content .=		"<div class=\"compare-type-selection-wrapper\">";
			$this->content .=			"<h1 class=\"tabHeader\" name=\"div.compare-type-selection-inner\">Please select type of comparison</h1>";
			$this->content .=			"<div class=\"compare-type-selection-inner\">";
			$this->content .=				"<input type=\"radio\" name=\"comparison_type\" value=\"default\" checked=\"checked\"><span>default</span><input type=\"radio\" name=\"comparison_type\" value=\"custom\"><span>custom</span>";
			$this->content .=			"</div>";
			$this->content .=		"</div>";
			
			
			
			$this->content .= 		"<div id=\"set_weights\"></div>";
			$this->content .=		"<div class=\"comparison-button-panel\"><button class=\"proceed-to-comparison\"><span>Proceed to comparison</span></button></div>";
			$this->content .=	"</div>";
			$this->content .= "</div>";	
		}
		private function fetch_arguments() {
			if( isset($_REQUEST['cat_id']) ) {
				$this->cat_id = $_REQUEST['cat_id'];
				$categ = new Category($this->cat_id);
				$this->set_category_name = $categ->get_name();
			}
			else {
				$this->cat_id = "n/a";
				$this->set_category_name = "n/a";
	
			}
		}
		public function Compare(){
			$this->content = "";
			$this->fetch_arguments();
			$this->generate_content();
		}
		public function get_content(){
			return $this->content;
		}
	}
?>
