/*****************************************************************************\
|   Author  : Zorbas Dimitrios  1115 2007 00078                             **|
|   contact : zorbash@hotmail.com                                           **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: generic xml parser (mostly-deprecated)                     **|
|   									    **|                        
|   			                                                    **|   
\*****************************************************************************/


/* we do some basic parsing/formating to print some output.*/ 


function parse_status(xml){
	var status=null;
	$(xml).find("status").each(function(){
			status = $(this).text();
			
	});
	if(status == "success" || status == "SUCCESS" ){
		return 0;
	}
	else return 1;
}

function parse_errors(xml){
	var index = 0;
	$(xml).find("error").each(function(){
		errorArray[index++] = $(this).text();
	});
}


function searchResultsFormatting(xml){
	var searchResultsV = "<div id='searchResultsV'><h3>Search Results</h3><ul>";
	var ul = "";
	
	
	
	/*we could integrate the following part to a category parser*/
	$(xml).find("category").each(function(){
		
		var id = $(this).find("id").text();
		
		
		var name = $(this).find("name").text();
		var popularity = $(this).find("popularity").text();
		var number_of_prods = $(this).find("number_of_prods").text();
		var description = $(this).find("description").text();
		var image = $(this).find("image").text();
		
		var li="";
		
		li	= "<li class='searchResult' " + "id=searchResultId"+ id +"><img src=" + "http://www.nokia-tuning.net/phones/6120Classic.gif" + " alt='image'/><span>id:</span><span class='id'>" +id+ "</span><span>category name" +name+ "</span><span>Popularity: " +popularity+ "</span><span>Description: " +description+ "</span></li>";
		ul += li;
		//alert(ul);
		//alert($("#searchResultsV ul").val());
		//$("#searchResultsV ul").append($("li .resultObject"));
		
	});
	//alert("foo bar lala");
	searchResultsV += ul;
	searchResultsV += "<ul></div>";
	//alert(searchResultV);
	return searchResultsV;
	
}



function visitCategoryFormatting(xml){
	
	/*to gather all variable declarations here*/
	var num_attributes ="";
	
	
	/*we parse and format the basic info div*/
	var visitCategoryV = "<div id='visitCategoryV'><div id='visitCategoryInfo'><ul>";
	var ul = "";
	$(xml).find("category").each(function(){
		
		var id = $(this).find("id").text();
		category_id = id;
		//alert(category_id);
		var name = $(this).find("name").text();
		var popularity = $(this).find("popularity").text();
		
		var description = $(this).find("description").text();
		var image = $(this).find("image").text();
		
		var li="";
		
		li	= "<li class='searchResult'><img src=" + "http://www.nokia-tuning.net/phones/6120Classic.gif" + " alt='image'/><span>id:</span><span class='id'>" +id+ "</span><span>category name" +name+ "</span></li><li><span>Popularity: " +popularity+ "</span></li><li><span>Description: " +description+ "</span></li>";
		ul += li;
		
		//alert($("#searchResultsV ul").val());
		//$("#searchResultsV ul").append($("li .resultObject"));
		
	});
	
	visitCategoryV += ul;
	visitCategoryV += "<ul><iframe title='YouTube video player' width='320' height='270' src='http://www.youtube.com/embed/2Z4m4lnjxkY' frameborder='0' allowfullscreen></iframe>";
	visitCategoryV += "</div></div>";
	/*we add a youtube iframe*/
	/*parsing and formating for the basic info div and complete */
	
	
	
	
	
	/*here we parse and format the attributes of each category*/
	
	var visitCategoryAttributes = "<div id='visitCategoryAttributes'><ul>";
	var ul="";
	
	/*we fetch the number of attributes*/
	num_attributes = $(this).find("num_attributes");
	
	$(xml).find("attribute").each(function(){
		
		
		var id = $(this).find("id").text();
		var name = $(this).find("name").text();
		var description = $(this).find("description").text();
		
		
		var li = "";
		
		li = "<li><span>name " + name + "</span><span>description" + description + "</span></li>";
		ul += li;
		
		
		//alert($(this).html());
	
	});
	visitCategoryV += ul;
	visitCategoryV += "<ul></div>";
	/*ending visitCategoryAttributes*/
	
	
	
	//alert(visitCategoryV);
	/*
	
	/*here we parse and format the entities of each category*/
	
	/*to create the table we have to name correctly the cells of the table*/
	
	var visitCategoryEntities = "<div id='visitCategoryEntities'><table>";
	var tr="";
	tr += "<tr><td>image</td><td>name</td><td>description</td>";
	/*here we do fetch the names of the attributes , to fix the table*/
	
	$(xml).find("attribute").each(function(){
		tr += "<td>" + $(this).find("name").text() + "</td>";
		
	});
	
	/*we close the header row*/
	tr += "</tr>";
	//alert("Header Row " + tr);
	
	
	/*we fill the table with contents*/
	$(xml).find("entity").each(function(){
		
		
		
		
		var name = $(this).find("name").text();
		var description = $(this).find("description").text();
		var image = $(this).find("image").text();
		
		
		tr += "<tr><td><img src=" + "http://www.nokia-tuning.net/phones/6120Classic.gif" + " alt='image'/>" + "</td><td>" + name + "</td><td>" +description + "</td>";
		
		
		$(this).find("entity_attribute").each(function(){
			tr += "<td>" + $(this).find("attr_value").text() +"</td>";
			//alert(tr);
		});
		
		/*closing up the row of each entity*/
		
		tr += "</tr>";
		//alert("tr of each entity" + tr);
	});
	 
	
	/*we accumulate to the table element*/
	visitCategoryEntities +=  tr;
	visitCategoryEntities += "</table>";
	//alert(visitCategoryEntities);
	
	/*we also add a button for the custom comparison*/
	visitCategoryEntities += "</div>";
	
	
	return visitCategoryV + visitCategoryEntities;
	
	//alert(searchResultV);
	
	
}



/*get_comparable_attributes parser*/
function show_comparables_formatter(xml){
	
	var num_comp_attrs = "";
	var attribute; 
	
	$(xml).find("num_comp_attrs").each(function(){
		num_comp_attrs = $(this).text();
		attribute = new Array(num_comp_attrs);
		for (i=0; i < num_comp_attrs; i++) attribute[i]=new Array(3);
	});
	
	var index=0;
	$(xml).find("attribute").each(function(){
		attribute[index][0] = $(this).find("id").text();
		attribute[index][1] = $(this).find("name").text();
		attribute[index][2] = $(this).find("type").text();
		index++;
	});
	
	
	var table = "";
	table += "<table>";
		
	var tr = "";	
	for( i=0; i < num_comp_attrs; i++ ){
		tr += "<tr>";
		tr += "<td>" + attribute[i][1] + "</td>";
		tr += "<td>" + attribute[i][2] + "</td>";
		if( attribute[i][2] == "COUNTABLE"){
			tr += "<td>" + "<input type='text' value='weight' />" + "</td>";
		}
		else{
			tr += "<td><select>";
			index=0;
			$(xml).find("attribute").each(function(){
				if($(this).find("id").text() == attribute[i][0]){
					
					$(this).find("value").each(function(){
						tr += "<option>" + $(this).text() + "</option>";
					});
					
				}
				index++;
			});
			tr += "</select></td>";
			tr += "<td>" + "<input type='text' value='weight' />" + "</td>";
		}
		
		tr += "</tr>";
		
	}
	table += tr + "</table>";
	return table;
	
	
	
}

