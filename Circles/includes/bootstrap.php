<?php

//Inlcude config File
require_once(dirname(__FILE__) . "/config.php");
//Load Main Functions Here
require_once("functions/general.php");

//Auto Load Classes Here
 // you don't want to display errors on a prod environment
error_reporting(0);

function __autoload($className)
{
   
        include "classes/class.". $className . ".php";
       
}





/*set_error_handler("customError");*/




date_default_timezone_set("Asia/Kolkata");//INDIA --- :)
?>