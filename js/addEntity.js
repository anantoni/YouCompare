/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: javascript part for page add entity   (deprecated)         **|
|                                                                           **|   
\*****************************************************************************/



function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
	});
	return vars;
}


$(function() {
	$( "#dialog:ui-dialog" ).dialog( "destroy" );
	var id = getUrlVars()["cat_id"];
	$( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 800,
			width: 600,
			modal: true,
			buttons: {
				"submit entity": function() {
					alert("submit");
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				
			}
		});
	$( "#add-entity" )
			.button()
			.click(function() {
				$.get("get_attrs.php", { cat_id: id },function(xml){
					if(xml.length > 0){
						alert(xml);	
						$("#dialog-form").html(xml);
						$( "#dialog-form" ).dialog( "open" );			
					}
					else{
						alert("oops , empty response.sorry.");	
					}
				});	
				
			});
	

});
