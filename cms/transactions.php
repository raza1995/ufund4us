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
$sPageName = '<li>Transactions</li>';
$sTransactionLink = 'style="color:#F3BE00"';
$sLeftMenuTransactions = 'active';

if(isset($_GET['m']) && $_GET['m'] == 'edit' and $_GET['id'] > 0)
{
	$sStatus = $_GET['s'];
	$iId = $_GET['id'];
	
	$oregister->update_user_status($sStatus,$iId);
	$oregister->redirect('transactions.php?msg=5');	
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
<title>Admin<?php echo sWEBSITENAME;?> - Transactions</title>
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
		  <h1 class="h1styling">Transactions</h1>
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
             $sUsersData = $oregister->gettransactions();
			 $iCountRecords = count($sUsersData);
			 //if($iCountRecords>0){
			 ?>
				<table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Transaction #</th>
                  <th>Campaign Title</th>
                  <th>Requested By</th>
                  <th>Requested Role</th>
                  <th>Requested Amount</th>
                  <th>Requested Date</th>
                  <th>Status</th>
				  <th>Paid By</th>
				  <th>Payment Method</th>
				  <th>Paid Date</th>
				  <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?
                for($i=0;$i<$iCountRecords;$i++){
					if ($sUsersData[$i]['ispaid'] == 1) {
					  $paidstatus = 'Paid';
					} elseif ($sUsersData[$i]['ispaid'] == 2) {
					  $paidstatus = 'Rejected';
					} else {
					  $paidstatus = 'Pending';
					}
					if ($sUsersData[$i]['paid_date'] = '00/00/0000') {
					  $paiddate = '';
					} else {
					  $paiddate = $sUsersData[$i]['paid_date'];
					}
				?>
                <tr>
                  <td><?=str_pad($sUsersData[$i]['id'], 10, "0", STR_PAD_LEFT);?></td>
                  <td><?=$sUsersData[$i]['fld_campaign_title'];?></td>
                  <td><?=$sUsersData[$i]['requested_by_name'];?></td>
                  <td><?=$sUsersData[$i]['fld_role'];?></td>
                  <td><?=$sUsersData[$i]['request_amount'];?></td>
                  <td><?=$sUsersData[$i]['request_date'];?></td>
				  <td><?=$paidstatus;?></td>
                  <td><?=$sUsersData[$i]['paid_by_name'];?></td>
                  <td><?=$sUsersData[$i]['payment_method'];?></td>
                  <td><?=$paiddate;?></td>
                  <td align="left">
                  <a href="edit_transactions.php?tid=<?=$sUsersData[$i]['id']?>&m=edit" style="margin-right:10px;" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Transaction"><span class="glyphicon glyphicon-pencil"></span></a> 
                  </td>
                </tr>
                <? }?>
                </tbody>
                </table>
				<div class="formodal"></div>
                <script>
			  </script>
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
      $('#example1').DataTable();
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
<? include_once('bottom.php');?>