<?php 
include('dbconn.php');
$cid=$_REQUEST['cid'];
$filename = $_POST['file'];
if(isset($_POST['file'])){
    $file = '../uploads/videogallery/' . $_POST['file'];
    if(file_exists($file)){
        unlink($file);
    }
	$query="DELETE FROM tbl_video WHERE fld_campaign_id='".$cid."' AND fld_video='".$filename."'";
	mysqli_query( $con, $query );
}
?>