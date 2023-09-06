<?
require_once("functions_constants.php");
require_once("configuration/dbconfig.php");
$REQUEST = &$_REQUEST;

$check_address = $application_settings->getKey('ADDRESS_FOR_RECEIVING_CHECK');
// echo 'ret_data: <pre>'; print_r($ret_data); echo '</pre>';
$check_address = ($check_address == null) ? '' : $check_address;

//die(var_dump($REQUEST));
$cid = $REQUEST['cid'];
$pid = $REQUEST['pid'];

$txbamount = $REQUEST['txbamount'];
$txbrewardid = '';
if (isset($REQUEST['txbrewardid'])) {
    $txbrewardid = $REQUEST['txbrewardid'];
}
$hashid = $REQUEST['hashid'];
$is_SMS_Allow = 1;
$errortype = '';
//require('lib/AuthnetAIM.class.php');
/*define("AUTHORIZENET_API_LOGIN_ID", "5Wajf74Ba");
define("AUTHORIZENET_TRANSACTION_KEY", "5mpEEe88m3kA33VX");
define("AUTHORIZENET_SANDBOX", false);		
$payment = new AuthnetAIM(AUTHORIZENET_API_LOGIN_ID, AUTHORIZENET_TRANSACTION_KEY);*/

/*define("AUTHORIZENET_API_LOGIN_ID", "59kvZ4Se");
define("AUTHORIZENET_TRANSACTION_KEY", "8rA2a68eDJ4449ak");
define("AUTHORIZENET_SANDBOX", true);
$payment = new AuthnetAIM(AUTHORIZENET_API_LOGIN_ID, AUTHORIZENET_TRANSACTION_KEY, AUTHORIZENET_SANDBOX);*/
require('lib/init.php'); //Library for Stripe Merchant Account
if (array_key_exists('btnSubmit', $REQUEST)) {
    $Text_Messaging = $REQUEST['fld_text_messaging'];
    function ccMasking($number, $maskingCharacter = 'X')
    {
        return substr($number, 0, 4) . str_repeat($maskingCharacter, strlen($number) - 8) . substr($number, -4);
    }

    //Card Information
    $donateamount = str_replace(',', '', $REQUEST['donateamount']); //Amount Donated
    $amount = str_replace(".", "", $donateamount); //Amount Donate without point e.g 12.00 > 1200
    $checknumber = isset($REQUEST['checknumber']) ? $REQUEST['checknumber'] : '';
    $ccardnumber1 = str_replace(' ', '', $REQUEST['ccardnumber']); //Credit Card
    $ccardnumber = str_replace('_', '', $ccardnumber1); //Credit Card
    $cardtype = $REQUEST['cardtype']; //Credit Card Type e.g Visa, Mastercard
    //$cctype = $oregister->check_cc($ccardnumber, true);
    //$cctype = 'Visa';
    //$ccardnumbermasked = ccMasking($ccardnumber); //Masked Credit Card e.g xxxxxxxxxxxxxx0213
    $ccardnumbermasked = substr($ccardnumber, -4); //Last 4 Digits e.g. 1234
    /*$ccardexpdate = str_replace('-', '', $REQUEST['ccardexpdate']);
    $ccardexpdate2 = explode("-", $REQUEST['ccardexpdate']);
    $ccexp_month = $ccardexpdate2[0]; //Expiry Month e.g 08
    if ($ccexp_month == '10') {
        $exp_month = $ccexp_month; //Expiry Month without zero e.g 10 > 10
    } else {
        $exp_month = str_replace("0", "", $ccexp_month); //Expiry Month without zero e.g 09 > 9
    }
    $ccexp_year = $ccardexpdate2[1]; //Expiry Year e.g 18*/
    $ccardexpdatemm = $REQUEST['ccardexpdatemm'];
    if ($ccardexpdatemm == '10') {
        $exp_month = $ccardexpdatemm; //Expiry Month without zero e.g 10 > 10
    } else {
        $exp_month = str_replace("0", "", $ccardexpdatemm); //Expiry Month without zero e.g 09 > 9
    }
    $ccexp_year = $REQUEST['ccardexpdateyy']; //Expiry Year e.g 18

    $ccardcvv = $REQUEST['ccardcvv']; //Credit Card CVV or CVC e.g 222
    //End of Card Information
    //Card Address Information
    //$billingaddress1 = $REQUEST['billingaddress1']; //Billing Address Line 1
    //$billingaddress2 = $REQUEST['billingaddress2']; //Billing Address Line 2
    //$billingcity = $REQUEST['billingcity']; //Billing City
    //$billingstate = $REQUEST['billingstate']; //Billing State
    $billingpostalcode = $REQUEST['billingpostalcode']; //Billing Postal Code
    $billingcountry = $REQUEST['billingcountry']; //Billing Country
    //End of Card Address Information

    //Donor Information
    $hashid = $REQUEST['hashid']; //Donor ID
    $donorname = $REQUEST['donorname']; //Donor First Name
    $donorlname = $REQUEST['donorlname']; //Donor Last Name
    $donoremail = $REQUEST['donoremail']; //Donor Email
    $client_ip = $_SERVER['REMOTE_ADDR']; //Donor IP Address
    //End of Donor Information
    //Participant Information
    $participantid = $REQUEST['participantid']; //Participant ID
    $participantname = $REQUEST['participantname']; //Participant Full Name
    //End of Participant Information
    //Campaign Information
    $REQUEST['campid'] = isset($REQUEST['campid']) ? $REQUEST['campid'] : $cid;
    $campid = $REQUEST['campid']; //Campaign #
    $campname = $REQUEST['campname']; //Campaign Title
    //End of Campaign Information
    //Rewards
    if (array_key_exists('rewardid', $REQUEST)) {
        $rewardid = $REQUEST['rewardid'];
        $isreward = 1;
        $aRewardDetail = $oCampaign->get_rewards($campid, $rewardid);
        $reward_desc = $aRewardDetail['reward_desc'];
    } else {
        $rewardid = 0;
        $isreward = 0;
        $reward_desc = '';
    }
    //End of Rewards

    $displaylisted = 0;
    if (isset($REQUEST['displaylisted'])) {
        $displaylisted = 1;
    }
    $gateway_online = true;
    $transaction_id = '';
    $msgType = '';
    if ($gateway_online == true) {
        \Stripe\Stripe::setApiKey(STRIPE_API_KEY); //Initialize Stripe Gateway (Live)
        $current_date = date('m/d/Y h:i:s A'); //Current Date e.g 09/29/2016 01:20:00 AM
        //Card Information
        try {
            $aCampaignDetail = $oCampaign->getcampaigndetail($cid);
            $fld_accid = $aCampaignDetail['fld_ac'];
            $myCard = \Stripe\Token::create(array(
                "card" => array(
                    "name" => $donorname . ' ' . $donorlname,
                    "number" => $ccardnumber,
                    "exp_month" => $exp_month,
                    "exp_year" => $ccexp_year,
                    "cvc" => $ccardcvv
                )
            ));
            $create_token = $myCard->__toArray(true);
            $token = $create_token['id'];
            
            $app_fee_percentage = DEFAULT_APP_FEE;//20%
            if( isset($aCampaignDetail['app_fee_percentage']) ){
                $app_fee_percentage = $aCampaignDetail['app_fee_percentage'];
            }

            if($app_fee_percentage > 0){
                $app_fee_percentage = $app_fee_percentage/100;
                $application_fee = $amount * $app_fee_percentage;
            }
            else{
                $application_fee = $amount * DEFAULT_APP_FEE/100;
            }
            
            $application_fee = floor($application_fee);

            
            $charge = \Stripe\Charge::create(
                array(
                    'amount' => $amount,
                    'currency' => 'usd',
                    'source' => $token,
                    'description' => $donorname . ' ' . $donorlname . ' @ ' . $cid . ' (' . $participantname . ')',
                    'metadata' => array(
                        'Campaign #' => $cid,
                        'Campaign Title' => $campname,
                        'Customer ID' => $hashid,
                        'Customer Email' => $donoremail,
                        'Customer First Name' => $donorname,
                        'Customer Last Name' => $donorlname,
                        'Participant ID' => $pid,
                        'Participant First Name' => $participantname,
                        'Participant Last Name' => '',
                        'Amount' => '$ ' . $donateamount,
                        'Timestamp' => $current_date
                    ),
                    'application_fee' => $application_fee,
                    'destination' => $fld_accid
                )
            );

            $charge_array = $charge->__toArray(true);
            $card_transaction = $charge_array['id'];
            $funding = $charge_array['source']['funding']; //Credit/Debit
            $brand = $charge_array['source']['brand']; //Card Brand E.g. Visa/Mastercard
            $transaction_id = str_replace('ch_', '', $card_transaction); //Transaction # without card_
            $transactionid = $oCampaign->inserttransaction($transaction_id, $campid, $hashid, $participantid, $donorname, $donorlname, $donoremail, $ccardnumbermasked, $client_ip, $donateamount, $brand, $funding, $displaylisted, $rewardid, $isreward, $reward_desc);
            $oCampaign->donation_email($transactionid, $campid, $participantid, $donoremail, $donateamount, $donorname, $donorlname, $rewardid, $isreward, $reward_desc);

            $donor_details = $oCampaign->getdonordetails($cid, $pid, $hashid);
            $DonorPhone = $donor_details['uphone'];
            if ($is_SMS_Allow == 1 && $Text_Messaging == 1) {
                if ($DonorPhone != "" && $DonorPhone != "___-___-____") {
                    $donorfullname = trim($donorname . " " . $donorlname);
                    $body = "Hi " . $donorfullname . "\n";
                    $body .= "Thank you.\n";
                    $body .= "Your donation of $" . $donateamount . " to " . $campname . " has been received.\n";
                    //if(preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $DonorPhone)) {
                    $DonorPhone = str_replace(array("-", " "), array("", ""), $DonorPhone);
                    $body = utf8_encode($body);
                    if ($sent_sms = send_sms($DonorPhone, $body)) {
                        $sms_sent_id = $sent_sms['sid'];
                        $sms_date_created = $sent_sms['date_created'];
                        $tz = new DateTimeZone('America/Los_Angeles');
                        $sms_date = new DateTime($sms_date_created);
                        $sms_date->setTimezone($tz);
                        $sms_date_created = $sms_date->format('Y-m-d h:i:s');
                        if ($oCampaign->donationsmsupdate($transaction_id, $sms_sent_id, $sms_date_created)) {
                            $smsdonationinsert = $oCampaign->donationsmsinsert($cid, $hashid, $pid, $donorname, $donorlname, $donoremail, $DonorPhone, $participantname, $sms_sent_id, $sms_date_created);
                        }
                    }
                    //}
                }
            }
            $oregister->redirect('receipt.php?transaction=' . $transactionid . '');
        } catch (Stripe_CardError $e) {
            // Invalid card entered
            $errortype = 2;
            $body = $e->getJsonBody();
            $err = $body['error'];
            $got_error = $err['message'];
        } catch (Stripe_InvalidRequestError $e) {
            // Invalid parameters were supplied to Stripe's API
            $errortype = 3;
            $got_error = 'Sending invalid request on Stripe.';
        } catch (Stripe_AuthenticationError $e) {
            // Authentication with Stripe's API failed
            $errortype = 3;
            $got_error = 'Stripe API Authentication failed.';
        } catch (Stripe_ApiConnectionError $e) {
            // Network communication with Stripe failed
            $errortype = 3;
            $got_error = 'Stripe API Connection not established.';
        } catch (Stripe_Error $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            $errortype = 3;
            $body = $e->getJsonBody();
            $err = $body['error'];
            $got_error = $err['message'];
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
            $errortype = 3;
            $body = $e->getJsonBody();
            $err = $body['error'];
            $got_error = $err['message'];
        }

        //For Authorize Gateway
        /*$description = "Campaign #: $cid || Campaign Name: $campname || Donor Id: $hashid, Donor Name: $donorname $donorlname @ Participant Id: $pid, Participant Name: $participantname";
        $payment->setTransaction($ccardnumber, $ccardexpdate, $donateamount, $ccardcvv);
        $payment->setParameter("x_duplicate_window", 180);
        $payment->setParameter("x_cust_id", $hashid);
        $payment->setParameter("x_customer_ip", $_SERVER['REMOTE_ADDR']);
        $payment->setParameter("x_email", $donoremail);
        $payment->setParameter("x_email_customer", FALSE);
        $payment->setParameter("x_first_name", $donorname);
        $payment->setParameter("x_last_name", $donorlname);
        $payment->setParameter("x_description", $description);
        $payment->process();
        if ($payment->isApproved())
        {
            $transaction_id = $payment->getTransactionID();
            $transactionid = $oCampaign->inserttransaction($transaction_id, $campid, $hashid, $participantid, sanitize($donorname), sanitize($donorlname), $donoremail, $ccardnumbermasked, $ccardcvv, $client_ip, $donateamount, $cardtype, $displaylisted);
            $oCampaign->donation_email($campid, $participantid, $donoremail, $donateamount, $donorname, $donorlname);
            $oregister->redirect('receipt.php?transaction='.$transactionid.'');
        } else if ($payment->isDeclined()) {
            $errortype = 2;
        } else if ($payment->isError()) {
            $errortype = 3;
        }*/
    } else {
        //$transactionid = $oCampaign->inserttransaction($transaction_id, $campid, $hashid, $participantid, $donorname, $donorlname, $donoremail, $ccardnumbermasked, $ccardcvv, $client_ip, $donateamount, $cardtype, $displaylisted);
        //$oCampaign->donation_email($campid, $participantid, $donoremail, $donateamount, $donorname, $donorlname);
        //$oregister->redirect('receipt.php?transaction='.$transactionid.'');
        echo $donateamount;
    }
}


