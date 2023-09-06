<?php
require_once("../configuration/dbconfig.php");
if ($_POST['uid'] != '') {
	$uid = $_POST['uid'];
} elseif ($_GET['uid'] != '') {
	$uid = $_GET['uid'];
}
$aUserDetail = $oregister->getuserdetail($uid);
$fld_campaign_logo = $aUserDetail['fld_image'];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets2/img/favicon.png">

    <title></title>

    <!-- Bootstrap core CSS -->
    <link href="assets2/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="assets2/css/main.css" rel="stylesheet">
    <link href="assets2/css/croppic.css" rel="stylesheet">

    <!-- Fonts from Google Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,900' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Mrs+Sheppards&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
	<div class="container">
		<div class="row mt ">
			<div class="col-lg-4 ">
				<h4 class="centered"> Profile Logo </h4>
				<div id="cropContainerPreload"></div>
			</div>		
		</div>		
	</div>
	<!-- <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script> -->
	<script src="assets2/js/main.js"></script>
	<script src=" https://code.jquery.com/jquery-2.1.3.min.js"></script>
	<script src="assets2/js/bootstrap.min.js"></script>
	<script src="assets2/js/jquery.mousewheel.min.js"></script>
   	<script src="js/croppic.min.js"></script>
    
    <script>
		var croppicContainerPreloadOptions = {
				uploadUrl:'img_save_to_file3.php?uid=<?=$uid;?>',
				cropUrl:'img_crop_to_file3.php?uid=<?=$uid;?>',
				loadPicture:'uploads/profilelogo/<?=$fld_campaign_logo;?>',
				enableMousescroll:true,
				loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> ',
				onBeforeImgUpload: function(){ console.log('onBeforeImgUpload') },
				onAfterImgUpload: function(){ console.log('onAfterImgUpload') },
				onImgDrag: function(){ console.log('onImgDrag') },
				onImgZoom: function(){ console.log('onImgZoom') },
				onBeforeImgCrop: function(){ console.log('onBeforeImgCrop') },
				onAfterImgCrop:function(){ window.close(); },
				onReset:function(){ console.log('onReset') },
				onError:function(errormessage){ console.log('onError:'+errormessage) }
		}
		var cropContainerPreload = new Croppic('cropContainerPreload', croppicContainerPreloadOptions);
	</script>
  </body>
</html>
