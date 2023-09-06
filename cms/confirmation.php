<?
require_once("../configuration/dbconfig.php");
require_once(dirname(__FILE__)."/../login_check.php");

if ($_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5) {
	$oregister->redirect('dashboard.php');
}

$fld_nonprofit = 0;
$sStartCampMenu = 'active';
$_SESSION['campid'] = $_GET['cid'];
$start_campaign = 'start_campaign.php?m=e&cid='.$_GET['cid'].'';
$sPageName = '<li><a href="start_campaign.php?cid='.$_GET['cid'].'">Start New Campaign</a></li><li><a href="basic_information.php?cid='.$_GET['cid'].'">Basic Information</a></li><li><a href="build_team.php?cid='.$_GET['cid'].'">Build Team</a></li><li>Confirmation</li>';

if (array_key_exists('golive1', $_POST)) {
	$cid = $_GET['cid'];
	$uid = $_SESSION['uid'];
	$chkdate = $oCampaign->chkcampaign2($cid);
	$uid = $chkdate['fld_uid'];
	$camphash = $oregister->generatepasshash(20);
	$camp_status = $chkdate['fld_status'];
	$start_date = $chkdate['daysstart'];
	$end_date = $chkdate['daysend'];
	$startend_date = $chkdate['startenddate'];
	if ($start_date >= 0 && $start_date <= $startend_date && $camp_status == 1) {
		
		$oCampaign->makeitlive($cid, $uid, '1', $camphash);
		//$oCampaign->send_email_to_manager($cid, $uid);
		//$oCampaign->send_email_to_participants($cid, $uid);
		
	} elseif ($end_date < 0 && $camp_status == 1) {
		//Invalid
	}  else {
		$oCampaign->makeitlive($cid, $uid, '0', $camphash);
		//$oCampaign->send_email_to_participants($cid, $uid);
	}
	$oregister->redirect('golive.php?cid='.$cid.'');
}

