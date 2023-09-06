<?
require_once("../configuration/dbconfig.php");

$REQUEST = &$_REQUEST;	
//Declare variable required bellow
$REQUEST['m'] = isset($REQUEST['m']) ? $REQUEST['m'] : '';
$REQUEST['msg'] = isset($REQUEST['msg']) ? $REQUEST['msg'] : '';
if( !isset($aMessage[$REQUEST['msg']]) ){
	$aMessage[$REQUEST['msg']] = '';
}

if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else { 
	if ($_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5) {
		$oregister->redirect('dashboard.php');
	}
}
$sPageName = '<li>Unsubscribe</li>';
$sUnsubscribedLink = 'style="color:#F3BE00"';
$sLeftMenuUnsubscribed = 'active';
$getuserid = $_SESSION['uid'];
$getrid = $_SESSION['role_id'];

/*if(isset($REQUEST['m']) && $REQUEST['m'] == 'edit' and $REQUEST['id'] > 0)
{
	$sStatus = $REQUEST['s'];
	$iId = $REQUEST['id'];
	
	$oregister->update_user_status($sStatus,$iId);
	$oregister->redirect('unsubscribe.php?msg=5');	
}else if(isset($REQUEST['m']) && $REQUEST['m'] == 'del' and $REQUEST['id'] > 0)
{
	$iId = $REQUEST['id'];	
	$oregister->delete_user($iId);
	$oregister->redirect('unsubscribe.php?msg=6');	
}*/

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
<title>Admin<?php echo sWEBSITENAME;?> - Unsubscribe</title>
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
    padding: 5px 8px !important;
}

#example1_wrapper .table.dataTable tbody tr.selected {
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
		  <?
			if ($REQUEST['m'] == 'participants') {
				$gethead = 'Participants';
				$module = 1;
			} elseif ($REQUEST['m'] == 'donors') {
				$gethead = 'Donors';
				$module = 2;
			} else {
				$gethead = 'Donors';
				$module = 3;
			}
			
		  ?>
		  <h1 class="h1styling"><?=$gethead;?> Unsubscribe</h1>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box white">
          	 <?
			if(isset($REQUEST['m']) && $REQUEST['m'] && isset($REQUEST['msg']) && $REQUEST['msg']!= ""){
			?>
			<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><?=$aMessage[$REQUEST['msg']]?>
			</div>
			<? }?>
             
			 <?
			 if ($module == 2) {
             ?>
				<table id="example1" class="table table-striped table-bordered">
                <thead>
                <tr>
                  <th>Donor's Name</th>
                  <th>Donor's Email</th>
                  <th>Participant's Name</th>
                  <th>Participant's Email</th>
                  <th>Campaign's ID</th>
                  <th>Campaign's Manager Name</th>
                  <th>Campaign Title</th>
                  <th>Unsubscribe Date</th>
                  <th>Action</th>
                </tr>
                </thead>
                </table>
				<? } elseif ($module == 1) { 
				?>
				<table id="example1" class="table table-striped table-bordered">
                <thead>
                <tr>
                  <th>Participant's Name</th>
                  <th>Participant's Email</th>
                  <th>Campaign's ID</th>
                  <th>Campaign's Manager Name</th>
                  <th>Campaign Title</th>
                  <th>Unsubscribe Date</th>
                  <th>Action</th>
                </tr>
                </thead>
                </table>
				<? } else { ?>
				<table id="example1" class="table table-striped table-bordered">
                <thead>
                <tr>
                  <th>Donor's Name</th>
                  <th>Donor's Email</th>
                  <th>Participant's Name</th>
                  <th>Participant's Email</th>
                  <th>Campaign's ID</th>
                  <th>Campaign's Manager Name</th>
                  <th>Campaign Title</th>
                  <th>Unsubscribe Date</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                </table>
				<? } ?>
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
<script>
    $(document).ready(function(){
      var mod = <?=$module;?>;
	  if (mod == 2) {
		var table = $('#example1').DataTable({
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "unsubscribe_ajax.php?module=<?=$module;?>&uid=<?=$getuserid;?>&rid=<?=$getrid;?>",
			"scrollY": '50vh',
			"scrollX": true,
			"autoWidth": false,
			"columnDefs": [
				{ "width": "200px", "targets": 0 },   //Donors Name
				{ "width": "250px", "targets": 1 },   //Donors Email
				{ "width": "200px", "targets": 2 },  //Participant Name
				{ "width": "250px", "targets": 3 },   //Participant Email
				{ "width": "120px", "targets": 4 },   //Campaign ID
				{ "width": "200px", "targets": 5 },   //Campaign Manager Name
				{ "width": "300px", "targets": 6 },  //Campaign Title
				{ "width": "150px", "targets": 7 },  //Unsubscribe Date
				{ "width": "50px", "targets": 8 },  //Action
			],  
		 
		});
	  } else if (mod == 1) {
		var table = $('#example1').DataTable({
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "unsubscribe_ajax.php?module=<?=$module;?>&uid=<?=$getuserid;?>&rid=<?=$getrid;?>",
			"scrollY": '50vh',
			"scrollX": true,
			"autoWidth": false,
			"columnDefs": [
				{ "width": "200px", "targets": 0 },  //Participant Name
				{ "width": "250px", "targets": 1 },   //Participant Email
				{ "width": "120px", "targets": 2 },   //Campaign ID
				{ "width": "200px", "targets": 3 },   //Campaign Manager Name
				{ "width": "300px", "targets": 4 },  //Campaign Title
				{ "width": "150px", "targets": 5 },  //Unsubscribe Date
				{ "width": "50px", "targets": 6 },  //Action
			],  
		 
		});
	  } else {
		var table = $('#example1').DataTable({
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "unsubscribe_ajax.php?module=<?=$module;?>&uid=<?=$getuserid;?>&rid=<?=$getrid;?>",
			"scrollY": '50vh',
			"scrollX": true,
			"autoWidth": false,
			language: {
				processing: "<img src='images/loading-spinner-blue.gif'> Loading...",
			},
			"columnDefs": [
				{ "width": "200px", "targets": 0 },   //Donors Name
				{ "width": "250px", "targets": 1 },   //Donors Email
				{ "width": "200px", "targets": 2 },  //Participant Name
				{ "width": "250px", "targets": 3 },   //Participant Email
				{ "width": "120px", "targets": 4 },   //Campaign ID
				{ "width": "200px", "targets": 5 },   //Campaign Manager Name
				{ "width": "300px", "targets": 6 },  //Campaign Title
				{ "width": "150px", "targets": 7 },  //Unsubscribe Date
				{ "width": "50px", "targets": 8 },  //Action
			],  
		 
		});
	  }
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
	setTimeout(function(){ 
		$('<div class="newuser_btn" style="width:152px !important;float:right; margin:-10px 0 0 20px "><a href="unsubscribe.php?m=participants"><button class="btn btn-block btn-primary" style="width:150px; margin-top:10px;"><span class="newtext">Particpants</span></button></a></div><div class="newuser_btn" style="width:152px !important;float:right; margin:-10px 0 0 20px "><a href="unsubscribe.php?m=donors"><button class="btn btn-block btn-primary" style="width:150px; margin-top:10px;"><span class="newtext">Donors</span></button></a></div>').appendTo('div.dataTables_filter');
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

<script type="text/javascript">
   $('#notifications').delay(3000).fadeOut('slow');
</script>
</body>
</html>
<? include_once('bottom.php');?>