if ( $cid > 0 && $pid > 0) {
    $aCampaignDetail = $oCampaign->getcampaigndetail($cid);
    $fld_campaign_id = $aCampaignDetail['fld_campaign_id'];
    $fld_campaign_title = $aCampaignDetail['fld_campaign_title'];
    $fld_cname = $aCampaignDetail['fld_cname'];
    $fld_clname = $aCampaignDetail['fld_clname'];

    $fld_cemail = $aCampaignDetail['fld_cemail'];
    $fld_nonprofit_number = $aCampaignDetail['fld_nonprofit_number'];
    $fld_desc1 = $aCampaignDetail['fld_desc1'];
    $fld_desc2 = $aCampaignDetail['fld_desc2'];
    $fld_desc3 = $aCampaignDetail['fld_desc3'];
    $fld_team_size = $aCampaignDetail['fld_team_size'];
    $fld_donor_size = $aCampaignDetail['fld_donor_size'];
    $fld_organization_name = $aCampaignDetail['fld_organization_name'];
    $fld_hashcamp = $aCampaignDetail['fld_hashcamp'];

    $aParticipantDetail = $oregister->getuserdetail($pid);
    $participant_uid = checkSetInArrayAndReturn($aParticipantDetail,'fld_uid', '');
    $participant_fname = checkSetInArrayAndReturn($aParticipantDetail,'fld_name', '');
    $participant_lname = checkSetInArrayAndReturn($aParticipantDetail, 'fld_lname', '');
    $participant_phone = checkSetInArrayAndReturn($aParticipantDetail, 'fld_phone', '');
    $participant_email = checkSetInArrayAndReturn($aParticipantDetail, 'fld_email', '');
    if ($txbrewardid != '') {
        $aRewardDetail = $oCampaign->get_rewards($cid, $txbrewardid);
        $reward_desc = $aRewardDetail['reward_desc'];
    }

    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
            "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <!-- saved from url=(0031)http://ufund4us.com/contact-us/ -->
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>.:: <?= $fld_campaign_title; ?> - Ufund4Us ::.</title>
        <link rel="icon" type="image/png" sizes="16x16" href="cms/images/favicon.png">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <style>
            ul.list-unstyled {
                margin-bottom: 0 !important;
                text-align: center;
            }

            ul.list-unstyled li {
                width: 100% !important;
            }

            .nav-tabs > li {
                float: none;
                display: inline-block;
                zoom: 1;
            }

            .nav-tabs {
                text-align: center;
            }

            #hide_fields { display: none; }


        </style>
    </head>

    <body>
    <? include_once('header.php'); ?>
    <section class="ipcontentsection">
        <div class="contents">
            <div class="container">
                <!-- Top Heading -->
                <div class="top-heading-dark">
                    <h1><?= $participant_fname . " " . $participant_lname; ?></h1>
                </div>


                    <!-- Campaign Name -->
                    <div class="campaign-heading">
                        <h2><?= $fld_campaign_title; ?></h2>
                        <h4>Campaign # <?= str_pad($fld_campaign_id, 7, "0", STR_PAD_LEFT); ?></h4>

                        <? if ($txbrewardid != '') { ?>
                            <h4>Reward Description:<br><?= $reward_desc; ?></h4>
                        <? } ?>
                    </div>

                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#paymentviacard">Payment Via Card</a></li>
                        <li><a data-toggle="tab" href="#paymentviacheck">Payment via Check</a></li>
                    </ul>

                    <div class="tab-content">

                        <div id="paymentviacard" class="tab-pane fade in active">
                            <!-- Top Heading -->
                            <form id="donations" action="" data-toggle="validator" method="POST" role="form" class="white-box-donation  make-donation">
                                <div class="form-group">
                                    <? if ($txbamount != '') { ?>
                                        <div class="donation-price-box" style="padding:5px !important;width:180px !important">
                                            <span style="float:left">$</span>
                                            <? if ($txbrewardid != '') { ?>
                                                <input type="hidden" name="rewardid" id="rewardid"
                                                       value="<?= $txbrewardid; ?>"/>
                                                <input readonly style="width:140px !important;font-size:28px !important;"
                                                       class="form-control donateamount" name="donateamount"  id="donateamount"
                                                       value="<?= number_format($txbamount, 2, '.', ','); ?>" placeholder="0.00"
                                                       data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                            <? } else { ?>
                                                <input required data-error-message="Donating amount must be greater than $1.00"
                                                       style="width:140px !important;font-size:28px !important;"
                                                       class="form-control donateamount" name="donateamount" id="donateamount"
                                                       value="<?= number_format($txbamount, 2, '.', ','); ?>" placeholder="0.00"
                                                       data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                            <? } ?>
                                        </div>
                                        <div align="center" style="padding-top: 20px;">
                                <span class="donating"
                                      style="color:black; font-size:22px; "><b>You are donating</b></span>
                                        </div>
                                    <? } else { ?>
                                        <div class="donation-price-box" style="padding:5px !important;width:180px !important">
                                            <span style="float:left">$</span>
                                            <input style="width:140px !important;font-size:28px !important;"
                                                   class="form-control donateamount" name="donateamount" id="donateamount"
                                                   data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                        </div>
                                        <span class="donating" style="color:black; font-size:22px; display: block; text-align: center;"><b>Enter Your Donation Amount</b></span>
                                    <? } ?>

                                    <div class="form-group col-md-12 checkbox checkbox_add_fee_in_donation text-center"
                                         >
                                        <div class="checkboxbg">
                                            <input name="add_fee_in_donation" class="add_fee_in_donation" id="add_fee_in_donation1" type="checkbox">
                                            <label class="donationlabel" style="padding-left: 0;" for="add_fee_in_donation1">
                                                Include processing fee
                                            </label>
                                        </div>
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                            <!-- Top Heading -->

                                <div class="">
                                    <? if ($errortype == 3 || $errortype == 2) { ?>
                                        <div class="formcontainer col-md-8">
                                            <div class="alert alert-danger">
                                                <strong>Error!</strong> <?= $got_error; ?>
                                            </div>
                                        </div>
                                    <? } ?>
                                    <div class="formcontainer col-md-8">
                                        <h3 align="center" style="margin-bottom: 20px;">Cardholder Details</h3>
                                        <div class="clearfix"></div>
                                        <div class="form-group col-md-6">
                                            <label for="donorname">Cardholder First Name</label>
                                            <input type="text" class="form-control" id="donorname" name="donorname"
                                                   placeholder="Enter First Name" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="donorlname">Cardholder Last Name</label>
                                            <input type="text" class="form-control" id="donorlname" name="donorlname"
                                                   placeholder="Enter Last Name">
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group  col-md-5">
                                            <label for="ccardnumber">Credit Card Number</label>
                                            <input type="text" class="form-control" id="ccardnumber" name="ccardnumber"
                                                   data-inputmasking="'mask': ['9999 9999 9999 9999', '9999 9999 9999 9999']"
                                                   data-mask="" placeholder="XXXX XXXX XXXX XXXX" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                        <div class="form-group col-md-5">
                                            <!-- <input type="text" class="form-control" id="ccardexpdate" name="ccardexpdate" data-inputmask="'mask': ['99-99', '99-99']" data-mask="" placeholder="MM-YY" required>
                                            <div class="help-block with-errors"></div> -->
                                            <div class="form-group col-md-6">
                                                <label for="ccardexpdatemm">EXP MM</label>
                                                <select class="form-control" id="ccardexpdatemm" name="ccardexpdatemm"
                                                        required>
                                                    <option value="">MM</option>
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="ccardexpdateyy">EXP YYYY</label>
                                                <select class="form-control" id="ccardexpdateyy" name="ccardexpdateyy"
                                                        required>
                                                    <option value="">YYYY</option>
                                                    <?
                                                    $currentyear = date('Y');
                                                    $currentyear2 = date('y');
                                                    $yy = strtotime($currentyear);
                                                    $yy2 = strtotime('+15 years', $yy);
                                                    $finalfullyear = date('Y', $yy2);
                                                    $finalsortyear = date('y', $yy2);
                                                    $sna = 0;
                                                    for ($sny = $currentyear2; $sny <= $finalsortyear; $sny++) {
                                                        $yearsn1 = $currentyear + $sna;
                                                        $yearsn2 = $currentyear2 + $sna;
                                                        echo "<option value='" . $yearsn2 . "'>" . $yearsn1 . "</option>";
                                                        $sna++;
                                                    }
                                                    ?>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="ccardcvv">CVV</label>
                                            <input type="text" class="form-control" id="ccardcvv" name="ccardcvv"
                                                   data-inputmasking="'mask': ['999', '999']" data-mask="" placeholder="CVV"
                                                   required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group col-md-6">
                                            <label for="donoremail">Receipt Email Address</label>
                                            <input type="email" class="form-control" id="donoremail" name="donoremail"
                                                   placeholder="Enter Email" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <h3 align="center">Billing Details</h3>
                                        <div class="clearfix"></div>
                                        <!-- <div class="form-group col-md-6">
                          <label for="billingaddress">Address Line 1</label>
                          <input type="text" class="form-control" id="billingaddress1" name="billingaddress1" placeholder="Enter Address Line 1" required>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="billingaddress">Address Line 2</label>
                          <input type="text" class="form-control" id="billingaddress2" name="billingaddress2" placeholder="Enter Address Line 2">
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group col-md-6">
                          <label for="billingcity">City</label>
                          <input type="text" class="form-control" id="billingcity" name="billingcity" placeholder="Enter City" required>
                          <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="billingstate">State</label>
                          <?
                                        $sSData = $oregister->getstate('231');
                                        $iSRecords = count($sSData);
                                        ?>
                          <select class="form-control" id="billingstate" name="billingstate" required>
                              <option value="" selected>Select state</option>
                              <?
                                        for ($s = 0; $s < $iSRecords; $s++) {
                                            ?>
                              <option value="<?= $sSData[$s]['name'] ?>"><?= $sSData[$s]['name'] ?></option>
                              <?
                                        }
                                        ?>
                          </select>
                        </div>
                        <div class="clearfix"></div> -->
                                        <div class="form-group col-md-6">
                                            <label for="billingpostalcode">Postal Code</label>
                                            <input type="text" class="form-control" id="billingpostalcode"
                                                   name="billingpostalcode" placeholder="Enter Postal Code" pattern="\d*"
                                                   required data-error="Number digits allowed only e.g 90001">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="billingcountry">Country</label>
                                            <select class="form-control" id="billingcountry" name="billingcountry">
                                                <option value="">Select Country</option>
                                                <option value="AU">Australia</option>
                                                <option value="AT">Austria</option>
                                                <option value="BE">Belgium</option>
                                                <option value="BR">Brazil</option>
                                                <option value="CA">Canada</option>
                                                <option value="DK">Denmark</option>
                                                <option value="FI">Finland</option>
                                                <option value="FR">France</option>
                                                <option value="DE">Germany</option>
                                                <option value="HK">Hong Kong</option>
                                                <option value="IE">Ireland</option>
                                                <option value="IT">Italy</option>
                                                <option value="JP">Japan</option>
                                                <option value="LU">Luxembourg</option>
                                                <option value="MX">Mexico</option>
                                                <option value="NL">Netherlands</option>
                                                <option value="NZ">New Zealand</option>
                                                <option value="NO">Norway</option>
                                                <option value="PT">Portugal</option>
                                                <option value="SG">Singapore</option>
                                                <option value="ES">Spain</option>
                                                <option value="SE">Sweden</option>
                                                <option value="CH">Switzerland</option>
                                                <option value="UK">United Kingdom</option>
                                                <option value="US" selected>United States</option>
                                            </select>
                                        </div>
                                        <div class="clearfix"></div>
                                        <input type="hidden" class="form-control" id="participantid" name="participantid"
                                               value="<?= $pid; ?>">
                                        <input type="hidden" class="form-control" id="participantname"
                                               name="participantname"
                                               value="<?= $participant_fname . " " . $participant_lname; ?>">
                                        <input type="hidden" class="form-control" id="campid" name="campid"
                                               value="<?= $cid; ?>">
                                        <input type="hidden" class="form-control" id="hashid" name="hashid"
                                               value="<?= $hashid; ?>">
                                        <input type="hidden" class="form-control" id="campname" name="campname"
                                               value="<?= $fld_campaign_title; ?>">
                                        <input type="hidden" class="form-control" id="cardtype" name="cardtype">

                                        <input type="hidden" class="form-control" id="fld_text_messaging"
                                               name="fld_text_messaging" value="<?= $REQUEST['fld_text_messaging']; ?>">
                                        <input type="hidden" class="form-control" id="cid" name="cid"
                                               value="<?= $REQUEST['cid']; ?>">
                                        <input type="hidden" class="form-control" id="pid" name="pid"
                                               value="<?= $REQUEST['pid']; ?>">
                                        <input type="hidden" class="form-control" id="txbamount" name="txbamount"
                                               value="<?= $REQUEST['txbamount']; ?>">
                                        <input type="hidden" class="form-control"  name="hashid"
                                               value="<?= $REQUEST['hashid']; ?>">
                                        <div class="form-group col-md-12 checkbox">
                                            <div class="checkboxbg">
                                                <input name="displaylisted" id="displaylisted" type="checkbox">
                                            </div>
                                            <label class="donationlabel" for="displaylisted">
                                                Display my donation to everyone who views this page.
                                            </label>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group col-md-6 col-xs-6 text-left">
                                            <button type="reset" class="cancel btn btn-lg">CANCEL</button>
                                        </div>
                                        <div class="form-group col-md-6 col-xs-6 text-right">
                                            <input type="hidden" name="btnSubmit" id="btnSubmit"></input>
                                            <button type="submit" name="btnSubmit1" id="btnSubmit1"
                                                    class="donation btn btn-lg">DONATE
                                            </button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    <!-- make-donation -->
                        <div id="paymentviacheck" class="tab-pane fade">
                            <!-- Top Heading -->

                            <form id="donations_via_check" action="" data-toggle="validator" method="POST" role="form" class="white-box-donation  make-donation print_form">
                                <input type="hidden" name="action" value="track_check_payment">
                                <h1 class="showOnTheprintview">
                                    <?= $fld_campaign_title; ?>
                                </h1>
                                <div class="form-group">
                                    <? if ($txbamount != '') { ?>
                                            <div class="donation-price-box hide-in-print" style="padding:5px !important;width:180px !important; display: block; text-align: center;">
                                                <span id="dolarsign" style=" display: inline-block; text-align: center;" >$</span>
                                                <? if ($txbrewardid != '') { ?>
                                                    <input type="hidden" name="rewardid" 
                                                           value="<?= $txbrewardid; ?>"/>
                                                    <input readonly style="width:140px !important;font-size:28px !important; display: block; text-align: center;"
                                                           class="form-control donation_price" name="donateamount" 
                                                           value="<?= number_format($txbamount, 2, '.', ','); ?>" placeholder="0.00"
                                                           data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">

                                                <? } else { ?>
                                                    <input required data-error-message="Donating amount must be greater than $1.00"
                                                           style="width:140px !important;font-size:28px !important; display: block; text-align: center;"
                                                           class="form-control donation_price donateamount" name="donateamount" 
                                                           value="<?= number_format($txbamount, 2, '.', ','); ?>" placeholder="0.00"
                                                           data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                                <? } ?>
                                            </div>
                                        <div align="center" style="padding-top: 20px;">
                                    <span class="donating"
                                          style="color:black; font-size:22px; "><b>You are donating</b></span>
                                        </div>
                                    <? } else { ?>
                                        <div class="donation-price-box" style="padding:5px !important;width:180px !important">
                                            <span style="float:left">$</span>
                                            <input style="width:140px !important;font-size:28px !important;"
                                                   class="form-control donateamount" name="donateamount"  
                                                   data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                        </div>
                                        <span class="donating" style="color:black; font-size:22px; display: block; text-align: center;"><b>Enter Your Donation Amount</b></span>
                                    <? } ?>

                                    <div class="form-group col-md-12 checkbox checkbox_add_fee_in_donation text-center"
                                         >
                                        <div class="checkboxbg">
                                            <input name="add_fee_in_donation" class="add_fee_in_donation"  type="checkbox">
                                            <label class="donationlabel" style="padding-left: 0;" for="add_fee_in_donation2">
                                                Add 3% to your donation to cover credit card processing fee
                                            </label>
                                        </div>
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                                <!-- Top Heading -->
                                <!-- Top Heading -->
                                <div class="">
                                    <? if ($errortype == 3 || $errortype == 2) { ?>
                                        <div class="formcontainer col-md-8">
                                            <div class="alert alert-danger">
                                                <strong>Error!</strong> <?= $got_error; ?>
                                            </div>
                                        </div>
                                    <? } ?>
                                    <div class="formcontainer col-md-8">
                                        <h3 align="center" class="font">Check Details</h3>
                                        <div class="clearfix"></div>
                                        <div class="form-group col-md-6">
                                            <label for="donor_first_name">Donor First Name</label>
                                            <input type="text" class="form-control"  name="donor_first_name"
                                                   placeholder="Enter First Name" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="donor_last_name">Donor Last Name</label>
                                            <input type="text" class="form-control"  name="donor_last_name"
                                                   placeholder="Enter Last Name">
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group  col-md-6">
                                            <label for="check_number">Check Number</label>
                                            <input type="text" class="form-control check_number_field"  name="check_number"
                                                   placeholder="Enter check number" required>
                                            <div class="help-block with-errors"></div>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="donor_email">Bank Name</label>
                                            <input type="text" class="form-control bank_name_field"  name="bank_name"
                                                   placeholder="Bank name" required>
                                            <div class="help-block with-errors"></div>
                                        </div>

                                        <div class="clearfix"></div>
                                        <div class="form-group col-md-6">
                                            <label for="donor_email">Donor Email Address</label>
                                            <input type="email" class="form-control email_address_field"  name="donor_email"
                                                   placeholder="Enter Email" required>
                                            <div class="help-block with-errors"></div>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="donor_phone_number">Donor Phone</label>
                                            <input type="text" class="form-control email_donor_phone"  name="donor_phone_number" data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="___-___-____" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <?php /*
                                        <h3 align="center" class="font">Billing Details2</h3>
                                        <div class="clearfix"></div>
                                        <div class="form-group col-md-6">
                                            <label for="postal_code">Postal Code</label>
                                            <input type="text" class="form-control" 
                                                   name="postal_code" placeholder="Enter Postal Code" pattern="\d*"
                                                   required data-error="Number digits allowed only e.g 90001">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="country">Country</label>
                                            <select class="form-control"  name="country">
                                                <option value="">Select Country</option>
                                                <option value="AU">Australia</option>
                                                <option value="AT">Austria</option>
                                                <option value="BE">Belgium</option>
                                                <option value="BR">Brazil</option>
                                                <option value="CA">Canada</option>
                                                <option value="DK">Denmark</option>
                                                <option value="FI">Finland</option>
                                                <option value="FR">France</option>
                                                <option value="DE">Germany</option>
                                                <option value="HK">Hong Kong</option>
                                                <option value="IE">Ireland</option>
                                                <option value="IT">Italy</option>
                                                <option value="JP">Japan</option>
                                                <option value="LU">Luxembourg</option>
                                                <option value="MX">Mexico</option>
                                                <option value="NL">Netherlands</option>
                                                <option value="NZ">New Zealand</option>
                                                <option value="NO">Norway</option>
                                                <option value="PT">Portugal</option>
                                                <option value="SG">Singapore</option>
                                                <option value="ES">Spain</option>
                                                <option value="SE">Sweden</option>
                                                <option value="CH">Switzerland</option>
                                                <option value="UK">United Kingdom</option>
                                                <option value="US" selected>United States</option>
                                            </select>
                                        </div>
                                        <div class="clearfix"></div>
                                        */
                                        ?>
                                        

                                        <div id="hide_fields">

                                            <div class="clearfix"></div>
                                            <h3 align="center" class="font">Participant Details</h3>
                                            <div class="clearfix"></div>

                                            <div>
                                                <label for="participant_id">Participant Id</label>
                                                <input type="text" class="form-control"  name="participant_id"
                                                       value="<?= $pid; ?>">
                                                <div class="help-block with-errors"></div>
                                            </div>

                                            <div>
                                                <label for="participant_name">Participant Name</label>
                                                <input type="text" class="form-control" 
                                                       name="participant_name"
                                                       value="<?= $participant_fname . " " . $participant_lname; ?>">
                                                <div class="help-block with-errors"></div>
                                            </div>

                                            <div>
                                                <label for="campaign_id">Campaign Id</label>
                                                <input type="text" class="form-control"  name="campaign_id"
                                                       value="<?= $cid; ?>">
                                                <div class="help-block with-errors"></div>
                                            </div>

                                            <div>
                                                <label for="campaign_name">Campaign Name</label>
                                                    <input type="text" class="form-control"  name="campaign_name"
                                                           value="<?= $fld_campaign_title; ?>">
                                                    <div class="help-block with-errors"></div>
                                            </div>

                                        </div>

                                        <div class="form-group col-md-12 donation_page_note1">
                                            <strong>Note: <span style="color: red;">Must include this receipt with check, and send to below address</span></strong>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="form-group col-md-12 donation_page_note1">
                                            <strong>Address: </strong> <?php echo $check_address;?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="clearfix"></div>
                                        <div class="form-group col-md-4 col-xs-4 text-left">
                                            <button type="reset" class="cancel btn btn-lg" id="print_cancel">CANCEL</button>
                                        </div>
                                        <div class="form-group col-md-8 col-xs-8 text-right">
                                            <button type="button" name="btnSubmit1" id="print_check"
                                                    class="importStyle donation btn btn-lg">SAVE & PRINT MAILING INSTRUCTIONS
                                            </button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </form>
                        <!-- make-donation -->
                        </div>
                    </div>

                <!-- container -->
            </div>
            <!-- contents -->
        </div>

    </section>

    <? include_once('footer.php'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.js"></script>

    <style>
        .showOnTheprintview {
            display: none;
            text-align: center;
            color: #000;
            font-family: serif !important;
        }
        @media print {
            @page { margin: 0; }
            body { margin: 1.6cm; }

            .showOnTheprintview {
                display: block;
                font-family: serif !important;
            }

            label{
                display: inline-block;
                float: left;
                margin-left: 10px;
                padding: 0;
            }

            input {
                display: inline-block;
                border:none;
                margin-left: 57%;
                padding: 0;
                text-align: right !important;
            }
           
            select {
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                border: none; /* If you want to remove the border as well */
                background: none;
                margin-left: 60%;
            }
            .check_number_field {
                margin-left:60% !important;
            }
            .bank_name_field {
                margin-left:63% !important;
            }
            .email_address_field {
                margin-left:53.5% !important;
            }
            .email_donor_phone {
                margin-left:61.5% !important;
            }
            #hide_fields {
                display: block !important;
                text-align: right;
            }
            #hide_fields input {margin-right: 0%!important;}
            #dolarsign{
                font-size: 28px;
                float: none;
                display: inline-block;
                position: absolute;
                margin-left: 215px;

            }

            .donation_price{
                display: inline-block;

                margin-left: 280px;
            }

            #showin{
                margin-bottom: -5px;
            }

            .font{
                text-transform: uppercase;
            }

            button { display: none !important; }

            #check_amount {
                display: inline-block;
                text-align: center;
            }
            .donationlabel {
                display: inline;
            }
            .donation_page_note1 {
                /*text-align: center;*/
                margin-top: 15px;
                margin-left: 150px;
            }
            .checkboxbg {
                float: none!important;
                text-align: center;
                padding:0!important;
                margin-top: 15px;
            }
            .donationlabel {
                float: none!important;
                text-align: center;
                display: inline-block;
                padding:0!important;
                margin-left: 0!important;

            }
            .add_fee_in_donation {
                margin-left: 0!important;
            }
        }



        .container li.active a {
            background-color: #fcb514 !important;
            font-weight: bold;
        }


        .container li a {
            border-color: #FCB514 #fcb514 transparent #fcb514 !important;
            font-weight: bold;

        }

    </style>

    <script>

        $(document).on('click','#print_check', function(){
            /* get the action attribute from the <form action=""> element */
            var formData = $('#donations_via_check').serialize()
            $.ajax({
                type: 'post',
                url: 'checkPayments.php',
                data: formData,
                success: function (data) {
                  console.log('form was submitted, ', data);
                }
            });

            $('.print_form').printThis({
                importCSS: false,
                loadCSS: "",
                importStyle: $(this).hasClass('importStyle'),
            });
        });
    </script>
    <script type="text/javascript" src="js/accounting.js"></script>
    <script>
        function creditCardTypeFromNumber(num) {
            num = num.replace(/[^\d]/g, '');
            if (num.match(/^5[1-5]\d{3}$/)) {
                return 'MasterCard';
            } else if (num.match(/^4\d{3}/)) {
                return 'Visa';
            } else if (num.match(/^3[47]\d{1}/)) {
                return 'AmEx';
            } else if (num.match(/^6011\d{4}/)) {
                return 'Discover';
            }
            return 'UNKNOWN';
        }

        $(document).on('keyup', '#ccardnumber', function () {
            var cardtype = creditCardTypeFromNumber($(this).val());
            $("#cardtype").val(cardtype);
            if (cardtype == 'AmEx') {
                $("#ccardnumber").inputmasking({"mask": "9999 9999 9999 999"}); //specifying options
                $("#ccardcvv").inputmasking({"mask": "9999"}); //specifying options
            } else {
                $("#ccardnumber").inputmasking({"mask": "9999 9999 9999 9999"}); //specifying options
                $("#ccardcvv").inputmasking({"mask": "999"}); //specifying options
            }
            $('#donations').validator();
        });

        function addCommas(x, txtname) {
            var mval = accounting.formatMoney(x);
            mval = mval.replace('$', '');
            document.getElementById(txtname).value = mval;
        }
    </script>
    <script src="css/owl.carousel/owl.carousel.min.js"></script>
    <script src="css/owl.carousel/owl.custom.js"></script>
    <!--<script src="cms/js/mask.js"></script>-->
    <script src="js/jquery.inputmask.js"></script>
    <script>
        //must include inputmask.js on page for masking
        function reInitMaskingForAllInputFields(){
            $("#ccardnumber").inputmasking();
            $("#donateamount").inputmask();
            // $("[data-mask]").inputmask("999-999-9999");
            $("input[name=donor_phone_number]").inputmask();
        }

    </script>
    <script src="cms/js/validator.js"></script>
    <!-- <script src="js/jquery.maskMoney.js" type="text/javascript"></script> -->
    <script src="js/jquery.inputmask.bundle.js"></script>
    <script src="js/phone.js"></script>
    <script>
        //Start__{__Update donation amount when click on check box of include processing fee, just bellow the donation amount field
        var donationAmount = [];
        donationAmount['tab-paymentviacheck'] = '';
        donationAmount['tab-paymentviacard'] = '';
        function setDonationAmountAcToActiveTab() {
            donationAmount['tab-paymentviacheck'] = $('#paymentviacheck').find("input[name=donateamount]").val();
            donationAmount['tab-paymentviacard'] = $('#paymentviacard').find("input[name=donateamount]").val();
        }

        function getDonationAmountAcToActiveTab() {
            var amountInDonateAmountFieldOnPageLoad = 0;
            if( $('#paymentviacheck').hasClass('active') ){
                return donationAmount['tab-paymentviacheck']
            }
            else{ 
                return donationAmount['tab-paymentviacard']
            }
        }

        jQuery(document).ready(function ($) {
            
            reInitMaskingForAllInputFields()

            //on page ready
            setDonationAmountAcToActiveTab();

            $('.add_fee_in_donation').on('click', function (e) {
                var donationAmount = getDonationAmountAcToActiveTab();
                if(donationAmount == ''){
                    console.log('revert click on checkbox');
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                console.log('donationAmount=',donationAmount);
                if ( $(this).is(':checked') ) {
                    donationAmount = parseFloat(donationAmount);
                    var amounthreePercentOfDonation = donationAmount * (3 / 100);
                    donationAmount = donationAmount + amounthreePercentOfDonation;
                    donationAmount = donationAmount.toFixed(2)
                }
                // console.log('is checked-'+$(this).is(':checked')+', donationAmount='+donationAmount);

                $(this).parents('form').find(".donateamount").val(donationAmount);
            });

            $('.donateamount').on('keyup', function () {
                setDonationAmountAcToActiveTab();
                $(".add_fee_in_donation").prop("checked", false);
            });
            //End__}__Update donation amount when click on check box of include processing fee, just bellow the donation amount field

        });

        /*$('#donations').validator().on('submit', function (e) {
              if (e.isDefaultPrevented()) {
            $('#btnSubmit').prop('disabled', false);
          } else {
            $('#btnSubmit').click();
            $('#btnSubmit').prop('disabled', true);
          }
        });*/
        $('#donations').validator().on('submit', function (e) {
            if (e.isDefaultPrevented()) {
                $('#btnSubmit1').prop('disabled', false);
            } else {
                $('#btnSubmit1').prop('disabled', true);
            }
        });
        $('#billingcountry').on('change', function () {
            //alert( this.value ); // or $(this).val()
            $iCountryId = this.value;
            $.ajax({
                url: "cms/showstates.php?countryid=" + $iCountryId, success: function (result) {
                    $("#billingstate").empty();
                    $("#billingstate").html(result);
                }
            });
        });
    </script>
    <script src="bars/bars.js"></script>
    </body>
    </html>
<? } else {
    if (isset($REQUEST['cid']) && isset($REQUEST['pid']) && isset($REQUEST['hashid'])) {
        $cid = $REQUEST['cid'];
        $pid = $REQUEST['pid'];
        $hashid = $REQUEST['hashid'];
        $oregister->redirect('campaign.php?cid=' . $hashid . '|' . $cid . '|' . $pid . '');
    } else {
        $oregister->redirect('index.php');
    }
} ?>