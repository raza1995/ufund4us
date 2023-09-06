<?php 
date_default_timezone_set('America/Los_Angeles'); //TimeZone
$benchmark = 1; //0 = Off, 1 = On
$stime = microtime();
$stime = explode(' ', $stime);
$stime = $stime[1] + $stime[0];
$sstart = $stime;


//######## Error reporting Enable / Disable section ########
// $_REQUEST['error_reporting'] = 'E_ALL'; //error_reporting=E_ALL
// $_REQUEST['error_reporting'] = 'E_ALL__AND__E_DEPRECATED'; //error_reporting=E_ALL__AND__E_DEPRECATED
$error_reporting = isset($_REQUEST['error_reporting']) ? $_REQUEST['error_reporting'] : ''; 
if($error_reporting == 'E_ALL'){  
	error_reporting(E_ALL);
}
else if($error_reporting == 'E_ALL__AND__E_DEPRECATED'){  
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}
else{
	error_reporting(0);
}

//true: don't show error during process of getting image size
define('CAN_CHANGE_ERROR_REPORTING_FOR_GET_IMAGE_SIZE', true);



$DB_host = "localhost";
$production = 1;
$define_bank_details = "live"; //"live" / "test"

//when trying to use project on local mechine
if( isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'localhost'){
	$production = 0;
	$define_bank_details = "test";
}

// echo $production; die();
define('sWEBSITENAME','Ufund4Us');
define('SITE_TITLE_WITH_ADMIN',"UFund4Us Administrator");
define('sHOMELIVE', "https://ufund4us.com/app");//it must be live url(not localhost)

if($production == '1')
{	
	if(true){ //app, live
	 	define('DIR_NAME', 'app/');
		$DB_name = "ufund45_ufundapp";

		
		//Live keys
		define("STRIPE_API_KEY","sk_live_yIeyziLGJGXrFgU5usArxqen");//Secret key //Stripe details of: 
		define("STRIPE_API_VERSION","2019-11-05");

		$define_bank_details = "live";

	}
	else{ //test 
		define('DIR_NAME', 'test/');
		$DB_name = "ufund45_test1ufund";

		//Testing keys
		define("STRIPE_API_KEY","sk_test_qvQbBWRgDrFNSMIdY8TPdHRp");//Secret key //Stripe details of:
		define("STRIPE_API_VERSION","2019-11-05");

		$define_bank_details = "test";

	}

	$DB_user = "ufund45_ufund";
	$DB_pass = "UFund4US..1";

	define('SITE_DOMAIN', 'ufund4us.com');
	define('SITE2_DOMAIN', SITE_DOMAIN);//Main site domain
	define('SITE_DOMAIN_CAP','UFUND4US.COM');
	define('SITE_URL','https://'.SITE_DOMAIN.'/');//https://ufund4us.com/
	define('SITE_FULL_URL','https://www.'.SITE_DOMAIN.'/');//https://www.ufund4us.com/

	define('sHOME',SITE_URL.DIR_NAME);
	define('sHOMES',SITE_URL.DIR_NAME);
	define('sHOMECMS',SITE_URL.DIR_NAME.'cms/');
	define('sHOMESCMS',SITE_URL.DIR_NAME.'cms/');
	define('sENC_KEY','30btrigno');

	define('PROJECT_ENV',"PRODUCTION");
	define("SUPPORT_PHONE",'+19519668262');	
	define("SUPPORT_NUMBER_4_DISPLAY",'+19519668262');//SUPPORT_NUMBER_4_DISPLAY;'888-419-1008'
}
else{
	define('DIR_NAME', 'app/');
	$DB_user = "root";
	$DB_pass = "";
	$DB_name = "ufund-25feb2020";

	define('SITE_DOMAIN', 'ufund4us.com');	
	define('SITE2_DOMAIN', SITE_DOMAIN);//Main site domain
	define('SITE_DOMAIN_CAP','UFUND4US.COM');
	define('SITE_URL','https://localhost/ufund4us.com/');
	define('SITE_FULL_URL','https://localhost/ufund4us.com/');
	define('sHOME',SITE_URL.DIR_NAME);
	define('sHOMES',SITE_URL.DIR_NAME);
	define('sHOMECMS',SITE_URL.DIR_NAME.'cms/');
	define('sHOMESCMS',SITE_URL.DIR_NAME.'cms/');
	define('sENC_KEY',"30btrigno");

	define('PROJECT_ENV',"LOCALHOST");

	//Testing keys
	define("STRIPE_API_KEY","sk_test_qvQbBWRgDrFNSMIdY8TPdHRp");//Secret key //Stripe details of:
	define("STRIPE_API_VERSION","2019-11-05");

	define("SUPPORT_PHONE",'+19519668262');	
	define("SUPPORT_NUMBER_4_DISPLAY",'+19519668262');	
}

