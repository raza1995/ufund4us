<?php
class TESTIMONIAL
{
	private $db;
	function __construct($DB_con)
	{
		$this->db = $DB_con;
	}
	public function testimonial($sTestimonialTitle,$sTestimonialShortDesc,$sTestimonialDescription,$sTestimonialAddDate,$filename,$sTestimonialFeatured)
	{
		try
		{
			$stmt = $this->db->prepare("INSERT INTO tbl_testimonial(fld_testimonial_title,fld_testimonial_shortdesc,fld_testimonial_description,fld_testimonial_add_date,fld_testimonial_image,fld_testimonial_featured,fld_testimonial_status) VALUES( :testimonialtitle, :testimonialshortdesc, :testimonialdesc, :testimonialadddate, :testimonialimage, :testimonialfeatured, 1)");
												  
			$stmt->bindparam(":testimonialtitle", $sTestimonialTitle);
			$stmt->bindparam(":testimonialshortdesc", $sTestimonialShortDesc);
			$stmt->bindparam(":testimonialdesc", $sTestimonialDescription);	
			$stmt->bindparam(":testimonialadddate", $sTestimonialAddDate);
			$stmt->bindparam(":testimonialimage", $filename);
			$stmt->bindparam(":testimonialfeatured", $sTestimonialFeatured);
				
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