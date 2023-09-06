<?php 
$REQUEST = &$_REQUEST;
checkSetInArrayAndReturn($REQUEST, 'action', '');
get_set_real_escape_string_from_req($mysqliCon, $REQUEST, $REQUEST, 'cp_id', 0);
get_set_real_escape_string_from_req($mysqliCon, $REQUEST, $REQUEST, 'cur_state', '');
	  
	  // echo '<pre>'; print_r($_SESSION); die();
function getActionsHTML($value){
  $tempActionsAry = [];
  
  if($value['cur_state'] == 'settled'){

  }
  else{
      $tempActionsAry[] = '<a data-cp_id="'.$value['cp_id'].'" href="'.sHOMECMS.'check_payment.php?action=edit_check_payment&cp_id='.$value['cp_id'].'">Edit</a>';
  }

  // $tempActionsAry[] = '<a href="'.sHOMECMS.'check_payment.php?action=delete_check_payment&cp_id='.$value['cp_id'].'">Delete</a>';

  //Don't show refund link, now its included in dropdown of status change
  if( false && $value['cur_state'] != "refunded"){
    $tempActionsAry[] = '<a class="refund_href" data-cp_id="'.$value['cp_id'].'" href="'.sHOMECMS.'check_payment.php?action=refund_check_payment&cur_state=refunded&cp_id='.$value['cp_id'].'">Refund</a>';
  }
  $tempActionsHTML = implode(' | ', $tempActionsAry);
  return $tempActionsHTML;
}//getActionsHTML//

function getCheckPaymentStatusWithSelectedDropDown($curStatus, $cp_id){
	//Since we are allowing to refund settled payment, so we don't need of it
  // if($curStatus == 'settled'){
	// 	return ucfirst($curStatus);
	// }

	$statusDropDown = '<select data-cp_id="'.$cp_id.'" class="check_payment_status_dropdown">';
	foreach(CHECK_PAYMENT_STATUS_ARY as $value){
		$statusDropDown .=	'<option value="'.$value.'" '.($curStatus == $value ? 'selected' : '').'>'.ucfirst($value).'</option>';
	}
	$statusDropDown .= '</select>';
	return $statusDropDown;
}

/**
$old_cp_data: Before update check payment data, 
$REQUEST: url request object , 
$check_payments: check payment class object, 
$oregister: class object
*/
function ifHasActionOfSettled($old_cp_data, $REQUEST , $check_payments, $oregister){
    // echo __line__."- ifHasActionOfSettled - <pre>"; var_dump(['old_cp_data'=>$old_cp_data]); die();
  	$donation_id = 0;
    $fData = [];
    $check_payments->mapFields($fData, $old_cp_data);
    $donation_id = 0;
    $refund_id = 0;

    if( isset($old_cp_data["donation_id"]) && 
        is_numeric($old_cp_data["donation_id"]) && 
        $old_cp_data["donation_id"] > 0
    ){
      $donation_id = $old_cp_data["donation_id"];
    }

    if( isset($old_cp_data["refund_id"]) && 
        is_numeric($old_cp_data["refund_id"]) && 
        $old_cp_data["refund_id"] > 0
    ){
      $refund_id = $old_cp_data["refund_id"];
    }

    if(  $REQUEST['cur_state'] == 'settled' || 
        ($REQUEST['cur_state'] == 'refunded' && $donation_id > 0) ||
        ($donation_id > 0 || $refund_id > 0)
    ){
	    $REQUEST['refund_id'] = $refund_id;
      $REQUEST['donation_id'] = $donation_id;
      
      $donation_resp = $oregister->insert_or_update_admin_donation($fData, $REQUEST);
      if( $donation_resp['update_check_payment'] == true ){
        //update check payment row
        $check_payments->update($old_cp_data['cp_id'], array('donation_id'=> $donation_resp['donation_id'], 'refund_id'=> $donation_resp['refund_id']) ); 
      }
	  }
}//End of functions


