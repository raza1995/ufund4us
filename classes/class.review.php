<?php

class REVIEW

{

	private $db;

	function __construct($DB_con)

	{

		$this->db = $DB_con;

	}

	public function review($sUtitle,$sUuniversityid,$sUdesc)

	{

		try

		{

			$stmt = $this->db->prepare("INSERT INTO tbl_review(fld_name,fld_desc,university_id,fld_status) 

		                                               VALUES(:Utitle, :Udesc, :Uuniversity_id, 1)");

												  

			$stmt->bindparam(":Utitle", $sUtitle);
			
			$stmt->bindparam(":Udesc", $sUdesc);
			
			$stmt->bindparam(":Uuniversity_id", $sUuniversityid);
			
		

		


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