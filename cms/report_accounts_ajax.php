<?php
	date_default_timezone_set('America/Los_Angeles'); //TimeZone
	include_once ('php/dbconn.php');
	require ('../lib/init.php');
	ini_set('memory_limit', '-1');
	\Stripe\Stripe::setApiKey(STRIPE_API_KEY); //Initialize Stripe Gateway
	
	$REQUEST = &$_REQUEST;
	if ($REQUEST['uid'] == '' || strlen($REQUEST['uid']) < 10) {
		exit();
	} 
	/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2010 - Allan Jardine
	 * License:   GPL v2 or BSD (3-point)
	 */
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	$aColumns = array( "c.fld_campaign_id",
						"c.fld_campaign_title",
						"DATE_FORMAT(c.fld_campaign_edate, '%m/%d/%Y') AS CampaignEndDate",
						"COUNT(d.id) AS NumberofDonations",
						"c.fld_ac",
						"c.fld_payment_method",
						"c.fld_organization_name",
						"c.fld_payable_to",
						"c.fld_cemail",
						"c.fld_campaign_edate");
	
	$aColumns2 = array( "c.fld_campaign_id",
						"c.fld_campaign_title",
						"DATE_FORMAT(c.fld_campaign_edate, '%m/%d/%Y') AS CampaignEndDate",
						"COUNT(d.id) AS NumberofDonations",
						"c.fld_ac",
						"c.fld_payment_method",
						"c.fld_organization_name",
						"c.fld_payable_to",
						"c.fld_cemail",
						"c.fld_campaign_edate");
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "c.fld_campaign_id";
	
	/* DB table to use */
	$sTable = "tbl_donations";
	
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
	
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $REQUEST['iDisplayStart'] ) && $REQUEST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysqli_real_escape_string($con,  $REQUEST['iDisplayStart'] ).", ".
			mysqli_real_escape_string($con,  $REQUEST['iDisplayLength'] );
	}
	
	$sOrder = "";
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
	
	
	$sWhere = "";
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	//$sWhere = "WHERE fld_role_id = '4'";
	if ( $REQUEST['sSearch'] != "" )
	{
		$sWhere = " WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($con,  $REQUEST['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( $REQUEST['bSearchable_'.$i] == "true" && $REQUEST['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($con, $REQUEST['sSearch_'.$i])."%' ";
		}
	}
	
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns2))."
		FROM   $sTable d
		INNER JOIN tbl_campaign c ON d.cid=c.fld_campaign_id
		$sWhere
		GROUP BY d.cid
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
		FROM $sTable d 
		INNER JOIN tbl_campaign c ON d.cid=c.fld_campaign_id 
		GROUP BY d.cid
	";
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
	
	$sno = 0;
	while ( $aRow = mysqli_fetch_array($rResult, MYSQLI_ASSOC) )
	{
		$sno++;
		$deleteallow = false;
		$NumberofDonations = $aRow['NumberofDonations'];
		$CampaignEndDate = $aRow['CampaignEndDate'];
		if ($CampaignEndDate == '') {
			$deleteallow = true;
		}
		$payment_method = 'Yes';
		$fld_organization_name = "";
		$fld_payable_to = "";
		$fld_cemail = "";
		$available_amount = 0.00;
		$pending_amount = 0.00;
		$fld_payment_method = $aRow['fld_payment_method'];
		if ($fld_payment_method == 1) {
			$payment_method = 'No';
			$fld_organization_name = $aRow['fld_organization_name'];
			$fld_payable_to = $aRow['fld_payable_to'];
			$fld_cemail = $aRow['fld_cemail'];
		}
		
		if ($aRow['fld_ac'] != '') {
			$accounts_query = \Stripe\Account::retrieve("$aRow[fld_ac]");
			$accounts = $accounts_query->__toArray(true);
			
			$balance = \Stripe\Balance::retrieve(array("stripe_account" => $accounts['id']));
			$balance_array = $balance->__toArray(true);
			if (isset($balance_array['available'][0]['amount'])) {
				$available_amount = $balance_array['available'][0]['amount'];
			} 
			if (isset($balance_array['available'][0]['amount'])) {
				$pending_amount = $balance_array['pending'][0]['amount'];
			}
		} 
		
		$row = array();
		/* General output */
		$row[] = '<td>'.$sno.'</td>';
		if (isset($accounts['tos_acceptance']['date']) && $accounts['tos_acceptance']['date'] != '') {
			$row[] = '<td>'.date('m/d/Y H:i:s',$accounts['tos_acceptance']['date']).'</td>';
		} else {
			$row[] = '<td>TOS not available</td>';
		}
		$row[] = '<td>'.$accounts['metadata']['Campaign_No'].'</td>';
		$row[] = '<td>'.$accounts['metadata']['Campaign_Name'].'</td>';
		$row[] = '<td>'.$CampaignEndDate.'</td>';
		$row[] = '<td>'.$NumberofDonations.'</td>';
		$row[] = '<td>Available: '.number_format($available_amount / 100, 2, '.', ',').'</td>';
		$row[] = '<td>Pending: '.number_format($pending_amount / 100, 2, '.', ',').'</td>';
		if (isset($accounts['legal_entity']['verification']['status']) && $accounts['legal_entity']['verification']['status'] == 'unverified') {
			$row[] = '<td>'.$accounts['legal_entity']['verification']['details'].'</td>';
		} else {
			$row[] = '<td>Verified</td>';
		}
		$row[] = '<td><a href="https://dashboard.stripe.com/applications/users/'.$accounts['id'].'" target="_blank">'.$accounts['id'].'</a></td>';
		$row[] = '<td>'.$payment_method.'</td>';
		$row[] = '<td>'.$fld_organization_name.'</td>';
		$row[] = '<td>'.$fld_payable_to.'</td>';
		$row[] = '<td>'.$fld_cemail.'</td>';
		if ($deleteallow == true) {
			$row[] = '<td>[ <a href="javascript:void(0);" value="'.$accounts['id'].'" class="deleteacct">Delete</a> ]</td>';
		} else {
			$row[] = '<td></td>';
		}
		$output['aaData'][] = $row; 
	}
	
	echo json_encode( $output );
?>