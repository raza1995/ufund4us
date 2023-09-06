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
$sPageName = '<li>Dashboard</li>';
$sLeftMenuHierarchy = 'active';

if(isset($_GET['m']) && $_GET['m'] == 'del' and $_GET['id'] > 0)
{
	$iId = $_GET['id'];	
	//$chkrecords = $oregister->chk_user($iId);
	$chkrecords2 = $oregister->chk_hierarchy($iId);
	$iCountRecords2 = count($chkrecords2);
	if ($iCountRecords2 > 0) {
		$oregister->redirect('hierarchy_screen.php?msg=7');
		echo "not delete";
	} else {
		$oregister->delete_selected_user($iId);
		$oregister->redirect('hierarchy_screen.php?msg=6');	
	}
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
<title>Admin<?php echo sWEBSITENAME;?> - Hierarchy Screen</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<link href="bower_components/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
<link href="bower_components/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">
<style>
.sweet-alert { margin: auto; transform: translateX(-50%); }
.sweet-alert.sweetalert-lg { width: 600px; }
.swal-wide{
    width:850px !important;
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
		  <h1 class="h1styling">Hierarchy Screen</h1>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box">
			 <?
			if(isset($_GET['m']) && $_GET['m']){
			?>
			<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><?=$aMessage[$_GET['msg']]?>
			</div>
			<? }?>
             <?
			 if ($_SESSION['role_id'] == 1) {
				$getuserid = 1;
				$getrid = 1;
			 } else {
				$getuserid = $_SESSION['uid'];
				$getrid = $_SESSION['role_id'];
			 }
			 if (!empty($_GET['nodeid'])) {
				$getuserid = base64_decode($_GET['nodeid']);
			 }
			 if (!empty($_GET['rid'])) {
				$getrid = base64_decode($_GET['rid']);
			 }
            //if($iCountRecords>0){
			 ?>
				<table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>S/No.</th>
                  <th>Name</th>
				  <th>Company Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Role</th>
                  <th>Actions</th>
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
<script>
    $(document).ready(function(){
		var table = $('#example1').DataTable({ 
		 "bProcessing": true,
		 "bServerSide": true,
		 "sAjaxSource": "hierarchy_screen_ajax.php?uid=<?=$getuserid;?>&rid=<?=$getrid;?>&module=<?=$_SESSION['role_id'];?>",
		 "scrollY": '50vh',
		 "scrollX": true,
		 "autoWidth": false,
		 language: {
			processing: "<img src='images/loading-spinner-blue.gif'> Loading...",
		 },
		 "columnDefs": [
			{ "width": "40px", "targets": 0 },   //S/NO
			{ "width": "150px", "targets": 1 },   //Name
			{ "width": "150px", "targets": 2 },   //Company Name
			{ "width": "150px", "targets": 3 },   //Email
			{ "width": "80px", "targets": 4 },  //Phone
			{ "width": "90px", "targets": 5 },   //Role
			{ "width": "50px", "targets": 6 }   //Action
		 ],
	  });
	  setTimeout(function(){
		$('#example1').on('click', '.information', function(){
			var uid = $(this).attr('value');
			var uname = $(this).attr('value2');
			//alert('aaa');
			$.post('participantdetails.php', 'pid=' + uid +'&act=5', function (response) {
			  var jdata = JSON.parse(response);
			  var leaguedetails = ''+jdata.leaguedetails+'';
			  var row1 = leaguedetails.split(",");
			  $('.formodal').empty();
			  var modallarge = '<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">';
			  modallarge += '<div class="modal-dialog modal-lg">';
			  modallarge += '<div class="modal-content">';
              modallarge += '<div class="modal-header">';
              modallarge += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
              modallarge += '<h4 class="modal-title" id="mySmallModalLabel">'+uname+'</h4>';
              modallarge += '</div>';
              modallarge += '<div class="modal-body">';
			  modallarge += '<table class="tab1 table table-bordered">';
			  modallarge += '<thead>';
			  modallarge += '<tr>';
			  modallarge += '<th style="font-size:13px !important; width:8%; text-align:left;">Campaign #</th>';
			  modallarge += '<th style="font-size:13px !important; width:25%; text-align:left;">Campaign Title</th>';
			  modallarge += '<th style="font-size:13px !important; width:22%; text-align:left;">Campaign Admin.</th>';
			  modallarge += '<th style="font-size:13px !important; width:10%; text-align:left;">Start Date</th>';
			  modallarge += '<th style="font-size:13px !important; width:10%; text-align:left;">End Date</th>';
			  modallarge += '<th style="font-size:13px !important; width:25%; text-align:left;">Company Name</th>';
			  modallarge += '</tr>';
			  modallarge += '</thead>';
			  modallarge += '<tbody>';
			  if (jdata.counter > 0) {
				for(var i = 0; i < row1.length; i++) {
				  var row2 = row1[i].split("|");
				  modallarge += '<tr>';
				  modallarge += '<td style="font-size:13px !important; width:8%; text-align:left;">'+row2[0]+'</td>';
				  modallarge += '<td style="font-size:13px !important; width:25%; text-align:left;">'+row2[1]+'</td>';
				  modallarge += '<td style="font-size:13px !important; width:22%; text-align:left;">'+row2[2]+'</td>';
				  modallarge += '<td style="font-size:13px !important; width:10%; text-align:left;">'+row2[3]+'</td>';
				  modallarge += '<td style="font-size:13px !important; width:10%; text-align:left;">'+row2[4]+'</td>';
				  modallarge += '<td style="font-size:13px !important; width:25%; text-align:left;">'+row2[5]+'</td>';
				  modallarge += '</tr>';
				}
			  } else {
				modallarge += '<tr>';
				modallarge += '<td colspan="6" align="center">No any information yet...!</td>';
				modallarge += '</tr>';
			  }
				modallarge += '</tbody>';
				modallarge += '</table>';
				modallarge += '</div>';
				modallarge += '<div class="modal-footer">';
				modallarge += '<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
				modallarge += '</div>';
				modallarge += '</div>';
				modallarge += '</div>';
				modallarge += '</div>';
				$('.formodal').append(modallarge);
				$(".bs-example-modal-lg").modal('show');
			});
		});
		$('#example1').on( 'click', '.loggedin', function () {
			var is_loggedin = $(this);
			swal({
				title: "Are you sure?",
				text: "BY CLICKING “AGREE” OR OTHERWISE CONTINUING YOUR USE, YOU CONFIRM THAT YOU ARE AN AUTHORIZED LICENSED USER OF <?php echo sWEBSITENAME;?> WHO HAS AGREED TO THE TERMS OF SERVICE AND THE PRIVACY POLICY* OF <?php echo sWEBSITENAME;?> AND THAT YOU ARE ENGAGING IN LICENSED AND AUTHORIZED USE ONLY; ANY UNAUTHORIZED OR UNLICENSED USE VIOLATES THE TERMS OF SERVICE AND IS PROHIBITED BY APPLICABLE FEDERAL AND STATE LAW. BY CONTINUING, YOU ARE BOUND BY THE TERMS OF SERVICE AND PRIVACY POLICY OF <?php echo sWEBSITENAME;?> AND YOUR USE IS I ACCORDANCE WITH THE FOREGOING.   ALL OTHER RIGHTS ARE RESERVED BY <?php echo sWEBSITENAME;?>.",
				type: "warning",
				customClass: 'sweetalert-lg',
				showCancelButton: true,
				confirmButtonClass: "btn-danger",
				confirmButtonText: "Yes",
				closeOnConfirm: false
			},
			function(){
				//swal("Deleted!", "Your imaginary file has been deleted.", "success");
				is_loggedin.closest('form').submit();
				swal.close();
				console.log("close swal");		    
			    $('.sweet-overlay').hide();
			    $('.showSweetAlert').hide();
				//$()
			});
		
		});
	  
	  }, 2000);
	  
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
  </script>
  <script type="text/javascript">
    function getval(sel) {
	    e = $.Event('keyup');
		e.keyCode= 13; // enter
		var roles = $("#roles option:selected").text();
		if (roles == 'Select Roles') {
			var selected = '';
		} else {
			var selected = roles;
		}
		var filtered = $('#example1_filter input').val(selected);
		filtered.trigger(e);
    }
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
<? include_once('bottom.php');?>