if($_GET['cid'] > 0)
{
	$cid = $_GET['cid'];
	$uid = $_SESSION['uid'];
	$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
	$uid = $aCampaignDetail['fld_uid'];
	$galleryDetail = $oCampaign->getimagegallery($cid);
	$videoDetail = $oCampaign->getvideogallery($cid);
	
	
	$fld_campaign_title = $aCampaignDetail['fld_campaign_title'];
	if( $fld_campaign_title != '')
	{
	//Logo, Image, Image Gallery, Video Gallery
	//Logo Code Start
	$fld_campaign_logo = $aCampaignDetail['fld_campaign_logo'];
	$directory = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/uploads/logo/';
	$ext_logo = pathinfo($directory.$fld_campaign_logo, PATHINFO_EXTENSION);
	$size_logo = filesizeWithoutError('uploads/logo/'.$fld_campaign_logo);
	$makelogolink1 = '{name: "'.$fld_campaign_logo.'",size: '.$size_logo.',type: "image/'.$ext_logo.'",file: "'.$directory.$fld_campaign_logo.'"}';
	if ($fld_campaign_logo != '') {
		$makelogolink = $makelogolink1;
	} else {
		$makelogolink = '';
	}
	//Logo Code End
	//Image Code Start
	$directory = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/uploads/image/';
	$fld_campaign_image = $aCampaignDetail['fld_campaign_image'];
	$ext_image = pathinfo($directory.$fld_campaign_image, PATHINFO_EXTENSION);
	$size_image = filesizeWithoutError('uploads/image/'.$fld_campaign_image);
	$makeimagelink1 = '{name: "'.$fld_campaign_image.'",size: '.$size_image.',type: "image/'.$ext_image.'",file: "'.$directory.$fld_campaign_image.'"}';
	if ($fld_campaign_image != '') {
		$makeimagelink = $makeimagelink1;
	} else {
		$makeimagelink = '';
	}
	//Image Code End
	//Image Gallery Code Start
	$directory = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/uploads/imagegallery/';
	$itemsimagegallery = array();

	foreach($galleryDetail as $imagegallery) {
		$image_name = $imagegallery['fld_image'];
		$ext_image = pathinfo($directory.$imagegallery['fld_image'], PATHINFO_EXTENSION);
		$size_image = filesizeWithoutError('uploads/imagegallery/'.$imagegallery['fld_image']);
		$itemsimagegallery[] = '{name: "'.$image_name.'",size: '.$size_image.',type: "image/'.$ext_image.'",file: "'.$directory.$image_name.'"}';
	}
	$imagegallery2 = implode(",", $itemsimagegallery);
	$imagegallery = isset($imagegallery) ? $imagegallery : '';
	if (isset($imagegallery['fld_image']) && $imagegallery['fld_image'] != '') {
		$makeimagegallerylink = $imagegallery2;
	} else {
		$makeimagegallerylink = '';
	}
	//Image Gallery Code End
	//Video Gallery Code Start
	$directory = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/uploads/videogallery/';
	$itemsvideogallery = array();
	foreach($videoDetail as $videogallery) {
		$image_name = $videogallery['fld_video'];
		$ext_image = pathinfo($directory.$videogallery['fld_video'], PATHINFO_EXTENSION);
		$size_image = filesizeWithoutError('uploads/videogallery/'.$videogallery['fld_video']);
		$itemsvideogallery[] = '{name: "'.$image_name.'",size: '.$size_image.',type: "video/'.$ext_image.'",file: "'.$directory.$image_name.'"}';
	}
	$videogallery2 = implode(",", $itemsvideogallery);
	if ($videogallery['fld_video'] != '') {
		$makevideogallerylink = $videogallery2;
	} else {
		$makevideogallerylink = '';
	}
	//Video Gallery Code End
	//Logo, Image, Image Gallery, Video Gallery
	
	$fld_organization_name = $aCampaignDetail['fld_organization_name'];
	$fld_team_name = $aCampaignDetail['fld_team_name'];
	$fld_team_size = $aCampaignDetail['fld_team_size'];
	$fld_donor_size = $aCampaignDetail['fld_donor_size'];
	$fld_campaign_sdate = date('m/d/Y',strtotime($aCampaignDetail['fld_campaign_sdate']));
	$fld_campaign_edate = date('m/d/Y',strtotime($aCampaignDetail['fld_campaign_edate']));
	$fld_campaign_goal = number_format($aCampaignDetail['fld_campaign_goal'], 2, '.', ',');
	$fld_participant_goal = number_format($aCampaignDetail['fld_participant_goal'], 2, '.', ',');	
	$fld_nonprofit = $aCampaignDetail['fld_nonprofit'];	
	$fld_nonprofit_number = $aCampaignDetail['fld_nonprofit_number'];	
	$fld_desc1 = $aCampaignDetail['fld_desc1'];	
	$fld_desc2 = $aCampaignDetail['fld_desc2'];
	$fld_desc3 = $aCampaignDetail['fld_desc3'];
	$fld_donation_level1 = $aCampaignDetail['fld_donation_level1'];	
	$fld_donation_level2 = $aCampaignDetail['fld_donation_level2'];	
	$fld_donation_level3 = $aCampaignDetail['fld_donation_level3'];	
	}else{
		$fld_campaign_sdate = date('m/d/Y');
		$fld_campaign_edate = date('m/d/Y', strtotime("+20 days"));
	}
	$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
	$fld_cname = $aCampaignDetail['fld_cname'];
	$fld_cemail = $aCampaignDetail['fld_cemail'];
	$fld_cphone = $aCampaignDetail['fld_cphone'];
	$fld_caddress = $aCampaignDetail['fld_caddress'];
	$iCountry = $aCampaignDetail['fld_ccountry'];
	$iState = $aCampaignDetail['fld_cstate'];
	$fld_city = $aCampaignDetail['fld_ccity'];
	$fld_czipcode = $aCampaignDetail['fld_czipcode'];
	$aParticipantDetailSelected = $oCampaign->getparticipantdetailselected($cid, $uid, $_SESSION['role_id']);
}else{
	$oregister->redirect('manage_campaign.php');
}
$sSettingsData = $oregister->getsettingsdetail(1);
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
<title>Admin<?php echo sWEBSITENAME;?> - Confirmation</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<link href="bower_components/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
<link href="css/jquery.filer.css" type="text/css" rel="stylesheet" />
<link href="css/themes/jquery.filer-dragdropbox-theme.css" type="text/css" rel="stylesheet" />
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
		  <h1 class="h1styling">Confirmation</h1>
		  <div class="line3"></div>
		  <? if ($fld_campaign_title != '') { ?>
		  <h4 class="h4styling" ><?=$fld_campaign_title;?></h4>
		  <div class="line3"></div>
		  <? } ?>
		  <!-- .white-box -->
          <div class="white-box" style="        background: #F1F1F1;">
			<div class="Campaign_in">

