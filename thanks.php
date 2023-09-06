<?
require_once("configuration/dbconfig.php");
require_once("configuration/message.php");

$_REQUEST['m'] = isset($_REQUEST['m']) ? $_REQUEST['m'] : ''; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0031)<?php echo SITE_URL;?>contact-us/ -->
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Sign In</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.css">
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>  
<link href="css/style.css" rel="stylesheet" type="text/css"> 

<link href="css/ninja-slider.css" rel="stylesheet" type="text/css">
<script src="js/ninja-slider.js" type="text/javascript"></script>
<script>
function FormSubmit(){
	document.frmLogin.submit();
}
</script>
</head>
<body>
<?
$msg = '';
if ($_REQUEST['m'] != '') {
	$msg = $_REQUEST['m'];
}

$aMessage[$msg] = isset($aMessage[$msg]) ? $aMessage[$msg] : '';
?>
<? include_once('header.php');?>
<section class="ipcontentsection">
	<div class="container newContainer">
		<div class="mid_sec2">
			<div class="mid_secin">
				<div class="row2">
					<h3>Thanks</h3>
					<p><?php echo $aMessage[$msg];?></p>
					<p> Click here to <a href="sign-in.php">Sign In</a></p>
					<div></div>
				</div>
			</div>
		</div>
	</div>
</section>  
<? include_once('footer.php');?>
</body>
</html>