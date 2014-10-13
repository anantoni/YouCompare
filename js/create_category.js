/*******************************
 Software Engineering 2011 - YouCompare Website
 Code developed by Anastasios Antoniadis
 May-June 2011
 *******************************/

var toggle = 0;
var name = "";
var description = "";
var keywords = "";
var category_type = "";
var image_path = "";
var namePattern = /[^A-Za-z0-9().?!&*%-_ ]/;
var numberPattern = /[^0-9.-]/;
var keywordsPattern = /[^A-Za-z0-9-_,]/;

/******************** Category Errors *********************/
var nameErrorFound = true;
var descriptionErrorFound = false;
var keywordsErrorFound = false;

/*************** Countable Attribute Errors ********************/
var countableAttributeNameErrorFound = true;
var countableAttributeMinValueErrorFound = true;
var countableAttributeMaxValueErrorFound = true;
var countableAttributeDescriptionErrorFound = false;

/*************** Distinct Attribute Errors ********************/
var distinctAttributeNameErrorFound = true;
var distinctAttributeValueNameErrorFound = true;
var distinctAttributeDescriptionErrorFound = false;

/*************** Uncomparable Attribute Errors ********************/
var uncomparableAttributeNameErrorFound = true;
var uncomparableAttributeDescriptionErrorFound = false;

var AttributeArray = [];
var counter = 0;
var not_removed_attributes_counter = 0;
var distinct_value_counter = 0;
var countable_mode = "add";
var distinct_mode = "add";
var uncomparable_mode = "add";
var editedAttribute = -1;
var weight_options = ""


$(document).ready(function() {
    
        $("#createCategoryButton").attr("disabled", "enabled");
        $("#saveCountableAttributeButton").attr("disabled", "enabled");
        $("#saveDistinctAttributeButton").attr("disabled", "enabled");
        $("#saveUncomparableAttributeButton").attr("disabled", "enabled");     
                
});


/*************************************************** CHECK VALIDITY FUNCTION TA GENIKA STOIXEIA THS KATHGORIAS - START ****************************************************************************/
function checkValidity(event) {
    
	/*fetching input values*/
	var className = event.target.className;
	event.stopPropagation();
        
       
        /************************************ Username Check ************************************/
	if ( className == "createCategoryName" ) {
            
                $(".createCategoryNameError").html("<img class='loading' src='images/loadingLogin9.gif'>");
		if ( $(".createCategoryName").val().length <= 3 ) { 
                        if ( $(".createCategoryName").val() != "" )
                            $(".createCategoryNameError").html("<img src='images/invalid_input.png'> Too short").show('slow');
                        else 
                            $(".createCategoryNameError").empty();
                        nameErrorFound = true;
                }
	        else if ( $(".createCategoryName").val().length > 40 ) {
                        $(".createCategoryNameError").html( "<img src='images/invalid_input.png'> Too long" ).show('slow');
                        nameErrorFound = true;
                }
                else if ( namePattern.test( $(".createCategoryName").val() ) ) {
                        $(".createCategoryNameError").html("<img src='images/invalid_input.png'> Invalid category name").show('slow');
                        nameErrorFound = true;
                }
                else {                    
			
			/*********************** AJAX CALL FOR USERNAME AVAILABILITY ***************************************************************/
			$.post("logic_includes/logic.php", { call: "check_category_name" , category_name: $(".createCategoryName").val() },function(xml) {
          
				if ( xml.length > 0 ) {
                                        var status = parse_status(xml);
					if ( status == 0 ) {
						$(".createCategoryNameError").html("<img src='images/valid_input.png'>").show('slow');
                                                nameErrorFound = false;
                                        }
					else {
						parse_errors(xml);
						for ( var error in errorArray ) {
							if ( errorArray[error] == "CATEGORY_NAME_ALREADY_EXISTS" ) {
								$(".createCategoryNameError").html("<img src='images/invalid_input.png'> Already taken").show('slow');
                                                                nameErrorFound = true;
                                                        }
							else if ( errorArray[error] == "BAD_REQUEST" ) {
								$(".createCategoryNameError").html("<img src='images/invalid_input.png'> Unknown Error").show('slow');
                                                                nameErrorFound = true;
                                                        }
                                                        else if ( errorArray[error] == "MYSQL_ERROR" ) {
								$(".createCategoryNameError").html("<img src='images/invalid_input.png'> Database Error").show('slow');
                                                                nameErrorFound = true;
                                                        }
                                                        else {
                                                                $(".createCategoryNameError").html("<img src='images/invalid_input.png'> Internal Error").show('slow');
                                                                nameErrorFound = true;
                                                        }
						}
					}
				}
				else {
					 $(".createCategoryNameError").html("AJAX Error");
                                         nameErrorFound = true;
                                }
			});
                }
		
	}
        
        /************************************ Description Check ************************************/
	else if ( className == "createCategoryDescription" ) {
                        $(".createCategoryDescriptionError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                        if ( $(".createCategoryDescription").val().length > 500 ) {
                                $(".createCategoryDescriptionError").html("<img src='images/invalid_input.png'> Too long").show('slow');
                                descriptionErrorFound = true;
                        }
                        else {
                                $(".createCategoryDescriptionError").html("<img src='images/valid_input.png'>").show('slow');
                                descriptionErrorFound = false;
                        }
                
	}
        /************************************ Keyworks Check ************************************/
	else if ( className == "createCategoryKeywords" ) {
     
                        $(".createCategoryKeywordsError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                        if ( $(".createCategoryKeywords").val().length > 500 ) {
                                $(".createCategoryKeywordsError").html("<img src='images/invalid_input.png'> Too long").show('slow');
                                keywordsErrorFound = true;
                        }
                        else if ( keywordsPattern.test( $(".createCategoryKeywords").val() ) ) {
                                $(".createCategoryKeywordsError").html("<img src='images/invalid_input.png'> Invalid keywords: use only letters, numbers and \",\" to separate them").show('slow');
                                keywordsErrorFound = true;
                        }
                        else {
                                $(".createCategoryKeywordsError").html("<img src='images/valid_input.png'>").show('slow');
                                keywordsErrorFound = false;
                        }         
                    
	}
        
        if ( !nameErrorFound && !descriptionErrorFound && !keywordsErrorFound && counter > 0 && not_removed_attributes_counter > 0 ) {
                        $("#createCategoryButton").removeAttr("disabled");              
                        $("#createCategoryButton").addClass("createCategoryButtonEnabled");
        }
        else {
                        $("#createCategoryButton").attr( "disabled", "enabled" );
                        $("#createCategoryButton").removeClass("createCategoryButtonEnabled");                     
        }
}
/*************************************************** CHECK VALIDITY FUNCTION TA GENIKA STOIXEIA THS KATHGORIAS - END ****************************************************************************/



/************************************************************* CHECK ATTRIBUTE NAME AVAILABILITY FUNCTION - START *******************************************************************/
function checkAttributeNameAvailability( name, mode ) {
                        if ( mode == "add" ) {                     
                            for ( var i = 0 ; i < AttributeArray.length ; i++ )
                                if ( AttributeArray[i][0] == name ) 
                                    return -1;

                        }
                        else {
                            for ( i = 0 ; i < AttributeArray.length ; i++ )
                                if ( i != editedAttribute )
                                    if ( AttributeArray[i][0] == name ) 
                                        return -1;
                            
                        }
                             return 0;    
}
/************************************************************* CHECK ATTRIBUTE NAME AVAILABILITY FUNCTION - END ********************************************************************/



