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
$sStripeReport = 'style=""';
$get_sess = session_id();
//$sLeftMenuReport = 'active';
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
<title>Admin<?php echo sWEBSITENAME;?> - Stripe/<?php echo sWEBSITENAME;?> Accounts Report</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<link href="bower_components/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
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
		  <h1 class="h1styling">Stripe/<?php echo sWEBSITENAME;?> Accounts Report</h1>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box white">
			<h2 align="center">Stripe Connected Accounts</h2>
          	<table id="example1" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>S/No.</th>
						<th>Timestamp</th>
						<th>Campaign #</th>
						<th>Campaign Title</th>
						<th>Closed Date</th>
						<th># of Donations</th>
						<th>Available Amount</th>
						<th>Pending Amount</th>
						<th>Needed Information</th>
						<th>Account #</th>
						<th>Direct Deposit</th>
						<th>Organization Name</th>
						<th>Make Check Payable To</th>
						<th>Check Will be Emailed To</th>
						<th>Action</th>
					</tr>
                </thead>
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
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "report_accounts_ajax.php?uid=<?=$get_sess;?>",
		// "scrollY": '50vh',
  //       "scrollX": true,
		"autoWidth": false,
		language: {
			processing: "<img src='images/loading-spinner-blue.gif'> Loading...",
		},
		"columnDefs": [
			{ "width": "60px", "targets": 0 },   //S.No
			{ "width": "100px", "targets": 1 },   //Timestamp
			{ "width": "100px", "targets": 2 },   //Campaign #
			{ "width": "250px", "targets": 3 },   //Campaign Title
			{ "width": "100px", "targets": 4 },   //Closed Date
			{ "width": "150px", "targets": 5 },   //# of Donations
			{ "width": "150px", "targets": 6 },    //Available Amount
			{ "width": "120px", "targets": 7 },    //Pending Amount
			{ "width": "150px", "targets": 8 },   //Needed Information
			{ "width": "120px", "targets": 9 },   //Account ID
			{ "width": "150px", "targets": 10 },   //Direct Deposit
			{ "width": "200px", "targets": 11 },   //Organization Name
			{ "width": "200px", "targets": 12 },   //Make Check Payable To
			{ "width": "200px", "targets": 13 },   //Check Will be Emailed To
			{ "width": "100px", "targets": 14 },   //Action
		],
		"order": [[ 0, "desc" ]]
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
	
	$('#example1 tbody').on( 'click', '.deleteacct', function () {
		var acctid = $(this).attr('value');
		var tr = $(this).parent().parent();
		var textlive = "You are going to delete Account ID: "+acctid+" ?";
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
			swal({title: "Please Wait...!",   text: "Checking Account, Processing...", type: "success", timer: timer1, showConfirmButton: false });
			$.post('acct_manage.php', 'acctid='+acctid+'', function (response) {
				var jdata = JSON.parse(response);
				//var participantview = ''+jdata.participantview+'';
				if (jdata.status == 1) {
					tr.remove();
					/*$("#example1").dataTable().fnDestroy();
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
							{ "width": "80px", "targets": 4 },   //Closed Date
							{ "width": "100px", "targets": 5 },   //# of Donations
							{ "width": "120px", "targets": 6 },    //Available Amount
							{ "width": "120px", "targets": 7 },    //Pending Amount
							{ "width": "150px", "targets": 8 },   //Needed Information
							{ "width": "120px", "targets": 9 },   //Account ID
							{ "width": "100px", "targets": 10 },   //Direct Deposit
							{ "width": "200px", "targets": 11 },   //Organization Name
							{ "width": "150px", "targets": 12 },   //Make Check Payable To
							{ "width": "150px", "targets": 13 },   //Check Will be Emailed To
							{ "width": "100px", "targets": 14 },   //Action
						],
						"order": [[ 0, "asc" ]]
					});*/
					swal({title: "Success...!",   text: "Stripe Account ID ("+acctid+") has been deleted from stripe server", type: "success" });
				} else {
					swal({title: "Error...!",   text: "Stripe Account ID ("+acctid+") not deleted from stripe server", type: "warning" });
				}
			});
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
<script src="bower_components/sweetalert/sweetalert.min.js"></script>
<script type="text/javascript">
   $('#notifications').delay(3000).fadeOut('slow');
</script>
</body>
</html>
<?  ?>
<? include_once('bottom.php');?>
<?php mysqli_close($conn1); ?>