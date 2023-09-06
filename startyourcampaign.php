<?
require_once("configuration/dbconfig.php");
$REQUEST = &$_REQUEST;
checkSetInArrayAndReturn($REQUEST, 'cid', 0);

$msg1 = '';
// Google Recaptcha
require('lib/src/autoload.php');
$siteKey = CAPTCH_SITE_KEY;
$secret = CAPTCH_SECRET;
$recaptcha = new \ReCaptcha\ReCaptcha($secret);
// Google Recaptcha
if(array_key_exists('submit', $_POST))
{
	$fld_name = xss_clean($REQUEST['fld_name']);
	$fld_organization = xss_clean($REQUEST['fld_organization']);
	$fld_email = xss_clean($REQUEST['fld_email']);
	$fld_phone = xss_clean($REQUEST['fld_phone']);
	$fld_call = xss_clean($REQUEST['fld_call']);
	$gRecaptchaResponse = $REQUEST['g-recaptcha-response']; //google captcha post data
	$remoteIp = $_SERVER['REMOTE_ADDR']; //to get user's ip
	$recaptchaErrors = ''; // blank varible to store error
	$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp); //method to verify captcha
	if ($resp->isSuccess()) {
		if ($fld_name != '' && $fld_organization != '' && $fld_email != '' && $fld_phone != '' && $fld_call != '') {
			$sn_dist_email = 0;
			if ($REQUEST['cid'] > 0) {
				$sn_dist_email = 1;
			} 
			if ($oCampaign->sendstartyourcampaign($fld_name,$fld_organization,$fld_email,$fld_phone,$fld_call,$sn_dist_email,$getemail,$getname))
			{
				if ($sn_dist_email == 1) {
					$oregister->redirect("thanks.php?msg=12&cid=".$REQUEST['cid']."");
				} else {
					$oregister->redirect("thanks.php?msg=12");
				}
			} else {
				$oregister->redirect('startyourcampaign.php?msg=1');
			}
		} else {
			$msg1 = '<p style="font-size:14px; text-align:center"><b>Error:</b> Please review the form</p>';
		}
	} else {
		$recaptchaErrors = $resp->getErrorCodes(); // set the error in varible
		if(isset($recaptchaErrors[0])) {
			$msg1 = '<p style="font-size:14px; text-align:center"><b>Error:</b> '.$recaptchaErrors[0].'</p>';
		}
	}
	//$msg1 = '<div></div>';
	//$oregister->redirect('index.php');
	if (isset($REQUEST['msg'])) {
		$msg1 = '<p style="font-size:14px; text-align:center"><b>Error:</b> Please review the form</p>';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0031)<?php echo SITE_URL;?>contact-us/ -->
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>.:: Start Your Campaign - <?=$getcname;?> ::.</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.css">
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>  
<link href="css/style.css" rel="stylesheet" type="text/css"> 

<link href="css/ninja-slider.css" rel="stylesheet" type="text/css">
<script src="js/ninja-slider.js" type="text/javascript"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<? include_once('header.php');?>
<section class="ipcontentsection">
	<div class="container newContainer">
		<div class="mid_sec2">
			<div class="mid_secin">
				<div class="row2">
					<h1 class="h1styling" align="center">Start Your Campaign</h1>
					<br><br>
					<div class="col-sm-12">
					<? 
						echo $msg1;
					?>
					</div>
					<p style="text-align:center">Thank you for your interest in <?=$getcname;?>. Please provide the following information so one of our fundraising specialists can contact you to begin your fundraiser. We look forward to contacting you soon.</p>
					<form action="" data-toggle="validator" id="startyourcampaign"  method="post">
						<div class="form-group col-sm-6">
							<label for="fld_name" class="control-label">First and last name <i style="color:red;">*</i></label>
							<input type="text" class="form-control" id="fld_name" name="fld_name" placeholder="e.g John Smith" required>
							<div class="help-block with-errors"></div>
						</div>
						<div class="form-group col-sm-6">
							<label for="fld_organization" class="control-label">Name of organization <i style="color:red;">*</i></label>
							<input type="text" class="form-control" id="fld_organization" name="fld_organization" value="" placeholder="e.g John Baseball" required>
							<div class="help-block with-errors"></div>
						</div>
						<div class="clearfix"></div>
						<div class="form-group col-sm-6">
							<label for="fld_email" class="control-label">Email address <i style="color:red;">*</i></label>
							<input type="email" class="form-control" id="fld_email" name="fld_email" placeholder="e.g johnsmith@domain.com" required>
							<div class="help-block with-errors"></div>
						</div>
						<div class="form-group col-sm-6">
							<label for="fld_phone" class="control-label">Contact phone # <i style="color:red;">*</i></label>
							<input type="text" class="form-control" id="fld_phone" name="fld_phone" data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="e.g 123-123-4567" required>
							<div class="help-block with-errors"></div>
						</div>
						<div class="clearfix"></div>
						<div class="form-group col-sm-12">
							<label for="fld_call" class="control-label">Best time to call <i style="color:red;">*</i></label>
							<textarea type="text" class="form-control" id="fld_call" name="fld_call" placeholder="e.g 3.00 PM" required></textarea>
							<div class="help-block with-errors"></div>
						</div>
						<div class="clearfix"></div>
						<div class="form-group col-sm-12">
							<div class="g-recaptcha" data-<?php echo CAPTCH_SITE_KEY;?>></div>
						</div>
						<div class="clearfix"></div>
						<div class="form-group">
							<div class="col-sm-5">
								<span class="fa fa-chevron-left"></span><button class="btn-2" type="button" onClick="window.location.href='index.php'">Cancel</button>
							</div>
							<div class="col-sm-2"></div>
							<div class="col-sm-5">
								<button class="btn-2" name="submit" type="submit">Submit </button><span class="fa fa-chevron-right"></span>
							</div>
						</div>
						<div class="clearfix"></div>
					</form>
					<div class="col-sm-12">
						<div align="left" style="font-family:arial; margin-top:40px; font-size:26px"><b><i>"If you fail to plan, you are planning to fail."</i></b></div>
						<div align="left" style="font-family:new times roman; margin-top:-8px; margin-left:65%; font-size:18px"><i>by Benjamin Franklin</i></div>
						<div align="left" style="text-align:center;font-family:new times roman; margin-top:5px; font-size:18px">Sums up why 99% of all fundraising efforts fail to reach their goals.</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<? include_once('footer.php');?>
<script src="cms/js/validator.js"></script>
<script src="cms/js/jquery.inputmask.js"></script>
<script>
$("[data-mask]").inputmask();
</script>
</body>
</html>