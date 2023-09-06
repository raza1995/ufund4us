<?
require_once("../configuration/dbconfig.php");
if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else { 
	$user_details = $oregister->getuserdetail($_SESSION['uid']);
	checkAndSetInArray($user_details,'fld_name', '');

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
$sPageName = '<li>Manage Campaign</li>';
$sCampaignLink = 'style="color:#F3BE00"';
$sLeftMenuCampaign = 'active';

if (array_key_exists('fastpaysubmit', $_POST)) {
	$cid = $_POST['cid'];
	$cuid = $_POST['cuid'];
	$ctitle = $_POST['ctitle'];
	$roleid = $_SESSION['role_id'];
	$emailfrom = $_POST['emailfrom'];
	$emailfname = $_POST['emailfname'];
	$emaillname = $_POST['emaillname'];
	$amountreq = str_replace(',', '', $_POST['amountreq']);
	
	
	//$emailto = $_POST['emailto'];
	$rid = $_POST['rid'];
	$rname = $_POST['rname'];
	$did = $_POST['did'];
	$dname = $_POST['dname'];
	$aid = $_POST['aid'];
	$aname = $_POST['aname'];
	//$checkpayableto = $_POST['checkpayableto'];
	//$emailmsg = $_POST['emailmsg'];
	$executed = $oCampaign->insert_fast_pay($rid,$rname,$did,$dname,$aid,$aname,$cid,$cuid,$ctitle,$roleid,$emailfrom,$emailfname,$emaillname,$amountreq,$emailto,$checkpayableto);
	if ($executed) {
		$oregister->redirect('manage_campaign.php?msg=8');
	}
}

if (array_key_exists('paymentcsubmit', $_POST)) {
	$cid = $_POST['cid'];
	$cuid = $_POST['cuid'];
	$ctitle = $_POST['ctitle'];
	$rid = $_POST['rid'];
	$rname = $_POST['rname'];
	$did = $_POST['did'];
	$dname = $_POST['dname'];
	$aid = $_POST['aid'];
	$aname = $_POST['aname'];
	
	
	$pc_moneyraised = $_POST['pc_moneyraised'];
	$pc_ufundamt = $_POST['pc_ufundamt'];
	
	$pc_cprofitraised = $_POST['pc_cprofitraised'];
	$pc_cfirstpayment = $_POST['pc_cfirstpayment'];
	$pc_cfirstpaiddate = date("Y-m-d", strtotime($_POST['pc_cfirstpaiddate']));
	$pc_cfirstchecknumber = $_POST['pc_cfirstchecknumber'];
	$pc_csecondpayment = $_POST['pc_csecondpayment'];
	$pc_csecondpaiddate = date("Y-m-d", strtotime($_POST['pc_csecondpaiddate']));
	$pc_csecondchecknumber = $_POST['pc_csecondchecknumber'];
	
	$pc_dpercentage = $_POST['pc_dpercentage'];
	$pc_dflname = $_POST['pc_dflname'];
	$pc_dpayment = $_POST['pc_dpayment'];
	$pc_dpaiddate = date("Y-m-d", strtotime($_POST['pc_dpaiddate']));
	$pc_dchecknumber = $_POST['pc_dchecknumber'];
	
	$pc_rpercentage = $_POST['pc_rpercentage'];
	$pc_rflname = $_POST['pc_rflname'];
	$pc_rpayment = $_POST['pc_rpayment'];
	$pc_rpaiddate = date("Y-m-d", strtotime($_POST['pc_rpaiddate']));
	$pc_rchecknumber = $_POST['pc_rchecknumber'];
	$executed = $oCampaign->insert_payment_center($cid,$cuid,$ctitle,$rid,$rname,$did,$dname,$aid,$aname,$pc_moneyraised,$pc_ufundamt,$pc_cprofitraised,$pc_cfirstpayment,$pc_cfirstpaiddate,$pc_cfirstchecknumber,$pc_csecondpayment,$pc_csecondpaiddate,$pc_csecondchecknumber,$pc_dflname,$pc_dpayment,$pc_dpaiddate,$pc_dchecknumber,$pc_rflname,$pc_rpayment,$pc_rpaiddate,$pc_rchecknumber,$pc_dpercentage,$pc_rpercentage);
	if ($executed) {
		$oregister->redirect('manage_campaign.php?msg=9');
	}
}

if(isset($_GET['m']) && $_GET['m'] == 'edit' and $_GET['id'] > 0)
{
	$sStatus = $_GET['s'];
	$iId = $_GET['id'];
	
	$oCampaign->update_campaign_status($sStatus,$iId);
	$oregister->redirect('manage_campaign.php?msg=5');	
}else if(isset($_GET['m']) && $_GET['m'] == 'del' and $_GET['id'] > 0)
{
	$iId = $_GET['id'];	
	$uid = $_SESSION['uid'];
	$roleid = $_SESSION['role_id'];
	$chkdate = $oCampaign->chkcampaign($iId, $uid);
	$camp_status = $chkdate['fld_status'];
	$start_date = $chkdate['daysstart'];
	$end_date = $chkdate['daysend'];
	$startend_date = $chkdate['startenddate'];
	$daysleft = $chkdate['daysleft'];
	if ($roleid == 2 || $roleid == 3 || $roleid == 6) {
		if ($daysleft > 0) {
			$oCampaign->delete_campaign($iId);
		}
	} elseif ($roleid == 1) {
		$oCampaign->delete_campaign($iId);
	}
	$oregister->redirect('manage_campaign.php?msg=6');
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
<title>Admin<?php echo sWEBSITENAME;?> - Manage Campaign</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<link href="bower_components/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<style>
.dataTables_wrapper {
	overflow-x: scroll!important;
}
#example1 th {
	vertical-align: middle;
}
#example1 td {
	vertical-align: middle;
}
#example1_wrapper table.dataTable {
	margin-bottom: 0px !important;
	margin-top: 0px !important;
}
#example1_wrapper .dataTables_scrollBody .sorting:after, #example1_wrapper .dataTables_scrollBody .sorting_asc:after{content:'';display:none !important;}
#example1_wrapper table{
  margin: 0 auto;
  width: 100%;
  clear: both;
  border-collapse: collapse;
  table-layout: fixed; 
  word-wrap:break-word; 
}
#example1_wrapper .table > tbody > tr > td, #example1_wrapper .table > tbody > tr > th, #example1_wrapper .table > tfoot > tr > td, #example1_wrapper .table > tfoot > tr > th, #example1_wrapper .table > thead > tr > td, #example1_wrapper .table > thead > tr > th {
    padding: 2px 8px !important;
}

#example1_wrapper .table.dataTable tbody tr.selected {
    background-color: #B0BED9 !important;
}

#example1 th.sorting_desc {
    line-height: 0px !important;
	overflow: hidden;
}

#example1 th.sorting_desc:after {
    display:none;
}

#example2_wrapper table.dataTable {
	margin-bottom: 0px !important;
	margin-top: 0px !important;
}
#example2_wrapper .dataTables_scrollBody .sorting:after, #example2_wrapper .dataTables_scrollBody .sorting_asc:after{content:'';display:none !important;}
#example2_wrapper table{
  margin: 0 auto;
  width: 100%;
  clear: both;
  border-collapse: collapse;
  table-layout: fixed; 
  word-wrap:break-word; 
}
#example2_wrapper .table > tbody > tr > td, #example2_wrapper .table > tbody > tr > th, #example2_wrapper .table > tfoot > tr > td, #example2_wrapper .table > tfoot > tr > th, #example2_wrapper .table > thead > tr > td, #example2_wrapper .table > thead > tr > th {
    padding: 5px 8px !important;
}

#example2_wrapper .table.dataTable tbody tr.selected {
    background-color: #B0BED9 !important;
}

