<?php
  require_once('../db.php');
  $trans_dir = 'files/trans/';
  $audit_dir = 'files/audit/';
  $doc_dir = 'files/doc/';
  $sales_dir = 'files/sales/';

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from pro_audit where id=".$id;
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
    <title>Program Audit Report</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">

        <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
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
        <div class="container">
          <a class="navbar-brand" href="index.php">Program Audit Report</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
                            <ul class="navbar-nav"><a class="btn btn-outline-danger" href="index.php"><i class="fa fa-sign-out-alt"></i><span>Back</span></a>
                            </ul>


          </div>
        </div>
      </nav>

      <div class="container">
        <div class="row justify-content-center">
          <div class="card">
            <div class="card-header">
             Program Audit Report
            </div>
            <div class="card-body">
              <div class="row">
               
                <div class="col-md">
                    <h5 class="form-control">
                      <span class="title">Party Name</span>
                      <span><?php echo $row['party'] ?></span>
                    </h5>
                    <h5 class="form-control">
                      <span class="title">Organization Name</span>
                      <span><?php echo $row['org'] ?></span>
                    </h5>
                    
                    <h5 class="form-control">
                      <span class="title">Years</span>
                      <span><?php echo $row['start_yr'] ?></span>
                      <span><?php echo $row['end_yr'] ?></span>
                    </h5>

                    
                    <span class="title">Transaction Image </span>
                    <img src="<?php echo $trans_dir.$row['trans'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" height="200">

                    <br/>

                     <span class="title">Audit Report Image </span>
                    <img src="<?php echo $audit_dir.$row['audit'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" height="200">

                    <br/>

                     <span class="title">Document Image</span>
                    <img src="<?php echo $doc_dir.$row['doc'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" height="200">

                    <br/>

                     <span class="title">Sales Image </span>
                    <img src="<?php echo $sales_dir.$row['sales'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" height="200">

                  

                      
                </div>

              </div>
            </div>
          </div>
        </div>

      </div>
</div>
</div>


     <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
      <script type="text/javascript">
      $(document).ready(function() {
          $('#example').DataTable();
        } );
       </script>
    </body>
  </html>
