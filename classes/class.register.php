<?php
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use SparkPost\SparkPost;

class REGISTER {
	private $db;
	function __construct($DB_con) {
		$this->db = $DB_con;
	}
	public function generatepasshash($length) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	public function generatepin($length) {
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	public function encrypt($string, $key) {
		$result = '';
		for ($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result .= $char;
		}
		return base64_encode($result);
	}
	public function decrypt($string, $key) {
		$result = '';
		$string = base64_decode($string);
		for ($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			$result .= $char;
		}
		return $result;
	}
	public function register($sName, $sLName, $sEmail, $sPassword, $iRoleId, $ref) {
		try
		{
			if ($ref != '') {
				$repid = '';
				$repfname = '';
				$replname = '';
				$distid = '';
				$distfname = '';
				$distlname = '';
				$adminid = '';
				$adminfname = '';
				$adminlname = '';
				$comm_val = '';
				
				$stmt6 = $this->db->prepare("SELECT a.uid, a.uname, a.ulname, a.groleid, b.fld_role_id FROM tbl_generated_link a
				INNER JOIN tbl_users b ON a.uid = b.fld_uid
				WHERE a.gkeyhash = '$ref' AND a.isused = 0 AND a.isactive = 1");
				$stmt6->execute();
				$refRow = $stmt6->fetch(PDO::FETCH_ASSOC);
				if ($stmt6->rowCount() > 0) {
					$uid = $refRow['uid'];
					$uname = $refRow['uname'];
					$ulname = $refRow['ulname'];
					$groleid = $refRow['groleid'];
					$ref_role_id = $refRow['fld_role_id'];
					$stmt = $this->db->prepare("INSERT INTO tbl_users(fld_name,fld_lname,fld_email,fld_password,fld_join_date,fld_role_id)VALUES(:UName, :ULName, :UEmail, :UPassword,:JoinDate,:iRoleId)");
					$stmt->bindparam(":UName", $sName);
					$stmt->bindparam(":ULName", $sLName);
					$stmt->bindparam(":UEmail", $sEmail);
					$stmt->bindparam(":UPassword", $sPassword);
					$tempDate = date('Y-m-d H:i:s');
					$stmt->bindparam(":JoinDate", $tempDate);
					$stmt->bindparam(":iRoleId", $groleid);
					$stmt->execute();
					$lastid = $this->db->lastInsertId();
					$stmt5 = $this->db->prepare("SELECT fld_roleid, fld_gvalue FROM tbl_gsettings WHERE fld_roleid = '$groleid'");
					$stmt5->execute();
					$ridRow = $stmt5->fetch(PDO::FETCH_ASSOC);
					if ($stmt5->rowCount() > 0) {
						$comm_val = $ridRow['fld_gvalue'];
					}					
					if ($groleid == 3) {
						//Distributor
						$stmt3 = $this->db->prepare("INSERT INTO tbl_tree(uid, uname, ulname, aid, aname, alname, creationdate)VALUES('$lastid','$sName','$sLName','$uid','$uname','$ulname', NOW())");
						//Default Commission Percentage
						$stmt88 = $this->db->prepare("SELECT * FROM tbl_gsettings_user WHERE fld_userid = '$lastid' LIMIT 1");
						$stmt88->execute();
						if ($stmt88->rowCount() == 0) {
							$stmt77 = $this->db->prepare("INSERT INTO tbl_gsettings_user (fld_gtitle,fld_gcode,fld_gvalue,fld_userid,fld_roleid) VALUES ('Distributor Commission','dist-commission','$comm_val','$lastid','3')");
							$stmt77->execute();
						}
					} elseif ($groleid == 6) {
						//Representative
						$stmt00 = $this->db->prepare("SELECT a.*, b.fld_phone, b.fld_email
						FROM tbl_tree a
						INNER JOIN tbl_users b ON (a.did = b.fld_uid OR a.aid = b.fld_uid)
						WHERE a.uid = '$uid'
						LIMIT 1");
						$stmt00->execute();
						$refinfoRow = $stmt00->fetch(PDO::FETCH_ASSOC);
						if ($stmt00->rowCount() > 0) //Help to find
						{
							$distid = $refinfoRow['uid'];
							$distfname = $refinfoRow['uname'];
							$distlname = $refinfoRow['ulname'];
							$adminid = $refinfoRow['aid'];
							$adminfname = $refinfoRow['aname'];
							$adminlname = $refinfoRow['alname'];
						} else {
							$distid = 0;
							$distfname = '';
							$distlname = '';
							$adminid = $uid;
							$adminfname = $uname;
							$adminlname = $ulname;
						}
						$stmt3 = $this->db->prepare("INSERT INTO tbl_tree(uid, uname, ulname, did, dname, dlname, aid, aname, alname, creationdate)VALUES('$lastid','$sName','$sLName','$distid','$distfname','$distlname','$adminid','$adminfname','$adminlname', NOW())");
					} elseif ($groleid == 2) {
						//Campaign Manager
						$stmt00 = $this->db->prepare("SELECT a.*, gl.groleid, b.fld_phone, b.fld_email
							FROM tbl_tree a
							INNER JOIN tbl_generated_link gl ON (a.uid = gl.uid OR a.rid = gl.uid OR a.did = gl.uid OR a.aid = gl.uid)
							INNER JOIN tbl_users b ON (a.uid = b.fld_uid OR a.rid = b.fld_uid OR a.did = b.fld_uid OR a.aid = b.fld_uid)
							WHERE a.uid = '$uid'
							LIMIT 1");
						$stmt00->execute();
						$refinfoRow = $stmt00->fetch(PDO::FETCH_ASSOC);
						if ($stmt00->rowCount() > 0) //Help to find
						{
							if ($ref_role_id == 3 && $groleid == 2) {
								$repid1 = $refinfoRow['rid'];
								$repfname1 = $refinfoRow['rname'];
								$replname1 = $refinfoRow['rlname'];
								$distid1 = $refinfoRow['uid'];
								$distfname1 = $refinfoRow['uname'];
								$distlname1 = $refinfoRow['ulname'];
								$adminid1 = $refinfoRow['aid'];
								$adminfname1 = $refinfoRow['aname'];
								$adminlname1 = $refinfoRow['alname'];
							} elseif ($ref_role_id == 6 && $groleid == 2) {
								$repid1 = $refinfoRow['uid'];
								$repfname1 = $refinfoRow['uname'];
								$replname1 = $refinfoRow['ulname'];
								$distid1 = $refinfoRow['did'];
								$distfname1 = $refinfoRow['dname'];
								$distlname1 = $refinfoRow['dlname'];
								$adminid1 = $refinfoRow['aid'];
								$adminfname1 = $refinfoRow['aname'];
								$adminlname1 = $refinfoRow['alname'];
							} else {
								$repid1 = $refinfoRow['rid'];
								$repfname1 = $refinfoRow['rname'];
								$replname1 = $refinfoRow['rlname'];
								$distid1 = $refinfoRow['did'];
								$distfname1 = $refinfoRow['dname'];
								$distlname1 = $refinfoRow['dlname'];
								$adminid1 = $refinfoRow['uid'];
								$adminfname1 = $refinfoRow['uname'];
								$adminlname1 = $refinfoRow['ulname'];
							}
						}
						$stmt6 = $this->db->prepare("SELECT groleid FROM tbl_generated_link WHERE usedbyuid = '$uid' AND isused = 1 AND isactive = 1 LIMIT 1");
						$stmt6->execute();
						$refRow = $stmt6->fetch(PDO::FETCH_ASSOC);
						if ($stmt6->rowCount() > 0) {
							$cm_groleid = $refRow['groleid'];
							$repid = $repid1;
							$repfname = $repfname1;
							$replname = $replname1;
							$distid = $distid1;
							$distfname = $distfname1;
							$distlname = $distlname1;
							$adminid = $adminid1;
							$adminfname = $adminfname1;
							$adminlname = $adminlname1;
							$stmt3 = $this->db->prepare("INSERT INTO tbl_tree(uid, uname, ulname, rid, rname, rlname, did, dname, dlname, aid, aname, alname, creationdate)VALUES('$lastid','$sName','$sLName','$repid','$repfname','$replname','$distid','$distfname','$distlname','$adminid','$adminfname','$adminlname', NOW())");
						} else {
							$repid = $refinfoRow['uid'];
							$repfname = $refinfoRow['uname'];
							$replname = $refinfoRow['ulname'];
							$distid = $refinfoRow['did'];
							$distfname = $refinfoRow['dname'];
							$distlname = $refinfoRow['dlname'];
							$adminid2 = $refinfoRow['aid'];
							$adminfname2 = $refinfoRow['aname'];
							$adminlname2 = $refinfoRow['alname'];
							if ($adminfname2 != '') {
								$adminid = $refinfoRow['aid'];
								$adminfname = $refinfoRow['aname'];
								$adminlname = $refinfoRow['alname'];
							} else {
								$adminid = $uid;
								$adminfname = $uname;
								$adminlname = $ulname;
							}
							$stmt3 = $this->db->prepare("INSERT INTO tbl_tree(uid, uname, ulname, rid, rname, rlname, did, dname, dlname, aid, aname, alname, creationdate)VALUES('$lastid','$sName','$sLName','$repid','$repfname','$replname','$distid','$distfname','$distlname','$adminid','$adminfname','$adminlname', NOW())");
						}
					}
					$stmt3->execute();
					$stmt4 = $this->db->prepare("INSERT INTO tbl_commission(uid, uname, rid, rname, commission, withdraw, description, transaction_type, creationdate)VALUES('$lastid','$sName','$uid','$uname', '$comm_val', '0.00', 'Direct Commision from ($sName) Signup', 'Deposit', NOW())");
					$stmt4->execute();
					$stmt7 = $this->db->prepare("UPDATE tbl_generated_link SET isused = '1', usedbyuid = '$lastid', usedbyuname = '$sName', usedbyulname = '$sLName', useddate = NOW() WHERE gkeyhash = '$ref'");
					$stmt7->execute();
				} else {
					$stmt = $this->db->prepare("INSERT INTO tbl_users(fld_name,fld_lname,fld_email,fld_password,fld_join_date,fld_role_id)VALUES(:UName, :ULName, :UEmail, :UPassword,:JoinDate,:iRoleId)");
					$stmt->bindparam(":UName", $sName);
					$stmt->bindparam(":ULName", $sLName);
					$stmt->bindparam(":UEmail", $sEmail);
					$stmt->bindparam(":UPassword", $sPassword);
					$tempDate = date('Y-m-d H:i:s');
					$stmt->bindparam(":JoinDate", $tempDate);
					$stmt->bindparam(":iRoleId", $iRoleId);
					$stmt->execute();
					$lastid = $this->db->lastInsertId();
					$stmt2 = $this->db->prepare("SELECT fld_uid, fld_name, fld_lname, fld_phone, fld_email FROM tbl_users WHERE fld_uid = '1'");
					$stmt2->execute();
					$refRow = $stmt2->fetch(PDO::FETCH_ASSOC);
					if ($stmt2->rowCount() > 0) {
						$ref_id = $refRow['fld_uid'];
						$ref_name = $refRow['fld_name'];
						$reffname = $refRow['fld_name'];
						$reflname = $refRow['fld_lname'];
						$refphone = $refRow['fld_phone'];
						$refemail = $refRow['fld_email'];
					}
					$stmt3 = $this->db->prepare("INSERT INTO tbl_tree(uid, uname, ulname, aid, aname, alname, creationdate)VALUES('$lastid','$sName','$sLName','$ref_id','$reffname','$reflname', NOW())");
					$stmt3->execute();
					$lastid = $this->db->lastInsertId();
					//Find Distributor ID
					$stmt90 = $this->db->prepare("SELECT u.*
					FROM tbl_tree tr
					INNER JOIN tbl_users u ON (tr.uid = u.fld_uid OR tr.did = u.fld_uid)
					WHERE tr.uid = '$lastid' AND u.fld_role_id = '3'
					ORDER BY tr.nid ASC
					LIMIT 1");
					$stmt90->execute();
					if ($stmt90->rowCount() > 0) {
						$refRow = $stmt90->fetch(PDO::FETCH_ASSOC);
						$ref_id = $refRow['fld_uid'];
						$ref_name = $refRow['fld_name'];
						$reffname = $refRow['fld_name'];
						$reflname = $refRow['fld_lname'];
						$refphone = $refRow['fld_phone'];
						$refemail = $refRow['fld_email'];
						$refimage = $refRow['fld_brand_logo_header'];
						$is_refimage = 1;
						$Company_Title = $refRow['fld_cname'];
						$is_Company_Title = 1;
					} else {
						$stmt2 = $this->db->prepare("SELECT fld_uid, fld_name, fld_lname, fld_phone, fld_email FROM tbl_users WHERE fld_uid = '1'");
						$stmt2->execute();
						if ($stmt2->rowCount() > 0) {
							$refRow = $stmt2->fetch(PDO::FETCH_ASSOC);
							$ref_id = $refRow['fld_uid'];
							$ref_name = $refRow['fld_name'];
							$reffname = $refRow['fld_name'];
							$reflname = $refRow['fld_lname'];
							$refphone = $refRow['fld_phone'];
							$refemail = $refRow['fld_email'];
							$refimage = '';
							$is_refimage = 0;
							$Company_Title = sWEBSITENAME;
							$is_Company_Title = 0;
						}
					}
					//Start Emailing
					try
					{
						//SparkPost Init
						$httpClient = new GuzzleAdapter(new Client());
						$sparky = new SparkPost($httpClient, ['key' => SPARK_POST_KEY]);
						//SparkPost Init
						$sparky->setOptions(['async' => false]);
						$promise = $sparky->transmissions->post([
							'content' => ['template_id' => 'sign-up-template'],
							'substitution_data' => [
								"sHOMECMS" => sHOMECMS,
								"sHOME" => sHOME,
								"ref_id" => $ref_id,
								"ref_name" => $ref_name,
								"reffname" => $reffname,
								"reflname" => $reflname,
								"refphone" => $refphone,
								"refemail" => $refemail,
								"sName" => $sName,
								"sLName" => $sLName,
								"sEmail" => $sEmail,
								"refimage" => $refimage,
								"is_refimage" => $is_refimage,
								"fromemail" => INFO_EMAIL,
								"is_Company_Title" => $is_Company_Title,
								"Company_Title" => $Company_Title,
							],
							'description' => sWEBSITENAME.' Member',
							'metadata' => [
								'Campaign_ID' => "",
								'Campaign_Name' => "",
								'Subject' => sWEBSITENAME." Member",
							],
							'recipients' => [
								[
									'address' => [
										'name' => $sName . ' ' . $sLName,
										'email' => $sEmail,
									],
								],
							],
						]);
						$transmissionid = $promise->getBody()['results']['id'];
					} catch (\Exception $e) {
						echo $e->getCode() . "\n";
						echo $e->getMessage() . "\n";
					}
				}
			} else {
				$stmt = $this->db->prepare("INSERT INTO tbl_users(fld_name,fld_lname,fld_email,fld_password,fld_join_date,fld_role_id)VALUES(:UName, :ULName, :UEmail, :UPassword,:JoinDate,:iRoleId)");
				$stmt->bindparam(":UName", $sName);
				$stmt->bindparam(":ULName", $sLName);
				$stmt->bindparam(":UEmail", $sEmail);
				$stmt->bindparam(":UPassword", $sPassword);
				$tempDate = date('Y-m-d H:i:s');
				$stmt->bindparam(":JoinDate", $tempDate);
				$stmt->bindparam(":iRoleId", $iRoleId);
				$stmt->execute();
				$lastid = $this->db->lastInsertId();
				$stmt2 = $this->db->prepare("SELECT fld_uid, fld_name, fld_lname, fld_phone, fld_email FROM tbl_users WHERE fld_uid = '1'");
				$stmt2->execute();
				$refRow = $stmt2->fetch(PDO::FETCH_ASSOC);
				if ($stmt2->rowCount() > 0) {
					$ref_id = $refRow['fld_uid'];
					$ref_name = $refRow['fld_name'];
					$reffname = $refRow['fld_name'];
					$reflname = $refRow['fld_lname'];
					$refphone = $refRow['fld_phone'];
					$refemail = $refRow['fld_email'];
				}
				$stmt3 = $this->db->prepare("INSERT INTO tbl_tree(uid, uname, ulname, aid, aname, alname, creationdate)VALUES('$lastid','$sName','$sLName','$ref_id','$reffname','$reflname', NOW())");
				$stmt3->execute();
				$lastid = $this->db->lastInsertId();
				//Find Distributor ID
				$stmt90 = $this->db->prepare("SELECT u.*
				FROM tbl_tree tr
				INNER JOIN tbl_users u ON (tr.uid = u.fld_uid OR tr.did = u.fld_uid)
				WHERE tr.uid = '$lastid' AND u.fld_role_id = '3'
				ORDER BY tr.nid ASC
				LIMIT 1");
				$stmt90->execute();
				if ($stmt90->rowCount() > 0) {
					$refRow = $stmt90->fetch(PDO::FETCH_ASSOC);
					$ref_id = $refRow['fld_uid'];
					$ref_name = $refRow['fld_name'];
					$reffname = $refRow['fld_name'];
					$reflname = $refRow['fld_lname'];
					$refphone = $refRow['fld_phone'];
					$refemail = $refRow['fld_email'];
					$refimage = $refRow['fld_brand_logo_header'];
					$is_refimage = 1;
					$Company_Title = $refRow['fld_cname'];
					$is_Company_Title = 1;
				} else {
					$stmt2 = $this->db->prepare("SELECT fld_uid, fld_name, fld_lname, fld_phone, fld_email FROM tbl_users WHERE fld_uid = '1'");
					$stmt2->execute();
					if ($stmt2->rowCount() > 0) {
						$refRow = $stmt2->fetch(PDO::FETCH_ASSOC);
						$ref_id = $refRow['fld_uid'];
						$ref_name = $refRow['fld_name'];
						$reffname = $refRow['fld_name'];
						$reflname = $refRow['fld_lname'];
						$refphone = $refRow['fld_phone'];
						$refemail = $refRow['fld_email'];
						$refimage = '';
						$is_refimage = 0;
						$Company_Title = sWEBSITENAME;
						$is_Company_Title = 0;
					}
				}
				//Start Emailing
				try
				{
					//SparkPost Init
					$httpClient = new GuzzleAdapter(new Client());
					$sparky = new SparkPost($httpClient, ['key' => SPARK_POST_KEY]);
					//SparkPost Init
					$sparky->setOptions(['async' => false]);
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'sign-up-template'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"ref_id" => $ref_id,
							"ref_name" => $ref_name,
							"reffname" => $reffname,
							"reflname" => $reflname,
							"refphone" => $refphone,
							"refemail" => $refemail,
							"sName" => $sName,
							"sLName" => $sLName,
							"sEmail" => $sEmail,
							"refimage" => $refimage,
							"is_refimage" => $is_refimage,
							"fromemail" => INFO_EMAIL,
							"is_Company_Title" => $is_Company_Title,
							"Company_Title" => $Company_Title,
						],
						'description' => sWEBSITENAME.' Member',
						'metadata' => [
							'Campaign_ID' => "",
							'Campaign_Name' => "",
							'Subject' => sWEBSITENAME." Member",
						],
						'recipients' => [
							[
								'address' => [
									'name' => $sName . ' ' . $sLName,
									'email' => $sEmail,
								],
							],
						],
					]);
					$transmissionid = $promise->getBody()['results']['id'];
				} catch (\Exception $e) {
					echo $e->getCode() . "\n";
					echo $e->getMessage() . "\n";
				}
			}
			return $stmt6;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function register2($sName, $sLName, $sEmail, $sPassword, $iRoleId, $refferalid, $cid) {
		try
		{
			$stmt = $this->db->prepare("INSERT INTO tbl_users(fld_name,fld_lname,fld_email,fld_password,fld_join_date,fld_role_id)
			VALUES
			(:UName, :ULName, :UEmail, :UPassword,:JoinDate,:iRoleId)");
			$stmt->bindparam(":UName", $sName);
			$stmt->bindparam(":ULName", $sLName);
			$stmt->bindparam(":UEmail", $sEmail);
			$stmt->bindparam(":UPassword", $sPassword);
			$tempDate = date('Y-m-d H:i:s');
			$stmt->bindparam(":JoinDate", $tempDate);
			$stmt->bindparam(":iRoleId", $iRoleId);
			$stmt->execute();
			$lastid = $this->db->lastInsertId();
			$stmt2 = $this->db->prepare("UPDATE tbl_participants SET uid = '$lastid' WHERE cid = '$cid' AND id = '$refferalid'");
			$stmt2->execute();
			$stmt3 = $this->db->prepare("UPDATE tbl_participants_details SET uid = '$lastid' WHERE cid = '$cid' AND pid = '$refferalid'");
			$stmt3->execute();
			return $lastid;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function register3($sName, $sLName, $sEmail, $sPassword, $iRoleId, $cid, $cuid) {
		try
		{
			$sPhone = '';
			$stmt10 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_email = '$sEmail' AND fld_role_id NOT IN ('3','4','6')");
			$stmt10->execute();
			if ($stmt10->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO tbl_users(fld_name,fld_lname,fld_email,fld_password,fld_join_date,fld_role_id)
				VALUES
				(:UName, :ULName, :UEmail, :UPassword,:JoinDate,:iRoleId)");
				$stmt->bindparam(":UName", $sName);
				$stmt->bindparam(":ULName", $sLName);
				$stmt->bindparam(":UEmail", $sEmail);
				$stmt->bindparam(":UPassword", $sPassword);
				$tempDate = date('Y-m-d H:i:s');
				$stmt->bindparam(":JoinDate", $tempDate);
				$stmt->bindparam(":iRoleId", $iRoleId);
				$stmt->execute();
				$lastid = $this->db->lastInsertId();
			} else {
				$userRow = $stmt10->fetch(PDO::FETCH_ASSOC);
				$sName = $userRow['fld_name'];
				$sLName = $userRow['fld_lname'];
				$sEmail = $userRow['fld_email'];
				$sPhone = $userRow['fld_phone'];
				$lastid = $userRow['fld_uid'];
			}
			$stmt11 = $this->db->prepare("SELECT * FROM tbl_participants WHERE cid = '$cid' AND uemail = '$sEmail'");
			$stmt11->execute();
			if ($stmt11->rowCount() == 0) {
				$stmt2 = $this->db->prepare("INSERT INTO tbl_participants (cid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES ('$cid','$lastid','$cuid','$sName','$sLName','$sEmail','$sPhone',NOW())");
				$stmt2->execute();
				$pid = $this->db->lastInsertId();
			}
			$stmt12 = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$cid' AND uemail = '$sEmail'");
			$stmt12->execute();
			if ($stmt12->rowCount() == 0) {
				$stmt3 = $this->db->prepare("INSERT INTO tbl_participants_details (cid,pid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES ('$cid','$pid','$lastid','$cuid','$sName','$sLName','$sEmail','$sPhone',NOW())");
				$stmt3->execute();
			}
			return $lastid;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function join_campaign($uid, $cid, $cuid, $sEmail) {
		try
		{
			$stmt1 = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$cid' AND uemail = '$sEmail'");
			$stmt1->execute();
			if ($stmt1->rowCount() == 0) {
				$stmt10 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_email = '$sEmail'");
				$stmt10->execute();
				$userRow = $stmt10->fetch(PDO::FETCH_ASSOC);
				$sName = $userRow['fld_name'];
				$sLName = $userRow['fld_lname'];
				$sEmail = $userRow['fld_email'];
				$sPhone = $userRow['fld_phone'];
				$lastid = $userRow['fld_uid'];
				$stmt4 = $this->db->prepare("SELECT * FROM tbl_participants WHERE cid = '$cid' AND uemail = '$sEmail'");
				$stmt4->execute();
				if ($stmt4->rowCount() == 0) {
					$stmt2 = $this->db->prepare("INSERT INTO tbl_participants (cid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES ('$cid','$lastid','$cuid','$sName','$sLName','$sEmail','$sPhone',NOW())");
					$stmt2->execute();
					$pid = $this->db->lastInsertId();
				} else {
					$PidRow = $stmt4->fetch(PDO::FETCH_ASSOC);
					$pid = $PidRow['id'];
				}
				$stmt3 = $this->db->prepare("INSERT INTO tbl_participants_details (cid,pid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES ('$cid','$pid','$lastid','$cuid','$sName','$sLName','$sEmail','$sPhone',NOW())");
				$stmt3->execute();
				return 0;
			} else {
				return 1;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function emailtoadmin($sName, $sLName, $sEmail, $sPassword, $sRoleId) {
		try
		{
			try {
				//SparkPost Init
				$httpClient = new GuzzleAdapter(new Client());
				$sparky = new SparkPost($httpClient, ['key' => SPARK_POST_KEY]);
				//SparkPost Init
				$sparky->setOptions(['async' => false]);
				$promise = $sparky->transmissions->post([
					'content' => ['template_id' => 'campaign-manager-request'],
					'substitution_data' => [
						"sHOMECMS" => sHOMECMS,
						"sHOME" => sHOME,
						"sName" => $sName,
						"sLName" => $sLName,
						"sEmail" => $sEmail,
						"sPassword" => $sPassword,
					],
					'description' => "Campaign Manager Request",
					'metadata' => [
						'Campaign_ID' => "",
						'Campaign_Name' => "",
						'Subject' => "Campaign Manager Request",
					],
					'recipients' => [
						[
							'address' => [
								'name' => sWEBSITENAME." Administrator",
								'email' => INFO_EMAIL,
							],
						],
					],
				]);
				$transmissionid = $promise->getBody()['results']['id'];
				$emailsent = 1;
			} catch (\Exception $e) {
				$emailsent = 0;
				echo $e->getCode() . "\n";
				echo $e->getMessage() . "\n";
			}
			return $emailsent;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function unsubscribe($cid, $pid, $did) {
		try
		{
			if ($pid != '' && $did != '') {
				$stmt = $this->db->prepare("SELECT pd.fld_email AS pemail, pd.fld_name AS pname, pd.fld_lname AS plname, pd.fld_phone AS pphone, u.fld_uid, pd.fld_image, u.fld_ftime, u.fld_password, u.fld_name, u.fld_lname, u.fld_email, u.fld_phone, b.fld_cname, b.fld_cemail, b.fld_cphone, b.fld_campaign_title, b.fld_campaign_logo, b.fld_campaign_id, b.fld_hashcamp, a.uname, a.ulname, b.fld_organization_name
				FROM tbl_donors_details a
				INNER JOIN tbl_users u ON a.uid = u.fld_uid
				INNER JOIN tbl_users pd ON pd.fld_uid = '$pid'
				INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
				WHERE a.cid = '$cid' AND a.uid = '$did'");
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				//Find Distributor ID
				$stmt90 = $this->db->prepare("SELECT u.*
				FROM tbl_campaign c
				INNER JOIN tbl_tree tr ON c.fld_uid = tr.uid
				INNER JOIN tbl_users u ON u.fld_uid = tr.did
				WHERE c.fld_campaign_id = '$cid'
				ORDER BY u.fld_uid ASC
				LIMIT 1");
				$stmt90->execute();
				if ($stmt90->rowCount() > 0) {
					$refRow = $stmt90->fetch(PDO::FETCH_ASSOC);
					$ref_name = $refRow['fld_name'];
					$reffname = $refRow['fld_name'];
					$reflname = $refRow['fld_lname'];
					$refphone = $refRow['fld_phone'];
					$refemail = $refRow['fld_email'];
					$refimage = $refRow['fld_brand_logo_header'];
					$is_refimage = 1;
					$Company_Title = $refRow['fld_cname'];
					$is_Company_Title = 1;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = sWEBSITENAME;
					$is_Company_Title = 0;
				}
				$participantemail = $row['pemail'];
				$participantfname = $row['pname'];
				$participantlname = $row['plname'];
				$participantphone = $row['pphone'];
				$donorfname = $row['fld_name'];
				$donorlname = $row['fld_lname'];
				$donoremail = $row['fld_email'];
				$donorphone = $row['fld_phone'];
				$campname = $row['fld_cname'];
				$campemail = $row['fld_cemail'];
				$campphone = $row['fld_cphone'];
				$camptitle = $row['fld_campaign_title'];
				$reqmode = 1; //show donors and participants
				try
				{
					//SparkPost Init
					$httpClient = new GuzzleAdapter(new Client());
					$sparky = new SparkPost($httpClient, ['key' => SPARK_POST_KEY]);
					//SparkPost Init
					$sparky->setOptions(['async' => false]);
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'unsubscribe'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"reqmode" => $reqmode,
							"canfname" => $donorfname,
							"canlname" => $donorlname,
							"canemail" => $donoremail,
							"donorfname" => $donorfname,
							"donorlname" => $donorlname,
							"donoremail" => $donoremail,
							"donorphone" => $donorphone,
							"participantfname" => $participantfname,
							"participantlname" => $participantlname,
							"participantemail" => $participantemail,
							"participantphone" => $participantphone,
							"campname" => $campname,
							"campemail" => $campemail,
							"campphone" => $campphone,
							"camptitle" => $camptitle,
							"refimage" => $refimage,
							"is_refimage" => $is_refimage,
							"fromemail" => INFO_EMAIL,
						],
						'description' => $camptitle,
						'metadata' => [
							'Campaign_ID' => "",
							'Campaign_Name' => $camptitle,
							'Subject' => "Unsubscribe Request",
						],
						'recipients' => [
							[
								'address' => [
									'name' => sWEBSITENAME." Administrator",
									'email' => INFO_EMAIL,
								],
							],
						],
					]);
					$transmissionid = $promise->getBody()['results']['id'];
					$emailsent = 1;
				} catch (\Exception $e) {
					$emailsent = 0;
					echo $e->getCode() . "\n";
					echo $e->getMessage() . "\n";
				}
				$stmt_unsubscribe = $this->db->prepare("UPDATE tbl_donors_details SET is_unsubscribe = '1', is_unsubscribe_date = NOW() WHERE cid = '$cid' AND uid = '$did'");
				$stmt_unsubscribe->execute();
				return $emailsent;
			} elseif ($pid == '' && $did != '') {
				$stmt = $this->db->prepare("SELECT u.fld_name, u.fld_lname, u.fld_email, u.fld_phone, b.fld_cname, b.fld_cemail, b.fld_cphone, b.fld_campaign_title, b.fld_campaign_id
				FROM tbl_donors_details a
				INNER JOIN tbl_users u ON a.uid = u.fld_uid
				INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
				WHERE a.cid = '$cid' AND a.uid = '$did'");
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				//Find Distributor ID
				$stmt90 = $this->db->prepare("SELECT u.*
				FROM tbl_campaign c
				INNER JOIN tbl_tree tr ON c.fld_uid = tr.uid
				INNER JOIN tbl_users u ON u.fld_uid = tr.did
				WHERE c.fld_campaign_id = '$cid'
				ORDER BY u.fld_uid ASC
				LIMIT 1");
				$stmt90->execute();
				if ($stmt90->rowCount() > 0) {
					$refRow = $stmt90->fetch(PDO::FETCH_ASSOC);
					$ref_name = $refRow['fld_name'];
					$reffname = $refRow['fld_name'];
					$reflname = $refRow['fld_lname'];
					$refphone = $refRow['fld_phone'];
					$refemail = $refRow['fld_email'];
					$refimage = $refRow['fld_brand_logo_header'];
					$is_refimage = 1;
					$Company_Title = $refRow['fld_cname'];
					$is_Company_Title = 1;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = sWEBSITENAME;
					$is_Company_Title = 0;
				}
				$donorfname = $row['fld_name'];
				$donorlname = $row['fld_lname'];
				$donoremail = $row['fld_email'];
				$donorphone = $row['fld_phone'];
				$campname = $row['fld_cname'];
				$campemail = $row['fld_cemail'];
				$campphone = $row['fld_cphone'];
				$camptitle = $row['fld_campaign_title'];
				$reqmode = 2; //show donors only
				try
				{
					//SparkPost Init
					$httpClient = new GuzzleAdapter(new Client());
					$sparky = new SparkPost($httpClient, ['key' => SPARK_POST_KEY]);
					//SparkPost Init
					$sparky->setOptions(['async' => false]);
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'unsubscribe'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"reqmode" => $reqmode,
							"canfname" => $donorfname,
							"canlname" => $donorlname,
							"canemail" => $donoremail,
							"donorfname" => $donorfname,
							"donorlname" => $donorlname,
							"donoremail" => $donoremail,
							"donorphone" => $donorphone,
							"participantfname" => "",
							"participantlname" => "",
							"participantemail" => "",
							"participantphone" => "",
							"campname" => $campname,
							"campemail" => $campemail,
							"campphone" => $campphone,
							"camptitle" => $camptitle,
							"refimage" => $refimage,
							"is_refimage" => $is_refimage,
							"fromemail" => INFO_EMAIL,
						],
						'description' => $camptitle,
						'metadata' => [
							'Campaign_ID' => "",
							'Campaign_Name' => $camptitle,
							'Subject' => "Unsubscribe Request",
						],
						'recipients' => [
							[
								'address' => [
									'name' => sWEBSITENAME." Administrator",
									'email' => INFO_EMAIL,
								],
							],
						],
					]);
					$transmissionid = $promise->getBody()['results']['id'];
					$emailsent = 1;
				} catch (\Exception $e) {
					$emailsent = 0;
					echo $e->getCode() . "\n";
					echo $e->getMessage() . "\n";
				}
				$stmt_unsubscribe = $this->db->prepare("UPDATE tbl_donors_details SET is_unsubscribe = '1', is_unsubscribe_date = NOW() WHERE cid = '$cid' AND uid = '$did'");
				$stmt_unsubscribe->execute();
				return $emailsent;
			} elseif ($pid != '' && $did == '') {
				$stmt = $this->db->prepare("SELECT a.uname, a.ulname, a.uemail, a.uphone, a.pid, a.id, a.uid,
				b.fld_cname, b.fld_cemail, b.fld_cphone, b.fld_campaign_title, b.fld_campaign_logo, b.fld_campaign_id, b.fld_hashcamp
				FROM tbl_participants_details a
				INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
				WHERE a.cid = '$cid' AND a.uid = '$pid'");
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				//Find Distributor ID
				$stmt90 = $this->db->prepare("SELECT u.*
				FROM tbl_campaign c
				INNER JOIN tbl_tree tr ON c.fld_uid = tr.uid
				INNER JOIN tbl_users u ON u.fld_uid = tr.did
				WHERE c.fld_campaign_id = '$cid'
				ORDER BY u.fld_uid ASC
				LIMIT 1");
				$stmt90->execute();
				if ($stmt90->rowCount() > 0) {
					$refRow = $stmt90->fetch(PDO::FETCH_ASSOC);
					$ref_name = $refRow['fld_name'];
					$reffname = $refRow['fld_name'];
					$reflname = $refRow['fld_lname'];
					$refphone = $refRow['fld_phone'];
					$refemail = $refRow['fld_email'];
					$refimage = $refRow['fld_brand_logo_header'];
					$is_refimage = 1;
					$Company_Title = $refRow['fld_cname'];
					$is_Company_Title = 1;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = sWEBSITENAME;
					$is_Company_Title = 0;
				}
				$participantfname = $row['uname'];
				$participantlname = $row['ulname'];
				$participantemail = $row['uemail'];
				$participantphone = $row['uphone'];
				$campname = $row['fld_cname'];
				$campemail = $row['fld_cemail'];
				$campphone = $row['fld_cphone'];
				$camptitle = $row['fld_campaign_title'];
				$reqmode = 3; //show participant only
				try
				{
					//SparkPost Init
					$httpClient = new GuzzleAdapter(new Client());
					$sparky = new SparkPost($httpClient, ['key' => SPARK_POST_KEY]);
					//SparkPost Init
					$sparky->setOptions(['async' => false]);
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'unsubscribe'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"reqmode" => $reqmode,
							"canfname" => $participantfname,
							"canlname" => $participantlname,
							"canemail" => $participantemail,
							"donorfname" => "",
							"donorlname" => "",
							"donoremail" => "",
							"donorphone" => "",
							"participantfname" => $participantfname,
							"participantlname" => $participantlname,
							"participantemail" => $participantemail,
							"participantphone" => $participantphone,
							"campname" => $campname,
							"campemail" => $campemail,
							"campphone" => $campphone,
							"camptitle" => $camptitle,
							"refimage" => $refimage,
							"is_refimage" => $is_refimage,
							"fromemail" => INFO_EMAIL,
						],
						'description' => $camptitle,
						'metadata' => [
							'Campaign_ID' => "",
							'Campaign_Name' => $camptitle,
							'Subject' => "Unsubscribe Request",
						],
						'recipients' => [
							[
								'address' => [
									'name' => sWEBSITENAME." Administrator",
									'email' => INFO_EMAIL,
								],
							],
						],
					]);
					$transmissionid = $promise->getBody()['results']['id'];
					$emailsent = 1;
				} catch (\Exception $e) {
					$emailsent = 0;
					echo $e->getCode() . "\n";
					echo $e->getMessage() . "\n";
				}
				$stmt_unsubscribe = $this->db->prepare("UPDATE tbl_participants_details SET is_unsubscribe = '1', is_unsubscribe_date = NOW() WHERE cid = '$cid' AND uid = '$pid'");
				$stmt_unsubscribe->execute();
				return $emailsent;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_user($sName, $sLName, $sEmail, $sPassword, $SPhone, $SAddress, $SCity, $SState, $SCountry, $iRoleId, $SZip, $SStatus = "2") {
		try
		{
			//echo 'pass:'.$password = $sPassword;
			$stmt = $this->db->prepare("INSERT INTO tbl_users(fld_name,fld_lname,fld_email,fld_password,fld_join_date,fld_phone,fld_address,fld_city,fld_state,fld_country,fld_role_id,fld_zip,fld_status) VALUES(:UName, :ULName, :UEmail, :UPassword,:JoinDate,:SPhone,:SAddress,:SCity,:SState,:SCountry,:iRoleId,:SZip,:SStatus)");
			$stmt->bindparam(":UName", $sName);
			$stmt->bindparam(":ULName", $sLName);
			$stmt->bindparam(":UEmail", $sEmail);
			$stmt->bindparam(":UPassword", $sPassword);
			$tempDate = date('Y-m-d H:i:s');
			$stmt->bindparam(":JoinDate", $tempDate);
			$stmt->bindparam(":SPhone", $SPhone);
			$stmt->bindparam(":SAddress", $SAddress);
			$stmt->bindparam(":SCity", $SCity);
			$stmt->bindparam(":SState", $SState);
			$stmt->bindparam(":SCountry", $SCountry);
			$stmt->bindparam(":iRoleId", $iRoleId);
			$stmt->bindparam(":SZip", $SZip);
			$stmt->bindparam(":SStatus", $SStatus);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_profile_image($fld_image, $fld_uid) {
		try
		{
			$stmt = $this->db->prepare("UPDATE tbl_users SET fld_image = '$fld_image' WHERE fld_uid='$fld_uid'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_profile($sCName, $sName, $sLName, $sPhone, $sAddress, $sZipcode, $sCity, $sState, $sCountry, $sEmail, $sPassword, $iId) {
		try
		{
			$stmt = $this->db->prepare("UPDATE tbl_users SET
			fld_cname=:sCName,
			fld_name=:sName,
			fld_lname=:sLName,
			fld_phone=:sPhone,
			fld_address=:sAddress,
			fld_zip=:sZipcode,
			fld_city=:sCity,
			fld_state=:sState,
			fld_country=:sCountry,
			fld_email=:sEmail,
			fld_password=:sPassword
			WHERE fld_uid=:iId");
			$stmt->bindparam(":sCName", $sCName);
			$stmt->bindparam(":sName", $sName);
			$stmt->bindparam(":sLName", $sLName);
			$stmt->bindparam(":sPhone", $sPhone);
			$stmt->bindparam(":sAddress", $sAddress);
			$stmt->bindparam(":sZipcode", $sZipcode);
			$stmt->bindparam(":sCity", $sCity);
			$stmt->bindparam(":sState", $sState);
			$stmt->bindparam(":sCountry", $sCountry);
			$stmt->bindparam(":sEmail", $sEmail);
			$stmt->bindparam(":sPassword", $sPassword);
			$stmt->bindparam(":iId", $iId);
			$stmt->execute();
			$stmt1 = $this->db->prepare("UPDATE tbl_participants SET
			uname=:sName,
			ulname=:sLName,
			uemail=:sEmail,
			uphone=:sPhone
			WHERE uid=:iId");
			$stmt1->bindparam(":sName", $sName);
			$stmt1->bindparam(":sLName", $sLName);
			$stmt1->bindparam(":sEmail", $sEmail);
			$stmt1->bindparam(":sPhone", $sPhone);
			$stmt1->bindparam(":iId", $iId);
			$stmt1->execute();
			$stmt2 = $this->db->prepare("UPDATE tbl_participants_details SET
			uname=:sName,
			ulname=:sLName,
			uemail=:sEmail,
			uphone=:sPhone
			WHERE uid=:iId");
			$stmt2->bindparam(":sName", $sName);
			$stmt2->bindparam(":sLName", $sLName);
			$stmt2->bindparam(":sEmail", $sEmail);
			$stmt2->bindparam(":sPhone", $sPhone);
			$stmt2->bindparam(":iId", $iId);
			$stmt2->execute();
			$stmt3 = $this->db->prepare("UPDATE tbl_donors SET
			uname=:sName,
			ulname=:sLName,
			uemail=:sEmail,
			uphone=:sPhone
			WHERE uid=:iId");
			$stmt3->bindparam(":sName", $sName);
			$stmt3->bindparam(":sLName", $sLName);
			$stmt3->bindparam(":sEmail", $sEmail);
			$stmt3->bindparam(":sPhone", $sPhone);
			$stmt3->bindparam(":iId", $iId);
			$stmt3->execute();
			$stmt4 = $this->db->prepare("UPDATE tbl_donors_details SET
			uname=:sName,
			ulname=:sLName,
			uemail=:sEmail,
			uphone=:sPhone
			WHERE uid=:iId");
			$stmt4->bindparam(":sName", $sName);
			$stmt4->bindparam(":sLName", $sLName);
			$stmt4->bindparam(":sEmail", $sEmail);
			$stmt4->bindparam(":sPhone", $sPhone);
			$stmt4->bindparam(":iId", $iId);
			$stmt4->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_user($sCName, $sName, $sLName, $sEmail, $sPassword, $sPhone, $sAddress, $sCity, $sState, $sCountry, $sZipcode, $sRoleId, $iId, $SStatus = "2") {
		try
		{
			$stmt = $this->db->prepare("UPDATE tbl_users SET
			fld_cname=:sCName,
			fld_name=:sName,
			fld_lname=:sLName,
			fld_role_id=:sRoleId,
			fld_email=:sEmail,
			fld_password=:sPassword,
			fld_phone=:sPhone,
			fld_address=:sAddress,
			fld_city=:sCity,
			fld_state=:sState,
			fld_country=:sCountry,
			fld_status=:SStatus,
			fld_zip=:sZipcode
			WHERE fld_uid=:iId");
			$stmt->bindparam(":sCName", $sCName);
			$stmt->bindparam(":sName", $sName);
			$stmt->bindparam(":sLName", $sLName);
			$stmt->bindparam(":sRoleId", $sRoleId);
			$stmt->bindparam(":sEmail", $sEmail);
			$stmt->bindparam(":sPassword", $sPassword);
			$stmt->bindparam(":sPhone", $sPhone);
			$stmt->bindparam(":sAddress", $sAddress);
			$stmt->bindparam(":sCity", $sCity);
			$stmt->bindparam(":sState", $sState);
			$stmt->bindparam(":sCountry", $sCountry);
			$stmt->bindparam(":sZipcode", $sZipcode);
			$stmt->bindparam(":iId", $iId);
			$stmt->bindparam(":SStatus", $SStatus);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_transaction($tid, $paid_by_id, $paid_by_fname, $paid_by_lname, $status, $method, $bankname, $checkto, $checknumber, $checkamount, $bankname2, $transactionnumber) {
		try
		{
			if ($status == 1 && $method == 'Check') {
				$stmt = $this->db->prepare("UPDATE tbl_transactions SET
			  ispaid = '$status',
			  paid_by_id = '$paid_by_id',
			  paid_by_name = '$paid_by_fname $paid_by_lname',
			  payment_method = '$method',
			  bankname = '$bankname',
			  checkto = '$checkto',
			  checknumber = '$checknumber',
			  checkamount = '$checkamount',
			  checkissuedate = NOW(),
			  checkduedate = NOW(),
			  paid_date = NOW()
			  WHERE id='$tid'");
			} elseif ($status == 1 && $method == 'Online') {
				$stmt = $this->db->prepare("UPDATE tbl_transactions SET
			  ispaid = '$status',
			  paid_by_id = '$paid_by_id',
			  paid_by_name = '$paid_by_fname $paid_by_lname',
			  payment_method = '$method',
			  bankname = '$bankname2',
			  transactionno = '$transactionnumber',
			  paid_date = NOW()
			  WHERE id='$tid'");
			} elseif ($status == 1 && $method == 'Cash') {
				$stmt = $this->db->prepare("UPDATE tbl_transactions SET
			  ispaid = '$status',
			  paid_by_id = '$paid_by_id',
			  paid_by_name = '$paid_by_fname $paid_by_lname',
			  payment_method = '$method',
			  paid_date = NOW()
			  WHERE id='$tid'");
			}
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_state($iStateId, $iCountryId) {
		try
		{
			$stmt2 = $this->db->prepare("SELECT name FROM states WHERE name='$iStateId'");
			$stmt2->execute();
			$stateRow = $stmt2->fetch(PDO::FETCH_ASSOC);
			if ($stmt2->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO states(name, country_name) VALUES('" . $iStateId . "','" . $iCountryId . "')");
				$stmt->execute();
				return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_city($iCityId, $iStateId) {
		try
		{
			$stmt2 = $this->db->prepare("SELECT name FROM cities WHERE name='$iCityId'");
			$stmt2->execute();
			$stateRow = $stmt2->fetch(PDO::FETCH_ASSOC);
			if ($stmt2->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO cities(name, state_name) VALUES('" . $iCityId . "','" . $iStateId . "')");
				$stmt->execute();
				return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function user_chk_login($sEmail,$sPassword) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_email =:umail AND fld_password=:upassword AND fld_role_id != 4 AND fld_status='1'");
			$stmt->execute(array(':umail' => $sEmail, ':upassword' => $sPassword));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 1) {
				return 1;
			} else {
				return 0;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function user_chk_credentials($sEmail) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_email =:umail AND fld_role_id != 4 AND fld_status='1'");
			$stmt->execute(array(':umail' => $sEmail));
			$userRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			} 
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function user_login($uemail, $upassword, $pLogin, $allLogin) {
		try
		{
			//	echo "SELECT * FROM  tbl_users WHERE fld_email ='".$uemail."' and fld_password='".$upassword."' and fld_status='1'";
			$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_role_id != '4' AND fld_email =:umail and fld_password=:upassword and fld_status='1'");
			$stmt->execute(array(':umail' => $uemail, ':upassword' => $upassword));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				//	print_r($userRow);
				//$rolesRow = $stmt->fetchall(PDO::FETCH_ASSOC);
				$_SESSION['bkp_enable'] = 0;
				if ($pLogin == 1 || $allLogin == 1) {
					$_SESSION['bkp_enable'] = 1;
					$_SESSION['bkp_uid'] = $_SESSION['uid'];
					$_SESSION['bkp_uname'] = $_SESSION['uname'];
					$_SESSION['bkp_ulname'] = $_SESSION['ulname'];
					$_SESSION['bkp_role_id'] = $_SESSION['role_id'];
				} else {
					unset($_SESSION['bkp_enable']);
					unset($_SESSION['bkp_uid']);
					unset($_SESSION['bkp_uname']);
					unset($_SESSION['bkp_ulname']);
					unset($_SESSION['bkp_role_id']);
				}
				$_SESSION['uid'] = $userRow['fld_uid'];
				$_SESSION['uname'] = $userRow['fld_name'];
				$_SESSION['ulname'] = $userRow['fld_lname'];
				$_SESSION['role_id'] = $userRow['fld_role_id'];
				if ($userRow['fld_ftime'] == 1) {
					//Check first time login
					$_SESSION['ftime'] = 1;
				} else {
					$_SESSION['ftime'] = 0;
				}
				$rolesRow  = isset($rolesRow) ? $rolesRow : [];
				$_SESSION['role_array'] = $rolesRow;
				//print_r($_SESSION);
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function user_uid_login($uid, $uemail, $upassword, $pLogin, $allLogin) {
		try
		{
			//	echo "SELECT * FROM  tbl_users WHERE fld_email ='".$uemail."' and fld_password='".$upassword."' and fld_status='1'";
			$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_uid =:uid and fld_password=:upassword and fld_status='1'");
			$stmt->execute(array(':uid' => $uid, ':upassword' => $upassword));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				//	print_r($userRow);
				//$rolesRow = $stmt->fetchall(PDO::FETCH_ASSOC);
				$_SESSION['bkp_enable'] = 0;
				if ($pLogin == 1 || $allLogin == 1) {
					$_SESSION['bkp_enable'] = 1;
					$_SESSION['bkp_uid'] = $_SESSION['uid'];
					$_SESSION['bkp_uname'] = $_SESSION['uname'];
					$_SESSION['bkp_ulname'] = $_SESSION['ulname'];
					$_SESSION['bkp_role_id'] = $_SESSION['role_id'];
				} else {
					unset($_SESSION['bkp_enable']);
					unset($_SESSION['bkp_uid']);
					unset($_SESSION['bkp_uname']);
					unset($_SESSION['bkp_ulname']);
					unset($_SESSION['bkp_role_id']);
				}
				$_SESSION['uid'] = $userRow['fld_uid'];
				$_SESSION['uname'] = $userRow['fld_name'];
				$_SESSION['ulname'] = $userRow['fld_lname'];
				$_SESSION['role_id'] = $userRow['fld_role_id'];
				if ($userRow['fld_ftime'] == 1) {
					//Check first time login
					$_SESSION['ftime'] = 1;
				} else {
					$_SESSION['ftime'] = 0;
				}
				$rolesRow  = isset($rolesRow) ? $rolesRow : [];
				$_SESSION['role_array'] = $rolesRow;
				//print_r($_SESSION);
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function forgotpassword($uemail) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_email =:umail");
			$stmt->execute(array(':umail' => $uemail));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getusers() {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_role_id IN (1,2,3,6) ORDER BY fld_name ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getcommission() {
		try
		{
			if ($_SESSION['role_id'] == 1) {
				$stmt = $this->db->prepare("SELECT b.fld_name AS ufname, b.fld_lname AS ulname, c.fld_name AS rfname, c.fld_lname AS rlname, a.commission, withdraw, description, transaction_type, DATE_FORMAT(creationdate, '%m/%d/%Y') AS creationdate FROM tbl_commission a
				LEFT JOIN tbl_users b ON b.fld_uid = a.uid
				LEFT JOIN tbl_users c ON c.fld_uid = a.rid");
			} else {
				$stmt = $this->db->prepare("SELECT b.fld_name AS ufname, b.fld_lname AS ulname, c.fld_name AS rfname, c.fld_lname AS rlname, a.commission, withdraw, description, transaction_type, DATE_FORMAT(creationdate, '%m/%d/%Y') AS creationdate
				FROM tbl_commission a
				LEFT JOIN tbl_users b ON b.fld_uid = a.uid
				LEFT JOIN tbl_users c ON c.fld_uid = a.rid
				WHERE a.uid = '$_SESSION[uid]' OR a.rid = '$_SESSION[uid]'");
			}
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function gethierarchy($getuserid, $getrid) {
		try
		{
			if ($_SESSION['role_id'] == 1) {
				if ($getuserid > 1) {
					if ($getrid == 1) {
						$whereclause = "WHERE tr.aid = '$getuserid'";
						$limitclause = "";
					} elseif ($getrid == 3) {
						$whereclause = "WHERE tr.did = '$getuserid'";
						$limitclause = "";
					} elseif ($getrid == 6) {
						$whereclause = "WHERE tr.rid = '$getuserid'";
						$limitclause = "";
					} elseif ($getrid == 2) {
						$whereclause = "";
						$limitclause = "LIMIT 0";
					}
					$stmt = $this->db->prepare("SELECT tr.uname, tr.rid, tr.uid, tr.creationdate, ur.fld_name, ur.fld_lname, ur.fld_email, ur.fld_phone, ur.fld_role_id FROM tbl_tree tr INNER JOIN tbl_users ur ON ur.fld_uid = tr.uid $whereclause ORDER BY ur.fld_name ASC $limitclause");
				} else {
					$stmt = $this->db->prepare("SELECT tr.uname, tr.rid, tr.uid, tr.creationdate, ur.fld_name, ur.fld_lname, ur.fld_email, ur.fld_phone, ur.fld_role_id FROM tbl_tree tr INNER JOIN tbl_users ur ON ur.fld_uid = tr.uid ORDER BY ur.fld_name ASC");
				}
			}if ($_SESSION['role_id'] == 3) {
				if ($getrid == 1) {
					$whereclause = "WHERE tr.aid = '$getuserid'";
					$limitclause = "";
				} elseif ($getrid == 3) {
					$whereclause = "WHERE tr.did = '$getuserid'";
					$limitclause = "";
				} elseif ($getrid == 6) {
					$whereclause = "WHERE tr.rid = '$getuserid'";
					$limitclause = "";
				} elseif ($getrid == 2) {
					$whereclause = "";
					$limitclause = "LIMIT 0";
				}
				$stmt = $this->db->prepare("SELECT tr.uname, tr.rid, tr.uid, tr.creationdate, ur.fld_name, ur.fld_lname, ur.fld_email, ur.fld_phone, ur.fld_role_id FROM tbl_tree tr INNER JOIN tbl_users ur ON ur.fld_uid = tr.uid $whereclause ORDER BY ur.fld_name ASC $limitclause");
			}if ($_SESSION['role_id'] == 6) {
				if ($getrid == 1) {
					$whereclause = "WHERE tr.aid = '$getuserid'";
					$limitclause = "";
				} elseif ($getrid == 3) {
					$whereclause = "WHERE tr.did = '$getuserid'";
					$limitclause = "";
				} elseif ($getrid == 6) {
					$whereclause = "WHERE tr.rid = '$getuserid'";
					$limitclause = "";
				} elseif ($getrid == 2) {
					$whereclause = "";
					$limitclause = "LIMIT 0";
				}
				$stmt = $this->db->prepare("SELECT tr.uname, tr.rid, tr.uid, tr.creationdate, ur.fld_name, ur.fld_lname, ur.fld_email, ur.fld_phone, ur.fld_role_id FROM tbl_tree tr INNER JOIN tbl_users ur ON ur.fld_uid = tr.uid $whereclause ORDER BY ur.fld_name ASC $limitclause");
			}
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdonors() {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_role_id = '4' ORDER BY fld_name ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdonorsunsubscribed($getuserid, $getrid) {
		try
		{
			if ($getrid == 1) {
				$whereclause = "tr.aid = '$getuserid'";
			} elseif ($getrid == 3) {
				$whereclause = "tr.did = '$getuserid'";
			} elseif ($getrid == 6) {
				$whereclause = "tr.rid = '$getuserid'";
			} else {
				$whereclause = "";
			}
			$stmt = $this->db->prepare("SELECT dd.uid AS DonorId, dd.uname AS DonorFName, dd.ulname AS DonorLName, dd.uemail AS DonorEmail, DATE_FORMAT(dd.is_unsubscribe_date, '%m/%d/%Y') AS is_unsubscribe_date, pd.uid AS ParticipantId, pd.uname AS ParticipantFName, pd.ulname AS ParticipantLName, pd.uemail AS ParticipantEmail, c.fld_campaign_id AS CampaignId, c.fld_cname AS CampaignFName, c.fld_clname AS CampaignLName, c.fld_campaign_title AS CampaignTitle
				FROM tbl_donors_details dd
				LEFT JOIN tbl_participants_details pd ON pd.uid = dd.puid
				LEFT JOIN tbl_campaign c ON c.fld_campaign_id = dd.cid
				LEFT JOIN tbl_tree tr ON tr.uid = c.fld_uid
				WHERE $whereclause AND dd.is_unsubscribe = '1'
				ORDER BY dd.uname,dd.ulname ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getparticipantsunsubscribed() {
		try
		{
			$stmt = $this->db->prepare("SELECT
			pd.uid AS ParticipantId, pd.uname AS ParticipantFName, pd.ulname AS ParticipantLName, pd.uemail AS ParticipantEmail, DATE_FORMAT(pd.is_unsubscribe_date, '%m/%d/%Y') AS is_unsubscribe_date,
			c.fld_campaign_id AS CampaignId, c.fld_cname AS CampaignFName, c.fld_clname AS CampaignLName, c.fld_campaign_title AS CampaignTitle
			FROM tbl_participants_details pd
			LEFT JOIN tbl_campaign c ON c.fld_campaign_id = pd.cid
			WHERE pd.is_unsubscribe = '1'
			ORDER BY pd.uname,pd.ulname ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getparticipants($rid, $uid) {
		try
		{
			if ($rid == 1) {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_role_id = '5' ORDER BY fld_name ASC");
			} elseif ($rid == 2) {
				$stmt = $this->db->prepare("SELECT a.*
					FROM tbl_users a
					INNER JOIN tbl_participants_details pd ON pd.uid = a.fld_uid
					LEFT JOIN tbl_tree i ON i.uid = pd.cuid
					WHERE a.fld_role_id = '5' AND i.uid = '$uid'");
			} elseif ($rid == 3) {
				$stmt = $this->db->prepare("SELECT a.*
					FROM tbl_users a
					INNER JOIN tbl_participants_details pd ON pd.uid = a.fld_uid
					LEFT JOIN tbl_tree i ON i.uid = pd.cuid
					WHERE a.fld_role_id = '5' AND i.did = '$uid'");
			} elseif ($rid == 6) {
				$stmt = $this->db->prepare("SELECT a.*
					FROM tbl_users a
					INNER JOIN tbl_participants_details pd ON pd.uid = a.fld_uid
					LEFT JOIN tbl_tree i ON i.uid = pd.cuid
					WHERE a.fld_role_id = '5' AND i.rid = '$uid'");
			}
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function gettransactions() {
		try
		{
			$stmt = $this->db->prepare("SELECT a.id, b.fld_campaign_title, a.requested_by_name, c.fld_role, a.request_amount, DATE_FORMAT(a.request_date, '%m/%d/%Y') AS request_date, a.ispaid, a.paid_by_name, a.payment_method, DATE_FORMAT(a.paid_date, '%m/%d/%Y') AS paid_date
			FROM tbl_transactions a
			LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
			LEFT JOIN tbl_role c ON c.fld_role_id = a.requested_by_role
			ORDER BY a.id DESC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function gettransactionsdetail($tid) {
		try
		{
			$stmt = $this->db->prepare("SELECT a.id, a.cid, b.fld_campaign_title, a.requested_by_name, c.fld_role, a.request_amount,
			DATE_FORMAT(a.request_date, '%m/%d/%Y') AS request_date, a.ispaid, a.paid_by_name, a.payment_method,
			DATE_FORMAT(a.paid_date, '%m/%d/%Y') AS paid_date,
			a.bankname, a.checkto, a.checknumber, a.checkamount, a.transactionno, a.requested_by_role
			FROM tbl_transactions a
			LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
			LEFT JOIN tbl_role c ON c.fld_role_id = a.requested_by_role
			WHERE a.id = '$tid'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getparticipantslist($cid, $pid) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE uid = :pid AND cid = :cid AND is_unsubscribe = '1'");
			$stmt->execute(array(':pid' => $pid, ':cid' => $cid));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdonorslist($cid, $pid, $did) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM tbl_donors_details WHERE uid = :did AND puid = :pid AND cid = :cid AND is_unsubscribe = '1'");
			$stmt->execute(array(':did' => $did, ':pid' => $pid, ':cid' => $cid));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdonorslist2($cid, $did) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM tbl_donors_details WHERE uid = :did AND cid = :cid AND is_unsubscribe = '1'");
			$stmt->execute(array(':did' => $did, ':cid' => $cid));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getuserdetail($id) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_uid =:id");
			$stmt->execute(array(':id' => $id));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getallcampaign() {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM tbl_campaign WHERE fld_active = 1");
			$stmt->execute();
			$campaignRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $campaignRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getallparticipant($id) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_participants WHERE cid =:id");
			$stmt->execute(array(':id' => $id));
			$participantRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $participantRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	
	public function setDataForAdminDonation($data){
		$iData = [];
		$iData['refferal_by'] = $data['refferal_by']; 
		$iData['tid'] = $data['tid'];
		$iData['cmfname'] = $data['cmfname'];
		$iData['cmlname'] = $data['cmlname'];
		$iData['uemail'] = $data['uemail'];
		$iData['uphone'] = $data['uphone'];
		$iData['uid'] = $data['uid']; 
		$iData['payment_through'] = $data['payment_through'];
		$iData['sms_sent_date'] = $data['sms_sent_date'];
		$iData['email_sent_date'] = $data['email_sent_date'];
		$iData['card_number'] = $data['card_number']; 
		$iData['donation_amount'] = $data['donation_amount'];
		$iData['cid'] = $data['cid'];
		$iData['ufname'] = $data['ufname'];
		$iData['ulname'] = $data['ulname']; 
		$iData['client_ip'] = $data['client_ip'];
		$iData['reward_id'] = $data['reward_id'];
		$iData['reward_desc'] = $data['reward_desc'];
		if( isset($data['creationdate']) ){
			$iData['creationdate'] = $data['creationdate']; 
		}
		return $iData;
	}

	public function update_admin_donation($data) {

	}
	/**
	* @purpose: 
		- This funciton can only be called from Super Admin
		- Settel check payment donation
		- Insert donor and donor details ( first check already exist in system or not )
	  @author: abdulrauf618@gmail.com
	*/
	// public function insert_or_update_admin_donation($data) 
	public function insert_or_update_admin_donation($data, $REQUEST) {
		// echo "insert_or_update_admin_donation==<pre>"; print_r(['data'=>$data, 'REQUEST'=>$REQUEST]); die();
		$donation_id = 0;
		$refund_id = 0;
		try
		{	
			if( isset($REQUEST["donation_id"]) && 
				is_numeric($REQUEST["donation_id"]) && 
				$REQUEST["donation_id"] > 0
			){
				$donation_id = $REQUEST["donation_id"];
			}

			if( isset($REQUEST['refund_id']) && 
				is_numeric($REQUEST['refund_id']) && 
				$REQUEST['refund_id'] > 0
			){
				$refund_id = $REQUEST['refund_id'];
			}

			$data = $this->setDataForAdminDonation($data);
			// echo __line__."- insert_or_update_admin_donation - <pre>"; var_dump(['donation_id'=>$donation_id,'data'=>$data]); die();
			
			/* //Donation update case, now we delete old donation on update first
			if( $donation_id > 0 ){
				$modQueryPart = "";
				if($REQUEST['cur_state'] == 'settled'){
			    	$modQueryPart = ", mode=1 ";
				}
				else if($REQUEST['cur_state'] == 'refunded'){
			    	$modQueryPart = ", mode=2 ";
				}

				$sql =  "UPDATE tbl_donations SET 
						 uid= :uid, 
						 tid= :tid, 
						 ufname= :cmfname, 
						 ulname= :cmlname, 
						 uemail= :uemail, 
						 uphone= :uphone, 
						 refferal_by= :refferal_by, 
						 payment_through= :payment_through, 
						 sms_sent_date= :sms_sent_date, 
						 email_sent_date= :email_sent_date, 
						 card_number= :card_number, 
						 donation_amount= :donation_amount, 
						 cid= :cid, 
						 cmfname= :ufname, 
						 cmlname= :ulname, 
						 client_ip= :client_ip, 
						 reward_id= :reward_id, 
						 reward_desc= :reward_desc
						 $modQueryPart
						 where id=".$donation_id."
						 ";
				
				// echo __line__."- update donation - <pre>"; var_dump(['sql'=>$sql,'data'=>$data]); die();
			

				$stmt = $this->db->prepare($sql);
				$stmt->execute($data);

			}
			*/

			//Delete old donation when donation id is exist and status going to change
			if( $donation_id > 0 && $REQUEST['cur_state'] != 'settled'){
				//Delete old donation
				try {
					$stmt = $this->db->prepare("DELETE from tbl_donations WHERE id = '" . $donation_id . "'");
					$stmt->execute();
					$donation_id = 0;
				} catch (PDOException $e) {
					echo $e->getMessage();
				}
			}
			if( $refund_id > 0 && $REQUEST['cur_state'] != 'refunded'){
				//Delete old refund row
				try {
					$stmt = $this->db->prepare("DELETE from tbl_donations_refund WHERE id = '" . $refund_id . "'");
					$stmt->execute();
					$refund_id = 0;
				} catch (PDOException $e) {
					echo $e->getMessage();
				}
			}

			// echo "1767 - insert_or_update_admin_donation==<pre>"; print_r(['data'=>$data, 'REQUEST'=>$REQUEST]); die();
			if($REQUEST['cur_state'] == 'settled'){
				//INSERT CHECK PAYMENT INTO DONATIONS 
				// echo "<pre>"; var_dump($data); die();
			    $sql =  "INSERT INTO tbl_donations SET 
									 cid=:cid,
			    					 uid=:uid,
									 tid=:tid,
									 check_payment_id=:check_payment_id,
									 ufname=:cmfname,
									 ulname=:cmlname,
									 uemail=:uemail,
									 cmfname=:ufname,
									 cmlname=:ulname,
									 uphone=:uphone,
									 donation_amount=:donation_amount,
									 card_number=:card_number,
									 payment_through=:payment_through,
									 refferal_by=:refferal_by ,
									 client_ip=:client_ip,
									 sms_sent_date=:sms_sent_date,
									 email_sent_date=:email_sent_date,
									 reward_id=:reward_id,
									 reward_desc=:reward_desc,
									 creationdate=:creationdate 
								";
				$tdData = array(
					"cid"=>$data["cid"],
					"uid"=>$data["uid"],
					"tid"=>$data["tid"],
					"check_payment_id"=>$REQUEST["cp_id"],
					"ufname"=>$data["ufname"],
					"ulname"=>$data["ulname"],
					"uemail"=>$data["uemail"],					
					"cmfname"=>$data["cmfname"],
					"cmlname"=>$data["cmlname"],
					"uphone"=>$data["uphone"],
					"donation_amount"=>$data["donation_amount"],
					"card_number"=>$data["card_number"],
					"payment_through"=>$data["payment_through"],
					"refferal_by"=>$data["refferal_by"],
					"client_ip"=>$data["client_ip"],
					"sms_sent_date"=>$data["sms_sent_date"],
					"email_sent_date"=>$data["email_sent_date"],
					"reward_id"=>$data["reward_id"],
					"reward_desc"=>$data["reward_desc"],
					"creationdate"=>$data["creationdate"],
				);				
				$stmt = $this->db->prepare($sql);
				$stmt->execute($tdData);
				$donation_id = $this->db->lastInsertId();
				// die(print_r([$donation_id, $tdData]));
			}
			else if($REQUEST['cur_state'] == 'refunded'){
				//INSERT CHECK PAYMENT INTO DONATIONS 
				// echo "<pre>"; var_dump($data); die();
			    $sql =  "
			    INSERT INTO tbl_donations_refund SET 
						 tid= 'check',
						 check_payment_id=:check_payment_id, 
						 cid= :cid, 
						 uid= :uid, 
						 cmfname= :ufname, 
						 cmlname= :ulname, 
						 ufname= :cmfname, 
						 ulname= :cmlname, 
						 uemail= :uemail, 
						 uphone= :uphone, 
						 donation_amount= :donation_amount, 
						 card_number= :card_number, 
						 payment_through= :payment_through, 
						 refferal_by= :refferal_by, 
						 client_ip= :client_ip 
						 ";

				$rfData = array(
					"check_payment_id"=>$REQUEST["cp_id"],
					"cid"=>$data["cid"],
					"uid"=>$data["uid"],
					"cmfname"=>$data["cmfname"],
					"cmlname"=>$data["cmlname"],
					"ufname"=>$data["ufname"],
					"ulname"=>$data["ulname"],
					"uemail"=>$data["uemail"],
					"uphone"=>$data["uphone"],
					"donation_amount"=>$data["donation_amount"],
					"card_number"=>$data["card_number"],
					"payment_through"=>$data["payment_through"],
					"refferal_by"=>$data["refferal_by"],
					"client_ip"=>$data["client_ip"]
				);
				$stmt = $this->db->prepare($sql);
				$stmt->execute($rfData);
				$refund_id = $this->db->lastInsertId();
				// die(var_dump($data));
			}	
            // REMOVE unnecssory and SET parameters in data arrray, then use that data for donor and donor deatils insert/update
            $data['full_name'] = $data['ufname'].' '.$data['ulname'];
			//die(var_dump($data));
            unset($data['tid'],$data['ufname'], $data['ulname'], $data['payment_through'], $data['sms_sent_date'], $data['email_sent_date'], $data['card_number'], $data['donation_amount'], $data['client_ip'], $data['reward_id'], $data['reward_desc']);
        

        	// Check we can insert donor and donor details ?
	        $canAddDonor = true;
	        $canAddDonorDetails = true;
	        $did = 0; //donor id

	        // check we already have donor( Email ) in our system___{___
		        $alreadyHaveDonorQuery = $this->db->prepare("SELECT * FROM  tbl_donors WHERE uemail = '".$data["uemail"]."' || uphone = '".$data["uphone"]."'");
	    		$alreadyHaveDonorQuery->execute();
				$alreadyHaveDonorRow = $alreadyHaveDonorQuery->fetch(PDO::FETCH_ASSOC);
				if ($alreadyHaveDonorQuery->rowCount() > 0) {
					$canAddDonor = false;
					$did = $alreadyHaveDonorRow['id'];
				}
				if( $canAddDonor == false){
					$alreadyHaveDonorDetailsQuery = $this->db->prepare("SELECT * FROM  tbl_donors_details WHERE uemail = '".$data["uemail"]."' || uphone = '".$data["uphone"]."' || did='".$did."'");
		    		$alreadyHaveDonorDetailsQuery->execute();
					$alreadyHaveDonorDetailsRow = $alreadyHaveDonorDetailsQuery->fetch(PDO::FETCH_ASSOC);
					if ($alreadyHaveDonorDetailsQuery->rowCount() > 0) {
						$canAddDonorDetails = false;
					}
				}
	        // check we already have donor( Email ) in our system___}___

	        if($canAddDonor == true){
		        try { 
		            $sql3 =  "INSERT INTO tbl_donors(cid, uid, puid, uname, ulname, uemail, uphone, creationdate, participantid, participantname) 
		                                                VALUES(:cid, :uid, :refferal_by, :cmfname, :cmlname, :uemail, :uphone, :creationdate, :refferal_by, :full_name)";
		            $stmt3 = $this->db->prepare($sql3);
		            $stmt3->execute($data);
		            $did = $this->db->lastInsertId();
		        } catch (\Exception $exception) {
		            die("97".$exception->getMessage());
		        }
	    	}//if of $canAddDonor ends here

	    	if($canAddDonorDetails == true){
		        try {
		            $data['did'] = $did;
		            $data['is_unsubscribe'] = 0;
		            $data['sent_date'] = date('y-m-d H:i:s');
		            $data['sms_sent_date'] = date('y-m-d H:i:s');
		            $data['likeas'] = 1;

		            // unset($data['full_name']);
					//print_r($data);
					//exit;
					//die(var_dump(sizeof($data)));

		            $sql2 =  "INSERT INTO tbl_donors_details(cid, did, uid, puid, uname, ulname, uemail, uphone, creationdate, is_unsubscribe, sent_date, likeas,sms_sent_date, participantid, participantname) 
												VALUES(:cid, :did, :uid, :refferal_by, :cmfname, :cmlname, :uemail, :uphone, :creationdate, :is_unsubscribe, :sent_date, :likeas,:sms_sent_date, :refferal_by, :full_name)";
		            $stmt2 = $this->db->prepare($sql2);
		            $stmt2->execute($data);
		        } 
		        catch (\Exception $exception) {
		            die("9".$exception->getMessage());
		        }
	    	}//if of $canAddDonorDetails ends here
		} catch (PDOException $e) {
			echo $e->getMessage();
		}

		$donation_resp = [];
		$donation_resp['update_check_payment'] = true;//($donation_id > 0 || $refund_id > 0) ? true : false;
		$donation_resp['donation_id'] = $donation_id;
		$donation_resp['refund_id'] = $refund_id;
		return $donation_resp;
	}
	
	public function getuserdetail2($id) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE pid='$id'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function checkemail($email, $cid, $uid) {
		try
		{
			if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_participants_details WHERE uemail = '$email' AND cid = '$cid'");
			} else {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_participants_details WHERE uemail = '$email' AND cid = '$cid' AND cuid='$uid'");
			}
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function checkemail2($email, $cid, $uid) {
		try
		{
			if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_participants_details WHERE uemail = '$email' AND cid = '$cid'");
			} else {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_participants_details WHERE uemail = '$email' AND cid = '$cid' AND cuid='$uid'");
			}
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function checkemail4($email, $cid, $uid, $cemail) {
		try
		{
			if ($email == $cemail) {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_campaign WHERE fld_campaign_id = '$cid'");
			} else {
				if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
					$stmt = $this->db->prepare("SELECT * FROM  tbl_participants_details WHERE uemail = '$email' AND cid = '$cid'");
				} else {
					$stmt = $this->db->prepare("SELECT * FROM  tbl_participants_details WHERE uemail = '$email' AND cid = '$cid' AND cuid='$uid'");
				}
			}
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getrepresentative($uid, $role_id) {
		try
		{
			$stmt = $this->db->prepare("SELECT b.fld_name, b.fld_lname, b.fld_email, b.fld_phone, b.fld_uid FROM tbl_tree a LEFT JOIN tbl_users b ON a.rid = b.fld_uid WHERE a.uid = '$uid' AND b.fld_role_id = '$role_id'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdistributor($uid, $role_id) {
		try
		{
			$stmt = $this->db->prepare("SELECT b.fld_name, b.fld_lname, b.fld_email, b.fld_phone, b.fld_uid FROM tbl_tree a LEFT JOIN tbl_users b ON a.rid = b.fld_uid WHERE a.uid = '$uid' AND b.fld_role_id = '$role_id'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getadmins($role_id) {
		try
		{
			$stmt = $this->db->prepare("SELECT fld_name, fld_lname, fld_email, fld_phone, fld_uid FROM tbl_users WHERE fld_role_id = '$role_id'");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getuseronline($id) {
		try
		{
			$stmt = $this->db->prepare("SELECT fld_lastactivity,fld_uid,fld_ftime FROM tbl_users WHERE fld_uid='$id'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else{
				return [];
			}
			
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getuserdetailemail($email) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_email='$email' AND fld_role_id NOT IN ('3','4','6')");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				$userRow['userexists'] = 1;
			} else {
				$userRow['userexists'] = 0;
			}
			return $userRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getuserdetailbyemail($email) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_email='$email' AND fld_role_id = '5'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getuserdetailbyemailfordonor($email,$phone) {
		try
		{
			$phone = str_replace('___-___-____','', $phone);
			if ($email != '' && $phone != '') {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_email = '$email' AND fld_role_id = '4' LIMIT 1");
			} elseif ($email == '' && $phone != '') {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_phone = '$phone' AND fld_role_id = '4' LIMIT 1");
			} elseif ($email != '' && $phone == '') {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_email = '$email' AND fld_role_id = '4' LIMIT 1");
			}
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getuserdetailbyemailforparticipant($email,$phone) {
		try
		{
			$phone = str_replace('___-___-____','', $phone);
			if ($email != '' && $phone != '') {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_email = '$email' AND fld_phone = '$phone' AND fld_role_id = '5' LIMIT 1");
			} elseif ($email == '' && $phone != '') {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_phone = '$phone' AND fld_role_id = '5' LIMIT 1");
			} elseif ($email != '' && $phone == '') {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_email = '$email' AND fld_role_id = '5' LIMIT 1");
			}
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getuserroledetail($roled) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_role_access WHERE fld_role_id =:id");
			$stmt->execute(array(':id' => $roled));
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_user($id) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_role_access WHERE fld_role_id =:id");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_hierarchy($id) {
		try
		{
			$stmt = $this->db->prepare("SELECT *, COUNT(d.id) AS donorscount, COUNT(c.id) AS participantscount
			FROM tbl_tree a
			LEFT JOIN tbl_campaign b ON a.rid = b.fld_uid OR a.uid = b.fld_uid
			LEFT JOIN tbl_participants_details c ON c.uid = a.rid OR c.cuid = a.uid
			LEFT JOIN tbl_donors_details d ON c.uid = d.puid
			WHERE a.rid = '$id' or a.uid = '$id' AND b.fld_uid = '$id'
			GROUP BY b.fld_campaign_id
			HAVING donorscount > 0 OR participantscount > 0");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function delete_user($id) {
		try
		{
			$stmt = $this->db->prepare("DELETE from tbl_users WHERE fld_uid = '" . $id . "'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function check_participant_donation($id) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_donations WHERE refferal_by = '" . $id . "'");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			$counter = $stmt->rowCount();
			if ($counter > 0) {
				return $counter;
			} else {
				return 0;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function check_donor_donation($id) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_donations WHERE uid = '" . $id . "'");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			$counter = $stmt->rowCount();
			if ($counter > 0) {
				return $counter;
			} else {
				return 0;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function delete_participant($id) {
		try
		{
			$stmt = $this->db->prepare("DELETE from tbl_participants WHERE uid = '" . $id . "'");
			$stmt->execute();
			$stmt2 = $this->db->prepare("DELETE from tbl_participants_details WHERE uid = '" . $id . "'");
			$stmt2->execute();
			$stmt3 = $this->db->prepare("DELETE from tbl_donors WHERE puid = '" . $id . "'");
			$stmt3->execute();
			$stmt4 = $this->db->prepare("DELETE from tbl_donors_details WHERE puid = '" . $id . "'");
			$stmt4->execute();
			$stmt5 = $this->db->prepare("DELETE from tbl_users WHERE fld_uid = '" . $id . "'");
			$stmt5->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function delete_donor($id) {
		try
		{
			$stmt = $this->db->prepare("DELETE from tbl_donors WHERE uid = '" . $id . "'");
			$stmt->execute();
			$stmt2 = $this->db->prepare("DELETE from tbl_donors_details WHERE uid = '" . $id . "'");
			$stmt2->execute();
			$stmt3 = $this->db->prepare("DELETE from tbl_users WHERE fld_uid = '" . $id . "'");
			$stmt3->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function delete_selected_user($id) {
		try
		{
			$stmt = $this->db->prepare("DELETE from tbl_users WHERE fld_uid = '" . $id . "'");
			$stmt->execute();
			$stmt2 = $this->db->prepare("DELETE from tbl_campaign WHERE fld_uid = '" . $id . "'");
			$stmt2->execute();
			$stmt3 = $this->db->prepare("DELETE from tbl_tree WHERE rid = '" . $id . "' OR uid = '" . $id . "'");
			$stmt3->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_user_status($sStatus, $iId) {
		try
		{
			$stmt = $this->db->prepare("UPDATE  tbl_users SET fld_status=:sStatus WHERE fld_uid=:iId");
			$stmt->bindparam(":sStatus", $sStatus);
			$stmt->bindparam(":iId", $iId);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_lastactivity($iId) {
		try
		{
			$stmt = $this->db->prepare("UPDATE tbl_users SET fld_lastactivity = NOW() WHERE fld_uid = '$iId'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_ftime($iId) {
		try
		{
			$stmt = $this->db->prepare("UPDATE tbl_users SET fld_ftime = '0' WHERE fld_uid = '$iId'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_role($sRole, $sRolePid, $iRights) {
		try
		{
			//echo $sRole.'----'.$sRolePid;
			$stmt = $this->db->prepare("INSERT INTO tbl_role(fld_role,fld_role_pid,fld_role_status,fld_rights) VALUES('" . $sRole . "','" . $sRolePid . "','1','" . $iRights . "')");
			$stmt->execute();
			return $this->db->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function generated_hash($uid, $uname, $ulname, $groleid, $generated_hash) {
		try
		{
			$stmt = $this->db->prepare("INSERT INTO tbl_generated_link(uid,uname,ulname,groleid,gkeyhash,creationdate) VALUES ('" . $uid . "','" . $uname . "','" . $ulname . "','" . $groleid . "','" . $generated_hash . "', NOW())");
			$stmt->execute();
			return $this->db->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_role_access($sRoleID, $sModuleId, $sView, $sAdd, $sEdit, $sDel) {
		try
		{
			$stmt = $this->db->prepare("INSERT INTO tbl_role_access(fld_role_id,fld_module_id,fld_view,fld_add,fld_edit,fld_delete) VALUES('" . $sRoleID . "','" . $sModuleId . "','" . $sView . "','" . $sAdd . "','" . $sEdit . "','" . $sDel . "')");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_role_access($sRoleID, $sModuleId, $sView, $sAdd, $sEdit, $sDel, $iId) {
		try
		{
			$stmt = $this->db->prepare("UPDATE  tbl_role_access SET fld_role_id=:sRoleID,fld_module_id=:sModuleId,fld_view=:sView,fld_add=:sAdd,fld_edit=:sEdit,fld_delete=:sDel WHERE fld_roleaccess_id=:iId");
			$stmt->bindparam(":sRoleID", $sRoleID);
			$stmt->bindparam(":sModuleId", $sModuleId);
			$stmt->bindparam(":sView", $sView);
			$stmt->bindparam(":sAdd", $sAdd);
			$stmt->bindparam(":sEdit", $sEdit);
			$stmt->bindparam(":sDel", $sDel);
			$stmt->bindparam(":iId", $iId);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getroleaccessdetail($iRoleId, $iModuleId) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_role_access WHERE fld_role_id =:iRoleId and  fld_module_id =:iModuleId");
			$stmt->execute(array(':iRoleId' => $iRoleId, ':iModuleId' => $iModuleId));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function delete_role($id) {
		try
		{
			$stmt = $this->db->prepare("DELETE from tbl_role WHERE fld_role_id = '" . $id . "'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getroles() {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_role ORDER BY fld_role ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getuserrole() {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_role WHERE fld_role_id NOT IN (4,5) ORDER BY fld_role ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getgeneratelink($getvalue) {
		try
		{
			$sql = "SELECT * FROM  tbl_role WHERE fld_role_id IN ($getvalue) ORDER BY fld_role ASC";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdonorrole() {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_role WHERE fld_role_id = '4' ORDER BY fld_role ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getparticipantrole() {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_role WHERE fld_role_id = '5' ORDER BY fld_role ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getbyzipcode($country_short, $city) {
		try
		{
			$stmt = $this->db->prepare("SELECT c.id AS cityid, c.name AS cityname, b.id AS stateid, b.name AS statename, a.id AS countryid, a.name AS countryname FROM countries a INNER JOIN states b ON b.country_id = a.id INNER JOIN cities c ON c.state_id = b.id WHERE a.sortname = '$country_short' AND c.name = '$city'");
			$stmt->execute();
			$zipcodeRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $zipcodeRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getproles() {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_role WHERE fld_role_pid = 0 ORDER BY fld_role ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getroledetail($id) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_role WHERE fld_role_id =:id");
			$stmt->execute(array(':id' => $id));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_role($sRolePId, $sRole, $iId, $iRights) {
		try
		{
			$stmt = $this->db->prepare("UPDATE tbl_role SET
			fld_role=:sRole,
			fld_role_pid=:sRolePId,
			fld_rights=:iRights
			WHERE fld_role_id=:iId");
			$stmt->bindparam(":sRole", $sRole);
			$stmt->bindparam(":sRolePId", $sRolePId);
			$stmt->bindparam(":iRights", $iRights);
			$stmt->bindparam(":iId", $iId);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function send_sms($number, $body) {

		$ID = TWILIO_ID;

		$token = TWILIO_TOKEN;

		$twilio_number = TWILIO_PHONE_NUMBER;

		$url = 'https://api.twilio.com/2010-04-01/Accounts/' . $ID . '/Messages.json';

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		curl_setopt($ch, CURLOPT_USERPWD, $ID . ':' . $token);

		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS,

			'To=' . rawurlencode('+' . $number) .

			'&From=' . rawurlencode($twilio_number) .

			'&Body=' . rawurlencode($body));

		$resp = curl_exec($ch);

		curl_close($ch);

		return json_decode($resp, true);

	}
	public function update_role_status($sStatus, $iId) {
		try
		{
			$stmt = $this->db->prepare("UPDATE tbl_role SET fld_role_status=:sStatus WHERE fld_role_id=:iId");
			$stmt->bindparam(":sStatus", $sStatus);
			$stmt->bindparam(":iId", $iId);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getmodules() {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_module ORDER BY fld_module ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getcountry() {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  countries ORDER BY name ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getstate($iCID) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  states WHERE country_id = '" . $iCID . "' ORDER BY name ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getstate2($iCID) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  states WHERE country_short_name = '" . $iCID . "' ORDER BY name ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getcity($iSID) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  cities WHERE state_name = '" . $iSID . "' ORDER BY name ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getsettingsdetail($id) {
		try
		{
			$stmt = $this->db->prepare("SELECT * FROM  tbl_global_settings WHERE fld_gid =:id");
			$stmt->execute(array(':id' => $id));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_settings($sDCom, $sRepCom, $sComm, $sDLevel1, $sDLevelAmt1, $sDLevel2, $sDLevelAmt2, $sDLevel3, $sDLevelAmt3) {
		try
		{
			/*$stmt = $this->db->prepare("UPDATE  tbl_global_settings SET  fld_distributor_com=:sDCom,fld_rep_com=:sRepCom,fld_commision=:sComm,fld_donation_level1=:sDLevel1,		fld_donation_level1_amt=:sDLevelAmt1,fld_donation_level2=:sDLevel2,fld_donation_level2_amt=:sDLevelAmt2,fld_donation_level3=:sDLevel3 ,fld_donation_level3_amt=:sDLevelAmt3			WHERE fld_gid=:iId");
				$stmt->bindparam(":sDCom", $sDCom);
				$stmt->bindparam(":sRepCom", $sRepCom);
				$stmt->bindparam(":sComm", $sComm);
				$stmt->bindparam(":sDLevel1", $sDLevel1);
				$stmt->bindparam(":sDLevelAmt1", $sDLevelAmt1);
				$stmt->bindparam(":sDLevel2", $sDLevel2);
				$stmt->bindparam(":sDLevelAmt2", $sDLevelAmt2);
				$stmt->bindparam(":sDLevel3", $sDLevel3);
				$stmt->bindparam(":sDLevelAmt3", $sDLevelAmt3);
			*/
			$stmt = $this->db->prepare("UPDATE  tbl_global_settings SET  fld_distributor_com='" . $sDCom . "',fld_rep_com='" . $sRepCom . "',fld_commision='" . $sComm . "',fld_donation_level1='" . $sDLevel1 . "',fld_donation_level1_amt='" . $sDLevelAmt1 . "',fld_donation_level2='" . $sDLevel2 . "',fld_donation_level2_amt='" . $sDLevelAmt2 . "',fld_donation_level3='" . $sDLevel3 . "' ,fld_donation_level3_amt='" . $sDLevelAmt3 . "'	WHERE fld_gid='1'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	####################### Global Settimgs #################
	public function getsettings($roleid, $userid) {
		try
		{
			if ($roleid == 1) {
				$stmt = $this->db->prepare("SELECT * FROM tbl_gsettings_user gs INNER JOIN tbl_users u ON u.fld_uid = gs.fld_userid AND u.fld_role_id = gs.fld_roleid ORDER BY fld_gtitle ASC");
			} else {
				$stmt = $this->db->prepare("SELECT * FROM tbl_gsettings_user gs INNER JOIN tbl_users u ON u.fld_uid = gs.fld_userid AND u.fld_role_id = gs.fld_roleid WHERE gs.fld_userid = '$userid' ORDER BY fld_gtitle ASC");
			}
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_settingsdetail($id) {
		try
		{
			$stmt = $this->db->prepare("SELECT gs.*, u.fld_name, u.fld_lname FROM tbl_gsettings_user gs INNER JOIN tbl_users u ON gs.fld_userid = u.fld_uid WHERE fld_gid =:id");
			$stmt->execute(array(':id' => $id));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function insert_gsettings($sTitle, $sCode, $sValue) {
		try
		{
			//echo $sRole.'----'.$sRolePid;
			$stmt = $this->db->prepare("INSERT INTO tbl_gsettings_user(fld_gtitle,fld_gcode,fld_gstatus,fld_gvalue) VALUES('" . $sTitle . "','" . $sCode . "','1','" . $sValue . "')");
			$stmt->execute();
			return $this->db->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function update_gsettings($sGID, $sValue) {
		try
		{
			$stmt = $this->db->prepare("UPDATE tbl_gsettings_user SET
			fld_gvalue=:sValue
			WHERE fld_gid=:iId");

			$stmt->bindparam(":sValue", $sValue);
			$stmt->bindparam(":iId", $sGID);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function update_gsettings_status($sStatus, $iId) {
		try
		{
			$stmt = $this->db->prepare("UPDATE tbl_gsettings SET fld_gstatus=:sStatus WHERE fld_gid=:iId");
			$stmt->bindparam(":sStatus", $sStatus);
			$stmt->bindparam(":iId", $iId);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function delete_gsettings($id) {
		try
		{
			$stmt = $this->db->prepare("DELETE from tbl_gsettings WHERE fld_gid = '" . $id . "'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	######################################################
	public function is_loggedin() {
		if (isset($_SESSION['user_session'])) {
			return true;
		}
	}
	public function redirect($url) {
		header("Location: $url");
	}
	public function getbrand($ref) {
		try
		{
			$stmt1 = $this->db->prepare("SELECT * FROM tbl_generated_link WHERE gkeyhash = '$ref'");
			$stmt1->execute();
			if ($stmt1->rowCount() == 1) {
				$refRow = $stmt1->fetch(PDO::FETCH_ASSOC);
				$groleid = $refRow['groleid'];
				$userid = $refRow['uid'];
				$usedbyuid = $refRow['usedbyuid'];
				if ($groleid == 6) {
					$stmt2 = $this->db->prepare("SELECT * FROM tbl_generated_link WHERE usedbyuid = '$userid' AND groleid = '3'");
					$stmt2->execute();
					$refRow = $stmt2->fetch(PDO::FETCH_ASSOC);
					$groleid = $refRow['groleid'];
					$userid = $refRow['uid'];
					$usedbyuid = $refRow['usedbyuid'];

					$stmt1 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_uid = '$usedbyuid'");
					$stmt1->execute();
					$userRow = $stmt1->fetch(PDO::FETCH_ASSOC);
					return $userRow;

				} elseif ($groleid == 2) {
					$stmt1 = $this->db->prepare("SELECT * FROM tbl_generated_link WHERE usedbyuid = '$userid' AND groleid = '6'");
					$stmt1->execute();
					if ($stmt1->rowCount() > 0) {
						$refRow = $stmt1->fetch(PDO::FETCH_ASSOC);
						$groleid = $refRow['groleid'];
						$userid = $refRow['uid'];
						$usedbyuid = $refRow['usedbyuid'];
					}

					$stmt2 = $this->db->prepare("SELECT * FROM tbl_generated_link WHERE usedbyuid = '$userid' AND groleid = '3'");
					$stmt2->execute();
					if ($stmt2->rowCount() > 0) {
						$refRow = $stmt2->fetch(PDO::FETCH_ASSOC);
						$groleid = $refRow['groleid'];
						$userid = $refRow['uid'];
						$usedbyuid = $refRow['usedbyuid'];
					}

					$stmt1 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_uid = '$usedbyuid'");
					$stmt1->execute();
					$userRow = $stmt1->fetch(PDO::FETCH_ASSOC);
					return $userRow;
				} elseif ($groleid == 3) {
					$stmt1 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_uid = '$userid'");
					$stmt1->execute();
					$userRow = $stmt1->fetch(PDO::FETCH_ASSOC);
					return $userRow;
				}
			} else {
				return 1;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getbrand1($cid) {
		try
		{
			if (strpos($cid, '|') !== false) {
				$cid3 = explode("|", $cid);
				if ($cid3[1] != '') {
					$cid = $cid3[1];
				} else {
					$cid = $cid3[0];
				}
			} 
			
			$stmt1 = $this->db->prepare("SELECT u.*
			FROM
			tbl_campaign a
			INNER JOIN tbl_tree tr ON tr.uid = a.fld_uid
			INNER JOIN tbl_users u ON (u.fld_uid = tr.did OR u.fld_uid = tr.uid )
			WHERE (a.fld_hashcamp = '$cid' OR a.fld_campaign_id = '$cid' OR u.fld_uid = '$cid' ) AND u.fld_cname <> '' LIMIT 1");
			$stmt1->execute();
			if ($stmt1->rowCount() == 1) {
				$refRow = $stmt1->fetch(PDO::FETCH_ASSOC);
				return $refRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getbrandbyuid($cid) {
		try
		{
			if (strpos($cid, '|') !== false) {
				$cid3 = explode("|", $cid);
				if ($cid3[1] != '') {
					$cid = $cid3[1];
				} else {
					$cid = $cid3[0];
				}
			}
			
			$stmt1 = $this->db->prepare("SELECT *
			FROM
			tbl_users
			WHERE fld_uid = '$cid' AND fld_role_id = 3 LIMIT 1");
			$stmt1->execute();
			if ($stmt1->rowCount() == 1) {
				$refRow = $stmt1->fetch(PDO::FETCH_ASSOC);
				return $refRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getbrand2($uid) {
		try
		{
			$stmt1 = $this->db->prepare("SELECT u.*
			FROM  tbl_tree tr
			INNER JOIN tbl_users u ON (u.fld_uid = tr.did OR u.fld_uid = tr.uid)
			WHERE (tr.uid = '$uid' OR tr.rid = '$uid' OR did = '$uid') AND u.fld_cname <> '' LIMIT 1 ");
			$stmt1->execute();
			if ($stmt1->rowCount() == 1) {
				$refRow = $stmt1->fetch(PDO::FETCH_ASSOC);
				return $refRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function check_cc($cc, $extra_check = false) {
		$cards = array(
			"visa" => "(4\d{12}(?:\d{3})?)",
			"amex" => "(3[47]\d{13})",
			"jcb" => "(35[2-8][89]\d\d\d{10})",
			"maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",
			"solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",
			"mastercard" => "(5[1-5]\d{14})",
			"switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",
		);
		$names = array("Visa", "American Express", "JCB", "Maestro", "Solo", "Mastercard", "Switch");
		$matches = array();
		$pattern = "#^(?:" . implode("|", $cards) . ")$#";
		$result = preg_match($pattern, str_replace(" ", "", $cc), $matches);
		if ($extra_check && $result > 0) {
			$result = (validatecard($cc)) ? 1 : 0;
		}
		return ($result > 0) ? $names[sizeof($matches) - 2] : false;
	}
	public function getbranddetail($roleid, $id) {
		try
		{
			if ($roleid == 3) {
				//Distributor
				$stmt = $this->db->prepare("SELECT * FROM  tbl_users WHERE fld_uid=:id");
			} elseif ($roleid == 5) {
				//
				$stmt = $this->db->prepare("SELECT u.*
				FROM tbl_participants_details p
				INNER JOIN tbl_tree tr ON tr.uid = p.cuid
				INNER JOIN tbl_users u ON tr.did = u.fld_uid
				WHERE p.uid=:id
				ORDER BY tr.nid ASC
				LIMIT 1");
			} else {
				//Rest of Roles
				$stmt = $this->db->prepare("SELECT u.*
				FROM tbl_tree tr
				INNER JOIN tbl_users u ON tr.did = u.fld_uid
				WHERE tr.uid=:id
				ORDER BY tr.nid ASC
				LIMIT 1");
			}
			$stmt->execute(array(':id' => $id));
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	
	public function checkdonoremail($email, $cid, $uid) {
		try
		{
			if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_donors_details WHERE uemail = '$email' AND cid = '$cid'");
			} else {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_donors_details WHERE uemail = '$email' AND cid = '$cid' AND puid='$uid'");
			}
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function checkparticipantemail($email, $cid, $uid) {
		try
		{
			if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_participants_details WHERE uemail = '$email' AND cid = '$cid'");
			} else {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_participants_details WHERE uemail = '$email' AND cid = '$cid' AND cuid='$uid'");
			}
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function checkdonorphone($phone, $cid, $uid) {
		try
		{
			if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_donors_details WHERE uphone = '$phone' AND cid = '$cid'");
			} else {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_donors_details WHERE uphone = '$phone' AND cid = '$cid' AND puid='$uid'");
			}
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function checkparticipantphone($phone, $cid, $uid) {
		try
		{
			if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_participants_details WHERE uphone = '$phone' AND cid = '$cid'");
			} else {
				$stmt = $this->db->prepare("SELECT * FROM  tbl_participants_details WHERE uphone = '$phone' AND cid = '$cid' AND cuid='$uid'");
			}
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
			else {
				return [];
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function logout() {
		session_destroy();
		unset($_SESSION['user_session']);
		return true;
	}

}
?>