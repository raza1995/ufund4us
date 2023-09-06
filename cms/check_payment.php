<?php
require_once("../configuration/dbconfig.php");
require_once("../functions_constants.php");
require_once("../login_check.php");

// $sPageName = '<li><a href="users.php">Check Payment</a></li><li>Check Payment</li>';
if ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5 || $_SESSION['role_id'] == 6) {
  $oregister->redirect('dashboard.php');
}

//All actions, declarations and processing work of check_payment is in check_payment_processing.php file
require_once("check_payment_processing.php");
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
<title>Admin<?php echo sWEBSITENAME;?> - Check Payment</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<link href="bower_components/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
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

  /*.dataTables_scrollBody #example1 thead tr{
    display: none;
  }*/

  #example1_wrapper tr td:nth-child(2), #example1_wrapper th:nth-child(2)
  {
    width: 100px;
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
    		  <h1 class="h1styling">Check Payment</h1>
    		  <div class="line3"></div>
    		  
          <?php if($REQUEST['action'] == 'edit_check_payment' 
                || $REQUEST['action'] == 'add_check_payment'){

          $old_cp_data = [];
          if($REQUEST['action'] == 'edit_check_payment' && $REQUEST['cp_id'] > 0){
            $old_cp_data = $check_payments->get( ['cp_id'=>$REQUEST['cp_id']] );
            if($old_cp_data['count'] == 1){
              $old_cp_data = $old_cp_data['rows'][0]; 
            }
          }

          $check_payments->checkAndSetBasicDataOfCP($old_cp_data);
          // echo '<pre>'; print_r($old_cp_data); die();
          $fData = [];
          $check_payments->mapFields($fData, $old_cp_data);
          ?> 
          <!-- .white-box - Edit container - | start   -->
          <div class="white-box" id="check_payment_edit_div" style=" background: rgba(245, 245, 245, 0);    border: 0px solid #d9d6d6;">
            <?php if(isset($_GET['msg']))
            {?>
			         <div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><?=$aMessage[$_GET['msg']]?>
		          </div>
			      <? 
            }?>

		        <div class=" full-main">
              <form action=""  method="post">
                  <!-- <input type="hidden" name="tid"> -->
                  <input type="hidden" name="cp_id" value="<?php echo $fData['cp_id'];?>">
                  
                  <input type="hidden" name="admin_donation" value="1">
                  <input type="hidden" name="uid">
                  <input type="hidden" name="ufname">
                  <input type="hidden" name="ulname">
                  <input type="hidden" name="refferal_by" value="<?php echo $fData['pid'];?>">
                  <input type="hidden" name="payment_through" value="check">
                  <input type="hidden" name="sms_sent_date" value="<?= date('Y-m-d H:i:s') ?>">
                  <input type="hidden" name="email_sent_date" value="<?= date('Y-m-d H:i:s')?>">
                  <input type="hidden" name="tid" value="check">
                  <input type="hidden" name="client_ip" value="0">
                  <input type="hidden" name="reward_id" value="0">
                  <input type="hidden" name="reward_desc" value="0">
                  <input type="hidden" name="creationdate" value="<?= date('Y-m-d H:i:s')?>">
                  <!-- <input type="hidden" name="reward_id" value="0"> -->


                	<div class="clearfix"></div>
                  
                  <div class="form-group col-sm-6">
                    <label for="fld_lname" class="control-label">Check Amount<span style="color:#FF0000">*</span></label>
                    <input type="text" class="form-control" id="check_amount" name="donation_amount" placeholder="Enter Check Amount" value="<?php echo $fData['donation_amount'];?>" required>
                    <div class="help-block with-errors"></div>
                  </div>
                  <div class="clearfix"></div>

                  <div class="form-group col-sm-6">
                		<label for="check_number" class="control-label">Check Number<span style="color:#FF0000">*</span></label>
                		<input type="text" class="form-control" id="check_number" name="card_number" placeholder="Enter Check Number" value="<?php echo $fData['card_number'];?>" required>
                		<div class="help-block with-errors"></div>
                	</div>

                  <div class="form-group col-sm-6">
                    <label for="bank_name" class="control-label">Bank name<span style="color:#FF0000">*</span></label>
                    <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Enter Bank name" value="<?php echo $fData['bank_name'];?>" required>
                    <div class="help-block with-errors"></div>
                  </div>
            	
                  
                  <div class="col-md-12"><h2 class="page-header" style="color: #868484; font-family: Open Sans; font-size: 24px; margin:0px 0 20px">Participent Details</h2></div>
                

                  <div class="form-group col-sm-6">
                      <label for="fld_email" class="control-label">Campaign Name<span style="color:#FF0000">*</span></label>
                      <select class="form-control colorMeBlue noValue" name="cid" id="campaign" required>
                          <option value="">Select Campaign</option>
                          <?
                          if($campRecords>0){
                          for($i=0;$i<$campRecords;$i++){
                          ?>
                              <option value="<?=$campaign[$i]['fld_campaign_id']?>"  
                              <?php echo ($fData['cid'] == $campaign[$i]['fld_campaign_id']) ? 'selected' : '';?> 
                              ><?=$campaign[$i]['fld_campaign_title']?></option>
                          <? }}?>
                      </select>
                  </div>

                  <div class="form-group col-sm-6">
                      <label for="fld_email" class="control-label">Participant Name<span style="color:#FF0000">*</span></label>
                      <select class="form-control colorMeBlue noValue" name="pid" id="pid" required>
                          <option value="">Select participant</option>
                          <?php 
                          if($fData['pid'] != ""){
                            echo '<option value="'.$fData['pid'].'" selected>'.$fData['participant_name'].'</option>';
                          }?>
                      </select>
                  </div>

                  <div class="clearfix"></div>

            	
                  <div class="col-md-12"><h2 class="page-header" style="color: #868484; font-family: Open Sans; font-size: 24px; margin:0px 0 20px">Donor Details</h2></div>
               
                  <div class="form-group col-sm-6">
                		<label for="fld_email" class="control-label">Donor First Name<span style="color:#FF0000">*</span></label>
                		<input type="text" class="form-control" id="donor_fname" name="cmfname" placeholder="Enter First name" value="<?php echo $fData['cmfname'];?>" required>
                		<div class="help-block with-errors"></div>
                  </div>
                  
                  <div class="form-group col-sm-6">
                		<label for="fld_email" class="control-label">Donor Last Name<span style="color:#FF0000">*</span></label>
                		<input type="text" class="form-control" id="donor_Lname" name="cmlname" placeholder="Enter Last Name" value="<?php echo $fData['cmlname'];?>" required>
                		<div class="help-block with-errors"></div>
                	</div>
                  
                  <div class="clearfix"></div>

                  <div class="form-group col-sm-6">
                       <label for="uemail" class="control-label">Donor Email<span style="color:#FF0000">*</span></label>
                       <input type="email" class="form-control" id="uemail" name="uemail" placeholder="Enter Email address" value="<?php echo $fData['uemail'];?>" required>
                       <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group col-sm-6">
                       <label for="uphone" class="control-label">Donor Phone<span style="color:#FF0000">*</span></label>
                       <input type="text" class="form-control" id="uphone" name="uphone" placeholder="Enter Phone Number" value="<?php echo $fData['uphone'];?>" required>
                       <div class="help-block with-errors"></div>
                  </div>

                  <div class="clearfix"></div>               
                  
                  <div class="form-group col-sm-6">
                      <label for="cur_state" class="control-label">Status<span style="color:#FF0000">*</span></label>
                      <select class="form-control colorMeBlue noValue" name="cur_state" id="cur_state" required>
                          <option value="">Select status</option>
                          <?
                          foreach(CHECK_PAYMENT_STATUS_ARY as $value){
                            echo '<option value="'.$value.'" '.($fData['cur_state'] == $value ? 'selected' : '').'>'.ucfirst($value).'</option>';
                          }
                          ?>
                      </select>
                  </div>


                  <div class="clearfix"></div>    

                  <div class="form-group">
                  	<div class="col-sm-6" align="left">
              			   <button class="btn btn-primary waves-effect waves-light" type="button" onClick="window.location.href='dashboard.php'"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Cancel</button>
              		  </div>
              		
                		<div class="col-sm-6" align="right">
                			<button class="btn btn-success waves-effect waves-light" type="submit">Save & Continue <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
                		</div>
                  </div>

                  <div class="clearfix"></div>
              </form>
            </div>
          </div><!-- .white-box - Edit container -  | End -->
          <?php }
          else{ ?>
          <!-- .white-box - listing container - | start   -->
          <div class="white-box white"  id="check_payment_listing_div" >

                <!-- <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Action</th>
                      <th>Status</th>
                      <th>Date</th>
                      <th>Amount</th>
                      <th>Cid</th>
                      <th>Camp name</th>
                      <th>Check number</th>
                      <th>First name</th>
                      <th>Last name</th>
                      <th>Email</th>
                      <th>Phone#</th>
                      <th>Postal code</th>
                      <th>Country</th>
                      <th>Participant id</th>
                      <th>Participant name</th>
                    </tr>
                  </thead>
              </table> -->

              <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle mb-0">
                <thead>
                  <tr>
                    <th width="100px">Action</th>
                    <th>Status</th>
                    <th width="250px">Date</th>
                    <th>Amount</th>
                    <th>Cid</th>
                    <th width="150px">Camp name</th>
                    <th width="150px">Check number</th>
                    <th>Bank name</th>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Email</th>
                    <th>Phone#</th>
                    <th width="150px">Postal code</th>
                    <th>Country</th>
                    <th width="150px">Participant id</th>
                    <th width="250px">Participant name</th>
                  </tr>
                </thead>
            </table>
          </div><!-- .white-box - listing container -  | End -->
          <?php } ?>          


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
<!-- </div> -->
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
<!--<script src="js/mask.js"></script>-->
<!--Sparkline charts js -->
<script src="bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
<script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>


<script src="bower_components/datatables/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>


<script src="../js/jquery.noty.packaged.min.js" async></script>
<script src="js/validator.js"></script>



<script type="text/javascript">
var sHOMEcms = '<?= sHOMECMS ?>';
var table;
$(document).ready(function(){

  $('#data-table-default').DataTable({
      responsive: true,
      searching: true, 
      paging: true, 
      info: true,
      "bProcessing": true,
      "bServerSide": true,
      autoWidth: false,
      language: {
        processing: "<img src='images/loading-spinner-blue.gif'> Loading...",
       },
       ajax: function(data, callback){
      $.ajax({
        url: "check_payment.php?action=get_listing",
        'data': data,
        dataType: 'json',
        beforeSend: function(){
          console.log("ajax before send");
          // Here, manually add the loading message.
          $('#data-table-default > tbody').html(
            '<tr class="odd">' +
              '<td valign="top" colspan="6" class="dataTables_empty">Loading333</td>' +
            '</tr>'
          );
        },
        success: function(res){
          console.log('ajax success ', res);
          var html = '';
          for(x in res.data){
            html += '<tr>'+res.data[x]+'</tr>';
          }
          $('#data-table-default > tbody').html(html);
          $('#data-table-default_processing').hide();
        }
      });
    }  
    });

  table = $('#example1').DataTable({
    "bProcessing": true,
     "bServerSide": true,
     // "scrollY": '50vh',
     // "scrollX": true,
     "autoWidth": false,

     language: {
      processing: "<img src='images/loading-spinner-blue.gif'> Loading...",
     },
    // "ajax": "check_payment.php?action=get_listing",
    ajax: function(data, callback){
      $.ajax({
        url: "check_payment.php?action=get_listing",
        'data': data,
        dataType: 'json',
        beforeSend: function(){
          console.log("ajax before send");
          // Here, manually add the loading message.
          $('#example1 > tbody').html(
            '<tr class="odd">' +
              '<td valign="top" colspan="6" class="dataTables_empty">Loading333</td>' +
            '</tr>'
          );
        },
        success: function(res){
          console.log('ajax success ', res);
          var html = '';
          for(x in res.data){
            html += '<tr>'+res.data[x]+'</tr>';
          }
          $('#example1 > tbody').html(html);
          $('#example1_processing').hide();
        }
      });
    }     
  });

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

            // if(ajaxData['cur_state'] == 'settled' || ajaxData['cur_state'] == 'refunded'){
               // window.location.href = sHOMEcms+"check_payment.php?action=settel_cp&cp_id="+ajaxData['cp_id'];
               $("#actions_td_"+ajaxData['cp_id']).html(response.actions_html);
            // } 
        }
      });//end ajax

  }
  
  $(document).on('click','.refund_href',function(e){
      e.preventDefault();
      e.stopPropagation();
      // var ajaxData = {'cur_state':'refunded'}
      // var url = $(this).attr('href');
      // updateStatus(url, ajaxData);

     var ajaxData = {};
     ajaxData['cp_id']     = $(this).data('cp_id');
     ajaxData['cur_state'] = 'refunded';
     var url = "<?php echo sHOMECMS;?>check_payment.php?action=update_check_payment_status";
     updateStatus(url, ajaxData);

  });

  $(document).on('change','.check_payment_status_dropdown',function(){
     var ajaxData = {};
     ajaxData['cp_id']     = $(this).data('cp_id');
     ajaxData['cur_state'] = $(this).val();
     var url = sHOMEcms + "check_payment.php?action=update_check_payment_status";
     updateStatus(url, ajaxData);
  });//change event

  $(document).on('change','#campaign',function(){
      var camp_id = $(this).val();
      $.ajax({
          type: "POST",
          url: sHOMEcms + "check_payment.php",
          data: {'id':camp_id, 'action': 'get_camp_details'},
          success: function (response) {
              if(response.success){
                  console.log("response ", response);
                  $('#pid').empty();
                  var resVal = "<option value='"+v.id+"'>"+v.uname + ' ' + v.ulname+"</option>";
                  // var resVal = `<option value="${v.id}">${v.uname + ' ' + v.ulname}</option>`;
                  $.each(response.record, function(k,v){
                      $('#pid').append(resVal);
                  })
              }
              
          }
      });
  });

  $(document).on('change','#pid',function(){
      var pid = $(this).val();
      var sHOMEcms = '<?= sHOMECMS ?>'
          $.ajax({
              type: "POST",
              url: sHOMEcms + "check_payment.php",
              data: {'pid':pid, 'action': 'get_pid_details'},
              success: function (response) {
                  if(response.success){
                  console.log("response ", response);
                      $('input[name="ufname"]').val(response.record.uname);
                      $('input[name="ulname"]').val(response.record.ulname);
                      $('input[name="uid"]').val(response.record.uid);
                      // $('input[name="uphone"]').val(response.record.uphone);
                      $('input[name="refferal_by"]').val(response.record.pid);
                  }
                  
              }
          });
  });

});
</script>
<? include_once('bottom.php');?>
</body>
</html>
