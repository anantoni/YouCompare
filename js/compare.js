/*** file: compare.js ***/
/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: Gui Part for page compare  (javascript)                    **|
|                                                                           **|   
\*****************************************************************************/


var comparison_type;
var available_categories_count = 0;
var choice_cat_id;


function set_weights_div(event){
	
	

	var caller = event.data.mode;

	var selector_cat_id = $("#category_select option:selected").attr("name");
	var preset_category = $("span.comparison-selection-indicator").attr("name");

	if(caller == "select") {
		if( selector_cat_id != "default" ) {
			$("span.comparison-selection-indicator").html($("#category_select option:selected").html());
			$("span.comparison-selection-indicator").attr("name",selector_cat_id);
			choice_cat_id = selector_cat_id;
		}
		else {
			$("span.comparison-selection-indicator").html("n/a");
			$("span.comparison-selection-indicator").attr("name","n/a");
			choice_cat_id = "n/a";
		}
	}
	else {

		choice_cat_id = preset_category;

	}

	if( comparison_type == "custom" && choice_cat_id != "n/a" ) {
		/*console.log("here");*/
		
		

		var category_id = "";




			$("body").css("cursor", "progress");
			/*if(selector_cat_id == "default") {
				category_id = preset_category;
				$("span.comparison-selection-indicator").html("n/a");
			}			
			else {
				$("span.comparison-selection-indicator").html($("#category_select option:selected").html());
				category_id = selector_cat_id;
			}
			*/






			$.post("views/compare_set_weights.php", { cat_id: choice_cat_id },function(xml){
				if(xml.length > 0){	
					//console.log(xml);	
					$("#set_weights").html(xml);
					$("body").css("cursor", "auto");
					/*** MAIN WEIGHTS SLIDERS & INDICATORS ***/
					/*** SETTING UP SLIDER FOR EACH ATTRIBUTE MAIN WEIGHT***/
					$( ".slider" ).slider({
						value:50,
						min: 0,
						max: 100,
						step: 1,
						slide: function( event, ui ) {
							var id = $(this).attr("name");
							var index = $( ".slider" ).index();
							
							/*** BINDING SIDE INPUT CHANGES WITH ASSOSIATED SLIDER  ***/
							$("input.slider_indicator").each(function() {
								//console.log($(this).attr("name"));
								if( $(this).attr("name") == id ) $(this).val(ui.value);
							});
						}
					});
					/*** BINDING SLIDER CHANGES WITH ASSOSIATED SIDE INPUT ***/
					$("input.slider_indicator").change(function() {
							var id = $(this).attr("name");
							var value = $(this).val();
							$(".slider").each(function() {
								if( $(this).attr("name") == id ) $(this).slider("value",value);
							});
					});	
					/*** END OF MAIN WEIGHTS SLIDERS & INDICATORS ***/
	
					/*** COUNTABLE ATTRIBUTES RANGE SLIDERS ***/
					var range_specs = [];
					$(".slider-range").each(function(index) {
						var tempArray =[];
						tempArray[0] = $(this).attr("min").toString();
						tempArray[1] = $(this).attr("max").toString();
						//console.log($(this).attr("min"),$(this).attr("max"));
						range_specs[index] = tempArray;
						$(this).slider({
							range: true,
							min: $(this).attr("min"),
							max: $(this).attr("max"),
							animate: "slow",
							values: tempArray,
							slide: function( event, ui ) {
								var id = $(this).attr("name");
							
								/*** BINDING SIDE INPUT CHANGES WITH ASSOSIATED RANGE SLIDER  ***/
								$("input.slider-range-indicator-min").each(function() {
									//console.log($(this).attr("name"));
									if( $(this).attr("name") == id ) $(this).val(ui.values[0]);
								});
								$("input.slider-range-indicator-max").each(function() {
									//console.log($(this).attr("name"));
									if( $(this).attr("name") == id ) $(this).val(ui.values[1]);
								});
								
							}
						});
					});
					/*** BINDING RANGE SLIDER CHANGES WITH ASSOSIATED SIDE INPUTS ***/
					$("input.slider_indicator").change(function() {
							var id = $(this).attr("name");
							var value = $(this).val();
							$(".slider").each(function() {
								if( $(this).attr("name") == id ) $(this).slider("value",value);
							});
					});				
				
					/*** END OF COUNTABLE ATTRIBUTES RANGE SLIDERS ***/

					//$(".comparison-button-panel button.proceed-to-comparison").bind("click",{cat_id: choice_cat_id },proceed_to_comparison);	
				}
				else{
					console.log("oops , empty response.sorry.");	
				}
			});
		
		}
		else if(comparison_type == "custom" && choice_cat_id == "n/a") {
			$("#set_weights").html("<span>No category selected</span>");
		}	
		else {
			$("#set_weights").html("");
			/*
			if($("#category_select option:selected").attr("name") == "default") {
				$("span.comparison-selection-indicator").html("n/a");
			}
			else {
				$("span.comparison-selection-indicator").html($("#category_select option:selected").html());
			}
			*/
		}
}