/******************************************************** CHECK COUNTABLE ATTRIBUTE VALIDITY - START *******************************************************************************/
function checkCountableAttributeValidity(event){
	/*fetching input values*/
	var currentId = $(this).attr('id');
	event.stopPropagation();
        
       
        /************************************ Attibute Name Check ************************************/
	if ( currentId == "countableAttributeName" ) {
            
                        $(".countableAttributeNameError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                        if ( $("#countableAttributeName").val().length < 2 ) { 
                                    if ( $("#countableAttributeName").val() != "" )
                                        $(".countableAttributeNameError").html("<img src='images/invalid_input.png'> Too short").show('slow');
                                    else 
                                        $(".countableAttributeNameError").empty();
                                    countableAttributeNameErrorFound = true;
                        }
                        else if ( $("#countableAttributeName").val().length > 50 ) {
                                    $(".countableAttributeNameError").html( "<img src='images/invalid_input.png'> Too long" ).show('slow');
                                    countableAttributeNameErrorFound = true;
                        }
                        else if ( namePattern.test( $("#countableAttributeName").val() ) ) {
                                    $(".countableAttributeNameError").html("<img src='images/invalid_input.png'> Invalid category name").show('slow');
                                    countableAttributeNameErrorFound = true;
                        }
                        else {
                                        if ( checkAttributeNameAvailability( $("#countableAttributeName").val(), countable_mode ) == 0 ) {
                                            $(".countableAttributeNameError").html("<img src='images/valid_input.png'> ").show('slow');
                                            countableAttributeNameErrorFound = false;
                                        }
                                        else {
                                            $(".countableAttributeNameError").html("<img src='images/invalid_input.png'> Duplicate name found").show('slow');
                                            countableAttributeNameErrorFound = true;
                                        }
                        }
                                
	}
        
        /************************************ Attribute Description Check ************************************/
	else if ( currentId == "countableAttributeDescription" ) {
            
                        $(".countableAttributeDescriptionError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                        if ( $("#countableAttributeDescription").val().length > 500 ) {
                                    $(".countableAttributeDescriptionError").html("<img src='images/invalid_input.png'> Too long").show('slow');
                                    countableAttributeDescriptionErrorFound = true;
                        }             
                        else {
                                    $(".countableAttributeDescriptionError").html("<img src='images/valid_input.png'>").show('slow');
                                    countableAttributeDescriptionErrorFound = false;
                        }
                                                              
	}
        /************************************ Attribute Min Value Check ************************************/
        else if ( currentId == "countableAttributeMinValue" ) {
            
                        $(".countableAttributeMinValueError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                        if ( $( "#countableAttributeMinValue" ).val() == "" ) {
                                    $(".countableAttributeMinValueError").empty();
                                    countableAttributeMinValueErrorFound = true;
                        }
                        else if ( numberPattern.test( $( "#countableAttributeMinValue" ).val() ) ) {
                                    $(".countableAttributeMinValueError").html("<img src='images/invalid_input.png'> Invalid").show('slow');
                                    countableAttributeMinValueErrorFound = true;
                        }
                        else {
                                if ( $( "#countableAttributeMaxValue" ).val() != "" && !( numberPattern.test( $( "#countableAttributeMaxValue" ).val() ) ) ) {
                                    if ( parseFloat( $( "#countableAttributeMaxValue" ).val() )  <  parseFloat( $( "#countableAttributeMinValue" ).val() ) ) {
                                                $(".countableAttributeMaxValueError").html("<img src='images/invalid_input.png'> Less than min value").show('slow');
                                                countableAttributeMaxValueErrorFound = true;
                                    }
                                }
                                $(".countableAttributeMinValueError").html("<img src='images/valid_input.png'>").show('slow');
                                countableAttributeMinValueErrorFound = false;
                        }
                        
                            
        }
        /************************************ Attribute Max Value Check ************************************/
        else if ( currentId == "countableAttributeMaxValue" ) {
            
                        $(".countableAttributeMaxValueError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                        if ( $( "#countableAttributeMaxValue" ).val() == "" ) {
                                    $(".countableAttributeMaxValueError").empty();
                                    countableAttributeMaxValueErrorFound = true;
                        }
                        else if ( numberPattern.test( $( "#countableAttributeMaxValue" ).val() ) ) {
                                    $(".countableAttributeMaxValueError").html("<img src='images/invalid_input.png'> Invalid").show('slow');
                                    countableAttributeMaxValueErrorFound = true;
                        }
                        else if ( parseFloat( $( "#countableAttributeMaxValue" ).val() )  <  parseFloat( $( "#countableAttributeMinValue" ).val() ) ) {
                                    $(".countableAttributeMaxValueError").html("<img src='images/invalid_input.png'> Less than min value").show('slow');
                                    countableAttributeMaxValueErrorFound = true;
                        }
                        else {
                                    $(".countableAttributeMaxValueError").html("<img src='images/valid_input.png'>").show('slow');
                                    countableAttributeMaxValueErrorFound = false;
                        }             

        }
        if ( !countableAttributeNameErrorFound && !countableAttributeDescriptionErrorFound && !countableAttributeMinValueErrorFound && !countableAttributeMaxValueErrorFound ) {
                        $("#saveCountableAttributeButton").removeAttr("disabled");              
                        $("#saveCountableAttributeButton").addClass("createCategoryButtonEnabled");
        }
        else {
                        $("#saveCountableAttributeButton").attr( "disabled", "enabled" );
                        $("#saveCountableAttributeButton").removeClass("createCategoryButtonEnabled");                     
        }
        
}
/******************************************************************* CHECK COUNTABLE ATTRIBUTE VALIDITY - END *******************************************************************************/




/****************************************************************** CHECK DISTINCT VALUES AND WEIGHTS - START *******************************************************************************/
function checkDistinctValuesWeights() {
            

                        valueNameArray = document.getElementsByClassName( "distinctAttributeValueName");
                        if ( valueNameArray.length > 0  ) {

                                    var error_found = false;
                                    for ( var i = 0 ; i < valueNameArray.length ; i++ ) {
                                            if ( valueNameArray[i].value.length > 50 ) {
                                                $( "#distinctAttributeValueName"+j+"Error" ).html("<img src='images/invalid_input.png'> Too long").show('slow');
                                                error_found = true;
                                                break;
                                            }
                                            else if ( namePattern.test( valueNameArray[i].value ) ) {
                                                $( "#distinctAttributeValueName"+j+"Error" ).html("<img src='images/invalid_input.png'> Invalid").show('slow');
                                                error_found = true;
                                                break;
                                            }
                                            else if ( valueNameArray[i].value == "" ) {
                                                error_found = true;
                                                break;
                                            }
                                    }
                                    for (  i = 0 ; i < valueNameArray.length ; i++ ) {
                                        for ( var j = 0 ; j < valueNameArray.length ; j++ ) {
                                            if ( i != j ) {
                                                    if ( valueNameArray[i].value == valueNameArray[j].value ) {
                                                        if ( valueNameArray[i].value != "" ) {
                                                            $( "#distinctAttributeValueName"+(i+1)+"Error" ).html("<img src='images/invalid_input.png'> Duplicate found").show('slow');
                                                            $( "#distinctAttributeValueName"+(j+1)+"Error" ).html("<img src='images/invalid_input.png'> Duplicate found").show('slow');
                                                        }
                                                        error_found = true;

                                                    }
                                            }
                                        }
                                    }
                                    
                                    if ( !distinctAttributeNameErrorFound && !distinctAttributeDescriptionErrorFound && error_found == false ) {
                                        
                                        for (  i = 0 ; i < valueNameArray.length ; i++ ) 
                                            $( "#distinctAttributeValueName"+(i+1)+"Error" ).html("<img src='images/valid_input.png'>").show('slow');
                                        
                                        $("#saveDistinctAttributeButton").removeAttr("disabled");              
                                        $("#saveDistinctAttributeButton").addClass("createCategoryButtonEnabled");

                                    }
                                    else {
                                        
                                        $("#saveDistinctAttributeButton").attr( "disabled", "enabled" );
                                        $("#saveDistinctAttributeButton").removeClass("createCategoryButtonEnabled"); 
                                        
                                    }
                                    
                        }                      
}
/****************************************************************** CHECK DISTINCT VALUES AND WEIGHTS - END *******************************************************************************/




