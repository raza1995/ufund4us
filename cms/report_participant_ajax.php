<?php
	include_once ('php/dbconn.php');
	$REQUEST = &$_REQUEST;	
	//Declare variable required bellow
	$rid = $REQUEST['rid'];
	$uid = $REQUEST['uid'];
	
	//Get SparkPost Bounces Email
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.sparkpost.com/api/v1/suppression-list?per_page=10000");
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
	
	//for testing
	// echo '<pre>'; print_r($array_bounce ); die();
	// $array_bounce = array();

	//End Get SparkPost Bounces Email
	$aColumns = array('b.fld_campaign_id','b.fld_campaign_title','a.uid','a.uname','a.ulname','b.fld_donor_size AS donorrequire','up.fld_image',
	'(SELECT COUNT(e.puid) FROM tbl_donors_details e WHERE e.cid = a.cid AND e.puid = a.uid) AS donorupload',
	'(SELECT COUNT(f.donation_amount) FROM tbl_donations f WHERE f.cid = a.cid AND f.refferal_by = a.uid AND f.mode = 1) AS donation_amount',
	'(SELECT SUM(g.donation_amount) FROM tbl_donations g WHERE g.cid = a.cid AND g.refferal_by = a.uid AND g.mode = 1) AS sumofdonations',
	'b.fld_participant_goal AS participantgoal');
	$aColumns1 = array('b.fld_campaign_id','b.fld_campaign_title','a.uname','b.fld_donor_size AS donorrequire',
	'(SELECT COUNT(e.puid) FROM tbl_donors_details e WHERE e.cid = a.cid AND e.puid = a.uid) AS donorupload',
	'',
	'b.fld_participant_goal AS participantgoal',
	'(SELECT SUM(g.donation_amount) FROM tbl_donations g WHERE g.cid = a.cid AND g.refferal_by = a.uid AND g.mode = 1) AS sumofdonations', 'up.fld_image');
	$sIndexColumn = "b.fld_campaign_id";
	$sTable = "tbl_participants_details";
	
	
	/* Database connection information */
	/*$gaSql['user']       = "root";
	$gaSql['password']   = "mysql";
	$gaSql['db']         = "ufunds4u";
	$gaSql['server']     = "localhost";*/
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * MySQL connection
	 */
	$gaSql['link'] =  $con or
		die( 'Could not open connection to server' );
	
	// mysqli_select_db($con,$DB_name) or die( 'Could not select database '. $DB_name );
	
	$REQUEST['sSearch'] = $sSearch = mysqli_real_escape_string($con,  $REQUEST['sSearch'] );
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $REQUEST['iDisplayStart'] ) && $REQUEST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysqli_real_escape_string($con,  $REQUEST['iDisplayStart'] ).", ".
			mysqli_real_escape_string($con,  $REQUEST['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	if ( isset( $REQUEST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $REQUEST['iSortingCols'] ) ; $i++ )
		{
			if ( $REQUEST[ 'bSortable_'.intval($REQUEST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $REQUEST['iSortCol_'.$i] ) ]."
				 	".mysqli_real_escape_string($con,  $REQUEST['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	if ($rid == 1) {
		$sWhere = "";
	} else {
		$sWhere = "WHERE b.fld_uid = '$uid' AND b.fld_ab1575_pupil_fee = 0 ";
	}

	if ( $REQUEST['sSearch'] != "" )
	{
		if ($rid == 1) {
			$sWhere = " WHERE (";
		} {
			$sWhere = " WHERE b.fld_uid = '$uid' AND b.fld_ab1575_pupil_fee = 0 AND (";
		} 

		$temWheres = [];
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			// $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($con,  $REQUEST['sSearch'] )."%' OR ";


			if($aColumns[$i] == "dd.uid AS DonorId"){
				if( is_int($sSearch) ){
					// $sWhere .= $aColumns[$i]." = '".$sSearch."' OR ";
					$temWheres[] = $aColumns[$i]." = '".$sSearch."' ";
				}
			}
			else{
				// $sWhere .= $aColumns[$i]." LIKE '%".$sSearch."%' OR ";

				$tempColName = explode("AS", $aColumns[$i]);
				$temWheres[] = $tempColName[0]." LIKE '%".$sSearch."%'  ";
			}
		}
		// echo '<pre>'; print_r($temWheres); die();
		$sWhere .= implode(" OR ", $temWheres);
		// die($sWhere);
		// $sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	$temWheres = [];
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($REQUEST['bSearchable_'.$i]) && $REQUEST['bSearchable_'.$i] == "true" && isset($REQUEST['sSearch_'.$i]) && $REQUEST['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			// $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($con, $REQUEST['sSearch_'.$i])."%' ";

			$tempColName = explode("AS", $aColumns[$i]);
			$temWheres[] = $tempColName[0]." LIKE '%".mysqli_real_escape_string($con, $REQUEST['sSearch_'.$i])."%' ";
		}

		$sWhere .= implode(" OR ", $temWheres);
	}
	
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	if ($rid == 1) {
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM $sTable a
			LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid 
			LEFT JOIN tbl_users up ON up.fld_uid = a.uid 
			$sWhere 
			GROUP BY a.uid 
			$sOrder
			$sLimit
		";
	} else {
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM   $sTable a 
			LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid
			LEFT JOIN tbl_users up ON up.fld_uid = a.uid
			$sWhere 
			GROUP BY a.uid
			$sOrder
			$sLimit
		";
	} 
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
	if ($rid == 1) {
		$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable a 
		LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid 
		LEFT JOIN tbl_users up ON up.fld_uid = a.uid 
		GROUP BY a.uid
		";
	} else {
		$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable a
		LEFT JOIN tbl_campaign b ON b.fld_campaign_id = a.cid 
		LEFT JOIN tbl_users up ON up.fld_uid = a.uid 
		$sWhere
		GROUP BY a.uid
		";
	}
	// echo $sQuery; die(); 
	$rResultTotal = mysqli_query( $gaSql['link'], $sQuery ) or die(mysqli_error($con));
	$aResultTotal = mysqli_fetch_array($rResultTotal, MYSQLI_NUM);;
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($REQUEST['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysqli_fetch_array($rResult, MYSQLI_ASSOC) )
	{
		$uid = $aRow['uid'];
		$cid = $aRow['fld_campaign_id'];
		//Get Donors Details
		$BadEmailCounter = 0;
		$sQuery = "SELECT uemail, is_read, is_unsubscribe, sent_email FROM tbl_donors_details WHERE cid='$cid' AND puid='$uid'";
		$BadEmailDetailExec = mysqli_query( $gaSql['link'], $sQuery ) or die(mysqli_error($con));
		$BadEmailDetailCount = mysqli_num_rows($BadEmailDetailExec);
		while($BadEmailDetailRow = mysqli_fetch_assoc($BadEmailDetailExec)){
			$BadEmailDetail[] = $BadEmailDetailRow;
		}
		foreach ($array_bounce['results'] as $bounce) {
			//if ($bounce['source'] == 'Bounce Rule') {
				$bademail = $bounce['recipient'];
				for ($zz=0; $zz < $BadEmailDetailCount; $zz++) {
					if ($bademail == $BadEmailDetail[$zz]['uemail'] && $BadEmailDetail[$zz]['is_read'] == 0) {
						$BadEmailCounter++;
					}
				}
			//}
		}
		$moneyraised = number_format($aRow['sumofdonations'], 2, '.', '');
		if ($aRow['fld_image'] != '') {
			$fld_image = 'Yes';
		} else {
			$fld_image = 'No';
		}
		$row = array();
		/* General output */
		$row[] = '<td>'.$aRow['fld_campaign_id'].'</td>';
		$row[] = '<td>'.$aRow['fld_campaign_title'].'</td>';
		$row[] = '<td>'.$aRow['uname'].' '.$aRow['ulname'].'</td>';
		$row[] = '<td>'.$aRow['donorrequire'].'</td>';
		$row[] = '<td>'.$aRow['donorupload'].'</td>';
		$row[] = '<td>'.$BadEmailCounter.'</td>';
		$row[] = '<td>'.$aRow['participantgoal'].'</td>';
		$row[] = '<td>'.$moneyraised.'</td>';
		$row[] = '<td>'.$fld_image.'</td>';
		$output['aaData'][] = $row; 
	}
	
	echo json_encode( $output );
?>