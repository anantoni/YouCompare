<?php
/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: view part for page add entity                              **|
|                                                                           **|   
\*****************************************************************************/
	class addEntity{
		private $content;
		
		private function generate_content(){
			$this->content .= "<div id=\"dialog-form\"></div>";
			$this->content .= "<div id=\"addEntity\">";
			$this->content .= "<h1>addEntity</h1>";
			$this->content .= "<button id=\"add-entity\">Add new entity</button>";
			$this->content .= "</div>";	
		}
		public function addEntity(){
			$this->content = "";
			$this->generate_content();
		}
		public function get_content(){
			return $this->content;
		}
	}
?>