/*********************************************** CHECK DISTINCT ATTRIBUTE VAILIDITY FUNCTION - START ***********************************************************************************/
function checkDistinctAttributeValidity(event){
	/*fetching input values*/
	var currentId = $(this).attr('id');
        var className = $(this).attr('class');
       	event.stopPropagation();
              
       
        /************************************ Attribute Name Check ************************************/
	if ( currentId == "distinctAttributeName" ) {
            
                        $(".distinctAttributeNameError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                        if ( $("#distinctAttributeName").val().length < 2 ) { 
                                if ( $("#distinctAttributeName").val() != "" )
                                    $(".distinctAttributeNameError").html("<img src='images/invalid_input.png'> Too short").show('slow');
                                else 
                                    $(".distinctAttributeNameError").empty();
                                distinctAttributeNameErrorFound = true;
                        }
                        else if ( $("#distinctAttributeName").val().length > 50 ) {
                                $(".distinctAttributeNameError").html( "<img src='images/invalid_input.png'> Too long" ).show('slow');
                                distinctAttributeNameErrorFound = true;
                        }
                        else if ( namePattern.test( $("#distinctAttributeName").val() ) ) {
                                $(".distinctAttributeNameError").html("<img src='images/invalid_input.png'> Invalid Attribute name").show('slow');
                                distinctAttributeNameErrorFound = true;
                        }
                        else {
                                       if ( checkAttributeNameAvailability( $("#distinctAttributeName").val(), distinct_mode ) == 0 ) {
                                            $(".distinctAttributeNameError").html("<img src='images/valid_input.png'> ").show('slow');
                                            distinctAttributeNameErrorFound = false;
                                        }
                                        else {
                                            $(".distinctAttributeNameError").html("<img src='images/invalid_input.png'> Duplicate name found").show('slow');
                                            distinctAttributeNameErrorFound = true;
                                        }
                                
                        }
	}
        
        /************************************ Attribute Description Check ************************************/
	else if ( currentId == "distinctAttributeDescription" ) {

                        $(".distinctAttributeDescriptionError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                        if ( $("#distinctAttributeDescription").val().length > 500 ) {
                                $(".distinctAttributeDescriptionError").html("<img src='images/invalid_input.png'> Too long").show('slow');
                                distinctAttributeDescriptionErrorFound = true;
                        }
                        else {
                                $(".distinctAttributeDescriptionError").html("<img src='images/valid_input.png'>").show('slow');
                                distinctAttributeDescriptionErrorFound = false;
                        }
               
	}     
        
        if ( className == "distinctAttributeValueName" ) {
            
                        $("#"+currentId+"Error").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                        if ( $( "#"+currentId ).val() == "" ) {
                                $( "#"+currentId+"Error" ).empty();
                                distinctAttributeValueNameErrorFound = true;
                        }
                        else if ( $( "#"+currentId ).val().length > 50 ) {
                                $( "."+currentId+"Error" ).html("<img src='images/invalid_input.png'> Too long").show('slow');
                                distinctAttributeValueNameErrorFound = true;
                        }
                        else if ( namePattern.test( $("#"+currentId ).val() ) ) {
                                $( "#"+currentId+"Error" ).html("<img src='images/invalid_input.png'> Invalid").show('slow');
                                distinctAttributeValueNameErrorFound = true;
                        }
                        else {
                                $( "#"+currentId+"Error" ).html("<img src='images/valid_input.png'>").show('slow');
                                distinctAttributeValueNameErrorFound = false;
                        }
                
	}    
        checkDistinctValuesWeights();
}

/************************************************************** CHECK DISTINCT ATTRIBUTE VAILIDITY FUNCTION - END ***********************************************************************************/




/********************************************************* CHECK UNCOMPARABLE ATTRIBUTE VALIDITY FUNCTION - START ***********************************************************************************/
function checkUncomparableAttributeValidity(event){
	/*fetching input values*/
	var currentId = $(this).attr('id');
	event.stopPropagation();
        
       
        /************************************ Attribute Name Check ************************************/
	if ( currentId == "uncomparableAttributeName" ) {
            
                        $(".uncomparableAttributeNameError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                        if ( $("#uncomparableAttributeName").val().length < 2 ) { 
                                if ( $("#uncomparableAttributeName").val() != "" )
                                    $(".uncomparableAttributeNameError").html("<img src='images/invalid_input.png'> Too short").show('slow');
                                else 
                                    $(".uncomparableAttributeNameError").empty();
                                uncomparableAttributeNameErrorFound = true;
                        }
                        else if ( $("#uncomparableAttributeName").val().length > 50 ) {
                                $(".uncomparableAttributeNameError").html( "<img src='images/invalid_input.png'> Too long" ).show('slow');
                                uncomparableAttributeNameErrorFound = true;
                        }
                        else if ( namePattern.test( $("#uncomparableAttributeName").val() ) ) {
                                $(".uncomparableAttributeNameError").html("<img src='images/invalid_input.png'> Invalid Attribute name").show('slow');
                                uncomparableAttributeNameErrorFound = true;
                        }
                        else {
                                
                                   if ( checkAttributeNameAvailability( $("#uncomparableAttributeName").val(), uncomparable_mode ) == 0 ) {
                                        $(".uncomparableAttributeNameError").html("<img src='images/valid_input.png'> ").show('slow');
                                        uncomparableAttributeNameErrorFound = false;
                                    }
                                    else {
                                        $(".uncomparableAttributeNameError").html("<img src='images/invalid_input.png'> Duplicate name found").show('slow');
                                        uncomparableAttributeNameErrorFound = true;
                                    }
                           
                        }
               
	}
        
        /************************************ Attribute Description Check ************************************/
	else if ( currentId == "uncomparableAttributeDescription" ) {
            
                        $(".uncomparableAttributeDescriptionError").html("<img class='loading' src='images/loadingLogin9.gif'>").show('slow');
                        if ( $("#uncomparableAttributeDescription").val().length > 500 ) {
                                $(".uncomparableAttributeDescriptionError").html("<img src='images/invalid_input.png'> Too long").show('slow');
                                uncomparableAttributeDescriptionErrorFound = true;
                        }
                        else {
                                $(".uncomparableAttributeDescriptionError").html("<img src='images/valid_input.png'>").show('slow');
                                uncomparableAttributeDescriptionErrorFound = false;
                        }
                
	}    
        if ( !uncomparableAttributeNameErrorFound && !uncomparableAttributeDescriptionErrorFound ) {
                        
                        $("#saveUncomparableAttributeButton").removeAttr("disabled");              
                        $("#saveUncomparableAttributeButton").addClass("createCategoryButtonEnabled");
        }
        else {
                        $("#saveUncomparableAttributeButton").attr( "disabled", "enabled" );
                        $("#saveUncomparableAttributeButton").removeClass("createCategoryButtonEnabled");                     
        }  
        
}
/********************************************************** CHECK UNCOMPARABLE ATTRIBUTE VALIDITY FUNCTION - END ***********************************************************************************/




/********************************************************** FUNCTION SHOW CATEGORY GENERAL INFO - START *******************************************************************************************/
function showCategoryGeneralInfo() {
    
                        var general_info = "";

                        $('.ModalWindow').hide();
                        $('#create_category').slideUp( 'slow' ); 


                        if ( $( ".createCategoryName" ).val() != "" && !nameErrorFound ) 
                            general_info += "<span><b>Category Name:</b> " + $( ".createCategoryName" ).val() + "</span> ";

                        if ( $( ".createCategoryKeywords" ).val() != "" && !keywordsErrorFound ) 
                            general_info += "<span style=\"display: inline-block; margin-left: 20px;\"><b>Category Keywords:</b> " + $( ".createCategoryKeywords" ).val() + "</span> ";

                        if ( $( ".createCategoryType:checked").val() == 1 ) 
                            general_info += "<span style=\"display: inline-block; margin-left: 20px;\"><b>Category Type:</b> Public</span>";
                        else 
                            general_info += "<span style=\"display: inline-block; margin-left: 20px;\"><b>Category Type:</b> Private</span>";

                        $('#general_info').html( general_info );
                
}
/******************************************************* FUNCTIONS SHOW CATEGORY GENERAL INFO - END ************************************************************************************************/




