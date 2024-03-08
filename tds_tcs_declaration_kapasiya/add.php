<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['kap_firm_id']) && !isset($_SESSION['kap_seasonal_year_id']))
{
  header('Location: /kapasiya-sales-rg/');
}

	

  if (isset($_POST['Submit'])) {

    $firm_id=$_SESSION['kap_firm_id'];
    $seasonal_year_id=$_SESSION['kap_seasonal_year_id'];


  

    $ext_party_id = $_POST['ext_party_id'];
    

    if($_POST['date']!='')
    {
      $date = str_replace('/', '-', $_POST['date']);
      $date = date('Y-m-d', strtotime($date));
    }

    $good_exceeding = $_POST['good_exceeding'];
    $audit_report_id = implode(',', $_POST['audit_report_id']);
    $status = $_POST['status'];


    include('../global_function.php'); 
    $data=getStaticFileStoragePath("tds_tcs_declaration_kapasiya");  //function from global_function file
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
			$sql = "INSERT INTO `tds_tcs_declaration_kapasiya`(`firm_id`, `seasonal_year_id`, `ext_party_id`, `date`, `good_exceeding`, `audit_report_id`, `username`, `created_at`, `updated_at`, `status`, `doc_file`, `img_title`) VALUES ('".$firm_id."', '".$seasonal_year_id."', '".$ext_party_id."', '".$date."','".$good_exceeding."','".$audit_report_id."', '".$username."', '".$timestamp."', '".$timestamp."', '".$status."', '".$imgStore."', '".$imgTitle."')";
      
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
