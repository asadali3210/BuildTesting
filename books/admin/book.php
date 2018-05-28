<?php 
	
	//website: http://www.roigap.com 
	
	session_start() ; 
	if( !isset($_SESSION['uid']) ) { 
		header('location: login.php') ; 
	}  
	
	/* --- include database file --- */ 
	require_once('../includes/dbconnect.php' ) ;  
	
	$pageTitle = "Add New Book" ;  
	$isError = 0 ; 
	$errorMessage = "" ; 
	$isSuccess = 1 ; 
	$successMessage = "" ;   
	$bookname = "" ;  
	
	if ( $_SERVER['REQUEST_METHOD'] == "POST" ) { 
		if ( isset($_POST['inputAddBook']) ) { 
			// add book name form submitted  
			if ( isset($_POST['inputBName']) ) { 
				$bookname = trim($_POST['inputBName'],' ' ) ;  
			} 
			else { 
				$isError = 1 ; 
				$errorMessage = "Please Enter A Book Name" ; 
			}  
			if ( $isError != 1 ) { 
				$author = $_SESSION['uid'] ; 
				$query = "INSERT INTO books ( bName, bAuthor, bIsActive, bIsDeleted, bAddedOn, bUpdatedOn ) VALUES ( '$bookname', $author, 1, 0,  NOW(), NOW() ) ; " ; 
				$resultSet = mysqli_query($link, $query ) ;  
				if ( $resultSet == 1 ) { 
					//$isSuccess = 1 ; 
					//$successMessage = "New Book Has Been Added Successfully." ; 
					header('location: viewbooks.php?done=1') ; 
				} 
				else { 
					$isError = 1 ; 
					$errorMessage = "An Error Occured. Please Try Again." ; 
				}
			} 
				
		} 
	} 
	
	/* --- include header file --- */ 
	require_once('header.php' ) ;  	
?> 


<h2> Add New Book </h2>

<div style="text-align:center;margin:0 auto; width:300px; " > 

	<div class="err"  > 
		<?php 
			if ( $isError == 1 ) { 
				echo $errorMessage ; 
			} 
		?> 
	</div> 

	<div class="succ"  > 
		<?php 
			if ( $isSuccess == 1 ) { 
				echo $successMessage ; 
			} 
		?> 
	</div> 
	<form name="frmAddBook" id="frmAddBook" action="" method="post" >  
		<div class="width_30" > 
			<div class="pull_left" > Book Title: </div> 
			<div class="pull_right" > 
				<input type="text" name="inputBName" id="inputBName" value="<?php echo $bookname;?>" class="" > 
			</div> 
		</div>  
		<div class="clear"> &nbsp; </div>  
		<div  class="width_30" > 
			<div class="pull_left" style="width:50%;"  >  </div>  
			<div> 
			<input type="hidden" name="inputAddBook" value="1" > 
			<input type="submit" name="inputSubmit" value="Submit" class="pull_right"  >  
			</div> 
		</div>  
		<div class="clear"> &nbsp; </div> 
	</form>  
</div> 
<br> 
<br> 

<hr>



</body>
</html> 