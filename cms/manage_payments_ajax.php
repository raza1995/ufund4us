<?php
	include_once ('php/dbconn.php');
	$REQUEST = &$_REQUEST;	
	//Declare variable required bellow
	$rid = isset($REQUEST['rid']) ? $REQUEST['rid'] : 0;
	$uid = isset($REQUEST['uid']) ? $REQUEST['uid'] : 0;
	$REQUEST['module'] = $module = isset($REQUEST['module']) ? $REQUEST['module'] : "";
	$REQUEST['sSearch'] = isset($REQUEST['sSearch']) ? $REQUEST['sSearch'] : "";
	$REQUEST['sEcho'] = isset($REQUEST['sEcho']) ? $REQUEST['sEcho'] : "";
	$WhereClause = "";
	if ($module == 'disputed') {
		$aColumns = array( 'a.tid', 'a.disputeid', 'a.cid', 'c.fld_campaign_title', 'COALESCE(a.cmfname," ", a.cmlname)', 'DATE_FORMAT(a.creationdate, "%m/%d/%Y %h:%i:%s")',
			'a.uid', 'a.ufname', 'a.ulname', 'a.uemail', 'a.donation_amount', 'a.card_number', 'a.payment_through', 'DATE_FORMAT(c.fld_campaign_sdate, "%m/%d/%Y")', 'DATE_FORMAT(c.fld_campaign_edate, "%m/%d/%Y")',
			'a.refferal_by', 'b.fld_name', 'b.fld_lname', 'b.fld_email', 'c.fld_bank_accno');
		$aColumns1 = array( 'a.tid', 'a.cid', 'c.fld_campaign_title', 'COALESCE(a.cmfname," ", a.cmlname)', 'a.uid', 'a.ufname', 'a.uemail', 'a.donation_amount', 'a.card_number', 'a.payment_through', 'a.tid', 'a.disputeid', 'a.id', 'b.fld_name', 'b.fld_email');
		$sIndexColumn = "a.tid";
		$sTable = "tbl_donations_dispute";
	} 
	elseif ($module == 'refunded') {
		$aColumns = array( 'a.tid', 'a.refundid', 'a.cid', 'c.fld_campaign_title', 'COALESCE(a.cmfname," ", a.cmlname)', 'DATE_FORMAT(a.creationdate, "%m/%d/%Y %h:%i:%s")',
			'a.uid', 'a.ufname', 'a.ulname', 'a.uemail', 'a.donation_amount', 'a.card_number', 'a.payment_through', 'DATE_FORMAT(c.fld_campaign_sdate, "%m/%d/%Y")', 'DATE_FORMAT(c.fld_campaign_edate, "%m/%d/%Y")',
			'a.refferal_by', 'b.fld_name', 'b.fld_lname', 'b.fld_email', 'c.fld_bank_accno');
		$aColumns1 = array( 'a.tid', 'a.cid', 'c.fld_campaign_title', 'COALESCE(a.cmfname," ", a.cmlname)', 'a.did', 'a.ufname', 'a.uemail', 'a.donation_amount', 'a.card_number', 'a.payment_through', 'a.tid', 'a.refundid', 'a.id', 'b.fld_name', 'b.fld_email');
		$sIndexColumn = "a.tid";
		$sTable = "tbl_donations_refund";
	} 
	elseif ($module == 'paid') {
		$aColumns = array( 'a.tid', 'a.cid', 'c.fld_campaign_title', 'COALESCE(a.cmfname," ",a.cmlname)', 'DATE_FORMAT(a.creationdate, "%m/%d/%Y %h:%i:%s")',
			'a.uid', 'a.ufname', 'a.ulname', 'a.uemail', 'a.donation_amount', 'a.card_number', 'a.payment_through', 'DATE_FORMAT(c.fld_campaign_sdate, "%m/%d/%Y")', 'DATE_FORMAT(c.fld_campaign_edate, "%m/%d/%Y")',
			'a.refferal_by', 'b.fld_name', 'b.fld_lname', 'b.fld_email', 'a.mode', 'c.fld_bank_accno');
		$aColumns1 = array( 'a.mode', 'a.cid', 'c.fld_campaign_title', 'COALESCE(a.cmfname," ", a.cmlname)', 'a.did', 'a.ufname', 'a.uemail', 'a.donation_amount', 'a.card_number', 'a.payment_through', 'a.tid', 'a.id', 'b.fld_name', 'b.fld_email', '');
		$sIndexColumn = "a.creationdate";
		$sTable = "tbl_donations";
		$WhereClause = 'WHERE a.mode = 1';
	} 
	else {
		$aColumns = array( 'a.tid', 'a.cid', 'c.fld_campaign_title', 'COALESCE(a.cmfname," ",a.cmlname)', 'DATE_FORMAT(a.creationdate, "%m/%d/%Y %h:%i:%s")',
			'a.uid', 'a.ufname', 'a.ulname', 'a.uemail', 'a.donation_amount', 'a.card_number', 'a.payment_through', 'DATE_FORMAT(c.fld_campaign_sdate, "%m/%d/%Y")', 'DATE_FORMAT(c.fld_campaign_edate, "%m/%d/%Y")',
			'a.refferal_by', 'b.fld_name', 'b.fld_lname', 'b.fld_email', 'a.mode', 'c.fld_bank_accno');
		$aColumns1 = array( 'a.mode', 'a.cid', 'c.fld_campaign_title', 'COALESCE(a.cmfname," ", a.cmlname)', 'a.did', 'a.ufname', 'a.uemail', 'a.donation_amount', 'a.card_number', 'a.payment_through', 'a.tid', 'a.id', 'b.fld_name', 'b.fld_email', '');
		$sIndexColumn = "a.creationdate";
		$sTable = "tbl_donations";
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
	
	$sOrder	= "";
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
	if ($WhereClause != '') {
		$sWhere = $WhereClause;
	}

	if(count($aColumns) > 0){
		if ( $REQUEST['sSearch'] != "")
		{
			
				
			$sWhereOrAry = [];
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$sWhereOrAry[] = $aColumns[$i]." LIKE '%".mysqli_real_escape_string($con,  $REQUEST['sSearch'] )."%' ";
			}
				// $sWhere = substr_replace( $sWhere, "", -3 );
			

			if(count($sWhereOrAry)>0){
				if ( $sWhere == "" ){
					$sWhere = "WHERE (";
				}
				else{
					$sWhere = $sWhere." AND (";
				}
				$sWhere .= implode(" OR ", $sWhereOrAry);
				$sWhere .= ')';
			}
		}

		$sWhere2AndAry = [];
		/* Individual column filtering */
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( isset($REQUEST['bSearchable_'.$i]) && $REQUEST['bSearchable_'.$i] == "true" &&
				 isset($REQUEST['sSearch_'.$i]) && $REQUEST['sSearch_'.$i] != '' 
			)
			{
				
				$sWhere2AndAry[] = $aColumns[$i]." LIKE '%".mysqli_real_escape_string($con, $REQUEST['sSearch_'.$i])."%' ";
			}
		}

		if(count($sWhere2AndAry)>0){
			if ( $sWhere == "" ){
				$sWhere = "WHERE (";
			}
			else{
				$sWhere = $sWhere." AND (";
			}
			$sWhere .= implode(" AND ", $sWhere2AndAry);
			$sWhere .= ')';
		}
	}
	
	
	if ($module == 'disputed') {
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS a.tid, a.disputeid, a.cid, c.fld_campaign_title AS ctitle, COALESCE(a.cmfname, ' ', a.cmlname) AS cmname, DATE_FORMAT(a.creationdate, '%m/%d/%Y %h:%i:%s') AS tdate,
			a.uid AS did, a.ufname, a.ulname, a.uemail, a.donation_amount, a.card_number, a.payment_through, DATE_FORMAT(c.fld_campaign_sdate, '%m/%d/%Y') AS sdate, DATE_FORMAT(c.fld_campaign_edate, '%m/%d/%Y') AS edate,
			a.refferal_by AS pid, b.fld_name AS pfname, b.fld_lname AS plname, b.fld_email AS pemail, c.fld_bank_accno AS cac
			FROM $sTable a
			LEFT JOIN tbl_users b ON a.refferal_by = b.fld_uid
			LEFT JOIN tbl_campaign c ON a.cid = c.fld_campaign_id
			$sWhere
			$sOrder
			$sLimit
		";
	} 
	elseif ($module == 'refunded') {
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS a.tid, a.refundid, a.cid, c.fld_campaign_title AS ctitle, COALESCE(a.cmfname, ' ', a.cmlname) AS cmname, DATE_FORMAT(a.creationdate, '%m/%d/%Y %h:%i:%s') AS tdate,
			a.uid AS did, a.ufname, a.ulname, a.uemail, a.donation_amount, a.card_number, a.payment_through, DATE_FORMAT(c.fld_campaign_sdate, '%m/%d/%Y') AS sdate, DATE_FORMAT(c.fld_campaign_edate, '%m/%d/%Y') AS edate,
			a.refferal_by AS pid, b.fld_name AS pfname, b.fld_lname AS plname, b.fld_email AS pemail, c.fld_bank_accno AS cac, a.check_payment_id
			FROM $sTable a
			LEFT JOIN tbl_users b ON a.refferal_by = b.fld_uid
			LEFT JOIN tbl_campaign c ON a.cid = c.fld_campaign_id
			$sWhere
			$sOrder
			$sLimit
		";
	} 
	elseif ($module == 'paid') {
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS tid, a.cid, c.fld_campaign_title AS ctitle, COALESCE(a.cmfname, ' ', a.cmlname) AS cmname, DATE_FORMAT(a.creationdate, '%m/%d/%Y %h:%i:%s') AS tdate,
			a.uid AS did, a.ufname, a.ulname, a.uemail, a.donation_amount, a.card_number, a.payment_through, DATE_FORMAT(c.fld_campaign_sdate, '%m/%d/%Y') AS sdate, DATE_FORMAT(c.fld_campaign_edate, '%m/%d/%Y') AS edate,
			a.refferal_by AS pid, b.fld_name AS pfname, b.fld_lname AS plname, b.fld_email AS pemail, a.mode, c.fld_bank_accno AS cac, a.check_payment_id
			FROM $sTable a
			LEFT JOIN tbl_users b ON a.refferal_by = b.fld_uid
			LEFT JOIN tbl_campaign c ON a.cid = c.fld_campaign_id
			$sWhere
			$sOrder
			$sLimit
		";
	} 
	else {
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS tid, a.cid, c.fld_campaign_title AS ctitle, COALESCE(a.cmfname, ' ', a.cmlname) AS cmname, DATE_FORMAT(a.creationdate, '%m/%d/%Y %h:%i:%s') AS tdate,
			a.uid AS did, a.ufname, a.ulname, a.uemail, a.donation_amount, a.card_number, a.payment_through, DATE_FORMAT(c.fld_campaign_sdate, '%m/%d/%Y') AS sdate, DATE_FORMAT(c.fld_campaign_edate, '%m/%d/%Y') AS edate,
			a.refferal_by AS pid, b.fld_name AS pfname, b.fld_lname AS plname, b.fld_email AS pemail, a.mode, c.fld_bank_accno AS cac, a.check_payment_id
			FROM $sTable a
			LEFT JOIN tbl_users b ON a.refferal_by = b.fld_uid
			LEFT JOIN tbl_campaign c ON a.cid = c.fld_campaign_id
			$sWhere
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
	if ($module == 'disputed') {
		$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM $sTable a
		LEFT JOIN tbl_users b ON a.refferal_by = b.fld_uid
		LEFT JOIN tbl_campaign c ON a.cid = c.fld_campaign_id
		";
	} elseif ($module == 'refunded') {
		$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM $sTable a
		LEFT JOIN tbl_users b ON a.refferal_by = b.fld_uid
		LEFT JOIN tbl_campaign c ON a.cid = c.fld_campaign_id
		";
	} elseif ($module == 'paid') {
		$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM $sTable a
		LEFT JOIN tbl_users b ON a.refferal_by = b.fld_uid
		LEFT JOIN tbl_campaign c ON a.cid = c.fld_campaign_id
		$WhereClause
		";
	} else {
		$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM $sTable a
		LEFT JOIN tbl_users b ON a.refferal_by = b.fld_uid
		LEFT JOIN tbl_campaign c ON a.cid = c.fld_campaign_id
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
		$refund_button = '';
		$dispute_button = '';

		$row = array();
		/* General output */
		if ($module == 'disputed') {
			$row[] = '<td>Disputed</td>';
			$row[] = '<td>'.$aRow['cid'].'</td>';
			$row[] = '<td>'.$aRow['ctitle'].'</td>';
			$row[] = '<td>'.$aRow['cmname'].'</td>';
			$row[] = '<td>'.$aRow['did'].'</td>';
			$row[] = '<td>'.$aRow['ufname'].' '.$aRow['ulname'].'</td>';
			$row[] = '<td>'.$aRow['uemail'].'</td>';
			$row[] = '<td>'.$aRow['donation_amount'].'</td>';
			$row[] = '<td>'.$aRow['card_number'].'</td>';
			$row[] = '<td>'.$aRow['payment_through'].'</td>';
			$row[] = '<td>'.$aRow['tid'].'</td>';
			$row[] = '<td>'.$aRow['disputeid'].'</td>';
			$row[] = '<td>'.$aRow['tdate'].'</td>';
			$row[] = '<td>'.$aRow['pfname'].' '.$aRow['plname'].'</td>';
			$row[] = '<td>'.$aRow['pemail'].'</td>';
		} 
		elseif ($module == 'refunded') {
			$row[] = '<td>Refunded</td>';
			$row[] = '<td>'.$aRow['cid'].'</td>';
			$row[] = '<td>'.$aRow['ctitle'].'</td>';
			$row[] = '<td>'.$aRow['cmname'].'</td>';
			$row[] = '<td>'.$aRow['did'].'</td>';
			$row[] = '<td>'.$aRow['ufname'].' '.$aRow['ulname'].'</td>';
			$row[] = '<td>'.$aRow['uemail'].'</td>';
			$row[] = '<td>'.$aRow['donation_amount'].'</td>';
			$row[] = '<td>'.$aRow['card_number'].'</td>';
			$row[] = '<td>'.$aRow['payment_through'].'</td>';
			$row[] = '<td>'.$aRow['tid'].'</td>';
			$row[] = '<td>'.$aRow['refundid'].'</td>';
			$row[] = '<td>'.$aRow['tdate'].'</td>';
			$row[] = '<td>'.$aRow['pfname'].' '.$aRow['plname'].'</td>';
			$row[] = '<td>'.$aRow['pemail'].'</td>';
		} 
		elseif ($module == 'paid') {
			if ($aRow['mode'] == 3) {
				$mode = 'disabled';
				$status = 'Disputed';
			} elseif ($aRow['mode'] == 2) {
				$mode = 'disabled';
				$status = 'Refunded';
			} elseif ($aRow['mode'] == 1) {
				$mode = '';
				$status = 'Paid';
			} else {
				$mode = '';
				$status = 'Rejected';
			}
			
			// echo "<pre>"; print_r($aRow); die();
			if ($aRow['tid'] == 'check' && isset($aRow['check_payment_id']) && $aRow['check_payment_id'] > 0) {
				$refund_button = '<button class="btn btn-block btn-primary check_payment_refundclick" '.$mode.' cid="'.$aRow['cid'].'" data-check_payment_id="'.$aRow['check_payment_id'].'" demail="'.$aRow['uemail'].'" dname="'.$aRow['ufname'].' '.$aRow['ulname'].'" pname="'.$aRow['pfname'].' '.$aRow['plname'].'" amountreq="'.$aRow['donation_amount'].'" tid="ch_'.$aRow['tid'].'" cmname="'.$aRow['cmname'].'" ctitle="'.$aRow['ctitle'].'" cno="'.$aRow['cid'].'" cac="'.$aRow['cac'].'" pid="'.$aRow['pid'].'" did="'.$aRow['did'].'" sdate="'.$aRow['sdate'].'" edate="'.$aRow['edate'].'" style=" margin-top:0px;"><span class="fa fa-money"></span> <span class="newtext">Refund Check Payment</span></button>';
				
			}
			else if (strlen($aRow['tid']) >= 20) {
				$tid = 'ch_'.$aRow['tid'];
				$refund_button = '<button class="btn btn-block btn-primary refundclick" '.$mode.' cid="'.$aRow['cid'].'" demail="'.$aRow['uemail'].'" dname="'.$aRow['ufname'].' '.$aRow['ulname'].'" pname="'.$aRow['pfname'].' '.$aRow['plname'].'" amountreq="'.$aRow['donation_amount'].'" tid="ch_'.$aRow['tid'].'" cmname="'.$aRow['cmname'].'" ctitle="'.$aRow['ctitle'].'" cno="'.$aRow['cid'].'" cac="'.$aRow['cac'].'" pid="'.$aRow['pid'].'" did="'.$aRow['did'].'" sdate="'.$aRow['sdate'].'" edate="'.$aRow['edate'].'" style="width:100px; margin-top:0px;"><span class="fa fa-money"></span> <span class="newtext">Refund</span></button>';
				$dispute_button = '<button class="btn btn-block btn-primary refundclick" '.$mode.' cid="'.$aRow['cid'].'" demail="'.$aRow['uemail'].'" dname="'.$aRow['ufname'].' '.$aRow['ulname'].'" pname="'.$aRow['pfname'].' '.$aRow['plname'].'" amountreq="'.$aRow['donation_amount'].'" tid="ch_'.$aRow['tid'].'" cmname="'.$aRow['cmname'].'" ctitle="'.$aRow['ctitle'].'" cno="'.$aRow['cid'].'" cac="'.$aRow['cac'].'" pid="'.$aRow['pid'].'" did="'.$aRow['did'].'" sdate="'.$aRow['sdate'].'" edate="'.$aRow['edate'].'" style="width:100px; margin-top:0px;"><span class="fa fa-money"></span> <span class="newtext">Dispute</span></button>';
			} else {
				$tid = $aRow['tid'];
			}
			$row[] = '<td>'.$status.'</td>';
			$row[] = '<td>'.$aRow['cid'].'</td>';
			$row[] = '<td>'.$aRow['ctitle'].'</td>';
			$row[] = '<td>'.$aRow['cmname'].'</td>';
			$row[] = '<td>'.$aRow['did'].'</td>';
			$row[] = '<td>'.$aRow['ufname'].' '.$aRow['ulname'].'</td>';
			$row[] = '<td>'.$aRow['uemail'].'</td>';
			$row[] = '<td>'.$aRow['donation_amount'].'</td>';
			$row[] = '<td>'.$aRow['card_number'].'</td>';
			$row[] = '<td>'.$aRow['payment_through'].'</td>';
			$row[] = '<td>'.$aRow['tid'].'</td>';
			$row[] = '<td>'.$aRow['tdate'].'</td>';
			$row[] = '<td>'.$aRow['pfname'].' '.$aRow['plname'].'</td>';
			$row[] = '<td>'.$aRow['pemail'].'</td>';
			$row[] = '<td><div style="float:left; margin-right: 5px">'.$refund_button.'</div><div style="float:left">'.$dispute_button.'</div></td>';
		} 
		else {
			if ($aRow['mode'] == 3) {
				$mode = 'disabled';
				$status = 'Disputed';
			} elseif ($aRow['mode'] == 2) {
				$mode = 'disabled';
				$status = 'Refunded';
			} elseif ($aRow['mode'] == 1) {
				$mode = '';
				$status = 'Paid';
			} else {
				$mode = '';
				$status = 'Rejected';
			}

			// echo "<pre>"; print_r($aRow); die();
			if ($aRow['tid'] == 'check' && isset($aRow['check_payment_id']) && $aRow['check_payment_id'] > 0) {
				$refund_button = '<button class="btn btn-block btn-primary check_payment_refundclick" '.$mode.' cid="'.$aRow['cid'].'" data-check_payment_id="'.$aRow['check_payment_id'].'" demail="'.$aRow['uemail'].'" dname="'.$aRow['ufname'].' '.$aRow['ulname'].'" pname="'.$aRow['pfname'].' '.$aRow['plname'].'" amountreq="'.$aRow['donation_amount'].'" tid="ch_'.$aRow['tid'].'" cmname="'.$aRow['cmname'].'" ctitle="'.$aRow['ctitle'].'" cno="'.$aRow['cid'].'" cac="'.$aRow['cac'].'" pid="'.$aRow['pid'].'" did="'.$aRow['did'].'" sdate="'.$aRow['sdate'].'" edate="'.$aRow['edate'].'" style=" margin-top:0px;"><span class="fa fa-money"></span> <span class="newtext">Refund Check Payment</span></button>';
				
			}
			else if (strlen($aRow['tid']) >= 20) {
				$tid = 'ch_'.$aRow['tid'];
				$refund_button = '<button class="btn btn-block btn-primary refundclick" '.$mode.' cid="'.$aRow['cid'].'" demail="'.$aRow['uemail'].'" dname="'.$aRow['ufname'].' '.$aRow['ulname'].'" pname="'.$aRow['pfname'].' '.$aRow['plname'].'" amountreq="'.$aRow['donation_amount'].'" tid="ch_'.$aRow['tid'].'" cmname="'.$aRow['cmname'].'" ctitle="'.$aRow['ctitle'].'" cno="'.$aRow['cid'].'" cac="'.$aRow['cac'].'" pid="'.$aRow['pid'].'" did="'.$aRow['did'].'" sdate="'.$aRow['sdate'].'" edate="'.$aRow['edate'].'" rdmode="refund" style="width:100px; margin-top:0px;"><span class="fa fa-money"></span> <span class="newtext">Refund</span></button>';
				$dispute_button = '<button class="btn btn-block btn-primary refundclick" '.$mode.' cid="'.$aRow['cid'].'" demail="'.$aRow['uemail'].'" dname="'.$aRow['ufname'].' '.$aRow['ulname'].'" pname="'.$aRow['pfname'].' '.$aRow['plname'].'" amountreq="'.$aRow['donation_amount'].'" tid="ch_'.$aRow['tid'].'" cmname="'.$aRow['cmname'].'" ctitle="'.$aRow['ctitle'].'" cno="'.$aRow['cid'].'" cac="'.$aRow['cac'].'" pid="'.$aRow['pid'].'" did="'.$aRow['did'].'" sdate="'.$aRow['sdate'].'" edate="'.$aRow['edate'].'" rdmode="dispute" style="width:100px; margin-top:0px;"><span class="fa fa-money"></span> <span class="newtext">Dispute</span></button>';
			} else {
				$tid = $aRow['tid'];
				$refund_button = '';
				$dispute_button = '';
			}
			$row[] = '<td>'.$status.'</td>';
			$row[] = '<td>'.str_pad($aRow['cid'], 7, "0", STR_PAD_LEFT).'</td>';
			$row[] = '<td>'.$aRow['ctitle'].'</td>';
			$row[] = '<td>'.$aRow['cmname'].'</td>';
			$row[] = '<td>'.$aRow['did'].'</td>';
			$row[] = '<td>'.$aRow['ufname'].' '.$aRow['ulname'].'</td>';
			$row[] = '<td>'.$aRow['uemail'].'</td>';
			$row[] = '<td>'.$aRow['donation_amount'].'</td>';
			$row[] = '<td>'.$aRow['card_number'].'</td>';
			$row[] = '<td>'.$aRow['payment_through'].'</td>';
			$row[] = '<td>'.$aRow['tid'].'</td>';
			$row[] = '<td>'.$aRow['tdate'].'</td>';
			$row[] = '<td>'.$aRow['pfname'].' '.$aRow['plname'].'</td>';
			$row[] = '<td>'.$aRow['pemail'].'</td>';
			$row[] = '<td><div style="float:left; margin-right: 5px">'.$refund_button.'</div><div style="float:left">'.$dispute_button.'</div></td>';
		}
		$output['aaData'][] = $row; 
	}
	
	echo json_encode( $output );
?>