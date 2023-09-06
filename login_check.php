<?php
//if user id is not set in session then redirect on sign-in page
if( isset($_SESSION['uid']) && $_SESSION['uid'] > 0 ) {
	//user is login
}
else{
	$oregister->redirect('../sign-in.php');
}
?>