<!--<div class="div_image">
</div>-->
<div class="formdiv-in">
   <form method="post">
    <h2>Campaign Information</h2>
	<div class="form-group col-sm-6">
		<label for="fld_cname" class="control-label">Campaign Manager Name<span style="color:#FF0000">*</span></label>
		<div><?=$fld_cname?></div>
	</div>
	<div class="form-group col-sm-6">
		<label for="fld_cemail" class="control-label">Email<span style="color:#FF0000">*</span></label>
		<div><?=$fld_cemail?></div>
	</div>
    <div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_cphone" class="control-label">Phone<span style="color:#FF0000">*</span></label>
		<div><?=$fld_cphone?></div>
    </div>

	<div class="form-group col-sm-6">
		<label for="fld_caddress" class="control-label">Address<span style="color:#FF0000">*</span></label>
		<div><?=$fld_caddress?></div>
    </div>
    <div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_czipcode" class="control-label">Zipcode<span style="color:#FF0000">*</span></label>
		<div><?=$fld_czipcode;?></div>
  	</div>

	<div class="form-group col-sm-6">
		<label for="fld_city" class="control-label">City<span style="color:#FF0000">*</span></label>
		<div><?=$fld_city;?></div>
	</div>
    <div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_state" class="control-label">State<span style="color:#FF0000">*</span></label>
		<div><?=$iState;?></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_country" class="control-label">Country<span style="color:#FF0000">*</span></label>
		<div><?=$iCountry;?></div>
	</div>
   <div class="clearfix"></div>
   
	<h2>Basic Information</h2>
	<div class="form-group col-sm-6">
		<label for="fld_campaign_title" class="control-label">Campaign Name<span style="color:#FF0000">*</span></label>
		<div><?=$fld_campaign_title?></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_organization_name" class="control-label">Organization Name<span style="color:#FF0000">*</span></label>
		<div><?=$fld_organization_name?></div>
	</div>
    <div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_team_size" class="control-label">Organization Size (Participants)<span style="color:#FF0000">*</span></label>
		<div><?=$fld_team_size?></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_donor_size" class="control-label">Donors Targeted by Participant<span style="color:#FF0000">*</span></label>
		<div><?=$fld_donor_size?></div>
	</div>
    <div class="clearfix"></div>
   
	<div class="form-group col-sm-6">
		<label for="fld_campaign_sdate" class="control-label">Start Date (Min 20 Days, Max 30 Days)<span style="color:#FF0000">*</span></label>
		<div><?=$fld_campaign_sdate?></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_campaign_edate" class="control-label">End Date<span style="color:#FF0000">*</span></label>
		<div><?=$fld_campaign_edate?></div>
	</div>
	<div class="clearfix"></div>
 
	<div class="form-group col-sm-6">
		<label for="fld_campaign_goal" class="control-label">Campaign Goal<span style="color:#FF0000">*</span></label>
		<div><?=$fld_campaign_goal?></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_participant_goal" class="control-label">Participant Goal<span style="color:#FF0000">*</span></label>
		<div><?=$fld_participant_goal?></div>
	</div>
	<div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_nonprofit_label" class="control-label">Are you a Non-Profit Organization<span style="color:#FF0000">*</span></label>
        <div class="col-sm-12">
			<div class="radio col-sm-3">
            <input disabled type="radio" class="nonprofitshowhide" name="fld_nonprofit[]" id="radio1" value="1" <? if ($fld_nonprofit == 1) {echo "checked";} ?> >
            <label for="radio1"> Yes </label>
			</div>
			<div class="radio col-sm-3" style="margin-top:10px">
			<input disabled type="radio" class="nonprofitshowhide" name="fld_nonprofit[]" id="radio2" value="0" <? if ($fld_nonprofit == 0) {echo "checked";} ?> >
            <label for="radio2"> No </label>
			</div>
            <div class="help-block with-errors" style="margin-top:10px"></div>
        </div>
	</div>
	
	<div class="form-group col-sm-6 nonprofithide" style="display:none;">
		<label for="fld_nonprofit_number" class="control-label">501c Nonprofit Number<span style="color:#FF0000">*</span></label>
		<div><?=$fld_nonprofit_number;?></div>
	</div>
	<div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_desc1" class="control-label">How Your Donation Will be Used?<span style="color:#FF0000">*</span></label>
		<div><?=$fld_desc1?></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_desc2" class="control-label">Why Donations Are Needed<span style="color:#FF0000">*</span></label>
		<div><?=$fld_desc2?></div>
	</div>
    <div class="clearfix"></div>
	<div class="form-group col-sm-6">
		<label for="fld_desc3" class="control-label">Thank You Note<span style="color:#FF0000">*</span></label>
		<div><?=$fld_desc3?></div>
	</div>
    <div class="clearfix"></div>
    <h2 class="head1">Logo</h2>
    <div class="colmd_12" style="background-color:#FFFFFF;     margin-bottom: 19px;">
    <div id="content" style="padding:20px;" align="center" class="choose_btn"><input style="display:none;" type="file" name="logo" id="logo" ></div>
	</div>
	
    <div class="clearfix"></div>
	<h2>Tell Your Story</h2>
	<? if ($makeimagegallerylink != '') { ?>
		<h2>Image Gallery</h2>
    	<div id="content"><input style="display:none;" type="file" name="galleryfiles[]" id="filer_input2" multiple="multiple"></div>
		<div class="clearfix"></div>
	<? } ?>

	<?php
	if ($makevideogallerylink != '') { ?>
		<h2>Video Gallery</h2>
    	<div id="content" style="padding:0px 20px 0px 20px;" align="center">
    		<input type="text" readonly placeholder="Youtube Video Link" class="form-control"  name="videofiles" id="videofiles" value=""/>
    	</div>
    	<div class="clearfix"></div>
	<? } ?>
	
   
			  
			  <div class="clearfix"></div>
	<div class="accordion_in acc_active div7" style="    padding-bottom: 22px;">
                    <h2 class="acc_head">Participants List</h2>
                    <div class="acc_content">
                      <div class="scrol">
                        <div class="tabdiv">
                          <div class="rowdiv">
                            <table class="tab3">
                              <thead>
                                <tr>
                                  <th>First Name</th>
								  <th>Last Name</th>
                                  <th>Email</th>
                                  <th>Phone</th>
                                </tr>
                              </thead>
							  <tbody id="tbodypartlist">
								<?
								$iCountRecords = count($aParticipantDetailSelected);
								if($iCountRecords>0){
									for($i=0;$i<$iCountRecords;$i++){
									$participant_sel_name = $aParticipantDetailSelected[$i]['uname'];	
									$participant_sel_lname = $aParticipantDetailSelected[$i]['ulname'];	
									$participant_sel_email = $aParticipantDetailSelected[$i]['uemail'];	
									$participant_sel_phone = $aParticipantDetailSelected[$i]['uphone'];	
									$participant_sel_id = $aParticipantDetailSelected[$i]['uid'];	
								?>
								<tr class="itemRow">
								  <td><?=$participant_sel_name;?></td>
								  <td><?=$participant_sel_lname;?></td>
								  <td><?=$participant_sel_email;?></td>
								  <td><?=$participant_sel_phone;?></td>
								</tr>
								<? } } else { ?>
								<tr class="itemRow empty">
								  <td colspan="5" align="center">There is no any Participant</td>
								</tr>
								<? } ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
				  <button class="btn btn-success waves-effect waves-light" type="submit" id="golive1" name="golive1" style="display:none"></button>
				  <button class="btn btn-success waves-effect waves-light" type="submit" name="submitform" id="submitform" style="display:none"></button>
				  <input type="hidden" name="fld_campaign_id" id="fld_campaign_id" value="<?=$_GET['cid']?>">
              
              
   <div class="clearfix"></div>
   <div class="form-group">
    	<div class="col-sm-6 basic-but-left" align="left">
			<button class="btn btn-primary waves-effect waves-light" type="button" onClick="step3(<?=$_GET['cid']?>)"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Back</button>
			<button class="btn btn-primary waves-effect waves-light" type="button" onclick="window.location.href='manage_campaign.php'"><span class="btn-label"><i class="fa fa-times"></i></span>Cancel</button>
		</div>
		
		<div class="col-sm-6 basic-but-right" align="right">
			<button class="btn btn-success waves-effect waves-light build_team-but" id="golive" type="button">Save Campaign <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
		</div>
   </div>
   <div class="clearfix"></div>
   </form>
   </div>