/***************************************************************** FUNCTIONS AVAILABLE WHEN DOCUMENT IS LOADED - START *****************************************************************************/
$(document).ready( function() {  
    
                 for ( var option_index = 0 ; option_index < 101; option_index++ ) {
                        weight_options += "<option val=\""+option_index+"\">"+option_index+"</option>"
                }
                
               
                /*********************** TOGGLE GENERAL CATEGORY INFO ******************************/
                $(".trigger").click( function() {
                    
                        $('#create_category').slideToggle( "slow", ToggleCategoryInfoCallbackFunction );                     //Toggle Hide or Show   
                        return false;                                                                                      
                
                });
                
                /*********************** TOGGLE GENERAL CATEGORY INFO CALLBACK FUNCTION ****************************************************************/
                function ToggleCategoryInfoCallbackFunction() {
                    
                        if ( $( "#create_category" ).is( ":visible" ) ) 
                            $( "#visible_info" ).html( "[Hide]" );
                        else {
                            $( "#visible_info" ).html( "[Show]" );
                            showCategoryGeneralInfo();
                        }
                        
                }
                
                /*********************** TOGGLE ATTRIBUTE ARRAYS ***************************************************************************************/
                $("p.attribute_toggle").live( "click", function() {
                    
                        var id = $(this).attr('id');
                        
                        if ( id == "countable_toggle" )
                             $( "#CountableTable" ).slideToggle( "slow", ToggleAttributeTablesCallbackFunction );                     //Toggle Hide or Show
                        else if ( id == "distinct_toggle" )
                             $( "#DistinctTable" ).slideToggle( "slow", ToggleAttributeTablesCallbackFunction );
                        else 
                             $( "#UncomparableTable" ).slideToggle( "slow", ToggleAttributeTablesCallbackFunction );                                                 
    
                });
                
                /*********************** TOGGLE ATTRIBUTE ARRAYS CALLBACK FUNCTION ********************************************************************/
                function ToggleAttributeTablesCallbackFunction() {      
                    
                        var id = $(this).attr('id');
                 
                        if ( id == "CountableTable" )      { 
                                
                                $("#countable_visible").empty();
                                
                                if ( $( "#CountableTable" ).is(":visible") ) 
                                    $( "#countable_visible" ).html( "[Hide]" );
                                
                                else 
                                    $( "#countable_visible" ).html( "[Show]" );  
                              
                        }
                        else if ( id == "DistinctTable" ) { 
    
                                $("#distinct_visible").empty();
                                
                                if ( $( "#DistinctTable" ).is(":visible") ) 
                                    $( "#distinct_visible" ).html( "[Hide]" );

                                else                         
                                    $( "#distinct_visible" ).html( "[Show]" );      
                            
                        }
                        else {
                            
                                $("#uncomparable_visible").empty();
                                
                                if ( $( "#UncomparableTable" ).is(":visible") ) 
                                    $( "#uncomparable_visible" ).html( "[Hide]" );
                                 
                                else 
                                    $( "#uncomparable_visible" ).html( "[Show]" );
                                 
                        }
                        return false;       
                    
                }
                

                /************************ Add Countable Attribute Button click **********************/
                $('#addCountableAttributeButton').click(function(e) {
                                                                    
                        showCategoryGeneralInfo();
                        e.preventDefault();
                        
                        $("#AddCountableAttributeModalWindow input").val("");
                        $("#AddCountableAttributeModalWindow textarea").val("");
                        $("#countableFilterableYes")[0].checked = true;

                        countableAttributeNameErrorFound = true;
                        countableAttributeMinValueErrorFound = true;
                        countableAttributeMaxValueErrorFound = true;
                        countableAttributeDescriptionErrorFound = false;

                        $(".countableAttributeNameError").empty();
                        $(".countableAttributeDescriptionError").empty();
                        $(".countableAttributeMinValueError").empty();
                        $(".countableAttributeMaxValueError").empty();
                        
                        $("#saveCountableAttributeButton").attr( "disabled", "enabled" );
                        $("#saveCountableAttributeButton").removeClass("createCategoryButtonEnabled"); 

                        //transition effect     
                        $('#AddCountableAttributeModalWindow').fadeIn( 1000 );    
                        $('#AddCountableAttributeModalWindow').fadeTo( "slow", 1.0 );  
                        countable_mode = "add";
                        
                });


                /************************ Add Distinct Attribute Button click **********************/
                $('#addDistinctAttributeButton').click(function(e) {                       
                        
                        showCategoryGeneralInfo();
                        e.preventDefault();
                        
                        $("#AddDistinctAttributeModalWindow input").val("");
                        $("#AddDistinctAttributeModalWindow textarea").val("");
                        $("#distinctFilterableYes")[0].checked = true;

                        distinctAttributeNameErrorFound = true;
                        distinctAttributeValueNameErrorFound = true;
                        distinctAttributeDescriptionErrorFound = false;
                        
                        $(".distinctAttributeNameError").empty();
                        $(".distinctAttributeDescriptionError").empty();
                        $( "#Values-Weights" ).empty();
                        
                        $("#saveDistinctAttributeButton").attr( "disabled", "enabled" );
                        $("#saveDistinctAttributeButton").removeClass("createCategoryButtonEnabled"); 

                        //transition effect     
                        $('#AddDistinctAttributeModalWindow').fadeIn( 1000 );    
                        $('#AddDistinctAttributeModalWindow').fadeTo( "slow", 1.0 );
                        distinct_mode = "add";

                });
        
        
                /************************ Add Uncomparable Attribute Button click **********************/
                $('#addUncomparableAttributeButton').click(function(e) {
                                                
                        showCategoryGeneralInfo();
                        e.preventDefault();
                        
                        $("#AddUncomparableAttributeModalWindow input").val("");
                        $("#AddUncomparableAttributeModalWindow textarea").val("");

                        $("#uncomparableFilterableYes")[0].checked = true;

                        uncomparableAttributeNameErrorFound = true;
                        uncomparableAttributeDescriptionErrorFound = false;
                        
                        $(".uncomparableAttributeNameError").empty();
                        $(".uncomparableAttributeDescriptionError").empty();
                        
                        $("#saveUncomparableAttributeButton").attr( "disabled", "enabled" );
                        $("#saveUncomparableAttributeButton").removeClass("createCategoryButtonEnabled"); 

                        //transition effect     
                        $('#AddUncomparableAttributeModalWindow').fadeIn( 1000 );    
                        $('#AddUncomparableAttributeModalWindow').fadeTo( "slow", 1.0 );
                        uncomparable_mode = "add";

                });

                /*************************** Close Countable Attribute Modal Window *************************************************/
                $('#AddCountableAttributeModalWindow .closeButton').click(function (e) {
                    
                        e.preventDefault();
                        $('#AddCountableAttributeModalWindow').hide();
                        $("#AddCountableAttributeModalWindow input").val("");
                        $("#AddCountableAttributeModalWindow textarea").val("");
                        $("#countableFilterableYes")[0].checked = true;

                        countableAttributeNameErrorFound = true;
                        countableAttributeMinValueErrorFound = true;
                        countableAttributeMaxValueErrorFound = true;
                        countableAttributeDescriptionErrorFound = false;

                        $(".countableAttributeNameError").empty();
                        $(".countableAttributeDescriptionError").empty();
                        $(".countableAttributeMinValueError").empty();
                        $(".countableAttributeMaxValueError").empty();
                        
                        $("#saveCountableAttributeButton").attr( "disabled", "enabled" );
                        $("#saveCountableAttributeButton").removeClass("createCategoryButtonEnabled"); 
                          
                });     
                
                /*************************** Close Distinct Attribute Modal Window *************************************************/
                $('#AddDistinctAttributeModalWindow .closeButton').click(function (e) {
                     
                        e.preventDefault();
                        $('#AddDistinctAttributeModalWindow').hide();
                        $("#AddDistinctAttributeModalWindow input").val("");
                        $("#AddDistinctAttributeModalWindow textarea").val("");
                        $("#distinctFilterableYes")[0].checked = true;

                        distinctAttributeNameErrorFound = true;
                        distinctAttributeValueNameErrorFound = true;
                        distinctAttributeDescriptionErrorFound = false;
                        
                        $(".distinctAttributeNameError").empty();
                        $(".distinctAttributeDescriptionError").empty();
                        $( "#Values-Weights" ).empty();
                        
                        $("#saveDistinctAttributeButton").attr( "disabled", "enabled" );
                        $("#saveDistinctAttributeButton").removeClass("createCategoryButtonEnabled"); 
                                           
                });     
                
                /*************************** Close Uncomparable Attribute Modal Window *********************************************/
                $('#AddUncomparableAttributeModalWindow .closeButton').click(function (e) {
                     
                        e.preventDefault();
                        $('#AddUncomparableAttributeModalWindow').hide();
                        $("#AddUncomparableAttributeModalWindow input").val("");
                        $("#AddUncomparableAttributeModalWindow textarea").val("");

                        $("#uncomparableFilterableYes")[0].checked = true;

                        uncomparableAttributeNameErrorFound = true;
                        uncomparableAttributeDescriptionErrorFound = false;
                        
                        $(".uncomparableAttributeNameError").empty();
                        $(".uncomparableAttributeDescriptionError").empty();
                        
                        $("#saveUncomparableAttributeButton").attr( "disabled", "enabled" );
                        $("#saveUncomparableAttributeButton").removeClass("createCategoryButtonEnabled"); 
               
                });     
     
});
/***************************************************************** FUNCTIONS AVAILABLE WHEN DOCUMENT IS LOADED - END *****************************************************************************/



