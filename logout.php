<?php 
include ("main.php"); 
	$_SESSION = array();
	
	// Finally, destroy the session.
	session_destroy();
	
	header("Location: index2.php");
	
?>
