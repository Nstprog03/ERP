<?php
  require_once('../db.php');
  $attend_dir = 'files/attend/';
  $salary_dir = 'files/salary/';
  $pf_dir = 'files/pf/';
  $epf_dir = 'files/epf/';

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from emp_salary where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  if(isset($_POST['Submit'])){
    $firm = $_POST['firm'];
    $mnt = $_POST['mnt'];
    $start_yr = $_POST['start_yr'];
    $end_yr = $_POST['end_yr'];    
    
    $attend = $_FILES['attend']['name'];
    $attend_imgTmp = $_FILES['attend']['tmp_name'];
    $attend_imgSize = $_FILES['attend']['size'];
   
    $salary = $_FILES['salary']['name'];
    $salary_imgTmp = $_FILES['salary']['tmp_name'];
    $salary_imgSize = $_FILES['salary']['size'];

    $pf = $_FILES['pf']['name'];
    $pf_imgTmp = $_FILES['pf']['tmp_name'];
    $pf_imgSize = $_FILES['pf']['size'];

    $epf = $_FILES['epf']['name'];
    $epf_imgTmp = $_FILES['epf']['tmp_name'];
    $epf_imgSize = $_FILES['epf']['size'];        


   
    if($attend){

      $attendimgExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));

      $attendallowExt  = array('jpeg', 'jpg', 'png', 'gif', 'pdf', 'doc', 'xls', 'docx', 'xlsx');

      $attendimage = time().'_'.rand(1000,9999).'.'.$attendimgExt;

      if(in_array($attendimgExt, $attendallowExt)){

        if($attend_imgSize < 5000000){
          unlink($attend_dir.$row['attend']);
          move_uploaded_file($attend_imgTmp ,$attend_dir.$attendimage);
        }else{
          $errorMsg = 'Image too large';
        }
      }else{
        $errorMsg = 'Please select a valid image';
      }
    }else{

      $attendimage = $row['attend'];
    }

    if($salary){

          $salaryimgExt = strtolower(pathinfo($salary, PATHINFO_EXTENSION));

          $salaryallowExt  = array('jpeg', 'jpg', 'png', 'gif', 'pdf', 'doc', 'xls', 'docx', 'xlsx');

          $salaryimage = time().'_'.rand(1000,9999).'.'.$salaryimgExt;

          if(in_array($salaryimgExt, $salaryallowExt)){

            if($salary_imgSize < 5000000){
              unlink($salary_dir.$row['salary']);
              move_uploaded_file($salary_imgTmp ,$salary_dir.$salaryimage);
            }else{
              $errorMsg = 'Image too large';
            }
          }else{
            $errorMsg = 'Please select a valid image';
          }
        }else{

          $salaryimage = $row['salary'];
        }

    if($pf){

          $pfimgExt = strtolower(pathinfo($pf, PATHINFO_EXTENSION));

          $pfallowExt  = array('jpeg', 'jpg', 'png', 'gif', 'pdf', 'doc', 'xls', 'docx', 'xlsx');

          $pfimage = time().'_'.rand(1000,9999).'.'.$pfimgExt;

          if(in_array($pfimgExt, $pfallowExt)){

            if($pf_imgSize < 5000000){
              unlink($pf_dir.$row['pf']);
              move_uploaded_file($pf_imgTmp ,$pf_dir.$pfimage);
            }else{
              $errorMsg = 'Image too large';
            }
          }else{
            $errorMsg = 'Please select a valid image';
          }
        }else{

          $pfimage = $row['pf'];
        }

    if($epf){

          $epfimgExt = strtolower(pathinfo($epf, PATHINFO_EXTENSION));

          $epfallowExt  = array('jpeg', 'jpg', 'png', 'gif', 'pdf', 'doc', 'xls', 'docx', 'xlsx');

          $epfimage = time().'_'.rand(1000,9999).'.'.$epfimgExt;

          if(in_array($epfimgExt, $epfallowExt)){

            if($epf_imgSize < 5000000){
              unlink($epf_dir.$row['epf']);
              move_uploaded_file($epf_imgTmp ,$epf_dir.$epfimage);
            }else{
              $errorMsg = 'Image too large';
            }
          }else{
            $errorMsg = 'Please select a valid image';
          }
        }else{

          $epfimage = $row['epf'];
        }    
  
  
    if(!isset($errorMsg)){
      $sql = "update emp_salary
                  set firm = '".$firm."',
                    start_yr = '".$start_yr."',
                    end_yr = '".$end_yr."',                    
                    mnt = '".$mnt."',
                    attend = '".$attendimage."',
                    salary = '".$salaryimage."',
                    pf = '".$pfimage."',
                    epf = '".$epfimage."'


          where id=".$id;
      $result = mysqli_query($conn, $sql);
      if($result){
        $successMsg = 'New record updated successfully';
        header('Location:index.php');
      }else{
        $errorMsg = 'Error '.mysqli_error($conn);
      }
    }

  }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Employee Salary List</title>
   <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../style4.css">
    <link rel="stylesheet" href="../css/custom.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>
     <script> 
    $(function(){
     $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html"); 
    });
    </script>   

<style type="text/css">
.modal-dialog {width:800px;}
.thumbnail {margin-bottom:6px;}
.modal.fade .modal-dialog {
      -webkit-transform: translate(0, 0);
      -ms-transform: translate(0, 0); // IE9 only
          transform: translate(0, 0);

 }
</style>
    
    
    
  </head>
  <body>


    <div class="wrapper">
      <div id="sidebarnav"></div>

        <!-- Page Content  -->
        <div id="content">
          <div id="topnav"></div>


    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
      <div class="container">
        <a class="navbar-brand" href="index.php">Employee List</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto"></ul>
            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index.php"><i class="fa fa-sign-out-alt"></i>Back</a></li>
            </ul>
        </div>
      </div>
    </nav>

      <div class="container">
        
      </div>
</div>
</div>

 
    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

  </body>
</html>
