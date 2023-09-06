<?
require_once("../configuration/dbconfig.php");
$REQUEST = &$_REQUEST;

if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
}
$sPageName = '<li><a href="donors.php">Manage Donors</a></li> <li>Edit Donor</li>';
$sDonorLink = 'style="color:#F3BE00"';
$sLeftMenuDonors = 'active';
$id = $REQUEST['uid'];


if(isset($REQUEST['m']) && $REQUEST['m'] == 'edit')
{
	$id = $REQUEST['uid'];
	$aUserDetail = $oregister->getuserdetail($id);
	$sPassword = $oregister->decrypt($aUserDetail['fld_password'],sENC_KEY);
	$m = 'edit';
	
	$sState1 = $aUserDetail['fld_state'];
	if ($sState1 != '') {
		$sState = $sState1;
	} else {
		$sState = '';
	}
	$sCountry1 = $aUserDetail['fld_country'];
	if ($sCountry1 != '') {
		$sCountry = $sCountry1;
	} else {
		$sCountry = 'United States';
	}
	
	$sCity = $aUserDetail['fld_city'];
}else{
	$m = 'add';
	//$sState = 3919;
	$sCountry = 'United States';
	
}

if( isset($REQUEST['m']) && $REQUEST['m'] == 'edit' && 
	isset($REQUEST['fld_name']) && $REQUEST['fld_name'] !='' &&
	isset($REQUEST['fld_email']) && $REQUEST['fld_email']!=''
)
{
	$sRolePId = $REQUEST['fld_role_pid'];
	$sRole = $REQUEST['fld_role'];
	
	$sRoleId = $REQUEST['fld_role_id'];
	$sName = $REQUEST['fld_name'];
	$sLName = $REQUEST['fld_lname'];
	$sEmail = $REQUEST['fld_email'];
	$sPassword = $oregister->encrypt($REQUEST['fld_password'],sENC_KEY);
	$sPhone = $REQUEST['fld_phone'];
	$sAddress = $REQUEST['fld_address'];
	$sCity = $REQUEST['fld_city'];
	$sState = $REQUEST['fld_state'];
	$sCountry = $REQUEST['fld_country'];
	$sZipcode = $REQUEST['fld_zip'];
	$iId = $REQUEST['fld_pid'];
	$SStatus=$REQUEST['fld_status'];
	$oregister->chk_state($sState, $sCountry);
	$oregister->chk_city($sCity, $sState);
	$oregister->update_user('',$sName,$sLName,$sEmail,$sPassword,$sPhone,$sAddress,$sCity,$sState,$sCountry,$sZipcode,$sRoleId,$iId,$SStatus);
	$oregister->redirect('donors.php?msg=4');
		
}else if($REQUEST['m'] == 'add')
{
	$sRolePId = $REQUEST['fld_role_pid'];
	$sRole = $REQUEST['fld_role'];
	$sRoleId = $REQUEST['fld_role_id'];
	$sName = $REQUEST['fld_name'];
	$sLName = $REQUEST['fld_lname'];
	$sEmail = $REQUEST['fld_email'];
	$sPassword = $oregister->encrypt($REQUEST['fld_password'],sENC_KEY);
	$sPhone = $REQUEST['fld_phone'];
	$sAddress = $REQUEST['fld_address'];
	$sCity = $REQUEST['fld_city'];
	$sState = $REQUEST['fld_state'];
	$sCountry = $REQUEST['fld_country'];
	$sZipcode = $REQUEST['fld_zip'];
	$iId = $REQUEST['fld_pid'];
	$SStatus=$REQUEST['fld_status'];
	$oregister->chk_state($sState, $sCountry);
	$oregister->chk_city($sCity, $sState);
	$oregister->insert_user($sName,$sLName,$sEmail,$sPassword,$sPhone,$sAddress,$sCity,$sState,$sCountry,$sRoleId,$sZipcode,$SStatus);
	$oregister->redirect('donors.php?msg=3');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
<title>Admin<?php echo sWEBSITENAME;?> - Edit Donors</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<!-- Preloader -->
<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<div id="wrapper">
  <? include_once('header.php');?>
  <!-- Left side column. contains the logo and sidebar -->
  <? include_once('left_panel.php');?>
  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <!--row -->
      <div class="row">
          <div class="col-sm-12">
		  <!-- .white-box -->
          <div class="white-box " style="    background: rgba(245, 245, 245, 0);    border: 0px solid #d9d6d6;">
		  <h1 class="h1styling">Edit Donor</h1>
		  <div class="line3"></div>
		  <div class=" full-main">
			
   <form data-toggle="validator"  method="post">
	<div class="form-group col-sm-6">
		<label for="fld_role_id" class="control-label">User Level<span style="color:#FF0000">*</span></label>
		<select class="form-control colorMeBlue noValue" name="fld_role_id" id="fld_role_id" required>
                  	<option value="">Select User Level</option>
                    <?
					$sRoleData = $oregister->getdonorrole();
					$iCountRecords = count($sRoleData);
					if($iCountRecords>0){
					for($i=0;$i<$iCountRecords;$i++){
					?>
                     <option value="<?=$sRoleData[$i]['fld_role_id']?>" <? if($sRoleData[$i]['fld_role_id'] == $aUserDetail['fld_role_id']){?> selected<? }?>><?=$sRoleData[$i]['fld_role']?></option>
                    <? }}?>
                   
        </select>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_name" class="control-label">First Name<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_name" name="fld_name" placeholder="Enter First Name" value="<?=$aUserDetail['fld_name']?>" required>
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_lname" class="control-label">Last Name</label>
		<input type="text" class="form-control" id="fld_lname" name="fld_lname" placeholder="Enter Last Name" value="<?=$aUserDetail['fld_lname']?>">
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_phone" class="control-label">Phone<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_phone" name="fld_phone" value="<?=$aUserDetail['fld_phone']?>" required data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="___-___-____">
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_status" class="control-label">Status<span style="color:#FF0000">*</span></label>
		<select name="fld_status" id="fld_status" required class="form-control"><option value="1" <?php if($aUserDetail['fld_status']==1){?>selected<?php }?>>Active</option><option value="2" <?php if($aUserDetail['fld_status']==2){?>selected<?php }?>>Inactive</option></select>
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
	
   <div class="col-md-12"><h2 class="page-header" style="color: #868484; font-family: Open Sans; font-size: 24px;margin:0px 0 20px">Login Details</h2></div>
   
   <div class="form-group col-sm-6">
		<label for="fld_email" class="control-label">Email address<span style="color:#FF0000">*</span></label>
		<input type="email" class="form-control" id="fld_email" name="fld_email" placeholder="Enter email" value="<?=$aUserDetail['fld_email']?>" required>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_password" class="control-label">Password<span style="color:#FF0000">*</span></label>
		<input type="password" class="form-control" id="fld_password" name="fld_password" placeholder="Password" value="<?=$sPassword?>" required data-rule-email="true">
        <a href="javascript:void(0);" id="hideShowPwd"  style="font-size:20px;" alt="Hide Password" title="Hide Password" onClick="hideShowPassword()">
            <i class="fa fa-eye"></i>
        </a>
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_cpassword" class="control-label">Confirm Password<span style="color:#FF0000">*</span></label>
		<input type="password" class="form-control" id="fld_cpassword" name="fld_cpassword" placeholder="Password" value="<?=$sPassword?>" required oninput="check(this)">
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
	
	<div class="col-md-12"><h2 class="page-header" style="color: #868484; font-family: Open Sans; font-size: 24px;margin:0px 0 20px">Address Details</h2></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_address" class="control-label">Address<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_address" name="fld_address" placeholder="Enter address" value="<?=$aUserDetail['fld_address']?>" required>
		<div class="help-block with-errors"></div>
	</div>

	<div class="form-group col-sm-6">
		<label for="fld_zip" class="control-label">ZIP Code<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_zip" name="fld_zip" placeholder="Enter zipcode" value="<?=$aUserDetail['fld_zip']?>" required>
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_city" class="control-label">City<span style="color:#FF0000">*</span></label>
		<?
		$sCiData = $oregister->getcity($sState);
		$iCiRecords = count($sCiData);
		?>
		<div class="styled-select"  id="divCity">
		<!--<select name="fld_city" id="fld_city" class="form-control colorMeBlue noValue" required>
                  <option value="" selected>Select city</option>
                  <?
                  for($ci=0;$ci<$iCiRecords;$ci++)
				  {
				  ?>
                  <option value="<?=$sCiData[$ci]['name']?>" <? if($aUserDetail['fld_city'] == $sCiData[$ci]['name']){?> selected<? }?>><?=$sCiData[$ci]['name']?></option>
                  <?
				  }
				  ?>
        </select>-->
		<input type="text" class="form-control" id="fld_city" name="fld_city" placeholder="Enter City" value="<?=$aUserDetail['fld_city'];?>" required>
	</div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_state" class="control-label">State<span style="color:#FF0000">*</span></label>
		<?
        $sSData = $oregister->getstate($sCountry);
		$iSRecords = count($sSData);
        ?>
		<div class="styled-select" id="divState">
        <!--<select name="fld_state" id="fld_state"  class="form-control colorMeBlue noValue" required>
                  <option value="" selected>Select state</option>
                  <?
                  for($s=0;$s<$iSRecords;$s++)
				  {
				  ?>
                  <option value="<?=$sSData[$s]['name']?>" <? if($sState == $sSData[$s]['name']){?> selected<? }?>><?=$sSData[$s]['name']?></option>
                  <?
				  }
				  ?>
        </select>-->
		<input type="text" class="form-control" id="fld_state" name="fld_state" placeholder="Enter State" value="<?=$sState;?>" required>
		</div>
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_country" class="control-label">Country<span style="color:#FF0000">*</span></label>
		<?
        $sCData = $oregister->getcountry();
		$iCRecords = count($sCData);
        ?>
		<!--<select name="fld_country" id="fld_country"  class="form-control colorMeBlue" required >
				  <option value="" selected>Select country</option>
				  <option value="United States" selected>United States</option>
                  <?
                  for($c=0;$c<$iCRecords;$c++)
				  {
				  ?>
					<option value="<?=$sCData[$c]['name']?>" <? if($sCountry == $sCData[$c]['name']){?> selected<? }?>><?=$sCData[$c]['name']?></option>
                  <?
				  }
				  ?>
        </select>-->
		<input type="text" class="form-control" id="fld_country" name="fld_country" placeholder="Enter Country" value="<?=$sCountry;?>" required>
		<div class="help-block with-errors"></div>
	</div>
   <div class="clearfix"></div>
   
   <div class="form-group">
		<input type="hidden" name="fld_pid" id="fld_pid" value="<?=$id?>">
        <input type="hidden" name="m" id="m" value="<?=$m?>">
    	<div class="col-sm-6" align="left">
			<button class="btn btn-primary waves-effect waves-light" type="button" onClick="window.location.href='dashboard.php'"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Cancel</button>
		</div>
		
		<div class="col-sm-6" align="right">
			<button class="btn btn-success waves-effect waves-light" type="submit">Save & Continue <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
		</div>
   </div>
   <div class="clearfix"></div>
   </form>
   </div>
   </div>
</div>
		  </div>
		  </div>
    </div>
    <!-- /.container-fluid -->
  </div>
  <!-- /#page-wrapper -->
	<!-- #footer -->
    <? include_once('footer.php');?>
	<!-- /#footer -->
</div>
<!-- /#wrapper -->
<!-- jQuery -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Menu Plugin JavaScript -->
<script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
<!--Nice scroll JavaScript -->
<script src="js/jquery.nicescroll.js"></script>

<!--Wave Effects -->
<script src="js/waves.js"></script>
<!-- Custom Theme JavaScript -->
<script src="js/myadmin.js"></script>
<!--Counter js -->
<script src="bower_components/waypoints/lib/jquery.waypoints.js"></script>
<script src="bower_components/counterup/jquery.counterup.min.js"></script>
<!--<script src="js/mask.js"></script>-->
<!--Sparkline charts js -->
<script src="bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
<script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>
<script src="js/validator.js"></script>
<script>
$('.fa-check').css('color','transparent');
function check(input) {
    if (input.value != document.getElementById('fld_password').value) {
        input.setCustomValidity('Password and Confirm Password do not match!');
    } else {
        // input is valid -- reset the error message
        input.setCustomValidity('');
    }
}

$('select').on('change', function(){
    var $this = $(this);
    
    if (!$this.val()) {
        $this.addClass('noValue');
    } else {
        $this.removeClass('noValue');
    }
});
/*var fld_city = $("#fld_city").val();
if (fld_city != '') {	
	$("#fld_city").removeClass('noValue');
} 
var fld_state = $("#fld_state").val();
if (fld_state != '') {	
	$("#fld_state").removeClass('noValue');
} 
var fld_country = $("#fld_country").val();
if (fld_country != '') {	
	$("#fld_country").removeClass('noValue');
} 
$('#fld_zip').on('blur', function() {
	  $izipcode = this.value;
	  $.ajax({url: "showzipcode.php?zid="+$izipcode, success: function(result){
		var jdata = JSON.parse(result);
		$("#fld_country").removeClass('noValue');
		$("#fld_state").removeClass('noValue');
		$("#fld_city").removeClass('noValue');
		if (jdata.country == 'United States') {
			$("#fld_country option:selected").text(jdata.country).val(jdata.countryid);
			$("#fld_state option:selected").text(jdata.state).val(jdata.stateid);
			$("#fld_city option:selected").text(jdata.city).val(jdata.cityid);
			document.getElementById('fld_zip').setCustomValidity("");
			$('#fld_zip').focusout();
		} else {
			document.getElementById('fld_zip').setCustomValidity('This is Invalid Zipcode, Please enter a Valid Zipcode');
			$('#fld_zip').focusout();
		}
    }});
});
$('#fld_country').on('change', function() {
	  //alert( this.value ); // or $(this).val()
	  $iCountryId = this.value;
	  $.ajax({url: "showstate.php?cid="+$iCountryId, success: function(result){
        $("#divState").html(result);
    }});
});

$('#fld_state').on('change', function() {
	//  alert( this.value ); // or $(this).val()
	  $iStateId = this.value;
	  $.ajax({url: "showcity.php?sid="+$iStateId, success: function(result){
        $("#divCity").html(result);
    }});
});*/

function hideShowPassword() {
    //alert("test");
    var userPassword = $("#fld_password");
    var userCpassword = $("#fld_cpassword");
    if(userPassword.val() != "") {
        if($(userPassword).attr('type') == "password" && userCpassword.attr('type') == "password") {
            $(userPassword).attr('type', 'text');
            $(userCpassword).attr('type', 'text');
            $("#hideShowPwd").removeClass('green').addClass('red');
            $("#hideShowPwd").attr('alt', 'Hide Password');
            $("#hideShowPwd").attr('title', 'Hide Password');
        } else {
            $(userPassword).attr('type', 'password');
            $(userCpassword).attr('type', 'password');
            $("#hideShowPwd").removeClass('red').addClass('green');
            $("#hideShowPwd").attr('alt', 'Show Password');
            $("#hideShowPwd").attr('title', 'Show Password');
        }
    } else {
        //$("#userPasswordError").css('color', 'red').text('First Enter Password');
    }
}

</script>
<script src="js/jquery.inputmask.js"></script>
<script>
$("[data-mask]").inputmask();
</script>
</body>
</html>