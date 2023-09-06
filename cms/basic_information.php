<?
require_once("../configuration/dbconfig.php");
require ('../lib/init.php');
$REQUEST = &$_REQUEST;
checkAndSetInArray($REQUEST,'cid', 0);

// echo '<pre>'; print_r($_SESSION); die();
$def_campaign_sdate = date('m/d/Y');
$def_campaign_edate = date('m/d/Y', strtotime("+20 days"));
$role_id = isset($_SESSION['role_id'])  ? $_SESSION['role_id'] : 0;
$role_id = (int)$role_id;
// echo $REQUEST['cid']; die();

\Stripe\Stripe::setApiKey(STRIPE_API_KEY); //Initialize Stripe Gateway
\Stripe\Stripe::setApiVersion(STRIPE_API_VERSION);//'2017-06-05');

$errortype = '';
if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else { 
	if ($_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5) {
		$oregister->redirect('manage_campaign_participant.php');
	}
}

$canPrintExceptions = false;//when you want to test exception change it to true
$exceptionE = null; // it will hold last exception 

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
$fld_nonprofit = 0;
$fld_show_participant_goal = 1;
$fld_text_messaging = 0;
$fld_ab1575_pupil_fee = 0;
$fld_rewards = 0;
$sStartCampMenu = 'active';
$_SESSION['campid'] = $REQUEST['cid'];
$start_campaign = 'start_campaign.php?m=e&cid='.$REQUEST['cid'].'';
$sPageName = '<li><a href="start_campaign.php?m=e&cid='.$REQUEST['cid'].'">Start New Campaign</a></li> <li>Basic Information</li>';
$sSettingsData = $oregister->getsettingsdetail(1);
// echo "39--Request<pre>"; var_dump($REQUEST); echo "</pre>";
//if($REQUEST['fld_campaign_id'] > 0)
if (array_key_exists('savecontinue', $REQUEST))
{
	//include_once ('../lib/init.php');
	$iCid = issetCheck($REQUEST, 'fld_campaign_id', 0);
	$sTitle = issetCheck($REQUEST, 'fld_campaign_title', '');
	$sOrgName = issetCheck($REQUEST, 'fld_organization_name', '');
	$sTeamName = issetCheck($REQUEST, 'fld_team_name', '');
	$sTeamSize = issetCheck($REQUEST, 'fld_team_size', 0);
	$sDonorSize = issetCheck($REQUEST, 'fld_donor_size', 0);
	$pin = issetCheck($REQUEST, 'pin', '');
	$chash = issetCheck($REQUEST, 'chash', 0);
	$sStartDate = date('Y-m-d',strtotime(issetCheck($REQUEST, 'fld_campaign_sdate', '')));
	$sEndDate = date('Y-m-d',strtotime(issetCheck($REQUEST, 'fld_campaign_edate', '')));
	$sCGoal = str_replace(',', '', issetCheck($REQUEST, 'fld_campaign_goal', 0));
	$sPGoal = str_replace(',', '', issetCheck($REQUEST, 'fld_participant_goal', 0));	
	$ab1575pupilfee = issetCheck($REQUEST, 'fld_ab1575_pupil_fee', 0);
	$showparticipantgoal = issetCheck($REQUEST, 'fld_show_participant_goal', 0);
	$textmessaging = issetCheck($REQUEST, 'fld_text_messaging', '');
	$nonprofit = issetCheck($REQUEST, 'fld_nonprofit', '');	
	$nonprofit = issetCheck($REQUEST, 'fld_nonprofit', '');	
	$nonprofit_number = issetCheck($REQUEST, 'fld_nonprofit_number', 0);	
	foreach ($nonprofit as $key => $nonprofit1) {
		$checked = $nonprofit1;
	}
	if ($checked == 1) {
		$nonprofit = '1';
	} else {
		$nonprofit = '0';
	}
	
	$rewards = issetCheck($REQUEST, 'fld_rewards', '');	
	foreach ($rewards as $key => $rewards1) {
		$checked = $rewards1;
	}
	if ($checked == 1) {
		$rewards = '1';
	} else {
		$rewards = '0';
	}
	
	if ($_SESSION['role_id'] != 1) {
		if ($fld_ab1575_pupil_fee == 1) {
			$checked = 1;
		} else {
			if( isset($ab1575pupilfee) && is_array($ab1575pupilfee) ){
				foreach ($ab1575pupilfee as $key => $ab1575pupilfee1) {
					$checked = $ab1575pupilfee1;
				}
			}
		}
	} else {
		foreach ($ab1575pupilfee as $key => $ab1575pupilfee1) {
			$checked = $ab1575pupilfee1;
		}
	}
	if ($checked == 1) {
		$ab1575pupilfee = '1';
	} else {
		$ab1575pupilfee = '0';
	}
	
	foreach ($showparticipantgoal as $key => $showparticipantgoal1) {
		$checked = $showparticipantgoal1;
	}
	if ($checked == 1) {
		$showparticipantgoal = '1';
	} else {
		$showparticipantgoal = '0';
	}
	foreach ($textmessaging as $key => $textmessaging1) {
		$checked = $textmessaging1;
	}
	if ($checked == 1) {
		$textmessaging = '1';
	} else {
		$textmessaging = '0';
	}
	$sDesc1 = issetCheck($REQUEST, 'fld_desc1', '');	
	$sDesc2 = issetCheck($REQUEST, 'fld_desc2', '');	
	$sDesc3 = issetCheck($REQUEST, 'fld_desc3', '');
	//$sDonationLevel1 = str_replace(',', '', issetCheck($REQUEST, 'fld_donation_level1', ''));
	//$sDonationLevel2 = str_replace(',', '', issetCheck($REQUEST, 'fld_donation_level2', ''));
	//$sDonationLevel3 = str_replace(',', '', issetCheck($REQUEST, 'fld_donation_level3', ''));
	$sDonationLevel1 = $sSettingsData['fld_donation_level1_amt'];
	$sDonationLevel2 = $sSettingsData['fld_donation_level2_amt'];
	$sDonationLevel3 = $sSettingsData['fld_donation_level3_amt'];
	if ($pin != '') {
		$generatepin = $pin;
	} else {
		$generatepin = $oregister->generatepin(4);
	}
	if ($chash != '') {
		$generatechash = $chash;
	} else {
		$generatechash = $oregister->generatepasshash(6);
	}
	$videofiles = issetCheck($REQUEST, 'videofiles', '');
	/* New Fields */
	$accid = issetCheck($REQUEST, 'accid', '');
	//$fld_organization_type = issetCheck($REQUEST, 'fld_organization_type', '');
	$fld_organization_type = 'company';
	$fld_organ_other = issetCheck($REQUEST, 'fld_organ_other', '');
	$fld_taxid_number = issetCheck($REQUEST, 'fld_taxid_number', '');
	
	$fld_payment_method = issetCheck($REQUEST, 'fld_payment_method', '');	
	foreach ($fld_payment_method as $key => $fld_payment_method1) {
		$checked1 = $fld_payment_method1;
	}
	$fld_account_name = "";
	if ($checked1 == 1) {
		$payment_method = '1';
		$fld_routing_number = ROUTING_NUMBER;
		$fld_account_number = ACCOUNT_NUMBER;
		$fld_account_name = sWEBSITENAME;
		$fld_bank_name = 'First Bank';
		$fld_payable_to = issetCheck($REQUEST, 'fld_payable_to', '');
	} else {
		$payment_method = '0';
		$fld_routing_number = issetCheck($REQUEST, 'fld_routing_number', '');
		$fld_account_number = issetCheck($REQUEST, 'fld_account_number', '');
		$fld_account_name = issetCheck($REQUEST, 'fld_account_name', '');
		$fld_bank_name = issetCheck($REQUEST, 'fld_bank_name', '');
		$fld_payable_to = '';
	}
	//$fld_account_type = issetCheck($REQUEST, 'fld_account_type', '');
	/* New Fields */
	if ($accid != '') {
		$account = \Stripe\Account::retrieve("$accid");
		$accounts = $account->__toArray(true);
		$account_counter = count($accounts['external_accounts']['data']);
	}
	$oCampaign->insert_update_video($iCid,$videofiles,1);
	if ($oCampaign->update_campaign($iCid,xss_clean($sTitle),xss_clean($sOrgName),xss_clean($sTeamName),$sTeamSize,$sDonorSize,$sStartDate,$sEndDate,$sCGoal,$sPGoal,$ab1575pupilfee,$showparticipantgoal,$textmessaging,$rewards,$nonprofit,$nonprofit_number,xss_clean($sDesc1),xss_clean($sDesc2),xss_clean($sDesc3),$sDonationLevel1,$sDonationLevel2,$sDonationLevel3,$generatepin,$fld_organization_type,$fld_organ_other,$fld_taxid_number,$fld_account_number,$payment_method,$fld_payable_to,$generatechash)) {
		if ($rewards == 1) {
			$reward_ids = $REQUEST['rewards']['reward_ids'];
			$reward_amt = $REQUEST['rewards']['reward_amt'];
			$reward_desc = $REQUEST['rewards']['rewards_desc'];
			$reward_desc_details = $REQUEST['rewards']['rewards_desc_details'];
			$fullname = $_SESSION['uname']." ".$_SESSION['ulname'];
			$uid = $_SESSION['uid'];
			for ($sn=0; $sn < 4; $sn++) {
				$array['reward_ids'] = $reward_ids[$sn];
				$array['reward_amt'] = $reward_amt[$sn];
				$array['reward_desc'] = $reward_desc[$sn];
				$array['reward_desc_details'] = $reward_desc_details[$sn];
				$rewards2[] = $array;
			}
			//print_r($rewards2);
			foreach ($rewards2 as $rewards_data) {
				$reward_ids = $rewards_data['reward_ids'];
				$reward_amt = str_replace(",", "", $rewards_data['reward_amt']);
				$reward_desc = $rewards_data['reward_desc'];
				$reward_desc_details = $rewards_data['reward_desc_details'];
				if ($reward_amt != '' && $reward_desc != '') {
					if ($reward_ids != '') {
						$oCampaign->update_rewards($reward_ids,$iCid,$uid,xss_clean($reward_amt),xss_clean($reward_desc),xss_clean($reward_desc_details),xss_clean($fullname));
					} else {
						$oCampaign->insert_rewards($reward_ids,$iCid,$uid,xss_clean($reward_amt),xss_clean($reward_desc),xss_clean($reward_desc_details),xss_clean($fullname));
					}
				}
			}
		
		}
		
		$aCampaignDetail = $oCampaign->getcampaigndetail($iCid);
		// die("214--".var_dump($iCid));
		$firstname = $aCampaignDetail['fld_cname'];
		$lastname = $aCampaignDetail['fld_clname'];
		$sEmail = $aCampaignDetail['fld_cemail'];
		$iCityId = $aCampaignDetail['fld_ccity'];
		$sAddress = $aCampaignDetail['fld_caddress'];
		$sZipcode = $aCampaignDetail['fld_czipcode'];
			
		$sSsn1 = $aCampaignDetail['fld_ssn'];
		$sSsn = $oregister->decrypt($sSsn1,sENC_KEY);
		$sDob1 = $aCampaignDetail['fld_dob'];
		$sDob = $oregister->decrypt($sDob1,sENC_KEY);
		$DOB = explode("-",$sDob);
		$Date = $DOB[2];
		$Month = $DOB[1];
		$Year = $DOB[0];
		
		//Update old account case
		if ($accid != '') { //Stripe
			if ($account_counter == 0) {
				try {
					// $account->business_name = $sOrgName;
					//$account->legal_entity->business_name = $sOrgName;
					/*if ($nonprofit == 1) {
						$account->legal_entity->business_tax_id = $nonprofit_number;
					} else {
						$account->legal_entity->business_tax_id = $fld_taxid_number;
					}*/
					/*if ($nonprofit == 1) {
						$account->legal_entity->type = $fld_organ_other;
					} else {*/
						//$account->legal_entity->type = 'company';
					//}
					$account->settings->payouts->statement_descriptor = SITE_DOMAIN_CAP;
					$account->business_profile->support_phone = SUPPORT_PHONE;
					$account->tos_acceptance->date = time();
					$account->tos_acceptance->ip = $_SERVER['REMOTE_ADDR'];
					$account->save();
				} catch (Stripe_InvalidRequestError $e) {
					$exceptionE = $e;
					// Invalid parameters were supplied to Stripe's API
					$errortype = 2;
					$msged = '<b>Error: Invalid request submitted.</b>';
				} catch (Stripe_AuthenticationError $e) {
					$exceptionE = $e;
					// Authentication with Stripe's API failed
					$errortype = 3;
					$msged = '<b>Error: Connection failed.</b>';
				} catch (Stripe_ApiConnectionError $e) {
					$exceptionE = $e;
					// Network communication with Stripe failed
					$errortype = 4;
					$msged = '<b>Error: Network communication error.</b>';
				} catch (Stripe_Error $e) {
					$exceptionE = $e;
					// Display a very generic error to the user, and maybe send
					// yourself an email
					$errortype = 5;
					$msged = '<b>Error: Connection not established</b>';
				} catch (Exception $e) {
					$exceptionE = $e;
					// Something else happened, completely unrelated to Stripe
					$errortype = 6;
					$msged = '<b>Error: Updation error. Please enter a valid information.</b>';
				}
				
				try {
					//https://stripe.com/docs/api/external_account_bank_accounts/create
					$account->external_accounts->create(array('external_account' => array(
						'object' => 'bank_account',
						'country' => 'US',
						'currency' => 'usd',
						'account_holder_name' => $fld_account_name,
						'account_holder_type' => 'company',
						'routing_number' => $fld_routing_number,
						'account_number' => $fld_account_number
					)));
				} catch (Stripe_InvalidRequestError $e) {
					$exceptionE = $e;
					// Invalid parameters were supplied to Stripe's API
					$errortype = 2;
					$msged = '<b>Error: Invalid request submitted.</b>';
				} catch (Stripe_AuthenticationError $e) {
					$exceptionE = $e;
					// Authentication with Stripe's API failed
					$errortype = 3;
					$msged = '<b>Error: Connection failed.</b>';
				} catch (Stripe_ApiConnectionError $e) {
					$exceptionE = $e;
					// Network communication with Stripe failed
					$errortype = 4;
					$msged = '<b>Error: Network communication error.</b>';
				} catch (Stripe_Error $e) {
					$exceptionE = $e;
					// Display a very generic error to the user, and maybe send
					// yourself an email
					$errortype = 5;
					$msged = '<b>Error: Connection not established</b>';
				} catch (Exception $e) {
					$exceptionE = $e;
					// Something else happened, completely unrelated to Stripe
					$errortype = 6;
					$msged = '<b>Error: Bank creating, Please enter a valid information.</b>';
				}

				if($exceptionE != null && $canPrintExceptions) { echo __line__.'<pre>'; var_dump($exceptionE->getJsonBody()); die(); }
			}//when stripe id, did fetched any account, if end
			else {
				try {
					// $account->business_name = $sOrgName;
					//$account->legal_entity->business_name = $sOrgName;
					/*if ($nonprofit == 1) {
						$account->legal_entity->business_tax_id = $nonprofit_number;
					} else {
						$account->legal_entity->business_tax_id = $fld_taxid_number;
					}*/
					/*if ($fld_organization_type == 'Other') {
						$account->legal_entity->type = $fld_organ_other;
					} else {*/
						//$account->legal_entity->type = 'company';
					//}
					$account->settings->payouts->statement_descriptor = SITE_DOMAIN_CAP;
					$account->business_profile->support_phone = SUPPORT_PHONE;
					$account->tos_acceptance->date = time();
					$account->tos_acceptance->ip = $_SERVER['REMOTE_ADDR'];
					$account->save();

					$bank_acno = $account['external_accounts']['data'][0]['id'];
				} catch (Stripe_InvalidRequestError $e) {
					$exceptionE = $e;
					// Invalid parameters were supplied to Stripe's API
					$errortype = 2;
					$msged = '<b>Error: Invalid request submitted.</b>';
				} catch (Stripe_AuthenticationError $e) {
					$exceptionE = $e;
					// Authentication with Stripe's API failed
					$errortype = 3;
					$msged = '<b>Error: Connection failed.</b>';
				} catch (Stripe_ApiConnectionError $e) {
					$exceptionE = $e;
					// Network communication with Stripe failed
					$errortype = 4;
					$msged = '<b>Error: Network communication error.</b>';
				} catch (Stripe_Error $e) {
					$exceptionE = $e;
					// Display a very generic error to the user, and maybe send
					// yourself an email
					$errortype = 5;
					$msged = '<b>Error: Connection not established</b>';
				} catch (Exception $e) {
					$exceptionE = $e;
					// Something else happened, completely unrelated to Stripe
					$errortype = 6;
					$msged = '<b>Error: Updation error. Please enter a valid information.</b>';
				} 
				
				$new_external_account = null;
				try {
					//https://stripe.com/docs/api/external_account_bank_accounts/create
					$new_external_account = array('external_account' => 
							array(
								'object' => 'bank_account',
								'country' => 'US',
								'currency' => 'usd',
								'default_for_currency' => true,
								'account_holder_name' => $fld_account_name,
								'account_holder_type' => 'company',
								'routing_number' => $fld_routing_number,
								'account_number' => $fld_account_number
							)
					);
					$account->external_accounts->create($new_external_account);
					$account->external_accounts->retrieve($bank_acno)->delete();
				} catch (Stripe_InvalidRequestError $e) {
					$exceptionE = $e;
					// Invalid parameters were supplied to Stripe's API
					$errortype = 2;
					$msged = '<b>Error: Invalid request submitted.</b>';
				} catch (Stripe_AuthenticationError $e) {
					$exceptionE = $e;
					// Authentication with Stripe's API failed
					$errortype = 3;
					$msged = '<b>Error: Connection failed.</b>';
				} catch (Stripe_ApiConnectionError $e) {
					$exceptionE = $e;
					// Network communication with Stripe failed
					$errortype = 4;
					$msged = '<b>Error: Network communication error.</b>';
				} catch (Stripe_Error $e) {
					$exceptionE = $e;
					// Display a very generic error to the user, and maybe send
					// yourself an email
					$errortype = 5;
					$msged = '<b>Error: Connection not established</b>';
				} catch (Exception $e) {
					$exceptionE = $e;
					// Something else happened, completely unrelated to Stripe
					$errortype = 6;
					$msged = '<b>Error: Bank creating, Please enter a valid information.</b>';
				}
				if($exceptionE != null && $canPrintExceptions) { echo __line__.'<pre>'; var_dump(["exceptionE"=>$exceptionE->getJsonBody(), "new_external_account"=>$new_external_account, "account"=>$account]); die(); }
			}
		} 
		//create new stripe account case
		else { //Stripe
			//Create Stripe Account
			if ($nonprofit == 1) {
				$tax_id = $nonprofit_number;
			} else {
				$tax_id = $fld_taxid_number;
			}
			try {
				//https://stripe.com/docs/api/accounts/create
				$stripe_ac_create_ary = array(
						"type" => "custom",
						"country" => "US",
						'email' => $sEmail, //Email Address
						'requested_capabilities' => array('card_payments','transfers'),

						'business_profile' => array(
												'mcc' => "8398", //Charitable and Social Service Organizations - Fundraising //https://stripe.com/docs/connect/setting-mcc
												'name'=>$sOrgName, //Business Name
												'product_description' => "Software, SaaS",
												'support_email' => $sEmail,
												'support_phone' => SUPPORT_PHONE,
												'support_url' => SITE_FULL_URL,
												'url'=>SITE_FULL_URL_FOR_STRIPE_BUSINESS_PROFILE
											),
						'business_type' => "individual",
						'individual' => array(
										'address' => array(
											'city' => $iCityId, //Representative City
											'country' => 'US', //Representative Country
											"line1" => $sAddress, //Representative Address Line1
											"postal_code" => $sZipcode, //Representative Postal Code
											"state" => 'CA' //Representative State
										),
										'dob' => array(
												'day' => $Date, //Representative Date of Birth (Day)
												'month' => $Month, //Representative Date of Birth (Month)
												'year' => $Year //Representative Date of Birth (Year)
											),
										'email' => $sEmail, //Email Address
										'phone' => SUPPORT_PHONE, 
										'first_name' => $firstname, //Representative First Name
										//'id_number' => '000000000', //The government-issued ID number of the individual
										'last_name' => $lastname, //Representative Last Name
										'ssn_last_4' => $sSsn //Representative SSN #
										
										//These things are the part of type company
										// 'name' => $sOrgName, //Business Name
										// 'tax_id' => $tax_id, //Business Tax #
									),
						
						'external_account' => array(
							'object' => 'bank_account',
							'country' => 'US',
							'currency' => 'usd',
							'account_holder_name' => $fld_account_name,
							'account_holder_type' => 'company',
							'routing_number' => $fld_routing_number,
							'account_number' => $fld_account_number
						),
						'metadata' => array(
							'Campaign_No' => $iCid, //Campaign #
							'Campaign_Name' => $sTitle //Campaign Name
						),
						'settings' => array( 
										'payouts' => array(
													'statement_descriptor' => SITE_DOMAIN_CAP,
													'schedule' => array(
																	'interval' => 'manual'
																) 
												 )
										 ),
						'tos_acceptance' => array(
							'date' => time(), //Creation Date from Representative Acceptance
							'ip' => $_SERVER['REMOTE_ADDR'] //Representative IP
						)
					);
				// echo '499---1feb2020--1614<pre>'; print_r($stripe_ac_create_ary); die();
				$account = \Stripe\Account::create($stripe_ac_create_ary);
				//Create Stripe Account
				$account1 = $account->__toArray(true);
				$accid = $account1['id'];
				$oCampaign->update_acc_campaign($iCid, $accid);
			} catch (Stripe_InvalidRequestError $e) {
				$exceptionE = $e;
				// Invalid parameters were supplied to Stripe's API
				$errortype = 2;
				$msged = '<b>Error: Invalid request submitted.</b>';
			} catch (Stripe_AuthenticationError $e) {
				$exceptionE = $e;
				// Authentication with Stripe's API failed
				$errortype = 3;
				$msged = '<b>Error: Connection failed.</b>';
			} catch (Stripe_ApiConnectionError $e) {
				$exceptionE = $e;
				// Network communication with Stripe failed
				$errortype = 4;
				$msged = '<b>Error: Network communication error.</b>';
			} catch (Stripe_Error $e) {
				$exceptionE = $e;
				// Display a very generic error to the user, and maybe send
				// yourself an email
				$errortype = 5;
				$msged = '<b>Error: Connection not established</b>';
			} catch (Exception $e) {
				$exceptionE = $e;
				// Something else happened, completely unrelated to Stripe
				$errortype = 6;
				$body = $e->getJsonBody();
				$err  = $body['error'];
				$msged = '<b>Error: '.$err['message'].'</b>';
				// echo '<pre>'; var_dump($body); die();

			}


			if($exceptionE != null && $canPrintExceptions) { echo __line__.'<pre>'; var_dump(["exceptionE"=>$exceptionE->getJsonBody(), "new_external_account"=>$new_external_account, "account"=>$account]); die(); }
		} //Stripe
	}
	if($iCid > 0 && $errortype == '')
	{
		$oregister->redirect('build_team.php?cid='.$iCid);
	}
}
if (array_key_exists('save', $REQUEST)) {
	//include_once ('../lib/init.php');
	$iCid = $REQUEST['fld_campaign_id'];
	$sTitle = $REQUEST['fld_campaign_title'];
	$sOrgName = $REQUEST['fld_organization_name'];
	$sTeamName = issetCheck($REQUEST, 'fld_team_name', '');
	$sTeamSize = issetCheck($REQUEST, 'fld_team_size', 0);
	$sDonorSize = issetCheck($REQUEST, 'fld_donor_size', 0);
	$pin = issetCheck($REQUEST, 'pin', '');
	$chash = issetCheck($REQUEST, 'chash', '');
	$sStartDate = date('Y-m-d',strtotime($REQUEST['fld_campaign_sdate']));
	$sEndDate = date('Y-m-d',strtotime($REQUEST['fld_campaign_edate']));
	$sCGoal = str_replace(',', '', $REQUEST['fld_campaign_goal']);
	$sPGoal = str_replace(',', '', $REQUEST['fld_participant_goal']);	
	$ab1575pupilfee = $REQUEST['fld_ab1575_pupil_fee'];
	$showparticipantgoal = $REQUEST['fld_show_participant_goal'];
	$textmessaging = $REQUEST['fld_text_messaging'];
	$nonprofit = $REQUEST['fld_nonprofit'];	
	$nonprofit_number = $REQUEST['fld_nonprofit_number'];	
	foreach ($nonprofit as $key => $nonprofit1) {
		$checked = $nonprofit1;
	}
	if ($checked == 1) {
		$nonprofit = '1';
	} else {
		$nonprofit = '0';
	}
	
	$rewards = $REQUEST['fld_rewards'];	
	foreach ($rewards as $key => $rewards1) {
		$checked = $rewards1;
	}
	if ($checked == 1) {
		$rewards = '1';
	} else {
		$rewards = '0';
	}
	
	if ($_SESSION['role_id'] != 1) {
		if ($fld_ab1575_pupil_fee == 1) {
			$checked = 1;
		} else {
			foreach ($ab1575pupilfee as $key => $ab1575pupilfee1) {
				$checked = $ab1575pupilfee1;
			}
		}
	} else {
		foreach ($ab1575pupilfee as $key => $ab1575pupilfee1) {
			$checked = $ab1575pupilfee1;
		}
	}
	
	if ($checked == 1) {
		$ab1575pupilfee = '1';
	} else {
		$ab1575pupilfee = '0';
	}
	
	foreach ($showparticipantgoal as $key => $showparticipantgoal1) {
		$checked = $showparticipantgoal1;
	}
	if ($checked == 1) {
		$showparticipantgoal = '1';
	} else {
		$showparticipantgoal = '0';
	}
	
	foreach ($textmessaging as $key => $textmessaging1) {
		$checked = $textmessaging1;
	}
	if ($checked == 1) {
		$textmessaging = '1';
	} else {
		$textmessaging = '0';
	}
	
	$sDesc1 = $REQUEST['fld_desc1'];	
	$sDesc2 = $REQUEST['fld_desc2'];	
	$sDesc3 = $REQUEST['fld_desc3'];
	$sDonationLevel1 = $sSettingsData['fld_donation_level1_amt'];
	$sDonationLevel2 = $sSettingsData['fld_donation_level2_amt'];
	$sDonationLevel3 = $sSettingsData['fld_donation_level3_amt'];
	if ($pin != '') {
		$generatepin = $pin;
	} else {
		$generatepin = $oregister->generatepin(4);
	}
	if ($chash != '') {
		$generatechash = $chash;
	} else {
		$generatechash = $oregister->generatepasshash(6);
	}
	$videofiles = $REQUEST['videofiles'];
	$accid = $REQUEST['accid'];
	/* New Fields */
	$fld_accid = $REQUEST['accid'];
	//$fld_organization_type = $REQUEST['fld_organization_type'];
	$fld_organization_type = 'company';
	$fld_organ_other = issetCheck($REQUEST, 'fld_organ_other', '');
	$fld_taxid_number = $REQUEST['fld_taxid_number'];
	
	$fld_payment_method = $REQUEST['fld_payment_method'];	
	foreach ($fld_payment_method as $key => $fld_payment_method1) {
		$checked1 = $fld_payment_method1;
	}
	if ($checked1 == 1) {
		$payment_method = '1';
		$fld_routing_number = ROUTING_NUMBER2;
		$fld_account_number = ACCOUNT_NUMBER2;
		$fld_account_name = ACCOUNT_NAME2;
		$fld_bank_name = BANK_NAME2;
		$fld_payable_to = $REQUEST['fld_payable_to'];
	} else {
		$payment_method = '0';
		$fld_routing_number = $REQUEST['fld_routing_number'];
		$fld_account_number = $REQUEST['fld_account_number'];
		$fld_account_name = $REQUEST['fld_account_name'];
		$fld_bank_name = $REQUEST['fld_bank_name'];
		$fld_payable_to = '';
	}
	//$fld_account_type = $REQUEST['fld_account_type'];
	/* New Fields */
	/*if ($fld_accid != '') {
		$account = \Stripe\Account::retrieve($fld_accid);
		//$account = \Stripe\Account::retrieve('acct_191HpGKtK6loXZJ8');
		$accounts = $account->__toArray(true);
		$account_counter = count($accounts[external_accounts][data]);
	}*/
	
	$oCampaign->insert_update_video($iCid,$videofiles,1);
	if ($oCampaign->update_campaign($iCid,xss_clean($sTitle),xss_clean($sOrgName),xss_clean($sTeamName),$sTeamSize,$sDonorSize,$sStartDate,$sEndDate,$sCGoal,$sPGoal,$ab1575pupilfee,$showparticipantgoal,$textmessaging,$rewards,$nonprofit,$nonprofit_number,xss_clean($sDesc1),xss_clean($sDesc2),xss_clean($sDesc3),$sDonationLevel1,$sDonationLevel2,$sDonationLevel3,$generatepin,$fld_organization_type,$fld_organ_other,$fld_taxid_number,$fld_account_number,$payment_method,$fld_payable_to,$generatechash))
	{
		if ($rewards == 1) {
			$reward_ids = $REQUEST['rewards']['reward_ids'];
			$reward_amt = $REQUEST['rewards']['reward_amt'];
			$reward_desc = $REQUEST['rewards']['rewards_desc'];
			$reward_desc_details = $REQUEST['rewards']['rewards_desc_details'];
			$fullname = $_SESSION['uname']." ".$_SESSION['ulname'];
			$uid = $_SESSION['uid'];
			for ($sn=0; $sn < 4; $sn++) {
				$array['reward_ids'] = $reward_ids[$sn];
				$array['reward_amt'] = $reward_amt[$sn];
				$array['reward_desc'] = $reward_desc[$sn];
				$array['reward_desc_details'] = $reward_desc_details[$sn];
				$rewards2[] = $array;
			}
			//print_r($rewards2);
			foreach ($rewards2 as $rewards_data) {
				$reward_ids = $rewards_data['reward_ids'];
				$reward_amt = str_replace(",", "", $rewards_data['reward_amt']);
				$reward_desc = $rewards_data['reward_desc'];
				$reward_desc_details = $rewards_data['reward_desc_details'];
				if ($reward_amt != '' && $reward_desc != '') {
					if ($reward_ids != '') {
						$oCampaign->update_rewards($reward_ids,$iCid,$uid,xss_clean($reward_amt),xss_clean($reward_desc),xss_clean($reward_desc_details),xss_clean($fullname));
					} else {
						$oCampaign->insert_rewards($reward_ids,$iCid,$uid,xss_clean($reward_amt),xss_clean($reward_desc),xss_clean($reward_desc_details),xss_clean($fullname));
					}
				} else {
					if ($reward_ids != '') {
						$oCampaign->delete_rewards($reward_ids,$iCid,$uid,xss_clean($reward_amt),xss_clean($reward_desc),xss_clean($reward_desc_details),xss_clean($fullname));
					}
				}
			}
		
		}	
		
		/*$aCampaignDetail = $oCampaign->getcampaigndetail($iCid);
		$firstname = $aCampaignDetail['fld_cname'];
		$lastname = $aCampaignDetail['fld_clname'];
		$sEmail = $aCampaignDetail['fld_cemail'];
		$iCityId = $aCampaignDetail['fld_ccity'];
		$sAddress = $aCampaignDetail['fld_caddress'];
		$sZipcode = $aCampaignDetail['fld_czipcode'];
			
		$sSsn1 = $aCampaignDetail['fld_ssn'];
		$sSsn = $oregister->decrypt($sSsn1,sENC_KEY);
		$sDob1 = $aCampaignDetail['fld_dob'];
		$sDob = $oregister->decrypt($sDob1,sENC_KEY);
		$DOB = explode("-",$sDob);
		$Date = $DOB[2];
		$Month = $DOB[1];
		$Year = $DOB[0];
		
		if ($fld_accid != '') {
			if ($account_counter == 0) {
				try {
					$account->business_name = $sOrgName;
					$account->legal_entity->business_name = $sOrgName;
					if ($nonprofit == 1) {
						$account->legal_entity->business_tax_id = $nonprofit_number;
					} else {
						$account->legal_entity->business_tax_id = $fld_taxid_number;
					}
					//if ($fld_organization_type == 'Other') {
					//	$account->legal_entity->type = $fld_organ_other;
					//} else {
						$account->legal_entity->type = 'company';
					//}
					$account->statement_descriptor = SITE_DOMAIN_CAP;
					$account->support_phone = SUPPORT_PHONE;
					$account->tos_acceptance->date = time();
					$account->tos_acceptance->ip = $_SERVER['REMOTE_ADDR'];
					$account->save();
				} catch (Stripe_InvalidRequestError $e) {
					// Invalid parameters were supplied to Stripe's API
					$errortype = 2;
					$msged = '<b>Error: Invalid request submitted.</b>';
				} catch (Stripe_AuthenticationError $e) {
					// Authentication with Stripe's API failed
					$errortype = 3;
					$msged = '<b>Error: Connection failed.</b>';
				} catch (Stripe_ApiConnectionError $e) {
					// Network communication with Stripe failed
					$errortype = 4;
					$msged = '<b>Error: Network communication error.</b>';
				} catch (Stripe_Error $e) {
					// Display a very generic error to the user, and maybe send
					// yourself an email
					$errortype = 5;
					$msged = '<b>Error: Connection not established</b>';
				} catch (Exception $e) {
					// Something else happened, completely unrelated to Stripe
					$errortype = 6;
					$msged = '<b>Error: Updation error. Please enter a valid information.</b>';
				}
				
				try {
					$account->external_accounts->create(array('external_account' => array(
						'object' => 'bank_account',
						'country' => 'US',
						'currency' => 'usd',
						'account_holder_name' => $fld_account_name,
						'account_holder_type' => 'company',
						'routing_number' => $fld_routing_number,
						'account_number' => $fld_account_number
					)));
				} catch (Stripe_InvalidRequestError $e) {
					// Invalid parameters were supplied to Stripe's API
					$errortype = 2;
					$msged = '<b>Error: Invalid request submitted.</b>';
				} catch (Stripe_AuthenticationError $e) {
					// Authentication with Stripe's API failed
					$errortype = 3;
					$msged = '<b>Error: Connection failed.</b>';
				} catch (Stripe_ApiConnectionError $e) {
					// Network communication with Stripe failed
					$errortype = 4;
					$msged = '<b>Error: Network communication error.</b>';
				} catch (Stripe_Error $e) {
					// Display a very generic error to the user, and maybe send
					// yourself an email
					$errortype = 5;
					$msged = '<b>Error: Connection not established</b>';
				} catch (Exception $e) {
					// Something else happened, completely unrelated to Stripe
					$errortype = 6;
					$msged = '<b>Error: Bank creating, Please enter a valid information.</b>';
				}
				
			} else {
				
				try {
					$account->business_name = $sOrgName;
					$account->legal_entity->business_name = $sOrgName;
					if ($nonprofit == 1) {
						$account->legal_entity->business_tax_id = $nonprofit_number;
					} else {
						$account->legal_entity->business_tax_id = $fld_taxid_number;
					}
					//if ($fld_organization_type == 'Other') {
					//	$account->legal_entity->type = $fld_organ_other;
					//} else {
						$account->legal_entity->type = 'company';
					//}
					$account->statement_descriptor = SITE_DOMAIN_CAP;
					$account->support_phone = SUPPORT_PHONE;
					$account->tos_acceptance->date = time();
					$account->tos_acceptance->ip = $_SERVER['REMOTE_ADDR'];
					$account->save();
					$bank_acno = $account['external_accounts']['data'][0]['id'];
				} catch (Stripe_InvalidRequestError $e) {
					// Invalid parameters were supplied to Stripe's API
					$errortype = 2;
					$msged = '<b>Error: Invalid request submitted.</b>';
				} catch (Stripe_AuthenticationError $e) {
					// Authentication with Stripe's API failed
					$errortype = 3;
					$msged = '<b>Error: Connection failed.</b>';
				} catch (Stripe_ApiConnectionError $e) {
					// Network communication with Stripe failed
					$errortype = 4;
					$msged = '<b>Error: Network communication error.</b>';
				} catch (Stripe_Error $e) {
					// Display a very generic error to the user, and maybe send
					// yourself an email
					$errortype = 5;
					$msged = '<b>Error: Connection not established</b>';
				} catch (Exception $e) {
					// Something else happened, completely unrelated to Stripe
					$errortype = 6;
					$msged = '<b>Error: Updation error. Please enter a valid information.</b>';
				}
				
				try {
					$account->external_accounts->create(array('external_account' => array(
					'object' => 'bank_account',
					'country' => 'US',
					'currency' => 'usd',
					'default_for_currency' => true,
					'account_holder_name' => $fld_account_name,
					'account_holder_type' => 'company',
					'routing_number' => $fld_routing_number,
					'account_number' => $fld_account_number
					)));
					$account->external_accounts->retrieve($bank_acno)->delete();
				} catch (Stripe_InvalidRequestError $e) {
					// Invalid parameters were supplied to Stripe's API
					$errortype = 2;
					$msged = '<b>Error: Invalid request submitted.</b>';
				} catch (Stripe_AuthenticationError $e) {
					// Authentication with Stripe's API failed
					$errortype = 3;
					$msged = '<b>Error: Connection failed.</b>';
				} catch (Stripe_ApiConnectionError $e) {
					// Network communication with Stripe failed
					$errortype = 4;
					$msged = '<b>Error: Network communication error.</b>';
				} catch (Stripe_Error $e) {
					// Display a very generic error to the user, and maybe send
					// yourself an email
					$errortype = 5;
					$msged = '<b>Error: Connection not established</b>';
				} catch (Exception $e) {
					// Something else happened, completely unrelated to Stripe
					$errortype = 6;
					$msged = '<b>Error: Bank creating, Please enter a valid information.</b>';
				}
			}
		} else { //Stripe
			//Create Stripe Account
			if ($nonprofit == 1) {
				$tax_id = $nonprofit_number;
			} else {
				$tax_id = $fld_taxid_number;
			}
			try {
				$account = \Stripe\Account::create(
					array(
						"country" => "US",
						"managed" => true,
						'email' => $sEmail, //Email Address
						'business_name' => $sOrgName, //Business Name
						'business_url' => SITE_FULL_URL, //Business URL
						'legal_entity' => array(
							'address' => array(
								'city' => $iCityId, //Representative City
								'country' => 'US', //Representative Country
								"line1" => $sAddress, //Representative Address Line1
								"postal_code" => $sZipcode, //Representative Postal Code
								"state" => 'CA' //Representative State
							),
							'business_name' => $sOrgName, //Business Name
							'business_tax_id' => $tax_id, //Business Tax #
							'dob' => array(
								'day' => $Date, //Representative Date of Birth (Day)
								'month' => $Month, //Representative Date of Birth (Month)
								'year' => $Year //Representative Date of Birth (Year)
							),
							'first_name' => $firstname, //Representative First Name
							'last_name' => $lastname, //Representative Last Name
							//'personal_id_number' => '000000000', //Representative Personal ID #
							'ssn_last_4' => $sSsn, //Representative SSN #
							'type' => 'company' //Business Type
						),
						'statement_descriptor' => SITE_DOMAIN_CAP,
						'support_phone' => SUPPORT_PHONE,
						'tos_acceptance' => array(
							'date' => time(), //Creation Date from Representative Acceptance
							'ip' => $_SERVER['REMOTE_ADDR'] //Representative IP
						),
						'external_account' => array(
							'object' => 'bank_account',
							'country' => 'US',
							'currency' => 'usd',
							'default_for_currency' => true,
							'account_holder_name' => $fld_account_name,
							'account_holder_type' => 'company',
							'routing_number' => $fld_routing_number,
							'account_number' => $fld_account_number
						),
						'transfer_schedule' => array(
							'interval' => 'manual'
						)
					)
				);
				//Create Stripe Account
				$account1 = $account->__toArray(true);
				$accid = $account1['id'];
				$oCampaign->update_acc_campaign($iCid, $accid);
			} catch (Stripe_InvalidRequestError $e) {
				// Invalid parameters were supplied to Stripe's API
				$errortype = 2;
				$msged = '<b>Error: Invalid request submitted.</b>';
			} catch (Stripe_AuthenticationError $e) {
				// Authentication with Stripe's API failed
				$errortype = 3;
				$msged = '<b>Error: Connection failed.</b>';
			} catch (Stripe_ApiConnectionError $e) {
				// Network communication with Stripe failed
				$errortype = 4;
				$msged = '<b>Error: Network communication error.</b>';
			} catch (Stripe_Error $e) {
				// Display a very generic error to the user, and maybe send
				// yourself an email
				$errortype = 5;
				$msged = '<b>Error: Connection not established</b>';
			} catch (Exception $e) {
				// Something else happened, completely unrelated to Stripe
				$errortype = 6;
				$msged = '<b>Error: Updation error. Please enter a valid information.</b>';
			}
		} //Stripe */
	}
}

