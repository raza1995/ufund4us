<?
require_once("../configuration/dbconfig.php");
$REQUEST = &$_REQUEST;	
//Declare variable required bellow
$thankyou = "";

if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else { 
	if ($_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5) {
		$oregister->redirect('dashboard.php');
	}
}
$sPageName = '<li>Email Management</li>';
$sEmailManage = 'active';

if (array_key_exists('generatesubmit', $REQUEST)) {

}

if (array_key_exists('emailsubmit', $REQUEST)) {

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
<title>Admin<?php echo sWEBSITENAME;?> - Email Management</title>
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
a.dt-button.fixed {
	float:left;
	background: #F3BE00 none repeat scroll 0 0 !important;
    border: 0px;
    border-radius: 3px;
    padding: 6px 12px;
    color: #fff;
    margin-right: 6px;
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
		 <h1 class="h1styling">Email Management</h1>
		 <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box">
			<form id="frmCampaign" style="width:60% !important;" name="frmCampaign" method="post" data-toggle="validator" >
			  <div class="form-group col-md-6">
				<div style="padding-left:0px;font-size:16px;font-weight:bold;padding-bottom:10px;color:#868484; font-family: 'Open Sans';margin-top: 8px;">Select Campaign</div>
				<select class="form-control colorMeBlue noValue" name="fld_campaign_id" id="fld_campaign_id" onchange="chkschedule(this.value)" required>
				  <option value="">Select Campaign</option>
				  <?
				  $uid = $_SESSION['uid'];
				  $rid = $_SESSION['role_id'];
				  $CampaignData = $oCampaign->getallcampaigns($uid, $rid);
				  $iCountRecords = count($CampaignData);
				  if($iCountRecords>0){
  					for($i=0;$i<$iCountRecords;$i++){
				  ?>
				  <option value="<?=$CampaignData[$i]['fld_campaign_id']?>" <? if($CampaignData[$i]['fld_campaign_id'] == $_GET['cid']){?> selected<? }?>><?=$CampaignData[$i]['fld_campaign_id']." - ".$CampaignData[$i]['fld_campaign_title'];?></option>
				  <? }}?>                  
			    </select>
				<div class="help-block with-errors"></div>
			  </div>
			  <div class="showhide" style="display:none;">
			  <div class="form-group col-md-3">
				<div style="padding-left:0px;font-size:16px;font-weight:bold;padding-bottom:10px;color:#868484; font-family: 'Open Sans';margin-top: 8px;">Last Sent</div>
				<input readonly class="form-control" name="emaillastsent" id="emaillastsent" />
			  </div>
			  <div class="form-group col-md-3">
				<div style="padding-left:0px;font-size:16px;font-weight:bold;padding-bottom:10px;color:#868484; font-family: 'Open Sans';margin-top: 8px;">Next Schedule</div>
				<input readonly class="form-control" name="emailnextsent" id="emailnextsent" />
			  </div>
			  </div>
			  <div class="clearfix"></div>
			  <? if($_SESSION['role_id'] == 1) { ?>
			  <div id="viewreport" style="display:none;">
			  <div class="col-lg-12">
				<div class="col-lg-6">
				  <button class="btn btn-primary waves-effect waves-light viewreportmodal" type="button"><span class="btn-label" ><i class="fa fa-check"></i></span>View Reports</button>
				</div>
			  </div>
			  <div class="clearfix"></div>
			  <br>
			  </div>
			  <div class="col-lg-12">
				<div class="col-lg-6">
				  <button class="btn btn-primary waves-effect waves-light participantrestrict" data-toggle="modal" data-target="#viewparticipantsmodal" type="button"><span class="btn-label" ><i class="fa fa-check"></i></span>View Participants</button>
				</div>
				<div class="col-lg-6">
				  <button class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#sendparticipantsmodal" type="button"><span class="btn-label" style=""><i class="fa fa-check"></i></span>Send Participants</button>
				</div>
			  </div>
			  <div class="clearfix"></div>
			  <br>
			  <div class="col-lg-12">
				<div class="col-lg-6">
				  <button class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#viewdonorsmodal" type="button"><span class="btn-label" style=""><i class="fa fa-check"></i></span>View Donors</button>
				</div>
				<div class="col-lg-6">
				  <button class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#senddonorsmodal" type="button"><span class="btn-label" style=""><i class="fa fa-check"></i></span>Send Donors</button>
				</div>
			  </div>
			  <div class="col-lg-12">
			    <div><?=$thankyou;?></div>
		      </div>
			  <div class="clearfix"></div>
			  <? } else { ?>
			  <div id="viewreport" style="display:none;">
			  <div class="col-lg-12">
				<div class="col-lg-6">
				  <button class="btn btn-primary waves-effect waves-light viewreportmodal" type="button"><span class="btn-label" style=""><i class="fa fa-check"></i></span>View Reports</button>
				</div>
			  </div>
			  <div class="clearfix"></div>
			  <br>
			  </div>
			  <div class="col-lg-12">
				<div class="col-lg-6">
				  <button class="btn btn-primary waves-effect waves-light participantrestrict" data-toggle="modal" data-target="#viewparticipantsmodal" type="button"><span class="btn-label" style=""><i class="fa fa-check"></i></span>View Participants</button>
				</div>
			  </div>
			  <div class="clearfix"></div>
			  <br>
			  <div class="col-lg-12">
				<div class="col-lg-6">
				  <button class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#viewdonorsmodal" type="button"><span class="btn-label" style=""><i class="fa fa-check"></i></span>View Donors</button>
				</div>
			  </div>
			  <div class="col-lg-12">
			    <div><?=$thankyou;?></div>
		      </div>
			  <div class="clearfix"></div>
			  <? } ?>
			  <div id="viewparticipantsmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="viewparticipantsmodalLabel" aria-hidden="true" style="display: none;">
				     <div class="modal-dialog">
					    <div class="modal-content">
						   <div class="modal-header">
						      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							  <h4 class="modal-title" id="myModalLabel">View Participants</h4>
						   </div>
						   <div class="modal-body">
						      <p><b>Remaining Participants:</b> Participants not sent out this week.</b></p>
						      <p><b>All Participants:</b> Show all Participants doesnt matter already sent out.</b></p>
						   </div>
						   <div class="modal-footer">
						      <button type="button" class="btn btn-default waves-effect viewparticipants" value="1">Remaining Participants</button>
						      <button type="button" class="btn btn-default waves-effect viewparticipants" value="0">All Participants</button>
						      <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
						   </div>
					    </div>
						<!-- /.modal-content -->
					 </div>
					 <!-- /.modal-dialog -->
			   </div>
			   <div id="sendparticipantsmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="sendparticipantsmodalLabel" aria-hidden="true" style="display: none;">
				     <div class="modal-dialog">
					    <div class="modal-content">
						   <div class="modal-header">
						      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							  <h4 class="modal-title" id="myModalLabel">Send Participants</h4>
						   </div>
						   <div class="modal-body">
						      <p><b>Remaining Participants:</b> Participants not sent out this week.</b></p>
						      <p><b>All Participants:</b> Show all Participants doesnt matter already sent out.</b></p>
						   </div>
						   <div class="modal-footer">
						      <button type="button" class="btn btn-default waves-effect sentparticipants" value="1">Remaining Participants</button>
						      <button type="button" class="btn btn-default waves-effect sentparticipants" value="0">All Participants</button>
						      <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
						   </div>
					    </div>
						<!-- /.modal-content -->
					 </div>
					 <!-- /.modal-dialog -->
			   </div>
			   <div id="viewdonorsmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="viewdonorsmodalLabel" aria-hidden="true" style="display: none;">
				     <div class="modal-dialog">
					    <div class="modal-content">
						   <div class="modal-header">
						      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							  <h4 class="modal-title" id="myModalLabel">View Donors</h4>
						   </div>
						   <div class="modal-body">
						      <p><b>Remaining Donors:</b> Donors not sent out this week.</b></p>
						      <p><b>All Donors:</b> Show all donors doesnt matter already sent out.</b></p>
						   </div>
						   <div class="modal-footer">
						      <button type="button" class="btn btn-default waves-effect viewdonors" value="1">Remaining Donors</button>
						      <button type="button" class="btn btn-default waves-effect viewdonors" value="0">All Donors</button>
						      <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
						   </div>
					    </div>
						<!-- /.modal-content -->
					 </div>
					 <!-- /.modal-dialog -->
			   </div>
			   <div id="senddonorsmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="senddonorsmodalLabel" aria-hidden="true" style="display: none;">
				     <div class="modal-dialog">
					    <div class="modal-content">
						   <div class="modal-header">
						      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							  <h4 class="modal-title" id="myModalLabel">Send Donors</h4>
						   </div>
						   <div class="modal-body">
						      <p><b>Remaining Donors:</b> Donors not sent out this week.</b></p>
						      <p><b>All Donors:</b> Show all donors doesnt matter already sent out.</b></p>
						   </div>
						   <div class="modal-footer">
						      <button type="button" class="btn btn-default waves-effect sentdonors" value="1">Remaining Donors</button>
						      <button type="button" class="btn btn-default waves-effect sentdonors" value="0">All Donors</button>
						      <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
						   </div>
					    </div>
						<!-- /.modal-content -->
					 </div>
					 <!-- /.modal-dialog -->
			   </div>
			</form>
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
<script src="//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.3/js/buttons.colVis.min.js"></script>
<script src="bower_components/sweetalert/sweetalert.min.js"></script>
<script type="text/javascript">
	var uid = '<?=base64_encode($_SESSION['uid']);?>';
	function chkschedule(cid) {
		//alert(cid);
		if (cid != '') {
			$.post('email_manage_post.php', 'cid=' + cid + '&uid=' + uid + '&act=5', function (response) {
				var jdata = JSON.parse(response);
				if (jdata.counter > 0) {
					var rid = <?=$rid;?>;
					var camp_id = jdata.camp_id;
					var camp_title = jdata.camp_title;
					var ab1575_pupil = jdata.ab1575_pupil;
					var sdate = jdata.sdate;
					var edate = jdata.edate;
					var last_updated = jdata.last_updated;
					var next_sent = jdata.next_sent;
					if (rid > 1) {
						if (ab1575_pupil == 1) {
							$('.participantrestrict').hide();
						} else {
							$('.participantrestrict').show();
						}
					}
					//alert(next_sent);
					$('.showhide').show();
					if (last_updated != '00/00/0000') {
						$('#emaillastsent').val(last_updated);
					} else {
						$('#emaillastsent').val('');
					}
					if (next_sent != '00/00/0000') {
						$('#emailnextsent').val(next_sent);
					} else {
						$('#emailnextsent').val('');
					}
					$('#viewreport').show();
				} else {
					$('#emaillastsent').val('');
					$('#emailnextsent').val('');
					$('#viewreport').hide();
				}
			});
		} else {
			$('.showhide').hide();
			$('#emaillastsent').val('');
			$('#emailnextsent').val('');
			$('#viewreport').hide();
		}
	}

	$('.viewreportmodal').on('click', function(){
		$('.formodal').empty();
		var campaignid = $('#fld_campaign_id').val();
		var campaigntitle = $("#fld_campaign_id option:selected").text();
		$.post('email_manage_post.php', 'cid=' + campaignid + '&uid=' + uid + '&act=6', function (response) {
			var jdata = JSON.parse(response);
			var reportview = ''+jdata.emailreport+'';
			var row1 = reportview.split(",");
		 	var reportlist = '<div class="modal fade reportshow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
				  reportlist += '<div class="modal-dialog modal-lg">';
				  	reportlist += '<div class="modal-content">';
					  reportlist += '<div class="modal-header">';
						reportlist += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
						reportlist += '<h4 class="modal-title" id="myModalLabel">'+campaigntitle+'</h4>';
					  reportlist += '</div>';
					  reportlist += '<div class="modal-body white-box white" style="background: #fff;overflow-x: scroll;position: relative;width: 100%;">';
						reportlist += '<table id="example1" class="table table-bordered table-striped">';
						  reportlist += '<thead>';
							reportlist += '<tr>';
							  reportlist += '<th>Campaign Id</th>';
							  reportlist += '<th>Campaign Title</th>';
							  reportlist += '<th>Emails Projected</th>';
							  reportlist += '<th>Emails Sent</th>';
							  reportlist += '<th>Emails Error</th>';
							  reportlist += '<th>Emails Missing</th>';
							  reportlist += '<th>Emails Type</th>';
							  reportlist += '<th>Submitted By</th>';
							  reportlist += '<th>Sent Date</th>';
							reportlist += '</tr>';
						  reportlist += '</thead>';
						  reportlist += '<tbody>';
						  if (jdata.counter > 0) {
							for(var i = 0; i < row1.length; i++) {
							var row2 = row1[i].split("|");
							reportlist += '<tr>';
							reportlist += '<td>'+row2[0]+'</td>';
							reportlist += '<td>'+row2[1]+'</td>';
							reportlist += '<td>'+row2[2]+'</td>';
							reportlist += '<td>'+row2[3]+'</td>';
							reportlist += '<td>'+row2[4]+'</td>';
							reportlist += '<td>'+row2[5]+'</td>';
							reportlist += '<td>'+row2[6]+'</td>';
							reportlist += '<td>'+row2[8]+' - '+row2[7]+'</td>';
							reportlist += '<td>'+row2[9]+'</td>';
							reportlist += '</tr>';
							}
						  } else {
							reportlist += '<tr>';
							reportlist += '<td colspan="9" align="center">No any information yet...!</td>';
							reportlist += '</tr>';
						  }
						  reportlist += '</tbody>';
						reportlist += '</table>';
					  reportlist += '<div class="modal-footer">';
						reportlist += '<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
					  reportlist += '</div>';
					  reportlist += '</div>';
					reportlist += '</div>';
				  reportlist += '</div>';
				reportlist += '</div>';
				$('.formodal').append(reportlist);
				$(".reportshow").modal('show');
				var table = $('#example1').DataTable({
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
						{ "width": "110px", "targets": 0 },   //Campaign ID
						{ "width": "300px", "targets": 1 },  //Campaign Title
						{ "width": "200px", "targets": 2 },  //Emails Projected
						{ "width": "110px", "targets": 3 },   //Emails Sent
						{ "width": "110px", "targets": 4 },   //Emails Error
						{ "width": "110px", "targets": 5 },   //Emails Missing
						{ "width": "110px", "targets": 6 },   //Emails Type
						{ "width": "200px", "targets": 7 },   //Submitted By
						{ "width": "110px", "targets": 8 },   //Sent Date
					],
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
		});
	});

	$('.viewparticipants').on('click', function(){
		$('.formodal').empty();
		var actionid = $(this).val();
		var campaignid = $('#fld_campaign_id').val();
		$.post('email_manage_post.php', 'cid=' + campaignid + '&uid=' + uid + '&act=1&actionid=' + actionid + '', function (response) {
			var jdata = JSON.parse(response);
			var participantview = ''+jdata.participantview+'';
			var row1 = participantview.split(",");
		 	var particiapntlist = '<div class="modal fade participantshow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
				  particiapntlist += '<div class="modal-dialog modal-lg">';
				  	particiapntlist += '<div class="modal-content">';
					  particiapntlist += '<div class="modal-header">';
						particiapntlist += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
						particiapntlist += '<h4 class="modal-title" id="myModalLabel">'+jdata.campaigntitle+'</h4>';
					  particiapntlist += '</div>';
					  particiapntlist += '<div class="modal-body white-box white" style="background: #fff;overflow-x: scroll;position: relative;width: 100%;">';
						particiapntlist += '<table id="example1" class="table table-bordered table-striped">';
						  particiapntlist += '<thead>';
							particiapntlist += '<tr>';
							  particiapntlist += '<th>Campaign Id</th>';
							  particiapntlist += '<th>Campaign Title</th>';
							  particiapntlist += '<th>Campaign Manager</th>';
							  particiapntlist += '<th>Participant Id</th>';
							  particiapntlist += '<th>Participant Name</th>';
							  particiapntlist += '<th>Participant Email</th>';
							particiapntlist += '</tr>';
						  particiapntlist += '</thead>';
						  particiapntlist += '<tbody>';
						  if (jdata.counter > 0) {
							for(var i = 0; i < row1.length; i++) {
							var row2 = row1[i].split("|");
							particiapntlist += '<tr>';
							particiapntlist += '<td>'+row2[0]+'</td>';
							particiapntlist += '<td>'+row2[1]+'</td>';
							particiapntlist += '<td>'+row2[2]+' '+row2[3]+'</td>';
							particiapntlist += '<td>'+row2[4]+'</td>';
							particiapntlist += '<td>'+row2[5]+'  '+row2[6]+'</td>';
							particiapntlist += '<td>'+row2[7]+'</td>';
							particiapntlist += '</tr>';
							}
						  } else {
							particiapntlist += '<tr>';
							particiapntlist += '<td colspan="8" align="center">No any information yet...!</td>';
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
				$(".participantshow").modal('show');
				var table = $('#example1').DataTable({
					"scrollY": '50vh',
					"scrollX": true,
					"autoWidth": false,
					"columnDefs": [
						{ "width": "110px", "targets": 0 },   //Campaign ID
						{ "width": "300px", "targets": 1 },  //Campaign Title
						{ "width": "200px", "targets": 2 },  //Campaign Manager Name
						{ "width": "120px", "targets": 3 },   //Participant ID
						{ "width": "180px", "targets": 4 },   //Participant Name
						{ "width": "250px", "targets": 5 },   //Participant Email
					],
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
	   });
   });

   $('.sentparticipants').on('click', function(){
		var actionid = $(this).val();
		var campaignid = $('#fld_campaign_id').val();
		var campaignid2 = $('#fld_campaign_id option:selected').text();
		var campaigntitle = campaignid2.split(" - ");
		var textlive = "Campaign Title: "+campaigntitle[1]+"";
		//alert('sending');
		swal({   
        title: "Do you want to continue?",   
        text: textlive,   
        type: "warning",   
        showCancelButton: true,   
        confirmButtonColor: "#FCB514",   
        confirmButtonText: "Yes",
		cancelButtonText: "No",
        closeOnConfirm: false 
		}, function(){
			var timer1 = 3000;
			swal({title: "Please Wait...!",   text: "Sending Emails to participants.", type: "success", timer: timer1, showConfirmButton: false });
			$.post('email_manage_post.php', 'cid=' + campaignid + '&uid=' + uid + '&act=3&actionid=' + actionid + '', function (response) {
				var jdata = JSON.parse(response);
				var totalcounter = jdata.totalcounter;
				var sentcounter = jdata.sentcounter;
				swal({title: "Email Sent...!",   text: "Emails has been sent to participants ("+sentcounter+" of "+totalcounter+").", type: "success" });
			});
		});
	});

   $('.viewdonors').on('click', function(){
		$('.formodal').empty();
		var actionid = $(this).val();
		var campaignid = $('#fld_campaign_id').val();

		$.post('email_manage_post.php', 'cid=' + campaignid + '&uid=' + uid + '&act=2&actionid=' + actionid + '', function (response) {
			var jdata = JSON.parse(response);
			var donorview = ''+jdata.donorview+'';
			var row1 = donorview.split(",");
		 	var donorlist = '<div class="modal fade donorshow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
				  donorlist += '<div class="modal-dialog modal-lg">';
				  	donorlist += '<div class="modal-content">';
					  donorlist += '<div class="modal-header">';
						donorlist += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
						donorlist += '<h4 class="modal-title" id="myModalLabel">'+jdata.campaigntitle+'</h4>';
					  donorlist += '</div>';
					  donorlist += '<div class="modal-body white-box white" style="background: #fff;overflow-x: scroll;position: relative;width: 100%;">';
						donorlist += '<table id="example2" class="table table-bordered table-striped">';
						  donorlist += '<thead>';
							donorlist += '<tr>';
							  donorlist += '<th style="font-size:13px !important; width:20%; text-align:left;">Campaign Id</th>';
							  donorlist += '<th style="font-size:13px !important; width:20%; text-align:left;">Campaign Title</th>';
							  donorlist += '<th style="font-size:13px !important; width:20%; text-align:left;">Donor Id</th>';
							  donorlist += '<th style="font-size:13px !important; width:20%; text-align:left;">Donor Name</th>';
							  donorlist += '<th style="font-size:13px !important; width:20%; text-align:left;">Donor Email</th>';
							  donorlist += '<th style="font-size:13px !important; width:20%; text-align:left;">Participant Id</th>';
							  donorlist += '<th style="font-size:13px !important; width:16%; text-align:left;">Participant Name</th>';
							  donorlist += '<th style="font-size:13px !important; width:16%; text-align:left;">Participant Email</th>';
							donorlist += '</tr>';
						  donorlist += '</thead>';
						  donorlist += '<tbody>';
						  if (jdata.counter > 0) {
							for(var i = 0; i < row1.length; i++) {
							var row2 = row1[i].split("|");
							donorlist += '<tr>';
							donorlist += '<td style="font-size:13px !important; width:5%; text-align:left;">'+row2[0]+'</td>';
							donorlist += '<td style="font-size:13px !important; width:15%; text-align:left;">'+row2[1]+'</td>';
							donorlist += '<td style="font-size:13px !important; width:4%; text-align:left;">'+row2[2]+'</td>';
							donorlist += '<td style="font-size:13px !important; width:10%; text-align:left;">'+row2[3]+' '+row2[4]+'</td>';
							donorlist += '<td style="font-size:13px !important; width:10%; text-align:left;">'+row2[5]+'</td>';
							donorlist += '<td style="font-size:13px !important; width:4%; text-align:left;">'+row2[6]+'</td>';
							donorlist += '<td style="font-size:13px !important; width:10%; text-align:left;">'+row2[7]+'  '+row2[8]+'</td>';
							donorlist += '<td style="font-size:13px !important; width:10%; text-align:left;">'+row2[9]+'</td>';
							donorlist += '</tr>';
							}
						  } else {
							donorlist += '<tr>';
							donorlist += '<td colspan="8" align="center">No any information yet...!</td>';
							donorlist += '</tr>';
						  }
						  donorlist += '</tbody>';
						donorlist += '</table>';
					  donorlist += '<div class="modal-footer">';
						donorlist += '<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
					  donorlist += '</div>';
					  donorlist += '</div>';
					donorlist += '</div>';
				  donorlist += '</div>';
				donorlist += '</div>';
				$('.formodal').append(donorlist);
				$(".donorshow").modal('show');
				var table = $('#example2').DataTable({
					"scrollY": '50vh',
					"scrollX": true,
					"autoWidth": false,
					"columnDefs": [
						{ "width": "110px", "targets": 0 },   //Campaign ID
						{ "width": "300px", "targets": 1 },  //Campaign Title
						{ "width": "100px", "targets": 2 },  //Donor ID
						{ "width": "180px", "targets": 3 },   //Donor Name
						{ "width": "250px", "targets": 4 },   //Donor Email
						{ "width": "120px", "targets": 5 },   //Participant ID
						{ "width": "180px", "targets": 6 },   //Participant Name
						{ "width": "250px", "targets": 7 },   //Participant Email
					],
				});
				$('#example2 tbody').on( 'click', 'tr', function () {
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
	   });
   });
   $('.sentdonors').on('click', function(){
		var actionid = $(this).val();
		var campaignid = $('#fld_campaign_id').val();
		var campaignid2 = $('#fld_campaign_id option:selected').text();
		var campaigntitle = campaignid2.split(" - ");
		var textlive = "Campaign Title: "+campaigntitle[1]+"";
		swal({   
        title: "Do you want to continue?",   
        text: textlive,   
        type: "warning",   
        showCancelButton: true,   
        confirmButtonColor: "#FCB514",   
        confirmButtonText: "Yes",
		cancelButtonText: "No",
        closeOnConfirm: false 
		}, function(){
			var timer1 = 3000;
			swal({title: "Please Wait...!",   text: "Sending Emails to donors.", type: "success", timer: timer1, showConfirmButton: false });
			$.post('email_manage_post.php', 'cid=' + campaignid + '&uid=' + uid + '&act=4&actionid=' + actionid + '', function (response) {
				var jdata = JSON.parse(response);
				var totalcounter = jdata.totalcounter;
				var sentcounter = jdata.sentcounter;
				swal({title: "Email Sent...!",   text: "Emails has been sent to donors ("+sentcounter+" of "+totalcounter+").", type: "success" });
			});
		});
	});
   $('#notifications').delay(3000).fadeOut('slow');
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