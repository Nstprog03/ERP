<?php
  require_once('../db.php');
	$trans_dir = 'files/trans/';
	$audit_dir = 'files/audit/';
	$doc_dir = 'files/doc/';
	$sales_dir = 'files/sales/';

  if (isset($_POST['Submit'])) {
    $party_name = $_POST['party_name'];
    $orgname = $_POST['orgname'];
    $start_yr = $_POST['start_yr'];
    $end_yr = $_POST['end_yr'];  
    

    $trans_img = $_FILES['trans']['name'];
		$trans_imgTmp = $_FILES['trans']['tmp_name'];
		$trans_imgSize = $_FILES['trans']['size'];

	$audit_img = $_FILES['audit']['name'];
		$audit_imgTmp = $_FILES['audit']['tmp_name'];
		$audit_imgSize = $_FILES['audit']['size'];

	$doc_img = $_FILES['doc']['name'];
		$doc_imgTmp = $_FILES['doc']['tmp_name'];
		$doc_imgSize = $_FILES['doc']['size'];

	$sales_img = $_FILES['sales']['name'];
		$sales_imgTmp = $_FILES['sales']['tmp_name'];
		$sales_imgSize = $_FILES['sales']['size'];							

	
    if(!empty($trans_img)){
	
			$trans_imgExt = strtolower(pathinfo($trans_img, PATHINFO_EXTENSION));

			$trans_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

			$trans_img = time().'_'.rand(1000,9999).'.'.$trans_imgExt;

			if(in_array($trans_imgExt, $trans_allowExt)){

				if($trans_imgSize < 5000000){
					move_uploaded_file($trans_imgTmp ,$trans_dir.$trans_img);
				}else{
					$errorMsg = 'Image too large';
				}
			}else{
				$errorMsg = 'Please select a valid image';
			}


		}

	if(!empty($audit_img)){
	
			$audit_imgExt = strtolower(pathinfo($audit_img, PATHINFO_EXTENSION));

			$audit_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

			$audit_img = time().'_'.rand(1000,9999).'.'.$audit_imgExt;

			if(in_array($audit_imgExt, $audit_allowExt)){

				if($audit_imgSize < 5000000){
					move_uploaded_file($audit_imgTmp ,$audit_dir.$audit_img);
				}else{
					$errorMsg = 'Image too large';
				}
			}else{
				$errorMsg = 'Please select a valid image';
			}


		}

	if(!empty($doc_img)){
	
			$doc_imgExt = strtolower(pathinfo($doc_img, PATHINFO_EXTENSION));

			$doc_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

			$doc_img = time().'_'.rand(1000,9999).'.'.$doc_imgExt;

			if(in_array($doc_imgExt, $doc_allowExt)){

				if($doc_imgSize < 5000000){
					move_uploaded_file($doc_imgTmp ,$doc_dir.$doc_img);
				}else{
					$errorMsg = 'Image too large';
				}
			}else{
				$errorMsg = 'Please select a valid image';
			}


		}		

	if(!empty($sales_img)){
	
			$sales_imgExt = strtolower(pathinfo($sales_img, PATHINFO_EXTENSION));

			$sales_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

			$sales_img = time().'_'.rand(1000,9999).'.'.$sales_imgExt;

			if(in_array($sales_imgExt, $sales_allowExt)){

				if($sales_imgSize < 5000000){
					move_uploaded_file($sales_imgTmp ,$sales_dir.$sales_img);
				}else{
					$errorMsg = 'Image too large';
				}
			}else{
				$errorMsg = 'Please select a valid image';
			}


		}		


		if(!isset($errorMsg)){
			$sql = "insert into  pro_audit(start_yr, end_yr, org, party, trans, audit, doc, sales)
					values('".$start_yr."', '".$end_yr."', '".$orgname."', '".$party_name."', '".$trans_img."', '".$audit_img."', '".$doc_img."', '".$sales_img."')";
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
