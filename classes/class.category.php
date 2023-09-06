<?php
class CATEGORY
{
	private $db;
	function __construct($DB_con)
	{
		$this->db = $DB_con;
	}
	public function category($sCategoryPID,$sCategoryTitle,$sCategoryURL,$sCategoryShortDescription,$sCategoryDescription,$filename,$sCategoryMtitle,$sCategoryMKeyword,$sCategoryMDesc)
	{
		try
		{
			$stmt = $this->db->prepare("INSERT INTO  tbl_category(fld_category_pid,fld_category_title,fld_category_url,fld_category_shortdesc,fld_category_description,fld_category_image,fld_category_metatitle,fld_category_metakeyword,fld_category_metadesc,fld_category_status) 
		                                               VALUES( :categoryPID, :categorytitle, :categoryurl, :categorysdesc, :categorydesc, :categoryimage, :categorymetatitle, :categorymetakeyword, :categorymetadesc, 1)");
												  
			$stmt->bindparam(":categoryPID", $sCategoryPID);
			$stmt->bindparam(":categorytitle", $sCategoryTitle);
			$stmt->bindparam(":categoryurl", $sCategoryURL);
			$stmt->bindparam(":categorysdesc", $sCategoryShortDescription);	
			$stmt->bindparam(":categorydesc", $sCategoryDescription);
			$stmt->bindparam(":categoryimage", $filename);
			$stmt->bindparam(":categorymetatitle", $sCategoryMtitle);
			$stmt->bindparam(":categorymetakeyword", $sCategoryMKeyword);
			$stmt->bindparam(":categorymetadesc", $sCategoryMDesc);
			
				
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