<?php
	include_once ('php/dbconn.php');
	$REQUEST = &$_REQUEST;
	$rid = $REQUEST['rid'];
	$uid = $REQUEST['uid'];
	$module = $REQUEST['module'];
	
	$aColumns = array( 'ur.fld_uid', 'tr.uname', 'ur.fld_cname', 'ur.fld_email', 'ur.fld_phone', '(SELECT r.fld_role FROM tbl_role r WHERE r.fld_role_id = ur.fld_role_id)', 'tr.rid', 'tr.uid', 'tr.creationdate', 'ur.fld_name', 'ur.fld_lname', 'ur.fld_password');
	$sIndexColumn = "tr.uname";
	$sTable = "tbl_tree";
	$whereclause = "";
	if ($module == 1) {
		if ($uid > 1) {
			if ($rid == 1) {
				$whereclause = "WHERE tr.aid = '$uid'";
				$limitclause = "";
			} elseif ($rid == 3) {
				$whereclause = "WHERE tr.did = '$uid'";
				$limitclause = "";
			} elseif ($rid == 6) {
				$whereclause = "WHERE tr.rid = '$uid'";
				$limitclause = "";
			} elseif ($rid == 2) {
				$whereclause = "WHERE tr.aid = '0'";
				$limitclause = "LIMIT 0";
			}
		} else {
			$whereclause = "WHERE tr.aid";
		}
	} elseif ($module == 3) {
		if ($rid == 1) {
			$whereclause = "WHERE tr.aid = '$uid'";
			$limitclause = "";
		} elseif ($rid == 3) {
			$whereclause = "WHERE tr.did = '$uid'";
			$limitclause = "";
		} elseif ($rid == 6) {
			$whereclause = "WHERE tr.rid = '$uid'";
			$limitclause = "";
		} elseif ($rid == 2) {
			$whereclause = "WHERE tr.did = '0'";
			$limitclause = "LIMIT 0";
		}
	} elseif ($module == 6) {
		if ($rid == 1) {
			$whereclause = "WHERE tr.aid = '$uid'";
			$limitclause = "";
		} elseif ($rid == 3) {
			$whereclause = "WHERE tr.did = '$uid'";
			$limitclause = "";
		} elseif ($rid == 6) {
			$whereclause = "WHERE tr.rid = '$uid'";
			$limitclause = "";
		} elseif ($rid == 2) {
			$whereclause = "WHERE tr.rid = '0'";
			$limitclause = "LIMIT 0";
		} 
	} elseif ($module == 2) {
		if ($rid == 1) {
			$whereclause = "WHERE tr.aid = '$uid'";
			$limitclause = "";
		} elseif ($rid == 3) {
			$whereclause = "WHERE tr.did = '$uid'";
			$limitclause = "";
		} elseif ($rid == 6) {
			$whereclause = "WHERE tr.rid = '$uid'";
			$limitclause = "";
		} elseif ($rid == 2) {
			$whereclause = "WHERE tr.uid = '0'";
			$limitclause = "LIMIT 0";
		} 
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
	
	$sOrder  = "";
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

	$sWhere = $whereclause;
	if ( $REQUEST['sSearch'] != "" )
	{
		$sWhere = $whereclause." AND (";
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
		if ( isset($REQUEST['bSearchable_'.$i]) && $REQUEST['bSearchable_'.$i] == "true" && 
			isset($REQUEST['sSearch_'.$i]) && $REQUEST['sSearch_'.$i] != '' )
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
	
	
	$sQuery = "
				SELECT SQL_CALC_FOUND_ROWS tr.uname, tr.rid, tr.uid, tr.creationdate, ur.fld_name, ur.fld_lname, ur.fld_email, ur.fld_role_id, ur.fld_phone, ur.fld_cname, ur.fld_password
				FROM $sTable tr
				INNER JOIN tbl_users ur ON ur.fld_uid = tr.uid
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
		FROM $sTable tr 
		INNER JOIN tbl_users ur ON ur.fld_uid = tr.uid 
		$sWhere
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
	$sn = 0;
	while ( $aRow = mysqli_fetch_array($rResult, MYSQLI_ASSOC) )
	{
		$sn++;
		if ($aRow['fld_role_id'] != 1 && $aRow['fld_role_id'] != 3) {
			$com_name_query = "SELECT ur.fld_cname FROM $sTable tr INNER JOIN tbl_users ur ON ur.fld_uid = tr.did WHERE tr.uid = '".$aRow['uid']."' LIMIT 1";
			$com_name_result = mysqli_query( $gaSql['link'], $com_name_query ) or die(mysqli_error($con));
			$com_name_row = mysqli_fetch_array($com_name_result, MYSQLI_ASSOC);
			$cname = $com_name_row['fld_cname'];
		} else {
			$cname = $aRow['fld_cname'];
		}
		
		$row = array();
		/* General output */
		if ($aRow['fld_role_id'] == 1) {
			$role_name = 'Administrator';
		} elseif ($aRow['fld_role_id'] == 2) {
			$role_name = 'Campaign Manager';
		} elseif ($aRow['fld_role_id'] == 3) {
			$role_name = 'Distributor';
		} elseif ($aRow['fld_role_id'] == 4) {
			$role_name = 'Donors';
		} elseif ($aRow['fld_role_id'] == 5) {
			$role_name = 'Participants';
		} elseif ($aRow['fld_role_id'] == 6) {
			$role_name = 'Representative';
		}
		if ($module == 1 || $module == 3) {
			if ($aRow['fld_role_id'] == 3 || $aRow['fld_role_id'] == 6) {
				$appliedclass = '<input type="submit" style="display:none;" /><button type="button" class="loggedin" value="Submit" style="background: none;border: 0px;padding: 0px;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="Login with this '.$role_name.'"><span class="fa fa-external-link"></span></a></button>';
			} else {
				$appliedclass = '<button type="submit" value="Submit" style="background: none;border: 0px;padding: 0px;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="Login with this '.$role_name.'"><span class="fa fa-external-link"></span></a></button>';
			}
			$participant_login = '<form action="../sign-in.php" method="POST" target="_blank">
						<input type="hidden" name="email" id="email" value="'.$aRow['fld_email'].'" />
						<input type="hidden" name="password" id="password" value="'.$aRow['fld_password'].'" />
						<input type="hidden" name="alllogin" id="alllogin" value="1" />
						<input type="hidden" name="_hidCheckSubmit" id="_hidCheckSubmit" value="1" />
						'.$appliedclass.'
					</form>';
		} else {
			$participant_login = '';
		}
		$row[] = '<td>'.$sn.'.</td>';
		$row[] = '<td><a href="?nodeid='.base64_encode($aRow['uid'])."&rid=".base64_encode($aRow['fld_role_id']).'">'.$aRow['fld_name'].' '.$aRow['fld_lname'].'</a></td>';
		$row[] = '<td>'.$cname.'</td>';
		$row[] = '<td>'.$aRow['fld_email'].'</td>';
		$row[] = '<td>'.$aRow['fld_phone'].'</td>';
		$row[] = '<td>'.$role_name.'</td>';
		$row[] = '<td>
					<a href="?id='.$aRow['uid'].'&m=del" style="margin-right:10px;float:left;" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete" onClick="return confirmDelete()"><span class="glyphicon glyphicon-remove-circle"></span></a>
					<a href="javascript:void(0);" class="information" value2="'.$aRow['fld_name'].' '.$aRow['fld_lname'].'" value="'.$aRow['uid'].'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Information"><span class="fa fa-info-circle"></span></a>
					'.$participant_login.'
				  </td>';	
			
		$output['aaData'][] = $row; 
	}
	
	echo json_encode( $output );
?>