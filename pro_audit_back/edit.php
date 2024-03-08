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

  if(isset($_POST['Submit'])){
		$party_name = $_POST['party'];
    $org = $_POST['org'];
		$start_yr = $_POST['start_yr'];
    $end_yr = $_POST['end_yr'];
    
    

		$trans_img = $_FILES['trans']['name'];
		$transimgTmp = $_FILES['trans']['tmp_name'];
		$transimgSize = $_FILES['trans']['size'];

    $audit_img = $_FILES['audit']['name'];
    $auditimgTmp = $_FILES['audit']['tmp_name'];
    $auditimgSize = $_FILES['audit']['size'];

    $doc_img = $_FILES['doc']['name'];
    $docimgTmp = $_FILES['doc']['tmp_name'];
    $docimgSize = $_FILES['doc']['size'];

    $sales_img = $_FILES['sales']['name'];
    $salesimgTmp = $_FILES['sales']['tmp_name'];
    $salesimgSize = $_FILES['sales']['size'];


   
		if($trans_img){

			$transimgExt = strtolower(pathinfo($trans_img, PATHINFO_EXTENSION));

			$transallowExt  = array('jpeg', 'jpg', 'png', 'gif');

			$userPic = time().'_'.rand(1000,9999).'.'.$transimgExt;

			if(in_array($transimgExt, $transallowExt)){

				if($transimgSize < 5000000){
					unlink($trans_dir.$row['trans']);
					move_uploaded_file($transimgTmp ,$trans_dir.$userPic);
				}else{
					$errorMsg = 'Image too large';
				}
			}else{
				$errorMsg = 'Please select a valid image';
			}
		}else{

			$userPic = $row['trans'];
		}

if($audit_img){

      $auditimgExt = strtolower(pathinfo($audit_img, PATHINFO_EXTENSION));

      $auditallowExt  = array('jpeg', 'jpg', 'png', 'gif');

      $auditimage = time().'_'.rand(1000,9999).'.'.$auditimgExt;

      if(in_array($auditimgExt, $auditallowExt)){

        if($auditimgSize < 5000000){
          unlink($audit_dir.$row['audit']);
          move_uploaded_file($auditimgTmp ,$audit_dir.$auditimage);
        }else{
          $errorMsg = 'Image too large';
        }
      }else{
        $errorMsg = 'Please select a valid image';
      }
    }else{

      $auditimage = $row['audit'];
    }

if($doc_img){

      $docimgExt = strtolower(pathinfo($doc_img, PATHINFO_EXTENSION));

      $docallowExt  = array('jpeg', 'jpg', 'png', 'gif');

      $docimage = time().'_'.rand(1000,9999).'.'.$docimgExt;

      if(in_array($docimgExt, $docallowExt)){

        if($docimgSize < 5000000){
          unlink($doc_dir.$row['doc']);
          move_uploaded_file($docimgTmp ,$doc_dir.$docimage);
        }else{
          $errorMsg = 'Image too large';
        }
      }else{
        $errorMsg = 'Please select a valid image';
      }
    }else{

      $docimage = $row['doc'];
    }   



if($sales_img){

      $salesimgExt = strtolower(pathinfo($sales_img, PATHINFO_EXTENSION));

      $salesallowExt  = array('jpeg', 'jpg', 'png', 'gif');

      $salesimage = time().'_'.rand(1000,9999).'.'.$salesimgExt;

      if(in_array($salesimgExt, $salesallowExt)){

        if($salesimgSize < 5000000){
          unlink($sales_dir.$row['sales']);
          move_uploaded_file($salesimgTmp ,$sales_dir.$salesimage);
        }else{
          $errorMsg = 'Image too large';
        }
      }else{
        $errorMsg = 'Please select a valid image';
      }
    }else{

      $salesimage = $row['sales'];
    }      
  
  
		if(!isset($errorMsg)){
			$sql = "update pro_audit
									set party = '".$party_name."',
                    org = '".$org."',
										start_yr = '".$start_yr."',
                    end_yr = '".$end_yr."',                    
                    trans = '".$userPic."',
                    audit = '".$auditimage."',
                    doc = '".$docimage."',
                    sales = '".$salesimage."'

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
    <title>PHP CRUD</title>
 
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
        <a class="navbar-brand" href="index.php">PHP CRUD WITH IMAGE</a>
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
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                Edit Profile
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                      <label for="party">Party Name</label>
                      <input type="text" class="form-control" name="party"  placeholder="Enter Name" value="<?php echo $row['party']; ?>">
                    </div>
                    <div class="form-group">
                      <label for="org">Organization Name</label>
                      <input type="text" class="form-control" name="org" placeholder="Enter Mobile Number" value="<?php echo $row['org']; ?>">
                    </div>
                    <div class="form-group">
                      <label for="start_yr">Report Start Year</label>
                      <input type="text" class="form-control" name="start_yr" placeholder="Enter Email" value="<?php echo $row['start_yr']; ?>">
                    </div>

                        <div class="form-group">
                      <label for="end_yr">Report End Year</label>
                      <input type="text" class="form-control" name="end_yr" placeholder="Enter Email" value="<?php echo $row['end_yr']; ?>">
                    </div>
                   
                    <div class="form-group">
                      <label for="trans">Transaction Image</label>
                      <div class="col-md-4">
                        <img src="<?php echo $trans_dir.$row['trans'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" width="100">
                        <input type="file" class="form-control" name="trans" value="">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="trans">Audit Image</label>
                      <div class="col-md-4">
                        <img src="<?php echo $audit_dir.$row['audit'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" width="100">
                        <input type="file" class="form-control" name="audit" value="">
                      </div>
                    </div>

                     <div class="form-group">
                      <label for="doc">Doc Image</label>
                      <div class="col-md-4">
                        <img src="<?php echo $doc_dir.$row['doc'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" width="100">
                        <input type="file" class="form-control" name="doc" value="">
                      </div>
                    </div>

                     <div class="form-group">
                      <label for="sales">sales Image</label>
                      <div class="col-md-4">
                        <img src="<?php echo $sales_dir.$row['sales'] ?>" onerror="this.onerror=null; this.src='https://miro.medium.com/fit/c/96/96/1*qM3sCrj0aR_iyvJ8pRs-eg.jpeg'" width="100">
                        <input type="file" class="form-control" name="sales" value="">
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
  </body>
</html>