if($REQUEST['action'] == 'delete_check_payment'){
  	//first get row
    if($REQUEST['cp_id'] > 0){
		$old_cp_id_data = $check_payments->get( ['cp_id'=>$REQUEST['cp_id']] );
		// echo 'old_cp_id_data<pre>'; print_r($old_cp_id_data); die();
		if($old_cp_id_data['count'] == 1){
			$old_cp_id_data = $old_cp_id_data['rows'][0];
			//firt check has transection? , if yes then remove it first then delete checck payment
			if($old_cp_id_data['donation_id'] > 0){
				$check_payments->deleteDonation($old_cp_id_data['donation_id']);
			}
			$check_payments->delete($old_cp_id_data['cp_id']);
		}
	}
}



if(
  $REQUEST['action'] == 'update_check_payment_status' ||
  $REQUEST['action'] == 'refund_check_payment'
){
		// echo '<pre>'; print_r($REQUEST); die();
	  
	  $updateResp = 'Incorrect request data!';

	  if($REQUEST['cp_id'] > 0 && $REQUEST['cur_state'] != ''){
	  	$old_cp_id_data = $check_payments->get( ['cp_id'=>$REQUEST['cp_id']] );
	  	// echo 'old_cp_id_data<pre>'; print_r($old_cp_id_data); die();
	  	if($old_cp_id_data['count'] == 1){
		  	$updateResp = $check_payments->update($REQUEST['cp_id'], array('cur_state'=>$REQUEST['cur_state']));
		}

	  	//When check is settled, then get details of check, perform transection, keep transection id in check payment row, in case we want to reverse it, then delete the row of transections
	  	if( $old_cp_id_data['rows'][0]['cur_state'] != $REQUEST['cur_state'] ){ 
          if( ($REQUEST['cur_state'] == 'settled' || $REQUEST['cur_state'] == 'refunded') 
              || ($old_cp_id_data['rows'][0]["donation_id"] > 0 || $old_cp_id_data['rows'][0]["refund_id"] > 0)
          ){
             ifHasActionOfSettled($old_cp_id_data['rows'][0], $REQUEST, $check_payments, $oregister);
          }

      }
	  }

    //Prepare response data
    $ret = [];
    $ret["success"] = false;
    $ret["actions_html"] = "";
	  if( is_bool($updateResp) ){
	  		$ret["msg"] = $updateResp === true ? 'Updated' : 'Unable to update';
        $ret["success"] = ($updateResp === true);

        $new_cp_row = $check_payments->get( ['cp_id'=>$REQUEST['cp_id']] );
        // echo '<pre>'; print_r($new_cp_row); die();        
        $ret["actions_html"] = getActionsHTML($new_cp_row['rows'][0]);
	  }
	  else if( is_string($updateResp) ){
	  	$ret["msg"] = $updateResp;
	  }
	  else{
	  	$ret["msg"] = 'Please try again later!';
	  }
	  echo json_encode($ret); die();
}


