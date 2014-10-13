<?php

	class categoryAuto
	{
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	VARIABLES
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/
		private $errno;
		private $con;
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF VARIABLES
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/
		
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	CONSTRUCTOR/DESTRUCTOR
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/	
/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	END OF CONSTRUCTOR/DESTRUCTOR
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/

/*
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	ACCESSORS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/

		public function __construct()																			
		{
			$this->errno = DB_OK;

			$this->con = new mysqli("edudb.di.uoa.gr", "soft_tech", "#s@f+#", "soft_tech_db");						//	connect to database

			/* check connection */
			if( mysqli_connect_errno() )
				return MYSQL_CONNECT_ERROR;																			//	failed to connect

			$this->con->query("SET NAMES utf8"); 		
		}	

	/*
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/	
	
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
	ACCESSORS
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/

	public function get_categories_by_popularity( $name, $LIMIT )
	{
		$this->errno = DB_OK;
		$category_array = array();
		$query = "SELECT c.cat_id,c.cat_name,AVG(ure.rate) FROM category c, user_rates_category ure where c.cat_name LIKE \"".$name."%\" and c.cat_id = ure.cat_id group by c.cat_id order by AVG(ure.rate) DESC LIMIT ".$LIMIT.";";
		if ( ! ( $result = $this->con->query( $query )))
		{
			$this->errno = MYSQL_ERROR;																				//	Failed to connect	
			return ;
		}

                $exists = "";
		while ( $row = $result->fetch_row() )																		//	initialize the structure with the data received from the database
		{
			$tmp = array();
                        array_push($tmp,$row[0]);
                        array_push($tmp,$row[1]);
                        array_push($tmp,$row[2]);
                        array_push($category_array,$tmp);
                        unset($tmp);
                        $exists.="'".$row[0]."',";
		}		
		if ($exists !="")
                    $exists = substr($exists,0,-1);
     
		if ( $result->num_rows <  $LIMIT )																			//	there is no such entity in this category
		{
                        
			$new_limit = $LIMIT - $result->num_rows;
                        $query2 = "SELECT cat_id, cat_name FROM category WHERE cat_name LIKE \"".$name."%\"";
                        if ($exists != "")
                            $query2.="AND cat_id NOT IN ($exists)";
                        $query2.="LIMIT $new_limit";
                        if ( ! ( $result2 = $this->con->query($query2)))
			{
				$this->errno = MYSQL_ERROR;																			//	Failed to connect	
				return ;
			}
			
			while ( $row2 = $result2->fetch_row() )																	//	initialize the structure with the data received from the database
			{	
                                $tmp = array();
                                array_push($tmp,$row2[0]);
                                array_push($tmp,$row2[1]);
                                array_push($tmp,0);
                                array_push($category_array,$tmp);
                                unset($tmp);
			}	
                        $result2->close();
		}

		$result->close();
                return $category_array;

	}
        };