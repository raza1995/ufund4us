<?php
	include_once ('php/dbconn.php');
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
	$aColumns = array( 'fld_role_id', 'fld_status', 'fld_name', 'fld_lname', 'fld_email', 'fld_phone', 'fld_name', 'fld_name', 'fld_uid');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "fld_uid";
	
	/* DB table to use */
	$sTable = "tbl_users";
	
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
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysqli_real_escape_string($con,  $_GET['iDisplayStart'] ).", ".
			mysqli_real_escape_string($con,  $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysqli_real_escape_string($con,  $_GET['sSortDir_'.$i] ) .", ";
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
	$sWhere = "WHERE fld_role_id = '4'";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = " WHERE fld_role_id = '4' AND (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($con,  $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" &&
			 isset($_GET['sSearch_'.$i]) && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($con, $_GET['sSearch_'.$i])."%' ";
		}
	}
	
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
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
		FROM   $sTable WHERE fld_role_id = '4'
	";
	$rResultTotal = mysqli_query( $gaSql['link'], $sQuery ) or die(mysqli_error($con));
	$aResultTotal = mysqli_fetch_array($rResultTotal, MYSQLI_NUM);;
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysqli_fetch_array($rResult, MYSQLI_ASSOC) )
	{
		if ($aRow['fld_status'] == 1) {
			$statusrow = '<a href="donors.php?id='.$aRow['fld_uid'].'&s=2&m=edit" onClick="return confirmStatus(2)" data-toggle="tooltip" data-placement="top" title="" data-original-title="Deactivate Donor"><span class="fa fa-fw fa-thumbs-o-down"></span></a>';
			$statusrow2 = '<i class="fa fa-fw fa-thumbs-o-up"></i>';
		} else {
			$statusrow = '<a href="donors.php?id='.$aRow['fld_uid'].'&s=1&m=edit" onClick="return confirmStatus(1)" data-toggle="tooltip" data-placement="top" title="" data-original-title="Activate Donor"><span class="fa fa-fw fa-thumbs-o-up"></span> </a>';
			$statusrow2 = '<i class="fa fa-fw fa-thumbs-o-down"></i>';
		}
		if ($aRow['fld_role_id'] == 1) {
			$role_type = 'Administrator';
		} elseif ($aRow['fld_role_id'] == 2) {
			$role_type = 'Campaign Manager';
		} elseif ($aRow['fld_role_id'] == 3) {
			$role_type = 'Distributor';
		} elseif ($aRow['fld_role_id'] == 4) {
			$role_type = 'Donors';
		} elseif ($aRow['fld_role_id'] == 5) {
			$role_type = 'Participants';
		} elseif ($aRow['fld_role_id'] == 6) {
			$role_type = 'Representative';
		}
		$row = array();
		/* General output */
		$row[] = '<td>'.$statusrow2.'</td>';
		$row[] = '<td><a href="edit_donors.php?uid='.$aRow['fld_uid'].'&m=edit">'.$aRow['fld_name'].' '.$aRow['fld_lname'].'</a></td>';
		$row[] = '<td>'.$aRow['fld_email'].'</td>';
		$row[] = '<td>'.$aRow['fld_phone'].'</td>';
		$row[] = '<td>'.$role_type.'</td>';
		$row[] = '<td>
					<a href="edit_donors.php?uid='.$aRow['fld_uid'].'&m=edit" data-toggle="tooltip" data-placement="top" style="margin-right:10px" title="" data-original-title="Edit Donor"><span class="glyphicon glyphicon-pencil"></span></a>
					'.$statusrow.'
					<a href="donors.php?id='.$aRow['fld_uid'].'&m=del" onClick="return confirmDelete()" style="margin-right:10px" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Donor"><span class="glyphicon glyphicon-remove-circle"></span></a>
					<a href="javascript:void(0);" class="information" value2="'.$aRow['fld_name'].' '.$aRow['fld_lname'].'" value="'.$aRow['fld_uid'].'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Information"><span class="fa fa-info-circle"></span></a>
				  </td>';
		$output['aaData'][] = $row; 
	}
	
	echo json_encode( $output );
?>