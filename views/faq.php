<?php



class Faq{ 

	private $content;
	
	private function generate_content()
	{
		$this->content .= "<div id=\"faq\">";

		
		$this->content .= "<ul>";
		$this->content .= "<li><a href=\"faq.php#login\">How to login?</a></li>";
		$this->content .= "<li><a href=\"faq.php#register\">How to register?</a></li>";
		$this->content .= "<li><a href=\"faq.php#createCategory\">How to create a category?</a></li>";
		$this->content .= "<li><a href=\"faq.php#simpleCompare\">How to compare?</a></li>";
		$this->content .= "<li><a href=\"faq.php#customCompare\">How to make a custom comparison?</a></li>";
		$this->content .= "</ul>";
		$this->content .= "</br></br>";

		$this->content .= "<a name=\"login\"></a>How to login?";
		$this->content .= "Just fill in your username and password and press the <b>\"Login\"</b> button.";
		$this->content .= "</br></br></br>";
		$this->content .= "<a name=\"register\"></a>How to register?";
		$this->content .= "Just fill the necessary info ( and add any optional info you want) and press <b>\"Register\"</b> button";
		$this->content .= "</br></br></br>";

		$this->content .= "<a name=\"createCategory\"></a>How to create a category?";
		$this->content .= "Fill in the name of category.Add items.For each item,add the necessary values.";
		$this->content .= "</br></br></br>";

		$this->content .= "<a name=\"simpleCompare\"></a>How to compare?";
		$this->content .= "Just press the compare button and fill in the name of category.";
		$this->content .= "</br></br></br>";

		$this->content .= "<a name=\"customCompare\"></a>How to make a custom comparison?";
		$this->content .= "Choose the items you want to compare and then fill in your personal preferences for filters about the attributes.";
		$this->content .= "</br></br></br>";
		
		$this->content .= "</div>";
	}
	public function Faq(){
		$this->content = "";
		$this->generate_content();
	}
	public function get_content(){
		return $this->content;
	}

}

?>