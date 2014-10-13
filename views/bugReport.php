<?php
/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: view part for page bug report	                            **|
|                                                                           **|   
\*****************************************************************************/
	class BugReport{
		private $content;
		
		private function generate_content(){
			$this->content .= "<div id=\"bugReport\">";
			$this->content .= "<h1>Report Bug</h1>";
			$this->content .= "<ul>";
			$this->content .= "<li><textarea placeholder=\"Please supply us with feedback about the bug you found.\"></textarea></li>";
			$this->content .= "<li><button title=\"click to submit bug report\"><span>Submit bug</span></button></li>";
			$this->content .= "</ul>";
			$this->content .= "</div>";	
		}
		public function BugReport(){
			$this->content = "";
			$this->generate_content();
		}
		public function get_content(){
			return $this->content;
		}
	}
?>
