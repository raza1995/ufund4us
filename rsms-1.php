<?php


date_default_timezone_set('America/Los_Angeles'); //TimeZone
//date_default_timezone_set('Asia/Karachi'); //TimeZone
//set_time_limit(0);
//error_reporting(-1); //Reporting
ini_set('max_execution_time', 999999); //7200 seconds = 120 minutes
ini_set('memory_limit', '1536M');
include("cms/php/dbconn.php");
include_once('cms/classes/class.phpmailer.php');
include("lib/vendor/autoload.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if( !isset($_REQUEST['number']) ){
    echo 'dafa hojao'; die();
}

$number = $_REQUEST['number'];
$body = "rufi-testing-".date('Y-m-d h:i:s');
echo 'sms send respnose <pre>'; print_r( send_sms($number,$body) ); die();

?>