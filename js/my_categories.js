/*******************************
 Edited by Anastasios Antoniadis - May 2011
 Rest of code written by Anastasios Antoniadis - May&June 2011
 
 *******************************/

/********************	CONFIRM BOX	****************************/
(function($){
	
	$.confirm = function(params){
		if($('#confirmOverlay').length){
			// A confirm is already shown on the page:
			return false;
		}
		
		var buttonHTML = '';
		$.each(params.buttons,function(name,obj){
			
			// Generating the markup for the buttons:
			
			buttonHTML += '<a href="#" class="button '+obj['class']+'">'+name+'<span></span></a>';
			
			if(!obj.action){
				obj.action = function(){};
			}
		});
		
		var markup = [
			'<div id="confirmOverlay">',
			'<div id="confirmBox">',
			'<h1>',params.title,'</h1>',
			'<p>',params.message,'</p>',
			'<div id="confirmButtons">',
			buttonHTML,
			'</div></div></div>'
		].join('');
		
		$(markup).hide().appendTo('body').fadeIn();
		
		var buttons = $('#confirmBox .button'),
			i = 0;

		$.each(params.buttons,function(name,obj){
			buttons.eq(i++).click(function(){
				
				// Calling the action attribute when a
				// click occurs, and hiding the confirm.
				
				obj.action();
				$.confirm.hide();
				return false;
			});
		});
	};

	$.confirm.hide = function(){
		$('#confirmOverlay').fadeOut(function(){
			$(this).remove();
		});
	};
	
})(jQuery);


/*********************************************************************/




$( document ).ready( function() {
    
            $(".manage_category").bind( "click", function() { 
                    
                    var currentId = $(this).attr('id');
                    window.location = "./manage_category.php?id="+currentId;

            })
            
            $(".manage_entities").bind( "click", function() { 
                    
                    var currentId = $(this).attr('id');
                    window.location = "./manageEntities.php?cat_id="+currentId;

            })
    
            $(".manage_users").bind( "click", function() { 
                    
                    var currentId = $(this).attr('id');
                    window.location = "./manageUsers.php?cat_id="+currentId;

            })
            
            $(".delete_category").bind( "click", function() {
var currentId = $(this).attr('id');
    $.confirm({
            'title'		: 'Delete Category Confirmation',
            'message'	: 'Delete This Category <br />Please Confirm this action! Continue?',
            'buttons'	: {
                    'Yes'	: {
                            'class': 'blue',
                            'action': function(){
                                
                   		    $.post( "logic_includes/logic.php", { call: "delete_category", cat_id: currentId } ,function(xml){

                                if ( xml.length > 0 ){

                                        var status = parse_status(xml);
                                        if ( status == 0 ) {

                                                $("#deleteCategoryResult").html( "<br><p style=\"font-weight: bold;\">Category deleted successfully!</p>");
                                                $( "#category"+currentId ).remove();

                                        }
                                        else {
                                                $("#deleteCategoryResult").html( "<br><p style=\"font-weight: bold;\">An error has occured</p>");
                                              
                                        }
                                }
                                else{
                                        $("#deleteCategoryResult").html( "<br><p style=\"font-weight: bold;\">AJAX error</p>");
                                }
                        });   
                            }
                    },
                    'No'	: {
                            'class' : 'blue',
                            'action': function(){
                            }	
                    }
            }
    });
                
                    
                
                
                
            });
        
        
            
            /***************************************** TABLE TOGGLE *************************************************************************************/
            $(".table_toggle").live( "click", function() {
                                          
                        var id = $(this).attr('id');
                        
                        if ( id == "category_member_toggle" )
                             $( "#categoryMemberList" ).slideToggle( "slow", ToggleCallbackFunction );                     //Toggle Hide or Show
                        else if ( id == "editor_member_toggle" )
                             $( "#editorMemberTable" ).slideToggle( "slow", ToggleCallbackFunction );
                        else if ( id == "sub_moderator_toggle" )
                             $( "#subModeratorTable" ).slideToggle( "slow", ToggleCallbackFunction );
                        else 
                             $( "#moderatorTable" ).slideToggle( "slow", ToggleCallbackFunction ); 
            });
        
        
        
            /************************************** TOGGLE CALLBACK FUNCTION *****************************************************************************/
            function ToggleCallbackFunction() {      
                    
                        var id = $(this).attr('id');
                 
                        if ( id == "categoryMemberList" )      { 
                           
                                if ( $( "#categoryMemberList" ).is(":visible") ) 
                                    $( "#category_member_visible" ).html( "[Hide]" );
                                
                                else 
                                    $( "#category_member_visible" ).html( "[Show]" );  
                              
                        }
                        else if ( id == "editorMemberTable" ) { 
                                
                                if ( $( "#editorMemberTable" ).is(":visible") ) 
                                    $( "#editor_member_visible" ).html( "[Hide]" );

                                else                         
                                    $( "#editor_member_visible" ).html( "[Show]" );      
                            
                        }
                        else if ( id == "subModeratorTable" ) { 
                                
                                if ( $( "#subModeratorTable" ).is(":visible") ) 
                                    $( "#sub_moderator_visible" ).html( "[Hide]" );

                                else                         
                                    $( "#sub_moderator_visible" ).html( "[Show]" );      
                            
                        }
                        else {
                            
                                if ( $( "#moderatorTable" ).is(":visible") ) 
                                    $( "#moderator_visible" ).html( "[Hide]" );
                                 
                                else 
                                    $( "#moderator_visible" ).html( "[Show]" );
                                 
                        }
                        return false;       
                    
                }
})