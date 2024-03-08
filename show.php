<?php
  require_once('../db.php');
$upload_dir = 'files/uploads/';
  $gst_dir = 'files/gst/';
  $iec_dir = 'files/iec/';
  $msme_dir = 'files/msme/';
  $fact_dir = 'files/fact/';
  $cheque_dir = 'files/cheque/';

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from party where id=".$id;
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
    <title>PHP CRUD</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/4.0.0/ekko-lightbox.min.css">
    
    
    
  </head>
  <body>

      <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container">
          <a class="navbar-brand" href="index.php">PHP CRUD WITH IMAGE</a>
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
              Party Details
            </div>
            <div class="card-body">
              <div class="row">
               
                <div class="col-md">
                    <h5 class="form-control">
                      <span class="title">Party Name</span>
                      <span><?php echo $row['party_name'] ?></span>
                    </h5>
                    <h5 class="form-control">
                      <span class="title">Party Email</span>
                      <span><?php echo $row['party_email'] ?></span>
                    </h5>
                    <h5 class="form-control">
                      <span class="title">Party Address</span>
                      <span><?php echo $row['party_address'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">IEC code</span>
                      <span><?php echo $row['iec_code'] ?></span>
                    </h5>


                    <h5 class="form-control">
                      <span class="title">Udhyog Aadhar</span>
                      <span><?php echo $row['ud_aadhar'] ?></span>
                    </h5>


                    <h5 class="form-control">
                      <span class="title">Contact Person</span>
                      <span><?php echo $row['contact_per'] ?></span>
                    </h5>


                    <h5 class="form-control">
                      <span class="title">Contact Number</span>
                      <span><?php echo $row['contact_number'] ?></span>
                    </h5>

                         <h5 class="form-control">
                          <span class="title">Bank Name</span>
                      <span><?php echo $row['bank_name'] ?></span>
                    </h5>

                    <h5 class="form-control">
                          <span class="title">Bank AC Number</span>
                      <span><?php echo $row['bank_ac_number'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Bank Branch</span>
                      <span><?php echo $row['bank_branch'] ?></span>
                    </h5>

                    <span class="title">Water ID </span>
                    <img src="<?php echo $upload_dir.$row['waterid_img'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" height="200">

                  </br>

                    <span class="title">GST ID</span>
                    <img src="<?php echo $gst_dir.$row['gst_img'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" height="200">
                    </br>
                    <span class="title">Cancelled Cheque</span>
                    <img src="<?php echo $cheque_dir.$row['cheque_img'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" height="200">
                    </br>
                    <span class="title">IEC Image</span>
                    <img src="<?php echo $iec_dir.$row['iec_img'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" height="200">
                    </br>
                    <span class="title">MSME</span>
                    <img src="<?php echo $msme_dir.$row['msme_img'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" height="200">
                    </br>
                    <span class="title">Fact IMG</span>
                    <img src="<?php echo $fact_dir.$row['fact_img'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" height="200">

                      
                </div>

              </div>
            </div>
          </div>
        </div>

      </div>


      <script src="js/bootstrap.min.js" charset="utf-8"></script>
      <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/4.0.0/ekko-lightbox.js" charset="utf-8"></script>
      <script type="text/javascript">
      $(document).ready(function() {
          $('#example').DataTable();
        } );
       </script>
    </body>
  </html>
