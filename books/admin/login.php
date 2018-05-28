<?php 
	session_start() ;  
	if ( isset($_SESSION['uid']) ) { 
		header('location: index.php' ) ; 
	} 
	
	/* --- include database file --- */ 
	require_once('../includes/dbconnect.php' ) ;  
	
	$uname = "" ; 
	$upassword = "" ; 
	$isError = 0  ; 
	$errorMessage = "" ; 
	
	if ( $_SERVER['REQUEST_METHOD']  == "POST" ) { 
		
		// a form has been submitted 
		if ( isset($_POST['inputULogin']) ) { 
			
			// a user login request 
			if ( isset($_POST['inputUName']) && strlen($_POST['inputUName']) != 0  ) { 
				$name = $_POST['inputUName'] ; 
				$uname = mysqli_escape_string( $link , $name ) ; 
			} 
			else { 
				$isError = 1 ; 
				$errorMessage = " Please Fill Username <br> " ; 
			} 
			if ( isset($_POST['inputUPassword'])  && strlen($_POST['inputUPassword']) != 0  ) { 
				$pass = $_POST['inputUPassword'] ; 
				$upassword = mysqli_escape_string( $link , $pass ) ; 
				
			} 
			else { 
				$isError = 1 ; 
				$errorMessage .= " Please Fill Password  <br> " ; 
			} 
			
			if ( $isError != 1 ) { 
				
				// validate user now 
				$query = " SELECT uID FROM users WHERE uUsername = '$uname' AND uPassword = '$upassword' AND uIsActive = 1 AND uIsDeleted = 0 ; " ; 
				$resultSet = mysqli_query( $link, $query ) ; 
				if ( !$resultSet ) { 
					$isError = 1 ; 
					$errorMessage = " An Error Has Occured. Please Try Again. " ; 
				} 
				else { 
					if ( mysqli_num_rows( $resultSet) <= 0 ) { 
						$isError = 1 ; 
						$errorMessage = "<br> Invalid Username or Password. " ; 
					} 
					else { 
						while ( $row = mysqli_fetch_assoc($resultSet) ) { 
							$_SESSION['uid'] = $row['uID'] ; 
							// now redirect user to home page 
							header( 'location: index.php' ) ; 
						} 
					} 
				} 
			} 
			
			
		} 
	} 
?> 

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
<link rel="stylesheet" type="text/css" href="../css/style.css" > 
<title> Log In </title>
</head>

<body> 
	<div> 
		<div class="error_message" >  
			<div class="error_style" style="" > 
			<?php 
				if ( $isError == 1 ) { 
					echo $errorMessage ; 
				} 
			?> 
			</div> 
		</div>  
		<div style="text-align:center; width: 30%; margin:0 auto; padding:5px;  " > 
			<form name="frmLogin" id="frmLogin" action="" method="post" > 
				<div class="pull_left" > Username: </div> 
				<div> 
					<input type="text" name="inputUName" id="inputUName" value="<?php echo $uname;?>" > 
				</div> 
				<div class="clear" > &nbsp; </div> 
				<div  class="pull_left" > Password: </div> 
				<div> 
					<input type="password" name="inputUPassword" id="inputUPassword" value="<?php echo $upassword;?>" > 
				</div> 
				<div class="clear" > &nbsp; </div> 				
				<div> 
					<input type="hidden" name="inputULogin" id="inputULogin" value="1" > 
					<input type="submit" name="inputSubmit" id="inputSubmit" value="Log In" > 
				</div> 
			</form>   
		</div> 
	</div> 
</body>
</html>
