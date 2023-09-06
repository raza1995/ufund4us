<?
require_once("../configuration/dbconfig.php");
require_once(dirname(__FILE__)."/../login_check.php");

$REQUEST = &$_REQUEST;

$camp_inform = '';
$basic_inform = '';
$build_team = '';
$go_live = '';
if (isset($REQUEST['cid'])) {
	$ciddd = $REQUEST['cid'];
	$camp_inform = 'href="start_campaign.php?m=e&cid='.$ciddd.'"';
	$basic_inform = 'href="basic_information.php?cid='.$ciddd.'"';
	$build_team = 'href="build_team.php?cid='.$ciddd.'"';
	$go_live = 'href="golive.php?cid='.$ciddd.'"';
}

if ($_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5) {
	$oregister->redirect('dashboard.php');
}

$sPageName = '<li><a '.$camp_inform.'>Your Campaign Start</a></li><li><a '.$basic_inform.'>Basic Information</a></li><li><a '.$build_team.'>Build Your Team</a></li><li>Finish</li>';
$sStartCampMenu = 'active';

$cid = $REQUEST['cid'];	
$start_campaign = 'start_campaign.php?m=e&cid='.$REQUEST['cid'].'';
$basic_information = 'basic_information.php?cid='.$REQUEST['cid'].'';
//$build_team = 'build_team.php?cid='.$REQUEST['cid'].'';
$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
// echo "cid=".$cid.", aCampaignDetail:<pre>"; print_r($aCampaignDetail); die();
$fld_uid = $aCampaignDetail['fld_uid'];
$fld_campaign_id = $aCampaignDetail['fld_campaign_id'];
$fld_campaign_title = $aCampaignDetail['fld_campaign_title'];
$fld_campaign_hash = $aCampaignDetail['fld_hashcamp'];
$fld_accid = $aCampaignDetail['fld_ac'];
$fld_campaign_sdate = date('m/d/Y', strtotime($aCampaignDetail['fld_campaign_sdate']));
// echo " _SESSION:<pre>"; print_r($_SESSION); die();
$uid_for_pid = isset($_SESSION['uid']) ? $_SESSION['uid'] : 0;
// $url = sHOME.'campaign.php?cid='.$fld_campaign_hash.'|'.$cid.'|'.$uid_for_pid;
$url = sHOME.'campaign.php?cid='.$fld_campaign_hash.'|'.$cid;
if($fld_campaign_hash == ""){
	$oregister->redirect(sHOMESCMS.'build_team.php?cid='.$cid);
}

$representativeDetail = $oregister->getrepresentative($fld_uid, 6);
$iCountRecords3 = count($representativeDetail);

checkAndSetInArray($representativeDetail, 'fld_name', '');
checkAndSetInArray($representativeDetail, 'fld_lname', '');
checkAndSetInArray($representativeDetail, 'fld_email', '');
checkAndSetInArray($representativeDetail, 'fld_phone', '');
checkAndSetInArray($representativeDetail, 'fld_uid', 0);

$rep_name2 = $representativeDetail['fld_name'];
$rep_lname2 = $representativeDetail['fld_lname'];
$rep_email2 = $representativeDetail['fld_email'];
$rep_phone2 = $representativeDetail['fld_phone'];
$rep_id2 = $representativeDetail['fld_uid'];

