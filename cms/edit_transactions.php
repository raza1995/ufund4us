<?
require_once("../configuration/dbconfig.php");


if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else {
	if ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5 || $_SESSION['role_id'] == 6) {
		$oregister->redirect('dashboard.php');
	}
}
$sPageName = '<li><a href="transactions.php">Transactions</a></li> <li>Edit Transactions</li>';
$sTransactionLink = 'style="color:#F3BE00"';
$sLeftMenuTransactions = 'active';
$tid = $_GET['tid'];

if(isset($_GET['m']) && $_GET['m'] == 'edit')
{
	$tid = $_GET['tid'];
	$aUserDetail = $oregister->gettransactionsdetail($tid);
	$m = 'edit';
	$id = $aUserDetail['id'];
	$cid = $aUserDetail['cid'];
	$fld_campaign_title = $aUserDetail['fld_campaign_title'];
	$requested_by_name = $aUserDetail['requested_by_name'];
	$requested_by_role = $aUserDetail['requested_by_role'];
	$fld_role = $aUserDetail['fld_role'];
	$request_amount = $aUserDetail['request_amount'];
	$request_date = $aUserDetail['request_date'];
	$ispaid = $aUserDetail['ispaid'];
	$paid_by_name = $aUserDetail['paid_by_name'];
	$payment_method = $aUserDetail['payment_method'];
	$paid_date = $aUserDetail['paid_date'];
	$bankname = $aUserDetail['bankname'];
	$checkto = $aUserDetail['checkto'];
	$checknumber = $aUserDetail['checknumber'];
	$checkamount = $aUserDetail['checkamount'];
	$transactionno = $aUserDetail['transactionno'];
}

