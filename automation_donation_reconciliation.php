<?
include("cms/php/dbconn.php");
require ('lib/init.php');
ini_set('memory_limit', '-1');
date_default_timezone_set('America/Los_Angeles'); //TimeZone
include_once('cms/classes/class.phpmailer.php');

function generatepasshash($length) 
{
	$characters = '!@#$%^&*().0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
function encrypt($string, $key = '30btrigno') 
{
	$result = '';
	for($i=0; $i<strlen($string); $i++) 
	{
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	}
	return base64_encode($result);
}


$to_date = date('m/d/Y');
$from_date = date("m/d/Y",strtotime("-1 month"));

$QueryDonations = "SELECT * FROM tbl_donations";
$ResultDonations = mysqli_query($conn1, $QueryDonations) or die("ERROR: Cannot fetch the donations paid records...!");
$ResultDonationsRows = mysqli_num_rows($ResultDonations);
if ($ResultDonationsRows > 0) {
	while($Rows = mysqli_fetch_assoc($ResultDonations)) {
		$DataDonations[] = $Rows;
	}
} else {
	$DataDonations[] = '';
}

$QueryRefunds = "SELECT * FROM tbl_donations_refund";
$ResultRefunds = mysqli_query($conn1, $QueryRefunds) or die("ERROR: Cannot fetch the donations refunds record...!");
$ResultRefundsRows = mysqli_num_rows($ResultRefunds);
if ($ResultRefundsRows > 0) {
	while($Rows2 = mysqli_fetch_assoc($ResultRefunds)) {
		$DataRefunds[] = $Rows2;
	}
} else {
	$DataRefunds[] = '';
}

$QueryDisputes = "SELECT * FROM tbl_donations_dispute";
$ResultDisputes = mysqli_query($conn1, $QueryDisputes) or die("ERROR: Cannot fetch the donations disputes record...!");
$ResultDisputesRows = mysqli_num_rows($ResultDisputes);
if ($ResultDisputesRows > 0) {
	while($Rows3 = mysqli_fetch_assoc($ResultDisputes)) {
		$DataDisputes[] = $Rows3;
	}
} else {
	$DataDisputes[] = '';
}

\Stripe\Stripe::setApiKey(STRIPE_API_KEY); //Initialize Stripe Gateway

$charges_query = \Stripe\Charge::all(array("limit" => 100, "created" => array("gte" => strtotime("-1 month")))); //Getting all charges record 100 rows per page
//$charges_query = \Stripe\Charge::all(array("limit" => 100)); //Getting all charges record 100 rows per page
$newarray = '';
foreach ($charges_query->autoPagingIterator() as $charge) {
  $newarray[] = $charge;
}
$json = json_encode($newarray);
$stripe_records = json_decode($json, true);
$stripe_records_count = count($stripe_records);
//print_r($stripe_records);

$paid_array = ''; //Initial paid only without refunded and disputed.
$refund_array = ''; //Initial refunded only without disputed.
$dispute_array = ''; //Initial disputed only.

for ($sn=0; $sn < $stripe_records_count; $sn++) {
	$stripe_charge_id = str_replace ('ch_', '', $stripe_records[$sn]['id']); //Getting Stripe ChargeID
	if ($stripe_records[$sn]['paid'] == 1) { //Getting data paid only without refunded and disputed.
		
		//Missing Donations
		if (array_search($stripe_charge_id, array_column($DataDonations, 'tid'))) {
		} else {
			if($stripe_records[$sn]['refunded'] == '' && $stripe_records[$sn]['dispute'] == '') {
				$paid_array[] = $stripe_records[$sn]; //Data saved in paid array.
			}
		}
		
		//Missing Refunds
		if (array_search($stripe_charge_id, array_column($DataRefunds, 'tid'))) {
		} else {
			if($stripe_records[$sn]['refunded'] != '') { //Getting data refunded only without disputed.
				$refund_array[] = $stripe_records[$sn]; //Data saved in refund array.
			}
		}
		
		//Missing Disputes
		if (array_search($stripe_charge_id, array_column($DataDisputes, 'tid'))) {
		} else {
			if($stripe_records[$sn]['dispute'] != '') { //Getting data disputed only.
				$dispute_array[] = $stripe_records[$sn]; //Data saved in dispute array.
			}
		}
		
	}
}
$is_paid_array_exists = count($paid_array);
$snopaid = 0;
if ($is_paid_array_exists > 0 && (is_array($paid_array) || is_object($paid_array))) {
	foreach ($paid_array as $paid_data) {
		$snopaid++;
		$paid_data['metadata']['Timestamp'];
		$cid = $paid_data['metadata']['Campaign #'];
		mysqli_real_escape_string($conn1, $paid_data['metadata']['Campaign Title']);
		$custid = $paid_data['metadata']['Customer ID'];
		$custemail = $paid_data['metadata']['Customer Email'];
		$refid = $paid_data['metadata']['Participant ID'];
		mysqli_real_escape_string($conn1, $paid_data['metadata']['Participant First Name']);
		$participantname = mysqli_real_escape_string($conn1, $paid_data['metadata']['Participant First Name']);
		$refname = mysqli_real_escape_string($conn1, $paid_data['metadata']['Customer First Name']);
		$reflname = mysqli_real_escape_string($conn1, $paid_data['metadata']['Customer Last Name']);
		$refemail = $paid_data['metadata']['Customer Email'];
		$time_stamp1 = $paid_data['metadata']['Timestamp'];
		$time_stamp = date("Y-m-d H:i:s", strtotime($time_stamp1));
		$payment_method = $paid_data['source']['funding'];
		$payment_through = $paid_data['source']['brand'];
		$card_number = $paid_data['source']['last4'];
		//if ($paid_data['captured'] == 1) {echo "Yes";} else {echo "No";}
		number_format(($paid_data['amount']/100),2);
		$amounting = $paid_data['amount']/100;
		$tid = str_replace ('ch_', '', $paid_data['id']);
		$paid_data['destination'];
		
		$QueryUsers = "SELECT u.*, c.fld_cname, c.fld_clname FROM tbl_users u INNER JOIN tbl_campaign c ON c.fld_campaign_id = '$cid' WHERE u.fld_uid = '$custid' AND u.fld_role_id = '4'"; //Donors Role Only
		$ResultUsers = mysqli_query($conn1, $QueryUsers) or die("ERROR: Cannot fetch the user records...!");
		$ResultUsersRows = mysqli_num_rows($ResultUsers);
		if ($ResultUsersRows > 0) { //If User exists 
			$Rows = mysqli_fetch_assoc($ResultUsers);
			$cust_fnanme = mysqli_real_escape_string($conn1, $Rows['fld_name']);
			$cust_lnanme = mysqli_real_escape_string($conn1, $Rows['fld_lname']);
			$cust_email = mysqli_real_escape_string($conn1, $Rows['fld_email']);
			$cust_phone = mysqli_real_escape_string($conn1, $Rows['fld_phone']);
			$cm_fname = mysqli_real_escape_string($conn1, $Rows['fld_cname']);
			$cm_lname = mysqli_real_escape_string($conn1, $Rows['fld_clname']);
			
			//Checking Donors Master Table if user exists
			$QueryDonors = "SELECT * FROM tbl_donors WHERE uid = '$custid' AND cid = '$cid' AND puid = '$refid'";
			$ResultDonors = mysqli_query($conn1, $QueryDonors) or die("ERROR: Cannot fetch the donor records...!");
			$ResultDonorsRows = mysqli_num_rows($ResultDonors);
			if ($ResultDonorsRows > 0) {
				$Rows2 = mysqli_fetch_assoc($ResultDonors);
				$pid = mysqli_real_escape_string($conn1, $Rows2['id']);
				$uname = mysqli_real_escape_string($conn1, $Rows2['uname']);
				$ulname = mysqli_real_escape_string($conn1, $Rows2['ulname']);
				$uemail = mysqli_real_escape_string($conn1, $Rows2['uemail']);
				$uphone = mysqli_real_escape_string($conn1, $Rows2['uphone']);
				$creationdate = mysqli_real_escape_string($conn1, $Rows2['creationdate']);
			} else {
				//Inserting the Donor Master Records Who is Donated
				$QueryDonorsInserted = "INSERT INTO tbl_donors (cid, uid, puid, uname, ulname, uemail, uphone, creationdate, usedas, participantid, participantname) VALUES ('$cid','$custid','$refid','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone', '$time_stamp','Single','$refid','$refname $reflname')";
				$ResultDonorsInserted = mysqli_query($conn1, $QueryDonorsInserted) or die("ERROR: Cannot inserting the donor records...! 1");
				$pid = mysqli_insert_id($conn1);
			}
			
			//Checking Donors Slave Table if user exists
			$QueryDonorsDetails = "SELECT * FROM tbl_donors_details WHERE uid = '$custid' AND cid = '$cid' AND puid = '$refid'";
			$ResultDonorsDetails = mysqli_query($conn1, $QueryDonorsDetails) or die("ERROR: Cannot fetch the donor details records...!");
			$ResultDonorsDetailsRows = mysqli_num_rows($ResultDonorsDetails);
			if ($ResultDonorsDetailsRows == 0) {	
				//Inserting the Donor Slave Records Who is Donated
				$QueryDonorsDetailsInserted = "INSERT INTO tbl_donors_details (cid, uid, did, puid, uname, ulname, uemail, uphone, creationdate, usedas, participantid, participantname) VALUES ('$cid','$custid', '$pid', '$refid','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone', '$time_stamp','Single','$refid','$refname $reflname')";
				$ResultDonorsDetailsInserted = mysqli_query($conn1, $QueryDonorsDetailsInserted) or die("ERROR: Cannot inserting the donor details records...!");
			}
			
			//Checking Donations if user exists
			$QueryDonationsChk = "SELECT * FROM tbl_donations WHERE uid = '$custid' AND tid = '$tid' AND uemail = '$custemail' AND cid = '$cid' AND refferal_by = '$refid'";
			$ResultDonationsChk = mysqli_query($conn1, $QueryDonationsChk) or die("ERROR: Cannot fetch the donations records against customer...!");
			$ResultDonationsChkRows = mysqli_num_rows($ResultDonationsChk);
			if ($ResultDonationsChkRows == 0) {
				//Inserting the Donation Records Who is Donated
				$QueryDonationInserted = "INSERT INTO tbl_donations (tid, cid, uid, cmfname, cmlname, ufname, ulname, uemail, uphone, donation_amount, card_number, payment_method, payment_through, imageurl, comment, refferal_by, client_ip, displaylisted, mode, creationdate) VALUES ('$tid','$cid','$custid','$cm_fname','$cm_lname','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone','$amounting','$card_number','$payment_method','$payment_through','','','$refid','','0','1','$time_stamp')";
				$ResultDonationInserted = mysqli_query($conn1, $QueryDonationInserted) or die("ERROR: Cannot inserting the donation records...!");
			}
			
		} else { //If User doesnt exists
			$QueryCampaign = "SELECT fld_cname, fld_clname FROM tbl_campaign WHERE fld_campaign_id = '$cid'";
			$ResultCampaign = mysqli_query($conn1, $QueryCampaign) or die("ERROR: Cannot fetch the campaign records...!");
			$ResultCampaignRows = mysqli_num_rows($ResultCampaign);
			if ($ResultCampaignRows > 0) { //Campaign Records
				$Rows = mysqli_fetch_assoc($ResultCampaign);
				$cm_fname = mysqli_real_escape_string($conn1, $Rows['fld_cname']);
				$cm_lname = mysqli_real_escape_string($conn1, $Rows['fld_clname']);
			}
			$cust_fnanme = $refname;
			$cust_lnanme = $reflname;
			$cust_email = $refemail;
			$cust_phone = "___-___-____";
			
			$hased_password1 = generatepasshash(20); //Generating Password (20 Chars)
			$hased_password = encrypt($hased_password1); //Encypting password hashes
			$QueryUsers = "INSERT INTO tbl_users (fld_role_id, fld_status, fld_email, fld_password, fld_name, fld_lname, fld_phone, fld_join_date) VALUES ('4', '1', '$cust_email', '$hased_password', '$cust_fnanme', '$cust_lnanme', '$cust_phone', NOW())";
			$ResultUsers = mysqli_query($conn1, $QueryUsers) or die("ERROR: Cannot inserting the user records...!");
			$last_id = mysqli_insert_id($conn1);
			
			//Inserting the Donor Master Records Who is Donated
			$QueryDonorsInserted = "INSERT INTO tbl_donors (cid, uid, puid, uname, ulname, uemail, uphone, creationdate, usedas, participantid, participantname) VALUES ('$cid','$last_id','$refid','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone', '$time_stamp','Single','$refid','$participantname')";
			$ResultDonorsInserted = mysqli_query($conn1, $QueryDonorsInserted) or die("ERROR: Cannot inserting the donor records...! 2");
			$pid = mysqli_insert_id($conn1);
				
			//Inserting the Donor Slave Records Who is Donated
			$QueryDonorsDetailsInserted = "INSERT INTO tbl_donors_details (cid, uid, did, puid, uname, ulname, uemail, uphone, creationdate, usedas, participantid, participantname) VALUES ('$cid','$last_id', '$pid', '$refid','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone', '$time_stamp','Single','$refid','$participantname')";
			$ResultDonorsDetailsInserted = mysqli_query($conn1, $QueryDonorsDetailsInserted) or die("ERROR: Cannot inserting the donor details records...!");
			
			//Inserting the Donation Records Who is Donated
			$QueryDonationInserted = "INSERT INTO tbl_donations (tid, cid, uid, cmfname, cmlname, ufname, ulname, uemail, uphone, donation_amount, card_number, payment_method, payment_through, imageurl, comment, refferal_by, client_ip, displaylisted, mode, creationdate) VALUES ('$tid','$cid','$last_id','$cm_fname','$cm_lname','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone','$amounting','$card_number','$payment_method','$payment_through','','','$refid','','0','1','$time_stamp')";
			$ResultDonationInserted = mysqli_query($conn1, $QueryDonationInserted) or die("ERROR: Cannot inserting the donation records...!");
		}
	}
}
$is_refund_array_exists = count($refund_array);
$snorefund = 0;
if ($is_refund_array_exists > 0 && (is_array($refund_array) || is_object($refund_array))) {
	foreach ($refund_array as $refund_data) {
		$snorefund++;
		$refund_data['metadata']['Timestamp'];
		$cid = $refund_data['metadata']['Campaign #'];
		mysqli_real_escape_string($conn1, $refund_data['metadata']['Campaign Title']);
		$custid = $refund_data['metadata']['Customer ID'];
		$refid = $refund_data['metadata']['Participant ID'];
		mysqli_real_escape_string($conn1, $refund_data['metadata']['Participant First Name']);
		$refname = mysqli_real_escape_string($conn1, $refund_data['metadata']['Customer First Name']);
		$reflname = mysqli_real_escape_string($conn1, $refund_data['metadata']['Customer Last Name']);
		$refemail = $refund_data['metadata']['Customer Email'];
		$time_stamp1 = $refund_data['metadata']['Timestamp'];
		$time_stamp = date("Y-m-d H:i:s", strtotime($time_stamp1));
		$payment_method = $refund_data['source']['funding'];
		$payment_through = $refund_data['source']['brand'];
		$card_number = $refund_data['source']['last4'];
		//if ($refund_data['captured'] == 1) {echo "Yes";} else {echo "No";}
		number_format(($refund_data['amount_refunded']/100),2);
		$amounting = $refund_data['amount_refunded']/100;
		$tid = str_replace ('ch_', '', $refund_data['id']);
		$refund_id = $refund_data['refunds']['data']['0']['id'];
		
		$QueryUsers = "SELECT u.*, c.fld_cname, c.fld_clname FROM tbl_users u INNER JOIN tbl_campaign c ON c.fld_campaign_id = '$cid' WHERE u.fld_uid = '$custid' AND u.fld_role_id = '4'"; //Donors Role Only
		$ResultUsers = mysqli_query($conn1, $QueryUsers) or die("ERROR: Cannot fetch the user records...!");
		$ResultUsersRows = mysqli_num_rows($ResultUsers);
		if ($ResultUsersRows > 0) { //If User exists 
			$Rows = mysqli_fetch_assoc($ResultUsers);
			$cust_fnanme = mysqli_real_escape_string($conn1, $Rows['fld_name']);
			$cust_lnanme = mysqli_real_escape_string($conn1, $Rows['fld_lname']);
			$cust_email = mysqli_real_escape_string($conn1, $Rows['fld_email']);
			$cust_phone = mysqli_real_escape_string($conn1, $Rows['fld_phone']);
			$cm_fname = mysqli_real_escape_string($conn1, $Rows['fld_cname']);
			$cm_lname = mysqli_real_escape_string($conn1, $Rows['fld_clname']);
			
			//Checking Donors Master Table if user exists
			$QueryDonors = "SELECT * FROM tbl_donors WHERE uid = '$custid' AND cid = '$cid' AND puid = '$refid'";
			$ResultDonors = mysqli_query($conn1, $QueryDonors) or die("ERROR: Cannot fetch the donor records...!");
			$ResultDonorsRows = mysqli_num_rows($ResultDonors);
			if ($ResultDonorsRows > 0) {
				$Rows2 = mysqli_fetch_assoc($ResultDonors);
				$pid = mysqli_real_escape_string($conn1, $Rows2['id']);
				$uname = mysqli_real_escape_string($conn1, $Rows2['uname']);
				$ulname = mysqli_real_escape_string($conn1, $Rows2['ulname']);
				$uemail = mysqli_real_escape_string($conn1, $Rows2['uemail']);
				$uphone = mysqli_real_escape_string($conn1, $Rows2['uphone']);
				$creationdate = mysqli_real_escape_string($conn1, $Rows2['creationdate']);
			} else {
				//Inserting the Donor Master Records Who is Donated
				$QueryDonorsInserted = "INSERT INTO tbl_donors (cid, uid, puid, uname, ulname, uemail, uphone, creationdate, usedas, participantid, participantname) VALUES ('$cid','$custid','$refid','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone', '$time_stamp','Single','$refid','$refname $reflname')";
				$ResultDonorsInserted = mysqli_query($conn1, $QueryDonorsInserted) or die("ERROR: Cannot inserting the donor records...! 3");
				$pid = mysqli_insert_id($conn1);
			}
			
			//Checking Donors Slave Table if user exists
			$QueryDonorsDetails = "SELECT * FROM tbl_donors_details WHERE uid = '$custid' AND cid = '$cid' AND puid = '$refid'";
			$ResultDonorsDetails = mysqli_query($conn1, $QueryDonorsDetails) or die("ERROR: Cannot fetch the donor details records...!");
			$ResultDonorsDetailsRows = mysqli_num_rows($ResultDonorsDetails);
			if ($ResultDonorsDetailsRows == 0) {	
				//Inserting the Donor Slave Records Who is Donated
				$QueryDonorsDetailsInserted = "INSERT INTO tbl_donors_details (cid, uid, did, puid, uname, ulname, uemail, uphone, creationdate, usedas, participantid, participantname) VALUES ('$cid','$custid', '$pid', '$refid','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone', '$time_stamp','Single','$refid','$refname $reflname')";
				$ResultDonorsDetailsInserted = mysqli_query($conn1, $QueryDonorsDetailsInserted) or die("ERROR: Cannot inserting the donor details records...!");
			}
			
			//Checking Donations Refund if user exists
			$QueryDonationsChk = "SELECT * FROM tbl_donations_refund WHERE uid = '$custid' AND cid = '$cid' AND refferal_by = '$refid'";
			$ResultDonationsChk = mysqli_query($conn1, $QueryDonationsChk) or die("ERROR: Cannot fetch the donations refund records against customer...!");
			$ResultDonationsChkRows = mysqli_num_rows($ResultDonationsChk);
			if ($ResultDonationsChkRows == 0) {
				//Inserting the Donation Refund Records Who is Donated
				$QueryDonationInserted = "INSERT INTO tbl_donations_refund (tid, refundid, cid, uid, cmfname, cmlname, ufname, ulname, uemail, uphone, donation_amount, card_number, payment_method, payment_through, refferal_by, client_ip, creationdate) VALUES ('$tid','$refund_id','$cid','$custid','$cm_fname','$cm_lname','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone','$amounting','$card_number','$payment_method','$payment_through','$refid','','$time_stamp')";
				$ResultDonationInserted = mysqli_query($conn1, $QueryDonationInserted) or die("ERROR: Cannot inserting the refund donation records...!");
				
				//Updating the Mode 2 in Donation Records Who is Refunded
				$QueryDonationUpdate = "UPDATE tbl_donations SET mode = '2' WHERE tid = '$tid'";
				$ResultDonationUpdate = mysqli_query($conn1, $QueryDonationUpdate) or die("ERROR: Cannot updating the donation records...!");
			}
			
		} else { //If User doesnt exists 
			$QueryCampaign = "SELECT fld_cname, fld_clname FROM tbl_campaign WHERE fld_campaign_id = '$cid'";
			$ResultCampaign = mysqli_query($conn1, $QueryCampaign) or die("ERROR: Cannot fetch the campaign records...!");
			$ResultCampaignRows = mysqli_num_rows($ResultCampaign);
			if ($ResultCampaignRows > 0) { //Campaign Records
				$Rows = mysqli_fetch_assoc($ResultCampaign);
				$cm_fname = mysqli_real_escape_string($conn1, $Rows['fld_cname']);
				$cm_lname = mysqli_real_escape_string($conn1, $Rows['fld_clname']);
			}
			$cust_fnanme = $refname;
			$cust_lnanme = $reflname;
			$cust_email = $refemail;
			$cust_phone = "___-___-____";
			
			$hased_password1 = generatepasshash(20); //Generating Password (20 Chars)
			$hased_password = encrypt($hased_password1); //Encypting password hashes
			$QueryUsers = "INSERT INTO tbl_users (fld_role_id, fld_status, fld_email, fld_password, fld_name, fld_lname, fld_join_date) VALUES ('4', '1', '$cust_email', '$hased_password', '$cust_fnanme', '$cust_lnanme', NOW())";
			$ResultUsers = mysqli_query($conn1, $QueryUsers) or die("ERROR: Cannot inserting the user records...!");
			$last_id = mysqli_insert_id($conn1);
			
			//Inserting the Donor Master Records Who is Donated
			$QueryDonorsInserted = "INSERT INTO tbl_donors (cid, uid, puid, uname, ulname, uemail, uphone, creationdate, usedas, participantid, participantname) VALUES ('$cid','$last_id','$refid','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone', '$time_stamp','Single','$refid','$refname $reflname')";
			$ResultDonorsInserted = mysqli_query($conn1, $QueryDonorsInserted) or die("ERROR: Cannot inserting the donor records...! 4");
			$pid = mysqli_insert_id($conn1);
				
			//Inserting the Donor Slave Records Who is Donated
			$QueryDonorsDetailsInserted = "INSERT INTO tbl_donors_details (cid, uid, did, puid, uname, ulname, uemail, uphone, creationdate, usedas, participantid, participantname) VALUES ('$cid','$last_id', '$pid', '$refid','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone', '$time_stamp','Single','$refid','$refname $reflname')";
			$ResultDonorsDetailsInserted = mysqli_query($conn1, $QueryDonorsDetailsInserted) or die("ERROR: Cannot inserting the donor details records...!");
			
			//Inserting the Donation Refund Records Who is Donated
			$QueryDonationInserted = "INSERT INTO tbl_donations_refund (tid, refundid, cid, uid, cmfname, cmlname, ufname, ulname, uemail, uphone, donation_amount, card_number, payment_method, payment_through, refferal_by, client_ip, creationdate) VALUES ('$tid','$refund_id','$cid','$custid','$cm_fname','$cm_lname','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone','$amounting','$card_number','$payment_method','$payment_through','$refid','','$time_stamp')";
			$ResultDonationInserted = mysqli_query($conn1, $QueryDonationInserted) or die("ERROR: Cannot inserting the refund donation records...!");
			
			//Updating the Mode 2 in Donation Records Who is Refunded
			$QueryDonationUpdate = "UPDATE tbl_donations SET mode = '2' WHERE tid = '$tid'";
			$ResultDonationUpdate = mysqli_query($conn1, $QueryDonationUpdate) or die("ERROR: Cannot updating the donation records...!");
		}
	}
}
$is_dispute_array_exists = count($dispute_array);
$snodispute = 0;
if ($is_dispute_array_exists > 0 && (is_array($dispute_array) || is_object($dispute_array))) {
	foreach ($dispute_array as $dispute_data) {
		$snodispute++;
		$dispute_data['metadata']['Timestamp'];
		$cid = $dispute_data['metadata']['Campaign #'];
		mysqli_real_escape_string($conn1, $dispute_data['metadata']['Campaign Title']);
		$custid = $dispute_data['metadata']['Customer ID'];
		$refid = $dispute_data['metadata']['Participant ID'];
		mysqli_real_escape_string($conn1, $dispute_data['metadata']['Participant First Name']);
		$refname = mysqli_real_escape_string($conn1, $dispute_data['metadata']['Customer First Name']);
		$reflname = mysqli_real_escape_string($conn1, $dispute_data['metadata']['Customer Last Name']);
		$refemail = $dispute_data['metadata']['Customer Email'];
		$time_stamp1 = $dispute_data['metadata']['Timestamp'];
		$time_stamp = date("Y-m-d H:i:s", strtotime($time_stamp1));
		$payment_method = $dispute_data['source']['funding'];
		$payment_through = $dispute_data['source']['brand'];
		$card_number = $dispute_data['source']['last4'];
		//if ($dispute_data['captured'] == 1) {echo "Yes";} else {echo "No";}
		number_format(($dispute_data['dispute']['amount']/100),2);
		$amounting = $dispute_data['dispute']['amount']/100;
		$tid = str_replace ('ch_', '', $dispute_data['id']);
		$dispute_id = $dispute_data['dispute']['id'];
		
		$QueryUsers = "SELECT u.*, c.fld_cname, c.fld_clname FROM tbl_users u INNER JOIN tbl_campaign c ON c.fld_campaign_id = '$cid' WHERE u.fld_uid = '$custid' AND u.fld_role_id = '4'"; //Donors Role Only
		$ResultUsers = mysqli_query($conn1, $QueryUsers) or die("ERROR: Cannot fetch the user records...!");
		$ResultUsersRows = mysqli_num_rows($ResultUsers);
		if ($ResultUsersRows > 0) { //If User exists 
			$Rows = mysqli_fetch_assoc($ResultUsers);
			$cust_fnanme = mysqli_real_escape_string($conn1, $Rows['fld_name']);
			$cust_lnanme = mysqli_real_escape_string($conn1, $Rows['fld_lname']);
			$cust_email = mysqli_real_escape_string($conn1, $Rows['fld_email']);
			$cust_phone = mysqli_real_escape_string($conn1, $Rows['fld_phone']);
			$cm_fname = mysqli_real_escape_string($conn1, $Rows['fld_cname']);
			$cm_lname = mysqli_real_escape_string($conn1, $Rows['fld_clname']);
			
			//Checking Donors Master Table if user exists
			$QueryDonors = "SELECT * FROM tbl_donors WHERE uid = '$custid' AND cid = '$cid' AND puid = '$refid'";
			$ResultDonors = mysqli_query($conn1, $QueryDonors) or die("ERROR: Cannot fetch the donor records...!");
			$ResultDonorsRows = mysqli_num_rows($ResultDonors);
			if ($ResultDonorsRows > 0) {
				$Rows2 = mysqli_fetch_assoc($ResultDonors);
				$pid = mysqli_real_escape_string($conn1, $Rows2['id']);
				$uname = mysqli_real_escape_string($conn1, $Rows2['uname']);
				$ulname = mysqli_real_escape_string($conn1, $Rows2['ulname']);
				$uemail = mysqli_real_escape_string($conn1, $Rows2['uemail']);
				$uphone = mysqli_real_escape_string($conn1, $Rows2['uphone']);
				$creationdate = mysqli_real_escape_string($conn1, $Rows2['creationdate']);
			} else {
				//Inserting the Donor Master Records Who is Donated
				$QueryDonorsInserted = "INSERT INTO tbl_donors (cid, uid, puid, uname, ulname, uemail, uphone, creationdate, usedas, participantid, participantname) VALUES ('$cid','$custid','$refid','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone', '$time_stamp','Single','$refid','$refname $reflname')";
				$ResultDonorsInserted = mysqli_query($conn1, $QueryDonorsInserted) or die("ERROR: Cannot inserting the donor records...! 5");
				$pid = mysqli_insert_id($conn1);
			}
			
			//Checking Donors Slave Table if user exists
			$QueryDonorsDetails = "SELECT * FROM tbl_donors_details WHERE uid = '$custid' AND cid = '$cid' AND puid = '$refid'";
			$ResultDonorsDetails = mysqli_query($conn1, $QueryDonorsDetails) or die("ERROR: Cannot fetch the donor details records...!");
			$ResultDonorsDetailsRows = mysqli_num_rows($ResultDonorsDetails);
			if ($ResultDonorsDetailsRows == 0) {	
				//Inserting the Donor Slave Records Who is Donated
				$QueryDonorsDetailsInserted = "INSERT INTO tbl_donors_details (cid, uid, did, puid, uname, ulname, uemail, uphone, creationdate, usedas, participantid, participantname) VALUES ('$cid','$custid', '$pid', '$refid','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone', '$time_stamp','Single','$refid','$refname $reflname')";
				$ResultDonorsDetailsInserted = mysqli_query($conn1, $QueryDonorsDetailsInserted) or die("ERROR: Cannot inserting the donor details records...!");
			}
			
			//Checking Donations Dispute if user exists
			$QueryDonationsChk = "SELECT * FROM tbl_donations_dispute WHERE uid = '$custid' AND cid = '$cid' AND refferal_by = '$refid'";
			$ResultDonationsChk = mysqli_query($conn1, $QueryDonationsChk) or die("ERROR: Cannot fetch the donations dispute records against customer...!");
			$ResultDonationsChkRows = mysqli_num_rows($ResultDonationsChk);
			if ($ResultDonationsChkRows == 0) {
				//Inserting the Donation Dispute Records Who is Donated
				$QueryDonationInserted = "INSERT INTO tbl_donations_dispute (tid, disputeid, cid, uid, cmfname, cmlname, ufname, ulname, uemail, uphone, donation_amount, card_number, payment_method, payment_through, refferal_by, client_ip, creationdate) VALUES ('$tid','$dispute_id','$cid','$custid','$cm_fname','$cm_lname','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone','$amounting','$card_number','$payment_method','$payment_through','$refid','','$time_stamp')";
				$ResultDonationInserted = mysqli_query($conn1, $QueryDonationInserted) or die("ERROR: Cannot inserting the donation dispute records...!");
				
				//Updating the Mode 3 in Donation Records Who is Refunded
				$QueryDonationUpdate = "UPDATE tbl_donations SET mode = '3' WHERE tid = '$tid'";
				$ResultDonationUpdate = mysqli_query($conn1, $QueryDonationUpdate) or die("ERROR: Cannot updating the donation records...!");
			}
			
		} else { //If User doesnt exists 
			$QueryCampaign = "SELECT fld_cname, fld_clname FROM tbl_campaign WHERE fld_campaign_id = '$cid'";
			$ResultCampaign = mysqli_query($conn1, $QueryCampaign) or die("ERROR: Cannot fetch the campaign records...!");
			$ResultCampaignRows = mysqli_num_rows($ResultCampaign);
			if ($ResultCampaignRows > 0) { //Campaign Records
				$Rows = mysqli_fetch_assoc($ResultCampaign);
				$cm_fname = mysqli_real_escape_string($conn1, $Rows['fld_cname']);
				$cm_lname = mysqli_real_escape_string($conn1, $Rows['fld_clname']);
			}
			$cust_fnanme = $refname;
			$cust_lnanme = $reflname;
			$cust_email = $refemail;
			$cust_phone = "___-___-____";
		
			$hased_password1 = generatepasshash(20); //Generating Password (20 Chars)
			$hased_password = encrypt($hased_password1); //Encypting password hashes
			$QueryUsers = "INSERT INTO tbl_users (fld_role_id, fld_status, fld_email, fld_password, fld_name, fld_lname, fld_join_date) VALUES ('4', '1', '$cust_email', '$hased_password', '$cust_fnanme', '$cust_lnanme', NOW())";
			$ResultUsers = mysqli_query($conn1, $QueryUsers) or die("ERROR: Cannot inserting the user records...!");
			$last_id = mysqli_insert_id($conn1);
			
			//Inserting the Donor Master Records Who is Donated
			$QueryDonorsInserted = "INSERT INTO tbl_donors (cid, uid, puid, uname, ulname, uemail, uphone, creationdate, usedas, participantid, participantname) VALUES ('$cid','$last_id','$refid','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone', '$time_stamp','Single','$refid','$refname $reflname')";
			$ResultDonorsInserted = mysqli_query($conn1, $QueryDonorsInserted) or die("ERROR: Cannot inserting the donor records...! 6");
			$pid = mysqli_insert_id($conn1);
				
			//Inserting the Donor Slave Records Who is Donated
			$QueryDonorsDetailsInserted = "INSERT INTO tbl_donors_details (cid, uid, did, puid, uname, ulname, uemail, uphone, creationdate, usedas, participantid, participantname) VALUES ('$cid','$last_id', '$pid', '$refid','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone', '$time_stamp','Single','$refid','$refname $reflname')";
			$ResultDonorsDetailsInserted = mysqli_query($conn1, $QueryDonorsDetailsInserted) or die("ERROR: Cannot inserting the donor details records...!");
			
			//Inserting the Donation Dispute Records Who is Donated
			$QueryDonationInserted = "INSERT INTO tbl_donations_dispute (tid, disputeid, cid, uid, cmfname, cmlname, ufname, ulname, uemail, uphone, donation_amount, card_number, payment_method, payment_through, refferal_by, client_ip, creationdate) VALUES ('$tid','$dispute_id','$cid','$custid','$cm_fname','$cm_lname','$cust_fnanme','$cust_lnanme','$cust_email','$cust_phone','$amounting','$card_number','$payment_method','$payment_through','$refid','','$time_stamp')";
			$ResultDonationInserted = mysqli_query($conn1, $QueryDonationInserted) or die("ERROR: Cannot inserting the donation dispute records...!");
			
			//Updating the Mode 2 in Donation Records Who is Refunded
			$QueryDonationUpdate = "UPDATE tbl_donations SET mode = '3' WHERE tid = '$tid'";
			$ResultDonationUpdate = mysqli_query($conn1, $QueryDonationUpdate) or die("ERROR: Cannot updating the donation records...!");
		}
	}
}

	$header = '
	<table width="100%" border="0">
		<tbody>
			<tr>
				<td width="70%" style="padding: 0px 20px; text-align:center;font-family:arial; font-size:16px">
					<h1 style="font-family:arial; font-family:arial; font-size:20px"><b><u><i>Donation Reconciliation</i></u></b></h1>
					<h2 align="center" style="font-family:arial; font-size:17px"><b>From '.$from_date.'<br>To '.$to_date.'</b></h2>
				</td>
				<td width="30%" align="right"><img src="cms/emails/logo.png" width="20%" height="7%" /></td>
			</tr>
		</tbody>
	</table>';
	$html = '
	<style>
	#ac_chart
	{
		font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
		width:99%;
		border-collapse:collapse;
	}
	#ac_chart td, #ac_chart th 
	{
		font-size:1.4em;
		border:1px solid #98bf21;
		padding:3px 7px 2px 7px;
	}
	#ac_chart th 
	{
		font-size:1.5em;
		text-align:left;
		padding-top:5px;
		padding-bottom:4px;
		background-color:#F3BE00;
		-webkit-print-color-adjust:exact;
		color:black;
	}
	#ac_chart tr.alt td 
	{
		color:#000;
		background-color:#EAF2D3;
	}
	</style>
	';
	
	//Paid Donations
	$html .= '
	<h1 align="center" style="font-family:arial; font-family:arial; font-size:20px"><b><u>Paid Donations Without Refund/Dispute</u></b></h1>
	<table width="100%" id="ac_chart" style="font-size:0.8em;">
		<thead>
			<tr>
				<th width="5%">S.No</th>
				<th width="8%">TimeStamp</th>
				<th width="18%">Donor Full Name/ID</th>
				<th width="12%">Donor Email</th>
				<th width="18%">Participant Full Name/ID</th>
				<th width="18%">Campaign Title/ID</th>
				<th width="8%">Donation Amount</th>
				<th width="13%">Charge ID</th>
			</tr>
		</thead>
		<tbody>
	';
	$snopaidhtml = 0;
	$paidtotalamount = 0.00;
	if ($is_paid_array_exists > 0 && (is_array($paid_array) || is_object($paid_array))) {
	foreach ($paid_array as $paid_data) {
		$paidtotalamount += $paid_data['amount'];
	$snopaidhtml++;
	$html .= '
			<tr>
				<td>'.$snopaidhtml.'</td>
				<td>'.$paid_data['metadata']['Timestamp'].'</td>
				<td>'.mysqli_real_escape_string($conn1, $paid_data['metadata']['Customer First Name']).' '.mysqli_real_escape_string($conn1, $paid_data['metadata']['Customer Last Name']).' ('.$paid_data['metadata']['Customer ID'].')</td>
				<td>'.$paid_data['metadata']['Customer Email'].'</td>
				<td>'.mysqli_real_escape_string($conn1, $paid_data['metadata']['Participant First Name']).' ('.$paid_data['metadata']['Participant ID'].')</td>
				<td>'.mysqli_real_escape_string($conn1, $paid_data['metadata']['Campaign Title']).' ('.$paid_data['metadata']['Campaign #'].')</td>
				<td align="right">$'.number_format(($paid_data['amount']/100),2).'</td>
				<td>'.$paid_data['id'].'</td>
			</tr>
	';
	} } else {
	$html .= '
			<tr>
				<td align="center" colspan="8">No Records Found...</td>
			</tr>
	';
	}
	$html .= '
		</tbody>
	</table>
	';
	
	//Refund Donations
	$html .= '
	<h1 align="center" style="font-family:arial; font-family:arial; font-size:20px"><b><u>Refund Donations Without Dispute</u></b></h1>
	<table width="100%" id="ac_chart" style="font-size:0.8em;">
		<thead>
			<tr>
				<th width="5%">S.No</th>
				<th width="8%">TimeStamp</th>
				<th width="18%">Donor Full Name/ID</th>
				<th width="12%">Donor Email</th>
				<th width="18%">Participant Full Name/ID</th>
				<th width="18%">Campaign Title/ID</th>
				<th width="8%">Donation Amount</th>
				<th width="13%">Refund ID</th>
			</tr>
		</thead>
		<tbody>
	';
	$snorefundhtml = 0;
	$refundtotalamount = 0.00;
	if ($is_refund_array_exists > 0 && (is_array($refund_array) || is_object($refund_array))) {
	foreach ($refund_array as $refund_data) {
	$refundtotalamount += $refund_data['amount_refunded'];
	$snorefundhtml++;
	$html .= '
			<tr>
				<td>'.$snorefundhtml.'</td>
				<td>'.$refund_data['metadata']['Timestamp'].'</td>
				<td>'.mysqli_real_escape_string($conn1, $refund_data['metadata']['Customer First Name']).' '.mysqli_real_escape_string($conn1, $refund_data['metadata']['Customer Last Name']).' ('.$refund_data['metadata']['Customer ID'].')</td>
				<td>'.$refund_data['metadata']['Customer Email'].'</td>
				<td>'.mysqli_real_escape_string($conn1, $refund_data['metadata']['Participant First Name']).' ('.$refund_data['metadata']['Participant ID'].')</td>
				<td>'.mysqli_real_escape_string($conn1, $refund_data['metadata']['Campaign Title']).' ('.$refund_data['metadata']['Campaign #'].')</td>
				<td align="right">$'.number_format(($refund_data['amount_refunded']/100),2).'</td>
				<td>'.$refund_data['refunds']['data']['0']['id'].'</td>
			</tr>
	';
	} } else {
	$html .= '
			<tr>
				<td align="center" colspan="8">No Records Found...</td>
			</tr>
	';
	}
	$html .= '
		</tbody>
	</table>
	';
	
	//Dispute Only
	$html .= '
	<h1 align="center" style="font-family:arial; font-family:arial; font-size:20px"><b><u>Dispute Donations</u></b></h1>
	<table width="100%" id="ac_chart" style="font-size:0.8em;">
		<thead>
			<tr>
				<th width="5%">S.No</th>
				<th width="8%">TimeStamp</th>
				<th width="18%">Donor Full Name/ID</th>
				<th width="12%">Donor Email</th>
				<th width="18%">Participant Full Name/ID</th>
				<th width="18%">Campaign Title/ID</th>
				<th width="8%">Donation Amount</th>
				<th width="13%">Dispute ID</th>
			</tr>
		</thead>
		<tbody>
	';
	$snodisputehtml = 0;
	$disputetotalamount = 0.00;
	if ($is_dispute_array_exists > 0 && (is_array($dispute_array) || is_object($dispute_array))) {
	foreach ($dispute_array as $dispute_data) {
	$disputetotalamount += $dispute_data['dispute']['amount'];
	$snodisputehtml++;
	$html .= '
			<tr>
				<td>'.$snodisputehtml.'</td>
				<td>'.$dispute_data['metadata']['Timestamp'].'</td>
				<td>'.mysqli_real_escape_string($conn1, $dispute_data['metadata']['Customer First Name']).' '.mysqli_real_escape_string($conn1, $dispute_data['metadata']['Customer Last Name']).' ('.$dispute_data['metadata']['Customer ID'].')</td>
				<td>'.$dispute_data['metadata']['Customer Email'].'</td>
				<td>'.mysqli_real_escape_string($conn1, $dispute_data['metadata']['Participant First Name']).' ('.$dispute_data['metadata']['Participant ID'].')</td>
				<td>'.mysqli_real_escape_string($conn1, $dispute_data['metadata']['Campaign Title']).' ('.$dispute_data['metadata']['Campaign #'].')</td>
				<td align="right">$'.number_format(($dispute_data['amount']/100),2).'</td>
				<td>'.$dispute_data['dispute']['id'].'</td>
			</tr>
	';
	} } else {
	$html .= '
			<tr>
				<td align="center" colspan="8">No Records Found...</td>
			</tr>
	';
	}
	$html .= '
		</tbody>
	</table>
	';
	
	$html .= '<h1 align="left" style="font-family:arial; font-family:arial; font-size:20px"><b><u>Stats</u></b></h1>';
	$html .= '
	<table width="60%" id="ac_chart" style="font-size:0.8em;">
		<thead>
			<tr>
				<th width="25%">Dated</th>
				<th width="25%">Paid Donations</th>
				<th width="25%">Refund Donations</th>
				<th width="25%">Dispute Donations</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>'.$to_date.'</td>
				<td>'.$snopaid.' ( $'.number_format(($paidtotalamount/100),2).' )</td>
				<td>'.$snorefund.' ( $'.number_format(($refundtotalamount/100),2).' )</td>
				<td>'.$snodispute.' ( $'.number_format(($disputetotalamount/100),2).' )</td>
			</tr>
		</tbody>
	</table>
	';
	$html .= '<h2 align="left" style="font-family:arial; font-size:14px"><i>Generated on '.date("m/d/Y @ h:i:s").'</i></h2>';
	
	//__construct($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P')
	$configForMpdf = setVariableInAssocArrayForMpdf('c','Legal-L','','',10,10,40,10,10,5,'L');
	$mpdf=new \Mpdf\Mpdf($configForMpdf);
	
	$mpdf->SetHTMLHeader($header);
	$mpdf->WriteHTML($html);
	$datetime = date("mdY").'-'.date("his");
	$mpdf->Output('cms/files/donation-reconciliation-'.$datetime.'.pdf', 'F');
	$attachedfile = ''.sHOMECMS.'files/donation-reconciliation-'.$datetime.'.pdf';
	$filename = 'donation-reconciliation-'.$datetime.'.pdf';
	$path = ''.sHOMECMS.'files/';
	
	//Email Setting
	$to = KURT_EMAIL;
	$cc = TEST_EMAIL_1;
	$mail = new phpmailer;
	$mail->CharSet = 'UTF-8';
	$mail->Mailer  = 'mail';
	if ($cc != '') {
		$mail->addCC($cc);
	}
	$mail->AddBCC(CLIENT_EMAIL_1, CLIENT_1_NAME);
	$mail->AddReplyTo(NO_REPLY_EMAIL,sWEBSITENAME);
	$mail->SetFrom(NO_REPLY_EMAIL, sWEBSITENAME);
	$mail->isHTML(true);
	$mail->Subject = sWEBSITENAME.' reconciliation weekly report';
	$mail->AddAddress(trim($to), "Kurt Gairing");
	$mail->AddAttachment('cms/files/'.$filename);      // attachment
	//$mail->SMTPDebug  = 1;
	//Email Setting
	
	$mail->Body = '
		<html>
		<head>
			<style type="text/css">
				html { background-color:#E1E1E1; margin:0; padding:0; }
				body, #bodyTable, #bodyCell, #bodyCell{height:100% !important; margin:0; padding:0; width:100% !important;font-family:Helvetica, Arial, "Lucida Grande", sans-serif;}
				table{border-collapse:collapse;}
				table[id=bodyTable] {width:100%!important;margin:auto;max-width:700px!important;color:#7A7A7A;font-weight:normal;}
				img, a img{border:0; outline:none; text-decoration:none;height:auto; line-height:100%;}
				a {text-decoration:none !important;border-bottom: 1px solid;}
				h1, h2, h3, h4, h5, h6{color:#5F5F5F; font-weight:normal; font-family:Helvetica; font-size:20px; line-height:125%; text-align:Left; letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:10px;margin-left:0;padding-top:0;padding-bottom:0;padding-left:0;padding-right:0;}
	
				.ReadMsgBody{width:100%;} .ExternalClass{width:100%;} 
				.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div{line-height:100%;} 
				table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} 
				#outlook a{padding:0;} 
				img{-ms-interpolation-mode: bicubic;display:block;outline:none; text-decoration:none;} 
				body, table, td, p, a, li, blockquote{-ms-text-size-adjust:100%; -webkit-text-size-adjust:100%; font-weight:normal!important;} 
				.ExternalClass td[class="ecxflexibleContainerBox"] h3 {padding-top: 10px !important;} 
	
				h1{display:block;font-size:26px;font-style:normal;font-weight:normal;line-height:100%;}
				h2{display:block;font-size:20px;font-style:normal;font-weight:normal;line-height:120%;}
				h3{display:block;font-size:17px;font-style:normal;font-weight:normal;line-height:110%;}
				h4{display:block;font-size:18px;font-style:italic;font-weight:normal;line-height:100%;}
				.flexibleImage{height:auto;}
				.linkRemoveBorder{border-bottom:0 !important;}
				table[class=flexibleContainerCellDivider] {padding-bottom:0 !important;padding-top:0 !important;}
	
				body, #bodyTable{background-color:#E1E1E1;}
				#emailHeader{background-color:#E1E1E1;}
				#emailBody{background-color:#FFFFFF;}
				#emailFooter{background-color:#E1E1E1;}
				.nestedContainer{background-color:#F8F8F8; border:1px solid #CCCCCC;}
				.emailButton{background-color:#205478; border-collapse:separate; border-radius: 35px}
				.buttonContent{color:#FFFFFF; font-family:Helvetica; font-size:18px; font-weight:bold; line-height:100%; padding:15px; text-align:center;}
				.buttonContent a{color:#FFFFFF; display:block; text-decoration:none!important; border:0!important;}
				.emailCalendar{background-color:#FFFFFF; border:1px solid #CCCCCC;}
				.emailCalendarMonth{background-color:#205478; color:#FFFFFF; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:bold; padding-top:10px; padding-bottom:10px; text-align:center;}
				.emailCalendarDay{color:#205478; font-family:Helvetica, Arial, sans-serif; font-size:60px; font-weight:bold; line-height:100%; padding-top:20px; padding-bottom:20px; text-align:center;}
				.imageContentText {margin-top: 10px;line-height:0;}
				.imageContentText a {line-height:0;}
				#invisibleIntroduction {display:none !important;}
	
				span[class=ios-color-hack] a {color:#275100!important;text-decoration:none!important;}
				span[class=ios-color-hack2] a {color:#205478!important;text-decoration:none!important;}
				span[class=ios-color-hack3] a {color:#8B8B8B!important;text-decoration:none!important;}
				.a[href^="tel"], a[href^="sms"] {text-decoration:none!important;color:#606060!important;pointer-events:none!important;cursor:default!important;}
				.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {text-decoration:none!important;color:#606060!important;pointer-events:auto!important;cursor:default!important;}
			</style>
		</head>
		<body bgcolor="#E1E1E1" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
			<center style="background-color:#E1E1E1;">
				<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="table-layout: fixed;max-width:100% !important;width: 100% !important;min-width: 100% !important;">
					<tr>
						<td align="center" valign="top" id="bodyCell">
							<table bgcolor="#FFFFFF"  border="0" cellpadding="0" cellspacing="0" width="700" id="emailBody">
								<tr>
									<td align="center" valign="top">
										<table border="0" cellpadding="0" cellspacing="0" width="100%" style="color:#FFFFFF;" bgcolor="#f5f5f5">
											<tr>
												<td align="center" valign="top">
													<table border="0" cellpadding="0" cellspacing="0" width="700" class="flexibleContainer">
														<tr>
															<td align="center" valign="top" width="700" class="flexibleContainerCell">
																<table border="0" cellpadding="0" cellspacing="0" width="100%">
																	<tr>
																		<td width="28%" height="86" valign="top" class="textContent">
																			<div style="padding:10px;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#FFFFFF;"><img src="'.SITE_FULL_URL.'app/cms/emails/logo.png"  /></div>
																		</td>
																		<td width="43%" valign="top" class="textContent">
																		<div style="padding:10px;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#FFFFFF;">&nbsp;</div>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td align="center" valign="top">
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td align="center" valign="top">
													<table border="0" cellpadding="0" cellspacing="0" width="700" class="flexibleContainer">
														<tr>
															<td align="center" valign="top" width="700" class="flexibleContainerCell">
																<table border="0" cellpadding="30" cellspacing="0" width="100%">
																	<tr>
																		<td align="center" valign="top">
																			<table border="0" cellpadding="0" cellspacing="0" width="100%">
																				<tr>
																					<td valign="top" class="textContent">
																						<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-top:0;margin-bottom:20px;text-align:left;">Good Morning,</div>
																						<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-top:0;margin-bottom:20px;text-align:left;">Attached is your reconciliation weekly '.sWEBSITENAME.' report.  This report will help guide your success by detailing participation levels, email status, and funds raised to date.  Please utilize this report to encourage each participants commitment to reach their goals!</div>
																						<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-top:0;margin-bottom:20px;text-align:left;">If you need any additional information or help, please call us at '.SUPPORT_NUMBER_4_DISPLAY.'</div>
																						<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-top:0;margin-bottom:20px;text-align:left;">Thank you,</div>
																						<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-top:0;margin-bottom:10px;text-align:left;">Your '.sWEBSITENAME.' Team</div>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td align="center" valign="top">
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td align="center" valign="top">
													<table border="0" cellpadding="0" cellspacing="0" width="700" class="flexibleContainer">
														<tr>
															<td align="center" valign="top" width="700" class="flexibleContainerCell">
																<table border="0" cellpadding="30" cellspacing="0" width="100%">
																	<tr>
																		<td align="center" valign="top">
																			<table border="0" cellpadding="0" cellspacing="0" width="100%">
																				<tr>
																					<td valign="top" class="textContent">
																						<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#5F5F5F;">Generated on '.$to_date.'</div>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							<table bgcolor="#E1E1E1" border="0" cellpadding="0" cellspacing="0" width="700" id="emailFooter">
								<tr>
									<td align="center" valign="top">
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td align="center" valign="top">
													<table border="0" cellpadding="0" cellspacing="0" width="700" class="flexibleContainer">
														<tr>
															<td align="center" valign="top" width="700" class="flexibleContainerCell">
																<table border="0" cellpadding="0" cellspacing="0" width="100%">
																	<tr>
																		<td width="28%" valign="center" bgcolor="#2e2e2e">
																			<div style="padding:10px 10px"><img src="'.SITE_FULL_URL.'app/cms/emails/footer-logo.png" width="178" height="46"  /></div>
																		</td>
																		<td width="50%" valign="center" bgcolor="#2e2e2e">
																			<div style="padding:10px 10px; font-size:15px;color:gray">Copyright &copy; '.COPY_RIGHT_YEAR.' | <a href="'.SITE_FULL_URL.'" style="color:#fcb514;">'.sWEBSITENAME.'</a>. All rights reserved.</div>
																		</td>
																		<td width="22%" valign="center" bgcolor="#2e2e2e">
																			<div style="padding:10px 10px; font-size:15px;color:gray">Powered by <a href="http://www.lyja.com/" style="color:#fcb514;">Lyja</a></div>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</center>
		</body>
		</html>
		';
		if ($mail->Send()) {
			echo "Success: Email has been sent...!";
			$file = 'cms/files/' . $filename;
			if(file_exists($file)){
				unlink($file);
			}
		} else {
			echo "Failed: Email has not been sent...!";
		}