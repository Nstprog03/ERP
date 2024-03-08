<?php
  require_once('../db.php');
$assreport_dir = 'files/gstreport/';

  if (isset($_POST['Submit'])) {
    $party_name = $_POST['party_name'];
    $gst_report_type = $_POST['gst_report_type'];
    $gst_report_name = $_POST['gst_report_name'];
    $gst_report_yr = $_POST['gst_report_yr'];
    $gst_report_eyr = $_POST['gst_report_eyr'];
    $gst_report_mnt = $_POST['gst_report_mnt'];
    
    

    $gst_report_img = $_FILES['gst_report_img']['name'];
		$gst_reportimgTmp = $_FILES['gst_report_img']['tmp_name'];
		$gst_reportimgSize = $_FILES['gst_report_img']['size'];

	
    if(!empty($gst_report_img)){
	
			$gst_report_imgExt = strtolower(pathinfo($gst_report_img, PATHINFO_EXTENSION));

			$gst_reportallowExt  = array('jpeg', 'jpg', 'png', 'gif');

			$gst_report_img = time().'_'.rand(1000,9999).'.'.$gst_report_imgExt;

			if(in_array($gst_report_imgExt, $gst_reportallowExt)){

				if($gst_reportimgSize < 5000000){
					move_uploaded_file($gst_reportimgTmp ,$assreport_dir.$gst_report_img);
				}else{
					$errorMsg = 'Image too large';
					echo $errorMsg;
				}
			}else{
				$errorMsg = 'Please select a valid image';
				echo $errorMsg;
			}


		}


		if(!isset($errorMsg)){
			$sql = "insert into  gst_report(party_name, gst_report_type, gst_report_name, gst_report_yr, gst_report_eyr, gst_report_mnt, gst_report_img)
					values('".$party_name."', '".$gst_report_type."', '".$gst_report_name."', '".$gst_report_yr."', '".$gst_report_eyr."', '".$gst_report_mnt."', '".$gst_report_img."')";
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
