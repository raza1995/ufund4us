<?php
	include_once ('php/dbconn.php');
	$REQUEST = &$_REQUEST;	
	//Declare variable required bellow
	$REQUEST['rid'] = isset($REQUEST['rid']) ? $REQUEST['rid'] : '';
	$REQUEST['uid'] = isset($REQUEST['uid']) ? $REQUEST['uid'] : '';
	$REQUEST['module'] = isset($REQUEST['module']) ? $REQUEST['module'] : '';
	$REQUEST['sSearch'] = isset($REQUEST['sSearch']) ? $REQUEST['sSearch'] : '';
	$REQUEST['sEcho'] = isset($REQUEST['sEcho']) ? $REQUEST['sEcho'] : '';


	$rid = $REQUEST['rid'];
	$uid = $REQUEST['uid'];
	$module = $REQUEST['module'];
	
	if ($module == 2) {
		$aColumns = array( 'dd.uid AS DonorId', 'dd.uname AS DonorFName', 'dd.ulname AS DonorLName', 'dd.uemail AS DonorEmail', 'DATE_FORMAT(dd.is_unsubscribe_date, "%m/%d/%Y") AS is_unsubscribe_date', 'pd.uid AS ParticipantId', 'pd.uname AS ParticipantFName', 'pd.ulname AS ParticipantLName', 'pd.uemail AS ParticipantEmail', 'c.fld_campaign_id AS CampaignId', 'c.fld_cname AS CampaignFName', 'c.fld_clname AS CampaignLName', 'c.fld_campaign_title AS CampaignTitle');
		$aColumns1 = array( 'dd.uname', 'dd.uemail', 'pd.uname', 'pd.uemail', 'c.fld_campaign_id', 'c.fld_cname', 'c.fld_campaign_title', 'DATE_FORMAT(dd.is_unsubscribe_date, "%m/%d/%Y")');
		$sIndexColumn = "dd.uid";
		$sTable = "tbl_donors_details";
		if ($rid == 1) {
			$whereclause = "WHERE dd.is_unsubscribe = '1'";
		} elseif ($rid == 3) {
			$whereclause = "WHERE tr.did = '$uid' AND dd.is_unsubscribe = '1'";
		}  elseif ($rid == 6) {
			$whereclause = "WHERE tr.rid = '$uid' AND dd.is_unsubscribe = '1'";
		} else {
			$whereclause = "WHERE tr.uid = '$uid' AND dd.is_unsubscribe = '1'";
		}
	} 
	elseif ($module == 1) {
		$aColumns = array( 'pd.uid AS ParticipantId', 'pd.uname AS ParticipantFName', 'pd.ulname AS ParticipantLName', 'pd.uemail AS ParticipantEmail', 'DATE_FORMAT(pd.is_unsubscribe_date, "%m/%d/%Y") AS is_unsubscribe_date', 'c.fld_campaign_id AS CampaignId', 'c.fld_cname AS CampaignFName', 'c.fld_clname AS CampaignLName', 'c.fld_campaign_title AS CampaignTitle');
		$aColumns1 = array( 'pd.uname', 'pd.uemail', 'c.fld_campaign_id', 'c.fld_cname', 'c.fld_campaign_title', 'DATE_FORMAT(pd.is_unsubscribe_date, "%m/%d/%Y")');
		$sIndexColumn = "pd.uid";
		$sTable = "tbl_participants_details";
		if ($rid == 1) {
			$whereclause = "WHERE pd.is_unsubscribe = '1'";
		} elseif ($rid == 3) {
			$whereclause = "WHERE tr.did = '$uid' AND pd.is_unsubscribe = '1'";
		}  elseif ($rid == 6) {
			$whereclause = "WHERE tr.rid = '$uid' AND pd.is_unsubscribe = '1'";
		} else {
			$whereclause = "WHERE tr.uid = '$uid' AND pd.is_unsubscribe = '1'";
		}
	} 
	else {
		$aColumns = array( 'dd.uid AS DonorId', 'dd.uname AS DonorFName', 'dd.ulname AS DonorLName', 'dd.uemail AS DonorEmail', 'DATE_FORMAT(dd.is_unsubscribe_date, "%m/%d/%Y") AS is_unsubscribe_date', 'pd.uid AS ParticipantId', 'pd.uname AS ParticipantFName', 'pd.ulname AS ParticipantLName', 'pd.uemail AS ParticipantEmail', 'c.fld_campaign_id AS CampaignId', 'c.fld_cname AS CampaignFName', 'c.fld_clname AS CampaignLName', 'c.fld_campaign_title AS CampaignTitle');
		$aColumns1 = array( 'dd.uname', 'dd.uemail', 'pd.uname', 'pd.uemail', 'c.fld_campaign_id', 'c.fld_cname', 'c.fld_campaign_title', 'DATE_FORMAT(dd.is_unsubscribe_date, "%m/%d/%Y")');
		$sIndexColumn = "dd.uid";
		$sTable = "tbl_donors_details";
		if ($rid == 1) {
			$whereclause = "WHERE dd.is_unsubscribe = '1'";
		} elseif ($rid == 3) {
			$whereclause = "WHERE tr.did = '$uid' AND dd.is_unsubscribe = '1'";
		}  elseif ($rid == 6) {
			$whereclause = "WHERE tr.rid = '$uid' AND dd.is_unsubscribe = '1'";
		} else {
			$whereclause = "WHERE tr.uid = '$uid' AND dd.is_unsubscribe = '1'";
		}
	}

	$gaSql['link'] =  $con or
		die( 'Could not open connection to server' );

	$REQUEST['sSearch'] = $sSearch = mysqli_real_escape_string($con,  $REQUEST['sSearch'] );
	
	// mysqli_select_db($con,$DB_name) or die( 'Could not select database '. $DB_name );
	
	
	$sLimit = "";
	if ( isset( $REQUEST['iDisplayStart'] ) && $REQUEST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysqli_real_escape_string($con,  $REQUEST['iDisplayStart'] ).", ".
			mysqli_real_escape_string($con,  $REQUEST['iDisplayLength'] );
	}
	
	$sOrder = "";				
	if ( isset( $REQUEST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $REQUEST['iSortingCols'] ) ; $i++ )
		{
			if ( $REQUEST[ 'bSortable_'.intval($REQUEST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns1[ intval( $REQUEST['iSortCol_'.$i] ) ]."
				 	".mysqli_real_escape_string($con,  $REQUEST['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	$sWhere = "";
	if ($module == 2) {
		$sWhere = $whereclause;
	} elseif ($module == 1) {
		$sWhere = $whereclause;
	} else {
		$sWhere = $whereclause;
	}
	
	// die($sWhere);

	if ( $REQUEST['sSearch'] != "" )
	{
		$sWhere = $whereclause." AND (";
		$temWheres = [];
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
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
	
	// die($sWhere);
	$temWheres = [];
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{	
		if ( isset($REQUEST['bSearchable_'.$i]) && $REQUEST['bSearchable_'.$i] == "true" &&
			 isset($REQUEST['sSearch_'.$i]) && $REQUEST['sSearch_'.$i] != '' 
		)
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$tempColName = explode("AS", $aColumns[$i]);
			$temWheres[] = $tempColName[0]." LIKE '%".mysqli_real_escape_string($con, $REQUEST['sSearch_'.$i])."%' ";
		}
		$sWhere .= implode(" OR ", $temWheres);
	}
	
	

	if ($module == 2) {
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM $sTable dd
			LEFT JOIN tbl_participants_details pd ON pd.uid = dd.puid
			LEFT JOIN tbl_campaign c ON c.fld_campaign_id = dd.cid
			LEFT JOIN tbl_tree tr ON tr.uid = c.fld_uid
			$sWhere
			$sOrder
			$sLimit
		";
	} elseif ($module == 1) {
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM $sTable pd
			LEFT JOIN tbl_campaign c ON c.fld_campaign_id = pd.cid
			LEFT JOIN tbl_tree tr ON tr.uid = c.fld_uid
			$sWhere
			$sOrder
			$sLimit
		";
	} else {
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM $sTable dd
			LEFT JOIN tbl_participants_details pd ON pd.uid = dd.puid
			LEFT JOIN tbl_campaign c ON c.fld_campaign_id = dd.cid
			LEFT JOIN tbl_tree tr ON tr.uid = c.fld_uid
			$sWhere
			$sOrder
			$sLimit
		";
	}
	// echo $sQuery."<br/>";die();
	$rResult = mysqli_query( $gaSql['link'], $sQuery ) or die(mysqli_error($con));
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysqli_query( $gaSql['link'], $sQuery ) or die(mysqli_error($con));
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal, MYSQLI_NUM);;
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	if ($module == 2) {
		$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable dd 
		LEFT JOIN tbl_participants_details pd ON pd.uid = dd.puid
		LEFT JOIN tbl_campaign c ON c.fld_campaign_id = dd.cid
		LEFT JOIN tbl_tree tr ON tr.uid = c.fld_uid
		$whereclause
		";
	} elseif ($module == 1) {
		$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable pd 
		LEFT JOIN tbl_campaign c ON c.fld_campaign_id = pd.cid
		LEFT JOIN tbl_tree tr ON tr.uid = c.fld_uid
		$whereclause
		";
	} else {
		$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable dd 
		LEFT JOIN tbl_participants_details pd ON pd.uid = dd.puid
		LEFT JOIN tbl_campaign c ON c.fld_campaign_id = dd.cid
		LEFT JOIN tbl_tree tr ON tr.uid = c.fld_uid
		$whereclause
		";
	}
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
		$row = array();
		/* General output */
		if ($module == 2) {
			$row[] = '<td>'.$aRow['DonorFName'].' '.$aRow['DonorLName'].'</td>';
			$row[] = '<td>'.$aRow['DonorEmail'].'</td>';
			$row[] = '<td>'.$aRow['ParticipantFName'].' '.$aRow['ParticipantLName'].'</td>';
			$row[] = '<td>'.$aRow['ParticipantEmail'].'</td>';
			$row[] = '<td>'.$aRow['CampaignId'].'</td>';
			$row[] = '<td>'.$aRow['CampaignFName'].' '.$aRow['CampaignLName'].'</td>';
			$row[] = '<td>'.$aRow['CampaignTitle'].'</td>';
			$row[] = '<td>'.$aRow['is_unsubscribe_date'].'</td>';
			$row[] = '<td></td>';
		} elseif ($module == 1) {
			$row[] = '<td>'.$aRow['ParticipantFName'].' '.$aRow['ParticipantLName'].'</td>';
			$row[] = '<td>'.$aRow['ParticipantEmail'].'</td>';
			$row[] = '<td>'.$aRow['CampaignId'].'</td>';
			$row[] = '<td>'.$aRow['CampaignFName'].' '.$aRow['CampaignLName'].'</td>';
			$row[] = '<td>'.$aRow['CampaignTitle'].'</td>';
			$row[] = '<td>'.$aRow['is_unsubscribe_date'].'</td>';
			$row[] = '<td></td>';
		} else {
			$row[] = '<td>'.$aRow['DonorFName'].' '.$aRow['DonorLName'].'</td>';
			$row[] = '<td>'.$aRow['DonorEmail'].'</td>';
			$row[] = '<td>'.$aRow['ParticipantFName'].' '.$aRow['ParticipantLName'].'</td>';
			$row[] = '<td>'.$aRow['ParticipantEmail'].'</td>';
			$row[] = '<td>'.$aRow['CampaignId'].'</td>';
			$row[] = '<td>'.$aRow['CampaignFName'].' '.$aRow['CampaignLName'].'</td>';
			$row[] = '<td>'.$aRow['CampaignTitle'].'</td>';
			$row[] = '<td>'.$aRow['is_unsubscribe_date'].'</td>';
			$row[] = '<td></td>';
		}
		$output['aaData'][] = $row; 
	}
	
	echo json_encode( $output );
?>