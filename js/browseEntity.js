
/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: Gui Part for page browse entity  (javascript)              **|
|                                                                           **|   
\*****************************************************************************/



var entityInfoHeight = "26px";
var browse_entity_in_comparison = false;

function navigatePreviousEntity(){
	var previous_id = "";
	$.post("logic_includes/logic.php", { call: "entity_cList" , cat_id: browseEntity_currentCategoryId , entid: browseEntity_currentEntityId ,mode: "previous" },function(xml){
		if(xml.length > 0){	
			//console.log(xml);
			previous_id = xml;
			window.location.replace( location.pathname + "?cat_id=" +  browseEntity_currentCategoryId + "&ent_id=" + previous_id);				
		}
		else{
			/*console.log("oops , empty response.sorry.");	*/
		}
	});
}
function navigateNextEntity(){
	
	var next_id = "";
	$.post("logic_includes/logic.php", { call: "entity_cList" , cat_id: browseEntity_currentCategoryId , entid: browseEntity_currentEntityId ,mode: "next" },function(xml){
		if(xml.length > 0){	
			//console.log(xml);
			next_id = xml;
			window.location.replace( location.pathname + "?cat_id=" +  browseEntity_currentCategoryId + "&ent_id=" + next_id);				
		}
		else{
			/*console.log("oops , empty response.sorry.");*/
		}
	});
}

function toggleTab() {
	var toToggle = $(this).attr("name");
		

	if( toToggle == "entityInfoInner" ){
		if(entityInfoHeight == "26px"){
			$("#browseEntity div#entityInfo").css("height",entityInfoHeight);
			$("#browseEntity div#"+toToggle).toggle("slow");
		}
		else {
			$("#browseEntity div#"+toToggle).toggle("slow");
			$("#browseEntity div#entityInfo").css("height",entityInfoHeight);
		}
		if(entityInfoHeight == "150px"){
			entityInfoHeight = "26px";
		}
		else {
			entityInfoHeight = "150px";
		}
			
	}
	else {
		$("#browseEntity div#"+toToggle).toggle("slow");
	}
	
}

function rate_entity(e){
	/*console.log("rate",e);*/
}

function deleteEntity() {
	/*console.log("delete-entity");*/
	if(confirm("Are you sure you want to delete this entity?")) {
		
		$.post("logic_includes/logic.php", { call: "delete_entity" , cat_id: browseEntity_currentCategoryId , ent_id: browseEntity_currentEntityId },function(xml){
			if(xml.length > 0){	
				/*console.log(xml);*/
				/*console.log("yes");*/
				navigateNextEntity();				
			}
			else{
				/*console.log("oops , empty response.sorry.");*/
			}
		});
	}
	else {
		 /*console.log("no");*/
	}
}

function addToComparison() {
	/*console.log("add to comparison");*/
	$.post("logic_includes/logic.php", { call: "addtocomp" , cat_id: browseEntity_currentCategoryId , ent_id: browseEntity_currentEntityId , mode: "0" },function(xml){
		if(xml.length > 0) {	
			/*console.log(xml);*/
		}
		else {
			/*console.log("oops , empty response.sorry.");*/
		}
		
		$("button.toCompare span").text("Remove from comparison").attr("name","remove");
		//browse_entity_in_comparison = true;
	});


}

function removeFromComparison() {
	/*console.log("remove from comparison");*/
	$.post("logic_includes/logic.php", { call: "addtocomp" , cat_id: browseEntity_currentCategoryId , ent_id: browseEntity_currentEntityId , mode: "1" },function(xml){
		if(xml.length > 0) {	
			/*console.log(xml);*/
		}
		else {
			/*console.log("oops , empty response.sorry.");*/
		}
		$("button.toCompare span").text("Add to comparison").attr("name","add");
		//browse_entity_in_comparison = false;
	});

}

function toCompare() {
	//console.log($(this).find("span").attr("name"));
	if ($(this).find("span").attr("name") == "add") {
		addToComparison();
		
	}
	else {
		removeFromComparison();
		
	}
}

function check_if_in_comparison(in_comparison) {
	//console.log("check if in comparison");
	$.post("logic_includes/logic.php", { call: "addtocomp" , cat_id: browseEntity_currentCategoryId , ent_id: browseEntity_currentEntityId , mode: "3" },function(xml){
		if(xml.length > 0) {	
			//console.log(xml);
			if(parse_status(xml) == 0) {
				$("button.toCompare span").text("Remove from comparison");
				browse_entity_in_comparison = true;
			}
		}
		else {
			//console.log("oops , empty response.sorry.");	
		}
	});
}

function display_imageBox() {
	//console.log("imageBox");
	var src = $(this).attr("src");
	$("div.imageBox").toggle("slow",function(){
		$("div.imageBox").html("<span class=\"close\"></span><img src=\""+src+"\" alt=\"imageBox-image\"><p>"+entity.entity_name+"</p>");
	

	});
	
	//console.log("<div class=\"imageBox\"><img src=\""+$(this).attr("src")+"\" alt=\"imageBox-image\"></div>");

}

function close_imageBox() {
	$("div.imageBox").hide("slow");
}

$(function() {
	//console.log(entity);
	$("#browseEntity button.browse-entity-previous").bind("click",navigatePreviousEntity);
	$("#browseEntity button.browse-entity-next").bind("click",navigateNextEntity);
	$("#browseEntity button.toCompare").bind("click",toCompare);
	$("#browseEntity button#delete-entity").bind("click",deleteEntity);
	$("#browseEntity h1.tabHeader").bind("click",toggleTab);
	$("div.mainOuterRate").live("mouseover",rate_entity);
	$("div#basicInfoWrapper img.image").live('click',display_imageBox);
	$("span.close").live('click',close_imageBox);
	//check_if_in_comparison(browse_entity_in_comparison);
});