.dataTables_wrapper .dataTables_scroll {
    clear: both;
    overflow: auto;
}
a.dt-button.fixed {
	float:left;
	background: #F3BE00 none repeat scroll 0 0 !important;
    border: 0px;
    border-radius: 3px;
    padding: 6px 12px;
    color: #fff;
    margin-right: 6px;
}
.preloader_datatable {
	width: 100%;
    height: 100%;
    top: 0px;
    position: fixed;
    z-index: 99999;
    rgba(255, 255, 255, 0.10);
	text-align: center;
}
.preloader_datatable .preloadimgtext {
	position: absolute;
    top: calc(50% - 3.5px);
    left: calc(45% - 3.5px);
}
</style>
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
<div class="preloader_datatable" style="display:none;">
	<div class="preloadimgtext">
		<img src="images/preload.gif" /><br>
		Loading...Please wait
	</div>
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
		  <h1 class="h1styling">Manage Campaign</h1>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box" style="background: #fff;">
          	 <?
			if(isset($_GET['m']) && $_GET['m']){
			?>
			<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button><?=$aMessage[$_GET['msg']]?>
			</div>
			<? }?>
             <?
			 $uid = $_SESSION['uid'];
			 $rid = $_SESSION['role_id'];
			 $mode = 'compact';
			 if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'extended') {
				$mode = 'extended';
			 }
             //$sCampaignData = $oCampaign->getcampaign($uid, $rid);
			 //$iCountRecords = count($sCampaignData);
			 //if($iCountRecords>0){
			 ?>
				<table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Status</th>
				  <th>Campaign</th>
				  <th>Campaign ID</th>
                  <th>Campaign Name</th>
                  <!-- <th>Application fee</th> -->
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th># Of Days Left</th>
                  <th># Of Participants</th>
                  <th># Of Participants Enrolled</th>
				  <th>Total # of donors</th>
				  <th># Of Projected Donors Uploaded</th>
				  <th># Of Phone# Uploaded</th>
				  <th>% of Donors Uploaded</th>
				  <? if ($mode == 'extended') { ?>
				  <th>Bad Emails</th>
				  <? } ?>
				  <th>Unsubscribe Donors</th>
				  <th># Of Donation Received</th>
				  <th>Avg. Donation Amount</th>
				  <th>Profit Raised</th>
				  <th>Campaign Goal</th>
				  <th>% of Goal</th>
				  <th>Money Raised</th>
				  <th>Fast Pay Amount Available</th>
				  <th>Money Withdrawn</th>
				  <th>Remaining Profit to be paid</th>
				  <? if ($_SESSION['role_id'] == 1) { //Administrator ?>
					  <th>Comm. Level</th>
					  <th>Dist. Comm</th>
					  <th>Dist. Name</th>
					  <th>Contact Name</th>
					  <th>Company Name</th>
					  <th>Rep. Comm</th>
					  <th>Rep. Name</th>
					  <th>ER Comm</th>
					  <th>Campaign Paid</th>
					  <th>Dist. Paid</th>
					  <th>Rep. Paid</th>
					  <th>Account ID</th>
				  	  <th width="150px">Action</th>
				  <? } 
				  elseif ($_SESSION['role_id'] == 3) { //Distributor ?>
					  <th>Comm. Level</th>
					  <th>Dist. Comm</th>
					  <th>Dist. Name</th>
					  <th>Contact Name</th>
					  <th>Company Name</th>
					  <th>Rep. Comm</th>
					  <th>Rep. Name</th>
					  <th>Campaign Paid</th>
					  <th>Dist. Paid</th>
					  <th>Rep. Paid</th>
				  <? } 
				  elseif ($_SESSION['role_id'] == 6) { //Representative ?>
					  <th>Company Name</th>
					  <th>Rep. Comm</th>
					  <th>Rep. Name</th>
					  <th>Campaign Paid</th>
					  <th>Rep. Paid</th>
				  <? } 
				  elseif ($_SESSION['role_id'] == 2) { //Campaign Manager ?>
					  <th>Company Name</th>
					  <th>Campaign Paid</th>
				  <? } ?>
                  
                </tr>
                </thead>
                </table>
				<form id="frmfastpay" name="frmfastpay" data-toggle="validator" method="post" action="">
				<div id="fastpay" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				  <div class="modal-dialog">
					<div class="modal-content">
					  <div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
						<h4 class="modal-title camptitle" id="myModalLabel"></h4> <!-- Modal Heading Campaign Title -->
					  </div>
					  <div class="modal-body">
						<div class="submission">
						<!--
						<div class="form-group col-md-12">
						<label for="emailfrom">Email From<span style="color:#FF0000">*</span></label>
						<input type="textbox" class="form-control" id="emailfrom" name="emailfrom" placeholder="Enter Email From" required>
						<div class="help-block with-errors"></div>
					    </div>-->
						<div class="form-group col-md-6">
						  <div>Campaign Start Date:__________________</div>
						  <div id="forsdate" style="margin-left:60%; margin-top:-8%;"></div>
					    </div> 
					    <div class="form-group col-md-6">
						  <div>Campaign End Date:__________________</div>
						  <div id="foredate" style="margin-left:60%; margin-top:-8%;"></div>
					    </div>
					    <div class="clearfix"></div>
						<div class="form-group col-md-6">
						  <div>Requested Date:_______________________</div>
						  <div id="fordate" style="margin-left:60%; margin-top:-8%;"></div>
					    </div> 
					    <div class="form-group col-md-6">
						  <div>Requested Time:______________________</div>
						  <div id="fortime" style="margin-left:60%; margin-top:-8%;"></div>
					    </div>
					    <div class="clearfix"></div>
					  <div class="form-group col-md-6">
						<label for="emailfname">Campaign Manager First Name<span style="color:#FF0000">*</span></label>
						<input type="text" class="form-control" id="emailfname" name="emailfname" placeholder="Receiver's First Name" required>
						<div class="help-block with-errors"></div>
					  </div> 
					  <div class="form-group col-md-6">
						<label for="emaillname">Campaign Manager Last Name</label>
						<input type="text" class="form-control" id="emaillname" name="emaillname" placeholder="Receiver's Last Name">
						<div class="help-block with-errors"></div>
					  </div>
					  <div class="clearfix"></div>
					  <div class="form-group col-md-6">
						<label for="amountreq">Check Amount<span style="color:#FF0000">*</span></label>
						<input type="text" readonly class="form-control" id="amountreq" name="amountreq" placeholder="Enter Amount">
						<div class="help-block with-errors"></div>
					  </div> 
					  <!--<div class="form-group col-md-6">
						<label for="emailfrom">Check will be emailed to<span style="color:#FF0000">*</span></label>
						<input type="email" class="form-control" data-minlength="6" id="emailto" name="emailto" placeholder="Enter Email" required>
						<div class="help-block with-errors"></div>
					  </div>-->
					  <div class="clearfix"></div>
					  <div class="form-group col-md-12">
						<div>Your donations will be directly deposited into the account listed below.</div>
						<div>The deposit will appear on your statement as <?php echo SITE_DOMAIN;?>.</div>
						<div>Please allow 48 hours for the deposit to be credited to your account.</div>
					  </div>
					  <div class="clearfix"></div>
					  <div class="form-group col-md-6">
						<label for="bankname">Bank Name<span style="color:#FF0000">*</span></label>
						<input type="text" class="form-control" id="bankname" name="bankname" value="" readonly>
						<div class="help-block with-errors"></div>
					  </div> 
					  <div class="form-group col-md-6">
						<label for="accountname">Account Name<span style="color:#FF0000">*</span></label>
						<input type="text" class="form-control" id="accountname" name="accountname" value="" readonly>
						<div class="help-block with-errors"></div>
					  </div> 
					  <div class="clearfix"></div>
					  <div class="form-group col-md-6">
						<label for="routingnumber">Routing Number<span style="color:#FF0000">*</span></label>
						<input type="text" class="form-control" id="routingnumber" name="routingnumber" value="" readonly>
						<div class="help-block with-errors"></div>
					  </div> 
					  <div class="form-group col-md-6">
						<label for="accountnumber">Account Number<span style="color:#FF0000">*</span></label>
						<input type="text" class="form-control" id="accountnumber" name="accountnumber" value="" readonly>
						<div class="help-block with-errors"></div>
					  </div> 
					  <div class="clearfix"></div>
					  <div class="form-group col-md-12">
						<div>Please call <?php echo SUPPORT_NUMBER_4_DISPLAY;?></div>
						<br/>
						<div>Thank You</div>
						<div><?php echo sWEBSITENAME;?></div>
					  </div>
					  <div style="clear:both"></div>
					  </div>
					  <div class="modal-footer">
						<input type="hidden" name="cid" id="cid" >
						<input type="hidden" name="emailfrom" id="emailfrom" >
						<input type="hidden" name="cuid" id="cuid" >
						<input type="hidden" name="ctitle" id="ctitle" >
						<input type="hidden" name="rid" id="rid" >
						<input type="hidden" name="rname" id="rname" >
						<input type="hidden" name="did" id="did" >
						<input type="hidden" name="dname" id="dname" >
						<input type="hidden" name="aid" id="aid" >
						<input type="hidden" name="aname" id="aname" >
						<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
						<button type="submit" name="fastpaysubmit" id="fastpaysubmit" class="btn btn-success waves-effect waves-light">Send Request <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
					  </div>
					  </div>
					</div>
					<!-- /.modal-content -->
				  </div>
				<!-- /.modal-dialog -->
				</div>
				</form>
			 <? 
			 //Administrator
			 if ($_SESSION['role_id'] == 1) 
			{  ?>
				<!-- Payment Center -->
				<form id="frmpaymentcenter" name="frmfastpay" data-toggle="validator" method="post" action="">
				<div id="paymentcenter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				  <div class="modal-dialog">
					<div class="modal-content">
					  <div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
						<h4 class="modal-title camptitle" id="myModalLabel"></h4> <!-- Modal Heading Campaign Title -->
					  </div>
					  <div class="modal-body">
						<div class="submission">
						<!--
						<div class="form-group col-md-12">
						<label for="emailfrom">Email From<span style="color:#FF0000">*</span></label>
						<input type="textbox" class="form-control" id="emailfrom" name="emailfrom" placeholder="Enter Email From" required>
						<div class="help-block with-errors"></div>
					    </div>-->
						<div class="form-group col-md-6">
						  <label for="pc_moneyraised">Money Raised</label>
						  <input readonly class="form-control" id="pc_moneyraised" name="pc_moneyraised" placeholder="Money Raised">
					    </div> 
						<div class="form-group col-md-6">
						  <label for="pc_ufundamt"><?php echo sWEBSITENAME;?> $</label>
						  <input readonly class="form-control" id="pc_ufundamt" name="pc_ufundamt" placeholder="<?php echo sWEBSITENAME;?> $">
					    </div> 
						<div class="clearfix"></div>
						<div class="form-group col-md-4">
						  <label for="pc_cprofitraised">Campaign Profit Raised</label>
						  <input readonly class="form-control" id="pc_cprofitraised" name="pc_cprofitraised" placeholder="Campaign Profit Raised">
						  <br>
						  <label for="pc_cfirstpayment">1st Payment</label>
						  <input type="text" class="form-control" id="pc_cfirstpayment" name="pc_cfirstpayment" placeholder="1st Payment">
						  <br>
						  <label for="pc_cfirstpaiddate">Date Paid</label>
						  <input type="text" class="form-control" id="pc_cfirstpaiddate" name="pc_cfirstpaiddate" placeholder="Date Paid">
						  <br>
						  <label for="pc_cfirstchecknumber">Check #</label>
						  <input type="text" class="form-control" id="pc_cfirstchecknumber" name="pc_cfirstchecknumber" placeholder="Check #">
						  <br>
						  <label for="pc_csecondpayment">2nd Payment</label>
						  <input type="text" class="form-control" id="pc_csecondpayment" name="pc_csecondpayment" placeholder="2nd Payment">
						  <br>
						  <label for="pc_csecondpaiddate">Date Paid</label>
						  <input type="text" class="form-control" id="pc_csecondpaiddate"  name="pc_csecondpaiddate" placeholder="Date Paid">
						  <br>
						  <label for="pc_csecondchecknumber">Check #</label>
						  <input type="text" class="form-control" id="pc_csecondchecknumber" name="pc_csecondchecknumber" placeholder="Check #">
					    </div> 
					    <div class="form-group col-md-4">
						  <label for="pc_dflname">Dist. First & Last</label>
						  <input type="text" class="form-control" id="pc_dflname" name="pc_dflname" placeholder="Dist. First & Last Name">
						  <br>
						  <label for="pc_dpercentage">Dist. Percentage</label>
						  <input type="text" class="form-control" id="pc_dpercentage" name="pc_dpercentage" placeholder="Dist. Percentage">
						  <br>
						  <label for="pc_dpayment">$ Paid</label>
						  <input type="text" class="form-control" id="pc_dpayment" name="pc_dpayment" placeholder="Dist. Payment">
						  <br>
						  <label for="pc_dpaiddate">Date Paid</label>
						  <input type="text" class="form-control" id="pc_dpaiddate" name="pc_dpaiddate" placeholder="Date Paid">
						  <br>
						  <label for="pc_dchecknumber">Check #</label>
						  <input type="text" class="form-control" id="pc_dchecknumber" name="pc_dchecknumber" placeholder="Check #">
					    </div>
						<div class="form-group col-md-4">
						  <label for="pc_rflname">Rep. First & Last</label>
						  <input type="text" class="form-control" id="pc_rflname" name="pc_rflname" placeholder="Rep. First & Last Name">
						  <br>
						  <label for="pc_rpercentage">Rep. Percentage</label>
						  <input type="text" class="form-control" id="pc_rpercentage" name="pc_rpercentage" placeholder="Rep. Percentage">
						  <br>
						  <label for="pc_rpayment">$ Paid</label>
						  <input type="text" class="form-control" id="pc_rpayment" name="pc_rpayment" placeholder="Rep. Payment">
						  <br>
						  <label for="pc_rpaiddate">Date Paid</label>
						  <input type="text" class="form-control" id="pc_rpaiddate"  name="pc_rpaiddate" placeholder="Date Paid">
						  <br>
						  <label for="pc_rchecknumber">Check #</label>
						  <input type="text" class="form-control" id="pc_rchecknumber" name="pc_rchecknumber" placeholder="Check #">
					    </div>
					    <div class="clearfix"></div>
						
					  <div class="clearfix"></div>
					  <div style="clear:both"></div>
					  </div>
					  <div class="modal-footer">
						<input type="hidden" name="cid" id="cid" class="cid" >
						<input type="hidden" name="cuid" id="cuid" class="cuid" >
						<input type="hidden" name="ctitle" id="ctitle" class="ctitle" >
						<input type="hidden" name="rid" id="rid" class="rid" >
						<input type="hidden" name="rname" id="rname" class="rname" >
						<input type="hidden" name="did" id="did" class="did" >
						<input type="hidden" name="dname" id="dname" class="dname" >
						<input type="hidden" name="aid" id="aid" class="aid" >
						<input type="hidden" name="aname" id="aname" class="aname" >
						<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
						<button type="submit" name="paymentcsubmit" id="paymentcsubmit" class="btn btn-success waves-effect waves-light">Save <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
					  </div>
					  </div>
					</div>
					<!-- /.modal-content -->
				  </div>
				<!-- /.modal-dialog -->
				</div>
				</form>
				<!-- Payment Center -->
			    <? 
			 } ?>
				<div class="formodal"></div>
			</div>
        </div>
      </div>
    </div>
    <!-- /.container-fluid -->
  </div>
  <!-- /#page-wrapper -->
	<!-- #footer -->
    <? include_once('footer.php');?>
	<!-- /#footer -->
