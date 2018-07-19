<?php
/**
* Config File Database Information and Super Global Array , paths etc.
* 
* 
*/

//Database Info    
if(!defined('DATABASE_HOST'))
    define('DATABASE_HOST', 'localhost');
if(!defined('DATABASE_USERNAME'))
    define('DATABASE_USERNAME', 'platetn1_circle');
if(!defined('DATABASE_PASSWORD'))
    define('DATABASE_PASSWORD','circle'); 
if(!defined('DATABASE_NAME'))
    define('DATABASE_NAME', 'platen1_circles');


if(!defined('ROOT_SITE_COMPLETE'))
{
    define('ROOT_SITE_COMPLETE','http://platestheapp.com/circles/');
}


//Define Access token

define('CODE_ACCESS_TOKEN',"I2S0215WMAB08");
// Define Image Sizes
define('MAX_IMAGE_WIDTH', 3000);
define('MAX_IMAGE_HEIGHT', 3000);
define('PROFILE_IMAGE_WIDTH', 230);
define('PROFILE_IMAGE_HEIGHT', 230);
define('POST_IMAGE_WIDTH', 400);
define('POST_IMAGE_HEIGHT', 300);
define('MAX_POST_IMAGE_WIDTH', 677);
define('MAX_POST_IMAGE_HEIGHT', 525);
define('IMAGE_THUMBNAIL_WIDTH', 200);
define('IMAGE_THUMBNAIL_HEIGHT', 200);
//Main Super Global Array
$KONNECT_GLOBALS = array(
 //Image Types
    'imageTypes'            => array(
                                    'jpg',
                                    'jpeg',
                                    'png',
                                    'gif'
                                )
    
    
);
?>