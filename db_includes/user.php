<?php
/*
 *  user.php:           One of the db classes for the youcompare project
 *  Implemented by:	Galanos Athanasios
 *  std code:		std06249
 *  A.M. code:		1115200600249
 *  Semester:		Spring 2011
 */
    
    class user      
    {                           //class user handles every action a user can do, unregistered, admin or simple user
        private $id;            //id, assigned at registration, unique for each user
        private $username;      //username, unique for each user
        private $name;          //name of the user
        private $surname;       //suranme of the user
        private $email;         //email of the user
        private $privileges;    //privileges of the user(admin, simple user, unverified
        private $logged_in;     //login status of user
        private $con;           //connection to mysql server
        private $errno;         //variable always set to last error

///////////////////////////constructor - destructor/////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
        public function user($username, $log_status)        //constructor
        {
            $this->errno=DB_OK;    //setting error to  no error
            $this->username="";     //initalazing data members
            $this->name="";
            $this->surname="";
            $this->email="";
            $logged_in=NOT_LOGGED_IN;
            $this->con= new mysqli("edudb.di.uoa.gr", "soft_tech", "#s@f+#", "soft_tech_db");//connecting to mysql server
            if($this->con->connect_errno)       //checking if a connection was established
            {
                $this->errno=MYSQL_CONNECT_ERROR;//error
                return ;
            }
            $this->con->query("SET NAMES utf8;");     //comunication with ut8 encoding
            if ($log_status==LOGGED_IN)     //user has logged in previously
            {   
               if(!($result=$this->con->query("SELECT user_id, name, surname, email, privileges from user where username=\"$username\";")))//retrieving info of the user
               {
                   $this->errno=MYSQL_ERROR;    //mysql error
                   return ;
               }
               else
               {
                   if($result->num_rows!=1) //must return one user
                   {//username is unique so it will return one user or nothing
                       $result->close();    //closing mysql result
                       $this->errno=WRONG_USERNAME_OR_PASSWORD; //wrong username given
                       return ;
                   }
                   $row= $result->fetch_row();  //retriving result
                   $this->username=$username;   //setting username
                   $this->id=$row[0];   //setting user id
                   $this->name=$row[1];     //setting data memeber name
                   $this->surname=$row[2];  //setting data member surname
                   $this->email=$row[3];    //setting email
                   $this->privileges=$row[4];   //setting user privileges
                   $this->logged_in=LOGGED_IN;  //setting login status
                   $result->close();    //closing mysql result
               }
            }
            else if($log_status==NOT_LOGGED_IN)     //unlogged user
            {
                $this->username=$username;      //waiting for login later....
                $this->logged_in=NOT_LOGGED_IN;
            }
            return ;
        }


        public  function __destruct() //destructor of the class
        {
            if(!$this->con->connect_errno)  //if a connection was made
            {
                $this->con->close();        //closing it at destruction
            }
        }
////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////login-register-delete_account//////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
        public function login($password)    //login method, validates username and password
        {
            $this->errno=LOGGED_IN;             //setting errno to no error
            if($this->logged_in==LOGGED_IN)     //if allready loged in, no need to do ti again
            {
                $this->errno=ALLREADY_LOGGED_IN;
                return ;
            }
            else if($this->logged_in==NOT_LOGGED_IN)        //retrieving credentials
            {
                if( !($result=$this->con->query("SELECT user_id, name, surname, email, privileges from user where username=\"$this->username\" AND password=password(\"$password\");")) )
                {
                    $this->errno=MYSQL_ERROR;
                    return ;
                }
                if($result->num_rows==0)    //didn't found username and password
                {
                    $this->logged_in=NOT_LOGGED_IN; //failed to login
                    $result->close();               //closing mysql result
                    $this->errno= WRONG_USERNAME_OR_PASSWORD;   //setting error to wrong username or password
                    return ;
                }
                else
                {
                   $row= $result->fetch_row();
                   if($row[4]==UNVERIFIED_USER)       //user found but not verified yet
                   {
                       $this->errno=UNVERIFIED_USER;
                       return ;
                   }
                   $this->privileges=$row[4];    //setting user privileges
                   $this->id=$row[0];           //setting user id
                   $this->name=$row[1];         //setting the name of the user
                   $this->surname=$row[2];      //setting the surname of the user
                   $this->email=$row[3];        //setting email
                   $this->logged_in=LOGGED_IN;  //succesful login
                   $result->close();            //closing mysql result
                   return ;
               }
            }
        }

        public function register($profile, $password)   //register function
        {
            $this->errno=DB_OK;    //setting error to  no error
            $query="INSERT INTO user (username, password, email";   //constructing cuery
            if(!is_null($profile->name) && strcmp($profile->name, "")!=0)        //name is optional
            {
                $query.=", name";
            }
            if(!is_null($profile->surname) && strcmp($profile->name, "")!=0)    //surname is optional
            {
                $query.=", surname";
            }
            $query.=") VALUES(\"$profile->username\", password(\"$password\"), \"$profile->email\"";
            if(!is_null($profile->name))        //name is optional
            {
                $query.=", \"$profile->name\"";
            }
            if(!is_null($profile->surname))     //surname is optional
            {
                $query.=", \"$profile->surname\"";
            }
            $query.= ");";
            if(!$this->con->query("START TRANSACTION;"))    //begining database transaction
            {
                $this->errno=MYSQL_ERROR;   //mysql failure
                return ;
            }
            if(!$this->con->query($query))  //registering
            {
                $this->con->query("ROLLBACK;"); //failed to register the new user rollbacking
                $this->errno=MYSQL_ERROR;
                return ;
            }
            /*the insert_id data member of mysqli, returns last id(in an auto increment field) created by the last insert statement*/
	    $md5_user=md5($this->con->insert_id);   //user is going to be verified by md5 on id field
            $header='FROM: noreply@youcompare.com'; //constructing verification mail
	    $body="Welcome at YouCompare\n\nPlesae follow this link to complete yor registration: http://cgi.di.uoa.gr/~std06048/youcompare/verify.php?user=$md5_user";
	    if(!mail("$profile->email", "YouCompare Account activation", $body, $header)) //sending mail in order to verify user
	    {
                $this->errno=FAILED_TO_SEND_MAIL;   //if the email was not send to the user
		$this->con->query("ROLLBACK;");     //rollbacking because he will not be able to register
		return ;                            //so there is no need to keep his information in the DBMS
	    }
            $this->con->query("COMMIT;");   //successfull registration
            return ;
        }

        public function delete_account()        //deleting user account
        {
            $this->errno=DB_OK;    //setting error to  no error
            if($this->logged_in!=LOGGED_IN)     //user must be logged in in order to delete his account
            {
                $this->errno=NOT_LOGGED_IN;
                return ;
            }
            if(!$this->con->query("START TRANSACTION;"))    //begining database transaction
            {
                $this->errno=MYSQL_ERROR;   //failure
                return ;
            }
            if(!$this->con->query("DELETE FROM user WHERE user_id=$this->id;"))    //begining database transaction
            {//deleting user and all dependancies (ratings, category rigths, request for rigths) ( mysql on delete cascade )
                $this->con->query("ROLLBACK;");
                $this->errno=MYSQL_ERROR;   //failure
                return ;
            }
            $this->logged_in=NOT_LOGGED_IN;   //setting login status to not logged in order to prevent the use of any method after the delete action
            $this->con->query("COMMIT;");
            return ;
        }
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////verify/////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
        public function verify_user($user_hash)    //method that verifies a registered user.
        {   //this method requires a parameter thas is the md5 of the user id
            //this parameter is sent with an email during registration
            $this->errno=DB_OK;    //setting error to  no error
            if(!($result=$this->con->query("SELECT privileges FROM user where md5(user_id)=\"$user_hash\";")))
            {
                $this->errno=MYSQL_ERROR;       //mysql error
                return;
            }
            if(!($priv=$result->fetch_row()))//one row must be returned
            {
                $this->errno=USERNAME_DONT_EXIST;       //user didn't found
                return;
            }
            else
            {
                if($priv[0]!=UNVERIFIED_USER)       //if user allready verified
                {
                    $this->errno=USER_ALLREADY_VERIFIED; //user is allready verified
                    return ;
                }
            }
            $result->close();   //closing mysql result
            if(!$this->con->query("UPDATE user SET privileges=0 WHERE md5(user_id)=\"$user_hash\";"))
            {       //updating database in order to verify the user....
                $this->errno=MYSQL_ERROR;
                return;
            }
            return ;
        }
////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////rating////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
    public function rate_category($cat_id, $rating) //method for rating a category
    {
        $this->errno=DB_OK;    //setting error to no error
        if($this->logged_in!=LOGGED_IN)
        {
            $this->errno=NOT_LOGGED_IN;     //user is not logged in, only logged in users can rate
            return ;
        }
        if(!($result=$this->con->query("SELECT cat_id FROM category WHERE cat_id=$cat_id;")))
        {                                           //checking if category exists
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())       //Expecting result with one row or empty, category id is unique
        {
            $this->errno=CATEGORY_DONT_EXIST;   //empty result, category not found
            return ;
        }
        $result->close();           //closing mysql result
        if(!($result=$this->con->query("SELECT user_id FROM user_rates_category WHERE cat_id=$cat_id AND user_id=$this->id;")))
        {                   //a user can only rate once each category, checking if user has allready rated this category
            $this->errno=MYSQL_ERROR;
            $result->close();
            return ;
        }
        if($result->fetch_row())
        {   //if the result set is not empty means that the user has allready rated this category
            $result->close();
            $this->errno=ALLREADY_RATED;
            return ;
        }
        $result->close();
        if(!$this->con->query("INSERT INTO user_rates_category (cat_id, user_id, rate) VALUES ($cat_id, $this->id, $rating);"))
        {           //inserting into the database rating rating info
            $this->errno=MYSQL_ERROR;   //mysql error
            return ;
        }
        //if no error returned user successfuly rated the desired category
        return ;
    }

    public function rate_entity($ent_id, $rating)   //method for rating entity
    {
        $this->errno=DB_OK;    //setting error to  no error
        if($this->logged_in!=LOGGED_IN)     //user must be logged in order to rate the entity
        {
            $this->errno=NOT_LOGGED_IN;
            return ;
        }
        if(!($result=$this->con->query("SELECT ent_id FROM entity WHERE ent_id=$ent_id;")))
        {       //checking if entity exists
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())       //expecting a result of one row or empty set
        {
            $this->errno=ENTITY_DONT_EXIST; //empty result set, entity don't exist
            return ;
        }
        if(!($result=$this->con->query("SELECT user_id FROM user_rates_entity WHERE ent_id=$ent_id AND user_id=$this->id;")))
        {           //a user can only rate once each entity, checking if user has allready rated this entity
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if($result->fetch_row())
        {       //user allready rated
            $result->close();   //closing mysql result
            $this->errno=ALLREADY_RATED;
            return ;
        }
        $result->close();   //closing mysql result
        if(!$this->con->query("START TRANSACTION;"))        //a transaction is required, all queries must succeed
        {   
            $this->errno=MYSQL_ERROR;
            return ;
        }		
        if(!$this->con->query("INSERT INTO user_rates_entity (ent_id, user_id, rate) VALUES ($ent_id, $this->id, $rating);"))
        {   //user failed to rated the entity
            $this->con->query("ROLLBACK;");
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$this->con->query("UPDATE entity SET ent_rate = (select avg(rate) FROM user_rates_entity WHERE ent_id=$ent_id) WHERE ent_id=$ent_id;"))
        {   //update entities rating
            $this->con->query("ROLLBACK;");
	    $this->errno=MYSQL_ERROR;
            return ;
        }
	$this->con->query("COMMIT;");//user successfully rated this entity
        return ;
    }
////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////mutators/////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
    public function set_profile($info)      //setting profile info
    {
        $this->errno=DB_OK;    //setting error to  no error
        if($this->logged_in!=LOGGED_IN)     //user must be logged in order to change the info
        {
            $this->errno=NOT_LOGGED_IN;
            return ;
        }
        $query="UPDATE user SET ";      //constructing update query
        $mustupdate=0;      //variable that indicates whether a data member was changed
        if(strcmp($this->username, $info->username)!=0)     //username changed
        {
            $must_update=1;                                 //data member changed
            $query.="username=\"$info->username\"";
        }
        if(strcmp($this->email, $info->email)!=0)           //email changed
        {
            if($must_update==1) //if previous field was updated mysql query requires ','
            {
                $query.=", ";
            }
            $must_update==1;//data member changed
            $query.="email=\"$info->email\"";
        }
        if(strcmp($this->name, $info->name)!=0)         //name changed
        {
            if($must_update==1) //if previous field was updated mysql query requires ','
            {
                $query.=", ";
            }
            $must_update==1;                            //data member changed
            $query.="name=\"$info->name\"";
        }
        if(strcmp($this->surname, $info->surname)!=0)   //surname changed
        {
            if($must_update==1) //if previous field was updated mysql query requires ','
            {
                $query.=", ";
            }
            $must_update==1;                            //data member changed
            $query.="surname=\"$info->surname\"";
        }
        $query.= " WHERE user_id=$this->id;";           //end of query
        if($must_update==1)
        {
            if(!$this->con->query($query) )             //updating......
            {
                $this->errno=MYSQL_ERROR;
                return ;
            }
            $this->username=$info->username;        //updating data members
            $this->email=$info->email;
            $this->name=$info->name;
            $this->surname=$info->surname;
        }
        return ;
    }

    public function set_password($new_password, $old_password)     //method that change user password with the new one
    {
        $this->errno=DB_OK;    //setting error to  no error
        if($this->logged_in!=LOGGED_IN)             //user must be logged in in order to change his password
        {
            $this->errno=NOT_LOGGED_IN;
            return ;
        }
        if(!($result=$this->con->query("SELECT user_id FROM user WHERE password=password(\"$old_password\") AND user_id=$this->id;")))
        {
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())        //if result is not empty username allrady exists
        {
            $result->close();
            $this->errno=WRONG_USERNAME_OR_PASSWORD;
            return ;
        }
        $result->close();
        if(!$this->con->query("UPDATE user SET password=password(\"$new_password\") WHERE user_id=$this->id;"))
        {   //updating user with the new password
            $this->errno=MYSQL_ERROR;
            return ;
        }
        return ;
    }

    public function set_username($new_username) //method that change the username of the user
    {
        $this->errno=DB_OK;    //setting error to  no error
        if($this->logged_in!=LOGGED_IN)     //user must be logged in in order to change his username
        {
            $this->errno=NOT_LOGGED_IN;
            return ;
        }
        if(strcmp($this->username, $new_username)!=0)//if there is a change
        {
            if(!($result=$this->con->query("SELECT user_id FROM user WHERE username=\"$new_username\";") ) )
            {                                   //checking if username allready exists
                $this->errno=MYSQL_ERROR;
                return ;
            }
            if($result->fetch_row())        //if result is not empty username allrady exists
            {
                $this->errno=USERNAME_ALLREADY_EXISTS;
                return ;
            }
            if(!$this->con->query("UPDATE user SET username=\"$new_username\" WHERE user_id=$this->id;") )
            {                                     //changin username to the new one
                $this->errno=MYSQL_ERROR;
                return ;
            }
            $this->username=$new_username;  //updating data member
        }
        return ;
    }

    public function set_email($new_email)       //method that change the email of the user
    {
        if($this->logged_in!=LOGGED_IN) //user must be logged in order to change his email
        {
            $this->errno=NOT_LOGGED_IN;
            return ;
        }
        $this->errno=DB_OK;         //setting error to no error
        if(strcmp($this->email, $new_email)!=0)     //if the email is not changed no need to update it
        {
            if(!($result=$this->con->query("SELECT user_id FROM user WHERE email=\"$new_email\";") ) )
            {               //checking if new mail is unique
                $this->errno=MYSQL_ERROR;
                return ;
            }
            if($result->fetch_row())    //if the result is not empty email allready exists
            {
                $result->close();
                $this->errno=EMAIL_ALLREADY_EXISTS;
                return ;
            }
            $result->close();
            if(!$this->con->query("UPDATE user SET email=\"$new_email\" WHERE user_id=$this->id;") )
            {                                   //updating with new email
                $this->errno=MYSQL_ERROR;
                return ;
            }
            $this->email=$new_email;                //setting data member
        }
        return ;
    }

    public function set_name($new_name)   //method that change the name of the user
    {
        if($this->logged_in!=LOGGED_IN)     //must be logged in order to change name
        {
            $this->errno=NOT_LOGGED_IN;
            return ;
        }
        $this->errno=DB_OK;
        if(strcmp($this->name, $new_name)!=0)   //if not changed since last time
        {
            if(!$this->con->query("UPDATE user SET name=\"$new_name\" WHERE user_id=$this->id;") )
            {       //updating
                $this->errno=MYSQL_ERROR;    //mysql error
                return ;
            }
            $this->name=$new_name;  //updating data member
        }
        return ;
    }

    public function set_surname($new_surname)    //method that change the surname of the user
    {
        $this->errno=DB_OK;
        if($this->logged_in!=LOGGED_IN)         //user must be logged in in order to change the surname
        {
            $this->errno=NOT_LOGGED_IN;
            return ;
        }
        if(strcmp($this->surname, $new_surname)!=0)     //if not changed since last time
        {
            if(!$this->con->query("UPDATE user SET surname=\"$new_surname\" WHERE user_id=$this->id;") )
            {//updating surname
                $this->errno=MYSQL_ERROR;   //mysql error
                return ;
            }
            $this->surname=$new_surname;    //updating data member
        }
        return ;
    }

    public function forgot_pasword($username)   //method that generates a new password for the user and send him a mail
    {
        $this->errno=DB_OK;    //setting error to no error
        if($this->logged_in==LOGGED_IN)     //if user logged in how lost password?????
        {
            $this->errno=WRONG_INPUT;
            return ;
        }
        $new_password="";   //generating random password
        for($i=0; $i<10; $i++)      //10 digit password with numbers and letters
        {
            if(rand(0,1))           //generating a number
            {
                $new_password .= rand(0, 9);
            }
            else                    //generating a letter
            {
                $new_password .= $this->return_char(rand(0, 14));
            }
        }
        if(!($result=$this->con->query("SELECT email FROM user WHERE username=\"$username\";") ))
        {                                   //retrieving mail of the user
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!($email=$result->fetch_row()))          //if mail not found wrong username given
        {
            $this->errno=USERNAME_DONT_EXIST;       //username don't found
            return ;
        }
        $email=$email[0]; //retrieving email
        $result->close();
        if(!$this->con->query("START TRANSACTION;") )       //transaction required
        {
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$this->con->query("UPDATE user SET password=password(\"$new_password\") WHERE username=\"$username\";") )
        {                                       //updating password with a new random one
            $this->con->query("ROLLBACK;");
            $this->errno=MYSQL_ERROR;
            return ;
        }
        $header='FROM: noreply@youcompare.com';     //sending password to the user
        $body="You requested a new password, if you didn't do this action please talk to a moderator\n\nYour new password is: $new_password\n\nPlease change this password with a new one as soon as possible";
        if(!mail($email, "YouCompare New password", $body, $header))        //sending mail
        {                           //if mail delivery failed rollbacking the change
            $this->errno=FAILED_TO_SEND_MAIL;
            $this->con->query("ROLLBACK;");
            return ;
        }
        $this->con->query("COMMIT;");   
        return ;
    }


    private function return_char($i)       //returns a character
    {
        if($i==1)
            return "a";
        else if($i==2)
            return "w";
        else if($i==3)
            return "c";
        else if($i==4)
            return "d";
        else if($i==5)
            return "e";
        else if($i==6)
            return "g";
        else if($i==7)
            return "h";
        else if($i==7)
            return "y";
        else if($i==8)
            return "j";
        else if($i==9)
            return "k";
        else if($i==10)
            return "l";
        else if($i==11)
            return "u";
        else if($i==12)
            return "n";
        else if($i==13)
            return "x";
        else if($i==14)
            return "p";
    }

///////////////////////////end of mutators///////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

    
//////////////////////////////////accessors/////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
        public function get_errno()     //return the last error of the system
        {
            return $this->errno;
        }

        public function is_logged_in()  //return login status
        {
            return $this->logged_in;
        }

        public function get_privileges()        //get user privileges
        {
            $this->errno=DB_OK;    //setting error to  no error
            if($this->logged_in != LOGGED_IN)//if not logged in error
            {
                $this->errno=NOT_LOGGED_IN;
                return ;
            }
            return $this->privileges;   //return the privileges of the user
        }

        public function get_id() //get user id
        {
            $this->errno=DB_OK;    //setting error to  no error
            if($this->logged_in != LOGGED_IN)//if not logged in error
            {
                $this->errno=NOT_LOGGED_IN;
                return ;
            }
            return $this->id;       //get user id
       }

        public function get_full_name()     //get full name of the user
        {
            $this->errno=DB_OK;    //setting error to  no error
            if($this->logged_in != LOGGED_IN)//if not logged in error
            {
                    $this->errno=NOT_LOGGED_IN;
                    return ;
            }
            return $this->name." ".$this->surname;//returns full name
        }

        public function get_email() //get the email of the user
        {
            $this->errno=DB_OK;    //setting error to  no error
            if($this->logged_in != LOGGED_IN)//if not logged in error
            {
                $this->errno= NOT_LOGGED_IN;
                return ;
            }
            return $this->email;        //return email
        }

        public function get_surname()   //get surname of the user
        {
            $this->errno=DB_OK;    //setting error to  no error
            if($this->logged_in != LOGGED_IN)//if not logged in error
            {
                $this->errno=NOT_LOGGED_IN;
                return ;
            }
            return $this->surname;	//returns the surname of the user
        }

        public function get_name() // get name of the user
        {
            $this->errno=DB_OK;    //setting error to  no error
            if($this->logged_in != LOGGED_IN)//if not logged in error
            {
                $this->errno=NOT_LOGGED_IN;
                return ;
            }
            return $this->name;		//returns the name of the user
        }

        public function get_username()      //get username
        {
            $this->errno=DB_OK;    //setting error to  no error
            if($this->logged_in != LOGGED_IN)//if not logged in error
            {
                $this->errno=NOT_LOGGED_IN;
                return ;
            }
            return $this->username;	//returns the name of the user
        }

        public function get_info()      //get all info of the user
        {
            $this->errno=DB_OK;    //setting error to  no error
            if($this->logged_in!=LOGGED_IN)     //user must be logged in
            {
                $this->errno=NOT_LOGGED_IN;
                return ;
            }
            $info= new profile_info();          //setting info structure
            $info->username=$this->username;
            $info->name=$this->name;
            $info->surname=$this->surname;
            $info->privileges=$this->privileges;
            $info->email=$this->email;
            return $info;       //returning info
        }
//////////////////////////////////end of accessors//////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////admin functions///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
    public function delete_user_account($username)  //deletes a user account, only an admin can do this
    {
        $this->errno=DB_OK;     //initialazing error
        if($this->logged_in!=LOGGED_IN)
        {//user must be logged in in order to perform this action
            $this->errno=NOT_LOGGED_IN;
            return ;
        }
        if($this->privileges!=ADMINISTRATOR)
        {//user must be an administrator in order o perform this action
            $this->errno=INSUFFICIENT_RIGHTS;
            return ;
        }
        if(!($result=$this->con->query("SELECT user_id from user where username=\"$username\";")))
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())   //wrong username, user not found
        {
            $result->close();       //closing result
            $this->errno=USERNAME_DONT_EXIST;   //returning error
            return ;
        }
        $result->close();
        if(!$this->con->query("DELETE FROM user WHERE username=\"$username\";"))//all dependencies like rating and category rights are deleted automatically by mysql
        {       //deleting user
            $this->errno=MYSQL_ERROR;   
            return ;
        }
        return ;
    }

    public function set_user_privileges($username, $privileges)  //method that changes user privileges, only an admin can do this
    {
        $this->errno=DB_OK;     //initialazing error
        if($this->logged_in!=LOGGED_IN)
        {//user must be logged in in order to perform this action
            $this->errno=NOT_LOGGED_IN;
            return ;
        }
        if($privileges!=ADMINISTRATOR && $privileges!=SIMPLE_USER)
        {
            $this->errno=WRONG_INPUT;           //checking for correct input
            return ;
        }
        if($this->privileges!=ADMINISTRATOR)
        {//user must be an administrator in order o perform this action
            $this->errno=INSUFFICIENT_RIGHTS;
            return ;
        }
        if(!($result=$this->con->query("SELECT user_id from user where username=\"$username\";"))) //does the user exist???
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())   //if result is empty, user not found
        {
            $result->close();
            $this->errno=USERNAME_DONT_EXIST;
            return ;
        }
        $result->close();
        if(!$this->con->query("UPDATE user SET privileges=$privileges WHERE username=\"$username\";"))
        {       //updating user privileges
            $this->errno=MYSQL_ERROR;   //mysql failure
            return ;
        }
        return ;
    }