function proceed_to_comparison() {

	console.info("proceed pressed ",$("span.comparison-selection-indicator").attr("name"),choice_cat_id);
	var cat_id;

	
	if(comparison_type == "default") {

		cat_id = $("span.comparison-selection-indicator").attr("name");
		

	}
	else {

		cat_id = choice_cat_id

	}
	

	
	if( cat_id == "n/a" ) {
		alert("please select a category available for comparison first!");
		return;
	}


	var weightsObj = {};
	weightsObj.cat_id = cat_id;
		

	if( comparison_type == "custom") {
		
		
		weightsObj.countable_attributes = {};
		weightsObj.distinct_attributes = {};	


		/*** passing countable attributes to object ***/
		$("table.attribute_weights tr").each(function(index) {
	
			if(index !=0) {
				//var countable_attr = {};
				var attr_id = $(this).find("input.slider_indicator").attr("name").substring(10);
				var attr_values = {};
				var range_values = {};		

				var radio_choice;
								

			
				/*** fetching main weight ***/
				attr_values.weight = $(this).find("input.slider_indicator").val();

				
				 
				


				attr_values.focus = $(this).find("select#cmp_focus").val();
				
				range_values.min = $(this).find("input.slider-range-indicator-min").val();
				range_values.max = $(this).find("input.slider-range-indicator-max").val();		
				attr_values.range = range_values;
				attr_values.specific = $(this).find("input.specific-value-indicator").val();
				
				
				radio_choice = $(this).find("input.choice-indicator:checked").val();
				console.log(radio_choice);

				
				
				attr_values.choice = radio_choice;

				weightsObj.countable_attributes[attr_id] = attr_values;
			}
		});

		/*** passing distinct attributes to object ***/
		$("div.distinct-attribute").each(function() {
		
			var attr_id;
			var attr_values = {};
			attr_values.individual_weights = [];
			attr_values.individual_names = [];

			attr_id = $(this).attr("name");
			attr_values.main_weight = $(this).find("table.distinct_attribute_main_weight input.slider_indicator").val();
		
			$(this).find(" table.distinct_attribute_weights input.slider_indicator").each(function(index) {
				attr_values.individual_weights[index] = $(this).val();
	
			});
			$(this).find(" table.distinct_attribute_weights span.name_indicator").each(function(index) {
				attr_values.individual_names[index] = $(this).text();

			});
		
			weightsObj.distinct_attributes[attr_id] = attr_values;
		
		});

		$.post("logic_includes/logic.php", { call: "set_weights_to_session" , weights: weightsObj , cat_id: cat_id },function(xml){
			if(xml.length > 0){
				console.info(xml);
				/*** redirecting to results page ***/

				//window.location.replace( location.pathname.substr(1,21) + "comparison_results.php?cat_id="+cat_id+"&comparison_type=custom");
				//window.location = "cgi.di.uoa.gr"  + location.pathname.substr(0,21) + "comparison_results.php?cat_id="+cat_id+"&comparison_type=custom";
				//window.location = "http://" + document.location.hostname + "/youcompare/comparison_results.php?cat_id="+cat_id+"&comparison_type=custom";
				/* WTF WTF WTF */
				window.location = "comparison_results.php?cat_id="+cat_id+"&comparison_type=custom";
			}
			else {
				console.warning("oops , empty response.sorry.");
			}
		});
	}
	else {
		
		//window.location = "http://" + document.location.hostname + "/youcompare/comparison_results.php?cat_id="+cat_id+"&comparison_type=default";
		window.location = "comparison_results.php?cat_id="+cat_id+"&comparison_type=default";
	


	}

	
	
	
}


