<?php 
	session_start() ; 
	if( !isset($_SESSION['uid']) ) { 
		header('location: login.php') ; 
	} 
	
	/* --- include header file --- */ 
	require_once('header.php' ) ;  	
?> 

<h2> Add New Book </h2>

<!------------------------------------------------------------ Toggle jQTE Button ------------------------------------------------------------>
<button class="status"> Toggle Editor  </button>

<!------------------------------------------------------------ jQUERY TEXT EDITOR ------------------------------------------------------------>

<textarea name="textarea" class="jqte-test"><b>My contents are from <u><span style="color:rgb(0, 148, 133);">TEXTAREA</span></u></b></textarea>

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


<hr>



</body>
</html>