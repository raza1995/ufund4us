<?php
include(dirname(__FILE__)."/../../../constants.php");
include("../mpdf.php");

$header = '
<table width="100%" border="0">
  <tbody>
    <tr>
      <td width="45%" align="left"><img src="../../emails/logo.png" width="35%" height="15%" /></td>
      <td width="10%">&nbsp;</td>
      <td width="45%" align="right"><img src="../../emails/logo.png" width="35%" height="15%" /></td>
    </tr>
  </tbody>
</table>';
$html = '<h2 align="center" style="font-family:arial"><b><u><i>Instructions to Join "Campaign Name"</i></u></b></h2>';
$html .= '<h2 align="center" style="font-family:arial; font-size:18px"><b>Campaign starts "Date" and ends "date"</b></h2>';
$html .= '<div align="left" style="font-family:arial; margin-top:40px; font-size:16px"><b>Step by step instructions to become a participant:</b></div>';
$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:50px; font-size:16px">Log onto '.SITE_DOMAIN.'</div>';
$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:50px; font-size:16px">Select Join Campaign</div>';
$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:100px; font-size:16px">Enter campaign # ___________________ & campaign ID # ___________________</div>';
$html .= '<div align="left" style="font-family:arial; margin-top:40px; font-size:16px"><b>Create and account (follow the on screen instructions):</b></div>';
$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:50px; font-size:16px">After creating your acount, you will be prompted to enter your email and password again. This will take you to edit your profile.</div>';
$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:100px; font-size:16px">If you already have an account login as shown on the page.</div>';
$html .= '<div align="left" style="font-family:arial; margin-top:40px; font-size:16px"><b>Enter your profile:</b></div>';
$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:50px; font-size:16px">Upload your profile picture (this will be used to personalize your campaign)</div>';
$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:100px; font-size:16px"><b><u>Save and continue</u></b></div>';
$html .= '<div align="left" style="font-family:arial; margin-top:40px; font-size:16px"><b>Enter your donors:</b></div>';
$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:50px; font-size:16px">You are required to enter ____ potential donor email addresses.</div>';
$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:50px; font-size:16px">You can log back in at any time to enter additional donors.</div>';
$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:100px; font-size:16px">Select <b><u>Update</u></b> to save your donors.</div>';
$html .= '<div align="left" style="font-family:arial; margin-top:15px; margin-left:100px; font-size:16px">Select <b><u>Preview</u></b> to see your campaign letter.</div>';
$html .= '<div align="left" style="font-family:arial; margin-top:40px; margin-left:150px; font-size:20px"><b><i><u>Thank you for joining "Campaign Name"</u></i></b></div>';
$html .= '<div align="left" style="font-family:arial; margin-top:40px; margin-left:150px; font-size:26px"><b><i>"If you fail to plan, you are planning to fail."</i></b></div>';
$html .= '<div align="left" style="font-family:new times roman; margin-top:-8px; margin-left:530px; font-size:18px"><i>by Benjamin Franklin</i></div>';
$html .= '<div align="left" style="font-family:new times roman; margin-top:5px; margin-left:200px; font-size:18px">Sums up why 99% of all fundraising efforts fail to reach their goals.</div>';


$mpdf=new mPDF('c','A4','','',10,10,40,10,10,5);
$mpdf->SetHTMLHeader($header);
$mpdf->WriteHTML($html);
/*$mpdf->Output(SITE_ROOT . 'files/pdf-generation/customs-cover-letter-'.$datetime.'.pdf', 'F');
$attachedfile = ''.HTTP_SERVER.'files/pdf-generation/customs-cover-letter-'.$datetime.'.pdf';
$filename = 'customs-cover-letter-'.$datetime.'.pdf';
$path = 'files/pdf-generation/';*/

$mpdf->Output();
?>