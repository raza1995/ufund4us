<?php
class PAGE
{
	private $db;
	function __construct($DB_con)
	{
		$this->db = $DB_con;
	}
	public function page($sMetaTitle,$sMetaKeyword,$sMetaDescription,$sShortDesc,$sPageTitle,$sPageUrl,$sPageDescription,$sPageparent,$filename,$sLocation)
	{
		try
		{
			$stmt = $this->db->prepare("INSERT INTO tbl_pages(fld_meta_title,fld_meta_keywords,fld_meta_description,fld_shortdescription,fld_page_title,fld_page_url,fld_paged_description,fld_parent_id,profile_pic,fld_location,fld_page_status) 
		                                               VALUES(:metitle, :mekeyword, :medesc, :srtdesc, :pagetitle, :pageurl, :pagedescription, :location, :Fparent, :image, 1)");
												  
			$stmt->bindparam(":metitle", $sMetaTitle);
			$stmt->bindparam(":mekeyword", $sMetaKeyword);
			$stmt->bindparam(":medesc", $sMetaDescription);
			$stmt->bindparam(":srtdesc", $sShortDesc);	
			$stmt->bindparam(":pagetitle", $sPageTitle);	
			$stmt->bindparam(":pageurl", $sPageUrl);	
			$stmt->bindparam(":Fparent", $sPageparent);	
			$stmt->bindparam(":pagedescription", $sPageDescription);
			$stmt->bindparam(":location", $sLocation);
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