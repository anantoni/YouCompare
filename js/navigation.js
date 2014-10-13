




/*function ajaxContentManagement(content){
	
	alert(content);
	$.post("navigation.php", {call: "register"}, function(data){
				if(data.length >0) {
					$('#mainContent').html("");
					$('#mainContent').html(data);
					
					
				}
				else{
					alert("no data to show");	
				}
	});
	
	
}*/





	
	
$(function() {
	
	$("#searchSubmitC").click(function(){
		var searchQuery = $("#searchBar #searchInput").val();
		$.post("logic.php", { call: "search" , sq: searchQuery },function(xml){
			if(xml.length > 0){
				
				//alert(xml);
				var status = parse_status(xml);
				
				if( status == 0){
					
					var searchResultsV = searchResultsFormatting(xml);
					$("#mainContainer").html("");
					$("#mainContainer").html(searchResultsV);
					
					/*Super Komparsiliki!*/
					/*Edw exw parei th ul me ta searchResults tha valw click handler gia kathe apotelesma*/
					$("#mainContainer #searchResultsV .searchResult").click(function(){
						
						var clickedCategoryId = $(this).find(".id").html();
						//alert("Debug: you selected category: " + clickedCategoryId);
						
						/*we fetched the clicked category's id so it's time to make an api call to visit category*/
						
						/*visit category api call*/
						$.post("logic.php", { call: "visitCategory" , cat_id: clickedCategoryId },function(xml){
							
							if(xml.length >0 ){
								
								var status = parse_status(xml);
								
								if(status == 0){
									//alert("visit call ok");
									var visitCategoryV = visitCategoryFormatting(xml);
									//alert("Formatter output" + visitCategoryV);
									/*if the formatting output is ok ,we insert the output to the mainContainer*/
									$("#mainContainer").html("");
									$("#mainContainer").html(visitCategoryV);
									
									/*we add the controller for the custom comparison button*/
									
									$("#mainContainer").append("<button class='customComparisonC'><span>custom comparison</span></button>");
									$("#mainContainer button").click(function(){
										$.post("logic.php", { call: "get_comparable_attributes" , cat_id: category_id},function(xml){
											if(xml.length >0 ){
												//alert("i called for category_id " + category_id);
												var status = parse_status(xml);
												if( status == 0 ){
													var show_comparables = show_comparables_formatter(xml);
													$("#mainContainer").append(show_comparables);
													$("#mainContainer").append("<button id='getScoresC'><span>get scores</span></button>");
													$("#mainContainer #getScoresC").click(function(){
														
														
														
													});
												}
												else{
													alert("possibly malformed response from get_comparable_attributes");	
												}
											}
											else{
												
												alert("empty response from logic, get_comparable_attributes");	
												parse_errors(xml);
											}
											
											
											
										});
										
									});
									
									
								}
								else{
									alert("possibly malformed request to visit category");
								}
								
								
								
							}
							else{
								
								alert("our request to visit category came back empty");
							}
							
						});
						/*end of visit category api call*/
						
					});
					
				}
				else{
					alert("possibly malformed request to search");
				}
				
			}
			else{
				alert("our request to search came back empty");
			}
			
		});
		
		
	});
	
	
	$("#registerC").click(function(){
		//ajaxContentManagement("register");
		$.post("navigation.php", {call: "register"}, function(data){
				if(data.length >0) {
					$('#mainContainer').html("");
					$('#mainContainer').html(data);
					$("#registerSubmitC").click(function(){
						
					});
					
				}
				else{
					alert("no data to show");	
				}
		});
	});
	
	
	$("#create_categoryC").click(function(){
		//ajaxContentManagement("register");
		$.post("navigation.php", {call: "create_category"}, function(data){
				if(data.length >0) {
					$('#mainContainer').html("");
					$('#mainContainer').html(data);
					$('#mainContainer #createCategory fieldset table td button.addAttributesC').click(function(){
						$(".slider-range").slider({
							range: true,
							min: 0,
							max: 100,
							values: [ 25, 55 ],
							slide: function( event, ui ) {
								$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
							}
						});
						$( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) + " - $" + $( "#slider-range" ).slider( "values", 1 ) );
						//$("#createCategory fieldset table").append("<tr><td><input type='text' value='attribute name'></td><td><div class='slider-range'></td></tr>");
						$("#createCategory fieldset").append("<div class='slider-range'></div><p><label for='amount'>Price range:</label><input type='text' id='amount' style='border:0; color:#f6931f; font-weight:bold;' /></p>");
					});
					
					
				}
				else{
					alert("no data to show");	
				}
		});
	});
	
	
});
	
	
	
