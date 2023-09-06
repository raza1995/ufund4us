<?php
date_default_timezone_set('America/Los_Angeles'); //TimeZone
include("../../php/dbconn.php");
include_once('../../classes/class.phpmailer.php');

//Email Setting
$to = TEST_EMAIL_1;
$mail = new phpmailer;
$mail->CharSet = 'UTF-8';
$mail->Mailer  = 'mail';
if ($cc != '') {
	$mail->addCC($cc);
}
$mail->AddReplyTo(TEST_EMAIL_1, TEST_NAEM_OF_EMAIL_1);
$mail->SetFrom(TEST_EMAIL_1, TEST_NAEM_OF_EMAIL_1);
$mail->isHTML(true);
$mail->Subject = sWEBSITENAME.' Donor Letter';
$mail->AddAddress(trim($to));
//Email Setting

$mail->Body = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <style>
        body{
            font-family:Arial;
            margin:0px;
            background-color: #f1f1f1;
        }
        table{
            background-color:#fff;
        }
        table{border-collapse:collapse;}
        .emailButton{background-color:#205478; border-collapse:separate; border-radius: 35px}
        .buttonContent{color:#FFFFFF; font-family:Helvetica; font-size:18px; font-weight:bold; line-height:100%; padding:15px; text-align:center;}
        .buttonContent a{color:#FFFFFF; display:block; text-decoration:none!important; border:0!important;}
    </style>
</head>
<body bgcolor="#E1E1E1" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
  
<div align="center">    
    <table width="700" border="0" style="margin:0 auto;"  cellspacing="0"  cellpadding="0">
      <tbody>
        <tr>
          <td width="280" rowspan="4"><div style="border-right:10px solid #f6bb22;border-bottom:10px solid #f6bb22;"><img src="'.SITE_FULL_URL.'app/cms/emails/newletter/images/left_img.jpg" width="300" height="280"  /></div></td>
          <td width="35">&nbsp;</td>
          <td colspan="2"><div align="center" style="margin-top: 12px;margin-bottom: 12px;"><img src="'.SITE_FULL_URL.'app/cms/emails/newletter/images/baseball.jpg"  width="150"/></div></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td width="49"><img src="'.SITE_FULL_URL.'app/cms/emails/newletter/images/user.jpg" width="40" /></td>
          <td width="308"><h3 style="margin:0px;">PARTICIPANT NAME</h3><h2 style="margin:0px;">KURT GAIRING</h2></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><img src="'.SITE_FULL_URL.'app/cms/emails/newletter/images/clock.jpg"  width="40" /></td>
          <td><h3 style="margin:0px;">TIME LEFT:</h3><h2 style="margin:0px;color:red;">343 DAYS, 7 HOURS</h2></td>
        </tr>
      </tbody>
    </table>
    
    
    <table border="0"  width="700" style="margin:0 auto;"  cellspacing="0"  cellpadding="0">
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0"  cellspacing="0" >
                    <tr>
                        <td>
                            <div style="margin:0px 0px 0px 50px;color: #000;">
                                <h3>
                                    <br />
                                    <h3 style="font-size:18px;">Hi, Saadat Ansari</h3>
                                    <br />
                                </div>
    
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
    
        </tr>
    </table>
    <table border="0"  width="700" style="margin:0 auto;" cellpadding="0"  cellspacing="0" >
        <tr>
            <td>
                <div style="margin:0px 50px 20px 50px;">
                    <p style="font-size:18px;font-weight:normal;">Centennial Baseball and I need your help this year!</p>
                    <h3 style="font-size:18px;">Please click the “SUPPORT NOW!” and see how your donation will help <strong>Centennial Baseball.</strong></h3>
                </div>
            </td>
        </tr>
    </table>
    
    <table border="0"  width="700" style="margin:0 auto;" cellpadding="0">
        <tr>
            <td style="    text-align: center;">
                <table border="0" cellpadding="0" cellspacing="0" class="emailButton" style="margin:0 auto;background-color:#205478; border-collapse:separate; border-radius: 35px; background-color: #FCB514;">
                    <tr>
                        <td align="center" valign="middle" class="buttonContent" style="color:#FFFFFF;padding-top:15px;padding-bottom:15px;padding-right:30px;padding-left:30px;">
                            <a style="color:#000;font-size:18px;border-radius:100px; line-height:135%;" href="'.SITE_URL.'app/campaign.php?cid={{Campaign_HashKey}}|{{Campaign_Id}}|{{ParticipantId}}&hashid={{DonorId}}" target="_blank">SUPPORT NOW!</a>
                        </td>
                    </tr>
                </table>
    
    
        </tr>
        <tr>
            <td>
                <div style="margin:20px 50px 20px 50px; font-size:18px;">
                    <p>Thank you for your support.</p>
                    <p>Kurt Gairing</p>
    
                </div>
            </td>
        </tr>
    </table>
    <table border="0"  width="700" style="margin:0 auto;" cellpadding="5">
        <tr>
            <td style="text-align:center;font-size: 10px;border-top: 2px solid #f6bb22;padding-top: 5px;">
                <h1 style="margin:0px;margin-top:3px;margin-bottom:3px; font-size: 15px;">3 EASY STEPS TO CREATE YOUR OWN <span style="color:#f1aa00;">DONATION-BASED</span> FUNDRAISING CAMPAIGN</h1>
            </td>
        </tr>
    </table>
    <table border="0"  width="700" style="margin:0 auto;" cellpadding="0">
        <tr>
            <td style="text-align:center;background-color:#f6bb22;" width="33%">
                <a href="#" style="text-decoration:none;">
                    <div style="padding-top:10px;">
                        <img src="'.SITE_FULL_URL.'app/cms/emails/newletter/images/circle-01.png" width="20" alt="" />
                        <h2  style="margin-top:10px;margin-bottom:10px;color: #fff;font-size: 11px;">START YOUR CAMPAIGN NOW</h2>
                    </div>
                </a>
            </td>
            <td style="text-align:center;background-color:#efae1e;" width="34%">
                <a href="#" style="text-decoration:none;">
                    <div style="padding-top:10px;">
                        <img src="'.SITE_FULL_URL.'app/cms/emails/newletter/images/circle-02.png" width="20" alt="" />
                        <h2  style="margin-top:10px;margin-bottom:10px;color: #fff;font-size: 11px;">SET YOUR FUNDRAISING GOALS</h2>
                    </div>
                </a>
            </td>
            <td style="text-align:center;background-color:#e59f24;" width="33%">
                <a href="#" style="text-decoration:none;">
                    <div style="padding-top:10px;">
                        <img src="'.SITE_FULL_URL.'app/cms/emails/newletter/images/circle-03.png" width="20" alt=""/>
                        <h2  style="margin-top:10px;margin-bottom:10px;color: #fff;font-size: 11px;">TRACK AND MANAGE YOUR RESULTS</h2>
                    </div>
                </a>
            </td>
        </tr>
    </table>
    <table border="0"  width="700" cellpadding="0" style="background:#3d3732;color:#fff;margin:0 auto;">
        <tr>
            <td>
                <p style="margin-left: 20px; font-size:12px;">Copyright © <?php echo COPY_RIGHT_YEAR;?> | <a href="#" style="color:#f6bb22;text-decoration:none;">'.sWEBSITENAME.'</a>. All rights reserved.  |  Powered by <a href="#" style="color:#f6bb22;text-decoration:none;">Lyja</a></p>
            </td>
            <td style="text-align:right;padding-right: 20px;">
                <img src="'.SITE_FULL_URL.'app/cms/emails/newletter/images/logo_footer.png" width="120" alt="" />
            </td>
        </tr>
    </table>
    <table border="0"  width="700" cellpadding="0" style="margin:0 auto; text-align:left; background:none !important;">
        <tr>
            <td>
                <p style="font-size:12px;margin:10px 20px;">Disclaimer: <small style="font-size:12px;">Kurt Gairing provided your email address to us. If you no longer wish to not receive emails sent on behalf of Kurt Gairing, <a href="#" style="color:red;"> please click here</a></small></p>
            </td>
        </tr>
    </table>


</div>
</body>
</html>';
if ($mail->Send()) {
	echo "Success: Email has been sent...!";
} else {
	echo "Failed: Email has not been sent...!";
}
?>