function toggleCountableAttribute() {
	//console.info("toggleAttribute",$(this).attr("name"));
	

	var attr_name = $(this).attr("name");


	if($(this).attr("checked") != "checked") {
		if($(this).attr("class") == "countable-include-checkbox") {		
		
			var $weight_indicator = null;
			$weight_indicator = $("div.countable-attributes-wrapper").find("input.slider_indicator");
			$weight_indicator.each(function(){
				console.log($(this).attr("name"),attr_name);
				if($(this).attr("name") == "countable-"+attr_name) {
							
					$(this).val(0);
				
				}
			});
		
			var $weight_slider = null;
			$weight_slider = $("div.countable-attributes-wrapper").find(".slider");
			$weight_slider.each(function(){
				console.log($(this).attr("name"),attr_name);
				if($(this).attr("name") == "countable-"+attr_name) {
								
					$(this).slider("value",0);
				
				}
			});
		}	
	}
	else {
		var $weight_indicator = null;
		$weight_indicator = $("div.countable-attributes-wrapper").find("input.slider_indicator");
		$weight_indicator.each(function(){
			console.log($(this).attr("name"),attr_name);
			if($(this).attr("name") == "countable-"+attr_name) {
							
				$(this).val(50);
			
			}
		});
		
		var $weight_slider = null;
		$weight_slider = $("div.countable-attributes-wrapper").find(".slider");
		$weight_slider.each(function(){
			console.log($(this).attr("name"),attr_name);
			if($(this).attr("name") == "countable-"+attr_name) {
							
				$(this).slider("value",50);
			
			}
		});

	}

	$("div.countable-attributes-wrapper").find("table.attribute_weights td.hideable").each(function() {	
		if($(this).attr("name") == attr_name)// $(this).toggle("fast");
			if(!$(this).parent("tr").find(".countable-include-checkbox").is(":checked"))
				$(this).css("visibility","hidden");
			else
				$(this).css("visibility","visible");
	});
}


function toggleDistinctAttribute(event) {
	var attr_name = $(this).attr("name");

	event.stopPropagation();
	if($(this).attr("checked") != "checked") {		
		console.log("1");
		var $weight_indicator = null;
		$weight_indicator = $("div.distinct-attribute").find("input.slider_indicator");
		$weight_indicator.each(function(){
			//console.log($(this).attr("name"),attr_name);
			if($(this).attr("name") == attr_name) {
						
				$(this).val(0);
			
			}
		});
		var $weight_slider = null;
		$weight_slider = $("div.distinct-attribute").find(".slider");
		$weight_slider.each(function(){
			//console.log($(this).attr("name"),attr_name);
			if($(this).attr("name") == attr_name) {
							
				$(this).slider("value",0);
			
			}
		});
		
		//console.log("div.distinct-attributes"+attr_name);
		$("div.distinct-attribute-inner"+attr_name).toggle("fast");

		
		
	}	
	else {
		console.log("2");
		var $weight_indicator = null;
		$weight_indicator = $("div.distinct-attribute").find("input.slider_indicator");
		$weight_indicator.each(function(){
			//console.log($(this).attr("name"),attr_name);
			if($(this).attr("name") == attr_name) {
							
				$(this).val(50);
			
			}
		});
		
		var $weight_slider = null;
		$weight_slider = $("div.distinct-attribute").find(".slider");
		$weight_slider.each(function(){
			//console.log($(this).attr("name"),attr_name);
			if($(this).attr("name") == attr_name) {
							
				$(this).slider("value",50);
			
			}
		});
		

	}

	


}

function radio_comparison_type_change() {
	comparison_type = $(this).val();
	set_weights_div(
		{
			data: {
				mode: "radio"
			}
		}
	);
	console.log(comparison_type);

}

function toggleHeader() {
	
	var toToggle = $(this).attr("name");
	if( toToggle.substr(0,28) == "div.distinct-attribute-inner") {
		if( $(this).find("input.distinct-include-checkbox").attr("checked") == "checked" ) {
			$("div#set_weights").find(toToggle.toString()).toggle("fast");
		}
	}
	else {
		$("div#compare").find(toToggle.toString()).toggle("fast");
	}
}

$(function() {
	
	$("#category_select").live('change',{mode: "select"},set_weights_div);
	$("input.countable-include-checkbox").live('change',toggleCountableAttribute);
	$("input.distinct-include-checkbox").live('change',toggleDistinctAttribute);
	$("h1.tabHeader").live('click',toggleHeader);

	comparison_type = $('input:radio[name=comparison_type]:checked').val();
	
	available_categories_count = $("select#category_select option").length - 1;


	$('input:radio[name=comparison_type]').live('change',radio_comparison_type_change);
	
	$(".comparison-button-panel button.proceed-to-comparison").bind("click",{cat_id: choice_cat_id },proceed_to_comparison);

});
