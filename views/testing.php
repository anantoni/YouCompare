<?php
require_once 'browseCategory.php';
require_once 'drawBrowsePage.php';

//$fullPage=new drawbrowsePage();

	$c=new browse_CategoryPage();
	$sth=12;
	$c->set_numAttr($sth);
	$foo="Kitsos\n";
	$c->set_categoryName($foo);
	$c->set_numEnt($sth);
	$var=array("A","B","C","D","E","F","A","B","C","D","E","F");
	$c->set_attributes($var);
	
	for($j=0;$j<$sth;$j++)
	{	for($i=0;$i<14;$i++)
			{$var1[$j][$i]=$i;}
		$var1[$j][13]=0;
		$var1[$j][14]="/images/add.png";
	}
	
	$c->set_entities($var1);
	$c->create_content();
	$koko=$c->get_content();
	
		//$newp=new Page("browseCategory");
		//$newp->set_content($koko);
		
		//$newp->display("release");
	
	//$fullPage->create_content($koko);
	
	echo $koko;
?>
