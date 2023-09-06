<?php 
include('dbconn.php');
$cid=$_REQUEST['cid'];
if(isset($_POST['file'])){
    $file = '../uploads/logo/' . $_POST['file'];
    if(file_exists($file)){
        unlink($file);
    }
	$query="UPDATE tbl_campaign SET fld_campaign_logo='' where fld_campaign_id='".$cid."'";
	mysqli_query( $con, $query );
}
?>