////////////////////////////end of admin functions//////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////comunicate with///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
    public function communicate($to/*username of the recipient*/, $subject, $body)  //method that send mail to another user
    {
        $this->errno=DB_OK;     //setting errno to no error
        if($this->logged_in!=LOGGED_IN)
        {
            $this->errno=NOT_LOGGED_IN;
            return ;
        }//retrieving email of the recipient
        if(!($result=$this->con->query("SELECT email from user where username=\"$to\";")))  //retrieving mail and checks if user exists
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!($email=$result->fetch_row()))   //if result is empty user not found
        {
            $result->close();
            $this->errno=USERNAME_DONT_EXIST;
            return ;
        }
        $result->close();
        $header='FROM: no-reply@youcompare.com';//sending mail
        $title= "You have a New message from user: $this->username";//title
        if(!mail($email[0], $title, "Subject: $subject\n\n".$body, $header)) //sending the mail to the recipient
        {
            $this->errno=FAILED_TO_SEND_MAIL;   //failed to send mail
            return ;
        }
        return ;
    }
////////////////////////////end of comunicate with//////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////category rigths/////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
    public function getYourCategories()     //method that returns all closed categories that the user has privileges
    {
        $array = array();
        $this->errno=DB_OK;                 //setting error to no error
        if($this->logged_in!=LOGGED_IN)     //user must be logged in in order to do this action
        {
            $this->errno=NOT_LOGGED_IN;
            return $array;
        }
        if(!($result=$this->con->query("select ur.cat_id, ca.cat_name,ur.level_of_rights from user_has_rights ur,category ca where ca.cat_id = ur.cat_id and ur.user_id =".$this->id." order by ur.level_of_rights DESC;")))
        {                                   //mysql error
            $this->errno=MYSQL_ERROR;
            return $array;
        }  
        while ( $row = $result->fetch_row() )	//constructing result															//	initialize the structure with the data received from the database
        {
            $array[] = array($row[0],$row[1],$row[2]);
        }
        $result->close();       //closing mysql result
        return $array;              //returning resutl
    }
    
    public function pendingMembReq($cat_id)     //method that returns if the user has a pending member request for a specific category
    {
        $this->errno=DB_OK;     //setting error to no error
        if($this->logged_in!=LOGGED_IN)         //user must be logged in in order to do this action
        {
            $this->errno=NOT_LOGGED_IN;
            return ;
        }
        if(!($result=$this->con->query("SELECT cat_id from category WHERE cat_id=$cat_id;")))  //retrieving mail and checks if user exists
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())   //if result is empty category not found
        {
            $result->close();
            $this->errno=CATEGORY_DONT_EXIST;
            return ;
        }
        $result->close();
        if(!($result=$this->con->query("SELECT cat_id FROM pending_members WHERE user_id=$this->id AND cat_id=$cat_id;")))
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if($result->num_rows == 0)
        {                       //category hasn't pending requests
            return false;
        }
        else        ///category has pending requests
        {
            return true;
        }
    }

    public function updateMemberPriv($uid,$cat_id,$priv)    //update privileges of a user for a category
    {
        $this->errno=DB_OK;
        if($this->logged_in!=LOGGED_IN)         //user must be logged in in order to do this action
        {
            $this->errno=NOT_LOGGED_IN;
            return ;
        }
        if(!($result=$this->con->query("SELECT user_id from user where user_id=$uid;")))  //retrieving mail and checks if user exists
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())   //if result is empty user not found
        {
            $result->close();
            $this->errno=USERNAME_DONT_EXIST;
            return ;
        }
        $result->close();
        if(!($result=$this->con->query("SELECT cat_id from category WHERE cat_id=$cat_id;")))  //retrieving mail and checks if user exists
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())   //if result is empty category not found
        {
            $result->close();
            $this->errno=CATEGORY_DONT_EXIST;
            return ;
        }
        $result->close();
        if(!$this->con->query("UPDATE user_has_rights SET level_of_rights=$priv WHERE user_id=$uid AND cat_id=$cat_id;"))
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        return ;
    }

    public function deleteMember($uid,$cat_id)      //delete member from category
    {/*delete from user has rights*/
        $this->errno=DB_OK;
        if($this->logged_in!=LOGGED_IN) //user must be logged in in order to do this action
        {
            $this->errno=NOT_LOGGED_IN;
            return;
        }
        if(!($result=$this->con->query("SELECT user_id from user where user_id=$uid;")))  //retrieving mail and checks if user exists
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())   //if result is empty user not found
        {
            $result->close();
            $this->errno=USERNAME_DONT_EXIST;
            return ;
        }
        $result->close();
        if(!($result=$this->con->query("SELECT cat_id from category WHERE cat_id=$cat_id;")))  //retrieving mail and checks if user exists
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())   //if result is empty category not found
        {
            $result->close();
            $this->errno=CATEGORY_DONT_EXIST;
            return ;
        }
        $result->close();
        if(!$this->con->query("DELETE FROM user_has_rights WHERE user_id=$uid AND cat_id=$cat_id;"))
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return;
        }
        return;
    }
    
    public function deleteFromPending($uid,$cat_id)         //delete a user from the pending list
    { 
        $this->errno=DB_OK;
        if($this->logged_in!=LOGGED_IN)     //user must be logged in in order to do this action
        {
            $this->errno=NOT_LOGGED_IN;
            return;
        }
        if(!($result=$this->con->query("SELECT user_id from user where user_id=$uid;")))  //retrieving mail and checks if user exists
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())   //if result is empty user not found
        {
            $result->close();
            $this->errno=USERNAME_DONT_EXIST;
            return ;
        }
        $result->close();
        if(!($result=$this->con->query("SELECT cat_id from category WHERE cat_id=$cat_id;")))  //retrieving mail and checks if user exists
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())   //if result is empty category not found
        {
            $result->close();
            $this->errno=CATEGORY_DONT_EXIST;
            return ;
        }
        $result->close();
        if(!$this->con->query("DELETE FROM pending_members WHERE user_id=$uid AND cat_id=$cat_id;"))
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return;
        }
        return;
    }
    
    public function approveMemberReq($uid,$cat_id,$rights)
    {
        /* delete from pending memberships $uid,$cat_id, then add to user has rights*/
        $this->errno=DB_OK;
        if($this->logged_in!=LOGGED_IN)
        {
            $this->errno=NOT_LOGGED_IN;
            return;
        }
        if(!($result=$this->con->query("SELECT user_id from user where user_id=$uid;")))  //retrieving mail and checks if user exists
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())   //if result is empty user not found
        {
            $result->close();
            $this->errno=USERNAME_DONT_EXIST;
            return ;
        }
        $result->close();
        if(!($result=$this->con->query("SELECT cat_id from category WHERE cat_id=$cat_id;")))  //retrieving mail and checks if user exists
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return ;
        }
        if(!$result->fetch_row())   //if result is empty category not found
        {
            $result->close();
            $this->errno=CATEGORY_DONT_EXIST;
            return ;
        }
        $result->close();
        if(!$this->con->query("DELETE FROM pending_members WHERE user_id=$uid AND cat_id=$cat_id;"))
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return;
        }
        //* deleted from pending insert to user_has_rights *//
        if(!$this->con->query("INSERT into user_has_rights (cat_id, user_id, level_of_rights) VALUES($cat_id, $uid, $rights);"))
        {       //mysql error
            $this->errno=MYSQL_ERROR;
            return;
        }
        return;
    }
    
    public function becomeMemberReq($cat_id)    //methods that submits a request for a category
    {
        $this->errno=DB_OK;
        if($this->logged_in!=LOGGED_IN) //user must be logged in
        {
            $this->errno=NOT_LOGGED_IN;
            return;
        }
        if(!$this->con->query("INSERT INTO pending_members (user_id, cat_id) VALUES ($this->id, $cat_id);"))  
        {                       //submiting request
            $this->errno=MYSQL_ERROR;
            return ;
        }
    }
