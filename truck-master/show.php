<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
  $dir = "/static_file_storage/"; 

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from truck_master where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Truck Details </title>
 
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Truck Databse Details</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
         


            <?php
          $page=1;
          if(isset($_GET['page']))
          {
            $page=$_GET['page'];
          }
          ?>

             <ul class="navbar-nav ml-auto"><a class="btn btn-outline-danger" href="index.php?page=<?php echo $page ?>"><i class="fa fa-sign-out-alt"></i><span>Back</span></a>
                            </ul>


          </div>
        </div>
      </nav>

               <!-- last change on Record START-->
          <?php
          $sqlLastChange="select username,updated_at from truck_master where id='".$row['id']."'";

           $resultlLastChange = mysqli_query($conn, $sqlLastChange);

           if(mysqli_num_rows($resultlLastChange)>0)
           {
              $lastChangeRow=mysqli_fetch_assoc($resultlLastChange);

              //.get username from user master
              $user_name='';
              $sqlgetUser="select * from users where id='".$lastChangeRow['username']."'";
              $sqlResultGetUser = mysqli_query($conn, $sqlgetUser);
              if(mysqli_num_rows($sqlResultGetUser)>0)
              {
                $getUserRow=mysqli_fetch_assoc($sqlResultGetUser);
                $user_name=$getUserRow['name'];
              }


              echo "
               <div class='last-updates'>
                      <span class='fullch'><span class='chtext'><span class='icon-edit'></span>Last Updated By :</span> <span class='userch'>".$user_name."</span> - <span class='datech'>".date('d/m/Y h:i:s A', strtotime($lastChangeRow['updated_at']))."</span> </span>
                 </div>
              ";
           }
          ?>

          <!-- last change on record END-->

      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="card">
            <div class="card-header">
              Truck Details
            </div>
            <div class="card-body">
              <div class="row">
               
              

                  <div class="field-show mb-3 col-sm-6">
                    <div class="label">
                    <h6 class="title">Truck No.</h6>
                    </div>
                    <div class="form-control view-form">  
                      <span><?php echo $row['truck_no'] ?></span>
                    </div>
                    </div>



                    <?php
                           $transport='';
                            $sql2 = "select * from transport where id='".$row['transport']."'";
                            $result2 = mysqli_query($conn, $sql2);

                            if(mysqli_num_rows($result2)>0)
                            {
                              $row2=mysqli_fetch_array($result2);
                              $transport=$row2['trans_name'];
                            }
                            ?>  


                  <div class="field-show mb-3 col-sm-6">
                    <div class="label">
                    <h6 class="title">Transport</h6>
                    </div>
                    <div class="form-control view-form">  
                      <span><?php echo $transport ?></span>
                    </div>
                    </div>

</div>

                <div class="row">
                  <?php
                    if($row['rc'] != ''){
                    $attend =  $dir.$row['rc'];
                    if($attend)
                    {
                    $attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));
                    $attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');
                    if(in_array($attendExt, $attend_allowExt)) 
                    {
                    ?>
                    <div class="form-group col-md-4 field-show-image">
                    <div class="image-upload">  
                    <div class="label">
                    <h6 class="title">RC File</h6>
                    </div>
                    <div class="filed-form-control">  
                    <img src="<?php echo $dir.$row['rc'] ?>"  data-toggle="modal" data-target="#myModal" id="1" onerror="this.onerror=null; this.src='../../image/no-image.jpg'" height="300" width="300">
                    </div>
                    <div class="text-center mt-3"> (Click Image to Open)</div>                                                 
                    </div>
                    </div>
                    <?php
                    }
                    else
                    {
                    ?>
                    <div class="form-group col-md-4 field-show-image">
                    <div class="image-upload">  
                    <div class="label">
                    <h6 class="title">RC File</h6>
                    </div>
                    <div class="filed-form-control">  
                    <a href="<?php echo $dir.$row['rc'] ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>
                    </div>
                    </div>
                    </div>
                    <?php
                    }
                    }
                    }
                    ?>                                            



                   <?php
                    if($row['kyc'] != ''){
                    $attend =  $dir.$row['kyc'];
                    if($attend)
                    {
                    $attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));
                    $attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');
                    if(in_array($attendExt, $attend_allowExt)) 
                    {
                    ?>
                    <div class="form-group col-md-4 field-show-image pl-0">
                    <div class="image-upload">  
                    <div class="label">
                    <h6 class="title">KYC File</h6>
                    </div>
                    <div class="filed-form-control">  
                    <img src="<?php echo $dir.$row['kyc'] ?>"  data-toggle="modal" data-target="#myModal" id="1" onerror="this.onerror=null; this.src='../../image/no-image.jpg'" height="300" width="300">
                    </div>
                    <div class="text-center mt-3"> (Click Image to Open)</div>                                                 
                    </div>
                    </div>
                    <?php
                    }
                    else
                    {
                    ?>
                    <div class="form-group col-md-4 field-show-image pl-0">
                    <div class="image-upload">  
                    <div class="label">
                    <h6 class="title">KYC File</h6>
                    </div>
                    <div class="filed-form-control">  
                    <a href="<?php echo $dir.$row['kyc'] ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>
                    </div>
                    </div>
                    </div>
                    <?php
                    }
                    }
                    }
                    ?>   

</div>
                                      <div id="myModal" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-body">
                    <img class="img-responsive" src="" width="600" height="600" />
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                    </div>
                    </div>
                    </div>
                      
                </div>

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
      <script type="text/javascript">
      
          $(document).ready(function () {
        $('#myModal').on('show.bs.modal', function (e) {
            var image = $(e.relatedTarget).attr('src');
            $(".img-responsive").attr("src", image);
        });
        });
    
       </script>
    </body>
  </html>
