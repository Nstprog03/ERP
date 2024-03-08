<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

$dir = "/static_file_storage/"; 
  $unlink_path=$_SERVER['DOCUMENT_ROOT'].$dir;


 if(isset($_POST['Submit'])){
    $dir = 'files/';
    $id=$_POST['id'];

    $lab_name = $_POST['lab_name'];
    $gst_no = $_POST['gst_no'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $contact_no = $_POST['contact_no'];
    $bank_name = $_POST['bank_name'];
    $bank_ifsc = $_POST['bank_ifsc'];
    $account_no = $_POST['account_no'];
    $branch = $_POST['branch'];
    $pan_no = strtoupper($_POST['pan_no']);

  include_once('../global_function.php'); 
  $data=getStaticFileStoragePath("laboratory-master");  //from global_function.php
  $root_path=$data[0]; // file move path
  $store_path=$data[1]; // db store path

    $imgArr=array();
    $filecount = count($_FILES['doc_file']['tmp_name']);  
    foreach ($_FILES['doc_file']['tmp_name'] as $key =>  $imges) {

      $img = $_FILES['doc_file']['name'][$key];

      $imgTmp = $_FILES['doc_file']['tmp_name'][$key];
      $imgSize = $_FILES['doc_file']['size'][$key];

  
      if(!empty($img)){
        
        $imgExt = strtolower(pathinfo($img, PATHINFO_EXTENSION));

        $allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'csv', 'pdf', 'xls', 'xlsx', 'txt');

        $img = time().'_'.rand(1000,9999).'.'.$imgExt;
        // array_push($imgArr,$img);
        $imgArr[$key] = $img;
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

      }else{
        $imgArr[$key] = '';
      }
    }
    
    $finalimg = array();
    if(count($imgArr) > 0){
      foreach($imgArr as $k => $v){
        if($v == "" && isset($_POST['oldfile'][$k])){
          $finalimg[] = $_POST['oldfile'][$k];
        }else{
          if($v!='' && $v!=null)
          {
            $finalimg[] = $store_path.$v;
          }
        }
      }
    }


    $img_title = $_POST['img_title'];
    $imgTitle = implode(',', $img_title);
    $imgStore = implode(',', $finalimg);


    $sql="select * from laboratory_master where id='".$id."'";
    $result = mysqli_query($conn, $sql);
    $row=mysqli_fetch_assoc($result);
    
    $OldDBImg = explode(',', $row['doc_file']); 
    $result1=array_diff($OldDBImg,$finalimg);
    foreach ($result1 as  $item) 
    {
        if($item!='')
        {
          $item=trim($item);             
          unlink($unlink_path.$item); 
        }
    }

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");
    
   

    if(!isset($errorMsg)){
      $sql = "update laboratory_master
                  set lab_name = '".$lab_name."',
                    gst_no = '".$gst_no."',
                    address = '".$address."',
                    city = '".$city."',
                    contact_no = '".$contact_no."',
                    bank_name = '".$bank_name."',
                    bank_ifsc = '".$bank_ifsc."',
                    account_no = '".$account_no."',
                    branch = '".$branch."',
                    doc_file = '".$imgStore."',
                    img_title = '".$imgTitle."',
                    pan_no = '".$pan_no."',
                    username = '".$username."',
                    updated_at = '".$timestamp."'
          where id=".$id;          
      $result = mysqli_query($conn, $sql);
      if($result){
        $successMsg = 'New record updated successfully';
       
        $page=1;
        if(isset($_POST['page']))
        {
          $page=$_POST['page'];
        }
        header("Location: index.php?page=$page");

      }else{
        $errorMsg = 'Error '.mysqli_error();
      }
    }

  }
?>