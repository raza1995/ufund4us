<?
require_once("configuration/dbconfig.php");
error_reporting(-1);

$transactionid = $_GET['transactionid'];
$comment = $_GET['comment'];
$transid = $_GET['transactionid'];

require_once( 'lib/Facebook/FacebookSession.php');
require_once( 'lib/Facebook/FacebookRequest.php' );
require_once( 'lib/Facebook/FacebookResponse.php' );
require_once( 'lib/Facebook/FacebookSDKException.php' );
require_once( 'lib/Facebook/FacebookRequestException.php' );
require_once( 'lib/Facebook/FacebookPermissionException.php' );
require_once( 'lib/Facebook/FacebookRedirectLoginHelper.php');
require_once( 'lib/Facebook/FacebookAuthorizationException.php' );
require_once( 'lib/Facebook/GraphObject.php' );
require_once( 'lib/Facebook/GraphUser.php' );
require_once( 'lib/Facebook/GraphSessionInfo.php' );
require_once( 'lib/Facebook/Entities/AccessToken.php');
require_once( 'lib/Facebook/HttpClients/FacebookCurl.php' );
require_once( 'lib/Facebook/HttpClients/FacebookHttpable.php');
require_once( 'lib/Facebook/HttpClients/FacebookCurlHttpClient.php');


use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\GraphSessionInfo;
use Facebook\FacebookHttpable;
use Facebook\FacebookCurlHttpClient;
use Facebook\FacebookCurl;

$aTransactionDetail = $oCampaign->gettransactiondetail($transactionid);
$donation_amount = $aTransactionDetail['donation_amount'];
$card_number = $aTransactionDetail['card_number'];
$new_card_number = substr($card_number, -4);
//$expiry_date = $aTransactionDetail['expiry_date'];
$payment_through = $aTransactionDetail['payment_through'];
$cid = $aTransactionDetail['cid'];
$pid = $aTransactionDetail['refferal_by'];
$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
$fld_campaign_id = $aCampaignDetail['fld_campaign_id'];
$fld_campaign_title = $aCampaignDetail['fld_campaign_title'];
$fld_organization_name = $aCampaignDetail['fld_organization_name'];
$fld_nonprofit = $aCampaignDetail['fld_nonprofit'];
$fld_nonprofit_number = $aCampaignDetail['fld_nonprofit_number'];

$aParticipantDetail = $oregister->getuserdetail($pid);
$participant_fname = $aParticipantDetail['fld_name'];
$participant_lname = $aParticipantDetail['fld_lname'];
$participant_phone = $aParticipantDetail['fld_phone'];
$participant_email = $aParticipantDetail['fld_email'];

$fileName = '';

$app_id = FACEBOOK_APP_ID;
$app_secret = FACEBOOK_APP_SECRET;
$redirect_url=SITE_URL.'app/fbprocess.php?transactionid='.$transactionid.'&comment='.$comment.'';

//3.Initialize application, create helper object and get fb sess
FacebookSession::setDefaultApplication($app_id,$app_secret);
$helper = new FacebookRedirectLoginHelper($redirect_url);
$sess = $helper->getSessionFromRedirect();
//check if facebook session exists
if(isset($_SESSION['fb_token'])){
	$sess = new FacebookSession($_SESSION['fb_token']);
}

//logout
$logout = SITE_URL.'app/cms/logout.php';
if(isset($sess)) {
	$response = (new FacebookRequest(
		$sess, 'POST', '/me/feed', array(
			'message' => $comment
		)
	))->execute()->getGraphObject()->asArray();
	//print_r( $response );
	$_SESSION['fb_token']=$sess->getToken();
	$request = new FacebookRequest($sess,'GET','/me');
	$response = $request->execute();
	$graph = $response->getGraphObject(GraphUser::classname());
	$name = $graph->getName();
	$id = $graph->getId();
	$image = 'https://graph.facebook.com/'.$id.'/picture?width=300';
	$profiledirectory = 'cms/uploads/donorimage/';
	$email = $graph->getProperty('email');
	$data = file_get_contents($image);
	$fileName = $id.'.jpg';
	$file = fopen($profiledirectory.$fileName, 'w+');
	fputs($file, $data);
	fclose($file);
	$oCampaign->updatetransaction($transid, $fileName, $comment);
	//$oregister->redirect('index.php');
	$redirect = 'thankyou.php?transaction='.$transid.'';
	header("location:".$redirect);
} else {
	$loginurl = $helper->getLoginUrl(array('publish_actions'));
	header("location:".$loginurl);
}
?>