if($define_bank_details == 'test'){
	define("ROUTING_NUMBER","110000000");
	define("ACCOUNT_NUMBER","000123456789");
	define('ACCOUNT_NAME', 'Bart Garrett');
	define('BANK_NAME', 'BB&T Bank');

}
else if($define_bank_details == 'live'){
	define("ROUTING_NUMBER","122239131");
	define("ACCOUNT_NUMBER","009474122117");
	define('ACCOUNT_NAME', 'Bart Garrett');
	define('BANK_NAME', 'BB&T Bank');
}

//defined in code, basic info line# 581, need to ask from kurt for it, because will use main routing details rather the these 2nd
define("ROUTING_NUMBER2","111000025");
define("ACCOUNT_NUMBER2","000123456789");
define('ACCOUNT_NAME2', 'Jane Austen');
define('BANK_NAME2', '');


define("Instructions1_PDF", sHOME.'cms/UFund4Us Instructions.pdf');
define("Instructions2_VU_PDF", sHOME.'cms/Video_Upload_Instructions_UFund4Us.pdf');
define("COPY_RIGHT_YEAR", date('Y'));

define('DB_USER',$DB_user);
define('DB_PASS',$DB_pass);
define('DB_NAME',$DB_name);

if(true){
	//Discount card twilio 
	define("TWILIO_ID","AC90cd64a5aec3e2248e29f6f5c7f13ca5");
	define("TWILIO_TOKEN","76b93e6e899512f2c3567ea9c8a317e7");
	define("TWILIO_PHONE_NUMBER","+16066719838");
	

	define("SPARK_POST_KEY","3dec808848252dc06072dfdf85b74c8cd04cafbb");
}
else{
	// //Ufund twilio
	define("TWILIO_ID","AC88602ed0bd5934afed50a39b476c16b8");
	define("TWILIO_TOKEN","a1ecd75336521e5bc15bb6f4c507f2e3");
	define("TWILIO_PHONE_NUMBER","+19097669348");

	define("SPARK_POST_KEY","3dec808848252dc06072dfdf85b74c8cd04cafbb");
}

define('SITE_FULL_URL_FOR_STRIPE_BUSINESS_PROFILE',sHOMELIVE);

//FB details are sign from abdulrauf618 
define("FACEBOOK_APP_SECRET","3304751bc6ac8d0350b4cca0ba25c467");
define("FACEBOOK_APP_ID","1309410545750631");


define('SITE_PHONE', '(704)650-0543');

//abdurraoof.rv@gmail.com
define('CAPTCH_SITE_KEY', "6Ld2uc0UAAAAAGUN9faX0QKCBamQ0HLv6IJVfcB3");
define('CAPTCH_SECRET', "6Ld2uc0UAAAAABe4xDHhFR8GD4uZfD0-HpV2fs_s");

define('KURT_EMAIL', "kurt@ufund4us.com");
define('KURT_NAME_IN_EMAIL', "Kurt Gairing");
define('INFO_EMAIL', "info@ufund4us.com");
define('INFO_EMAIL_NAME', "UFund4Us CRON");
define('NO_REPLY_EMAIL', 'no-reply@ufund4us.com');

define('EMAILS_EMAIL', 'emails@ufund4us.com');
define('EMAILS_NAME_IN_EMAIL', 'Ufund4Us');

define('FAST_PAY_EMAIL', 'fastpay@ufund4us.com');
define('FAST_PAY_NAME_IN_EMAIL', 'FastPay Administrator');

//Emails for sparkpost
define('INFO_EMAIL_SP', 'info@email.ufund4us.com');

define('TEST_EMAIL_1', 'raoof@lyja.com');
define('TEST_NAEM_OF_EMAIL_1', 'Rauf');

define('TEST_EMAIL_2', TEST_EMAIL_1);

define('WEB_EMAIL_1', EMAILS_EMAIL);
define('CLIENT_EMAIL_1', "ufund@myrasolutions.com");
define('CLIENT_1_NAME', "Raj Tuli");//Raj Tuli

define('CLIENT_EMAIL_2', CLIENT_EMAIL_1);
define('CLIENT_2_NAME', CLIENT_1_NAME);

define('TOC_DOCUMENT_LINK', SITE_URL.DIR_NAME.'cms/TOC_Fundme_R1.1.2_Rev_10-2016.pdf');


define('DEFAULT_APP_FEE', 20);
// this is value, where we use it, need to use in percentage, for it 20/100=0.2
/**
Following files has included constants, if these files are included we can use defined constants in it, for testing echo this, 
echo 'HI-'.PROJECT_ENV; die();
//include("php/dbconn.php");
*/
?>