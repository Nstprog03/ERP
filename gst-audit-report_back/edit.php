<?php
  require_once('../db.php');
  $assreport_dir = 'files/gstreport/';

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from gst_report where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  if(isset($_POST['Submit'])){
		$party_name = $_POST['party_name'];
    $gst_report_type = $_POST['gst_report_type'];
    $gst_report_name = $_POST['gst_report_name'];
		$gst_report_yr = $_POST['gst_report_yr'];
    $gst_report_eyr = $_POST['gst_report_eyr'];
    $gst_report_mnt = $_POST['gst_report_mnt'];
    
    

		$gst_report_img = $_FILES['gst_report_img']['name'];
		$imgTmp = $_FILES['gst_report_img']['tmp_name'];
		$imgSize = $_FILES['gst_report_img']['size'];


   
		if($gst_report_img){

			$imgExt = strtolower(pathinfo($gst_report_img, PATHINFO_EXTENSION));

			$allowExt  = array('jpeg', 'jpg', 'png', 'gif');

			$userPic = time().'_'.rand(1000,9999).'.'.$imgExt;

			if(in_array($imgExt, $allowExt)){

				if($imgSize < 5000000){
					unlink($assreport_dir.$row['gst_report_img']);
					move_uploaded_file($imgTmp ,$assreport_dir.$userPic);
				}else{
					$errorMsg = 'Image too large';
				}
			}else{
				$errorMsg = 'Please select a valid image';
			}
		}else{

			$userPic = $row['gst_report_img'];
		}

   
  
  
		if(!isset($errorMsg)){
			$sql = "update gst_report
									set party_name = '".$party_name."',
										gst_report_type = '".$gst_report_type."',
                    gst_report_name = '".$gst_report_name."',
                    gst_report_yr = '".$gst_report_yr."',
                    gst_report_eyr = '".$gst_report_eyr."',
                    gst_report_mnt = '".$gst_report_mnt."',
                    gst_report_img = '".$userPic."'

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
    <title>Update GST Report</title>


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
      <div class="container-fluid">
        <a class="navbar-brand" href="index.php">GST Report</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto"></ul>
            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index.php"><i class="fa fa-sign-out-alt"></i></a></li>
            </ul>
        </div>
      </div>
    </nav>

      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                Update GST Report
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                      <label for="party_name">Party Name</label>
                      <input type="text" class="form-control" name="party_name"  placeholder="Enter Name" value="<?php echo $row['party_name']; ?>">
                    </div>
                    <div class="form-group">
                      <label for="gst_report_type">Assement Report Type</label>
                      <input type="text" class="form-control" name="gst_report_type" placeholder="Enter Mobile Number" value="<?php echo $row['gst_report_type']; ?>">
                    </div>
                    <div class="form-group">
                      <label for="gst_report_name">Assement Report Name</label>
                      <input type="text" class="form-control" name="gst_report_name" placeholder="Enter Mobile Number" value="<?php echo $row['gst_report_name']; ?>">
                    </div>
                    <div class="form-group">
                      <label for="gst_report_yr">GST Report Strart Year</label>
                      <input type="text" class="form-control" name="gst_report_yr" placeholder="Enter Email" value="<?php echo $row['gst_report_yr']; ?>">
                    </div>

                        <div class="form-group">
                      <label for="gst_report_eyr">GST Report End Year</label>
                      <input type="text" class="form-control" name="gst_report_eyr" placeholder="Enter Email" value="<?php echo $row['gst_report_eyr']; ?>">
                    </div>

                     <div class="form-group">
                      <label for="gst_report_mnt">GST Report Month</label>
                      <input type="text" class="form-control" name="gst_report_mnt" placeholder="Enter Email" value="<?php echo $row['gst_report_mnt']; ?>">
                    </div>
                   
                    <div class="form-group">
                      <label for="gst_report_img">GST Report Image</label>
                      <div class="col-md-4">
                        <img src="<?php echo $assreport_dir.$row['gst_report_img'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" width="100">
                        <input type="file" class="form-control" name="gst_report_img" value="">
                      </div>
                    </div>
                    

                    <div class="form-group">
                      <button type="submit" name="Submit" class="btn btn-primary waves">Submit</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>

  </body>
</html>
