<?php 
	$pageTitle = "View Book" ;  
	/* --- include database and header file --- */ 
	require_once('includes/dbconnect.php' ) ;  
	$bId = 0 ;  
	if ( isset($_REQUEST['bId']) ) { 
		$bId = intval(trim($_REQUEST['bId'])) ;  
		$query = "SELECT pText FROM pages WHERE pbID = $bId ORDER BY pNumber ASC  ; " ; 
		$resultSetPages = mysqli_query($link, $query) ;  
		
	}
	require_once('includes/header.php'); 
?> 
<div id="magazine">
	<?php 
		if ( $resultSetPages ) { 
			if ( mysqli_num_rows($resultSetPages) > 0 ) { 
				while( $row= mysqli_fetch_assoc($resultSetPages) ) { 
					echo "<div><div style='padding-top:30px;padding-bottom:30px;margin:0 75px;'>" ; 
					echo $row['pText'] ;  
					echo "</div></div>" ;  		
				} 
			} 
		} 
	?> 
</div>


<script type="text/javascript">

	$(window).ready(function() {
		$('#magazine').turn({
							display: 'double',
							acceleration: true,
							gradients: !$.isTouch,
							elevation:50,
							when: {
								turned: function(e, page) {
									/*console.log('Current view: ', $(this).turn('view'));*/
								}
							}
						});
	});
	
	
	$(window).bind('keydown', function(e){
		
		if (e.keyCode==37)
			$('#magazine').turn('previous');
		else if (e.keyCode==39)
			$('#magazine').turn('next');
			
	});

</script> 
<style type="text/css">
body{
	/*background:#ccc;*/
}
#magazine{
	/*width:1152px;*/ 
	/*width:1162px;  */ 
	height:500px;
}
#magazine .turn-page{
	background-color:#ccc;
	background-size:100% 100%; 
	/*border: 1px solid #000;  
    border-radius: 3px;  
	padding: 20px 18px ;  
	padding-top:50px; */
} 
.p1 { 
	/*background-color:olive !important ;*/ 
} 
.bookpage { 
	background:url(images/book-leaf.png); 
} 
</style> 

</body>
</html>