</div>
<!-- /#wrapper -->
<!-- jQuery -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Menu Plugin JavaScript -->
<script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
<!--Nice scroll JavaScript -->
<script src="js/jquery.nicescroll.js"></script>
<script src="bower_components/datatables/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.3/js/buttons.colVis.min.js"></script>
<script>
    $(document).ready(function(){
      var table = $('#example1').DataTable({ 
		 //dom: 'Bfrtip',
		 dom: 'Blfrtip',
		 buttons: [
			{
				extend: 'copyHtml5',
				text: 'Copy',
				className: 'fixed'
			},
			{
				extend: 'excelHtml5',
				text: 'To Excel',
				className: 'fixed'
			},
			{
				extend: 'csvHtml5',
				text: 'To CSV',
				className: 'fixed'
			},
			{
				extend: 'pdfHtml5',
				text: 'To PDF',
				className: 'fixed'
			}
		 ],
		 "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		 "paging": true,
		 "searching": { "regex": true },
		 "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
		 "pageLength": 10,
		 "bProcessing": true,
		 "bServerSide": true,
		 "sAjaxSource": "manage_campaign_ajax.php?mode=<?=$mode;?>&uid=<?=$_SESSION['uid'];?>&rid=<?=$_SESSION['role_id'];?>",
		 // "scrollY": '50vh',
		 // "scrollX": true,
		  "autoWidth": false,
		 language: {
			processing: "<img src='images/loading-spinner-blue.gif'> Loading...",
		 },
		 "columnDefs": [
				 <? if ($mode == 'extended') 
				{ ?>
					{ "width": "60px", "targets": 0 },   //Status
					{ "width": "100px", "targets": 1 },   //Campaign #
					{ "width": "100px", "targets": 2 },   //Campaign ID
					{ "width": "250px", "targets": 3 },  //Campaign Name
					{ "width": "100px", "targets": 4 },   //Start Date
					{ "width": "100px", "targets": 5 },   //End Date
					{ "width": "100px", "targets": 6 },  //# of Days Left
					{ "width": "100px", "targets": 7 },  //# of Participants
					{ "width": "120px", "targets": 8 },  //# of Participants Enrolled
					{ "width": "125px", "targets": 9 }, //Total # of donors
					{ "width": "125px", "targets": 10 }, //# of Projected Donors Uploaded
					{ "width": "125px", "targets": 11 }, //# Of Phone# Uploaded
					{ "width": "100px", "targets": 12 },  //% of Donors Required
					{ "width": "100px", "targets": 13 },  //Undeliverable Emails (Bad Emails)
					{ "width": "100px", "targets": 14 },  //Unsubscribe Donors
					{ "width": "100px", "targets": 15 }, //# of Donation Received
					{ "width": "130px", "targets": 16 }, //Average Donation Amount
					{ "width": "100px", "targets": 17 },  //Profit Raised
					{ "width": "100px", "targets": 18 },  //Campaign Goal
					{ "width": "100px", "targets": 19 },  //% of Goal
					{ "width": "100px", "targets": 20 },  //Money Raised
					{ "width": "100px", "targets": 21 }, //FastPay Amount
					{ "width": "100px", "targets": 22 },  //Money Withdrawn
					{ "width": "100px", "targets": 23 },  //Remaining Profit to be Paid
					<? if ($_SESSION['role_id'] == 1) 
					{ //Administrator ?>
						{ "width": "100px", "targets": 24 },  //Comm. Level
						{ "width": "100px", "targets": 25 },  //Dist. Comm.
						{ "width": "100px", "targets": 26 },  //Dist. Name.
						{ "width": "100px", "targets": 27 },  //Contact Name.
						{ "width": "100px", "targets": 28 },  //Company Name.
						{ "width": "100px", "targets": 29 },  //Rep. Comm.
						{ "width": "100px", "targets": 30 },  //Rep. Name.
						{ "width": "100px", "targets": 31 },  //UFund Comm.
						{ "width": "100px", "targets": 32 },  //Campaign Paid
						{ "width": "100px", "targets": 33 },  //Dist. Paid
						{ "width": "100px", "targets": 34 },  //Rep. Paid
						{ "width": "150px", "targets": 35 },  //Account ID
						{ "width": "100px", "targets": 36 }   //Action	
					<? } 
					elseif ($_SESSION['role_id'] == 3) { //Distributor ?>
						{ "width": "100px", "targets": 24 },  //Comm. Level
						{ "width": "100px", "targets": 25 },  //Dist. Comm.
						{ "width": "100px", "targets": 26 },  //Dist. Name.
						{ "width": "100px", "targets": 27 },  //Contact Name.
						{ "width": "100px", "targets": 28 },  //Company Name.
						{ "width": "100px", "targets": 29 },  //Rep. Comm.
						{ "width": "100px", "targets": 30 },  //Rep. Name.
						{ "width": "100px", "targets": 31 },  //Campaign Paid
						{ "width": "100px", "targets": 32 },  //Dist. Paid
						{ "width": "100px", "targets": 33 },  //Rep. Paid
						// { "width": "100px", "targets": 34 }   //Action	
					<? } 
					elseif ($_SESSION['role_id'] == 6) { //Representative ?>
						{ "width": "100px", "targets": 24 },  //Company Name.
						{ "width": "100px", "targets": 25 },  //Rep. Comm.
						{ "width": "100px", "targets": 26 },  //Rep. Name.
						{ "width": "100px", "targets": 27 },  //Campaign Paid
						{ "width": "100px", "targets": 28 },  //Rep. Paid
						// { "width": "100px", "targets": 29 }   //Action	
					<? } 
					elseif ($_SESSION['role_id'] == 2) { //Campaign Manager ?>
						{ "width": "100px", "targets": 24 },  //Company Name.
						{ "width": "100px", "targets": 25 },  //Campaign Paid
						// { "width": "100px", "targets": 26 }   //Action	
					<? 
					}  
				} //$mode == 'extended' if end
				else { ?>
					{ "width": "60px", "targets": 0 },   //Status
					{ "width": "100px", "targets": 1 },   //Campaign #
					{ "width": "100px", "targets": 2 },   //Campaign ID
					{ "width": "150px", "targets": 3 },  //Campaign Name
					{ "width": "150px", "targets": 4 },   //Start Date
					{ "width": "100px", "targets": 5 },   //End Date
					{ "width": "100px", "targets": 6 },  //# of Days Left
					{ "width": "100px", "targets": 7 },  //# of Participants
					{ "width": "120px", "targets": 8 },  //# of Participants Enrolled
					{ "width": "225px", "targets": 9 }, //Total # of donors
					{ "width": "125px", "targets": 10 }, //# of Projected Donors Uploaded
					{ "width": "225px", "targets": 11 }, //# Of Phone# Uploaded
					{ "width": "225px", "targets": 12 },  //% of Donors Required
					{ "width": "200px", "targets": 13 },  //Unsubscribe Donors
					{ "width": "200px", "targets": 14 }, //# of Donation Received
					{ "width": "200px", "targets": 15 }, //Average Donation Amount
					{ "width": "200px", "targets": 16 },  //Profit Raised
					{ "width": "150px", "targets": 17 },  //Campaign Goal
					{ "width": "150px", "targets": 18 },  //% of Goal
					{ "width": "100px", "targets": 19 },  //Money Raised
					{ "width": "100px", "targets": 20 }, //FastPay Amount
					{ "width": "200px", "targets": 21 },  //Money Withdrawn
					{ "width": "150px", "targets": 22 },  //Remaining Profit to be Paid
					<? if ($_SESSION['role_id'] == 1) 
					{ //Administrator ?>
						{ "width": "200px", "targets": 23 },  //Comm. Level
						{ "width": "100px", "targets": 24 },  //Dist. Comm.
						{ "width": "100px", "targets": 25 },  //Dist. Name.
						{ "width": "150px", "targets": 26 },  //Contact Name.
						{ "width": "150px", "targets": 27 },  //Company Name.
						{ "width": "150px", "targets": 28 },  //Rep. Comm.
						{ "width": "150px", "targets": 29 },  //Rep. Name.
						{ "width": "100px", "targets": 30 },  //UFund Comm.
						{ "width": "100px", "targets": 31 },  //Campaign Paid
						{ "width": "150px", "targets": 32 },  //Dist. Paid
						{ "width": "100px", "targets": 33 },  //Rep. Paid
						{ "width": "150px", "targets": 34 },  //Account ID
						{ "width": "100px", "targets": 35 }   //Action	
					<? } 
					elseif ($_SESSION['role_id'] == 3) { //Distributor ?>
						{ "width": "100px", "targets": 23 },  //Comm. Level
						{ "width": "100px", "targets": 24 },  //Dist. Comm.
						{ "width": "100px", "targets": 25 },  //Dist. Name.
						{ "width": "100px", "targets": 26 },  //Contact Name.
						{ "width": "100px", "targets": 27 },  //Company Name.
						{ "width": "100px", "targets": 28 },  //Rep. Comm.
						{ "width": "100px", "targets": 29 },  //Rep. Name.
						{ "width": "100px", "targets": 30 },  //Campaign Paid
						{ "width": "100px", "targets": 31 },  //Dist. Paid
						{ "width": "100px", "targets": 32 },  //Rep. Paid
						// { "width": "100px", "targets": 33 }   //Action	
					<? } 
					elseif ($_SESSION['role_id'] == 6) { //Representative ?>
						{ "width": "100px", "targets": 23 },  //Company Name.
						{ "width": "100px", "targets": 24 },  //Rep. Comm.
						{ "width": "100px", "targets": 25 },  //Rep. Name.
						{ "width": "100px", "targets": 26 },  //Campaign Paid
						{ "width": "100px", "targets": 27 },  //Rep. Paid
						// { "width": "100px", "targets": 28 }   //Action	
					<? } 
					elseif ($_SESSION['role_id'] == 2) { //Campaign Manager ?>
						{ "width": "100px", "targets": 23 },  //Company Name.
						{ "width": "100px", "targets": 24 },  //Campaign Paid
						// { "width": "100px", "targets": 25 }   //Action	
					<? } ?>
				<? }//end of else of mode ?>
		 ],//end of column
		"order": [[ 1, "desc" ]],
	  });
	  $('#example1 tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
		$('#button').click( function () {
			table.row('.selected').remove().draw( false );
		});
      });
	  
      $(document).ready(function() {
        var table = $('#example').DataTable({
          "columnDefs": [
          { "visible": false, "targets": 2 }
          ],
          "order": [[ 2, 'asc' ]],
          "displayLength": 25,
          "drawCallback": function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;

            api.column(2, {page:'current'} ).data().each( function ( group, i ) {
              if ( last !== group ) {
                $(rows).eq( i ).before(
                  '<tr class="group"><td colspan="5">'+group+'</td></tr>'
                  );

                last = group;
              }
            } );
          }
        } );

    // Order by the grouping
    $('#example tbody').on( 'click', 'tr.group', function () {
      var currentOrder = table.order()[0];
      if ( currentOrder[0] === 2 && currentOrder[1] === 'asc' ) {
        table.order( [ 2, 'desc' ] ).draw();
      }
      else {
        table.order( [ 2, 'asc' ] ).draw();
      }
    } );
  } );
    });
	setTimeout(function(){ 
		//$('<div class="newuser_btn" style="width:152px !important;float:right; margin:-10px 0 0 20px "><a href="start_campaign.php"><button class="btn btn-block btn-primary" style="width:150px; margin-top:10px;"><span class="fa fa-plus"></span> <span class="newtext">New Campaign</span></button></a></div><div align="right"><a style="float:right;" href="start_campaign.php?mode=extended"><button class="btn btn-block btn-primary" style="width:150px; margin-top:10px;"><span class="newtext">Extended</span></button></a><a style="float:right;margin-right:6px" href="start_campaign.php?mode=compact"><button class="btn btn-block btn-primary" style="width:150px; margin-top:10px;"><span class="newtext">Compact</span></button></a></div>').appendTo('div.dataTables_filter');
		$('<div class="newuser_btn" style="float:right; margin:-10px 0 0 6px "><a href="?mode=extended"><button class="btn btn-block btn-primary" style="width:120px; margin-top:10px;"><span class="newtext">Extended</span></button></a></div><div class="newuser_btn" style="float:right; margin:-10px 0 0 6px "><a href="?mode=compact"><button class="btn btn-block btn-primary" style="width:120px; margin-top:10px;"><span class="newtext">Compact</span></button></a></div><div class="newuser_btn" style="float:right; margin:-10px 0 0 6px "><a href="start_campaign.php"><button class="btn btn-block btn-primary" style="width:150px; margin-top:10px;"><span class="fa fa-plus"></span> <span class="newtext">New Campaign</span></button></a></div>').appendTo('div.dataTables_filter');
		$('#example1').on('click', '.camphistory', function(){
			var cid = $(this).attr('cid');
			var ctitle = $(this).attr('ctitle');
				$('.preloader_datatable').show(); 
				$.post('paymenthistory.php', 'cid=' + cid + '&act=1', function (response) {
				var jdata1 = JSON.parse(response);
				var camphistory = '<div class="modal fade camphistory1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
				    camphistory += '<div class="modal-dialog modal-lg">';
				  	  camphistory += '<div class="modal-content">';
					    camphistory += '<div class="modal-header">';
						  camphistory += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>';
						  camphistory += '<h4 class="modal-title" id="myModalLabel">'+ctitle+'</h4>';
					    camphistory += '</div>';
					    camphistory += '<div class="modal-body">';
						  camphistory += '<table id="example2" class="table table-bordered table-striped">';
						    camphistory += '<thead>';
							  camphistory += '<tr>';
							  	camphistory += '<th style="font-size:13px !important; text-align:left;">S/No</th>';
							    camphistory += '<th style="font-size:13px !important; text-align:left;">Check #</th>';
							    camphistory += '<th style="font-size:13px !important; text-align:left;">Payment</th>';
							    camphistory += '<th style="font-size:13px !important; text-align:left;">Date</th>';
							    camphistory += '<th style="font-size:13px !important; text-align:left;">Profit Raised</th>';
							  camphistory += '</tr>';
						    camphistory += '</thead>';
						    camphistory += '<tbody>';
						    if (jdata1.counter > 0) {
						      var jdata = jdata1.array_record[0];
						      if (jdata.cfirstcheckno != '' && jdata.cfirstpayment > 0)
							  {
							  camphistory += '<tr>';
							  camphistory += '<td style="font-size:13px !important; text-align:left;">1.</td>';
							  camphistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.cfirstcheckno+'</td>';
							  camphistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.cfirstpayment+'</td>';
							  camphistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.cfirstpaiddate+'</td>';
							  camphistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.cprofitraised+'</td>';
							  camphistory += '</tr>';
							  }
							  if (jdata.csecondcheckno != '' && jdata.csecondpayment > 0)
							  {
							  camphistory += '<tr>';
							  camphistory += '<td style="font-size:13px !important; text-align:left;">2.</td>';
							  camphistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.csecondcheckno+'</td>';
							  camphistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.csecondpayment+'</td>';
							  camphistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.csecondpaiddate+'</td>';
							  camphistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.cprofitraised+'</td>';
							  camphistory += '</tr>';
							  }
							}
							camphistory += '</tbody>';
						  camphistory += '</table>';
					    camphistory += '<div class="modal-footer">';
						  camphistory += '<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
					    camphistory += '</div>';
					    camphistory += '</div>';
					  camphistory += '</div>';
				    camphistory += '</div>';
				  camphistory += '</div>';
				  $('.formodal').empty();
				  $('.formodal').append(camphistory);
				  $(".camphistory1").modal('show');
				  if (jdata1.counter > 0) {
				  $('#example2').DataTable({
					"scrollY": '50vh',
					"scrollX": true,
					"autoWidth": false,
					"columnDefs": [
						{ "width": "241px", "targets": 0 },   //Distributor Name
						{ "width": "123px", "targets": 1 },   //Check #
						{ "width": "145px", "targets": 2 },   //Payment
						{ "width": "137px", "targets": 3 },   //Date
						{ "width": "136px", "targets": 4 },   //Comm. Rate
					],
				  });
				  } else {
				  	$('#example2').DataTable();
				  }
				  $('.preloader_datatable').hide(); 
			});
		});
		$('#example1').on('click', '.disthistory', function(){
			var cid = $(this).attr('cid');
			var ctitle = $(this).attr('ctitle');
			$('.preloader_datatable').show(); 
			$.post('paymenthistory.php', 'cid=' + cid + '&act=1', function (response) {
				var jdata1 = JSON.parse(response);
				var disthistory = '<div class="modal fade disthistory1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
				    disthistory += '<div class="modal-dialog modal-lg">';
				  	  disthistory += '<div class="modal-content">';
					    disthistory += '<div class="modal-header">';
						  disthistory += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>';
						  disthistory += '<h4 class="modal-title" id="myModalLabel">'+ctitle+'</h4>';
					    disthistory += '</div>';
					    disthistory += '<div class="modal-body">';
						  disthistory += '<table id="example2" class="table table-bordered table-striped">';
						    disthistory += '<thead>';
							  disthistory += '<tr>';
							    disthistory += '<th style="font-size:13px !important; text-align:left;">Distributor Name</th>';
							    disthistory += '<th style="font-size:13px !important; text-align:left;">Check #</th>';
							    disthistory += '<th style="font-size:13px !important; text-align:left;">Payment</th>';
							    disthistory += '<th style="font-size:13px !important; text-align:left;">Date</th>';
							    disthistory += '<th style="font-size:13px !important; text-align:left;">Comm. Rate</th>';
							  disthistory += '</tr>';
						    disthistory += '</thead>';
						    disthistory += '<tbody>';
						    if (jdata1.counter > 0) {
						      var jdata = jdata1.array_record[0];
						      if (jdata.dcheckno != '' && jdata.dpayment > 0)
						      {
							  disthistory += '<tr>';
							  disthistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.dflname+'</td>';
							  disthistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.dcheckno+'</td>';
							  disthistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.dpayment+'</td>';
							  disthistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.dpaiddate+'</td>';
							  disthistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.desper+'</td>';
							  disthistory += '</tr>';
							  }
							} 
						    disthistory += '</tbody>';
						  disthistory += '</table>';
					    disthistory += '<div class="modal-footer">';
						  disthistory += '<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
					    disthistory += '</div>';
					    disthistory += '</div>';
					  disthistory += '</div>';
				    disthistory += '</div>';
				  disthistory += '</div>';
				  $('.formodal').empty();
				  $('.formodal').append(disthistory);
				  $(".disthistory1").modal('show');
				  if (jdata1.counter > 0) {
				  $('#example2').DataTable({
					"scrollY": '50vh',
					"scrollX": true,
					"autoWidth": false,
					"columnDefs": [
						{ "width": "241px", "targets": 0 },   //Distributor Name
						{ "width": "123px", "targets": 1 },   //Check #
						{ "width": "145px", "targets": 2 },   //Payment
						{ "width": "137px", "targets": 3 },   //Date
						{ "width": "136px", "targets": 4 },   //Comm. Rate
					],
				  });
				  } else {
				  	$('#example2').DataTable({
					
				  	});
				  }
				  $('.preloader_datatable').hide(); 
			});
		});
		$('#example1').on('click', '.rephistory', function(){
			var cid = $(this).attr('cid');
			var ctitle = $(this).attr('ctitle');
				$('.preloader_datatable').show(); 
				$.post('paymenthistory.php', 'cid=' + cid + '&act=1', function (response) {
				var jdata1 = JSON.parse(response);
				var rephistory = '<div class="modal fade rephistory1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
				    rephistory += '<div class="modal-dialog modal-lg">';
				  	  rephistory += '<div class="modal-content">';
					    rephistory += '<div class="modal-header">';
						  rephistory += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>';
						  rephistory += '<h4 class="modal-title" id="myModalLabel">'+ctitle+'</h4>';
					    rephistory += '</div>';
					    rephistory += '<div class="modal-body">';
						  rephistory += '<table id="example2" class="table table-bordered table-striped">';
						    rephistory += '<thead>';
							  rephistory += '<tr>';
							    rephistory += '<th style="font-size:13px !important; text-align:left;">Representative Name</th>';
							    rephistory += '<th style="font-size:13px !important; text-align:left;">Check #</th>';
							    rephistory += '<th style="font-size:13px !important; text-align:left;">Payment</th>';
							    rephistory += '<th style="font-size:13px !important; text-align:left;">Date</th>';
							    rephistory += '<th style="font-size:13px !important; text-align:left;">Comm. Rate</th>';
							  rephistory += '</tr>';
						    rephistory += '</thead>';
						    rephistory += '<tbody>';
						    if (jdata1.counter > 0) {
						      var jdata = jdata1.array_record[0];
						      if (jdata.rcheckno != '' && jdata.rpayment > 0)
						      {
							  rephistory += '<tr>';
							  rephistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.rflname+'</td>';
							  rephistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.rcheckno+'</td>';
							  rephistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.rpayment+'</td>';
							  rephistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.rpaiddate+'</td>';
							  rephistory += '<td style="font-size:13px !important; text-align:left;">'+jdata.repper+'</td>';
							  rephistory += '</tr>';
							  }
							}
							rephistory += '</tbody>';
						  rephistory += '</table>';
					    rephistory += '<div class="modal-footer">';
						  rephistory += '<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
					    rephistory += '</div>';
					    rephistory += '</div>';
					  rephistory += '</div>';
				    rephistory += '</div>';
				  rephistory += '</div>';
				  $('.formodal').empty();
				  $('.formodal').append(rephistory);
				  $(".rephistory1").modal('show');
				  if (jdata1.counter > 0) {
				  $('#example2').DataTable({
					"scrollY": '50vh',
					"scrollX": true,
					"autoWidth": false,
					"columnDefs": [
						{ "width": "241px", "targets": 0 },   //Distributor Name
						{ "width": "123px", "targets": 1 },   //Check #
						{ "width": "145px", "targets": 2 },   //Payment
						{ "width": "137px", "targets": 3 },   //Date
						{ "width": "136px", "targets": 4 },   //Comm. Rate
					],
				  });
				  } else {
				  	$('#example2').DataTable();
				  }
				  $('.preloader_datatable').hide(); 
			});
		});
		$('#example1').on('click', '.paymentcenterclick', function(){
			$('.preloader_datatable').show(); 
			var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth()+1; //January is 0!
				var yyyy = today.getFullYear();
				if(dd<10){
					dd='0'+dd
				} 
				if(mm<10){
					mm='0'+mm
				} 
			var today = dd+'/'+mm+'/'+yyyy;
			var campprofitraised = $(this).attr('campprofitraised');
			var moneyraised = $(this).attr('moneyraised');
			var ufund4us = $(this).attr('ufund4us');
			var distper = $(this).attr('distper');
			var repper = $(this).attr('repper');
			var camptitle = $(this).attr('camptitle');
			var cid = $(this).attr('cid');
			var cuid = $(this).attr('cuid');
			var rname = $(this).attr('rname');
			var rid = $(this).attr('rid');
			var dname = $(this).attr('dname');
			var did = $(this).attr('did');
			var aname = $(this).attr('aname');
			var aid = $(this).attr('aid');
			var cfirstpayment = $(this).attr('cfirstpayment');
			var cfirstpaiddate1 = $(this).attr('cfirstpaiddate');
			if (cfirstpaiddate1 != '') {
				var cfirstpaiddate = cfirstpaiddate1;
			} else {
				var cfirstpaiddate = today;
			}
			var cfirstcheckno = $(this).attr('cfirstcheckno');
			var csecondpayment = $(this).attr('csecondpayment');
			var csecondpaiddate1 = $(this).attr('csecondpaiddate');
			if (csecondpaiddate1 != '') {
				var csecondpaiddate = csecondpaiddate1;
			} else {
				var csecondpaiddate = today;
			}
			var csecondcheckno = $(this).attr('csecondcheckno');
			var rpayment = $(this).attr('rpayment');
			var rpaiddate1 = $(this).attr('rpaiddate');
			if (rpaiddate1 != '') {
				var rpaiddate = rpaiddate1;
			} else {
				var rpaiddate = today;
			}
			var rcheckno = $(this).attr('rcheckno');
			var dpayment = $(this).attr('dpayment');
			var dpaiddate1 = $(this).attr('dpaiddate');
			//alert(dpaiddate1);
			if (dpaiddate1 != '') {
				var dpaiddate = dpaiddate1;
			} else {
				var dpaiddate = today;
			}
			var dcheckno = $(this).attr('dcheckno');
			
			$(".camptitle").html(''+camptitle+'');
			//alert (cid);
			$(".cid").val(''+cid+'');
			$(".cuid").val(''+cuid+'');
			$(".ctitle").val(''+camptitle+'');
			
			$(".rid").val(''+rid+'');
			$(".rname").val(''+rname+'');
			$(".did").val(''+did+'');
			$(".dname").val(''+dname+'');
			$(".aid").val(''+aid+'');
			$(".aname").val(''+aname+'');
			
			$("#pc_moneyraised").val(''+moneyraised+'');
			$("#pc_ufundamt").val(''+ufund4us+'');
			
			$("#pc_cprofitraised").val(''+campprofitraised+'');
			$("#pc_cfirstpayment").val(''+cfirstpayment+'');
			$("#pc_cfirstpaiddate").val(''+cfirstpaiddate+'');
			$("#pc_cfirstchecknumber").val(''+cfirstcheckno+'');
			$("#pc_csecondpayment").val(''+csecondpayment+'');
			$("#pc_csecondpaiddate").val(''+csecondpaiddate+'');
			$("#pc_csecondchecknumber").val(''+csecondcheckno+'');
			var distpercent = '';
			var reppercent = '';
			if (did != '' && rid != '') {
				distpercent = Math.round((distper / moneyraised) * 100);
				reppercent = Math.round((repper / moneyraised) * 100);
			} else if (rid != '') {
				distpercent = Math.round((distper / moneyraised) * 100);
				reppercent = Math.round((repper / moneyraised) * 100);
			} else if (did != '') {
				distpercent = Math.round((distper / moneyraised) * 100);
				reppercent = Math.round((repper / moneyraised) * 100);
			} else {
				distpercent = 0;
				reppercent = 0;
			}
			
			$("#pc_dflname").val(''+dname+'');
			$("#pc_dpercentage").val(''+distpercent+'');
			if (distpercent == 0) {
				$("#pc_dpercentage").attr('readonly', 'readonly');
			}
			$("#pc_dpayment").val(''+dpayment+'');
			$("#pc_dpaiddate").val(''+dpaiddate+'');
			$("#pc_dchecknumber").val(''+dcheckno+'');
			
			$("#pc_rflname").val(''+rname+'');
			$("#pc_rpercentage").val(''+reppercent+'');
			if (reppercent == 0) {
				$("#pc_rpercentage").attr('readonly', 'readonly');
			}
			$("#pc_rpayment").val(''+rpayment+'');
			$("#pc_rpaiddate").val(''+rpaiddate+'');
			$("#pc_rchecknumber").val(''+rcheckno+'');
			
			$("#paymentcenter").modal('show');
			$('.preloader_datatable').hide(); 
			
		});
		
		$('#example1').on('click', '.fastpayclick', function(){
			$('.preloader_datatable').show(); 
			if ($(this).hasClass("disabled")) {
				
			} else {
				$("#emailfrom").val('');
				$("#emailto").val('');
				$("#emailto2").val('');
				$("#emailfname").val('');
				$("#emaillname").val('');
				$("#cid").val('');
				$("#cuid").val('');
				$("#ctitle").val('');
				$("#amountreq").val('');
				$("#rid").val('');
				$("#rname").val('');
				$("#did").val('');
				$("#dname").val('');
				$("#aid").val('');
				$("#aname").val('');
				$("#bankname").val('');
				$("#accountname").val('');
				$("#routingnumber").val('');
				$("#accountnumber").val('');
				$("#forsdate").html('');
				$("#foredate").html('');
				$("#fordate").html('');
				$("#fortime").html('');
				function addCommas(nStr)
				{
					nStr += '';
					x = nStr.split('.');
					x1 = x[0];
					x2 = x.length > 1 ? '.' + x[1] : '';
					var rgx = /(\d+)(\d{3})/;
					while (rgx.test(x1)) {
						x1 = x1.replace(rgx, '$1' + ',' + '$2');
					}
					return x1 + x2;
				}
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth()+1; //January is 0!
				var yyyy = today.getFullYear();
				if(dd<10){
					dd='0'+dd
				} 
				if(mm<10){
					mm='0'+mm
				} 
				var today = dd+'/'+mm+'/'+yyyy;
				
				var dt = new Date();
				var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
								
				var cid = $(this).attr('cid');
				var cuid = $(this).attr('cuid');
				var camptitle = $(this).attr('camptitle');
				var emailfrom = $(this).attr('emailfrom');
				var fname = $(this).attr('fname');
				var lname = $(this).attr('lname');
				var amountlimit = $(this).attr('amountlimit');
				var sdate = $(this).attr('sdate');
				var edate = $(this).attr('edate');
				var rid = $(this).attr('rid');
				var rname = $(this).attr('rname');
				var did = $(this).attr('did');
				var dname = $(this).attr('dname');
				var aid = $(this).attr('aid');
				var aname = $(this).attr('aname');
				
				var accid = $(this).attr('accid');
				var bankname = '';
				var accountname = '';
				var bankrouting = '';
				var bankaccount = $(this).attr('bankaccount');
				$(".camptitle").html(''+camptitle+'');
				
				
				//alert(accid);
				if (accid != '') {
					$.post('campaignmanagepostbank.php', 'cid=' + cid + '&accid=' + accid + '&act=1', function (response) {
						var jdata = JSON.parse(response);
						bankname = jdata.bankname;
						accountname = jdata.accountname;
						bankrouting = jdata.bankrouting;
						
						$("#emailfrom").val(''+emailfrom+'');
						$("#emailto").val(''+emailfrom+'');
						$("#emailto2").val(''+emailfrom+'');
						$("#emailfname").val(''+fname+'');
						$("#emaillname").val(''+lname+'');
						$("#cid").val(''+cid+'');
						$("#cuid").val(''+cuid+'');
						$("#ctitle").val(''+camptitle+'');
						$("#amountreq").val(''+addCommas(amountlimit)+'');
				
						$("#rid").val(''+rid+'');
						$("#rname").val(''+rname+'');
						$("#did").val(''+did+'');
						$("#dname").val(''+dname+'');
						$("#aid").val(''+aid+'');
						$("#aname").val(''+aname+'');
				
						$("#bankname").val(''+bankname+'');
						$("#accountname").val(''+accountname+'');
						$("#routingnumber").val(''+bankrouting+'');
						$("#accountnumber").val(''+bankaccount+'');
				
						$("#forsdate").html(''+sdate+'');
						$("#foredate").html(''+edate+'');
						$("#fordate").html(''+today+'');
						$("#fortime").html(''+time+'');
						$("#fastpay").modal('show');
						$('.preloader_datatable').hide(); 
					});
				} else {
					$("#emailfrom").val(''+emailfrom+'');
					$("#emailto").val(''+emailfrom+'');
					$("#emailto2").val(''+emailfrom+'');
					$("#emailfname").val(''+fname+'');
					$("#emaillname").val(''+lname+'');
					$("#cid").val(''+cid+'');
					$("#cuid").val(''+cuid+'');
					$("#ctitle").val(''+camptitle+'');
					$("#amountreq").val(''+addCommas(amountlimit)+'');
				
					$("#rid").val(''+rid+'');
					$("#rname").val(''+rname+'');
					$("#did").val(''+did+'');
					$("#dname").val(''+dname+'');
					$("#aid").val(''+aid+'');
					$("#aname").val(''+aname+'');
				
					$("#bankname").val(''+bankname+'');
					$("#accountname").val(''+accountname+'');
					$("#routingnumber").val(''+bankrouting+'');
					$("#accountnumber").val(''+bankaccount+'');
					
					$("#forsdate").html(''+sdate+'');
					$("#foredate").html(''+edate+'');
					$("#fordate").html(''+today+'');
					$("#fortime").html(''+time+'');
					$("#fastpay").modal('show');
					$('.preloader_datatable').hide(); 
				}
			}
		});
		// Donation List
		$('#example1').on('click', '.donationslist', function(){
			var cid = $(this).attr('cid');
			var camptitle = $(this).attr('camptitle');
			//alert('clicked');
			$('.formodal').empty();
			$('.preloader_datatable').show(); 
			$.post('campaignmanagepost.php', 'cid=' + cid + '&act=1', function (response) {
 console.log("response ", response);
			  var jdata = JSON.parse(response);
			  var donationdetail = ''+jdata.donationdetails+'';
			  var row1 = donationdetail.split(",");
			  var donationlist = '<div class="modal fade donationsshow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
				    donationlist += '<div class="modal-dialog modal-lg">';
				  	  donationlist += '<div class="modal-content">';
					    donationlist += '<div class="modal-header">';
						  donationlist += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>';
						  donationlist += '<h4 class="modal-title" id="myModalLabel">'+camptitle+'</h4>';
					    donationlist += '</div>';
					    donationlist += '<div class="modal-body">';
						  donationlist += '<table id="example2" class="table table-bordered table-striped">';
						    donationlist += '<thead>';
							  donationlist += '<tr>';
							    donationlist += '<th style="font-size:13px !important; text-align:left;">Campaign Title</th>';
							    donationlist += '<th style="font-size:13px !important; text-align:left;">Donors Name</th>';
							    donationlist += '<th style="font-size:13px !important; text-align:left;">Donors Email</th>';
							    donationlist += '<th style="font-size:13px !important; text-align:left;">Participant Name</th>';
							    donationlist += '<th style="font-size:13px !important; text-align:left;">Participant Email</th>';
							    donationlist += '<th style="font-size:13px !important; text-align:left;">Donation Amount</th>';
							    donationlist += '<th style="font-size:13px !important; text-align:left;">Transaction ID</th>';
							    donationlist += '<th style="font-size:13px !important; text-align:left;">Transaction #</th>';
							    donationlist += '<th style="font-size:13px !important; text-align:left;">Transaction Date</th>';
							  donationlist += '</tr>';
						    donationlist += '</thead>';
						    donationlist += '<tbody>';
							if (jdata.counter > 0) {
							  for(var i = 0; i < row1.length; i++) {
							  var row2 = row1[i].split("|");
							  donationlist += '<tr>';
							  donationlist += '<td style="font-size:13px !important; text-align:left;">'+row2[0]+'</td>';
							  donationlist += '<td style="font-size:13px !important; text-align:left;">'+row2[1]+' '+row2[2]+'</td>';
							  donationlist += '<td style="font-size:13px !important; text-align:left;">'+row2[3]+'</td>';
							  donationlist += '<td style="font-size:13px !important; text-align:left;">'+row2[4]+' '+row2[5]+'</td>';
							  donationlist += '<td style="font-size:13px !important; text-align:left;">'+row2[6]+'</td>';
							  donationlist += '<td style="font-size:13px !important; text-align:left;">'+row2[7]+'</td>';
							  donationlist += '<td style="font-size:13px !important; text-align:left;">'+row2[8]+'</td>';
							  donationlist += '<td style="font-size:13px !important; text-align:left;">'+row2[10]+'</td>';
							  donationlist += '<td style="font-size:13px !important; text-align:left;">'+row2[9]+'</td>';
							  donationlist += '</tr>';
							  }
							} else {
							  donationlist += '<tr>';
							  donationlist += '<td colspan="8" align="center">No any information yet...!</td>';
							  donationlist += '</tr>';
							}
						    donationlist += '</tbody>';
						  donationlist += '</table>';
					    donationlist += '<div class="modal-footer">';
						  donationlist += '<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
					    donationlist += '</div>';
					    donationlist += '</div>';
					  donationlist += '</div>';
				    donationlist += '</div>';
				  donationlist += '</div>';
				  $('.formodal').append(donationlist);
				  $(".donationsshow").modal('show');
				  $('#example2').DataTable({
					dom: 'Bfrtip',
					buttons: [
						{
							extend: 'copyHtml5',
							text: 'Copy',
							className: 'fixed'
						},
						{
							extend: 'excelHtml5',
							text: 'To Excel',
							className: 'fixed'
						},
						{
							extend: 'csvHtml5',
							text: 'To CSV',
							className: 'fixed'
						},
						{
							extend: 'pdfHtml5',
							text: 'To PDF',
							className: 'fixed'
						}
					],
					"scrollY": '50vh',
					"scrollX": true,
					"autoWidth": false,
					"columnDefs": [
						{ "width": "250px", "targets": 0 },   //Campaign Title
						{ "width": "200px", "targets": 1 },   //Donors Name
						{ "width": "200px", "targets": 2 },   //Donors Email
						{ "width": "200px", "targets": 3 },   //Participant Name
						{ "width": "200px", "targets": 4 },   //Participant Email
						{ "width": "100px", "targets": 5 },    //Donation Amount
						{ "width": "100px", "targets": 6 },   //Transaction ID
						{ "width": "220px", "targets": 7 },   //Transaction #
						{ "width": "150px", "targets": 8 },   //Transaction Date
					],
				  });
				  $('.preloader_datatable').hide(); 
			});
		});
		// #Participant Enrolled
		$('#example1').on('click', '.participantenrolled', function(){
			var cid = $(this).attr('cid');
			var camptitle = $(this).attr('camptitle');
			$('.formodal').empty();
			$('.preloader_datatable').show(); 
			$.post('campaignmanagepost.php', 'cid=' + cid + '&act=3', function (response) {
			  function addCommas(nStr)
			  {
				nStr += '';
				x = nStr.split('.');
				x1 = x[0];
				x2 = x.length > 1 ? '.' + x[1] : '';
				var rgx = /(\d+)(\d{3})/;
				while (rgx.test(x1)) {
				  x1 = x1.replace(rgx, '$1' + ',' + '$2');
				}
				return x1 + x2;
			  }
			  var jdata = JSON.parse(response);
			  var participantenrolleddetail = ''+jdata.participantenrolleddetails+'';
			  var row1 = participantenrolleddetail.split(",");
			  var participantenrolledlist = '<div class="modal fade participantenrolledshow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
				    participantenrolledlist += '<div class="modal-dialog modal-lg">';
				  	  participantenrolledlist += '<div class="modal-content">';
					    participantenrolledlist += '<div class="modal-header">';
						  participantenrolledlist += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>';
						  participantenrolledlist += '<h4 class="modal-title" id="myModalLabel">'+camptitle+'</h4>';
					    participantenrolledlist += '</div>';
					    participantenrolledlist += '<div class="modal-body">';
					    participantenrolledlist += '<div id="participantlist">';
						  participantenrolledlist += '<table id="example2" class="table table-bordered table-striped">';
						    participantenrolledlist += '<thead>';
							  participantenrolledlist += '<tr>';
							    participantenrolledlist += '<th style="font-size:13px !important; text-align:left;">Participant Name</th>';
							    participantenrolledlist += '<th style="font-size:13px !important; text-align:left;">Donors Required</th>';
							    participantenrolledlist += '<th style="font-size:13px !important; text-align:left;">Donors Uploaded</th>';
								participantenrolledlist += '<th style="font-size:13px !important; text-align:left;">Donors Unsubscribed</th>';
							    participantenrolledlist += '<th style="font-size:13px !important; text-align:left;">Bad Emails</th>';
							    participantenrolledlist += '<th style="font-size:13px !important; text-align:left;">Goal</th>';
							    participantenrolledlist += '<th style="font-size:13px !important; text-align:left;">Money Raised</th>';
							    participantenrolledlist += '<th style="font-size:13px !important; text-align:left;">Image Uploaded</th>';
							  participantenrolledlist += '</tr>';
						    participantenrolledlist += '</thead>';
						    participantenrolledlist += '<tbody>';
							if (jdata.counter > 0) {
							  for(var i = 0; i < row1.length; i++) {
							  var row2 = row1[i].split("|");
							  participantenrolledlist += '<tr>';
							  participantenrolledlist += '<td style="font-size:13px !important; text-align:left;">'+row2[0]+'</td>';
							  participantenrolledlist += '<td style="font-size:13px !important; text-align:left;">'+row2[1]+'</td>';
							  participantenrolledlist += '<td style="font-size:13px !important; text-align:left;">'+row2[2]+'</td>';
							  participantenrolledlist += '<td style="font-size:13px !important; text-align:left;">'+row2[10]+'</td>';
							  participantenrolledlist += '<td style="font-size:13px !important; text-align:left;">'+row2[3]+'</td>';
							  participantenrolledlist += '<td style="font-size:13px !important; text-align:left;">'+row2[6]+'</td>';
							  participantenrolledlist += '<td style="font-size:13px !important; text-align:left;">'+addCommas(row2[7])+'</td>';
							  if (row2[8] != '') {
								participantenrolledlist += '<td style="font-size:13px !important; text-align:left;">Yes</td>';
							  } else {
								participantenrolledlist += '<td style="font-size:13px !important; text-align:left;">No</td>';
							  }
							  participantenrolledlist += '</tr>';
							  }
							}
						    participantenrolledlist += '</tbody>';
						  participantenrolledlist += '</table>';
						  participantenrolledlist += '</div>';
						  
					    participantenrolledlist += '<div class="modal-footer">';
						  participantenrolledlist += '<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
					    participantenrolledlist += '</div>';
					    participantenrolledlist += '</div>';
					  participantenrolledlist += '</div>';
				    participantenrolledlist += '</div>';
				  participantenrolledlist += '</div>';
				  $('.formodal').append(participantenrolledlist);
				  $(".participantenrolledshow").modal('show');
				  $('#example2').DataTable({
					dom: 'Bfrtip',
					buttons: [
						{
							extend: 'copyHtml5',
							text: 'Copy',
							className: 'fixed'
						},
						{
							extend: 'excelHtml5',
							text: 'To Excel',
							className: 'fixed'
						},
						{
							extend: 'csvHtml5',
							text: 'To CSV',
							className: 'fixed'
						},
						{
							extend: 'pdfHtml5',
							text: 'To PDF',
							className: 'fixed'
						}
					],
					"scrollY": '50vh',
					"scrollX": true,
					"autoWidth": false,
					"columnDefs": [
						{ "width": "250px", "targets": 0 },   //Participant Name
						{ "width": "100px", "targets": 1 },   //# of Prospect Donors Required
						{ "width": "100px", "targets": 2 },   //# of Prospect Donors Uploaded
						{ "width": "100px", "targets": 3 },   //% of Donors Required
						{ "width": "100px", "targets": 4 },   //% of Donors Required
						{ "width": "100px", "targets": 5 },   //# of Donations Received
						{ "width": "100px", "targets": 6 },    //Average Donation Amount
						{ "width": "100px", "targets": 7 },   //Participant Goal
					],
					"order": [[ 2, "asc" ]]
				  });
				  $('.preloader_datatable').hide(); 
			});
			
		});

		
		// Unsubscribe Donors
		$('#example1').on('click', '.unsubscribeddonors', function(){
			var cid = $(this).attr('cid');
			var camptitle = $(this).attr('camptitle');
			//alert('clicked');
			$('.formodal').empty();
			$('.preloader_datatable').show(); 
			$.post('campaignmanagepost.php', 'cid=' + cid + '&act=4', function (response) {
			  var jdata = JSON.parse(response);
			  var unsubscribeddonordetail = ''+jdata.unsubscribeddonordetails+'';
			  var row1 = unsubscribeddonordetail.split(",");
			  var unsubscribedonorlist = '<div class="modal fade unsubscribedonorshow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
				    unsubscribedonorlist += '<div class="modal-dialog modal-lg">';
				  	  unsubscribedonorlist += '<div class="modal-content">';
					    unsubscribedonorlist += '<div class="modal-header">';
						  unsubscribedonorlist += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>';
						  unsubscribedonorlist += '<h4 class="modal-title" id="myModalLabel">'+camptitle+'</h4>';
					    unsubscribedonorlist += '</div>';
					    unsubscribedonorlist += '<div class="modal-body">';
						  unsubscribedonorlist += '<table id="example2" class="table table-bordered table-striped">';
						    unsubscribedonorlist += '<thead>';
							  unsubscribedonorlist += '<tr>';
							    unsubscribedonorlist += '<th style="font-size:13px !important; text-align:left;">Campaign Title</th>';
							    unsubscribedonorlist += '<th style="font-size:13px !important; text-align:left;">Donors Name</th>';
							    unsubscribedonorlist += '<th style="font-size:13px !important; text-align:left;">Donors Email</th>';
								unsubscribedonorlist += '<th style="font-size:13px !important; text-align:left;">Unsubscribe Date</th>';
							    unsubscribedonorlist += '<th style="font-size:13px !important; text-align:left;">Participant Name</th>';
							    unsubscribedonorlist += '<th style="font-size:13px !important; text-align:left;">Participant Email</th>';
							  unsubscribedonorlist += '</tr>';
						    unsubscribedonorlist += '</thead>';
						    unsubscribedonorlist += '<tbody>';
							if (jdata.counter > 0) {
							  for(var i = 0; i < row1.length; i++) {
							  var row2 = row1[i].split("|");
							  unsubscribedonorlist += '<tr>';
							  unsubscribedonorlist += '<td style="font-size:13px !important; text-align:left;">'+row2[0]+'</td>';
							  unsubscribedonorlist += '<td style="font-size:13px !important; text-align:left;">'+row2[1]+' '+row2[2]+'</td>';
							  unsubscribedonorlist += '<td style="font-size:13px !important; text-align:left;">'+row2[3]+'</td>';
							  unsubscribedonorlist += '<td style="font-size:13px !important; text-align:left;">'+row2[10]+'</td>';
							  unsubscribedonorlist += '<td style="font-size:13px !important; text-align:left;">'+row2[6]+' '+row2[7]+'</td>';
							  unsubscribedonorlist += '<td style="font-size:13px !important; text-align:left;">'+row2[8]+'</td>';
							  unsubscribedonorlist += '</tr>';
							  }
							}
						    unsubscribedonorlist += '</tbody>';
						  unsubscribedonorlist += '</table>';
					    unsubscribedonorlist += '<div class="modal-footer">';
						  unsubscribedonorlist += '<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
					    unsubscribedonorlist += '</div>';
					    unsubscribedonorlist += '</div>';
					  unsubscribedonorlist += '</div>';
				    unsubscribedonorlist += '</div>';
				  unsubscribedonorlist += '</div>';
				  $('.formodal').append(unsubscribedonorlist);
				  $(".unsubscribedonorshow").modal('show');
				  $('#example2').DataTable({
					dom: 'Bfrtip',
					buttons: [
						{
							extend: 'copyHtml5',
							text: 'Copy',
							className: 'fixed'
						},
						{
							extend: 'excelHtml5',
							text: 'To Excel',
							className: 'fixed'
						},
						{
							extend: 'csvHtml5',
							text: 'To CSV',
							className: 'fixed'
						},
						{
							extend: 'pdfHtml5',
							text: 'To PDF',
							className: 'fixed'
						}
					],
					"scrollY": '50vh',
					"scrollX": true,
					"autoWidth": false,
					"columnDefs": [
						{ "width": "250px", "targets": 0 },  //Campaign Title
						{ "width": "200px", "targets": 1 },  //Donors Name
						{ "width": "250px", "targets": 2 },  //Donors Email
						{ "width": "100px", "targets": 3 },  //Participants Name
						{ "width": "200px", "targets": 4 },  //Participants Email
						{ "width": "250px", "targets": 5 },  //Action
					],
				  });
				  $('.preloader_datatable').hide(); 
			});
		});

		// # of Projected Donors Uploaded
		$('#example1').on('click', '.participantdonor', function(){
			var cid = $(this).attr('cid');
			var camptitle = $(this).attr('camptitle');
			//alert('clicked');
			$('.formodal').empty();
			$('.preloader_datatable').show(); 
			$.post('campaignmanagepost.php', 'cid=' + cid + '&act=2', function (response) {
			  var jdata = JSON.parse(response);
			  var participantdonordetail = ''+jdata.participantdonordetails+'';
			  var row1 = participantdonordetail.split(",");
			  var participantdonorlist = '<div class="modal fade participantdonorshow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
				    participantdonorlist += '<div class="modal-dialog modal-lg">';
				  	  participantdonorlist += '<div class="modal-content">';
					    participantdonorlist += '<div class="modal-header">';
						  participantdonorlist += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>';
						  participantdonorlist += '<h4 class="modal-title" id="myModalLabel">'+camptitle+'</h4>';
					    participantdonorlist += '</div>';
					    participantdonorlist += '<div class="modal-body">';
						  participantdonorlist += '<table id="example2" class="table table-bordered table-striped">';
						    participantdonorlist += '<thead>';
							  participantdonorlist += '<tr>';
							    participantdonorlist += '<th style="font-size:13px !important; text-align:left;">Campaign Title</th>';
							    participantdonorlist += '<th style="font-size:13px !important; text-align:left;">Donors Name</th>';
							    participantdonorlist += '<th style="font-size:13px !important; text-align:left;">Donors Email</th>';
							    participantdonorlist += '<th style="font-size:13px !important; text-align:left;">Participant Name</th>';
							    participantdonorlist += '<th style="font-size:13px !important; text-align:left;">Participant Email</th>';
							    participantdonorlist += '<th style="font-size:13px !important; text-align:left;">Actions</th>';
							  participantdonorlist += '</tr>';
						    participantdonorlist += '</thead>';
						    participantdonorlist += '<tbody>';
							if (jdata.counter > 0) {
							  for(var i = 0; i < row1.length; i++) {
							  var row2 = row1[i].split("|");
							  participantdonorlist += '<tr>';
							  participantdonorlist += '<td style="font-size:13px !important; text-align:left;">'+row2[0]+'</td>';
							  participantdonorlist += '<td style="font-size:13px !important; text-align:left;">'+row2[1]+' '+row2[2]+'</td>';
							  participantdonorlist += '<td style="font-size:13px !important; text-align:left;">'+row2[3]+'</td>';
							  participantdonorlist += '<td style="font-size:13px !important; text-align:left;">'+row2[6]+' '+row2[7]+'</td>';
							  participantdonorlist += '<td style="font-size:13px !important; text-align:left;">'+row2[8]+'</td>';
							  if (row2[4] == 1 && row2[5] == 1) {
							  participantdonorlist += '<td style="font-size:13px !important; text-align:left;"><a><span class="fa fa-check-circle" style="background: #9e9e9e;padding: 7px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0; color:green !important;" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-original-title="Donor read the email &amp; visited" title=""></span></a></td>';
							  } else {
							  participantdonorlist += '<td style="font-size:13px !important; text-align:left;"><a></a><span class="fa fa-circle-o" style="background: #9e9e9e;padding: 7px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0; color:white !important;" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-original-title="Donor has not read the email" title=""></span></td>';
							  }
							  participantdonorlist += '</tr>';
							  }
							}
						    participantdonorlist += '</tbody>';
						  participantdonorlist += '</table>';
					    participantdonorlist += '<div class="modal-footer">';
						  participantdonorlist += '<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
					    participantdonorlist += '</div>';
					    participantdonorlist += '</div>';
					  participantdonorlist += '</div>';
				    participantdonorlist += '</div>';
				  participantdonorlist += '</div>';
				  $('.formodal').append(participantdonorlist);
				  $(".participantdonorshow").modal('show');
				  $('#example2').DataTable({
					dom: 'Bfrtip',
					buttons: [
						{
							extend: 'copyHtml5',
							text: 'Copy',
							className: 'fixed'
						},
						{
							extend: 'excelHtml5',
							text: 'To Excel',
							className: 'fixed'
						},
						{
							extend: 'csvHtml5',
							text: 'To CSV',
							className: 'fixed'
						},
						{
							extend: 'pdfHtml5',
							text: 'To PDF',
							className: 'fixed'
						}
					],
					"scrollY": '50vh',
					"scrollX": true,
					"autoWidth": false,
					"columnDefs": [
						{ "width": "250px", "targets": 0 },  //Campaign Title
						{ "width": "200px", "targets": 1 },  //Donors Name
						{ "width": "200px", "targets": 2 },  //Donors Email
						{ "width": "200px", "targets": 3 },  //Participants Name
						{ "width": "200px", "targets": 4 },  //Participants Email
						{ "width": "100px", "targets": 5 },  //Action
					],
				  });
				  $('.preloader_datatable').hide(); 
			});
		});
		$('#pc_rpaiddate, #pc_dpaiddate, #pc_cfirstpaiddate, #pc_csecondpaiddate').datepicker({
		  autoclose: true,
		  todayHighlight: true
		});
	}, 1000);
  </script>
<!--Wave Effects -->
<script src="js/waves.js"></script>
<!-- Custom Theme JavaScript -->
<script src="js/myadmin.js"></script>
<!--Counter js -->

<script src="bower_components/waypoints/lib/jquery.waypoints.js"></script>
<script src="bower_components/counterup/jquery.counterup.min.js"></script>
<!--Sparkline charts js -->
<script src="bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
<script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>
<script src="bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="js/validator.js"></script>
<script type="text/javascript">
   $('#notifications').delay(3000).fadeOut('slow');
</script>
</body>
</html>
<? include_once('bottom.php');?>