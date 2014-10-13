/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: Gui Part for page bug report  (javascript)                 **|
|                                                                           **|   
\*****************************************************************************/




$(function() {
	$("#bugReport button").click(function(){
		$.post("bugReportMailer.php", { report: $("#bugReport textarea").val() },function(xml){
			if(xml.length > 0){
				if(xml == "success") alert("your report has been succesfully submitted");
				else alert("report submission failure");
			}
			else{
				alert("oops , an unexpected error occured, sorry.");
			}
		});

	});
	
	


});
