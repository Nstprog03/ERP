<?php
session_start();
require_once('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

if(isset($_SESSION["user_type"]) && $_SESSION["user_type"] != 'master'){
    header("location: ../index.php");
    exit;
}
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from users where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }



  if(isset($_POST['submit'])){


    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $status = $_POST['status'];


    if($_POST['password']!='')
    {
      $password=password_hash($_POST['password'], PASSWORD_DEFAULT);
    }
    else
    {
      $password=$row['password'];
    }

    if($row['user_type']=='master')
    {
      $status=1;
    }


    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");

  
    if(!isset($errorMsg)){
      $sql = "UPDATE `users` SET 
      `name`='".$name."',
      `email`='".$email."',
      `username`='".$username."',
      `password`='".$password."',
      `user_status`='".$status."',
      `updated_at`='".$timestamp."'
      WHERE id='".$_GET['id']."'";
      $result = mysqli_query($conn, $sql);


      if($result)
      {
        $successMsg = 'New record updated successfully';
        header('Location:index.php');
      }else{
        $errorMsg = 'Error '.mysqli_error($conn);
        echo $errorMsg;
      }
    }

  }


?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Users Master</title>
  
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../style4.css">
    <link rel="stylesheet" href="../css/custom.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

     <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>
    
    <!-- jQuery UI library -->
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>


     <script> 

    $(function(){
     $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html"); 
    });
    </script> 
  </head>
  <body>

    
    <div class="wrapper">
      <div id="sidebarnav"></div>

        <!-- Page Content  -->
        <div id="content">
          <div id="topnav"></div>

    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
      <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Users Master</a>
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
        <div class="row justify-content-center">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">Edit User</div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">
                  
                  <div class="row">
                    <div class="form-group col-md-4">
                      <label for="name">Name</label>
                      <input 
                      id="name" type="text" name="name" class="form-control" placeholder="Enter Name" value="<?php echo $row['name'] ?>" required="">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="email">Email</label>
                      <input type="text" name="email" class="form-control" placeholder="Enter Email" value="<?php echo $row['email'] ?>">
                    </div>


                    <?php
                        $status='';
                        $msg='';
                        if($row['user_type']=='master')
                        { 
                          $msg=' <b>(Master Admin Account)</b>';
                          $status='disabled';  
                        }
                    ?>


                    <div class="form-group col-md-4">
                        <label for="status">Status <?php echo $msg; ?></label>
                          <select name="status" class="form-control" <?php echo $status; ?>>
                           
                            <option value="1"  <?php if($row['user_status']=='1'){echo 'selected';}?>>Active</option>
                            <option value="0"  <?php if($row['user_status']=='0'){echo 'selected';}?>>Inactive</option>
                          </select>
                      </div>



                    <div class="form-group col-md-4">
                      <label for="username"></label>
                       <label for="username">Username</label>
                      <input type="text"  name="username" class="form-control" placeholder="Enter Username"  onkeypress="return spaceNotAllowedValidation(event)" value="<?php echo $row['username'] ?>" required>
                    </div>

                     <div class="form-group col-md-4">
                      <label for="password"></label>
                       <label for="password">Change Password</label>
                      <input id="password" type="password"  name="password" class="form-control" placeholder="Enter Password" onkeypress="return spaceNotAllowedValidation(event)">
                     </div>

                      <div class="form-group col-md-4">
                      <label for="conf_password"></label>
                       <label for="password">Confirm Password</label>
                      <input id="conf_password" type="password"  name="conf_password" class="form-control" onkeypress="return spaceNotAllowedValidation(event)" placeholder="Confirm Password">
                     </div>

                   


                  </div>
                 

                    <div class="form-group">
                      <button type="submit" name="submit" class="btn btn-primary waves">Submit</button>
                    </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

</div>
</div>
   
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <script>



      $(document).ready(function() {



      
        $('#password').on('input', function() {

          var conf_password = $('#conf_password').val();
           $('span.error-keyup-1').hide();
          $(':input[type="submit"]').prop('disabled', false);

          if(conf_password!='')
          {

            if(conf_password!=this.value)
            {
                $('#conf_password').after('<span class="error error-keyup-1 text-danger">Password Not Match...</span>');
                $(':input[type="submit"]').prop('disabled', true);
            }
            else
            {
              $('#conf_password').after('<span class="error error-keyup-1 text-success">Password Matched...</span>');

              $(':input[type="submit"]').prop('disabled', false);
            }

          }


             
             

        });


        $('#conf_password').on('input', function() {

            var password = $('#password').val();

             $('span.error-keyup-1').hide();
             $(':input[type="submit"]').prop('disabled', false);


            if(password!=this.value)
            {
                $(this).after('<span class="error error-keyup-1 text-danger">Password Not Match...</span>');
                $(':input[type="submit"]').prop('disabled', true);
            }
            else
            {
              $(this).after('<span class="error error-keyup-1 text-success">Password Matched...</span>');

              $(':input[type="submit"]').prop('disabled', false);
            }


  
           
           

        });


    });






      function spaceNotAllowedValidation(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode==32)
        {
          return false;
        }
        
        return true;       
      }   

    </script>
  </body>
</html>
