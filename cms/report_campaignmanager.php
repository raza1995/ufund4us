<?
require_once("../configuration/dbconfig.php");
if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else {
	if ($_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5) {
		$oregister->redirect('dashboard.php');
	}
}
$sPageName = '<li>Reports</li><li>Campaign Manager Report</li>';
$sCampaignManagerLink = 'style="color:#F3BE00"';
$sLeftMenuReport = 'active';

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
<title>Admin<?php echo sWEBSITENAME;?> - Campaign Manager Report</title>
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

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
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
		  <h1 class="h1styling">Campaign Manager Report</h1>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box white">
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
             $sCampaignData = $oCampaign->getcampaignmanagerreport($uid, $rid);
			 $iCountRecords = count($sCampaignData);
			 //if($iCountRecords>0){
			 ?>
				<table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Status</th>
                  <th>Campaign Name</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th># Of Days Left</th>
                  <th># Of Participants</th>
                  <th># Of Participants Enrolled</th>
                  <th>% Of Participants Enrolled</th>
                  <th># Of Projected Donor Required</th>
                  <th># Of Projected Donor Uploaded</th>
                  <th>% Of Donors Required</th>
                  <th># Of Donations Received</th>
				  <th>Average Donation Amount</th>
				  <th>Profit Raised</th>
				  <th>Campaign Goal</th>
				  <th>% Of Goal</th>
				  <th>Money Raised</th>
                </tr>
                </thead>
                <tbody>
                <?
                for($i=0;$i<$iCountRecords;$i++){
					$avg_donation_amt = 0;
					//handling for preventing divisiable by zeror error
					if($sCampaignData[$i]['donation_count'] != 0){
						$avg_donation_amt = $sCampaignData[$i]['donation_sum']/$sCampaignData[$i]['donation_count'];
					}
					$profit_raised = 0.8*$sCampaignData[$i]['donation_sum'];
				?>
                <tr>
                  <td><? if($sCampaignData[$i]['fld_status'] == 1){?> <i class="fa fa-fw fa-thumbs-o-up"></i><? }else{?> <i class="fa fa-fw fa-thumbs-o-down"></i> <? }?></td>
                  <td><a href="start_campaign.php?m=e&cid=<?=$sCampaignData[$i]['fld_campaign_id']?>"><?=$sCampaignData[$i]['fld_campaign_title']?></a></td>
                  <td><?=date('F j, Y',strtotime($sCampaignData[$i]['fld_campaign_sdate']))?></td>
                  <td><?=date('F j, Y',strtotime($sCampaignData[$i]['fld_campaign_edate']))?></td>
                  <td><?=$sCampaignData[$i]['daysleft']?></td>
				  <td><?=$sCampaignData[$i]['fld_team_size']?></td>
				  <td><?=$sCampaignData[$i]['participant_count']?></td>
				  <td><?php
				  $v = 0;
				  if($sCampaignData[$i]['fld_team_size'] != 0){
				  	$v = number_format((float)($sCampaignData[$i]['participant_count']/$sCampaignData[$i]['fld_team_size'])*100, 2, '.', '');
				  }
				  echo $v;
				  ?> %</td>
				  <td><?=$sCampaignData[$i]['fld_donor_size']?></td>
				  <td><?=$sCampaignData[$i]['donors_count']?></td>
				  <td><?php
				  $v = 0;
				  if($sCampaignData[$i]['fld_donor_size'] != 0){
				  	$v = number_format((float)($sCampaignData[$i]['donors_count']/$sCampaignData[$i]['fld_donor_size'])*100, 2, '.', '');
					
				  }
				  echo $v;
				  ?> %</td>
				  
				  
				  <td><?=$sCampaignData[$i]['donation_count']?></td>
				  <td><?=$avg_donation_amt?></td>
				  <td><?=$profit_raised?></td>
				  <td><?=$sCampaignData[$i]['fld_campaign_goal']?></td>
				  <td><?php
				  $v = 0;
				  if($sCampaignData[$i]['fld_campaign_goal'] != 0){
				  	$v = number_format((float)($sCampaignData[$i]['donation_sum']/$sCampaignData[$i]['fld_campaign_goal'])*100, 2, '.', '');
				  }
				  echo $v;
				  ?> %</td>
				  <td><?=$sCampaignData[$i]['donation_sum']?></td>
                </tr>
                <? } ?>
                </tbody>
                </table>
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
<script>
$(document).ready(function(){
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
		// "scrollY": '50vh',
		// "scrollX": true,
		"autoWidth": false,
		"columnDefs": [
			{ "width": "100px", "targets": 0 },   //Status
			{ "width": "150px", "targets": 1 },   //Campaign Name
			{ "width": "200px", "targets": 2 },   //Start Date
			{ "width": "200px", "targets": 3 },   //End Date
			{ "width": "200px", "targets": 4 },   //# Of Days Left
			{ "width": "200px", "targets": 5 },   //# Of Participants
			{ "width": "200px", "targets": 6 },    //# Of Participants Enrolled
			{ "width": "200px", "targets": 7 },   //% Of Participants Enrolled
			{ "width": "250px", "targets": 8 },   //# Of Projected Donor Required
			{ "width": "250px", "targets": 9 },   //# Of Projected Donor Uploaded
			{ "width": "250px", "targets": 10 },   //% Of Donors Required
			{ "width": "250px", "targets": 11 },   //# Of Donations Received
			{ "width": "200px", "targets": 12 },   //Average Donation Amount
			{ "width": "200px", "targets": 13 },   //Profit Raised
			{ "width": "200px", "targets": 14 },   //Campaign Goal
			{ "width": "200px", "targets": 15 },   //% Of Goal
			{ "width": "200px", "targets": 16 },   //Money Raised
		],
		"order": [[ 0, "asc" ]]
	});
	$('#example1 tbody').on( 'click', 'tr', function () {
		if ( $(this).hasClass('selected') ) {
			$(this).removeClass('selected');
		} else {
			table.$('tr.selected').removeClass('selected');
			$(this).addClass('selected');
		}
		$('#button').click( function () {
			table.row('.selected').remove().draw( false );
		});
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

<script type="text/javascript">
   $('#notifications').delay(3000).fadeOut('slow');
</script>
</body>
</html>
<? include_once('bottom.php');?>