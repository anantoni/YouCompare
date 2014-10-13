<?php


class Disclaimer{

	private $content;
	
	private function generate_content()
	{
		$this->content .= "<div id=\"disclaimer\">";
		$this->content .= "With your access to “YouCompare” (from now on “we”, “our”, “ours”, “YouCompare”, “http://YouCompare.com”), you accept all following terms of law.</br>";
		$this->content .= "If you do not accept all following terms of law,please do not register and use \"YouCompare\".It is possible for us to change the terms at any time</br>";
		$this->content .= "and we shall try to inform you as soon as possible with the appropriate way,but, please reconsider to read periodically again the current page.</br>";
		$this->content .= "</br></br>";
		$this->content .= "You accept not to publish any inappropriate content which is restricted by your country's law.";		
		$this->content .= "</div>";
	}

	public function Disclaimer(){
		$this->content = "";
		$this->generate_content();
	}
	public function get_content(){
		return $this->content;
	}
}

?>