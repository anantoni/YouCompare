<?php
/*
 *  search.php:         One of the db classes for the youcompare project
 *  Implemented by:	Galanos Athanasios
 *  std code:		std06249
 *  A.M. code:		1115200600249
 *  Semester:		Spring 2011
 */

    class search
    {
        private $errno;     //variable always set to last error

        public function search()        //constructor
        {
            $this->errno=DB_OK;         //initialazing error
        }

        public function get_errno()
        {
            return $this->errno;
        }

        public function execute($parameters)        //executing search
        {
            $this->errno=DB_OK;     //setting error to no error
            $count=0;
            if(count($parameters)==0)
            {//at least one search parameter has to be given
                $this->errno=WRONG_INPUT;
                return ;
            }
            $con= new mysqli("edudb.di.uoa.gr", "soft_tech", "#s@f+#", "soft_tech_db");//connection with mysql
            if($con->connect_errno)
            {
                $this->errno=MYSQL_CONNECT_ERROR;//error
                return ;
            }
            $con->query("SET NAMES utf8;"); //comunication with ut8 encoding
            $query= "SELECT cat_id, cat_name, sum(score) AS final_score FROM "; //constructing query
            //returning category id and name, also search score is being returned
            for ($i=0; $i<count($parameters); $i++)
            {//queries that search titles, keywords and entities
                $arquery[]="SELECT cat_id, cat_name, CHAR_LENGTH(cat_name)/(0.5+ CHAR_LENGTH(cat_name)- CHAR_LENGTH(\"$parameters[$i]\"))*50 AS score from category WHERE cat_name LIKE \"%$parameters[$i]%\"";  //match title
                //the firt query is searching for a match in the title of a category, the length of the given search parameter and the match found are taking into consideration for the scoring
                $arquery[]="SELECT cat_id, cat_name, 65 AS score from category WHERE cat_keywords LIKE \"%#$parameters[$i]%\"";
                //the second query search for a match in category keywords, a standar score is returned if a match is found
                $arquery[]="SELECT category.cat_id, cat_name, CHAR_LENGTH(ent_name)/(0.5+ CHAR_LENGTH(ent_name)- CHAR_LENGTH(\"$parameters[$i]\"))*10 AS score FROM category, entity WHERE category.cat_id=entity.cat_id AND ent_name LIKE \"%$parameters[$i]%\"";//match entity name
                //the third query is searching for a match in the title of an entity, the length of the given search parameter and the match found are taking into consideration for the scoring
            }
            for ($i=0; $i<count($arquery); $i++)  //constructing the whole query from the sub-queries
            {
                if($i==0)
                {           
                    $query.= " ( ($arquery[$i])";
                }
                else
                {       //union all results, union all is used because a category can match more than one times with the same score
                    $query.= " UNION ALL ($arquery[$i])";
                }
            }
            $query.=") as temp GROUP BY cat_id ORDER BY final_score DESC;"; //group by category order by score descending
            //group all results by id in order to sum the subscores, ordering by score descending
            if( !($result=$con->query($query) ) )       //making query
            {
                $this->errno=MYSQL_ERROR;
                return ;
            }
            $array=array();
            while($row=$result->fetch_row())    //returning the result
            {
                $array[$count][0]=$row[0];  //id of the category
                $array[$count][1]=$row[1];  //name of the category
                if( !($num_of_entities=$con->query("SELECT count(*) FROM category, entity WHERE category.cat_id=entity.cat_id AND category.cat_id=".$row[0]) ) )
                {     //geting number of entities for a specific category
                    $this->errno=MYSQL_ERROR;
                    return ;
                }
                $entities=$num_of_entities->fetch_row();
                $array[$count][2]=$entities[0]; //number of entities
                $num_of_entities->close();
                if( !($rating=$con->query("SELECT count(*), avg(rate) FROM category, user_rates_category WHERE category.cat_id=user_rates_category.cat_id AND category.cat_id=".$row[0]) ) )
                {
                    $this->errno=MYSQL_ERROR;
                    return ;
                }//geting user rating
                $rating=$rating->fetch_row();
                if($rating[0]==0)       //if a category doesn't have rating
                {           //zero is assigned
                     $array[$count][3]=0;
                }
                else
                {
                    $array[$count][3]=$rating[1];
                }
                $array[$count][4]=$row[2];      //search rarting
                $count++;       //number of results so far
            }
            $result->close();       //free result
            $con->close();      //closing connections
            return $array;          //returning results
        }
    }
?>