<?php
////////////////////////////////////////////////////////////////////
/////////////////////USER PRIVILEGES////////////////////////////////
define("LOGIC_GUEST_VIEWER",-1);              /**/
define("LOGIC_REGISTERED",0);                 /**/
define("LOGIC_CATEGORY_MEMBER",	1 );          /**/
define("LOGIC_EDITOR_MEMBER",	2 );          /**/
define("LOGIC_SUB_MODERATOR",	3 );          /**/
define("LOGIC_MODERATOR",	4 );          /**/
define("LOGIC_ADMINISTRATOR",   5 );          /**/
/************************************************/
    define("ADMINISTRATOR", 1);
    define("SIMPLE_USER", 0);
    define("UNVERIFIED_USER", -1);
    define("DELETED_USER",-2);
//////////////////////////Data classes//////////////////////////////
class profile_info          //a class that contains all vital info that belongs to a user
{
	public $username;
	public $name;
	public $surname;
	public $email;
	public $privileges;
}

class category_info         //a class that contains all vital info that belongs to a category
{
	public $cat_name;
	public $cat_keywords;
	public $is_open;
	public $cat_description;
	public $attr_info_array;
	public $rate;		
	public $cat_image;
	public $cat_video;
	public $cat_id;
};

class attribute_info    //a class that contains all vital info that belongs to an attribute
{
	public $name;
	public $description;
	public $comparability;
	public $type_elements;
	public $is_filterable;
	public $id;
    public $default_weight;
}

class entity_info       //a class that contains all vital info that belongs to an entity
{
	public $entity_id;
	public $entity_name;
	public $entity_description;
	public $entity_attribute_values;
	public $entity_image;
	public $entity_video;
        public $rate;
};

//found or not a username or email
define("FOUND", 1);
define("NOT_FOUND", 0);

//category openess
define("OPEN", 1);
define("CLOSE", 0);

// rights upon category
define( "NO_RIGHTS_UPON_CATEGORY", -1 );
define( "CATEGORY_MEMBER",	0 );
define( "EDITOR_MEMBER"	 ,	1 );
define( "SUB_MODERATOR"	 ,	2 );
define( "MODERATOR"		 ,	3 );

// comparability
define( "UNCOMPARABLE"	 ,	0 );
define( "DISTINCT"	 	 ,	1 );
define( "COUNTABLE"	 	 ,	2 );

//type of compare
define( "BIG_IS_BETTER"	 ,	0 );
define( "LOWER_IS_BETTER"	 	 ,	1 );
define( "MIDDLE_VALUE"	 	 ,	2 );

//Sorting methods
define("ALPHABETICAL", 0);
define("POPULARITY", 1);
define ("NUMBER_OF_ENTITIES", 2);

//Errors
define("LOGGED_IN",0);                      //User logged in
define("DB_OK", 0);                         //No error
define("NOT_LOGGED_IN", -1);                //User is not logged in
define("MYSQL_CONNECT_ERROR", -2);          //Failed to connect to mysql server
define("WRONG_USERNAME_OR_PASSWORD", -3);   //
define("MYSQL_ERROR", -4);                  //A mysql query failed
define("USERNAME_ALLREADY_EXISTS", -5);     //The given username belongs to another user
define("INSUFFICIENT_RIGHTS", -6);
define("USERNAME_DONT_EXIST", -7);          //The requested user don't exist
define("USER_ALLREADY_VERIFIED", -8);       //User has verified his account
define("ALLREADY_RATED", -9);               //User has allready rated the category or the entity
define("ENTITY_DONT_EXIST", -10);           //The requested entity don't exist
define("CATEGORY_DONT_EXIST", -11);         //The requested category don't exist
define("USER_HAS_ALLREADY_RIGTHS", -12);               //User has allready rigths for this category
define("EMAIL_ALLREADY_EXISTS", -13);       //Email belongs to another user
define("NO_MATCH_FOUND", -14);
define("UNEXPECTED_RESULTS_ERROR", -15);    //Database returned unexpected result
define("EMPTY_VALUE", -16);
define("VALUE_OUT_OF_BOUNDS", -17);         //the given value of an entity is out of the given bounds
define("ENTITY_EXISTS", -18);               //an enity with the same name allready exist for this category
define("WRONG_INPUT",-19);                  //unexpected input given
define("NOT_ENOUGH_ATTRIBUTES", -20);
define("CATEGORY_EXISTS", -21);             //category with the given name exists
define("ATTRIBUTE_DOESNT_EXIST", -22);      //requested attribute not found
define("ATTRIBUTE_EXISTS", -23);            //an attribute with the same name allready exist for this category
define("COMPARABILITY_ERROR", -24);
define("WRONG_ID", -25);
define("ALLREADY_LOGGED_IN", -26);          //User is allredy logged in
define("FAILED_TO_SEND_MAIL", -27);         //Failed to sent a mail to the user
define("CATEGORY_CLOSED",-28);
?>