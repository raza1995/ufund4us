<?php
ob_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
//ini_set("output_buffering", 1);
require_once("cms/php/dbconn.php");
function recallmysql() {
	if (!mysqli_ping($conn1)) {
		$conn1 = mysqli_connect($DB_HOST1, $DB_USER1, $DB_PASS1) or die("<div>MySQL Error: Oops! UNABLE to CONNECT to the DATABASE!</div>");
		mysqli_select_db($conn1, $DB_NAME1) or die("<div>MYSQL ERROR: Oops! Database access FAILED!</div>");
		mysqli_set_charset($conn1, 'utf8') or die("<div>UNABLE to SET database connection ENCODING!</div>");
	} 
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
if ( strtolower( $_SERVER["HTTPS"] ) != "on" ) {
    exit(header("location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}"));
}
//Getting HashKey Only
if (isset($_REQUEST["v"]) && $_REQUEST["v"] != '') {
	$hashkey = xss_clean($_REQUEST["v"]);
	//Generate Campaign Participant Link
	if (isset($_REQUEST["u"]) && $_REQUEST["u"] != '') {
		$pid = xss_clean($_REQUEST["u"]);
		//Generate Campaign Participant Donor Link
		if (isset($_REQUEST["d"]) && $_REQUEST["d"] != '') {
			$did = xss_clean($_REQUEST["d"]);
			$QueryLinks="SELECT * 
			FROM tbl_campaign c
			INNER JOIN tbl_donors_details d ON d.cid = c.fld_campaign_id
			WHERE c.fld_campaign_hashkey = '$hashkey' AND d.puid = '$pid' AND d.uid = '$did'
			LIMIT 1";
			$ResultLinks= mysqli_query($conn1, $QueryLinks) or die("ERROR: Cannot fetch the link records...!");
			$ResultLinksRows = mysqli_num_rows($ResultLinks);
			if ($ResultLinksRows > 0) {
				$Rows = mysqli_fetch_array($ResultLinks);
				$shortlink = $Rows['fld_campaign_hashkey'];
				$cidhash = $Rows['fld_hashcamp'];
				$cid = $Rows['fld_campaign_id'];
				$longlink = "".sHOME."campaign.php?cid=".$cidhash."|".$cid."|".$pid."&hashid=".$did."";
				exit(header("location: {$longlink}"));
			}
		}
		if (isset($_REQUEST["m"]) && $_REQUEST["m"] != '') {
			$QueryLinks="SELECT * 
			FROM tbl_campaign c
			INNER JOIN tbl_participants_details p ON p.cid = c.fld_campaign_id
			WHERE c.fld_campaign_hashkey = '$hashkey' AND p.pid = '$pid' 
			LIMIT 1";
			$ResultLinks= mysqli_query($conn1, $QueryLinks) or die("ERROR: Cannot fetch the link records...!");
			$ResultLinksRows = mysqli_num_rows($ResultLinks);
			if ($ResultLinksRows > 0) {
				$Rows = mysqli_fetch_array($ResultLinks);
				$shortlink = $Rows['fld_campaign_hashkey'];
				$cid = $Rows['fld_campaign_id'];
				$longlink = "".sHOME."signup.php?cid=$cid&refferalid=$pid";
				exit(header("location: {$longlink}"));
			}
		}
	}	
} else {
	exit(header("location: https://{$_SERVER['SERVER_NAME']}"));
}
ob_end_flush();
?>