</div>
		  </div>
		  </div>
    </div>
    <!-- /.container-fluid -->
  </div></div>
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
<script type="text/javascript" src="js/jquery.filer.min.js?v=1.0.5"></script>
<script type="text/javascript" src="js/custom.js?v=1.0.5"></script>
<!--Wave Effects -->
<script src="js/waves.js"></script>
<!-- Custom Theme JavaScript -->
<script src="js/myadmin.js"></script>
<!--Counter js -->
<script src="bower_components/waypoints/lib/jquery.waypoints.js"></script>
<script src="bower_components/counterup/jquery.counterup.min.js"></script>
<script src="js/mask.js"></script>
<!--Sparkline charts js -->
<script src="bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
<script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
<script src="bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>
<script src="bower_components/sweetalert/sweetalert.min.js"></script>
<script src="bower_components/sweetalert/jquery.sweet-alert.custom.js"></script>
<script src="js/validator.js"></script>
<script>
$('.fa-check').hide();
    
    function parseDate(str) {
    var mdy = str.split('/')
    return new Date(mdy[2], mdy[0]-1, mdy[1]);
}


function daydiff(first, second) {
   return (second-first)/(1000*60*60*24)
}
$('.fa-check').css('color','transparent');
//Date picker
$('#fld_campaign_sdate').datepicker({
    format: 'mm/dd/yyyy',
	autoclose: true
});
$(document).on('click', '.campaignstartdate', function(){
	$('#fld_campaign_sdate').focus();
});


