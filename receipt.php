<?

require_once("configuration/dbconfig.php");

$transactionid = $_GET['transaction'];

$aTransactionDetail = $oCampaign->gettransactiondetail($transactionid);

$donation_amount = $aTransactionDetail['donation_amount'];

$card_number = $aTransactionDetail['card_number'];
//Rewards
$is_reward = $aTransactionDetail['is_reward'];
$reward_id = $aTransactionDetail['reward_id'];
$reward_desc = $aTransactionDetail['reward_desc'];
//Rewards

$new_card_number = substr($card_number, -4);

$expiry_date = isset($aTransactionDetail['expiry_date']) ? $aTransactionDetail['expiry_date'] : "";

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



if (array_key_exists('btnSubmit', $_POST))

{

	//$forfacebook = $_POST['forfacebook'];

	//if (isset($forfacebook)) {

		//$comment = $_POST['comment'];

		//$transid = $_GET['transid'];

		//$oregister->redirect('fbprocess.php?transactionid='.$transactionid.'&comment='.$comment.'');

	//} else {

		//$oregister->redirect('index.php');

		$comment = $_POST['comment'];

		$transid = $_POST['transid'];

		$oCampaign->updatetransaction($transid, '', $comment);

		$redirect = 'thankyou.php?transaction='.$transid.'';

		header("location:".$redirect);

	//}

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<!-- saved from url=(0031)<?php echo SITE_URL;?>contact-us/ -->

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>.:: <?=$fld_campaign_title;?> - <?php echo sWEBSITENAME;?> ::.</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="css/bootstrap.css">

<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">





<script src="js/jquery.min.js"></script>

<script src="js/bootstrap.min.js"></script>  

<link href="bars/bars.css" rel="stylesheet" type="text/css">

<link href="css/style.css" rel="stylesheet" type="text/css"> 

<link href="css/style1.css" rel="stylesheet" type="text/css"> 

<link href="css/style-resp.css" rel="stylesheet">



<link href="css/ninja-slider.css" rel="stylesheet" type="text/css">

<script src="js/ninja-slider.js" type="text/javascript"></script>

<link href="css/owl.carousel.min.css" rel="stylesheet" type="text/css">

<link href="css/owl.theme.default.css" rel="stylesheet" type="text/css">

<style>

@media print

{    

    .signupul, .leavemsg, .donation, .donation-thanks, .need-fundraise, .footerfluid

    {

        display: none !important;

    }

	.light-header-bg-logo .logo

	{

        display: none !important;

    }

	.light-header-bg-logo .logo.print-logo{display: block !important;}

	.light-header-bg-logo:after {

    content: '';

    background: url(logo_print.png) no-repeat;

    height: 66px;

    width: 173px;

    display: block;

}

}



</style>



</head>



<body>

<? include_once('header.php');?>

<section class="ipcontentsection">

  <!-- header -->

  <div class="contents">

	<div class="container">	

      <!-- Top Heading -->  

      <div class="top-heading-dark">

        <h1><?=$fld_campaign_title;?></h1>

		<h4>Campaign # <?=str_pad($fld_campaign_id, 7, "0", STR_PAD_LEFT);?></h4>

      </div>

      <!-- Top Heading -->  

	  <!-- Campaign Name -->  

      <div class="campaign-heading">

        <h2>Thanks for Your Donation to <?=$participant_fname." ".$participant_lname;?></h2>

		<div class="donation-price-box">$<?=number_format($donation_amount, 2,'.',',');?></div>

	  </div>

      <!-- Top Heading --> 

	  <div class="white-box-donation  make-donation">

		<div class="formcontainer col-md-6">

		  <div class="receipt_container">

			<h3>Your Receipt</h3>

			<div class="clearfix"></div>

			<div class="text-center col-md-12 total-donated">Total Donated: <span class="price_highlight">$<?=number_format($donation_amount, 2,'.',',');?><span></div>

			<div class="text-center col-md-12 textbox">Paid with <?=$payment_through;?> <?=$new_card_number;?></div>

			<!--<div class="text-center col-md-12 textbox">We will email a receipt at the end of the year to all donors!</div>-->

			<? if ($fld_nonprofit == 1) { ?>

			<div class="text-center col-md-12 textbox"><b><?=$fld_organization_name;?></b> is a non profit organization. Our Tax ID number is <b><?=$fld_nonprofit_number;?></b>. Please print this receipt to share with your tax consultant.</div>

			<? } ?>

			<div class="clearfix"></div>

			<p class="text-center textbox">This donation will appear on your credit card or bank statement as FundMe, Inc.</p>

			<? if ($is_reward == 1) { ?>
			
			<p class="text-center textbox">Reward: <?=$reward_desc;?></p>

			<? } ?>

		  </div>

		  <div class="clearfix"></div>

		  <form action="" method="POST">

		    <div class="form-group leavemsg col-md-12">

			  <label for="exampleInputEmail1">Leave a Message</label>

			  <textarea class="form-control" rows="15" id="comment" name="comment"></textarea>

			  <!--<div align="center">

				<input type="checkbox" name="forfacebook" id="forfacebook">

				<label for="forfacebook"> Is message post to facebook too</label>

			  </div>-->

		    </div>

		    <div class="form-group col-md-12 text-center">

			  <button type="button" onClick="window.print()" class="donation btn btn-lg">PRINT THIS PAGE</button>

		    </div>

			<div class="form-group col-md-12 text-center">

			  <input type="hidden" name="transid" id="transid" value="<?=$transactionid;?>">

			  <button type="submit" name="btnSubmit" id="btnSubmit" class="donation btn btn-lg">POST MESSAGE</button>

		    </div>

		  </form>

		  <div class="clearfix"></div>

		</div>

		<div class="donation-thanks">

        <b>Thank You for Your Donation! </b><br>

        Your Donation may be tax deductible, please keep your receipt <br>

        that will be emailed to you shortly.

      </div>

      <!-- donation-thanks -->

      </div>

      <!-- make-donation -->

      <div class="need-fundraise">

        <div class="nf-title1">

          If your group needs to fundraise, <?php echo sWEBSITENAME;?> will give you the tools needed.

        </div>

        <!-- nf-title1 -->    

        <div class="nf-title2">

          3 easy steps to create your own donation-based fundraising campaign

        </div>

        <!-- nf-title2 -->

        <div class="nf-button">

		  <a href="startyourcampaign.php">Start your campaign today!</a>

        </div>

		<!-- nf-button -->

      </div>

    </div>

    <!-- container -->

  </div>

  <!-- contents -->

</section>  

<? include_once('footer.php');?>

<script type="text/javascript" src="js/accounting.js"></script>

<script>

function addCommas(x,txtname) {

	var mval = accounting.formatMoney(x); 

	mval = mval.replace('$', '');

	document.getElementById(txtname).value = mval;

}

</script>

<script src="css/owl.carousel/owl.carousel.min.js"></script>

<script src="css/owl.carousel/owl.custom.js"></script>

<!--<script src="cms/js/mask.js"></script>-->

</body></html>