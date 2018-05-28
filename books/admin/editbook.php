<?php 
	
	//website: http://www.roigap.com 
	
	session_start() ; 
	if( !isset($_SESSION['uid']) ) { 
		header('location: login.php') ; 
	}  
	$pageTitle = "Edit Book" ;  
	$bId = 0 ; 
	$bookname = "" ; 
	$pagetext = "" ; 
	$pageNum = 0 ;  
	$totalPages = 0 ;  
	$formType = "Add" ; 
	$pageStatus = 'notfound' ;    
	/* --- include database and header file --- */ 
	require_once('../includes/dbconnect.php' ) ; 
	require_once('header.php' ) ;  	    
	if ( $_SERVER['REQUEST_METHOD'] == "POST" &&  isset($_REQUEST['bId']) ) { 
		$bId = intval($_REQUEST['bId']) ;  
		//var_dump ($_POST); 
		if ( isset($_POST['inputAddPage']) ||  isset($_POST['inputUpdatePage']) ) { 
			if ( isset($_POST['inputtextarea']) ) { 
				$pagetext = $_POST['inputtextarea'] ; 
			} 
			if ( isset( $_POST['inputPageNum']) ) { 
				if ( strlen($_POST['inputPageNum']) != 0 ) { 
					$pageNum = intval($_POST['inputPageNum']) ; 
				} 
				else { 
					$isError = 1 ; 
					$errorMessage = " <br> Please Select a Page Number For This Page. Can Not Save As An Anonymous Page." ; 
				} 
			} 
			else { 
				$isError = 1 ; 
				$errorMessage = " <br> Please Select a Page Number For This Page. Can Not Save As An Anonymous Page." ; 
			} 
		
			if ( $isError != 1 ) { 
				if ( isset($_POST['inputAddPage']) ) { 
					//save this page to database... 
					$query = "INSERT INTO pages ( pNumber, pText, pbID ) VALUES ( $pageNum, '$pagetext', $bId ) ; " ;  
					$resultSet = mysqli_query( $link, $query ) ; 
					if ( $resultSet ) { 
						$isSuccess = 1 ; 
						$successMessage = "<br> Page Has Been Saved Successfully." ; 
						//clear the page text 
						$pagetext = "" ;  
					} 
					else { 
						$isError = 1 ; 
						$errorMessage = " <br> An Error Has Occured. Please Try Again.  " ; 
					} 
				} 
				else if ( isset($_POST['inputUpdatePage']) ) { 
					//save this page to database... 
					$query = "UPDATE pages SET pText = '$pagetext' WHERE pNumber = $pageNum AND pbID = $bId  ; " ;  
					//var_dump($query) ; 
					$resultSet = mysqli_query( $link, $query ) ; 
					if ( $resultSet ) { 
						$isSuccess = 1 ; 
						$successMessage = "<br> Page Has Been Saved Successfully." ; 
						// 
						$pageStatus = 'found' ; 
						$formType = 'Update' ; 
					} 
					else { 
						$isError = 1 ; 
						$errorMessage = " <br> An Error Has Occured. Please Try Again.  " ; 
					} 
				} 
				//else update request  
			} 
		} 
		if ( isset($_POST['inputSelectPage']) ) {  
			
			if ( isset($_POST['inputPageNum']) ) { 
				$pageNum = intval($_POST['inputPageNum']) ;  
				//fetch this page from database 
				$query = "SELECT * FROM pages WHERE pbID = $bId AND pNumber = $pageNum LIMIT 0,1 ; " ;  
				$resultSet = mysqli_query($link, $query) ; 
				if ( $resultSet) { 
					if ( mysqli_num_rows( $resultSet) > 0 ) { 
						$row = mysqli_fetch_assoc($resultSet) ; 
						$pagetext=$row['pText'] ; 
						$pageStatus = 'found' ;  
						$formType = "Update" ;    
					} 
				} 
				else { 
					$isError = 1 ; 
					$errorMessage = " <br> An Error Has Occured. Please Try Again. " ; 
				} 
			} 
		} 
	}  
	if ( isset($_REQUEST['bId']) ) {  
		$bId = intval($_REQUEST['bId']) ; 
		// get book detail from datbase 
		$query = "SELECT * FROM books WHERE bId = $bId ;  " ;  
		$resultSet = mysqli_query( $link, $query ) ; 
		if ( $resultSet ) { 
			if ( mysqli_num_rows($resultSet) > 0 ) { 
				$row = mysqli_fetch_assoc($resultSet) ; 
				$bookname = $row['bName'] ; 
			} 
		} 
		if ( $pageStatus == 'notfound' ) { 
			echo "here"; 
			//get last page num for this book 
			$query = "SELECT MAX(pNumber) as lastPage FROM pages WHERE pbID = $bId ; " ; 
			$resultSet = mysqli_query($link, $query) ; 
			if ( $resultSet ) { 
				if ( mysqli_num_rows( $resultSet) > 0 ) { 
					$row = mysqli_fetch_assoc( $resultSet) ; 			
					$pageNum = intval($row['lastPage']) + 1  ; 
				} 
				else { 
					$pageNum = 1 ;  
				} 
			} 
			else { 
				$pageNum = 1 ; 
			}   
		} 
		//get total pages count   
		$query = "SELECT COUNT(pID) as totalPages FROM pages WHERE pbID = $bId  ; " ;  
		$resultSet = mysqli_query($link, $query) ; 
		if ( $resultSet ) { 
			if ( mysqli_num_rows($resultSet) > 0  ) { 
				$row = mysqli_fetch_assoc($resultSet) ;  
				$totalPages = intval($row['totalPages']) ; 
			} 
		} 
		//get all pages for drop down 
		$query = "SELECT pNumber FROM pages WHERE pbID = $bId ORDER BY pNumber ASC ; " ;  
		$resultSetPages = mysqli_query( $link, $query ) ;  
		//var_dump( $query ) ; 
	} 	
	
	if( $pageNum != 0 ) { 
		// show the page to user 
		$query = " SELECT * FROM pages WHERE pbID = $bId AND pNumber = $pageNum LIMIT 0,1 ; " ;  
		$resultSet = mysqli_query($link, $query) ; 
		if ( $resultSet) { 
			if ( mysqli_num_rows($resultSet) > 0 ) { 
				$row = mysqli_fetch_assoc($resultSet); 
				$pagetext = $row['pText']; 
			} 
		} 
	} 	
	
	
