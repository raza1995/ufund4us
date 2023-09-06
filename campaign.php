<?
require_once("configuration/dbconfig.php");
$REQUEST = &$_REQUEST;
function printDD(...$parms){
	$inTestMode = isset($_REQUEST['inTestMode']) ? $_REQUEST['inTestMode'] : 0;
	if($inTestMode){
		echo "<pre>"; print_r($parms); echo "</pre>"; die();
	}
}

checkAndSetInArray($REQUEST,'pid', 0);

if ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'on') {
    exit(header("location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}"));
}

$cid22 = $REQUEST['cid'];
$cid2 = str_replace('!','|',$cid22);
$cid3 = explode("|", $cid2);
// echo '<pre>'; var_dump($cid3); die();
if( count($cid3) < 2 ){
	$oregister->redirect(SITE_FULL_URL);
}
$from_cid3_camphash = $cid3[0];
$from_cid3_campid = isset($cid3[1]) ? $cid3[1] : 0;
$from_cid3_pid = isset($cid3[2]) ? $cid3[2] : 0;

try{
	$aCampaignDetail = $oCampaign->getcampaigndetail2($from_cid3_camphash);
}
catch(Exception $e) {
	printDD( 'Message: '.$e->getMessage() );
}
// echo '<pre>'; print_r($aCampaignDetail); die();
if( !isset($aCampaignDetail['fld_campaign_id']) ){
	$oregister->redirect(SITE_FULL_URL.'?msg=Campaign details not found');
}
printDD('LINE='.__LINE__);
$after_app_fee_percentage = get_after_app_fee_percentage($aCampaignDetail);


$participant_available = false;
$campid_available = false;
$styleapplied = '';
if (array_key_exists("1",$cid3)) { //Campaign ID exists
	$campid = $from_cid3_campid;
	$campid_available = true;
	$CampaignGraphTotal = $oCampaign->getcampaigngraphtotal($from_cid3_camphash);
	$tcampaign_goal = $CampaignGraphTotal['campaign_goal'];
	$tcampaign_goal_original = $CampaignGraphTotal['campaign_goal_original'];
	$tcampaign_raised = $CampaignGraphTotal['campaign_raised'];
	if ($tcampaign_raised > $tcampaign_goal) {
		$updated_goal = $tcampaign_raised+1000;
		$oCampaign->campaigngoalupdate($updated_goal, $tcampaign_goal_original, $tcampaign_goal, $from_cid3_camphash);
	}
}

$pid = 0;
$participant_image = '';
if (array_key_exists("2",$cid3) && ($from_cid3_pid != 0 && $from_cid3_pid != '')) { //Participant ID exists
	$pid = $from_cid3_pid;
	$participant_available = true;
	/*$CampaignGraphParticipant = $oCampaign->getcampaigngraphparticipant($from_cid3_camphash, $pid);
	$tparticipant_goal = $CampaignGraphParticipant['participant_goal'];
	$tparticipant_goal_original = $CampaignGraphParticipant['participant_goal_original'];
	$tparticipant_raised = $CampaignGraphParticipant['participant_raised'];
	if ($tparticipant_raised > $tparticipant_goal) {
		$updated_participant_goal = $tparticipant_raised+100;
		$oCampaign->campaignparticipantgoalupdate($updated_participant_goal, $tparticipant_goal_original, $tparticipant_goal, $from_cid3_camphash);
	}*/
	$aParticipantDetail = $oregister->getuserdetail($pid);
	$participant_fname = checkSetInArrayAndReturn($aParticipantDetail, 'fld_name', '');
	$participant_lname = checkSetInArrayAndReturn($aParticipantDetail, 'fld_lname', '');
	$participant_image = checkSetInArrayAndReturn($aParticipantDetail, 'fld_image', '');
	$participantdirectory = sHOMESCMS.'uploads/profilelogo/';
	$styleapplied = 'style="margin-top:65px;"';
	$aCampaignGraphParticipant = $oCampaign->getcampaigngraphparticipant($from_cid3_camphash, $pid);
	
	$participant_goal = $aCampaignGraphParticipant['participant_goal'];
	$participant_raised = $aCampaignGraphParticipant['participant_raised'] *$after_app_fee_percentage;
	$participant_graph_total_per = (($participant_raised / $participant_goal) * 100)*$after_app_fee_percentage;
}
$hashid = "";
if (isset($REQUEST['hashid'])) {
	$hashid = $REQUEST['hashid'];
	$oCampaign->isreadupdate($from_cid3_campid, $pid, $hashid);
	//$formaction = 'donation.php?hashid='.$hashid.'';
}

