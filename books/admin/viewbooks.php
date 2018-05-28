<?php 

	//website: http://www.roigap.com 
	
	session_start() ; 
	if( !isset($_SESSION['uid']) ) { 
		header('location: login.php') ; 
	}  
	$pageTitle = "All Book" ;  
	/* --- include database and header file --- */ 
	require_once('../includes/dbconnect.php' ) ;  
	require_once('header.php' ) ;  	 
	$query = "SELECT * FROM books ; " ; 
	$resultSet = mysqli_query( $link, $query ) ; 
	
	if ( isset($_REQUEST['done']) ) { 
		if ( $_REQUEST['done'] == 1 ) { 
			$isSuccess = 1 ; 
			$successMessage = "New Book Has Been Added Successfully." ; 
		}  
	} 
	
?> 


<h2> All Books  </h2>

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
		<table id="books" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Book Name</th>
						<th>Book Active</th>
						<th>Added On </th>
						<th>Updated On </th> 
						<th>Edit</th>  
					</tr>
				</thead>
				<!--<tfoot>
					<tr>
						<th>Book Name</th>
						<th>Book Active</th>
						<th>Added On </th>
						<th>Updated On </th> 
					</tr>
				</tfoot>--> 
				<tbody> 
					<?php 
						if ( $resultSet  ) {    
							if ( mysqli_num_rows($resultSet) > 0 ) {  
								$status = "" ; 
								while( $row = mysqli_fetch_assoc($resultSet) ) { 
									echo "<tr>" ; 
									echo "<td>" ; 
									echo $row['bName'] ;  
									echo "</td>" ; 
									echo "<td>" ; 
									if ( $row['bIsActive'] == 1 ) 
										$status = "YES" ; 
									else 
										$status = "NO" ; 
									echo $status; 
									echo "</td>" ; 
									echo "<td>" ; 
									echo $row['bAddedOn'] ; 
									echo "</td>" ; 
									echo "<td>" ; 
									echo $row['bUpdatedOn'] ; 
									echo "</td>" ; 
									echo "<td>" ; 
									echo "<a href='editbook.php?bId=".$row['bID']."' target='_blank'> Edit</a>" ; 
									echo "</td>" ; 
									echo "</tr>" ;  
								} 
							} 
						} 
					?>

				</tbody>
			</table>
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
		} );
	</script>

</body>
</html> 