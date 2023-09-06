<?
require_once("configuration/dbconfig.php");
$transactionid = $_GET['transaction'];
$aTransactionDetail = $oCampaign->gettransactiondetail($transactionid);
$donor_email = $aTransactionDetail['uemail'];
$donation_amount = $aTransactionDetail['donation_amount'];
$card_number = $aTransactionDetail['card_number'];
$new_card_number = substr($card_number, -4);
$expiry_date = isset($aTransactionDetail['expiry_date']) ? $aTransactionDetail['expiry_date'] : "";
$payment_through = $aTransactionDetail['payment_through'];
$uid = $aTransactionDetail['uid'];
$cid = $aTransactionDetail['cid'];
$pid = $aTransactionDetail['refferal_by'];
$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
$fld_campaign_id = $aCampaignDetail['fld_campaign_id'];
$fld_hashcamp = $aCampaignDetail['fld_hashcamp'];
$fld_campaign_title = $aCampaignDetail['fld_campaign_title'];
$fld_organization_name = $aCampaignDetail['fld_organization_name'];
$fld_nonprofit = $aCampaignDetail['fld_nonprofit'];
$fld_nonprofit_number = $aCampaignDetail['fld_nonprofit_number'];

$aParticipantDetail = $oregister->getuserdetail($pid);
$participant_fname = $aParticipantDetail['fld_name'];
$participant_lname = $aParticipantDetail['fld_lname'];
$participant_phone = $aParticipantDetail['fld_phone'];
$participant_email = $aParticipantDetail['fld_email'];
$url = sHOME.'campaign.php?cid='.$fld_hashcamp.'!'.$cid.'!'.$pid.'%26hashid='.$uid.'';
$text = 'Please take a moment to review '.$fld_campaign_title.'. Your support is greatly appreciated! ';
$email_msg = "Hi%0D%0A%0D%0APlease take a moment and consider supporting $fld_campaign_title. Your generous donation will help make a difference.%0D%0A%0D%0A";
$email_msg .= "$url%0D%0A%0D%0A";
$email_msg .= "Thank you for your consideration.";

if (array_key_exists('btnSubmit', $_POST))
{
	$forfacebook = $_POST['forfacebook'];
	if (isset($forfacebook)) {
		$comment = $_POST['comment'];
		//$transid = $_GET['transid'];
		$oregister->redirect('fbprocess.php?transactionid='.$transactionid.'&comment='.$comment.'');
	} else {
		$oregister->redirect('index.php');
		$comment = $_POST['comment'];
		$transid = $_POST['transid'];
		$oCampaign->updatetransaction($transid, '', $comment);
		$oregister->redirect('index.php');
	}
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
        <br>
		<h3>Your message has been successfully submitted.</h3>
		<h3>Your receipt has been email to <?=$donor_email;?></h3>
		<h3>HELP US SPREAD THE WORD!</h3>
		<div class="fleft">    
			<div class="post_social">
				<a href="javascript:void(0)" class="icon-fb" onclick="javascript:genericSocialShare('http://www.facebook.com/sharer.php?u=<?=$url;?>')" title="Facebook Share"><img src="images/fb.png"/></a>
				<a href="javascript:void(0)" class="icon-tw" onclick="javascript:genericSocialShare('http://twitter.com/share?text=<?=$text;?>;url=<?=$url;?>')" title="Twitter Share"><img src="images/tw.png"/></a>
				<a href="mailto:?subject=Join me in supporting <?=$fld_campaign_title;?>&body=<?=$email_msg;?>" class="icon-tw" title="Email Share"><img src="images/mail.png"/></a>
			</div>
			<script type="text/javascript" async >
			function genericSocialShare(url){
				window.open(url,'sharer','toolbar=0,status=0,width=648,height=395');
				return true;
			}
			</script>
		</div>
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