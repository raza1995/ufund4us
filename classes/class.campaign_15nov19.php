<?php
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use SparkPost\SparkPost;
class CAMPAIGN {
	private $db;
	function __construct($DB_con) {
		$this->db = $DB_con;
	}
	public function getparticipantreport($uid, $rid) {
		try {
			if ($rid == 1) {
				//role id 1
				$stmt = $this->db->prepare("SELECT b.fld_campaign_id, b.fld_campaign_title, a.uid, a.uname, a.ulname, b.fld_donor_size AS donorrequire,  up.fld_image,
				(SELECT COUNT(e.puid) FROM tbl_donors_details e WHERE e.cid = a.cid AND e.puid = a.uid) AS donorupload,
				(SELECT COUNT(f.donation_amount) FROM tbl_donations f WHERE f.cid = a.cid AND f.refferal_by = a.uid AND f.mode = '1') AS donation_amount,
				(SELECT SUM(g.donation_amount) FROM tbl_donations g WHERE g.cid = a.cid AND g.refferal_by = a.uid AND g.mode = '1') AS sumofdonations,
				b.fld_participant_goal AS participantgoal
				FROM tbl_participants_details a
				LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
				LEFT JOIN tbl_users up ON up.fld_uid = a.uid
				GROUP BY a.uid");
			} else {
				$stmt = $this->db->prepare("SELECT b.fld_campaign_id, b.fld_campaign_title, a.uid, a.uname, a.ulname, b.fld_donor_size AS donorrequire,  up.fld_image,
				(SELECT COUNT(e.puid) FROM tbl_donors_details e WHERE e.cid = a.cid AND e.puid = a.uid) AS donorupload,
				(SELECT COUNT(f.donation_amount) FROM tbl_donations f WHERE f.cid = a.cid AND f.refferal_by = a.uid AND f.mode = '1') AS donation_amount,
				(SELECT SUM(g.donation_amount) FROM tbl_donations g WHERE g.cid = a.cid AND g.refferal_by = a.uid AND g.mode = '1') AS sumofdonations,
				b.fld_participant_goal AS participantgoal
				FROM tbl_participants_details a
				LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
				LEFT JOIN tbl_users up ON up.fld_uid = a.uid
				WHERE b.fld_uid = '$uid' AND b.fld_ab1575_pupil_fee = 0
				GROUP BY a.uid");
			}
			$stmt->execute();
			$donationRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			/*if($stmt->rowCount() > 0)
				{
			*/
			return $donationRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getcampaignmanagerreport($uid, $rid) {
		try {
			if ($rid == 1) {
				//role id 1
				$stmt = $this->db->prepare("SELECT a.*, DATEDIFF(a.fld_campaign_edate, CURDATE()) AS daysleft,
				(SELECT IFNULL(COUNT(d.cid),0) FROM tbl_participants_details d WHERE d.cid = a.fld_campaign_id) AS participant_count,
				(SELECT IFNULL(COUNT(b.cid),0) FROM tbl_donations b WHERE b.cid = a.fld_campaign_id AND b.mode = '1') AS donation_count,
				(SELECT IFNULL(SUM(c.donation_amount),0) FROM tbl_donations c WHERE c.cid = a.fld_campaign_id AND c.mode = '1') AS donation_sum,
				(SELECT IFNULL(COUNT(e.cid),0) FROM tbl_donors_details e WHERE e.cid = a.fld_campaign_id) AS donors_count
				FROM tbl_campaign a
				WHERE a.fld_active = 1
				ORDER BY a.fld_campaign_id DESC");
			} else {
				$stmt = $this->db->prepare("SELECT a.*, DATEDIFF(a.fld_campaign_edate, CURDATE()) AS daysleft,
				(SELECT IFNULL(COUNT(d.cid),0) FROM tbl_participants_details d WHERE d.cid = a.fld_campaign_id) AS participant_count,
				(SELECT IFNULL(COUNT(b.cid),0) FROM tbl_donations b WHERE b.cid = a.fld_campaign_id AND b.mode = '1') AS donation_count,
				(SELECT IFNULL(SUM(c.donation_amount),0) FROM tbl_donations c WHERE c.cid = a.fld_campaign_id AND c.mode = '1') AS donation_sum,
				(SELECT IFNULL(COUNT(e.cid),0) FROM tbl_donors_details e WHERE e.cid = a.fld_campaign_id) AS donors_count
				FROM tbl_campaign a
				WHERE a.fld_active = 1 AND a.fld_uid = '$uid'
				ORDER BY a.fld_campaign_id DESC");
			}
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getrepresentativereport($uid, $rid) {
		try {
			if ($rid == 1) {
				//role id 1
				$stmt = $this->db->prepare("SELECT a.*, DATEDIFF(a.fld_campaign_edate, CURDATE()) AS daysleft,
				(SELECT IFNULL(COUNT(b.cid),0) FROM tbl_donations b WHERE b.cid = a.fld_campaign_id AND b.mode = '1') AS donation_count,
				(SELECT IFNULL(SUM(c.donation_amount),0) FROM tbl_donations c WHERE c.cid = a.fld_campaign_id AND c.mode = '1') AS donation_sum,
				(SELECT fld_rep_com FROM tbl_global_settings) AS repcomm
				FROM tbl_campaign a
				INNER JOIN tbl_tree d ON d.uid = a.fld_uid
				WHERE a.fld_active = 1
				ORDER BY a.fld_campaign_id DESC");
			} else {
				$stmt = $this->db->prepare("SELECT a.*, DATEDIFF(a.fld_campaign_edate, CURDATE()) AS daysleft,
				(SELECT IFNULL(COUNT(b.cid),0) FROM tbl_donations b WHERE b.cid = a.fld_campaign_id AND b.mode = '1') AS donation_count,
				(SELECT IFNULL(SUM(c.donation_amount),0) FROM tbl_donations c WHERE c.cid = a.fld_campaign_id AND c.mode = '1') AS donation_sum,
				(SELECT fld_rep_com FROM tbl_global_settings) AS repcomm
				FROM tbl_campaign a
				INNER JOIN tbl_tree d ON d.uid = a.fld_uid
				WHERE d.rid = '$uid' AND a.fld_active = 1
				ORDER BY a.fld_campaign_id DESC");
			}
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getmanagedonors2($sName, $sId, $sRoleId) {
		try {
			if ($sRoleId == 1) {
				$stmt = $this->db->prepare("SELECT a.ufname AS donorfname, a.ulname AS donorlname, b.fld_campaign_title AS campaignname, b.fld_campaign_id AS campaignno, c.uname AS participantfname, c.ulname AS participantlname, IFNULL(a.donation_amount, '0.00') AS amount, DATE_FORMAT(a.creationdate, '%m/%d/%Y') AS donationdate, a.id AS transactionno
                FROM tbl_donations a
                INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
                INNER JOIN tbl_participants_details c ON a.refferal_by = c.uid
				WHERE a.mode = '1'
				GROUP BY b.fld_campaign_id
                ORDER BY a.cid, a.uid ASC");
			} else {
				$stmt = $this->db->prepare("SELECT a.ufname AS donorfname, a.ulname AS donorlname, b.fld_campaign_title AS campaignname, b.fld_campaign_id AS campaignno, c.uname AS participantfname, c.ulname AS participantlname, IFNULL(a.donation_amount, '0.00') AS amount, DATE_FORMAT(a.creationdate, '%m/%d/%Y') AS donationdate, a.id AS transactionno
                FROM tbl_donations a
                INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
                INNER JOIN tbl_participants_details c ON a.refferal_by = c.uid AND c.cid = a.cid
                WHERE a.refferal_by = '$sId' AND a.mode = '1'
				-- GROUP BY b.fld_campaign_id
                ORDER BY a.cid, a.uid ASC");
			}
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	//Start Newsletters
	public function emails_send_newsletter($checked1, $fld_smode_test_email, $id, $ffld_label, $ffld_role, $ffld_content, $ffld_status) {
		try {
			try {
				//SparkPost Init
				$httpClient = new GuzzleAdapter(new Client());
				$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
				//SparkPost Init
				$sparky->setOptions(['async' => false]);
				if ($checked1 == 1) {
					$stmt = $this->db->prepare("SELECT fld_uid, fld_email, fld_name, fld_lname FROM tbl_users WHERE fld_role_id = $id");
					$stmt->execute();
					if ($stmt->rowCount() > 0) {
						while ($emails_dataset = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$promise = $sparky->transmissions->post([
								'content' => [
									'from' => [
										'name' => 'UFund4Us',
										'email' => 'info@ufund4us.com',
									],
									'subject' => $ffld_label,
									'html' => $ffld_content,
								],
								'recipients' => [
									[
										'address' => [
											'name' => trim($emails_dataset['fld_name']) . " " . trim($emails_dataset['fld_lname']),
											'email' => trim($emails_dataset['fld_email']),
										],
									],
								],
								'cc' => [
									[
										'address' => [
											'name' => 'Ufund4Us',
											'email' => 'emails@ufund4us.com',
										],
									],
								],
							]);
						}
					}
				} else {
					$promise = $sparky->transmissions->post([
						'content' => [
							'from' => [
								'name' => 'UFund4Us',
								'email' => 'info@ufund4us.com',
							],
							'subject' => $ffld_label,
							'html' => $ffld_content,
						],
						'recipients' => [
							[
								'address' => [
									'name' => 'Test Email (UFund4Us)',
									'email' => trim($fld_smode_test_email),
								],
							],
						],
						'cc' => [
							[
								'address' => [
									'name' => 'Ufund4Us',
									'email' => 'emails@ufund4us.com',
								],
							],
						],
					]);
				}
				return;
			} catch (\Exception $e) {
				echo $e->getCode() . "\n";
				echo $e->getMessage() . "\n";
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getnewsletters() {
		try {
			$stmt = $this->db->prepare("SELECT a.*, DATE_FORMAT(a.creation_date,'%m/%d/%Y %r') AS creation_date, b.fld_role
            FROM tbl_newsletters a
			INNER JOIN tbl_role b ON b.fld_role_id = a.role
			ORDER BY a.label ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function delete_newsletter($id) {
		try {
			$stmt = $this->db->prepare("DELETE from tbl_newsletters WHERE id = '" . $id . "'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_newsletter($sStatus, $iId) {
		try {
			$stmt = $this->db->prepare("UPDATE tbl_newsletters SET status=:sStatus WHERE id=:iId");
			$stmt->bindparam(":sStatus", $sStatus);
			$stmt->bindparam(":iId", $iId);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_newsletter_content($id, $fld_label, $fld_role, $fld_content, $fld_status) {
		try {
			$stmt = $this->db->prepare("UPDATE tbl_newsletters SET label=:Label, role=:Role, content=:Content, status=:Status WHERE id=:Id");
			$stmt->bindparam(":Label", $fld_label);
			$stmt->bindparam(":Role", $fld_role);
			$stmt->bindparam(":Content", $fld_content);
			$stmt->bindparam(":Status", $fld_status);
			$stmt->bindparam(":Id", $id);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_newsletter_content($fld_label, $fld_role, $fld_content, $fld_status) {
		try {
			$stmt = $this->db->prepare("INSERT INTO tbl_newsletters (label,role,content,status,creation_date) VALUES (:Label, :Role, :Content, :Status, NOW())");
			$stmt->bindparam(":Label", $fld_label);
			$stmt->bindparam(":Role", $fld_role);
			$stmt->bindparam(":Content", $fld_content);
			$stmt->bindparam(":Status", $fld_status);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function fetch_newsletter_content($id) {
		try {
			$stmt = $this->db->prepare("SELECT a.*, DATE_FORMAT(a.creation_date,'%m/%d/%Y %r') AS creation_date, b.fld_role
            FROM tbl_newsletters a
			INNER JOIN tbl_role b ON b.fld_role_id = a.role
			WHERE a.id = $id");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function emails_newsletter($id) {
		try {
			$stmt = $this->db->prepare("SELECT fld_uid, fld_email, fld_name, fld_lname
            FROM tbl_users
			WHERE fld_role_id = $id");
			$stmt->execute();
			$userRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	//End Newsletters
	public function getinvitations($cid, $sId, $todays) {
		try {
			$stmt = $this->db->prepare("SELECT *
            FROM tbl_donors_invitation
            WHERE cid = '$cid' AND pid = '$sId' AND expiredatetime >= '$todays'
			ORDER BY id ASC LIMIT 5");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_invitations($p_cid, $p_pid, $p_email, $p_fname, $p_lname, $r_fname, $r_lname, $r_to, $r_cc, $r_msg, $todays, $expirydate, $generatedlink) {
		try {
			$recipients_fullname = $r_fname . " " . $r_lname;
			$stmt = $this->db->prepare("INSERT INTO tbl_donors_invitation (cid, pid, pfname, plname, pemail, revfname, revlname, revemailto, revemailcc,
			datetime, expiredatetime, linkgenerate) VALUES ('$p_cid', '$p_pid', '$p_fname', '$p_lname', '$p_email', '$r_fname', '$r_lname', '$r_to', '$r_cc', '$todays', '$expirydate', '$generatedlink')");
			if ($stmt->execute()) {
				$lastid = $this->db->lastInsertId();
				$stmt = $this->db->prepare("SELECT u.fld_email AS pemail, u.fld_name AS pname, u.fld_lname AS plname, u.fld_phone AS pphone, u.fld_uid, u.fld_image, CONCAT(b.fld_cname,' ',IFNULL(b.fld_clname,'')) AS fld_cname, b.fld_cemail, b.fld_cphone, b.fld_campaign_title, b.fld_campaign_logo, b.fld_campaign_id, b.fld_organization_name, b.fld_campaign_edate, b.fld_donor_size
				FROM tbl_participants_details a
				INNER JOIN tbl_users u ON a.uid = u.fld_uid
				INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
				WHERE a.cid = '$p_cid' AND a.uid='$p_pid' AND b.fld_campaign_edate >= NOW()");
				$stmt->execute();
				$email_manager_Row = $stmt->fetch(PDO::FETCH_ASSOC);
				//Find Distributor ID
				$stmt90 = $this->db->prepare("SELECT u.*
				FROM tbl_campaign c
				INNER JOIN tbl_tree tr ON c.fld_uid = tr.uid
				INNER JOIN tbl_users u ON u.fld_uid = tr.did
				WHERE c.fld_campaign_id = '$p_cid'
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
					$is_Company_Title = 0;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				$uid = $email_manager_Row['fld_uid'];
				$cname = $email_manager_Row['fld_cname'];
				$cemail = $email_manager_Row['fld_cemail'];
				$cphone = $email_manager_Row['fld_cphone'];
				$ctitle = $email_manager_Row['fld_campaign_title'];
				$corganization = $email_manager_Row['fld_organization_name'];
				$clogo = $email_manager_Row['fld_campaign_logo'];
				$uimage = $email_manager_Row['fld_image'];
				$pname = $email_manager_Row['pname'];
				$plname = $email_manager_Row['plname'];
				$pphone = $email_manager_Row['pphone'];
				$pemail = $email_manager_Row['pemail'];
				$campid = $email_manager_Row['fld_campaign_id'];
				$emailsrequired = $email_manager_Row['fld_donor_size'];
				$fld_enddate1 = date('Y-m-d', strtotime($email_manager_Row['fld_campaign_edate']));
				$current_date1 = date("Y-m-d H:i:s");
				$from = date_create($fld_enddate1 . " 23:59:59");
				$to = date_create($current_date1);
				$diff = date_diff($to, $from);
				$TimeLeft = $diff->format('%a Days, %H Hours');
				if ($clogo != '') {
					$shlogo = $clogo;
					$Is_Campaign_Logo = true;
				} else {
					$shlogo = $ctitle;
					$Is_Campaign_Logo = false;
				}
				if ($uimage != '') {
					$uhlogo = $uimage;
					$Is_ParticipantImage = true;
				} else {
					$uhlogo = '';
					$Is_ParticipantImage = false;
				}
				//SparkPost Insert Participant Generate Link
				try {
					//SparkPost Init
					$httpClient = new GuzzleAdapter(new Client());
					$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
					//SparkPost Init
					$sparky->setOptions(['async' => false]);
					if ($r_cc != '') {
						$promise = $sparky->transmissions->post([
							'content' => ['template_id' => 'participant-generate-link'],
							'substitution_data' => [
								"sHOMECMS" => sHOMECMS,
								"sHOME" => sHOME,
								"Is_Campaign_Logo" => $Is_Campaign_Logo,
								"Campaign_Logo" => $shlogo,
								"Is_ParticipantImage" => $Is_ParticipantImage,
								"ParticipantImage" => $uhlogo,
								"RevFName" => $r_fname,
								"RevLName" => $r_lname,
								"Campaign_Id" => $campid,
								"Campaign_Title" => $ctitle,
								"Campaign_Organization" => $corganization,
								"ParticipantId" => $p_pid,
								"ParticipantFName" => $pname,
								"ParticipantLName" => $plname,
								"TimeLeft" => $TimeLeft,
								"Subject" => "$pname $plname Needs your help!!!",
								"Emails_Required" => $emailsrequired,
								"ParticipantPhone" => $pphone,
								"LinkRef" => $generatedlink,
								"fromemail" => "info@ufund4us.com",
								"refimage" => $refimage,
								"is_refimage" => $is_refimage,
								"Company_Title" => $Company_Title,
								"is_Company_Title" => $is_Company_Title,
							],
							'description' => "Participant Generate Link",
							'metadata' => [
								'Campaign_ID' => "$campid",
								'Campaign_Name' => "$ctitle",
								'Subject' => "$pname $plname Needs your help!!!",
							],
							'recipients' => [
								[
									'address' => [
										'name' => $recipients_fullname,
										'email' => $r_to,
									],
								],
							],
							'cc' => [
								[
									'address' => [
										'name' => $recipients_fullname,
										'email' => $r_cc,
									],
								],
							],
						]);
					} else {
						$promise = $sparky->transmissions->post([
							'content' => ['template_id' => 'participant-generate-link'],
							'substitution_data' => [
								"sHOMECMS" => sHOMECMS,
								"sHOME" => sHOME,
								"Is_Campaign_Logo" => $Is_Campaign_Logo,
								"Campaign_Logo" => $shlogo,
								"Is_ParticipantImage" => $Is_ParticipantImage,
								"ParticipantImage" => $uhlogo,
								"RevFName" => $r_fname,
								"RevLName" => $r_lname,
								"Campaign_Id" => $campid,
								"Campaign_Title" => $ctitle,
								"Campaign_Organization" => $corganization,
								"ParticipantId" => $p_pid,
								"ParticipantFName" => $pname,
								"ParticipantLName" => $plname,
								"TimeLeft" => $TimeLeft,
								"Subject" => "$pname $plname Needs your help!!!",
								"Emails_Required" => $emailsrequired,
								"ParticipantPhone" => $pphone,
								"LinkRef" => $generatedlink,
								"fromemail" => "info@ufund4us.com",
								"refimage" => $refimage,
								"is_refimage" => $is_refimage,
								"Company_Title" => $Company_Title,
								"is_Company_Title" => $is_Company_Title,
							],
							'description' => "Participant Generate Link",
							'metadata' => [
								'Campaign_ID' => "$campid",
								'Campaign_Name' => "$ctitle",
								'Subject' => "$pname $plname Needs your help!!!",
							],
							'recipients' => [
								[
									'address' => [
										'name' => $recipients_fullname,
										'email' => $r_to,
									],
								],
							],
						]);
					}
					$transmissionid = $promise->getBody()['results']['id'];
					$stmt55 = $this->db->prepare("UPDATE tbl_donors_invitation SET emailsent = 1 WHERE id = '$lastid'");
					$stmt55->execute();
				} catch (\Exception $e) {
					echo $e->getCode() . "\n";
					echo $e->getMessage() . "\n";
				}
				return $lastid;
			} else {
				return 0;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getmanagedonors($sName, $sId, $sRoleId) {
		try {
			if ($sRoleId == 1) {
				$stmt = $this->db->prepare("SELECT b.*,a.uid, DATEDIFF(b.fld_campaign_edate, CURDATE()) AS daysleft,
                        (SELECT COUNT(id) FROM tbl_donors_details c WHERE c.cid = a.cid AND c.puid = '$sId') AS NoOfDonors
                        FROM tbl_participants_details a
                        INNER JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
                        GROUP BY b.fld_campaign_id
                        ORDER BY b.fld_campaign_id ASC");
			} else {
				$stmt = $this->db->prepare("SELECT b.*,a.uid, DATEDIFF(b.fld_campaign_edate, CURDATE()) AS daysleft,
                        (SELECT COUNT(id) FROM tbl_donors_details c WHERE c.cid = a.cid AND c.puid = '$sId') AS NoOfDonors
                        FROM tbl_participants_details a
                        INNER JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
                        WHERE a.uid = '$sId'
                        GROUP BY b.fld_campaign_id
                        ORDER BY b.fld_campaign_id ASC");
			}
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	//Manage Payments
	public function getpayments($uid, $rid) {
		try {
			$stmt = $this->db->prepare("SELECT tid, a.cid, c.fld_campaign_title AS ctitle, COALESCE(a.cmfname, ' ', a.cmlname) AS cmname, DATE_FORMAT(a.creationdate, '%m/%d/%Y %h:%i:%s') AS tdate,
			a.uid AS did, a.ufname, a.ulname, a.uemail, a.donation_amount, a.card_number, a.payment_through, DATE_FORMAT(c.fld_campaign_sdate, '%m/%d/%Y') AS sdate, DATE_FORMAT(c.fld_campaign_edate, '%m/%d/%Y') AS edate,
			a.refferal_by AS pid, b.fld_name AS pfname, b.fld_lname AS plname, b.fld_email AS pemail, a.mode, c.fld_bank_accno AS cac
			FROM tbl_donations a
			LEFT JOIN tbl_users b ON a.refferal_by = b.fld_uid
			LEFT JOIN tbl_campaign c ON a.cid = c.fld_campaign_id
			WHERE a.mode = '1'
			ORDER BY a.id DESC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getpayments0($uid, $rid) {
		try {
			$stmt = $this->db->prepare("SELECT a.tid, a.disputeid, a.cid, c.fld_campaign_title AS ctitle, COALESCE(a.cmfname, ' ', a.cmlname) AS cmname, DATE_FORMAT(a.creationdate, '%m/%d/%Y %h:%i:%s') AS tdate,
			a.uid AS did, a.ufname, a.ulname, a.uemail, a.donation_amount, a.card_number, a.payment_through, DATE_FORMAT(c.fld_campaign_sdate, '%m/%d/%Y') AS sdate, DATE_FORMAT(c.fld_campaign_edate, '%m/%d/%Y') AS edate,
			a.refferal_by AS pid, b.fld_name AS pfname, b.fld_lname AS plname, b.fld_email AS pemail, c.fld_bank_accno AS cac
			FROM tbl_donations_dispute a
			LEFT JOIN tbl_users b ON a.refferal_by = b.fld_uid
			LEFT JOIN tbl_campaign c ON a.cid = c.fld_campaign_id
			ORDER BY a.id DESC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getpayments1($uid, $rid) {
		try {
			$stmt = $this->db->prepare("SELECT a.tid, a.refundid, a.cid, c.fld_campaign_title AS ctitle, COALESCE(a.cmfname, ' ', a.cmlname) AS cmname, DATE_FORMAT(a.creationdate, '%m/%d/%Y %h:%i:%s') AS tdate,
			a.uid AS did, a.ufname, a.ulname, a.uemail, a.donation_amount, a.card_number, a.payment_through, DATE_FORMAT(c.fld_campaign_sdate, '%m/%d/%Y') AS sdate, DATE_FORMAT(c.fld_campaign_edate, '%m/%d/%Y') AS edate,
			a.refferal_by AS pid, b.fld_name AS pfname, b.fld_lname AS plname, b.fld_email AS pemail, c.fld_bank_accno AS cac
			FROM tbl_donations_refund a
			LEFT JOIN tbl_users b ON a.refferal_by = b.fld_uid
			LEFT JOIN tbl_campaign c ON a.cid = c.fld_campaign_id
			ORDER BY a.id DESC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getpayments2($uid, $rid) {
		try {
			$stmt = $this->db->prepare("SELECT tid, a.cid, c.fld_campaign_title AS ctitle, COALESCE(a.cmfname, ' ', a.cmlname) AS cmname, DATE_FORMAT(a.creationdate, '%m/%d/%Y %h:%i:%s') AS tdate,
			a.uid AS did, a.ufname, a.ulname, a.uemail, a.donation_amount, a.card_number, a.payment_through, DATE_FORMAT(c.fld_campaign_sdate, '%m/%d/%Y') AS sdate, DATE_FORMAT(c.fld_campaign_edate, '%m/%d/%Y') AS edate,
			a.refferal_by AS pid, b.fld_name AS pfname, b.fld_lname AS plname, b.fld_email AS pemail, a.mode, c.fld_bank_accno AS cac
			FROM tbl_donations a
			LEFT JOIN tbl_users b ON a.refferal_by = b.fld_uid
			LEFT JOIN tbl_campaign c ON a.cid = c.fld_campaign_id
			WHERE a.mode = 1
			ORDER BY a.id DESC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function refund_process($dname, $pname, $amountreq, $tid, $reason, $cmname, $ctitle, $cno, $cac, $cid, $pid, $did, $demail, $refund_transaction_id) {
		try {
			$transactionid = str_replace('ch_', '', $tid); //Amount Donated
			$stmt = $this->db->prepare("SELECT a.*, b.fld_campaign_title AS camptitle, pd.uname AS participantfname, pd.ulname AS participantlname
			FROM tbl_donations a
			INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
			LEFT JOIN tbl_participants_details pd ON pd.uid = a.refferal_by
			WHERE a.tid = '$transactionid'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
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
					$is_Company_Title = 0;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				//return $userRow;
				$ttid = $userRow['tid'];
				$ccid = $userRow['cid'];
				$uid = $userRow['uid'];
				$cmfname = $userRow['cmfname'];
				$cmlname = $userRow['cmlname'];
				$uemail = $userRow['uemail'];
				$uphone = $userRow['uphone'];
				//For Email Information
				$ufname = $userRow['ufname'];
				$ulname = $userRow['ulname'];
				$transaction_date = date("m/d/Y H:i:s", strtotime($userRow['donation_amount']));
				$actualdonationamount = $userRow['donation_amount'];
				$donationamount = $amountreq;
				$payment_through = $userRow['payment_through'];
				$card_number = $userRow['card_number'];
				$participantfname = $userRow['participantfname'];
				$participantlname = $userRow['participantlname'];
				$camptitle = $userRow['camptitle'];
				//For Email Information
				$payment_method = $userRow['payment_method'];
				$refferal_by = $userRow['refferal_by'];
				$ipaddress = $_SERVER['REMOTE_ADDR'];
				$stmt2 = $this->db->prepare("INSERT INTO tbl_donations_refund
				(tid,refundid,cid,uid,cmfname,cmlname,ufname,ulname,uemail,uphone,donation_amount,card_number,payment_method,payment_through,refferal_by,client_ip,creationdate)
				VALUES
				(:ttid,:refund_transaction_id,:ccid,:uid,:cmfname,:cmlname,:ufname,:ulname,:uemail,:uphone,:donationamount,:card_number,:payment_method,:payment_through,:refferal_by,:ipaddress,NOW())");
				$stmt2->bindparam(":ttid", $ttid);
				$stmt2->bindparam(":refund_transaction_id", $refund_transaction_id);
				$stmt2->bindparam(":ccid", $ccid);
				$stmt2->bindparam(":uid", $uid);
				$stmt2->bindparam(":cmfname", $cmfname);
				$stmt2->bindparam(":cmlname", $cmlname);
				$stmt2->bindparam(":ufname", $ufname);
				$stmt2->bindparam(":ulname", $ulname);
				$stmt2->bindparam(":uemail", $uemail);
				$stmt2->bindparam(":uphone", $uphone);
				$stmt2->bindparam(":donationamount", $donationamount);
				$stmt2->bindparam(":card_number", $card_number);
				$stmt2->bindparam(":payment_method", $payment_method);
				$stmt2->bindparam(":payment_through", $payment_through);
				$stmt2->bindparam(":refferal_by", $refferal_by);
				$stmt2->bindparam(":ipaddress", $ipaddress);
				$stmt2->execute();
				$stmt3 = $this->db->prepare("UPDATE tbl_donations SET mode = '2' WHERE tid = '$transactionid'");
				$stmt3->execute();
				try {
					//SparkPost Init
					$httpClient = new GuzzleAdapter(new Client());
					$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
					//SparkPost Init
					$sparky->setOptions(['async' => false]);
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'refund-donation'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"ufname" => $ufname,
							"ulname" => $ulname,
							"transaction_date" => $transaction_date,
							"actualdonationamount" => $actualdonationamount,
							"donationamount" => $donationamount,
							"payment_through" => $payment_through,
							"card_number" => $card_number,
							"participantfname" => $participantfname,
							"participantlname" => $participantlname,
							"camptitle" => $camptitle,
							"fromemail" => "info@ufund4us.com",
							"refimage" => $refimage,
							"is_refimage" => $is_refimage,
							"Company_Title" => $Company_Title,
							"is_Company_Title" => $is_Company_Title,
						],
						'description' => "$ufname $ulname requested refund",
						'metadata' => [
							'Campaign_ID' => "$ccid",
							'Campaign_Name' => "$camptitle",
							'Subject' => "$ufname $ulname requested refund",
						],
						'recipients' => [
							[
								'address' => [
									'name' => "$ufname $ulname",
									'email' => "$uemail",
								],
							],
						],
						'cc' => [
							[
								'address' => [
									'name' => "UFund4Us Administrator",
									'email' => "info@ufund4us.com",
								],
							],
						],
					]);
				} catch (\Exception $e) {
					echo $e->getCode() . "\n";
					echo $e->getMessage() . "\n";
				}
				return $stmt3;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function dispute_process($dname, $pname, $amountreq, $tid, $reason, $cmname, $ctitle, $cno, $cac, $cid, $pid, $did, $demail, $dispute_transaction_id) {
		try {
			$transactionid = str_replace('ch_', '', $tid); //Amount Donated
			$stmt = $this->db->prepare("SELECT a.*, b.fld_campaign_title AS camptitle, pd.uname AS participantfname, pd.ulname AS participantlname
			FROM tbl_donations a
			INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
			LEFT JOIN tbl_participants_details pd ON pd.uid = a.refferal_by
			WHERE a.tid = '$transactionid'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				//return $userRow;
				$ttid = $userRow['tid'];
				$ccid = $userRow['cid'];
				$uid = $userRow['uid'];
				$cmfname = $userRow['cmfname'];
				$cmlname = $userRow['cmlname'];
				$uemail = $userRow['uemail'];
				$uphone = $userRow['uphone'];
				//For Email Information
				$ufname = $userRow['ufname'];
				$ulname = $userRow['ulname'];
				$transaction_date = date("m/d/Y H:i:s", strtotime($userRow['donation_amount']));
				$actualdonationamount = $userRow['donation_amount'];
				$donationamount = $amountreq;
				$payment_through = $userRow['payment_through'];
				$card_number = $userRow['card_number'];
				$participantfname = $userRow['participantfname'];
				$participantlname = $userRow['participantlname'];
				$camptitle = $userRow['camptitle'];
				//For Email Information
				$payment_method = $userRow['payment_method'];
				$refferal_by = $userRow['refferal_by'];
				$ipaddress = $_SERVER['REMOTE_ADDR'];
				$stmt2 = $this->db->prepare("INSERT INTO tbl_donations_dispute
				(tid,disputeid,cid,uid,cmfname,cmlname,ufname,ulname,uemail,uphone,donation_amount,card_number,payment_method,payment_through,refferal_by,client_ip,creationdate)
				VALUES
				(:ttid,:dispute_transaction_id,:ccid,:uid,:cmfname,:cmlname,:ufname,:ulname,:uemail,:uphone,:donationamount,:card_number,:payment_method,:payment_through,:refferal_by,:ipaddress,NOW())");
				$stmt2->bindparam(":ttid", $ttid);
				$stmt2->bindparam(":dispute_transaction_id", $dispute_transaction_id);
				$stmt2->bindparam(":ccid", $ccid);
				$stmt2->bindparam(":uid", $uid);
				$stmt2->bindparam(":cmfname", $cmfname);
				$stmt2->bindparam(":cmlname", $cmlname);
				$stmt2->bindparam(":ufname", $ufname);
				$stmt2->bindparam(":ulname", $ulname);
				$stmt2->bindparam(":uemail", $uemail);
				$stmt2->bindparam(":uphone", $uphone);
				$stmt2->bindparam(":donationamount", $donationamount);
				$stmt2->bindparam(":card_number", $card_number);
				$stmt2->bindparam(":payment_method", $payment_method);
				$stmt2->bindparam(":payment_through", $payment_through);
				$stmt2->bindparam(":refferal_by", $refferal_by);
				$stmt2->bindparam(":ipaddress", $ipaddress);
				$stmt2->execute();
				$stmt3 = $this->db->prepare("UPDATE tbl_donations SET mode = '3' WHERE tid = '$transactionid'");
				$stmt3->execute();
				return $stmt3;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	//Manage Payments
	public function getcampaign($uid, $rid) {
		try {
			if ($rid == 1) {
				$stmt = $this->db->prepare("SELECT a.*, l.*,
				DATE_FORMAT(cfirstpaiddate, '%m/%d/%Y') AS cfirstpaiddate,
				DATE_FORMAT(csecondpaiddate, '%m/%d/%Y') AS csecondpaiddate,
				DATE_FORMAT(rpaiddate, '%m/%d/%Y') AS rpaiddate,
				DATE_FORMAT(dpaiddate, '%m/%d/%Y') AS dpaiddate,
				DATEDIFF(a.fld_campaign_edate, CURDATE()) AS daysleft,
				i.rid AS rid, i.rname AS rfname, i.rlname AS rlname, i.did, i.dname AS dfname, i.dlname, i.aid, i.aname AS afname, i.alname,
				(SELECT COUNT(b.cid) FROM tbl_participants_details b WHERE b.cid = a.fld_campaign_id) AS participantenrolled,
				(SELECT COUNT(c.cid) FROM tbl_donors_details c WHERE c.cid = a.fld_campaign_id) AS donorenrolled,
				(SELECT COUNT(d.cid) FROM tbl_donations d WHERE d.cid = a.fld_campaign_id AND d.mode = '1') AS donations,
				(SELECT IFNULL(SUM(e.donation_amount), 0.00) FROM tbl_donations e WHERE e.cid = a.fld_campaign_id AND e.mode = '1') AS moneyraised,
				(SELECT IFNULL(COUNT(f.cid),0) FROM tbl_donors_details f WHERE f.cid = a.fld_campaign_id AND f.is_unsubscribe = '1') AS donors_unsubscribe
				FROM tbl_campaign a
				LEFT JOIN tbl_tree i ON i.uid = a.fld_uid
				LEFT JOIN tbl_transaction l ON l.cid = a.fld_campaign_id
				WHERE a.fld_active = 1
				GROUP BY a.fld_campaign_id
				ORDER BY a.fld_campaign_id DESC");
			} elseif ($rid == 3) {
				$stmt = $this->db->prepare("SELECT a.*, l.*,
				DATE_FORMAT(cfirstpaiddate, '%m/%d/%Y') AS cfirstpaiddate,
				DATE_FORMAT(csecondpaiddate, '%m/%d/%Y') AS csecondpaiddate,
				DATE_FORMAT(rpaiddate, '%m/%d/%Y') AS rpaiddate,
				DATE_FORMAT(dpaiddate, '%m/%d/%Y') AS dpaiddate,
				DATEDIFF(a.fld_campaign_edate, CURDATE()) AS daysleft,
				i.rid AS rid, i.rname AS rfname, i.rlname AS rlname, i.did, i.dname AS dfname, i.dlname, i.aid, i.aname AS afname, i.alname,
				(SELECT COUNT(b.cid) FROM tbl_participants_details b WHERE b.cid = a.fld_campaign_id) AS participantenrolled,
				(SELECT COUNT(c.cid) FROM tbl_donors_details c WHERE c.cid = a.fld_campaign_id) AS donorenrolled,
				(SELECT COUNT(d.cid) FROM tbl_donations d WHERE d.cid = a.fld_campaign_id AND d.mode = '1') AS donations,
				(SELECT IFNULL(SUM(e.donation_amount), 0.00) FROM tbl_donations e WHERE e.cid = a.fld_campaign_id AND e.mode = '1') AS moneyraised,
				(SELECT IFNULL(COUNT(f.cid),0) FROM tbl_donors_details f WHERE f.cid = a.fld_campaign_id AND f.is_unsubscribe = '1') AS donors_unsubscribe
				FROM tbl_campaign a
				LEFT JOIN tbl_tree i ON i.uid = a.fld_uid
				LEFT JOIN tbl_transaction l ON l.cid = a.fld_campaign_id
				WHERE a.fld_active = 1 AND i.did = '$uid' OR i.uid = '$uid'
				GROUP BY a.fld_campaign_id
				ORDER BY a.fld_campaign_id DESC");
			} elseif ($rid == 6) {
				$stmt = $this->db->prepare("SELECT a.*, l.*,
				DATE_FORMAT(cfirstpaiddate, '%m/%d/%Y') AS cfirstpaiddate,
				DATE_FORMAT(csecondpaiddate, '%m/%d/%Y') AS csecondpaiddate,
				DATE_FORMAT(rpaiddate, '%m/%d/%Y') AS rpaiddate,
				DATE_FORMAT(dpaiddate, '%m/%d/%Y') AS dpaiddate,
				DATEDIFF(a.fld_campaign_edate, CURDATE()) AS daysleft,
				i.rid AS rid, i.rname AS rfname, i.rlname AS rlname, i.did, i.dname AS dfname, i.dlname, i.aid, i.aname AS afname, i.alname,
				(SELECT COUNT(b.cid) FROM tbl_participants_details b WHERE b.cid = a.fld_campaign_id) AS participantenrolled,
				(SELECT COUNT(c.cid) FROM tbl_donors_details c WHERE c.cid = a.fld_campaign_id) AS donorenrolled,
				(SELECT COUNT(d.cid) FROM tbl_donations d WHERE d.cid = a.fld_campaign_id AND d.mode = '1') AS donations,
				(SELECT IFNULL(SUM(e.donation_amount), 0.00) FROM tbl_donations e WHERE e.cid = a.fld_campaign_id AND e.mode = '1') AS moneyraised,
				(SELECT IFNULL(COUNT(f.cid),0) FROM tbl_donors_details f WHERE f.cid = a.fld_campaign_id AND f.is_unsubscribe = '1') AS donors_unsubscribe
				FROM tbl_campaign a
				LEFT JOIN tbl_tree i ON i.uid = a.fld_uid
				LEFT JOIN tbl_transaction l ON l.cid = a.fld_campaign_id
				WHERE a.fld_active = 1 AND i.rid = '$uid' OR i.uid = '$uid'
				GROUP BY a.fld_campaign_id
				ORDER BY a.fld_campaign_id DESC");
			} else {
				$stmt = $this->db->prepare("SELECT a.*, l.*,
				DATE_FORMAT(cfirstpaiddate, '%m/%d/%Y') AS cfirstpaiddate,
				DATE_FORMAT(csecondpaiddate, '%m/%d/%Y') AS csecondpaiddate,
				DATE_FORMAT(rpaiddate, '%m/%d/%Y') AS rpaiddate,
				DATE_FORMAT(dpaiddate, '%m/%d/%Y') AS dpaiddate,
				DATEDIFF(a.fld_campaign_edate, CURDATE()) AS daysleft,
				i.rid AS rid, i.rname AS rfname, i.rlname AS rlname, i.did, i.dname AS dfname, i.dlname, i.aid, i.aname AS afname, i.alname,
				(SELECT COUNT(b.cid) FROM tbl_participants_details b WHERE b.cid = a.fld_campaign_id) AS participantenrolled,
				(SELECT COUNT(c.cid) FROM tbl_donors_details c WHERE c.cid = a.fld_campaign_id) AS donorenrolled,
				(SELECT COUNT(d.cid) FROM tbl_donations d WHERE d.cid = a.fld_campaign_id AND d.mode = '1') AS donations,
				(SELECT IFNULL(SUM(e.donation_amount), 0.00) FROM tbl_donations e WHERE e.cid = a.fld_campaign_id AND e.mode = '1') AS moneyraised,
				(SELECT IFNULL(COUNT(f.cid),0) FROM tbl_donors_details f WHERE f.cid = a.fld_campaign_id AND f.is_unsubscribe = '1') AS donors_unsubscribe
				FROM tbl_campaign a
				LEFT JOIN tbl_tree i ON i.uid = a.fld_uid
				LEFT JOIN tbl_transaction l ON l.cid = a.fld_campaign_id
				WHERE a.fld_active = 1 AND  i.uid = '$uid'
				GROUP BY a.fld_campaign_id
				ORDER BY a.fld_campaign_id DESC");
			}
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function payment_history($cid) {
		try {
			$stmt = $this->db->prepare("SELECT *, DATE_FORMAT(dpaiddate,'%m/%d/%Y') AS dpaiddate, DATE_FORMAT(rpaiddate,'%m/%d/%Y') AS rpaiddate, DATE_FORMAT(cfirstpaiddate,'%m/%d/%Y') AS cfirstpaiddate, DATE_FORMAT(csecondpaiddate,'%m/%d/%Y') AS csecondpaiddate, DATE_FORMAT(firstrequesteddate,'%m/%d/%Y') AS firstrequesteddate, DATE_FORMAT(secondrequesteddate,'%m/%d/%Y') AS secondrequesteddate
			FROM tbl_transaction
			WHERE cid = '$cid'");
			$stmt->execute();
			$paymentRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			/*if($stmt->rowCount() > 0)
				{
			*/
			return $paymentRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function donations_list($cid) {
		try {
			$stmt = $this->db->prepare("SELECT b.fld_campaign_title, a.ufname AS donorfname, a.ulname AS donorlname, a.uemail AS donoremail, c.uname AS participantfname, c.ulname AS participantlname, c.uemail AS participantemail, a.donation_amount, a.id AS transactionnumber, a.creationdate, a.tid
			FROM tbl_donations a
			LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
			LEFT JOIN tbl_participants_details c ON c.cid = a.cid AND c.uid = a.refferal_by
			WHERE a.cid = '$cid' AND a.mode = '1'");
			$stmt->execute();
			$donationRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			/*if($stmt->rowCount() > 0)
				{
			*/
			return $donationRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function participant_enrolled2($cid) {
		try {
			$stmt = $this->db->prepare("SELECT a.uid, a.uname, a.ulname, b.fld_donor_size AS donorrequire,  up.fld_image, aa.is_unsubscribe,
			(SELECT COUNT(e.puid) FROM tbl_donors_details e WHERE e.cid = a.cid AND e.puid = a.uid) AS donorupload,
			(SELECT COUNT(dd.is_unsubscribe) FROM tbl_donors_details dd WHERE dd.cid = a.cid AND dd.puid = a.uid AND dd.is_unsubscribe = '1') AS donorunsubscribe,
			(SELECT COUNT(f.donation_amount) FROM tbl_donations f WHERE f.cid = a.cid AND f.refferal_by = a.uid AND f.mode = '1') AS donation_amount,
			(SELECT SUM(g.donation_amount) FROM tbl_donations g WHERE g.cid = a.cid AND g.refferal_by = a.uid AND g.mode = '1') AS sumofdonations,
			b.fld_participant_goal AS participantgoal
			FROM tbl_participants a
			LEFT JOIN tbl_participants_details aa ON a.uid = aa.uid
			LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
			LEFT JOIN tbl_users up ON up.fld_uid = a.uid
			WHERE a.cid = '$cid'
			GROUP BY a.uid");
			$stmt->execute();
			$donationRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			/*if($stmt->rowCount() > 0)
				{
			*/
			return $donationRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function participant_enrolled($cid) {
		try {
			$stmt = $this->db->prepare("SELECT a.uid, a.uname, a.ulname, b.fld_donor_size AS donorrequire,  up.fld_image, a.is_unsubscribe,
			(SELECT COUNT(e.puid) FROM tbl_donors_details e WHERE e.cid = a.cid AND e.puid = a.uid) AS donorupload,
			(SELECT COUNT(dd.is_unsubscribe) FROM tbl_donors_details dd WHERE dd.cid = a.cid AND dd.puid = a.uid AND dd.is_unsubscribe = '1') AS donorunsubscribe,
			(SELECT COUNT(f.donation_amount) FROM tbl_donations f WHERE f.cid = a.cid AND f.refferal_by = a.uid AND f.mode = '1') AS donation_amount,
			(SELECT SUM(g.donation_amount) FROM tbl_donations g WHERE g.cid = a.cid AND g.refferal_by = a.uid AND g.mode = '1') AS sumofdonations,
			b.fld_participant_goal AS participantgoal
			FROM tbl_participants_details a
			LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
			LEFT JOIN tbl_users up ON up.fld_uid = a.uid
			WHERE a.cid = '$cid'
			GROUP BY a.uid");
			$stmt->execute();
			$donationRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			/*if($stmt->rowCount() > 0)
				{
			*/
			return $donationRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function unsubscribed_donors($cid) {
		try {
			$stmt = $this->db->prepare("SELECT b.fld_campaign_title, a.uname AS donorfname, a.ulname AS donorlname, a.uemail AS donoremail, a.sent_email AS donorsentemail, a.is_read AS donorread, c.uname AS participantfname, c.ulname AS participantlname, c.uemail AS participantemail, a.is_unsubscribe, DATE_FORMAT(a.is_unsubscribe_date, '%m/%d/%Y') AS unsubscribe_date
			FROM tbl_donors_details a
			LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
			LEFT JOIN tbl_participants_details c ON c.cid = a.cid AND c.uid = a.puid
			WHERE a.cid = '$cid' AND a.is_unsubscribe = '1'
			GROUP BY a.uemail, a.puid");
			$stmt->execute();
			$donationRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			/*if($stmt->rowCount() > 0)
				{
			*/
			return $donationRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function donorparticipant_list($cid) {
		try {
			$stmt = $this->db->prepare("SELECT b.fld_campaign_title, a.uname AS donorfname, a.ulname AS donorlname, a.uemail AS donoremail, a.sent_email AS donorsentemail, a.is_read AS donorread, c.uname AS participantfname, c.ulname AS participantlname, c.uemail AS participantemail
			FROM tbl_donors_details a
			LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
			LEFT JOIN tbl_participants_details c ON c.cid = a.cid AND c.uid = a.puid
			where a.cid = '$cid'
			GROUP BY a.uemail, a.puid");
			$stmt->execute();
			$donationRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			/*if($stmt->rowCount() > 0)
				{
			*/
			return $donationRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function inserttransaction($transaction_id, $campid, $hashid, $participantid, $donorname, $donorlname, $donoremail, $ccardnumbermasked, $client_ip, $donateamount, $brand, $funding, $displaylisted, $rewardid, $isreward, $reward_desc) {
		try {
			if (isset($hashid) && $hashid != '') {
				//Indirect with Donors Id
				$stmt = $this->db->prepare("SELECT * FROM tbl_donors WHERE cid='$campid' AND uid='$hashid' LIMIT 1");
			} else {
				//Direct without Donors Id
				$stmt = $this->db->prepare("SELECT * FROM tbl_donors WHERE cid='$campid' AND uemail='$donoremail' AND uname='$donorname' LIMIT 1");
			}
			$stmt->execute();
			$donorRow = $stmt->fetch(PDO::FETCH_ASSOC);
			$donorfname = $donorname;
			$donorlname = $donorlname;
			$donorphone = '';
			if ($stmt->rowCount() > 0) {
				$donorfname = $donorname;
				$donorlname = $donorlname;
				$donorphone = $donorRow['uphone'];
				$gotid = $donorRow['uid'];
			} else {
				$stmt10 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_email='$donoremail' AND fld_name = '$donorfname' AND fld_role_id = '4' LIMIT 1");
				$stmt10->execute();
				$userRow = $stmt10->fetch(PDO::FETCH_ASSOC);
				if ($stmt10->rowCount() == 0) {
					//Generating Password
					$length = 14;
					$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					$charactersLength = strlen($characters);
					$randomString = '';
					for ($i = 0; $i < $length; $i++) {
						$randomString .= $characters[rand(0, $charactersLength - 1)];
					}
					$passhash = $randomString;
					//Generating Password
					//Encrypt Password
					$string = $passhash;
					$key = sENC_KEY;
					$result = '';
					for ($i = 0; $i < strlen($string); $i++) {
						$char = substr($string, $i, 1);
						$keychar = substr($key, ($i % strlen($key)) - 1, 1);
						$char = chr(ord($char) + ord($keychar));
						$result .= $char;
					}
					$userpass = base64_encode($result);
					//Encrypt Password
					$stmt = $this->db->prepare("INSERT INTO tbl_users(fld_role_id,fld_status,fld_email,fld_password,fld_name,fld_lname,fld_phone,fld_join_date) VALUES ('4','1',:fld_email,:fld_password,:fld_name,:fld_lname,:fld_phone,NOW())");
					//$stmt->bindparam(":fld_role_id", "4");
					//$stmt->bindparam(":fld_status", "1");
					$stmt->bindparam(":fld_email", $donoremail);
					$stmt->bindparam(":fld_password", $userpass);
					$stmt->bindparam(":fld_name", $donorfname);
					$stmt->bindparam(":fld_lname", $donorlname);
					$stmt->bindparam(":fld_phone", $donorphone);
					$stmt->execute();
					$gotid = $this->db->lastInsertId();
				} else {
					$gotid = $userRow['fld_uid'];
				}
				$stmt11 = $this->db->prepare("SELECT * FROM tbl_donors WHERE cid='$campid' AND uid='$gotid' LIMIT 1");
				$stmt11->execute();
				$pidrow = $stmt11->fetch(PDO::FETCH_ASSOC);
				if ($stmt11->rowCount() == 0) {
					$stmt = $this->db->prepare("INSERT INTO tbl_donors(cid,uid,puid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:uid,:puid,:uname,:ulname,:uemail,:uphone,NOW())");
					$stmt->bindparam(":cid", $campid);
					$stmt->bindparam(":uid", $gotid);
					$stmt->bindparam(":puid", $participantid);
					$stmt->bindparam(":uname", $donorfname);
					$stmt->bindparam(":ulname", $donorlname);
					$stmt->bindparam(":uemail", $donoremail);
					$stmt->bindparam(":uphone", $donorphone);
					$stmt->execute();
					$pid = $this->db->lastInsertId();
				} else {
					$pid = $pidrow['id'];
				}
				$stmt12 = $this->db->prepare("SELECT * FROM tbl_donors_details WHERE cid='$cid' AND uid='$uid' LIMIT 1");
				$stmt12->execute();
				$stmt12->fetchall(PDO::FETCH_ASSOC);
				if ($stmt12->rowCount() == 0) {
					$stmt2 = $this->db->prepare("INSERT INTO tbl_donors_details(cid,did,uid,puid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:did,:uid,:puid,:uname,:ulname,:uemail,:uphone,NOW())");
					$stmt2->bindparam(":cid", $campid);
					$stmt2->bindparam(":did", $pid);
					$stmt2->bindparam(":uid", $gotid);
					$stmt2->bindparam(":puid", $participantid);
					$stmt2->bindparam(":uname", $donorfname);
					$stmt2->bindparam(":ulname", $donorlname);
					$stmt2->bindparam(":uemail", $donoremail);
					$stmt2->bindparam(":uphone", $donorphone);
					$stmt2->execute();
				}
			}
			$stmt2 = $this->db->prepare("SELECT * FROM tbl_campaign WHERE fld_campaign_id='$campid' LIMIT 1");
			$stmt2->execute();
			$campaignRow = $stmt2->fetch(PDO::FETCH_ASSOC);
			if ($stmt2->rowCount() > 0) {
				$cmfname = $campaignRow['fld_cname'];
				$cmlname = $campaignRow['fld_clname'];
			}
			$stmt3 = $this->db->prepare("INSERT INTO tbl_donations
            (tid, cid, uid, cmfname, cmlname, ufname, ulname, uemail, uphone, donation_amount, payment_method, payment_through, client_ip, refferal_by, card_number, displaylisted, is_reward, reward_id, reward_desc, creationdate)
            VALUES (:tid, :cid, :uid, :cmfname, :cmlname, :ufname, :ulname, :uemail, :uphone, :donation_amount, :payment_method, :payment_through, :client_ip, :refferal_by, :card_number, :displaylisted, :isreward, :rewardid, :reward_desc, NOW())");
			$stmt3->bindparam(":tid", $transaction_id);
			$stmt3->bindparam(":cid", $campid);
			$stmt3->bindparam(":uid", $gotid);
			$stmt3->bindparam(":cmfname", $cmfname);
			$stmt3->bindparam(":cmlname", $cmlname);
			$stmt3->bindparam(":ufname", $donorfname);
			$stmt3->bindparam(":ulname", $donorlname);
			$stmt3->bindparam(":uemail", $donoremail);
			$stmt3->bindparam(":uphone", $donorphone);
			$stmt3->bindparam(":donation_amount", $donateamount);
			$stmt3->bindparam(":payment_method", $funding);
			$stmt3->bindparam(":payment_through", $brand);
			$stmt3->bindparam(":client_ip", $client_ip);
			$stmt3->bindparam(":refferal_by", $participantid);
			$stmt3->bindparam(":card_number", $ccardnumbermasked);
			$stmt3->bindparam(":displaylisted", $displaylisted);
			$stmt3->bindparam(":rewardid", $rewardid);
			$stmt3->bindparam(":isreward", $isreward);
			$stmt3->bindparam(":reward_desc", $reward_desc);
			$stmt3->execute();
			//transaction id
			$transactionid = $this->db->lastInsertId();
			return $transactionid;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getcampaigndetail($id) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM  tbl_campaign WHERE fld_campaign_id = '$id'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getcampaigndetail3($id) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM  tbl_campaign WHERE fld_campaign_id = '$id'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function gettransactiondetail($id) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM  tbl_donations WHERE id = '$id'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getmessagedetail($campid, $pid) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM  tbl_donations WHERE cid = '$campid' ORDER BY id DESC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function updatetransaction($transid, $donorimage, $comment) {
		try {
			$stmt = $this->db->prepare("UPDATE tbl_donations SET comment = :comment WHERE id = :id");
			$stmt->bindparam(":id", $transid);
			//$stmt->bindparam(":imageurl", $donorimage);
			$stmt->bindparam(":comment", $comment);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getcampaigngraphparticipant($camphash, $pid) {
		try {
			$stmt = $this->db->prepare("SELECT a.fld_participant_goal AS participant_goal, a.fld_participant_goal_original AS participant_goal_original, IFNULL(SUM(b.donation_amount),0.00) AS participant_raised FROM tbl_campaign a
			LEFT JOIN tbl_donations b ON b.cid = a.fld_campaign_id AND b.refferal_by = $pid AND b.mode = '1'
			WHERE a.fld_hashcamp = '$camphash'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getcampaigngraphparticipant2($camphash, $pid) {
		try {
			$stmt = $this->db->prepare("SELECT a.fld_participant_goal AS participant_goal, a.fld_campaign_goal_original AS campaign_goal_original, a.fld_participant_goal_original AS participant_goal_original, IFNULL(SUM(b.donation_amount),0.00) AS participant_raised FROM tbl_campaign a
			LEFT JOIN tbl_donations b ON b.cid = a.fld_campaign_id AND b.refferal_by = '$pid' AND b.mode = '1'
			WHERE a.fld_campaign_id = '$camphash'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getcampaigngraphtotal($camphash) {
		try {
			$stmt = $this->db->prepare("SELECT a.fld_campaign_goal AS campaign_goal, a.fld_campaign_goal_original AS campaign_goal_original, IFNULL(SUM(b.donation_amount),0.00) AS campaign_raised FROM tbl_campaign a
			LEFT JOIN tbl_donations b ON b.cid = a.fld_campaign_id AND b.mode = '1'
			WHERE a.fld_hashcamp = '$camphash'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function campaigngoalupdate($updated_goal, $tcampaign_goal_original, $tcampaign_goal, $camphash) {
		try {
			if ($tcampaign_goal_original != '') {
				$campaign_goal = $tcampaign_goal;
				$campaign_goal_query = '';
			} else {
				$campaign_goal = $tcampaign_goal;
				$campaign_goal_query = ", fld_campaign_goal_original = '$campaign_goal'";
			}
			$stmt = $this->db->prepare("UPDATE tbl_campaign
			SET fld_campaign_goal = '$updated_goal', fld_goal_increase_date = NOW() $campaign_goal_query
			WHERE fld_hashcamp = '$camphash'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function campaignparticipantgoalupdate($updated_participant_goal, $tparticipant_goal_original, $tparticipant_goal, $camphash) {
		try {
			if ($tparticipant_goal_original != '') {
				$participant_goal = $tparticipant_goal;
				$participant_goal_query = '';
			} else {
				$participant_goal = $tparticipant_goal;
				$participant_goal_query = ", fld_participant_goal_original = '$participant_goal'";
			}
			$stmt = $this->db->prepare("UPDATE tbl_campaign
			SET fld_participant_goal = '$updated_participant_goal', fld_goal_increase_date = NOW() $participant_goal_query
			WHERE fld_hashcamp = '$camphash'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getcampaigngraphtotal2($camphash) {
		try {
			$stmt = $this->db->prepare("SELECT a.fld_campaign_goal AS campaign_goal, IFNULL(SUM(b.donation_amount),0.00) AS campaign_raised FROM tbl_campaign a
			LEFT JOIN tbl_donations b ON b.cid = a.fld_campaign_id AND b.mode = '1' AND b.mode = '1'
			WHERE a.fld_campaign_id = '$camphash'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getcampaigndetail2($id) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM  tbl_campaign WHERE fld_hashcamp = '$id'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getpercent($cid) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_transaction WHERE cid = '$cid' ORDER BY id DESC LIMIT 1");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getgallerydetails($id, $filename) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM  tbl_gallery WHERE fld_campaign_id = '$id' AND fld_image_name = '$filename'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_update_video($iCid, $videofiles, $active) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM  tbl_video WHERE fld_campaign_id = '$iCid'");
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$stmt1 = $this->db->prepare("UPDATE tbl_video SET fld_video = :fld_video, fld_status = :fld_status WHERE fld_campaign_id = :fld_campaign_id");
				$stmt1->bindparam(":fld_campaign_id", $iCid);
				$stmt1->bindparam(":fld_video", $videofiles);
				$stmt1->bindparam(":fld_status", $active);
				$stmt1->execute();
			} else {
				$stmt1 = $this->db->prepare("INSERT INTO tbl_video (fld_campaign_id,fld_video,fld_status) VALUES (:fld_campaign_id,:fld_video,:fld_status)");
				$stmt1->bindparam(":fld_campaign_id", $iCid);
				$stmt1->bindparam(":fld_video", $videofiles);
				$stmt1->bindparam(":fld_status", $active);
				$stmt1->execute();
			}
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getvideogallery($id) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM  tbl_video WHERE fld_campaign_id =:id");
			$stmt->execute(array(':id' => $id));
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getimagegallery($id) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM  tbl_gallery WHERE fld_campaign_id =:id");
			$stmt->execute(array(':id' => $id));
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdonordetail($id, $uid) {
		try {
			$stmt = $this->db->prepare("SELECT a.*
			FROM tbl_donors a
			LEFT JOIN tbl_donations d ON a.cid = d.cid AND a.uid = d.uid
			WHERE a.puid = '$uid' AND a.cid NOT IN ('$id') AND
			NOT EXISTS
			(SELECT NULL FROM tbl_donors_details b WHERE a.uname = b.uname AND b.cid = '$id')
			GROUP BY uid,uname
			ORDER BY a.ulname, a.uname ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getparticipantdetail($id, $uid) {
		try {
			$stmt = $this->db->prepare("SELECT a.*
			FROM tbl_participants a WHERE a.cuid = '$uid' AND
			NOT EXISTS
			(SELECT NULL FROM tbl_participants_details b WHERE a.uname = b.uname AND b.cid = '$id')
			GROUP BY uid,uname
			ORDER BY a.ulname, a.uname ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdonorsearched($id, $uid, $query) {
		try {
			$stmt = $this->db->prepare("SELECT a.*
			FROM tbl_donors a WHERE a.puid = '$uid' AND a.cid NOT IN ('$id') AND (a.uname LIKE '%$query%' OR a.uemail LIKE '%$query%' OR a.uphone LIKE '%$query%') AND
			NOT EXISTS
			(SELECT NULL FROM tbl_donors_details b WHERE a.uname = b.uname AND b.cid = '$id')
			GROUP BY uid,uname
			ORDER BY uname ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getparticipantsearched($id, $uid, $query) {
		try {
			$stmt = $this->db->prepare("SELECT a.*
			FROM tbl_participants a WHERE a.cuid = '$uid' AND a.cid NOT IN ('$id') AND (a.uname LIKE '%$query%' OR a.uemail LIKE '%$query%' OR a.uphone LIKE '%$query%') AND
			NOT EXISTS
			(SELECT NULL FROM tbl_participants_details b WHERE a.uname = b.uname AND b.cid = '$id')
			GROUP BY uid,uname
			ORDER BY uname ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdonordetailedit($cid, $pid, $did) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_donors_details WHERE uid='$did'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getparticipantdetailedit($cid, $pid) {
		try {
			$stmt = $this->db->prepare("SELECT a.*, b.fld_image FROM tbl_participants_details a INNER JOIN tbl_users b ON b.fld_uid = a.uid WHERE pid='$pid'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_participant($cid, $pid, $uid, $pname, $plname, $pemail, $phone) {
		try {
			//$stmt1 = $this->db->prepare("UPDATE tbl_users SET fld_name = '$pname', fld_lname = '$plname', fld_email = '$pemail', fld_phone= '$phone' WHERE fld_uid = '$pid'");
			//$stmt1->execute();
			$stmt2 = $this->db->prepare("UPDATE tbl_participants SET uname = :uname, ulname = :ulname, uemail = :uemail, uphone = :uphone WHERE id = :id AND cid = :cid");
			$stmt2->bindparam(":id", $pid);
			$stmt2->bindparam(":cid", $cid);
			$stmt2->bindparam(":uname", $pname);
			$stmt2->bindparam(":ulname", $plname);
			$stmt2->bindparam(":uemail", $pemail);
			$stmt2->bindparam(":uphone", $phone);
			$stmt2->execute();
			$stmt3 = $this->db->prepare("UPDATE tbl_participants_details SET uname = :uname, ulname = :ulname, uemail = :uemail, uphone = :uphone WHERE pid = :pid AND cid = :cid");
			$stmt3->bindparam(":pid", $pid);
			$stmt3->bindparam(":cid", $cid);
			$stmt3->bindparam(":uname", $pname);
			$stmt3->bindparam(":ulname", $plname);
			$stmt3->bindparam(":uemail", $pemail);
			$stmt3->bindparam(":uphone", $phone);
			$stmt3->execute();
			return $stmt2;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function donor_update($cid, $pid, $did, $pname, $plname, $pemail, $pphone) {
		try {
			$stmt = $this->db->prepare("UPDATE tbl_users SET fld_name = :fld_name, fld_lname = :fld_lname, fld_email = :fld_email, fld_phone = :fld_phone WHERE fld_uid = :fld_uid");
			$stmt->bindparam(":fld_uid", $did);
			$stmt->bindparam(":fld_name", $pname);
			$stmt->bindparam(":fld_lname", $plname);
			$stmt->bindparam(":fld_email", $pemail);
			$stmt->bindparam(":fld_phone", $pphone);
			$stmt->execute();
			$stmt2 = $this->db->prepare("UPDATE tbl_donors SET uname = :uname, ulname = :ulname, uemail = :uemail, uphone = :uphone WHERE cid = :cid AND puid = :puid AND uid = :uid");
			$stmt2->bindparam(":cid", $cid);
			$stmt2->bindparam(":puid", $pid);
			$stmt2->bindparam(":uid", $did);
			$stmt2->bindparam(":uname", $pname);
			$stmt2->bindparam(":ulname", $plname);
			$stmt2->bindparam(":uemail", $pemail);
			$stmt2->bindparam(":uphone", $pphone);
			$stmt2->execute();
			$stmt3 = $this->db->prepare("UPDATE tbl_donors_details SET uname = :uname, ulname = :ulname, uemail = :uemail, uphone = :uphone WHERE cid = :cid AND puid = :puid AND uid = :uid");
			$stmt3->bindparam(":cid", $cid);
			$stmt3->bindparam(":puid", $pid);
			$stmt3->bindparam(":uid", $did);
			$stmt3->bindparam(":uname", $pname);
			$stmt3->bindparam(":ulname", $plname);
			$stmt3->bindparam(":uemail", $pemail);
			$stmt3->bindparam(":uphone", $pphone);
			$stmt3->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdonordetailselected($id, $uid) {
		try {
			$stmt = $this->db->prepare("SELECT * 
			FROM tbl_donors_details WHERE cid='$id' and puid='$uid'
			ORDER BY ulname, uname ASC");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getbademails($uid, $id) {
		try {
			$stmt = $this->db->prepare("SELECT uemail, is_read, is_unsubscribe, sent_email
			FROM tbl_donors_details
			WHERE cid='$id' AND puid='$uid'");
			$stmt->execute();
			$userRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getbademailsbycampaign($cid) {
		try {
			$stmt = $this->db->prepare("SELECT uemail, is_read, is_unsubscribe, sent_email
			FROM tbl_donors_details
			WHERE cid='$cid'");
			$stmt->execute();
			$userRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdonordetailsbyparticipants($uid, $id) {
		try {
			$stmt = $this->db->prepare("SELECT COUNT(a.id) AS curr_donors, (SELECT fld_donor_size FROM tbl_campaign WHERE fld_campaign_id = '$id') AS req_donors
			FROM tbl_donors_details a
			WHERE a.cid='$id' AND a.puid='$uid' AND a.puid > 0");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getparticipantdetailselected($id, $uid, $roleid) {
		try {
			if ($roleid == 1 || $roleid == 3 || $roleid == 6) {
				$stmt = $this->db->prepare("SELECT a.sms_sent_id, a.id, a.pid, a.uid, a.uname, a.ulname, a.uemail, a.uphone, a.is_unsubscribe, up.fld_image,
				(SELECT IFNULL(SUM(b.donation_amount),0.00) FROM tbl_donations b WHERE b.cid = a.cid AND b.refferal_by = a.uid AND b.mode = '1') AS moneyraised
				FROM tbl_participants_details a
				LEFT JOIN tbl_users up ON up.fld_uid = a.uid
				WHERE a.cid='$id'
				GROUP BY a.uemail,a.id
				ORDER BY a.ulname, a.uname ASC");
			} else {
				$stmt = $this->db->prepare("SELECT a.sms_sent_id, a.id, a.pid, a.uid, a.uname, a.ulname, a.uemail, a.uphone, a.is_unsubscribe, up.fld_image,
				(SELECT IFNULL(SUM(b.donation_amount),0.00) FROM tbl_donations b WHERE b.cid = a.cid AND b.refferal_by = a.uid AND b.mode = '1') AS moneyraised
				FROM tbl_participants_details a
				LEFT JOIN tbl_users up ON up.fld_uid = a.uid
				WHERE a.cid='$id' AND a.cuid='$uid'
				GROUP BY a.uemail,a.id
				ORDER BY a.ulname, a.uname ASC");
			}
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getparticipantdetailselectedbycsv($id, $uid, $roleid, $email,$phone) {
		try {
			if ($roleid == 1 || $roleid == 3 || $roleid == 6) {
				if ($email != '' && $phone != '') {
					$stmt = $this->db->prepare("SELECT id, pid, uid, uname, ulname, uemail, uphone, sms_sent_id
					FROM tbl_participants_details WHERE cid='$id' and uemail = '$email' and uphone = '$phone'");
				} elseif ($email == '' && $phone != '') {
					$stmt = $this->db->prepare("SELECT id, pid, uid, uname, ulname, uemail, uphone, sms_sent_id
					FROM tbl_participants_details WHERE cid='$id' and uphone = '$phone'");
				} elseif ($email != '' && $phone == '') {
					$stmt = $this->db->prepare("SELECT id, pid, uid, uname, ulname, uemail, uphone, sms_sent_id
					FROM tbl_participants_details WHERE cid='$id' and uemail = '$email'");
				}
			} else {
				if ($email != '' && $phone != '') {
					$stmt = $this->db->prepare("SELECT id, pid, uid, uname, ulname, uemail, uphone, sms_sent_id
					FROM tbl_participants_details WHERE cid='$id' and cuid='$uid' and uemail = '$email' and uphone = '$phone'");
				} elseif ($email == '' && $phone != '') {
					$stmt = $this->db->prepare("SELECT id, pid, uid, uname, ulname, uemail, uphone, sms_sent_id
					FROM tbl_participants_details WHERE cid='$id' and cuid='$uid' and uphone = '$phone'");
				} elseif ($email != '' && $phone == '') {
					$stmt = $this->db->prepare("SELECT id, pid, uid, uname, ulname, uemail, uphone, sms_sent_id
					FROM tbl_participants_details WHERE cid='$id' and cuid='$uid' and uemail = '$email'");
				}
			}
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getparticipants($cid) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$cid'");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_schedule($cid, $date) {
		try {
			$stmt = $this->db->prepare("SELECT fld_campaign_id, fld_campaign_title, DATE_FORMAT(fld_campaign_sdate, '%m/%d/%Y') AS sdate, DATE_FORMAT(fld_campaign_edate, '%m/%d/%Y') AS edate, DATE_FORMAT(fld_last_updated, '%m/%d/%Y') AS fld_last_updated
			FROM tbl_campaign
			WHERE fld_campaign_id = '$cid' AND fld_campaign_edate >= '$date' AND fld_campaign_sdate NOT IN ('0000-00-00') AND fld_status = 1");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getparticipants2($cid) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$cid' AND uid > 0");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getallcampaign($date) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_campaign WHERE fld_campaign_sdate <= '$date' AND fld_status = 1");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function cronupdatecampaign($cid) {
		try {
			$stmt = $this->db->prepare("UPDATE tbl_campaign SET fld_sendconfirmation = 1 WHERE fld_campaign_id = '$cid'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_campaign($iUid, $sName, $sLName, $sDob, $sSsn, $sEmail, $sPhone, $sAddress, $iCountryId, $iStateId, $iCityId, $sZipcode) {
		try {
			//echo "INSERT INTO tbl_campaign(fld_uid,fld_cname,fld_cemail,fld_cphone,fld_caddress,fld_ccountry,fld_cstate,fld_ccity,fld_czipcode) VALUES('".$iUid."','".$sName."','".$sEmail."','".$sPhone."','".$sAddress."','".$iCountryId."','".$iStateId."','".$iCityId."','".$sZipcode."')";
			$stmt99 = $this->db->prepare("SELECT u.*
				FROM tbl_tree tr
				INNER JOIN tbl_users u ON (tr.uid = u.fld_uid OR tr.did = u.fld_uid)
				WHERE tr.uid = '$iUid' AND u.fld_role_id = '3'
				ORDER BY tr.nid ASC
				LIMIT 1");
			$stmt99->execute();
			if ($stmt99->rowCount() > 0) {
				$CommRow = $stmt99->fetch(PDO::FETCH_ASSOC);
				$DistID = $CommRow['fld_uid'];
				$stmt88 = $this->db->prepare("SELECT * FROM tbl_gsettings_user WHERE fld_userid = '$DistID' ORDER BY fld_gid ASC LIMIT 1");
				$stmt88->execute();
				if ($stmt88->rowCount() > 0) {
					$global_comm_row = $stmt88->fetch(PDO::FETCH_ASSOC);
					$comm_dist = $global_comm_row['fld_gvalue'];
					$comm_admin = 7 - $comm_dist;
					$comm_rep = 0;
				}
			} else {
				$comm_dist = 7;
				$comm_admin = 0;
				$comm_rep = 0;
			}
			$stmt = $this->db->prepare("INSERT INTO tbl_campaign(fld_uid,fld_cname,fld_clname,fld_cemail,fld_cphone,fld_caddress,fld_ccountry,fld_cstate,fld_ccity,fld_czipcode,fld_dob,fld_ssn,fld_status,fld_active,fld_admin_per,fld_dist_per,fld_rep_per) VALUES(:fld_uid,:fld_cname,:fld_clname,:fld_cemail,:fld_cphone,:fld_caddress,:fld_ccountry,:fld_cstate,:fld_ccity,:fld_czipcode,:fld_dob,:fld_ssn,'1','1','$comm_admin','$comm_dist','$comm_rep')");
			$stmt->bindparam(":fld_uid", $iUid);
			$stmt->bindparam(":fld_cname", $sName);
			$stmt->bindparam(":fld_clname", $sLName);
			$stmt->bindparam(":fld_cemail", $sEmail);
			$stmt->bindparam(":fld_cphone", $sPhone);
			$stmt->bindparam(":fld_caddress", $sAddress);
			$stmt->bindparam(":fld_ccountry", $iCountryId);
			$stmt->bindparam(":fld_cstate", $iStateId);
			$stmt->bindparam(":fld_ccity", $iCityId);
			$stmt->bindparam(":fld_czipcode", $sZipcode);
			$stmt->bindparam(":fld_dob", $sDob);
			$stmt->bindparam(":fld_ssn", $sSsn);
			$stmt->execute();
			return $this->db->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_campaign_adddonors1($cid, $uid, $name, $lname, $email, $phone, $password, $using, $participantid, $participantname) {
		try {
			if ($email != '' && $phone == '') {
				$stmt10 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_email='$email' AND fld_role_id = '4'");
			} elseif ($email == '' && $phone != '') {
				$stmt10 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_phone ='$phone' AND fld_role_id = '4'");
			} else {
				$stmt10 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_email='$email' AND fld_role_id = '4'");
			}
			$stmt10->execute();
			$stmt10->fetchall(PDO::FETCH_ASSOC);
			if ($stmt10->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO tbl_users(fld_role_id,fld_status,fld_email,fld_password,fld_name,fld_lname,fld_phone,fld_join_date) VALUES ('4','1',:fld_email,:fld_password,:fld_name,:fld_lname,:fld_phone,NOW())");
				//$stmt->bindparam(":fld_role_id", "4");
				//$stmt->bindparam(":fld_status", "1");
				$stmt->bindparam(":fld_email", $email);
				$stmt->bindparam(":fld_password", $password);
				$stmt->bindparam(":fld_name", $name);
				$stmt->bindparam(":fld_lname", $lname);
				$stmt->bindparam(":fld_phone", $phone);
				$stmt->execute();
				$gotid = $this->db->lastInsertId();
			}
			$stmt11 = $this->db->prepare("SELECT * FROM tbl_donors WHERE cid='$cid' AND uid='$gotid' AND puid='$uid'");
			$stmt11->execute();
			$stmt11->fetchall(PDO::FETCH_ASSOC);
			if ($stmt11->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO tbl_donors(cid,uid,puid,uname,ulname,uemail,uphone,creationdate,usedas,participantid,participantname) VALUES (:cid,:uid,:puid,:uname,:ulname,:uemail,:uphone,NOW(),:usedas,:participantid,:participantname)");
				$stmt->bindparam(":cid", $cid);
				$stmt->bindparam(":uid", $gotid);
				$stmt->bindparam(":puid", $uid);
				$stmt->bindparam(":uname", $name);
				$stmt->bindparam(":ulname", $lname);
				$stmt->bindparam(":uemail", $email);
				$stmt->bindparam(":uphone", $phone);
				$stmt->bindparam(":usedas", $using);
				$stmt->bindparam(":participantid", $participantid);
				$stmt->bindparam(":participantname", $participantname);
				$stmt->execute();
				$pid = $this->db->lastInsertId();
			}
			$stmt12 = $this->db->prepare("SELECT * FROM tbl_donors_details WHERE cid='$cid' AND uid='$gotid' AND puid='$uid'");
			$stmt12->execute();
			$stmt12->fetchall(PDO::FETCH_ASSOC);
			if ($stmt12->rowCount() == 0) {
				$stmt2 = $this->db->prepare("INSERT INTO tbl_donors_details(cid,did,uid,puid,uname,ulname,uemail,uphone,creationdate,usedas,participantid,participantname) VALUES (:cid,:did,:uid,:puid,:uname,:ulname,:uemail,:uphone,NOW(),:usedas,:participantid,:participantname)");
				$stmt2->bindparam(":cid", $cid);
				$stmt2->bindparam(":did", $pid);
				$stmt2->bindparam(":uid", $gotid);
				$stmt2->bindparam(":puid", $uid);
				$stmt2->bindparam(":uname", $name);
				$stmt2->bindparam(":ulname", $lname);
				$stmt2->bindparam(":uemail", $email);
				$stmt2->bindparam(":uphone", $phone);
				$stmt2->bindparam(":usedas", $using);
				$stmt2->bindparam(":participantid", $participantid);
				$stmt2->bindparam(":participantname", $participantname);
				$stmt2->execute();
				return $this->db->lastInsertId();
			} else {
				return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_campaign_adddonors2($cid, $gotid, $uid, $name, $lname, $email, $phone, $using, $participantid, $participantname) {
		try {
			$stmt11 = $this->db->prepare("SELECT * FROM tbl_donors WHERE cid='$cid' AND uid='$gotid' AND puid='$uid'");
			$stmt11->execute();
			$stmt11->fetchall(PDO::FETCH_ASSOC);
			if ($stmt11->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO tbl_donors(cid,uid,puid,uname,ulname,uemail,uphone,creationdate,usedas,participantid,participantname) VALUES (:cid,:uid,:puid,:uname,:ulname,:uemail,:uphone,NOW(),:usedas,:participantid,:participantname)");
				$stmt->bindparam(":cid", $cid);
				$stmt->bindparam(":uid", $gotid);
				$stmt->bindparam(":puid", $uid);
				$stmt->bindparam(":uname", $name);
				$stmt->bindparam(":ulname", $lname);
				$stmt->bindparam(":uemail", $email);
				$stmt->bindparam(":uphone", $phone);
				$stmt->bindparam(":usedas", $using);
				$stmt->bindparam(":participantid", $participantid);
				$stmt->bindparam(":participantname", $participantname);
				$stmt->execute();
				$pid = $this->db->lastInsertId();
			}
			$stmt12 = $this->db->prepare("SELECT * FROM tbl_donors_details WHERE cid='$cid' AND uid='$gotid' AND puid='$uid'");
			$stmt12->execute();
			$stmt12->fetchall(PDO::FETCH_ASSOC);
			if ($stmt12->rowCount() == 0) {
				$stmt2 = $this->db->prepare("INSERT INTO tbl_donors_details(cid,did,uid,puid,uname,ulname,uemail,uphone,creationdate,usedas,participantid,participantname) VALUES (:cid,:did,:uid,:puid,:uname,:ulname,:uemail,:uphone,NOW(),:usedas,:participantid,:participantname)");
				$stmt2->bindparam(":cid", $cid);
				$stmt2->bindparam(":did", $pid);
				$stmt2->bindparam(":uid", $gotid);
				$stmt2->bindparam(":puid", $uid);
				$stmt2->bindparam(":uname", $name);
				$stmt2->bindparam(":ulname", $lname);
				$stmt2->bindparam(":uemail", $email);
				$stmt2->bindparam(":uphone", $phone);
				$stmt2->bindparam(":usedas", $using);
				$stmt2->bindparam(":participantid", $participantid);
				$stmt2->bindparam(":participantname", $participantname);
				$stmt2->execute();
				return $this->db->lastInsertId();
			} else {
				return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_campaign_addparticipants1($cid, $uid, $name, $lname, $email, $phone, $password) {
		try {
			/*$stmt10 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_email='$email'");
				$stmt10->execute();
				$stmt10->fetchall(PDO::FETCH_ASSOC);
				if($stmt10->rowCount() == 0)
				{
					$stmt = $this->db->prepare("INSERT INTO tbl_users(fld_role_id,fld_status,fld_email,fld_password,fld_name,fld_lname,fld_phone,fld_join_date) VALUES ('5','1','".$email."','".$password."','".$name."','".$lname."','".$phone."',NOW())");
					$stmt->execute();
					$gotid = $this->db->lastInsertId();
			*/
			$gotid = '';
			if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
				$stmt11 = $this->db->prepare("SELECT * FROM tbl_participants WHERE cid='$cid' AND cuid='$uid' AND uemail='$email'");
			} else {
				$stmt11 = $this->db->prepare("SELECT * FROM tbl_participants WHERE cid='$cid' AND uemail='$email'");
			}
			$stmt11->execute();
			$pidrow = $stmt11->fetch(PDO::FETCH_ASSOC);
			if ($stmt11->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO tbl_participants(cid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:uid,:cuid,:uname,:ulname,:uemail,:uphone,NOW())");
				$stmt->bindparam(":cid", $cid);
				$stmt->bindparam(":uid", $gotid);
				$stmt->bindparam(":cuid", $uid);
				$stmt->bindparam(":uname", $name);
				$stmt->bindparam(":ulname", $lname);
				$stmt->bindparam(":uemail", $email);
				$stmt->bindparam(":uphone", $phone);
				$stmt->execute();
				$pid = $this->db->lastInsertId();
			} else {
				$pid = $pidrow['id'];
			}
			if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
				$stmt12 = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid='$cid' AND uemail='$email'");
			} else {
				$stmt12 = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid='$cid' AND cuid='$uid' AND uemail='$email'");
			}
			$stmt12->execute();
			$stmt12->fetchall(PDO::FETCH_ASSOC);
			if ($stmt12->rowCount() == 0) {
				$stmt2 = $this->db->prepare("INSERT INTO tbl_participants_details(cid,pid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:pid,:uid,:cuid,:uname,:ulname,:uemail,:uphone,NOW())");
				$stmt2->bindparam(":cid", $cid);
				$stmt2->bindparam(":pid", $pid);
				$stmt2->bindparam(":uid", $gotid);
				$stmt2->bindparam(":cuid", $uid);
				$stmt2->bindparam(":uname", $name);
				$stmt2->bindparam(":ulname", $lname);
				$stmt2->bindparam(":uemail", $email);
				$stmt2->bindparam(":uphone", $phone);
				$stmt2->execute();
				return $this->db->lastInsertId();
			} else {
				return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_campaign_addparticipants2($cid, $gotid, $uid, $name, $lname, $email, $phone) {
		try {
			if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
				$stmt11 = $this->db->prepare("SELECT * FROM tbl_participants WHERE cid='$cid' AND uid='$gotid'");
			} else {
				$stmt11 = $this->db->prepare("SELECT * FROM tbl_participants WHERE cid='$cid' AND cuid='$uid' AND uid='$gotid'");
			}
			$stmt11->execute();
			$pidrow = $stmt11->fetch(PDO::FETCH_ASSOC);
			if ($stmt11->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO tbl_participants(cid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:uid,:cuid,:uname,:ulname,:uemail,:uphone,NOW())");
				$stmt->bindparam(":cid", $cid);
				$stmt->bindparam(":uid", $gotid);
				$stmt->bindparam(":cuid", $uid);
				$stmt->bindparam(":uname", $name);
				$stmt->bindparam(":ulname", $lname);
				$stmt->bindparam(":uemail", $email);
				$stmt->bindparam(":uphone", $phone);
				$stmt->execute();
				$pid = $this->db->lastInsertId();
			} else {
				$pid = $pidrow['id'];
			}
			if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
				$stmt12 = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid='$cid' AND uid='$gotid'");
			} else {
				$stmt12 = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid='$cid' AND cuid='$uid' AND uid='$gotid'");
			}
			$stmt12->execute();
			$stmt12->fetchall(PDO::FETCH_ASSOC);
			if ($stmt12->rowCount() == 0) {
				$stmt2 = $this->db->prepare("INSERT INTO tbl_participants_details(cid,pid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:pid,:uid,:cuid,:uname,:ulname,:uemail,:uphone,NOW())");
				$stmt2->bindparam(":cid", $cid);
				$stmt2->bindparam(":pid", $pid);
				$stmt2->bindparam(":uid", $gotid);
				$stmt2->bindparam(":cuid", $uid);
				$stmt2->bindparam(":uname", $name);
				$stmt2->bindparam(":ulname", $lname);
				$stmt2->bindparam(":uemail", $email);
				$stmt2->bindparam(":uphone", $phone);
				$stmt2->execute();
				return $this->db->lastInsertId();
			} else {
				return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function email_manage_post_participants($id, $uid, $uname, $ulname, $uemail, $uphone, $cname, $cemail, $cphone, $ctitle, $is_clogo, $clogo, $hashcamp, $campid) {
		try {
			try {
				//Find Distributor ID
				$stmt90 = $this->db->prepare("SELECT u.*
				FROM tbl_campaign c
				INNER JOIN tbl_tree tr ON c.fld_uid = tr.uid
				INNER JOIN tbl_users u ON u.fld_uid = tr.did
				WHERE c.fld_campaign_id = '$campid'
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
					$is_Company_Title = 0;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				//SparkPost Init
				$httpClient = new GuzzleAdapter(new Client());
				$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
				//SparkPost Init
				$sparky->setOptions(['async' => false]);
				$campaign_id_subject = "$Campaign_Title ($Campaign_Title and $ParticipantFName $ParticipantLName needs your help.)";
				$promise = $sparky->transmissions->post([
					'content' => ['template_id' => 'participants-template'],
					'substitution_data' => [
						"sHOMECMS" => sHOMECMS,
						"sHOME" => sHOME,
						"id" => $id,
						"uid" => $uid,
						"uname" => $uname,
						"ulname" => $ulname,
						"uemail" => $uemail,
						"uphone" => $uphone,
						"cname" => $cname,
						"cemail" => $cemail,
						"cphone" => $cphone,
						"ctitle" => $ctitle,
						"is_clogo" => $is_clogo,
						"clogo" => $clogo,
						"hashcamp" => $hashcamp,
						"campid" => $campid,
						"fromemail" => "info@ufund4us.com",
						"refimage" => $refimage,
						"is_refimage" => $is_refimage,
						"Company_Title" => $Company_Title,
						"is_Company_Title" => $is_Company_Title,
					],
					'description' => $ctitle,
					'metadata' => [
						'Campaign_ID' => "$campid",
						'Campaign_Name' => "$ctitle",
						'Subject' => "Campaign Join Confirmation",
					],
					'recipients' => [
						[
							'address' => [
								'name' => $uname . ' ' . $ulname,
								'email' => $uemail,
							],
						],
					],
				]);
				$transmissionid = $promise->getBody()['results']['id'];
				$stmt = $this->db->prepare("UPDATE tbl_participants_details SET sent_email = 1, sent_id = '$transmissionid' WHERE cid = '$cid' AND id = '$id'");
				$stmt->execute();
			} catch (\Exception $e) {
				echo $e->getCode() . "\n";
				echo $e->getMessage() . "\n";
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function generate_link($id, $ufname, $ulname, $uemail, $refferallink, $linktype, $emailfname, $emaillname, $email_from, $email_to, $email_cc, $email_msg) {
		try {
			try {
				$stmt90 = $this->db->prepare("SELECT u.*
				FROM tbl_tree tr
				INNER JOIN tbl_users u ON (tr.uid = u.fld_uid OR tr.did = u.fld_uid)
				WHERE tr.uid = '$id' AND u.fld_role_id = '3'
				ORDER BY tr.nid ASC
				LIMIT 1");
				$stmt90->execute();
				if ($stmt90->rowCount() > 0) {
					$refRow = $stmt90->fetch(PDO::FETCH_ASSOC);
					$refimage = $refRow['fld_brand_logo_header'];
					$is_refimage = true;
					$Company_Title = $refRow['fld_cname'];
					$is_Company_Title = 0;
				} else {
					$refimage = '';
					$is_refimage = false;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				//SparkPost Init
				$httpClient = new GuzzleAdapter(new Client());
				$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
				//SparkPost Init
				$sparky->setOptions(['async' => false]);
				if ($email_cc != '') {
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'generate-link-4us'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"ufname" => $ufname,
							"ulname" => $ulname,
							"uemail" => $uemail,
							"roletype" => $linktype,
							"refferallink" => $refferallink,
							"email_msg" => $email_msg,
							"stfname" => $emailfname,
							"stlname" => $emaillname,
							"stemail" => $email_to,
							"refimage" => $refimage,
							"is_refimage" => $is_refimage,
							"fromemail" => "info@ufund4us.com",
							"Company_Title" => $Company_Title,
							"is_Company_Title" => $is_Company_Title,
						],
						'description' => "Generate Link Invitation",
						'metadata' => [
							'Subject' => "Generate Link Invitation",
						],
						'recipients' => [
							[
								'address' => [
									'name' => $emailfname . ' ' . $emaillname,
									'email' => $email_to,
								],
							],
						],
						'cc' => [
							[
								'address' => [
									'name' => $stfname . " " . $stlname,
									'email' => $email_cc,
								],
							],
						],
					]);
				} else {
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'generate-link-4us'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"ufname" => $ufname,
							"ulname" => $ulname,
							"uemail" => $uemail,
							"roletype" => $linktype,
							"refferallink" => $refferallink,
							"email_msg" => $email_msg,
							"stfname" => $emailfname,
							"stlname" => $emaillname,
							"stemail" => $email_to,
							"refimage" => $refimage,
							"is_refimage" => $is_refimage,
							"fromemail" => "info@ufund4us.com",
							"Company_Title" => $Company_Title,
							"is_Company_Title" => $is_Company_Title,
						],
						'description' => "Generate Link Invitation",
						'metadata' => [
							'Subject' => "Generate Link Invitation",
						],
						'recipients' => [
							[
								'address' => [
									'name' => $emailfname . ' ' . $emaillname,
									'email' => $email_to,
								],
							],
						],
					]);
				}
				$transmissionid = $promise->getBody()['results']['id'];
				if ($transmissionid) {
					return 1;
				} else {
					return 0;
				}
			} catch (\Exception $e) {
				echo $e->getCode() . "\n";
				echo $e->getMessage() . "\n";
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function hierarchy_campaign_details($uid) {
		try {
			$stmt = $this->db->prepare("SELECT c.*, u.fld_cname AS fld_company
			FROM tbl_campaign c
			LEFT JOIN tbl_tree tr ON tr.uid = c.fld_uid
			LEFT JOIN tbl_users u ON tr.did = u.fld_uid
			WHERE tr.aid = '$uid' OR tr.did = '$uid' OR tr.rid = '$uid' OR tr.uid = '$uid'
			GROUP BY c.fld_campaign_id");
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function email_manage_post_donors($Is_Campaign_Logo, $Campaign_Logo, $Is_ParticipantImage, $ParticipantImage, $DonorId, $DonorFName, $DonorLName, $Campaign_Id, $Campaign_HashKey, $Campaign_Title, $Campaign_Organization, $ParticipantId, $ParticipantFName, $ParticipantLName, $DonorEmail) {
		try {
			try {
				//Find Distributor ID
				$stmt90 = $this->db->prepare("SELECT u.*
				FROM tbl_campaign c
				INNER JOIN tbl_tree tr ON c.fld_uid = tr.uid
				INNER JOIN tbl_users u ON u.fld_uid = tr.did
				WHERE c.fld_campaign_id = '$Campaign_Id'
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
					$is_Company_Title = 0;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				//SparkPost Init
				$httpClient = new GuzzleAdapter(new Client());
				$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
				//SparkPost Init
				$sparky->setOptions(['async' => false]);
				$promise = $sparky->transmissions->post([
					'content' => ['template_id' => 'donors-template'],
					'substitution_data' => [
						'sHOMECMS' => sHOMECMS,
						'sHOME' => sHOME,
						'Is_Campaign_Logo' => $Is_Campaign_Logo,
						'Campaign_Logo' => $Campaign_Logo,
						'Is_ParticipantImage' => $Is_ParticipantImage,
						'ParticipantImage' => $ParticipantImage,
						'DonorId' => $DonorId,
						'DonorFName' => $DonorFName,
						'DonorLName' => $DonorLName,
						'Campaign_Id' => $Campaign_Id,
						'Campaign_HashKey' => $Campaign_HashKey,
						'Campaign_Title' => $Campaign_Title,
						'Campaign_Organization' => $Campaign_Organization,
						'ParticipantId' => $ParticipantId,
						'ParticipantFName' => $ParticipantFName,
						'ParticipantLName' => $ParticipantLName,
						'fromemail' => "info@ufund4us.com",
						'refimage' => $refimage,
						'is_refimage' => $is_refimage,
						'Company_Title' => $Company_Title,
						'is_Company_Title' => $is_Company_Title,
					],
					'description' => $Campaign_Title,
					'metadata' => [
						'Campaign_ID' => "$Campaign_Id",
						'Campaign_Name' => "$Campaign_Title",
						'Subject' => "$Campaign_Title and $ParticipantFName $ParticipantLName needs your help",
					],
					'recipients' => [
						[
							'address' => [
								'name' => $DonorFName . " " . $DonorLName,
								'email' => $DonorEmail,
							],
						],
					],
				]);
				$TransmissionID = $promise->getBody()['results']['id'];
				$stmt = $this->db->prepare("UPDATE tbl_donors_details SET sent_email = 1, sent_id = '$TransmissionID' WHERE cid = '$Campaign_Id' AND puid = '$ParticipantId' AND uid = '$DonorId'");
				$stmt->execute();
			} catch (\Exception $e) {
				echo $e->getCode() . "\n";
				echo $e->getMessage() . "\n";
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_campaign_donors1($cid, $cuid, $name, $lname, $email, $phone, $password, $using, $participantid, $participantname) {
		try {
			if ($email != '' && $phone == '') {
				$stmt10 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_email='$email' AND fld_role_id = '4'");
			} elseif ($email == '' && $phone != '') {
				$stmt10 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_phone='$phone' AND fld_role_id = '4'");
			} else {
				$stmt10 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_email='$email' AND fld_role_id = '4'");
			}
			$stmt10->execute();
			$stmt10->fetchall(PDO::FETCH_ASSOC);
			if ($stmt10->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO tbl_users(fld_role_id,fld_status,fld_email,fld_password,fld_name,fld_lname,fld_phone,fld_join_date) VALUES ('4','1',:fld_email,:fld_password,:fld_name,:fld_lname,:fld_phone,NOW())");
				//$stmt->bindparam(":fld_role_id", "4");
				//$stmt->bindparam(":fld_status", "1");
				$stmt->bindparam(":fld_email", $email);
				$stmt->bindparam(":fld_password", $password);
				$stmt->bindparam(":fld_name", $name);
				$stmt->bindparam(":fld_lname", $lname);
				$stmt->bindparam(":fld_phone", $phone);
				$stmt->execute();
				$gotid = $this->db->lastInsertId();
			}
			$stmt11 = $this->db->prepare("SELECT * FROM tbl_donors WHERE cid='$cid' AND puid='$cuid' AND uid='$gotid'");
			$stmt11->execute();
			$stmt11->fetchall(PDO::FETCH_ASSOC);
			if ($stmt11->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO tbl_donors(cid,uid,puid,uname,ulname,uemail,uphone,creationdate,usedas,participantid,participantname) VALUES (:cid,:uid,:puid,:uname,:ulname,:uemail,:uphone,NOW(),:usedas,:participantid,:participantname)");
				$stmt->bindparam(":cid", $cid);
				$stmt->bindparam(":uid", $gotid);
				$stmt->bindparam(":puid", $cuid);
				$stmt->bindparam(":uname", $name);
				$stmt->bindparam(":ulname", $lname);
				$stmt->bindparam(":uemail", $email);
				$stmt->bindparam(":uphone", $phone);
				$stmt->bindparam(":usedas", $using);
				$stmt->bindparam(":participantid", $participantid);
				$stmt->bindparam(":participantname", $participantname);
				$stmt->execute();
				$pid = $this->db->lastInsertId();
			}
			$stmt12 = $this->db->prepare("SELECT * FROM tbl_donors_details WHERE cid='$cid' AND puid='$cuid' AND uid='$gotid'");
			$stmt12->execute();
			$stmt12->fetchall(PDO::FETCH_ASSOC);
			if ($stmt12->rowCount() == 0) {
				$stmt2 = $this->db->prepare("INSERT INTO tbl_donors_details(cid,did,uid,puid,uname,ulname,uemail,uphone,creationdate,usedas,participantid,participantname) VALUES (:cid,:did,:uid,:puid,:uname,:ulname,:uemail,:uphone,NOW(),:usedas,:participantid,:participantname)");
				$stmt2->bindparam(":cid", $cid);
				$stmt2->bindparam(":did", $pid);
				$stmt2->bindparam(":uid", $gotid);
				$stmt2->bindparam(":puid", $cuid);
				$stmt2->bindparam(":uname", $name);
				$stmt2->bindparam(":ulname", $lname);
				$stmt2->bindparam(":uemail", $email);
				$stmt2->bindparam(":uphone", $phone);
				$stmt2->bindparam(":usedas", $using);
				$stmt2->bindparam(":participantid", $participantid);
				$stmt2->bindparam(":participantname", $participantname);
				$stmt2->execute();
				return $this->db->lastInsertId();
			} else {
				return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_campaign_donors2($cid, $gotid, $cuid, $name, $lname, $email, $phone, $using, $participantid, $participantname) {
		try {
			$stmt11 = $this->db->prepare("SELECT * FROM tbl_donors WHERE cid='$cid' AND uid='$gotid' AND puid='$cuid'");
			$stmt11->execute();
			$stmt11->fetchall(PDO::FETCH_ASSOC);
			if ($stmt11->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO tbl_donors(cid,uid,puid,uname,ulname,uemail,uphone,creationdate,usedas,participantid,participantname) VALUES (:cid,:uid,:puid,:uname,:ulname,:uemail,:uphone,NOW(),:usedas,:participantid,:participantname)");
				$stmt->bindparam(":cid", $cid);
				$stmt->bindparam(":uid", $gotid);
				$stmt->bindparam(":puid", $cuid);
				$stmt->bindparam(":uname", $name);
				$stmt->bindparam(":ulname", $lname);
				$stmt->bindparam(":uemail", $email);
				$stmt->bindparam(":uphone", $phone);
				$stmt->bindparam(":usedas", $using);
				$stmt->bindparam(":participantid", $participantid);
				$stmt->bindparam(":participantname", $participantname);
				$stmt->execute();
				$pid = $this->db->lastInsertId();
			}
			$stmt12 = $this->db->prepare("SELECT * FROM tbl_donors_details WHERE cid='$cid' AND uid='$gotid' AND puid='$cuid'");
			$stmt12->execute();
			$stmt12->fetchall(PDO::FETCH_ASSOC);
			if ($stmt12->rowCount() == 0) {
				$stmt2 = $this->db->prepare("INSERT INTO tbl_donors_details(cid,did,uid,puid,uname,ulname,uemail,uphone,creationdate,usedas,participantid,participantname) VALUES (:cid,:did,:uid,:puid,:uname,:ulname,:uemail,:uphone,NOW(),:usedas,:participantid,:participantname)");
				$stmt2->bindparam(":cid", $cid);
				$stmt2->bindparam(":did", $pid);
				$stmt2->bindparam(":uid", $gotid);
				$stmt2->bindparam(":puid", $cuid);
				$stmt2->bindparam(":uname", $name);
				$stmt2->bindparam(":ulname", $lname);
				$stmt2->bindparam(":uemail", $email);
				$stmt2->bindparam(":uphone", $phone);
				$stmt2->bindparam(":usedas", $using);
				$stmt2->bindparam(":participantid", $participantid);
				$stmt2->bindparam(":participantname", $participantname);
				$stmt2->execute();
				return $this->db->lastInsertId();
			} else {
				return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_campaign_participants1($cid, $cuid, $name, $lname, $email, $phone, $password) {
		try {
			/*$stmt10 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_email='$email'");
				$stmt10->execute();
				$stmt10->fetchall(PDO::FETCH_ASSOC);
				if($stmt10->rowCount() == 0)
				{
					$stmt = $this->db->prepare("INSERT INTO tbl_users(fld_role_id,fld_status,fld_email,fld_password,fld_name,fld_lname,fld_phone,fld_join_date) VALUES ('5','1','".$email."','".$password."','".$name."','".$lname."','".$phone."',NOW())");
					$stmt->execute();
					$gotid = $this->db->lastInsertId();
			*/
			$gotid = '';
			$stmt11 = $this->db->prepare("SELECT * FROM tbl_participants WHERE cid='$cid' AND uemail='$email'");
			$stmt11->execute();
			$pidrow = $stmt11->fetch(PDO::FETCH_ASSOC);
			if ($stmt11->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO tbl_participants(cid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:uid,:cuid,:uname,:ulname,:uemail,:uphone,NOW())");
				$stmt->bindparam(":cid", $cid);
				$stmt->bindparam(":uid", $gotid);
				$stmt->bindparam(":cuid", $cuid);
				$stmt->bindparam(":uname", $name);
				$stmt->bindparam(":ulname", $lname);
				$stmt->bindparam(":uemail", $email);
				$stmt->bindparam(":uphone", $phone);
				$stmt->execute();
				$pid = $this->db->lastInsertId();
			} else {
				$pid = $pidrow['id'];
			}
			$stmt12 = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid='$cid' AND uemail='$email'");
			$stmt12->execute();
			$stmt12->fetchall(PDO::FETCH_ASSOC);
			if ($stmt12->rowCount() == 0) {
				$stmt2 = $this->db->prepare("INSERT INTO tbl_participants_details(cid,pid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:pid,:uid,:cuid,:uname,:ulname,:uemail,:uphone,NOW())");
				$stmt2->bindparam(":cid", $cid);
				$stmt2->bindparam(":pid", $pid);
				$stmt2->bindparam(":uid", $gotid);
				$stmt2->bindparam(":cuid", $cuid);
				$stmt2->bindparam(":uname", $name);
				$stmt2->bindparam(":ulname", $lname);
				$stmt2->bindparam(":uemail", $email);
				$stmt2->bindparam(":uphone", $phone);
				$stmt2->execute();
				return $this->db->lastInsertId();
			} else {
				return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_campaign_participants2($cid, $gotid, $cuid, $name, $lname, $email, $phone) {
		try {
			if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
				$stmt11 = $this->db->prepare("SELECT * FROM tbl_participants WHERE cid='$cid' AND uid='$gotid'");
			} else {
				$stmt11 = $this->db->prepare("SELECT * FROM tbl_participants WHERE cid='$cid' AND cuid='$cuid' AND uid='$gotid'");
			}
			$stmt11->execute();
			$pidrow = $stmt11->fetch(PDO::FETCH_ASSOC);
			if ($stmt11->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO tbl_participants(cid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:uid,:cuid,:uname,:ulname,:uemail,:uphone,NOW())");
				$stmt->bindparam(":cid", $cid);
				$stmt->bindparam(":uid", $gotid);
				$stmt->bindparam(":cuid", $cuid);
				$stmt->bindparam(":uname", $name);
				$stmt->bindparam(":ulname", $lname);
				$stmt->bindparam(":uemail", $email);
				$stmt->bindparam(":uphone", $phone);
				$stmt->execute();
				$pid = $this->db->lastInsertId();
			} else {
				$pid = $pidrow['id'];
			}
			if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) {
				$stmt12 = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid='$cid' AND uid='$gotid'");
			} else {
				$stmt12 = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid='$cid' AND cuid='$cuid' AND uid='$gotid'");
			}
			$stmt12->execute();
			$stmt12->fetchall(PDO::FETCH_ASSOC);
			if ($stmt12->rowCount() == 0) {
				$stmt2 = $this->db->prepare("INSERT INTO tbl_participants_details(cid,pid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:pid,:uid,:cuid,:uname,:ulname,:uemail,:uphone,NOW())");
				$stmt2->bindparam(":cid", $cid);
				$stmt2->bindparam(":pid", $pid);
				$stmt2->bindparam(":uid", $gotid);
				$stmt2->bindparam(":cuid", $cuid);
				$stmt2->bindparam(":uname", $name);
				$stmt2->bindparam(":ulname", $lname);
				$stmt2->bindparam(":uemail", $email);
				$stmt2->bindparam(":uphone", $phone);
				$stmt2->execute();
				return $this->db->lastInsertId();
			} else {
				return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function delete_donors_details($cid, $pid, $did) {
		try {
			$stmt = $this->db->prepare("DELETE FROM tbl_donors_details WHERE cid = '$cid' AND puid = '$pid' AND uid = '$did'");
			$stmt->execute();
			return $this->db->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function delete_donors($cid, $pid, $did) {
		try {
			$stmt = $this->db->prepare("DELETE FROM tbl_donors WHERE cid = '$cid' AND puid = '$pid' AND uid = '$did'");
			$stmt->execute();
			return $this->db->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function isreadupdate($cid, $pid, $hashid) {
		try {
			$stmt = $this->db->prepare("UPDATE tbl_donors_details SET is_read = 1 WHERE cid = '$cid' AND puid = '$pid' AND uid = '$hashid'");
			$stmt->execute();
			return $this->db->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function delete_participants_details($cid, $pid) {
		try {
			$stmt = $this->db->prepare("DELETE FROM tbl_participants_details WHERE cid = '$cid' AND id = '$pid'");
			$stmt->execute();
			return $this->db->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function send_email_to_updatedonor($cid, $pid, $did) {
		try {
			//include_once('class.phpmailer.php');
			//include_once('class.smtp.php');
			$stmt = $this->db->prepare("SELECT pd.fld_email AS pemail, pd.fld_name AS pname, pd.fld_lname AS plname, pd.fld_phone AS pphone, u.fld_uid, pd.fld_image, u.fld_ftime, u.fld_password, u.fld_name, u.fld_email, u.fld_phone, CONCAT(b.fld_cname,' ',IFNULL(b.fld_clname,'')) AS fld_cname, b.fld_cemail, b.fld_cphone, b.fld_campaign_title, b.fld_campaign_logo, b.fld_campaign_id, b.fld_hashcamp, a.uname, a.ulname, a.uid, b.fld_organization_name, b.fld_campaign_edate
			FROM tbl_donors_details a
			LEFT JOIN tbl_users u ON a.uid = u.fld_uid
			LEFT JOIN tbl_users pd ON pd.fld_uid = a.puid
			LEFT JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
			WHERE a.cid = '$cid' AND a.puid = '$pid' AND a.uid = '$did' AND b.fld_campaign_edate >= NOW()");
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
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
					$is_Company_Title = 0;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				//$j = 0;
				while ($email_manager_Row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$uid = $email_manager_Row['fld_uid'];
					$uname = $email_manager_Row['fld_name'];
					$uemail = $email_manager_Row['fld_email'];
					$uphone = $email_manager_Row['fld_phone'];
					$cname = $email_manager_Row['fld_cname'];
					$cemail = $email_manager_Row['fld_cemail'];
					$cphone = $email_manager_Row['fld_cphone'];
					$ctitle = $email_manager_Row['fld_campaign_title'];
					$corganization = $email_manager_Row['fld_organization_name'];
					$clogo = $email_manager_Row['fld_campaign_logo'];
					$uimage = $email_manager_Row['fld_image'];
					$donorname = $email_manager_Row['uname'];
					$donorlname = $email_manager_Row['ulname'];
					$donorid = $email_manager_Row['uid'];
					$pname = $email_manager_Row['pname'];
					$plname = $email_manager_Row['plname'];
					$pphone = $email_manager_Row['pphone'];
					$pemail = $email_manager_Row['pemail'];
					$hashcamp = $email_manager_Row['fld_hashcamp'];
					$campid = $email_manager_Row['fld_campaign_id'];
					$linkcreate = '' . sHOME . 'campaign.php?cid=' . $hashcamp . '|' . $campid . '|' . $pid . '&hashid=' . $uid . '';
					$linkcreate2 = '' . sHOME . 'unsubscribe.php?cid=' . $campid . '&pid=' . $pid . '&did=' . $uid . '';
					$fld_enddate1 = date('Y-m-d', strtotime($email_manager_Row['fld_campaign_edate']));
					$current_date1 = date("Y-m-d H:i:s");
					$from = date_create($fld_enddate1 . " 23:59:59");
					$to = date_create($current_date1);
					$diff = date_diff($to, $from);
					$TimeLeft = $diff->format('%a Days, %H Hours');
					if ($clogo != '') {
						$shlogo = $clogo;
						$Is_Campaign_Logo = true;
					} else {
						$shlogo = $ctitle;
						$Is_Campaign_Logo = false;
					}
					if ($uimage != '') {
						$uhlogo = $uimage;
						$Is_ParticipantImage = true;
					} else {
						$uhlogo = '';
						$Is_ParticipantImage = false;
					}
					try {
						//SparkPost Init
						$httpClient = new GuzzleAdapter(new Client());
						$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
						//SparkPost Init
						$sparky->setOptions(['async' => false]);
						$promise = $sparky->transmissions->post([
							'content' => ['template_id' => 'donors-template'],
							'substitution_data' => [
								"sHOMECMS" => sHOMECMS,
								"sHOME" => sHOME,
								"Is_Campaign_Logo" => $Is_Campaign_Logo,
								"Campaign_Logo" => $shlogo,
								"Is_ParticipantImage" => $Is_ParticipantImage,
								"ParticipantImage" => $uhlogo,
								"DonorId" => $donorid,
								"DonorFName" => $donorname,
								"DonorLName" => $donorlname,
								"Campaign_Id" => $campid,
								"Campaign_HashKey" => $hashcamp,
								"Campaign_Title" => $ctitle,
								"Campaign_Organization" => $corganization,
								"ParticipantId" => $pid,
								"ParticipantFName" => $pname,
								"ParticipantLName" => $plname,
								"ParticipantEmail" => $pemail,
								"TimeLeft" => $TimeLeft,
								"fromemail" => "info@ufund4us.com",
								"refimage" => $refimage,
								"is_refimage" => $is_refimage,
								"Company_Title" => $Company_Title,
								"is_Company_Title" => $is_Company_Title,
							],
							'description' => $ctitle,
							'metadata' => [
								'Campaign_ID' => "$campid",
								'Campaign_Name' => "$ctitle",
								'Subject' => "$ctitle and $pname $plname needs your help",
							],
							'recipients' => [
								[
									'address' => [
										'name' => $donorname . ' ' . $donorlname,
										'email' => $uemail,
									],
								],
							],
						]);
						$transmissionid = $promise->getBody()['results']['id'];
						$stmt55 = $this->db->prepare("UPDATE tbl_donors_details SET sent_id = '$transmissionid', sent_email = '1' WHERE cid = '$cid' AND puid = '$pid' AND uid = '$donorid'");
						$stmt55->execute();
					} catch (\Exception $e) {
						echo $e->getCode() . "\n";
						echo $e->getMessage() . "\n";
					}
				}
			}
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function send_email_to_donors($cid, $pid) {
		try {
			//include_once('class.phpmailer.php');
			//include_once('class.smtp.php');
			$stmt = $this->db->prepare("SELECT a.sms_sent_id, a.sent_id, b.fld_text_messaging, b.fld_campaign_hashkey, pd.fld_email AS pemail, pd.fld_name AS pname, pd.fld_lname AS plname, pd.fld_phone AS pphone, u.fld_uid, pd.fld_image, u.fld_ftime, u.fld_password, u.fld_name, u.fld_email, u.fld_phone, CONCAT(b.fld_cname,' ',IFNULL(b.fld_clname,'')) AS fld_cname, b.fld_cemail, b.fld_cphone, b.fld_campaign_title, b.fld_campaign_logo, b.fld_campaign_id, b.fld_hashcamp, a.uname, a.ulname, a.uphone, a.uid, b.fld_organization_name, b.fld_campaign_edate
			FROM tbl_donors_details a
			LEFT JOIN tbl_users u ON a.uid = u.fld_uid
			LEFT JOIN tbl_users pd ON pd.fld_uid = a.puid
			LEFT JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
			WHERE a.cid = '$cid' AND a.sent_email = 0 AND a.is_unsubscribe = 0 AND a.puid = '$pid' AND b.fld_campaign_edate >= CURDATE()");
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
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
					$is_Company_Title = 0;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				//$j = 0;
				while ($email_manager_Row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$fld_text_messaging = $email_manager_Row['fld_text_messaging'];
					$fld_campaign_hashkey = $email_manager_Row['fld_campaign_hashkey'];
					$email_sent = true;
					$sms_sent = true;
					$email_sent1 = $email_manager_Row['sent_id'];
					$sms_sent1 = $email_manager_Row['sms_sent_id'];
					if ($email_sent1 != '') {
						$email_sent = false;
					}
					if ($sms_sent1 != '') {
						$sms_sent = false;
					}
					$uid = $email_manager_Row['fld_uid'];
					$uname = $email_manager_Row['fld_name'];
					$uemail = $email_manager_Row['fld_email'];
					$uphone = $email_manager_Row['fld_phone'];
					$cname = $email_manager_Row['fld_cname'];
					$cemail = $email_manager_Row['fld_cemail'];
					$cphone = $email_manager_Row['fld_cphone'];
					$ctitle = $email_manager_Row['fld_campaign_title'];
					$corganization = $email_manager_Row['fld_organization_name'];
					$clogo = $email_manager_Row['fld_campaign_logo'];
					$uimage = $email_manager_Row['fld_image'];
					$donorname = $email_manager_Row['uname'];
					$donorlname = $email_manager_Row['ulname'];
					$donorphone = $email_manager_Row['uphone'];
					$donorid = $email_manager_Row['uid'];
					$pname = $email_manager_Row['pname'];
					$plname = $email_manager_Row['plname'];
					$pphone = $email_manager_Row['pphone'];
					$pemail = $email_manager_Row['pemail'];
					$hashcamp = $email_manager_Row['fld_hashcamp'];
					$campid = $email_manager_Row['fld_campaign_id'];
					$linkcreate = '' . sHOME . 'campaign.php?cid=' . $hashcamp . '|' . $campid . '|' . $pid . '&hashid=' . $uid . '';
					$linkcreate2 = '' . sHOME . 'unsubscribe.php?cid=' . $campid . '&pid=' . $pid . '&did=' . $uid . '';
					$fld_enddate1 = date('Y-m-d', strtotime($email_manager_Row['fld_campaign_edate']));
					$current_date1 = date("Y-m-d H:i:s");
					$from = date_create($fld_enddate1 . " 23:59:59");
					$to = date_create($current_date1);
					$diff = date_diff($to, $from);
					$TimeLeft = $diff->format('%a Days, %H Hours');
					if ($clogo != '') {
						$shlogo = $clogo;
						$Is_Campaign_Logo = true;
					} else {
						$shlogo = $ctitle;
						$Is_Campaign_Logo = false;
					}
					if ($uimage != '') {
						$uhlogo = $uimage;
						$Is_ParticipantImage = true;
					} else {
						$uhlogo = '';
						$Is_ParticipantImage = false;
					}
					if ($uemail != '' && $email_sent == true) {
					try {
						//SparkPost Init
						$httpClient = new GuzzleAdapter(new Client());
						$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
						//SparkPost Init
						$sparky->setOptions(['async' => false]);
						$promise = $sparky->transmissions->post([
							'content' => ['template_id' => 'donors-template'],
							'substitution_data' => [
								"sHOMECMS" => sHOMECMS,
								"sHOME" => sHOME,
								"Is_Campaign_Logo" => $Is_Campaign_Logo,
								"Campaign_Logo" => $shlogo,
								"Is_ParticipantImage" => $Is_ParticipantImage,
								"ParticipantImage" => $uhlogo,
								"DonorId" => $donorid,
								"DonorFName" => $donorname,
								"DonorLName" => $donorlname,
								"Campaign_Id" => $campid,
								"Campaign_HashKey" => $hashcamp,
								"Campaign_Title" => $ctitle,
								"Campaign_Organization" => $corganization,
								"ParticipantId" => $pid,
								"ParticipantFName" => $pname,
								"ParticipantLName" => $plname,
								"ParticipantEmail" => $pemail,
								"TimeLeft" => $TimeLeft,
								"fromemail" => "info@ufund4us.com",
								"refimage" => $refimage,
								"is_refimage" => $is_refimage,
								"Company_Title" => $Company_Title,
								"is_Company_Title" => $is_Company_Title,
							],
							'description' => $ctitle,
							'metadata' => [
								'Campaign_ID' => "$campid",
								'Campaign_Name' => "$ctitle",
								'Subject' => "$ctitle and $pname $plname needs your help",
							],
							'recipients' => [
								[
									'address' => [
										'name' => $donorname . ' ' . $donorlname,
										'email' => $uemail,
									],
								],
							],
						]);
						$transmissionid = $promise->getBody()['results']['id'];
						$stmt55 = $this->db->prepare("UPDATE tbl_donors_details SET sent_id = '$transmissionid', sent_email = '1', sent_date = NOW() WHERE cid = '$cid' AND puid = '$pid' AND uid = '$donorid'");
						$stmt55->execute();
					} catch (\Exception $e) {
						echo $e->getCode() . "\n";
						echo $e->getMessage() . "\n";
					}
					}
					//SMS Sending
					if ($fld_text_messaging == 1 && $sms_sent == true) {
						$generate_short_link = ''.str_replace('app/','',sHOME).'l.php?v='.$fld_campaign_hashkey.'&u='.$pid.'&d='.$donorid.'';
						$ParticipantFullName = trim($pname." ".$plname);
						$body = "Hi! It’s $ParticipantFullName. Please take a second to view a fundraiser that I am participating in by clicking the link below.\n";
						$body .= "".$generate_short_link."\n";
						$body .= "Thank You!";
						if ($donorphone != "" && $donorphone != "000-000-0000" && $donorphone != "___-___-____") {
							if ($sent_sms = send_sms(str_replace("-","",$donorphone),utf8_encode($body))) {
								$sms_status = 1;
								$sms_sent_id = $sent_sms['sid'];
								if ($sms_sent_id != '') {
									$sms_date_created = $sent_sms['date_created'];
									$sms_message = $sent_sms['message'];
									$sms_details = $sms_message;
									
									$tz = new DateTimeZone('America/Los_Angeles');
									$sms_date = new DateTime($sms_date_created);
									$sms_date->setTimezone($tz);
									$sms_date_created = $sms_date->format('Y-m-d h:i:s');
									
									//Update the donor sms sent record
									$stmt_sms = $this->db->prepare("UPDATE tbl_donors_details SET sms_sent_id = '$sms_sent_id', sms_sent_date = '$sms_date_created' WHERE cid = '$cid' AND uid = '$donorid' AND puid = '$pid'");
									$executed = $stmt_sms->execute();
									//Track the record of SMS details
									$stmt_sms2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','$donorid','$pid','$donorname','$donorlname','$uemail','$donorphone','$sms_date_created','$ParticipantFullName','3','$sms_sent_id','$sms_date_created','1','$sms_details')");
									$executed2 = $stmt_sms2->execute();
								}
							} else {
								//Error when sending
							}
						} else {
							//Invalid Number
						}
					}
				}
			}
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function resent_donors($cid, $pid, $did) {
		try {
			//include_once('class.phpmailer.php');
			//include_once('class.smtp.php');
			$curr_date = date("l");
			$stmt = $this->db->prepare("SELECT pd.fld_email AS pemail, pd.fld_name AS pname, pd.fld_lname AS plname, pd.fld_phone AS pphone, u.fld_uid, pd.fld_image, u.fld_ftime, u.fld_password, u.fld_name, u.fld_email, u.fld_phone, CONCAT(b.fld_cname,' ',IFNULL(b.fld_clname,'')) AS fld_cname, b.fld_cemail, b.fld_cphone, b.fld_campaign_title, b.fld_campaign_logo, b.fld_campaign_id, b.fld_hashcamp, a.uname, a.ulname, b.fld_organization_name, b.fld_campaign_edate
			FROM tbl_donors_details a
			INNER JOIN tbl_users u ON a.uid = u.fld_uid
			INNER JOIN tbl_users pd ON pd.fld_uid = '$pid'
			INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
			WHERE a.cid = '$cid' AND a.puid='$pid' AND a.is_unsubscribe = 0 AND a.uid = '$did' AND b.fld_campaign_edate >= CURDATE()");
			$stmt->execute();
			//$email_manager_Row = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
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
					$is_Company_Title = 0;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				//$j = 0;
				//foreach ($email_manager_Row as $row) {
				while ($email_manager_Row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$uid = $email_manager_Row['fld_uid'];
					$uname = $email_manager_Row['fld_name'];
					$uemail = $email_manager_Row['fld_email'];
					$uphone = $email_manager_Row['fld_phone'];
					$cname = $email_manager_Row['fld_cname'];
					$cemail = $email_manager_Row['fld_cemail'];
					$cphone = $email_manager_Row['fld_cphone'];
					$ctitle = $email_manager_Row['fld_campaign_title'];
					$corganization = $email_manager_Row['fld_organization_name'];
					$clogo = $email_manager_Row['fld_campaign_logo'];
					$uimage = $email_manager_Row['fld_image'];
					$donorname = $email_manager_Row['uname'];
					$donorlname = $email_manager_Row['ulname'];
					$pname = $email_manager_Row['pname'];
					$plname = $email_manager_Row['plname'];
					$pphone = $email_manager_Row['pphone'];
					$pemail = $email_manager_Row['pemail'];
					$hashcamp = $email_manager_Row['fld_hashcamp'];
					$campid = $email_manager_Row['fld_campaign_id'];
					$fld_enddate1 = date('Y-m-d', strtotime($email_manager_Row['fld_campaign_edate']));
					$current_date1 = date("Y-m-d H:i:s");
					$from = date_create($fld_enddate1 . " 23:59:59");
					$to = date_create($current_date1);
					$diff = date_diff($to, $from);
					$TimeLeft = $diff->format('%a Days, %H Hours');
					//$uftime = $row[$j][fld_ftime];
					$linkcreate = '' . sHOME . 'campaign.php?cid=' . $hashcamp . '|' . $campid . '|' . $pid . '&hashid=' . $uid . '';
					$linkcreate2 = '' . sHOME . 'unsubscribe.php?cid=' . $campid . '&pid=' . $pid . '&did=' . $uid . '';
					if ($clogo != '') {
						$shlogo = $clogo;
						$Is_Campaign_Logo = true;
					} else {
						$shlogo = $ctitle;
						$Is_Campaign_Logo = false;
					}
					if ($uimage != '') {
						$uhlogo = $uimage;
						$Is_ParticipantImage = true;
					} else {
						$uhlogo = '';
						$Is_ParticipantImage = false;
					}
					try {
						//SparkPost Init
						$httpClient = new GuzzleAdapter(new Client());
						$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
						//SparkPost Init
						$sparky->setOptions(['async' => false]);
						$promise = $sparky->transmissions->post([
							'content' => ['template_id' => 'donors-template'],
							'substitution_data' => [
								"sHOMECMS" => sHOMECMS,
								"sHOME" => sHOME,
								"Is_Campaign_Logo" => $Is_Campaign_Logo,
								"Campaign_Logo" => $shlogo,
								"Is_ParticipantImage" => $Is_ParticipantImage,
								"ParticipantImage" => $uhlogo,
								"DonorId" => $did,
								"DonorFName" => $donorname,
								"DonorLName" => $donorlname,
								"Campaign_Id" => $campid,
								"Campaign_HashKey" => $hashcamp,
								"Campaign_Title" => $ctitle,
								"Campaign_Organization" => $corganization,
								"ParticipantId" => $pid,
								"ParticipantFName" => $pname,
								"ParticipantLName" => $plname,
								"ParticipantEmail" => $pemail,
								"TimeLeft" => $TimeLeft,
								"fromemail" => "info@ufund4us.com",
								"refimage" => $refimage,
								"is_refimage" => $is_refimage,
								"Company_Title" => $Company_Title,
								"is_Company_Title" => $is_Company_Title,
							],
							'description' => $ctitle,
							'metadata' => [
								'Campaign_ID' => "$campid",
								'Campaign_Name' => "$ctitle",
								'Subject' => "$ctitle and $pname $plname needs your help",
							],
							'recipients' => [
								[
									'address' => [
										'name' => $donorname . ' ' . $donorlname,
										'email' => $uemail,
									],
								],
							],
						]);
						$transmissionid = $promise->getBody()['results']['id'];
						$stmt55 = $this->db->prepare("UPDATE tbl_donors_details SET sent_id = '$transmissionid', sent_email = '1' WHERE cid = '$cid' AND puid = '$pid' AND uid = '$did'");
						$stmt55->execute();
					} catch (\Exception $e) {
						echo $e->getCode() . "\n";
						echo $e->getMessage() . "\n";
					}
				}
			}
			return $email_manager_Row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function generatelinkemail($user_name, $ufname, $ulname, $uemail, $uphone, $linktype, $emailfname, $emaillname, $email_from, $email_to, $email_cc, $email_msg, $refferallink) {
		try {
			try {
				//SparkPost Init
				$httpClient = new GuzzleAdapter(new Client());
				$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
				//SparkPost Init
				$sparky->setOptions(['async' => false]);
				if ($email_cc != '') {
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'link-invitation'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"user_name" => $user_name,
							"ufname" => $ufname,
							"ulname" => $ulname,
							"uemail" => $uemail,
							"uphone" => $uphone,
							"linktype" => $linktype,
							"refferallink" => $refferallink,
							"emailfname" => $emailfname,
							"emaillname" => $emaillname,
							"email_to" => $email_to,
							"email_msg" => $email_msg,
						],
						'description' => "Link Invitation",
						'metadata' => [
							'Campaign_ID' => "",
							'Campaign_Name' => "",
							'Subject' => "Link Invitation",
						],
						'recipients' => [
							[
								'address' => [
									'name' => $emailfname . " " . $emaillname,
									'email' => $email_to,
								],
							],
						],
						'cc' => [
							[
								'address' => [
									'name' => $emailfname . " " . $emaillname,
									'email' => $email_cc,
								],
							],
						],
					]);
				} else {
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'link-invitation'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"user_name" => $user_name,
							"ufname" => $ufname,
							"ulname" => $ulname,
							"uemail" => $uemail,
							"uphone" => $uphone,
							"linktype" => $linktype,
							"refferallink" => $refferallink,
							"emailfname" => $emailfname,
							"emaillname" => $emaillname,
							"email_to" => $email_to,
							"email_msg" => $email_msg,
						],
						'description' => "Link Invitation",
						'metadata' => [
							'Campaign_ID' => "",
							'Campaign_Name' => "",
							'Subject' => "Link Invitation",
						],
						'recipients' => [
							[
								'address' => [
									'name' => $emailfname . " " . $emaillname,
									'email' => $email_to,
								],
							],
						],
					]);
				}
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
	public function resent_participants_details($cid, $pid) {
		try {
			$stmt = $this->db->prepare("SELECT a.uname, a.ulname, a.uemail, a.uphone, a.pid, a.id, a.pid, a.uid,
			CONCAT(b.fld_cname,' ',IFNULL(b.fld_clname,'')) AS fld_cname, b.fld_cemail, b.fld_cphone, b.fld_campaign_title, b.fld_campaign_logo, b.fld_campaign_id, b.fld_hashcamp
			FROM tbl_participants_details a
			INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
			WHERE a.cid = '$cid' AND a.is_unsubscribe = 0 AND a.id = '$pid' AND b.fld_campaign_edate >= NOW()");
			$stmt->execute();
			//$email_manager_Row = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
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
					$is_Company_Title = 0;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				//$j = 0;
				//foreach ($email_manager_Row as $row) {
				while ($email_manager_Row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$id = $email_manager_Row['id'];
					//$uid = $email_manager_Row['uid'];
					$uid = $email_manager_Row['pid'];
					$uname = $email_manager_Row['uname'];
					$ulname = $email_manager_Row['ulname'];
					$uemail = $email_manager_Row['uemail'];
					$uphone = $email_manager_Row['uphone'];
					$cname = $email_manager_Row['fld_cname'];
					$cemail = $email_manager_Row['fld_cemail'];
					$cphone = $email_manager_Row['fld_cphone'];
					$ctitle = $email_manager_Row['fld_campaign_title'];
					$clogo = $email_manager_Row['fld_campaign_logo'];
					$hashcamp = $email_manager_Row['fld_hashcamp'];
					$campid = $email_manager_Row['fld_campaign_id'];
					if ($clogo != '') {
						$shlogo = $clogo;
						$is_clogo = true;
					} else {
						$shlogo = $ctitle;
						$is_clogo = false;
					}
					$linkcreate = sHOME . 'signup.php?cid=' . $campid . '&refferalid=' . $uid . '';
					$linkcreate2 = '' . sHOME . 'unsubscribe.php?cid=' . $campid . '&pid=' . $uid . '';
					$attachedfile = sHOME . 'cms/UFund4Us Instructions.pdf';
					//Sparkpost Participant Template
					try {
						//SparkPost Init
						$httpClient = new GuzzleAdapter(new Client());
						$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
						//SparkPost Init
						$sparky->setOptions(['async' => false]);
						$campaign_id_subject = "$Campaign_Title ($Campaign_Title and $ParticipantFName $ParticipantLName needs your help.)";
						$promise = $sparky->transmissions->post([
							'content' => ['template_id' => 'participants-template'],
							'substitution_data' => [
								"sHOMECMS" => sHOMECMS,
								"sHOME" => sHOME,
								"id" => $id,
								"uid" => $uid,
								"uname" => $uname,
								"ulname" => $ulname,
								"uemail" => $uemail,
								"uphone" => $uphone,
								"cname" => $cname,
								"cemail" => $cemail,
								"cphone" => $cphone,
								"ctitle" => $ctitle,
								"is_clogo" => $is_clogo,
								"clogo" => $clogo,
								"hashcamp" => $hashcamp,
								"campid" => $campid,
								"fromemail" => "info@ufund4us.com",
								"refimage" => $refimage,
								"is_refimage" => $is_refimage,
								"Company_Title" => $Company_Title,
								"is_Company_Title" => $is_Company_Title,
							],
							'description' => $ctitle,
							'metadata' => [
								'Campaign_ID' => "$campid",
								'Campaign_Name' => "$ctitle",
								'Subject' => "Campaign Join Confirmation",
							],
							'recipients' => [
								[
									'address' => [
										'name' => $uname . ' ' . $ulname,
										'email' => $uemail,
									],
								],
							],
						]);
						$transmissionid = $promise->getBody()['results']['id'];
						$stmt55 = $this->db->prepare("UPDATE tbl_participants_details SET sent_email = '1' WHERE cid = '$cid' AND id = '$id'");
						$stmt55->execute();
					} catch (\Exception $e) {
						echo $e->getCode() . "\n";
						echo $e->getMessage() . "\n";
					}
					//Sparkpost Participant Template
				}
			}
			return $email_manager_Row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function sendstartyourcampaign($fld_name, $fld_organization, $fld_email, $fld_phone, $fld_call, $sn_dist_email, $getemail, $getname) {
		try {
			if ($sn_dist_email == 1) {
				$email_name = $getname;
				$email_address = $getemail;
			} else {
				$email_name = 'UFund4Us Administrator';
				$email_address = 'info@ufund4us.com';
			}
			//SparkPost Init
			$httpClient = new GuzzleAdapter(new Client());
			$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
			//SparkPost Init
			$sparky->setOptions(['async' => false]);
			$promise = $sparky->transmissions->post([
				'content' => ['template_id' => 'start-your-campaign'],
				'substitution_data' => [
					"sHOMECMS" => sHOMECMS,
					"sHOME" => sHOME,
					"fld_name" => $fld_name,
					"fld_organization" => $fld_organization,
					"fld_email" => $fld_email,
					"fld_phone" => $fld_phone,
					"fld_call" => $fld_call,
					"fromemail" => "info@ufund4us.com",
				],
				'description' => "$fld_name requested to start a campaign",
				'metadata' => [
					'Campaign_ID' => "",
					'Campaign_Name' => "",
					'Subject' => "$fld_name requested to start a campaign",
				],
				'recipients' => [
					[
						'address' => [
							'name' => $email_name,
							'email' => $email_address,
						],
					],
				],
			]);
			try {
				$transmissionid = $promise->getBody()['results']['id'];
			} catch (\Exception $e) {
				echo $e->getCode() . "\n";
				echo $e->getMessage() . "\n";
			}
			return $promise;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function delete_participants($cid, $pemail) {
		try {
			$stmt = $this->db->prepare("DELETE FROM tbl_participants WHERE uemail = '$pemail'");
			$stmt->execute();
			return $this->db->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function donation_email($transactionid, $cid, $pmid, $donoremail, $donateamount, $donorname, $donorlname, $rewardid, $isreward, $reward_desc) {
		try {
			$stmt = $this->db->prepare("SELECT a.uname, a.ulname, a.uemail, a.uphone, a.pid, a.id, a.uid,
			CONCAT(b.fld_cname,' ',IFNULL(b.fld_clname,'')) AS fld_cname, b.fld_cemail, b.fld_cphone, b.fld_campaign_title, b.fld_organization_name, b.fld_nonprofit, b.fld_nonprofit_number, b.fld_campaign_logo, b.fld_campaign_id, b.fld_hashcamp
			FROM tbl_participants_details a
			INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
			WHERE a.cid = '$cid' AND a.uid = '$pmid'");
			$stmt->execute();
			//$email_manager_Row = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
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
					$is_Company_Title = 0;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				//$j = 0;
				//foreach ($email_manager_Row as $row) {
				while ($email_manager_Row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$id = $email_manager_Row['id'];
					$uid = $email_manager_Row['uid'];
					$uname = $email_manager_Row['uname'];
					$ulname = $email_manager_Row['ulname'];
					$uemail = $email_manager_Row['uemail'];
					$uphone = $email_manager_Row['uphone'];
					$cname = $email_manager_Row['fld_cname'];
					$cemail = $email_manager_Row['fld_cemail'];
					$cphone = $email_manager_Row['fld_cphone'];
					$ctitle = $email_manager_Row['fld_campaign_title'];
					$corganname = $email_manager_Row['fld_organization_name'];
					$nonprofit = $email_manager_Row['fld_nonprofit'];
					$nonprofitnumber = $email_manager_Row['fld_nonprofit_number'];
					$clogo = $email_manager_Row['fld_campaign_logo'];
					$hashcamp = $email_manager_Row['fld_hashcamp'];
					$campid = $email_manager_Row['fld_campaign_id'];
					if ($clogo != '') {
						$shlogo = $clogo;
						$is_clogo = true;
					} else {
						$shlogo = $ctitle;
						$is_clogo = false;
					}
					$linkcreate = '' . sHOME . 'startyourcampaign.php';
					try {
						//SparkPost Init
						$httpClient = new GuzzleAdapter(new Client());
						$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
						//SparkPost Init
						$sparky->setOptions(['async' => false]);
						$promise = $sparky->transmissions->post([
							'content' => ['template_id' => 'donation-template'],
							'substitution_data' => [
								"sHOMECMS" => sHOMECMS,
								"sHOME" => sHOME,
								"id" => $id,
								"uid" => $uid,
								"uname" => $uname,
								"ulname" => $ulname,
								"uemail" => $uemail,
								"uphone" => $uphone,
								"cname" => $cname,
								"cemail" => $cemail,
								"cphone" => $cphone,
								"ctitle" => $ctitle,
								"corganname" => $corganname,
								"nonprofit" => $nonprofit,
								"nonprofitnumber" => $nonprofitnumber,
								"is_clogo" => $is_clogo,
								"clogo" => $clogo,
								"rewardid" => $rewardid,
								"isreward" => $isreward,
								"reward_desc" => $reward_desc,
								"hashcamp" => $hashcamp,
								"campid" => $campid,
								"donoremail" => $donoremail,
								"donateamount" => $donateamount,
								"donorname" => $donorname,
								"donorlname" => $donorlname,
								"fromemail" => "info@ufund4us.com",
								"refimage" => $refimage,
								"is_refimage" => $is_refimage,
								"Company_Title" => $Company_Title,
								"is_Company_Title" => $is_Company_Title,
							],
							'description' => $ctitle,
							'metadata' => [
								'Campaign_ID' => "$campid",
								'Campaign_Name' => "$ctitle",
								'Subject' => "Ufund4Us Donation Receipt",
							],
							'recipients' => [
								[
									'address' => [
										'name' => $donorname . ' ' . $donorlname,
										'email' => $donoremail,
									],
								],
							],
						]);
						$TransmissionID = $promise->getBody()['results']['id'];
						$update_trans_id = $this->db->prepare(" UPDATE tbl_donations SET email_sent_id = '$TransmissionID', email_sent_date = NOW() WHERE id = '$transactionid' ");
						$update_trans_id->execute();
					} catch (\Exception $e) {
						echo $e->getCode() . "\n";
						echo $e->getMessage() . "\n";
					}
				}
			}
			return $email_manager_Row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chkcampaign($cid, $cuid) {
		try {
			$stmt = $this->db->prepare("SELECT fld_status, DATEDIFF(CURDATE(), fld_campaign_sdate) AS daysstart, DATEDIFF(fld_campaign_edate, CURDATE()) AS daysend, DATEDIFF(fld_campaign_edate, fld_campaign_sdate) AS startenddate, DATEDIFF(fld_campaign_sdate, CURDATE()) AS daysleft FROM tbl_campaign WHERE fld_campaign_id = '$cid'");
			$stmt->execute();
			$CalRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $CalRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chkcampaign2($cid) {
		try {
			$stmt = $this->db->prepare("SELECT fld_uid, fld_status, DATEDIFF(CURDATE(), fld_campaign_sdate) AS daysstart, DATEDIFF(fld_campaign_edate, CURDATE()) AS daysend, DATEDIFF(fld_campaign_edate, fld_campaign_sdate) AS startenddate, DATEDIFF(fld_campaign_sdate, CURDATE()) AS daysleft FROM tbl_campaign WHERE fld_campaign_id = '$cid'");
			$stmt->execute();
			$CalRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $CalRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function participants_details($pid, $cid) {
		try {
			$stmt = $this->db->prepare("SELECT b.fld_campaign_id, b.fld_campaign_title, b.fld_cname, b.fld_donor_size,
				(SELECT COUNT(c.id) FROM tbl_donors_details c WHERE c.cid = a.cid AND c.puid = '$pid') AS donoruploaded,
				(SELECT IFNULL(SUM(d.donation_amount),0) FROM tbl_donations d INNER JOIN tbl_donors_details e ON e.uid = d.uid AND e.cid = d.cid WHERE e.puid = '$pid' AND d.mode = '1') AS moneyraised
				FROM tbl_participants_details a
				INNER JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
				WHERE a.uid = '$pid'");
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function participants_details2($pid, $cid) {
		try {
			$stmt = $this->db->prepare("SELECT b.fld_campaign_id, b.fld_campaign_title, b.fld_cname, b.fld_donor_size,
				(SELECT COUNT(c.id) FROM tbl_donors_details c WHERE c.cid = a.cid AND c.puid = '$pid') AS donoruploaded,
				(SELECT IFNULL(SUM(d.donation_amount),0) FROM tbl_donations d INNER JOIN tbl_donors_details e ON e.uid = d.uid AND e.cid = d.cid WHERE e.puid = '$pid' AND d.mode = '1') AS moneyraised
				FROM tbl_participants_details a
				INNER JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
				WHERE a.uid = '$pid'");
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function top10moneyraiser($cid) {
		try {
			$stmt = $this->db->prepare("SELECT b.fld_campaign_id, b.fld_campaign_title, b.fld_cname, e.uname, e.ulname,
			(SELECT IFNULL(SUM(c.donation_amount),0) FROM tbl_donations c WHERE c.cid='$cid' AND c.refferal_by = e.uid) AS moneyraised
			FROM tbl_donations a
			INNER JOIN tbl_participants_details e ON e.cid = a.cid AND e.uid = a.refferal_by
			INNER JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
			WHERE a.cid='$cid' AND a.mode = '1' GROUP BY e.uid ORDER BY moneyraised DESC LIMIT 10");
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function top10donors($cid) {
		try {
			$stmt = $this->db->prepare("SELECT ufname, ulname, uemail, uphone, donation_amount, displaylisted
			FROM tbl_donations
			WHERE cid='$cid' AND mode = '1'
			ORDER BY donation_amount DESC");
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function donors_details2($pid, $cid) {
		try {
			$stmt = $this->db->prepare("SELECT a.* FROM tbl_donations a INNER JOIN tbl_donors_details b ON a.uid = b.uid
			WHERE a.cid = '$cid' AND b.puid = '$pid' AND a.mode = '1' GROUP BY a.id");
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getallcampaigns($uid, $rid) {
		try {
			if ($rid == 1) {
				//role id 1
				$stmt = $this->db->prepare("SELECT * FROM tbl_campaign WHERE fld_campaign_sdate != '' AND fld_campaign_edate >= DATE(NOW())");
			} elseif ($rid == 3) {
				$stmt = $this->db->prepare("SELECT c.fld_campaign_id, c.fld_campaign_title
				FROM tbl_tree tr
				LEFT JOIN tbl_campaign c ON tr.uid = c.fld_uid
				WHERE tr.did = '$uid' AND c.fld_campaign_edate >= DATE(NOW())");
			} elseif ($rid == 6) {
				$stmt = $this->db->prepare("SELECT c.fld_campaign_id, c.fld_campaign_title
				FROM tbl_tree tr
				LEFT JOIN tbl_campaign c ON tr.uid = c.fld_uid
				WHERE tr.rid = '$uid' AND c.fld_campaign_edate >= DATE(NOW())");
			} else {
				$stmt = $this->db->prepare("SELECT * FROM tbl_campaign WHERE fld_uid = '$uid' AND fld_campaign_sdate != '' AND fld_campaign_edate >= DATE(NOW())");
			}
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getallcampaigns2() {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_campaign WHERE fld_campaign_sdate != '' AND fld_campaign_edate != '' AND fld_campaign_title != ''");
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function viewparticipants1($cid) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_campaign WHERE fld_campaign_id = '$cid' AND fld_status = 1");
			$stmt->execute();
			$stmtRow = $stmt->fetch(PDO::FETCH_ASSOC);
			return $stmtRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function viewparticipants2($cid) {
		try {
			$stmt = $this->db->prepare("SELECT a.uid, a.puid, a.uname, a.ulname, a.uemail, a.uphone
					  FROM tbl_donors_details a
					  WHERE a.cid = '$cid' AND a.is_unsubscribe = 0 AND NOT EXISTS
					  (SELECT NULL FROM tbl_donations b WHERE b.cid = '$cid' AND b.uid = a.uid AND b.mode = '1')");
			$stmt->execute();
			$stmtRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $stmtRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function viewparticipants3($pid) {
		try {
			$stmt = $this->db->prepare("SELECT fld_name AS pname, fld_lname AS plname, fld_email AS pemail, fld_phone AS pphone, fld_image FROM tbl_users WHERE fld_uid = '$pid'");
			$stmt->execute();
			$stmtRow = $stmt->fetch(PDO::FETCH_ASSOC);
			return $stmtRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function donors_donated($did) {
		try {
			$stmt = $this->db->prepare("SELECT a.ufname AS donationfname, a.ulname AS donationlname, a.uemail AS donationemail, c.fld_cname, c.fld_campaign_title, d.uname AS pname, d.ulname AS plname, d.uemail AS pemail, b.uname, b.ulname, b.uemail, b.uphone, a.donation_amount, a.id AS transactionno, DATE_FORMAT(a.creationdate, '%m/%d/%Y') AS donationdate
			FROM tbl_donations a
			LEFT JOIN tbl_donors_details b ON a.uid = b.uid
			LEFT JOIN tbl_campaign c ON c.fld_campaign_id = a.cid
			LEFT JOIN tbl_participants_details d ON d.uid = a.refferal_by
			WHERE b.uid = '$did' AND a.mode = '1' GROUP BY a.id");
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function donors_donated1($did) {
		try {
			$stmt = $this->db->prepare("SELECT c.fld_cname, c.fld_campaign_title, d.uname AS pname, d.ulname AS plname, d.uemail AS pemail, b.uname, b.ulname, b.uemail, b.uphone
			FROM  tbl_donors_details b
			LEFT JOIN tbl_campaign c ON c.fld_campaign_id = b.cid
			LEFT JOIN tbl_participants_details d ON d.uid = b.puid
			WHERE b.uid = '$did'");
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function participant_donors_details($pid, $cid) {
		try {
			if ($_SESSION['role_id'] == '5') {
				$stmt = $this->db->prepare("SELECT a.*, b.uname AS participantname, b.ulname AS participantlname FROM tbl_donors_details a INNER JOIN tbl_participants_details b ON a.puid = b.uid
				WHERE b.cid = '$cid' AND b.uid = '$pid'");
			} else {
				$stmt = $this->db->prepare("SELECT a.*, b.uname AS participantname, b.ulname AS participantlname FROM tbl_donors_details a INNER JOIN tbl_participants_details b ON a.puid = b.uid
				WHERE b.cid = '$cid'");
			}
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function participant_donation_details($pid, $cid) {
		try {
			if ($_SESSION['role_id'] == '5') {
				$stmt = $this->db->prepare("SELECT a.*, b.uname AS participantname, b.ulname AS participantlname FROM tbl_donations a INNER JOIN tbl_participants_details b ON a.refferal_by = b.uid
				WHERE b.cid = '$cid' AND b.uid = '$pid' AND a.mode = '1'");
			} else {
				$stmt = $this->db->prepare("SELECT a.*, b.uname AS participantname, b.ulname AS participantlname FROM tbl_donations a INNER JOIN tbl_participants_details b ON a.refferal_by = b.uid
				WHERE b.cid = '$cid' AND a.mode = '1'");
			}
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function participants_details3($pid) {
		try {
			$stmt = $this->db->prepare("SELECT b.fld_campaign_id, b.fld_campaign_title, b.fld_cname, b.fld_donor_size,
				(SELECT COUNT(c.id) FROM tbl_donors_details c WHERE c.cid = a.cid AND c.puid = '$pid') AS donoruploaded,
				(SELECT IFNULL(SUM(d.donation_amount),0) FROM tbl_donations d INNER JOIN tbl_donors_details e ON e.uid = d.uid AND e.cid = d.cid WHERE e.puid = '$pid' AND d.cid = a.cid AND d.mode = '1') AS moneyraised
				FROM tbl_participants_details a
				INNER JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
				WHERE a.uid = '$pid'");
			$stmt->execute();
			$ParRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $ParRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function makeitlive($cid, $cuid, $live, $camphash) {
		try {
			$stmt2 = $this->db->prepare("SELECT fld_hashcamp FROM tbl_campaign WHERE fld_campaign_id = '$cid'");
			$stmt2->execute();
			$ParRow = $stmt2->fetch(PDO::FETCH_ASSOC);
			$hashkey = $ParRow['fld_hashcamp'];
			if ($hashkey != '') {
				$stmt = $this->db->prepare("UPDATE tbl_campaign SET fld_live = '$live' WHERE fld_campaign_id = '$cid'");
			} else {
				$stmt = $this->db->prepare("UPDATE tbl_campaign SET fld_live = '$live', fld_hashcamp = '$camphash' WHERE fld_campaign_id = '$cid'");
			}
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function send_email_to_manager($cid, $cuid) {
		try {
			//include_once('class.phpmailer.php');
			//include_once('class.smtp.php');
			$stmt = $this->db->prepare("SELECT CONCAT(a.fld_cname,' ',IFNULL(a.fld_clname,'')) AS fld_cname, a.fld_cemail, a.fld_cphone, a.fld_campaign_title, c.fld_name, c.fld_lname, c.fld_email, c.fld_campaign_logo
			FROM tbl_campaign a
			INNER JOIN tbl_tree b ON b.uid = a.fld_uid
			INNER JOIN tbl_users c ON c.fld_uid = b.rid
			WHERE a.fld_campaign_id = '$cid'");
			$stmt->execute();
			$email_manager_Row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
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
					$is_Company_Title = 0;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				$cname = $email_manager_Row['fld_cname'];
				$cemail = $email_manager_Row['fld_cemail'];
				$cphone = $email_manager_Row['fld_cphone'];
				$ctitle = $email_manager_Row['fld_campaign_title'];
				$clogo = $email_manager_Row['fld_campaign_logo'];
				if ($clogo != '') {
					$shlogo = $clogo;
					$is_clogo = true;
				} else {
					$shlogo = $ctitle;
					$is_clogo = false;
				}
				$rfname = $email_manager_Row['fld_name'];
				$rlname = $email_manager_Row['fld_lname'];
				$remail = $email_manager_Row['fld_email'];
				try {
					//SparkPost Init
					$httpClient = new GuzzleAdapter(new Client());
					$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
					//SparkPost Init
					$sparky->setOptions(['async' => false]);
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'campaign-live-confirmation'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"cname" => $cname,
							"cemail" => $cemail,
							"cphone" => $cphone,
							"ctitle" => $ctitle,
							"rfname" => $rfname,
							"rlname" => $rlname,
							"remail" => $remail,
							"is_clogo" => $is_clogo,
							"clogo" => $clogo,
							"fromemail" => "info@ufund4us.com",
							"refimage" => $refimage,
							"is_refimage" => $is_refimage,
							"Company_Title" => $Company_Title,
							"is_Company_Title" => $is_Company_Title,
						],
						'description' => $ctitle,
						'metadata' => [
							'Campaign_ID' => "$cid",
							'Campaign_Name' => "$ctitle",
							'Subject' => "Campaign Live Confirmation",
						],
						'recipients' => [
							[
								'address' => [
									'name' => $cname,
									'email' => $cemail,
								],
							],
						],
					]);
					$transmissionid = $promise->getBody()['results']['id'];
				} catch (\Exception $e) {
					echo $e->getCode() . "\n";
					echo $e->getMessage() . "\n";
				}
				return $email_manager_Row;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function send_email_to_participants($cid, $cuid) {
		try {
			$stmt = $this->db->prepare("SELECT b.fld_text_messaging, b.fld_campaign_hashkey, a.id, a.cid, a.uid, a.pid, a.uname, a.ulname, a.uemail, a.uphone,
			CONCAT(b.fld_cname,' ',IFNULL(b.fld_clname,'')) AS fld_cname, b.fld_cemail, b.fld_cphone, b.fld_campaign_title, b.fld_campaign_logo, b.fld_hashcamp, b.fld_campaign_id
			FROM tbl_participants_details a
			INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
			WHERE a.cid = '$cid' AND a.is_unsubscribe = 0 AND a.sent_email = '0' AND b.fld_campaign_edate >= NOW()");
			$stmt->execute();
			//$email_manager_Row = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
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
					$is_Company_Title = 0;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				$j = 0;
				//foreach ($email_manager_Row as $row) {
				while ($email_manager_Row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					//$cid = $cid;
					$id = $email_manager_Row['id'];
					$uid = $email_manager_Row['pid'];
					$uid2 = $email_manager_Row['uid'];
					$uname = $email_manager_Row['uname'];
					$ulname = $email_manager_Row['ulname'];
					$uemail = $email_manager_Row['uemail'];
					$uphone = $email_manager_Row['uphone'];
					$cname = $email_manager_Row['fld_cname'];
					$cemail = $email_manager_Row['fld_cemail'];
					$cphone = $email_manager_Row['fld_cphone'];
					$ctitle = $email_manager_Row['fld_campaign_title'];
					$clogo = $email_manager_Row['fld_campaign_logo'];
					$hashcamp = $email_manager_Row['fld_hashcamp'];
					$campid = $email_manager_Row['fld_campaign_id'];
					$fld_campaign_hashkey = $email_manager_Row['fld_campaign_hashkey'];
					$fld_text_messaging = $email_manager_Row['fld_text_messaging'];
					if ($clogo != '') {
						$shlogo = $clogo;
						$is_clogo = true;
					} else {
						$shlogo = $ctitle;
						$is_clogo = false;
					}
					$linkcreate = sHOME . 'signup.php?cid=' . $campid . '&refferalid=' . $uid . '';
					$linkcreate2 = '' . sHOME . 'unsubscribe.php?cid=' . $campid . '&pid=' . $uid . '';
					//Sparkpost Participant Template
					if ($uemail != '') {
					try {
						//SparkPost Init
						$httpClient = new GuzzleAdapter(new Client());
						$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
						//SparkPost Init
						$sparky->setOptions(['async' => false]);
						$campaign_id_subject = "$Campaign_Title ($Campaign_Title and $ParticipantFName $ParticipantLName needs your help.)";
						$promise = $sparky->transmissions->post([
							'content' => ['template_id' => 'participants-template'],
							'substitution_data' => [
								"sHOMECMS" => sHOMECMS,
								"sHOME" => sHOME,
								"id" => $id,
								"uid" => $uid,
								"uname" => $uname,
								"ulname" => $ulname,
								"uemail" => $uemail,
								"uphone" => $uphone,
								"cname" => $cname,
								"cemail" => $cemail,
								"cphone" => $cphone,
								"ctitle" => $ctitle,
								"is_clogo" => $is_clogo,
								"clogo" => $clogo,
								"hashcamp" => $hashcamp,
								"campid" => $campid,
								"fromemail" => "info@ufund4us.com",
								"refimage" => $refimage,
								"is_refimage" => $is_refimage,
								"Company_Title" => $Company_Title,
								"is_Company_Title" => $is_Company_Title,
							],
							'description' => $ctitle,
							'metadata' => [
								'Campaign_ID' => "$campid",
								'Campaign_Name' => "$ctitle",
								'Subject' => "Campaign Join Confirmation",
							],
							'recipients' => [
								[
									'address' => [
										'name' => $uname . ' ' . $ulname,
										'email' => $uemail,
									],
								],
							],
						]);
						$transmissionid = $promise->getBody()['results']['id'];
						$stmt55 = $this->db->prepare("UPDATE tbl_participants_details SET sent_id = '$transmissionid', sent_date = NOW(), sent_email = '1' WHERE cid = '$cid' AND id = '$id'");
						$stmt55->execute();
					} catch (\Exception $e) {
						echo $e->getCode() . "\n";
						echo $e->getMessage() . "\n";
					}
					}
					//Sparkpost Participant Template
					
					//SMS Sending
					if ($fld_text_messaging == 1) {
						$generate_short_link = ''.str_replace('app/','',sHOME).'l.php?v='.$fld_campaign_hashkey.'&u='.$uid.'&m=1';
						$ParticipantFullName = trim($uname." ".$ulname);
						$body = "Hi! It’s $ParticipantFullName.\n";
						$body = "You are being invited to join $ctitle by $cname. Please click on the link below to join this campaign.\n";
						$body .= "".$generate_short_link."\n";
						$body .= "Thank You!";
						if ($uphone != "" && $uphone != "000-000-0000" && $uphone != "___-___-____") {
							if ($sent_sms = send_sms(str_replace("-","",$uphone),utf8_encode($body))) {
								$sms_status = 1;
								$sms_sent_id = $sent_sms['sid'];
								if ($sms_sent_id != '') {
									$sms_date_created = $sent_sms['date_created'];
									$sms_message = $sent_sms['message'];
									$sms_details = $sms_message;
									
									$tz = new DateTimeZone('America/Los_Angeles');
									$sms_date = new DateTime($sms_date_created);
									$sms_date->setTimezone($tz);
									$sms_date_created = $sms_date->format('Y-m-d h:i:s');
									
									//Update the donor sms sent record
									$stmt_sms = $this->db->prepare("UPDATE tbl_participants_details SET sms_sent_id = '$sms_sent_id', sms_sent_date = '$sms_date_created' WHERE cid = '$cid' AND id = '$id'");
									$executed = $stmt_sms->execute();
									
									$stmt_get_participant = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$cid' AND id = '$id'");
									$stmt_get_participant->execute();
									if ($stmt_get_participant->rowCount() > 0) {
										$participantRow = $stmt_get_participant->fetch(PDO::FETCH_ASSOC);
										$pid = $participantRow['uid'];
										//Track the record of SMS details
										$stmt_sms2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','$did','$pid','$uname','$ulname','$uemail','$uphone','$sms_date_created','$cname','4','$sms_sent_id','$sms_date_created','1','$sms_details')");
										$executed2 = $stmt_sms2->execute();
									} else {
										//Track the record of SMS details
										$stmt_sms2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','$did','0','$uname','$ulname','$uemail','$uphone','$sms_date_created','$cname','4','$sms_sent_id','$sms_date_created','1','$sms_details')");
										$executed2 = $stmt_sms2->execute();
									}
								}
							} else {
								//Error when sending
							}
						} else {
							//Invalid Number
						}
					}
					return $email_manager_Row;
				}
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_state($iStateId, $iCountryId) {
		try {
			$stmt2 = $this->db->prepare("SELECT name FROM states WHERE name='$iStateId'");
			$stmt2->execute();
			$stateRow = $stmt2->fetch(PDO::FETCH_ASSOC);
			if ($stmt2->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO states(name, country_name) VALUES(:name, :country_name)");
				$stmt->bindparam(":name", $iStateId);
				$stmt->bindparam(":country_name", $iCountryId);
				$stmt->execute();
				return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdonordonations($cid, $uid, $pid, $email) {
		try {
			$stmt = $this->db->prepare("SELECT IFNULL(SUM(donation_amount),0.00) AS donation_amount, DATE_FORMAT(creationdate,'%m/%d/%Y') AS creationdate, uemail AS receiptemail FROM tbl_donations WHERE cid='$cid' AND (uid='$uid') AND refferal_by ='$pid' AND mode = '1'");
			$stmt->execute();
			$donationRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $donationRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_city($iCityId, $iStateId) {
		try {
			$stmt2 = $this->db->prepare("SELECT name FROM cities WHERE name='$iCityId'");
			$stmt2->execute();
			$stateRow = $stmt2->fetch(PDO::FETCH_ASSOC);
			if ($stmt2->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO cities(name, state_name) VALUES(:name, :state_name)");
				$stmt->bindparam(":name", $iCityId);
				$stmt->bindparam(":state_name", $iStateId);
				$stmt->execute();
				return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_acc_campaign($iId, $accid) {
		try {
			$stmt = $this->db->prepare("UPDATE tbl_campaign SET fld_ac = '$accid' WHERE fld_campaign_id = '$iId'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_campaign_step_1($iCid, $sName, $sLName, $sDob, $sSsn, $sEmail, $sPhone, $sAddress, $iCountryId, $iStateId, $iCityId, $sZipcode) {
		try {
			$stmt = $this->db->prepare("UPDATE tbl_campaign SET
			fld_cname=:sName,
			fld_clname=:sLName,
			fld_cemail=:sEmail,
			fld_cphone=:sPhone,
			fld_caddress=:sAddress,
			fld_ccountry=:iCountryId,
			fld_cstate=:iStateId,
			fld_ccity=:iCityId,
			fld_czipcode=:sZipcode,
			fld_dob=:sDob,
			fld_ssn=:sSsn,
			fld_active = '1',
			fld_status = '1'
			WHERE fld_campaign_id=:iCid");
			$stmt->bindparam(":sName", $sName);
			$stmt->bindparam(":sLName", $sLName);
			$stmt->bindparam(":sEmail", $sEmail);
			$stmt->bindparam(":sPhone", $sPhone);
			$stmt->bindparam(":sAddress", $sAddress);
			$stmt->bindparam(":iCountryId", $iCountryId);
			$stmt->bindparam(":iStateId", $iStateId);
			$stmt->bindparam(":iCityId", $iCityId);
			$stmt->bindparam(":sZipcode", $sZipcode);
			$stmt->bindparam(":sDob", $sDob);
			$stmt->bindparam(":sSsn", $sSsn);
			$stmt->bindparam(":iCid", $iCid);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_campaign($iCid, $sTitle, $sOrgName, $sTeamName, $sTeamSize, $sDonorSize, $sStartDate, $sEndDate, $sCGoal, $sPGoal, $ab1575pupilfee, $showparticipantgoal, $textmessaging, $rewards, $nonprofit, $nonprofit_number, $sDesc1, $sDesc2, $sDesc3, $sDonationLevel1, $sDonationLevel2, $sDonationLevel3, $generatepin, $fld_organization_type, $fld_organ_other, $fld_taxid_number, $fld_account_number, $payment_method, $fld_payable_to, $generatechash) {
		try {
			if ($nonprofit == 0) {
				$nonprofit_number = '';
			} else {
				$nonprofit_number = $nonprofit_number;
			}
			$stmt = $this->db->prepare("UPDATE tbl_campaign SET
			fld_campaign_title=:sTitle,
			fld_organization_name=:sOrgName,
			fld_team_name=:sTeamName,
			fld_team_size=:sTeamSize,
			fld_donor_size=:sDonorSize,
			fld_campaign_sdate=:sStartDate,
			fld_campaign_edate=:sEndDate,
			fld_campaign_goal=:sCGoal,
			fld_participant_goal=:sPGoal,
			fld_ab1575_pupil_fee=:ab1575pupilfee,
			fld_show_participant_goal=:showparticipantgoal,
			fld_text_messaging=:fld_text_messaging,
			fld_rewards=:rewards,
			fld_nonprofit=:nonprofit,
			fld_nonprofit_number=:nonprofit_number,
			fld_desc1=:sDesc1,
			fld_desc2=:sDesc2,
			fld_desc3=:sDesc3,
			fld_donation_level1=:sDonationLevel1,
			fld_donation_level2=:sDonationLevel2,
			fld_donation_level3=:sDonationLevel3,
			fld_pin=:generatepin,
			fld_organization_type=:organtype,
			fld_organ_other=:organother,
			fld_taxid_number=:taxidno,
			fld_bank_accno=:bankaccno,
			fld_payment_method=:paymentmethod,
			fld_payable_to=:payableto,
			fld_campaign_hashkey=:campaign_hashkey,
			fld_status=1,
			fld_active=1
			WHERE fld_campaign_id=:iCid");
			$stmt->bindparam(":sTitle", $sTitle);
			$stmt->bindparam(":sOrgName", $sOrgName);
			$stmt->bindparam(":sTeamName", $sTeamName);
			$stmt->bindparam(":sTeamSize", $sTeamSize);
			$stmt->bindparam(":sDonorSize", $sDonorSize);
			$stmt->bindparam(":sStartDate", $sStartDate);
			$stmt->bindparam(":sEndDate", $sEndDate);
			$stmt->bindparam(":sCGoal", $sCGoal);
			$stmt->bindparam(":sPGoal", $sPGoal);
			$stmt->bindparam(":ab1575pupilfee", $ab1575pupilfee);
			$stmt->bindparam(":showparticipantgoal", $showparticipantgoal);
			$stmt->bindparam(":fld_text_messaging", $textmessaging);
			$stmt->bindparam(":rewards", $rewards);
			$stmt->bindparam(":nonprofit", $nonprofit);
			$stmt->bindparam(":nonprofit_number", $nonprofit_number);
			$stmt->bindparam(":sDesc1", $sDesc1);
			$stmt->bindparam(":sDesc2", $sDesc2);
			$stmt->bindparam(":sDesc3", $sDesc3);
			$stmt->bindparam(":sDonationLevel1", $sDonationLevel1);
			$stmt->bindparam(":sDonationLevel2", $sDonationLevel2);
			$stmt->bindparam(":sDonationLevel3", $sDonationLevel3);
			$stmt->bindparam(":generatepin", $generatepin);
			$stmt->bindparam(":organtype", $fld_organization_type);
			$stmt->bindparam(":organother", $fld_organ_other);
			$stmt->bindparam(":taxidno", $fld_taxid_number);
			$stmt->bindparam(":bankaccno", $fld_account_number);
			$stmt->bindparam(":paymentmethod", $payment_method);
			$stmt->bindparam(":payableto", $fld_payable_to);
			$stmt->bindparam(":campaign_hashkey", $generatechash);
			$stmt->bindparam(":iCid", $iCid);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	//Donros Rewards
	public function donor_rewards($cid) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_rewards WHERE cid = '$cid' ORDER BY id ASC");
			$stmt->execute();
			$rewardsRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			/*if($stmt->rowCount() > 0)
				{
			*/
			return $rewardsRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_rewards($id, $cid, $uid, $reward_amt, $reward_desc, $reward_desc_details, $fullname) {
		try {
			$stmt = $this->db->prepare("INSERT INTO tbl_rewards (cid, uid, reward_amount, reward_desc, reward_desc_details, created_by, created_date) VALUES ('$cid', '$uid', '$reward_amt', '$reward_desc', '$reward_desc_details', '$fullname', NOW())");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_rewards($id, $cid, $uid, $reward_amt, $reward_desc, $reward_desc_details, $fullname) {
		try {
			$stmt = $this->db->prepare("UPDATE tbl_rewards SET reward_amount = '$reward_amt', reward_desc = '$reward_desc', reward_desc_details = '$reward_desc_details' WHERE id = '$id' AND cid = '$cid'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function delete_rewards($id, $cid, $uid, $reward_amt, $reward_desc, $reward_desc_details, $fullname) {
		try {
			$stmt = $this->db->prepare("DELETE FROM tbl_rewards WHERE id = '$id' AND cid = '$cid'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function get_rewards($cid, $id) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_rewards WHERE id = '$id' AND cid = '$cid'");
			$stmt->execute();
			$rewardsRow = $stmt->fetch(PDO::FETCH_ASSOC);
			return $rewardsRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	//Donros Rewards
	public function chk_campaign($c_number, $iId) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_campaign WHERE fld_campaign_id = '$c_number' OR fld_pin = '$c_number'");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_campaign2($searchby, $searchvalue) {
		try {
			if ($searchby == 'campid') {
				$stmt = $this->db->prepare("SELECT *, DATE_FORMAT(fld_campaign_sdate,'%m/%d/%Y') AS fld_campaign_sdate FROM tbl_campaign WHERE fld_pin LIKE '%$searchvalue%'");
			} elseif ($searchby == 'campno') {
				$stmt = $this->db->prepare("SELECT *, DATE_FORMAT(fld_campaign_sdate,'%m/%d/%Y') AS fld_campaign_sdate FROM tbl_campaign WHERE fld_campaign_id LIKE '%$searchvalue%'");
			} elseif ($searchby == 'organization') {
				$stmt = $this->db->prepare("SELECT *, DATE_FORMAT(fld_campaign_sdate,'%m/%d/%Y') AS fld_campaign_sdate FROM tbl_campaign WHERE fld_organization_name LIKE '%$searchvalue%'");
			}
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_already_joined($c_name, $cm_name, $campaignid, $iId, $ufname, $ulname, $uemail, $uphone) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$campaignid' AND uid = '$iId'");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_campaign_user($uid, $cuid, $cid, $cno) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE uid = '$uid' AND cid = '$cid' AND cuid='$cuid'");
			$stmt->execute();
			$userRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_campid($camp_id, $camp_no) {
		try {
			if ($camp_no != '' && $camp_id != '') {
				$stmt = $this->db->prepare("SELECT * FROM tbl_campaign WHERE fld_pin = '$camp_id' AND fld_campaign_id = '$camp_no'");
			} elseif ($camp_no != '' && $camp_id == '') {
				$stmt = $this->db->prepare("SELECT * FROM tbl_campaign WHERE fld_campaign_id = '$camp_no'");
			}
			if ($camp_no == '' && $camp_id != '') {
				$stmt = $this->db->prepare("SELECT * FROM tbl_campaign WHERE fld_pin = '$camp_id'");
			}
			$stmt->execute();
			$userRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_already_joined2($iId, $camp_number, $camp_id) {
		try {
			if ($iId != '' && $camp_number != '' && $camp_id != '') {
				$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$camp_number' AND uid = '$iId'");
			} elseif ($iId != '' && $camp_number == '' && $camp_id != '') {
				$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$campaignid' AND uid = '$iId'");
			} elseif ($iId != '' && $camp_number != '' && $camp_id == '') {
				$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$campaignid' AND uid = '$iId'");
			} elseif ($iId == '' && $camp_number != '' && $camp_id != '') {
				$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$campaignid' AND uid = '$iId'");
			} elseif ($iId == '' && $camp_number != '' && $camp_id == '') {
				$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$campaignid' AND uid = '$iId'");
			} elseif ($iId == '' && $camp_number == '' && $camp_id != '') {
				$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$campaignid' AND uid = '$iId'");
			}
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function join_campaign2($uid, $cuid, $cid, $cno) {
		try {
			$stmt10 = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_uid = '$uid'");
			$stmt10->execute();
			$userRow = $stmt10->fetch(PDO::FETCH_ASSOC);
			$ufname = $userRow['fld_name'];
			$ulname = $userRow['fld_lname'];
			$uemail = $userRow['fld_email'];
			$uphone = $userRow['fld_phone'];
			$stmt = $this->db->prepare("SELECT * FROM tbl_participants WHERE cid = '$cid' AND cuid = '$cuid' AND uid = '$uid'");
			$stmt->execute();
			if ($stmt->rowCount() == 0) {
				$stmt2 = $this->db->prepare("INSERT INTO tbl_participants (cid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:uid,:cuid,:uname,:ulname,:uemail,:uphone,NOW())");
				$stmt2->bindparam(":cid", $cid);
				$stmt2->bindparam(":uid", $uid);
				$stmt2->bindparam(":cuid", $cuid);
				$stmt2->bindparam(":uname", $ufname);
				$stmt2->bindparam(":ulname", $ulname);
				$stmt2->bindparam(":uemail", $uemail);
				$stmt2->bindparam(":uphone", $uphone);
				$stmt2->execute();
				$pid = $this->db->lastInsertId();
			} else {
				$participantRow = $stmt->fetch(PDO::FETCH_ASSOC);
				$pid = $participantRow['id'];
			}
			$stmt4 = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$cid' AND cuid = '$cuid' AND uid = '$uid'");
			$stmt4->execute();
			if ($stmt4->rowCount() == 0) {
				$stmt3 = $this->db->prepare("INSERT INTO tbl_participants_details (cid,pid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:pid,:uid,:cuid,:uname,:ulname,:uemail,:uphone,NOW())");
				$stmt3->bindparam(":cid", $cid);
				$stmt3->bindparam(":pid", $pid);
				$stmt3->bindparam(":uid", $uid);
				$stmt3->bindparam(":cuid", $cuid);
				$stmt3->bindparam(":uname", $ufname);
				$stmt3->bindparam(":ulname", $ulname);
				$stmt3->bindparam(":uemail", $uemail);
				$stmt3->bindparam(":uphone", $uphone);
				$stmt3->execute();
			}
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function join_campaign($c_name, $cm_name, $campaignid, $campaignmanagerid, $iId, $ufname, $ulname, $uemail, $uphone) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_participants WHERE cid = '$campaignid' AND uid = '$iId'");
			$stmt->execute();
			$userRow = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() == 0) {
				$stmt2 = $this->db->prepare("INSERT INTO tbl_participants (cid,uid,cuid,uname,uemail,uphone,creationdate) VALUES (:cid,:uid,:cuid,:uname,:uemail,:uphone,NOW())");
				$stmt2->bindparam(":cid", $campaignid);
				$stmt2->bindparam(":uid", $iId);
				$stmt2->bindparam(":cuid", $campaignmanagerid);
				$stmt2->bindparam(":uname", $ufname);
				$stmt2->bindparam(":ulname", $ulname);
				$stmt2->bindparam(":uemail", $uemail);
				$stmt2->bindparam(":uphone", $uphone);
				$stmt2->execute();
				$pid = $this->db->lastInsertId();
				$stmt3 = $this->db->prepare("INSERT INTO tbl_participants_details (cid,pid,uid,cuid,uname,ulname,uemail,uphone,creationdate) VALUES (:cid,:pid,:uid,:cuid,:uname,:uemail,:uphone,NOW())");
				$stmt3->bindparam(":cid", $campaignid);
				$stmt3->bindparam(":pid", $pid);
				$stmt3->bindparam(":uid", $iId);
				$stmt3->bindparam(":cuid", $campaignmanagerid);
				$stmt3->bindparam(":uname", $ufname);
				$stmt3->bindparam(":ulname", $ulname);
				$stmt3->bindparam(":uemail", $uemail);
				$stmt3->bindparam(":uphone", $uphone);
				$stmt3->execute();
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function user_redirect($refferalid, $cid) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$cid' AND pid = '$refferalid'");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_campaignlogo($iCid, $logo) {
		try {
			$stmt = $this->db->prepare("UPDATE tbl_campaign SET
			fld_campaign_logo='$logo'
			WHERE fld_campaign_id='$iCid'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_campaigngallery($iCid, $logo) {
		try {
			$stmt = $this->db->prepare("UPDATE tbl_campaign SET
			fld_campaign_logo='$logo'
			WHERE fld_campaign_id='$iCid'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function delete_campaign($id) {
		try {
			$stmt = $this->db->prepare("DELETE from tbl_user_to_campaign WHERE fld_campaign_id = '" . $id . "'");
			$stmt->execute();
			$stmt = $this->db->prepare("DELETE from tbl_donor_to_campaign WHERE fld_campaign_id = '" . $id . "'");
			$stmt->execute();
			$stmt = $this->db->prepare("DELETE from tbl_campaign WHERE fld_campaign_id = '" . $id . "'");
			$stmt->execute();
			$stmt = $this->db->prepare("DELETE from tbl_participants WHERE cid = '" . $id . "'");
			$stmt->execute();
			$stmt = $this->db->prepare("DELETE from tbl_participants_details WHERE cid = '" . $id . "'");
			$stmt->execute();
			$stmt = $this->db->prepare("DELETE from tbl_donors WHERE cid = '" . $id . "'");
			$stmt->execute();
			$stmt = $this->db->prepare("DELETE from tbl_donors_details WHERE cid = '" . $id . "'");
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function update_campaign_status($sStatus, $iId) {
		try {
			$stmt = $this->db->prepare("UPDATE  tbl_campaign SET fld_status=:sStatus WHERE fld_campaign_id=:iId");
			$stmt->bindparam(":sStatus", $sStatus);
			$stmt->bindparam(":iId", $iId);
			$stmt->execute();
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_payment_center($cid, $cuid, $ctitle, $rid, $rname, $did, $dname, $aid, $aname, $pc_moneyraised, $pc_ufundamt, $pc_cprofitraised, $pc_cfirstpayment, $pc_cfirstpaiddate, $pc_cfirstchecknumber, $pc_csecondpayment, $pc_csecondpaiddate, $pc_csecondchecknumber, $pc_dflname, $pc_dpayment, $pc_dpaiddate, $pc_dchecknumber, $pc_rflname, $pc_rpayment, $pc_rpaiddate, $pc_rchecknumber, $pc_dpercentage, $pc_rpercentage) {
		try {
			$stmt2 = $this->db->prepare("SELECT * FROM tbl_transaction WHERE cid = '$cid'");
			$stmt2->execute();
			$userRow = $stmt2->fetchall(PDO::FETCH_ASSOC);
			if ($stmt2->rowCount() == 0) {
				$stmt = $this->db->prepare("INSERT INTO tbl_transaction (cid, ctitle, rid, did, aid, cname, rname, dname, aname, firstrequesteddate, firstrequestedamount, secondrequesteddate, secondrequestedamount, cprofitraised, cfirstpayment, cfirstpaiddate, cfirstcheckno, csecondpayment, csecondpaiddate, csecondcheckno, rflname, rpayment, rpaiddate, rcheckno, dflname, dpayment, dpaiddate, dcheckno, moneyraised, ufundamt, desper, repper, creationdate) VALUES (:cid, :ctitle, :rid, :did, :aid, '', :rname, :dname, :aname, '', '', '', '', :cprofitraised, :cfirstpayment, :cfirstpaiddate, :cfirstcheckno, :csecondpayment, :csecondpaiddate, :csecondcheckno, :rflname, :rpayment, :rpaiddate, :rcheckno, :dflname, :dpayment, :dpaiddate, :dcheckno, :moneyraised, :ufundamt, :desper, :repper, NOW())");
				$stmt->bindparam(":cid", $cid);
				$stmt->bindparam(":ctitle", $ctitle);
				$stmt->bindparam(":rid", $rid);
				$stmt->bindparam(":did", $did);
				$stmt->bindparam(":aid", $aid);
				//$stmt->bindparam(":cname", "");
				$stmt->bindparam(":rname", $rname);
				$stmt->bindparam(":dname", $dname);
				$stmt->bindparam(":aname", $aname);
				//$stmt->bindparam(":firstrequesteddate", "");
				//$stmt->bindparam(":firstrequestedamount", "");
				//$stmt->bindparam(":secondrequesteddate", "");
				//$stmt->bindparam(":secondrequestedamount", "");
				$stmt->bindparam(":cprofitraised", $pc_cprofitraised);
				$stmt->bindparam(":cfirstpayment", $pc_cfirstpayment);
				$stmt->bindparam(":cfirstpaiddate", $pc_cfirstpaiddate);
				$stmt->bindparam(":cfirstcheckno", $pc_cfirstchecknumber);
				$stmt->bindparam(":csecondpayment", $pc_csecondpayment);
				$stmt->bindparam(":csecondpaiddate", $pc_csecondpaiddate);
				$stmt->bindparam(":csecondcheckno", $pc_csecondchecknumber);
				$stmt->bindparam(":rflname", $rname);
				$stmt->bindparam(":rpayment", $pc_rpayment);
				$stmt->bindparam(":rpaiddate", $pc_rpaiddate);
				$stmt->bindparam(":rcheckno", $pc_rchecknumber);
				$stmt->bindparam(":dflname", $dname);
				$stmt->bindparam(":dpayment", $pc_dpayment);
				$stmt->bindparam(":dpaiddate", $pc_dpaiddate);
				$stmt->bindparam(":dcheckno", $pc_dchecknumber);
				$stmt->bindparam(":moneyraised", $pc_moneyraised);
				$stmt->bindparam(":ufundamt", $pc_ufundamt);
				$stmt->bindparam(":desper", $pc_dpercentage);
				$stmt->bindparam(":repper", $pc_rpercentage);
				$executed = $stmt->execute();
			} else {
				$stmt = $this->db->prepare("UPDATE tbl_transaction SET
				cprofitraised = '$pc_cprofitraised',
				cfirstpayment = '$pc_cfirstpayment',
				cfirstpaiddate = '$pc_cfirstpaiddate',
				cfirstcheckno = '$pc_cfirstchecknumber',
				csecondpayment = '$pc_csecondpayment',
				csecondpaiddate = '$pc_csecondpaiddate',
				csecondcheckno = '$pc_csecondchecknumber',
				rpayment = '$pc_rpayment',
				rpaiddate = '$pc_rpaiddate',
				rcheckno = '$pc_rchecknumber',
				dpayment = '$pc_dpayment',
				dpaiddate = '$pc_dpaiddate',
				dcheckno = '$pc_dchecknumber',
				moneyraised = '$pc_moneyraised',
				ufundamt = '$pc_ufundamt',
				desper = '$pc_dpercentage',
				repper = '$pc_rpercentage'
				WHERE cid = '$cid'");
				$executed = $stmt->execute();
			}
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function insert_fast_pay($rid, $rname, $did, $dname, $aid, $aname, $cid, $cuid, $ctitle, $roleid, $emailfrom, $emailfname, $emaillname, $amountreq, $emailto, $checkpayableto) {
		try {
			$stmt2 = $this->db->prepare("SELECT * FROM tbl_transaction WHERE cid = '$cid'");
			$stmt2->execute();
			$userRow = $stmt2->fetchall(PDO::FETCH_ASSOC);
			if ($stmt2->rowCount() == 0) {
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
					$is_Company_Title = 0;
				} else {
					$ref_name = '';
					$reffname = '';
					$reflname = '';
					$refphone = '';
					$refemail = '';
					$refimage = '';
					$is_refimage = 0;
					$Company_Title = 'UFund4Us';
					$is_Company_Title = false;
				}
				$stmt = $this->db->prepare("INSERT INTO tbl_transaction (cid, cemail, checkpayableto, ctitle, rid, did, aid, cname, rname, dname, aname, firstrequesteddate, firstrequestedamount) VALUES (:cid, :cemail, :checkpayableto, :ctitle, :rid, :did, :aid, :cname, :rname, :dname, :aname, NOW(), :firstrequestedamount)");
				$stmt->bindparam(":cid", $cid);
				$stmt->bindparam(":cemail", $emailto);
				$stmt->bindparam(":checkpayableto", $checkpayableto);
				$stmt->bindparam(":ctitle", $ctitle);
				$stmt->bindparam(":rid", $rid);
				$stmt->bindparam(":did", $did);
				$stmt->bindparam(":aid", $aid);
				$stmt->bindparam(":cname", $cname);
				$stmt->bindparam(":rname", $rname);
				$stmt->bindparam(":dname", $dname);
				$stmt->bindparam(":aname", $aname);
				$stmt->bindparam(":firstrequestedamount", $amountreq);
				$executed = $stmt->execute();
			} else {
				$stmt = $this->db->prepare("UPDATE tbl_transaction SET secondrequesteddate = NOW(), secondrequestedamount = '$amountreq' WHERE cid = '$cid'");
				$executed = $stmt->execute();
			}
			if ($executed) {
				//SparkPost Insert Fast Pay Template
				try {
					//SparkPost Init
					$httpClient = new GuzzleAdapter(new Client());
					$sparky = new SparkPost($httpClient, ['key' => '3dec808848252dc06072dfdf85b74c8cd04cafbb']);
					//SparkPost Init
					$sparky->setOptions(['async' => false]);
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'fast-pay-template'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"emailfname" => $emailfname,
							"emaillname" => $emaillname,
							"emailfrom" => $emailfrom,
							"emailto" => $emailto,
							"ctitle" => $ctitle,
							"amountreq" => number_format($amountreq, 2, '.', ','),
							"checkpayableto" => $checkpayableto,
							"fromemail" => "info@ufund4us.com",
							"refimage" => $refimage,
							"is_refimage" => $is_refimage,
							"Company_Title" => $Company_Title,
							"is_Company_Title" => $is_Company_Title,
						],
						'description' => "FastPay ($ctitle)",
						'metadata' => [
							'Campaign_ID' => "$cid",
							'Campaign_Name' => "$ctitle",
							'Subject' => "FastPay ($ctitle) Request by $emailfname $emaillname",
						],
						'recipients' => [
							[
								'address' => [
									'name' => 'FastPay Administrator',
									'email' => 'fastpay@ufund4us.com',
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
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_participant_campaign($cid, $uid) {
		try {
			$stmt09 = $this->db->prepare("SELECT count(*) AS counter FROM tbl_participants_details WHERE cid='$cid' AND uid='$uid'");
			$stmt09->execute();
			$row = $stmt09->fetch(PDO::FETCH_ASSOC);
			$counter = $row['counter'];
			return $counter;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function chk_validate_link($cid, $uid, $link) {
		try {
			$stmt09 = $this->db->prepare("SELECT count(*) AS counter FROM tbl_donors_invitation WHERE cid='$cid' AND pid='$uid' AND linkgenerate='$link' AND expiredatetime >= NOW()");
			$stmt09->execute();
			$row = $stmt09->fetch(PDO::FETCH_ASSOC);
			$counter = $row['counter'];
			return $counter;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function donationsmsupdate($id, $sms_id, $sms_sent_date) {
		try {
			$stmt = $this->db->prepare("UPDATE tbl_donations SET sms_sent_id = :sms_id, sms_sent_date = :sms_sent_date WHERE tid =:id");
			$stmt->bindparam(":id", $id);
			$stmt->bindparam(":sms_id", $sms_id);
			$stmt->bindparam(":sms_sent_date", $sms_sent_date);
			$executed = $stmt->execute();
			return $executed;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function donationsmsinsert($cid, $hashid, $pid, $donorname, $donorlname, $donoremail, $DonorPhone, $participantname, $sms_sent_id, $sms_date_created) {
		try {
			$stmt2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid, did, pid, dfname, dlname, demail, dphone, creationdate, participantname, likeas, sms_sent_id, sms_sent_date, status) VALUES 
			('$cid', '$hashid', '$pid', '$donorname', '$donorlname', '$donoremail', '$DonorPhone', NOW(), '$participantname', '7', '$sms_sent_id', '$sms_date_created', '1')");
			$executed = $stmt2->execute();
			return $executed;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function send_sms($number, $body) {
		$ID = 'AC88602ed0bd5934afed50a39b476c16b8';
		$token = 'a1ecd75336521e5bc15bb6f4c507f2e3';
		$twilio_number = "+19097669348";
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
	public function sms_resend_donors($cid, $pid, $did, $donor_fname, $donor_lname, $donor_phone, $donor_email, $participantid, $participantname, $sms_sent_id, $sms_date_created, $sms_details) {
		try {
			//Update the donor sms sent record
			$stmt = $this->db->prepare("UPDATE tbl_donors_details SET sms_sent_id = '$sms_sent_id', sms_sent_date = '$sms_date_created' WHERE cid = '$cid' AND uid = '$did' AND puid = '$pid'");
			$executed = $stmt->execute();
			//Track the record of SMS details
			$stmt2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','$did','$pid','$donor_fname','$donor_lname','$donor_email','$donor_phone','$sms_date_created','$participantname','3','$sms_sent_id','$sms_date_created','1','$sms_details')");
			$executed2 = $stmt2->execute();
			return $executed;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getparticipantdetails($cid, $pid) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE uid='$pid' LIMIT 1");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getuserdetails($uid) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_users WHERE fld_uid='$uid' AND fld_role_id = 5 LIMIT 1");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getselectedparticipantdetails($cid,$cuid,$pid) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid='$cid' AND cuid='$cuid' AND uid='$pid' ");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getselectedparticipantdetails2($cid,$cuid,$pid) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid='$cid' AND cuid='$cuid' AND id='$pid' ");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function getdonordetails($cid, $pid, $did) {
		try {
			$stmt = $this->db->prepare("SELECT * FROM tbl_donors_details WHERE cid='$cid' AND puid='$pid' AND uid='$did' LIMIT 1");
			$stmt->execute();
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				return $userRow;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function sms_resend_participants($cid, $pid, $participant_fname, $participant_lname, $participant_email, $participant_phone, $campaign_manager_name, $sms_sent_id, $sms_date_created, $sms_details) {
		try {
			//Update the donor sms sent record
			$stmt = $this->db->prepare("UPDATE tbl_participants_details SET sms_sent_id = '$sms_sent_id', sms_sent_date = '$sms_date_created' WHERE cid = '$cid' AND uid = '$pid'");
			$executed = $stmt->execute();
			//Track the record of SMS details
			$stmt2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','$did','$pid','$participant_fname','$participant_lname','$participant_email','$participant_phone','$sms_date_created','$campaign_manager_name','6','$sms_sent_id','$sms_date_created','1','$sms_details')");
			$executed2 = $stmt2->execute();
			return $executed;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function sms_resend_participants2($cid, $pid, $participant_fname, $participant_lname, $participant_email, $participant_phone, $campaign_manager_name, $sms_sent_id, $sms_date_created, $sms_details) {
		try {
			//Update the donor sms sent record
			$stmt = $this->db->prepare("UPDATE tbl_participants_details SET sms_sent_id = '$sms_sent_id', sms_sent_date = '$sms_date_created' WHERE cid = '$cid' AND id = '$pid'");
			$executed = $stmt->execute();
			
			$stmt3 = $this->db->prepare("SELECT * FROM tbl_participants_details WHERE cid = '$cid' AND id = '$pid'");
			$stmt3->execute();
			
			$ParticipantRow = $stmt3->fetch(PDO::FETCH_ASSOC);
			if ($stmt3->rowCount() > 0) {
				$pid = $ParticipantRow['uid'];
				//Track the record of SMS details
				$stmt2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','$did','$pid','$participant_fname','$participant_lname','$participant_email','$participant_phone','$sms_date_created','$campaign_manager_name','6','$sms_sent_id','$sms_date_created','1','$sms_details')");
				$executed2 = $stmt2->execute();
			} else {
				//Track the record of SMS details
				$stmt2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','$did','0','$participant_fname','$participant_lname','$participant_email','$participant_phone','$sms_date_created','$campaign_manager_name','6','$sms_sent_id','$sms_date_created','1','$sms_details')");
				$executed2 = $stmt2->execute();
			}
			return $executed;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	
	public function update_participant_details($cid,$cuid,$pid,$uphone) {
		try {
			$stmt3 = $this->db->prepare("SELECT p.*, c.fld_text_messaging, c.fld_campaign_title, c.fld_campaign_hashkey, c.fld_cname, c.fld_clname FROM tbl_participants_details p INNER JOIN tbl_campaign c ON p.cid = c.fld_campaign_id WHERE p.cid = '$cid' AND p.pid = '$pid'");
			$stmt3->execute();
			$ParticipantRow = $stmt3->fetch(PDO::FETCH_ASSOC);
			if ($stmt3->rowCount() > 0) {
				$uid = $ParticipantRow['uid'];
				$fld_text_messaging = $ParticipantRow['fld_text_messaging'];
				$ctitle = $ParticipantRow['fld_campaign_title'];
				$cname = $ParticipantRow['fld_cname']." ".$ParticipantRow['fld_clname'];
				$campaign_hashkey = $ParticipantRow['fld_campaign_hashkey'];
				$uname = $ParticipantRow['uname'];
				$ulname = $ParticipantRow['ulname'];
				$uemail = $ParticipantRow['uemail'];
				
				$stmt = $this->db->prepare("UPDATE tbl_participants_details SET uphone = '$uphone' WHERE cid='$cid' AND pid='$pid' AND cuid='$cuid'");
				$executed = $stmt->execute();
				$stmt = $this->db->prepare("UPDATE tbl_participants SET uphone = '$uphone' WHERE cid='$cid' AND uid='$uid' AND cuid='$cuid'");
				$executed = $stmt->execute();
				$stmt = $this->db->prepare("UPDATE tbl_users SET fld_phone = '$uphone' WHERE fld_uid='$uid' AND fld_role_id = '5'");
				$executed = $stmt->execute();
			
				if ($fld_text_messaging == 1) {
					$generate_short_link = ''.str_replace('app/','',sHOME).'l.php?v='.$campaign_hashkey.'&u='.$pid.'&m=1';
					$ParticipantFullName = trim($uname." ".$ulname);
					$body = "Hi! It’s $ParticipantFullName.\n";
					$body = "You are being invited to join $ctitle by $cname. Please click on the link below to join this campaign.\n";
					$body .= "".$generate_short_link."\n";
					$body .= "Thank You!";
					if ($uphone != "" && $uphone != "000-000-0000" && $uphone != "___-___-____") {
						if ($sent_sms = send_sms(str_replace("-","",$uphone),utf8_encode($body))) {
							$sms_status = 1;
							$sms_sent_id = $sent_sms['sid'];
							if ($sms_sent_id != '') {
								$sms_date_created = $sent_sms['date_created'];
								$sms_message = $sent_sms['message'];
								$sms_details = $sms_message;
								
								$tz = new DateTimeZone('America/Los_Angeles');
								$sms_date = new DateTime($sms_date_created);
								$sms_date->setTimezone($tz);
								$sms_date_created = $sms_date->format('Y-m-d h:i:s');
								
								//Update the donor sms sent record
								$stmt_sms = $this->db->prepare("UPDATE tbl_participants_details SET sms_sent_id = '$sms_sent_id', sms_sent_date = '$sms_date_created' WHERE cid = '$cid' AND pid = '$pid'");
								$executed = $stmt_sms->execute();
								
								//Track the record of SMS details
								$stmt_sms2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','0','$pid','$uname','$ulname','$uemail','$uphone','$sms_date_created','$cname','4','$sms_sent_id','$sms_date_created','1','')");
								$executed2 = $stmt_sms2->execute();
							}
						} else {
								//Error when sending
							}
					} else {
						//Invalid Number
					}
					/*if () {
						//Track the record of SMS details
						$stmt2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','$did','$pid','$participant_fname','$participant_lname','$participant_email','$participant_phone','$sms_date_created','$campaign_manager_name','6','$sms_sent_id','$sms_date_created','1','$sms_details')");
						$executed2 = $stmt2->execute();
					} else {
						//Track the record of SMS details
						$stmt2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','$did','0','$participant_fname','$participant_lname','$participant_email','$participant_phone','$sms_date_created','$campaign_manager_name','6','$sms_sent_id','$sms_date_created','1','$sms_details')");
						$executed2 = $stmt2->execute();
					}*/
				}
			}
			return $ParticipantRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	
	public function update_donors_details($cid,$pid,$did,$uphone) {
		try {
			$stmt3 = $this->db->prepare("SELECT d.*, p.uname AS pname, p.ulname AS plname, c.fld_text_messaging, c.fld_campaign_title, c.fld_campaign_hashkey, c.fld_cname, c.fld_clname FROM tbl_donors_details d INNER JOIN tbl_campaign c ON d.cid = c.fld_campaign_id INNER JOIN tbl_participants_details p ON p.cid = '$cid' AND p.uid = '$pid' WHERE d.cid = '$cid' AND d.puid = '$pid' AND d.uid = '$did'");
			$stmt3->execute();
			$DonorRow = $stmt3->fetch(PDO::FETCH_ASSOC);
			if ($stmt3->rowCount() > 0) {
				$did = $DonorRow['uid'];
				$participantid = $DonorRow['puid'];
				$participantname = $DonorRow['pname']." ".$DonorRow['plname'];
				$fld_text_messaging = $DonorRow['fld_text_messaging'];
				$ctitle = $DonorRow['fld_campaign_title'];
				$cname = $DonorRow['fld_cname']." ".$DonorRow['fld_clname'];
				$campaign_hashkey = $DonorRow['fld_campaign_hashkey'];
				$uname = $DonorRow['uname'];
				$ulname = $DonorRow['ulname'];
				$uemail = $DonorRow['uemail'];
				
				$stmt = $this->db->prepare("UPDATE tbl_donors_details SET uphone = '$uphone' WHERE cid='$cid' AND puid='$pid' AND uid='$did'");
				$executed = $stmt->execute();
				$stmt = $this->db->prepare("UPDATE tbl_donors SET uphone = '$uphone' WHERE cid='$cid' AND puid='$pid' AND uid='$did'");
				$executed = $stmt->execute();
				$stmt = $this->db->prepare("UPDATE tbl_users SET fld_phone = '$uphone' WHERE fld_uid='$did' AND fld_role_id = '4'");
				$executed = $stmt->execute();
			
				if ($fld_text_messaging == 1) {
					$generate_short_link = ''.str_replace('app/','',sHOME).'l.php?v='.$campaign_hashkey.'&u='.$participantid.'&d='.$did.'';
					$ParticipantFullName = trim($participantname);
					$body = "Hi! It’s $ParticipantFullName. Please take a second to view a fundraiser that I am participating in by clicking the link below.\n";
					$body .= "".$generate_short_link."\n";
					$body .= "Thank You!";
					if ($uphone != "" && $uphone != "000-000-0000" && $uphone != "___-___-____") {
						if ($sent_sms = send_sms(str_replace("-","",$uphone),utf8_encode($body))) {
							$sms_status = 1;
							$sms_sent_id = $sent_sms['sid'];
							if ($sms_sent_id != '') {
								$sms_date_created = $sent_sms['date_created'];
								$sms_message = $sent_sms['message'];
								$sms_details = $sms_message;
								
								$tz = new DateTimeZone('America/Los_Angeles');
								$sms_date = new DateTime($sms_date_created);
								$sms_date->setTimezone($tz);
								$sms_date_created = $sms_date->format('Y-m-d h:i:s');
								
								//Update the donor sms sent record
								$stmt_sms = $this->db->prepare("UPDATE tbl_donors_details SET sms_sent_id = '$sms_sent_id', sms_sent_date = '$sms_date_created' WHERE cid = '$cid' AND puid = '$pid' AND uid = '$did'");
								$executed = $stmt_sms->execute();
								
								//Track the record of SMS details
								$stmt_sms2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','$did','$pid','$uname','$ulname','$uemail','$uphone','$sms_date_created','$cname','3','$sms_sent_id','$sms_date_created','1','')");
								$executed2 = $stmt_sms2->execute();
							}
						} else {
								//Error when sending
							}
					} else {
						//Invalid Number
					}
					/*if () {
						//Track the record of SMS details
						$stmt2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','$did','$pid','$participant_fname','$participant_lname','$participant_email','$participant_phone','$sms_date_created','$campaign_manager_name','6','$sms_sent_id','$sms_date_created','1','$sms_details')");
						$executed2 = $stmt2->execute();
					} else {
						//Track the record of SMS details
						$stmt2 = $this->db->prepare("INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$cid','$did','0','$participant_fname','$participant_lname','$participant_email','$participant_phone','$sms_date_created','$campaign_manager_name','6','$sms_sent_id','$sms_date_created','1','$sms_details')");
						$executed2 = $stmt2->execute();
					}*/
				}
			}
			return $DonorRow;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	
}
?>