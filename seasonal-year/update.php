<?php 
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
  if(isset($_POST['submit']))
  {
        $id = $_POST['id'];

         $username = $_SESSION["username"];
        date_default_timezone_set('Asia/Kolkata');
        $timestamp=date("Y-m-d H:i:s");


        $sql = "select `startdate`,`enddate` from seasonal_year where id = ".$id;
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0)
        {

                  
                $startdate = DateTime::createFromFormat('d/m/Y', $_POST['startdate']);
                $startdate=$startdate->format('Y-m-d');

                $enddate = DateTime::createFromFormat('d/m/Y', $_POST['enddate']);
                $enddate=$enddate->format('Y-m-d');
              
                $id = $_POST['id'];

                $sql="select * from seasonal_year where (('".$startdate."' BETWEEN startdate AND enddate) OR ('".$enddate."' BETWEEN startdate AND enddate))";

                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) 
                {

                echo $errorMsg = '<h2>Entered Financial Year is already Exists</h2>';
                exit;

                }
                else {
                $sql = "update seasonal_year
                                set 
                                startdate = '".$startdate."',
                                enddate = '".$enddate."',
                                username = '".$username."',
                                updated_at = '".$timestamp."'
                                where id='".$id."'";  
                    $result = mysqli_query($conn, $sql);
                    if($result){
                        $successMsg = 'New record updated successfully';
                        header('Location:index.php');

                    }else{

                        error_reporting();
                    }

                }

            

            

         
        }



    
}

?>