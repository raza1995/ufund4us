<?php
class BLOG
{
	private $db;
	function __construct($DB_con)
	{
		$this->db = $DB_con;
	}
	public function blog($sBlogTitle,$sBlogShortDescription,$sBlogDescription,$sBlogAddDate,$filename,$sBlogPopular)
	{
		try
		{
			$stmt = $this->db->prepare("INSERT INTO tbl_blog(fld_blog_title,fld_blog_short_description,fld_blog_description,fld_blog_add_date,fld_blog_image,fld_blog_popular,fld_blog_status) 
		                                               VALUES( :blogtitle, :blogsdesc, :blogdesc, :blogadddate, :blogimage, :blogpopular, 1)");
												  
			$stmt->bindparam(":blogtitle", $sBlogTitle);
			$stmt->bindparam(":blogsdesc", $sBlogShortDescription);
			$stmt->bindparam(":blogdesc", $sBlogDescription);	
			$stmt->bindparam(":blogadddate", $sBlogAddDate);
			$stmt->bindparam(":blogimage", $filename);
			$stmt->bindparam(":blogpopular", $sBlogPopular);
				
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