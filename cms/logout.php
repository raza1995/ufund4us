<?
require_once("../configuration/dbconfig.php");
if (isset($_SESSION['bkp_enable']) && $_SESSION['bkp_enable'] == 1) {
	//Create Old Sessions
	$_SESSION['uid'] = $_SESSION['bkp_uid'];
	$_SESSION['uname'] = $_SESSION['bkp_uname'];
	$_SESSION['ulname'] = $_SESSION['bkp_ulname'];
	$_SESSION['role_id'] = $_SESSION['bkp_role_id'];
	
	//Destroy New Session
	unset($_SESSION['bkp_enable']);
	unset($_SESSION['bkp_uid']);
	unset($_SESSION['bkp_uname']);
	unset($_SESSION['bkp_ulname']);
	unset($_SESSION['bkp_role_id']);
	header('location:dashboard.php');
} else {
	session_destroy();
	//session_unregister();
	header('location:../sign-in.php');
}
?>