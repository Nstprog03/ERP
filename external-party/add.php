<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
  


  if (isset($_POST['Submit'])) {

    $partyname = $_POST['partyname'];
    $contact_per = $_POST['contact_per'];
    $contact_no = $_POST['contact_no'];
    //$broker = $_POST['broker'];
    $address = $_POST['address'];
    $gstin = $_POST['gstin'];

    $city=$_POST['city'];
    $district=$_POST['district'];
    $state=$_POST['state'];
    $pincode=$_POST['pincode'];
    $pan_no=strtoupper($_POST['pan_no']);
    $fact_lic_no=$_POST['fact_lic_no'];
    $iec_code = $_POST['iec_code'];
    $ud_aadhar = $_POST['ud_aadhar'];
    $party_email = $_POST['party_email'];

   

      $bankArr=array();
    foreach ($_POST['bank_name'] as $key => $bank_name) 
    {
        $bankArr[$key]['bank_name']=$bank_name;
        $bankArr[$key]['bank_ac_number']=$_POST['bank_ac_number'][$key];
        $bankArr[$key]['bank_branch']=$_POST['bank_branch'][$key];
        $bankArr[$key]['ifsc']=$_POST['ifsc'][$key];
    }

    $bankDetails=json_encode($bankArr);

    include_once('../global_function.php'); 
    $data=getStaticFileStoragePath("external-party");  //from global_function.php
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path



    //file upload
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
			$sql = "insert into external_party(partyname, contact_per, contact_no, address, gstin,city,district,state,pincode,pan_no,fact_lic_no,iec_code,ud_aadhar,party_email,doc_file,img_title,username,created_at,updated_at,bankDetails)
					values('".$partyname."', '".$contact_per."', '".$contact_no."', '".$address."', '".$gstin."','".$city."','".$district."','".$state."','".$pincode."','".$pan_no."','".$fact_lic_no."','".$iec_code."','".$ud_aadhar."','".$party_email."','".$imgStore."','".$imgTitle."', '".$username."', '".$timestamp."', '".$timestamp."','".$bankDetails."')";
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
