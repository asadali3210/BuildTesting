<?php 

	// php database connection file 
	
	$link = new mysqli ( "<DATBASE_SERVER_URL>" , "<MYSQL_USERNAME>" , "<USER_PASSWORD>" , "<DATABASE_NAME>" ) ; 
	if (mysqli_connect_errno()) {
		printf("Database Connection failed: %s\n", mysqli_connect_error());
		exit();
	} 
	


?>