$('#fld_campaign_edate').datepicker({
	format: 'mm/dd/yyyy',
	autoclose: true
}).change(function () { 	var dif=daydiff(parseDate($('#fld_campaign_sdate').val()), parseDate($('#fld_campaign_edate').val()));
	
	if(dif<20 || dif>30)
	{
	  $('.pp').css('color','red');
      $('.fp').css('color','#c44');
	  $('.fa-check').show();
	  document.getElementById('fld_campaign_edate').setCustomValidity('Campaigns must be between 20 to 30 days long. First check can be requested at 1/2 way point of campaign');
     
	}
	else{
		$('.fa-check').hide();
		document.getElementById('fld_campaign_edate').setCustomValidity('');
	}
});
$(document).on('click', '.campaignenddate', function(){
	$('#fld_campaign_edate').focus();
});
</script>
<script type="text/javascript" src="js/accounting.js"></script>
<script>
function addCommas(x,txtname) {

	var mval = accounting.formatMoney(x); 
	mval = mval.replace('$', '');
	document.getElementById(txtname).value = mval;
}

var nonprofit = <?=$fld_nonprofit;?>;
if (nonprofit == 1) {
	$('.nonprofithide').show();
}

$('.nonprofitshowhide').on('click', function() {
	var checkedvalue = $(this).val();
	if (checkedvalue == 1) {
		$('.nonprofithide').show();
	} else {
		$('.nonprofithide').hide();
	}
});

$('#exp1').on('click', function() {
	swal("How Your Donation Will be Used", "The profits from this fundraiser will go to help pay for tournaments, busing, umpires, balls, field upkeep, insurance, uniforms, and all the other necessities to get our players ready and stay safe for the upcoming season.\n \n <i><b>Note: Highlight the above copy and press control c to cut and then control v to paste into your campaign</b></i>");
});

$('#exp2').on('click', function() {
	swal("Why Donations Are Needed", "Your donation is very important to our teams because without it we would not have a team.  School budget cuts have been so drastic in our district that we receive very little financial support.  Everything that is paid out for the team is paid with money that has been raised through sponsorship, donations, and fundraiser profits.\n \n <i><b>Note: Highlight the above copy and press control c to cut and then control v to paste into your campaign</b></i>");
});

