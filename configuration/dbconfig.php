<?
require_once(dirname(__FILE__).'/../functions_constants.php');

include(dirname(__FILE__).'/db_connection_mysqli.php');
include(dirname(__FILE__).'/db_connection_pdo.php');

session_start();

//SparkPost Init
require ('lib/vendor/autoload.php');
//SparkPost Init
//Twilio Init Function
function send_sms($number,$body)
{
    $ID = TWILIO_ID;
	$token = TWILIO_TOKEN;
	$twilio_number = TWILIO_PHONE_NUMBER;
	$url = 'https://api.twilio.com/2010-04-01/Accounts/' . $ID . '/Messages.json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

    curl_setopt($ch, CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD,$ID . ':' . $token);

    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        'To=' . rawurlencode('+1' . $number) .
        '&From=' . rawurlencode($twilio_number) .
        '&Body=' . rawurlencode($body));

    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode($resp,true);
}
//Twilio Init Function

$aTmpUri = explode("/", trim($_SERVER['REQUEST_URI']));

include_once __DIR__.'/../classes/class.user.php';
$user = new USER($DB_con);

include_once __DIR__.'/../classes/class.register.php';
$oregister = new REGISTER($DB_con);	

include_once __DIR__.'/../classes/class.campaign.php';
$oCampaign = new CAMPAIGN($DB_con);	

include_once __DIR__.'/../classes/class.application_settings.php';
$application_settings = new application_settings($DB_con);	

include_once __DIR__.'/../classes/class.check_payments.php';
$check_payments = new check_payments($DB_con);	

include_once(__DIR__.'/../configuration/message.php');

function sanitize($string) {
	$text = str_replace(',', '', $string);
	//$string2 = html_entity_decode($string);
	$string1 = htmlentities($text, ENT_QUOTES, 'UTF-8');
	//$return = htmlentities($string1, ENT_QUOTES);
	return $string1;
}

function xss_clean($data)
{
// Fix &entity\n;
$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

// Remove any attribute starting with "on" or xmlns
$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

// Remove javascript: and vbscript: protocols
$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

// Remove namespaced elements (we do not need them)
$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

do
{
    // Remove really unwanted tags
    $old_data = $data;
    $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
}
while ($old_data !== $data);

// we are done...
$returndata = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
return $data;
}

if (isset($_GET['ref']) || isset($_REQUEST['cid']) || isset($_REQUEST['link'])) {
	if (isset($_GET['ref'])) {
		$getbrand = $oregister->getbrand($_GET['ref']);
	} elseif (isset($_REQUEST['cid'])) {
		$getbrand = $oregister->getbrand1($_REQUEST['cid']);
		if ($getbrand == '') {
			$getbrand = $oregister->getbrandbyuid($_REQUEST['cid']);
		}
	} elseif (isset($_REQUEST['link'])) {
		$decoder = base64_decode($_REQUEST['link']);
		$get_decoded = explode("&",$decoder);
		$uid = str_replace('uid=','',$get_decoded[0]);
		$getbrand = $oregister->getbrand2($uid);
	}
	if ($getbrand == '') {
		$getname = '';
		$getlogo = 'images/logo_print.png';
		$getphone = '';
		$getemail = '';
		$getcname = sWEBSITENAME;
	} else {
		$getname = $getbrand['fld_name']." ".$getbrand['fld_lname'];
		$getlogo = "cms/uploads/brandlogo/".$getbrand['fld_brand_logo_header'];
		$getphone = $getbrand['fld_phone'];
		$getemail = $getbrand['fld_email'];
		$getcname = $getbrand['fld_cname'];
	}
} else {
	$getname = '';
	$getlogo = 'images/logo_print.png';
	$getphone = '';
	$getemail = '';
	$getcname = sWEBSITENAME;
}