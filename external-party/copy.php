<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
  $dir = 'files/docs/';

  if (isset($_GET['id'])) 
  {



    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");


    $id = $_GET['id'];
    $sql = "select * from external_party where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
      $row = mysqli_fetch_assoc($result);

      $party=$row['partyname'].' - Copy';


      $sql = "insert into external_party(partyname, contact_per, contact_no, address, gstin,city,district,state,pincode,pan_no,fact_lic_no,iec_code,ud_aadhar,party_email,username,created_at,updated_at,bankDetails)
                    values('".$party."', '".$row['contact_per']."', '".$row['contact_no']."', '".$row['address']."', '".$row['gstin']."','".$row['city']."','".$row['district']."','".$row['state']."','".$row['pincode']."','".$row['pan_no']."','".$row['fact_lic_no']."','".$row['iec_code']."','".$row['ud_aadhar']."','".$row['party_email']."', '".$username."', '".$timestamp."', '".$timestamp."','".$row['bankDetails']."')";
        $result = mysqli_query($conn, $sql);
        if($result){
            $successMsg = 'New record added successfully';
            header('Location: index.php');
        }else{
            $errorMsg = 'Error '.mysqli_error($conn);
            echo $errorMsg;
        }




    }else {
      $errorMsg = 'Could not Find Any Record';
    }

	
			
		
  }
?>
