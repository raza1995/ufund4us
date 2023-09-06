<?php 
include('dbconn.php');
$uid=$_REQUEST['uid'];
if(isset($_POST['file'])){
    $file = '../uploads/brandlogo/' . $_POST['file'];
    if(file_exists($file)){
        unlink($file);
    }
	$query="UPDATE tbl_users SET fld_brand_logo_header='' where fld_uid='".$uid."'";
	mysqli_query( $con, $query );
}
?>