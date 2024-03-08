<?php
session_start();
require_once('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['doc_firm_id']) || !isset($_SESSION['doc_seasonal_year_id']))
{
  header('Location: index.php');
}

  

  if (isset($_POST['Submit'])) {

    $firm_id = $_SESSION['doc_firm_id'];
    $seasonal_year_id = $_SESSION['doc_seasonal_year_id'];


    include_once('../global_function.php'); 
    $data=getFileStoragePath("documentation",$_SESSION['doc_seasonal_year_id']);  //from global_function.php
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path

	

	$imgArr=array();
    $img_titleArr = array();
    foreach ($_FILES['doc_file']['tmp_name'] as $key =>  $imges) {
    	
    	$img_title = $_POST['img_title'][$key];
    	$img = $_FILES['doc_file']['name'][$key];
			$imgTmp = $_FILES['doc_file']['tmp_name'][$key];
			$imgSize = $_FILES['doc_file']['size'][$key];

	
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


    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");



	

		if(!isset($errorMsg)){
			$sql = "insert into documentation(firm_id,seasonal_year_id, doc_file,img_title,username,created_at,updated_at)
					values('".$firm_id."', '".$seasonal_year_id."','".$imgStore."', '".$imgTitle."', '".$username."', '".$timestamp."', '".$timestamp."')";
			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'New record added successfully';
				header('Location: home.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
				echo $errorMsg;
			}
		}
  }
?>
