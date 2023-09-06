<?php
class BANNER
{
	private $db;
	function __construct($DB_con)
	{
		$this->db = $DB_con;
	}
	public function banner($sBannerTitle,$sBannerSubTitle,$sBannershortDesc,$sBannerDescription,$sBannerone,$sBannertwo,$filename)
	{
		try
		{
			$stmt = $this->db->prepare("INSERT INTO tbl_banner(fld_banner_title,fld_banner_sub_title,fld_banner_shortdesc,fld_banner_description,fld_banner_one,fld_banner_two,fld_banner_image,fld_banner_status) 
		                                               VALUES( :bannertitle, :bannersubtitle, :bannershortdesc, :bannerdesc, :bone, :btwo,  :image, 1)");
												  
			$stmt->bindparam(":bannertitle", $sBannerTitle);
			$stmt->bindparam(":bannersubtitle", $sBannerSubTitle);
			$stmt->bindparam(":bannershortdesc", $sBannershortDesc);		
			$stmt->bindparam(":bannerdesc", $sBannerDescription);
			$stmt->bindparam(":bone", $sBannerone);
			$stmt->bindparam(":btwo", $sBannertwo);
			$stmt->bindparam(":image", $filename);	
			//$stmt->bindparam(":pageImage", $sPageImage);	
			//$stmt->bindparam(":pagebanner", $sPageBanner);										  
				
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