<?
checkSetInArrayAndReturn($_SESSION, 'uname', '');
checkSetInArrayAndReturn($_SESSION, 'role_id', 0);
checkSetInArrayAndReturn($_SESSION, 'uid', 0);


if (isset($_REQUEST['url'])) {
	$get_ref = $_REQUEST['url'];
} else {
	$get_ref = '';
}
if (isset($get_ref) && $get_ref == '') {
	$iId = $_SESSION['uid'];

	$access_head = true;
} 
else {
	$decoder = base64_decode($get_ref);
	$get_decoded = explode("&",$decoder);
	$getid = (int) filter_var($get_decoded[0], FILTER_SANITIZE_NUMBER_INT);
	$iId = $getid;
	$access_head = false;
}

$aUserDetailss = $oregister->getuserdetail($iId);

$profiles_image1 = checkAndReturnOnly($aUserDetailss, 'fld_image', '');

if (isset($profiles_image1) && $profiles_image1 != '') {

	$profiles_image = 'uploads/profilelogo/'.$profiles_image1.'';

} else {
	$profiles_image = 'uploads/profilelogo/default-profile-pic.jpg';
}

if ($_SESSION['role_id'] == 1) {
	$role_name = 'Administrator';
} elseif ($_SESSION['role_id'] == 2) {
	$role_name = 'Campaign Manager';
} elseif ($_SESSION['role_id'] == 3) {
	$role_name = 'Distributor';
} elseif ($_SESSION['role_id'] == 5) {
	$role_name = 'Participant';
} elseif ($_SESSION['role_id'] == 6) {
	$role_name = 'Representative';
} else {
	$role_name = '';
}
?>

<nav class="navbar navbar-default navbar-static-top" style="margin-bottom: 0">

	<div class="navbar-header"> 

	<a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse"><i class="ti-menu"></i></a>

      <div class="top-left-part">
			<a class="logo" href="dashboard.php">
				<?php 
				//Default Logo
				$img_header_logo = "";
				$img_footer_logo = "";
				if ($_SESSION['role_id'] > 1) { 
				//Find Distributor Brand
					$role_id = $_SESSION['role_id'];
					$user_id = $_SESSION['uid'];
					$aBrandData = $oregister->getbranddetail($role_id, $user_id);
					if (count($aBrandData) > 0) {
						/*$img_header_logo = $aBrandData['fld_brand_logo_header'];
						$img_footer_logo = $aBrandData['fld_brand_logo_footer'];*/
						$img_header_logo = $aBrandData['fld_brand_logo_header'];
						$img_footer_logo = $aBrandData['fld_brand_logo_header'];
					}
				} else {
					$img_header_logo = basename($getlogo);
					$img_footer_logo = basename($getlogo);
				}
				?>
				<?php 
				if ($_SESSION['role_id'] == 1) {
					echo '<img src="../images/logo_print.png" height="60px">';
				} elseif ($img_header_logo != '') { 
					echo '<img src="uploads/brandlogo/'.$img_header_logo.'" height="60px">';
				} else {
					echo '<img src="dist/img/logo.png" width="120px">';
				}
				?>
			</a>
	  </div>
      <ul class="nav navbar-top-links navbar-left hidden-xs">

        <li><a href="javascript:void(0)" class="open-close hidden-xs waves-effect waves-light"><i class="icon-arrow-left-circle ti-menu"></i></a></li>

      </ul>

	  <ol class="breadcrumb" style="float:left;margin-top:15px !important; margin-bottom:0px; background-color:#f1f1f1 !important;">

			<i class="fa fa-dashboard"></i>

			<li><a href="dashboard.php">Home</a></li>

            <?=$sPageName;?>

      </ol>

      <ul class="nav navbar-top-links navbar-right pull-right">
        <!-- <li style="color: red; margin: 15px 0px"><strong>Test Site...Test Site...Test Site...Test Site...Test Site...Test Site...</strong></li> -->
        <li class="dropdown"> <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"> <img src="<?=$profiles_image;?>" alt="" onerror="this.src='../images/img.png';" width="36" height="36" class="img-circle"><b class="hidden-xs">Welcome <?=$_SESSION['uname']?>!</b> </a>

          <ul class="dropdown-menu dropdown-user">

            <li><a href="edit_profile.php"><i class="ti-user"></i> My Profile</a></li>

            <li role="separator" class="divider"></li>

            <li><a href="logout.php"><i class="fa fa-power-off"></i> Logout</a></li>

          </ul>

          <!-- /.dropdown-user -->

        </li>

        <!-- /.dropdown -->

      </ul>

    </div>

    <!-- /.navbar-header -->

    <!-- /.navbar-top-links -->

    <!-- /.navbar-static-side -->

</nav>

<!--<script>

function update_activity(){

    $.ajax({

        type: "POST",

        url: "activityupdate.php",

        async: false

    }).success(function(){

    });

}

setInterval(function(){update_activity();}, 8000);

</script>-->