<?php
	$response=array();
	$folder='folders';
	$requestMethod="POST";
	$host=$_SERVER['HTTP_HOST'];
	$thisPath=realpath('.');

	$folderPath=$thisPath."/".$folder;
	if ( !file_exists($folderPath) ) { 
		mkdir($folderPath);
	} 
	$folders=array( "1"=>"1","2"=>"2", "3"=>"3", "4"=>"4", "5"=>"5" ) ; 
	foreach($folders as $key=>$value) { 
		$imageFolderPath=$folderPath."/".$value;
		if ( !file_exists($imageFolderPath) ) { 
			mkdir($imageFolderPath);
		} 
	} 
	if ( $_SERVER['REQUEST_METHOD']!=$requestMethod) { 
		$response['error']="Invalid Request Method. ".$requestMethod." required" ;
	} 
	else { 
		if ( isset($_POST['folder']) ) { 
			$requiredFolder=trim($_POST['folder']);
			$requiredFolderPath=$folderPath."/".$requiredFolder;
			if ( file_exists($requiredFolderPath) ) { 
				$contentArray=scandir($requiredFolderPath);
				for ( $i=2; $i<sizeof($contentArray); $i++) { 
					$file=$contentArray[$i];
					$fileLocation=$host."/".$folder."/".$requiredFolder."/".$file;
					$response[]=$fileLocation;
				} 
			} 
			else { 
				$response['error']="Folder Not Found.";
			} 
		} 
		else { 
			$response['error']="Folder Name is Required.";
		} 
	} 
    $res=array();
    $res['result']=$response;
    $res=json_encode($res);
	$res="{".substr($res,1,strlen($res)-2)."}";
        echo $res;

/*<form name="frmFolderName" action="#" method="post">

	<input type="text" name="folder" value="1">
	<input type="submit" name="submit" value="submit">
</form>*/
?>