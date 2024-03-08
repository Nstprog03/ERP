<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

  
  $stampDir = 'files/stamp/';
  $logo_dir = 'files/logo/';


  if (isset($_POST['Submit'])) {
    $party_name = $_POST['party_name'];
    
    $party_address = $_POST['party_address'];
    $iec_code = $_POST['iec_code'];
    $ud_aadhar = $_POST['ud_aadhar'];
    $contact_per = $_POST['contact_per'];


    include_once('../global_function.php'); 
    $data=getStaticFileStoragePath("firm");  //from global_function.php
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path
    
   


    $bankArr=array();
    foreach ($_POST['bank_name'] as $key => $bank_name) 
    {
    	$bankArr[$key]['bank_name']=$bank_name;
    	$bankArr[$key]['bank_ac_number']=$_POST['bank_ac_number'][$key];
    	$bankArr[$key]['bank_branch']=$_POST['bank_branch'][$key];
    	$bankArr[$key]['ifsc']=$_POST['ifsc'][$key];
    }

    $bankDetails=json_encode($bankArr);



    $party_email = json_encode($_POST['party_email']);
    $contact_number = json_encode($_POST['contact_number']);


    $city=$_POST['city'];
    $district=$_POST['district'];
    $state=$_POST['state'];
    $pincode=$_POST['pincode'];
    $pan_no=strtoupper($_POST['pan_no']);
    $gst_in=strtoupper($_POST['gst_in']);
    $tan_no = strtoupper($_POST['tan_no']);
    $fact_lic_no=$_POST['fact_lic_no'];


    $show_in_qis=0;
    if(isset($_POST['show_in_qis']))
    {
         $show_in_qis=1;
    }
    
    




    //firm short form

    $getFirmName=explode(" ",$_POST['party_name']);
    $shortForm='';

    for($i=0; $i<=count($getFirmName)-1; $i++)
    {
    	if(count($getFirmName)==1)
    	{
    		$shortForm=strtoupper(substr($getFirmName[$i], 0,3));
    	}
    	else if($i<3)
    	{
    		$a=substr($getFirmName[$i],0,1);
	    	$shortForm=$shortForm.$a;
    	}
    }

    


        //logo

	    $logo_img = $_FILES['logo_img']['name'];
		$logoimgTmp = $_FILES['logo_img']['tmp_name'];
		$logoimgSize = $_FILES['logo_img']['size'];	

		if(!empty($logo_img))
    	{
			$logo_imgExt = strtolower(pathinfo($logo_img, PATHINFO_EXTENSION));

			$logo_allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'pdf', 'doc', 'xls', 'csv', 'docx', 'xlsx');

			$logo_img = time().'_'.rand(1000,9999).'.'.$logo_imgExt;

			if(in_array($logo_imgExt, $logo_allowExt)){

				if($logoimgSize < 5000000){
					move_uploaded_file($logoimgTmp ,$logo_dir.$logo_img);
				}else{
					$errorMsg = 'Image too large';
				}
			}else{
				$errorMsg = 'Please select a valid image';
			}
		}



         //stamp

        $stamp_img = $_FILES['stamp_img']['name'];
        $StampimgTmp = $_FILES['stamp_img']['tmp_name'];
        $stampimgSize = $_FILES['stamp_img']['size']; 

        if(!empty($stamp_img))
        {
            $logo_imgExt = strtolower(pathinfo($stamp_img, PATHINFO_EXTENSION));

            $logo_allowExt  = array('jpeg', 'jpg', 'png');

            $stamp_img = time().'_'.rand(1000,9999).'.'.$logo_imgExt;

            if(in_array($logo_imgExt, $logo_allowExt)){

                if($stampimgSize < 5000000){
                    move_uploaded_file($StampimgTmp ,$stampDir.$stamp_img);
                }else{
                    $errorMsg = 'Image too large';
                }
            }else{
                $errorMsg = 'Please select a valid image';
            }
        }





        //dynamic images

		$imgArr=array();
    $img_titleArr = array();
    foreach ($_FILES['docImg']['tmp_name'] as $key =>  $imges) {
    	
    	$img_title = $_POST['img_title'][$key];
    	$img = $_FILES['docImg']['name'][$key];
			$imgTmp = $_FILES['docImg']['tmp_name'][$key];
			$imgSize = $_FILES['docImg']['size'][$key];

	
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
			$sql = "insert into party(party_name,party_shortform, party_email, party_address, iec_code, ud_aadhar, contact_per, contact_number, stamp_img,city,district,state,pincode,pan_no,gst_in,tan_no,fact_lic_no,logo_img, docImg,img_title,bankDetails,show_in_qis,username,created_at,updated_at)
					values('".$party_name."','".$shortForm."', '".$party_email."', '".$party_address."', '".$iec_code."', '".$ud_aadhar."', '".$contact_per."', '".$contact_number."','".$stamp_img."', '".$city."', '".$district."', '".$state."', '".$pincode."', '".$pan_no."', '".$gst_in."','".$tan_no."', '".$fact_lic_no."', '".$logo_img."','".$imgStore."','".$imgTitle."','".$bankDetails."','".$show_in_qis."','".$username."','".$timestamp."','".$timestamp."')";
			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'New record added successfully';
				header('Location: index.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
			}
		}
  }
?>