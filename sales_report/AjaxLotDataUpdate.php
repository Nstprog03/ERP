<?php
session_start();
require_once('../db.php');
if(isset($_POST['updateLotData']))
{

    $id=$_POST['record_id'];



    $lot_no=array();
    $lot_bales=array();
    if(isset($_POST['lot_no']))
    {
        $lot_no=$_POST['lot_no'];
    }

    if(isset($_POST['lot_bales']))
    {
        $lot_bales=$_POST['lot_bales'];
    }



    $lot_no = json_encode($lot_no);
    $lot_bales = json_encode($lot_bales);
    $no_of_bales = $_POST['noOFBales'];

 
    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");
 
    $sql = "UPDATE `sales_report` SET 
    `lot_no`='".$lot_no."',
    `lot_bales`='".$lot_bales."',
    `noOFBales`='".$no_of_bales."',
    `username`='".$username."',
    `updated_at`='".$timestamp."'
      WHERE id=".$id;
    $result = mysqli_query($conn, $sql);
  
    if($result)
    {
        $response['success']=true;
    }
    else
    {
        $response['success']=false;
    }


    echo json_encode($response);
    exit;
}

?>