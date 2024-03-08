<?php
session_start();
include('../../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../../login.php");
    exit;
}
if(!isset($_SESSION["qis_form"]) || !isset($_SESSION["qis_firm"]) || !isset($_SESSION["qis_quarter"]) || !isset($_SESSION["qis_year"])){
    header("location:index.php");
    exit;
}

  $firm_id=explode('/',$_SESSION['qis_firm'])[1];
  $year=$_SESSION['qis_year'];
  $form=$_SESSION['qis_form'];
  $quarter=$_SESSION['qis_quarter'];

 
  $upload_dir='files/'.$quarter.'_'.$form.'/';


  if (isset($_POST['Submit'])) 
  {
   
   
    	$image = $_FILES['file']['name'];
		$tmp_image = $_FILES['file']['tmp_name'];
		$size = $_FILES['file']['size'];


	include_once('../../global_function.php'); 
	$data=getStaticFileStoragePath("qis");  //from global_function.php
	$root_path=$data[0]; // file move path
	$store_path=$data[1]; // db store path

		

	
    if(!empty($image))
    {
			
	
			$image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

			$allow_ext  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'pdf');

			$image = time().'_'.rand(1000,9999).'.'.$image_ext;

			if(in_array($image_ext, $allow_ext)){

				if($size < 5000000){
					move_uploaded_file($tmp_image ,$root_path.$image);
					$image=$store_path.$image;
					$status='completed';
				}else{
					$errorMsg = 'Image too large';
					echo $errorMsg;
				}
			}else{
				$errorMsg = 'Please select a valid image';
				echo $errorMsg;
			}
	}
	else
	{
		$status='pending';
	}

	 $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");



	if(!isset($errorMsg))
	{
		$sql = "insert into  qis(firm, form, year, quarter, status, file,username,created_at,updated_at)
				values('".$firm_id."', '".$form."', '".$year."', '".$quarter."', '".$status."', '".$image."', '".$username."', '".$timestamp."', '".$timestamp."')";

		$result = mysqli_query($conn, $sql);
		if($result){
			$successMsg = 'New record added successfully';
			header('Location: index1.php');
		}else{
			$errorMsg = 'Error '.mysqli_error($conn);
			echo $errorMsg;
		}
	}

  }
?>
