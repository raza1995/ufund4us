<?php
class NEWS
{
	private $db;
	function __construct($DB_con)
	{
		$this->db = $DB_con;
	}
	public function news($sNewsTitle,$sNewsShortDescription,$sNewsDescription,$sNewsAddDate,$filename,$sNewsPopular)
	{
		try
		{
			$stmt = $this->db->prepare("INSERT INTO tbl_news(fld_news_title,fld_news_short_description,fld_news_description,fld_news_add_date,fld_news_image,fld_news_latest,fld_news_status) 
		                                               VALUES( :newstitle, :newshortdesc, :newsdesc, :newsadddate, :newsimage, :newspopular, 1)");
												  
			$stmt->bindparam(":newstitle", $sNewsTitle);
			$stmt->bindparam(":newshortdesc", $sNewsShortDescription);
			$stmt->bindparam(":newsdesc", $sNewsDescription);	
			$stmt->bindparam(":newsadddate", $sNewsAddDate);
			$stmt->bindparam(":newsimage", $filename);
			$stmt->bindparam(":newspopular", $sNewsPopular);
				
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