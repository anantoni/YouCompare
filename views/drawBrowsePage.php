<?php
include_once '../gui.php';

class drawbrowsePage{
	private $content;
	private $numAttr;	//number of category's attributes
	private $numEnt;	//number of items
	private $categoryName;
	private $description;
	private $attributes;//tanle with attributes names;
	private $entities; //each row is one item as follows

	public function create_content($content){
		
		$cont=$content;
		$newp=new Page("browseCategory");
		$newp->set_content($cont);
		
		$newp->display("release");}
	
	}
?>
