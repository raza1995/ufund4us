<?
require('php/dbconn.php');
include('php/class.thumbnail.php');
ini_set ('gd.jpeg_ignore_warning', 1);
error_reporting(E_ALL & ~E_NOTICE);
$act = $_REQUEST['act'];
$cid = $_REQUEST['cid'];
$uid = $_REQUEST['uid'];
$direction = $_REQUEST['rotate'];
$filename = basename($_REQUEST['file']);
if ($act == 1 && $cid != '' && $filename != '') {
	$file = 'uploads/logo/thumb_' . $filename;
    if(file_exists($file)){
        unlink($file);
    }
	// File and rotation
	$success = 0;
	$rotateFilename = 'uploads/logo/' . $filename; // PATH
	if ($direction == 'right') {
		$degrees = -90;
	} else {
		$degrees = 90;
	}
	$fileType = strtolower(substr($rotateFilename, strrpos($rotateFilename, '.') + 1));

	if($fileType == 'png' || $fileType == 'PNG'){
		header('Content-type: image/png');
		$source = imagecreatefrompng($rotateFilename);
		$bgColor = imagecolorallocatealpha($source, 255, 255, 255, 127);
		// Rotate
		$rotate = imagerotate($source, $degrees, $bgColor);
		imagesavealpha($rotate, true);
		$final = imagepng($rotate,$rotateFilename);
		if ($final) {
			$success = 1;
		} else {
			$success = 0;
		}
	}

	if($fileType == 'jpg' || $fileType == 'jpeg'){
		header('Content-type: image/jpeg');
		$source = imagecreatefromjpeg($rotateFilename);
		// Rotate
		$rotate = imagerotate($source, $degrees, 0);
		$final = imagejpeg($rotate,$rotateFilename);
		if ($final) {
			$success = 1;
		} else {
			$success = 0;
		}
	}
	// Free the memory
	imagedestroy($source);
	imagedestroy($rotate);
	
	$resized = makeThumbnails('uploads/logo/', 'uploads/logo/', $filename);
	
	$result['is_success'] = $success;
	echo json_encode($result);
	
} elseif ($act == 2 && $cid != '' && $filename != '') {
	
	$file = 'uploads/imagegallery/thumb_' . $filename;
    if(file_exists($file)){
        unlink($file);
    }
	// File and rotation
	$success = 0;
	$rotateFilename = 'uploads/imagegallery/' . $filename; // PATH
	if ($direction == 'right') {
		$degrees = -90;
	} else {
		$degrees = 90;
	}
	$fileType = strtolower(substr($rotateFilename, strrpos($rotateFilename, '.') + 1));

	if($fileType == 'png' || $fileType == 'PNG'){
		header('Content-type: image/png');
		$source = imagecreatefrompng($rotateFilename);
		$bgColor = imagecolorallocatealpha($source, 255, 255, 255, 127);
		// Rotate
		$rotate = imagerotate($source, $degrees, $bgColor);
		imagesavealpha($rotate, true);
		$final = imagepng($rotate,$rotateFilename);
		if ($final) {
			$success = 1;
		} else {
			$success = 0;
		}
	}

	if($fileType == 'jpg' || $fileType == 'jpeg'){
		header('Content-type: image/jpeg');
		$source = imagecreatefromjpeg($rotateFilename);
		// Rotate
		$rotate = imagerotate($source, $degrees, 0);
		$final = imagejpeg($rotate,$rotateFilename);
		if ($final) {
			$success = 1;
		} else {
			$success = 0;
		}
	}
	// Free the memory
	imagedestroy($source);
	imagedestroy($rotate);
	
	$resized = makeThumbnails('uploads/imagegallery/', 'uploads/imagegallery/', $filename);
	
	$result['is_success'] = $success;
	echo json_encode($result);

} elseif ($act == 3 && $uid != '' && $filename != '') {

	$file = 'uploads/profilelogo/thumb_' . $filename;
    if(file_exists($file)){
        unlink($file);
    }
	// File and rotation
	$success = 0;
	$rotateFilename = 'uploads/profilelogo/' . $filename; // PATH
	if ($direction == 'right') {
		$degrees = -90;
	} else {
		$degrees = 90;
	}
	$fileType = strtolower(substr($rotateFilename, strrpos($rotateFilename, '.') + 1));

	if($fileType == 'png' || $fileType == 'PNG'){
		header('Content-type: image/png');
		$source = imagecreatefrompng($rotateFilename);
		$bgColor = imagecolorallocatealpha($source, 255, 255, 255, 127);
		// Rotate
		$rotate = imagerotate($source, $degrees, $bgColor);
		imagesavealpha($rotate, true);
		$final = imagepng($rotate,$rotateFilename);
		if ($final) {
			$success = 1;
		} else {
			$success = 0;
		}
	}

	if($fileType == 'jpg' || $fileType == 'jpeg'){
		header('Content-type: image/jpeg');
		$source = imagecreatefromjpeg($rotateFilename);
		// Rotate
		$rotate = imagerotate($source, $degrees, 0);
		$final = imagejpeg($rotate,$rotateFilename);
		if ($final) {
			$success = 1;
		} else {
			$success = 0;
		}
	}
	// Free the memory
	imagedestroy($source);
	imagedestroy($rotate);
	
	$resized = makeThumbnails('uploads/profilelogo/', 'uploads/profilelogo/', $filename);
	
	$result['is_success'] = $success;
	echo json_encode($result);
	
} elseif ($act == 4 && $uid != '' && $filename != '') {

	$file = 'uploads/profilelogo/thumb_' . $filename;
    if(file_exists($file)){
        unlink($file);
    }
	// File and rotation
	$success = 0;
	$rotateFilename = 'uploads/profilelogo/' . $filename; // PATH
	if ($direction == 'right') {
		$degrees = -90;
	} else {
		$degrees = 90;
	}
	$fileType = strtolower(substr($rotateFilename, strrpos($rotateFilename, '.') + 1));

	if($fileType == 'png' || $fileType == 'PNG'){
		//header('Content-type: image/png');
		$source = imagecreatefrompng($rotateFilename);
		$bgColor = imagecolorallocatealpha($source, 255, 255, 255, 127);
		// Rotate
		$rotate = imagerotate($source, $degrees, $bgColor);
		imagesavealpha($rotate, true);
		$final = imagepng($rotate,$rotateFilename);
		if ($final) {
			$success = 1;
		} else {
			$success = 0;
		}
	}

	if($fileType == 'jpg' || $fileType == 'jpeg'){
		//header('Content-type: image/jpeg');
		$source = imagecreatefromjpeg($rotateFilename);
		// Rotate
		$rotate = imagerotate($source, $degrees, 0);
		$final = imagejpeg($rotate,$rotateFilename);
		if ($final) {
			$success = 1;
		} else {
			$success = 0;
		}
	}
	// Free the memory
	imagedestroy($source);
	imagedestroy($rotate);
	
	$resized = makeThumbnails('uploads/profilelogo/', 'uploads/profilelogo/', $filename);
	
	$result['is_success'] = $success;
	echo json_encode($result);
	
} elseif ($act == 5 && $cid != '') {
	
	$query="SELECT fld_campaign_logo FROM tbl_campaign WHERE fld_campaign_id = '".$cid."'";
	$exec_query = mysqli_query( $con, $query );
	$row = mysqli_fetch_array($exec_query, MYSQLI_ASSOC);
	
	$result['is_success'] = 1;
	$result['select_logo'] = $row['fld_campaign_logo'];
	echo json_encode($result);
	
}

?>