<?php
	class app{
		private $username;
		private $password;
		private $hostname;
		private $dbname;
		private static $db;
		private static $fileExt;
		private static $folderLocation;
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
				$index=array();
				$index['number']=$phone;
				$index['Lat']=$lat;
				$index['Long']=$long;
				$response[]=$index;
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
			if ( isset($_POST['phoneNumber']) ) { 
				$phone=$_POST['phoneNumber'];
				$phone=mysqli_real_escape_string($this->db,$phone);
				$query="select * from tbl_fileinfo where phoneNumber='$phone'; " ; 
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
			} 
			else { 
				$error="Incomplete Parameters"; 
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
			if ( isset($_POST['phoneNumber']) && isset($_POST['callerNumber']) && isset($_POST['time']) && isset($_FILES['file']) && isset($_POST['type'])   ) {
				$phoneNumber = $_POST['phoneNumber'];
				$phoneNumber= mysqli_real_escape_string($this->db,$phoneNumber);
				$callerNumber = trim($_POST['callerNumber'],' ');
				$callerNumber= mysqli_real_escape_string($this->db,$callerNumber);
				$time = trim($_POST['time'],' ');
				$time= mysqli_real_escape_string($this->db,$time);
				$type = trim($_POST['type'],' ');
				$type= mysqli_real_escape_string($this->db,$type);
				if ( $_FILES['file']['error']<1 ) { 
					$filename=$_FILES['file']['name'];
					$fileUniqueName=$phoneNumber."_to_".$callerNumber."_".date('D-M-Y,H-i-s').".".$this->fileExt;
					$relativePath="/".$this->folderLocation."/".$fileUniqueName;
					$absolutePath=realpath(".").$relativePath;
					
					if( move_uploaded_file($_FILES['file']['tmp_name'],$absolutePath) ) { 
						$query="insert into tbl_fileinfo ( phoneNumber, callerNumber, time, file_path, type) values('$phoneNumber', '$callerNumber', '$time', '$relativePath', $type) ;";
						$result=$this->execute($query);
						if ( $result) { 
							$status=true;
						}
						else
						{
							$error="Database Query Error";
						}
					} 
					else { 
						$error="Could Not Save File";
					} 
				} 
				else{
					$error="File Error";
				}
			}
			else{
				$error="Required Parameters are missing";
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
						 
						 $response[]= $row;
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
		private function fetchStatus() { 
			$error="";
			$response=array();
			if ( isset($_POST['phoneNumber']) ) { 
				$phoneNumber = $_POST['phoneNumber'];
				$phoneNumber= mysqli_real_escape_string($this->db,$phoneNumber);
				$query="select * from tbl_status where sDeviceName='$phoneNumber';" ; 
				$resultFetch=$this->execute($query);
				if ($resultFetch){
					if( mysqli_num_rows($resultFetch)>0 ) { 
						$counter=0;
						$row=mysqli_fetch_assoc($resultFetch);
						$previousStatus=$row['sStatus'];		 
						if($previousStatus) { 
							$newStatus=0;
							$query="update tbl_status set  sStatus = $newStatus where sDeviceName = '$phoneNumber'  ; " ; 
							$resultAdd=$this->execute($query);
							if ( $resultAdd) { 
								/*$response['status']=0;*/
							}	
						}
						else{
							/*$response['status']=intval($row['sStatus']);*/
						} 
						$response['status']=intval($row['sStatus']);
					}	
					else { 
						$newStatus=0;
						$query="insert into tbl_status (sDeviceName, sStatus) values( '$phoneNumber', $newStatus ) ; " ; 
						$resultAdd=$this->execute($query);
						if ( $resultAdd) { 
							$response['status']=0;
						} 
					} 
				}	
				else { 
					$error="Database Query Error";
				} 
			} 
			else { 
				$error="Required Parameters are missing";
			} 
			if ( $error !="") { 
				$response['error']=$error;
			} 
			return $response;
		} 
		private function updateStatus() { 
			$error="";
			$response=array();
			if ( isset($_POST['phoneNumber']) ) { 
				$phoneNumber = $_POST['phoneNumber'];
				$phoneNumber= mysqli_real_escape_string($this->db,$phoneNumber);
				$query="select * from tbl_status where sDeviceName='$phoneNumber';" ; 
				$resultFetch=$this->execute($query);
				if ($resultFetch){
					if( mysqli_num_rows($resultFetch)>0 ) { 
						$counter=0;
						$row=mysqli_fetch_assoc($resultFetch);		 
						$previousStatus=$row['sStatus'];		 
						if(!$previousStatus) { 
							$newStatus=1;
							$query="update tbl_status set  sStatus = $newStatus where sDeviceName = '$phoneNumber'  ; " ; 
							$resultAdd=$this->execute($query);
							if ( $resultAdd) { 
								$response['status']=1;
							}	
						}
						else{
							$response['status']=intval($row['sStatus']);
						} 
					}	
					else { 
						$newStatus=1;
						$query="insert into tbl_status (sDeviceName, sStatus) values( '$phoneNumber', $newStatus ) ; " ; 
						$resultAdd=$this->execute($query);
						if ( $resultAdd) { 
							$response['status']=1;
						} 
					} 
				}	
				else { 
					$error="Database Query Error";
				} 
			} 
			else { 
				$error="Required Parameters are missing";
			} 
			if ( $error !="") { 
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
			elseif ( $method=="fetchStatus"){
				$response=$this->fetchStatus();
			}
			elseif ( $method=="updateStatus"){
				$response=$this->updateStatus();
			}
			else{
				$response['error']="Invalid Request Method";
			}
			return $response;			
		} 
		public function run() { 
			 $method="";
			 $response=$this->validateRequestMethod();
			 if ( $response) {
				$response=$this->validateMethod();
				if (!$response){
					$response['error']= "Method Not Defined";
				}
				else { 
					$method=$response;
					$response=$this->transferControl($response);
				} 
			 }
			 else { 
			 	$response['error']= "Invalid Request Method";
			 }

			 $response=json_encode($response);
			 if( $method!="filewrite" && $method!="fetchStatus" && $method!="updateStatus" ){
				 $response="[".substr($response,1,strlen($response)-2)."]";
			 }
			 echo $response;
		} 
		function __construct(){
			$this->hostname="<DATABASE_SERVER_URL>";
			$this->username="<DATABASE_USER_NAME>";
			$this->password="<DATABASE_PASSWORD>";
			$this->dbname="<DATABASE_NAME>";
			$this->fileExt="<FILE_EXTENSION>"; //e.g. 3gpp 
			$this->folderLocation="<FOLDER_LOCATION>"; //e.g., files 
			$this->connect_db();
		}
		
	}

	$v=new app();
	$v->run();
?>