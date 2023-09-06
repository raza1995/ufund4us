<section class="darkheader">
   <div class="container">
      <div class="col-md-12 dark-header-bg">
         <?php if ((isset($_GET['ref']) || isset($_REQUEST['cid'])) && $getphone != '' && $getemail != '') { ?>
         <ul class="navbar-nav pull-right hidden-xs">
            <li><a href="mailto:<?=$getemail;?>"><i class="fa fa-envelope"></i> <?=$getemail;?></a></li>
            <li><a href="javascript:void(0);"><i class="fa fa-phone"></i> <?=$getphone;?></a></li>
            <li class="hidden-sm hidden-xs"><a href="#"><i class="fa fa-facebook-f"></i></a></li>
            <li class="hidden-sm hidden-xs"><a href="#"><i class="fa fa-twitter"></i></a></li>
            <li class="hidden-sm hidden-xs"><a href="#"><i class="fa fa-linkedin"></i></a></li>
            <li class="hidden-sm hidden-xs"><a href="#"><i class="fa fa-google-plus"></i></a></li>
         </ul>
		 <?php } else { ?>
		 <ul class="navbar-nav pull-right hidden-xs">
            <li><a href="#"><i class="fa fa-envelope"></i><?php echo INFO_EMAIL;?></a></li>
            <li><a href="#"><i class="fa fa-phone"></i> 951.966.8262</a></li>
            <li class="hidden-sm hidden-xs"><a href="#"><i class="fa fa-facebook-f"></i></a></li>
            <li class="hidden-sm hidden-xs"><a href="#"><i class="fa fa-twitter"></i></a></li>
            <li class="hidden-sm hidden-xs"><a href="#"><i class="fa fa-linkedin"></i></a></li>
            <li class="hidden-sm hidden-xs"><a href="#"><i class="fa fa-google-plus"></i></a></li>
         </ul>
		 <?php } ?>
      </div>
   </div>
</section>
<section class="lightheader">
   <div class="container">
      <div class="col-md-3 col-sm-4 light-header-bg-logo">
         <?php if ((isset($_GET['ref']) || isset($_REQUEST['cid']) || isset($_REQUEST['link'])) && $getlogo != '') { ?>
		 <div class="logo-relative" style="background:url(images/logo2.png);">
			<img class="logo img-responsive" src="<?=$getlogo;?>">
			<img class="logo img-responsive hidden print-logo" src="cms/uploads/brandlogo/<?=$getlogo;?>" width="250px">
		 </div>
		 <?php } else { ?>
			<img class="logo img-responsive" src="images/logo.png">  
			<img class="logo img-responsive hidden print-logo" src="images/logo_print.png" width="250px">  
		 <?php } ?>
      </div>
      <div class="col-md-4 col-sm-7 light-header-bg-nav">
         <nav class="navbar navbar-default ipadportrat ipadland" role="navigation">
            <div class="navbar-header">
			   <? if (basename($_SERVER['PHP_SELF']) == 'campaign.php' || basename($_SERVER['PHP_SELF']) == 'donation.php' || basename($_SERVER['PHP_SELF']) == 'receipt.php' || basename($_SERVER['PHP_SELF']) == 'thankyou.php' || basename($_SERVER['PHP_SELF']) == 'sign-in.php' || basename($_SERVER['PHP_SELF']) == 'index.php' || basename($_SERVER['PHP_SELF']) == 'signup.php') { } else { ?>
               <div class="responsive-signup">
                  <div class="col-md-3 light-header-bg-nav">
                     <ul id="menu-signinup" class="signupul">
                        <li id="menu-item-15" class="signinli menu-item menu-item-type-custom menu-item-object-custom menu-item-15"><a href="sign-in.html">Sign In</a></li>
                        <li id="menu-item-16" class="signupli menu-item menu-item-type-custom menu-item-object-custom menu-item-16"><a href="sign-up.html">Sign Up</a></li>
                     </ul>
                  </div>
               </div>
			   <? } ?>
               <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#example-navbar-collapse">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               </button>
            </div>
            <div class="collapse navbar-collapse" id="example-navbar-collapse">
               <div class="menu-menu-1-container">
                  <ul id="menu-menu-1" class="nav navbar-nav">
                     <li id="menu-item-5" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-5"><a href="../">Home</a></li>
                  </ul>
               </div>
            </div>
         </nav>
      </div>
	  <? if (basename($_SERVER['PHP_SELF']) == 'campaign.php' || basename($_SERVER['PHP_SELF']) == 'donation.php' || basename($_SERVER['PHP_SELF']) == 'receipt.php' || basename($_SERVER['PHP_SELF']) == 'thankyou.php' || basename($_SERVER['PHP_SELF']) == 'sign-in.php' || basename($_SERVER['PHP_SELF']) == 'index.php' || basename($_SERVER['PHP_SELF']) == 'signup.php') { } else { ?>
      <div class="col-md-5 light-header-bg-nav hidemenue pull-right">
         <ul id="menu-signinup-1" class="signupul" style="width:100%">
            <? if(isset($_SESSION['uid']) && $_SESSION['uid']) { ?>
            <li class="signinli menu-item menu-item-type-custom menu-item-object-custom menu-item-15 "><a href="<?php echo sHOMESCMS;?>logout.php">Logout</a></li>
            <? } else { ?>
			<? if (basename($_SERVER['PHP_SELF']) != 'confirm_campaign.php' && basename($_SERVER['PHP_SELF']) != 'sign-up.php') { ?>
            <? if (basename($_SERVER['PHP_SELF']) == 'create_user.php') { $cuid = $_GET['cuid']; $cid = $_GET['cid'];?>
            <li class="signinli menu-item menu-item-type-custom menu-item-object-custom menu-item-15 "><a href="sign-in.php?cuid=<?=$cuid;?>&cid=<?=$cid;?>&action=joincampaign">Sign In</a></li>
            <? } else { ?>
            <li class="signinli menu-item menu-item-type-custom menu-item-object-custom menu-item-15 "><a href="<?php echo sHOMESCMS;?>sign-in.php">Sign In</a></li>
            <? } ?>
			<? } ?>
            <!--<li class="signupli menu-item menu-item-type-custom menu-item-object-custom menu-item-16" ><a href="signup.php">Sign Up</a></li>-->
            <? } ?>
			<? if (basename($_SERVER['PHP_SELF']) != 'join_campaign.php' && basename($_SERVER['PHP_SELF']) != 'confirm_campaign.php' && basename($_SERVER['PHP_SELF']) != 'sign-up.php') { ?>
            <li class="signupli menu-item menu-item-type-custom menu-item-object-custom menu-item-16"><a href="join_campaign.php">Join Campaign</a></li>
            <li class="startcamp menu-item menu-item-type-custom menu-item-object-custom menu-item-296"><a href="<?php echo sHOMESCMS;?>startyourcampaign.php">Start Campaign</a></li>
			<? } ?>
         </ul>
      </div>
	  <? } ?>
   </div>
