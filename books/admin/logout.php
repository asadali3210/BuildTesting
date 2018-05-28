<?php 
	session_start() ; 
	if ( isset($_SESSION['uid']) ) { 
		unset($_SESSION['uid'] ) ; 
		
	} 
	//now redirect user to login page 
	header( 'location: login.php' ) ; 
?>