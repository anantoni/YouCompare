<?php
	$mode__ = "logic";
	if (isSet($_POST["call"])) {
		$mode__ = $_POST["call"];
		
		switch ($mode__) {

			case "check_entity_name":
				include_once("../ajax/checkEntityName.php");
				break;

			case "login" :
				include_once("../ajax/_authenticateuser.php");
				break;
				
			case "register" :
				include_once("../ajax/_signup.php");
				break;
				
			case "check_username" :
				include_once("../ajax/check_username.php");
				break;
				
			case "check_email" :
				include_once("../ajax/check_email.php");
				break;
                            
                        case "check_category_name" :
				include_once("../ajax/check_category_name.php");
				break;
				
			case "create_category" :
				include_once("../ajax/_createcategory.php");
				break;
				
			case "show_category_attrs" :
				include_once("../ajax/get_attrs.php");
				break;
				
			case "add_item" :
				include_once("../ajax/additem.php");
				break;

			case "search" :
				include_once("search.php");
				break;

			case "visitCategory":
				include_once("../ajax/visitCategory.php");
				break;

			case "compare":
				include_once("../ajax/compare.php");
				break;
			
			case "get_comparable_attributes":
				include_once("../ajax/getCompAttrs.php");
				break;

			case "add_attribute":
				include_once("../ajax/add_attribute.php");
				break;

			case "edit_attribute":
				include_once("../ajax/editattribute.php");
				break;

			case "edit_specs":
				include_once("../ajax/editspecs.php");
				break;

			case "delete_attribute":
				include_once("../ajax/deleteattribute.php");
				break;
	
			case "delete_category":
				include_once("../ajax/deletecategory.php");
				break;

			case "add_to_comparison":
				include_once("../ajax/rmt_add_to_comparison.php");
				break;			
	
			case "get_comparisons":		
				include_once("../ajax/rmt_get_comparisons_js.php");
				break;		

			case "entity_cList":
				include_once("../ajax/rmt_entity_cList.php");
				break;	

			case "get_attrs_edit":
				include_once("../ajax/get_attrs_edit.php");
				break;
	
			case "edititem":
				include_once("../ajax/edititem.php");
				break;

			case "delete_entity":
				include_once("../ajax/deleteentity.php");
				break;	

			case "get_category_template":
				include_once("../ajax/get_category_template.php");
				break;
		
			case "set_weights_to_session":
				include_once("../ajax/rmt_set_weights_to_session.php");
				break;

			case "change_first_name":
				include_once("../ajax/change_first_name.php");
				break;

			case "change_last_name":
				include_once("../ajax/change_last_name.php");
				break;
			
			case "change_email":
				include_once("../ajax/change_email.php");
				break;

			case "change_password":
				include_once("../ajax/change_password.php");
				break;
			case "manage_users":
				include_once("../ajax/categoryUserMod.php");
				break;
			case "user_rates":
				include_once("../ajax/userRates.php");
				break;
			case "addtocomp":
				include_once("../ajax/rmt_add_to_comparison.php");
				break;
			default :
				header('Content-type: text/xml');
				$xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
				$xml.="<logic><error>
				<WRONG_PAGE></WRONG_PAGE>
				</error></logic>";
				echo $xml;
				break;
		}
	}
	else {
		header('Content-type: text/xml');
		$xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$xml.="<logic><error>
		<WRONG_PAGE></WRONG_PAGE>
		</error></logic>";
		echo $xml;
	}

?>
