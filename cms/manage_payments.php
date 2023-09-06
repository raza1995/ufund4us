<?
require_once("../configuration/dbconfig.php");
require('../lib/init.php'); //Library for Stripe Merchant Account
$REQUEST = &$_REQUEST;
//Declare variable required bellow
$REQUEST['module'] = $module = isset($REQUEST['module']) ? $REQUEST['module'] : "";

if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else { 
	if ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5 || $_SESSION['role_id'] == 6) {
		$oregister->redirect('dashboard.php');
	}
}
$sPageName = '<li>Manage Payment</li>';
$sCampaignLink = 'style="color:#F3BE00"';
$sLeftMenuPayments = 'active';

if (array_key_exists('refundsubmit', $REQUEST)) {
	\Stripe\Stripe::setApiKey(STRIPE_API_KEY); //Initialize Stripe Gateway
	$current_date = date('m/d/Y h:i:s A'); //Current Date e.g 09/29/2016 01:20:00 AM
	
	$dname = $REQUEST['dname']; //Donors Full Name
	$pname = $REQUEST['pname']; //Participants Full Name
	$amountreq = $REQUEST['amountreq']; //Amount Request
	$donateamount = str_replace(',', '', $amountreq); //Amount Donated
	$amount = str_replace(".", "", $donateamount); //Amount Donate without point e.g 12.00 > 1200
	$tid = $REQUEST['tid']; //Transaction #
	$reason = $REQUEST['reason']; //Reason for refund
	
	
	$cmname = $REQUEST['cmname']; //Campaign Manager Name
	$ctitle = $REQUEST['ctitle']; //Campaign Title
	$cno = $REQUEST['cno']; //Campaign #
	$cac = $REQUEST['cac']; //Campaign Account #
	
	$cid = $REQUEST['cid']; //Campaign #
	$pid = $REQUEST['pid']; //Participants ID
	$did = $REQUEST['did']; //Donors ID
	$demail = $REQUEST['demail']; //Donors Email
	
	if ($REQUEST['rdmode'] == 'refund') {
		try 
		{
			$refund = \Stripe\Refund::create(array(
				"charge" => $tid,
				"amount" => $amount,
				"metadata" => array(
					"Campaign #" => $cid, 
					"Campaign Title" => $ctitle, 
					"Customer ID" => $did, 
					"Customer Email" => $demail, 
					"Customer Full Name" => $dname, 
					"Participant ID" => $pid, 
					"Participant Full Name" => $pname, 
					"Amount" => "$ ".$amountreq,
					"Reason" => $reason,
					"Timestamp" => $current_date
				),
				"reason" => "requested_by_customer",
				"refund_application_fee" => true,
				"reverse_transfer" => true
			));
			$refund_array = $refund->__toArray(true);
			$refund_transaction_id = $refund_array['id'];
			if ($oCampaign->refund_process($dname,$pname,$amountreq,$tid,$reason,$cmname,$ctitle,$content,$cac,$cid,$pid,$did,$demail,$refund_transaction_id))
			{
				$oregister->redirect('manage_payments.php?msg=13'); //Success with notification
			} else {
				$oregister->redirect('manage_payments.php?msg=14'); //Fail with notification
			}
		} catch(Stripe_CardError $e) {
			// Invalid card entered
			$errortype = 2;
			$oregister->redirect('manage_payments.php?msg=15'); //Fail with notification
		} catch (Stripe_InvalidRequestError $e) {
			// Invalid parameters were supplied to Stripe's API
			$errortype = 3;
			$oregister->redirect('manage_payments.php?msg=18'); //Fail with notification
		} catch (Stripe_AuthenticationError $e) {
			// Authentication with Stripe's API failed
			$errortype = 3;
			$oregister->redirect('manage_payments.php?msg=17'); //Fail with notification
		} catch (Stripe_ApiConnectionError $e) {
			// Network communication with Stripe failed
			$errortype = 3;
			$oregister->redirect('manage_payments.php?msg=16'); //Fail with notification
		} catch (Stripe_Error $e) {
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$errortype = 3;
			$oregister->redirect('manage_payments.php?msg=15'); //Fail with notification
		} catch (Exception $e) {
			// Something else happened, completely unrelated to Stripe
			$errortype = 3;
			$oregister->redirect('manage_payments.php?msg=15'); //Fail with notification
		}
	} if ($REQUEST['rdmode'] == 'dispute') {
		try 
		{
			$dispute = \Stripe\Charge::retrieve($tid);
			$dispute_array = $dispute->__toArray(true);
			if ($dispute_array['dispute'] != null) {
				$dispute_transaction_id = $dispute_array['dispute']['id'];
			}
			if ($oCampaign->dispute_process($dname,$pname,$amountreq,$tid,$reason,$cmname,$ctitle,$content,$cac,$cid,$pid,$did,$demail,$dispute_transaction_id))
			{
				$oregister->redirect('manage_payments.php?msg=19'); //Success with notification
			} else {
				$oregister->redirect('manage_payments.php?msg=20'); //Fail with notification
			}
		} catch (Exception $e) {
			// Something else happened, completely unrelated to Stripe
			$errortype = 3;
			$oregister->redirect('manage_payments.php?msg=21'); //Fail with notification
		}
	} else {

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
<title>Admin<?php echo sWEBSITENAME;?> - Manage Payments</title>
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
		  <? if ($REQUEST['module'] == 'disputed') { ?>
		  <h1 class="h1styling">Disputed Donations</h1>
		  <? } elseif ($REQUEST['module'] == 'refunded') { ?>
		  <h1 class="h1styling">Refunded Donations</h1>
		  <? } elseif ($REQUEST['module'] == 'paid') { ?>
		  <h1 class="h1styling">Paid Donations</h1>
		  <? } else { ?>
		  <h1 class="h1styling">Manage Payments</h1>
		  <? } ?>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box" style="background: #fff;">
          	 <?
			if(isset($REQUEST['m']) && $REQUEST['m']){
			?>
			<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><?=$aMessage[$REQUEST['msg']]?>
			</div>
			<? }?>
			 <?
			 $uid = $_SESSION['uid'];
			 $rid = $_SESSION['role_id'];
			 $module = $REQUEST['module'];
			 if ($REQUEST['module'] == 'disputed') {
			 ?>
				<table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Status</th>
				  <th>Campaign #</th>
                  <th>Campaign Name</th>
                  <th>C.Manager Name</th>
                  <th>Donor's ID</th>
                  <th>Donor's Name</th>
                  <th>Donor's Email</th>
                  <th>Donation Amount</th>
                  <th>Card Number</th>
				  <th>Payment Through</th>
				  <th>Transaction ID</th>
				  <th>Dispute ID</th>
				  <th>Transaction Date</th>
				  <th>Participant's Name</th>
				  <th>Participant's Email</th>
                </tr>
                </thead>
                </table>
			 <?
			 } elseif ($REQUEST['module'] == 'refunded') {
			 ?>
				<table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Status</th>
				  <th>Campaign #</th>
                  <th>Campaign Name</th>
                  <th>C.Manager Name</th>
                  <th>Donor's ID</th>
                  <th>Donor's Name</th>
                  <th>Donor's Email</th>
                  <th>Donation Amount</th>
                  <th>Card Number</th>
				  <th>Payment Through</th>
				  <th>Transaction ID</th>
				  <th>Refunded ID</th>
				  <th>Transaction Date</th>
				  <th>Participant's Name</th>
				  <th>Participant's Email</th>
                </tr>
                </thead>
				</table>
			 <?
			 } elseif ($REQUEST['module'] == 'paid') {
				$modes = 1;
			 //if($iCountRecords>0){
			 ?>
				<table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Status</th>
				  <th>Campaign #</th>
                  <th>Campaign Name</th>
                  <th>C.Manager Name</th>
                  <th>Donor's ID</th>
                  <th>Donor's Name</th>
                  <th>Donor's Email</th>
                  <th>Donation Amount</th>
                  <th>Card Number</th>
				  <th>Payment Through</th>
				  <th>Transaction ID</th>
				  <th>Transaction Date</th>
				  <th>Participant's Name</th>
				  <th>Participant's Email</th>
                  <th>Action</th>
                </tr>
                </thead>
                </table>
			 <? } else {
			 //if($iCountRecords>0){
			 ?>
				<table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Status</th>
				  <th>Campaign #</th>
                  <th>Campaign Name</th>
                  <th>C.Manager Name</th>
                  <th>Donor's ID</th>
                  <th>Donor's Name</th>
                  <th>Donor's Email</th>
                  <th>Donation Amount</th>
                  <th>Card Number</th>
				  <th>Payment Through</th>
				  <th>Transaction ID</th>
				  <th>Transaction Date</th>
				  <th>Participant's Name</th>
				  <th>Participant's Email</th>
                  <th>Action</th>
                </tr>
                </thead>
                </table>
				<? } ?>
				
				<? if ($_SESSION['role_id'] == 1) { //Administrator ?>
				<form id="frmfastpay" name="frmfastpay" data-toggle="validator" method="post" action="">
				<div id="refund" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				  <div class="modal-dialog">
					<div class="modal-content">
					  <div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title camptitle" id="myModalLabel"></h4> <!-- Modal Heading Campaign Title -->
					  </div>
					  <div class="modal-body">
						<div class="submission">
						<!--
						<div class="form-group col-md-12">
						<label for="emailfrom">Email From<span style="color:#FF0000">*</span></label>
						<input type="textbox" class="form-control" id="emailfrom" name="emailfrom" placeholder="Enter Email From" required>
						<div class="help-block with-errors"></div>
					    </div>-->
						<div class="form-group col-md-6">
						  <div>Campaign Start Date:__________________</div>
						  <div id="forsdate" style="margin-left:60%; margin-top:-8%;"></div>
					    </div> 
					    <div class="form-group col-md-6">
						  <div>Campaign End Date:__________________</div>
						  <div id="foredate" style="margin-left:60%; margin-top:-8%;"></div>
					    </div>
					    <div class="clearfix"></div>
						<div class="form-group col-md-6">
						  <div>Requested Date:_______________________</div>
						  <div id="fordate" style="margin-left:60%; margin-top:-8%;"></div>
					    </div> 
					    <div class="form-group col-md-6">
						  <div>Requested Time:______________________</div>
						  <div id="fortime" style="margin-left:60%; margin-top:-8%;"></div>
					    </div>
					    <div class="clearfix"></div>
					  <div class="form-group col-md-6">
						<label for="dname">Donors Full Name</label>
						<input type="textbox" class="form-control" id="dname" name="dname" placeholder="Donors Full Name" readonly>
						<div class="help-block with-errors"></div>
					  </div> 
					  <div class="form-group col-md-6">
						<label for="pname">Participant Full Name</label>
						<input type="textbox" class="form-control" id="pname" name="pname" placeholder="Participants Full Name" readonly>
						<div class="help-block with-errors"></div>
					  </div>
					  <div class="clearfix"></div>
					  <div class="form-group col-md-6">
						<label id="lblamountreq" for="amountreq"> <span style="color:#FF0000">*</span></label>
						<input type="text" class="form-control" id="amountreq" name="amountreq" placeholder="Enter Amount" required>
						<div class="help-block with-errors"></div>
					  </div>
					  <div class="form-group col-md-6">
						<label for="tid">Transaction #</label>
						<input type="text" class="form-control" id="tid" name="tid" value="" readonly>
						<div class="help-block with-errors"></div>
					  </div>
					  <div class="form-group col-md-12">
						<label id="lblreason" for="reason"> <span style="color:#FF0000">*</span></label>
						<textarea class="form-control" rows="4" id="reason" name="reason" required></textarea>
						<div class="help-block with-errors"></div>
					  </div>
					  <!--<div class="form-group col-md-6">
						<label for="emailfrom">Check will be emailed to<span style="color:#FF0000">*</span></label>
						<input type="email" class="form-control" data-minlength="6" id="emailto" name="emailto" placeholder="Enter Email" required>
						<div class="help-block with-errors"></div>
					  </div>-->
					  <div class="clearfix"></div>
					  <div class="form-group col-md-12">
						<h3>Campaign Information</h3>
					  </div>
					  <div class="clearfix"></div>
					  <div class="form-group col-md-6">
						<label for="cmname">Campaign Manager Name</label>
						<input type="text" class="form-control" id="cmname" name="cmname" value="" readonly>
						<div class="help-block with-errors"></div>
					  </div> 
					  <div class="form-group col-md-6">
						<label for="ctitle">Campaign Title</label>
						<input type="text" class="form-control" id="ctitle" name="ctitle" value="" readonly>
						<div class="help-block with-errors"></div>
					  </div> 
					  <div class="clearfix"></div>
					  <div class="form-group col-md-6">
						<label for="cno">Campaign #</label>
						<input type="text" class="form-control" id="cno" name="cno" value="" readonly>
						<div class="help-block with-errors"></div>
					  </div> 
					  <div class="form-group col-md-6">
						<label for="cac">Campaign Account #</label>
						<input type="text" class="form-control" id="cac" name="cac" value="" readonly>
						<div class="help-block with-errors"></div>
					  </div> 
					  <div class="clearfix"></div>
					  </div>
					  <div class="modal-footer">
						<input type="hidden" name="cid" id="cid" >
						<input type="hidden" name="pid" id="pid" >
						<input type="hidden" name="did" id="did" >
						<input type="hidden" name="demail" id="demail" >
						<input type="hidden" name="rdmode" id="rdmode" >
						<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
						<button type="submit" name="refundsubmit" id="refundsubmit" class="btn btn-success waves-effect waves-light">Send Request <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
					  </div>
					  </div>
					</div>
					<!-- /.modal-content -->
				  </div>
				<!-- /.modal-dialog -->
				</div>
				</form>
				<? } //Administrator ?>
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
<script src="../js/jquery.noty.packaged.min.js" async></script>
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
		 "bProcessing": true,
		 "bServerSide": true,
		 "sAjaxSource": "manage_payments_ajax.php?uid=<?=$uid;?>&rid=<?=$rid;?>&module=<?=$module;?>",
		 // "scrollY": '50vh',
		 // "scrollX": true,
		 "autoWidth": false,
		  language: {
			processing: "<img src='images/loading-spinner-blue.gif'> Loading...",
		  },
		 "columnDefs": [
			{ "width": "100px", "targets": 0 },   //Status
			{ "width": "100px", "targets": 1 },   //Campaign #
			{ "width": "250px", "targets": 2 },  //Campaign Name
			{ "width": "200px", "targets": 3 },   //Campaign Manager Name
			{ "width": "100px", "targets": 4 },   //Donors ID
			{ "width": "200px", "targets": 5 },  //Donors Name
			{ "width": "150px", "targets": 6 },  //Donors Email
			{ "width": "150px", "targets": 7 },  //Donation Amt
			{ "width": "100px", "targets": 8 },  //Card Number
			{ "width": "150px", "targets": 9 }, //Payment Method
			{ "width": "150px", "targets": 10 }, //Transaction ID
			{ "width": "150px", "targets": 11 },  //Transaction Date
			{ "width": "200px", "targets": 12 },  //Participants Name
			{ "width": "200px", "targets": 13 },  //Participants Email
			{ "width": "210px", "targets": 14 }, //Action
		 ],
		 "order": [[ 11, "desc" ]]
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
        });

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
	setTimeout(function(){ 
		$('<div class="newuser_btn" style="width:122px !important;float:right; margin:-10px 0 0 5px "><a href="manage_payments.php?module=disputed"><button class="btn btn-block btn-primary" style="width:120px; margin-top:10px;"><span class="newtext">Disputed</span></button></a></div><div class="newuser_btn" style="width:122px !important;float:right; margin:-10px 0 0 5px "><a href="manage_payments.php?module=refunded"><button class="btn btn-block btn-primary" style="width:120px; margin-top:10px;"><span class="newtext">Refunded</span></button></a></div><div class="newuser_btn" style="width:122px !important;float:right; margin:-10px 0 0 5px "><a href="manage_payments.php?module=paid"><button class="btn btn-block btn-primary" style="width:120px; margin-top:10px;"><span class="newtext">Paid</span></button></a></div>').appendTo('div.dataTables_filter');
		
		function updateStatus(url, ajaxData){
		      var asyncReq = true; 
		      // console.log( $(this).data(), $(this).val() );
		      if(ajaxData['cur_state'] == 'settled'){
		         asyncReq = false;//request should be sync     
		      }
		      // console.log(ajaxData);
		      notyMessage('Updating status, please wait...', ""); 
		      $.ajax({
		        type: "POST",
		        url: url,
		        data: ajaxData,
		        dataType:"json",
		        async: asyncReq, 
		        success: function (response) {
		          // Noty message dialog
		            if(response.success){
		                // console.log("response ", response);
		                notyMessage('Status updated successfully', "success");
		            } else{
		                notyMessage('Something went wrong, please try again later', "error");
		            }

		            window.location.reload();
		        }
		      });//end ajax

		  }
		$('#example1').on('click', '.check_payment_refundclick', function(e){
		    e.preventDefault();
	      	e.stopPropagation();

		    var ajaxData = {};
		    ajaxData['cp_id']     = $(this).data('check_payment_id');
		    ajaxData['cur_state'] = 'refunded';
		    var url = "<?php echo sHOMECMS;?>check_payment.php?action=update_check_payment_status";
		    updateStatus(url, ajaxData);
		});

		$('#example1').on('click', '.refundclick', function(){
			if ($(this).hasClass("disabled")) {
				
			} else {
				$("#dname").val('');
				$("#pname").val('');
				$("#amountreq").val('');
				$("#tid").val('');
				$("#reason").val('');
				$("#cmname").val('');
				$("#ctitle").val('');
				$("#cno").val('');
				$("#cac").val('');
				$("#cid").val('');
				$("#pid").val('');
				$("#did").val('');
				$("#demail").val('');
				$("#forsdate").html('');
				$("#foredate").html('');
				$("#fordate").html('');
				$("#fortime").html('');
				function addCommas(nStr)
				{
					nStr += '';
					x = nStr.split('.');
					x1 = x[0];
					x2 = x.length > 1 ? '.' + x[1] : '';
					var rgx = /(\d+)(\d{3})/;
					while (rgx.test(x1)) {
						x1 = x1.replace(rgx, '$1' + ',' + '$2');
					}
					return x1 + x2;
				}
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth()+1; //January is 0!
				var yyyy = today.getFullYear();
				if(dd<10){
					dd='0'+dd
				} 
				if(mm<10){
					mm='0'+mm
				} 
				var today = dd+'/'+mm+'/'+yyyy;
				
				var dt = new Date();
				var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
								
				var cid = $(this).attr('cid');
				var dname = $(this).attr('dname');
				var pname = $(this).attr('pname');
				var amountreq = $(this).attr('amountreq');
				var tid = $(this).attr('tid');
				var reason = '';
				var cmname = $(this).attr('cmname');
				var ctitle = $(this).attr('ctitle');
				var cno = $(this).attr('cno');
				var cac = $(this).attr('cac');
				var pid = $(this).attr('pid');
				var did = $(this).attr('did');
				var sdate = $(this).attr('sdate');
				var edate = $(this).attr('edate');
				var demail = $(this).attr('demail');
				if ($(this).attr('rdmode') == 'refund') {
					$("#rdmode").val('refund');
					$("#lblamountreq").text('Refund Amount');
					$("#lblreason").text('Reason for refund');
					$(".camptitle").html(''+ctitle+' -- [REFUND]');
				} else if ($(this).attr('rdmode') == 'dispute') {
					$("#rdmode").val('dispute');
					$("#lblamountreq").text('Dispute Amount');
					$("#lblreason").text('Reason for dispute');
					$(".camptitle").html(''+ctitle+' -- [DISPUTE]');
				}
				$("#cid").val(''+cid+'');
				$("#dname").val(''+dname+'');
				$("#pname").val(''+pname+'');
				$("#amountreq").val(''+addCommas(amountreq)+'');
				$("#tid").val(''+tid+'');
				$("#reason").val(''+reason+'');
				$("#cmname").val(''+cmname+'');
				$("#ctitle").val(''+ctitle+'');
				$("#cno").val(''+cno+'');
				$("#cac").val(''+cac+'');
				$("#pid").val(''+pid+'');
				$("#did").val(''+did+'');
				$("#demail").val(''+demail+'');
				$("#forsdate").html(''+sdate+'');
				$("#foredate").html(''+edate+'');
				$("#fordate").html(''+today+'');
				$("#fortime").html(''+time+'');
				$("#refund").modal('show');
			}
		});
		/*$('#refund').on('click', '.refundsubmit', function(){
			var postdname = $('#dname').val();
			var postpname = $('#pname').val();
			var postamountreq = $('#amountreq').val();
			var posttid = $('#tid').val();
			var postreason = $('#reason').val();
			
			var postcmname = $('#cmname').val();
			var postctitle = $('#ctitle').val();
			var postcno = $('#cno').val();
			var postcac = $('#cac').val();

			var postcid = $('#cid').val();
			var postpid = $('#pid').val();
			var postdid = $('#did').val();
			var postdemail = $('#demail').val();
			
			$.post('managepaymentpost.php', 'dname=' + postdname + '&pname=' + postpname + '&amountreq=' + postamountreq + '&tid=' + posttid + '&reason=' + postreason + '&cmname=' + postcmname + '&ctitle=' + postctitle + '&cno=' + postcno + '&cac=' + postcac + '&cid=' + postcid + '&pid=' + postpid + '&did=' + postdid + '&demail=' + postdemail + '&act=1', function (response) {
				var jdata = JSON.parse(response);
			});
		});*/
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
<script src="bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="js/validator.js"></script>
<script type="text/javascript">
   $('#notifications').delay(3000).fadeOut('slow');
</script>
</body>
</html>
<? include_once('bottom.php');?>