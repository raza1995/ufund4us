<?php
	error_reporting(5);
	ini_set('max_execution_time', 600); //600 seconds = 10 minutes
	date_default_timezone_set('America/Los_Angeles'); //TimeZone
	include_once ('php/dbconn.php');
	$REQUEST = &$_REQUEST;
	$rid = checkSetInArrayAndReturn($REQUEST, 'rid', 0);
	$uid = $REQUEST['uid'];
	$mode = $REQUEST['mode'];
	$array_bounce = [];
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
		$array_bounce['results'] = isset($array_bounce['results']) ? $array_bounce['results'] : [];
	}
	//print_r($array_bounce );
	//End Get SparkPost Bounces Email
	
	$aColumns0 = array(
			'a.fld_campaign_id',
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
		'a.app_fee_percentage',
		'a.fld_campaign_id',
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
	$whereclause = ""; 
	$aColumns1 = array();
	if ($rid == 1) {
		$aColumns1 = array(
			'a.fld_status', 
			'a.fld_campaign_id', 
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
			'moneyraised*0.8', 
			'a.fld_campaign_goal', 
			'(moneyraised*0.8 / a.fld_campaign_goal)*100', 
			'moneyraised', 
			'', 
			'l.cfirstpayment + l.csecondpayment', 
			'(moneyraised*0.8) - (l.cfirstpayment + l.csecondpayment)', 
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
	} 
	

	$gaSql['link'] =  $con or
		die( 'Could not open connection to server' );
	
	// mysqli_select_db($con,$DB_name) or die( 'Could not select database '. $DB_name );
	
	
	$sLimit = "";
	if ( isset( $REQUEST['iDisplayStart'] ) && $REQUEST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysqli_real_escape_string($con,  $REQUEST['iDisplayStart'] ).", ".
			mysqli_real_escape_string($con,  $REQUEST['iDisplayLength'] );
	}
	
	
	if ( isset( $REQUEST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $REQUEST['iSortingCols'] ) ; $i++ )
		{
			if ( $REQUEST[ 'bSortable_'.intval($REQUEST['iSortCol_'.$i]) ] == "true" )
			{	
				if( isset($aColumns1[ intval( $REQUEST['iSortCol_'.$i] ) ]) ){
					$sOrder .= $aColumns1[ intval( $REQUEST['iSortCol_'.$i] ) ]."
				 		".mysqli_real_escape_string($con,  $REQUEST['sSortDir_'.$i] ) .", ";
				}
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	$whereclause = ($whereclause != "") ? $whereclause : 'Where 1=1 ';
	$sWhere = $whereclause;
	if ( $REQUEST['sSearch'] != "" )
	{
		$sWhere = $whereclause." AND (";
		for ( $i=0 ; $i<count($aColumns0) ; $i++ )
		{
			$sWhere .= $aColumns0[$i]." LIKE '%".mysqli_real_escape_string($con,  $REQUEST['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns0) ; $i++ )
	{
		if ( isset($REQUEST['bSearchable_'.$i]) && $REQUEST['bSearchable_'.$i] == "true" 
			&& isset($REQUEST['sSearch_'.$i]) && $REQUEST['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns0[$i]." LIKE '%".mysqli_real_escape_string($con, $REQUEST['sSearch_'.$i])."%' ";
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
	// echo $sQuery; die();
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
		"sEcho" => intval($REQUEST['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysqli_fetch_array($rResult, MYSQLI_ASSOC) )
	{
		// echo '<pre>'; print_r($aRow); die();
		$row = array();
		$after_app_fee_percentage = get_after_app_fee_percentage($aRow);
		
		/* General output */
		$accid = $aRow['fld_ac'];
		$bank_accountid = $aRow['fld_bank_accno'];
		$cid = trim($aRow['fld_campaign_id']);
		$getpercent = "SELECT * FROM tbl_transaction WHERE cid='".$cid."' ORDER BY id DESC LIMIT 1";
		$resultpercent = mysqli_query( $gaSql['link'], $getpercent ) or die(mysqli_error($con));
		$rowpercent = mysqli_fetch_array($resultpercent, MYSQLI_ASSOC);
		$repper = $rowpercent['repper'];
		$desper = $rowpercent['desper'];
		$adminper = $rowpercent['adminper'];
		if ($mode == 'extended') {
		$BadEmailCounter = 0;
		$getbademailsbycampaign = "SELECT uemail, is_read, is_unsubscribe, sent_email FROM tbl_donors_details WHERE cid='".$cid."'";
		$resultbademailsbycampaign = mysqli_query( $getbademailsbycampaign, $gaSql['link'] ) or die(mysqli_error($con));
		while ($rowbademailsbycampaign1 = mysqli_fetch_array($resultbademailsbycampaign, MYSQLI_ASSOC)) {
			$rowbademailsbycampaign[] = $rowbademailsbycampaign1;
		}
		$BadEmailDetailCount = mysqli_num_rows($resultbademailsbycampaign);
		foreach ($array_bounce['results'] as $bounce) {
			$bademail = $bounce['recipient'];
			for ($zz=0; $zz < $BadEmailDetailCount; $zz++) {
				if ($bademail == $rowbademailsbycampaign[$zz]['uemail'] && $rowbademailsbycampaign[$zz]['is_read'] == 0) {
					$BadEmailCounter++;
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
			$aRow['secondrequestedamount'] = isset($aRow['secondrequestedamount']) ? $aRow['secondrequestedamount'] : 0;
			$buttondisenbale = '';
			$actuallimit = number_format((float)(($aRow['moneyraised']*$after_app_fee_percentage) - ($aRow['firstrequestedamount'] + $aRow['secondrequestedamount'])), 2, '.', '');
			$amountlimit = $actuallimit*0.95;
		} 
		else if ($aRow['firstrequestedamount'] == 0 && $aRow['daysleft'] < 0) {
			$aRow['secondrequestedamount'] = isset($aRow['secondrequestedamount']) ? $aRow['secondrequestedamount'] : 0;
			$buttondisenbale = '';
			$actuallimit = number_format((float)(($aRow['moneyraised']*$after_app_fee_percentage) - ($aRow['firstrequestedamount'] + $aRow['secondrequestedamount'])), 2, '.', '');
			$amountlimit = $actuallimit;
		} 
		else if (isset($aRow['secondrequestedamount']) && $aRow['secondrequestedamount'] == 0 && $aRow['daysleft'] < 0) {
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

		$aRow['app_fee_percentage'] = isset($aRow['app_fee_percentage']) ? $aRow['app_fee_percentage'] : 20;
		//commission
		$row[] = '<td>'.$aRow['app_fee_percentage'].'%</td>';
		

		//donations_count
		$row[] = '<td>'.$aRow['donations'].'</td>';
		

		//actions column
		$actionColumn = '<td align="left" >';
		
		//when has no donations, then we can change application fee
		//After getting donation, don't allow to change application fee of campaign
		if($aRow['donations'] < 1){
			$actionColumn .= '
				<div style="float:left">
					<button class="btn btn-block btn-primary change_fee" style="width:100px; margin-top:0px;"
					data-fld_campaign_id="'.$aRow['fld_campaign_id'].'"
					data-fld_campaign_title="'.$aRow['fld_campaign_title'].'"
					data-app_fee_percentage="'.$aRow['app_fee_percentage'].'"
					>Change Fee</span></button>
				</div>';
		}
		$actionColumn .= '</td>';
		
		$row[] = $actionColumn;
		$output['aaData'][] = $row; 
	}
	
	echo json_encode( $output );
?>