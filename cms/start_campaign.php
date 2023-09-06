<?
require_once("../configuration/dbconfig.php");
require ('../lib/init.php');
\Stripe\Stripe::setApiKey(STRIPE_API_KEY); //Initialize Stripe Gateway 
$errortype = '';
$REQUEST = &$_REQUEST;
if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else { 
	$user_details = $oregister->getuserdetail($_SESSION['uid']);
	if ($_SESSION['role_id'] == 3) {
		if ($user_details['fld_cname'] == "" || $user_details['fld_name'] == "" || $user_details['fld_lname'] == "" || $user_details['fld_address'] == "" || $user_details['fld_zip'] == "" || $user_details['fld_city'] == "" || $user_details['fld_state'] == "" || $user_details['fld_country'] == "") {
			$oregister->redirect('edit_profile.php?error=1');
		}
	} else {
		if ($user_details['fld_name'] == "" || $user_details['fld_lname'] == "" || $user_details['fld_address'] == "" || $user_details['fld_zip'] == "" || $user_details['fld_city'] == "" || $user_details['fld_state'] == "" || $user_details['fld_country'] == "") {
			$oregister->redirect('edit_profile.php?error=1');
		}
		if ($_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5) {
			$oregister->redirect('manage_campaign_participant.php');
		}
	}
}
if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
$sPageName = '<li>Start New Campaign</li>';
$sStartCampMenu = 'active';
$camp_inform = '';
$basic_inform = '';
$build_team = '';
$go_live = '';
if (isset($REQUEST['cid'])) {
	$ciddd = $REQUEST['cid'];
	$camp_inform = 'href="start_campaign.php?m=e&cid='.$ciddd.'"';
	$basic_inform = 'href="basic_information.php?cid='.$ciddd.'"';
	$build_team = 'href="build_team.php?cid='.$ciddd.'"';
	$go_live = 'href="golive.php?cid='.$ciddd.'"';
}
if(isset($REQUEST['fld_cname']) && $REQUEST['fld_cname']!='' and $REQUEST['fld_cemail']!='')
{
	$iUid = $_SESSION['uid'];
	$sName = $REQUEST['fld_cname'];
	$sLName = $REQUEST['fld_clname'];
	$sDob = date('Y-m-d',strtotime($REQUEST['fld_dob']));
	$DOB = explode("-",$sDob);
	$Date = $DOB[2];
	$Month = $DOB[1];
	$Year = $DOB[0];
	$legalname = explode(' ', $sName);
	$counter = count($legalname);
	$counter2 = $counter - 1;
	$firstname = '';
	$lastname = '';
	for ($i=0; $i < $counter; $i++) {
		if ($i < $counter2) {
			$firstname .= $legalname[$i];
		} else {
			$lastname .= $legalname[$i];
		}
	}
	$sSsn = $REQUEST['fld_ssn'];
	$sEmail = $REQUEST['fld_cemail'];
	$sPhone = $REQUEST['fld_cphone'];
	$sAddress = $REQUEST['fld_caddress'];
	$iCountryId = $REQUEST['fld_country'];
	$iStateId = $REQUEST['fld_state'];
	$iCityId = $REQUEST['fld_city'];
	$sZipcode = $REQUEST['fld_czipcode'];	
	if($REQUEST['mode'] == 'add')
	{
		// echo "69-REQUEST<pre>"; print_r($REQUEST); echo "</pre>"; 
		$chk_state = $oCampaign->chk_state($iStateId, $iCountryId);
		$chk_city = $oCampaign->chk_city($iCityId, $iStateId);
		$sDob1 = $oregister->encrypt($sDob,sENC_KEY);
		$sSsn1 = $oregister->encrypt($sSsn,sENC_KEY);
		$iId = $oCampaign->insert_campaign($iUid,sanitize($sName),sanitize($sLName),$sDob1,$sSsn1,$sEmail,$sPhone,sanitize($sAddress),$iCountryId,$iStateId,$iCityId,$sZipcode);
		// echo "last inserted id:".$iId; die();
		if($iId > 0)
		{
			$oregister->redirect('basic_information.php?cid='.$iId);
		}
		/*try 
		{
			if ($account = \Stripe\Account::create(
				array(
					"country" => "US",
					"managed" => true,
					'email' => $sEmail, //Email Address
					'business_url' => SITE_FULL_URL, //Business URL
					'legal_entity' => array(
						'address' => array(
							'city' => $iCityId, //Representative City
							'country' => 'US', //Representative Country
							"line1" => $sAddress, //Representative Address Line1
							"postal_code" => $sZipcode, //Representative Postal Code
							"state" => 'CA' //Representative State
						),
						'dob' => array(
							'day' => $Date, //Representative Date of Birth (Day)
							'month' => $Month, //Representative Date of Birth (Month)
							'year' => $Year //Representative Date of Birth (Year)
						),
						'first_name' => $firstname, //Representative First Name
						'last_name' => $lastname, //Representative Last Name
						//'personal_id_number' => '000000000', //Representative Personal ID #
						'ssn_last_4' => $sSsn //Representative SSN #
					),
					'statement_descriptor' => SITE_DOMAIN_CAP,
					'support_phone' => SUPPORT_PHONE,
					'transfer_schedule' => array(
						'interval' => 'manual'
					)
				)
			)) 
			{
				$sDob1 = $oregister->encrypt($sDob,sENC_KEY);
				$sSsn1 = $oregister->encrypt($sSsn,sENC_KEY);
				$iId = $oCampaign->insert_campaign($iUid,sanitize($sName),sanitize($sLName),$sDob1,$sSsn1,$sEmail,$sPhone,sanitize($sAddress),$iCountryId,$iStateId,$iCityId,$sZipcode);
				$account1 = $account->__toArray(true);
				$accid = $account1['id'];
				$oCampaign->update_acc_campaign($iId, $accid);
				$oregister->redirect('basic_information.php?cid='.$iId);
			}
		} catch (Stripe_InvalidRequestError $e) {
			// Invalid parameters were supplied to Stripe's API
			echo $errortype = 2;
		} catch (Stripe_AuthenticationError $e) {
			// Authentication with Stripe's API failed
			echo $errortype = 3;
		} catch (Stripe_ApiConnectionError $e) {
			// Network communication with Stripe failed
			echo $errortype = 4;
		} catch (Stripe_Error $e) {
			// Display a very generic error to the user, and maybe send
			// yourself an email
			echo $errortype = 5;
		} catch (Exception $e) {
			// Something else happened, completely unrelated to Stripe
			echo $errortype = 6;
		}*/
	} else if($REQUEST['mode'] == 'edit'){
		$iUid = $_SESSION['uid'];
		$sName = $REQUEST['fld_cname'];
		$sLName = $REQUEST['fld_clname'];
		$sDob = date('Y-m-d',strtotime($REQUEST['fld_dob']));
		$DOB = explode("-",$sDob);
		$Date = $DOB[2];
		$Month = $DOB[1];
		$Year = $DOB[0];
		$sSsn = $REQUEST['fld_ssn'];
		$accid = $REQUEST['fld_ac'];
		$sEmail = $REQUEST['fld_cemail'];
		$sPhone = $REQUEST['fld_cphone'];
		$sAddress = $REQUEST['fld_caddress'];
		$iCountryId = $REQUEST['fld_country'];
		$iStateId = $REQUEST['fld_state'];
		$iCityId = $REQUEST['fld_city'];
		$sZipcode = $REQUEST['fld_czipcode'];	
		$chk_state = $oCampaign->chk_state($iStateId, $iCountryId);
		$chk_city = $oCampaign->chk_city($iCityId, $iStateId);
		$sDob1 = $oregister->encrypt($sDob,sENC_KEY);
		$sSsn1 = $oregister->encrypt($sSsn,sENC_KEY);
		if ($oCampaign->update_campaign_step_1($REQUEST['cid'],sanitize($sName),sanitize($sLName),$sDob1,$sSsn1,$sEmail,$sPhone,sanitize($sAddress),$iCountryId,$iStateId,$iCityId,$sZipcode))
		{
			$oregister->redirect('basic_information.php?cid='.$REQUEST['cid']);
		}
	}
}
if(isset($REQUEST['m']) && $REQUEST['m'] == 'e' and $REQUEST['cid'] > 0)
{
	$cid = $REQUEST['cid'];
	$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
	$fld_campaign_title = $aCampaignDetail['fld_campaign_title'];
	$fld_cname = $aCampaignDetail['fld_cname'];
	$fld_clname = $aCampaignDetail['fld_clname'];
	//$fld_dob = date('m/d/Y',strtotime($aCampaignDetail['fld_dob']));
	$fld_dob1 = $oregister->decrypt($aCampaignDetail['fld_dob'],sENC_KEY);
	if ($fld_dob1 != '') {
		$fld_dob = date('m/d/Y',strtotime($fld_dob1));
	} else {
		$fld_dob = '';
	}
	$fld_ssn = $oregister->decrypt($aCampaignDetail['fld_ssn'],sENC_KEY);
	$fld_ac = $aCampaignDetail['fld_ac'];
	$fld_cemail = $aCampaignDetail['fld_cemail'];
	$fld_cphone = $aCampaignDetail['fld_cphone'];
	$fld_caddress = $aCampaignDetail['fld_caddress'];
	$sState1 = $aCampaignDetail['fld_cstate'];
	if ($sState1 != '') {
		$iState = $sState1;
	} else {
		$iState = '';
	}
	$sCountry1 = $aCampaignDetail['fld_ccountry'];
	if ($sCountry1 != '') {
		$iCountry = $sCountry1;
	} else {
		$iCountry = 'United States';
	}
	//$iCountry = $aCampaignDetail['fld_ccountry'];
	//$iState = $aCampaignDetail['fld_cstate'];
	$fld_city = $aCampaignDetail['fld_ccity'];
	$fld_czipcode = $aCampaignDetail['fld_czipcode'];
	$mode = 'edit';
}else{
	$uDetail=$oregister->getuserdetail($_SESSION['uid']);
	$fld_campaign_title = '';
	$fld_cname =  $uDetail['fld_name'];
	$fld_clname =  $uDetail['fld_lname'];
	$fld_cemail =  $uDetail['fld_email'];
	$fld_ac = '';
	$fld_cphone =  $uDetail['fld_phone'];
	$fld_caddress =  $uDetail['fld_address'];
	$sState1 = $uDetail['fld_state'];
	if ($sState1 != '') {
		$iState = $sState1;
	} else {
		$iState = '';
	}
	$sCountry1 = $uDetail['fld_country'];
	if ($sCountry1 != '') {
		$iCountry = $sCountry1;
	} else {
		$iCountry = 'United States';
	}
	//$iCountry = $uDetail['fld_country'];
	//$iState = $uDetail['fld_state'];
	$fld_city = $uDetail['fld_city'];
	$fld_czipcode = $uDetail['fld_zip'];
	$mode = 'add';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
<title>Admin<?php echo sWEBSITENAME;?> - Start Campaign</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<!-- Preloader -->
<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<div id="wrapper">
  <? include_once('header.php');?>
  <!-- Left side column. contains the logo and sidebar -->
  <? include_once('left_panel.php');?>
  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <!--row -->
      <div class="row">
          <div class="col-sm-12">
		  <h1 class="h1styling">Start New Campaign</h1>
		  <div class="line3"></div>
		  <? if ($fld_campaign_title != '') { ?>
		  <h4 class="h4styling"><?=$fld_campaign_title;?></h4>
		  <div class="line3"></div>
		  <? } ?>
		  <!-- .white-box -->
          <div class="white-box">
			<div class="Campaign_in">
<!--<div class="div_image">
</div>-->
<div class="div_ul">
<ul>
<li class="selected"><a <?=$camp_inform;?> style="color: #fff;">YOUR CAMPAIGN START </a></li>
<li class="select_no"><a <?=$basic_inform;?>>BASIC INFORMATION</a></li>
<li class="select_no2"><a <?=$build_team;?>>BUILD YOUR TEAM</a></li>
<li class="select_no3"><a <?=$go_live;?>>FINISH</a></li>
</ul>
</div>
<div class="formdiv-in">
   <? if ($errortype == 6) { ?>
   <div id="notifications" class="alert alert-danger alert-dismissable" style="padding: 6px 15px !important">
       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error: </b>Account not created, please enter a valid information such as SSN, City, Zipcode and Date of Birth should be 18+
   </div>
   <? } elseif ($errortype == 5) { ?>
   <div id="notifications" class="alert alert-danger alert-dismissable" style="padding: 6px 15px !important">
       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error: </b>Invalid Connection Error
   </div>
   <? } elseif ($errortype == 4) { ?>
   <div id="notifications" class="alert alert-danger alert-dismissable" style="padding: 6px 15px !important">
       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error: </b>Connection Error.
   </div>
   <? } elseif ($errortype == 3) { ?>
   <div id="notifications" class="alert alert-danger alert-dismissable" style="padding: 6px 15px !important">
       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error: </b>Authentication Error.
   </div>
   <? } elseif ($errortype == 2) { ?>
   <div id="notifications" class="alert alert-danger alert-dismissable" style="padding: 6px 15px !important">
       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error: </b>Invalid request submitted.
   </div>
   <? } ?>
   <form name="start_campaign" id="start_campaign" data-toggle="validator"  method="post">
	<div class="form-group col-sm-6">
		<label for="fld_cname" class="control-label">Campaign Manager Full Legal First Name<span style="color:#FF0000">*</span></label>
<div class="input-labal">
   <input type="text" placeholder="Legal First Name" class="formdivtext" required="" name="fld_cname" id="fld_cname" value="<?=$fld_cname?>">
  <!-- <img src="images/img.png"  />-->
   <span class="fa fa-user">
   </span>
 </div>		<div class="help-block with-errors"></div>
	</div>
	<div class="form-group col-sm-6">
		<label for="fld_clname" class="control-label">Campaign Manager Full Legal Last Name<span style="color:#FF0000">*</span></label>
<div class="input-labal">
   <input type="text" placeholder="Legal Last Name" class="formdivtext" required="" name="fld_clname" id="fld_clname" value="<?=$fld_clname?>">
  <!-- <img src="images/img.png"  />-->
   <span class="fa fa-user">
   </span>
 </div>		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group col-sm-6">
		<label for="fld_cemail" class="control-label">Email<span style="color:#FF0000">*</span></label>
<div class="input-labal">
   <input type="email" placeholder="Email" class="formdivtext" required="" name="fld_cemail" id="fld_cemail" value="<?=$fld_cemail?>">
   <span class="fa fa-envelope">
   </span>
 </div>		<div class="help-block with-errors"></div>
	</div>
	<div class="form-group col-sm-6">
		<label for="fld_dob" class="control-label">Date of Birth<span style="color:#FF0000">*</span></label>
<div class="input-labal">
   <?php $fld_dob = isset($fld_dob) ? $fld_dob : '';
   if ($fld_dob != '') { ?>
   <input type="password" placeholder="mm/dd/yyyy" class="formdivtext" required="" name="fld_dob" id="fld_dob" value="<?=$fld_dob?>" data-inputmask="'mask': ['99/99/9999', '99/99/9999']" data-mask="">
   <a href="javascript:void(0);" id="hidedob"  style="font-size:20px;" alt="Hide Date of Birth" title="Show Date of Birth" onClick="showdob()">
	  <span class="fa fa-eye" style="border-left: 1px solid #e4e4e4;color: #aaaaaa;padding: 3px 8px;height: 26px;margin: 3px 0 0 0;font-size: 20px;position: absolute;right: 24px;z-index: 12;"></span>
   </a>
   <? } else { ?>
   <input type="text" placeholder="mm/dd/yyyy" class="formdivtext" required="" name="fld_dob" id="fld_dob" value="<?=$fld_dob?>" data-inputmask="'mask': ['99/99/9999', '99/99/9999']" data-mask="">
   <? } ?>
  <!-- <img src="images/img.png"  />-->
 </div>		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group col-sm-6">
		<label for="fld_cemail" class="control-label">Last 4 SSN #<span style="color:#FF0000">*</span></label>
<div class="input-labal">
   <?php
	$fld_ssn = isset($fld_ssn) ? $fld_ssn : '';
   if ($fld_ssn != '') { ?>
   <input type="password" placeholder="Last 4 SSN #" class="formdivtext" required="" name="fld_ssn" id="fld_ssn" value="<?=$fld_ssn?>" data-inputmask="'mask': ['9999', '9999']" data-mask="">
   <a href="javascript:void(0);" id="hidessn"  style="font-size:20px;" alt="Show Social Security Number" title="Show Social Security Number" onClick="showssn()">
	  <span class="fa fa-eye" style="border-left: 1px solid #e4e4e4;color: #aaaaaa;padding: 3px 8px;height: 26px;margin: 3px 0 0 0;font-size: 20px;position: absolute;right: 24px;z-index: 12;"></span>
   </a>
   <? } else { ?>
   <input type="text" placeholder="Last 4 SSN #" class="formdivtext" required="" name="fld_ssn" id="fld_ssn" value="<?=$fld_ssn?>" data-inputmask="'mask': ['9999', '9999']" data-mask="">   
   <? } ?>
 </div>		<div class="help-block with-errors"></div>
	</div>
	<div class="form-group col-sm-6">
		<label for="fld_cphone" class="control-label">Phone<span style="color:#FF0000">*</span></label>
<div class="input-labal">
   <input type="text" class="formdivtext" required="" name="fld_cphone" id="fld_cphone" value="<?=$fld_cphone?>" data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="___-___-____">
   <span class="fa fa-phone"></span>
 </div>		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group col-sm-6">
		<label for="fld_caddress" class="control-label">Address<span style="color:#FF0000">*</span></label>
<div class="input-labal">
   <input type="text" placeholder="Address" class="formdivtext" name="fld_caddress" id="fld_caddress" value="<?=$fld_caddress?>" >
   <span class="fa fa-map-marker"></span>
 </div>		<div class="help-block with-errors"></div>
	</div>
	<div class="form-group col-sm-6">
		<label for="fld_czipcode" class="control-label">ZIP Code<span style="color:#FF0000">*</span></label>
<div class="input-labal">
   <input type="text" placeholder="Zip Code" class="formdivtext" name="fld_czipcode" id="fld_czipcode" value="<?=$fld_czipcode;?>" required>
  	</div>		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group col-sm-6">
		<label for="fld_city" class="control-label">City<span style="color:#FF0000">*</span></label>
		<?
		$sCiData = $oregister->getcity($iState);
		$iCiRecords = count($sCiData);
		?>
		<div class="styled-select"  id="divCity">
		<!--<select name="fld_city" id="fld_city"  class="form-control colorMeBlue noValue" required>
		<option value="">Select city</option>
		<?
		for($ci=0;$ci<$iCiRecords;$ci++)
		{
		?>
			<option value="<?=$sCiData[$ci]['name']?>"  <? if($sCiData[$ci]['name'] == $fld_city){?> selected<? }?>><?=$sCiData[$ci]['name']?></option>
		<?
		}
		?>
		</select>-->
		<div class="input-labal">
		<input type="text" placeholder="Enter City" style="background-color: #ffffff !important;border-radius: 4px !important;border: 0px;color: black !important;width: 86%;" name="fld_city" id="fld_city" value="<?=$fld_city;?>" required>
		</div>
	</div>
	</div>
	<div class="form-group col-sm-6">
		<label for="fld_state" class="control-label">State<span style="color:#FF0000">*</span></label>
		<?
        $sSData = $oregister->getstate($iCountry);
        $iSRecords = 0;
        if($sSData){
        	$iSRecords = count($sSData);
        }
        ?>
		<div class="styled-select" id="divState">
        <!-- <select name="fld_state" id="fld_state"  class="form-control colorMeBlue noValue" required>
          <option value="">Select state</option>
		  <?
          for($s=0;$s<$iSRecords;$s++)
          {
          ?>
          <option value="<?=$sSData[$s]['name']?>" <? if($sSData[$s]['name'] == $iState){?> selected<? }?>><?=$sSData[$s]['name']?></option>
          <?
          }
          ?>
        </select>-->
		<div class="input-labal">
		<input type="text" placeholder="Enter State" style="background-color: #ffffff !important;border-radius: 4px !important;border: 0px;color: black !important;width: 86%;" name="fld_state" id="fld_state" value="<?=$iState;?>" required>
		</div>
		</div>
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group col-sm-6">
		<label for="fld_country" class="control-label">Country<span style="color:#FF0000">*</span></label>
		<?
        $sCData = $oregister->getcountry();
        $iCRecords = count($sCData);
        ?>
		<!--<select name="fld_country" id="fld_country" class="form-control colorMeBlue" required >
          <option value="231">United States</option>
		  <option value="">Select country</option>
          <?
          for($c=0;$c<$iCRecords;$c++)
          {
          ?>
          <option value="<?=$sCData[$c]['name']?>" <? if($sCData[$c]['name'] == $iCountry){?> selected<? }?>><?=$sCData[$c]['name']?></option>
          <?
          }
        ?>
        </select>-->
		<div class="input-labal">
		<input type="text" placeholder="Enter Country" style="background-color: #ffffff !important;border-radius: 4px !important;border: 0px;color: black !important;width: 86%;" name="fld_country" id="fld_country" value="<?=$iCountry;?>" required>
		</div>
		<div class="help-block with-errors"></div>
	</div>
   <div class="clearfix"></div>
   <div class="form-group">
    	<input type="hidden" name="mode" id="mode" value="<?=$mode?>">  
        <?php $cid_4_hidden_field = isset($REQUEST['cid']) ? $REQUEST['cid'] : 0; ?>
        <input type="hidden" name="cid" id="cid" value="<?=$cid_4_hidden_field?>"> 
        <input type="hidden" name="fld_ac" id="fld_ac" value="<?=$fld_ac?>"> 
		<div class="col-sm-6" align="left">
   <button class="btn btn-primary waves-effect waves-light" type="button" onclick="window.location.href='manage_campaign.php'"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Cancel</button><br><br>
   <a href="TOC_Fundme_R1.1.2_Rev_10-2016.pdf" target="_blank">Click here</a> to read our terms and conditions
  </div>
		<div class="col-sm-6" align="right">
   <button class="btn btn-success waves-effect waves-light disabled" type="submit">Save &amp; Continue <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button><br><br>
   By continuing your are agreeing to our terms and agreements
  </div>
   </div>
   <div class="clearfix"></div>
   </form>
   </div>
</div>
		  </div>
		  </div>
    </div>
    <!-- /.container-fluid -->
  </div>
  </div>
  <!-- /#page-wrapper -->
	<!-- #footer -->
    <? include_once('footer.php');?>
	<!-- /#footer -->
<!-- /#wrapper -->
</div>
<!-- jQuery -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Menu Plugin JavaScript -->
<script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
<!--Nice scroll JavaScript -->
<script src="js/jquery.nicescroll.js"></script>
<!--Wave Effects -->
<script src="js/waves.js"></script>
<!-- Custom Theme JavaScript -->
<script src="js/myadmin.js"></script>
<!--Counter js -->
<script src="bower_components/waypoints/lib/jquery.waypoints.js"></script>
<script src="bower_components/counterup/jquery.counterup.min.js"></script>
<!--<script src="js/mask.js"></script>-->
<!--Sparkline charts js -->
<script src="bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
<script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
<script src="bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>
<script src="js/validator.js"></script>
<script type="text/javascript">
   jQuery(document).ready(function($) {
    $('.vcarousel').carousel({
     interval: 3000
   })
    $(".counter").counterUp({
        delay: 100,
        time: 1200
    });
    $(':checkbox:checked').prop('checked',false);
 });
 $('select').on('change', function(){
    var $this = $(this);
    if (!$this.val()) {
        $this.addClass('noValue');
    } else {
        $this.removeClass('noValue');
    }
});
$('#fld_dob').datepicker({
	orientation: "auto right",
	endDate: "today",
	todayHighlight: true,
	//forceParse: false,
    format: 'mm/dd/yyyy',
	autoclose: true
});
/*$('#fld_country').on('change', function() {
	  //alert( this.value ); // or $(this).val()
	  $iCountryId = this.value;
	  $.ajax({url: "showstate1.php?cid="+$iCountryId, success: function(result){
        $("#divState").html(result);
    }});
});
$('#fld_state').on('change', function() {
	  //alert( this.value ); // or $(this).val()
	  $iStateId = this.value;
	  $.ajax({url: "showcity1.php?sid="+$iStateId, success: function(result){
        $("#divCity").html(result);
    }});
});
var fld_city = $("#fld_city").val();
if (fld_city != '') {	
	$("#fld_city").removeClass('noValue');
} 
var fld_state = $("#fld_state").val();
if (fld_state != '') {	
	$("#fld_state").removeClass('noValue');
} 
var fld_country = $("#fld_country").val();
if (fld_country != '') {	
	$("#fld_country").removeClass('noValue');
} 
$('#fld_czipcode').on('focusout', function() {
	  $izipcode = this.value;
	  $.ajax({url: "showzipcode.php?zid="+$izipcode, success: function(result){
		var jdata = JSON.parse(result);
		if (jdata) {
		$("#fld_country").removeClass('noValue');
		$("#fld_state").removeClass('noValue');
		$("#fld_city").removeClass('noValue');
		if (jdata.country == 'United States') {
			$("#fld_country option:selected").text(jdata.country).val(jdata.countryid);
			$("#fld_state option:selected").text(jdata.state).val(jdata.stateid);
			$("#fld_city option:selected").text(jdata.city).val(jdata.cityid);
			document.getElementById('fld_czipcode').setCustomValidity("");
			$('#fld_czipcode').focusout();
		} else {
			document.getElementById('fld_czipcode').setCustomValidity('This is Invalid Zipcode, Please enter a Valid Zipcode');
			$('#fld_czipcode').focusout();
		}
		}
    }});
});*/
function showdob() {
    var dob = $("#fld_dob");
    if(dob.val() != "") {
        if($(dob).attr('type') == "password") {
            $(dob).attr('type', 'text');
			$("#hidedob").attr('alt', 'Hide Date of Birth');
            $("#hidedob").attr('title', 'Hide Date of Birth');
        } else {
            $(dob).attr('type', 'password');
			$("#hidedob").attr('alt', 'Show Date of Birth');
            $("#hidedob").attr('title', 'Show Date of Birth');
        }
    } else {
    }
}
function showssn() {
    var ssn = $("#fld_ssn");
    if(ssn.val() != "") {
        if($(ssn).attr('type') == "password") {
            $(ssn).attr('type', 'text');
			$("#hidedob").attr('alt', 'Hide Social Security Number');
            $("#hidedob").attr('title', 'Hide Social Security Number');
        } else {
            $(ssn).attr('type', 'password');
			$("#hidedob").attr('alt', 'Show Social Security Number');
            $("#hidedob").attr('title', 'Show Social Security Number');
        }
    } else {
    }
}
</script>
<script src="js/jquery.inputmask.js"></script>
<script>
$("[data-mask]").inputmask();
</script>
</body>
</html>
<? } else { 
	$oregister->redirect('dashboard.php');
} ?>
<? include_once('bottom.php');?>