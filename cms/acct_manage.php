<?
require_once("../configuration/dbconfig.php");
require ('../lib/init.php');
ini_set('memory_limit', '-1');
date_default_timezone_set('America/Los_Angeles'); //TimeZone
if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else {
	if ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5 || $_SESSION['role_id'] == 6) {
		$result['status'] = 0;
		echo json_encode($result);
	}
}
$acctid = $_POST['acctid'];
if ($acctid != "") {
  \Stripe\Stripe::setApiKey(STRIPE_API_KEY); //Initialize Stripe Gateway
  $account = \Stripe\Account::retrieve($acctid);
  $accoutndone = $account->delete();
  if ($accoutndone) {
	$result['status'] = 1;
    echo json_encode($result);  
  } else {
	$result['status'] = 0;
    echo json_encode($result);
  }
}
?>