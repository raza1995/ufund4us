<?php 
include('dbconn.php');
$cid=$_REQUEST['cid'];
if(isset($_POST['file'])){
    $file = '../uploads/image/' . $_POST['file'];
    if(file_exists($file)){
        unlink($file);
    }
	$query="UPDATE tbl_campaign SET fld_campaign_image='' where fld_campaign_id='".$cid."'";
	mysqli_query( $con, $query );
}
?>