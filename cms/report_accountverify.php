<?
require_once("../configuration/dbconfig.php");

$REQUEST = &$_REQUEST;
$REQUEST['cid'] = isset($REQUEST['cid']) ? $REQUEST['cid'] : 0;

if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else {
	if ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5 || $_SESSION['role_id'] == 6) {
		$oregister->redirect('dashboard.php');
	}
}
$sPageName = '<li>Accounts Verify Report</li>';
$sAccountVerify = 'active';
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
<title>Admin<?php echo sWEBSITENAME;?> - Accounts Verify Report</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<link href="bower_components/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">
<link href="bower_components/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
<style>
button {

	background: #F3BE00 none repeat scroll 0 0 !important;

}
#example1_wrapper table{
  width: 100%;
  clear: both;
  border-collapse: collapse;
  table-layout: fixed; 
  word-wrap:break-word; 
}
#example1_wrapper table.dataTable {
	margin-bottom: 0px !important;
	margin-top: 0px !important;
	margin: 0px !important;
}
#example1_wrapper .dataTables_scrollBody .sorting:after, #example1_wrapper .dataTables_scrollBody .sorting_asc:after{content:'';display:none !important;}

#example1_wrapper .table > tbody > tr > td, #example1_wrapper .table > tbody > tr > th, #example1_wrapper .table > tfoot > tr > td, #example1_wrapper .table > tfoot > tr > th, #example1_wrapper .table > thead > tr > td, #example1_wrapper .table > thead > tr > th {
    padding: 5px 8px !important;
}

#example1_wrapper .table.dataTable tbody tr.selected {
    background-color: #B0BED9 !important;
}

#example2_wrapper table{
  width: 100%;
  clear: both;
  border-collapse: collapse;
  table-layout: fixed; 
  word-wrap:break-word; 
}
#example2_wrapper table.dataTable {
	margin-bottom: 0px !important;
	margin-top: 0px !important;
	margin: 0px !important;
}
#example2_wrapper .dataTables_scrollBody .sorting:after, #example2_wrapper .dataTables_scrollBody .sorting_asc:after{content:'';display:none !important;}

#example2_wrapper .table > tbody > tr > td, #example2_wrapper .table > tbody > tr > th, #example2_wrapper .table > tfoot > tr > td, #example2_wrapper .table > tfoot > tr > th, #example2_wrapper .table > thead > tr > td, #example2_wrapper .table > thead > tr > th {
    padding: 5px 8px !important;
}

