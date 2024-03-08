<?php
  require_once('../db.php');
  $attend_dir = 'files/attend/';
  $salary_dir = 'files/salary/';
  $pf_dir = 'files/pf/';
  $epf_dir = 'files/epf/';

  if (isset($_POST['Submit'])) {
    $firm = $_POST['firm'];
    $mnt = $_POST['mnt'];
    $start_yr = $_POST['start_yr'];
    $end_yr = $_POST['end_yr'];

$attend = $_FILES['attend']['name'];
		$attendimgTmp = $_FILES['attend']['tmp_name'];
		$attendimgSize = $_FILES['attend']['size'];	
    
$salary = $_FILES['salary']['name'];
		$salaryimgTmp = $_FILES['salary']['tmp_name'];
		$salaryimgSize = $_FILES['salary']['size'];

$pf = $_FILES['pf']['name'];
		$pfimgTmp = $_FILES['pf']['tmp_name'];
		$pfimgSize = $_FILES['pf']['size'];

$epf = $_FILES['epf']['name'];
		$epfimgTmp = $_FILES['epf']['tmp_name'];
		$epfimgSize = $_FILES['epf']['size'];				

if(!empty($attend))
    	{
			$attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));

			$attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'pdf', 'doc', 'xls', 'docx', 'xlsx');

			$attend = time().'_'.rand(1000,9999).'.'.$attendExt;

			if(in_array($attendExt, $attend_allowExt)){

				if($attendimgSize < 5000000){
					move_uploaded_file($attendimgTmp ,$attend_dir.$attend);
				}else{
					$errorMsg = 'Image too large';
				}
			}else{
				$errorMsg = 'Please select a valid image';
			}
		}

    if(!empty($salary))
    	{
			$salaryExt = strtolower(pathinfo($salary, PATHINFO_EXTENSION));

			$salary_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

			$salary = time().'_'.rand(1000,9999).'.'.$salaryExt;

			if(in_array($salaryExt, $salary_allowExt)){

				if($salaryimgSize < 5000000){
					move_uploaded_file($salaryimgTmp ,$salary_dir.$salary);
				}else{
					$errorMsg = 'Image too large';
				}
			}else{
				$errorMsg = 'Please select a valid image';
			}
		}

   if(!empty($pf))
    	{
			$pfExt = strtolower(pathinfo($pf, PATHINFO_EXTENSION));

			$pf_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

			$pf = time().'_'.rand(1000,9999).'.'.$pfExt;

			if(in_array($pfExt, $pf_allowExt)){

				if($pfimgSize < 5000000){
					move_uploaded_file($pfimgTmp ,$pf_dir.$pf);
				}else{
					$errorMsg = 'Image too large';
				}
			}else{
				$errorMsg = 'Please select a valid image';
			}
		}



   if(!empty($epf))
    	{
			$epfExt = strtolower(pathinfo($epf, PATHINFO_EXTENSION));

			$epf_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

			$epf = time().'_'.rand(1000,9999).'.'.$epfExt;

			if(in_array($epfExt, $epf_allowExt)){

				if($epfimgSize < 5000000){
					move_uploaded_file($epfimgTmp ,$epf_dir.$epf);
				}else{
					$errorMsg = 'Image too large';
				}
			}else{
				$errorMsg = 'Please select a valid image';
			}
		}



   
		if(!isset($errorMsg)){
			$sql = "insert into emp_salary(firm, start_yr, end_yr, mnt, attend, salary, pf, epf)
					values('".$firm."', '".$start_yr."', '".$end_yr."', '".$mnt."', '".$attend."', '".$salary."', '".$pf."', '".$epf."')";
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