$cid = $aCampaignDetail['fld_campaign_id'];
$show_participant_goal = $aCampaignDetail['fld_show_participant_goal'];
$show_ab1575 = $aCampaignDetail['fld_ab1575_pupil_fee'];
//if ($show_participant_goal == 0 || $show_ab1575 == 1) {
if ($show_participant_goal == 0) {
	$show_participant = false;
	$show_top10raiser = false;
} else {
	$show_participant = true;
	$show_top10raiser = true;
}
$top10moneyraised = $oCampaign->top10moneyraiser($cid);
$moneyraisedcounter = count($top10moneyraised);
$top10donors = $oCampaign->top10donors($cid);
$donorscounter = count($top10donors);
$getmessages = $oCampaign->getmessagedetail($cid, $pid);
$getmessagescounter = count($getmessages);
$aCampaignGraphTotal = $oCampaign->getcampaigngraphtotal($from_cid3_camphash);
$campaign_goal = $aCampaignGraphTotal['campaign_goal'];

$campaign_raised = $aCampaignGraphTotal['campaign_raised']*$after_app_fee_percentage;
$campaign_graph_total_per = (($campaign_raised / $campaign_goal) * 100)*$after_app_fee_percentage;
$galleryDetail = $oCampaign->getimagegallery($cid);
$gallerycounter = count($galleryDetail);
$gallerydirectory = sHOMESCMS.'uploads/imagegallery/';
$videoDetail = $oCampaign->getvideogallery($cid);
$videocounter = count($videoDetail);
$videodirectory = sHOMESCMS.'uploads/videogallery/';
$ext_video = pathinfo($videodirectory.$videoDetail[0]['fld_video'], PATHINFO_EXTENSION);
//Getting Rewards
$is_reward = $aCampaignDetail['fld_rewards'];
$rewards = $oCampaign->donor_rewards($cid);
$rewardscounter = count($rewards);
//Getting Rewards
$fld_campaign_id = $aCampaignDetail['fld_campaign_id'];
$fld_campaign_title = $aCampaignDetail['fld_campaign_title'];
$fld_text_messaging = $aCampaignDetail['fld_text_messaging'];
$fld_cname = $aCampaignDetail['fld_cname'];
$fld_clname = $aCampaignDetail['fld_clname'];
$fld_campaign_logo = $aCampaignDetail['fld_campaign_logo'];
$logodirectory = sHOMESCMS.'uploads/logo/';
$fld_cemail = $aCampaignDetail['fld_cemail'];
$fld_nonprofit_number = $aCampaignDetail['fld_nonprofit_number'];
$fld_desc1 = nl2br($aCampaignDetail['fld_desc1']);
$fld_desc2 = nl2br($aCampaignDetail['fld_desc2']);
$fld_desc3 = nl2br($aCampaignDetail['fld_desc3']);
$fld_team_size = $aCampaignDetail['fld_team_size'];
$fld_donor_size = $aCampaignDetail['fld_donor_size'];
$fld_organization_name = $aCampaignDetail['fld_organization_name'];
$fld_hashcamp = $aCampaignDetail['fld_hashcamp'];
$fld_activated = $aCampaignDetail['fld_status'];
$fld_enddate = date('Ymd',strtotime($aCampaignDetail['fld_campaign_edate']));
$fld_snddate = date('Ymd',strtotime($aCampaignDetail['fld_campaign_sdate']));
$current_date = date("Ymd");
$fld_enddate1 = date('Y-m-d',strtotime($aCampaignDetail['fld_campaign_edate']));
$current_date1 = date("Y-m-d H:i:s");
$from=date_create($fld_enddate1." 23:59:59");
$to=date_create($current_date1);
$diff=date_diff($to,$from);
if ($diff->invert == 0 && $diff->format('%R%a') >= 0) {
	if ($diff->format('%R%a') >= 2) {
		$timetoleft = "<span>".$diff->format('%a Days, %H Hours')."</span>";
	} else {
		$timetoleft = "<span style='color:red;'>".$diff->format('%a Days, %H Hours')."</span>";
	}
	$donationactivate = true;
} else {
	$timetoleft = "<span style='color:red;'>Expired</span>";
	$donationactivate = false;
}
$daysleft = str_pad(($fld_enddate - $current_date),2,"0",STR_PAD_LEFT);
$url = sHOME.'campaign.php?cid='.$from_cid3_camphash.'!'.$from_cid3_campid.'!'.$from_cid3_pid.'%26hashid='.$hashid.'';
$text = 'Please take a moment to review '.$fld_campaign_title.'. Your support is greatly appreciated! ';
$email_msg = "Hi%0D%0A%0D%0APlease take a moment and consider supporting $fld_campaign_title. Your generous donation will help make a difference.%0D%0A%0D%0A";
$email_msg .= "$url%0D%0A%0D%0A";
$email_msg .= "Thank you for your consideration.";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>.:: <?=$fld_campaign_title;?> - <?php echo sWEBSITENAME; ?> ::.</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<? if ($participant_image != '') { 
$data = getimagesizeWithoutError($participantdirectory.$participant_image);
$width = $data[0];
$height = $data[1];
?>
	<meta property="og:image:secure_url" content="<?=$participantdirectory.$participant_image;?>" />
	<meta property="og:image" content="<?=$participantdirectory.$participant_image;?>"/>
	<meta property="og:image:width"   content="<?=$width;?>"/>
    <meta property="og:image:height"  content="<?=$height;?>"/>
<? 
} else { 
$width = 0;
$height = 0;	
$data = getimagesizeWithoutError($logodirectory.$fld_campaign_logo);
if($data != false){
	$width = $data[0];
	$height = $data[1];
}
?>
	<meta property="og:image:secure_url" content="<?=$logodirectory.$fld_campaign_logo;?>" />
	<meta property="og:image" content="<?=$logodirectory.$fld_campaign_logo;?>"/>
	<meta property="og:image:width"   content="<?=$width;?>"/>
    <meta property="og:image:height"  content="<?=$height;?>"/>
<? } ?>
<meta property="og:title" content="<?=$fld_campaign_title;?>"/>
<meta property="og:url" content="<?=$url;?>"/>
<meta property="og:description" content="<?=$fld_campaign_title;?> campaign"/>
<link rel="stylesheet" href="css/bootstrap.css">
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>  
<link href="bars/bars.css" rel="stylesheet" type="text/css">
<link href="css/style.css" rel="stylesheet" type="text/css"> 
<link href="css/style1.css" rel="stylesheet" type="text/css"> 
<link href="css/style-resp.css" rel="stylesheet">
<link href="css/ninja-slider.css" rel="stylesheet" type="text/css">
<script src="js/ninja-slider.js" type="text/javascript"></script>
<link href="css/owl.carousel.min.css" rel="stylesheet" type="text/css">
<link href="css/owl.theme.default.css" rel="stylesheet" type="text/css">
<link href="cms/bower_components/sweetalert2/sweetalert2.css" rel="stylesheet" type="text/css">
<!-- Include a polyfill for ES6 Promises (optional) for IE11 and Android browser -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
<link href="css/video-js.css" rel="stylesheet">
<!-- If you'd like to support IE8 -->
<script src="js/videojs-ie8.min.js"></script>
<style>
.stuck {
    position: fixed;
    top: 19px;
    z-index: 999999999;
    margin: 0 auto;
    left: 46.15%;
    text-align: center;
}
.button_donatenow {
	font-size: 18px; 
	color: #fff; 
	font-weight: 600; 
	padding: 15px 15px; 
	text-align: center; 
	background: #fcb514; 
	border-radius: 3px; 
	display: inline-block; 
	text-transform: uppercase;
	position: relative;
	cursor: pointer;
    top: -16px;
    margin-left: 36%;
}
.button_donatenow a {
	color: #fff; 
}
.inputfont {
	font-size: 28px;
}
.inputfont2 {
	font-size: 35px;
	font-weight:600;
}
</style>
</head>
<body>
<? include_once('header.php');?>
<section class="ipcontentsection">
<div class="container newContainer">
	<div class="slide-banner">
      <div id="carousel-id" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
          <div class="item active">
            <!-- <img data-src="slide" alt="First slide" src="images/banner1.jpg" height="429" width="1314"> -->
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 company-logo">
			  <? if ($fld_campaign_logo != '') { ?>
				<div align="center"><img src="<?=$logodirectory.$fld_campaign_logo;?>" height="215" width="215" class="" alt="Image" /></div>
			  <? } else { ?>
				<div align="center"><img src="<?=$logodirectory.'default-logo.jpg';?>" height="215" width="215" class="" alt="Image" /></div>  
			  <? } ?>
              <!-- <img src="<?=$logodirectory.$fld_campaign_logo;?>" height="215" width="215" class="img-responsive" alt="Image" /> -->
              <div class="company-name jtextfill" style="height:120px;">
                <span><?=$fld_campaign_title;?></span>
              </div>
			  <div class="company-slogan">
				Campaign # <?=str_pad($fld_campaign_id, 7, "0", STR_PAD_LEFT);?>
			  </div>
              <!-- company-name -->
              <!--<div class="company-slogan">
                Fruit Company
              </div>-->
              <!-- company-slogan -->
            </div>
            <!-- company-logo -->
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 banner-right" style="height: 428px !important">
              <div class="row">
                <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 banner-graph">
                  <div class='wrap_right'>
                    <? if ($participant_available == true && $show_participant == true) { ?>
					<div class='bar_group'>
					  <div class='bar_group__bar thick' value='100'>
                        <div class="b_tooltip" style="top:220%;left:100%">
                          <span>Participant Goal<br>$ <?=number_format($participant_goal,2,'.',',');?></span>
                            <div class="b_tooltip--tri" style="top:-44px;-webkit-transform: rotate(180deg);-moz-transform: rotate(180deg);-o-transform: rotate(180deg);-ms-transform: rotate(180deg);transform: rotate(180deg);"></div>
                        </div>
                      </div>
                      <div class='bar_group__bar thick' value='<?=$participant_graph_total_per;?>'>
                        <div class="b_tooltip" style="top:-33%;left:100%">
                          <div class="b_tooltip--tri"></div>
                          <span>Participant Raised<br>$ <?=number_format($participant_raised,2,'.',',');?></span>
                        </div>
                      </div>
                    </div>
					<br><br>
					<? } ?>
					<div class='bar_group' <?=$styleapplied;?>>
                      <div class='bar_group__bar thick' value='100'>
                        <div class="b_tooltip" style="top:220%;left:100%">
                          <span>Campaign Goal<br>$ <?=number_format($campaign_goal,2,'.',',');?></span>
                          <div class="b_tooltip--tri" style="top:-44px;-webkit-transform: rotate(180deg);-moz-transform: rotate(180deg);-o-transform: rotate(180deg);-ms-transform: rotate(180deg);transform: rotate(180deg);"></div>
                        </div>
                      </div>
                      <div class='bar_group__bar thick' value='<?=$campaign_graph_total_per;?>'>
                        <div class="b_tooltip" style="top:-33%;left:100%">
                          <div class="b_tooltip--tri"></div>
                          <span>Campaign Raised<br>$ <?=number_format($campaign_raised,2,'.',',');?></span>
                        </div>
                      </div>
                    </div>
				  </div>
				</div>
				<? if ($participant_available == true) { ?>
                <!-- banner-graph -->
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 banner-pic">
                  <div align="center">
				  <div class="bp-name">
                    <?=$participant_fname." ".$participant_lname;?>
                  </div>
				  <? if ($participant_image != '') { ?>
					<img src="<?=$participantdirectory.$participant_image;?>" height="182" width="179" class="" alt="Image" />  
				  <? } else { ?>
					  <img src="<?=$participantdirectory.'default-profile-pic.jpg';?>" height="182" width="179" class="" alt="Image" />
				  <? } ?>
				  </div>
                  <!-- bp-name -->
				</div>
                <!-- banner-pic -->
				<? } ?>
              </div>
              <!-- row -->
            </div>
            <!-- banner-right -->
          </div>
          <!-- item --> 
        </div>
        <!-- carousel-inner -->
      </div>
      <!-- carousel-id -->
	  <br>
	  <h3 align="center">
		Campaign Ends In: <?=$timetoleft;?>
		<div class="nf-button sticky-element" style="margin-bottom: 0px !important"><a id="donatenow" href="javascript:void;">Donate</a></div>
	  </h3>
    </div>
    <!-- slide-banner -->  
	<div class="content-top">
	  <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-8 content-left">
		  <!-- testimonials -->
          <div class="testimonials white-box">
            <h3></h3>
            <div class="testm-message" style="color:black">
              To support &ldquo;<b><?=$fld_campaign_title;?></b>&rdquo; fundraiser. Choose a donation amount below.
			  Thank you very much for helping me meet our goal.
            </div>
            <!-- testm-message -->
            <? if ($participant_available == true) { ?>
			<div class="testm-name" style="color:black">
              <?=$participant_fname." ".$participant_lname;?>
            </div>
			<? } ?>
            <!-- testm-name -->
          </div>
          <!-- testimonials -->
		  <!-- personal-message -->
		  <div class="personal-msg white-box">
            <h3>Thank You Note</h3>
            <div class="pm-message">
              <?=$fld_desc3;?>
            </div>
          </div>
		  <!-- personal-message -->
		  <!-- slideshow & video -->
		  <?
		  if ($gallerycounter > 0 && $videocounter == 0 || $gallerycounter > 0 && $videocounter > 0) {
		  ?>
		  <!-- slideshow only -->
		  <div id="carousel-example-captions" data-ride="carousel" class="carousel slide">
            <ol class="carousel-indicators">
			  <? 
			  for ($o=0; $o < $gallerycounter; $o++) {
				if ($o == 0) {
				  echo '<li data-target="#carousel-example-captions" data-slide-to="'.$o.'" class="active"></li>';
				} else {
				  echo '<li data-target="#carousel-example-captions" data-slide-to="'.$o.'"></li>';
				}
			  }
			  ?>
            </ol>
            <div role="listbox" class="carousel-inner">
			  <? 
			  for ($p=0; $p < $gallerycounter; $p++) {
				if ($p == 0) {
				  echo '<div class="item active"> <img src="'.$gallerydirectory.$galleryDetail[$p]['fld_image_name'].'" style="width:750px; height:563px"></div>';
				} else {
				  echo '<div class="item"> <img src="'.$gallerydirectory.$galleryDetail[$p]['fld_image_name'].'" style="width:750px; height:563px"></div>';
				}
			  }
			  ?>
            </div>
            <a href="#carousel-example-captions" role="button" data-slide="prev" class="left carousel-control">
			  <span aria-hidden="true" class="fa fa-angle-left"></span>
			  <span class="sr-only">Previous</span>
			</a>
			<a href="#carousel-example-captions" role="button" data-slide="next" class="right carousel-control">
			  <span aria-hidden="true" class="fa fa-angle-right"></span>
			  <span class="sr-only">Next</span>
			</a>
		  </div>
		  <!-- slideshow only -->
		  <? } elseif ($gallerycounter == 0 && $videocounter > 0) { ?>
		  <!-- videos only -->
		  <?
		  //$videolink = 'https://youtu.be/zzAuwozsjT8';
		  $ytarray=explode("/", $videodirectory.$videoDetail[0]['fld_video']);
		  $ytendstring=end($ytarray);
		  $ytendarray=explode("?v=", $ytendstring);
		  $ytendstring=end($ytendarray);
		  $ytendarray=explode("&", $ytendstring);
		  $ytcode=$ytendarray[0];
		  echo "<iframe width=\"100%\" height=\"563\" src=\"https://www.youtube.com/embed/$ytcode\" frameborder=\"0\" allowfullscreen></iframe>";
		  ?>
		  <div style="margin-bottom:40px"></div>
		  <? } else { ?>
		  <? } ?>
		  <!--<div class="main-img">
            <img src="images/main-img.jpg" class="img-responsive" alt="Image" height="365" width="785">
          </div>
          <!-- slideshow & video -->
		  <div class="need-help white-box">
            <h3>Why we need your help</h3>
            <div class="pm-message">
              <div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 nh-col">
                  <div class="nh-sub-title">
                    Why Donations Are Needed
                  </div>
                  <!-- nh-sub-title -->
                  <div class="nh-message">
                    <?=$fld_desc2;?>
                  </div>
                  <!-- nh-message -->
				</div>
                <!-- nh-col -->
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 nh-col">
                  <div class="nh-sub-title">
                    How Your Donation Will be Used
                  </div>
                  <!-- nh-sub-title -->
                  <div class="nh-message">
                    <?=$fld_desc1;?>
                  </div>
                  <!-- nh-message -->
                </div>
				<!-- nh-col -->
              </div>
              <!-- row -->
            </div>
			<!-- testm-message -->
		  </div>
		  <!-- need-help -->
		</div>
		<!-- content-left -->
		<div class="col-xs-12 col-sm-12 col-md-5 col-lg-4 content-right">
		  <? if ($show_top10raiser == true) { ?>
		  <!-- Top 10 Raiser -->
          <div class="top-raisers">
            <div class="cr-title">
              <img src="images/star-icon.png" alt="" height="31" width="33"> <span>Top 10 Money Raisers</span>
            </div>
            <!-- cr-title -->
            <div class="ct-listing">
              <ul>
				<? 
				for ($aa = 0; $aa < $moneyraisedcounter; $aa++) {
					echo "<li>".$top10moneyraised[$aa]['uname']." <span>$ ".$top10moneyraised[$aa]['moneyraised']."</span></li>";
				}
				?>
			  </ul>
            </div>
            <!-- ct-listing -->
		  </div>
		  <!-- Top 10 Raiser -->
		  <? } ?>
          <!-- top-raisers -->
		  <div class="top-donors">
            <div class="cr-title">
              <img src="images/donors-icon.png" alt="" height="27" width="29"> <span>Donors</span>
            </div>
            <!-- cr-title -->
            <div class="ct-listing">
              <ul>
				<? 
				for ($bb = 0; $bb < $donorscounter; $bb++) {
					if ($top10donors[$bb]['displaylisted'] == 1) {
						echo "<li>".$top10donors[$bb]['ufname']." <span>$ ".$top10donors[$bb]['donation_amount']."</span></li>";
					} else {
						echo "<li>Anonymous <span>$ ".$top10donors[$bb]['donation_amount']."</span></li>";
					}
				}
				?>
              </ul>
            </div>
            <!-- ct-listing -->
          </div>
          <!-- top-donors -->
		  <div class="top-msgs">
            <div class="cr-title">
              <img src="images/message-icon2.png" alt="" height="24" width="37"> <span>Messages</span>
            </div>
            <!-- cr-title -->
			<div class="ct-listing msg-listing">
			  <ul>
				<?
				for ($mm = 0; $mm < $getmessagescounter; $mm++) {
					if ($getmessages[$mm]['comment'] != '') {
				?>
				<li class="row">
				  <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 ml-img">
					<?php if ($getmessages[$mm]['imageurl'] != '' ) {
						echo '<img src="'.sHOMESCMS.'uploads/donorimage/'.$getmessages[$mm]['imageurl'].'" class="img-responsive" alt="Image" height="59" width="77">';
					} else {
						echo '<img src="images/message-img.jpg" class="img-responsive" alt="Image" height="59" width="77">';
					}
					?>
				  </div>
				  <!-- ml-img -->
				  <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 ml-details">
					<div class="ml-title">
					  <?=$getmessages[$mm]['ufname']." ".$getmessages[$mm]['ulname'];?>
                    </div>
                    <!-- ml-title -->
					<div class="ml-para">
                      <?=$getmessages[$mm]['comment'];?>
                    </div>
                    <!-- ml-para -->
                  </div>
				  <!-- ml-details -->
                </li>
				<? } } ?>
			  </ul>
			</div>
			<!-- msg-listing -->
          </div>
          <!-- top-msgs -->
        </div>
        <!-- content-right -->
      </div>
      <!-- row -->
    </div>
    <!-- content-top -->
	<div class="white-box make-donation" align="center">
	  <h3><i>HELP US SPREAD THE WORD!</i></h3>
	  <p align="center">Please click the buttons below to share this page on Facebook, twitter or by email</p>
	  <div class="fleft">    
		<div class="post_social">
		  <!--<a href="javascript:void(0)" class="icon-fb" onclick="javascript:genericSocialShare('http://www.facebook.com/sharer.php?u=<?=$url;?>')" title="Facebook Share"><img src="images/fb.png"/></a>-->
		  <a href="javascript:void(0)" id="facebookpopup" class="icon-fb" title="Facebook Share"><img src="images/fb.png"/></a>
		  <a href="javascript:void(0)" class="icon-tw" onclick="javascript:genericSocialShare('http://twitter.com/share?text=<?=$text;?>;url=<?=$url;?>')" title="Twitter Share"><img src="images/tw.png"/></a>
		  <a href="mailto:?subject=Join me in supporting <?=$fld_campaign_title;?>&body=<?=$email_msg;?>" class="icon-tw" title="Email Share"><img src="images/mail.png"/></a>
	    </div>
	    <script type="text/javascript" async >
		  function genericSocialShare(url){
		    window.open(url,'sharer','toolbar=0,status=0,width=648,height=395');
		    return true;
		  }
	    </script>
	  </div>
	</div>
	<div id="donate_element">
	<? if ($fld_snddate <= $current_date && $donationactivate == true && $fld_activated == 1) { ?>
	<form action="donation.php" method="POST">
	<div class="white-box make-donation">
	  <h3 align="center">SELECT DONATION LEVEL</h3>
      <ul class="clearfix">
		<li class="chkamt" style="cursor: pointer; cursor: hand;" value="25">
		  <div class="md-li-inner">
			$<span>25</span>
		  </div>
		  <!-- md-li-inner -->
		  <div class="donate-button">
			<div class="db-icon"></div>
			<!-- db-icon -->
            <a class="chkamt" style="cursor: pointer; cursor: hand;" value="25">Click to Donate</a>
		  </div>
          <!-- donate-button -->
		</li>
		<li class="chkamt" style="cursor: pointer; cursor: hand;" value="50">
		  <div class="md-li-inner">
			$<span>50</span>
          </div>
          <!-- md-li-inner -->
          <div class="donate-button">
            <div class="db-icon"></div>
            <!-- db-icon -->
            <a class="chkamt" style="cursor: pointer; cursor: hand;" value="50">Click to Donate</a>
          </div>
          <!-- donate-button -->
		</li>
        <li class="chkamt" style="cursor: pointer; cursor: hand;" value="75">
          <div class="md-li-inner">
            $<span>75</span>
          </div>
          <!-- md-li-inner -->
          <div class="donate-button">
            <div class="db-icon"></div>
            <!-- db-icon -->
            <a class="chkamt" style="cursor: pointer; cursor: hand;" value="75">Click to Donate</a>
          </div>
          <!-- donate-button -->
        </li>
        <li class="chkamt" style="cursor: pointer; cursor: hand;" value="100">
          <div class="md-li-inner">
            $<span>100</span>
          </div>
          <!-- md-li-inner -->
          <div class="donate-button">
            <div class="db-icon"></div>
			<!-- db-icon -->
			<a class="chkamt" style="cursor: pointer; cursor: hand;" value="100">Click to Donate</a>
		  </div>
		  <!-- donate-button -->
        </li>
		<li class="chkamt" style="cursor: pointer; cursor: hand;" value="200">
		  <div class="md-li-inner">
			$<span>200</span>
		  </div>
		  <!-- md-li-inner -->
		  <div class="donate-button">
			<div class="db-icon"></div>
			<!-- db-icon -->
            <a class="chkamt" style="cursor: pointer; cursor: hand;" value="200">Click to Donate</a>
          </div>
		  <!-- donate-button -->
        </li>
		<li class="chkamt" style="cursor: pointer; cursor: hand;" >
		  <div class="md-li-inner otheramount">
			$<br><span>Other</span>
		  </div>
		  <!-- md-li-inner -->
		  <div class="donate-button otheramount">
			<div class="db-icon"></div>
			<!-- db-icon -->
			<a class="chkamt" style="cursor: pointer; cursor: hand;" >Click to Donate</a>
		  </div>
		  <!-- donate-button -->
		  <div class="formodal"></div>
		</li>
	  </ul>
      <?
	$sn = 0;
	if ($is_reward == 1 && $rewardscounter > 0) {
	for ($snreward=0; $snreward < $rewardscounter; $snreward++) {
	$sn++;
	//$rewards[$snreward]['reward_amount'] = <em>More Info</em>
	if ($rewards[$snreward]['reward_desc_details'] != '') {
		$amt = $rewards[$snreward]['reward_amount'];
		$desc = $rewards[$snreward]['reward_desc'];
		$desc_details = $rewards[$snreward]['reward_desc_details'];
		$additionalinfo = "<a href='javascript:void(0);' class='chkrewarddesc' style='font-size:12px;' desc='$desc' desc_details='$desc_details'><em>Additional Info</em></a>";
	} else {
		$additionalinfo = "";
	}
	?>
	<? if ($sn == 1 || $sn == 3) { ?>
	<div class="col-sm-12">
	<? } ?>
	<div class="col-sm-6" >
		<div class="row">
		<div class="form-group col-sm-4">
			<label for="rewards_amt1" class="control-label" style="font-size:14px;">Donation Amount</label>
			<div class="inputfont2"><span style="font-size:25px;">$</span> <?=str_replace(".00","",$rewards[$snreward]['reward_amount']);?></div>
		</div>
		<div class="form-group col-sm-8">
			<label for="rewards_desc1" class="control-label" style="font-size:14px;">Donation Description <?php echo $additionalinfo;?></label>
			<div class="inputfont"><?=$rewards[$snreward]['reward_desc'];?></div>
		</div>
		</div>
		<div class="row col-sm-12">
			<div class="button_donatenow"><a class="chkamount" value="<?=$rewards[$snreward]['reward_amount'];?>" donateid="<?=$rewards[$snreward]['id'];?>">Donate</a></div>
		</div>
	</div>
	<? if ($sn == 2 || $sn == 4) { ?>
	</div>
	<div class="clearfix"></div>
	<? } } ?>
	<? if ($rewardscounter == 1 || $rewardscounter == 3) { ?>
	</div>
	<div class="clearfix"></div>
	<? } } ?>
	  <div class="donation-thanks">
        Thank You for Your Consideration!!! <br>
        Your Donation may be tax deductible, please keep your receipt <br>
        that will be emailed to you shortly.
      </div>
      <!-- donation-thanks -->
    </div>
	<input type="hidden" name="txbamount" id="txbamount" />
	<input type="hidden" name="txbrewardid" id="txbrewardid" />
	<input type="hidden" name="cid" id="cid" value="<?=$campid;?>" />
	<input type="hidden" name="fld_text_messaging" id="fld_text_messaging" value="<?=$fld_text_messaging;?>" />
	<input type="hidden" name="pid" id="pid" value="<?=$pid;?>" />
	<input type="hidden" name="hashid" id="hashid" value="<?=$hashid;?>" />
	<button type="submit" name="btnsubmit" id="btnsubmit" style="display:none;"></button>
    <!-- make-donation -->
	</form>
	<? } else { ?>
	<div class="white-box make-donation">
	  <h3 align="center">THIS CAMPAIGN IS NOW CLOSED</h3>
	  <h4 align="center">Thank you to all of our supporters for their generous donations.</h4>
	</div>
	<? } ?>
	</div>
	<div class="need-fundraise">
      <div class="nf-title1">
        If your group needs to fundraise, <?php echo sWEBSITENAME; ?> will give you the tools needed.
      </div>
      <!-- nf-title1 -->
      <div class="nf-title2">
        3 easy steps to create your own donation-based fundraising campaign
      </div>
      <!-- nf-title2 -->
      <div class="nf-button">
        <a href="startyourcampaign.php">Start your campaign today!</a>
      </div>
      <!-- nf-button -->
    </div>
	<!-- need-fundraise -->
