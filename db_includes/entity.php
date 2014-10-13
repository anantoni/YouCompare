<?php

    class entity
    {
//data members
        private $con;       //connection to mysql server
        private $errno;     //last error happend
        private $id;        //id of the entity
        private $name;
        private $description;
        private $image;
        private $video;

//end of data members

//constructor
        public function entity($connection, $entid, $catid)        //constructon of the class
        {
            $this->errno=DB_OK;
            $this->con=$connection;
            if($entid!=-1)      //if not -1 entity allready exists, if -1 a new entity is going to  be inserted
            {
                if( ! ( $result=$this->con->query("SELECT ent_name, ent_description, ent_image, ent_video from entity where cat_id=$catid AND ent_id=$entid") ) )
                {       //retrieving all available info
                    $this->errno=MYSQL_ERROR;
                    return ;
                }
                if(!($row=$result->fetch_row() ))
                {       //entity dont't exist, probably deleted
                    $this->errno=ENTITY_DONT_EXIST;
                    return ;
                }

                $this->name = $row[0];
                $this->description = $row[1];
                $this->image = $row[2];
                $this->video = $row[3];
            }
            $this->id=$entid; //initialazing data member
        }
//end of constructor
//
//add / delete
    public function add_entity($info, $catid)       //adding new entity
    {
        $this->errno=DB_OK;
        if ( $this->id != -1 )      //if id!=-1 wrong initialzation
        {
                $this->errno = WRONG_ID;
                return ;
        }
        if(!($result=$this->con->query("SELECT ent_id FROM entity where cat_id=$catid AND ent_name=\"$info->entity_name\";")))
        {                           //retrieving entities that belongs to category with catid that has the same name
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if($result->fetch_row())//only one entity per category can have the same name
        {
            $result->close();
            $this->errno=ENTITY_EXISTS;     //entity with name $info->Entity_name allready exists for category with id $catid
            return;
        }
        $result->close();       //closing mysql result
        $query="INSERT INTO entity (cat_id, ent_name ";
        if(!is_null($info->entity_description))//if entity description is set
        {
            $query.=", ent_description";
        }
        if(!is_null($info->entity_image))//if entity image is set
        {
            $query.=", ent_image";
        }
        if(!is_null($info->entity_video))       //if entity video is set
        {
            $query.=", ent_video";
        }
        $query.=") VALUES ($catid, \"$info->entity_name\"";
        if(!is_null($info->entity_description))//if entity description is set
        {
            $query.=", \"$info->entity_description\" ";
        }
        if(!is_null($info->entity_image))//if entity image is set
        {
            $query.=",\"$info->entity_image\"";
        }
        if(!is_null($info->entity_video))//if entity video is set
        {
            $query.=", \"$info->entity_video\"";
        }
        $query.=");";
        if(!$this->con->query($query))  //insert new entity
        {
            $this->errno=MYSQL_ERROR;
            return ;
        }
        $id=$this->con->insert_id;      //geting the id of the entity
        //// insert_id is a mysqli property tha his value is the last auto increment value generated from an insert statement
        while( $element = each( $info->entity_attribute_values ) )  //inserting values for new entity
        {
            if(!($result=$this->con->query("SELECT comparability FROM attribute where attr_id=".$element['key'])))
            {   //geting comparability of attribute wich values belongs
                $this->errno=MYSQL_ERROR;
                return ;
            }
            if(!( $row=$result->fetch_row() ))      //attribute don't found
            {
                $this->errno=WRONG_ID;      //returning error
                return ;
            }
            else
            {
                if($row[0]==UNCOMPARABLE)       //uncomparable value
                {
                    if($element['value']==NULL)
                    {
                        $query="INSERT INTO entity_has_value (attr_id, ent_id, u_value) VALUES (".$element['key'].", $id, NULL);";
                    }
                    else
                    {
                        $query="INSERT INTO entity_has_value (attr_id, ent_id, u_value) VALUES (".$element['key'].", $id, \"".$element['value']."\");";
                    }
                }
                else if($row[0]==DISTINCT)      //distinct value is inserted
                {
                    if($element['value']==NULL)
                    {
                        $query="INSERT INTO entity_has_value (attr_id, ent_id, d_value) VALUES (".$element['key'].", $id, NULL);";
                    }
                    else
                    {
                        $query="INSERT INTO entity_has_value (attr_id, ent_id, d_value) VALUES (".$element['key'].", $id, \"".$element['value']."\");";
                    }
                }
                else        //countable value is inserted
                {
                    if($element['value']==NULL)
                    {
                        $query="INSERT INTO entity_has_value (attr_id, ent_id, c_value) VALUES (".$element['key'].", $id, NULL);";
                    }
                    else
                    {
                        $query="INSERT INTO entity_has_value (attr_id, ent_id, c_value) VALUES (".$element['key'].", $id, ".$element['value'].");";
                    }
                }
            }
            if(!$this->con->query($query))      //inserting value
            {
                $this->errno=MYSQL_ERROR;
                return ;
            }
            $result->close();
        }
        $this->id = $id;      ///an id is assigned
        $this->name = $info->entity_name;
        $this->description = $info->entity_description;
        $this->image = $info->entity_image;
        $this->video = $info->entity_video;
        return ;
    }

    public function remove_entity()
    {
        $this->errno=DB_OK;
        if(! $this->con->query("DELETE FROM entity WHERE ent_id=$this->id") )   //deleting an entity
        {       //all dependencies like values and ratings are deleted automatically
            $this->errno=MYSQL_ERROR;
            return ;
        }
        $this->id=-1;       //entity don't exist anymore
        return ;
    }

//end of add / delete

//mutators
    public function set_entity($info)       //seting entity info
    {
        if($this->id==-1)           //wrong initialization
        {
            $this->errno=WRONG_ID;
            return ;
        }
        $this->errno=DB_OK;
        $old_info=$this->get_info();    //retrieving old info
        if($this->get_errno()!=DB_OK)//internal error
        {//error allready set
            return ;
        }
        if( !($result=$this->con->query("SELECT cat_id from entity where ent_id=$this->id") ))    //updating..........
        {
            $this->errno=MYSQL_ERROR;       //error 
            return ;
        }
        if(!($row=$result->fetch_row()))
        {
            $this->errno=MYSQL_ERROR;       //error
            return ;
        }
        $result->close();
        if( !($result=$this->con->query("SELECT * from entity where ent_name=\"$info->entity_name\" AND cat_id=$row[0] AND ent_id!=$this->id;") ) )   //updating..........
        {
            $this->errno=MYSQL_ERROR;       //error
            return ;
        }
        if($result->num_rows!=0)
        {
            $result->close();
            $this->errno=ENTITY_EXISTS;       //error
            return ;
        }
        $result->close();       //closing result
        $query="UPDATE entity SET ";    //constructing UPDATE query
        if(strcmp($old_info->entity_name, $info->entity_name)!=0)
        {       //entity name changed
            $must_update=1;
            $query.="ent_name=\"$info->entity_name\" ";
        }
        if(is_null($info->entity_description))			//no description was given
        {
            if(isset($must_update))
            {
                $query.=",ent_description=NULL ";
            }
            else
            {
                $must_update=1;
                $query.="ent_description=NULL ";
            }
        }
        else if(strcmp($old_info->entity_description, $info->entity_description)!=0)		//match between old and new description
        {   //entity description changed
            if(isset($must_update))
            {
                $query.=",ent_description=\"$info->entity_description\" ";
            }
            else
            {
                $must_update=1;
                $query.="ent_description=\"$info->entity_description\" ";
            }
        }
        if(is_null($info->entity_image))			//no image was given
        {
            if(isset($must_update))
            {
                $query.=",ent_image=NULL ";
            }
            else
            {
                $must_update=1;
                $query.="ent_image=NULL ";
            }
        }
        else if(strcmp($old_info->entity_image, $info->entity_image)!=0)				//match between old and new image
        {   //entity image changed
            if(isset($must_update))
            {
                $query.=",ent_image=\"$info->entity_image\" ";
            }
            else
            {
                $must_update=1;
                $query.="ent_image=\"$info->entity_image\" ";
            }
        }
        if(is_null($info->entity_video))							//no video was given
        {
            if(isset($must_update))
            {
                $query.=",ent_video=NULL ";
            }
            else
            {
                $must_update=1;
                $query.="ent_video=NULL ";
            }
        }
        else if(strcmp($old_info->entity_video, $info->entity_video)!=0)					//match between old and new video
        {   //entity video changed
            if(isset($must_update))
            {
               $query.=",ent_video=\"$info->entity_video\" ";
            }
            else
            {
                $must_update=1;
                $query.="ent_video=\"$info->entity_video\" ";
            }
        }
        if(isset($must_update)) //if any change happened
        {
            $query.="WHERE ent_id=$this->id;";
            if( !$this->con->query($query) )    //updating..........
            {
                $this->errno=MYSQL_ERROR;       //error 
                return ;
            }
        }
        while( $element = each( $old_info->entity_attribute_values ) )  //for each entity value
        {
            $must_update=0;     //a variable that indicate if a change happened,no changes so far
            if(!isset($info->entity_attribute_values[$element["key"]]) && $info->entity_attribute_values[$element["key"]]!=NULL )
            {   //an attribute value was not found in the new info
                $this->errno=WRONG_INPUT;
                return ;
            }
            else
            {
                if( !($result=$this->con->query("SELECT comparability from attribute where attr_id=".$element["key"]) ) )
                {   //getting type of attribute for the value
                    $this->errno=MYSQL_ERROR;
                    return ;
                }
                $comparability=$result->fetch_row();
                if($comparability[0]==UNCOMPARABLE)     //uncomparable value
                {
                    if(strcmp($info->entity_attribute_values[$element["key"]], $old_info->entity_attribute_values[$element["key"]])!=0)
                    {
                        if($info->entity_attribute_values[$element["key"]]==NULL)
                        {
                             $query="UPDATE entity_has_value SET u_value=NULL WHERE attr_id=".$element["key"]." AND ent_id=$this->id;";
                        }
                        else
                        {
                            $query="UPDATE entity_has_value SET u_value=\"".$info->entity_attribute_values[$element["key"]]."\" WHERE attr_id=".$element["key"]." AND ent_id=$this->id;";
                        }
                        $must_update=1;//one values changed
                    }
                }
                else if($comparability[0]==DISTINCT)    //distinct value
                {
                    if(strcmp($info->entity_attribute_values[$element["key"]], $old_info->entity_attribute_values[$element["key"]])!=0)
                    {
                        if($info->entity_attribute_values[$element["key"]]==NULL)
                        {
                             $query="UPDATE entity_has_value SET d_value=NULL WHERE attr_id=".$element["key"]." AND ent_id=$this->id;";
                        }
                        else
                        {
                            $query="UPDATE entity_has_value SET d_value=\"".$info->entity_attribute_values[$element["key"]]."\" WHERE attr_id=".$element["key"]." AND ent_id=$this->id;";
                        }
                       $must_update=1;//one values changed
                    }
                }
                else            //countable value
                {
                    if($info->entity_attribute_values[$element["key"]]!=$old_info->entity_attribute_values[$element["key"]])
                    {
                        if($info->entity_attribute_values[$element["key"]]==NULL)
                        {
                             $query="UPDATE entity_has_value SET c_value=NULL WHERE attr_id=".$element["key"]." AND ent_id=$this->id;";
                        }
                        else
                        {
                            $query="UPDATE entity_has_value SET c_value=\"".$info->entity_attribute_values[$element["key"]]."\" WHERE attr_id=".$element["key"]." AND ent_id=$this->id;";
                        }
                        $must_update=1;     //one values changed
                    }
                }
            }
            if($must_update==1)     //at least one change happened
            {
                if( !$this->con->query($query) )            //updating.......
                {
                    $this->errno=MYSQL_ERROR;
                    return ;
                }
            }
        }
        $result->close();       //closing result
        $this->name = $info->entity_name;
        $this->description = $info->entity_description;
        $this->image = $info->entity_image;
        $this->video = $info->entity_video;
        return ;
    }

//end of mutators

//accessors
    public function get_attribute_values()  //geting entity values
    {
        if($this->id==-1)
        {
            $this->errno= WRONG_INPUT;      //wrong initialazation
            return ;
        }
        $this->errno=DB_OK;
        if( !( $result=$this->con->query("SELECT * FROM entity_has_value where ent_id=$this->id;") ) )
        {
            $this->errno=MYSQL_ERROR;
            return ;
        }
        while($row=$result->fetch_row())
        {
            if($row[2]!=NULL)           // uncomparable value
            {
                $array["$row[0]"]= $row[2];
            }
            else if($row[3]!=NULL)      //distinct values
            {
                $array["$row[0]"]= $row[3];
            }
            else                    //countable value
            {
                $array["$row[0]"]= $row[4];
            }
        }
        $result->close();       //closing result
        return $array;      //returning result
    }

    public function get_attribute_values_with_name()  //geting entity values
    {
        if($this->id==-1)
        {
            $this->errno= WRONG_INPUT;      //wrong initialazation
            return ;
        }
        $this->errno=DB_OK;
        if( !( $result=$this->con->query("SELECT attribute.attr_id, attr_name, u_value, d_value, c_value FROM entity_has_value, attribute WHERE attribute.attr_id=entity_has_value.attr_id AND  ent_id=$this->id;") ) )
        {
            $this->errno=MYSQL_ERROR;
            return ;
        }
        while($row=$result->fetch_row())
        {
            $array["$row[0]"]["name"]=$row[1];
            if($row[2]!=NULL)           // uncomparable value
            {
                $array["$row[0]"]["value"]= $row[2];
            }
            else if($row[3]!=NULL)      //distinct values
            {
                $array["$row[0]"]["value"]= $row[3];
            }
            else                    //countable value
            {
                $array["$row[0]"]["value"]= $row[4];
            }
        }
        $result->close();       //closing result
        return $array;      //returning result
    }

	
    public function get_errno()     //return last error status
    {
        return $this->errno;
    }

    public function get_id()        //returning entity id
    {
        $this->errno=DB_OK;
        if($this->id==-1)
        {
            $this->errno= WRONG_INPUT;      //wrong initialazation
            return ;
        }
        return $this->id;
    }

    public function get_name()      //returns entity name
    {
        $this->errno=DB_OK;
        if($this->id==-1)
        {
            $this->errno= WRONG_INPUT;      //wrong initialazation
            return ;
        }

        return $this->name;   //returning name
    }

    public function get_description()       //method that returns description
    {
       $this->errno=DB_OK;
        if($this->id==-1)
        {
            $this->errno= WRONG_INPUT;      //wrong initialazation
            return ;
        }

        return $this->description;    //returning description
    }

    public function get_image() //method that returns image
    {
       $this->errno=DB_OK;
        if($this->id==-1)
        {
            $this->errno= WRONG_INPUT;      //wrong initialazation
            return ;
        }

        return $this->image;  //returning image
    }

    public function get_video()
    {
        $this->errno=DB_OK;
        if($this->id==-1)
        {
            $this->errno= WRONG_INPUT;      //wrong initialazation
            return ;
        }

        return $this->video;  //returning video
    }

    public function get_rating()        //method that returns entity's rating
    {
        if($this->id==-1)
        {
            $this->errno= WRONG_INPUT;      //wrong initialazation
            return ;
        }
        $this->errno=DB_OK;
        if( ! ( $result=$this->con->query("SELECT AVG(rate) FROM user_rates_entity where ent_id=$this->id") ) )
        {       //retrieving rating
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!($row=$result->fetch_row() ))
        {       //entity dont't exist, probably deleted
            $this->errno=ENTITY_DONT_EXIST;
            return ;
        }
        if($row[0]==NULL)
        {
            $result->close();
            return 0;       //no ranking so far, returning 0
        }
        else
        {
            $result->close();   //closing result
            return $row[0];     //returning ranking
        }
    }

    public function has_user_rated($username) //Returns 0 if the user has evaluated the category otherwise 1
    {
        if($this->id==-1)
        {
            $this->errno= WRONG_INPUT;      //wrong initialazation
            return ;
        }
        $this->errno=DB_OK;
        if( ! ( $result=$this->con->query("SELECT user_id FROM user where username=\"$username\";") ) )
        {       //retrieving rating if exists
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!($row=$result->fetch_row() ))
        {      //user not found
            $this->errno=USERNAME_DONT_EXIST;
            return ;
        }
        $user_id=$row[0];
        $result->close();
        if( ! ( $result=$this->con->query("SELECT * FROM user_rates_entity where ent_id=$this->id AND user_id=$user_id ;") ) )
        {       //retrieving rating if exists
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!($row=$result->fetch_row() ))
        {       //user has not evaluated this entity so far
            $result->close();
            return 0;
        }
        else
        {
            $result->close();
            return 1;//user has allready evaluated this entity
        }
    }

	public function get_id_by_name( $entname, $catid )			//return an entity id given a specific name
	{
		$this->errno=DB_OK;
        if($this->id!=-1)
        {
            $this->errno= WRONG_INPUT;      //wrong initialazation
            return ;
        }
		
		 if( ! ( $result=$this->con->query("SELECT ent_id FROM entity where cat_id=$catid AND ent_name=\"$entname\";") ) )
        {       //retrieving id if exists
            $this->errno=MYSQL_ERROR;
            return ;
        }
		
		if(!($row=$result->fetch_row() ))
        {       //entity dont't exist, probably deleted
            $this->errno=ENTITY_DONT_EXIST;
            return ;
        }
		
		return $row[0];
	}


    public function get_info()      //method that returns all info of the entity
    {
        $this->errno=DB_OK;
        if($this->id==-1)
        {
            $this->errno= WRONG_INPUT;      //wrong initialazation
            return ;
        }

        $info=new entity_info();        //creating info structure
        $info->entity_id = $this->id;     //setting id
        $info->entity_name = $this->name; //setting name
        $info->entity_description = $this->description;      //setting description
        $info->entity_attribute_values = $this->get_attribute_values();   //retrieving entity values
        $info->rate = $this->get_rating();        //retrieving rating of the entity
        $info->entity_image = $this->image;        //setting image
        $info->entity_video = $this->video;        //setting video
        
        return $info;       //returning structure
    }
//end of accessors
    }
?>