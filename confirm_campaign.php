<?
require_once("configuration/dbconfig.php");
$msg1 = '';
$camp_number = $_GET['cid'];
$camp_id = $_GET['cno'];
$iId = '';
if ($camp_number != '' && $camp_id != '') {
	$camprow = $oCampaign->chk_campid($camp_id, $camp_number);
	if (count($camprow) > 0) {
		$campno = $camprow[0]['fld_campaign_id'];
		$campid = $camprow[0]['fld_pin'];
		$camptitle = $camprow[0]['fld_campaign_title'];
		$camporganname = $camprow[0]['fld_organization_name'];
		$campmname = $camprow[0]['fld_cname']." ".$camprow[0]['fld_clname'];
		$campmid = $camprow[0]['fld_uid'];
	} else {
		$oregister->redirect('join_campaign.php?msg=error');
	}
} elseif ($camp_number == '' && $camp_id != '') {
	$camprow = $oCampaign->chk_campid($camp_id, $camp_number);
	if (count($camprow) > 0) {
		$campno = $camprow[0]['fld_campaign_id'];
		$campid = $camprow[0]['fld_pin'];
		$camptitle = $camprow[0]['fld_campaign_title'];
		$camporganname = $camprow[0]['fld_organization_name'];
		$campmname = $camprow[0]['fld_cname']." ".$camprow[0]['fld_clname'];
		$campmid = $camprow[0]['fld_uid'];
	} else {
		$oregister->redirect('join_campaign.php?msg=error');
	}
} elseif ($camp_number != '' && $camp_id == '') {
	$camprow = $oCampaign->chk_campid($camp_id, $camp_number);
	if (count($camprow) > 0) {
		$campno = $camprow[0]['fld_campaign_id'];
		$campid = $camprow[0]['fld_pin'];
		$camptitle = $camprow[0]['fld_campaign_title'];
		$camporganname = $camprow[0]['fld_organization_name'];
		$campmname = $camprow[0]['fld_cname']." ".$camprow[0]['fld_clname'];
		$campmid = $camprow[0]['fld_uid'];
	} else {
		$oregister->redirect('join_campaign.php?msg=error');
	}
} else {
	$oregister->redirect('join_campaign.php');
}
if(array_key_exists('confirmcampaign', $_POST))
{
	$cid = $_POST['cid'];
	$cno = $_POST['cno'];
	$cuid = $_POST['cuid'];
	if ($_SESSION['uid']) {
		$uid = $_SESSION['uid'];
		$chkcamprow = $oCampaign->chk_campaign_user($uid, $cuid, $cid, $cno);
		if (count($chkcamprow) > 0) {
			$oregister->redirect('join_campaign.php?msg=alreadyjoined');
		} else {
			if ($chkcamprow = $oCampaign->join_campaign2($uid, $cuid, $cid, $cno)) {
				$oregister->redirect('cms/dashboard.php');
			}
		}
	} else {
		$oregister->redirect('create_user.php?cuid='.$cuid.'&cid='.$cid.'');
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0031)<?php echo SITE_URL;?>contact-us/ -->
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Campaign Confirmation</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.css">
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>  
<link href="css/style.css" rel="stylesheet" type="text/css"> 

<link href="css/ninja-slider.css" rel="stylesheet" type="text/css">
<script src="js/ninja-slider.js" type="text/javascript"></script>
</head>
<body>
<? include_once('header.php');?>
<section class="ipcontentsection">
	<div class="container newContainer">
		<div class="mid_sec2">
			<div class="mid_secin">
				<div class="row2">
					<h1 class="h1styling" align="center">Campaign Confirmation</h1>
					<br><br>
					<form action="" method="post">
						<div class="col-sm-12">
						<? 
						echo $msg1;
						?>
						</div>
						<div class="clearfix"></div>
						<div class="form-group col-sm-12">
							<div class="form-group col-sm-4"><label for="fld_camp_number" class="control-label">Campaign Title:</label></div>
							<div class="form-group col-sm-8"><label for="fld_camp_number" class="control-label"><?=$camptitle;?></label></div>
						</div>
						<div class="clearfix"></div>
						<div class="form-group col-sm-12">
							<div class="form-group col-sm-4"><label for="fld_camp_number" class="control-label">Organization Name:</label></div>
							<div class="form-group col-sm-8"><label for="fld_camp_number" class="control-label"><?=$camporganname;?></label></div>
						</div>
						<div class="clearfix"></div>
						<div class="form-group col-sm-12">
							<div class="form-group col-sm-4"><label for="fld_camp_number" class="control-label">Campaign Manager Name:</label></div>
							<div class="form-group col-sm-8"><label for="fld_camp_number" class="control-label"><?=$campmname;?></label></div>
						</div>
						<div class="clearfix"></div>
   
						<div class="form-group">
							<div class="col-sm-5">
								<span class="fa fa-chevron-left"></span><button class="btn-2" type="button" onClick="window.location.href='join_campaign.php'">Cancel</button>
							</div>
							<div class="col-sm-2"></div>
							<div class="col-sm-5">
								<input type="hidden" name="cuid" id="cuid" value="<?=$campmid;?>" />
								<input type="hidden" name="cid" id="cid" value="<?=$campno;?>" />
								<input type="hidden" name="cno" id="cno" value="<?=$campid;?>" />
								<button class="btn-2" name="confirmcampaign" type="submit">Confirm </button><span class="fa fa-chevron-right"></span>
							</div>
						</div>
						<div class="clearfix"></div>
						<div align="center" style="margin-top:30px"><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal"><img src="images/Help icon.jpg" width="80px" /><br>Need help click here</a></div>
					</form>
				</div>
			</div>
		</div>
	</div>
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
</section>
<? include_once('footer.php');?>
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
          videoId: 'Pt-T50AfZ-w',
		  playerVars: { 'rel': 0 },
        });
      }
    $('#myModal').on('hidden.bs.modal', function () {
		player.pauseVideo();
    });
</script>
</body>
</html>