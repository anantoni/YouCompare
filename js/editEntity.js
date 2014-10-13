/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: Gui Part for page edit entity  (javascript) (deprecated)   **|
|                                                                           **|   
\*****************************************************************************/



function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
	});
	return vars;
}

var editEntity_cat_id;

var editEntity_ent_id;

var entity_name;
var entity_desc;
var entity_img;
var entity_vid;
var attributes = {};
var object = new Object();

var i=0;

function submiteditEntity(){
	i = 0;
	editEntity_cat_id = getUrlVars()["cat_id"];
	editEntity_ent_id = getUrlVars()["ent_id"];
	editEntity_dialog_toggle = 0;
	entity_name = $("#dialog-form input[name=name]").val();
	entity_desc = $("#dialog-form textarea[name=desc]").val();
	entity_img = $("#dialog-form input[name=img]").val();
	entity_vid = $("#dialog-form input[name=vid]").val();
	$("#dialog-form input[name=attr\\[\\]]").each(function() {
		attributes[i++] = $(this).val();
	});
	$("#dialog-form select[name=attr\\[\\]]").each(function() {
		attributes[i++] = $(this).val();
  	});
	object = attributes;
	console.log(editEntity_cat_id.cat_id, editEntity_ent_id.ent_id , entity_name,entity_desc,entity_img,entity_vid,attributes,object);
	$.post("logic.php", { call: "edititem" , cat_id: editEntity_cat_id , ent_id: editEntity_ent_id ,name: entity_name , desc: entity_desc , vid: entity_vid , attr: object },function(xml){
			if(xml.length > 0){
				//alert(xml);
				console.log(xml);
				alert(xml);
				if(xml == "Entity edited successfully!") {
					$( "#dialog-form" ).dialog( "close" );
					window.location.reload();
				}
			}
			else{
				console.log("empty xml");
			}
			
	});	
}

var editEntity_dialog_contents = "";
var editEntity_dialog_toggle = 0;

$(function() {
	console.log("edit , i r here!");
	var id = getUrlVars()["cat_id"];
	var entid = getUrlVars()["ent_id"];

	
	$( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 800,
			width: 800,
			dialogClass: 'editEntityDialog',
			title: 'Edit entity',
			show: 'scale',
			hide: 'blind',
			modal: true,
			buttons: {
				"submit edit": function() {
					submiteditEntity();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				},
				"Reset fields": function() {
					$("#dialog-form input[name=name]").val("");
					$("#dialog-form textarea[name=desc]").val("");
					$("#dialog-form input[name=img]").val("");
					$("#dialog-form input[name=vid]").val("");
					$("#dialog-form input[name=attr\\[\\]]").each(function() {
						$(this).val("");
					});
					$("#dialog-form select[name=attr\\[\\]]").each(function() {
						$(this).val("");
  					});
				}
			}	
		});
	$( "#edit-entity" )
			.button()
			.click(function() {
				console.log("#edit-entity clicked!");
				if(editEntity_dialog_toggle == 0){
					$.post("logic.php", { call: "get_attrs_edit" , cat_id: id , ent_id: entid},function(xml){
						if(xml.length > 0){	
							$("#dialog-form").html(xml);
							$( "#dialog-form" ).dialog( "open" );
							editEntity_dialog_toggle = 1;
						}
						else{
							alert("oops , empty response.sorry.");	
						}
					});
				}
				else{
					$( "#dialog-form" ).dialog( "open" );
				}				
			});
	

});
