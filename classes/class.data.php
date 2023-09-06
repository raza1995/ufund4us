<?php
class DATA
{
	private $db;
	function __construct($DB_con)
	{
		$this->db = $DB_con;
	}
	public function data($sDataCId,$sDataTitle,$sDataAddress,$sDataShortDescription,$sDataDescription,$sDataEmail,$DataPhone,$filename)
	{
		try
		{
			$stmt = $this->db->prepare("INSERT INTO   tbl_data(fld_category_id,fld_data_title,fld_data_address,fld_data_shortdesc,fld_data_desc,fld_data_email,fld_data_phonenum,fld_data_image,fld_data_status) VALUES( :dataId, :datatitle, :dataaddress, :datasdesc, :datadesc, :dataemail, :dataphone, :dataimage, 1)");
												  
			$stmt->bindparam(":dataId", $sDataCId);
			$stmt->bindparam(":datatitle", $sDataTitle);
			$stmt->bindparam(":dataaddress", $sDataAddress);
			$stmt->bindparam(":datasdesc", $sDataShortDescription);	
			$stmt->bindparam(":datadesc", $sDataDescription);
			$stmt->bindparam(":dataemail", $sDataEmail);
			$stmt->bindparam(":dataphone", $DataPhone);
			$stmt->bindparam(":dataimage", $filename);
			
			$stmt->execute();	
			
			return $stmt;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}				
	}
	public function is_loggedin()
	{
		if(isset($_SESSION['user_session']))
		{
			return true;
		}
	}
	public function redirect($url)
	{
		header("Location: $url");
	}
	
	public function logout()
	{
		session_destroy();
		unset($_SESSION['user_session']);
		return true;
	}
	
}
?>