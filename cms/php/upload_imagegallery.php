<?php
    include('class.uploader.php');
	include('class.thumbnail.php');
    include('dbconn.php');
    $uploader = new Uploader();
	$cid=$_REQUEST['cid'];
    $data = $uploader->upload($_FILES['galleryfiles'], array(
        'limit' => 10, //Maximum Limit of files. {null, Number}
        'maxSize' => 10, //Maximum Size of files {null, Number(in MB's)}
        'extensions' => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
        'required' => false, //Minimum one file is required for upload {Boolean}
        'uploadDir' => '../uploads/imagegallery/', //Upload directory {String}
        'title' => array('auto'), //New file name {null, String, Array} *please read documentation in README.md
        'removeFiles' => true, //Enable file exclusion {Boolean(extra for jQuery.filer), String($_POST field name containing json data with file names)}
        'perms' => null, //Uploaded file permisions {null, Number}
        'onCheck' => null, //A callback function name to be called by checking a file for errors (must return an array) | ($file) | Callback
        'onError' => null, //A callback function name to be called if an error occured (must return an array) | ($errors, $file) | Callback
        'onSuccess' => null, //A callback function name to be called if all files were successfully uploaded | ($files, $metas) | Callback
        'onUpload' => null, //A callback function name to be called if all files were successfully uploaded (must return an array) | ($file) | Callback
        'onComplete' => null, //A callback function name to be called when upload is complete | ($file) | Callback
        'onRemove' => 'onFilesRemoveCallback' //A callback function name to be called by removing files (must return an array) | ($removed_files) | Callback
    ));
    
    if($data['isComplete']){
        $files = $data['data'];
		$query="INSERT INTO tbl_gallery (fld_campaign_id,fld_image_name,fld_image,fld_status) VALUES('".$cid."','".$files['metas'][0]['name']."','".$files['metas'][0]['name']."','1')";
		mysqli_query( $con, $query );
		$source_img = '../uploads/imagegallery/'.$files['metas'][0]['name'];
		$destination_img = '../uploads/imagegallery/'.$files['metas'][0]['name'];
		$d = compress($source_img, $destination_img, 60);
		$resized = makeThumbnails('../uploads/imagegallery/', '../uploads/imagegallery/', $files['metas'][0]['name']);
        //print_r($files);
		echo json_encode($files['metas'][0]['name']);
    }

    if($data['hasErrors']){
        $errors = $data['errors'];
        print_r($errors);
    }
    
    function onFilesRemoveCallback($removed_files){
        foreach($removed_files as $key=>$value){
            $file = '../uploads/imagegallery/' . $value;
            if(file_exists($file)){
                unlink($file);
            }
        }
        
        return $removed_files;
    }
?>