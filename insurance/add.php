<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}
	


  if (isset($_POST['Submit'])) {


  	include('../global_function.php'); 
	$data=getStaticFileStoragePath("insurance");  //this function called from global_function.php file
	$root_path=$data[0]; // file move path
	$store_path=$data[1]; // db store path






  	$firm = $_POST['firm'];
    $policyno = $_POST['policyno'];
    $comp_name = $_POST['comp_name'];
    $ins_type = $_POST['ins_type'];
    $vehicle_no = $_POST['vehicle_no'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $sum_ass = $_POST['sum_ass'];
    $status = $_POST['status'];


    if($start_date!='')
	{
		$start_date = DateTime::createFromFormat('d/m/Y', $_POST['start_date']);
    	$start_date=$start_date->format('Y-m-d');
	}
	if($end_date!='')
	{
		$end_date = DateTime::createFromFormat('d/m/Y', $_POST['end_date']);
    	$end_date=$end_date->format('Y-m-d');
	}



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
			$sql = "insert into  insurance(firm_id,policyno, comp_name, ins_type, vehicle_no, start_date, end_date, sum_ass, status, doc_file,img_title,username,created_at,updated_at)
					values('".$firm."','".$policyno."', '".$comp_name."', '".$ins_type."', '".$vehicle_no."', '".$start_date."', '".$end_date."', '".$sum_ass."', '".$status."', '".$imgStore."', '".$imgTitle."', '".$username."', '".$timestamp."', '".$timestamp."')";
					
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
