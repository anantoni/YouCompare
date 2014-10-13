<?php

/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: view  part for set weights module                          **|
|                                                                           **|   
\*****************************************************************************/


	require_once '../db_includes/DB_DEFINES.php';
	require_once '../db_includes/attribute.php';
	require_once '../db_includes/entity.php';
	require_once '../db_includes/user.php';
	require_once '../db_includes/category.php';


	if( isset($_REQUEST['cat_id']) ) {

		$content = "";

		$cat_id = $_REQUEST['cat_id'];

		$categ = new Category($cat_id);
		$attributes = $categ->get_attributes(1);

		$content .= "<h1 class=\"tabHeader\" name=\"div.set-weights-inner\">Weights Panel</h1>";
		$content .= "<div class=\"set-weights-inner\">";
		$content .= 	"<div class=\"basic-info\"><h1 class=\"tabHeader\" name=\"span.basic-info-span\">Info</h1><span class=\"basic-info-span\">Please select the weights for the comparison according to your preference.</span></div>";

		



		/*** COUNTABLE ATTRIBUTES ***/
		$attributes_countable .= "<div class=\"countable-attributes-wrapper\">";
		$attributes_countable .= "<h1 class=\"tabHeader\" name=\"table.attribute_weights\">Countable attributes</h1>";
		$attributes_countable .= "<table class=\"attribute_weights\">";	
		$attributes_countable .= "<tr><th title=\"uncheck to ignore attribute values in comparison\">Include</th><th title=\"name of attribute\">Name</th><th title=\"e.g (if you are interested in the entity with the higher value select \"higher\")\">Focus</th><th>Main Weight</th><th>Main Weight*</th><th title=\"if you are interested in values within a range enable the checkbox and set the range using the slider\">Range&nbsp;&nbsp;&nbsp;</th><th title=\"range min numeric indicator\">Min*</th><th title=\"range max numeric indicator\">Max*</th><th title =\"if you are interested in a specific value in that attribute\">Specific*</th></tr>";
		foreach( $attributes as $key=>$attribute ) {
			if( $attribute->comparability == 2) {
				$attributes_countable .= "<tr name=\"".$attribute->id."\">";



				$attributes_countable .= "<td class=\"non-hideable\" name=\"".$attribute->id."\"><input type=\"checkbox\" class=\"countable-include-checkbox\" name=\"".$attribute->id."\" checked=\"checked\"></td>";



				$attributes_countable .= "<td class=\"non-hideable\" name=\"".$attribute->id."\"><span>".$attribute->name."</span></td>";



				$attributes_countable .= "<td class=\"hideable\" name=\"".$attribute->id."\"><input class=\"choice-indicator\" value=\"focus\" type=\"radio\" name="."countable-".$attribute->id."\" checked=\"checked\"><select id=\"cmp_focus\"><option name=\"higher\">higher</option><option name=\"average\">average</option><option name=\"lower\">lower</option></select></td>";



				$attributes_countable .= "<td class=\"hideable\" name=\"".$attribute->id."\"><div class=\"slider\" name="."countable-".$attribute->id." ></div></td>";



				$attributes_countable .= "<td class=\"hideable\" name=\"".$attribute->id."\"><input class=\"slider_indicator\" name="."countable-".$attribute->id." type=\"number\" value=\"50\"/></td>";



				$attributes_countable .= "<td class=\"hideable\" name=\"".$attribute->id."\"><input class=\"choice-indicator\" value=\"range\" type=\"radio\" name="."countable-".$attribute->id."\"><div class=\"slider-range\" name="."countable-".$attribute->id." min=\"".$attribute->type_elements["Lower Limit"]."\" max=\"".$attribute->type_elements["Upper Limit"]."\"></div></td>";



				$attributes_countable .= "<td class=\"hideable\" name=\"".$attribute->id."\"><input class=\"slider-range-indicator-min\" name="."countable-".$attribute->id." type=\"number\" value=\"".$attribute->type_elements["Lower Limit"]."\"/></td>";



				$attributes_countable .= "<td class=\"hideable\" name=\"".$attribute->id."\"><input class=\"slider-range-indicator-max\" name="."countable-".$attribute->id." type=\"number\" value=\"".$attribute->type_elements["Upper Limit"]."\"/></td>";



				$attributes_countable .= "<td class=\"hideable\" name=\"".$attribute->id."\"><input class=\"choice-indicator\" value=\"specific\" type=\"radio\" name="."countable-".$attribute->id."\"><span class=\"specific-value-indicator-label\">Enable</span><input class=\"specific-value-indicator\" name="."countable-".$attribute->id." type=\"number\" value=\"". ($attribute->type_elements["Upper Limit"] + $attribute->type_elements["Lower Limit"]) / 2 ."\"/></td>";




				$attributes_countable .= "</tr>";
			}	
		}
		$attributes_countable .= "</table>";
		$attributes_countable .= "</div>";	
		$content .= $attributes_countable;
		/*** END OF COUNTABLE ATTRIBUTES ***/





		/*** DISTINCT ATTRIBUTES ***/
		$attributes_distinct = "<div class=\"distinct-attributes\">";
		foreach( $attributes as $key=>$attribute ) {
			if( $attribute->comparability == 1) {
				$attributes_distinct .= "<div class=\"distinct-attribute\" name=\"".$attribute->id."\">";
				$attributes_distinct .=		"<h1 class=\"tabHeader\" name=\"div.distinct-attribute-inner".$attribute->id."\">"."Distinct:&nbsp;".$attribute->name."<span class=\"distinct-include-checkbox\"> | Include: </span> <input title=\"uncheck to ignore attribute\" type=\"checkbox\" class=\"distinct-include-checkbox\" name=\"".$attribute->id."\" checked=\"checked\"></h1>";
				$attributes_distinct .=		"<div class=\"distinct-attribute-inner".$attribute->id."\" name=\"".$attribute->id."\" >";
				$attributes_distinct .=		"<table class=\"distinct_attribute_main_weight\" name=\"".$attribute->id."\">";
				$attributes_distinct .=		"<tr>";
				$attributes_distinct .=			"<td class=\"main_title\"><span>Main weight</span></td>";
				$attributes_distinct .=			"<td class=\"slider-td\"><div class=\"slider\" name=".$attribute->id." ></div></td>";
				$attributes_distinct .=			"<td><input class=\"slider_indicator\" name=".$attribute->id." type=\"number\" value=\"50\"/></td>";
				$attributes_distinct .=		"</tr>";
				$attributes_distinct .=		"</table>";

				$attributes_distinct .= 	"<table class=\"distinct_attribute_weights\" name=\"".$attribute->id."\">";
				$attributes_distinct .=			"<tr><th>Weights per distinct value</th></tr>";
				$attributes_distinct .=			"<tr><th>Name</th><th>Slider</th><th title=\"slider's numeric indicator\">Numeric</th></tr>";
				foreach($attribute->type_elements as $index=>$element) {
					
					$attributes_distinct .=		 "<tr>";
				
					$attributes_distinct .= 		"<td><span class=\"name_indicator\">".$element["Value"]."</span></td>";
					$attributes_distinct .= 		"<td><div class=\"slider\" name=".$attribute->id."-".$index." ></div></td>";
					$attributes_distinct .=			"<td><input class=\"slider_indicator\" name=".$attribute->id."-".$index." type=\"number\" value=\"50\"/></td>";

					$attributes_distinct .= 	"</tr>";
				}
				$attributes_distinct .= "</table>";
				$attributes_distinct .= "</div>";
				$attributes_distinct .= "</div>";
			}
		}
		$attributes_distinct .= "</div>";
		$content .= $attributes_distinct;
		/*** END OF DISTINCT ATTRIBUTES ***/




		/*** creating comparison button ***/
		//$comparison_button .= 
		//$content .= $comparison_button;
		$content .=	"</div>";
		echo $content;




	}
	else {
		echo "invalid arguments";
	}


?>
