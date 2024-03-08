<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}


  if (isset($_POST['Submit'])) {
    $bank_name = $_POST['bank_name'];
    $bank_branch = $_POST['bank_branch'];
    $branch_person = $_POST['branch_person'];
    $branch_no = $_POST['branch_no'];

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");
   
		if(!isset($errorMsg)){
			$sql = "insert into external_banks(bank_name, bank_branch, branch_person, branch_no,username,created_at,updated_at)
					values('".$bank_name."', '".$bank_branch."', '".$branch_person."', '".$branch_no."', '".$username."', '".$timestamp."', '".$timestamp."')";
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
