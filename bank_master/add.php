<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}

  if (isset($_POST['Submit'])) {
      
    $firm_id = $_SESSION["bank_firm_id"];
    $username=$_SESSION['username'];
    
    $bank_name = $_POST['bank_name'];
    $bank_ac_number = $_POST['bank_ac_number'];
    $bank_branch = $_POST['bank_branch'];
    $ifsc=strtoupper($_POST['ifsc']);
    $address=$_POST['address'];

    //bank form
    include_once('../global_function.php'); 
    $data=getStaticFileStoragePath("bank");  //from global_function.php
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path

	$imgArr=array();
    $img_titleArr = array();
    foreach ($_FILES['docImg']['tmp_name'] as $key =>  $imges) {
    	
    	$img_title = $_POST['img_title'][$key];
    	$img = $_FILES['docImg']['name'][$key];
			$imgTmp = $_FILES['docImg']['tmp_name'][$key];
			$imgSize = $_FILES['docImg']['size'][$key];

	
	    if(!empty($img)){
			array_push($img_titleArr,$img_title);
			$imgExt = strtolower(pathinfo($img, PATHINFO_EXTENSION));

			$allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'csv', 'pdf', 'xls', 'xlsx', 'txt');

			$img = time().'_'.rand(1000,9999).'.'.$imgExt;
			array_push($imgArr,$store_path.$img);

			if(in_array($imgExt, $allowExt)){

				if($imgSize < 5000000){
					move_uploaded_file($imgTmp ,$root_path.$img);
				}else{
					$errorMsg = 'Image too large';
					echo $errorMsg;
				}
			}else{
				$errorMsg = 'Please select a valid image';
				echo $errorMsg;
			}
		}
    }

    $imgTitle = implode(',', $img_titleArr);
    $imgStore = implode(',', $imgArr);
    
    date_default_timezone_set('Asia/Kolkata');
    $timestamp = date("Y-m-d H:i:s");
    

		if(!isset($errorMsg)){
			$sql = "insert into bank_master(bank_name, bank_ac_number, bank_branch,ifsc,address,docImg,img_title,firm_id,username,created_at,updated_at)
					values('".$bank_name."', '".$bank_ac_number."', '".$bank_branch."','".$ifsc."', '".$address."','".$imgStore."','".$imgTitle."','".$firm_id."','".$username."','".$timestamp."','".$timestamp."')";

			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'New record added successfully';
				header('Location: home.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
			}
		}
  }
?>