////////////////////////////end of category rights//////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
    }

    function check_username($username)      //check if a username allready exists in the database
    {
        $con= new mysqli("edudb.di.uoa.gr", "soft_tech", "#s@f+#", "soft_tech_db");//sindesi me mysql
        if($con->connect_errno)     //opening connection
        {
            return MYSQL_CONNECT_ERROR;//connect error
        }
        if(!($result=$con->query("SELECT user_id from user where username=\"$username\";")))
        {                           //returning user withe a specific username
            $con->close();
            return MYSQL_ERROR;     //mysql error
        }
        if($result->fetch_row())        //found a user with the given username
        {
            $result->close();
            $con->close();      //closing result & query
            return FOUND;       //username taken
        }
        $con->close();
        return NOT_FOUND;   //username not found
    }

    function check_email($mail) //check if an email allready exists in the database
    {
        $con= new mysqli("edudb.di.uoa.gr", "soft_tech", "#s@f+#", "soft_tech_db");//sindesi me mysql
        if($con->connect_errno)
        {
            return MYSQL_CONNECT_ERROR;//error
        }
        if(!($result=$con->query("SELECT user_id from user where email=\"$mail\";")))
        {                               //retrieving user with the specific email address
            $con->close();
            return MYSQL_ERROR;
        }
        if($result->fetch_row())    //expexting result of one row or empty
        {
            $result->close();
            $con->close();  //closing result & query
            return FOUND;   //mail allready exists
        }
        $con->close();
        return NOT_FOUND;   //mail not found
    }
?>