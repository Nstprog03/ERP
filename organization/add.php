<?php
  require_once('../db.php');
$org_dir = 'files/org/';

  if (isset($_POST['Submit'])) {
    $orgname = $_POST['orgname'];

		if(!isset($errorMsg)){
			$sql = "insert into  organization(orgname)
					values('".$orgname."')";
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
