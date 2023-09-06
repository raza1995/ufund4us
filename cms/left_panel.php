<?php 
if (basename($_SERVER['PHP_SELF']) == 'dashboard.php') {
	$sLeftMenuDashboard = 'active';
} else {
	$sLeftMenuDashboard = '';
} 
$accessr=$oregister->getuserroledetail($_SESSION['role_id']);
$acArray=array();
foreach($accessr as $k=>$v)
{
	if($v['fld_view']==1)
	{
		$acArray['view'][]=$v['fld_module_id'];
	}
	if($v['fld_add']==1)
	{
		$acArray['add'][]=$v['fld_module_id'];
	}
	if($v['fld_edit']==1)
	{
		$acArray['edit'][]=$v['fld_module_id'];
	}
	if($v['fld_delete']==1)
	{
		$acArray['delete'][]=$v['fld_module_id'];
	}
	
	
}
if($_SESSION['uid']) {
	$encoded_link = base64_encode("uid=".$_SESSION['uid']."&auth=1&redirect=1");
}

checkSetInArrayAndReturn($acArray, 'view', '');

//print_r($acArray);
$sStartCampMenu = isset($sStartCampMenu) ? $sStartCampMenu : "";
$sLeftMenuCampaign = isset($sLeftMenuCampaign) ? $sLeftMenuCampaign : "";
$sLeftMenuJoinCampaign = isset($sLeftMenuJoinCampaign) ? $sLeftMenuJoinCampaign : "";
$sLeftMenuHierarchy = isset($sLeftMenuHierarchy) ? $sLeftMenuHierarchy : "";
$sLeftMenuProspectLink = isset($sLeftMenuProspectLink) ? $sLeftMenuProspectLink : "";
$sGenerateLink = isset($sGenerateLink) ? $sGenerateLink : "";
$sLeftMenuDonors = isset($sLeftMenuDonors) ? $sLeftMenuDonors : "";
$sLeftMenuParticipants = isset($sLeftMenuParticipants) ? $sLeftMenuParticipants : "";
$sLeftApplicationSettings = isset($sLeftApplicationSettings) ? $sLeftApplicationSettings : "";
$sLeftMenuCommission = isset($sLeftMenuCommission) ? $sLeftMenuCommission : "";
$sLeftMenuPayments = isset($sLeftMenuPayments) ? $sLeftMenuPayments : "";
$sEmailManage = isset($sEmailManage) ? $sEmailManage : "";
$sUnsubscribedLink = isset($sUnsubscribedLink) ? $sUnsubscribedLink : "";
$sLeftMenuMaintenance = isset($sLeftMenuMaintenance) ? $sLeftMenuMaintenance : "";
$sLeftMenuReport = isset($sLeftMenuReport) ? $sLeftMenuReport : "";
$sParticipantLink = isset($sParticipantLink) ? $sParticipantLink : "";
$sCampaignManagerLink = isset($sCampaignManagerLink) ? $sCampaignManagerLink : "";
$sRepresentativeLink = isset($sRepresentativeLink) ? $sRepresentativeLink : "";
$sStripeReport = isset($sStripeReport) ? $sStripeReport : "";
$sStripeAccountReport = isset($sStripeAccountReport) ? $sStripeAccountReport : "";
$sAccountVerify = isset($sAccountVerify) ? $sAccountVerify : "";

$sSettingsLink = isset($sSettingsLink) ? $sSettingsLink : "";
$sDonorLink = isset($sDonorLink) ? $sDonorLink : "";
$sDistributorLink = isset($sDistributorLink) ? $sDistributorLink : "";
$sAdminLink = isset($sAdminLink) ? $sAdminLink : "";
$sLeftMenuNewsletters = isset($sLeftMenuNewsletters) ? $sLeftMenuNewsletters : "";
$sLeftMenuUsers = isset($sLeftMenuUsers) ? $sLeftMenuUsers : "";
$sUsersLink = isset($sUsersLink) ? $sUsersLink : "";
$sRoleLink = isset($sRoleLink) ? $sRoleLink : "";

?>
<!-- Navigation -->
   <div class="main-sidebar">