$('#exp3').on('click', function() {
	swal("Thank You Note", "On behalf of John Wilson's players, coaches, and boosters, I would like to thank you for your help in making this program possible.  This is a very important time in these young men's lives. \n Thank you again for being a part of it.\n \nCoach\n \n <i><b>Note: Highlight the above copy and press control c to cut and then control v to paste into your campaign</b></i>");
});

$('#exp4').on('click', function() {
	swal("Donors Targeted by Participant", "The Required # of Donors Targeted by Participant, this is the number of email address you require each participant to upload to the campaign.  These email address will only be used during your campaign and will not be used for any other form of solicitation.");
});
	 
$('.forimagegallery').hide();
$('.forvideogallery').hide();
$('#imagebutton').on('click', function() {
	 $('#videobutton').removeClass('btn-primary');
	 $('#videobutton').addClass('btn-success');
	 $(this).removeClass('btn-success');
	 $(this).addClass('btn-primary');
	 $('.forimagegallery').show();
	 $('.forvideogallery').hide();
});
$('#videobutton').on('click', function() {
	 $('#imagebutton').removeClass('btn-primary');
	 $('#imagebutton').addClass('btn-success');
	 $(this).removeClass('btn-success');
	 $(this).addClass('btn-primary');
	 $('.forimagegallery').hide();
	 $('.forvideogallery').show();
}); 
	 
function step1(id){
	window.location.href = 'start_campaign.php?cid='+id;
}

function step2(id){
	window.location.href = 'basic_information.php?cid='+id;
}
function step3(id){
	window.location.href = 'build_team.php?cid='+id;
}

$(document).on('blur', '#fld_campaign_goal', function(){
	var fld_team_size = parseFloat($('#fld_team_size').val().replace(/,/g, ''));
	var fld_campaign_goal = parseFloat($('#fld_campaign_goal').val().replace(/,/g, ''));
	if (fld_campaign_goal != '' && fld_team_size != '') {
		var total_fld_participant_goal = fld_campaign_goal/fld_team_size;
		addCommas(total_fld_participant_goal, 'fld_participant_goal')
	}
});

$(document).on('blur', '#fld_participant_goal', function(){
	var fld_team_size = parseFloat($('#fld_team_size').val().replace(/,/g, ''));
	var fld_participant_goal = parseFloat($('#fld_participant_goal').val().replace(/,/g, ''));
	if (fld_participant_goal != '' && fld_team_size != '') {
		var total_fld_campaign_goal = fld_participant_goal*fld_team_size;
		addCommas(total_fld_campaign_goal, 'fld_campaign_goal')
	}
});

$(document).on('blur', '#fld_team_size', function(){
	var fld_team_size = parseFloat($('#fld_team_size').val().replace(/,/g, ''));
	var fld_campaign_goal = parseFloat($('#fld_campaign_goal').val().replace(/,/g, ''));
	if (fld_campaign_goal != '' && fld_team_size != '') {
		var total_fld_participant_goal = fld_campaign_goal/fld_team_size;
		addCommas(total_fld_participant_goal, 'fld_participant_goal')
	}
});

