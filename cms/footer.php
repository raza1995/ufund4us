<footer class="footer text-center">
	<div class="col-md-5 logo-main "> 
		<div class="footer-logo">
			<a href="<?php echo sHOMESCMS;?>">
			<?php 
			if($_SESSION['role_id'] == 1) {
				echo '<img src="dist/img/footer-logo.png">';
			} 
			elseif ($img_footer_logo != '') {
				echo '<img src="uploads/brandlogo/'.$img_footer_logo.'" height="71px">';
			} 
			else {
				echo '<img src="dist/img/footer-logo.png">';
			}
			?>
			</a>
		</div>
	</div>
	<div class="Copyright">Copyright © <?php echo COPY_RIGHT_YEAR;?> | <a href="<?php echo SITE_URL;?>"><?php echo sWEBSITENAME;?>.</a> All rights reserved. </div>
	
	<div class="col-md-3 col-xs-12 poweredby pull-right"> 
		<a href="#">
			<span class="fa fa-facebook"></span>
		</a>  
		<a href="#">
			<span class="fa fa-twitter"></span>
		</a>
		<a href="#">
			<span class="fa fa-google-plus"></span>
		</a>
		<a href="#">
			<span class="fa fa-linkedin"></span>
		</a>
	</div>
	<div style="position: fixed; z-index: 1000; width: 120px; height: 70px; bottom: 70px; right: 15px;">
		<a href="javascript:void();" onclick="window.open('https://www.sitelock.com/verify.php?site=<?php echo SITE_DOMAIN;?>','SiteLock','width=600,height=600,left=160,top=170');" >
			<img class="img-responsive" alt="SiteLock" title="SiteLock" src="//shield.sitelock.com/shield/<?php echo SITE_DOMAIN;?>" />
		</a>
		<script type="text/javascript">
			var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.comodo.com/" : "http://www.trustlogo.com/");
			document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script language="JavaScript" type="text/javascript">
			TrustLogo("<?php echo SITE_URL;?>app/images/comodo_secure_seal_113x59_transp.png", "CL1", "none");
		</script>
		<a  href="https://ssl.comodo.com" id="comodoTL">Comodo SSL</a>
	</div>
	<p style="display: none;">a</p>
	
	<?php 
	if ($_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) { 
		if ($_SESSION['msg_box'] == '') { ?>
			<link href="bower_components/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
			<script src="bower_components/sweetalert/sweetalert.min.js"></script>
			<style>
			.sweet-alert { margin: auto; transform: translateX(-50%); }
			.sweet-alert.sweetalert-lg { width: 600px; }
			.swal-wide{
			    width:850px !important;
			}
			</style>
			<script>
			swal({
				title: "Note",
				text: "BY CLICKING “AGREE” OR OTHERWISE CONTINUING YOUR USE, YOU CONFIRM THAT YOU ARE AN AUTHORIZED LICENSED USER OF <?php echo sWEBSITENAME;?> WHO HAS AGREED TO THE TERMS OF SERVICE AND THE PRIVACY POLICY* OF <?php echo sWEBSITENAME;?> AND THAT YOU ARE ENGAGING IN LICENSED AND AUTHORIZED USE ONLY; ANY UNAUTHORIZED OR UNLICENSED USE VIOLATES THE TERMS OF SERVICE AND IS PROHIBITED BY APPLICABLE FEDERAL AND STATE LAW. BY CONTINUING, YOU ARE BOUND BY THE TERMS OF SERVICE AND PRIVACY POLICY OF <?php echo sWEBSITENAME;?> AND YOUR USE IS I ACCORDANCE WITH THE FOREGOING.   ALL OTHER RIGHTS ARE RESERVED BY <?php echo SITE_DOMAIN;?>.",
				type: "warning",
				customClass: 'sweetalert-lg',
				showCancelButton: false,
				closeOnConfirm: true
			},
			function(isConfirm) {
			  if (isConfirm) {
			    console.log("close swal");		    
			    $('.sweet-overlay').hide();
			    $('.showSweetAlert').hide();
			  } 
			});
			</script>
		<?php 
			$_SESSION['msg_box'] = 1; 
		} 
	}  
	?>
</footer>