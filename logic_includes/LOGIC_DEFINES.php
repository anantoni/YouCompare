<?php
define("LOGIC_OK",0);

/************** SEARCH ERROR CODES ************/
define("EMPTY_QUERY",-1);                   /**/
define("LONG_QUERY",-2);                    /**/
define("SHORT_QUERY",-3);                   /**/
define("EMPTY_SEARCH_QUERY",-4);            /**/
define("WRONG_SORT_WAY",-5);                /**/
define("WRONG_SORT_METHOD",-6);             /**/
define("WRONG_PAGE",-7);                    /**/
define("WRONG_RESULTS_PER_PAGE",-8);        /**/
define("NO_RESULTS",-9);                    /**/
define("SQL_ERROR",-10);                    /**/
/**********************************************/

/******* SEARCH DEFINED VARIABLES *************/
define("SORT_ALPHABETICAL",0);              /**/
define("SORT_POPULARITY",1);                /**/
define("SORT_NUM_ENTITIES",2);              /**/
define("SORT_SEARCH_RANK",3);               /**/
define("SORT_ASCENDING",1);                 /**/
define("SORT_DESCENDING",0);                /**/
define("SEARCH_MAX_WORDS", 20);             /**/
define("SEARCH_MIN_INPUT", 2);              /**/
define("SEARCH_MAX_INPUT", 200);            /**/
define("SEARCH_WORD_MIN_LENGTH", 2);        /**/
define("MAX_SAVED_SEARCHES",3);             /**/
         /**/
/**********************************************/


/********* VISIT CATEGORY ERROR CODES ***********/
define("NO_CATEGORY_ID_GIVEN",1);             /**/
define("CATEGORY_DOES_NOT_EXIST",-3);         /**/
//define("WRONG_SORT_WAY",-5);                /**/
//define("WRONG_SORT_METHOD",-6);             /**/
//define("WRONG_PAGE",-7);                    /**/
//define("WRONG_RESULTS_PER_PAGE",-8);        /**/
//define("NO_RESULTS",-9);                    /**/
//define("SQL_ERROR",-10);                    /**/
define("SORT_BY_ATTRIBUTE",2);                /**/
/**********************************************/


/*check attributes/entities */
define("BAD_INPUT",-11);
define("LONG_INPUT",-12);
define("SHORT_INPUT",-13);

?>