if ($iCountRecords3	== 0) {
	$rep_name = sWEBSITENAME;
	$rep_lname = '';
	$rep_email = INFO_EMAIL;
	$rep_phone = '888-419-1008';
} else {
	$rep_name = $rep_name2;
	$rep_lname = $rep_lname2;
	$rep_email = $rep_email2;
	$rep_phone = $rep_phone2;
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
<title>Admin<?php echo sWEBSITENAME;?> - Finished</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">
<link href="bower_components/sweetalert2/sweetalert2.css" rel="stylesheet" type="text/css">
<!-- Include a polyfill for ES6 Promises (optional) for IE11 and Android browser -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<style type="text/css">
.fleft{float: left;}
h1{font-size: 36px;margin-bottom: 0px;text-align: center;}
.post_social {margin: 0 0 10px;height: 35px;}
.post_social img{float:left;margin-right: 5px;}
.post_row img {float: left;overflow: hidden;width: 200px;margin-right: 15px;margin-bottom: 8px;}
.post_row p {font-size: 16px !important;}
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
		<h1 class="h1styling">Finish</h1>
		<div class="line3"></div>
		<? if ($fld_campaign_title != '') { ?>
		  <h4 class="h4styling"><?=$fld_campaign_title;?></h4>
		  <div class="line3"></div>
		<? } ?>
		
		 <div class="div_ul">
            <ul>
              <li class="select_no alreadyvisited borderright"><a <?=$camp_inform;?> style="color: #fff !important;">START YOUR CAMPAIGN</a></li>
              <li class="select_no alreadyvisited borderright"><a <?=$basic_inform;?> style="color: #fff !important;">BASIC INFORMATION</a></li>
              <li class="select_no alreadyvisited borderright" style="width:23%"><a <?=$build_team;?> style="color: #fff !important;">BUILD YOUR TEAM</a></li>
              <li class="select_no basic-back2 Build-back"><a <?=$go_live;?>>FINISHED</a></li>
            </ul>
          </div>
		  <div class="formdiv-in">
		  <? if ($fld_accid == '') { ?>
		  <div id="notifications" class="alert alert-danger alert-dismissable" style="padding: 6px 15px !important">
			  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Account not created, please goto <a href="start_campaign.php?m=e&cid=<?=$cid;?>">start your campaign</a> and click save button.</b>
		  </div>
		  </div>
		  <? } ?>
		 <div class="congratulations-top">
		 <img src="images/Congretulation-1.png" class="congretulation-img">
		 <h1>Congratulations on Completing Your Campaign!</h1>
		 <h3>Your campaign is scheduled to go live on <?=$fld_campaign_sdate;?></h3>
		 <h3>Please contact <?=$rep_name." ".$rep_lname;?> at <?=$rep_phone;?> if you have any questions.</h3>
		 <br>
		 <h3><?=$fld_campaign_title;?></h3>
		 <div class="line3" style="margin-left: 53px;margin-bottom: 0px;"></div>
		 <h4 align="center" style="margin-bottom: 30px;">Campaign # <?=str_pad($fld_campaign_id, 7, "0", STR_PAD_LEFT);?></h4>
		 </div>
		 <div align="center">
			<div id="link" style="display:none;"><a href="<?=$url;?>" target="_blank"><?=$url;?></a></div>
			<button class="btn btn-primary waves-effect waves-light" style="background:#fcb514;" name="linksubmit" id="linksubmit" onclick="window.open('<?=$url;?>');" type="button"><span class="btn-label" style=""><i class="fa fa-check"></i></span>Review your campaign</button>
			<!--<button class="btn btn-primary waves-effect waves-light" style="background:#fcb514;" name="copylink" id="copylink" onclick="copyToClipboard('#link');" type="button"><span class="btn-label" style=""><i class="fa fa-check"></i></span>Copy Link</button>-->
		 </div>
		  <!-- .white-box -->
		 <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) { } else { ?>
		 <div class="white-box congratulations-main" style="    background: rgba(245, 245, 245, 0);    border: 0px solid #d9d6d6;">
		 <h6> Spread the word about your campaign!</h6>
			<div class="fleft">    
				<div class="post_social">
					<!--<a href="javascript:void(0)" class="icon-fb" onclick="javascript:genericSocialShare('http://www.facebook.com/sharer.php?u=<?=$url;?>')" title="Facebook Share"><img src="images/fb.png"/></a>-->
					<a href="javascript:void(0)" id="facebookpopup" class="icon-fb" title="Facebook Share"><img src="images/fb.png"/></a>
					<a href="javascript:void(0)" class="icon-tw" onclick="javascript:genericSocialShare('http://twitter.com/share?text=<?php echo sWEBSITENAME;?> Campaign;url=<?=$url;?>')" title="Twitter Share"><img src="images/tw.png"/></a>
				</div>
				<script type="text/javascript" async >
				function genericSocialShare(url){
					window.open(url,'sharer','toolbar=0,status=0,width=648,height=395');
					return true;
				}
				</script>
			</div>
			<div class="clearfix"></div>
			<p>Do you need assistance from a fundraising professional to support your fundraising efforts?<br/>Please click here if you are interested.</p>
		  </div>
		  <? } ?>
		  <div style="clear:both"></div>
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
<script src="js/validator.js"></script>
<script src="bower_components/sweetalert2/sweetalert2.min.js"></script>
<script>
function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
}
//Popup for Facebook
$(document).on('click', '#facebookpopup', function(){
	var textfacebook = "<span style='margin-top:12px; display:block; text-align:left'>Facebook may prompt you to add a donation button as seen below.<br><b style='color:red'><i>PLEASE DO NOT SELECT NONPROFIT.</b></i> If this message appears simply close the window by selecting the X in the upper right corner.<br><b><i><?php echo sWEBSITENAME;?> has no control over donations made through Facebook.</i></b><br><img src='<?=sHOMES?>images/facebook_popup.jpg' width='100%' /></span>";
	//var textfacebook = "If Facebook asks you to add a donation button, <br><b><i><u>please do not Select Nonprofit!</u></i></b> Close window by selecting the X in the upper right corner. <br><br><?php echo sWEBSITENAME;?> has no control over donations made through Facebook when a donation button is added.<br><img src='<?=sHOMES?>images/facebook_popup.jpg' width='100%' />";
	var titlefacebook = "Facebook post submission";
	swal({
		//title: titlefacebook,
		text: textfacebook,
		//type: 'warning',
		//imageUrl: '<?=sHOMES?>images/facebook_popup.jpg',
		//imageWidth: 400,
		//imageHeight: 200,
		//imageAlt: 'Custom image',
		showCancelButton: false,
		showCloseButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: "Close Message Box",
		//cancelButtonText: "Cancel",
		closeOnConfirm: false
	}).then(function () {
		/*swal(
			'Facebook post submission',
			'Please check facebook popup',
			'success'
		);*/
		genericSocialShare('http://www.facebook.com/sharer.php?u=<?=$url;?>');
	}, function (dismiss) {
		if (dismiss === 'cancel') {
			swal(
				'Facebook post submission',
				'Cancelled',
				'error'
			);
		}
	});
	/*swal({   
        title: titlefacebook,
        text: textfacebook, 
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#FCB514",
        confirmButtonText: "Continue",
		cancelButtonText: "Cancel",
        closeOnConfirm: false 
	}, function(){
		var timer1 = 3000;
		swal({title: "Please Wait...!",   text: "Popup will initiate", type: "success", timer: timer1, showConfirmButton: false });
	});*/
});
</script>
</body>
</html>
<? include_once('bottom.php');?>