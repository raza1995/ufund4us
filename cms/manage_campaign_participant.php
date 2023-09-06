<?
require_once("../configuration/dbconfig.php");


if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else {
	if ($_SESSION['role_id'] == 4) {
		$oregister->redirect('dashboard.php');
	}
}
//$directory = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/cms/uploads/profilelogo/';

$sPageName = '<li>Manage Campaign</li>';
//$sCampaignLink = 'style="color:#F3BE00"';
$sLeftMenuCampaignParticipant = 'active';
$msg = 3;
if (array_key_exists('emailsubmit', $_POST)) {
	//Participant Portion
	$p_cid = sanitize($_POST['cid']);
	$p_pid = sanitize($_POST['pid']);
	$p_email = $_POST['emailfrom'];
	$p_fname = sanitize($_POST['pfname']);
	$p_lname = sanitize($_POST['plname']);
	//Receiver Portion
	$r_fname = sanitize($_POST['emailfname']);
	$r_lname = sanitize($_POST['emaillname']);
	$r_to = $_POST['emailto'];
	$r_cc = $_POST['emailcc'];
	//$r_msg = sanitize($_POST['emailmsg']);
	$r_msg = '';
	//Generate TimeStamp
	$todays = date('Y-m-d H:i:s');
	$expirydate = date('Y-m-d H:i:s', strtotime($todays . ' + 72 hours'));
	//Generate Link
	function generateRandomString($length) {
		$characters = '-_@0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	$generatedlink = generateRandomString(100);
	if ($oCampaign->insert_invitations($p_cid, $p_pid, $p_email, $p_fname, $p_lname, $r_fname, $r_lname, $r_to, $r_cc, $r_msg, $todays, $expirydate, $generatedlink)) {
		$oregister->redirect('manage_campaign_participant.php?msg=success');
		$msg = 1;
	} else {
		$msg = 0;
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
<title>Admin<?php echo sWEBSITENAME;?> - Manage Campaign</title>
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
		  <div style="position: fixed; z-index: 10; width: 134px; height: 70px; top: 70px; right: 15px;text-align: center;"><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal"><img src="../images/Help icon.jpg" width="60px" /><br>Need help click here</a></div>
		  <h1 class="h1styling">Manage Campaign</h1>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box white">
          	<?
			if(isset($_GET['m']) && $_GET['m']){
			?>
			<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
			  <? if ($msg == 1) { ?>
			  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Email has been sent...!
			  <? } ?>
			  <? if ($msg == 3) { ?>
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><?=$aMessage[$_GET['msg']]?>
			  <? } ?>
			</div>
			<? } ?>
             <?

			 $sName = isset($_SESSION['fld_name']) ? $_SESSION['fld_name'] : '';
			 $sId = isset($_SESSION['uid']) ? $_SESSION['uid'] : 0;
			 $sRoleId = $_SESSION['role_id'];
             $sCampaignData = $oCampaign->getmanagedonors($sName,$sId,$sRoleId);
			 //$oCampaign->getcampaign($uid, $rid);
			 
			 $iCountRecords = count($sCampaignData);
			 //if($iCountRecords>0){
			 ?>
				<table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Status</th>
				  <th>Campaign #</th>
				  <th>Campaign ID</th>
                  <th>Campaign Name</th>
				  <th>Enter Donor's Emails</th>
				  <th>Email Lifeline</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th># Of Days Left</th>
				  <th># Of Donors</th>
				  <th>Campaign Link</th>
                </tr>
                </thead>
                <tbody>
                <?
                for($i=0;$i<$iCountRecords;$i++){
					$cid = $sCampaignData[$i]['fld_campaign_id'];
					$todays = date('Y-m-d H:i:s');
					//$todays = '2017-11-07 04:13:00';
					$sInvitationData = $oCampaign->getinvitations($cid, $sId, $todays);
					$isdata = count($sInvitationData);
				?>
                <tr>
                  <td><? if($sCampaignData[$i]['fld_status'] == 1){?> <i class="fa fa-fw fa-thumbs-o-up"></i><? }else{?> <i class="fa fa-fw fa-thumbs-o-down"></i> <? }?></td>
                  <td><?=str_pad($sCampaignData[$i]['fld_campaign_id'], 7, "0", STR_PAD_LEFT);?></td>
                  <td><?=$sCampaignData[$i]['fld_pin'];?></td>
				  <td><a href="participant_build_team.php?cid=<?=$sCampaignData[$i]['fld_campaign_id']?>&uid=<?=$sCampaignData[$i]['uid']?>"><?=$sCampaignData[$i]['fld_campaign_title']?></a></td>
				  <?
				  if ($sCampaignData[$i]['daysleft'] >= 0) {
				  ?>
				  <td><a href="participant_build_team.php?cid=<?=$sCampaignData[$i]['fld_campaign_id']?>&uid=<?=$sCampaignData[$i]['uid']?>#donorssection">Enter Donor's Emails Here</a></td>
				  <? } else { ?>
				  <td><a href="javascript:void(0);" class="camp_expired" camp_title="<?=$sCampaignData[$i]['fld_campaign_title']?>">Enter Donor's Emails Here</a></td>
				  <? } ?>
				  <? if ($sCampaignData[$i]['daysleft'] >= 0) { ?>
					<td>
					  <ul>
					<? for ($ldata = 0; $ldata < $isdata; $ldata++) { ?>
				    <? if ($sInvitationData[$ldata]['datetime'] != "" && $sInvitationData[$ldata]['datetime'] != "0000-00-00 00:00:00") { 
					   //$timeleft = strtotime($sInvitationData['datetime']);
					   $is_expiry = '';
					   $dated_format = date('m/d/Y H:i:s', strtotime($sInvitationData[$ldata]['datetime']));
					   $dated = date($sInvitationData[$ldata]['datetime']);
					   $expirydate = date('Y-m-d H:i:s', strtotime($dated . ' + 72 hours'));
					   $from=date_create($expirydate);
					   $to=date_create(date('Y-m-d H:i:s'));
					   $diff=date_diff($to,$from, FALSE);
					   if ($diff->format('%R%a') >= 0 && $diff->format('%R%H') >= 0 && $diff->format('%R%i') > 0) {
					      $timeleft = $diff->format('%a Days, %H Hours');
						  $is_expiry = 0;
					   } else {
   						  $timeleft = "Expired";
						  $is_expiry = 1;
					   }
					?>
					  <li>Already Emailed @ <?=$dated_format;?><br>TimeLeft: <?=$timeleft;?></li>
					  
				    <? } } ?>
				    
				    <?php 
					$limitgenerate = 5;
					$linklimit = $limitgenerate - $isdata;
					for ($ldata = 0; $ldata < $linklimit; $ldata++) {
					?>
					  <li><a href="javascript:void(0);" class="lifeline" cid="<?=$sCampaignData[$i]['fld_campaign_id']?>">Generate Email Lifeline</a></li>
					  
					<?php } ?>
					  </ul>
					</td>
				  <? } else { ?>
				  <td><a href="javascript:void(0);" class="camp_expired" camp_title="<?=$sCampaignData[$i]['fld_campaign_title']?>">Campaign Expired</a></td>
				  <? } ?>
                  <td><?=date('m/d/Y',strtotime($sCampaignData[$i]['fld_campaign_sdate']))?></td>
                  <td><?=date('m/d/Y',strtotime($sCampaignData[$i]['fld_campaign_edate']))?></td>
                  <td><?=$sCampaignData[$i]['daysleft']?></td>
                  <td><?=$sCampaignData[$i]['NoOfDonors']?></td>
                  <td><a href="<?=sHOME."campaign.php?cid=".$sCampaignData[$i]['fld_hashcamp']."|".$sCampaignData[$i]['fld_campaign_id']."|".$_SESSION['uid'];?>" target="_blank">Link</a></td>
                </tr>
                <? }?>
                </tbody>
                </table>

        </div>
        </div>
      </div>
    </div>
    <!-- /.container-fluid -->
  </div>
  <!-- /#page-wrapper -->
	<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" id="yt-player">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
	<!-- #footer -->
    <? include_once('footer.php');?>
	<!-- /#footer -->
</div>
<!-- /#wrapper -->
<div class="modal fade generatelink" style="" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <form id="frmgenerateemail" name="frmgenerateemail" data-toggle="validator" method="post" action="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Participant Generate Link</h4>
                </div>
                <div class="modal-body">
                    <?php
                    $iId = $_SESSION['uid'];
                    $aUserDetail = $oregister->getuserdetail($iId);
                    $user_email = $aUserDetail['fld_email'];
                    $user_fname = $aUserDetail['fld_name'];
                    $user_lname = $aUserDetail['fld_lname'];
                    ?>
                    <div class="form-group col-md-12">
                        <label for="emailcc">Participant Email Address<span style="color:#FF0000">*</span></label>
                        <input type="textbox" readonly class="form-control" id="emailfrom" name="emailfrom" placeholder="Participant Email" value="<?=$user_email;?>" required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-6">
                        <label for="emailfname">First Name<span style="color:#FF0000">*</span></label>
                        <input type="textbox" class="form-control" id="emailfname" name="emailfname" placeholder="Receiver's First Name" required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="emaillname">Last Name</label>
                        <input type="textbox" class="form-control" id="emaillname" name="emaillname" placeholder="Receiver's Last Name">
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-6">
                        <label for="emailto">Email To<span style="color:#FF0000">*</span></label>
                        <input type="textbox" class="form-control" id="emailto" name="emailto" placeholder="Enter Email To" required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="emailcc">Email CC</label>
                        <input type="textbox" class="form-control" id="emailcc" name="emailcc" placeholder="Enter Email CC">
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div style="clear:both"></div>
                    <div class="col-lg-12">
                        <div><?=$thankyou;?></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div style="clear:both"></div>
                <div class="modal-footer">
                    <input type="hidden" name="cid" id="cid" value="">
                    <input type="hidden" name="pid" id="pid" value="<?=$iId;?>">
                    <input type="hidden" name="pfname" id="pfname" value="<?=$user_fname;?>">
                    <input type="hidden" name="plname" id="plname" value="<?=$user_lname;?>">

                    <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal"><span class="btn-label"><i class="fa fa-chevron-left"></i></span> Close</button>
                    <button type="submit" name="emailsubmit" id="emailsubmit" class="btn btn-success waves-effect waves-light">Send message <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
                </div>
            </div>
        </div>
    </form>
</div>
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
      $('#example1').DataTable({


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
   $('#example1').on('click', '.lifeline', function(){
	   var cid = $(this).attr('cid');
	   $('#cid').val(cid);
	   $(".generatelink").modal('show');
   });
   $('#notifications').delay(3000).fadeOut('slow');
   $('.camp_expired').on('click', function() {
	    var camp_title = $(this).attr('camp_title');
		swal(camp_title, "This campaign has expired.");
   });
</script>
<script type="text/javascript">
	var tag = document.createElement('script');

  tag.src = "https://www.youtube.com/iframe_api";
  var firstScriptTag = document.getElementsByTagName('script')[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('yt-player', {
          height: '390',
          width: '100%',
          videoId: 'WgNZqPOrQUM',
		  playerVars: { 'rel': 0 },
        });
      }
    $('#myModal').on('hidden.bs.modal', function () {
		player.pauseVideo();
    });
</script>
</body>
</html>
<? include_once('bottom.php');?>