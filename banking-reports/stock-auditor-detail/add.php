<?php
session_start();
include('../../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../../login.php");
    exit;
}

  	

  if (isset($_POST['Submit'])) {
    $firm = $_POST['firm'];
    $start_yr = $_POST['start_yr'];
    $end_yr = $_POST['end_yr'];  
    $audit1_name = $_POST['audit1_name'];  
    $audit1_addr = $_POST['audit1_addr'];  
    $audit1_no = $_POST['audit1_no'];  
    $audit2_name = $_POST['audit2_name'];  
    $audit2_addr = $_POST['audit2_addr'];  
    $audit2_no = $_POST['audit2_no']; 

    $username= $_SESSION["username"];
	date_default_timezone_set('Asia/Kolkata');
	$timestamp=date("Y-m-d H:i:s");


	include_once('../../global_function.php'); 
	$data=getStaticFileStoragePath("stock-auditor-detail");  //from global_function.php
	$root_path=$data[0]; // file move path
	$store_path=$data[1]; // db store path




    // audit 1 images

    $aud1_imgArr=array();
    $aud1_img_titleArr = array();
    foreach ($_FILES['aud1_doc_file']['tmp_name'] as $key =>  $imges) {
    	
    	$aud1_img_title = $_POST['aud1_img_title'][$key];
    	$aud1_img = $_FILES['aud1_doc_file']['name'][$key];
		$aud1_imgTmp = $_FILES['aud1_doc_file']['tmp_name'][$key];
		$aud1_imgSize = $_FILES['aud1_doc_file']['size'][$key];

	
	    if(!empty($aud1_img)){
				array_push($aud1_img_titleArr,$aud1_img_title);
				$aud1_imgExt = strtolower(pathinfo($aud1_img, PATHINFO_EXTENSION));

				$aud1_allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'csv', 'pdf', 'xls', 'xlsx', 'txt');

				$aud1_img = time().'_'.rand(1000,9999).'.'.$aud1_imgExt;
				array_push($aud1_imgArr,$store_path.$aud1_img);

				if(in_array($aud1_imgExt, $aud1_allowExt)){

					if($aud1_imgSize < 5000000){
						move_uploaded_file($aud1_imgTmp ,$root_path.$aud1_img);
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

    $aud1_imgTitle = implode(',', $aud1_img_titleArr);
    $aud1_imgStore = implode(',', $aud1_imgArr);



    // audit 2 images

    $aud2_imgArr=array();
    $aud2_img_titleArr = array();
    foreach ($_FILES['aud2_doc_file']['tmp_name'] as $key =>  $imges) {
    	
    	$aud2_img_title = $_POST['aud2_img_title'][$key];
    	$aud2_img = $_FILES['aud2_doc_file']['name'][$key];
		$aud2_imgTmp = $_FILES['aud2_doc_file']['tmp_name'][$key];
		$aud2_imgSize = $_FILES['aud2_doc_file']['size'][$key];

	
	    if(!empty($aud2_img)){
				array_push($aud2_img_titleArr,$aud2_img_title);
				$aud2_imgExt = strtolower(pathinfo($aud2_img, PATHINFO_EXTENSION));

				$aud2_allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'csv', 'pdf', 'xls', 'xlsx', 'txt');

				$aud2_img = time().'_'.rand(1000,9999).'.'.$aud2_imgExt;
				array_push($aud2_imgArr,$store_path.$aud2_img);

				if(in_array($aud2_imgExt, $aud2_allowExt)){

					if($aud2_imgSize < 5000000){
						move_uploaded_file($aud2_imgTmp ,$root_path.$aud2_img);
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

    $aud2_imgTitle = implode(',', $aud2_img_titleArr);
    $aud2_imgStore = implode(',', $aud2_imgArr);




		if(!isset($errorMsg)){
			$sql = "insert into stock_audit(firm, start_yr, end_yr, audit1_name, audit1_addr, audit1_no,  audit1_doc_file,audit1_img_title, audit2_name, audit2_addr, audit2_no, audit2_doc_file,audit2_img_title,username,created_at,updated_at)
					values('".$firm."', '".$start_yr."', '".$end_yr."', '".$audit1_name."', '".$audit1_addr."', '".$audit1_no."', '".$aud1_imgStore."', '".$aud1_imgTitle."', '".$audit2_name."', '".$audit2_addr."', '".$audit2_no."', '".$aud2_imgStore."', '".$aud2_imgTitle."', '".$username."', '".$timestamp."', '".$timestamp."')";
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
