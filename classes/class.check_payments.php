<?php
define('CHECK_PAYMENT_STATUS_ARY', ['pending', 'received', 'deposited', 'settled', 'refunded']);

class check_payments
{
	private $db;
	private $tableName = "check_payments";
	function __construct($DB_con)
	{
		$this->db = $DB_con;
	}
	
	function get($wData = array() ){
		$ret_data = [];
		$ret_data['count'] = 0;
		$ret_data['msg'] = '';
		$ret_data['rows'] = [];

		try{
			$sql = "SELECT 
					  tcp.*,
					  tc.fld_cname AS campaign_name2
					FROM
					  check_payments AS tcp 
					  INNER JOIN tbl_campaign AS tc 
					    ON tc.fld_campaign_id = tcp.campaign_id 
					";
			
			$whereAry = array();
			if( isset($wData['cp_id']) ){
				$whereAry[] = " tcp.cp_id = ".$wData['cp_id'];
			}

			if( isset($wData['search']) && $wData['search'] != ''){
				$searchParts = "(";
				$searchParts .= " tcp.donation_id LIKE '%".$wData['search']."%' OR";
				$searchParts .= " tcp.donor_first_name LIKE '%".$wData['search']."%' OR";
				$searchParts .= " tcp.donor_last_name LIKE '%".$wData['search']."%' OR";
				$searchParts .= " tcp.donor_email LIKE '%".$wData['search']."%' OR";
				$searchParts .= " tcp.donor_phone_number LIKE '%".$wData['search']."%' OR";
				$searchParts .= " tcp.participant_id LIKE '%".$wData['search']."%' OR";
				$searchParts .= " tcp.participant_id LIKE '%".$wData['search']."%' OR";
				$searchParts .= " tcp.donateamount LIKE '%".$wData['search']."%' OR";
				$searchParts .= " tcp.participant_name LIKE '%".$wData['search']."%' OR";
				$searchParts .= " tcp.cur_state LIKE '%".$wData['search']."%'";
				$searchParts .= ")";

				$whereAry[] = $searchParts;
			}

			if( count($whereAry) > 0){
				$whereStr = implode(' AND ', $whereAry);
				$sql = $sql." where ".$whereStr;
			}

			$sql = $sql." ORDER BY cp_id DESC";

			// echo 'wData: <pre>'; print_r($wData); die();
			// die($sql);
			$stmt = $this->db->prepare($sql);
			$stmt->execute();

			$ret_data['count'] = $stmt->rowCount();
			$ret_data['msg'] = '';			
			if( $ret_data['count'] > 0){
				// $signle_row =$stmt->fetch(PDO::FETCH_ASSOC);//for fetcing only single row
				$ret_data['rows'] = $stmt->fetchall(PDO::FETCH_ASSOC);//for fetching all rows
			}


		} catch (PDOException $e) {
			$ret_data['msg'] = $e->getMessage();
		}
		// echo 'all_rows: <pre>'; print_r($all_rows); die();
		return $ret_data;
	}//end fn

	function getFinalData($data){
		$finalData = [];
		foreach ($data as $key => $value) {
			if( is_string($value) ){
				$finalData[] = $key."='".$value."'";
			}
			else if( is_int($value) || is_float($value) ){
				$finalData[] = $key."=".$value;
			}
		}
		return $finalData;
	}