<div class="navbar-default sidebar nicescroll " role="navigation" style="position: initial;float: left;overflow: hidden;outline: none;">
    <div class="sidebar-nav navbar-collapse ">
      <ul class="nav" id="side-menu">
        <li <?=$sLeftMenuDashboard;?>><a href="dashboard.php" class="waves-effect"><img src="dist/img/img5.png"> <span>Dashboard</span> </a></li>
		<?php if(in_array("1",$acArray['view'])){?>
		<li <?=$sStartCampMenu?>><a href="start_campaign.php" class="waves-effect"><img src="dist/img/img6.png"> <span>Start New Campaign </span></a></li>
		<? } ?>

		<?php if(in_array("2",$acArray['view'])){?>
		<li class="<?=$sLeftMenuCampaign?>"><a href="manage_campaign.php" class="waves-effect"><img src="dist/img/img8.png"><span> Manage Campaign</span> </a></li>
		<? } ?>


		<?php if($_SESSION['role_id'] == 1) { ?>
			<li class="<?=$sLeftMenuCampaignParticipant?>"><a href="application_fee.php" class="waves-effect"><img src="dist/img/img25.png"><span> Application Fee</span> </a></li>
		<? } ?>
		
		<?php if(in_array("13",$acArray['view'])){?>
		<li class="<?=$sLeftMenuCampaignParticipant?>"><a href="manage_campaign_participant.php" class="waves-effect"><img src="dist/img/img8.png"><span> Manage Campaign</span> </a></li>
		<? } ?>

		<?php if(in_array("12",$acArray['view'])){?>
		<li class="<?=$sLeftMenuManageDonors?>"><a href="manage_donors.php" class="waves-effect"><img src="dist/img/img20.png"><span> Manage Donors</span> </a></li>
		<? } ?>
		
		<?php if(in_array("3",$acArray['view'])){?>
		<li class="<?=$sLeftMenuJoinCampaign?>"><a href="join_campaign.php" class="waves-effect"><img src="dist/img/img010.png"> <span>Join Campaign </span></a></li>
		<? } ?>
		
		<?php if(in_array("9",$acArray['view'])){?>
		<li class="<?=$sLeftMenuHierarchy?>"><a href="hierarchy_screen.php" class="waves-effect"><img src="dist/img/tree.png"> <span>Hierarchy Screen </span></a></li>
		<? } ?>
		
		<?php if($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {?>
		<?php if(in_array("21",$acArray['view'])){?>
			<li class="<?=$sLeftMenuProspectLink?>"><a href="#" class="waves-effect" data-toggle="modal" data-target="#prospectlink"><img src="dist/img/url-icon.png"> <span>Prospect Link </span></a></li>
		<? } ?>
		<? } ?>

		<?php if(in_array("9",$acArray['view'])){?>
		<li class="<?=$sGenerateLink?>"><a href="generate_link.php" class="waves-effect"><img src="dist/img/img15.png"><span> Generate Link</span> </a></li>
		<? } ?>
		
		
		<?php if(in_array("19",$acArray['view'])){?>
		<li class="<?=$sLeftMenuDonors?>"><a href="donors.php" class="waves-effect"><img src="dist/img/img14.png"> <span>Donors </span></a></li>
		<? } ?>
		
		
		<?php if(in_array("18",$acArray['view'])){?>
		<li class="<?=$sLeftMenuParticipants?>"><a href="participants.php" class="waves-effect"><img src="dist/img/img13.png"> <span>Participants</span> </a></li>
		<? } ?>

		<?php if( $_SESSION['role_id'] == 1 ){?>
		<li class="<?=$sLeftApplicationSettings?>"><a href="application_settings.php" class="waves-effect"><img src="dist/img/img9.png"> <span>Check Address</span> </a></li>
		<? } ?>
		
		
		<!--
		<?php if(in_array("11",$acArray['view'])){?>
		<li class="<?=$sLeftMenuCommission?>"><a href="commission.php" class="waves-effect"><img src="dist/img/img16.png"> <span>Commissions</span> </a></li>
		<? } ?>
		-->
		<?php if(in_array("17",$acArray['view'])){?>
		<li class="<?=$sLeftMenuPayments?>"><a href="manage_payments.php" class="waves-effect"><img src="dist/img/img25.png"> <span>Manage Payments</span> </a></li>
		<? } ?>
		<?php if(in_array("16",$acArray['view'])){?>
		<li class="<?=$sEmailManage?>"><a href="email_manage.php" class="waves-effect"><img src="dist/img/img23.png"> <span>Email Management</span> </a></li>
		<? } ?>
		
		<?php if(in_array("15",$acArray['view'])){?>
		<li class="<?=$sUnsubscribedLink?>"><a href="unsubscribe.php" class="waves-effect"><img src="dist/img/img16.png"> <span>Unsubscribes</span> </a></li>
		<? } ?>
		
        
        <?php if(in_array("7",$acArray['view'])){?>
		<li class="<?=$sLeftMenuMaintenance?>"> <a href="#" class="waves-effect"><img src="dist/img/img9.png"> <span>Commission </span><div class="fa arrow"></div></a>
          <ul class="nav nav-second-level">
			<?php if(in_array("4",$acArray['view'])){?>
            <li> <a href="global_settings.php" <?=$sSettingsLink?>>Distributor Commission Settings</a> </li>
			<? } ?>
          </ul>
          <!-- /.nav-second-level -->
        </li>
		<?php } ?>


		<?php 
		/*
		if(in_array("22",$acArray['view'])){?>
			<li  class=""> <a href="application_fee.php" class="waves-effect"><img src="dist/img/img9.png"> <span>Application Fee</span></a></li>
		<? } 
		*/
		?>
		
		<?php if(in_array("8",$acArray['view'])){?>
		<li class="<?=$sLeftMenuReport?>"> <a href="#" class="waves-effect"><img src="dist/img/img10.png"> <span>Reports </span><div class="fa arrow"></div></a>
          <ul class="nav nav-second-level">
			<?php if($_SESSION['role_id'] == 2) {?>
            <!--<li> <a href="report_donors.php" <?=$sDonorLink?>>Donors</a> </li>-->
            <li> <a href="report_participants.php" <?=$sParticipantLink?>>Participants</a> </li>
			<? } elseif($_SESSION['role_id'] == 6) { ?>
			<!--<li> <a href="report_donors.php" <?=$sDonorLink?>>Donors</a> </li>-->
            <li> <a href="report_participants.php" <?=$sParticipantLink?>>Participants</a> </li>
            <li> <a href="report_campaignmanager.php" <?=$sCampaignManagerLink?>>Campaign Manager</a> </li>
			<? } elseif($_SESSION['role_id'] == 3) { ?>
			<!--<li> <a href="report_donors.php" <?=$sDonorLink?>>Donors</a> </li>-->
            <li> <a href="report_participants.php" <?=$sParticipantLink?>>Participants</a> </li>
            <li> <a href="report_campaignmanager.php" <?=$sCampaignManagerLink?>>Campaign Manager</a> </li>
            <li> <a href="report_representative.php" <?=$sRepresentativeLink?>>Representative</a> </li>
			<? } elseif($_SESSION['role_id'] == 1) { ?>
			<!--<li> <a href="report_donors.php" <?=$sDonorLink?>>Donors</a> </li>-->
            <li> <a href="report_participants.php" <?=$sParticipantLink?>>Participants</a> </li>
            <li> <a href="report_campaignmanager.php" <?=$sCampaignManagerLink?>>Campaign Manager</a> </li>
            <li> <a href="report_representative.php" <?=$sRepresentativeLink?>>Representative</a> </li>
            <!--<li> <a href="report_distributor.php" <?=$sDistributorLink?>>Distributor</a> </li>-->
            <!--<li> <a href="report_admin.php" <?=$sAdminLink?>>UFund Admin</a> </li>-->
            <li> <a href="report_stripe.php" <?=$sStripeReport?>>Stripe Reports</a> </li>
			<li> <a href="report_accounts.php" <?=$sStripeAccountReport?>>Stripe Accounts Report</a> </li>
            <li> <a href="report_accountverify.php" <?=$sAccountVerify?>>Account Verify Reports</a> </li>
			<? } ?>
          </ul>
          <!-- /.nav-second-level -->
        </li>
		<? } ?>
		
		<?php if(in_array("20",$acArray['view'])){?>
		<li  class="<?=$sLeftMenuNewsletters?>"> <a href="manage_newsletters.php" class="waves-effect"><img src="dist/img/img12.png"> <span>Manage Newsletters</span></a></li>
		<? } ?>

		<? if($_SESSION['role_id'] == 1) { ?>
			<?php if(in_array("21",$acArray['view'])){?>
				<li  class="<?php echo isset($sLeftMenuCheckPayment) ? $sLeftMenuCheckPayment : ''; ?>"> <a href="check_payment.php" class="waves-effect"><img src="dist/img/img26.png"> <span>Check Payment</span></a></li>
			<? } ?>
		<? } ?>
		
		<?php if(in_array("5",$acArray['view'])){?>
		<li  class="<?=$sLeftMenuUsers?>"> <a href="#" class="waves-effect"><img src="dist/img/img12.png"> <span>Users & Roles</span> <div class="fa arrow"></div></a>
          <ul class="nav nav-second-level">
            <li> <a href="users.php" <?=$sUsersLink?>>Manage Users</a> </li>
			<?php if(in_array("6",$acArray['view'])){?>
			<li> <a href="roles.php" <?=$sRoleLink?>>Manage Roles</a> </li>
			<? } ?>
          </ul>
          <!-- /.nav-second-level -->
        </li>
		<? } ?>
		
      </ul>
    </div>
    <!-- /.sidebar-collapse -->
</div></div>
<div class="modal fade" id="prospectlink" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Prospect Link</h4>
				</div>
				<div class="modal-body">
					<div id="copiedlink">
					</div>
					<div class="input-group m-t-10">
							<input type="textbox" id="urllink" name="urllink" class="form-control" readonly value="<?php echo sHOMECMS."link.php?link=".$encoded_link;?>" required>
							<span class="input-group-btn">
								<button type="button" name="copylink" id="copylink" onclick="copyTextInsideId('urllink');" class="btn waves-effect waves-light btn-copy">Copy Link</button>
							</span>
						</div>
				</div>
				<div style="clear:both"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal"><span class="btn-label"><i class="fa fa-chevron-left"></i></span> Close</button>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		function copyTextInsideId(id){
			  /* Get the text field */
			  var copyText = document.getElementById(id);

			  /* Select the text field */
			  copyText.select();
			  copyText.setSelectionRange(0, 99999); /*For mobile devices*/

			  /* Copy the text inside the text field */
			  document.execCommand("copy");
			
			  /* Alert the copied text */
			  // alert("Copied the text: " + copyText.value);
		}
	</script>