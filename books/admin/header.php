<!DOCTYPE html> 
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title> <?php echo $pageTitle;	?> | Admin </title>

<link type="text/css" rel="stylesheet" href="../css/style.css">
<link type="text/css" rel="stylesheet" href="../css/jquery-te-1.4.0.css">

<script type="text/javascript" src="../js/jquery.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery-te-1.4.0.min.js" charset="utf-8"></script>
</head>

<body>
<h1>Administration Area! </h1>

<div class="navigation">
<a href="index.php" >Home</a>
<a href="book.php" > Create Book  </a>
<a href="viewbooks.php" > View All Books </a> 
<a href="logout.php" > Log Out </a> 

</div> 
<?php 
	$isError = 0 ; 
	$errorMessage = "" ; 
	$isSuccess = 0 ; 
	$successMessage = "" ; 
?>