$fld_organization_name = "";
$fld_team_name = "";
$fld_team_size = "";
$fld_donor_size = "";
$fld_campaign_sdate = "";
$fld_campaign_edate = "";
$fld_campaign_goal = 0;
$fld_participant_goal = "";
$fld_ab1575_pupil_fee = "";
$fld_show_participant_goal = "";
$fld_text_messaging = "";
$fld_organization_type = "";

$fld_organ_other  = "";
$readonly  = "";
$fld_nonprofit_number = "";
$fld_taxid_number  = "";
$fld_desc2  = "";
$fld_desc1  = "";
$fld_desc3  = "";
$fld_reward_ids = [];
$fld_reward_amount  = [];
$fld_reward_desc  = [];
$fld_reward_desc_details = [];
$fld_routing_number  = isset($fld_routing_number) ? $fld_routing_number : "";
$fld_account_number  = isset($fld_account_number) ? $fld_account_number : "";
$fld_bank_name  = "";
$fld_account_name  = isset($fld_account_name) ? $fld_account_name : "";
$fld_donation_level1  = "";
$fld_donation_level2  = "";
$fld_donation_level3  = "";
$makeimagegallerylink  = "";
$mode = "";
$makelogolink  = "";
$makevideogallerylink  = "";
// $ = "";
// $ = "";
// $ = "";



