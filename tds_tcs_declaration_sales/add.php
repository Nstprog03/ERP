<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

	$dir = 'files/';

  if (isset($_POST['Submit'])) {

    $module ="";
    if(isset($_POST['module']))
    {
      if($_POST['module'] == "sales"){
        $module=$_POST['module'];
      }elseif($_POST['module'] == "purchase_cotton"){
        $module=$_POST['module'];
      }
    }

    $firm = $_POST['firm'];
    $financiyal_year_id = $_POST['financial_year'];
    $ext_party_id = $_POST['ext_party_id'];
    $turnover = $_POST['turnover'];

    

    if($_POST['date']!='')
    {
      $date = str_replace('/', '-', $_POST['date']);
      $date = date('Y-m-d', strtotime($date));
    }

    $good_exceeding = $_POST['good_exceeding'];
    $audit_report_id = implode(',', $_POST['audit_report_id']);
    $status = $_POST['status'];


    include('../global_function.php'); 
    $data=getFileStoragePath("tds_tcs_declaration_".$module,$financiyal_year_id);  //function from global_function file
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
			$sql = "INSERT INTO `tds_tcs_declaration`(`firm_id`, `financiyal_year_id`, `module_indicator`, `ext_party_id`, `date`, `good_exceeding`, `audit_report_id`, `username`, `created_at`, `updated_at`, `status`, `doc_file`, `img_title`,turnover) VALUES ('".$firm."', '".$financiyal_year_id."', '".$module."', '".$ext_party_id."', '".$date."','".$good_exceeding."','".$audit_report_id."', '".$username."', '".$timestamp."', '".$timestamp."', '".$status."', '".$imgStore."', '".$imgTitle."','".$turnover."')";
      
			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'New record added successfully';
				header('Location: index.php?module='.$module);
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
				echo $errorMsg;
			}
		}
  }
?>
