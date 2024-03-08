<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

if (isset($_POST['submit'])) {
    
    $startdate = DateTime::createFromFormat('d/m/Y', $_POST['startdate']);
    $startdate=$startdate->format('Y-m-d');

    $enddate = DateTime::createFromFormat('d/m/Y', $_POST['enddate']);
    $enddate=$enddate->format('Y-m-d');


     $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");

    
    $sql="select * from financial_year where (('".$startdate."' BETWEEN startdate AND enddate) OR ('".$enddate."' BETWEEN startdate AND enddate))";
   
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
      
     echo $errorMsg = '<h2>Entered Financial Year is already Exists</h2>';
     exit;

    }
    else {
     $sql = "INSERT INTO financial_year(startdate, enddate, username,created_at,updated_at) VALUES ('".$startdate."', '".$enddate."', '".$username."', '".$timestamp."', '".$timestamp."')";
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