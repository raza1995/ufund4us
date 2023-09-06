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

$sPageName = '<li>Manage Users</li>';

$sLeftMenuUsers = 'active';

if(isset($_GET['m']) && $_GET['m'] == 'edit' and $_GET['id'] > 0)

{

	$sStatus = $_GET['s'];

	$iId = $_GET['id'];

	

	$oregister->update_user_status($sStatus,$iId);

	$oregister->redirect('users.php?msg=5');	

}else if(isset($_GET['m']) && $_GET['m'] == 'del' and $_GET['id'] > 0)

{

	$iId = $_GET['id'];	

	$oregister->delete_user($iId);

	$oregister->redirect('users.php?msg=6');	

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

<title>Admin<?php echo sWEBSITENAME;?> - Manage Users</title>

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

		  <!-- .white-box -->

		  <h1 class="h1styling">Manage Users</h1>

		  <div class="line3"></div>

          <div class="white-box white">

          	 <?

			if(isset($_GET['m']) && $_GET['m']){

			?>

			<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">

              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><?=$aMessage[$_GET['msg']]?>

			</div>

			<? }?>

				<table id="example1" class="table table-bordered table-striped">

                <thead>

                <tr>

                  <th>Status</th>

                  <th>Name</th>

                  <th>Email</th>

				  <th>Company/Brand Name</th>
                  <th>Phone</th>

                  <th>Role</th>

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

<script>

    $(document).ready(function(){
      var table = $('#example1').DataTable({
		 "bProcessing": true,
		 "bServerSide": true,
		 "sAjaxSource": "users_ajax.php?rid=<?=$_SESSION['role_id'];?>&uid=<?=$_SESSION['uid'];?>",
		 "scrollY": '50vh',
         "scrollX": true,
		 "autoWidth": false,
		 language: {
			processing: "<img src='images/loading-spinner-blue.gif'> Loading...",
		 },
		 "columnDefs": [
			{ "width": "40px", "targets": 0 },   //Status
			{ "width": "150px", "targets": 1 },   //Name
			{ "width": "150px", "targets": 2 },  //Email
			{ "width": "100px", "targets": 3 },   //Company Name
			{ "width": "50px", "targets": 4 },   //Phone
			{ "width": "70px", "targets": 5 },   //Role
			{ "width": "70px", "targets": 6 },  //Action
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
	<?php if ($_SESSION['role_id'] == 1) { ?>
	setTimeout(function(){ 
		$('<div class="newuser_btn" style="width:152px !important;float:right; margin:-10px 0 0 20px "><a href="edit_user.php?m=add"><button class="btn btn-block btn-primary" style="width:150px; margin-top:10px;"><span class="fa fa-plus"></span> <span class="newtext">New User</span></button></a></div>').appendTo('div.dataTables_filter');
	}, 1000);

	<?php } ?>
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