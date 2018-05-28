<?php 
	$pageTitle = "Home" ;  
	/* --- include database and header file --- */ 
	require_once('includes/dbconnect.php' ) ;  

	$query = "SELECT bName, bID FROM books WHERE bIsDeleted = 0 AND bIsActive = 1 ; " ; 
	$resultSetBooks = mysqli_query($link, $query) ;  
	
	require_once('includes/header.php'); 
?> 

	<div> 
		<h3 style="text-align:center;" > Welcome to Ebooks! </h3> 
		<div> 
			<table id="books" class="display" cellspacing="0" width="60%" style="text-align:center;">
				<thead>
					<tr>
						<th>Book Name</th>
						<th>Action</th>  
					</tr>
				</thead> 
				<tbody> 
					<?php 
						if ( $resultSetBooks  ) {    
							if ( mysqli_num_rows($resultSetBooks) > 0 ) {  
								while( $row = mysqli_fetch_assoc($resultSetBooks) ) { 
									echo "<tr>" ; 
									echo "<td>" ; 
									echo $row['bName'] ;  
									echo "</td>" ; 									
									echo "<td>" ; 
									echo "<a href='viewbook.php?bId=".$row['bID']."' target='_blank'> Read  </a>&nbsp;" ; 
									echo "<a href='javascript:void(0)' > Download  </a>" ; 
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

	<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="css/shCore.css">
	<link rel="stylesheet" type="text/css" href="css/demo.css">
	<style type="text/css" class="init"></style> 
	<style> 
		body { 
			font-family:"Times New Roman", Times, serif !important;
		} 
	</style>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="js/shCore.js"></script>
	<script type="text/javascript" language="javascript" src="js/demo.js"></script>
	<script type="text/javascript" language="javascript" class="init">
		$(document).ready(function() {
			$('#books').dataTable();
		} );
	</script>
    
</body>
</html>
