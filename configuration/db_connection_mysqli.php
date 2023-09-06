<?php 

//echo 'PHP_MAJOR_VERSION-'.PHP_MAJOR_VERSION;

$DB_USER1 = $DB_user;
$DB_PASS1 = $DB_pass;
$DB_NAME1 = $DB_name;
$DB_HOST1 = $DB_host; // database location
$mysqliCon = $con = $conn1 = mysqli_connect($DB_HOST1, $DB_USER1, $DB_PASS1) or die("<div>MySQL Error: Oops! UNABLE to CONNECT to the DATABASE!</div>");
mysqli_select_db($conn1, $DB_NAME1) or die("<div>MYSQL ERROR: Oops! Database access FAILED!</div>");
mysqli_set_charset($conn1, 'utf8') or die("<div>UNABLE to SET database connection ENCODING!</div>");

//echo 'con-1--PHP_MAJOR_VERSION='.PHP_MAJOR_VERSION.'<br/>'; //die( var_dump([$con,$conn1]) );

// echo "<pre>"; var_dump($mysqliCon); die();
?>