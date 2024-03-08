<?php
session_start();
include('../../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../../login.php");
    exit;
}
if(!isset($_SESSION["qis_form"]) || !isset($_SESSION["qis_firm"]) || !isset($_SESSION["qis_quarter"]) || !isset($_SESSION["qis_year"])){
    header("location:index.php");
    exit;
}

$dir = "/static_file_storage/"; 
  $unlink_path=$_SERVER['DOCUMENT_ROOT'].$dir;

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select q.*,p.party_name from qis q,party p where q.firm=p.id AND q.id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  if(isset($_POST['Submit'])){
		
  $firm_id=explode('/',$_SESSION['qis_firm'])[1];
  $year=$_SESSION['qis_year'];
  $form=$_SESSION['qis_form'];
  $quarter=$_SESSION['qis_quarter'];

  include_once('../../global_function.php'); 
  $data=getStaticFileStoragePath("qis");  //from global_function.php
  $root_path=$data[0]; // file move path
  $store_path=$data[1]; // db store path

  



		$image = $_FILES['q1_form1']['name'];
		$image_tmp = $_FILES['q1_form1']['tmp_name'];
		$image_size = $_FILES['q1_form1']['size'];
   
		if($image)
    {

			$image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

			$allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'xlsx', 'xls', 'doc', 'pdf', 'docx', 'csv');

			$image = time().'_'.rand(1000,9999).'.'.$image_ext;

			if(in_array($image_ext, $allowExt)){

				if($image_size < 5000000){

          if($row['file']!='') //delete old file
          {
            unlink($unlink_path.$row['file']);
          }
          move_uploaded_file($image_tmp ,$root_path.$image);
          $image=$store_path.$image;
				}else{
					$errorMsg = 'Image too large';
				}
			}else{
				$errorMsg = 'Please select a valid image';
			}
		}
    else{

			$image = $row['file'];
		}



  
		if(!isset($errorMsg)){


      if($image=='')
      {
        $status='pending';
      }
      else
      {
        $status='completed';
      }

      $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");


			$sql = "update qis
									set firm = '".$firm_id."',
                  	year = '".$year."',
                    quarter = '".$quarter."',                    
                    status = '".$status."',
                    file = '".$image."',
                    username = '".$username."',
                    updated_at = '".$timestamp."'
                
                
					           where id=".$id;

			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'New record updated successfully';
				header('Location:index1.php');
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
    <title>QIS Report</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">

        <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
   <link rel="stylesheet" href="../../style4.css">
    <link rel="stylesheet" href="../../css/custom.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>
     <script> 
    $(function(){
      $("#sidebarnav").load("../../nav.html"); 
      $("#topnav").load("../../nav2.html"); 
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit QIS Report</span></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
              
                            <ul class="navbar-nav ml-auto">
                              <li class="nav-item mr-2"><a class="btn btn-outline-danger" href="index1.php"><i class="fa fa-sign-out-alt"></i><span class="pl-1">Back</span></a></li>
                              <li class="nav-item mr-2"><a href="show.php?id=<?php echo $row['id'] ?>" class="btn btn-success"><i class="fa fa-eye"></i><span class="pl-1">Show</span></a></li>
                              <li class="nav-item"><a class="btn btn-outline-primary" href="create.php"><i class="fa fa-user-plus"></i><span class="pl-1">Add Record</span></a></li>
                              
                            </ul>


          </div>
      </div>
    </nav>

     <!-- last change on Record START-->
          <?php
          $sqlLastChange="select username,updated_at from qis where id='".$id."'";

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
                Edit QIS Report
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">

                  <div class="row">
                  <div class="form-group col-sm-4">
                      <label for="start_date">Firm</label>
                      <input class="form-control" type="text" name="firm" value="<?php echo $row['party_name'] ?>" readonly>
                    </div>

                    <div class="form-group col-sm-4">
                      <label for="start_date">Form</label>
                      <input class="form-control" type="text" name="firm" value="<?php echo $row['form']; ?>" readonly>
                    </div>

                    <div class="form-group col-sm-4">
                      <label for="start_date">Quarter</label>
                      <input class="form-control" type="text" name="firm" value="<?php echo $row['quarter']; ?>" readonly>
                    </div>

                    <div class="form-group col-sm-4">
                      <label for="start_date">Year</label>
                      <input class="form-control" type="text" name="firm" value="<?php echo $row['year']; ?>" readonly>
                    </div>

                </div>
                    


                  <div class="row">

<div class="form-group col-md-4">
<label class="image-label" for="file">
QIS <?php echo $_SESSION['qis_quarter'].' '.$_SESSION['qis_form'] ?> File
</label>
  <?php
  $file3 = $row['file'];
    if($file3 != '')
      {
      $attend =  $dir.$row['file'];
      if($attend)
        {
              $attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));
              $attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');
                      if(in_array($attendExt, $attend_allowExt)) 
                          {
                            ?>
                                <div class="image-upload">
                              <img id="preview-img1" src="<?php echo $dir.$row['file'] ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" class="img-fluid" height="300" width="300">
                          </div>
                <?php          
                                }
                                else
                                { 
                                ?>
                                <div class="file-section">
                                  <div class="field-show-image">
                                  <div class="image-upload editupload">  
                                  <img id="preview-img1" src="<?php echo $dir.$row['file'] ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" class="img-fluid" height="250" width="300">
                                  <div class="filed-form-control">  
                                    <a href="<?php echo $dir.$row['file'] ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>
                                  </div>
                                  </div>
                                  </div>                                                  
                </div>
                            <?php  }
                }
                } ?>

    <?php if ($file3 == '')
        { ?>
                <div class="image-upload">
                <img id="preview-img1" src="<?php echo $dir.$row['file'] ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" class="img-fluid" height="300" width="300"> 
                </div>
          <?php } ?>                                          

                            <div class="image-upload-edit">
                              <input type="file" id="img1"  onchange="readURL(this);" class="form-control" name="q1_form1" value="">
                            </div>
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
    

 function readURL(input) {
            var url = input.value;
            var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();

            $(input).parent().find('span.error-keyup-110').hide();
            if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) 
            {

                var reader = new FileReader();

                const fsize = input.files[0].size;
                const file_size = Math.round((fsize / 1024));


               

                if(file_size>1150) //1.1 MB
                {
                  $(input).after('<span class="error error-keyup-110 text-danger">Image Size Should Be 1 MB or Lesser...</span>');
                  $(input).val(''); 

                   imgId = '#preview-'+$(input).attr('id');
                  $(imgId).attr('src', '../../image/no-prev.jpg');

                }
                else
                {
                    reader.onload = function (e) {
                        imgId = '#preview-'+$(input).attr('id');
                        $(imgId).attr('src', e.target.result);
                    }

                     reader.readAsDataURL(input.files[0]);
                }
                

            }
            else
            {
                  imgId = '#preview-'+$(input).attr('id');
                  $(imgId).attr('src', '../../image/no-prev.jpg');
                  //$(imgId).find(".msg").html("This is not Image");
                 //$('.imagepreview').attr('src', '/assets/no_preview.png');
            }
}
        </script>
  </body>
</html>
