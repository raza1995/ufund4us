<?php
$use_constants_of = isset($_GET['use_constants_of']) ? $_GET['use_constants_of'] : ""; //it can be _dev 
require_once(dirname(__FILE__).'/constants'.$use_constants_of.'.php');

function issetCheck(&$ary, $key, $defVal=''){
	return checkSetInArrayAndReturn($ary, $key, $defVal);
}

function checkSetInArrayAndReturn(&$ary, $key, $defVal=''){
	$ary[$key] = isset($ary[$key]) ? $ary[$key] : $defVal;
	return $ary[$key];
}

function checkAndReturnOnly(&$ary, $key, $defVal=''){
	return isset($ary[$key]) ? $ary[$key] : $defVal;
}

function get_real_escape_string(&$mysqliCon, $str=''){
	if($str !='')
	$str = $mysqliCon->real_escape_string($str);

	return $str;
}

function get_set_real_escape_string_from_req($mysqliCon, &$data, $REQUEST, $key, $defVal){
	$str = checkSetInArrayAndReturn($REQUEST, $key, $defVal); 
	$data[$key] = get_real_escape_string($mysqliCon, $str); 
	// echo $str.'-'.$key.'-'.$defVal.'-'.$data[$key].'
	// ';
	return $data[$key];
}
function get_real_escape_string_from_req(&$mysqliCon, $REQUEST, $key, $defVal){
	$str = checkSetInArrayAndReturn($REQUEST, $key, $defVal);
	return get_real_escape_string($mysqliCon, $str); 
}

function checkAndSetInArray(&$ary, $key, $defVal=''){
	$ary[$key] = isset($ary[$key]) ? $ary[$key] : $defVal;
}

function issetOnArrayCheck(&$ary, $keysAry, $defVal){
	// echo "<pre>"; print_r($keysAry);
	foreach($keysAry as $kaKey=>$key){
		// var_dump($ary[$key]);
		$ary[$key] = isset($ary[$key]) ? $ary[$key] : $defVal;
		// echo "Key--------------".$key; 
		// die();
	}
	//print_r($ary); echo "</pre>";
	// echo "defVal:".$defVal;
	// die();
	return $ary;
}

//When index has empty value on index it will set with default value
function issetAndNotOnEmptyArrayCheck(&$ary, $keysAry, $defVal){
	foreach($keysAry as $kaKey=>$key){
		if( isset($ary[$key]) && $ary[$key] != ""){
		 	// $ary[$key] =  $ary[$key];
		}
		else{
			$ary[$key] =  $defVal;
		}
	}
	return $ary;
}

function getimagesizeWithoutError($imgURL){

	$eCurState = null;
	if(CAN_CHANGE_ERROR_REPORTING_FOR_GET_IMAGE_SIZE){
		$eCurState = error_reporting();
		//disable error reporting only for getimagezie method, after call of it again set EaLL
		error_reporting(0);
	}

	$data = getimagesize($imgURL);
	// echo var_dump($data); die();

	if(CAN_CHANGE_ERROR_REPORTING_FOR_GET_IMAGE_SIZE){
		error_reporting($eCurState);//revet old status
	}

	return $data;
}

function filesizeWithoutError($URL){

	$eCurState = null;
	if(CAN_CHANGE_ERROR_REPORTING_FOR_GET_IMAGE_SIZE){
		$eCurState = error_reporting();
		//disable error reporting only for getimagezie method, after call of it again set EaLL
		error_reporting(0);
	}

	$data = filesize($imgURL);
	// echo var_dump($data); die();

	if(CAN_CHANGE_ERROR_REPORTING_FOR_GET_IMAGE_SIZE){
		error_reporting($eCurState);//revet old status
	}

	if($data === false){
		return 0;
	}
	else{
		return $data;
	}
}



//SET variables in associative array,compatibility brige b/w old style call and new
function setVariableInAssocArrayForMpdf($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P'){
	$configForMpdf = array(
					'mode'=>$mode,
					'format'=>$format,
					'default_font_size'=>$default_font_size,
					'default_font'=>$default_font,
					'mgl'=>$mgl,
					'mgr'=>$mgr,
					'mgt'=>$mgt,
					'mgb'=>$mgb,
					'mgh'=>$mgh,
					'mgf'=>$mgf,
					'orientation'=>$orientation
				);
	return $configForMpdf;
}

 // die("3--". dirname(__FILE__) );  //3--D:\xampp\htdocs\ufund4us.com\app
//require_once(dirname(__FILE__)."/../constants_on_main.php");

//Load libraries inside the vendor folder
/*
- Right now we only have mpdf, calling from following files 
app\automation_donation_reconciliation.php:
app\cms\build_team.php:
app\cms\generatepdf.php:
app\cron2.php:
app\cron4.php:

*/

//if user id is not set in session then redirect on sign-in page
function isLogin(){
	if( isset($_SESSION['uid']) && $_SESSION['uid'] > 0 ) {
		return true;
	}
	else{
		return false;
	}
}

function get_after_app_fee_percentage($aRow){
	$app_fee_percentage = DEFAULT_APP_FEE;//20%
	if( isset($aRow['app_fee_percentage']) ){
	    $app_fee_percentage = $aRow['app_fee_percentage'];
	}
	$after_app_fee_percentage = 1;
	if($app_fee_percentage > 0){
		$after_app_fee_percentage = 1 - $app_fee_percentage/100; 
	}
	return $after_app_fee_percentage;
}
require_once __DIR__ . '/vendor/autoload.php';
?>