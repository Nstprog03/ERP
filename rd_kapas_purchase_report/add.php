<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['pur_firm_id']) && !isset($_SESSION['pur_financial_year_id']))
{
  header('Location: ../purchase_index.php');
}
  

  if (isset($_POST['Submit'])) {
    
     $report_date='';
    if($_POST['report_date']!='')
    {
        $report_date = DateTime::createFromFormat('d/m/Y', $_POST['report_date']);
        $report_date=$report_date->format('Y-m-d');  
    }



    include('../global_function.php'); 
    $data=getFileStoragePath("rd_kapas_purchase_report",$_SESSION['pur_financial_year_id']);  //function from global_function file
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path

    
    $invoice_no = $_POST['invoice_no'];
    $external_party = $_POST['external_party'];
    $firm = $_POST['firm'];
    $financial_year= $_POST['financial_year'];
    $product = $_POST['product'];
    $broker = $_POST['broker'];
    $basic_amt = $_POST['basic_amt'];
    $tax = $_POST['tax'];
    $tax_amt = $_POST['tax_amt'];
    $tcs = $_POST['tcs'];
    $tcs_amt = $_POST['tcs_amt'];
    $gd_value = $_POST['gd_value'];
   
    $net_amt = $_POST['net_amt'];

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");

    $imgArr=array();
    $img_titleArr = array();
    foreach ($_FILES['docimg']['tmp_name'] as $key =>  $imges) {
      
      $img_title = $_POST['img_title'][$key];
      $img = $_FILES['docimg']['name'][$key];
      $imgTmp = $_FILES['docimg']['tmp_name'][$key];
      $imgSize = $_FILES['docimg']['size'][$key];

  
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
			$sql = "insert into rd_kapas_report(report_date, invoice_no, external_party, firm,financial_year_id, product, broker, basic_amt, tax, tcs, tcs_amt, gd_value, net_amt,docimg,img_title,username,created_at,updated_at,tax_amt)
					values('".$report_date."', '".$invoice_no."', '".$external_party."', '".$firm."', '".$financial_year."', '".$product."', '".$broker."', '".$basic_amt."', '".$tax."', '".$tcs."', '".$tcs_amt."', '".$gd_value."', '".$net_amt."', '".$imgStore."', '".$imgTitle."', '".$username."', '".$timestamp."', '".$timestamp."', '".$tax_amt."')";
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