	function insert($data){
		$finalData = $this->getFinalData($data);
		try {			
			$sql = "INSERT INTO ".$this->tableName." SET ".implode(", ", $finalData);
			// echo "sql =".$sql;
			$stmt = $this->db->prepare($sql);
			return $stmt->execute();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	function getLastInserted(){
		return $this->db->lastInsertId();
	}
	

	function update($cp_id, $data){
		$finalData = $this->getFinalData($data);
		try {			
			$sql = "UPDATE ".$this->tableName." SET ".implode(", ", $finalData)." WHERE cp_id=".$cp_id;
			// echo $sql; die();
			$stmt = $this->db->prepare($sql);
			return $stmt->execute();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function delete($cp_id) {
		try {
			$stmt = $this->db->prepare("DELETE from ".$this->tableName." WHERE cp_id =".$cp_id);
			return $stmt->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function deleteDonation($id) {
		try {
			$stmt = $this->db->prepare("DELETE from tbl_donations WHERE id =".$id);
			return $stmt->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//When check is settled, then get details of check, perform transection, keep transection id in check payment row, in case we want to reverse it, then delete the row of transections
	function settelCheckPayment($cp_id){

	}


	function mapFields(&$fData, &$old_cp_data){
  	  $fData['cp_id'] = $old_cp_data['cp_id'];
  	  $fData['card_number'] = $old_cp_data['check_number'];
  	  $fData['bank_name'] = isset($old_cp_data['bank_name']) ? $old_cp_data['bank_name'] : '';
      $fData['donation_amount'] = $old_cp_data['donateamount'];
      $fData['cid'] = $old_cp_data['campaign_id'];
      $fData['pid'] = $old_cp_data['participant_id'];
      $fData['refferal_by'] = $old_cp_data['participant_id'];
      $fData['participant_name'] = $old_cp_data['participant_name'];
      $fData['cmfname'] = $old_cp_data['donor_first_name'];
      $fData['cmlname'] = $old_cp_data['donor_last_name'];
      $fData['uemail'] = $old_cp_data['donor_email'];
      $fData['uphone'] = $old_cp_data['donor_phone_number'];
      $fData['cur_state'] = isset($old_cp_data['cur_state']) ? $old_cp_data['cur_state'] : '';
		
      checkSetInArrayAndReturn($fData, 'tid', 'check');
      checkSetInArrayAndReturn($fData, 'uid', $_SESSION['uid']);
      checkSetInArrayAndReturn($fData, 'ufname', $_SESSION['uname']);
      checkSetInArrayAndReturn($fData, 'ulname', $_SESSION['ulname']);
	  checkSetInArrayAndReturn($fData, 'payment_through', 'check');
	  checkSetInArrayAndReturn($fData, 'sms_sent_date', date('Y-m-d H:i:s'));
	  checkSetInArrayAndReturn($fData, 'email_sent_date', date('Y-m-d H:i:s'));
	  checkSetInArrayAndReturn($fData, 'client_ip', '0');
	  checkSetInArrayAndReturn($fData, 'reward_id', '0');
	  checkSetInArrayAndReturn($fData, 'reward_desc', '0');
	  checkSetInArrayAndReturn($fData, 'creationdate', date('Y-m-d H:i:s'));
      
	}

	function reMapFields(&$fData, &$old_cp_data){
  	  $old_cp_data['cp_id'] = $fData['cp_id'] ;
  	  $old_cp_data['check_number'] = $fData['card_number'];
      $old_cp_data['bank_name'] = isset($fData['bank_name']) ? $fData['bank_name'] : '';
      $old_cp_data['donateamount'] = $fData['donation_amount'];
      $old_cp_data['campaign_id'] = $fData['cid'];
      $old_cp_data['participant_id'] = $fData['pid'];
      // $old_cp_data['participant_name'] = isset($fData['participant_name']) ? $fData['participant_name'] : '';
      $old_cp_data['donor_first_name'] = $fData['cmfname'];
      $old_cp_data['donor_last_name'] = $fData['cmlname'];
      $old_cp_data['donor_email'] = $fData['uemail'];
      $old_cp_data['donor_phone_number'] = $fData['uphone'];
      $old_cp_data['cur_state'] = isset($fData['cur_state']) ? $fData['cur_state'] : '';
	}

	function checkAndSetBasicDataOfCP(&$old_cp_data){
		//for add case or case in which no data found for id
          if( !isset($old_cp_data['cp_id']) ){
              $old_cp_data['cp_id'] = '';
              $old_cp_data['cur_state'] = '';
              $old_cp_data['check_number'] = '';
              $old_cp_data['bank_name'] = '';
              $old_cp_data['donateamount'] = '';
              $old_cp_data['campaign_id'] = '';
              $old_cp_data['campaign_name'] = '';
              $old_cp_data['participant_id'] = '';
              $old_cp_data['participant_name'] = '';
              $old_cp_data['donor_first_name'] = '';
              $old_cp_data['donor_last_name'] = '';
              $old_cp_data['donor_email'] = '';
              $old_cp_data['donor_phone_number'] = '';
              $old_cp_data['donateamount'] = '';
          }
	}
	
}//end class
?>