if(isset($REQUEST['cid']) && $REQUEST['cid'] > 0)
{
	$cid = $REQUEST['cid'];
	$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
	if( !isset($aCampaignDetail['fld_campaign_id']) ){
		$oregister->redirect('manage_campaign.php?msg=campaign details not found!');
		die();
	}
	// echo "942--aCampaignDetail: <pre>"; var_dump($aCampaignDetail); echo "</pre>"; 	
	$galleryDetail = $oCampaign->getimagegallery($cid);
	$videoDetail = $oCampaign->getvideogallery($cid);
	
	
	$fld_campaign_title = $aCampaignDetail['fld_campaign_title'];
	$fld_emailed_to = $aCampaignDetail['fld_cemail'];
	$fld_payable_to = $aCampaignDetail['fld_payable_to'];
	$fld_payment_method = $aCampaignDetail['fld_payment_method'];
	$fld_pin = $aCampaignDetail['fld_pin'];
	$fld_chash = $aCampaignDetail['fld_campaign_hashkey'];
	if( $fld_campaign_title != '')
	{
		//Logo, Image, Image Gallery, Video Gallery
		//Logo Code Start
		$fld_campaign_logo = $aCampaignDetail['fld_campaign_logo'];
		$directory = sHOMESCMS.'uploads/logo/';
		$ext_logo = pathinfo($directory.$fld_campaign_logo, PATHINFO_EXTENSION);
		$size_logo = filesizeWithoutError('uploads/logo/'.$fld_campaign_logo);
		if($size_logo === false){
			$size_logo = 0;	
		}
		$makelogolink1 = '{name: "'.$fld_campaign_logo.'",size: '.$size_logo.',type: "image/'.$ext_logo.'",file: "'.$directory.$fld_campaign_logo.'"}';
		$makelogolinkview = 'src: "'.$fld_campaign_logo.'"';
		if ($fld_campaign_logo != '') {
			$makelogolink = $makelogolink1;
		} else {
			$makelogolink = '';
		}
		//Logo Code End
		//Image Code Start
		$directory = sHOMESCMS.'uploads/image/';
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
		$directory = sHOMESCMS.'uploads/imagegallery/';
		$itemsimagegallery = array();
		$itemsimagegallery2 = array();
		// echo '<pre>'; var_dump($galleryDetail); die();
		if($galleryDetail){
			foreach($galleryDetail as $imagegallery) {
				$image_name = $imagegallery['fld_image'];
				$ext_image = pathinfo($directory.$imagegallery['fld_image'], PATHINFO_EXTENSION);
				$size_image = filesizeWithoutError('uploads/imagegallery/'.$imagegallery['fld_image']);
				$itemsimagegallery[] = '{name: "'.$image_name.'",size: '.$size_image.',type: "image/'.$ext_image.'",file: "'.$directory.$image_name.'"}';
				$itemsimagegallery2[] = '{src: "'.$image_name.'"}';
			}
		}
		$imagegallery2 = implode(",", $itemsimagegallery);
		$imagegallery3 = implode(",", $itemsimagegallery2);
		if (isset($imagegallery) && $imagegallery['fld_image'] != '') {
			$makeimagegallerylink = $imagegallery2;
			$makeimagegallerylink2 = $imagegallery3;
		} else {
			$makeimagegallerylink = '';
			$makeimagegallerylink2 = '';
		}
		//Image Gallery Code End
		//Video Gallery Code Start
		$directory = sHOMESCMS.'uploads/videogallery/';
		$itemsvideogallery = array();
		$itemsvideogallery2 = array();
		if($videoDetail){
			foreach($videoDetail as $videogallery) {
				$image_name = $videogallery['fld_video'];
				$ext_image = pathinfo($directory.$videogallery['fld_video'], PATHINFO_EXTENSION);
				$size_image = filesizeWithoutError('uploads/videogallery/'.$videogallery['fld_video']);
				$itemsvideogallery[] = '{name: "'.$image_name.'",size: '.$size_image.',type: "video/'.$ext_image.'",file: "'.$directory.$image_name.'"}';
				$itemsvideogallery2[] = '{src: "'.$image_name.'"}';
			}
		}
		$videogallery2 = implode(",", $itemsvideogallery);
		$videogallery3 = implode(",", $itemsvideogallery2);
		if (isset($videogallery) && $videogallery['fld_video'] != '') {
			$makevideogallerylink = $videogallery2;
			$makevideogallerylink2 = $videogallery3;
		} else {
			$makevideogallerylink = '';
			$makevideogallerylink2 = '';
		}
		//Video Gallery Code End
		//Logo, Image, Image Gallery, Video Gallery
		// echo '<pre>'; print_r($aCampaignDetail);die();
		$fld_organization_name = $aCampaignDetail['fld_organization_name'];
		$fld_team_name = $aCampaignDetail['fld_team_name'];
		$fld_team_size = $aCampaignDetail['fld_team_size'];
		$fld_donor_size = $aCampaignDetail['fld_donor_size'];
		$fld_campaign_sdate = date('m/d/Y',strtotime($aCampaignDetail['fld_campaign_sdate']));
		$fld_campaign_edate = date('m/d/Y',strtotime($aCampaignDetail['fld_campaign_edate']));
		$fld_campaign_goal = number_format($aCampaignDetail['fld_campaign_goal'], 2, '.', ',');
		$fld_participant_goal = number_format($aCampaignDetail['fld_participant_goal'], 2, '.', ',');	
		$fld_ab1575_pupil_fee = $aCampaignDetail['fld_ab1575_pupil_fee'];	
		$fld_show_participant_goal = $aCampaignDetail['fld_show_participant_goal'];	
		$fld_text_messaging = $aCampaignDetail['fld_text_messaging'];	
		if ($_SESSION['role_id'] != 1) {
			if ($fld_ab1575_pupil_fee == 1) {
				$readonly = 'disabled';
			} else {
				$readonly = '';
			}
		} else {
			$readonly = '';
		}
		
		issetAndNotOnEmptyArrayCheck($aCampaignDetail, ['fld_donation_level1', 'fld_donation_level2', 'fld_donation_level3'], 0);

		$fld_rewards = $aCampaignDetail['fld_rewards'];	
		$fld_nonprofit = $aCampaignDetail['fld_nonprofit'];	
		$fld_nonprofit_number = $aCampaignDetail['fld_nonprofit_number'];	
		$fld_desc1 = $aCampaignDetail['fld_desc1'];	
		$fld_desc2 = $aCampaignDetail['fld_desc2'];
		$fld_desc3 = $aCampaignDetail['fld_desc3'];
		$fld_donation_level1 = number_format($aCampaignDetail['fld_donation_level1'], 2, '.', ',');
		$fld_donation_level2 = number_format($aCampaignDetail['fld_donation_level2'], 2, '.', ',');
		$fld_donation_level3 = number_format($aCampaignDetail['fld_donation_level3'], 2, '.', ',');
		$fld_live = $aCampaignDetail['fld_live'];
		$fld_organization_type = $aCampaignDetail['fld_organization_type'];
		$fld_organ_other = $aCampaignDetail['fld_organ_other'];
		$fld_taxid_number = $aCampaignDetail['fld_taxid_number'];
		$fld_accid = $aCampaignDetail['fld_ac'];
		$fld_account_number = $aCampaignDetail['fld_bank_accno'];
		/*$fld_routing_number = $aCampaignDetail['fld_routing_number'];
		$fld_account_number = $aCampaignDetail['fld_account_number'];
		$fld_account_name = $aCampaignDetail['fld_account_name'];
		$fld_account_type = $aCampaignDetail['fld_account_type'];*/
		
		//These details are fetched from stripe
		if ($fld_accid != '') {
			try {
				$account = \Stripe\Account::retrieve("$fld_accid");
				$accounts = $account->__toArray(true);
				$fld_account_name = $accounts['external_accounts']['data'][0]['account_holder_name'];
				$fld_bank_name = $accounts['external_accounts']['data'][0]['bank_name'];
				$fld_account_type = $accounts['external_accounts']['data'][0]['account_holder_type'];
				$fld_routing_number = $accounts['external_accounts']['data'][0]['routing_number'];
				// die("fld_routing_number=".$fld_routing_number);
			} catch (Stripe_InvalidRequestError $e) {
				$exceptionE = $e;
				// Invalid parameters were supplied to Stripe's API
				$errortype = 2;
			} catch (Stripe_AuthenticationError $e) {
				$exceptionE = $e;
				// Authentication with Stripe's API failed
				$errortype = 3;
			} catch (Stripe_ApiConnectionError $e) {
				$exceptionE = $e;
				// Network communication with Stripe failed
				$errortype = 4;
			} catch (Stripe_Error $e) {
				$exceptionE = $e;
				// Display a very generic error to the user, and maybe send
				// yourself an email
				$errortype = 5;
			} catch (Exception $e) {
				$exceptionE = $e;
				// Something else happened, completely unrelated to Stripe
				$errortype = 6;
			}
		}
	} else {
		$fld_campaign_sdate = $def_campaign_sdate;
		$fld_campaign_edate = $def_campaign_edate;		
		//if ($fld_accid != '') {
			$fld_accid = $aCampaignDetail['fld_ac'];
		//}
	}
}else{
	$oregister->redirect('manage_campaign.php');
	die();
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
<title>Admin<?php echo sWEBSITENAME;?> - Basic Information</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="<?php echo sHOME;?>css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<link href="bower_components/sweetalert2/sweetalert2.css" rel="stylesheet" type="text/css">
<link href="bower_components/magnific-popup/magnific-popup.css" rel="stylesheet" type="text/css">
<link href="css/jquery.filer.css" type="text/css" rel="stylesheet" />
<link href="css/themes/jquery.filer-dragdropbox-theme.css" type="text/css" rel="stylesheet" />
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">
<!--<link href="assets/css/main.css" rel="stylesheet">-->
<!--<link href="assets/css/croppic.css" rel="stylesheet">-->

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<style>
.rotatorleft_disable, .rotatorgleft_disable, .rotatorright_disable, .rotatorgright_disable, jFiler-item-trash-action_disable {
	pointer-events: none;
    opacity: 0.4;
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
		  <h1 class="h1styling">Basic Information</h1>
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
<div class="div_ul">
<ul>
<li class="selected start-back"><a <?=$camp_inform;?> style="color: #fff !important;">START YOUR CAMPAIGN</a></li>
<li class="select_no basic-back"><a <?=$basic_inform;?> style="color: #fff !important;">BASIC INFORMATION</a></li>
<li class="select_no2"><a <?=$build_team;?>>BUILD YOUR TEAM</a></li>
<li class="select_no3"><a <?=$go_live;?>>FINISH</a></li>
</ul>
</div>
<div class="formdiv-in">
   <? if ($errortype != '') { ?>
   <div id="notifications" class="alert alert-danger alert-dismissable" style="padding: 6px 15px !important">
       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><?=$msged;?>
   </div>
   <? } ?>
   <form method="post" id="basicinformationform">
    <div class="form-group col-sm-6">
		<label for="fld_campaign_title" class="control-label">Campaign Name<span style="color:#FF0000">*</span></label>
		<input type="text" placeholder="Campaign Name" class="form-control" required name="fld_campaign_title" id="fld_campaign_title" value="<?=$fld_campaign_title?>"/>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_organization_name" class="control-label">Organization Name<span style="color:#FF0000">*</span></label>
		<input type="text" placeholder="Organization Name" class="form-control"  required name="fld_organization_name" id="fld_organization_name" value="<?=$fld_organization_name?>"/>
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
	
	<!--<div class="form-group col-sm-6">
		<label for="fld_team_name" class="control-label">Organization Nick Name<span style="color:#FF0000">*</span></label>
		<input type="text" placeholder="Team Name" class="form-control"  required name="fld_team_name" id="fld_team_name" value="<?=$fld_team_name?>"/>
		<div class="help-block with-errors"></div>
	</div>!-->
	
	<!--<div class="form-group col-sm-6">
		<label for="fld_organization_type" class="control-label">Organization Type<span style="color:#FF0000">*</span></label>
		<select class="form-control" name="fld_organization_type" id="fld_organization_type">
			<? if ($fld_organization_type == 'individual') { ?>
				<option selected>individual</option>
				<option>company</option>
				<option>Other</option>
			<? } elseif ($fld_organization_type == 'company') { ?>
				<option>individual</option>
				<option selected>company</option>
				<option>Other</option>
			<? } else { ?>
				<option>individual</option>
				<option>company</option>
				<option>Other</option>
			<? } ?>
		</select>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="organization_show_hide" style="display:none;">
	<div class="form-group col-sm-6">
		<label for="fld_organ_other" class="control-label">Other</label>
		<input type="text" placeholder="Organization Type" class="form-control" name="fld_organ_other" id="fld_organ_other" value="<?=$fld_organ_other?>" />
		<div class="help-block with-errors"></div>
	</div>
	</div>
    <div class="clearfix"></div>-->
	
	<div class="form-group col-sm-6">
		<label for="fld_team_size" class="control-label">Organization Size (Participants)<span style="color:#FF0000">*</span></label>
		<input type="text" placeholder="E.g. Number of players; club members, etc." class="form-control" pattern="\d*" required data-error="Number digits allowed only e.g 12" name="fld_team_size" id="fld_team_size" value="<?=$fld_team_size?>"/>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_donor_size" class="control-label">Suggested Number of Emails Per Participant<span style="color:#FF0000">*</span> <span style="cursor:pointer; font-size:11px;" id="exp4"><u>See Explanation</u></span></label>
		<input type="text" placeholder="Number of donors targeted" class="form-control" pattern="\d*" required data-error="Number digits allowed only e.g 15" name="fld_donor_size" id="fld_donor_size" value="<?=$fld_donor_size?>" />
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
   
	<div class="form-group col-sm-6">
		<label for="fld_campaign_sdate" class="control-label">Start Date (Min 20 Days, Max 30 Days)<span style="color:#FF0000">*</span></label>
		<div class="input-group calander-main">
		<input type="text" placeholder="Start Date" class="form-control mydatepicker" placeholder="mm/dd/yyyy"  id="fld_campaign_sdate" name="fld_campaign_sdate" required value="<?=$fld_campaign_sdate?>" data-mask="99/99/9999" 
		data-cur-date="<?=$fld_campaign_sdate?>"
		data-min-date="<?=$def_campaign_sdate?>"
		data-max-date="<?=$def_campaign_edate?>"
		/>
		<span class="input-group-addon campaignstartdate" style="border: 0px; padding:0 !important;">
            <span  class="fa fa-calendar" style="cursor:pointer;top: 4px !important;"></span>
        </span>
		</div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_campaign_edate" class="control-label">End Date<span style="color:#FF0000">*</span></label>
		<div class="input-group calander-main">
		<input type="text" placeholder="End Date" class="form-control mydatepicker" placeholder="mm/dd/yyyy" id="fld_campaign_edate" name="fld_campaign_edate"  required value="<?=$fld_campaign_edate?>" data-mask="99/99/9999"
		data-cur-date="<?=$fld_campaign_edate?>"
		data-min-date="<?=$def_campaign_sdate?>"
		data-max-date="<?=$def_campaign_edate?>"
		/>
		<span class="input-group-addon campaignenddate" style="border: 0px; padding:0 !important;">
            <span class="fa fa-calendar" style="cursor:pointer;top: 4px !important;"></span>
        </span>
		</div>
		<div class="col-sm-12">
		<div class="help-block with-errors"></div>
		</div>
	</div>
	<div class="clearfix"></div>
 
	<div class="form-group col-sm-6">
		<label for="fld_campaign_goal" class="control-label">Campaign Goal<span style="color:#FF0000">*</span></label>
		<div class="input-labal">
			<input type="text" placeholder="Campaign Goal" class="formdivtext basicinfousdinput" pattern="[-+]?[0-9]*[.,]?[0-9,.]+" required  data-error="Number digits allowed only e.g 1,000.00 or 1,000" name="fld_campaign_goal" id="fld_campaign_goal" value="<?=$fld_campaign_goal?>" onChange="addCommas(this.value,'fld_campaign_goal')">
			<span class="fa fa-usd donationdollar2"></span>
		</div>
		<!--<input type="text" placeholder="Campaign Goal" class="form-control"  required name="fld_campaign_goal" id="fld_campaign_goal" value="<?=$fld_campaign_goal?>" onChange="addCommas(this.value,'fld_campaign_goal')"/>-->
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_participant_goal" class="control-label">Participant Goal<span style="color:#FF0000">*</span></label>
		<div class="input-labal">
			<input type="text" placeholder="Participant Goal" class="formdivtext basicinfousdinput" pattern="[-+]?[0-9]*[.,]?[0-9,.]+" required data-error="Number digits allowed only e.g 1,000.00 or 1,000" name="fld_participant_goal" id="fld_participant_goal" value="<?=$fld_participant_goal?>" onChange="addCommas(this.value,'fld_participant_goal')">
			<span class="fa fa-usd donationdollar2"></span>
		</div>
		<!--<input type="text" placeholder="Participant Goal" class="form-control"  required name="fld_participant_goal" id="fld_participant_goal" value="<?=$fld_participant_goal?>" onChange="addCommas(this.value,'fld_participant_goal')"/>-->
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	
	<!-- New Field Added by Kurt on 08/21/2017 -->
	<div class="form-group col-sm-6">
		<label for="fld_ab1575_pupil_fee_label" class="control-label">Public School Pupil Fee Compliant<span style="color:#FF0000">*</span></label>
        <div class="col-sm-12">
			<div class="radio radio-warning col-sm-2" >
            <input type="radio" class="pupilyesno" name="fld_ab1575_pupil_fee[]" id="radio1" value="1" <?=$readonly;?> <? if ($fld_ab1575_pupil_fee == 1) {echo "checked";} ?> >
            <label for="radio1"> Yes </label>
			</div>
			<div class="radio radio-warning col-sm-2" style="margin-top:10px;">
			<input type="radio" class="pupilyesno" name="fld_ab1575_pupil_fee[]" id="radio2" value="0" <?=$readonly;?> <? if ($fld_ab1575_pupil_fee == 0) {echo "checked";} ?> >
            <label for="radio2"> No </label>
			</div>
			<div class="clearfix"></div>
			<p style="font-size: 11px; color:red;">Note: Once you select yes, this option can only be removed by <?php echo sWEBSITENAME;?> administration. Please contact your fundraising specialist with any questions.</p>
			<p>What is Public School Pupil Fee Compliant <a href="https://leginfo.legislature.ca.gov/faces/billNavClient.xhtml?bill_id=201120120AB1575" target="_blank">Click here</a></p>
            <div class="help-block with-errors" style="margin-top:10px"></div>
        </div>
	</div>
	<div class="form-group col-sm-6">
		<label for="fld_show_participant_goal_label" class="control-label">Show Participant Goal<span style="color:#FF0000">*</span></label>
        <div class="col-sm-12">
			<div class="radio radio-warning col-sm-2" >
            <input type="radio" class="pgoalyes" name="fld_show_participant_goal[]" id="radio1" value="1" <? if ($fld_show_participant_goal == 1) {echo "checked";} ?> >
            <label for="radio1"> Yes </label>
			</div>
			<div class="radio radio-warning col-sm-2" style="margin-top:10px;">
			<input type="radio" class="pgoalno" name="fld_show_participant_goal[]" id="radio2" value="0" <? if ($fld_show_participant_goal == 0) {echo "checked";} ?> >
            <label for="radio2"> No </label>
			</div>
            <div class="help-block with-errors" style="margin-top:10px"></div>
        </div>
		<br>
		<label for="fld_show_participant_goal_label" style="margin-top:15px;" class="control-label">Text Messaging<span style="color:#FF0000">*</span></label>
        <div class="col-sm-12">
			<div class="radio radio-warning col-sm-2" >
            <input type="radio" class="textmsgyes" name="fld_text_messaging[]" id="radio3" value="1" <? if ($fld_text_messaging == 1) {echo "checked";} ?> >
            <label for="radio3"> Yes </label>
			</div>
			<div class="radio radio-warning col-sm-2" style="margin-top:10px;">
			<input type="radio" class="textmsgno" name="fld_text_messaging[]" id="radio4" value="0" <? if ($fld_text_messaging == 0) {echo "checked";} ?> >
            <label for="radio4"> No </label>
			</div>
            <div class="clearfix"></div>
			<p style="font-size: 11px; color:red;">Note: A additional 1% will be deducted from your profits to utilize text messaging. This cost will not exceed $225.00.</p>
        </div>
	</div>
	<div class="clearfix"></div>
	<!-- New Field Added by Kurt on 08/21/2017 -->
	
	<div class="form-group col-sm-6">
		<label for="fld_nonprofit_label" class="control-label">Are you a Non-Profit Organization?<span style="color:#FF0000">*</span></label>
        <div class="col-sm-12">
			<div class="radio radio-warning col-sm-2" >
            <input type="radio" class="nonprofitshowhide" name="fld_nonprofit[]" id="radio1" value="1" <? if ($fld_nonprofit == 1) {echo "checked";} ?> >
            <label for="radio1"> Yes </label>
			</div>
			<div class="radio radio-warning col-sm-2" style="margin-top:10px;">
			<input type="radio" class="nonprofitshowhide" name="fld_nonprofit[]" id="radio2" value="0" <? if ($fld_nonprofit == 0) {echo "checked";} ?> >
            <label for="radio2"> No </label>
			</div>
            <div class="help-block with-errors" style="margin-top:10px"></div>
        </div>
	</div>
	
	<div class="form-group col-sm-6 nonprofithide" style="display:none;">
		<label for="fld_nonprofit_number" class="control-label">501c Nonprofit Number<span style="color:#FF0000">*</span></label>
		<input type="text" placeholder="501c Non-Profit Number" class="form-control"  required name="fld_nonprofit_number" id="fld_nonprofit_number" value="<?=$fld_nonprofit_number;?>" />
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6 taxidnumberhide" style="display:none;">
		<label for="fld_taxid_number" class="control-label">Tax ID Number<span style="color:#FF0000">*</span></label>
		<input type="text" placeholder="Tax ID Number" class="form-control"  required name="fld_taxid_number" id="fld_taxid_number" value="<?=$fld_taxid_number;?>" />
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_desc2" class="control-label">Why are donations needed?<span style="color:#FF0000">*</span> <span style="cursor:pointer; font-size:11px;" id="exp2"><u>See Examples</u></span></label>
		<textarea type="text" placeholder="Provide any information you would like to add." class="form-control" style="height:150px;" required name="fld_desc2" id="fld_desc2"><?=$fld_desc2?></textarea>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_desc1" class="control-label">How will your donation be used?<span style="color:#FF0000">*</span> <span style="cursor:pointer; font-size:11px;" id="exp1"><u>See Examples</u></span></label>
		<textarea type="text" placeholder="Briefly explain how the donations will be used." class="form-control" style="height:150px;" required name="fld_desc1" id="fld_desc1"><?=$fld_desc1?></textarea>
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
	<div class="form-group col-sm-6">
		<label for="fld_desc3" class="control-label">Thank You Note<span style="color:#FF0000">*</span> <span style="cursor:pointer; font-size:11px;" id="exp3"><u>See Examples</u></span></label>
		<textarea type="text" placeholder="Thank you note" class="form-control" style="height:150px;" name="fld_desc3" id="fld_desc3" required><?=$fld_desc3?></textarea>
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="donor_rewards" class="control-label">Donor Rewards<span style="color:#FF0000">*</span></label>
		<div class="col-sm-12">
			<div class="radio radio-warning col-sm-2" >
            <input type="radio" class="rewardsshowhide" name="fld_rewards[]" id="radio11" value="1" <? if ($fld_rewards == 1) {echo "checked";} ?> >
            <label for="radio11"> Yes </label>
			</div>
			<div class="radio radio-warning col-sm-2" style="margin-top:10px;">
			<input type="radio" class="rewardsshowhide" name="fld_rewards[]" id="radio22" value="0" <? if ($fld_rewards == 0) {echo "checked";} ?> >
            <label for="radio22"> No </label>
			</div>
            <div class="help-block with-errors" style="margin-top:10px"></div>
        </div>
	</div>
    <div class="clearfix"></div>
	
	<div class="rewardshide" style="display:none;">
	<?
	$rewardDetails = $oCampaign->donor_rewards($cid);
	// echo 'donor_rewards--<pre>'; var_dump($rewardDetails); die();
	$rewardDetailscount = count($rewardDetails);
	if($rewardDetailscount > 0){
		for ($snreward = 0; $snreward < 4; $snreward++) {
			$fld_reward_ids[$snreward] = $rewardDetails[$snreward]['id'];
			$fld_reward_amount[$snreward] = $rewardDetails[$snreward]['reward_amount'];
			$fld_reward_desc[$snreward] = $rewardDetails[$snreward]['reward_desc'];
			$fld_reward_desc_details[$snreward] = $rewardDetails[$snreward]['reward_desc_details'];
		}
	}

	for ($snreward = 0; $snreward < 4; $snreward++) {
		$fld_reward_ids[$snreward] = issetCheck($fld_reward_ids, $snreward, '');
		$fld_reward_amount[$snreward] = issetCheck($fld_reward_amount, $snreward, '');
		$fld_reward_desc[$snreward] = issetCheck($fld_reward_desc, $snreward, '');
		$fld_reward_desc_details[$snreward] = issetCheck($fld_reward_desc_details, $snreward, '');
	}
	?>
	<div class="col-sm-12">
	<div class="col-sm-6">
		<div class="row">
			<div class="form-group col-sm-4">
				<label for="rewards_amt1" class="control-label" style="font-size:14px;">Donation Amount<span style="color:#FF0000">*</span></label>
				<input type="hidden" name="rewards[reward_ids][]" value="<?=$fld_reward_ids[0];?>" />
				<input type="text" placeholder="e.g 50.00" class="form-control" name="rewards[reward_amt][]" id="rewards_amt1" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" value="<?=$fld_reward_amount[0];?>" />
				<div class="help-block with-errors"></div>
			</div>
			<div class="form-group col-sm-8">
				<label for="rewards_desc1" class="control-label" style="font-size:14px;">Donation Description<span style="color:#FF0000">*</span> [Max Char: 50]</label>
				<input type="text" placeholder="e.g T-Shirt" class="form-control" maxlength="50" name="rewards[rewards_desc][]" id="rewards_desc1" value="<?=$fld_reward_desc[0];?>" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-12">
				<label for="rewards_desc_details1" class="control-label" style="font-size:14px;">Reward Details<span style="color:#FF0000">*</span></label>
				<textarea type="text" placeholder="Reward Details" class="form-control" style="height:100px;" name="rewards[rewards_desc_details][]" id="rewards_desc_details1"><?=$fld_reward_desc_details[0]?></textarea>
				<div class="help-block with-errors"></div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="row">
			<div class="form-group col-sm-4">
				<label for="rewards_amt2" class="control-label" style="font-size:14px;">Donation Amount<span style="color:#FF0000">*</span></label>
				<input type="hidden" name="rewards[reward_ids][]" value="<?=$fld_reward_ids[1];?>" />
				<input type="text" placeholder="e.g 50.00" class="form-control" name="rewards[reward_amt][]" id="rewards_amt2" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" value="<?=$fld_reward_amount[1];?>" />
				<div class="help-block with-errors"></div>
			</div>
			<div class="form-group col-sm-8">
				<label for="rewards_desc2" class="control-label" style="font-size:14px;">Donation Description<span style="color:#FF0000">*</span> [Max Char: 50]</label>
				<input type="text" placeholder="e.g T-Shirt" class="form-control" maxlength="50" name="rewards[rewards_desc][]" id="rewards_desc2" value="<?=$fld_reward_desc[1];?>" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-12">
				<label for="rewards_desc_details2" class="control-label" style="font-size:14px;">Reward Details<span style="color:#FF0000">*</span></label>
				<textarea type="text" placeholder="Reward Details" class="form-control" style="height:100px;" name="rewards[rewards_desc_details][]" id="rewards_desc_details2"><?=$fld_reward_desc_details[1]?></textarea>
				<div class="help-block with-errors"></div>
			</div>
		</div>
	</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-sm-12">
	<div class="col-sm-6">
		<div class="row">
			<div class="form-group col-sm-4">
				<label for="rewards_amt3" class="control-label" style="font-size:14px;">Donation Amount<span style="color:#FF0000">*</span></label>
				<input type="hidden" name="rewards[reward_ids][]" value="<?=$fld_reward_ids[2];?>" />
				<input type="text" placeholder="e.g 50.00" class="form-control" name="rewards[reward_amt][]" id="rewards_amt3" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" value="<?=$fld_reward_amount[2];?>" />
				<div class="help-block with-errors"></div>
			</div>
			<div class="form-group col-sm-8">
				<label for="rewards_desc3" class="control-label" style="font-size:14px;">Donation Description<span style="color:#FF0000">*</span> [Max Char: 50]</label>
				<input type="text" placeholder="e.g T-Shirt" class="form-control" maxlength="50" name="rewards[rewards_desc][]" id="rewards_desc3" value="<?=$fld_reward_desc[2];?>" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-12">
				<label for="rewards_desc_details3" class="control-label" style="font-size:14px;">Reward Details<span style="color:#FF0000">*</span></label>
				<textarea type="text" placeholder="Reward Details" class="form-control" style="height:100px;" name="rewards[rewards_desc_details][]" id="rewards_desc_details3"><?=$fld_reward_desc_details[2]?></textarea>
				<div class="help-block with-errors"></div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="row">
			<div class="form-group col-sm-4">
				<label for="rewards_amt4" class="control-label" style="font-size:14px;">Donation Amount<span style="color:#FF0000">*</span></label>
				<input type="hidden" name="rewards[reward_ids][]" value="<?=$fld_reward_ids[3];?>" />
				<input type="text" placeholder="e.g 50.00" class="form-control" name="rewards[reward_amt][]" id="rewards_amt4" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" value="<?=$fld_reward_amount[3];?>" />
				<div class="help-block with-errors"></div>
			</div>
			<div class="form-group col-sm-8">
				<label for="rewards_desc4" class="control-label" style="font-size:14px;">Donation Description<span style="color:#FF0000">*</span> [Max Char: 50]</label>
				<input type="text" placeholder="e.g T-Shirt" class="form-control" maxlength="50" name="rewards[rewards_desc][]" id="rewards_desc4" value="<?=$fld_reward_desc[3];?>" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-12">
				<label for="rewards_desc_details4" class="control-label" style="font-size:14px;">Reward Details<span style="color:#FF0000">*</span></label>
				<textarea type="text" placeholder="Reward Details" class="form-control" style="height:100px;" name="rewards[rewards_desc_details][]" id="rewards_desc_details4"><?=$fld_reward_desc_details[3]?></textarea>
				<div class="help-block with-errors"></div>
			</div>
		</div>
	</div>
	</div>
	</div>
	
	<div class="colmd_12" align="center" style="clear:both"><h3 class="head1">Add Logo</h3><div class="line_new"></div></div>
    <div class="colmd_12" style="background-color:#FFFFFF;     margin-bottom: 19px;">
    <div id="content" style="padding:20px;" align="center" class="choose_btn"><input type="file" name="logo" id="logo" ></div>
    <h4 align="center">Logo should be no larger than 15MB</h4>
	</div>
	
    <div class="clearfix"></div>
  
     <div class="colmd_12" align="center"><h3 class="head1">Tell Your Story</h3><div class="line_new"></div></div>
     <div class="colmd_12" align="center">Time to brag about your team by simple uploading pictures or a great video. <a href="<?php echo Instructions2_VU_PDF;?>" target="_blank">Click here</a> to see</div>
	 <div class="colmd_12" align="center">how easy it is to upload your video, that can be simply taken on your smart phone.</div>
	 <div class="col-sm-12" style="background-color:#FFFFFF;margin-top:5px;margin-bottom:10px">
		<div class="col-sm-12" align="center">
			<h3>Choose Image Gallery or Video Gallery</h3>
		</div>
		<div class="col-sm-12" align="center">
			<button class="btn btn-success waves-effect waves-light" type="button" id="imagebutton" style="padding:6px 12px">Image Gallery</button>	
			<button class="btn btn-success waves-effect waves-light" type="button" id="videobutton" style="padding:6px 12px">Video Gallery</button>	
		</div>
		<div class="clearfix"></div>
	 </div>
	
    <div class="colmd_12" style="background-color:#FFFFFF;height:auto;">
    <div class="forimagegallery" >
	<h3 align="center" style="font-size:26px; color:#868484">Image Gallery</h3>
	<h4 align="center">If creating a video is not your thing, please upload multiple pictures of your organization and we will do the rest. Pictures should be no larger than 15MB</h4>
    <div id="content"><input type="file" name="galleryfiles[]" id="filer_input2" multiple="multiple"></div>
	<br>
	</div>
	<div class="forvideogallery">
	<br>
    <h3 align="center" style="font-size:26px; color:#868484">Video Gallery</h3>
	<h4 align="center" style="padding-left:2px">Upload a video of your team and let everyone know who you are and why you need their support. The video cannot be larger than 750MB</h4>
	<?php 
	$fld_video = "";
	//echo "1572-"; echo  var_dump($videoDetail);die();
	if( $videoDetail != null ){
		if( 	isset($videoDetail) 
			&&  isset($videoDetail[0]) 
			&& isset($videoDetail[0]['fld_video']) 
		){
			$fld_video = $videoDetail[0]['fld_video'];
		}
	}
	?>
    <!--<div id="content" style="padding:0px 20px 0px 20px;" align="center"><input type="file" name="videofiles[]" id="videos" multiple="multiple"></div>-->
    <div id="content" style="padding:0px 20px 0px 20px;" align="center"><input type="text" placeholder="Youtube Video Link" class="form-control"  name="videofiles" id="videofiles" value="<?=$fld_video;?>"/></div>
	<br>
	</div>
    <div style="clear:both"></div>
    </div>
    <div class="clearfix"></div>
	<br>
	<div class="col-sm-6">
		<div class="radio radio-warning col-sm-12" style="margin-top:10px; zoom: 1.5">
            <input type="radio" class="paymentmethodshowhide" name="fld_payment_method[]" id="radio3" value="0" <? if ($fld_payment_method == 0) {echo "checked";} ?> >
            <label for="radio3"> Direct Deposit </label>
            <p style="font-size: 6px; line-height:6px">Please enter your routing and account number below</p>
		</div>
    </div>
    <div class="col-sm-6">
    	<div class="radio radio-warning col-sm-12" style="margin-top:10px; zoom: 1.5">
			<input type="radio" class="paymentmethodshowhide" name="fld_payment_method[]" id="radio4" value="1" <? if ($fld_payment_method == 1) {echo "checked";} ?> >
			<label for="radio4"> Check Request </label>
			<div class="clearfix"></div>
			<p style="font-size: 6px; line-height:6px">Check will be emailed to the campaign manager’s email address . Checks can only be made out to your organization or school.</p>
		</div>
    </div>
	<div class="clearfix"></div>

	<div class="colmd_12" align="center"><h3 class="head1">Bank Details</h3><div class="line_new"></div></div>
	<div class="directdeposithide" style="display: none;">
	<div class="form-group col-sm-6">
		<label for="fld_routing_number" class="control-label">Routing Number<span style="color:#FF0000">*</span></label>
		<input type="text" placeholder="E.g. 12345678" class="form-control" data-inputmask="'mask': ['999999999', '999999999']" data-mask="" required name="fld_routing_number" id="fld_routing_number" value="<?=$fld_routing_number?>"/>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_account_number" class="control-label">Account Number<span style="color:#FF0000">*</span></label>
		<input type="text" placeholder="E.g. 1234567890" class="form-control" pattern="\d*" required  data-error="Number digits allowed only e.g. 1234567890" required name="fld_account_number" id="fld_account_number" value="<?=$fld_account_number?>"/>
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group col-sm-6">
		<label for="fld_routing_number" class="control-label">Confirm Routing Number<span style="color:#FF0000">*</span></label>
		<input type="text" placeholder="E.g. 12345678" class="form-control" data-inputmask="'mask': ['999999999', '999999999']" data-mask="" data-match="#fld_routing_number" data-match-error="Error: routing number don't matched" required value="<?=$fld_routing_number?>" />
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_account_number" class="control-label">Confirm Account Number<span style="color:#FF0000">*</span></label>
		<input type="text" placeholder="E.g. 1234567890" class="form-control" pattern="\d*" required  data-error="Number digits allowed only e.g. 1234567890" data-match="#fld_account_number" data-match-error="Error: account number don't matched" required value="<?=$fld_account_number?>" />
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_bank_name" class="control-label">Bank Name</label>
		<input type="text" readonly placeholder="E.g. Bank of America" class="form-control" name="fld_bank_name" id="fld_bank_name" value="<?=$fld_bank_name?>"/>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_account_name" class="control-label">Account Name<span style="color:#FF0000">*</span></label>
		<input type="text" placeholder="E.g. John Doe" class="form-control" required name="fld_account_name" id="fld_account_name" value="<?=$fld_account_name?>"/>
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	</div>

	<div class="paymentmethodhide" style="display: none;">
	<div class="form-group col-sm-6">
		<label for="fld_payable_to" class="control-label">Make Check Payable To<span style="color:#FF0000">*</span></label>
		<input type="text" placeholder="E.g. John Smith" class="form-control" name="fld_payable_to" id="fld_payable_to" value="<?=$fld_payable_to?>"/>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_emailed_to" class="control-label">Check Will Be Emailed To</label>
		<input type="text" readonly placeholder="E.g. email@domain.com" class="form-control" required name="fld_emailed_to" id="fld_emailed_to" value="<?=$fld_emailed_to?>"/>
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group col-sm-6">
		<br>
		<img src="Deluxe-eChecks-Logo.jpg" width="180" height="80">
	</div>
	<div class="form-group col-sm-6">
		<a id="youtubeplay" href="#" style="font-size:12px">See how Deluxe eCheck will delivery your funds safe and sound!</a><br>
		<iframe id="youtubeclicked" allowfullscreen="allowfullscreen" mozallowfullscreen="mozallowfullscreen" msallowfullscreen="msallowfullscreen" oallowfullscreen="oallowfullscreen" webkitallowfullscreen="webkitallowfullscreen" width="250" height="150" src="https://www.youtube.com/embed/i7sqcFUPVNU?rel=0"></iframe>
	</div>
	</div>
	<div class="clearfix"></div>
   <!--<div class="col-md-12">
			  <?
			  if ($fld_donation_level1 != '') {
				  $donation_level1 = $fld_donation_level1;
			  } else {
				  $donation_level1 = $sSettingsData['fld_donation_level1_amt'];
			  }
			  if ($fld_donation_level2 != '') {
				  $donation_level2 = $fld_donation_level2;
			  } else {
				  $donation_level2 = $sSettingsData['fld_donation_level2_amt'];
			  }
			  if ($fld_donation_level3 != '') {
				  $donation_level3 = $fld_donation_level3;
			  } else {
				  $donation_level3 = $sSettingsData['fld_donation_level3_amt'];
			  }
			  ?>
              <h3 class="text-center">Donation Levels</h3>
              <div class="line_new"></div>
              <div class="form-group col-md-4"><div class="gold_sec">
              <div class="img_gold">
              <img src="images/img15.png" class="img-responsive" style="width:100%" />
              <h4><?=$sSettingsData['fld_donation_level1']?></h4>
              
              </div>
			  <div align="center" class="donationlabel">
				<input type="text" placeholder="&#x24;Donation Amount" class="formdivtext donationinput" name="fld_donation_level1" id="fld_donation_level1" value="<?=$donation_level1?>" onChange="addCommas(this.value,'fld_donation_level1')" required>
				<span class="fa fa-usd donationdollar"></span>
			  </div>

			  <div class="col-sm-12">
				<div class="help-block with-errors" style="height: 35px;"></div>
			  </div>
              
              <h6><span>$<?=$sSettingsData['fld_donation_level1_amt']?> Minimum</span></h6>
              </div> 
			  </div>
			  
              <div class="form-group col-md-4"><div class="gold_sec">
              <div class="img_gold">
              <img src="images/img16.png" class="img-responsive" style="width:100%" />
              <h4><?=$sSettingsData['fld_donation_level2']?></h4>
              
              </div>
			  <div align="center" class="donationlabel">
				<input type="text" placeholder="&#x24;Donation Amount" class="formdivtext donationinput" name="fld_donation_level2" id="fld_donation_level2" value="<?=$donation_level2?>" onChange="addCommas(this.value,'fld_donation_level2')" required>
				<span class="fa fa-usd donationdollar"></span>
			  </div>
              
			  <div class="col-sm-12">
				<div class="help-block with-errors" style="height: 35px;"></div>
			  </div>
              <h6><span>$<?=$sSettingsData['fld_donation_level2_amt']?> Minimum</span></h6>
              </div> </div>
              <div class="form-group col-md-4"><div class="gold_sec">
              <div class="img_gold">
              <img src="images/img17.png" class="img-responsive" style="width:100%" />
              <h4><?=$sSettingsData['fld_donation_level3']?></h4>
              
              </div>
			  <div align="center" class="donationlabel">
				<input type="text" placeholder="&#x24;Donation Amount" class="formdivtext donationinput" name="fld_donation_level3" id="fld_donation_level3" value="<?=$donation_level3?>" onChange="addCommas(this.value,'fld_donation_level3')" required>
				<span class="fa fa-usd donationdollar"></span>
			  </div>
               
               <div class="col-sm-12">
				 <div class="help-block with-errors" style="height: 35px;"></div>
			   </div>
              <h6><span>$<?=$sSettingsData['fld_donation_level3_amt']?> Minimum</span></h6>
              </div> </div>
              
              
              </div>
   <div class="clearfix"></div>-->
   <div class="form-group">
    	<input type="hidden" name="mode" id="mode" value="<?=$mode?>">  
    	<input type="hidden" name="pin" id="pin" value="<?=$fld_pin?>">  
    	<input type="hidden" name="chash" id="chash" value="<?=$fld_chash?>">  
        <input type="hidden" name="cid" id="cid" value="<?=$REQUEST['cid']?>"> 
        <input type="hidden" name="accid" id="accid" value="<?=$fld_accid?>"> 
		<input type="hidden" name="fld_campaign_id" id="fld_campaign_id" value="<?php echo $REQUEST['cid']?>">
		<div class="row"> 
		<div class="col-sm-6 basic-but-left" align="left">
			<button class="btn btn-primary waves-effect waves-light" type="button" onClick="step1(<?=$REQUEST['cid']?>)"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Back</button>
			<button class="btn btn-primary waves-effect waves-light" type="button" onclick="window.location.href='manage_campaign.php'"><span class="btn-label"><i class="fa fa-times"></i></span>Cancel</button>
		</div>
		
		<div class="col-sm-6 basic-but-right" align="right">
			<button class="btn btn-success waves-effect waves-light" type="button" name="save1" id="save1">Save <span class="btn-label forright-icon"><i class="fa fa-floppy-o"></i></span></button>
			<button class="btn btn-success waves-effect waves-light" type="submit" name="save" id="save" formnovalidate="formnovalidate" style="display:none;">Save <span class="btn-label forright-icon"><i class="fa fa-floppy-o"></i></span></button>
			<button class="btn btn-success waves-effect waves-light build_team-but"  name="savecontinue1" id="savecontinue1" type="button">Save & Continue <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>			
			<button class="btn btn-success waves-effect waves-light build_team-but"  name="savecontinue" id="savecontinue" type="submit" style="display:none;">Save & Continue <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>			
		</div>
		</div>
		<div id="alertbottom" class="myadmin-alert myadmin-alert-icon myadmin-alert-click alert3 myadmin-alert-bottom" style="display: none; background: #fcb514 none repeat scroll 0 0;" > <i class="fa fa-info"></i> you can’t save and move on until the picture or video is uploaded </div>		
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
<script>
var nowDate = new Date();
var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
</script>
<script src="assets/js/main.js"></script>
<script src="bower_components/jquery/dist/jquery.min.js"></script>


<!-- Bootstrap Core JavaScript -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Menu Plugin JavaScript -->
<script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
<!--Nice scroll JavaScript -->
<script src="assets/js/jquery.mousewheel.min.js"></script>
<script src="assets/croppic.min.js"></script>


<script src="js/jquery.nicescroll.js"></script>
<script src="js/jquery.rotate.js"></script>
<script type="text/javascript" src="js/jquery.filer.min.js?v=1.0.5"></script>
<script type="text/javascript" src="js/custom.js?v=1.0.5"></script>
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
<script src="<?php echo sHOME;?>js/jquery-ui.min.js"></script>


<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>
<script src="bower_components/sweetalert2/sweetalert2.min.js"></script>
<script src="bower_components/magnific-popup/jquery.magnific-popup.min.js"></script>
<!--<script src="bower_components/magnific-popup/jquery.magnific-popup.js"></script>-->

<script src="js/validator.js"></script>
<script>
$('#youtubeplay').on('click', function(ev) {
    $("#youtubeclicked")[0].src += "&autoplay=1";
    ev.preventDefault();
});
$('.fa-check').hide();
    
    function parseDate(str) {
    var mdy = str.split('/')
    return new Date(mdy[2], mdy[0]-1, mdy[1]);
}


function daydiff(first, second) {
   return (second-first)/(1000*60*60*24)
}
$('.fa-check').css('color','transparent');

$(document).on('click', '.campaignstartdate', function(){
	$('#fld_campaign_sdate').focus();
});



$(document).on('click', '.campaignenddate', function(){
	$('#fld_campaign_edate').focus();
});
</script>
<script type="text/javascript" src="js/accounting.js"></script>
<script type="text/javascript">
function addCommas(x,txtname) {

	var mval = accounting.formatMoney(x); 
	mval = mval.replace('$', '');
	document.getElementById(txtname).value = mval;
}

var rewards = <?=$fld_rewards;?>;
if (rewards == 1) {
	$('.rewardshide').show();
} else {
	$('.rewardshide').hide();
}

var nonprofit = <?=$fld_nonprofit;?>;
if (nonprofit == 1) {
	$('.nonprofithide').show();
} else {
	$('.taxidnumberhide').show();
}

var checkedvalue1 = <?php echo $fld_payment_method = isset($fld_payment_method) ? $fld_payment_method : 0;?>;
if (checkedvalue1 == 1) {
	$('.paymentmethodhide').show();
} else {
	$('.directdeposithide').show();
}

$('.nonprofitshowhide').on('click', function() {
	var checkedvalue = $(this).val();
	if (checkedvalue == 1) {
		$('.taxidnumberhide').hide();
		$('.nonprofithide').show();
		$('#fld_taxid_number').val('');
	} else {
		$('.nonprofithide').hide();
		$('.taxidnumberhide').show();
	}
});

$('.pupilyesno').on('click', function() {
	var checkedvalue = $(this).val();
	if (checkedvalue == 1) {
		$('.pgoalno').prop("checked",true);
	} else {
		$('.pgoalyes').prop("checked",true);
	}
});

$('.rewardsshowhide').on('click', function() {
	var checkedvalue = $(this).val();
	if (checkedvalue == 1) {
		$('.rewardshide').show();
		$("#rewards_amt1").attr("required", "true");
		$("#rewards_desc1").attr("required", "true");
	} else {
		$('.rewardshide').hide();
		$("#rewards_amt1").attr("required", "false");
		$("#rewards_desc1").attr("required", "false");
	}
});

$('.paymentmethodshowhide').on('click', function() {
	var checkedvalue1 = $(this).val();
	if (checkedvalue1 == 1) {
		$('.directdeposithide').hide();
		$('.paymentmethodhide').show();
		$('#fld_payable_to').val('');
	} else {
		$('.paymentmethodhide').hide();
		$('.directdeposithide').show();
	}
});

$('#save1').on('click', function() {
	$('#basicinformationform').removeAttr("data-toggle");
	$('#basicinformationform').validator('destroy');
	$('#save').click();
});
$('#savecontinue1').on('click', function() {
	$('#basicinformationform').attr("data-toggle","validator");
	$('#basicinformationform').validator();
	$('#savecontinue').click();
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
	swal("Suggested Number of Emails Per Participant", "The suggested # of emails to by uploaded by each participant.  These email address will only be used during your campaign and will not be used for any other form of solicitation.");
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

/*var organizationtype = '<?=$fld_organization_type;?>';
if (organizationtype == 'Other') {
	$('.organization_show_hide').show();
} else {
	$('.organization_show_hide').hide();
}

$('#fld_organization_type').change(function(){
  if($(this).val() == 'Other'){ // or this.value == 'volvo'
    $('.organization_show_hide').show();
	$('#fld_organ_other').val('');
  } else {
	  $('.organization_show_hide').hide();
  }
});*/
	 
function step1(id){
	window.location.href = 'start_campaign.php?m=e&cid='+id;
}
function step3(id){
	window.location.href = 'build_team.php?cid='+id;
}
function step4(id){
	window.location.href = 'confirmation.php?cid='+id;
}
$(document).on('blur', '#fld_campaign_goal', function(){
	var fld_team_size = parseFloat($('#fld_team_size').val().replace(/,/g, ''));
	var fld_campaign_goal = parseFloat($('#fld_campaign_goal').val().replace(/,/g, ''));
	if (fld_campaign_goal != '' && fld_team_size != '') {
		var total_fld_participant_goal = fld_campaign_goal/fld_team_size;
		addCommas(total_fld_participant_goal, 'fld_participant_goal');
	}
});


$(document).on('blur', '#fld_team_size', function(){
	var fld_team_size = parseFloat($('#fld_team_size').val().replace(/,/g, ''));
	var fld_campaign_goal = parseFloat($('#fld_campaign_goal').val().replace(/,/g, ''));
	if (fld_campaign_goal != '' && fld_team_size != '') {
		var total_fld_participant_goal = fld_campaign_goal/fld_team_size;
		addCommas(total_fld_participant_goal, 'fld_participant_goal');
	}
});

function addDays(date, days) {
  var result = new Date(date);
  result.setDate(result.getDate() + days);
  return result;
}

function getDateStr(newDate){
	let newdateStr = newDate.getDate()+'/'+( parseInt(newDate.getMonth())+1 )+'/'+newDate.getFullYear();
	return newdateStr;
}

function setEndDate(){
    let sdate = $('#fld_campaign_sdate').val().trim();
    if(sdate != ""){
	    let sdate0 = addDays(sdate, 0);
	    let next20days = addDays(sdate, 20);
	    let next30days = addDays(sdate, 30);
	    let next20daysStr = getDateStr(next20days);
	    let next30daysStr = getDateStr(next30days);


	    $('#fld_campaign_edate')
	    .datepicker( "option", "startDate", next20days )
	    .datepicker( "option", "minDate", next20days )
	    .datepicker( "option", "maxDate", next30days );
	    console.log(sdate0, next20days, next30days);
	}
}


	var dateBasicConfig = {
		startDate: today,
	    format: 'mm/dd/yyyy',
	    <?php 
		if($role_id == '1'){
			echo "
			minDate: '-0D',
		    maxDate: '+1Y',
		    ";
		}
		else{
			echo "
			minDate: '0D',
		    // maxDate: '+30D',
		    maxDate: '+1Y',
		    ";
		}
		?>
		autoclose: true
	};

$(document).ready(function() {	
	//Date picker
	$('#fld_campaign_sdate').datepicker(dateBasicConfig).on('change', function(){
      	<?php 
		if($role_id != '1'){
      		echo 'setEndDate();';
      	}
      	?>
    });

	$('#fld_campaign_edate').datepicker(dateBasicConfig)
	.change(function () { 	
		var dif=daydiff(parseDate($('#fld_campaign_sdate').val()), parseDate($('#fld_campaign_edate').val()));
		date_adjust_allowed = <?=$_SESSION['role_id'];?>;
		if(dif<20 || dif>30)
		{
			if (date_adjust_allowed != 1) {
				$('.pp').css('color','red');
				$('.fp').css('color','#c44');
				$('.fa-check').show();
				document.getElementById('fld_campaign_edate').setCustomValidity('Campaigns must be between 20 to 30 days long. First check can be requested at 1/2 way point of campaign');
			}
		}
		else{
			if (date_adjust_allowed != 1) {
				$('.fa-check').hide();
				document.getElementById('fld_campaign_edate').setCustomValidity('');
			}
		}
		$('#basicinformationform').validator();
	});

	//Logo
     $("#logo").filer({
        limit: 1,
        maxSize: 15, //FileSize in MB
        extensions: ['jpg','gif','png','JPG','JPEG', 'jpeg','GIF','BMP','bmp','PNG'],
		changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Drag & drop files here.</h3> <span style="display:inline-block; margin: 15px 0">or</span></div><a class="jFiler-input-choose-btn blue">BROWSE FILES</a></div></div>',
        showThumbs: true,
        theme: "dragdropbox",
        templates: {
            box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
            item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb imgrotator">\
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
                                        <li><a class="rotatorleft" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Left rotate the image"><i class="fa fa-undo"></i></a>\</li>\
                                        <li><a class="rotatorright" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Right rotate the image"><i class="fa fa-repeat"></i></a>\</li>\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            itemAppend: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb imgrotator">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                            <span class="jFiler-item-others">{{fi-size2}}</span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
											<li class="textsuccess"></li>\
										</ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="rotatorleft" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Left rotate the image"><i class="fa fa-undo"></i></a></li>\
											<li><a class="rotatorright" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Right rotate the image"><i class="fa fa-repeat"></i></a></li>\
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
            url: "./php/upload_logo.php",
			data:{cid:$("#fld_campaign_id").val()},
            type: 'POST',
            enctype: 'multipart/form-data',
            beforeSend: function(){
				
			},
            success: function(data, el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");    
                });
				var imgprofilepic = jQuery.parseJSON(data);
				el.find(".jFiler-item-title b").attr("title", imgprofilepic);
				el.find(".jFiler-item-title b").text(imgprofilepic);
            },
            error: function(el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            statusCode: null,
            onProgress: function(){
				$('#alertbottom').show();
			},
            onComplete: function(filename){
				$('#alertbottom').hide();
				
				var value = 0
				$(".rotatorleft").rotate({
					bind:
					{
						click: function(){
							var cid = <?=$cid;?>;
							var filename = $(this).closest(" .jFiler-item-inner ").find(" .jFiler-item-title b ").attr("title");
							var rotate = 'left';
							value -=90;
							$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
							$(this).closest(" .jFiler-item-assets ").find(" .text-success ").text('Processing');
							$(this).closest(".jFiler-item-assets").find(" .rotatorleft ").attr('class', 'rotatorleft_disable');
							$(this).closest(".jFiler-item-assets").find(" .rotatorright ").attr('class', 'rotatorright_disable');
							$(this).closest(".jFiler-item-assets").find(" .icon-jfi-trash ").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
							$.post('img_controller.php', 'cid=' + cid + '&file=' + filename + '&rotate=' + rotate + '&act=1', function (response) {
								var jdata = JSON.parse(response);
								if (jdata.is_success == 1) {
									//Success
									setTimeout(function(){ 
										$(".rotatorleft_disable").closest(" .jFiler-item-assets ").find(" .text-success ").text('Success');
										$(".rotatorleft_disable").attr('class', 'rotatorleft');
										$(".rotatorright_disable").attr('class', 'rotatorright');
										$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
									}, 3000);
								} else {
									//Failed
									$(".rotatorleft_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Failed');
									$(".rotatorleft_disable").attr('class', 'rotatorleft');
									$(".rotatorright_disable").attr('class', 'rotatorright');
									$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
								}
							});
						}
					}
				});
				$(".rotatorright").rotate({
					bind:
					{
						click: function(){
							var cid = <?=$cid;?>;
							var filename = $(this).closest(" .jFiler-item-inner ").find(" .jFiler-item-title b ").attr("title");
							var rotate = 'right';
							value +=90;
							$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
							$(this).closest(" .jFiler-item-assets ").find(" .text-success ").text('Processing');
							$(this).closest(".jFiler-item-assets").find(" .rotatorleft ").attr('class', 'rotatorleft_disable');
							$(this).closest(".jFiler-item-assets").find(" .rotatorright ").attr('class', 'rotatorright_disable');
							$(this).closest(".jFiler-item-assets").find(" .icon-jfi-trash ").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
							$.post('img_controller.php', 'cid=' + cid + '&file=' + filename + '&rotate=' + rotate + '&act=1', function (response) {
								var jdata = JSON.parse(response);
								if (jdata.is_success == 1) {
									//Success
									setTimeout(function(){ 
										$(".rotatorright_disable").closest(" .jFiler-item-assets ").find(" .text-success ").text('Success');
										$(".rotatorleft_disable").attr('class', 'rotatorleft');
										$(".rotatorright_disable").attr('class', 'rotatorright');
										$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
									}, 3000);
								} else {
									//Failed
									$(".rotatorright_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Failed');
									$(".rotatorleft_disable").attr('class', 'rotatorleft');
									$(".rotatorright_disable").attr('class', 'rotatorright');
									$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
								}
							});
						}
					}
				});
			}
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
        afterShow: function(){},
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
	var value = 0
	$(".rotatorleft").rotate({
	bind:
	{
		click: function(){
			var cid = <?=$cid;?>;
			var filename = $(this).closest(" .jFiler-item-inner ").find(" img ").attr("src");
			var rotate = 'left';
			value -=90;
			$(this).closest(" .jFiler-item-assets ").find(" .textsuccess ").text('Processing');
			$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
			$(this).closest(".jFiler-item-assets").find(" .rotatorleft ").attr('class', 'rotatorleft_disable');
			$(this).closest(".jFiler-item-assets").find(" .rotatorright ").attr('class', 'rotatorright_disable');
			$(this).closest(".jFiler-item-assets").find(" .icon-jfi-trash ").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
			$.post('img_controller.php', 'cid=' + cid + '&file=' + filename + '&rotate=' + rotate + '&act=1', function (response) {
				var jdata = JSON.parse(response);
				if (jdata.is_success == 1) {
					//Success
					setTimeout(function(){ 
						$(".rotatorleft_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Success');
						$(".rotatorleft_disable").attr('class', 'rotatorleft');
						$(".rotatorright_disable").attr('class', 'rotatorright');
						$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
					}, 3000);
				} else {
					//Failed
					$(".rotatorleft_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Failed');
					$(".rotatorleft_disable").attr('class', 'rotatorleft');
					$(".rotatorright_disable").attr('class', 'rotatorright');
					$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
				}
			});
		}
	}
	});
	$(".rotatorright").rotate({
	bind:
	{
		click: function(){
			var cid = <?=$cid;?>;
			var filename = $(this).closest(" .jFiler-item-inner ").find(" img ").attr("src");
			var rotate = 'right';
			value +=90;
			$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
			$(this).closest(" .jFiler-item-assets ").find(" .textsuccess ").text('Processing');
			$(this).closest(".jFiler-item-assets").find(" .rotatorleft ").attr('class', 'rotatorleft_disable');
			$(this).closest(".jFiler-item-assets").find(" .rotatorright ").attr('class', 'rotatorright_disable');
			$(this).closest(".jFiler-item-assets").find(" .icon-jfi-trash ").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
			$.post('img_controller.php', 'cid=' + cid + '&file=' + filename + '&rotate=' + rotate + '&act=1', function (response) {
				var jdata = JSON.parse(response);
				if (jdata.is_success == 1) {
					//Success
					setTimeout(function(){ 
						$(".rotatorright_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Success');
						$(".rotatorleft_disable").attr('class', 'rotatorleft');
						$(".rotatorright_disable").attr('class', 'rotatorright');
						$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
					}, 3000);
				} else {
					//Failed
					$(".rotatorright_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Failed');
					$(".rotatorleft_disable").attr('class', 'rotatorleft');
					$(".rotatorright_disable").attr('class', 'rotatorright');
					$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
				}
			});
		}
	}
	});
	
	//Image Gallery
    $("#filer_input2").filer({
        limit: 6,
        maxSize: 15,
        extensions: ['jpg','gif','png','JPG','JPEG', 'jpeg','GIF','BMP','bmp','PNG'],
        changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Drag & drop files here.</h3> <span style="display:inline-block; margin: 15px 0">or</span></div><a class="jFiler-input-choose-btn blue">BROWSE IMAGE</a></div></div>',
        showThumbs: true,
        theme: "dragdropbox",
        templates: {
            box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
            item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb imggrotator">\
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
                                        <li><a class="rotatorgleft" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Left rotate the image"><i class="fa fa-undo"></i></a>\</li>\
                                        <li><a class="rotatorgright" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Right rotate the image"><i class="fa fa-repeat"></i></a>\</li>\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            itemAppend: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb imggrotator">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                            <span class="jFiler-item-others">{{fi-size2}}</span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
										<ul class="list-inline pull-left">\
											<li class="textsuccess"></li>\
										</ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="rotatorgleft" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Left rotate the image"><i class="fa fa-undo"></i></a></li>\
											<li><a class="rotatorgright" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Right rotate the image"><i class="fa fa-repeat"></i></a></li>\
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
				var imgprofilepic = jQuery.parseJSON(data);
				//alert(imgprofilepic);
				el.find(".jFiler-item-title b").attr("title", imgprofilepic);
				el.find(".jFiler-item-title b").text(imgprofilepic);
            },
            error: function(el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            statusCode: null,
            onProgress: function(){
				$('#alertbottom').show();
			},
            onComplete: function(){
				$('#alertbottom').hide();

				var value = 0
				$(".rotatorgleft").rotate({
					bind:
					{
						click: function(){
							var cid = <?=$cid;?>;
							var filename = $(this).closest(" .jFiler-item-inner ").find(" .jFiler-item-title b ").attr("title");
							var rotate = 'left';
							value -=90;
							$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
							$(this).closest(" .jFiler-item-assets ").find(" .text-success ").text('Processing');
							
							$(this).closest(".jFiler-item-assets").find(" .rotatorgleft ").attr('class', 'rotatorgleft_disable');
							$(this).closest(".jFiler-item-assets").find(" .rotatorgright ").attr('class', 'rotatorgright_disable');
							$(this).closest(".jFiler-item-assets").find(" .icon-jfi-trash ").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
							$.post('img_controller.php', 'cid=' + cid + '&file=' + filename + '&rotate=' + rotate + '&act=2', function (response) {
								var jdata = JSON.parse(response);
								if (jdata.is_success == 1) {
									//Success
									setTimeout(function(){ 
										$(".rotatorgleft_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Success');
										$(".rotatorgleft_disable").attr('class', 'rotatorgleft');
										$(".rotatorgright_disable").attr('class', 'rotatorgright');
										$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
									}, 3000);
								} else {
									//Failed
									$(".rotatorgleft_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Failed');
									$(".rotatorgleft_disable").attr('class', 'rotatorgleft');
									$(".rotatorgright_disable").attr('class', 'rotatorgright');
									$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
								}
							});
						}
					}
				});
				$(".rotatorgright").rotate({
					bind:
					{
						click: function(){
							var cid = <?=$cid;?>;
							var filename = $(this).closest(" .jFiler-item-inner ").find(" .jFiler-item-title b ").attr("title");
							var rotate = 'right';
							value +=90;
							$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
							$(this).closest(" .jFiler-item-assets ").find(" .text-success ").text('Processing');
							$(this).closest(".jFiler-item-assets").find(".rotatorgleft").attr('class', 'rotatorgleft_disable');
							$(this).closest(".jFiler-item-assets").find(".rotatorgright").attr('class', 'rotatorgright_disable');
							$(this).closest(".jFiler-item-assets").find(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
							$.post('img_controller.php', 'cid=' + cid + '&file=' + filename + '&rotate=' + rotate + '&act=2', function (response) {
								var jdata = JSON.parse(response);
								if (jdata.is_success == 1) {
									//Success
									setTimeout(function(){ 
										$(".rotatorgright_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Success');
										$(".rotatorgleft_disable").attr('class', 'rotatorgleft');
										$(".rotatorgright_disable").attr('class', 'rotatorgright');
										$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
									}, 3000);
								} else {
									//Failed
									$(".rotatorgright_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Failed');
									$(".rotatorgleft_disable").attr('class', 'rotatorgleft');
									$(".rotatorgright_disable").attr('class', 'rotatorgright');
									$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
								}
							});
						}
					}
				});
			}
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
	
	var value = 0
	$(".rotatorgleft").rotate({
	bind:
	{
		click: function(){
			var cid = <?=$cid;?>;
			var filename = $(this).closest(" .jFiler-item-inner ").find(" img ").attr("src");
			var rotate = 'left';
			value -=90;
			$(this).closest(" .jFiler-item-assets ").find(" .textsuccess ").text('Processing');
			$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
			$(this).closest(".jFiler-item-assets").find(" .rotatorgleft ").attr('class', 'rotatorgleft_disable');
			$(this).closest(".jFiler-item-assets").find(" .rotatorgright ").attr('class', 'rotatorgright_disable');
			$(this).closest(".jFiler-item-assets").find(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
			$.post('img_controller.php', 'cid=' + cid + '&file=' + filename + '&rotate=' + rotate + '&act=2', function (response) {
				var jdata = JSON.parse(response);
				if (jdata.is_success == 1) {
					//Success
					setTimeout(function(){ 
						//successful;
						$(".rotatorgleft_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Success');
						$(".rotatorgleft_disable").attr('class', 'rotatorgleft');
						$(".rotatorgright_disable").attr('class', 'rotatorgright');
						$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
					}, 3000);
				} else {
					//Failed
					$(".rotatorgleft_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Failed');
					$(".rotatorgleft_disable").attr('class', 'rotatorgleft');
					$(".rotatorgright_disable").attr('class', 'rotatorgright');
					$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
				}
			});
		}
	}
	});
	$(".rotatorgright").rotate({
	bind:
	{
		click: function(){
			var cid = <?=$cid;?>;
			var filename = $(this).closest(" .jFiler-item-inner ").find(" img ").attr("src");
			var rotate = 'right';
			value +=90;
			$(this).closest(" .jFiler-item-assets ").find(" .textsuccess ").text('Processing');
			$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
			$(this).closest(".jFiler-item-assets").find(" .rotatorgleft ").attr('class', 'rotatorgleft_disable');
			$(this).closest(".jFiler-item-assets").find(" .rotatorgright ").attr('class', 'rotatorgright_disable');
			$(this).closest(".jFiler-item-assets").find(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
			$.post('img_controller.php', 'cid=' + cid + '&file=' + filename + '&rotate=' + rotate + '&act=2', function (response) {
				var jdata = JSON.parse(response);
				if (jdata.is_success == 1) {
					//Success
					setTimeout(function(){ 
						$(".rotatorgright_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Success');
						$(".rotatorgleft_disable").attr('class', 'rotatorgleft');
						$(".rotatorgright_disable").attr('class', 'rotatorgright');
						$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
					}, 3000);
				} else {
					//Failed
					$(".rotatorgright_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Failed');
					$(".rotatorgleft_disable").attr('class', 'rotatorgleft');
					$(".rotatorgright_disable").attr('class', 'rotatorgright');
					$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
				}
			});
		}
	}
	});
	
	
	//Video Gallery
	 /*$("#videos").filer({
        limit: 1,
        maxSize: 750,
        extensions: ['mov','flv','mp4','avi','3gp','MOV','FLV','MP4','AVI','3GP'],
        changeInput: '<div class="jFiler-input-dragDrop" style="border:none!important"><div class="jFiler-input-inner"><a class="jFiler-input-choose-btn blue">ADD VIDEO</a></div></div>',
        showThumbs: true,
        theme: "dragdropbox",
        templates: {
            box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
            item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb videogalleryview">\
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
                                    <div class="jFiler-item-thumb videogalleryview">\
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
            onProgress: function(){
				$('#alertbottom').show();
			},
            onComplete: function(){
				$('#alertbottom').hide();
			}
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
                filesType: "Only Videos are allowed to be uploaded.",
                filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                filesSizeAll: "Files you’ve chosen are too large! Please upload files up to {{fi-maxSize}} MB."
            }
        }
    });*/
});


</script>
<!--<script src="js/jquery.inputmask.js"></script>-->
<script src="../js/jquery.inputmask.bundle.js"></script>
<script src="../js/phone.js"></script>
<script>
$("[data-mask]").inputmask();
jQuery( document ).ready(function( $ ) {
  $("#rewards_amt1").inputmask();
  $("#rewards_amt2").inputmask();
  $("#rewards_amt3").inputmask();
  $("#rewards_amt4").inputmask();
});
</script>
</body>
</html>
<? include_once('bottom.php');?>