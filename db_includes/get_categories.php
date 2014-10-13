<?php

    class get_categories
    {
        private $con;       //connection to mysql server
        private $errno;     //last error happend

        public function __construct()
        {
            $this->errno = DB_OK;
            $this->con = new mysqli("edudb.di.uoa.gr", "soft_tech", "#s@f+#", "soft_tech_db");	//connect to database

            /* check connection */
            if( mysqli_connect_errno() )
            {
                return MYSQL_CONNECT_ERROR;	//failed to connect
            }

            $this->con->query("SET NAMES utf8");
        }

        public function __destruct()
        {
            if( !$this->con->connect_errno )		//if no problem has occured
            {
                $this->con->close();					//close the connection
            }
        }
		
		public function get_errno()
        {
            return $this->errno;
        }

        public function get_most_popular($limit)      //returning $limit most popular categories in the site
        {                               //score is based to the rating and the number of rates
            $this->errno=DB_OK;     //setting errno to no error
            $array=array();
            $f_query = "SELECT count(rate.cat_id) FROM user_rates_category rate, category cat WHERE cat.cat_id = rate.cat_id GROUP BY rate.cat_id Order by count(rate.cat_id) DESC LIMIT 1";

	     if(!($result=$this->con->query($f_query)))        //executing query
            {
                $this->errno=MYSQL_ERROR;
                return ;
            }	
			
			
	     if ( $row=$result->fetch_row() )
		  $max_users = $row[0];
	     else
		  $max_users = 0;
			
            $query="SELECT cat.cat_id, cat_name, 0.2*COUNT(cat.cat_id)*10/".$max_users."+0.8*AVG(rate) as score FROM category as cat, user_rates_category as rate WHERE cat.cat_id = rate.cat_id GROUP BY cat_id ORDER BY (score) DESC LIMIT $limit";
		
            if(!($result=$this->con->query($query)))        //executing query
            {
                $this->errno=MYSQL_ERROR;
                return ;
            }
            while ($row=$result->fetch_row())       //for each row returned
            {
                $array["$row[1]"]["id"]= $row[0];
                $array["$row[1]"]["popularity"]= $row[2];
            }
            if($result->num_rows<$limit)
            {
                $new_limit=$limit-$result->num_rows;
                $result->close();
                $query="SELECT cat_id, cat_name, 0 FROM category WHERE cat_id NOT IN (SELECT DISTINCT cat_id from user_rates_category) LIMIT $new_limit;";
                if(!($result=$this->con->query($query)))        //executing query
                {
                    $this->errno=MYSQL_ERROR;
                    return ;
                }
                while ($row=$result->fetch_row())       //for each row returned
                {
                    $array["$row[1]"]["id"]= $row[0];
                    $array["$row[1]"]["popularity"]= $row[2];
                }
            }
            $result->close();
            return $array;
        }

	 public function get_N_categories_by_popularity( $from, $to, $sort_type, $name_start )
	 {
		$this->errno=DB_OK;
			
		if ( $to != -1 )																				
		{
			$query = "SELECT c.*, AVG(ure.rate) FROM category c, user_rates_category ure where c.cat_name LIKE \"".$name_start."%\" and c.cat_id = ure.cat_id GROUP BY c.cat_id ORDER BY AVG(ure.rate) ".$sort_type." ";
				
			if ( ! ( $result = $this->con->query( $query."LIMIT ".$to."" ) ) )
			{
				$this->errno = MYSQL_ERROR;																	//	Failed to connect	
				return ;	
			}	
		}
		else
		{     
			$query = "SELECT c.*, AVG(ure.rate) FROM category c, user_rates_category ure where c.cat_name LIKE \"".$name_start."%\" and c.cat_id = ure.cat_id GROUP BY c.cat_id ORDER BY AVG(ure.rate) ".$sort_type." ";
			
			if ( ! ( $result = $this->con->query( $query ) ) )
			{
				$this->errno = MYSQL_ERROR;																	//	Failed to connect	
				return ;
			}	
		}				
		
		$counter = 1;
		
		while ( $counter < $from )																			//	looping until we reach the first desirable record
		{
			$counter++;
			$result->fetch_row();
		}
		
		$cat_info_array = array();
		
		while ( $row = $result->fetch_row() )																//	initialize the structure with the data received from the database
		{	
			$counter ++;
			$category_info = new category_info();
			$category_info->cat_id = $row[0];	
			$category_info->cat_name = $row[1];
			$category_info->is_open = $row[2];
			$category_info->cat_description = $row[3];
			$category_info->cat_keywords = $row[4];
			$category_info->cat_image = $row[5];
			$category_info->cat_video = $row[6];
			$category_info->rate = $row[7];
			$cat_info_array[] = $category_info;																//	filling the array with entities			
		}
			
		if ( $counter < ( $limit = $to - $from ) || $to == -1 )
		{

			if ( $to != -1 )																				
			{
				$query = "SELECT * FROM category WHERE cat_name LIKE \"".$name_start."%\" AND cat_name NOT IN ( SELECT c.cat_name FROM category c, user_rates_category ure WHERE c.cat_name LIKE \"".$name_start."%\" AND c.cat_id = ure.cat_id ) GROUP BY cat_id ORDER BY cat_name ".$sort_type." ";
			
				if ( ! ( $result = $this->con->query($query." LIMIT ".$limit."" ) ) )
				{
					$this->errno = MYSQL_ERROR;																//	Failed to connect	
					return ;	
				}	
			}
			else
			{     
				$query = "SELECT * FROM category WHERE cat_name LIKE \"".$name_start."%\" AND cat_name NOT IN ( SELECT c.cat_name FROM category c, user_rates_category ure WHERE c.cat_name LIKE \"".$name_start."%\" AND c.cat_id = ure.cat_id ) GROUP BY cat_id ORDER BY cat_name ".$sort_type." ";
				if ( ! ( $result = $this->con->query( $query ) ) )
				{
					$this->errno = MYSQL_ERROR;																//	Failed to connect	
					return ;
				}	
			}				

			while ( $row = $result->fetch_row() )															//	initialize the structure with the data received from the database
			{	
				$category_info = new category_info();
				$category_info->cat_id = $row[0];	
				$category_info->cat_name = $row[1];
				$category_info->is_open = $row[2];
				$category_info->cat_description = $row[3];
				$category_info->cat_keywords = $row[4];
				$category_info->cat_image = $row[5];
				$category_info->cat_video = $row[6];
				$category_info->rate = 0;
				$cat_info_array[] = $category_info;															//	filling the array with entities			
			}			
		}
		return $cat_info_array;	
	}


        public function fetch( $cat_ids )		//given an array of category ids this function retrieves all main infos for each category
        {
            $array = array();
            $this->errno = DB_OK;
            if(count($cat_ids) == 0)			//if no ids were given
            {
                $this->errno = WRONG_INPUT;
                return ;
            }

            $query = "SELECT * FROM category WHERE (";		//start building query

            for($i = 0; $i < count($cat_ids) -1; $i++)
            {
                $query.= "cat_id = $cat_ids[$i] OR ";		//enter n-1 first category ids that were given
            }
            $query.= "cat_id = ". $cat_ids[count($cat_ids)-1]. ");";	//enter last category id and complete query

            if ( ! ( $result = $this->con->query($query) ) )
            {
                $this->errno = MYSQL_ERROR;		//Failed to connect
				return ;
            }

            if ( $result->num_rows != count($cat_ids) )		//then invalid category id was given
            {
				$this->errno = WRONG_INPUT;
				return;
            }
            $i=0;
            while($row = $result->fetch_row())				//for each category id given
            {
                $array[$i]["id"]=$row[0];//retrieve id in order
                $array[$i]["name"]=$row[1];	//retrieve name
                $array[$i]["is_open"]=$row[2];			//retrieve open status
                $array[$i]["description"]=$row[3];			//retrieve description
                $tok = strtok( $row[4], "#" );		//turning the keywords string into an array
                while ( $tok !== false )
                {
                        $array[$i]["keywords"][] = $tok;
                        $tok = strtok( "#" );
                }
                $array[$i]["image"]=$row[5];				//retrieve image
                $array[$i]["video"]=$row[6];					//retrieve video
                $i++;
            }
            return $array;
        }
    }
?>