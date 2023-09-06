<?php
require_once("../configuration/dbconfig.php");
if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
}
$uid = $_SESSION['uid'];
$oregister->update_lastactivity($uid);
?>