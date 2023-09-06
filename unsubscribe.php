<?
require_once("configuration/dbconfig.php");
require_once("configuration/message.php");

$REQUEST = &$_REQUEST;	
//Declare variable required bellow


if (isset($REQUEST['cid']) && is_numeric($REQUEST['cid'])) {
	$cid = xss_clean($REQUEST['cid']);
} else {
	$cid = 0;
}
if (isset($REQUEST['pid']) && is_numeric($REQUEST['pid'])) {
	$pid = xss_clean($REQUEST['pid']);
} else {
	$pid = 0;
}
if (isset($REQUEST['did']) && is_numeric($REQUEST['did'])) {
	$did = xss_clean($REQUEST['did']);
} else {
	$did = 0;
}
$isvalid = 0;
if ($cid > 0 && $pid > 0 || $did > 0) {
	if ($pid > 0 && $did > 0) { //Donor Details
		$CheckDetail = $oregister->getdonorslist($cid,$pid,$did);
		$CheckDetailCount = count($CheckDetail);
		if ($CheckDetailCount == 0) {
			$UserDetail = $oregister->getuserdetail($did);
			$name = $UserDetail['fld_name']." ".$UserDetail['fld_lname'];
			$isvalid = 1;
		} else {
			$isvalid = 0;
			$name = $CheckDetail['uname']." ".$CheckDetail['ulname'];
		}
	} elseif ($pid > 0 && $did == 0) { //Participant Details
		$CheckDetail = $oregister->getparticipantslist($cid,$pid);
		$CheckDetailCount = count($CheckDetail);
		if ($CheckDetailCount == 0) {
			$UserDetail = $oregister->getuserdetail2($pid);
			$name = $UserDetail['uname']." ".$UserDetail['ulname'];
			$isvalid = 1;
		} else {
			$isvalid = 0;
			$name = $CheckDetail['uname']." ".$CheckDetail['ulname'];
		}
	} elseif ($pid == 0 && $did > 0) { //Donor Details
		$CheckDetail = $oregister->getdonorslist2($cid,$did);
		$CheckDetailCount = count($CheckDetail);
		if ($CheckDetailCount == 0) {
			$UserDetail = $oregister->getuserdetail($did);
			$name = $UserDetail['fld_name']." ".$UserDetail['fld_lname'];
			$isvalid = 1;
		} else {
			$isvalid = 0;
			$name = $CheckDetail['uname']." ".$CheckDetail['ulname'];
		}
	}
	if ($isvalid == 1) {
		$oregister->unsubscribe($cid, $pid, $did);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0031)<?php echo SITE_URL;?>contact-us/ -->
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Unsubscribe</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.css">
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>  
<link href="css/style.css" rel="stylesheet" type="text/css"> 

<link href="css/ninja-slider.css" rel="stylesheet" type="text/css">
<script src="js/ninja-slider.js" type="text/javascript"></script>
</head>
<body>
<? include_once('header.php');?>
<section class="ipcontentsection">
	<div class="container newContainer">
		<div class="mid_sec2">
			<div class="mid_secin">
				<div class="row2">
					<h3>Thanks</h3>
					<?php if ($isvalid == 1 || $isvalid == 0) { ?>
					<p><?=$name;?></p>
					<?php } ?>
					<?php if ($isvalid == 1) { ?>
					<p><?=$aMessage[11];?></p>
					<?php } elseif ($isvalid == 0) { ?>
					<p><?=$aMessage[26];?></p>
					<? } ?>
					<p> Click here to <a href="sign-in.php">Sign In</a></p>
					<div></div>
				</div>
			</div>
		</div>
	</div>
</section>  
<? include_once('footer.php');?>
</body>
</html>