#example2_wrapper .table.dataTable tbody tr.selected {
    background-color: #B0BED9 !important;
}
</style>
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
		 <h1 class="h1styling">Account Verify Report</h1>
		 <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box">
			<? if($_SESSION['role_id'] == 1) { ?>
			<form id="frmCampaign" style="width:60% !important;" name="frmCampaign" method="post" data-toggle="validator" >
			  <div class="form-group col-md-6">
				<div style="padding-left:0px;font-size:16px;font-weight:bold;padding-bottom:10px;color:#868484; font-family: 'Open Sans';margin-top: 8px;">Select Campaign</div>
				<select class="form-control colorMeBlue noValue" name="fld_campaign_id" id="fld_campaign_id" required>
				  <option value="">Select Campaign</option>
				  <?
				  $uid = $_SESSION['uid'];
				  $rid = $_SESSION['role_id'];
				  $CampaignData = $oCampaign->getallcampaigns($uid, $rid);
				  $iCountRecords = count($CampaignData);
				  if($iCountRecords>0){
  					for($i=0;$i<$iCountRecords;$i++){
				  ?>
				  <option value="<?=$CampaignData[$i]['fld_campaign_id']?>" <? if($CampaignData[$i]['fld_campaign_id'] == $REQUEST['cid']){?> selected<? }?>><?=$CampaignData[$i]['fld_campaign_id']." - ".$CampaignData[$i]['fld_campaign_title'];?></option>
				  <? } } ?>                  
			    </select>
				<div class="help-block with-errors"></div>
			  </div>
			  <div class="clearfix"></div>
			  <div class="col-lg-12">
				<div class="col-lg-6">
				  <button class="btn btn-primary waves-effect waves-light" id="viewreport" type="button"><span class="btn-label" style=""><i class="fa fa-check"></i></span>View Report</button>
				</div>
			  </div>
			  <div class="clearfix"></div>
			</form>
			<? } ?>
			<div class="formodal">
			</div>
		  </div>
		  <div style="clear:both"></div>
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
<!--Sparkline charts js -->
<script src="bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
<script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>
<script src="js/validator.js"></script>
<script src="bower_components/datatables/jquery.dataTables.min.js"></script>
<script src="bower_components/sweetalert/sweetalert.min.js"></script>
<script type="text/javascript" src="js/accounting.js"></script>
<script>
function addCommas(x) {

	var mval = accounting.formatMoney(x); 
	mval = mval.replace('$', '');
	return mval;
}
</script>
<script type="text/javascript">
	$('#viewreport').on('click', function(){
		$('.formodal').empty();
		var campaignid = $('#fld_campaign_id').val();
		$.post('account_verify_post.php', 'cid=' + campaignid + '', function (response) {
			var jdata = JSON.parse(response);
			var particiapntlist = '<div class="modal fade participantshow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
				  particiapntlist += '<div class="modal-dialog modal-lg">';
				  	particiapntlist += '<div class="modal-content">';
					  particiapntlist += '<div class="modal-header">';
						particiapntlist += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
						particiapntlist += '<h4 class="modal-title" id="myModalLabel">('+campaignid+' - '+jdata.campaigntitle+') - '+jdata.campaignmname+'</h4>';
					  particiapntlist += '</div>';
					  particiapntlist += '<div class="modal-body white-box white" style="background: #fff;overflow-x: scroll;position: relative;width: 100%;">';
						particiapntlist += '<table class="table table-bordered table-striped">';
						  particiapntlist += '<thead>';
							particiapntlist += '<tr>';
							  particiapntlist += '<th># Donations</th>';
							  particiapntlist += '<th><?php echo sWEBSITENAME;?></th>';
							  particiapntlist += '<th>Stripe (Available)</th>';
							  particiapntlist += '<th>Stripe (Pending)</th>';
							  particiapntlist += '<th>Stripe (Total)</th>';
							particiapntlist += '</tr>';
						  particiapntlist += '</thead>';
						  particiapntlist += '<tbody>';
						  if (jdata.counter > 0) {
							particiapntlist += '<tr>';
							particiapntlist += '<td>'+jdata.nodonations+'</td>';
							particiapntlist += '<td>$ '+addCommas(jdata.donationsamount)+'</td>';
							particiapntlist += '<td>$ '+addCommas(jdata.stripeavailable)+'</td>';
							particiapntlist += '<td>$ '+addCommas(jdata.stripepending)+'</td>';
							particiapntlist += '<td>$ '+addCommas(jdata.stripetotal)+'</td>';
							particiapntlist += '</tr>';
						  } else {
							particiapntlist += '<tr>';
							particiapntlist += '<td colspan="5" align="center">No any information yet...!</td>';
							particiapntlist += '</tr>';
						  }
						  particiapntlist += '</tbody>';
						particiapntlist += '</table>';
					  particiapntlist += '<div class="modal-footer">';
						particiapntlist += '<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
					  particiapntlist += '</div>';
					  particiapntlist += '</div>';
					particiapntlist += '</div>';
				  particiapntlist += '</div>';
				particiapntlist += '</div>';
				$('.formodal').append(particiapntlist);
				$(".participantshow").modal('show');
	   });
   });
   
   $('select').on('change', function(){
    var $this = $(this);
    if (!$this.val()) {
        $this.addClass('noValue');
    } else {
        $this.removeClass('noValue');
    }
});
</script>
</body>
</html>
<? include_once('bottom.php');?>