</div>
</section>  
<? include_once('footer.php');?>
<script src="js/jquery.waypoints.min.js"></script>
<script src="js/sticky.min.js"></script>
<script src="js/jquery.textfill.min.js"></script>
<script src="cms/bower_components/sweetalert2/sweetalert2.min.js"></script>
<script>
$(document).on('click', '.chkamt', function(){
	var amount = $(this).attr('value');
	$('#txbamount').val(amount);
	$('#btnsubmit').click();
});
//Rewards
$(document).on('click', '.chkamount', function(){
	var amount = $(this).attr('value');
	var rewardid = $(this).attr('donateid');
	$('#txbamount').val(amount);
	$('#txbrewardid').val(rewardid);
	$('#btnsubmit').click();
});
//Rewards
var sticky = new Waypoint.Sticky({
  element: $('.sticky-element')[0]
})
$(document).on('click', '#donatenow', function(){
	//Donate Now
	//$("html, body").animate({ scrollTop: $("#donate_element").scrollTop() }, 1000);
	var scrollTop     = $(window).scrollTop(),
    elementOffset = $('#donate_element').offset().top,
    distance      = (elementOffset - scrollTop);
	$('html,body').animate({ scrollTop: elementOffset }, 'slow');
	$('.sticky-element').removeClass('stuck');
});
$(document).ready(function() {
    $('.jtextfill').textfill({ maxFontPixels: 60 });
});
//Popup for Facebook
$(document).on('click', '#facebookpopup', function(){
	var textfacebook = "<span style='margin-top:12px; display:block; text-align:left'>Facebook may prompt you to add a donation button as seen below.<br><b style='color:red'><i>PLEASE DO NOT SELECT NONPROFIT.</b></i> If this message appears simply close the window by selecting the X in the upper right corner.<br><b><i><?php echo sWEBSITENAME; ?> has no control over donations made through Facebook.</i></b><br><img src='<?=sHOMES?>images/facebook_popup.jpg' width='100%' /></span>";
	//var textfacebook = "If Facebook asks you to add a donation button, <br><b><i><u>please do not Select Nonprofit!</u></i></b> Close window by selecting the X in the upper right corner. <br><br><?php echo sWEBSITENAME; ?> has no control over donations made through Facebook when a donation button is added.<br><img src='<?=sHOMES?>images/facebook_popup.jpg' width='100%' />";
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
$(document).on('click', '.chkrewarddesc', function(){
	var desc = '<div style="line-height:30px;">'+$(this).attr('desc')+'</div>';
	var desc_details = '<div style="margin-top:20px;">'+$(this).attr('desc_details')+'</div>';
	swal({   
        title: desc,
        html: desc_details, 
        type: "info",
	});
});
</script>
<script src="js/video.js"></script>
<script src="css/owl.carousel/owl.carousel.min.js"></script>
<script src="css/owl.carousel/owl.custom.js"></script>
<script src="bars/bars.js"></script>
</body>
</html>