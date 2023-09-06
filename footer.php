<section class="footerfluid">
  <div class="container">
     <div class="col-md-3 pull-left row footer-logo-main"> 
         <div class="footer-logo">
           <?php if ((isset($_GET['ref']) || isset($_REQUEST['cid']) || isset($_REQUEST['link'])) && $getlogo != '') { ?>
           <img src="<?=$getlogo;?>" height="57">
		   <?php } else { ?>
           <img src="images/footer-logo.png">
			<?php } ?>
         </div>
     </div>

        <div class="col-md-7-copyright row">
          <div class="simplefooter"> 
               Copyright © <?php echo COPY_RIGHT_YEAR;?> l <a href="#"><?php echo sWEBSITENAME;?>.</a> All rights reserved. 
          </div>



<div class="footernav clearfix">
    <ul id="menu-footer" class="footerul">
       <li id="menu-item-29" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-29"><a href="<?php echo SITE_FULL_URL;?>terms-of-use/">Terms of Use</a></li>
      <li>|</li>
       <li id="menu-item-28" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-28"><a href="<?php echo SITE_FULL_URL;?>privacy-policy/">Privacy Policy</a></li>
      </ul>
</div>

        </div>

        <div class="col-md-2 col-xs-12 poweredby pull-right row"> 
          Powered by <a href="http://www.lyja.com/">Lyja</a>
        </div>


</div>
<div style="position: fixed; z-index: 1000; width: 120px; height: 70px; bottom: 70px; right: 15px;">
	<a href="javascript:void();" onclick="window.open('https://www.sitelock.com/verify.php?site=<?php echo SITE_DOMAIN;?>','SiteLock','width=600,height=600,left=160,top=170');" >
		<img class="img-responsive" alt="SiteLock" title="SiteLock" src="//shield.sitelock.com/shield/<?php echo SITE_DOMAIN;?>" />
	</a>
	<script type="text/javascript"> //<![CDATA[ 
var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.comodo.com/" : "http://www.trustlogo.com/");
document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
//]]>
	</script>
	<script language="JavaScript" type="text/javascript">
		TrustLogo("<?php echo SITE_URL;?>app/images/comodo_secure_seal_113x59_transp.png", "CL1", "none");
	</script>
	<a  href="https://ssl.comodo.com" id="comodoTL">Comodo SSL</a>
</div>
</section>
<script src="js/pi.global.js"></script>
<script src="js/jquery-1.10.2.min.js"></script>
<script src="js/bootstrap.min(1).js"></script>
<script>
	$('#myCarousel').carousel({
		interval: 1000 * 10
	});
</script>