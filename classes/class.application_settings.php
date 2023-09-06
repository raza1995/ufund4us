<?php
class application_settings
{
	private $db;
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
			$sql = "SELECT * FROM application_settings";
			
			$whereAry = array();
			if( isset($wData['key']) ){
				$whereAry[] = " `key` = '".$wData['key']."'";
			}

			if( count($whereAry) > 0){
				$whereStr = implode(' AND ', $whereAry);
				$sql = $sql." where ".$whereStr;
			}
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

	
	function getKey($key, $valueOrRow='value'){	
		$value = null;
		$ret_data = $this->get(['key'=>$key]);
		if($ret_data['count'] > 0){
			if($valueOrRow == 'row'){
				$value = $ret_data['rows'][0];
			}
			else{
				$value = $ret_data['rows'][0]['value'];
			}
		}
		return $value;
	}

	function update($key, $value){
		try {			
			$sql = "UPDATE application_settings SET `value` = '".$value."' WHERE `key` = '".$key."'";
			// echo $sql; die();
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}
	
}//end class
?>