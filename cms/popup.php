<?php
require_once("../configuration/dbconfig.php");
if ($_POST['cid'] != '') {
	$cid = $_POST['cid'];
} elseif ($_GET['cid'] != '') {
	$cid = $_GET['cid'];
}
$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
$logo_detail = $aCampaignDetail['fld_campaign_logo'];
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
	<style>
		.savebutton {
			color: #fff;border: 1px solid #CCCCCC;
			background: #FCB514;
			display: inline-block;
			padding: 8px 14px;
			cursor: pointer;
			text-decoration: none;
			text-align: center;
			white-space: nowrap;
			font-size: 12px;
			font-weight: bold;
			border-radius: 3px;
			vertical-align: middle;
			box-shadow: 0px 1px 5px rgba(0,0,0,0.05);
			transition: all 0.2s;
		}
		.savebutton:hover {
			background: #337ab7;
		}
	</style>
  </head>

  <body>
	<div class="container">
		<div class="row mt ">
			<div class="col-lg-4 ">
				<h4 class="centered"> Campaign Logo </h4>
				<div id="cropContainerPreload"></div>
			</div>		
		</div>
		<div class="row mt">
			<div class="col-lg-4" align="center">
				<button class="savebutton save">SAVE IMAGE</button>
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
				uploadUrl:'img_save_to_file.php?cid=<?=$cid;?>',
				cropUrl:'img_crop_to_file.php?cid=<?=$cid;?>',
				loadPicture:'uploads/logo/<?=$logo_detail;?>',
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
		$('.save').on('click', function() {
			$('.cropControlCrop').click();
		});
	</script>
  </body>
</html>
