<?
require_once("../configuration/dbconfig.php");
require_once( dirname(__FILE__)."/../login_check.php" );

$REQUEST = &$_REQUEST;

if( isLogin() == false )
{
	$oregister->redirect('../sign-in.php');
} 
else { 

	checkAndSetInArray($REQUEST, 'new_action', '');
	checkAndSetInArray($REQUEST, 'new_cid', 0);
	checkAndSetInArray($REQUEST, 'new_app_fee_percentage', 0);

	if( $REQUEST['new_action'] == "update_app_fee_percentage" 
		&& $REQUEST['new_cid'] > 0
		&& $REQUEST['new_app_fee_percentage'] > -1){
		if( is_numeric($REQUEST['new_app_fee_percentage']) ){
			$uQuery = "UPDATE tbl_campaign SET app_fee_percentage=".$REQUEST['new_app_fee_percentage']." WHERE fld_campaign_id=".$REQUEST['new_cid'];
			$stmt= $con->prepare($uQuery);
			$stmt->execute([]);
		}
	}

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

$sPageName = '<li>Manage Campaign Application Fee</li>';
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
<title>Admin<?php echo sWEBSITENAME;?> - Manage Campaign Application Fee</title>
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
    padding: 3px 8px !important;
}

table.dataTable thead .sorting:after{
	top: 2px !important; 
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
		  <h1 class="h1styling">Manage Campaign Application Fee</h1>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box" style="background: #fff;">
          	 <?
			if(isset($_GET['m']) && $_GET['m']){
			?>
			<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><?=$aMessage[$_GET['msg']]?>
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
				  <th>Campaign #</th>
				  <th>Campaign ID</th>
                  <th>Campaign Name</th>
                  <th>Start Date</th>
                  <th>End Date</th>                  
                  <th>App. Fee</th>
                  <th># Of Donation Received</th>
                  <th>Action</th>
                </tr>
                </thead>
                </table>
				
				
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
			
		 ],//left side buttons were showing from
		 "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		 "paging": true,
		 "searching": { "regex": true },
		 "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
		 "pageLength": 10,
		 "bProcessing": true,
		 "bServerSide": true,
		 "sAjaxSource": "application_fee_ajax.php?mode=<?=$mode;?>&uid=<?=$_SESSION['uid'];?>&rid=<?=$_SESSION['role_id'];?>",
		 "scrollY": '50vh',
		 "scrollX": true,
		 "autoWidth": false,
		 language: {
			processing: "<img src='images/loading-spinner-blue.gif'> Loading...",
		 },
		 "columnDefs": [
			{ "width": "60px", "targets": 0 },   //Status
			{ "width": "90px", "targets": 1 },   //Campaign #
			{ "width": "90px", "targets": 2 },   //Campaign ID
			{ "width": "250px", "targets": 3 },  //Campaign Name
			{ "width": "80px", "targets": 4 },   //Start Date
			{ "width": "80px", "targets": 5 },   //End Date
			{ "width": "70px", "targets": 6 },   //Commission
			{ "width": "70px", "targets": 7 }   //Action	
		 ],
		"order": [[ 1, "desc" ]],
	  });    
    });	
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
<style type="text/css">
.custom_r_modal_1{
	background-color: white;
	border: 1px solid #ededed;
	border-radius:10px;
	
	position: absolute;
	left: 45%;
	top:7%;
	z-index: 9999;

	padding: 10px;

	display: none;	
}

.custom_r_modal_1 .btn{
	background:#F3BE00 none repeat scroll 0 0 !important;
	border:1px solid #ededed;
}
.mb-10{
	margin-bottom: 10px;		
}

#application_fee_change_dialog h3, #application_fee_change_dialog h4{
	font-weight: bold;
}
</style>

<script type="text/javascript">
	var app_fee_data = [];
	var default_app_fee = <?php echo DEFAULT_APP_FEE;?>;
    $(document).ready(function(){
		$(document).on( 'click', '.change_fee', function () {
			var app_fee_percentage = default_app_fee;
			var fld_campaign_title = '';
			var new_cid = 0;

			app_fee_data = $(this).data();
			if(typeof app_fee_data !== 'undefined'){
				if(typeof app_fee_data['app_fee_percentage'] !== 'undefined'){
					app_fee_percentage = app_fee_data['app_fee_percentage'];
				}

				if(typeof app_fee_data['fld_campaign_title'] !== 'undefined'){
					fld_campaign_title = app_fee_data['fld_campaign_title'];
				}

				if(typeof app_fee_data['fld_campaign_id'] !== 'undefined'){
					new_cid = app_fee_data['fld_campaign_id'];
				}
			}			
			
			$("#new_camp_title").html(fld_campaign_title);

			$("#new_cid").val(new_cid);
			$("#new_app_fee_percentage").val(app_fee_percentage);
			
			setTimeout(function(){
			    $("#new_app_fee_percentage").focus();
				$([document.documentElement, document.body]).animate({
			        scrollTop: $("#application_fee_change_dialog").offset().top
			    }, 100);
			},100)
			
			console.log("clicked on change_fee", app_fee_data );
			$('#application_fee_change_dialog').show();
		});

		$(document).on( 'click', '#close_app_fee_btn', function () {
			$('#application_fee_change_dialog').hide();
		});

		$(document).on( 'click', '#update_app_fee_btn', function () {
			console.log('Updating application fee');
			$('#application_fee_change_dialog_form').submit();
			$('#application_fee_change_dialog').hide();
		});
    });
</script>

<div id="application_fee_change_dialog" class="custom_r_modal_1">
 	<form action="" method="get" id="application_fee_change_dialog_form">
		<h3>Change application fee</h3>
		<h4 id="new_camp_title" style="display: none;">Campaign name</h4>
		<h5>New App fee</h5>

		
		<input type="hidden" name="new_action" id="new_action" value="update_app_fee_percentage">
		<input type="hidden" name="new_cid" id="new_cid" value="">

		<input type="number" name="new_app_fee_percentage" id="new_app_fee_percentage" value="" class="form-control mb-10">
		<input type="button" name="update_app_fee_btn" id="update_app_fee_btn" value="Update" class="btn btn-block btn-primary mb-10">
		<input type="button" name="close_app_fee_btn" id="close_app_fee_btn" value="Cloes" class="btn btn-block btn-primary">
	</form>
</div>
</body>
</html>
<? include_once('bottom.php');?>