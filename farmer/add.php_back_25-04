<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}



  if (isset($_POST['Submit'])) {
    $farmer_name = $_POST['farmer_name'];
    $vlg_name = $_POST['vlg_name'];
    $tal_name = $_POST['tal_name'];
    $dist_name = $_POST['dist_name'];
    $bank_name = $_POST['bank_name'];
    $ifsc = $_POST['ifsc'];
    $branch = $_POST['branch'];
    $ac_no = $_POST['ac_no'];

    include_once('../global_function.php'); 
	$data=getStaticFileStoragePath("farmer");  //from global_function.php
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
			$sql = "insert into farmer (farmer_name, vlg_name, tal_name, dist_name, bank_name,ifsc,branch,ac_no,doc_file,img_title,username,created_at,updated_at)
					values('".$farmer_name."', '".$vlg_name."', '".$tal_name."', '".$dist_name."', '".$bank_name."', '".$ifsc."', '".$branch."', '".$ac_no."', '".$imgStore."', '".$imgTitle."', '".$username."', '".$timestamp."', '".$timestamp."')";
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
