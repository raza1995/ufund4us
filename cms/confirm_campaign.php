<?
   require_once("../configuration/dbconfig.php");
   $sPageName = '<li>Confirm Campaign</li>';
   $sLeftMenuJoinCampaign = 'active';
   if(!$_SESSION['uid'])
   {
   	$oregister->redirect('../sign-in.php');
   } else {
	if ($_SESSION['role_id'] == 4) {
		$oregister->redirect('dashboard.php');
	}
   }
   
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
   } else {
   	$oregister->redirect('join_campaign.php?msg=error');
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
   				if ($_SESSION['role_id'] == 5) {
					$oregister->redirect("participant_build_team.php?cid=$cid&uid=$uid");
				} else {
					$oregister->redirect('join_campaign.php?msg=success');
				}
   			}
   		}
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
      <title>Admin<?php echo sWEBSITENAME;?> - Confirm Campaign</title>
      <!-- Bootstrap Core CSS -->
      <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
      <!-- Menu CSS -->
      <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
      <!--My admin Custom CSS -->
      <link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
      <link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
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
                     <h1 class="h1styling">Confirm Campaign</h1>
                     <div class="line3"></div>
                     <!-- .white-box -->
                     <div class="white-box" style="    background: rgba(245, 245, 245, 0);    border: 0px solid #d9d6d6;">
                        <div class=" full-main" style="width:75% !important">
                           <form data-toggle="validator" method="post">
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
                                 <div class="col-sm-6">
                                    <button class="btn btn-primary waves-effect waves-light" type="button" onClick="window.location.href='join_campaign.php'"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Cancel</button>
                                 </div>
                                 <div class="col-sm-6" align="right">
                                    <input type="hidden" name="cuid" id="cuid" value="<?=$campmid;?>" />
                                    <input type="hidden" name="cid" id="cid" value="<?=$campno;?>" />
                                    <input type="hidden" name="cno" id="cno" value="<?=$campid;?>" />
                                    <button class="btn btn-success waves-effect waves-light" name="confirmcampaign" style="padding:10px 66px 11px 40px !important" type="submit">Confirm <span class="btn-label forright-icon" style="padding:12px 14px 32px 11px !important"><i class="fa fa-chevron-right"></i></span></button>
                                 </div>
                              </div>
                              <div class="clearfix"></div>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- /.container-fluid -->
      <!-- /#page-wrapper -->
      <!-- #footer -->
      <? include_once('footer.php');?>
      <!-- /#footer -->
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
      <script src="js/mask.js"></script>
      <!--Sparkline charts js -->
      <script src="bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
      <script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
      <!-- jQuery for carousel -->
      <script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
      <script src="bower_components/owl.carousel/owl.custom.js"></script>
      <script src="js/validator.js"></script>
      <script></script>
   </body>
</html>
<? include_once('bottom.php');?>