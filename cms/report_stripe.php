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
$sPageName = '<li>Reports</li><li>Stripe/'.sWEBSITENAME.' Report</li>';
$sStripeReport = 'style="color:#F3BE00"';
//$sLeftMenuReport = 'active';
include("php/dbconn.php");
require ('../lib/init.php');
ini_set('memory_limit', '-1');
date_default_timezone_set('America/Los_Angeles'); //TimeZone
\Stripe\Stripe::setApiKey(STRIPE_API_KEY); //Initialize Stripe Gateway

$QueryDonations = "SELECT * FROM tbl_donations";
$ResultDonations = mysqli_query($conn1, $QueryDonations) or die("ERROR: Cannot fetch the donations paid records...!");
$ResultDonationsRows = mysqli_num_rows($ResultDonations);
if ($ResultDonationsRows > 0) {
	while($Rows = mysqli_fetch_assoc($ResultDonations)) {
		$DataDonations[] = $Rows;
	}
}

$QueryRefunds = "SELECT * FROM tbl_donations_refund";
$ResultRefunds = mysqli_query($conn1, $QueryRefunds) or die("ERROR: Cannot fetch the donations refunds record...!");
$ResultRefundsRows = mysqli_num_rows($ResultRefunds);
if ($ResultRefundsRows > 0) {
	while($Rows2 = mysqli_fetch_assoc($ResultRefunds)) {
		$DataRefunds[] = $Rows2;
	}
}

$QueryDisputes = "SELECT * FROM tbl_donations_dispute";
$ResultDisputes = mysqli_query($conn1, $QueryDisputes) or die("ERROR: Cannot fetch the donations disputes record...!");
$ResultDisputesRows = mysqli_num_rows($ResultDisputes);
if ($ResultDisputesRows > 0) {
	while($Rows3 = mysqli_fetch_assoc($ResultDisputes)) {
		$DataDisputes[] = $Rows3;
	}
}
$paid_array = []; //Initial paid only without refunded and disputed.
$refund_array = []; //Initial refunded only without disputed.
$dispute_array = []; //Initial disputed only.
$DataDisputes = [];
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
<title>Admin<?php echo sWEBSITENAME;?> - Stripe/<?php echo sWEBSITENAME;?> Report</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<link href="bower_components/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/sweetalert2/sweetalert2.css" rel="stylesheet" type="text/css">
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
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
    padding: 6px 8px !important;
}

#example1_wrapper .table.dataTable tbody tr.selected {
    background-color: #B0BED9 !important;
}

#example2_wrapper table.dataTable {
	margin-bottom: 0px !important;
	margin-top: 0px !important;
}
#example2_wrapper .dataTables_scrollBody .sorting:after, #example1_wrapper .dataTables_scrollBody .sorting_asc:after{content:'';display:none !important;}
#example2_wrapper table{
  margin: 0 auto;
  width: 100%;
  clear: both;
  border-collapse: collapse;
  table-layout: fixed; 
  word-wrap:break-word; 
}
#example2_wrapper .table > tbody > tr > td, #example2_wrapper .table > tbody > tr > th, #example2_wrapper .table > tfoot > tr > td, #example2_wrapper .table > tfoot > tr > th, #example2_wrapper .table > thead > tr > td, #example2_wrapper .table > thead > tr > th {
    padding: 6px 8px !important;
}

#example2_wrapper .table.dataTable tbody tr.selected {
    background-color: #B0BED9 !important;
}

#example3_wrapper table.dataTable {
	margin-bottom: 0px !important;
	margin-top: 0px !important;
}
#example3_wrapper .dataTables_scrollBody .sorting:after, #example3_wrapper .dataTables_scrollBody .sorting_asc:after{content:'';display:none !important;}
#example3_wrapper table{
  margin: 0 auto;
  width: 100%;
  clear: both;
  border-collapse: collapse;
  table-layout: fixed; 
  word-wrap:break-word; 
}
#example3_wrapper .table > tbody > tr > td, #example3_wrapper .table > tbody > tr > th, #example3_wrapper .table > tfoot > tr > td, #example3_wrapper .table > tfoot > tr > th, #example3_wrapper .table > thead > tr > td, #example3_wrapper .table > thead > tr > th {
    padding: 6px 8px !important;
}

