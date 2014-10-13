<?php

/*
 *  category.php:	One of the db classes for the youcompare project
 *  Implemented by:	Karampelas Thomas
 *  std code:		std07015
 *  A.M. code:		1115200700015
 *  Semester:		Spring 2011
 */
	class category
	{

/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	VARIABLES
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/
		private $cat_id;
		private $errno;
		private $con;
		private $cat_name;
		private $is_open;
		private $cat_description;
		private $cat_keywords;
		private $image;
		private $video;
		private $rate;		
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF VARIABLES
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/
		
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~
	CONSTRUCTOR/DESTRUCTOR
	~~~~~~~~~~~~~~~~~~~~~~~~~
*/

		/* The constructor checks the validity of the id given and if it's valid it initializes class variables from the database. */
		/* The id can be also -1 in case of some function such as create_category where the knowledge of the id is impossible */
		public function __construct( $id )																			
		{
			$this->errno = DB_OK;
			
			$this->con = new mysqli("edudb.di.uoa.gr", "soft_tech", "#s@f+#", "soft_tech_db");						//	connect to database

			/* check connection */
			if( mysqli_connect_errno() )
				return MYSQL_CONNECT_ERROR;																			//	failed to connect

			$this->con->query("SET NAMES utf8");																	//	Setting encoding to UTF8 
			
			if ( $id != -1)																							//	-1 is given as id when the category hasn't been created yet
			{																										//	or the knowledge of the id isn't necessary
			
				if ( ! ( $result = $this->con->query( "SELECT c.*, AVG(urc.rate) FROM category c , user_rates_category urc WHERE c.cat_id = $id AND c.cat_id = urc.cat_id" ) ) )
				{
					$this->errno = MYSQL_ERROR;																		//	Failed to connect	
					return ;
				}

				if ( $result->num_rows == 0 )																		//	then the id is invalid
				{		
					$this->errno = WRONG_INPUT;
					return;
				}
				else
				{
					$row = $result->fetch_row();
					if ( $row[1] == NULL )
					{		
						$this->errno = WRONG_INPUT;
						return;
					}

					/* initializing class variables */
					$this->cat_name = $row[1];
					$this->is_open = $row[2];
					$this->cat_description = $row[3];
					$cat_keywords = $row[4];
					$this->image = $row[5];
					$this->video = $row[6];
					
					if ( isset( $row[7] ) )																			
						$this->rate = $row[7];
					else																							//	if no user has rated the category a default value of
						$this->rate = 0;																			//	0 should be returned instead
				
					$tok = strtok( $cat_keywords, "#" );															//	turning the keywords string into an array
		
					while ( $tok !== false ) 
					{
						$this->cat_keywords  [] = $tok;
						$tok = strtok( "#" );
					}
				}
			}
			
			$this->cat_id = $id;			
		}	

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		/* Destructor's job is just to terminate the connection with the database */
        public function __destruct() 																				
        {
            if( !$this->con->connect_errno )																		//	if no problem has occured
            {
                $this->con->close();																				//	close the connection
            }
        }

/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF CONSTRUCTOR/DESTRUCTOR
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/
	
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	CREATE/DELETE  FUNCTIONS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/
		/* create the category along with it's dependencies in the name of user $username */
		public function create_category ( $cat_info, $username )												
		{
			$this->errno = DB_OK;
			
			if ( $this->cat_id != -1 )																			//	The not exist category is obvious that has no id yet
			{
				$this->errno = WRONG_ID;
				return ;
			}
			
			$user = new user( $username , LOGGED_IN );															//	creating an object of the user who wants to create a category
																												//	it is mandatory to be logged in
			$attr_length = 0; 																					//	initialize with 0 in case the attribute array hasn't been set

			if ( !isset ( $cat_info ) )																			//	if the cat_info structure is null
			{
				$this->errno = WRONG_INPUT;																		//	then the input is wrong
				return ;
			}

			if ( isset ( $cat_info->attr_info_array ) )																								
				$attr_length = count ( $cat_info->attr_info_array );
			
			if ( $attr_length == 0 )																			//	the existance of at least one attribute is mandatory
			{
				$this->errno = NOT_ENOUGH_ATTRIBUTES;
				return ;
			}

			if ( !( $result = $this->con->query( "SELECT cat_id FROM category WHERE cat_name=\"$cat_info->cat_name\"" ) ) )	//	figuring out if category with such name exists
			{
				$this->errno = MYSQL_ERROR;
				return ;
			}
		
			if ( $result->num_rows != 0 )																		//	if category exists error code should be returned
			{
				$result->close();
				$this->errno = CATEGORY_EXISTS;
				return ;
			}			
			
			/* gathering all keywords into one variable with # as delimiter to insert it to the database */

			$kw_length = 0; 																					//	initialize with 0 in case the keywords array hasn't been set
	
			if ( ! is_null ( $cat_info->cat_keywords ) )
				$kw_length =  count( $cat_info->cat_keywords );

			if ( $kw_length == 0 )
				$kw = NULL;
			else
				$kw = "#".$cat_info->cat_keywords[0];
			
			for ( $i = 1; $i < $kw_length; $i++ )																//	generating the keyword string which will enter to the database
				$kw = $kw."#".$cat_info->cat_keywords[$i];
			
			if ( ! ( $result = $this->con->query( "SELECT user_id FROM user WHERE username=\"".$username."\"" ) ) )		
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to query	
				return ;
			}
			
			if( $result->num_rows == 1 )
			{
				$row = $result->fetch_row();
				$user_id = $row[0];																				//	getting user id to inform other tables once the category has 
			}																									//	been created		
			else 
			{
				$result->close();
				$this->errno = USERNAME_DONT_EXIST;
				return ;
			}
		
			$query = "INSERT INTO category ( cat_name, is_open" ;												//	rows cat_name and is open should always have value
			
			/* on the other hand the following variables may have null value as its existance is optional in the database */
			if ( !is_null( $kw ) )
			{
				$query = $query.", cat_keywords";
			}
			
			if ( !is_null( $cat_info->cat_description ) )
			{
				$query = $query.", cat_description";
			}
	
			if ( !is_null( $cat_info->cat_image ) )
			{
				$query = $query.", cat_image";
			}
			
			if ( !is_null( $cat_info->cat_video ) )
			{
				$query = $query.", cat_video";
			}	
	
			$query = $query." ) VALUES ( \"$cat_info->cat_name\", $cat_info->is_open";
	
			if ( !is_null( $kw ) )
			{
				$query = $query.", \"$kw\"";
			}
			
			if ( !is_null( $cat_info->cat_description ) )
			{
				$query = $query.", \"$cat_info->cat_description\"";
			}

			if ( !is_null( $cat_info->cat_image ) )
			{
				$query = $query.", \"$cat_info->cat_image\"";
			}
			
			if ( !is_null( $cat_info->cat_video ) )
			{
				$query = $query.", \"$cat_info->cat_video\"";
			}			
			
			$query = $query." )";

			$this->con->query("START TRANSACTION;");
			
			/* inserting to the database */
			if ( !$this->con->query( $query ) )
			{
		
				$this->con->query("ROLLBACK;");
				$this->errno = MYSQL_ERROR;																		//	failed to query				
				return ;				
			}	
		
			if( $this->con->affected_rows != 1 )																//	this query should affect 1 row
			{							
				$this->con->query("ROLLBACK;");
				$this->errno = MYSQL_ERROR;																			
				return ;				
			}			
			
			/* fill the class variable with the apropriate value */		
			$this->cat_id = $this->con->insert_id;																//	storing the id in the class
		
			if ( !$this->con->query( "INSERT INTO user_has_rights VALUES ( $this->cat_id, $user_id,".MODERATOR." )" ) )
			{							
				$this->con->query("ROLLBACK;");
				$this->errno = MYSQL_ERROR;																		//	failed to query				
				return ;				
			}	

			if( $this->con->affected_rows != 1 )
			{	
				$this->con->query("ROLLBACK;");
				$this->errno = MYSQL_ERROR;																		//	this query should affect 1 row			
				return ;				
			}			

			$this->insert_attributes ( $cat_info->attr_info_array );
	
			if ( $this->errno != DB_OK )
			{
				$this->con->query("ROLLBACK;");
				return ;
			}

			$this->con->query("COMMIT;");
			
			/* initialize class's variables */
			$this->cat_name = $cat_info->cat_name;
			$this->is_open = $cat_info->is_open;
			$this->cat_description = $cat_info->cat_description;

			if ( $kw_length == 0 )
				$this->cat_keywords = NULL;
			else
				$this->cat_keywords = $cat_info->cat_keywords;
				
			$this->image = $cat_info->cat_image;
			$this->video = $cat_info->cat_video;
			
			$this->rate = 0;																					//	Dummy initial value as the aggreed default rate is 0
		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
	
		/* delete the category along with it's dependencies */
		public function delete_category()
		{
			$this->errno = DB_OK;
			
			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}			
			
			$this->con->query("START TRANSACTION;");															//	Transaction is needed despite the one query as delete is on cascade
			
			if ( ! ( $result = $this->con->query ( "DELETE FROM category WHERE cat_id=$this->cat_id" ) ) )
			{			
				$this->con->query("ROLLBACK;");
				$this->errno = MYSQL_ERROR;																		//	Failed to connect			
				return ;				
			}
			
			if( $this->con->affected_rows == 0 )
			{		
				$this->con->query("ROLLBACK;");
				$this->errno = CATEGORY_DONT_EXIST;																//	this query should affect 1 row			
				return ;				
			}	
			
			$this->con->query("COMMIT;");
			
		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/
	
		/* adding attributes along with their dependencies */
		public function add_attributes( $attr_info_array )														
		{
		
			$this->errno = DB_OK;
		
			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}		
		
			if ( count ( $attr_info_array ) == 0 )																//	at least one attribute is mandatory
				return WRONG_INPUT;		
		
			$this->con->query("START TRANSACTION;");
		
			$id = $this->insert_attributes ( $attr_info_array );														
			
			if ( $this->errno != DB_OK )
			{
				$this->con->query("ROLLBACK;");
				return ;
			}
			
			$this->con->query("COMMIT;");	

			if ( count ( $attr_info_array ) == 1 )																//	returns the first attribute's id
				return $id;
		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/				
		
		/* removing attributes along with their dependencies */
		public function remove_attributes( $id_array )		
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
			
			$id_length = 0; 
	
			if ( isset ( $id_array ) )
				$id_length = count ( $id_array );
				
			if ( $id_length == 0 )																				//	the existance of at least one attribute is mandatory
				return WRONG_INPUT;		
			
			$this->con->query("START TRANSACTION;");
			
			for ( $i = 0; $i < $id_length; $i++ )																//	filling the database attribute attribute
			{
				$attr = new attribute( $this->con, $id_array[$i], $this->cat_id );
				
				if ( ( $this->errno = $attr->get_errno() ) != DB_OK )
				{
					$this->con->query("ROLLBACK;");
					return ;
				}
				
				$attr->remove_attribute();
				
				if ( ( $this->errno = $attr->get_errno() ) != DB_OK )
				{
					$this->con->query("ROLLBACK;");
					return ;
				}
			}					
			
			$this->con->query("COMMIT;");
	
		}				
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/
	
		/* adding entities along with their dependencies */
		public function add_entities( $ent_info_array )
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}			
			
			$ent_length = 0; 
	
			if ( isset ( $ent_info_array ) )
				$ent_length = count ( $ent_info_array );
				
			if ( $ent_length == 0 )																				//	we need at least one entity to proceed
			{
				$this->errno = WRONG_INPUT;
				return ;		
			}
			
			$this->con->query("START TRANSACTION;");

			for ( $i = 0; $i < $ent_length; $i++ )																//	filling the database entity entity
			{
				$ent = new entity( $this->con, -1, $this->cat_id );
				
				if ( ( $this->errno = $ent->get_errno() ) != DB_OK )
				{
					$this->con->query("ROLLBACK;");
					return ;
				}

				$ent->add_entity ( $ent_info_array[$i], $this->cat_id );
				
				if ( ( $this->errno = $ent->get_errno() ) != DB_OK )
				{
					$this->con->query("ROLLBACK;");
					return ;
				}
			}
			
			$this->con->query("COMMIT;");				
			
		}

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/
		
		/* removing entities along with their dependencies */
		public function remove_entities( $id_array )		
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
			
			$id_length = 0; 
	
			if ( isset ( $id_array ) )
				$id_length = count ( $id_array );
				
			if ( $id_length == 0 )																				//	the existance of at least one entity id is mandatory
				return WRONG_INPUT;		
			
			$this->con->query("START TRANSACTION;");
			
			for ( $i = 0; $i < $id_length; $i++ )																//	removing entity entity
			{
				$ent = new entity( $this->con, $id_array[$i], $this->cat_id );
				
				if ( ( $this->errno = $ent->get_errno() ) != DB_OK )
				{
					$this->con->query("ROLLBACK;");
					return ;
				}
				
				$ent->remove_entity();
				
				if ( ( $this->errno = $ent->get_errno() ) != DB_OK )
				{
					$this->con->query("ROLLBACK;");
					return ;
				}
			}					
			
			$this->con->query("COMMIT;");
		}

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/
	
		/* adding keywords in the of the ones that exist */
		public function add_keywords ( $new_keywords )
		{
		
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
			
			if ( ! ( $result = $this->con->query( "SELECT cat_keywords FROM category WHERE cat_id=$this->cat_id" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;
			}
			
			if ( $result->num_rows == 1 )																		//	as the id is unique one row should have been returned
			{
				$row = $result->fetch_row();
				$kw = $row[0];
			}				
			else
			{
				$this->errno = MYSQL_ERROR;
				return ;
			}			
			
			/* gathering all keywords into one variable with # as delimiter to insert it to the database */
			$kw_length = 0; 																					//	initialize with 0 in case the keywords array hasn't been set
	
			if ( isset ( $new_keywords ) )
				$kw_length =  count( $new_keywords );

			if ( $kw_length == 0 )
			{
				$this->errno = WRONG_INPUT;
				return ;		
			}
			
			for ( $i = 0; $i < $kw_length; $i++ )																//	generating the keyword string which will enter to the database
				$kw = $kw."#".$new_keywords[$i];			
			
			if( !$this->con->query( "UPDATE category SET cat_keywords=\"$kw\" WHERE cat_id=$this->cat_id;" ) )			
			{
				$this->errno = MYSQL_ERROR;					
				return ;																						//	Failed to query				
			}

			$this->cat_keywords = NULL;
			
			$tok = strtok( $kw, "#" );																			//	turning the keywords string into an array
			
			while ( $tok !== false ) 
			{
				$this->cat_keywords  [] = $tok;
				$tok = strtok( "#" );
			}			
	
		}	
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF CREATE/DELETE  FUNCTIONS
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
	
		public function get_id()
		{
			$this->errno = DB_OK;
			
			return $this->cat_id;
		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
	
		public function get_id_by_name( $cat_name )
		{
			$this->errno = DB_OK;

			if ( !( $result = $this->con->query( "SELECT cat_id FROM category WHERE cat_name=\"$cat_name\"" ) ) )	//	figuring out if category with such name exists
			{
				$this->errno = MYSQL_ERROR;
				return ;
			}
	
			if ( $result->num_rows == 1 )
			{
				$row = $result->fetch_row();
				return $row[0];		
			}
			else																								//	if category doesn't exist error code should be returned
			{
				$result->close();
				$this->errno = CATEGORY_DONT_EXIST;
				return ;
			}

		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/			
		
		public function get_ent_id_by_name( $ent_name )
		{
			$this->errno = DB_OK;

			$entity = new entity( $this->con, -1, $this->cat_id );											

			if ( ( $this->errno = $entity->get_errno() ) != DB_OK )
			{
				return ;
			}	

			$ent_id = $entity->get_id_by_name( $ent_name, $this->cat_id );

			if ( ( $this->errno = $entity->get_errno() ) != DB_OK )
			{
				return ;
			}			

			return $ent_id;
		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/			
		
		public function get_name()
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
			
			return $this->cat_name;
		}	
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	

		public function get_description()
		{
			$this->errno = DB_OK;		

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
			
			return $this->cat_description;
		}

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
	
		public function get_keywords()
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
			
			return $this->cat_keywords;
		}

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
		public function is_open()
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}

			return $this->is_open;
		}
	
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	

	
		public function get_rating()
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
		
			return $this->rate;
		}	
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
	
		public function get_image()
		{

			$this->errno = DB_OK;		

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
	
			return $this->image;
		}	

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/
	
		public function get_video()
		{

			$this->errno = DB_OK;	

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}

			return $this->video;
		}

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/		
		
		public function get_info()
		{
		
			$this->errno = DB_OK;	

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}			
			
			$cat_info = new category_info();
			
			$cat_info->cat_name = $this->cat_name;
			$cat_info->is_open = $this->is_open;
			$cat_info->cat_description = $this->cat_description;
			$cat_info->cat_keywords = $this->cat_keywords;
			$cat_info->image = $this->image;
			$cat_info->video = $this->video;
			$cat_info->rate = $this->rate;

			$cat_info->attr_info_array = $this->get_attributes( 0 );											//	getting all category's attributes
			
			if ( $this->errno != DB_OK )
			{
				return;			
			}
			return $cat_info;
		}
		

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	

		public function get_specific_attribute( $attr_id )
		{

			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
			
			if ( ! ( $result = $this->con->query( "SELECT * FROM attribute WHERE cat_id = $this->cat_id AND attr_id = $attr_id" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;
			}			

			if ( $result->num_rows == 0 )																		//	there is no such attribute in this category
			{
				$this->errno = WRONG_ID;																		//	Failed to connect	
				return ;
			}	
			
			$attribute = new attribute( $this->con, $attr_id, $this->cat_id );									//	creating an attribute object
			
			if ( ( $err = $attribute->get_errno() ) != DB_OK )
			{
				$this->errno = $err;
				return ;
			}			
			
			$attribute_info = $attribute->get_info();															//	getting attribute's info
			
			if ( ( $err = $attribute->get_errno() ) != DB_OK )
			{
				$this->errno = $err;
				return ;
			}	
			
			return $attribute_info;
		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/
	
		public function get_attributes( $to_compare )
		{

			$this->errno = DB_OK;
            $attr_info_array = array();
			
			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
				
			if ( $to_compare != 1 && $to_compare != 0 ) 
			{
				$this->errno = WRONG_INPUT;
				return ;
			}
			
			if ( $to_compare == 0 )																				//	if the upper level wants uncomparable attributes as well
			{																									//	then the to_compare will be set to 0
				if ( ! ( $result = $this->con->query( "SELECT * FROM attribute WHERE cat_id=$this->cat_id AND comparability=".UNCOMPARABLE."" ) ) )
				{
					$this->errno = MYSQL_ERROR;																	//	Failed to connect	
					return ;	
				}			

				while ( $row = $result->fetch_row() )															//	initialize the structure with the uncomparable attributes
				{																								//	received from the database
					$attribute_info = new attribute_info();
					$attribute_info->id = $row[0];	
					$attribute_info->name = $row[2];
					$attribute_info->comparability = $row[3];
					$attribute_info->is_filterable = $row[4];
					$attribute_info->description = $row[5];
					$attribute_info->type_elements[] = NULL;
					$attribute_info->default_weight = $row[6];
					
					$attr_info_array[] = $attribute_info;														//	filling the array with attributes
				}		
			}

			if ( ! ( $result = $this->con->query( "SELECT a.attr_name, a.comparability, a.filterable, a.attr_description, ic.*, a.default_weight FROM attribute a, is_countable ic WHERE ic.attr_id=a.attr_id AND cat_id=$this->cat_id AND comparability=".COUNTABLE."" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;	
			}			

			while ( $row = $result->fetch_row() )																//	initialize the structure with the countable attributes
				{																								//	received from the database
				$attribute_info = new attribute_info();
				$attribute_info->id = $row[4];	
				$attribute_info->name = $row[0];
				$attribute_info->comparability = $row[1];
				$attribute_info->is_filterable = $row[2];
				$attribute_info->description = $row[3];
				$attribute_info->type_elements = array ( "Upper Limit" =>$row[5], "Lower Limit"=>$row[6], "Comparison Type"=>$row[7] );	
				$attribute_info->default_weight = $row[8];
				
				$attr_info_array[] = $attribute_info;															//	filling the array with attributes
			}

			if ( ! ( $result = $this->con->query( "SELECT a.*, id.value_name, id.preference FROM attribute a, is_distinct id WHERE cat_id=$this->cat_id AND comparability=".DISTINCT." AND a.attr_id=id.attr_id ORDER BY attr_id" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;	
			}			

			if ( $row = $result->fetch_row() )
			{
				while ( $row )																					//	initialize the structure with the distinct attributes
				{																								//	received from the database	
					$attribute_info = new attribute_info();
					$attribute_info->id = $row[0];	
					$attribute_info->name = $row[2];
					$attribute_info->comparability = $row[3];
					$attribute_info->is_filterable = $row[4];
					$attribute_info->description = $row[5];
					$attribute_info->default_weight = $row[6];
					$attribute_info->type_elements[] =  array( "Preference"=>$row[8], "Value"=>$row[7] );

					while ( $row = $result->fetch_row() )
					{
						if ( $row[0] != $attribute_info->id )
							break;
						
						$attribute_info->type_elements[] =  array( "Preference"=>$row[8], "Value"=>$row[7] );
					}
					
					$attr_info_array[] = $attribute_info;														//	filling the array with attributes
				}	
			}
			
			return $attr_info_array;
		}		

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/			
		
		public function get_N_entities( $from, $to, $sort_field, $sort_type, $filtered, $attribute_id )
		{
			$this->errno = DB_OK;
			
			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}	
			
			if ( !strcmp( $sort_type, "ASC") && !strcmp( $sort_type, "DESC") )									//	the sort_type should be either ASC(ascending) or DEsc(Descending)
			{
				$this->errno = WRONG_INPUT;
				return ;
			}
			
			if ( $sort_field < 0 || $sort_field > 3 )															//	0, 1, 2, 3 stands for ordering by id, name, popoularity  
			{																									//	and specific countable attribute
				$this->errno = WRONG_INPUT;
				return ;
			}			
			
			if ( $sort_field == 3)																				//	on sorting by attribute we need the countable values of the entities
				$query = "( SELECT e.*,ehv.c_value FROM entity e"; 
			else
				$query = "( SELECT e.* FROM entity e"; 
		
			if ( !is_null( $filtered ) )																		//	if filtering on the results is needed the array $filtered
			{																									// 	will contain the filters
				$query .= ", entity_has_value ehv "; 															//	on filtering we need the entity_has_value table
				$count_filtered = count ( $filtered );
				
				for ( $i = 0; $i < count ( $filtered ); $i++ )													//	form the where quyries aplying the filters				
				{	

					$query_where[] = " AND ehv.ent_id = e.ent_id ";	
					
					if ( $filtered[$i]["Comp"] == COUNTABLE )													//	if attribute is countable
					{
						$spec_value = "c_";				
					}
					else if ( $filtered[$i]["Comp"] == DISTINCT )												//	if distinct
					{
						$spec_value = "d_";
					}
					else 
					{
						$spec_value = "u_";
					}
				
					$query_where[$i] .= " AND ehv.attr_id = ".$filtered[$i]["Id"];
					
					$temp ="  AND (";
					$l = 0;
					$j = 2; 																					//	definetely there should be an id an the comparability
					
					while ( $j < count ( $filtered[$i] ) )
					{						
						$j += 2;
						$query_where[$i] .= $temp;
						$query_where[$i] .=" ( ehv.".$spec_value."value ".$filtered[$i]["Op0.".$l.""]." \"".$filtered[$i]["Value0.".$l.""]."\"";
						
						if ( $filtered[$i]["Comp"] == COUNTABLE )
						{	
							$j += 2;
							$query_where[$i] .= " AND ehv.".$spec_value."value ".$filtered[$i]["Op1.".$l.""]." \"".$filtered[$i]["Value1.".$l.""]."\" )";	
						}
						else
						{
							$query_where[$i] .= " )";
						}
						
						$temp = " OR ";
						$l++;
					}
					
					$query_where[$i] .= " )";
				}
			}
			else
			{
				$query_where[] = " ";
				$count_filtered = 0;				
			}
		
			$query_attr = " ";
			
			if ( $sort_field == 3 )
			{

				$c = count($query_where) - 1;
			
				if ( is_null( $filtered ) )
				{
					$query .= ", entity_has_value ehv ";
			
					$query_where[] = " AND fq".$c.".ent_id = ehv.ent_id AND ehv.ent_id = e.ent_id AND ehv.attr_id = ".$attribute_id." ";	
				}	
				else
					$query_where[] = " AND fq".$c.".ent_id = ehv.ent_id AND ehv.attr_id = ".$attribute_id." ";
	
			}
			
			$final_query = $query." WHERE e.cat_id=$this->cat_id ".$query_where[0]."".$query_attr." GROUP BY e.ent_id )";

			for ( $i = 1, $j =0; $i < count($query_where); $i++, $j++ )											//	forming the query and we have as many nested selects
			{																									//	as the filters are
				$query_where[$i] .= " AND fq".$j.".ent_id = e.ent_id";
				$final_query = $query.", ".$final_query." as fq".$j." WHERE e.cat_id=$this->cat_id ".$query_where[$i]."".$query_attr."  GROUP BY e.ent_id )";
			}				

			if ( $sort_field == 1 )																				//	Sort by name
			{
				if ( $to != -1 )																				
				{
					if ( ! ( $result = $this->con->query( "SELECT fq.* FROM ".$final_query." as fq GROUP BY fq.ent_id ORDER BY fq.ent_name $sort_type LIMIT $to" ) ) )
					{
						$this->errno = MYSQL_ERROR;																//	Failed to connect	
						return ;	
					}	
				}
				else
				{       
					if ( ! ( $result = $this->con->query( "SELECT fq.* FROM ".$final_query." as fq GROUP BY fq.ent_id ORDER BY fq.ent_name $sort_type") ) )
					{
						$this->errno = MYSQL_ERROR;																//	Failed to connect	
						return ;
					}	
				}
				
			}
			else if ( $sort_field == 2 )																		//	Sort by rate
			{
				if ( $to != -1 )
				{				
					if ( ! ( $result = $this->con->query( "SELECT fq.* FROM ".$final_query." as fq GROUP BY fq.ent_id ORDER BY fq.ent_rate $sort_type LIMIT $to " ) ) )
					{
						$this->errno = MYSQL_ERROR;																//	Failed to connect	
						return ;	
					}
				}
				else
				{
					if ( ! ( $result = $this->con->query( "SELECT fq.* FROM ".$final_query." as fq GROUP BY fq.ent_id ORDER BY fq.ent_rate $sort_type" ) ) )
					{
						$this->errno = MYSQL_ERROR;																//	Failed to connect	
						return ;	
					}
				}					
			}
			else if ( $sort_field == 3 )																		//	Sort by countable attribute value
			{

				if ( $to != -1 )
				{		

					if ( ! ( $result = $this->con->query( "SELECT fq.* FROM ".$final_query." as fq GROUP BY fq.ent_id ORDER BY fq.c_value $sort_type LIMIT $to " ) ) )
					{
						$this->errno = MYSQL_ERROR;																//	Failed to connect	
						return ;	
					}
				}
				else
				{		
					if ( ! ( $result = $this->con->query( "SELECT fq.* FROM ".$final_query." as fq GROUP BY fq.ent_id ORDER BY fq.c_value $sort_type ") ) )
					{
						$this->errno = MYSQL_ERROR;																//	Failed to connect	
						return ;	
					}
				}	
			
			}
			else																								//	Default sort by id
			{
				if ( $to != -1 )
				{
					if ( ! ( $result = $this->con->query( "SELECT fq.* FROM ".$final_query." as fq GROUP BY fq.ent_id $sort_type LIMIT $to " ) ) )
					{
						$this->errno = MYSQL_ERROR;																//	Failed to connect	
						return ;	
					}
				}
				else
				{			
					if ( ! ( $result = $this->con->query( "SELECT fq.* FROM ".$final_query." as fq GROUP BY fq.ent_id $sort_type "  ) ) )
					{
						$this->errno = MYSQL_ERROR;																//	Failed to connect	
						return ;	
					}
				}				
			}
		
			$counter = 1;
			
			while ( $counter < $from )																			//	looping until we reach the first desirable record
			{
				$counter++;
				$result->fetch_row();
			}
			
			$ent_info_array = array();
			
			while ( $row = $result->fetch_row() )																//	initialize the structure with the data received from the database
			{	

				$entity = new entity( $this->con, $row[0], $this->cat_id );

				if ( ( $this->errno = $entity->get_errno() ) != DB_OK )
				{
					return ;
				}
				
				$entity_info = new entity_info();
				$entity_info->entity_id = $row[0];	
				$entity_info->entity_name = $row[2];
				$entity_info->entity_description = $row[3];
				$entity_info->entity_image = $row[4];
				$entity_info->entity_video = $row[5];
				$entity_info->rate = $row[6];
				$entity_info->entity_attribute_values = $entity->get_attribute_values_with_name();

				if ( ( $this->errno = $entity->get_errno() ) != DB_OK )
				{
					return ;
				}
				
				$ent_info_array[] = $entity_info;																//	filling the array with entities

			}		
 
			return $ent_info_array;
			
		}		

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/			

		public function get_specific_entity( $ent_id )
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
			
			$entity = new entity( $this->con, $ent_id, $this->cat_id );
			
			if ( ( $err = $entity->get_errno() ) != DB_OK )
			{
				$this->errno = $err;
				return ;
			}
			
			$entity_info = new entity_info();
			$entity_info = $entity->get_info();																	//	getting entity's info
			
			if ( ( $err = $entity->get_errno() ) != DB_OK )
			{
				$this->errno = $err;
				return ;
			}	

			return $entity_info;
		}	
	
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
	
		public function get_most_popular_entities( $limit )
		{
			$this->errno = DB_OK;
			$popular_entities = array();
			
			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
			
			if ( ! ( $result = $this->con->query( "SELECT e.ent_name, e.ent_id FROM entity e WHERE e.cat_id = $this->cat_id ORDER BY ent_rate DESC LIMIT $limit" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;
			}			
			
			while ( $row = $result->fetch_row() )																//	initialize the structure with the data received from the database
			{		
				$popular_entities[] = array("Name"=>$row[0],"Id"=>$row[1]);										// filling the array with popular entities
			}

			return $popular_entities;
		}
	
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
	
		public function get_number_of_entities()
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
		
			if ( ! ( $result = $this->con->query( "SELECT count(*) FROM entity WHERE cat_id=$this->cat_id" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;	
			}
		
			if ( $result->num_rows == 1 )																		//	as we asking for the number of entities one row should 
			{																									//	have been returned
				$row = $result->fetch_row();
				$num_of_entities = $row[0];
			}				
			else
			{
				$result->close();
				$this->errno = MYSQL_ERROR;
				return ;
			}			
		
			return $num_of_entities;
		}

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	

		public function get_prev_entity_id( $current_id )
		{
		
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}		

			if ( ! ( $result = $this->con->query( "SELECT ent_id FROM entity WHERE cat_id=$this->cat_id AND ent_id<".$current_id." GROUP BY ent_id DESC  LIMIT 1" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;	
			}
			
			if ( $result->num_rows == 1 )																		//	we just found our previous id
			{																									
				$row = $result->fetch_row();
				$previous_id = $row[0];
			}				
			else																								//	in this occasion the current_id is the first id of the category
			{																									//	thus as a previous id we have to return the last id of the category
				$result->close();
				
				if ( ! ( $result = $this->con->query( "SELECT ent_id FROM entity WHERE cat_id=$this->cat_id GROUP BY ent_id DESC LIMIT 1" ) ) )
				{
					$this->errno = MYSQL_ERROR;																	//	Failed to connect	
					return ;	
				}				

				if ( $result->num_rows == 1 )																	//	we just found our previous id
				{																									
					$row = $result->fetch_row();
					$previous_id = $row[0];
				}
				else
				{
					$result->close();
					$this->errno = MYSQL_ERROR;																	//	There is no way that no id is returned as we know that 
					return ;																					//	current id exists
				}				
				
				
			}	

			$result->close();
			return $previous_id;
		}

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	

		public function get_next_entity_id( $current_id )
		{
		
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}		

			if ( ! ( $result = $this->con->query( "SELECT ent_id FROM entity WHERE cat_id=$this->cat_id AND ent_id>".$current_id." LIMIT 1" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;	
			}
			
			if ( $result->num_rows == 1 )																		//	we just found our previous id
			{																									
				$row = $result->fetch_row();
				$next_id = $row[0];
			}				
			else																								//	in this occasion the current_id is the first id of the category
			{																									//	thus as a previous id we have to return the last id of the category
				$result->close();
				
				if ( ! ( $result = $this->con->query( "SELECT ent_id FROM entity WHERE cat_id=$this->cat_id LIMIT 1" ) ) )
				{
					$this->errno = MYSQL_ERROR;																	//	Failed to connect	
					return ;	
				}				

				if ( $result->num_rows == 1 )																	//	we just found our previous id
				{																								
					$row = $result->fetch_row();
					$next_id = $row[0];
				}
				else
				{
					$result->close();
					$this->errno = MYSQL_ERROR;																	//	There is no way that no id is returned as we know that 
					return ;																					//	current id exists
				}				
				
				
			}	

			$result->close();
			return $next_id;
		}

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
		public function get_all_entities_Ids()
		{
			$this->errno = DB_OK;
			$entities_list = array();
			
			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}

			if ( ! ( $result = $this->con->query( "SELECT ent_id FROM entity WHERE cat_id=$this->cat_id" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;
			} 
			
			while ( $row = $result->fetch_row() )																//	initialize the structure with the data received from the database
			{
				$entities_list[] = $row[0];
			}
			
			return $entities_list;
		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/		
		public function get_privileged_users( $privileges )
		{		
			$this->errno = DB_OK;
            $privileged_users = array();
			
			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}		
			
			if ( ! ( $result = $this->con->query( "SELECT u.username FROM user_has_rights uhr, user u WHERE uhr.level_of_rights=$privileges AND uhr.cat_id=$this->cat_id AND uhr.user_id = u.user_id" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;	
			}			

			while ( $row = $result->fetch_row() )
			{
				$privileged_users[] = $row[0];
			}
			
			return $privileged_users;
		}

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/		
	
		public function get_user_rights( $username )			
		{
			$this->errno = DB_OK;

			$user = new user( $username , LOGGED_IN );

			if ( ( $priv = $user->get_privileges() ) == NOT_LOGGED_IN )											//	if user is not logged in he definetely doesn't have 
			{																									//	privileges to create category
				$this->errno = NOT_LOGGED_IN;		
				return ;
			}

			if ( $this->cat_id == -1 )
			{
				$this->errno = WRONG_ID;
				return;
			}

			if ( $priv != ADMINISTRATOR )																		//	if user is an Administrator he can change anynithg in any category
			{																									//	if not a check is performed to the user's pivileges on the category	

				if ( ! ( $result = $this->con->query( "SELECT level_of_rights FROM user_has_rights WHERE cat_id=$this->cat_id AND user_id=".$user->get_id() ) ) )
				{
					$this->errno = MYSQL_ERROR;																	//	Failed to connect					
					return ;
				}
				
				if ( $result->num_rows == 0 )																	
				{
                    return NULL;
				}
				else if ( $result->num_rows == 1 )
				{
					$row = $result->fetch_row();
					return $row[0];
				}
				else
				{
					$result->close();
					$this->errno = MYSQL_ERROR;
					return ;																									
				}

			}
			else 
				return LOGIC_ADMINISTRATOR;
			
		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
	    public function get_pending_members()
		{
			$this->errno = DB_OK;
			$usernames_list = array();
			
			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}

			if ( ! ( $result = $this->con->query( "SELECT u.username from user u,pending_members pm where pm.user_id=u.user_id and pm.cat_id=".$this->cat_id.";") ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;
			}  
			
			while ( $row = $result->fetch_row() )																//	initialize the structure with the data received from the database
			{
				$usernames_list[] = $row[0];
			}
			
			return $usernames_list;    
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

		public function set_name( $new_name )
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}	
			
			if( !$this->con->query( "UPDATE category SET cat_name=\"$new_name\" WHERE cat_id=$this->cat_id;" ) )			
			{
				$this->errno = MYSQL_ERROR;					
				return ;																						//	Failed to query				
			}

			$this->cat_name = $new_name;			
			
		}
	
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
		public function set_description( $new_description )
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}	
			
			if ( ! is_null( $new_description ) )
			{
			
				if( !$this->con->query( "UPDATE category SET cat_description=\"$new_description\" WHERE cat_id=$this->cat_id;" ) )			
				{
					$this->errno = MYSQL_ERROR;					
					return ;																					//	Failed to query				
				}
				$this->cat_description = $new_description;
			}
			else
			{
			
				if( !$this->con->query( "UPDATE category SET cat_description=NULL WHERE cat_id=$this->cat_id;" ) )			
				{
					$this->errno = MYSQL_ERROR;					
					return ;																					//	Failed to query				
				}
				$this->cat_description = NULL;
			}	

		}
	
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
		public function set_keywords( $new_keywords )
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}		
			
			/* gathering all keywords into one variable with # as delimiter to insert it to the database */
			$kw_length = 0; 																					//	initialize with 0 in case the keywords array hasn't been set
	
			if ( isset ( $new_keywords ) )
				$kw_length =  count( $new_keywords );

			if ( $kw_length == 0 )
				$kw = NULL;
			else
				$kw ="#".$new_keywords[0];
			
			for ( $i = 1; $i < $kw_length; $i++ )																//	generating the keyword string which will enter to the database
				$kw = $kw."#".$new_keywords[$i];			
			
			if ( ! is_null( $kw ) )
			{
			
				if( !$this->con->query( "UPDATE category SET cat_keywords=\"$kw\" WHERE cat_id=$this->cat_id;" ) )			
				{
					$this->errno = MYSQL_ERROR;					
					return ;																					//	Failed to query				
				}
				$this->cat_keywords = $new_keywords;
			}
			else
			{
			
				if( !$this->con->query( "UPDATE category SET cat_keywords=NULL WHERE cat_id=$this->cat_id;" ) )			
				{
					$this->errno = MYSQL_ERROR;					
					return ;																					//	Failed to query				
				}
				$this->cat_keywords = NULL;
			}
		}
	
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
		public function set_openness( $new_openness )
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}	

			$this->con->query("START TRANSACTION;");
			
			/*  Change the openness. If the category changed from close to open its category members shall be erased from the database  */			
			if ( ! ( $result = $this->con->query ( "DELETE FROM user_has_rights WHERE cat_id=$this->cat_id AND level_of_rights=0 " ) ) )
			{		
				$this->con->query("ROLLBACK;");
				$this->errno = MYSQL_ERROR;																		//	Failed to connect			
				return ;				
			}
			
			if( !$this->con->query( "UPDATE category SET is_open=\"$new_openness\" WHERE cat_id=$this->cat_id;" ) )			
			{
				$this->con->query("ROLLBACK;");
				$this->errno = MYSQL_ERROR;					
				return ;																						//	Failed to query				
			}
			
			$this->con->query("COMMIT;");
			$this->is_open = $new_openness;
		}
	
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
		public function set_image( $new_image )
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}	

			if ( ! is_null( $new_image ) )
			{
			
				if( !$this->con->query( "UPDATE category SET cat_image=\"$new_image\" WHERE cat_id=$this->cat_id;" ) )			
				{
					$this->errno = MYSQL_ERROR;					
					return ;																					//	Failed to query				
				}
				$this->image = $new_image;
			}
			else
			{
			
				if( !$this->con->query( "UPDATE category SET cat_image=NULL WHERE cat_id=$this->cat_id;" ) )			
				{
					$this->errno = MYSQL_ERROR;					
					return ;																					//	Failed to query				
				}
				$this->image = NULL;
			}
			
		}
	
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
		public function set_video( $new_video )
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}	

			if ( ! is_null( $new_video ) )
			{
			
				if( !$this->con->query( "UPDATE category SET cat_video=\"$new_video\" WHERE cat_id=$this->cat_id;" ) )			
				{
					$this->errno = MYSQL_ERROR;					
					return ;																					//	Failed to query				
				}
				$this->video = $new_video;
			}
			else
			{
			
				if( !$this->con->query( "UPDATE category SET cat_video=NULL WHERE cat_id=$this->cat_id;" ) )			
				{
					$this->errno = MYSQL_ERROR;					
					return ;																					//	Failed to query				
				}
				$this->video = NULL;
			}		

		}
	
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
		public function set_specific_attribute( $attr_info )
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}	

			if ( !isset ( $attr_info->id ) )																	//	attribute's id needed
			{
				$this->errno = WRONG_ID;
				return ;
			}
		
			$attr = new attribute ( $this->con, $attr_info->id, $this->cat_id );
			
			if ( ( $this->errno = $attr->get_errno() ) != DB_OK )
			{
				return ;
			}					

			$this->con->query("START TRANSACTION;");
			
			$attr->set_attribute( $attr_info );
	
			if ( ( $this->errno = $attr->get_errno() ) != DB_OK )
			{
				$this->con->query("ROLLBACK;");
				return ;
			}		
		
			$this->con->query("COMMIT;");
		}
	
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		
		public function set_specific_entity( $ent_info )
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}	
	
			if ( !isset ( $ent_info->entity_id ) )																//	entity's id needed
			{
				$this->errno = WRONG_ID;
				return ;
			}
	
			$entity = new entity ( $this->con, $ent_info->entity_id, $this->cat_id );
			
			if ( ( $this->errno = $entity->get_errno() ) != DB_OK )
			{
				return ;
			}	
			
			$this->con->query("START TRANSACTION;");
			$entity->set_entity ( $ent_info );
	
			if ( ( $this->errno = $entity->get_errno() ) != DB_OK )
			{
                $this->con->query("ROLLBACK;");
				return ;
			}
			
            $this->con->query("COMMIT;");
			
		}	
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	

		public function set_priviliges ( $username, $new_privileges )
		{
			$this->errno = DB_OK;
			
			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}
			
			$user = new user( $username , LOGGED_IN );			
			
			if ( $new_privileges == NO_RIGHTS_UPON_CATEGORY )
			{
				if( !$this->con->query( "DELETE FROM user_has_rights WHERE cat_id=$this->cat_id AND user_id=".$user->get_id() ) )			
				{
					$this->errno = MYSQL_ERROR;					
					return ;																					//	Failed to query				
				}	
			}
			else
			{

				if( !$this->con->query( "UPDATE user_has_rights SET level_of_rights=$new_privileges WHERE cat_id=$this->cat_id AND user_id=".$user->get_id() ) )			
				{
					$this->errno = MYSQL_ERROR;					
					return ;																					//	Failed to query				
				}

				if( $this->con->affected_rows == 0 )															//	that means tha the user wasn't on the table for this category
				{		
					if( !$this->con->query( "INSERT INTO user_has_rights VALUES ( $this->cat_id, ".$user->get_id().",".$new_privileges." )" ) )		
					{
						$this->errno = MYSQL_ERROR;					
						return ;																				//	Failed to query				
					}				
				}
			}
			
		}	
		
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF MUTATORS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/
	
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	FILTER FUNCTIONS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/		


		public function get_specific_filter( $filter_id, $number_of_values, $comparability )					// filter id is an attribute id
		{
			$this->errno = DB_OK;
			$values = array();
			
			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}			
	
			$attr = new attribute( $this->con, $filter_id, $this->cat_id );										//	creating an attribute object using the filter_id
				
			if ( ( $this->errno = $attr->get_errno() ) != DB_OK )
			{
				return ;
			}		

			$is_filterable = $attr->is_filterable();
			
			if ( ( $this->errno = $attr->get_errno() ) != DB_OK )
			{
				return ;
			}

			if ( $is_filterable == 0 )																			//	checking filterability
			{
				$this->errno = ATTRIBUTE_NOT_FILTERABLE;
				return ;
			}

			if ( $comparability == COUNTABLE )																	//	if attribute is countable
			{
				$spec_value = "c_";
			}
			else if ( $comparability == DISTINCT )
			{
				$spec_value = "d_";
			}
			else 
			{
				$spec_value = "u_";
			}
			
			
			if ( $number_of_values > 0 )
			{

				/*	Locating and retrieving sorted the $number_of_values most common values for this attribute	*/
				
				if ( $comparability == COUNTABLE )																//	if attribute is countable
				{
					if ( ! ( $result = $this->con->query( "SELECT ".$spec_value."value, COUNT(*) FROM entity_has_value WHERE attr_id = $filter_id GROUP BY ".$spec_value."value ORDER BY ".$spec_value."value LIMIT $number_of_values" ) ) )
					{
						$this->errno = MYSQL_ERROR;																//	Failed to connect	
						return ;	
					}				
				
				}
				else
				{
					if ( ! ( $result = $this->con->query( "SELECT ".$spec_value."value, COUNT(*) FROM entity_has_value WHERE attr_id = $filter_id GROUP BY ".$spec_value."value ORDER BY count(*) DESC LIMIT $number_of_values" ) ) )
					{
						$this->errno = MYSQL_ERROR;																//	Failed to connect	
						return ;	
					}
				
				}

				if ( $result->num_rows > $number_of_values )
				{
				
					$this->errno = MYSQL_ERROR;																	//	Failed to connect	
					return ;	
				}					
				
			}
			else if ( $number_of_values == -1 )
			{
				/*	Locating and retrieving sorted all the values for this attribute	*/
				
				if ( $comparability == COUNTABLE )																//	if attribute is countable
				{	
					if ( ! ( $result = $this->con->query( "SELECT ".$spec_value."value, COUNT(*) FROM entity_has_value WHERE attr_id = $filter_id GROUP BY ".$spec_value."value ORDER BY ".$spec_value."value" ) ) )
					{			
						$this->errno = MYSQL_ERROR;																//	Failed to connect	
						return ;	
					}				
				}
				else
				{
				
					if ( ! ( $result = $this->con->query( "SELECT ".$spec_value."value, COUNT(*) FROM entity_has_value WHERE attr_id = $filter_id GROUP BY ".$spec_value."value ORDER BY count(*) DESC" ) ) )
					{			
						$this->errno = MYSQL_ERROR;																//	Failed to connect	
						return ;	
					}
				}
			}
			else 
			{
				$this->errno = WRONG_INPUT;	
				return ;	
			}			

			while ( $row = $result->fetch_row() )
			{
				$values[] = array("value" =>$row[0], "count"=>$row[1]);
			}					
			
			return $values;
			
		}			

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/
	
		public function get_filters( $number_of_filters )
		{
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}		
			
			$filters = array();
			
			if ( $number_of_filters > 0 )
			{	
			
				/*  Retrieve filter information	for the first $number_of_filters filters  */ 
				if ( ! ( $result = $this->con->query( "SELECT attr_id, attr_name, comparability FROM attribute WHERE cat_id = $this->cat_id AND filterable = 1 LIMIT $number_of_filters" ) ) )
				{
					$this->errno = MYSQL_ERROR;																	//	Failed to connect	
					return ;	
				}

				if ( $result->num_rows > $number_of_filters )
				{
					$this->errno = MYSQL_ERROR;																	//	Failed to connect	
					return ;	
				}	
				
			}
			else if ( $number_of_filters == -1 )
			{
				/*	Retrieve filter information	*/
				if ( ! ( $result = $this->con->query( "SELECT attr_id, attr_name, comparability FROM attribute WHERE cat_id = $this->cat_id AND filterable = 1" ) ) )
				{
					$this->errno = MYSQL_ERROR;																	//	Failed to connect	
					return ;	
				}			
			}			
			else 
			{
				$this->errno = WRONG_INPUT;	
				return ;	
			}
				
			while ( $row = $result->fetch_row() )
			{	
				$filters[] = array( "ID" => $row[0], "NAME" => $row[1], "COMPARABILITY" => $row[2] );
			}				
			
			return $filters;
		}

		
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF FILTER FUNCTIONS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/			
		
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	USEFULL FUNCTIONS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/		
		
		/*	This function checks if the user has equal or greater privileges which required for the action	*/
		public function can_access( $username, $lower_privileges )			
		{
			$this->errno = DB_OK;

			$user = new user( $username , LOGGED_IN );

			if ( ( $priv = $user->get_privileges() ) == NOT_LOGGED_IN )											//	if user is not logged in he definetely doesn't have 
			{																									//	privileges to create category
				$this->errno = NOT_LOGGED_IN;		
				return ;
			}

			if ( $priv != ADMINISTRATOR )																		//	if user is an Administrator he can change the name of the category
			{																									//	if not a check is performed to the user's pivileges on any category	

				if ( ! ( $result = $this->con->query( "SELECT level_of_rights FROM user_has_rights WHERE cat_id=$this->cat_id AND user_id=".$user->get_id() ) ) )
				{
					$this->errno = MYSQL_ERROR;																	//	Failed to connect					
					return ;
				}

				if ( $result->num_rows == 0 )																	
				{
					if ( $this->cat_id != -1 )
					{
						if ( $this->is_open() == OPEN )															//	all registered users should considered category members for all
						{																						//	open categories even if they are not on the table user_has_rights
							$level_of_rights = CATEGORY_MEMBER;
						}
						else
						{
							$result->close();
							$this->errno = INSUFFICIENT_RIGHTS;
							return ;							
						}
					}
					else
						$level_of_rights = CATEGORY_MEMBER;
				}
				else if ( $result->num_rows == 1 )
				{
					$row = $result->fetch_row();
					$level_of_rights = $row[0];
				}
				else
				{
					$result->close();
					$this->errno = MYSQL_ERROR;
					return ;																					//	it's ipossible to return > 1 rows						
				}
				
				if ( $level_of_rights < $lower_privileges )														//	has the user enough privileges?
				{
					$this->errno = INSUFFICIENT_RIGHTS;
					return ;				
				}
			}		
			
		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/

		public function category_exists( $name )
		{
		
			$this->errno = DB_OK;
		
			if ( ! ( $result = $this->con->query( "SELECT * FROM category WHERE cat_name=\"$name\"" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;
			}			

			if ( $result->num_rows == 0 )																		//	if nothing is found to the database
			{
				return CATEGORY_DONT_EXIST;																		//	category doesn't exist
			}			
			else																								//	else
				return CATEGORY_EXISTS;																			//	category exists
			
		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/

		public function has_entity_been_rated ( $ent_id, $username )
		{
		
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}			
		
			$entity = new entity( $this->con, $ent_id, $this->cat_id );
		
			$has_been_rated = $entity->has_user_rated( $username );
		
			if ( ( $this->errno = $entity->get_errno() ) != DB_OK )
			{
				return ;
			}
		
			return $has_been_rated;
		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
	
		public function has_category_been_rated ( $username )
		{
		
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}			
		
			$user = new user( $username , LOGGED_IN );		
			
			if ( ! ( $result = $this->con->query( "SELECT rate FROM user_rates_category WHERE user_id=".$user->get_id()." AND cat_id=$this->cat_id" ) ) )
			{
				$this->errno = MYSQL_ERROR;																		//	Failed to connect	
				return ;	
			}					
		
			if ( $result->num_rows >= 1 )																		//	if the category has been rated at least one user 
			{																									//	will have done it
				$result->close();	
				return 1;
			}				
			else
			{
				$result->close();
				return 0;
			}		

		}
		
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/
	
		public function check_ent_id_validity ( $ent_id )
		{
		
			$this->errno = DB_OK;

			if ( $this->cat_id == -1 )																			//	Such category hasn't been created
			{
				$this->errno = WRONG_ID;
				return ;
			}		

			$entity = new entity( $this->con, $ent_id, $this->cat_id );

			$returned_value = $entity->get_errno();
			
			if ( $returned_value == DB_OK )
			{
				return 1;
			}	
			else if ( $returned_value == ENTITY_DONT_EXIST ) 
			{
				return 0;
			}
			else
			{
				$this->ernno = $returned_value; 
				return 0;
			}
		}
	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
		private function insert_attributes( $attr_info_array )													//	this function was created to help the use of transactions 
		{																										//	as it is masked through add attributes to the upper level
																												//	and no transactions take place here
			$this->errno = DB_OK;
	
			$attr_length = 0; 
			
			if ( isset ( $attr_info_array ) )
				$attr_length = count ( $attr_info_array );
				
			if ( $attr_length == 0 )																			//	the existance of at least one attribute is mandatory
				return NOT_ENOUGH_ATTRIBUTES;		
		
			for ( $i = 0; $i < $attr_length; $i++ )																//	filling the database attribute attribute
			{

				$attr = new attribute( $this->con, -1, $this->cat_id );											//	creating a new attribute object. The id is -1 as this attribute
																												//	doesn't still exist in the database
				if ( ( $this->errno = $attr->get_errno() ) != DB_OK )
				{
					return ;
				}
	
				if ( $i == 0 )
					$id = $attr->add_attribute ( $attr_info_array[$i], $this->cat_id );
				else
					$attr->add_attribute ( $attr_info_array[$i], $this->cat_id );
					
				if ( ( $this->errno = $attr->get_errno() ) != DB_OK )
				{
					return ;
				}

			}		

			return $id;
		}		
		
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF USEFULL FUNCTIONS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/		

	}

?>