$(document).ready(function() {
	//Logo
     $("#logo").filer({
        limit: 1,
        maxSize: 15, //FileSize in MB
        extensions: ['jpg','gif','png','JPG','JPEG','GIF','BMP','bmp','PNG'],
		
        changeInput: '',
        showThumbs: true,
        theme: "dragdropbox",
        templates: {
            box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
            item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        <span class="jFiler-item-others">{{fi-size2}}</span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <li>{{fi-progressBar}}</li>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            itemAppend: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                            <span class="jFiler-item-others">{{fi-size2}}</span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
                                            <li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
                                        </ul>\
                                        <ul class="list-inline pull-right">\
                                            <li></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
            progressBar: '<div class="bar"></div>',
            itemAppendToEnd: false,
            removeConfirmation: true,
            _selectors: {
                list: '.jFiler-items-list',
                item: '.jFiler-item',
                progressBar: '.bar',
                remove: '.jFiler-item-trash-action'
            }
        },
        dragDrop: {
            dragEnter: null,
            dragLeave: null,
            drop: null,
        },
        uploadFile: {
            url: "./php/upload_logo.php",
			data:{cid:$("#fld_campaign_id").val()},
            type: 'POST',
            enctype: 'multipart/form-data',
            beforeSend: function(){},
            success: function(data, el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            error: function(el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            statusCode: null,
            onProgress: null,
            onComplete: null
        },
        files: [<?=$makelogolink;?>],
        addMore: false,
        clipBoardPaste: true,
        excludeName: null,
        beforeRender: null,
        afterRender: null,
        beforeShow: null,
        beforeSelect: null,
        onSelect: null,
        afterShow: null,
        onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
            var file = file.name;
			var cid = $("#fld_campaign_id").val();
            $.post('./php/remove_logo.php', {file: file, cid: cid});
        },
        onEmpty: null,
        options: null,
        captions: {
            button: "Choose Files",
            feedback: "Choose files To Upload",
            feedback2: "files were chosen",
            drop: "Drop file here to Upload",
            removeConfirmation: "Are you sure you want to remove this file?",
            errors: {
                filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                filesType: "Only Images are allowed to be uploaded.",
                filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                filesSizeAll: "Files you’ve chosen are too large! Please upload files up to {{fi-maxSize}} MB."
            }
        }
    });
	
	//Image
/*    $("#filer_input_1").filer({
        limit: null,
        maxSize: null,
        extensions: null,
        changeInput: '<div class="jFiler-input-dragDrop" style="border:none!important"><div class="jFiler-input-inner"><a class="jFiler-input-choose-btn blue">CHOOSE IMAGE</a></div></div>',
        showThumbs: true,
        theme: "dragdropbox",
        templates: {
            box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
            item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        <span class="jFiler-item-others">{{fi-size2}}</span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <li>{{fi-progressBar}}</li>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            itemAppend: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                            <span class="jFiler-item-others">{{fi-size2}}</span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
                                            <li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
                                        </ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
            progressBar: '<div class="bar"></div>',
            itemAppendToEnd: false,
            removeConfirmation: true,
            _selectors: {
                list: '.jFiler-items-list',
                item: '.jFiler-item',
                progressBar: '.bar',
                remove: '.jFiler-item-trash-action'
            }
        },
        dragDrop: {
            dragEnter: null,
            dragLeave: null,
            drop: null,
        },
        uploadFile: {
            url: "./php/upload_image.php",
            data:{cid:$("#fld_campaign_id").val()},
            type: 'POST',
            enctype: 'multipart/form-data',
            beforeSend: function(){},
            success: function(data, el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            error: function(el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            statusCode: null,
            onProgress: null,
            onComplete: null
        },
        files: [<?=$makeimagelink;?>],
        addMore: false,
        clipBoardPaste: true,
        excludeName: null,
        beforeRender: null,
        afterRender: null,
        beforeShow: null,
        beforeSelect: null,
        onSelect: null,
        afterShow: null,
        onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
            var file = file.name;
			var cid = $("#fld_campaign_id").val();
            $.post('./php/remove_image.php', {file: file, cid: cid});
        },
        onEmpty: null,
        options: null,
        captions: {
            button: "Choose Files",
            feedback: "Choose files To Upload",
            feedback2: "files were chosen",
            drop: "Drop file here to Upload",
            removeConfirmation: "Are you sure you want to remove this file?",
            errors: {
                filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                filesType: "Only Images are allowed to be uploaded.",
                filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                filesSizeAll: "Files you’ve chosen are too large! Please upload files up to {{fi-maxSize}} MB."
            }
        }
    });*/
	
    //Image Gallery
    $("#filer_input2").filer({
        limit: null,
        maxSize: 15,
        extensions: ['jpg','gif','png','JPG','JPEG','GIF','BMP','bmp','PNG'],
        changeInput: '',
        showThumbs: true,
        theme: "dragdropbox",
        templates: {
            box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
            item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        <span class="jFiler-item-others">{{fi-size2}}</span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <li>{{fi-progressBar}}</li>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            itemAppend: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                            <span class="jFiler-item-others">{{fi-size2}}</span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
                                            <li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
                                        </ul>\
                                        <ul class="list-inline pull-right">\
                                            <li></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
            progressBar: '<div class="bar"></div>',
            itemAppendToEnd: false,
            removeConfirmation: true,
            _selectors: {
                list: '.jFiler-items-list',
                item: '.jFiler-item',
                progressBar: '.bar',
                remove: '.jFiler-item-trash-action'
            }
        },
        dragDrop: {
            dragEnter: null,
            dragLeave: null,
            drop: null,
        },
        uploadFile: {
            url: "./php/upload_imagegallery.php",
            data:{cid:$("#fld_campaign_id").val()},
            type: 'POST',
            enctype: 'multipart/form-data',
            beforeSend: function(){},
            success: function(data, el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            error: function(el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            statusCode: null,
            onProgress: null,
            onComplete: null
        },
        files: [<?=$makeimagegallerylink;?>],
        addMore: false,
        clipBoardPaste: true,
        excludeName: null,
        beforeRender: null,
        afterRender: null,
        beforeShow: null,
        beforeSelect: null,
        onSelect: null,
        afterShow: null,
        onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
            var file = file.name;
			var cid = $("#fld_campaign_id").val();
            $.post('./php/remove_imagegallery.php', {file: file, cid: cid});
        },
        onEmpty: null,
        options: null,
        captions: {
            button: "Choose Files",
            feedback: "Choose files To Upload",
            feedback2: "files were chosen",
            drop: "Drop file here to Upload",
            removeConfirmation: "Are you sure you want to remove this file?",
            errors: {
                filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                filesType: "Only Images are allowed to be uploaded.",
                filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                filesSizeAll: "Files you’ve chosen are too large! Please upload files up to {{fi-maxSize}} MB."
            }
        }
    });
	
	
	//Video Gallery
	/*$("#videos").filer({
        limit: null,
        maxSize: 750,
        extensions: ['mov','flv','mp4','avi','3gp','MOV','FLV','MP4','AVI','3GP'],
        changeInput: '',
        showThumbs: true,
        theme: "dragdropbox",
        templates: {
            box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
            item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        <span class="jFiler-item-others">{{fi-size2}}</span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <li>{{fi-progressBar}}</li>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            itemAppend: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                            <span class="jFiler-item-others">{{fi-size2}}</span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
                                            <li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
                                        </ul>\
                                        <ul class="list-inline pull-right">\
                                            <li></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
            progressBar: '<div class="bar"></div>',
            itemAppendToEnd: false,
            removeConfirmation: true,
            _selectors: {
                list: '.jFiler-items-list',
                item: '.jFiler-item',
                progressBar: '.bar',
                remove: '.jFiler-item-trash-action'
            }
        },
        dragDrop: {
            dragEnter: null,
            dragLeave: null,
            drop: null,
        },
        uploadFile: {
            url: "./php/upload_videogallery.php",
            data:{cid:$("#fld_campaign_id").val()},
            type: 'POST',
            enctype: 'multipart/form-data',
            beforeSend: function(){},
            success: function(data, el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            error: function(el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            statusCode: null,
            onProgress: null,
            onComplete: null
        },
        files: [<?=$makevideogallerylink;?>],
        addMore: false,
        clipBoardPaste: true,
        excludeName: null,
        beforeRender: null,
        afterRender: null,
        beforeShow: null,
        beforeSelect: null,
        onSelect: null,
        afterShow: null,
        onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
            var file = file.name;
			var cid = $("#fld_campaign_id").val();
            $.post('./php/remove_videogallery.php', {file: file, cid: cid});
        },
        onEmpty: null,
        options: null,
        captions: {
            button: "Choose Files",
            feedback: "Choose files To Upload",
            feedback2: "files were chosen",
            drop: "Drop file here to Upload",
            removeConfirmation: "Are you sure you want to remove this file?",
            errors: {
                filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                filesType: "Only Images are allowed to be uploaded.",
                filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                filesSizeAll: "Files you’ve chosen are too large! Please upload files up to {{fi-maxSize}} MB."
            }
        }
    });*/
});


$('#golive').click(function(){
	$('#golive1').click();
});


</script>

</body>
</html>
<? include_once('bottom.php');?>