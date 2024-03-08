<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}
    
    


  if (isset($_POST['Submit'])) {
    $firm = $_POST['firm'];
    $party = $_POST['party'];
    $financial_year = $_POST['financial_year'];


    include('../global_function.php'); 
    $data=getFileStoragePath("ledger_purchase",$_POST['financial_year']);  //from global_function.php 
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");


		$imgArr=array();
    $img_titleArr = array();
    foreach ($_FILES['ledger']['tmp_name'] as $key =>  $imges) {
    	
    	$img_title = $_POST['img_title'][$key];
    	$img = $_FILES['ledger']['name'][$key];
			$imgTmp = $_FILES['ledger']['tmp_name'][$key];
			$imgSize = $_FILES['ledger']['size'][$key];

	
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
   
   
		if(!isset($errorMsg)){
			$sql = "insert into pur_ledger(firm, party, financial_year_id, ledger, img_title,username,created_at,updated_at)
					values('".$firm."', '".$party."', '".$financial_year."', '".$imgStore."', '".$imgTitle."', '".$username."', '".$timestamp."', '".$timestamp."')";
			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'New record added successfully';
				header('Location: index.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
			}
		}
  }
?>
