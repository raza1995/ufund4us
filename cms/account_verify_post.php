<?
ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
ini_set('memory_limit', '512M');
include("php/dbconn.php");
require('../lib/init.php'); //Library for Stripe Merchant Account
date_default_timezone_set('America/Los_Angeles'); //TimeZone

$cid = $_REQUEST['cid'];

$DonationAmount = 0.00;
$TotalDonations = 0;
$available_amount = 0.00;
$pending_amount = 0.00;

if ($cid != '') { //Account Verify
	//Stripe API Settings
	\Stripe\Stripe::setApiKey(STRIPE_API_KEY); //Initialize Stripe Gateway (Live)
	//Stripe
	
	$current_date = date('Y-m-d'); //2016-07-15
	$date = $current_date; //Todays Date
	$generated_date = date('m/d/Y'); // 07/15/2016
	$QueryCampaigns="SELECT * FROM tbl_campaign WHERE fld_campaign_id = '$cid' AND fld_status = 1";
	$ResultCampaigns = mysqli_query( $con, $QueryCampaigns);
	$ResultCampaignsRows = mysqli_num_rows($ResultCampaigns);
	$z = 0;
	if ($ResultCampaignsRows > 0) {
		$Rows = mysqli_fetch_assoc($ResultCampaigns);
		$Campaign_Id = $Rows['fld_campaign_id']; //getting campaign id
		$Campaign_Title = $Rows['fld_campaign_title']; //getting campaign title
		$Campaign_Manager_FName = $Rows['fld_cname']; //getting campaign manager first name
		$Campaign_Manager_LName = $Rows['fld_clname']; //getting campaign manager last name
		$Campaign_AccountID = $Rows['fld_ac']; //getting campaign account id
		
		//Stripe
		$balance = \Stripe\Balance::retrieve(array("stripe_account" => $Campaign_AccountID));
		$balance_array = $balance->__toArray(true);
		if (isset($balance_array['available'][0]['amount'])) {
			$available_amount = $balance_array['available'][0]['amount'] / 100;
		} 
		if (isset($balance_array['available'][0]['amount'])) {
			$pending_amount = $balance_array['pending'][0]['amount'] / 100;
		} 
		$total_amount = $available_amount + $pending_amount;
		//Stripe
		
		$QueryDonations="SELECT COUNT(id) AS TotalDonations, SUM(donation_amount) AS DonationAmount FROM tbl_donations WHERE cid = '$Campaign_Id' AND mode = 1";
		$ResultDonations = mysqli_query( $con, $QueryDonations);
		$ResultDonationsRows = mysqli_num_rows($ResultDonations);
		if ($ResultDonationsRows > 0) {
			$Rows1 = mysqli_fetch_assoc($ResultDonations);
			$TotalDonations = $Rows1['TotalDonations']; //total donations
			$DonationAmount = $Rows1['DonationAmount']; //donations amount
		}
	}
	$result['campaigntitle'] = $Campaign_Title;
	$result['campaignmname'] = $Campaign_Manager_FName.' '.$Campaign_Manager_LName;
	$result['nodonations'] = $TotalDonations;

	$aCampaignDetail = $Rows;
	$app_fee_percentage = DEFAULT_APP_FEE;//20%
	if( isset($aCampaignDetail['app_fee_percentage']) ){
	    $app_fee_percentage = $aCampaignDetail['app_fee_percentage'];
	}
	$after_app_fee_percentage = 1;
	if($app_fee_percentage > 0){
		$after_app_fee_percentage = 1 - $app_fee_percentage/100; 
	}

	$result['donationsamount'] = $DonationAmount * $after_app_fee_percentage;
	$result['counter'] = $ResultCampaignsRows;
	//Stripe
	$result['stripeavailable'] = $available_amount;
	$result['stripepending'] = $pending_amount;
	$result['stripetotal'] = $total_amount;
	
	echo json_encode($result);
}

?>