</section>
<section class="lightheader" style="display:none;">
   <div class="container ">
      <div class="col-md-4 col-sm-4 light-header-bg-logo">
         <img class="logo img-responsive" src="images/logo.png">  
         <img class="logo img-responsive hidden print-logo" src="images/logo_print.png" width="250px">  
      </div>
      <div class="col-md-5 col-sm-5 light-header-bg-nav">
         <nav class="navbar navbar-default ipadportrat ipadland" role="navigation">
            <div class="navbar-header">
			   <? if (basename($_SERVER['PHP_SELF']) == 'campaign.php' || basename($_SERVER['PHP_SELF']) == 'donation.php' || basename($_SERVER['PHP_SELF']) == 'receipt.php' || basename($_SERVER['PHP_SELF']) == 'thankyou.php' || basename($_SERVER['PHP_SELF']) == 'sign-in.php' || basename($_SERVER['PHP_SELF']) == 'index.php' || basename($_SERVER['PHP_SELF']) == 'signup.php') { } else { ?>
               <div class="responsive-signup">
                  <div class="col-md-3 light-header-bg-nav">
                     <ul id="menu-signinup" class="signupul">
                        <li id="menu-item-15" class="signinli menu-item menu-item-type-custom menu-item-object-custom menu-item-15"><a href="sign-in.html">Sign In</a></li>
                        <li id="menu-item-16" class="signupli menu-item menu-item-type-custom menu-item-object-custom menu-item-16"><a href="sign-up.html">Sign Up</a></li>
                     </ul>
                  </div>
               </div>
			   <? } ?>
               <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#example-navbar-collapse">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               </button>
            </div>
            <div class="collapse navbar-collapse" id="example-navbar-collapse">
               <div class="menu-menu-1-container">
                  <ul id="menu-menu-1" class="nav navbar-nav">
                     <li id="menu-item-5" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home menu-item-5"><a href="../">Home</a></li>
                     <li id="menu-item-14" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-14"><a href="../about-us/">About Us</a></li>
                     <li id="menu-item-13" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-13"><a href="../careers/">Careers</a></li>
                     <li id="menu-item-12" class="menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-10 current_page_item menu-item-12  "><a href="../contact-us/">Contact Us</a></li>
                  </ul>
               </div>
            </div>
         </nav>
      </div>
      <? if (basename($_SERVER['PHP_SELF']) == 'campaign.php' || basename($_SERVER['PHP_SELF']) == 'donation.php' || basename($_SERVER['PHP_SELF']) == 'receipt.php' || basename($_SERVER['PHP_SELF']) == 'thankyou.php') { } else { ?>
      <div class="col-md-3 light-header-bg-nav hidemenue">
         <ul id="menu-signinup-1" class="signupul">
            <? if ($_SESSION['uid']) { ?>
            <li class="signinli menu-item menu-item-type-custom menu-item-object-custom menu-item-15 "><a href="logout.php">Logout</a></li>
            <? } else { ?>
            <? if (basename($_SERVER['PHP_SELF']) == 'create_user.php') { $cuid = $_GET['cuid']; $cid = $_GET['cid'];?>
            <li class="signinli menu-item menu-item-type-custom menu-item-object-custom menu-item-15 "><a href="sign-in.php?cuid=<?=$cuid;?>&cid=<?=$cid;?>&action=jo
            ampaign">Sign In</a></li>
            <? } else { ?>
            <li class="signinli menu-item menu-item-type-custom menu-item-object-custom menu-item-15 "><a href="sign-in.php">Sign In</a></li>
            <? } ?>
            <li class="signupli menu-item menu-item-type-custom menu-item-object-custom menu-item-16" ><a href="signup.php">Sign Up</a></li>
            <? } ?>
         </ul>
      </div>
      <? } ?>
   </div>
</section>