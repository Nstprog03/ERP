<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}




  if (isset($_POST['Submit'])) {


    $invoice_no = $_POST['invoice_no'];
    $firm_id = $_POST['firm_id'];
    
    $amount = $_POST['amount'];
    $remark = $_POST['remark'];


    $ext_party_id='';
    $broker_id='';
    $pay_to=$_POST['pay_to'];
    if($pay_to=='e')
    {
      $ext_party_id = $_POST['ext_party_id'];
    }
    else if($pay_to=='b')
    {
      $broker_id = $_POST['broker_id'];
    }



    $date = '';
	if($_POST['date']!='')
    {
      $date = str_replace('/', '-', $_POST['date']);
      $date = date('Y-m-d', strtotime($date));
    }


    include_once('../global_function.php'); 
    $data=getFileStoragePath("other_payout");  //from global_function.php
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
			$sql = "insert into other_payout(invoice_no,firm_id,ext_party_id,broker_id,amount,date,remark,username,created_at,updated_at,doc_file,img_title,pay_to)
					values('".$invoice_no."','".$firm_id."', '".$ext_party_id."','".$broker_id."','".$amount."','".$date."','".$remark."', '".$username."', '".$timestamp."', '".$timestamp."', '".$imgStore."', '".$imgTitle."', '".$pay_to."')";
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
