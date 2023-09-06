<?
require_once("../configuration/dbconfig.php");
$REQUEST = &$_REQUEST;
$cid = checkAndReturnOnly($REQUEST, 'cid');
$act = checkAndReturnOnly($REQUEST, 'act');

if ($act == 1) { //Campaign,Dist,Rep,Admin Payment History
  $iCountRecords3 = 0;
  $paymentdetails = $oCampaign->payment_history($cid);
  $iCountRecords3 = count($paymentdetails);
  $array_record = null;
  
  if($iCountRecords3>0){
  	  for($l=0;$l<$iCountRecords3;$l++){
		    $array_record = $paymentdetails;
	    }
  }
  $result['array_record'] = $array_record;
  $result['counter'] = $iCountRecords3;
  echo json_encode($result);
}
?>