if($REQUEST['action'] == 'get_listing'){
  $wData = [];
  $search = '';
  if( isset($REQUEST['search']) && isset($REQUEST['search']['value']) ){
  	$search = $REQUEST['search']['value'];
  }

  if($search != ''){
  	$wData['search'] = $search;
  }
  $listing = $check_payments->get($wData);

  $output = array(
    "draw" => 2,
    "recordsTotal" => $listing['count'],
    "recordsFiltered" => $listing['count'],
    "data" => array()
  );

  
  // echo 'listing: <pre>'; print_r($listing); die();
  foreach ($listing['rows'] as $key => $value) {
    $row = array();
    $tempAction = '<td id="actions_td_'.$value['cp_id'].'">';
      $tempAction .=  getActionsHTML($value);
    $tempAction .= '</td>';
    $row[] = $tempAction;
    $row[] = '<td>'.getCheckPaymentStatusWithSelectedDropDown($value['cur_state'], $value['cp_id']).'</td>';
    $row[] = '<td>'.$value['created_at'].'</td>';
    $row[] = '<td>'.$value['donateamount'].'</td>';
    $row[] = '<td>'.$value['campaign_id'].'</td>';
    $row[] = '<td>'.$value['campaign_name'].'</td>';
    $row[] = '<td>'.$value['check_number'].'</td>';
    $row[] = '<td>'.checkSetInArrayAndReturn($value,'bank_name','').'</td>';
    $row[] = '<td>'.$value['donor_first_name'].'</td>';
    $row[] = '<td>'.$value['donor_last_name'].'</td>';
    $row[] = '<td>'.$value['donor_email'].'</td>';
    $row[] = '<td>'.$value['donor_phone_number'].'</td>';
    $row[] = '<td>'.$value['postal_code'].'</td>';
    $row[] = '<td>'.$value['country'].'</td>';
    $row[] = '<td>'.$value['participant_id'].'</td>';
    $row[] = '<td>'.$value['participant_name'].'</td>';
    $output['data'][] = $row;
    // break;
  }
  $output['recordsFiltered']=  count($output['data']);
  // echo '<pre>'; print_r($output); die();
  echo json_encode( $output );
  die();
}



$sPageName = '<li><a href="check_payment.php">Check Payment</a></li>';
$sUsersLink = 'style=""';
$sLeftMenuCheckPayment = 'active';

$campaign = $oregister->getallcampaign();
$campRecords = count($campaign);

//edit_check_payment / 
//When Check payment form submitted insert/update actions
if(isset($REQUEST['admin_donation'])){
  $old_cp_data = [];
  $check_payments->reMapFields($REQUEST, $old_cp_data);
  if($old_cp_data['cp_id'] > 0){
		//update
    	$old_cp_data['updated_at'] = date('Y-m-d h:i:s');
		$cp_id = $old_cp_data['cp_id'];
		unset($old_cp_data['cp_id']);
    	$check_payments->update($cp_id, $old_cp_data);
		$old_cp_data['cp_id'] = $cp_id;
	}
  else{
		//insert
		unset($old_cp_data['cp_id']);
		$old_cp_data['cur_state'] = 'pending';
		$old_cp_data['created_at'] = date('Y-m-d h:i:s');
		$old_cp_data['updated_at'] = date('Y-m-d h:i:s');
		$insertRes = $check_payments->insert($old_cp_data);
		if( $insertRes === true ){
			$old_cp_data['cp_id'] = getLastInserted();
		}
	}

	$old_cp_data = $check_payments->get( ['cp_id'=>$REQUEST['cp_id']] );
  if($old_cp_data['count'] == 1){
    $old_cp_data = $old_cp_data['rows'][0]; 
  }
  //in  case of not found data, set default value
  $check_payments->checkAndSetBasicDataOfCP($old_cp_data);
  // echo '155-<pre>'; print_r($REQUEST); die();
  ifHasActionOfSettled($old_cp_data, $REQUEST, $check_payments, $oregister);

  $oregister->redirect('check_payment.php?msg=3'); exit();
}//end of admin_donation


if($REQUEST['action'] == "get_camp_details" && isset($REQUEST['id']) ){
    $id = $REQUEST['id'];
    $participant = $oregister->getallparticipant($id);
    $partRecords = count($participant);
    header('Content-type: application/json');
    echo json_encode(['success' => true, 'record' => $participant]);
exit();
}

if($REQUEST['action'] == "get_pid_details" && isset($REQUEST['pid'])){
    $id = $REQUEST['pid'];
    $participant = $oregister->getuserdetail2($id);
    $partRecords = count($participant);
    header('Content-type: application/json');
    echo json_encode(['success' => true, 'record' => $participant]);
exit();
}


// die(print_r($campRecords)) ;


// die(print_r($Campaign));
?>