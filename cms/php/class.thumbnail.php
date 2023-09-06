<?php
date_default_timezone_set('America/Los_Angeles'); //TimeZone

function makeThumbnails($updir, $newdir, $img)
{
    $thumbnail_width = 173;
    $thumbnail_height = 66;
    $thumb_beforeword = "thumb_";
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
        imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
        $imgt($new_image, "$newdir" . "$thumb_beforeword" . "$img");
    }
}

function compress($source, $destination, $quality) {

    $info = getimagesizeWithoutError($source);

    if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg') {
        $image = imagecreatefromjpeg($source);
		imagejpeg($image, $destination, $quality);
    } elseif ($info['mime'] == 'image/gif') {
        $image = imagecreatefromgif($source);
		//imagegif($image, $destination, $quality);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
		if ($quality > 0 && $quality < 10) { // 0
			$quality1 = 0;
		} elseif ($quality >= 10 && $quality < 20) { // 1
			$quality1 = 1;
		} elseif ($quality >= 20 && $quality < 30) { // 2
			$quality1 = 2;
		} elseif ($quality >= 30 && $quality < 40) { // 3
			$quality1 = 3;
		} elseif ($quality >= 40 && $quality < 50) { // 4
			$quality1 = 4;
		} elseif ($quality >= 50 && $quality < 60) { // 5
			$quality1 = 5;
		} elseif ($quality >= 60 && $quality < 70) { // 6
			$quality1 = 6;
		} elseif ($quality >= 70 && $quality < 80) { // 7
			$quality1 = 7;
		} elseif ($quality >= 80 && $quality < 90) { // 8
			$quality1 = 8;
		} elseif ($quality >= 90) { // 9
			$quality1 = 9;
		}
		imagepng($image, $destination, $quality1);
	}
    return $destination;
}


?>