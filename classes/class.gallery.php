<?php

class GALLERY

{

	private $db;

	function __construct($DB_con)

	{

		$this->db = $DB_con;

	}

	public function gallery($sUtitle,$sUuniversityid,$filename)

	{

		try

		{

			$stmt = $this->db->prepare("INSERT INTO tbl_gallery(fld_name,university_id,fld_image,fld_status) 

		                                               VALUES(:Utitle, :Universityid, :image, 1)");

												  

			$stmt->bindparam(":Utitle", $sUtitle);
			
			$stmt->bindparam(":Universityid", $sUuniversityid);

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