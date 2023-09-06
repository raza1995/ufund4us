<?php

class UNIVERSITY

{

	private $db;

	function __construct($DB_con)

	{

		$this->db = $DB_con;

	}

	public function university($sUtitle,$sUphone,$sUaddress,$sUemail,$sUcontact,$sUdesc,$filename)

	{

		try

		{

			$stmt = $this->db->prepare("INSERT INTO tbl_university(fld_name,fld_phone,fld_address,fld_email,fld_contact_person,fld_desc,fld_image,fld_status) 

		                                               VALUES(:Utitle, :Uphone, :Uaddress, :Uemail, :Ucontact, :Udesc, :image, 1)");

												  

			$stmt->bindparam(":Utitle", $sUtitle);

			$stmt->bindparam(":Uphone", $sUphone);

			$stmt->bindparam(":Uaddress", $sUaddress);

			$stmt->bindparam(":Uemail", $sUemail);	
			
			$stmt->bindparam(":Ucontact", $sUcontact);

			$stmt->bindparam(":Udesc", $sUdesc);

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