?> 


<h2> Edit Book (<?php echo $bookname;?>) :  </h2>   
<div> 
<span class="pull_left " > Goto Page: </span> 
<?php 
if ( $resultSetPages ) { 
	if ( mysqli_num_rows($resultSetPages) > 0 ) {  
?> 
	<form name="frmSelectPage" id="frmSelectPage" action="editbook.php?bId=<?php echo $bId;?>" method="post" > 
		<select name="inputPageNum" id="inputPageNum" onchange="javascript: form.submit();" > 
			<?php 
			echo "<option value='' selected='selected'>---</optoin>";  
			while( $row = mysqli_fetch_assoc($resultSetPages) ) { 
				echo "<option value='".$row['pNumber']."'" ; 
				if ( $pageNum == $row['pNumber'] ) 
				echo "selected='selected'" ;  
				echo ">" .$row['pNumber']. "</option>" ; 
			} 	
			?> 	
		</select> 
		<input type="hidden" name="inputSelectPage" id="inputSelectPage" value="1"  />  
	</form>  
	<a href='editbook.php?bId=<?php echo $bId;?>' style="color:#658700;  " > New Page</a>  
<?php 
	} 
} 
?>  
</div> 
<div style="" > 

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
	
	<div> 
		
		<div class="float:left;"> 
			<!------------------------------------------------------------ Toggle jQTE Button ------------------------------------------------------------>
			<button class="status" style="float:left;"  > Toggle Editor  </button>
			<!------------------------------------------------------------ jQUERY TEXT EDITOR ------------------------------------------------------------>
			<button class="btnSave" style="float:right;" > Save  </button> 
		</div> 
		<div class="clear" > &nbsp; </div>
		<div class="" > 
			<?php 
				//if ( $pageStatus == 'notfound' ) { 
					//echo '<div class="pull_left" > New Page </div>  ' ;
				//} 
			?> 
			<div class="pull_right" >  
				<?php 
					if ( $pageStatus == 'notfound' ) { 
						echo "New Page: ".$pageNum; 
					} 
					else { 
						echo "Page ".$pageNum ; 
						if ( $totalPages > 0 ) 
							echo " of ".$totalPages;  
					} 
				?> 
			</div>  
		</div>  
		<div class="clear" > &nbsp; </div>  
		<form name="frmPage" id="frmPage" action="" method="post" > 
		<textarea name="inputtextarea" class="jqte-test" maxRows="4" maxChars="200"><?php echo $pagetext;?></textarea>  
		<input type="hidden" name="inputPageNum" id="inputPageNum" value="<?php echo $pageNum;?>"  /> 
		<input type="hidden" name="input<?php echo $formType;?>Page" id="input<?php echo $formType;?>Page" value="1"  />  
		</form> 
		<!--<input name="input" type="text" value="<b>My contents are from <u><span style=&quot;color:rgb(0, 148, 133);&quot;>INPUT</span></u></b>" class="jqte-test">-->
		
		<!--<span name="span" class="jqte-test"><b>My contents are from <u><span style="color:rgb(0, 148, 133);">SPAN</span></u></b></span>-->
		
		<script>
			$('.jqte-test').jqte();
			
			// settings of status
			var jqteStatus = true;
			$(".status").click(function()
			{
				jqteStatus = jqteStatus ? false : true;
				$('.jqte-test').jqte({"status" : jqteStatus})
			});
		</script> 
		
		<!------------------------------------------------------------ jQUERY TEXT EDITOR ------------------------------------------------------------>

		
	</div> 
	
	  
</div> 
<br> 
<br> 

<hr>

<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="../css/shCore.css">
	<link rel="stylesheet" type="text/css" href="../css/demo.css">
	<style type="text/css" class="init"></style> 
	<script type="text/javascript" language="javascript" src="../js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="../js/shCore.js"></script>
	<script type="text/javascript" language="javascript" src="../js/demo.js"></script>
	<script type="text/javascript" language="javascript" class="init">
		$(document).ready(function() {
			$('#books').dataTable(); 
			$(".btnSave").click( function() { 
				$(".btnSave").attr('disabled','disabled'); 
				$(".status").attr('disabled','disabled'); 
				$('#frmPage').submit() ;  
				
			} ) ; 
			
		} );
	</script>

</body>
</html> 