<?php
//require_once("../configuration/dbconfig.php");
if ($_POST) {
	echo "posting";
	//$name = $_FILES["videofiles"] ["name"];
	//$type = $_FILES["videofiles"] ["type"];
	//$size = $_FILES["videofiles"] ["size"];
	//echo $temp = $_FILES["videofiles"] ["tmp_name"];
	//$error = $_FILES["videofiles"] ["error"];
	//var_dump($_FILES['videos']);
	//echo $temp = $_FILES["videos"] ["tmp_name"];
	//echo $name = $_FILES["videos"] ["name"];
	//var_dump($_POST);
	print_r($_FILES);
	print_r($_POST);
}
/*$uploads_dir = 'uploads/';
$cid = $_POST['cid'];
$filename = $_POST['filename'];
//$filename = "c:/fakepath/1.mp4";
$name = basename($_FILES['filename']);
//print_r($_POST);
//echo $name = $_FILES["filename"]["name"];
if(move_uploaded_file($_FILES["filename"], "$uploads_dir/$name")) {
	echo "success";
} else {
	echo "fail";
}*/
?>