#example3_wrapper .table.dataTable tbody tr.selected {
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
    margin-right: 4px;
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
		  <h1 class="h1styling">Stripe/<?php echo sWEBSITENAME;?> Report</h1>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box white">
			<?php
			$charges_query = \Stripe\Charge::all(array("limit" => 100, "created" => array("gte" => strtotime("-1 month")))); //Getting all charges record 100 rows per page
			$newarray = [];
			foreach ($charges_query->autoPagingIterator() as $charge) {
				$newarray[] = $charge;
			}
			// echo 'stripe_records: <pre>'; echo var_dump($newarray); die();
			$stripe_records = [];
			if($newarray != ''){
				$json = json_encode($newarray);
				$stripe_records = json_decode($json, true);
			}
			$stripe_records_count = count($stripe_records);
			for ($sn=0; $sn < $stripe_records_count; $sn++) {
				$stripe_charge_id = str_replace ('ch_', '', $stripe_records[$sn]['id']); //Getting Stripe ChargeID
				if ($stripe_records[$sn]['paid'] == 1) { //Getting data paid only without refunded and disputed.
					//Missing Donations
					if (array_search($stripe_charge_id, array_column($DataDonations, 'tid'))) {
					} else {
						if($stripe_records[$sn]['refunded'] == '' && $stripe_records[$sn]['dispute'] == '') {
							$paid_array[] = $stripe_records[$sn]; //Data saved in paid array.
						}
					}
					//Missing Refunds
					if (array_search($stripe_charge_id, array_column($DataRefunds, 'tid'))) {
					} else {
						if($stripe_records[$sn]['refunded'] != '') { //Getting data refunded only without disputed.
							$refund_array[] = $stripe_records[$sn]; //Data saved in refund array.
						}
					}		
					//Missing Disputes
					if (array_search($stripe_charge_id, array_column($DataDisputes, 'tid'))) {
					} else {
						if($stripe_records[$sn]['dispute'] != '') { //Getting data disputed only.
							$dispute_array[] = $stripe_records[$sn]; //Data saved in dispute array.
						}
					}		
				}
			}
			?>
			<button class="btn btn-block btn-primary reconciliationscript" style="width:184px; margin-top:10px;"><span class="fa fa-plus"></span> <span class="newtext">Execute Reconciliation</span></button>
			<h2 align="center">Missing Paid Only without refunds/disputes</h2>
          	<table id="example1" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>S/No.</th>
						<th>Timestamp</th>
						<th>Campaign #</th>
						<th>Campaign Title</th>
						<th>Participant Id</th>
						<th>Participant Full Name</th>
						<th>Customer First Name</th>
						<th>Customer Last Name</th>
						<th>Customer Email</th>
						<th>Captured</th>
						<th>Amount</th>
						<th>Charge ID</th>
						<th>Destination Account</th>
					</tr>
                </thead>
                <tbody>
				<?
				$sno = 0;
				// echo "paid_array:<pre>"; echo var_dump($paid_array);die();
				if($paid_array == ""){//if its not an array, so before using it initialize it with emptry array
					$paid_array = [];
				}
				foreach ($paid_array as $paid_data) {
					$sno++;
				?>
					<tr>
						<td><?=$sno;?></td>
						<td><?=$paid_data['metadata']['Timestamp'];?></td>
						<td><?=$paid_data['metadata']['Campaign #'];?></td>
						<td><?=$paid_data['metadata']['Campaign Title'];?></td>
						<td><?=$paid_data['metadata']['Participant ID'];?></td>
						<td><?=$paid_data['metadata']['Participant First Name'];?></td>
						<td><?=$paid_data['metadata']['Customer First Name'];?></td>
						<td><?=$paid_data['metadata']['Customer Last Name'];?></td>
						<td><?=$paid_data['metadata']['Customer Email'];?></td>
						<td><? if ($paid_data['captured'] == 1) {echo "Yes";} else {echo "No";}?></td>
						<td>$ <?=number_format(($paid_data['amount']/100),2);?></td>
						<td><?=$paid_data['id'];?></td>
						<td><?=$paid_data['destination'];?></td>
					</tr>
				<? } ?>
                </tbody>
			</table>
			
			<h2 align="center">Missing Refunds Only without disputes</h2>
          	<table id="example2" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>S/No.</th>
						<th>Timestamp</th>
						<th>Campaign #</th>
						<th>Campaign Title</th>
						<th>Participant Id</th>
						<th>Participant Full Name</th>
						<th>Customer First Name</th>
						<th>Customer Last Name</th>
						<th>Customer Email</th>
						<th>Captured</th>
						<th>Amount</th>
						<th>Charge ID</th>
						<th>Refund Amount</th>
						<th>Refund ID</th>
					</tr>
                </thead>
                <tbody>
				<?
				$sno = 0;
				// echo "refund_array:<pre>"; echo var_dump($refund_array);die();
				if($refund_array == ""){//if its not an array, so before using it initialize it with emptry array
					$refund_array = [];
				}
				foreach ($refund_array as $refund_data) {
					$sno++;
				?>
					<tr>
						<td><?=$sno;?></td>
						<td><?=$refund_data['metadata']['Timestamp'];?></td>
						<td><?=$refund_data['metadata']['Campaign #'];?></td>
						<td><?=$refund_data['metadata']['Campaign Title'];?></td>
						<td><?=$refund_data['metadata']['Participant ID'];?></td>
						<td><?=$refund_data['metadata']['Participant First Name'];?></td>
						<td><?=$refund_data['metadata']['Customer First Name'];?></td>
						<td><?=$refund_data['metadata']['Customer Last Name'];?></td>
						<td><?=$refund_data['metadata']['Customer Email'];?></td>
						<td><? if ($refund_data['captured'] == 1) {echo "Yes";} else {echo "No";}?></td>
						<td>$ <?=number_format(($refund_data['amount']/100),2);?></td>
						<td><?=$refund_data['id'];?></td>
						<td>$ <?=number_format(($refund_data['amount_refunded']/100),2);?></td>
						<td><?=$refund_data['refunds']['data']['0']['id'];?></td>
					</tr>
				<? } ?>
                </tbody>
			</table>
			
			<h2 align="center">Missing Disputes Only</h2>
          	<table id="example3" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>S/No.</th>
						<th>Timestamp</th>
						<th>Campaign #</th>
						<th>Campaign Title</th>
						<th>Participant Id</th>
						<th>Participant Full Name</th>
						<th>Customer First Name</th>
						<th>Customer Last Name</th>
						<th>Customer Email</th>
						<th>Captured</th>
						<th>Amount</th>
						<th>Charge ID</th>
						<th>Dispute Amount</th>
						<th>Dispute ID</th>
					</tr>
                </thead>
                <tbody>
				<?
				$sno = 0;
				// echo "dispute_array:<pre>"; echo var_dump($dispute_array);die();
				if($dispute_array == ""){//if its not an array, so before using it initialize it with emptry array
					$dispute_array = [];
				}
				foreach ($dispute_array as $dispute_data) {
					$sno++;
				?>
					<tr>
						<td><?=$sno;?></td>
						<td><?=$dispute_data['metadata']['Timestamp'];?></td>
						<td><?=$dispute_data['metadata']['Campaign #'];?></td>
						<td><?=$dispute_data['metadata']['Campaign Title'];?></td>
						<td><?=$dispute_data['metadata']['Participant ID'];?></td>
						<td><?=$dispute_data['metadata']['Participant First Name'];?></td>
						<td><?=$dispute_data['metadata']['Customer First Name'];?></td>
						<td><?=$dispute_data['metadata']['Customer Last Name'];?></td>
						<td><?=$dispute_data['metadata']['Customer Email'];?></td>
						<td><? if ($dispute_data['captured'] == 1) {echo "Yes";} else {echo "No";}?></td>
						<td>$ <?=number_format(($dispute_data['amount']/100),2);?></td>
						<td><?=$dispute_data['id'];?></td>
						<td>$ <?=number_format(($dispute_data['dispute']['amount']/100),2);?></td>
						<td><?=$dispute_data['dispute']['id'];?></td>
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
<script src="//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script>
$(document).ready(function(){
$('.reconciliationscript').on('click', function() {
	swal({
		title: 'Donation Reconciliation<br>Are you sure?',
		text: "Execution of this procedure will load all missing donations to Ufund database",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Execute',
		cancelButtonText: 'Cancel',
		confirmButtonClass: 'btn btn-success',
		cancelButtonClass: 'btn btn-danger',
		buttonsStyling: false
	}).then(function () {
		swal(
			'Donation Reconciliation from stripe',
			'Please wait...it will takes some minutes...!<br>New tab will be open for reconciliation...!',
			'success'
		);
		//$('#initialize').click();
		window.open('../automation_donation_reconciliation.php', '_blank');
	}, function (dismiss) {
		// dismiss can be 'cancel', 'overlay',
		// 'close', and 'timer'
		if (dismiss === 'cancel') {
			swal(
				'Cancelled',
				'Reconciliation has been cancelled...!',
				'error'
			)
		}
	});
});
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
				orientation: 'landscape',
				pageSize: 'LEGAL',
				className: 'fixed'
			}
		],
		"scrollY": '50vh',
		"scrollX": true,
		"autoWidth": false,
		"columnDefs": [
			{ "width": "60px", "targets": 0 },   //S.No
			{ "width": "100px", "targets": 1 },   //Timestamp
			{ "width": "80px", "targets": 2 },   //Campaign #
			{ "width": "250px", "targets": 3 },   //Campaign Title
			{ "width": "80px", "targets": 4 },   //Participant Id
			{ "width": "120px", "targets": 5 },   //Participant Full Name
			{ "width": "120px", "targets": 6 },    //Customer First Name
			{ "width": "120px", "targets": 7 },   //Customer Last Name
			{ "width": "200px", "targets": 8 },   //Customer Email
			{ "width": "80px", "targets": 9 },   //Captured
			{ "width": "100px", "targets": 10 },   //Amount
			{ "width": "250px", "targets": 11 },   //Charge ID
			{ "width": "180px", "targets": 12 },   //Destination Account
		],
		"order": [[ 0, "asc" ]]
	});
	var table = $('#example2').DataTable({
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
				orientation: 'landscape',
				pageSize: 'LEGAL',
				className: 'fixed'
			}
		],
		"scrollY": '50vh',
		"scrollX": true,
		"autoWidth": false,
		"columnDefs": [
			{ "width": "60px", "targets": 0 },   //S.No
			{ "width": "100px", "targets": 1 },   //Timestamp
			{ "width": "80px", "targets": 2 },   //Campaign #
			{ "width": "250px", "targets": 3 },   //Campaign Title
			{ "width": "80px", "targets": 4 },   //Participant Id
			{ "width": "120px", "targets": 5 },   //Participant Full Name
			{ "width": "120px", "targets": 6 },    //Customer First Name
			{ "width": "120px", "targets": 7 },   //Customer Last Name
			{ "width": "200px", "targets": 8 },   //Customer Email
			{ "width": "80px", "targets": 9 },   //Captured
			{ "width": "100px", "targets": 10 },   //Amount
			{ "width": "250px", "targets": 11 },   //Charge ID
			{ "width": "100px", "targets": 12 },   //Refund Amount
			{ "width": "250px", "targets": 13 },   //Refund ID
		],
		"order": [[ 0, "asc" ]]
	});
	var table = $('#example3').DataTable({
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
				orientation: 'landscape',
				pageSize: 'LEGAL',
				className: 'fixed'
			}
		],
		"scrollY": '50vh',
		"scrollX": true,
		"autoWidth": false,
		"columnDefs": [
			{ "width": "60px", "targets": 0 },   //S.No
			{ "width": "100px", "targets": 1 },   //Timestamp
			{ "width": "80px", "targets": 2 },   //Campaign #
			{ "width": "250px", "targets": 3 },   //Campaign Title
			{ "width": "80px", "targets": 4 },   //Participant Id
			{ "width": "120px", "targets": 5 },   //Participant Full Name
			{ "width": "120px", "targets": 6 },    //Customer First Name
			{ "width": "120px", "targets": 7 },   //Customer Last Name
			{ "width": "200px", "targets": 8 },   //Customer Email
			{ "width": "80px", "targets": 9 },   //Captured
			{ "width": "100px", "targets": 10 },   //Amount
			{ "width": "250px", "targets": 11 },   //Charge ID
			{ "width": "100px", "targets": 12 },   //Dispute Amount
			{ "width": "250px", "targets": 13 },   //Dispute ID
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
	$('#example2 tbody').on( 'click', 'tr', function () {
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
	$('#example3 tbody').on( 'click', 'tr', function () {
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
<script src="bower_components/sweetalert2/sweetalert2.min.js"></script>
<script type="text/javascript">
   $('#notifications').delay(3000).fadeOut('slow');
</script>
</body>
</html>
<?  ?>
<? include_once('bottom.php');?>