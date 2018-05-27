<?php 
	$response = array() ; 
	if ( $_SERVER['REQUEST_METHOD'] != "GET" ) { 
		$response["Error"] = "Invalid Request Type "  ;  
	} 
	else { 
		// otherwise, continue 
		$link = mysqli_connect ( "<DATABASE_SERVER_URL>", "<DATABASE_NAME>" , "<DATABASE_USER_NAME>" , "<DATABASE_PASSWORD>" ) ;   
		if ( mysqli_connect_error() ) { 
			$response["Error"] = "Cannot Connect To Database"  ;  
		} 
		else {  
			$method = "" ; 
			if ( !isset($_REQUEST['method']) ) { 
				$response["Error"] = "Request Method Undefined"  ; 
			} 
			else { 
				if ( $_REQUEST['method'] == 'validate' || $_REQUEST['method'] == 'fetch' || $_REQUEST['method'] == 'update' ) { 
					if ( $_REQUEST['method'] == 'validate' ) { 
						//verify required parameters 
						if ( !isset($_REQUEST['username']) ||  !isset($_REQUEST['password']) ) { 
							$response["Error"] = "Incomplete Parameters"  ; 						
						} 
						else { 
							$username = trim($_REQUEST['username'],' ') ; 
							$password = $_REQUEST['password'] ;   
							$response["Success"] = "0"  ;  
							//validate user now 
							$query = "SELECT count(uID) as count FROM users WHERE uUsername = '$username' AND uPassword = '$password' ; " ;   
							$resultSet = mysqli_query( $link, $query) ; 
							if ( $resultSet ) { 
								if ( mysqli_num_rows($resultSet) > 0 )  { 
									$row = mysqli_fetch_assoc($resultSet) ; 
									if ( $row['count'] > 0 ) { 
										$response["Success"] = "1"  ;  
									}  
								} 
							} 
						} 
					}  
					else if ( $_REQUEST['method'] == 'fetch' ) {   
						$allWords = array() ; 
						$query = "SELECT wWord as Word, wDefinition as Definition FROM words ; " ;  
						$resultSet = mysqli_query( $link , $query ) ; 
						if ( $resultSet ) { 
							if ( mysqli_num_rows($resultSet) > 0 ) { 
								while ( $row = mysqli_fetch_assoc($resultSet) ) { 
									// add all words to an associative array    
									$allWords[$row['Word']] = $row['Definition'] ; 
								} 
							} 
						}   
						$response["Words"] = $allWords  ;    
					}  
					else if ( $_REQUEST['method'] == 'update' ) { 
						//verify required parameters 
						if ( !isset($_REQUEST['score']) ||  !isset($_REQUEST['username']) || strlen(trim($_REQUEST['username'],' ')) == 0  ) { 
							$response["Error"] = "Incomplete Parameters"  ; 						
						} 
						else { 
							$score = intval(trim($_REQUEST['score'],' ') ) ; 
							if ( $score < 0 ) 
								$score = -($score) ; 
							$username = trim($_REQUEST['username'],' ')  ;  
							$response["Success"] = "0"  ;    
							//update the score in database 
							$query = "UPDATE users SET uScore = uScore + $score WHERE uUsername = '$username' ; " ; 
							$resultSet = mysqli_query( $link , $query ) ;  
							if ( $resultSet ) { 
								$response["Success"] = "1"  ;  
							} 
							else { 
								$response["Success"] = "0"  ;  
							}    
						} 
					}
				}  
				else if ( $_REQUEST['method'] == 'signup' ) { 
					//add new user to database 
					if ( !isset($_REQUEST['username']) || !isset($_REQUEST['password'])  ) { 
						$response["Error"] = "Incomplete Parameters"  ;  
					} 
					else { 
						$username = $_REQUEST['username'] ; 
						$password = $_REQUEST['password'] ; 
						// first check if this username already exists or not. 
						$query = "SELECT count(uID) as count  FROM users WHERE uUsername = '$username' ; " ; 
						$resultSet = mysqli_query($link, $query ) ; 
						if ( $resultSet ) { 
							$row = mysqli_fetch_assoc($resultSet) ; 
							if ( $row['count'] > 0 ) { 
								$response["Error"] = "Username Already Exists" ; 
							} 
							else { 
								$query =  "INSERT INTO users ( uUsername, uPassword ) VALUES ( '$username' , '$password' ) ; " ;  
								$resultSet = mysqli_query($link, $query ) ; 
								if ( $resultSet ) { 
									$response['Success'] = "1" ; 
								} 
								else { 
									$response["Error"] = "Could Not Add User. " ; 
								} 							
							} 
						} 
						else { 
							$response["Error"] = "Could Not Add User. " ; 
						} 
						
					} 
				} 
				else { 
					$response["Error"] = "Invalid Request Method"  ; 
				} 
			}  
		} 
	} 	

	/*  ------- encode response to json and print ----------  */ 
	echo json_encode( $response ) ; 
?>