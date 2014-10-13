<?php

class Sitemap{ 

	private $content;
	
	private function generate_content()
	{		
		$this->content .= "<div id=\"sitemap\">";
		
		$this->content .= "<ul>";
		
		$this->content .= "<li><a href=\"./index.php\" title=\"Main Page\">Index Page</a></li>";
		$this->content .= "<li><a href=\"./gui_browse_allcategory.php\" title=\"Browse Categories\">Browse Categories</a></li>";
		$this->content .= "<li>User Choices for comparison";
		$this->content .= "<ul>";
		$this->content .= "<li><a href=\"./gui_default_comparison.php\" title=\"Default Compare\">Default Compare</a></li>";
		$this->content .= "<li><a href=\"./gui_custom_comparison.php\" title=\"Custom Compare\">Custom Compare</a></li>";
		$this->content .= "</ul>";
		$this->content .= "</li>";
		$this->content .= "<li><a href=\"./gui_create_category.php\" title=\"Create Category\">Create Category</li>";
		$this->content .= "<li><a href=\"./register.php\" title=\"Register\">Register</li>";
		$this->content .= "<li><a href=\"./contact.php\" title=\"Contact\">Contact</a></li>";
		$this->content .= "<li><a href=\"./sitemap.php\" title=\"Sitemap\">Sitemap</a></li>";
		
		$this->content .= "</ul>";
		
		$this->content .= "</div>";
	}
	
	public function Sitemap(){
		$this->content = "";
		$this->generate_content();
	}
	
	public function get_content(){
		return $this->content;
	}
	
?>
