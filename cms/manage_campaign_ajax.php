<?php
	session_start();
	//error_reporting(0);
	ini_set('max_execution_time', 600); //600 seconds = 10 minutes
	date_default_timezone_set('America/Los_Angeles'); //TimeZone
	include_once ('php/dbconn.php');
	$rid = $_GET['rid'];
	$uid = $_GET['uid'];
	$mode = $_GET['mode'];
	if ($mode == 'extended') {
		//Get SparkPost Bounces Email
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.sparkpost.com/api/v1/suppression-list?cursor=initial&limit=10000&per_page=10000&page=10000");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		$headers = array();
		$headers[] = "Content-Type: application/json";
		$headers[] = "Authorization: ".SPARK_POST_KEY;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result_bounce = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close ($ch);
		$array_bounce = json_decode($result_bounce, true);
	}
	//print_r($array_bounce );
	//End Get SparkPost Bounces Email
	
	$aColumns0 = array(
			'a.fld_campaign_id',
			'a.app_fee_percentage',
			'a.fld_cname',
			'a.fld_clname',
			'a.fld_cemail',
			'a.fld_campaign_title',
			'DATE_FORMAT(a.fld_campaign_sdate, "%m/%d/%Y")',
			'DATE_FORMAT(a.fld_campaign_edate, "%m/%d/%Y")',
			'a.fld_campaign_goal',
			'a.fld_participant_goal',
			'a.fld_team_size',
			'a.fld_donor_size',
			'a.fld_organization_name',
			'a.fld_pin',
			'a.fld_hashcamp',
			'l.repper',
			'l.desper',
			'l.adminper',
			'l.cname',
			'l.rname',
			'l.dname',
			'l.aname',
			'DATE_FORMAT(l.firstrequesteddate, "%m/%d/%Y")',
			'l.firstrequestedamount',
			'DATE_FORMAT(l.secondrequesteddate, "%m/%d/%Y")',
			'l.cprofitraised',
			'l.cfirstpayment',
			'DATE_FORMAT(l.cfirstpaiddate, "%m/%d/%Y")',
			'l.cfirstcheckno',
			'l.csecondpayment',
			'DATE_FORMAT(l.csecondpaiddate, "%m/%d/%Y")',
			'l.csecondcheckno',
			'l.rflname',
			'l.rpayment',
			'DATE_FORMAT(l.rpaiddate, "%m/%d/%Y")',
			'l.rcheckno',
			'l.dflname',
			'l.dpayment',
			'DATE_FORMAT(l.dpaiddate, "%m/%d/%Y")',
			'l.dcheckno',
			'l.moneyraised',
			'l.ufundamt',
			'DATE_FORMAT(cfirstpaiddate, "%m/%d/%Y")', 
			'DATE_FORMAT(csecondpaiddate, "%m/%d/%Y")', 
			'DATE_FORMAT(rpaiddate, "%m/%d/%Y")', 
			'DATE_FORMAT(dpaiddate, "%m/%d/%Y")', 
			'DATEDIFF(a.fld_campaign_edate, CURDATE())', 
			'i.rid', 
			'i.rname', 
			'i.rlname', 
			'i.did',
			'i.dname',
			'i.dlname',
			'i.aid',
			'i.aname',
			'i.alname',
			);
		$aColumns = array(
			'fld_ab1575_pupil_fee',
			'fld_show_participant_goal',
			'a.fld_campaign_id',
			'a.app_fee_percentage',
			'a.fld_uid',
			'a.fld_cname',
			'a.fld_clname',
			'a.fld_campaign_logo',
			'a.fld_cemail',
			'a.fld_campaign_title',
			'a.fld_campaign_sdate',
			'a.fld_campaign_edate',
			'a.fld_campaign_goal',
			'a.fld_participant_goal',
			'a.fld_team_size',
			'a.fld_donor_size',
			'a.fld_organization_name',
			'a.fld_pin','a.fld_status',
			'a.fld_active',
			'a.fld_live',
			'a.fld_hashcamp',
			'a.fld_pin',
			'a.fld_ac',
			'a.fld_bank_accno',
			'a.fld_payable_to',
			'l.cid',
			'l.cemail',
			'l.checkpayableto',
			'l.ctitle',
			'l.repper',
			'l.desper',
			'l.adminper',
			'l.rid',
			'l.did',
			'l.aid',
			'l.cname',
			'l.rname',
			'l.dname',
			'l.aname',
			'l.firstrequesteddate',
			'l.firstrequestedamount',
			'l.secondrequesteddate',
			'l.cprofitraised',
			'l.cfirstpayment',
			'l.cfirstpaiddate',
			'l.cfirstcheckno',
			'l.csecondpayment',
			'l.csecondpaiddate',
			'l.csecondcheckno',
			'l.rflname',
			'l.rpayment',
			'l.rpaiddate',
			'l.rcheckno',
			'l.dflname',
			'l.dpayment',
			'l.dpaiddate',
			'l.dcheckno',
			'l.moneyraised',
			'l.ufundamt',
			'DATE_FORMAT(cfirstpaiddate, "%m/%d/%Y") AS cfirstpaiddate', 
			'DATE_FORMAT(csecondpaiddate, "%m/%d/%Y") AS csecondpaiddate', 
			'DATE_FORMAT(rpaiddate, "%m/%d/%Y") AS rpaiddate', 
			'DATE_FORMAT(dpaiddate, "%m/%d/%Y") AS dpaiddate', 
			'DATEDIFF(a.fld_campaign_edate, CURDATE()) AS daysleft', 
			'i.rid AS rid', 
			'i.rname AS rfname', 
			'i.rlname AS rlname', 
			'i.did',
			'i.dname AS dfname',
			'i.dlname',
			'i.aid',
			'i.aname AS afname',
			'i.alname',
			'(SELECT COUNT(z.uphone) FROM tbl_donors_details z WHERE z.uphone REGEXP "[0-9]{3}-[0-9]{3}-[0-9]{4}" AND z.cid = a.fld_campaign_id) AS phonenumberuploaded',
			'(SELECT COUNT(b.cid) FROM tbl_participants_details b WHERE b.cid = a.fld_campaign_id) AS participantenrolled',
			'(SELECT COUNT(c.cid) FROM tbl_donors_details c WHERE c.cid = a.fld_campaign_id) AS donorenrolled',
			'(SELECT COUNT(d.cid) FROM tbl_donations d WHERE d.cid = a.fld_campaign_id AND d.mode = "1") AS donations',
			'u.fld_cname',
			'u.fld_name AS contactfname',
			'u.fld_lname AS contactlname',
			'a.fld_dist_per AS commlevel',
			'(SELECT ud.fld_cname FROM tbl_tree tr INNER JOIN tbl_users ud ON ud.fld_uid = tr.did WHERE tr.uid = a.fld_uid) AS comp_name',
			'(SELECT IFNULL(SUM(e.donation_amount), 0.00) FROM tbl_donations e WHERE e.cid = a.fld_campaign_id AND e.mode = "1") AS moneyraised',
			'(SELECT IFNULL(COUNT(f.cid),0) FROM tbl_donors_details f WHERE f.cid = a.fld_campaign_id AND f.is_unsubscribe = "1") AS donors_unsubscribe',
			'fld_admin_per',
			'fld_dist_per',
			'fld_rep_per'
			);
		$sIndexColumn = "a.fld_campaign_id";
		$sTable = "tbl_campaign";
	if ($rid == 1) {
		$aColumns1 = array(
			'a.fld_status', 
			'a.fld_campaign_id',
			'a.app_fee_percentage', 
			'a.fld_pin', 
			'a.fld_campaign_title', 
			'a.fld_campaign_sdate', 
			'a.fld_campaign_edate', 
			'daysleft', 
			'a.fld_team_size', 
			'participantenrolled', 
			'a.fld_donor_size', 
			'donorenrolled', 
			'(donorenrolled/(participantenrolled*fld_donor_size)*100)',
			'', 
			'donors_unsubscribe', 
			'donations', 
			'moneyraised/donations', 
			'moneyraised*a.app_fee_percentage', 
			'a.fld_campaign_goal', 
			'(moneyraised*a.app_fee_percentage / a.fld_campaign_goal)*100', 
			'moneyraised', 
			'', 
			'l.cfirstpayment + l.csecondpayment', 
			'(moneyraised*a.app_fee_percentage) - (l.cfirstpayment + l.csecondpayment)', 
			'', 
			'i.dname AS dfname', 
			'', 
			'i.rname AS rfname', 
			'', 
			'', 
			'', 
			'', 
			'');
			$whereclause = "WHERE a.fld_active = '1'";
	} elseif ($rid == 3) {
		$aColumns1 = array(
			'a.fld_status', 
			'a.fld_campaign_id', 
			'a.app_fee_percentage',
			'a.fld_pin', 
			'a.fld_campaign_title', 
			'a.fld_campaign_sdate', 
			'a.fld_campaign_edate', 
			'daysleft', 
			'a.fld_team_size', 
			'participantenrolled', 
			'a.fld_donor_size', 
			'donorenrolled', 
			'(donorenrolled/(participantenrolled*fld_donor_size)*100)',
			'', 
			'donors_unsubscribe', 
			'donations', 
			'moneyraised/donations', 
			'moneyraised*a.app_fee_percentage', 
			'a.fld_campaign_goal', 
			'(moneyraised*a.app_fee_percentage / a.fld_campaign_goal)*100', 
			'moneyraised', 
			'', 
			'l.cfirstpayment + l.csecondpayment', 
			'(moneyraised*a.app_fee_percentage) - (l.cfirstpayment + l.csecondpayment)', 
			'', 
			'i.dname AS dfname', 
			'', 
			'i.rname AS rfname', 
			'', 
			'', 
			'', 
			'');
			$whereclause = "WHERE a.fld_active = '1' AND i.did = '$uid' OR i.uid = '$uid'";
	} elseif ($rid == 6) {
		$aColumns1 = array(
			'a.fld_status', 
			'a.fld_campaign_id', 
			'a.app_fee_percentage',
			'a.fld_pin', 
			'a.fld_campaign_title', 
			'a.fld_campaign_sdate', 
			'a.fld_campaign_edate', 
			'daysleft', 
			'a.fld_team_size', 
			'participantenrolled', 
			'a.fld_donor_size', 
			'donorenrolled', 
			'(donorenrolled/(participantenrolled*fld_donor_size)*100)',
			'', 
			'donors_unsubscribe', 
			'donations', 
			'moneyraised/donations', 
			'moneyraised*a.app_fee_percentage', 
			'a.fld_campaign_goal', 
			'(moneyraised*a.app_fee_percentage / a.fld_campaign_goal)*100', 
			'moneyraised', 
			'', 
			'l.cfirstpayment + l.csecondpayment', 
			'(moneyraised*a.app_fee_percentage) - (l.cfirstpayment + l.csecondpayment)', 
			'', 
			'i.rname AS rfname', 
			'', 
			'', 
			'');
			$whereclause = "WHERE a.fld_active = '1' AND i.rid = '$uid' OR i.uid = '$uid'";
	} elseif ($rid == 2) {
		$aColumns1 = array(
			'a.fld_status', 
			'a.fld_campaign_id',
			'a.app_fee_percentage', 
			'a.fld_pin', 
			'a.fld_campaign_title', 
			'a.fld_campaign_sdate', 
			'a.fld_campaign_edate', 
			'daysleft', 
			'a.fld_team_size', 
			'participantenrolled', 
			'a.fld_donor_size', 
			'donorenrolled', 
			'(donorenrolled/(participantenrolled*fld_donor_size)*100)',
			'', 
			'donors_unsubscribe', 
			'donations', 
			'moneyraised/donations', 
			'moneyraised*a.app_fee_percentage', 
			'a.fld_campaign_goal', 
			'(moneyraised*a.app_fee_percentage / a.fld_campaign_goal)*100', 
			'moneyraised', 
			'', 
			'l.cfirstpayment + l.csecondpayment', 
			'(moneyraised*a.app_fee_percentage) - (l.cfirstpayment + l.csecondpayment)', 
			'', 
			'');
			$whereclause = "WHERE a.fld_active = '1' AND i.uid = '$uid'";
	}

	$gaSql['link'] =  $con or
		die( 'Could not open connection to server' );
	
	// mysqli_select_db($con,$DB_name) or die( 'Could not select database '. $DB_name );
	
	
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysqli_real_escape_string($con,  $_GET['iDisplayStart'] ).", ".
			mysqli_real_escape_string($con,  $_GET['iDisplayLength'] );
	}
	
	
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns1[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysqli_real_escape_string($con,  $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	$sWhere = $whereclause;
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = $whereclause." AND (";
		for ( $i=0 ; $i<count($aColumns0) ; $i++ )
		{
			$sWhere .= $aColumns0[$i]." LIKE '%".mysqli_real_escape_string($con,  $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns0) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns0[$i]." LIKE '%".mysqli_real_escape_string($con, $_GET['sSearch_'.$i])."%' ";
		}
	}
	
	
	$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM $sTable a
			LEFT JOIN tbl_tree i ON i.uid = a.fld_uid
			LEFT JOIN tbl_transaction l ON l.cid = a.fld_campaign_id
			INNER JOIN tbl_users u ON u.fld_uid = a.fld_uid
			$sWhere
			GROUP BY a.fld_campaign_id
			$sOrder
			$sLimit
		";
	$rResult = mysqli_query( $gaSql['link'], $sQuery ) or die(mysqli_error($con));
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysqli_query( $gaSql['link'], $sQuery ) or die(mysqli_error($con));
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal, MYSQLI_NUM);;
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM $sTable a
		LEFT JOIN tbl_tree i ON i.uid = a.fld_uid
		LEFT JOIN tbl_transaction l ON l.cid = a.fld_campaign_id
		INNER JOIN tbl_users u ON u.fld_uid = a.fld_uid
		$whereclause
		";
	$rResultTotal = mysqli_query( $gaSql['link'], $sQuery ) or die(mysqli_error($con));
	$aResultTotal = mysqli_fetch_array($rResultTotal, MYSQLI_NUM);;
	$iTotal = $aResultTotal[0];
	
	
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);

	while ( $aRow = mysqli_fetch_array($rResult, MYSQLI_ASSOC) )
	{
		$row = array();
		// echo "<pre>"; print_r($aRow); die();
		$after_app_fee_percentage = get_after_app_fee_percentage($aRow);
		$after_app_fee_20p = 1 - $after_app_fee_percentage;//100-80=20, its like 0.2
		/* General output */
		$accid = issetCheck($aRow, 'fld_ac', '');
		$aRow['firstrequestedamount'] = issetCheck($aRow, 'firstrequestedamount', 0);
		$aRow['secondrequestedamount'] = issetCheck($aRow, 'secondrequestedamount', 0);

		$dist_com = 0;
		$bank_accountid = issetCheck($aRow, 'fld_bank_accno', '');
		$aRow['fld_campaign_id'] = issetCheck($aRow,'fld_campaign_id', '');
		$cid = trim($aRow['fld_campaign_id']);
		$getpercent = "SELECT * FROM tbl_transaction WHERE cid='".$cid."' ORDER BY id DESC LIMIT 1";
		$resultpercent = mysqli_query( $gaSql['link'], $getpercent ) or die(mysqli_error($con));
		$rowpercent = mysqli_fetch_array($resultpercent, MYSQLI_ASSOC);
		
		checkAndSetInArray($rowpercent, 'repper', '');
		checkAndSetInArray($rowpercent,'desper','');
		checkAndSetInArray($rowpercent,'adminper','');

		$repper = $rowpercent['repper'];
		$desper = $rowpercent['desper'];
		$adminper = $rowpercent['adminper'];
		if ($mode == 'extended') {
		$BadEmailCounter = 0;
		$getbademailsbycampaign = "SELECT uemail, is_read, is_unsubscribe, sent_email FROM tbl_donors_details WHERE cid='".$cid."'";
		$resultbademailsbycampaign = mysqli_query($gaSql['link'], $getbademailsbycampaign ) or die(mysqli_error($con));
		while ($rowbademailsbycampaign1 = mysqli_fetch_array($resultbademailsbycampaign, MYSQLI_ASSOC)) {
			$rowbademailsbycampaign[] = $rowbademailsbycampaign1;
		}
		$BadEmailDetailCount = mysqli_num_rows($resultbademailsbycampaign);
		if( isset($array_bounce['results']) ){
			foreach ($array_bounce['results'] as $bounce) {
				$bademail = $bounce['recipient'];
				for ($zz=0; $zz < $BadEmailDetailCount; $zz++) {
					if ($bademail == $rowbademailsbycampaign[$zz]['uemail'] && $rowbademailsbycampaign[$zz]['is_read'] == 0) {
						$BadEmailCounter++;
					}
				}
			}
		}

		unset($rowbademailsbycampaign);
		}
		
		if ($aRow['moneyraised'] > 0 && $aRow['donorenrolled'] > 0 && $aRow['fld_campaign_goal'] > 0) {
			$profit_raised = number_format((float)($aRow['moneyraised']/$aRow['donorenrolled']), 2, '.', '');
			$camppercentage = (($aRow['moneyraised']*$after_app_fee_percentage)/$aRow['fld_campaign_goal'])*100;
		} else {
			$profit_raised = 0;
			$camppercentage = 0;
		}
		$date1=date_create(date('Y-m-d',strtotime($aRow['fld_campaign_sdate'])));
		$date2=date_create(date('Y-m-d',strtotime($aRow['fld_campaign_edate'])));
		$diff=date_diff($date1,$date2);
		$date_diff = $diff->format("%a")/2;
		//echo $aRow['daysleft'];
		//$per_date = ($aRow['daysleft']/$date_diff)*100;
		if ($aRow['firstrequestedamount'] == 0 && $aRow['daysleft'] > 0 && $aRow['daysleft'] <= $date_diff) {
			$buttondisenbale = '';
			$actuallimit = number_format((float)(($aRow['moneyraised']*$after_app_fee_percentage) - ($aRow['firstrequestedamount'] + $aRow['secondrequestedamount'])), 2, '.', '');
			$amountlimit = $actuallimit*0.95;
		} elseif ($aRow['firstrequestedamount'] == 0 && $aRow['daysleft'] < 0) {
			$buttondisenbale = '';
			$actuallimit = number_format((float)(($aRow['moneyraised']*$after_app_fee_percentage) - ($aRow['firstrequestedamount'] + $aRow['secondrequestedamount'])), 2, '.', '');
			$amountlimit = $actuallimit;
		} elseif ($aRow['secondrequestedamount'] == 0 && $aRow['daysleft'] < 0) {
			$buttondisenbale = '';
			$actuallimit = number_format((float)(($aRow['moneyraised']*$after_app_fee_percentage) - ($aRow['firstrequestedamount'] + $aRow['secondrequestedamount'])), 2, '.', '');
			$amountlimit = $actuallimit;
		} else {
			$buttondisenbale = 'disabled';
			$amountlimit = 0.00;
		}
		
		// Status
		if($aRow['fld_status'] == 1){
			$row[] = '<td><i class="fa fa-fw fa-thumbs-o-up"></i></td>';
		} else {
			$row[] = '<td><i class="fa fa-fw fa-thumbs-o-down"></i></td>';
		}
		
        // Campaign #
            $row[] = '<td><a href="start_campaign.php?m=e&cid='.$aRow['fld_campaign_id'].'">'.str_pad($aRow['fld_campaign_id'], 7, "0", STR_PAD_LEFT).'</a></td>';
        // Campaign ID
            $row[] = '<td>'.$aRow['fld_pin'].'</td>';
		// Campaign Name
			$row[] = '<td><a href="start_campaign.php?m=e&cid='.$aRow['fld_campaign_id'].'">'.$aRow['fld_campaign_title'].'</a></td>';
		// Application fee
        // $row[] = '<td>'.$aRow['app_fee_percentage'].'%</td>';

        // Start Date
		if ($aRow['fld_campaign_sdate'] != '0000-00-00') {
			$row[] = '<td>'.date("m/d/Y",strtotime($aRow['fld_campaign_sdate'])).'</td>';
		} else {
			$row[] = '<td></td>';
		}
		// End Date
		if ($aRow['fld_campaign_edate'] != '0000-00-00') {
			$row[] = '<td>'.date("m/d/Y",strtotime($aRow['fld_campaign_edate'])).'</td>';
		} else {
			$row[] = '<td></td>';
		}
		// # Of Days Left
            $row[] = '<td>'.$aRow['daysleft'].'</td>';
        // # Of Participants
			$row[] = '<td>'.$aRow['fld_team_size'].'</td>';
		// # Of Participants Enrolled
			if ($aRow['fld_ab1575_pupil_fee'] == 1) {
				if ($rid == 1) {
					$ab1575pupil_applied1 = 'participantenrolled';
					$ab1575pupil_applied2 = '<a class="participantdonor" cid="'.$aRow['fld_campaign_id'].'" camptitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.$aRow['donorenrolled'].'</a>';
					$ab1575pupil_applied3 = '<a class="donationslist" cid="'.$aRow['fld_campaign_id'].'" camptitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.$aRow['donations'].'</a>';
					$ab1575pupil_applied4 = '<a class="unsubscribeddonors" cid="'.$aRow['fld_campaign_id'].'" camptitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.$aRow['donors_unsubscribe'].'</a>';
				} else {
					$ab1575pupil_applied1 = 'ab1575pupil';
					$ab1575pupil_applied2 = ''.$aRow['donorenrolled'].'';
					$ab1575pupil_applied3 = ''.$aRow['donations'].'';
					$ab1575pupil_applied4 = ''.$aRow['donors_unsubscribe'].'';
				}
			} else {
				$ab1575pupil_applied1 = 'participantenrolled';
				$ab1575pupil_applied2 = '<a class="participantdonor" cid="'.$aRow['fld_campaign_id'].'" camptitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.$aRow['donorenrolled'].'</a>';
				$ab1575pupil_applied3 = '<a class="donationslist" cid="'.$aRow['fld_campaign_id'].'" camptitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.$aRow['donations'].'</a>';
				$ab1575pupil_applied4 = '<a class="unsubscribeddonors" cid="'.$aRow['fld_campaign_id'].'" camptitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.$aRow['donors_unsubscribe'].'</a>';
			}
			$row[] = '<td><a class="'.$ab1575pupil_applied1.'" cid="'.$aRow['fld_campaign_id'].'" camptitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.$aRow['participantenrolled'].'</a></td>';
		// Total # of donors
			$row[] = '<td>'.number_format($aRow['participantenrolled']*$aRow['fld_donor_size'], 0, '.', ',').'</td>';
		// # Of Projected Donors Uploaded
			$row[] = '<td>'.$ab1575pupil_applied2.'</td>';
		// # Of Phone# Uploaded
			$row[] = '<td>'.$aRow['phonenumberuploaded'].'</td>';
		// % of Donors Uploaded
			if ($aRow['donorenrolled'] > 0) {
				$row[] = '<td>'.number_format((float)($aRow['donorenrolled']/($aRow['participantenrolled']*$aRow['fld_donor_size']))*100, 1, '.', '').' %</td>';
			} else {
				$row[] = '<td>0.0 %</td>';
			}
		// Bad Emails
			if ($mode == 'extended') {
			$row[] = '<td>'.$BadEmailCounter.'</td>';
			}
		// Unsubscribe Donors
			$row[] = '<td>'.$ab1575pupil_applied4.'</td>';
		// # Of Donation Received
			$row[] = '<td>'.$ab1575pupil_applied3.'</td>';
		// Avg. Donation Amount
			if ($aRow['moneyraised'] > 0) {
				$row[] = '<td>'.number_format((float)($aRow['moneyraised']/$aRow['donations']), 2, '.', '').'</td>';
			} else {
				$row[] = '<td>0.00</td>';
			}

		// Profit Raised
			$row[] = '<td>'.number_format((float)($aRow['moneyraised']*  
				$after_app_fee_percentage
				 ), 2, '.', ',').'</td>';
		// Campaign Goal
			$row[] = '<td>'.number_format($aRow['fld_campaign_goal'],2,'.',',').'</td>';
		// % of Goal
			if ($aRow['moneyraised'] > 0 && $aRow['fld_campaign_goal'] > 0) {
				$row[] = '<td>'.number_format((float)($aRow['moneyraised']*$after_app_fee_percentage/$aRow['fld_campaign_goal'])*100, 1, '.', '').' %</td>';
			} else {
				$row[] = '<td>0.0 %</td>';
			}
		// Money Raised
			$row[] = '<td>'.number_format($aRow['moneyraised'], 2, '.', ',').'</td>';
		// Fast Pay Amount Available
			$row[] = '<td><button class="btn btn-block btn-primary fastpayclick '.$buttondisenbale.'" accid="'.$accid.'" bankaccount="'.$bank_accountid.'" cid="'.$aRow['fld_campaign_id'].'" camptitle="'.$aRow['fld_campaign_title'].'" emailfrom="'.$aRow['fld_cemail'].'" fname="'.$_SESSION['uname'].'" lname="'.$_SESSION['ulname'].'" amountlimit="'.number_format($amountlimit,2,'.','').'" cuid="'.$aRow['fld_uid'].'" sdate="'.date('m/d/Y',strtotime($aRow['fld_campaign_sdate'])).'" edate="'.date('m/d/Y',strtotime($aRow['fld_campaign_edate'])).'" rid="'.$aRow['rid'].'" rname="'.$aRow['rfname'].' '.$aRow['rlname'].'" did="'.$aRow['did'].'" dname="'.$aRow['dfname'].' '.$aRow['dlname'].'" aid="'.$aRow['aid'].'" aname="'.$aRow['afname'].' '.$aRow['alname'].'" style="width:100px; margin-top:0px;"><span class="fa fa-money"></span> <span class="newtext">Fast Pay</span></button></td>';
		// Money Withdrawn
			$row[] = '<td>'.number_format($aRow['cfirstpayment'] + $aRow['csecondpayment'],2,'.',',').'</td>';
		// Remaining Profit to be paid
			$row[] = '<td>'.number_format((float)(($aRow['moneyraised']*$after_app_fee_percentage) - ($aRow['cfirstpayment'] + $aRow['csecondpayment'])), 2, '.', '').'</td>';
		
		if ($rid == 1) { //Administrator
			// die('rid--'.$rid);
			$adminpercentage = 0;
			if ($aRow['did'] > 0 && $aRow['rid'] == 0) {
				if ($repper > 0) {
					$reppercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$despercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$admincalc = $aRow['fld_admin_per'];
				} else {
					$reppercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$despercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$admincalc = $aRow['fld_admin_per'];
				}
				$distpercentage = number_format((float)(($aRow['moneyraised'] / 100) * $reppercalc), 2, '.', '');
				$reppercentage = 0;
				$adminpercentage = number_format((float)(($aRow['moneyraised'] / 100) * (($after_app_fee_20p*100))), 2, '.', '');
				// Comm. Level
				$row[] = '<td>'.number_format((float)($aRow['commlevel']), 2, '.', '').' %</td>';
				// Dist. Comm
				$dist_com = number_format((float)(($aRow['moneyraised'] / 100) * $reppercalc), 2, '.', '');
				$row[] = '<td>'.$dist_com.'</td>';
				// Dist. Name
				$row[] = '<td>'.$aRow['dfname'].' '.$aRow['dlname'].'</td>';
				// Contact. Name
				$row[] = '<td>'.$aRow['contactfname'].' '.$aRow['contactlname'].'</td>';
				// Comp. Name
				if ($aRow['fld_cname'] != '') {
					$row[] = '<td>'.$aRow['fld_cname'].'</td>';
				} else {
					$row[] = '<td>'.$aRow['comp_name'].'</td>';
				}
				// Rep. Comm
				$row[] = '<td>&nbsp;</td>';
				// Rep. Name
				$row[] = '<td>&nbsp;</td>';
			} 
			elseif ($aRow['did'] == 0 && $aRow['rid'] > 0) {
				if ($repper > 0) {
					$reppercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$despercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$admincalc = $aRow['fld_admin_per'];
				} else {
					$reppercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$despercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$admincalc = $aRow['fld_admin_per'];
				}
				$distpercentage = number_format((float)(($aRow['moneyraised'] / 100) * $reppercalc), 2, '.', '');
				$reppercentage = 0;
				$adminpercentage = number_format((float)(($aRow['moneyraised'] / 100) * (($after_app_fee_20p*100))), 2, '.', '');

				// $adminpercentage = 1001;
				// Comm. Level
				$row[] = '<td>'.number_format((float)($aRow['commlevel']), 2, '.', '').' %</td>';
				// Dist. Comm
				$row[] = '<td>&nbsp;</td>';
				// Dist. Name
				$row[] = '<td>&nbsp;</td>';
				// Contact. Name
				$row[] = '<td>'.$aRow['contactfname'].' '.$aRow['contactlname'].'</td>';
				// Comp. Name
				if ($aRow['fld_cname'] != '') {
					$row[] = '<td>'.$aRow['fld_cname'].'</td>';
				} else {
					$row[] = '<td>'.$aRow['comp_name'].'</td>';
				}
				// Rep. Comm
				$row[] = '<td>&nbsp;</td>';
				// Rep. Name
				$row[] = '<td>'.$aRow['rfname'].' '.$aRow['rlname'].'</td>';
			} 
			elseif ($aRow['did'] > 0 && $aRow['rid'] > 0) { 
				if ($repper > 0 && $desper > 0) {
					$reppercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$despercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$admincalc = $aRow['fld_admin_per'];
				} else {
					$reppercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$despercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$admincalc = $aRow['fld_admin_per'];
				}
				$distpercentage = number_format((float)(($aRow['moneyraised'] / 100) * $reppercalc), 2, '.', '');
				$reppercentage = 0;
				$adminpercentage = number_format((float)(($aRow['moneyraised'] / 100) * (($after_app_fee_20p*100))), 2, '.', '');

				// $adminpercentage = 1011;
				// Comm. Level
				$row[] = '<td>'.number_format((float)($aRow['commlevel']), 2, '.', '').' %</td>';
				// Dist. Comm
				$dist_com = number_format((float)(($aRow['moneyraised'] / 100) * $despercalc), 2, '.', '');
				$row[] = '<td>'.$dist_com.'</td>';
				// Dist. Name
				$row[] = '<td>'.$aRow['dfname'].' '.$aRow['dlname'].'</td>';
				// Contact. Name
				$row[] = '<td>'.$aRow['contactfname'].' '.$aRow['contactlname'].'</td>';
				// Comp. Name
				if ($aRow['fld_cname'] != '') {
					$row[] = '<td>'.$aRow['fld_cname'].'</td>';
				} else {
					$row[] = '<td>'.$aRow['comp_name'].'</td>';
				}
				// Rep. Comm
				$row[] = '<td>&nbsp;</td>';
				// Rep. Name
				$row[] = '<td>'.$aRow['rfname'].' '.$aRow['rlname'].'</td>';
			} 
			elseif ($aRow['did'] == 0 && $aRow['rid'] == 0) {
				if ($repper > 0) {
					$reppercalc = 0;
					$admincalc = $aRow['fld_admin_per']+$aRow['fld_dist_per']+$aRow['fld_rep_per'];
				} else {
					$reppercalc = 0;
					$admincalc = $aRow['fld_admin_per']+$aRow['fld_dist_per']+$aRow['fld_rep_per'];
				}
				$distpercentage = number_format((float)(($aRow['moneyraised'] / 100) * 20), 2, '.', '');
				$reppercentage = 0;
				$adminpercentage = number_format((float)(($aRow['moneyraised'] / 100) * (($after_app_fee_20p*100))), 2, '.', '');

				// $adminpercentage = 00;
				// Comm. Level
				$row[] = '<td>&nbsp;</td>';
				// Dist. Comm
				$row[] = '<td>&nbsp;</td>';
				// Dist. Name
				$row[] = '<td>&nbsp;</td>';
				// Contact. Name
				$row[] = '<td>'.$aRow['contactfname'].' '.$aRow['contactlname'].'</td>';
				// Comp. Name
				if ($aRow['fld_cname'] != '') {
					$row[] = '<td>'.$aRow['fld_cname'].'</td>';
				} else {
					$row[] = '<td>'.$aRow['comp_name'].'</td>';
				}
				// Rep. Comm
				$row[] = '<td>&nbsp;</td>';
				// Rep. Name
				$row[] = '<td>&nbsp;</td>';
			}
			// UFund Comm/ ER comm
			// $row[] = '<td>'.$aRow['did'].'++'.$aRow['rid'].'----'.$adminpercentage.'</td>';
			$adminpercentage = $adminpercentage - $dist_com;
			$row[] = '<td>'.$adminpercentage.'</td>';
			// Campaign Paid
			$row[] = '<td><a class="camphistory" cid="'.$aRow['fld_campaign_id'].'" ctitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.number_format((float)(($aRow['cfirstpayment'] + $aRow['csecondpayment'])), 2, '.', '').'</a></td>';
			// Dist. Paid
			$row[] = '<td><a class="disthistory" cid="'.$aRow['fld_campaign_id'].'" ctitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.number_format((float)(($aRow['dpayment'])), 2, '.', '').'</a></td>';
			// Rep. Paid
			$row[] = '<td><a class="rephistory" cid="'.$aRow['fld_campaign_id'].'" ctitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.number_format((float)(($aRow['rpayment'])), 2, '.', '').'</a></td>';
			// Account ID
			$row[] = '<td>'.$aRow['fld_ac'].'</td>';
		
		} 
		elseif ($rid == 3) { //Distributor
		
			if ($aRow['did'] > 0 && $aRow['rid'] == 0) {
				if ($repper > 0) {
					$reppercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$despercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$admincalc = 0;
				} else {
					$reppercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$despercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$admincalc = 0;
				}
				$distpercentage = number_format((float)(($aRow['moneyraised'] / 100) * $reppercalc), 2, '.', '');
				$reppercentage = 0;
				// Comm. Level
				$row[] = '<td>'.number_format((float)($aRow['commlevel']), 2, '.', '').' %</td>';
				// Dist. Comm
				$dist_com = number_format((float)(($aRow['moneyraised'] / 100) * $reppercalc), 2, '.', '');
				$row[] = '<td>'.$dist_com.'</td>';
				// Dist. Name
				$row[] = '<td>'.$aRow['dfname'].' '.$aRow['dlname'].'</td>';
				// Contact. Name
				$row[] = '<td>'.$aRow['contactfname'].' '.$aRow['contactlname'].'</td>';
				// Comp. Name
				if ($aRow['fld_cname'] != '') {
					$row[] = '<td>'.$aRow['fld_cname'].'</td>';
				} else {
					$row[] = '<td>'.$aRow['comp_name'].'</td>';
				}
				// Rep. Comm
				$row[] = '<td>&nbsp;</td>';
				// Rep. Name
				$row[] = '<td>&nbsp;</td>';
			} elseif ($aRow['did'] == 0 && $aRow['rid'] > 0) {
				if ($repper > 0) {
					$reppercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$despercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$admincalc = 0;
				} else {
					$reppercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$despercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$admincalc = 0;
				}
				$distpercentage = 0;
				$reppercentage = 0;
				$adminpercentage = number_format((float)(($aRow['moneyraised'] / 100) * (($after_app_fee_20p*100))), 2, '.', '');


				// Comm. Level
				$row[] = '<td>&nbsp;</td>';
				// Dist. Comm
				$row[] = '<td>&nbsp;</td>';
				// Dist. Name
				$row[] = '<td>&nbsp;</td>';
				// Contact. Name
				$row[] = '<td>'.$aRow['contactfname'].' '.$aRow['contactlname'].'</td>';
				// Comp. Name
				if ($aRow['fld_cname'] != '') {
					$row[] = '<td>'.$aRow['fld_cname'].'</td>';
				} else {
					$row[] = '<td>'.$aRow['comp_name'].'</td>';
				}
				// Rep. Comm
				$row[] = '<td>&nbsp;</td>';
				// Rep. Name
				$row[] = '<td>'.$aRow['rfname'].' '.$aRow['rlname'].'</td>';
			} elseif ($aRow['did'] > 0 && $aRow['rid'] > 0) { 
				if ($repper > 0 && $desper > 0) {
					$reppercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$despercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$admincalc = $aRow['fld_admin_per'];
				} else {
					$reppercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$despercalc = $aRow['fld_dist_per']+$aRow['fld_rep_per'];
					$admincalc = $aRow['fld_admin_per'];
				}
				$distpercentage = number_format((float)(($aRow['moneyraised'] / 100) * $reppercalc), 2, '.', '');
				// Comm. Level
				$row[] = '<td>'.number_format((float)($aRow['commlevel']), 2, '.', '').' %</td>';
				// Dist. Comm
				$dist_com = number_format((float)(($aRow['moneyraised']) * $despercalc), 2, '.', '');
				$row[] = '<td>'.$dist_com.'</td>';
				// Dist. Name
				$row[] = '<td>'.$aRow['dfname'].' '.$aRow['dlname'].'</td>';
				// Contact. Name
				$row[] = '<td>'.$aRow['contactfname'].' '.$aRow['contactlname'].'</td>';
				// Comp. Name
				if ($aRow['fld_cname'] != '') {
					$row[] = '<td>'.$aRow['fld_cname'].'</td>';
				} else {
					$row[] = '<td>'.$aRow['comp_name'].'</td>';
				}
				// Rep. Comm
				$row[] = '<td>&nbsp;</td>';
				// Rep. Name
				$row[] = '<td>'.$aRow['rfname'].' '.$aRow['rlname'].'</td>';
			} elseif ($aRow['did'] == 0 && $aRow['rid'] == 0) {
				if ($repper > 0) {
					$lencount = strlen($repper);
					$lencounter = $lencount+1;
					$newrepper = str_pad($repper, $lencounter, "0", STR_PAD_LEFT);
					$newrepper2 = str_replace('.', '', $newrepper);
					$reppercalc = "0.$newrepper2";
					$reppercalc = 0;
					$admincalc = 0.07;
				} else {
					$reppercalc = 0;
					$admincalc = 0.07;
				}
				$distpercentage = number_format((float)(($aRow['moneyraised']) * $reppercalc), 2, '.', '');
				$reppercentage = 0;
				// Comm. Level
				$row[] = '<td>'.$aRow['commlevel'].'</td>';
				// Dist. Comm
				$row[] = '<td>&nbsp;</td>';
				// Dist. Name
				$row[] = '<td>&nbsp;</td>';
				// Contact. Name
				$row[] = '<td>'.$aRow['contactfname'].' '.$aRow['contactlname'].'</td>';
				// Comp. Name
				if ($aRow['fld_cname'] != '') {
					$row[] = '<td>'.$aRow['fld_cname'].'</td>';
				} else {
					$row[] = '<td>'.$aRow['comp_name'].'</td>';
				}
				// Rep. Comm
				$row[] = '<td>&nbsp;</td>';
				// Rep. Name
				$row[] = '<td>&nbsp;</td>';
			}
			// Campaign Paid
			$row[] = '<td><a class="camphistory" cid="'.$aRow['fld_campaign_id'].'" ctitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.number_format((float)(($aRow['cfirstpayment'] + $aRow['csecondpayment'])), 2, '.', '').'</a></td>';
			// Dist. Paid
			$row[] = '<td><a class="disthistory" cid="'.$aRow['fld_campaign_id'].'" ctitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.number_format((float)(($aRow['dpayment'])), 2, '.', '').'</a></td>';
			// Rep. Paid
			$row[] = '<td><a class="rephistory" cid="'.$aRow['fld_campaign_id'].'" ctitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.number_format((float)(($aRow['rpayment'])), 2, '.', '').'</a></td>';
		
		}

		elseif ($rid == 6) { //Representative 
		
			if ($aRow['did'] > 0 && $aRow['rid'] == 0) {
				if ($repper > 0) {
					$lencount = strlen($repper);
					$lencounter = $lencount+1;
					$newrepper = str_pad($repper, $lencounter, "0", STR_PAD_LEFT);
					$newrepper2 = str_replace('.', '', $newrepper);
					$reppercalc = "0.$newrepper2";
					$reppercalc = 0.07;
					$admincalc = 0;
				} else {
					$reppercalc = 0.07;
					$admincalc = 0;
				}
				$distpercentage = number_format((float)(($aRow['moneyraised']) * $reppercalc), 2, '.', '');
				$reppercentage = 0;
				// Comp. Name
				if ($aRow['fld_cname'] != '') {
					$row[] = '<td>'.$aRow['fld_cname'].'</td>';
				} else {
					$row[] = '<td>'.$aRow['comp_name'].'</td>';
				}
				// Rep. Comm
				$row[] = '<td>&nbsp;</td>';
				// Rep. Name
				$row[] = '<td>&nbsp;</td>';
			} elseif ($aRow['did'] == 0 && $aRow['rid'] > 0) {
				if ($repper > 0) {
					$lencount = strlen($repper);
					$lencounter = $lencount+1;
					$newrepper = str_pad($repper, $lencounter, "0", STR_PAD_LEFT);
					$newrepper2 = str_replace('.', '', $newrepper);
					$reppercalc = "0.$newrepper2";
					$reppercalc = 0.07;
					$admincalc = 0;
				} else {
					$reppercalc = 0.07;
					$admincalc = 0;
				}
				$distpercentage = 0;
				$reppercentage = number_format((float)(($aRow['moneyraised']) * $reppercalc), 2, '.', '');
				// Comp. Name
				if ($aRow['fld_cname'] != '') {
					$row[] = '<td>'.$aRow['fld_cname'].'</td>';
				} else {
					$row[] = '<td>'.$aRow['comp_name'].'</td>';
				}
				// Rep. Comm
				$row[] = '<td>'.number_format((float)(($aRow['moneyraised']) * $reppercalc), 2, '.', '').'</td>';
				// Rep. Name
				$row[] = '<td>'.$aRow['rfname'].' '.$aRow['rlname'].'</td>';
			} elseif ($aRow['did'] > 0 && $aRow['rid'] > 0) { 
				if ($repper > 0 && $desper > 0) {
					$lencount = strlen($repper);
					$lencounter = $lencount+1;
					$newrepper = str_pad($repper, $lencounter, "0", STR_PAD_LEFT);
					$newrepper2 = str_replace('.', '', $newrepper);
					$reppercalc = "0.$newrepper2";
					$newdesper = str_pad($desper, $lencounter, "0", STR_PAD_LEFT);
					$newdesper2 = str_replace('.', '', $newdesper);
					$despercalc = "0.$newdesper2";
					$admincalc = 0;
				} else {
					$reppercalc = 0.05;
					$despercalc = 0.02;
					$admincalc = 0;
				}
				$distpercentage = number_format((float)(($aRow['moneyraised']) * $reppercalc), 2, '.', '');
				$reppercentage = number_format((float)(($aRow['moneyraised']) * $despercalc), 2, '.', '');
				// Comp. Name
				if ($aRow['fld_cname'] != '') {
					$row[] = '<td>'.$aRow['fld_cname'].'</td>';
				} else {
					$row[] = '<td>'.$aRow['comp_name'].'</td>';
				}
				// Rep. Comm
				$row[] = '<td>'.number_format((float)(($aRow['moneyraised']) * $reppercalc), 2, '.', '').'</td>';
				// Rep. Name
				$row[] = '<td>'.$aRow['rfname'].' '.$aRow['rlname'].'</td>';
			} elseif ($aRow['did'] == 0 && $aRow['rid'] == 0) {
				if ($repper > 0) {
					$lencount = strlen($repper);
					$lencounter = $lencount+1;
					$newrepper = str_pad($repper, $lencounter, "0", STR_PAD_LEFT);
					$newrepper2 = str_replace('.', '', $newrepper);
					$reppercalc = "0.$newrepper2";
					$reppercalc = 0;
					$admincalc = 0.07;
				} else {
					$reppercalc = 0;
					$admincalc = 0.07;
				}
				$distpercentage = number_format((float)(($aRow['moneyraised']) * $reppercalc), 2, '.', '');
				$reppercentage = 0;
				// Comp. Name
				if ($aRow['fld_cname'] != '') {
					$row[] = '<td>'.$aRow['fld_cname'].'</td>';
				} else {
					$row[] = '<td>'.$aRow['comp_name'].'</td>';
				}
				// Rep. Comm
				$row[] = '<td>'.number_format((float)(($aRow['moneyraised']) * $reppercalc), 2, '.', '').'</td>';
				// Rep. Name
				$row[] = '<td>'.$aRow['rfname'].' '.$aRow['rlname'].'</td>';
			}
			// Campaign Paid
			$row[] = '<td><a class="camphistory" cid="'.$aRow['fld_campaign_id'].'" ctitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.number_format((float)(($aRow['cfirstpayment'] + $aRow['csecondpayment'])), 2, '.', '').'</a></td>';
			// Rep. Paid
			$row[] = '<td><a class="rephistory" cid="'.$aRow['fld_campaign_id'].'" ctitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.number_format((float)(($aRow['rpayment'])), 2, '.', '').'</a></td>';
			
		}
		elseif ($rid == 2) { //Campaign Manager 
			// Comp. Name
			if ($aRow['fld_cname'] != '') {
				$row[] = '<td>'.$aRow['fld_cname'].'</td>';
			} else {
				$row[] = '<td>'.$aRow['comp_name'].'</td>';
			}
			// Campaign Paid
			$row[] = '<td><a class="camphistory" cid="'.$aRow['fld_campaign_id'].'" ctitle="'.$aRow['fld_campaign_title'].'" href="javascript:void(0);">'.number_format((float)(($aRow['cfirstpayment'] + $aRow['csecondpayment'])), 2, '.', '').'</a></td>';
		}
		
		if ($rid == 1) {
			$admin_paymentcenter = '<a href="#" class="paymentcenterclick" style="margin-right:10px;" cid="'.$aRow['fld_campaign_id'].'" camptitle="'.$aRow['fld_campaign_title'].'" cuid="'.$aRow['fld_uid'].'" campprofitraised="'.number_format((float)($aRow['moneyraised']*$after_app_fee_percentage), 2, '.', '').'" moneyraised="'.$aRow['moneyraised'].'" ufund4us="'.number_format((float)(($aRow['moneyraised'])*0.10), 2, '.', '').'" distper="'.$distpercentage.'" repper="'.$reppercentage.'" cid="'.$aRow['fld_campaign_id'].'" rname="'.$aRow['rfname'].' '.$aRow['rlname'].'" rid="'.$aRow['rid'].'" dname="'.$aRow['dfname'].' '.$aRow['dlname'].'" did="'.$aRow['did'].'" aname="'.$aRow['afname'].' '.$aRow['alname'].'" aid="'.$aRow['aid'].'" cfirstpayment="'.$aRow['cfirstpayment'].'" cfirstpaiddate="'.$aRow['cfirstpaiddate'].'" cfirstcheckno="'.$aRow['cfirstcheckno'].'" csecondpayment="'.$aRow['csecondpayment'].'" csecondpaiddate="'.$aRow['csecondpaiddate'].'" csecondcheckno="'.$aRow['csecondcheckno'].'" rpayment="'.$aRow['rpayment'].'" rpaiddate="'.$aRow['rpaiddate'].'" rcheckno="'.$aRow['rcheckno'].'" dpayment="'.$aRow['dpayment'].'" dpaiddate="'.$aRow['dpaiddate'].'" dcheckno="'.$aRow['dcheckno'].'" data-toggle="tooltip" data-placement="top" data-original-title="Payment Center"><span class="fa fa-usd"></span></a>';
			if ($aRow['fld_ac'] != '') {
				$admin_stripe = '<a href="https://dashboard.stripe.com/'.$aRow['fld_ac'].'/dashboard" target="_blank" style="margin-right:10px;" data-toggle="tooltip" data-placement="top" data-original-title="Stripe Account ID: '.$aRow['fld_ac'].'"><span class="fa fa-cc-stripe"></span></a>';
			} else {
				$admin_stripe = '';
			}
		} else {
			$admin_paymentcenter = '';
			$admin_stripe = '';
		}
		if($aRow['fld_status'] == 1){
			$active_deactive = '<a href="manage_campaign.php?id='.$aRow['fld_campaign_id'].'&s=2&m=edit" onClick="return confirmStatus(2)" style="margin-right:10px;" data-toggle="tooltip" data-placement="top" data-original-title="Deactivate"><span class="fa fa-fw fa-thumbs-o-down"></span> </a>';
		} else {
			$active_deactive = '<a href="manage_campaign.php?id='.$aRow['fld_campaign_id'].'&s=1&m=edit" style="margin-right:10px;" onClick="return confirmStatus(1)"><span class="fa fa-fw fa-thumbs-o-up"></span> </a>';
		}
		
		$row[] = '
		<td align="left">
			<a href="start_campaign.php?m=e&cid='.$aRow['fld_campaign_id'].'" style="margin-right:10px;" data-toggle="tooltip" data-placement="top" data-original-title="Edit"><span class="glyphicon glyphicon-pencil"></span></a> 
			'.$active_deactive.'
			<a href="manage_campaign.php?id='.$aRow['fld_campaign_id'].'&m=del" style="margin-right:10px;" onClick="return confirmCampaignDelete()" data-toggle="tooltip" data-placement="top" data-original-title="Delete"><span class="glyphicon glyphicon-remove-circle"></span></a>
			'.$admin_paymentcenter.'
			'.$admin_stripe.'
        </td>';
		
		$output['aaData'][] = $row; 
	}
	
	echo json_encode( $output );
?>