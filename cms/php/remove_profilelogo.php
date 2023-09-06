<?php 
include('dbconn.php');
$uid=$_REQUEST['uid'];
if(isset($_POST['file'])){
    $file = '../uploads/profilelogo/' . $_POST['file'];
    if(file_exists($file)){
        unlink($file);
    }
	$query="UPDATE tbl_users SET fld_image='' where fld_uid='".$uid."'";
	mysqli_query( $con, $query );
}
?>