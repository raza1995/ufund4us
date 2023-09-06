<?
require_once("../configuration/dbconfig.php");
$sPageName = '<li><a href="roles.php">Manage Roles</a></li><li>Edit Role</li>';

$REQUEST = &$_REQUEST;
$REQUEST['fld_role'] = isset($REQUEST['fld_role']) ? $REQUEST['fld_role'] : '';

if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else {
	if ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5 || $_SESSION['role_id'] == 6) {
		$oregister->redirect('dashboard.php');
	}
}
$sPageName = '<li><a href="roles.php">Manage Roles</a></li> <li>Edit Role</li>';
$sRoleLink = 'style="color:#F3BE00"';
$sLeftMenuUsers = 'active';
if(isset($REQUEST['m']) && $REQUEST['m'] == 'edit')
{
	$id = $REQUEST['id'];
	$aRoleDetail = $oregister->getroledetail($id);
	$m = 'edit';
}else{
	$m = 'add';
}

if($REQUEST['fld_role']!='')
{
	$sRolePId = $REQUEST['fld_role_pid'];
	$sRole = $REQUEST['fld_role'];
	
	if($REQUEST['m'] == 'edit')
	{
		
		
		$iRoleId = $REQUEST['fld_role_id'];
		$sRights = $REQUEST['fld_rights'];
		$oregister->update_role($sRolePId,$sRole,$iId,$sRights);
		
		$m=0;
		
		//echo '<pre>';
		//print_r($REQUEST);
		
		foreach($REQUEST['mid'] as $moduleID)
		{
			$iMid = $moduleID;	
			//$iRoleId = $iId;
			
			if($sRights == 1)
			{
				$sViewModule = 1;
				$sAddModule = 1;
				$sEditModule = 1;
				$sDeleteModule = 1;	
			}else{
				$sViewModule = $REQUEST['m_view'.$iMid];
				$sAddModule = $REQUEST['m_add'.$iMid];
				$sEditModule = $REQUEST['m_edit'.$iMid];
				$sDeleteModule = $REQUEST['m_del'.$iMid];	
				
				if($sViewModule == 1)
				{
					$sViewModule =1;
				}else{
					$sViewModule =0;
				}
				
				if($sAddModule == 1)
				{
					$sAddModule =1;
				}else{
					$sAddModule =0;
				}
				
				if($sEditModule == 1)
				{
					$sEditModule =1;
				}else{
					$sEditModule =0;
				}
				
				if($sDeleteModule == 1)
				{
					$sDeleteModule =1;
				}else{
					$sDeleteModule =0;
				}
				
				
			}
			//echo '<br>'.$iRoleId.'--'.$iMid.'--'.$sViewModule.'--'.$sAddModule.'--'.$sEditModule.'--'.$sDeleteModule;
			
			$sModuleAccessData = $oregister->getroleaccessdetail($iRoleId,$iMid);
			if($sModuleAccessData['fld_roleaccess_id'] > 0)
			{
				$iRoleAccessId = $sModuleAccessData['fld_roleaccess_id'];
				$oregister->update_role_access($iRoleId,$iMid,$sViewModule,$sAddModule,$sEditModule,$sDeleteModule,$iRoleAccessId);	
			}else{
				
				$oregister->insert_role_access($iRoleId,$iMid,$sViewModule,$sAddModule,$sEditModule,$sDeleteModule);	
				
			}
						
			
			$m++;		
		}	
		
		$oregister->redirect('roles.php?msg=4');
	}else if($REQUEST['m'] == 'add')
	{
		
		$sRights = $REQUEST['fld_rights'];
		$iRoleId = $oregister->insert_role($sRole,$sRolePId,$sRights);	
		
		$m=0;
		foreach($REQUEST['mid'] as $moduleID)
		{
			$iMid = $moduleID;	
						
			if($sRights == 1)
			{
				$sViewModule = 1;
				$sAddModule = 1;
				$sEditModule = 1;
				$sDeleteModule = 1;	
			}else{
				$sViewModule = $REQUEST['m_view'.$iMid];
				$sAddModule = $REQUEST['m_add'.$iMid];
				$sEditModule = $REQUEST['m_edit'.$iMid];
				$sDeleteModule = $REQUEST['m_del'.$iMid];	
				
				if($sViewModule == 1)
				{
					$sViewModule =1;
				}else{
					$sViewModule =0;
				}
				
				if($sAddModule == 1)
				{
					$sAddModule =1;
				}else{
					$sAddModule =0;
				}
				
				if($sEditModule == 1)
				{
					$sEditModule =1;
				}else{
					$sEditModule =0;
				}
				
				if($sDeleteModule == 1)
				{
					$sDeleteModule =1;
				}else{
					$sDeleteModule =0;
				}
			}	
						
			$oregister->insert_role_access($iRoleId,$iMid,$sViewModule,$sAddModule,$sEditModule,$sDeleteModule);				
			$m++;		
		}		
		$oregister->redirect('roles.php?msg=3');
	}
	
	
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
<title>Admin<?php echo sWEBSITENAME;?> - Edit Roles</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="plugins/iCheck/all.css">
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
			<h1 class="h1styling">Edit Role</h1>
			<div class="line3"></div>
			<!-- .white-box -->
			<div class="white-box" style="    background: rgba(245, 245, 245, 0);    border: 0px solid #d9d6d6;">
			<div class=" full-main">
				<form data-toggle="validator"  method="post">
					<div class="form-group col-sm-6">
						<label for="fld_role" class="control-label">Role Title<span style="color:#FF0000">*</span></label>
						<input type="text" class="form-control" id="fld_role" name="fld_role" placeholder="Enter role title" value="<?=$aRoleDetail['fld_role']?>" required>
						<div class="help-block with-errors"></div>
					</div>
					
					<div class="form-group col-sm-6">
						<label for="fld_rights_1" class="control-label">Rights<span style="color:#FF0000">*</span></label><br>
						<div class="radio">
							<div class="col-sm-4">
								<input name="fld_rights" id="fld_rights_1" type="radio" value="2" onClick="showAccessDiv(this.value)" style="font-size:20px;" <? if($aRoleDetail['fld_rights'] == 2){?> checked<? }?> required>
								<label for="radio1"> Limited Access </label>
							</div>
							<div class="col-sm-3">
								<input name="fld_rights" id="fld_rights_2" type="radio" value="1" onClick="showAccessDiv(this.value)" <? if($aRoleDetail['fld_rights'] == 1){?> checked<? }?> required>
								<label for="radio1"> Full Access </label>
							</div>
						</div>
					</div>
					<?
					if($aRoleDetail['fld_rights'] == 2)
					{
						$sStyleCss = 'style="display:block;margin-top:20px;"';
					}else{
						$sStyleCss = 'style="display:none;margin-top:20px;"';
					}
					?>
					<div class="clearfix"></div>
					<div class="col-md-12" id="divAccess" <?=$sStyleCss?>>
						<table class="table table-bordered">
							<tbody>
								<tr>
									<th>Module</th>
									<th style="width:100px;">View</th>
									<th style="width:100px;">Add</th>
									<th style="width:100px;">Edit</th>
									<th style="width:100px;">Delete</th>
								</tr>
								<?
								if(isset($REQUEST['m']) && $REQUEST['m'] == 'edit')
								{
									$sModuleData = $oregister->getmodules();
									$iCountModule = count($sModuleData);
									for($i=0;$i<$iCountModule;$i++)
									{
										$iRoleID = $id;
										$iModuleId = $sModuleData[$i]['fld_module_id'];
										$sModuleAccessData = $oregister->getroleaccessdetail($iRoleID,$iModuleId);
									?>
								<tr>
									<td><?=$sModuleData[$i]['fld_module']?><input type="hidden" name="mid[]" id="mid[]" value="<?=$sModuleData[$i]['fld_module_id']?>"></td>
									<td><label><input type="checkbox" class="flat-red"  name="m_view<?=$sModuleData[$i]['fld_module_id']?>" id="m_view<?=$sModuleData[$i]['fld_module_id']?>" value="1" <? if($sModuleAccessData['fld_view'] == 1){?> checked<? }?>></label></td>
									<td><label><input type="checkbox" class="flat-red"  name="m_add<?=$sModuleData[$i]['fld_module_id']?>" id="m_add<?=$sModuleData[$i]['fld_module_id']?>" value="1" <? if($sModuleAccessData['fld_add'] == 1){?> checked<? }?>></label></td>
									<td><label><input type="checkbox" class="flat-red"  name="m_edit<?=$sModuleData[$i]['fld_module_id']?>" id="m_edit<?=$sModuleData[$i]['fld_module_id']?>" value="1" <? if($sModuleAccessData['fld_edit'] == 1){?> checked<? }?>></label></td>
									<td><label><input type="checkbox" class="flat-red"  name="m_del<?=$sModuleData[$i]['fld_module_id']?>" id="m_del<?=$sModuleData[$i]['fld_module_id']?>" value="1" <? if($sModuleAccessData['fld_delete'] == 1){?> checked<? }?>></label></td>
								</tr>
								<? } ?>
               
								<?
								}else{
								$sModuleData = $oregister->getmodules();
								$iCountModule = count($sModuleData);
								for($i=0;$i<$iCountModule;$i++)
								{
								?>
								<tr>
									<td><?=$sModuleData[$i]['fld_module']?><input type="hidden" name="mid[]" id="mid[]" value="<?=$sModuleData[$i]['fld_module_id']?>"></td>
									<td><label><input type="checkbox" class="flat-red"  name="m_view<?=$sModuleData[$i]['fld_module_id']?>" id="m_view<?=$sModuleData[$i]['fld_module_id']?>" value="1"></label></td>
									<td><label><input type="checkbox" class="flat-red"  name="m_add<?=$sModuleData[$i]['fld_module_id']?>" id="m_add<?=$sModuleData[$i]['fld_module_id']?>" value="1"></label></td>
									<td><label><input type="checkbox" class="flat-red"  name="m_edit<?=$sModuleData[$i]['fld_module_id']?>" id="m_edit<?=$sModuleData[$i]['fld_module_id']?>" value="1"></label></td>
									<td><label><input type="checkbox" class="flat-red"  name="m_del<?=$sModuleData[$i]['fld_module_id']?>" id="m_del<?=$sModuleData[$i]['fld_module_id']?>" value="1"></label></td>
								</tr>
								<? } }?>
							</tbody>
						</table>
					</div>
					<div class="form-group">
						<input type="hidden" name="fld_role_id" id="fld_role_id" value="<?=$id?>">
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
			</div></div>
		  </div>
    </div>
    <!-- /.container-fluid -->
  </div>
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
<!--Sparkline charts js -->
<script src="bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
<script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>

<script src="plugins/fastclick/fastclick.js"></script>
<script src="plugins/iCheck/icheck.min.js"></script>

<script type="text/javascript">
function showAccessDiv(id){
	if(id == 1){
		document.getElementById('divAccess').style.display = 'none';	
	}else if(id == 2){
		document.getElementById('divAccess').style.display = 'block';	
	}
}
   jQuery(document).ready(function($) {
    $('.vcarousel').carousel({
     interval: 3000
   })
    $(".counter").counterUp({
        delay: 100,
        time: 1200
    });
 });
</script>
<script>
  $(function () {
    

    //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass: 'iradio_minimal-red'
    });
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });

  });
</script>
</body>
</html>
<? include_once('bottom.php');?>