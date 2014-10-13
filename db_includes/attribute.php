<?php

/*
 *  attribute.php:	One of the db classes for the youcompare project
 *  Implemented by:	Karampelas Thomas
 *  std code:		std07015
 *  A.M. code:		1115200700015
 *  Semester:		Spring 2011
 */
	
	require_once "DB_DEFINES.php" ;
	
	class attribute
	{
	
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	VARIABLES
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/	
		private $attr_id;
		private $errno;
		private $con;
		private $name;
		private $comparability;
		private $is_filterable;
		private $description;
        private $default_weight;

/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF VARIABLES
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/			

/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	CONSTRUCTOR/DESTRUCTOR
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/	

		public function __construct( $con, $attr_id, $category_id )																				
		{
			$this->errno = DB_OK;		
			$this->con = $con;
			if ( $attr_id != -1)																					//	-1 is given as id when the attribute hasn't been created yet 
			{
				/* check the validity of the id given and whether or not it belongs to the category with id $category_id */
				if ( ! ( $result = $this->con->query( "SELECT attr_name, comparability, filterable, attr_description, default_weight FROM attribute WHERE attr_id=$attr_id AND cat_id=$category_id" ) ) )
				{
					$this->errno = MYSQL_ERROR;																		//	Failed to connect	
					return ;
				}

				if ( !($row = $result->fetch_row()) )																//	then the id is invalid
				{		
					$this->errno = WRONG_INPUT;
					return;
				}

				$this->name = $row[0];
				$this->comparability = $row[1];
				$this->is_filterable = $row[2];
				$this->description = $row[3];
				$this->default_weight= $row[4];
			}
			$this->attr_id = $attr_id;																				//	initialise class's variable		
		}

/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF CONSTRUCTOR/DESTRUCTOR
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/	

/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	CREATE/DELETE FUNCTIONS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/

		public function add_attribute ( $attr_info, $category_id )												
		{
			$this->errno = DB_OK;

			if ( $this->attr_id != -1 )
			{
				$this->errno = WRONG_ID;
				return ;
			}
   
			if ( ! ( $result = $this->con->query( "SELECT attr_id FROM attribute WHERE cat_id=$category_id AND attr_name=\"$attr_info->name\"" ) ) )
			{						
				$this->errno = MYSQL_ERROR;																		//	failed to query				
				return ;				
			}	
	
			if ( $result->num_rows != 0 )																		//	if an attribute with the same name exists in the same category 
			{																									//	an error code should be returned
				$result->close();
				$this->errno = ATTRIBUTE_EXISTS;
				return ;				
			}		

			$query = "INSERT INTO attribute ( attr_name, comparability, filterable, cat_id " ;
			
			/* the following variables may have null value as its existance is optional in the database */
			if ( ! is_null( $attr_info->description ) )
			{
				$query = $query.", attr_description";
			}

 			if ( ! is_null( $attr_info->default_weight ) )
			{
				$query = $query.", default_weight";
			}
			$query = $query." ) VALUES (  \"$attr_info->name\", $attr_info->comparability, $attr_info->is_filterable, $category_id ";
	
			if ( ! is_null( $attr_info->description ) )
			{
				$query = $query.", \"$attr_info->description\"";
			}

			if ( ! is_null( $attr_info->default_weight ) )
			{
				$query = $query.", $attr_info->default_weight";
			}
			
			$query = $query." )";

			/* inserting to the database */		
			if ( !$this->con->query( $query ) )
			{
				$this->errno = MYSQL_ERROR;																		//	failed to query				
				return ;				
			}	
  
			if( $this->con->affected_rows != 1 )																//	insert should affect 1 row
			{						
				$this->errno = MYSQL_ERROR;																					
				return ;				
			}				 

			$this->attr_id = $this->con->insert_id;
		 
			if ( ( $err = $this->insert_depending_comparability ( $attr_info, $category_id ) ) != DB_OK )						//	insert in the other tables related to attribute
			{							
				$this->errno = $err;																					
				return ;
			}
 
			/* fill the class variables with the apropriate values */
			$this->name = $attr_info->name;
			$this->comparability = $attr_info->comparability;
			$this->is_filterable = $attr_info->is_filterable;
			$this->description = $attr_info->description;
          
			return $this->attr_id;

		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
	
		public function remove_attribute()
		{	

			$this->errno = DB_OK;
			
			if ( $this->attr_id == -1 )
			{
				$this->errno = WRONG_ID;
				return ;
			}			
			
			if ( ! ( $result = $this->con->query ( "DELETE FROM attribute WHERE attr_id=$this->attr_id" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;
			}
			
			if( $this->con->affected_rows == 0 )
			{
				$result->close();
				$this->errno = ATTRIBUTE_DOESNT_EXIST;
				return ;						
			}
		}
		
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF CREATE/DELETE FUNCTIONS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/	

/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	ACCESSORS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/

		public function get_errno()
		{
			return $this->errno;
		}

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
		/* returning the possible values if distinct or the range of the possible values if countable of an attribute */
		public function get_dependencies( $comparability )
		{
			$this->errno = DB_OK;
			$elements=array();
			
			if ( $this->attr_id == -1 )
			{
				$this->errno = WRONG_ID;
				return ;
			}				
			
			if ( $comparability == UNCOMPARABLE )
				return ;
			else if ( $comparability == DISTINCT )
			{
				if ( ! ( $result = $this->con->query( "SELECT * FROM is_distinct WHERE attr_id=$this->attr_id" ) ) )
				{
					$this->errno = MYSQL_ERROR;																	//	Failed to connect	
					return ;	
				}
			
				while ( $row = $result->fetch_row() )															//	getting all distinct values		
				{
					$elements[] = array( "Preference"=>$row[2], "Value"=>$row[1] );
				}
				
			}
			else
			{
			
				if ( ! ( $result = $this->con->query( "SELECT * FROM is_countable WHERE attr_id=$this->attr_id" ) ) )
				{
					$this->errno = MYSQL_ERROR;																	//	Failed to connect	
					return ;	
				}

				if ( $result->num_rows == 1 )																	//	as the id is unique one row should have been returned
				{
					$row = $result->fetch_row();
					$elements = array ( "Upper Limit" =>$row[1], "Lower Limit"=>$row[2], "Comparison Type"=>$row[3] );
				}				
				else
				{
					$result->close();
					$this->errno = MYSQL_ERROR;
					return ;
				}
			}
			
			return $elements;
		}		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/			
		public function get_info()
		{
			$this->errno = DB_OK;

			if ( $this->attr_id == -1 )
			{
				$this->errno = WRONG_ID;
				return ;
			}	

			$attribute_info = new attribute_info();
			$attribute_info->id = $this->attr_id;
			$attribute_info->name = $this->name;
			$attribute_info->comparability = $this->comparability;
			$attribute_info->is_filterable = $this->is_filterable;
			$attribute_info->description = $this->description;
			$attribute_info->type_elements = $this->get_dependencies( $attribute_info->comparability );	
            $attribute_info->default_weight=$this->default_weight;
			
			return $attribute_info;
		}
		
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF ACCESSORS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/

/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	MUTATORS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/

		public function set_attribute( $attr_info )
		{
			$this->errno = DB_OK;
			$flag = 0;
			
			if ( $this->attr_id == -1 )																			//	Such attribute hasn't been created																			
			{
				$this->errno = WRONG_ID;
				return ;
			}	
	
			$query = "UPDATE attribute SET"; 
	
			if ( ! is_null( $attr_info->name ) )
			{
				$flag=1;
				$query .= " attr_name=\"$attr_info->name\"";													//	Setting attribute's name
				$this->name = $attr_info->name;
			}
	
			if ( ! is_null( $attr_info->description ) )	
			{		
				if ( $flag == 1 )
					$query .= " , ";
				else
					$flag=1;
					
				$query .= " attr_description=\"$attr_info->description\"";										//	Setting attribute's description
				$this->description = $attr_info->description;
			}
		
			if ( ! is_null( $attr_info->is_filterable ) )
			{
				if ( $flag == 1 )
					$query .= " , ";
				else
					$flag=1;
					
				$query .= " filterable=$attr_info->is_filterable";												//	Setting attribute's filterability
				$this->is_filterable = $attr_info->is_filterable;
			}
			
            if ( ! is_null( $attr_info->default_weight ) )
			{
				if ( $flag == 1 )
					$query .= " , ";
				else
					$flag=1;

				$query .= " default_weight=$attr_info->default_weight";											//	Setting attribute's filterability
				$this->default_weight = $attr_info->default_weight;
			}

			if ( $flag != 0 )
			{
				$query .=  " WHERE attr_id=$this->attr_id;";
	
				if( !$this->con->query( $query ) )			
				{
					$this->errno = MYSQL_ERROR;					
					return ;																					//	Failed to query				
				}
				
			}
			
			if ( $attr_info->comparability == COUNTABLE && ! is_null( $attr_info->type_elements ) )
			{
				$this->set_countable( $attr_info->type_elements );
			
				if ( $this->errno != DB_OK )
				{
					return ;
				}		
			}
			else if ( $attr_info->comparability == DISTINCT  && ! is_null( $attr_info->type_elements ) )		//	if the attribute is distinct we may have 4 cases
			{
			
				for ( $i = 0; $i < count ( $attr_info->type_elements ); $i++ )									//	for every attribute in the array
				{				
					if ( $attr_info->type_elements[$i]["Old"] === NULL && $attr_info->type_elements[$i]["New"] === NULL )	//	this is not an edit case just an error input
					{
						$this->errno = WRONG_INPUT;
						return ;
					}	
					else if ( $attr_info->type_elements[$i]["Old"] === NULL )									//	the first case needs an attribute's distinct value to be added 
					{																							//	along with its preference		
						$this->add_distinct( $attr_info->type_elements[$i] );
					}
					else if ( $attr_info->type_elements[$i]["New"] === NULL )									//	the second case needs an attribute's distinct value to be removed 
					{																							//	along with its preference
						$this->remove_distinct( $attr_info->type_elements[$i] );
					}
					else																						//	the third and fourth case needs either an attribute's distinct 
					{																							//	value along with its preference or the preference to be edited
						$this->set_distinct( $attr_info->type_elements[$i] );
					}
			
					if ( $this->errno != DB_OK )																//	if any error occurs stop the procedure
					{
						return ;
					}	
				}
			}
		}

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/		

		public function set_countable( $countable_elements )
		{
			$this->errno = DB_OK;

			if ( $this->attr_id == -1 )																			//	Such attribute hasn't been created																			
			{
				$this->errno = WRONG_ID;
				return ;
			}			
			
			if( !$this->con->query( "UPDATE is_countable SET upper_limit=".$countable_elements["Upper Limit"].", lower_limit=".$countable_elements["Lower Limit"].", comparison_type=".$countable_elements["Comparison Type"]." WHERE attr_id=$this->attr_id;" ) )			
			{
				$this->errno = MYSQL_ERROR;					
				return ;																						//	Failed to query				
			}	
			
			/* Setting to null all entity values that the countable value which has been assigned is out of the new bounds */
			if ( ! ( $result = $this->con->query( "UPDATE entity_has_value SET c_value=NULL WHERE ( ( c_value>".$countable_elements["Upper Limit"]." ) OR ( c_value<".$countable_elements["Lower Limit"]." ) ) AND attr_id=$this->attr_id" ) ) )
			{ 
				$this->errno = MYSQL_ERROR;																		//	Failed to connect		
				return ;			
			}				
			
		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
		
		/* This function adds an attributes value along with its preference */
		private function add_distinct( $distinct_element )
		{
			$this->errno = DB_OK;

			if ( $this->attr_id == -1 )																			//	Such attribute hasn't been created																			
			{
				$this->errno = WRONG_ID;
				return ;
			}	

			if ( ! ( $result = $this->con->query( "INSERT INTO is_distinct VALUES ( $this->attr_id, \"".$distinct_element["New"]."\", ".$distinct_element["Pref"]." )" ) ) )
			{ 	
				$this->errno = MYSQL_ERROR;																		//	Failed to connect		
				return ;			
			}
			
			if( $this->con->affected_rows != 1 )																//	after this insert 1 row should be affected
			{ 
				$this->errno = MYSQL_ERROR;																			
				return ;			
			}				
		}			
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/			
		
		/* This function removes an attributes value along with its preference */
		private function remove_distinct( $distinct_element )
		{
			$this->errno = DB_OK;

			if ( $this->attr_id == -1 )																			//	Such attribute hasn't been created																			
			{
				$this->errno = WRONG_ID;
				return ;
			}	

			if ( ! ( $result = $this->con->query( "DELETE FROM is_distinct WHERE value_name=\"".$distinct_element["Old"]."\" AND attr_id=$this->attr_id" ) ) )
			{ 	
				$this->errno = MYSQL_ERROR;																		//	Failed to connect		
				return ;			
			}
			
			if( $this->con->affected_rows != 1 )																//	after this insert 1 rows should be affected
			{ 
				$this->errno = MYSQL_ERROR;																			
				return ;			
			}

			/* Updating all entity values that this distinct value has been assigned */
			if ( ! ( $result = $this->con->query( "UPDATE entity_has_value SET d_value=NULL WHERE d_value=\"".$distinct_element["Old"]."\" AND attr_id=$this->attr_id" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect		
				return ;			
			}			
			
		}

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
		/* This function edits an attributes value along with its preference */
		private function set_distinct( $distinct_element )
		{
			$this->errno = DB_OK;

			if ( $this->attr_id == -1 )																			//	Such attribute hasn't been created																			
			{
				$this->errno = WRONG_ID;
				return ;
			}			
			
			if( !$this->con->query( "UPDATE is_distinct SET value_name=\"".$distinct_element["New"]."\", preference=".$distinct_element["Pref"]." WHERE attr_id=$this->attr_id AND value_name=\"".$distinct_element["Old"]."\";" ) )			
			{	
				$this->errno = MYSQL_ERROR;					
				return ;																						//	Failed to query				
			}	
			
			if( $this->con->affected_rows != 1 )																//	after this insert 1 rows should be affected
			{ 
				$this->errno = MYSQL_ERROR;																			
				return ;			
			}				

			/* Updating all entity values that this distinct value has been assigned */
			if ( ! ( $result = $this->con->query( "UPDATE entity_has_value SET d_value=\"".$distinct_element["New"]."\" WHERE d_value=\"".$distinct_element["Old"]."\" AND attr_id=$this->attr_id" ) ) )
			{ 
				$this->errno = MYSQL_ERROR;																		//	Failed to connect		
				return ;			
			}
			
		}		
		
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF MUTATORS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/

/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	USEFULL FUNCTIONS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/	

		private function insert_depending_comparability ( $attr_info, $cat_id )											
		{
	
			if ( $attr_info->comparability == DISTINCT )														//	if our attribute is distinct
			{
			
				for ( $i = 0; $i < count ( $attr_info->type_elements ); $i++ )									//	for every attribute in the array
				{
					if ( ! ( $result = $this->con->query( "INSERT INTO is_distinct VALUES ( $this->attr_id, \"".$attr_info->type_elements[$i]["Value"]."\", ".$attr_info->type_elements[$i]["Preference"]." )" ) ) )
					{ 
						$this->errno = MYSQL_ERROR;																//	Failed to connect		
						return ;				
					}
					
					if( $this->con->affected_rows != 1 )														//	after this insert 1 rows should be affected
					{																							
						$this->errno = MYSQL_ERROR;																//	Failed to connect		
						return ;
					}
				}	
				
			}
			else if ( $attr_info->comparability == COUNTABLE )													//	if our attribute is distinct
			{		
				if ( ! ( $result = $this->con->query( "INSERT INTO is_countable VALUES (  $this->attr_id, \"".$attr_info->type_elements["Upper Limit"]."\", \"".$attr_info->type_elements["Lower Limit"]."\" ,\"".$attr_info->type_elements["Comparison Type"]."\" )" ) ) )
				{ 
					$this->errno = MYSQL_ERROR;																	//	Failed to connect		
					return ;			
				}
				
				if( $this->con->affected_rows != 1 )															//	after this insert 1 rows should be affected
				{ 
					$this->errno = MYSQL_ERROR;																				
					return ;			
				}
				
			}
			
			/* inform the entity_has_value table that another attribute is in town */
			if ( ! ( $result = $this->con->query( "SELECT ent_id FROM entity WHERE cat_id=$cat_id" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;	
			}
			
			while ( $row = $result->fetch_row() )															
			{
				$ent_id = $row[0];
				
				if ( ! $this->con->query( "INSERT INTO entity_has_value ( attr_id, ent_id ) VALUES ( $this->attr_id, $ent_id )" ) )
				{ 
					$this->errno = MYSQL_ERROR;																	//	Failed to connect		
					return ;				
				}
			}			

		}	
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/			
		public function is_filterable()
		{		
			$this->errno = DB_OK;

			if ( $this->attr_id == -1 )
			{
				$this->errno = WRONG_ID;
				return ;
			}	
			
			return $this->is_filterable;
		}	
		
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END 0F USEFULL FUNCTIONS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/	

	}
?>