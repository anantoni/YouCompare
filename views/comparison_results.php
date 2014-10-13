<?php
	
/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: Back-end Part for page comparison results                  **|
|                                                                           **|   
\*****************************************************************************/

	require_once './db_includes/DB_DEFINES.php';
	require_once './db_includes/category.php';
	require_once './db_includes/entity.php';
	require_once './db_includes/attribute.php';
	require_once "results_specs.php";

	class ComparisonResults{
		/** content vars **/
		private $content;
		
		private $category;
		private $category_name;		
		private $cat_id;

		private $compare_input;
		private $scores;		

		private function generate_content(){
			$this->content .= "<div id=\"comparison-results\">";
			$this->content .=	"<h1 class=\"tabHeader\" name=\"div#scores-wrapper\">"."Comparison results for selected entities of category:".$this->category_name."</h1>";
			$this->content .= 	"<div class=\"comparison-results-inner\">";
			$this->content .=		"<div class=\"comparison-results-button-panel\">";
			$this->content .=			"<a href=\"comparison.php?cat_id=".$this->cat_id."\"><button class=\"compare-again\"><span>Compare again</span></button></a>";
			$this->content .=			"<a href=\"browseCategory.php?cat_id=".$this->cat_id."\"><button class=\"comparison-results-browse-category\"><span>Browse category $category_name</span></button></a>";					
				
			

			$this->content .= 	"</div>";
			$this->content .= $this->render_results();
			$this->content .= "</div>";	
		}

		private function create_compare_compatible_array() {
			
			
			$compatible_array = array();
			$compatible_array["entities"] = array();
			$compatible_array["entities"] = $_SESSION["comparisons"][$this->cat_id]["entities"];
			$compatible_array["attributes"] = array(); 
			$compatible_array["weights"] = array();
			$compatible_array["attribute_type"] = array();
			$compatible_array["distinct_individual_weights"] = array();
			$compatible_array["distinct_individual_names"] = array();
			$compatible_array["choice"] = array();
			$compatible_array["focus"] = array();
			$compatible_array["range"] = array();
			$compatible_array["specific"] = array();

			$attr_count = 0;

			//print_r($_SESSION["comparisons"]);
			
			if( isset($_SESSION["comparisons"][$this->cat_id]["weights"]["countable_attributes"]) ) {
				foreach($_SESSION["comparisons"][$this->cat_id]["weights"]["countable_attributes"] as $key=>$countable_attribute) {
					$compatible_array["attributes"][] = strval($key);
					$compatible_array["focus"][] =  $countable_attribute["focus"];
					$compatible_array["weights"][] = $countable_attribute["weight"];
					$compatible_array["attribute_type"][] = COUNTABLE;
					$compatible_array["choice"][] = $countable_attribute["choice"];
					$compatible_array["range"][] = $countable_attribute["range"];
					$compatible_array["specific"][] = $countable_attribute["specific"];
					$attr_count++;
				}
			}
			if(isset($_SESSION["comparisons"][$this->cat_id]["weights"]["distinct_attributes"])) {
				foreach($_SESSION["comparisons"][$this->cat_id]["weights"]["distinct_attributes"] as $key=>$distinct_attribute) {
			
					$compatible_array["attributes"][] = strval($key);
					$compatible_array["weights"][] = $distinct_attribute["main_weight"];
					$compatible_array["attribute_type"][] = DISTINCT;
					$compatible_array["distinct_individual_names"] = $distinct_attribute["individual_names"];
				
					$compatible_array["distinct_individual_weights"][$attr_count++] = $distinct_attribute["individual_weights"];
				}
			}
			//echo "<script>console.log(\"compatible array\",".json_encode($compatible_array).");</script>";	
			return $compatible_array;		

		}
		private function generate_script() {
			/** generating js code to be used for charts **/
				$script = "<script>";


				$script .= "var scores = ".json_encode($this->scores).";\n";
				$script .= "console.log(scores);\n"; 			
				$script .= "</script>";
				$this->content .= $script;

				/***** old ****/
				/*
				$script .= "var cmp_entity_names = [];";
				$script .= "var cmp_entity_total_scores = [];";
				$script .= "var cmp_attribute_names = [];";
				$script .= "var cmp_attribute_scores = [];";

				foreach($this->compare_input["attributes"] as $key=>$attribute) {
					$script .= "cmp_attribute_names[$key] = \"".$this->scores["attribute_name"][$key]."\"; ";

					$script .= "cmp_attribute_scores[$key] =  "."[]"." ; ";
					foreach($this->compare_input["entities"] as $index=>$entity) {						
						$script .= "cmp_attribute_scores[$key][$index] =  ".$this->scores["attribute_score"][$key][$index]." ; ";
					}
				}
			
				foreach($this->compare_input["entities"] as $key=>$entity) {
					$entity_info = $this->category->get_specific_entity( $entity );
					$script .= "cmp_entity_names[$key] = \"".$entity_info->entity_name."\"; ";
					$script .= "cmp_entity_total_scores[$key] = ".$this->scores["entity_total_score"][$entity]."; ";
				

					$results_content .= "<tr>";
					$results_content .=  "<td><span>".$entity_info->entity_name."</span></td>";
					$results_content .=  "<td><div class=\"score-bar\" name=\"".$this->scores["entity_total_score"][$entity]."\"><meter class=\"score-meter\">".$this->scores["entity_total_score"][$entity]."</meter></div></td>";
					$results_content .= "</tr>";
					//var_dump($this->scores["entity_total_score"][$entity]);
				}
				$script .= "</script>";
				var_dump($script);
				*/

		}

		private function generate_results() {
			$this->compare_input = $this->create_compare_compatible_array();

			
			$this->results = new Results($this->category,$this->cat_id,$this->compare_input["entities"],$this->compare_input["attributes"],$this->compare_input["attribute_type"],$this->compare_input["weights"],$this->compare_input["focus"],$this->compare_input["distinct_individual_weights"],$this->compare_input["distinct_individual_names"],$this->compare_input["choice"],$this->compare_input["range"],$this->compare_input["specific"]);



			$this->scores = $this->results->get_results();
			$this->generate_script();
		}

		private function render_results() {
			$results_content = "";


			$results_content .= "<div id=\"scores-wrapper\">";
			$results_content .= 	"<h1 class=\"tabHeader\" name=\"div.scores-inner\">Scores</h1>";
			$results_content .= 	"<div class=\"scores-inner\">";


			/*** SELECTED ENTITY INFO ***/
			$results_content .=		"<div class=\"entity-info-wrapper\"></div>";
			
			/*** RANKINGS ***/
			$results_content .=		"<div id=\"rankings-wrapper\">";
			$results_content .=			"<h1 class=\"tabHeader\" name=\"div.rankings-inner\">Rankings</h1>";
			$results_content .=			"<div class=\"rankings-inner\">";
			$results_content .=				"<div class=\"options-panel\">";
			$results_content .=				"</div>";
			$results_content .=				"<div id=\"rankings-content\"></div>";
			$results_content .=			"</div>";
			$results_content .=		"</div>";


			/*** TOTAL SCORES ***/			
			$results_content .=		"<div id=\"total-scores-wrapper\">";
			$results_content .=			"<h1 class=\"tabHeader\" name=\"div.total-scores-inner\">Total scores</h1>";
			$results_content .=			"<div class=\"total-scores-inner\">";
			$results_content .=				"<div class=\"options-panel\">";
			//$results_content .=					"<button class=\"fullscreen\"><span>View fullscreen</span></button>";
			$results_content .=				"</div>";
			$results_content .=				"<div id=\"google-total-scores\"></div>";			
			$results_content .=			"</div>";
			$results_content .=		"</div>";

			/*** ATTRIBUTE SCORES ***/
			$results_content .=		"<div id=\"attribute-scores-wrapper\">";
			$results_content .=			"<h1 class=\"tabHeader\" name=\"div.attributes-scores-inner\">Attribute scores</h1>";
			$results_content .=			"<div class=\"attributes-scores-inner\">";
			$results_content .=				"<div class=\"options-panel\">";
			$results_content .=					"<select class=\"attribute-select\"><option name=\"default\" value=\"default\">Please select an attribute</option></select>";
			$results_content .=					"<button class=\"editor-trigger\"><span>Open chart editor</span></button>";
			//$results_content .=					"<button class=\"fullscreen\"><span>View fullscreen</span></button>";
			$results_content .=				"</div>";
			$results_content .=				"<div id=\"google-attribute-scores\"></div>";						
			$results_content .=			"</div>";
			$results_content .=		"</div>";

			/*** TABULAR VISUALIZATION ***/
			$results_content .=		"<div id=\"tabular-scores-wrapper\">";
			$results_content .=			"<h1 class=\"tabHeader\" name=\"div.tabular-scores-inner\">Tabular visualisation of scores</h1>";
			$results_content .=				"<div class=\"tabular-scores-inner\">";
			$results_content .=				"<div class=\"options-panel\">";
			$results_content .=					"<div class=\"tabular-scores-pagination\">";
			$results_content .=						"<span>Entities per page</span>";

			$results_content .= 						"<select class=\"tabular-max-per-page\">";
			$results_content .=							"<option name=\"5\" value=\"5\">5</option>";
			$results_content .=							"<option name=\"10\" value=\"10\">10</option>";
			$results_content .=							"<option name=\"20\" value=\"20\">20</option>";
			$results_content .=							"<option name=\"50\" value=\"50\">50</option>";
			$results_content .=							"<option name=\"100\" value=\"100\">100</option>";
			$results_content .=						"</select>";

			$results_content .=					"</div>";
			//$results_content .=					"<button class=\"fullscreen\"><span>View fullscreen</span></button>";
			$results_content .=				"</div>";
			$results_content .=					"<div id=\"google-tabular-scores\"></div>";
			$results_content .=				"</div>";
			$results_content .=		"</div>";


			/*** CHART EDITOR VISUALIZATION ***/
			/*
			$results_content .=		"<div id=\"editor-scores-wrapper\">";
			$results_content .=			"<h1 class=\"tabHeader\" name=\"div.editor-scores-inner\">Chart editor</h1>";
			$results_content .=				"<div class=\"editor-scores-inner\">";
			$results_content .=					"<button class=\"editor-trigger\"><span>Open chart editor</span></button>";
			$results_content .=					"<div id=\"google-editor-scores\"></div>";
			$results_content .=				"</div>";
			$results_content .=		"</div>";
			*/


			$results_content .= 	"</div>";

			$results_content .=	"</div>";

			$results_content .= "</div>";

			return $results_content;
			//var_dump($results_content);
			/*** end generating total scores vizualization ***/			





		}
		private function check_arguments() {
			$status = "invalid arguments";
			if( isset($_REQUEST["cat_id"]) ) {
				$this->cat_id = intval($_REQUEST["cat_id"]);
				echo "<script>console.log(".json_encode($_SESSION["comparisons"]).");</script>";			
				
				if( isset($_SESSION["comparisons"][$this->cat_id]) ) {
					$status = "arguments ok";
				}
			}
			return $status;
		}

		private function pass_to_js() {
			
		}
		public function ComparisonResults(){
			$this->content = "";
			
			if( $this->check_arguments() == "arguments ok" ) {
				$this->category = new Category($this->cat_id);
				$this->category_name = $this->category->get_name();
				
				$this->generate_results();
				$this->generate_content();
			}
			else $this->content = "invalid arguments";
		}
		public function get_content(){
			return $this->content;
		}
	}
?>
