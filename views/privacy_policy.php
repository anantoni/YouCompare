<?php

class Privacy{ 

	private $content;
	
	private function generate_content()
	{
		$this->content .= "<div id=\"privacy\">";
		$this->content .= "Privacy Policy:</br>";
		$this->content .= "We keep the right to maintain your uploaded data,even your account has been deleted or banned.";
		$this->content .= "</div>";
		
	}

	public function Privacy(){
		$this->content = "";
		$this->generate_content();
	}
	
	public function get_content(){
		return $this->content;
	}	
}
?>