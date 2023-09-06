<?
require_once("configuration/dbconfig.php");
require_once("login_check.php");

$REQUEST = &$_REQUEST;
checkSetInArrayAndReturn($REQUEST, 'action', '');

if($REQUEST['action'] == 'track_check_payment'){
	$data = [];
	$data['cur_state'] = 'pending';
	$data['created_at'] = date('Y-m-d h:i:s');
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'donateamount', 0);
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'campaign_id', 0);
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'campaign_name', '');
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'bank_name', '');
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'check_number', '');
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'donor_first_name', '');
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'donor_last_name', '');
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'donor_email', '');
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'donor_phone_number', '');
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'postal_code', '');
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'country', '');
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'participant_id', 0);
	get_set_real_escape_string_from_req($mysqliCon, $data, $REQUEST, 'participant_name', '');
	$data['updated_at'] = $data['created_at'];
	
	// echo 'data: <pre>'; print_r([$REQUEST, $data]); //die();


	$insertRes = $check_payments->insert($data);
	// echo 'insertRes='; echo var_dump($insertRes);
	
	if( $insertRes === true ){
		echo "Tracked!";
	}
	else if( is_string($insertRes) ){
		echo "Got error, <br/>".$insertRes; die();
	}else{
		echo "Unable to track!";
	}
}
?>