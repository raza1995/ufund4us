<?php
/*
*	!!! THIS IS JUST AN EXAMPLE !!!, PLEASE USE ImageMagick or some other quality image processing libraries
*/
	require('php/dbconn.php');
	include('php/class.thumbnail.php');
	$cid = $_GET['cid'];
	$filename = $_GET['filename'];
    $imagePath = "uploads/imagegallery/";

	$allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
	$temp = explode(".", $_FILES["img"]["name"]);
	$extension = end($temp);
	
	//Check write Access to Directory

	if(!is_writable($imagePath)){
		$response = Array(
			"status" => 'error',
			"message" => 'Can`t upload File; no write Access'
		);
		print json_encode($response);
		return;
	}
	
	if ( in_array($extension, $allowedExts))
	  {
	  if ($_FILES["img"]["error"] > 0)
		{
			 $response = array(
				"status" => 'error',
				"message" => 'ERROR Return Code: '. $_FILES["img"]["error"],
			);			
		}
	  else
		{
			
	      $filename = $_FILES["img"]["tmp_name"];
		  list($width, $height) = getimagesizeWithoutError( $filename );

		  move_uploaded_file($filename,  $imagePath . $_FILES["img"]["name"]);

		  $response = array(
			"status" => 'success',
			"url" => $imagePath.$_FILES["img"]["name"],
			"width" => $width,
			"height" => $height
		  );
		  
		}
	  }
	else
	  {
	   $response = array(
			"status" => 'error',
			"message" => 'something went wrong, most likely file is to large for upload. check upload_max_filesize, post_max_size and memory_limit in you php.ini',
		);
	  }
	  $query="UPDATE tbl_gallery SET fld_image_name='".$_FILES["img"]["name"]."', fld_image='".$_FILES["img"]["name"]."' WHERE fld_campaign_id='".$cid."' AND fld_image_name='".$filename."'";
	  mysqli_query( $con, $query );
	  $resized = makeThumbnails('uploads/imagegallery/', 'uploads/imagegallery/', $filesnamen);
	  print json_encode($response);

?>
