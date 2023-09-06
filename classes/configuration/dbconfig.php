<?php
require_once(dirname(__FILE__).'/../../functions_constants.php');
include(dirname(__FILE__).'/db_connection_mysqli.php');
include(dirname(__FILE__).'/db_connection_pdo.php');


session_start();

/*
$DB_host = "localhost";
$DB_user = "stagineo_aryan";
$DB_pass = "iru&Zni3WdX8";
$DB_name = "stagineo_university";
define('sHOME','staging77.com.cp-in-4.webhostbox.net/educationinsta/');
define('sWEBSITENAME','Education Insta.Io | The New Way To Explore An Education');
try
{
	$DB_con = new PDO("mysql:host={$DB_host};dbname={$DB_name}",$DB_user,$DB_pass);
	$DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
	echo $e->getMessage();
}

*/


$aTmpUri = explode("/", trim($_SERVER['REQUEST_URI']));

if(in_array("webpanel", $aTmpUri))
{
		include_once '../classes/class.user.php';
		$user = new USER($DB_con);
		
		include_once '../classes/class.register.php';
		$oregister = new REGISTER($DB_con);
		
		include_once '../classes/class.university.php';
		$ouniversity = new UNIVERSITY($DB_con);
		
		include_once '../classes/class.gallery.php';
		$ogallery = new GALLERY($DB_con);
		
		include_once '../classes/class.review.php';
		$oreview = new REVIEW($DB_con);
		
		include_once '../classes/class.banner.php';
		$obanner = new BANNER($DB_con);
		
		include_once '../classes/class.page.php';
		$opage = new PAGE($DB_con);
		
		include_once '../classes/class.blog.php';
		$oblog = new BLOG($DB_con);

		include_once '../classes/class.news.php';
		$onews = new NEWS($DB_con);
		
		include_once '../classes/class.testimonial.php';
		$otestimonial = new TESTIMONIAL($DB_con);
		
		include_once '../classes/class.category.php';
		$ocategory = new CATEGORY($DB_con);



}
else
{
        include_once 'classes/class.user.php';
		$user = new USER($DB_con);
		
		include_once 'classes/class.register.php';
		$oregister = new REGISTER($DB_con);
		
		include_once 'classes/class.university.php';
		$ouniversity = new UNIVERSITY($DB_con);
		
		include_once 'classes/class.gallery.php';
		$ogallery = new GALLERY($DB_con);
		
		include_once 'classes/class.review.php';
		$oreview = new REVIEW($DB_con);
		
		include_once 'classes/class.banner.php';
		$obanner = new BANNER($DB_con);
		
		include_once 'classes/class.page.php';
		$opage = new PAGE($DB_con);
		
		include_once 'classes/class.blog.php';
		$oblog = new BLOG($DB_con);
		
		include_once 'classes/class.news.php';
		$onews = new NEWS($DB_con);
		
		include_once 'classes/class.testimonial.php';
		$otestimonial = new TESTIMONIAL($DB_con);
		
		include_once 'classes/class.category.php';
		$ocategory = new CATEGORY($DB_con);


}
