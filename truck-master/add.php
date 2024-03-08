<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
 

  if (isset($_POST['Submit'])) 
  {
    $truck_no = $_POST['truck_no'];
    $transport = $_POST['transport'];

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");

    include_once('../global_function.php'); 
	$data=getStaticFileStoragePath("truck-master");  //from global_function.php
	$root_path=$data[0]; // file move path
	$store_path=$data[1]; // db store path


		$rc = $_FILES['rc']['name'];
				$rcimgTmp = $_FILES['rc']['tmp_name'];
				$rcimgSize = $_FILES['rc']['size'];	
		    
		$kyc = $_FILES['kyc']['name'];
				$kycimgTmp = $_FILES['kyc']['tmp_name'];
				$kycimgSize = $_FILES['kyc']['size'];	

		if(!empty($rc))
		{
			$rcExt = strtolower(pathinfo($rc, PATHINFO_EXTENSION));

			$rc_allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'pdf', 'doc', 'xls', 'csv', 'docx', 'xlsx');

			$rc = time().'_'.rand(1000,9999).'.'.$rcExt;

			if(in_array($rcExt, $rc_allowExt)){

				if($rcimgSize < 5000000){
					move_uploaded_file($rcimgTmp ,$root_path.$rc);
					$rc=$store_path.$rc;

				}else{
					$errorMsg = 'Image too large';
					echo $errorMsg;
				}
			}else{
				$errorMsg = 'Please select a valid image';
				echo $errorMsg;
			}
		}

if(!empty($kyc))
    	{
			$kycExt = strtolower(pathinfo($kyc, PATHINFO_EXTENSION));

			$kyc_allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'pdf', 'doc', 'xls', 'csv', 'docx', 'xlsx');

			$kyc = time().'_'.rand(1000,9999).'.'.$kycExt;

			if(in_array($kycExt, $kyc_allowExt)){

				if($kycimgSize < 5000000){
					move_uploaded_file($kycimgTmp ,$root_path.$kyc);
					$kyc=$store_path.$kyc;
				}else{
					$errorMsg = 'Image too large';
					echo $errorMsg;
				}
			}else{
				$errorMsg = 'Please select a valid image';
				echo $errorMsg;
			}
		}		

 
		if(!isset($errorMsg)){
			$sql = "insert into truck_master(truck_no, transport, rc, kyc,username,created_at,updated_at)
					values('".$truck_no."', '".$transport."', '".$rc."', '".$kyc."', '".$username."', '".$timestamp."', '".$timestamp."')";
			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'New record added successfully';
				header('Location: index.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
				echo $errorMsg;
			}
		}
  }
?>