/****************************************************** GET ATTRIBUTES FROM CATEGORY TEMPLATE ******************************************************************************************/
$(function() {
    
         $( "#getCategoryTemplateButton" ).bind ( "click", function() {

                        $.post( "logic_includes/logic.php", { call: "get_category_template" , name: $("#getCategoryTemplateName").val() }, function(xml) {

                                if ( xml.length > 0 ) {
                                       
                                        var status = parse_status(xml);
                                        
					if ( status == 0 ) {
                                            
                                                    $("#CountableAttributes").css( "display", "block");
                                                    $(xml).find ("Countable").each( function()  {

                                                        $(this).find( "attribute" ).each( function() {

                                                            if ( checkAttributeNameAvailability( jQuery.trim( $(this).find("name").text() ), "add" ) == 0 ) {
                                                                
                                                                    AttributeArray[counter] = new Array(7);
                                                                    AttributeArray[counter][0] = jQuery.trim( $(this).find("name").text() );
                                                                    AttributeArray[counter][1] = jQuery.trim( $(this).find("description").text() );
                                                                    AttributeArray[counter][2] = 2;
                                                                    AttributeArray[counter][3] = jQuery.trim( $(this).find("lower").text() );
                                                                    AttributeArray[counter][4] = jQuery.trim( $(this).find("upper").text() );
                                                                    AttributeArray[counter][6] = jQuery.trim( $(this).find("filterability").text() );

                                                                    if ( AttributeArray[counter][6] == 1 )
                                                                        is_filterable = "Yes";
                                                                    else 
                                                                        is_filterable = "No";

                                                                    if ( jQuery.trim( $(this).find("optimal").text() ) == 0 )
                                                                         AttributeArray[counter][5] = "max";
                                                                    else if ( jQuery.trim( $(this).find("optimal").text() ) == 1 )
                                                                         AttributeArray[counter][5] = "min";
                                                                    else if ( jQuery.trim( $(this).find("optimal").text() ) == 2 )
                                                                         AttributeArray[counter][5] = "average";

                                                                    $("#CountableTable").append( "<tr class=\"alt\" id=\"attribute"+counter+"\"><th scope=\"row\">" + AttributeArray[counter][0] + "</th> <td> " + AttributeArray[counter][1] + " </td><td> " + AttributeArray[counter][3] + " </td><td> " +AttributeArray[counter][4] + " </td><td> " + AttributeArray[counter][5] + " </td><td> " + is_filterable + "</td><td> <span id=\"editAttribute"+counter+"\" class=\"editAttribute\">Edit</span></td><td><span id=\"removeAttribute"+counter+"\" class=\"removeAttribute\"><img class=\"remove_image\" src=\"images/close.png\">Remove</span></td></tr>" );    
                                                                    counter++;
                                                                    not_removed_attributes_counter++;
                                                            }
                                                        })

                                                    }); 

                                                    $("#DistinctAttributes").css( "display", "block");
                                                    $(xml).find( "Distinct" ).each( function() {

                                                    $(this).find( "attribute" ).each( function() {
                                                        
                                                            if ( checkAttributeNameAvailability( jQuery.trim( $(this).find("name").text() ), "add" ) == 0 ) {

                                                                    AttributeArray[counter] = new Array(6);
                                                                    AttributeArray[counter][0] = jQuery.trim( $(this).find("name").text() );
                                                                    AttributeArray[counter][1] = jQuery.trim( $(this).find("description").text() );
                                                                    AttributeArray[counter][2] = 1;
                                                                    AttributeArray[counter][3] = jQuery.trim( $(this).find("values").text() );
                                                                    AttributeArray[counter][4] = jQuery.trim( $(this).find("weights").text() );
                                                                    AttributeArray[counter][5] = jQuery.trim( $(this).find("filterability").text() );

                                                                    if ( AttributeArray[counter][5] == 1 )
                                                                        is_filterable = "Yes";
                                                                    else 
                                                                        is_filterable = "No";

                                                                     
                                                                    var valueParsing = AttributeArray[counter][3].split( "," );
                                                                    var weightParsing = AttributeArray[counter][4].split( "," );

                                                                    var displayString = "";
                                                                    displayString += valueParsing[0]+"=>"+weightParsing[0];
                                                                    for ( i = 1 ; i < valueParsing.length ; i++ )
                                                                        displayString += ","+valueParsing[i]+"=>"+weightParsing[i];

                                                                    $("#DistinctTable").append( "<tr class=\"alt\" id=\"attribute"+counter+"\"><th scope=\"row\">" + AttributeArray[counter][0] + "</th> <td> " + AttributeArray[counter][1] + " </td><td> " + displayString + " </td><td> " + is_filterable + "</td><td> <span id=\"editAttribute"+counter+"\" class=\"editAttribute\">Edit</span></td><td><span id=\"removeAttribute"+counter+"\" class=\"removeAttribute\"><img class=\"remove_image\" src=\"images/close.png\">Remove</span></td></tr>" );    
                                                                    counter++;
                                                                    not_removed_attributes_counter++;
                                                            }

                                                        })

                                                    });

                                                    $("#UncomparableAttributes").css( "display", "block");
                                                    $(xml).find( "Uncomparable" ).each( function() {

                                                        $(this).find( "attribute" ).each( function() {
                                                            
                                                            if ( checkAttributeNameAvailability( jQuery.trim( $(this).find("name").text() ), "add" ) == 0 ) {
                                                                
                                                                    AttributeArray[counter] = new Array(4);
                                                                    AttributeArray[counter][0] = jQuery.trim( $(this).find("name").text() );
                                                                    AttributeArray[counter][1] = jQuery.trim( $(this).find("description").text() );
                                                                    AttributeArray[counter][2] = 0;
                                                                    AttributeArray[counter][3] = jQuery.trim( $(this).find("filterability").text() );

                                                                    if ( AttributeArray[counter][3] == 1 )
                                                                        is_filterable = "Yes";
                                                                    else 
                                                                        is_filterable = "No";

                                                                        $("#UncomparableTable").append( "<tr class=\"alt\" id=\"attribute"+counter+"\"><th scope=\"row\">" + AttributeArray[counter][0] + "</th> <td> " + AttributeArray[counter][1] + " </td><td> " + is_filterable + "</td><td> <span id=\"editAttribute"+counter+"\" class=\"editAttribute\">Edit</span></td><td><span id=\"removeAttribute"+counter+"\" class=\"removeAttribute\"><img class=\"remove_image\" src=\"images/close.png\">Remove</span></td></tr>" );
                                                                        counter++;
                                                                        not_removed_attributes_counter++;
                                                            }

                                                        })

                                                    });
                                                     $( "#categoryTemplateError" ).html( "<span style=\"color: green; font-weight: bold;\">Category template loaded successfully</span>" );
						
                                        }
					else {
						parse_errors(xml);
                                                for ( var error in errorArray ){
                                                        if( errorArray[error] == "CATEGORY_NOT_EXISTS" ) 
                                                                $( "#categoryTemplateError" ).html( "<span style=\"color: red; font-weight: bold;\">The category you selected does not exist</span>");
                                                        
                                                        else if( errorArray[error] == "CATEGORY_CLOSED" ) 
                                                                $( "#categoryTemplateError" ).html( "<span style=\"color: red; font-weight: bold;\">You are not allowed to use this category as template</span>");
                                                        
                                                        else if( errorArray[error] == "MYSQL_ERROR" ) 
                                                                $( "#categoryTemplateError" ).html( "<span style=\"color: red; font-weight: bold;\">Database error</span>" );
                                                        
                                                        else if( errorArray[error] == "BAD_REQUEST" ) 
                                                                $( "#categoryTemplateError" ).html( "<span style=\"color: red; font-weight: bold;\">Internal Error</span>" );
                                                            
                                                        else if ( errorArray[error] == "NOT_ENOUGH_PRIVILEGES")
                                                                $( "#categoryTemplateError" ).html( "<span style=\"color: red; font-weight: bold;\">You are not allowed to use this category as template</span>" );
                                                        
                                                }
						
					}
				}
				else {
					 $( "#categoryTemplateError" ).html( "AJAX Error" )
                                         nameErrorFound = true;
                                }
			});
        
                })

        
	
        /***************************************************** CLICK BIND GIA ADD COUNTABLE ATTRIBUTE - START **********************************************************************/
        $("#saveCountableAttributeButton").bind( "click", function() {
                    var is_filterable = "";

                    if ( countable_mode == "add" ) {

                                AttributeArray[counter] = new Array(7);
                                AttributeArray[counter][0] = $( "#countableAttributeName" ).val();
                                AttributeArray[counter][1] = $( "#countableAttributeDescription" ).val();
                                AttributeArray[counter][2] = 2;
                                AttributeArray[counter][3] = $( "#countableAttributeMinValue" ).val();
                                AttributeArray[counter][4] = $( "#countableAttributeMaxValue" ).val();
                                AttributeArray[counter][5] = $( "#countableAttributeComparisonType" ).val();
                                AttributeArray[counter][6] = $("input:radio[@name='countablefilterable']:checked").val();
                                
                                if ( $("#countableFilterableYes").is(":checked" )  )
                                    AttributeArray[counter][6] = 1;
                                
                                else if ( $("#countableFilterableNo").is(":checked" )  )
                                    AttributeArray[counter][6] = 0;
                                

                                if ( AttributeArray[counter][6] == 1 )
                                    is_filterable = "Yes";
                                else 
                                    is_filterable = "No";
                                
                                $("#CountableAttributes").css( "display", "block");
                                $("#CountableTable").append( "<tr class=\"alt\" id=\"attribute"+counter+"\"><th scope=\"row\">" + AttributeArray[counter][0] + "</th> <td> " + AttributeArray[counter][1] + " </td><td> " + AttributeArray[counter][3] + " </td><td> " +AttributeArray[counter][4] + " </td><td> " + AttributeArray[counter][5] + " </td><td> " + is_filterable + "</td><td> <span id=\"editAttribute"+counter+"\" class=\"editAttribute\">Edit</span></td><td><span id=\"removeAttribute"+counter+"\" class=\"removeAttribute\"><img class=\"remove_image\" src=\"images/close.png\">Remove</span></td></tr>" );    
                                
                                counter++;
                                not_removed_attributes_counter++;

                        }
                        else {

                                AttributeArray[editedAttribute][0] = $( "#countableAttributeName" ).val();
                                AttributeArray[editedAttribute][1] = $( "#countableAttributeDescription" ).val();
                                AttributeArray[editedAttribute][2] = 2;
                                AttributeArray[editedAttribute][3] = $( "#countableAttributeMinValue" ).val();
                                AttributeArray[editedAttribute][4] = $( "#countableAttributeMaxValue" ).val();
                                AttributeArray[editedAttribute][5] = $( "#countableAttributeComparisonType" ).val();
                                AttributeArray[editedAttribute][6] = $("input:radio[@name='countablefilterable']:checked").val();
                                
                                if ( $("#countableFilterableYes").is(":checked" )  )
                                    AttributeArray[editedAttribute][6] = 1;
                                
                                else if ( $("#countableFilterableNo").is(":checked" )  )
                                    AttributeArray[editedAttribute][6] = 0;
                                

                                if ( AttributeArray[editedAttribute][6] == 1 )
                                    is_filterable = "Yes";
                                else 
                                    is_filterable = "No";
                                
                                $("#attribute"+editedAttribute).empty();
                                $("#attribute"+editedAttribute).html( "<th scope=\"row\">" + AttributeArray[editedAttribute][0] + " </th><td> " + AttributeArray[editedAttribute][1] + " </td><td> " + AttributeArray[editedAttribute][3] + " </td><td> " +AttributeArray[editedAttribute][4] + " </td><td> " + AttributeArray[editedAttribute][5] + " </td><td> " + is_filterable + "</td><td> <span id=\"editAttribute"+editedAttribute+"\" class=\"editAttribute\">Edit</span></td><td><span id=\"removeAttribute"+editedAttribute+"\" class=\"removeAttribute\"><img class=\"remove_image\" src=\"images/close.png\">Remove</span></td>" )

                        }

                        editedAttribute = -1;                     
                        countable_mode = "add";   
                        $( "#countableAttributeName" ).val("");
                        $( "#countableAttributeDescription" ).val("");
                        $( "#countableAttributeMinValue" ).val("");
                        $( "#countableAttributeMaxValue" ).val("");
                        $( "#countableAttributeComparisonType" ).val("");                            
                        $( "#AddCountableAttributeModalWindow" ).hide();

                        if ( !nameErrorFound && !descriptionErrorFound && !keywordsErrorFound && counter > 0 && not_removed_attributes_counter > 0 ) {
                                $("#createCategoryButton").removeAttr("disabled");              
                                $("#createCategoryButton").addClass("createCategoryButtonEnabled");
                        }
                        else {
                                $("#createCategoryButton").attr( "disabled", "enabled" );
                                $("#createCategoryButton").removeClass("createCategoryButtonEnabled");                     
                        }

                        countableAttributeNameErrorFound = true;
                        countableAttributeMinValueErrorFound = true;
                        countableAttributeMaxValueErrorFound = true;
                        $(".countableAttributeNameError").empty();
                        $(".countableAttributeDescriptionError").empty();
                        $(".countableAttributeMinValueError").empty();
                        $(".countableAttributeMaxValueError").empty();
                        
                        $("#countableFilterableYes")[0].checked = true;
                        $("#saveCountableAttributeButton").attr( "disabled", "enabled" );
                        $("#saveCountableAttributeButton").removeClass("createCategoryButtonEnabled"); 

            });
        /********************************************************** CLICK BIND GIA ADD COUNTABLE ATTRIBUTE - END *******************************************************************/
        
        
        
        
        /**************************************************************** CLICK BIND GIA ADD DISTINCT ATTRIBUTE - START *******************************************************************/
        $("#saveDistinctAttributeButton").bind( "click", function() {
            
                    var is_filterable = "";
                    var valueString;
                    var weightString;
                    var valueArray;
                    var weightArray;
                    var displayString = "";

                    if ( distinct_mode == "add" ) {

                                AttributeArray[counter] = new Array(6);
                                AttributeArray[counter][0] = $( "#distinctAttributeName" ).val();
                                AttributeArray[counter][1] = $( "#distinctAttributeDescription" ).val();
                                AttributeArray[counter][2] = 1;
                                
                                if ($("#distinctFilterableYes") .is(":checked" )  )
                                    AttributeArray[counter][5] = 1;
                                
                                else if ($("#distinctFilterableNo") .is(":checked" )  )
                                    AttributeArray[counter][5] = 0;
                                

                                if ( AttributeArray[counter][5] == 1 )
                                    is_filterable = "Yes";
                                else 
                                    is_filterable = "No";

                                valueArray = document.getElementsByClassName( "distinctAttributeValueName");
                                weightArray = document.getElementsByClassName( "distinctAttributeWeight");

                                valueString = valueArray[0].value;
                                for ( var i = 1 ; i < valueArray.length ; i++ ) 
                                    valueString = valueString + "," + valueArray[i].value;

                                weightString = weightArray[0].value;
                                for ( i = 1 ; i < weightArray.length ; i++ ) 
                                    weightString = weightString + "," + weightArray[i].value;

                                AttributeArray[counter][3] = valueString;
                                AttributeArray[counter][4] = weightString;
                                
                                displayString += valueArray[0].value+"=>"+weightArray[0].value;
                                for ( i = 1 ; i < valueArray.length ; i++ )
                                    displayString += ","+valueArray[i].value+"=>"+weightArray[i].value;
                                    
                                $("#DistinctAttributes").css( "display", "block");
                                $("#DistinctTable").append( "<tr class=\"alt\" id=\"attribute"+counter+"\"><th scope=\"row\">" + AttributeArray[counter][0] + "</th> <td> " + AttributeArray[counter][1] + " </td><td> " + displayString + " </td><td> " + is_filterable + "</td><td> <span id=\"editAttribute"+counter+"\" class=\"editAttribute\">Edit</span></td><td><span id=\"removeAttribute"+counter+"\" class=\"removeAttribute\"><img class=\"remove_image\" src=\"images/close.png\">Remove</span></td></tr>" );    
                                counter++;
                                not_removed_attributes_counter++;

                    }
                    else {

                                AttributeArray[editedAttribute][0] = $( "#distinctAttributeName" ).val();
                                AttributeArray[editedAttribute][1] = $( "#distinctAttributeDescription" ).val();
                                AttributeArray[editedAttribute][2] = 1;
                                
                                if ($("#distinctFilterableYes") .is(":checked" )  )
                                    AttributeArray[editedAttribute][5] = 1;
                                
                                else if ($("#distinctFilterableNo") .is(":checked" )  )
                                    AttributeArray[editedAttribute][5] = 0;
                                

                                if ( AttributeArray[editedAttribute][5] == 1 )
                                    is_filterable = "Yes";
                                else 
                                    is_filterable = "No";

                                valueArray = document.getElementsByClassName( "distinctAttributeValueName");
                                weightArray = document.getElementsByClassName( "distinctAttributeWeight");

                                valueString = valueArray[0].value;
                                for ( i = 1 ; i < valueArray.length ; i++ ) 
                                    valueString = valueString + "," + valueArray[i].value;

                                weightString = weightArray[0].value;
                                for ( i = 1 ; i < weightArray.length ; i++ ) 
                                    weightString = weightString + "," + weightArray[i].value;

                                AttributeArray[editedAttribute][3] = valueString;
                                AttributeArray[editedAttribute][4] = weightString;
                                
                                for ( i = 0 ; i < valueArray.length ; i++ )
                                    displayString += valueArray[i].value+"=>"+weightArray[i].value;
                                    

                                $("#attribute"+editedAttribute).empty();
                                $("#attribute"+editedAttribute).html( "<th scope=\"row\">" + AttributeArray[editedAttribute][0] + "</th> <td> " + AttributeArray[editedAttribute][1] + " </td><td> " +displayString + " </td><td> " + is_filterable + "</td><td> <span id=\"editAttribute"+editedAttribute+"\" class=\"editAttribute\">Edit</span></td><td><span id=\"removeAttribute"+editedAttribute+"\" class=\"removeAttribute\"><img class=\"remove_image\" src=\"images/close.png\">Remove</span></td>" )

                    }
                    editedAttribute = -1;
                    distinct_mode = "add";            

                    $( "#distinctAttributeName" ).val("");
                    $( "#distinctAttributeDescription" ).val("");
                    $( "#distinctFilterable" ).val("");
                    $( "#Values-Weights" ).empty();
                    $( "#AddDistinctAttributeModalWindow" ).hide();
                    distinct_value_counter = 0;

                    if ( !nameErrorFound && !descriptionErrorFound && !keywordsErrorFound && counter > 0 && not_removed_attributes_counter > 0 ) {
                            $("#createCategoryButton").removeAttr("disabled");              
                            $("#createCategoryButton").addClass("createCategoryButtonEnabled");
                    }
                    else {
                            $("#createCategoryButton").attr( "disabled", "enabled" );
                            $("#createCategoryButton").removeClass("createCategoryButtonEnabled");                     
                    }

                    distinctAttributeNameErrorFound = true;
                    distinctAttributeValueNameErrorFound = true;
                    $(".distinctAttributeNameError").empty();
                    $(".distinctAttributeDescriptionError").empty();
                    
                    $("#distinctFilterableYes")[0].checked = true;
                    $("#saveDistinctAttributeButton").attr( "disabled", "enabled" );
                    $("#saveDistinctAttributeButton").removeClass("createCategoryButtonEnabled"); 
                
        })
        /******************************************************************** CLICK BIND GIA ADD DISTINCT ATTRIBUTE BUTTON - END **************************************************************/
        
        
        
        
        /************************************************************* CLICK BIND GIA ADD UNCOMPARABLE ATTRIBUTE - START **********************************************************************/
        $("#saveUncomparableAttributeButton").bind( "click", function() {
                
                    var is_filterable = "";

                    if ( uncomparable_mode == "add" ) { 

                                AttributeArray[counter] = new Array(4);
                                AttributeArray[counter][0] = $( "#uncomparableAttributeName" ).val();
                                AttributeArray[counter][1] = $( "#uncomparableAttributeDescription" ).val();
                                AttributeArray[counter][2] = 0;                               
                                
                                if ( $("#uncomparableFilterableYes").is(":checked" )  )
                                    AttributeArray[counter][3] = 1;
                                
                                else if ( $("#uncomparableFilterableNo").is(":checked" )  )
                                    AttributeArray[counter][3] = 0;
                                

                                if ( AttributeArray[counter][3] == 1 )
                                    is_filterable = "Yes";
                                else 
                                    is_filterable = "No";

                                    $("#UncomparableAttributes").css("display", "block");
                                    $("#UncomparableTable").append( "<tr class=\"alt\" id=\"attribute"+counter+"\"><th scope=\"row\">" + AttributeArray[counter][0] + "</th> <td> " + AttributeArray[counter][1] + " </td><td> " + is_filterable + "</td><td> <span id=\"editAttribute"+counter+"\" class=\"editAttribute\">Edit</span></td><td><span id=\"removeAttribute"+counter+"\" class=\"removeAttribute\"><img class=\"remove_image\" src=\"images/close.png\">Remove</span></td></tr>" );
                                    counter++;
                                    not_removed_attributes_counter++;
                    }
                    else {

                                AttributeArray[editedAttribute][0] = $( "#uncomparableAttributeName" ).val();
                                AttributeArray[editedAttribute][1] = $( "#uncomparableAttributeDescription" ).val();
                                AttributeArray[editedAttribute][2] = 0;
                                if ( $("#uncomparableFilterableYes").is(":checked" )  )
                                    AttributeArray[editedAttribute][3] = 1;
                                
                                else if ( $("#uncomparableFilterableNo").is(":checked" )  )
                                    AttributeArray[editedAttribute][3] = 0;

                                
                                if ( AttributeArray[editedAttribute][3] == 1 )
                                    is_filterable = "Yes";
                                else 
                                    is_filterable = "No";

                                $("#attribute"+editedAttribute).empty();
                                $("#attribute"+editedAttribute).html( "<th scope=\"row\">" + AttributeArray[editedAttribute][0] + "</th> <td> " + AttributeArray[editedAttribute][1] + " </td><td> " + is_filterable + "</td><td> <span id=\"editAttribute"+editedAttribute+"\" class=\"editAttribute\">Edit</span></td><td><span id=\"removeAttribute"+editedAttribute+"\" class=\"removeAttribute\"><img class=\"remove_image\" src=\"images/close.png\">Remove</span></td>" )
                    }

                    $( "#uncomparableAttributeName" ).val("");
                    $( "#uncomparableAttributeDescription" ).val("");
                    $( "#AddUncomparableAttributeModalWindow" ).hide();

                    editedAttribute = -1;
                    uncomparable_mode = "add";

                    if ( !nameErrorFound && !descriptionErrorFound && !keywordsErrorFound && counter > 0 && not_removed_attributes_counter > 0 ) {
                            $("#createCategoryButton").removeAttr("disabled");              
                            $("#createCategoryButton").addClass("createCategoryButtonEnabled");
                    }
                    else {
                            $("#createCategoryButton").attr( "disabled", "enabled" );
                            $("#createCategoryButton").removeClass("createCategoryButtonEnabled");                     
                    }

                    uncomparableAttributeNameErrorFound = true;
                    $(".uncomparableAttributeNameError").empty();
                    $(".uncomparableAttributeDescriptionError").empty();
                    
                    $("#uncomparableFilterableYes")[0].checked = true;
                    $("#saveUncomparableAttributeButton").attr( "disabled", "enabled" );
                    $("#saveUncomparableAttributeButton").removeClass("createCategoryButtonEnabled"); 
                    
        });
        /********************************************************** CLICK BIND GIA ADD UNCOMPARABLE ATTRIBUTE - END *******************************************************************/
        
        
        
        
        /********************************************************** CLICK BIND GIA CREATE CATEGORY BUTTON - START *****************************************************************************/
        $("#createCategoryButton").bind( "click", function() {
            
                    var FinalAttributeArray = [];
                    var index = 0;
                    var j = 0;
                    

                    $("#image_iframe").contents().find( ".image_path" ).each( function () {
                        image_path = $(this).attr('id' );
                    })
                    
                    for ( i = 0 ; i < AttributeArray.length ; i++ ) {
                        if ( AttributeArray[i][0] != "" ) {

                               if ( AttributeArray[i].length == 7 ) {
                                   FinalAttributeArray[index] = new Array(7);
                                   for ( j = 0 ; j < 7 ; j++ )
                                       FinalAttributeArray[index][j] = AttributeArray[i][j];
                               }
                               else if ( AttributeArray[i].length == 6 ) {
                                   FinalAttributeArray[index] = new Array(6);
                                   for ( j = 0 ; j < 6 ; j++ ) 
                                       FinalAttributeArray[index][j] = AttributeArray[i][j];
                               }
                               else {
                                   FinalAttributeArray[index] = new Array(4);
                                   for ( j = 0 ; j < 4 ; j++ )
                                       FinalAttributeArray[index][j] = AttributeArray[i][j];
                               }
                               index++;                    
                        }
                    }

                    $.post( "logic_includes/logic.php", { call: "create_category", category_name: $(".createCategoryName").val() , category_description: $(".createCategoryDescription").val(), category_keywords: $(".createCategoryKeywords").val(), category_type: $(".createCategoryType:checked").val(), attributes_array: FinalAttributeArray, category_image: image_path } ,function(xml){

                                if ( xml.length > 0 ){

                                        var status = parse_status(xml);
                                        if ( status == 0 ) {

                                                $("#createCategoryResult").html( "<br><p style=\"font-weight: bold; color: green;\">Category created successfully!</p>");
                                                $(xml).find("id").each(function(){
                                                        var id = $(this).text();
                                                        window.location = "./manageEntities.php?cat_id="+id;
                                                });

                                        }
                                        else {
                                                $("#createCategoryResult").html( "<br><p style=\"font-weight: bold; color: red;\">An error has occured</p>");
                                                
                                        }
                                }
                                else{
                                        $("#createCategoryResult").html( "<br><p style=\"font-weight: bold; color: red;\">AJAX error</p>");
                                }
                        });
                        
        });
        /********************************************************** CLICK BIND GIA CREATE CATEGORY BUTTON - END *****************************************************************************/       
        
        
        
        /************************************************ CLICK BIND GIA ADD DISTINCT ATTRIBUTE VALUE BUTTON - START ************************************************************************/
        $("#addDistinctValueButton").bind( "click", function() {
            
                        distinct_value_counter++;
                        $("#Values-Weights").append( 
                            "<div id=\"distinct_value_weight"+distinct_value_counter+"\" class=\"distinct_value_weight\"><label class=\"distinct_attribute_label\">Value: </label><input id=\"distinctAttributeValueName"+distinct_value_counter+"\" class=\"distinctAttributeValueName\" type=\"text\" title=\"please specify a distinct value\" value=\"\" />"+
                            "<label class=\"distinct_attribute_label\"> Preference: </label><select id=\"distinctAttributeWeight"+distinct_value_counter+"\" class=\"distinctAttributeWeight\">"+weight_options+"</select><div id=\"distinctAttributeValueName"+distinct_value_counter+ "Error\" class=\"distinctAttributeValueNameError\"> </div></div>"               
                        );
                        distinctAttributeValueNameErrorFound = true;
    
                        $("#saveDistinctAttributeButton").attr( "disabled", "enabled" );
                        $("#saveDistinctAttributeButton").removeClass("createCategoryButtonEnabled");    
                    
        });
        /************************************************ CLICK BIND GIA ADD DISTINCT ATTRIBUTE VALUE BUTTON - END ************************************************************************/
        
        
        
        /************************************************ CLICK BIND GIA REMOVE DISTINCT ATTRIBUTE VALUE BUTTON - START ************************************************************************/
        $("#removeDistinctValueButton").bind( "click", function() {         
                    
                        $( ".distinct_value_weight:last" ).remove();
                        if ( distinct_value_counter > 1 )
                                distinct_value_counter--;
                        else {
                                $("#saveDistinctAttributeButton").attr( "disabled", "enabled" );
                                $("#saveDistinctAttributeButton").removeClass("createCategoryButtonEnabled");    
                        }
                        checkDistinctValuesWeights();
                        
        });
        /************************************************ CLICK BIND GIA REMOVE DISTINCT ATTRIBUTE VALUE BUTTON - END ************************************************************************/
        
        
	$("#create_category input").bind( "blur" ,checkValidity );
        $("#create_category textarea").bind( "blur" ,checkValidity );
        
        $("#AddCountableAttributeModalWindow input").bind( "blur" ,checkCountableAttributeValidity );
        $("#AddCountableAttributeModalWindow textarea").bind( "blur" ,checkCountableAttributeValidity );
        
        $("#AddDistinctAttributeModalWindow input").bind( "blur" ,checkDistinctAttributeValidity );       
        $("#AddDistinctAttributeModalWindow textarea").bind( "blur" ,checkDistinctAttributeValidity );
        $(".distinctAttributeValueName").live( "blur" ,checkDistinctAttributeValidity );
        
        $("#AddUncomparableAttributeModalWindow input").bind( "blur" ,checkUncomparableAttributeValidity );
        $("#AddUncomparableAttributeModalWindow textarea").bind( "blur" ,checkUncomparableAttributeValidity );
        
        $(".editAttribute").live( "click", function(event) {
            
                var currentId = $(this).attr('id');
                event.stopPropagation();
                var index = parseInt( currentId.substr(13) );
    
                showCategoryGeneralInfo();
                
                /******************************************* EDIT COUNTABLE ATTRIBUTE *****************************************/
                if ( AttributeArray[index].length == 7 ) {
                       
                        
                        countableAttributeNameErrorFound = false;
                        countableAttributeMinValueErrorFound = false;
                        countableAttributeMaxValueErrorFound = false;
                        
                        editedAttribute = index;
                        countable_mode = "edit";
                        $('#AddCountanbleAttributeModalWindow').fadeIn( 1000 );    
                        $('#AddCountableAttributeModalWindow').fadeTo( "slow", 1.0 );
                        
                        $( "#countableAttributeName" ).val( AttributeArray[index][0] );
                        $( "#countableAttributeDescription" ).val( AttributeArray[index][1] );
                        $( "#countableAttributeMinValue" ).val( AttributeArray[index][3] );
                        $( "#countableAttributeMaxValue" ).val( AttributeArray[index][4] );
                        $( "#countableAttributeComparisonType" ).val( AttributeArray[index][5] );
                        
                        if ( parseInt( AttributeArray[index][6] ) == 1 )
                            $("#countableFilterableYes")[0].checked = true;
                        else
                            $("#countableFilterableNo")[0].checked = true;
                        
                        $("#saveCountableAttributeButton").removeAttr("disabled");              
                        $("#saveCountableAttributeButton").addClass("createCategoryButtonEnabled");
                        
                }
                /******************************************* EDIT DISTINCT ATTRIBUTE *****************************************/
                else if ( AttributeArray[index].length == 6 ) {
                    
                        distinctAttributeNameErrorFound = false;
                        distinctAttributeValueNameErrorFound = false;
                        $("#Values-Weights").empty();
                        
                        editedAttribute = index;
                        distinct_mode = "edit";                        
                        $('#AddDistinctAttributeModalWindow').fadeIn( 1000 );    
                        $('#AddDistinctAttributeModalWindow').fadeTo( "slow", 1.0 );

                        $( "#distinctAttributeName" ).val( AttributeArray[index][0] );
                        $( "#distinctAttributeDescription" ).val( AttributeArray[index][1] );               
                        
                        var values = AttributeArray[index][3].split( ',' );
                        var weights = AttributeArray[index][4].split( ',' );
                        distinct_value_counter = values.length;
                        
                        for ( var i = 1 ; i <= distinct_value_counter ; i++ ) {
                            
                            $("#Values-Weights").append( 
                                "<div  class=\"distinct_value_weight\" id=\"distinct_value_weight"+i+"\"><label class=\"distinct_attribute_label\">Value: </label><input id=\"distinctAttributeValueName"+i+"\" class=\"distinctAttributeValueName\" type=\"text\" title=\"please specify a distinct value\" value=\"\" />"+
                                "<label class=\"distinct_attribute_label\"> Weight: </label><select id=\"distinctAttributeWeight"+i+"\" class=\"distinctAttributeWeight\">"+weight_options+"</select><div class=\"distinctAttributeValueNameError\" id=\"distinctAttributeValueName"+i+"Error\"> </div></div>"               
                            );
                                
                            $( "#distinctAttributeValueName"+i ).val( values[i-1] );
                            $( "#distinctAttributeWeight"+i ).val( weights[i-1] );
                                                        
                        }
                        
                        if ( parseInt( AttributeArray[index][5] ) == 1 )
                            $("#distinctFilterableYes")[0].checked = true;
                        else
                            $("#distinctFilterableNo")[0].checked = true;
                        
                        $("#saveDistinctAttributeButton").removeAttr("disabled");              
                        $("#saveDistinctAttributeButton").addClass("createCategoryButtonEnabled");
                        
                }
                /********************************** EDIT UNCOMPARABLE ATTRIBUTE ***********************************************/
                else {
                    
                        uncomparableAttributeNameErrorFound = false;
                        
                        editedAttribute = index;
                        uncomparable_mode = "edit";
                        $('#AddUncomparableAttributeModalWindow').fadeIn( 1000 );    
                        $('#AddUncomparableAttributeModalWindow').fadeTo( "slow", 1.0 );
                        
                        $( "#uncomparableAttributeName" ).val( AttributeArray[index][0] );
                        $( "#uncomparableAttributeDescription" ).val( AttributeArray[index][1] );
                        
                        if ( parseInt( AttributeArray[index][3] ) == 1 )
                            $("#uncomparableFilterableYes")[0].checked = true;
                        else
                            $("#uncomparableFilterableNo")[0].checked = true;
                        
                        $("#saveUncomparableAttributeButton").removeAttr("disabled");              
                        $("#saveUncomparableAttributeButton").addClass("createCategoryButtonEnabled");
                                                                     
                }
            
        });
        
        
        
        /******************************************* REMOVE ATTRIBUTE FUNCTION - START **************************************************************/
        $(".removeAttribute").live( "click", function(event) {
                        var currentId = $(this).attr('id');
                        var index = parseInt( currentId.substr(15) );
                        event.stopPropagation();

                        $( "#attribute"+index ).remove();

                        AttributeArray[index][0] = "";                        //Make corresponding attribute name "" (empty)
                        not_removed_attributes_counter--;
                        allowCategoryCreation();
                            
        });
        /******************************************* REMOVE ATTRIBUTE - END *******************************************************************************/
        
        
             
        $(".createCategoryType").bind( "click", allowCategoryCreation);
        
        function allowCategoryCreation() {
            
                        if ( !nameErrorFound && !descriptionErrorFound && !keywordsErrorFound && counter > 0  && not_removed_attributes_counter > 0 ) {
                                $("#createCategoryButton").removeAttr("disabled");              
                                $("#createCategoryButton").addClass("createCategoryButtonEnabled");
                        }
                        else {
                                $("#createCategoryButton").attr( "disabled", "enabled" );
                                $("#createCategoryButton").removeClass("createCategoryButtonEnabled");                     
                        }

        }
        
        $("#createCategoryButton").ajaxSuccess(function(e, xhr, settings) {
                allowCategoryCreation();
        });
        
});