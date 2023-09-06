<?
require_once("configuration/dbconfig.php");
$msg1 = '';
if(array_key_exists('joincampaign', $_POST))
{
	$camp_number = $_POST['fld_camp_number'];
	$camp_id = $_POST['fld_camp_id'];
	if ($camp_number != '' && $camp_id != '') {
		$oregister->redirect('confirm_campaign.php?cid='.$camp_number.'&cno='.$camp_id.'');
	} else {
		$msg1 = '<p style="font-size:14px; text-align:center"><b>Error:</b> Please type the campaign # and campaign ID</p>';
	}
	//$msg1 = '<div></div>';
	//$oregister->redirect('index.php');
}
if (isset($_GET['msg']) && $_GET['msg'] == 'error') {
	$msg1 = '<p style="font-size:14px; text-align:center; color:red;"><b>Error:</b> The campaign # and ID doesnt match...!</p>';
} elseif (isset($_GET['msg']) && $_GET['msg'] == 'alreadyjoined') {
	$msg1 = '<p style="font-size:14px; text-align:center; color:red;"><b>Error:</b> You already joined this campaign...!</p>';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0031)<?php echo SITE_URL;?>contact-us/ -->
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Join Campaign</title>
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
					<h1 class="h1styling" align="center">Join Campaign</h1>
					<br><br>
					<form action="" method="post">
						<div class="col-sm-12">
						<? 
						echo $msg1;
						?>
						</div>
						<div class="clearfix"></div>
						<div class="form-group col-sm-5">
							<label for="fld_camp_number" class="control-label">Enter Campaign # <i style="color:red;">*</i></label>
							<input type="text" class="form-control" id="fld_camp_number" name="fld_camp_number" placeholder="e.g 000000003">
						</div>
						<div class="form-group col-sm-2" align="center">
							<div style="font-size:20px; margin-top:30px">and</div>
						</div>
						<div class="form-group col-sm-5">
							<label for="fld_camp_id" class="control-label">Enter Campaign ID# <i style="color:red;">*</i></label>
							<input type="text" class="form-control" id="fld_camp_id" name="fld_camp_id" value="" placeholder="e.g 1234">
						</div>
						<div class="clearfix"></div>
						<div class="form-group">
							<div class="col-sm-5">
								<span class="fa fa-chevron-left"></span><button class="btn-2" type="button" onClick="window.location.href='index.php'">Cancel</button>
							</div>
							<div class="col-sm-2"></div>
							<div class="col-sm-5">
								<button class="btn-2" name="joincampaign" type="submit">Join Campaign </button><span class="fa fa-chevron-right"></span>
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
          videoId: '-sVgdnZFKqY',
		  playerVars: { 'rel': 0 },
        });
      }
    $('#myModal').on('hidden.bs.modal', function () {
		player.pauseVideo();
    });
</script>
</body>
</html>