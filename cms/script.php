<?
date_default_timezone_set('America/Los_Angeles'); //TimeZone
ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
ini_set('memory_limit', '512M');
include("php/dbconn.php");

function makeThumbnails($updir, $newdir, $img)
{
    $thumbnail_width = 524;
    $thumbnail_height = 487;
    $thumb_beforeword = "524x487_";
    $arr_image_details = getimagesizeWithoutError("$updir" . "$img"); // pass id to thumb name
    $original_width = $arr_image_details[0];
    $original_height = $arr_image_details[1];
    if ($original_width > $original_height) {
        $new_width = $thumbnail_width;
        $new_height = intval($original_height * $new_width / $original_width);
    } else {
        $new_height = $thumbnail_height;
        $new_width = intval($original_width * $new_height / $original_height);
    }
    $dest_x = intval(($thumbnail_width - $new_width) / 2);
    $dest_y = intval(($thumbnail_height - $new_height) / 2);
    if ($arr_image_details[2] == 1) {
        $imgt = "ImageGIF";
        $imgcreatefrom = "ImageCreateFromGIF";
    }
    if ($arr_image_details[2] == 2) {
        $imgt = "ImageJPEG";
        $imgcreatefrom = "ImageCreateFromJPEG";
    }
    if ($arr_image_details[2] == 3) {
        $imgt = "ImagePNG";
        $imgcreatefrom = "ImageCreateFromPNG";
    }
	if ($arr_image_details[2] == 4) {
        $imgt = "ImageBMP";
        $imgcreatefrom = "ImageCreateFromBMP";
    }
    if ($imgt) {
        $old_image = $imgcreatefrom("$updir" . "$img");
        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
		$backgroundColor = imagecolorallocate($new_image, 255, 255, 255);
		imagefill($new_image, 0, 0, $backgroundColor);
        imagecopyresampled($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
        $imgt($new_image, "$newdir" . "$thumb_beforeword" . "$img");
		
		$your_original_image = $newdir . $thumb_beforeword . $img;
		$your_frame_image = $newdir . 'left_img.png';
		
		$image = $imgcreatefrom($your_original_image);
		$frame = imagecreatefrompng($your_frame_image);

		imagecopyresampled($image, $frame, 0, 0, 0, 0, 524, 487, 524, 487); 
		imagepng($image, "$newdir" . "$thumb_beforeword" . "$img");
    }
}


$QueryCampaigns="SELECT fld_image FROM tbl_users WHERE fld_image != ''";
$ResultCampaigns = mysqli_query($conn1, $QueryCampaigns) or die("ERROR: Cannot fetch the campaign records...!");

$ResultCampaignsRows = mysqli_num_rows($ResultCampaigns);
if ($ResultCampaignsRows > 0) {
	//echo "abc";
	//Step 1 >> When Campaign Found
	while ($Rows = mysqli_fetch_array($ResultCampaigns, MYSQLI_ASSOC)) {
		$fld_image = $Rows['fld_image']; //getting campaign id
		makeThumbnails('uploads/profilelogo/', 'uploads/profilelogo/', $fld_image);
	}
}
?>