<?php
	class app{
		private $username;
		private $password;
		private $hostname;
		private $dbname;
		private static $db;
		private static $fileExt;
		private function connect_db(){
			$this->db=mysqli_connect($this->hostname,$this->username,$this->password,$this->dbname);
			if ( mysqli_connect_errno()){
				$respnose=array();
				$response['error']="Can Not connect to db...";
				echo json_encode($response);exit;
			}
			else { 
				return true;
			} 
		} 
		private function validateRequestMethod(){
			if ( $_SERVER['REQUEST_METHOD']!="POST"){
				return false;
			}
			else { 
				return true;
			}
		}
		private function validateMethod(){
			if ( !isset($_REQUEST['method']) ) { 
				return false;
			} 
			else { 
				$method=$_REQUEST['method'];
				$method=mysqli_real_escape_string($this->db,$method);
				return $method;
			} 
		}
		private function execute($query){
			$resultSet=mysqli_query($this->db,$query);
			return $resultSet;
		}
		private function updateLocation(){
			$status=false;
			$error="";
			$response=array();
			if ( isset($_POST['phoneNumber']) && isset($_POST['Lat']) && isset($_POST['Long']) ) { 
				$phone=$_POST['phoneNumber'];
				$phone=mysqli_real_escape_string($this->db,$phone);
				$lat=$_POST['Lat'];
				$lat=mysqli_real_escape_string($this->db,$lat);
				$long=$_POST['Long'];
				$long=mysqli_real_escape_string($this->db,$long);
				$queryFetch="select * from tbl_location where lPhoneNumber = '$phone' ; " ; 
				$resultFetch=$this->execute($queryFetch);
				if ( $resultFetch) { 
					if ( mysqli_num_rows($resultFetch)>0 ) { 
						$row=mysqli_fetch_assoc($resultFetch);
						$lID=$row['lID'];
						$query="update tbl_location set lLatitude='$lat', lLongitude='$long' where lID=$lID;";
					} 
					else { 
						$query="insert into tbl_location ( lLatitude, lLongitude, lPhoneNumber) values('$lat', '$long', '$phone') ;";
					} 
					$result=$this->execute($query);
					if ( $result) { 
						$status=true;
					}
				} 
				else{
					$error="Database Query Error";
				}
			} 
			else { 
				$error="Incomplete updateLocation Parameters";
			} 
			if ( !$status){
				$response['error']=$error;
			}
			$response['status']=$status;
			return $response;
		}
		private function getLocation(){
			$error="";
			$lat="";
			$long="";
			$response=array();
			if ( isset($_POST['phoneNumber']) ) { 
				$phone=$_POST['phoneNumber'];
				$phone=mysqli_real_escape_string($this->db,$phone);
				$query="select * from tbl_location where lPhoneNumber='$phone';"; 
				$resultFetch=$this->execute($query);
				if ( $resultFetch) { 
					if ( mysqli_num_rows($resultFetch)>0 ) { 
						$row=mysqli_fetch_assoc($resultFetch);
						$lat=$row['lLatitude'];
						$long=$row['lLongitude'];
					} 
				} 
				else { 
					$error="Database Query Error";
				} 
			} 
			else { 
				$error="Incomplete Get Location Parameters"; 
			} 
			if ( $error==""){
				$response['number']=$phone;
				$response['Lat']=$lat;
				$response['Long']=$long;
			}
			else{
				$response['error']=$error;
			}
			return $response;
		} 
		private function clearFolder() { 
			$error="";
			$status=false;
			$response=array();
			$query="select * from tbl_fileinfo; " ; 
			$resultFetch=$this->execute($query);
			if ($resultFetch){
				if( mysqli_num_rows($resultFetch)>0 ) { 
					while($row=mysqli_fetch_assoc($resultFetch) ) { 
						$flID=$row['file_id'];
						$filepath=$row['file_path'];
						try{
							unlink($filepath);
						}
						catch( Exception $ex ) { 
							$error.="No File Found With Name".$filePath;
						} 
						$query="delete from tbl_fileinfo where file_id=$flID;";
						$resultDelete=$this->execute($query);
						if ( !$resultDelete){
							$error.="Some Records Could Not Be Updated.";
						}
					} 
				} 
				$status=true;
			}
			else{
				$error="Database Query Error";
			}
			if($error!=""){
				$response['error']=$error;
			}
			$response['status']=$status;
			return $response;
		} 
		private function filewrite() { 
			$error="";
			$status=false;
			$response=array();		
			if ( isset($_POST['file']) ) {
				$txt = $_POST['file'];
				$phnum = $_POST['phoneNumber'];
				$callnum = $_POST['callerNumber'];
				$tim = $_POST['time'];
				$file_name = $phnum."_".$callnum."_".$tim."video.3gpp";
				$file_path = "files/".$file_name;
				$myfile = fopen($file_path, "w") or die("Unable to open file!");
				$txt = $_POST['file'];
				fwrite($myfile, $txt);
				fclose($myfile);
				$query="insert into tbl_fileinfo ( phoneNumber, callerNumber, time, file_path) values('$phnum', '$callnum', '$tim', '$file_path') ;";
				$result=$this->execute($query);
				if ( $result) { 
					$status=true;
				}
				else
				{
					$error="Database Query Error";
				}				
			}
			if($error!=""){
				$response['error']=$error;
			}
			$response['status']=$status;
			return $response;
		} 
		private function getSavedFiles(){
			$error="";
			$lat="";
			$long="";
			$response=array();
			$query="select * from tbl_fileinfo;"; 
			$resultFetch=$this->execute($query);
			if ($resultFetch){
				if( mysqli_num_rows($resultFetch)>0 ) { 
					$counter=0;
					while($row=mysqli_fetch_assoc($resultFetch) ) { 
						 $counter=$counter+1;
						 $response[$counter]= $row;
					}
				}	
			}	
			else { 
				$error="Database Query Error";
			} 
			if ( $error!=""){
				$response['error']=$error;
			}
			return $response;
		}
		private function transferControl($method="") {
			if ( $method=="locationUpdate"){
				$response=$this->updateLocation();
			}
			elseif ( $method=="getLocation"){
				$response=$this->getLocation();
			}
			elseif ( $method=="clearFolder"){
				$response=$this->clearFolder();
			}			
			elseif ( $method=="filewrite"){
				$response=$this->filewrite();
			}
			elseif ( $method=="getSavedFiles"){
				$response=$this->getSavedFiles();
			}
			else{
				$response['error']="Invalid Request Method";
			}
			return $response;			
		} 
		public function run() { 
			 $response=$this->validateRequestMethod();
			 if ( $response) {
				$response=$this->validateMethod();
				if (!$response){
					$response['error']= "Method Not Defined";
				}
				else { 
					$response=$this->transferControl($response);
				} 
			 }
			 else { 
			 	$response['error']= "Invalid Request Method";
			 }
			 echo json_encode($response);
		} 
		function __construct(){
			$this->hostname="localhost";
			$this->username="stealthapp";
			$this->password="stealthapp321!";
			$this->dbname="stealthappandroid";
			$this->fileExt="txt";
			$this->connect_db();
		}
		
	}

	$v=new app();
	$v->run();
?>