if($_POST['m'] == 'edit' && $_POST['fld_method'] != '')
{
	$tid = $_POST['tid'];
	$paid_by_id = $_SESSION['uid'];
	$paid_by_fname = $_SESSION['uname'];
	$paid_by_lname = $_SESSION['ulname'];
	
	$status = $_POST['fld_status'];
	$method = $_POST['fld_method'];
	$bankname = $_POST['fld_bankname'];
	$checkto = $_POST['fld_checkto'];
	$checknumber = $_POST['fld_checknumber'];
	$checkamount = $_POST['fld_checkamount'];
	$bankname2 = $_POST['fld_bankname2'];
	$transactionnumber = $_POST['fld_transactionnumber'];
	if ($status == '1' || $status == '2' && $method != '') {
	  $oregister->update_transaction($tid,$paid_by_id,$paid_by_fname,$paid_by_lname,$status,$method,$bankname,$checkto,$checknumber,$checkamount,$bankname2,$transactionnumber);
	  $oregister->redirect('transactions.php?msg=4');
	}
	$oregister->redirect('transactions.php');
		
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
<title>Admin<?php echo sWEBSITENAME;?> - Edit Transactions</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
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
		  <!-- .white-box -->
          <div class="white-box " style="    background: rgba(245, 245, 245, 0);    border: 0px solid #d9d6d6;">
		  <h1 class="h1styling">Edit Transactions</h1>
		  <div class="line3"></div>
		  <div class=" full-main">
			
			
		  <form data-toggle="validator"  method="post">
		  <div class="form-group col-sm-12">
			<h3>
			  <div>Campaign Title: <?=$fld_campaign_title;?><div>
			  <div>Campaign ID: <?=str_pad($cid, 10, "0", STR_PAD_LEFT);?><div>
			</h3>
		  </div>
		  <div class="clearfix"></div>
	<div class="form-group col-sm-6">
		<label for="fld_role_id" class="control-label">Type<span style="color:#FF0000">*</span></label>
		<select disabled class="form-control" name="fld_type" id="fld_type">
                  	<option value="Fast Pay Request">Fast Pay Request</option>
        </select>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_name" class="control-label">Requested by Name<span style="color:#FF0000">*</span></label>
		<input readonly type="text" class="form-control" id="fld_name" name="fld_name" value="<?=$requested_by_name;?>">
	</div>
	<div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_lname" class="control-label">Requested by Role</label>
		<select disabled class="form-control" name="fld_role" id="fld_role">
            <option value="<?=$fld_role;?>"><?=$fld_role;?></option>
        </select>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_phone" class="control-label">Requested Amount<span style="color:#FF0000">*</span></label>
		<input readonly type="text" class="form-control" id="fld_amount" name="fld_amount" value="<?=$request_amount;?>">
	</div>
	<div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_status" class="control-label">Status<span style="color:#FF0000">*</span></label>
		<select name="fld_status" id="fld_status" required class="form-control">
			<option value="0" <?php if($ispaid==0){?>selected<?php }?>>Pending</option>
			<option value="1" <?php if($ispaid==1){?>selected<?php }?>>Paid</option>
			<option value="2" <?php if($ispaid==2){?>selected<?php }?>>Rejected</option>
		</select>
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
	
   <div class="col-md-12"><h2 class="page-header" style="color: #868484; font-family: Open Sans; font-size: 24px;margin:0px 0 20px">Payment Details</h2></div>
   
   <div class="form-group col-sm-6">
		<label for="fld_method" class="control-label">Payment Method<span style="color:#FF0000">*</span></label>
		<select name="fld_method" id="fld_method" required class="form-control">
			<option value="" >Please Select</option>
			<option value="Check" <?php if($payment_method == 'Check'){?>selected<?php }?>>Check</option>
			<option value="Online" <?php if($payment_method == 'Online'){?>selected<?php }?>>Online</option>
			<option value="Cash" <?php if($payment_method == 'Cash'){?>selected<?php }?>>Cash</option>
		</select>
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	
	<div class="check" style="display:none;">
	<div class="form-group col-sm-6">
		<label for="fld_bankname" class="control-label">Bank Name</label>
		<input type="text" class="form-control" id="fld_bankname" name="fld_bankname" value="<?=$bankname;?>">
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_checkto" class="control-label">Check To</label>
		<input type="text" class="form-control" id="fld_checkto" name="fld_checkto" value="<?=$checkto;?>">
	</div>
    <div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_checknumber" class="control-label">Check Number</label>
		<input type="text" class="form-control" id="fld_checknumber" name="fld_checknumber" value="<?=$checknumber;?>">
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_checkamount" class="control-label">Check Amount</label>
		<? if ($checkamount > 0) {$checkamt = $checkamount;} else {$checkamt = $request_amount;} ?>
		<input type="text" class="form-control" id="fld_checkamount" name="fld_checkamount" value="<?=$checkamt;?>">
	</div>
	<div class="clearfix"></div>
	</div>
	
	<div class="online" style="display:none;">
	<div class="form-group col-sm-6">
		<label for="fld_bankname2" class="control-label">Bank Name</label>
		<input type="text" class="form-control" id="fld_bankname2" name="fld_bankname2" value="<?=$bankname;?>">
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_transactionnumber" class="control-label">Transaction #</label>
		<input type="text" class="form-control" id="fld_transactionnumber" name="fld_transactionnumber" value="<?=$transactionno;?>">
	</div>
    <div class="clearfix"></div>
	</div>
	
	<div class="clearfix"></div>
   
   <div class="form-group">
		<input type="hidden" name="fld_tid" id="fld_tid" value="<?=$tid?>">
        <input type="hidden" name="m" id="m" value="<?=$m?>">
    	<div class="col-sm-6" align="left">
			<button class="btn btn-primary waves-effect waves-light" type="button" onClick="window.location.href='transactions.php'"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Cancel</button>
		</div>
		
		<div class="col-sm-6" align="right">
			<button class="btn btn-success waves-effect waves-light" style="padding:10px 72px 11px 53px !important;" type="submit">Submit<span class="btn-label forright-icon" style="left:136px !important;"><i class="fa fa-chevron-right"></i></span></button>
		</div>
   </div>
   <div class="clearfix"></div>
   </form>
   </div>
   </div>
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
<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>
<script src="js/validator.js"></script>
<script src="js/jquery.inputmask.js"></script>
<script>
$(document).on('change', '#fld_method', function(){
	if ($(this).val() == 'Check') {
		$(".online").hide();
		$(".check").show();
	}
	if ($(this).val() == 'Online') {
		$(".check").hide();
		$(".online").show();
	}
	if ($(this).val() == 'Cash') {
		$(".check").hide();
		$(".online").hide();
	}
	if ($(this).val() == '') {
		$(".check").hide();
		$(".online").hide();
	}
});
$("[data-mask]").inputmask();
</script>
</body>
</html>
<? include_once('bottom.php');?>