<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
  $dir = "/static_file_storage/"; 
  $unlink_path=$_SERVER['DOCUMENT_ROOT'].$dir;

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

  if(isset($_POST['Submit'])){
    $truck_no = $_POST['truck_no'];
    $transport = $_POST['transport'];    
    
    $rc = $_FILES['rc']['name'];
    $rc_imgTmp = $_FILES['rc']['tmp_name'];
    $rc_imgSize = $_FILES['rc']['size'];

     $kyc = $_FILES['kyc']['name'];
    $kyc_imgTmp = $_FILES['kyc']['tmp_name'];
    $kyc_imgSize = $_FILES['kyc']['size'];

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");


  include_once('../global_function.php'); 
  $data=getStaticFileStoragePath("truck-master");  //from global_function.php
  $root_path=$data[0]; // file move path
  $store_path=$data[1]; // db store path
   

   
    if($rc){

      $rcimgExt = strtolower(pathinfo($rc, PATHINFO_EXTENSION));

      $rcallowExt  = array('jpeg', 'jpg', 'png', 'gif', 'pdf', 'doc', 'xls', 'csv', 'docx', 'xlsx');

      $rcimage = time().'_'.rand(1000,9999).'.'.$rcimgExt;

      if(in_array($rcimgExt, $rcallowExt)){

        if($rc_imgSize < 5000000){

          if($row['rc']!='')
          {
            unlink($unlink_path.$row['rc']);
          }
          move_uploaded_file($rc_imgTmp ,$root_path.$rcimage);
          $rcimage=$store_path.$rcimage;

        }else{
          $errorMsg = 'Image too large';
        }
      }else{
        $errorMsg = 'Please select a valid image';
      }
    }else{

      $rcimage = $row['rc'];
    }



    if($kyc){

      $kycimgExt = strtolower(pathinfo($kyc, PATHINFO_EXTENSION));

      $kycallowExt  = array('jpeg', 'jpg', 'png', 'gif', 'pdf', 'doc', 'xls', 'csv', 'docx', 'xlsx');

      $kycimage = time().'_'.rand(1000,9999).'.'.$kycimgExt;

      if(in_array($kycimgExt, $kycallowExt)){

        if($kyc_imgSize < 5000000){
          if($row['kyc']!='')
          {
            unlink($unlink_path.$row['kyc']);
          }

          move_uploaded_file($kyc_imgTmp ,$root_path.$kycimage);
          $kycimage=$store_path.$kycimage;
        
        }else{
          $errorMsg = 'Image too large';
        }
      }else{
        $errorMsg = 'Please select a valid image';
      }
    }else{

      $kycimage = $row['kyc'];
    }

    
  
  
    if(!isset($errorMsg)){
      $sql = "update truck_master
                  set truck_no = '".$truck_no."',
                    transport = '".$transport."',
                    rc = '".$rcimage."',
                    kyc = '".$kycimage."',
                    username = '".$username."',
                    updated_at = '".$timestamp."'

          where id=".$id;
      $result = mysqli_query($conn, $sql);
      if($result){
        $successMsg = 'New record updated successfully';
        $page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
        header("Location: index.php?page=$page");
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
    <title>Edit Truck Database</title>
 
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

        $('.searchDropdown').selectpicker();

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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit Truck Database</span></a>
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

          

            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index.php?page=<?php echo $page ?>"><i class="fa fa-sign-out-alt"></i>Back</a></li>
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
                Update Truck Database
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">
                  <div class="row">
                    <div class="form-group col-sm-6">
                      <label for="truck_no">Truck No.</label>
                      <input type="text" class="form-control" name="truck_no"  placeholder="Enter Name" value="<?php echo $row['truck_no']; ?>">
                    </div>

                  
                    <div class="form-group col-sm-6">
                      <label for="transport">Select Transport</label>
                      <?php
                            $sql = "select * from transport";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>
                      
                           <select name="transport" data-live-search="true" class="form-control searchDropdown" >
                         
                            
                            <?php                   
                                foreach ($conn->query($sql) as $result) {

                                  if($row['transport']==$result['id'])
                                  {
                                     echo "<option  value='".$result['id']."' selected>" .$result['trans_name']. "</option>";  
                                  }
                                  else
                                  {
                                       echo "<option  value=" .$result['id']. ">" .$result['trans_name']. "</option>";  
                                  }

                                   

                                }
                            ?>
                        </select>
                    </div>
                 </div>

<div class="row">       
<div class="form-group col-md-4">
<label for="kyc">KYC File</label>
<?php
$file3 = $row['kyc'];
if($file3 != '')
{
$attend =  $dir.$row['kyc'];
if($attend)
{
$attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));
$attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');
if(in_array($attendExt, $attend_allowExt)) 
{
?>
<div class="image-upload">
<img id="preview-img1" src="<?php echo $dir.$row['kyc'] ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" class="img-fluid" height="300" width="300">
</div>
<?php          
}
else
{ 
?>
<div class="file-section">
<div class="field-show-image">
<div class="image-upload editupload">  
<img id="preview-img1" src="<?php echo $dir.$row['kyc'] ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" class="img-fluid" height="250" width="300">
<div class="filed-form-control">  
<a href="<?php echo $dir.$row['kyc'] ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>
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
<img id="preview-img1" src="<?php echo $dir.$row['kyc'] ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" class="img-fluid" height="300" width="300"> 
</div>
          <?php } ?>                                          
<div class="image-upload-edit">
<input type="file" id="img1"  onchange="readURL(this);" class="form-control" name="kyc" value="">
</div>
</div>

<div class="form-group col-md-4">
<label for="rc">RC File</label>
<?php
$file3 = $row['rc'];
if($file3 != '')
{
$attend =  $dir.$row['rc'];
if($attend)
{
$attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));
$attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');
if(in_array($attendExt, $attend_allowExt)) 
{
?>
<div class="image-upload">
<img id="preview-img2" src="<?php echo $dir.$row['rc'] ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" class="img-fluid" height="300" width="300">
</div>
<?php          
}
else
{ 
?>
<div class="file-section">
<div class="field-show-image">
<div class="image-upload editupload">  
<img id="preview-img2" src="<?php echo $dir.$row['rc'] ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" class="img-fluid" height="250" width="300">
<div class="filed-form-control">  
<a href="<?php echo $dir.$row['rc'] ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>
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
<img id="preview-img2" src="<?php echo $dir.$row['rc'] ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" class="img-fluid" height="300" width="300"> 
</div>
          <?php } ?>                                          
<div class="image-upload-edit">
<input type="file" id="img2"  onchange="readURL(this);" class="form-control" name="rc" value="">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
          <script>

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


    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>


  </body>
</html>
