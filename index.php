<?php    

	// Example URL  
	// http://<YOUR_SITE_URL>/android/index.php?appId=<APP_AUTO_GENERATED_ID>&appName=<YOUR_APP_NAME> 
	
	
	
	$host = "<DATABASE_SERVER_URL>";
	$database = "<DATABASE_NAME>";
	$user = "<DATABASE_USER_NAME>";
	$password = "<DATABASE_PASSWORD>"; 
	$response = false ; 
	
	$conn = mysqli_connect("$host", "$user", "$password","$database") ; 
	
	if( $conn ) { 

		$request = 0 ; 
		if ( $_SERVER['REQUEST_METHOD'] == "GET" )  { 
		
			//a get request  
			if ( isset ( $_REQUEST['appId'] ) &&  isset( $_REQUEST['appName'] )  )  { 
				
				$appId = mysqli_real_escape_string( $conn  , $_REQUEST['appId']  ) ; 
				$appName = mysqli_real_escape_string( $conn  , $_REQUEST['appName']  ) ; 
				
				$query = "select aIsLive from apps where aID = '$appId' and aName = '$appName' ; "  ;  
				$result = mysqli_query ( $conn, $query ) ; 
				
				if ( mysqli_num_rows($result ) > 0  )  { 
				
					$row = mysqli_fetch_array( $result ) ; 
	
					if ( $row['aIsLive']  )  			
						$response =  true ; 			
				} 
			}
		}  
		
	} 
	
	echo json_encode( array( "result" => $response )  )  ; 	
	mysqli_close($conn);
 
 ?> 
 
 