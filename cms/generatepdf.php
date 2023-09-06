<?
require_once("../configuration/dbconfig.php");
if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
}
if($_GET['cid'] > 0)
{
	$cid = $_GET['cid'];
	$uid = $_SESSION['uid'];
	$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
	$aParticipantDetail = $oCampaign->getparticipantdetail($cid, $uid);
	$roleid = $_SESSION['role_id'];
	$aParticipantDetailSelected = $oCampaign->getparticipantdetailselected($cid, $uid, $roleid);
	$fld_campaign_title = $aCampaignDetail['fld_campaign_title'];
	$fld_organization_name = $aCampaignDetail['fld_organization_name'];
	$fld_team_name = $aCampaignDetail['fld_team_name'];
	$fld_team_size = $aCampaignDetail['fld_team_size'];
	$fld_donor_size = $aCampaignDetail['fld_donor_size'];
	$fld_campaign_id = str_pad($aCampaignDetail['fld_campaign_id'], 7, "0", STR_PAD_LEFT);
	$fld_campaign_sdate = date('m/d/Y',strtotime($aCampaignDetail['fld_campaign_sdate']));
	$fld_campaign_edate = date('m/d/Y',strtotime($aCampaignDetail['fld_campaign_edate']));
	$fld_campaign_goal = $aCampaignDetail['fld_campaign_goal'];
	$fld_participant_goal = $aCampaignDetail['fld_participant_goal'];	
	$fld_desc1 = $aCampaignDetail['fld_desc1'];	
	$fld_desc2 = $aCampaignDetail['fld_desc2'];	
	$fld_donation_level1 = $aCampaignDetail['fld_donation_level1'];	
	$fld_donation_level2 = $aCampaignDetail['fld_donation_level2'];	
	$fld_donation_level3 = $aCampaignDetail['fld_donation_level3'];
	$fld_live = $aCampaignDetail['fld_live'];
	
	$aCampaignGraphTotal = $oCampaign->getcampaigngraphtotal2($cid);
	$campaign_goal = $aCampaignGraphTotal['campaign_goal'];
	$campaign_raised = $aCampaignGraphTotal['campaign_raised'];
	$campaign_graph_total_per = ($campaign_raised / $campaign_goal) * 100;
}
					$header = '
					<table width="100%" border="0">
					<tbody>
						<tr>
							<td width="45%" align="left"><img src="emails/logo.png" width="35%" height="15%" /></td>
							<td width="10%">&nbsp;</td>
							<td width="45%" align="right"><img src="emails/logo.png" width="35%" height="15%" /></td>
						</tr>
					</tbody>
					</table>';
					$html = '<h2 align="center" style="font-family:arial"><b><u><i>Instructions to Join '.$fld_campaign_title.'</i></u></b></h2>';
					$html .= '<h2 align="center" style="font-family:arial; font-size:18px"><b>Campaign starts '.$fld_campaign_sdate.' and ends '.$fld_campaign_edate.'</b></h2>';
					$html .= '<div align="left" style="font-family:arial; margin-top:40px; font-size:16px"><b>Step by step instructions to become a participant:</b></div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:50px; font-size:16px">Log onto '.SITE_DOMAIN.'</div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:50px; font-size:16px">Select Join Campaign</div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:100px; font-size:16px">Enter campaign # <b><u>'.$fld_campaign_id.'</u></b> & campaign ID # <b><u>'.$fld_campaign_id.'</u></b></div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:40px; font-size:16px"><b>Create and account (follow the on screen instructions):</b></div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:50px; font-size:16px">After creating your account, you will be prompted to enter your email and password again. This will take you to edit your profile.</div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:100px; font-size:16px">If you already have an account login as shown on the page.</div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:40px; font-size:16px"><b>Enter your profile:</b></div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:50px; font-size:16px">Upload your profile picture (this will be used to personalize your campaign)</div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:100px; font-size:16px"><b><u>Save and continue</u></b></div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:40px; font-size:16px"><b>Enter your donors:</b></div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:50px; font-size:16px">You are required to enter '.str_pad($fld_donor_size, 3, "0", STR_PAD_LEFT).' potential donor email addresses.</div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:50px; font-size:16px">You can log back in at any time to enter additional donors.</div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:100px; font-size:16px">Select <b><u>Update</u></b> to save your donors.</div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:100px; font-size:16px">Select <b><u>Preview</u></b> to see your campaign letter.</div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:40px; margin-left:150px; font-size:20px"><b><i><u>Thank you for joining '.$fld_campaign_title.'</u></i></b></div>';
					$html .= '<div align="left" style="font-family:arial; margin-top:40px; margin-left:150px; font-size:26px"><b><i>"If you fail to plan, you are planning to fail."</i></b></div>';
					$html .= '<div align="left" style="font-family:new times roman; margin-top:-8px; margin-left:530px; font-size:18px"><i>by Benjamin Franklin</i></div>';
					$html .= '<div align="left" style="font-family:new times roman; margin-top:5px; margin-left:200px; font-size:18px">Sums up why 99% of all fundraising efforts fail to reach their goals.</div>';

					//__construct($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P')
					$configForMpdf = setVariableInAssocArrayForMpdf('c','A4','','',10,10,40,10,10,5);
					$mpdf=new \Mpdf\Mpdf($configForMpdf);
					
					$mpdf->SetHTMLHeader($header);
					$mpdf->WriteHTML($html);
					$datetime = date("mdY").'-'.date("his");
					/*$mpdf->Output(sHOMECMS . 'files/instructionsheet-'.$_SESSION['uname'].'-'.$datetime.'.pdf', 'F');
					$attachedfile = ''.sHOMECMS.'files/instructionsheet-'.$_SESSION['uname'].'-'.$datetime.'.pdf';
					$filename = 'instructionsheet-'.$_SESSION['uname'].'-'.$datetime.'.pdf';
					$path = ''.sHOMECMS.'files/';*/
					$mpdf->Output();


?>