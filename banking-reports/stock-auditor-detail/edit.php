<?php
session_start();
include('../../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../../login.php");
    exit;
}
 $dir = "/static_file_storage/"; 
  $unlink_path=$_SERVER['DOCUMENT_ROOT'].$dir;

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from stock_audit where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  if(isset($_POST['Submit'])){
		$firm = $_POST['firm'];
    $start_yr = $_POST['start_yr'];
    $end_yr = $_POST['end_yr'];
    $audit1_name = $_POST['audit1_name'];  
    $audit1_addr = $_POST['audit1_addr'];  
    $audit1_no = $_POST['audit1_no'];  
    $audit2_name = $_POST['audit2_name'];  
    $audit2_addr = $_POST['audit2_addr'];  
    $audit2_no = $_POST['audit2_no']; 

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");

    include_once('../../global_function.php'); 
    $data=getStaticFileStoragePath("stock-auditor-detail");  //from global_function.php
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path


     $aud1_imgArr=array();
    $aud1_filecount = count($_FILES['aud1_doc_file']['tmp_name']);  
    foreach ($_FILES['aud1_doc_file']['tmp_name'] as $key =>  $imges) 
    {

      $aud1_img = $_FILES['aud1_doc_file']['name'][$key];

      $aud1_imgTmp = $_FILES['aud1_doc_file']['tmp_name'][$key];
      $aud1_imgSize = $_FILES['aud1_doc_file']['size'][$key];

  
      if(!empty($aud1_img)){
        
        $aud1_imgExt = strtolower(pathinfo($aud1_img, PATHINFO_EXTENSION));

        $aud1_allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'csv', 'pdf', 'xls', 'xlsx', 'txt');

        $aud1_img = time().'_'.rand(1000,9999).'.'.$aud1_imgExt;
        // array_push($imgArr,$img);
        $aud1_imgArr[$key] = $aud1_img;
        if(in_array($aud1_imgExt, $aud1_allowExt)){

          if($aud1_imgSize < 5000000){
            move_uploaded_file($aud1_imgTmp ,$root_path.$aud1_img);
          }else{
            $errorMsg = 'Image too large';
            echo $errorMsg;
          }
        }else{
          $errorMsg = 'Please select a valid image';
          echo $errorMsg;
        }

      }else{
        $aud1_imgArr[$key] = '';
      }
    }
    
    $aud1_finalimg = array();
    if(count($aud1_imgArr) > 0){
      foreach($aud1_imgArr as $k => $v){
        if($v == "" && isset($_POST['aud1_oldfile'][$k])){
          $aud1_finalimg[] = $_POST['aud1_oldfile'][$k];
        }else{
          if($v!='' && $v!=null)
          {
            $aud1_finalimg[] = $store_path.$v;
          }
          
        }
      }
    }


    $aud1_img_title = $_POST['aud1_img_title'];
    $aud1_imgTitle = implode(',', $aud1_img_title);
    $aud1_imgStore = implode(',', $aud1_finalimg);
    
    $aud1_OldDBImg = explode(',', $row['audit1_doc_file']); 
    $aud1_result1=array_diff($aud1_OldDBImg,$aud1_finalimg);
    foreach ($aud1_result1 as  $item) {
          if($item!='')
          {
            $item=trim($item);             
            unlink($unlink_path.$item); 
          }
    }





    //audit 2 image

     $aud2_imgArr=array();
    $aud2_filecount = count($_FILES['aud2_doc_file']['tmp_name']);  
    foreach ($_FILES['aud2_doc_file']['tmp_name'] as $key =>  $imges) 
    {

      $aud2_img = $_FILES['aud2_doc_file']['name'][$key];

      $aud2_imgTmp = $_FILES['aud2_doc_file']['tmp_name'][$key];
      $aud2_imgSize = $_FILES['aud2_doc_file']['size'][$key];

  
      if(!empty($aud2_img)){
        
        $aud2_imgExt = strtolower(pathinfo($aud2_img, PATHINFO_EXTENSION));

        $aud2_allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'csv', 'pdf', 'xls', 'xlsx', 'txt');

        $aud2_img = time().'_'.rand(1000,9999).'.'.$aud2_imgExt;
        // array_push($imgArr,$img);
        $aud2_imgArr[$key] = $aud2_img;
        if(in_array($aud2_imgExt, $aud2_allowExt)){

          if($aud2_imgSize < 5000000){
            move_uploaded_file($aud2_imgTmp ,$root_path.$aud2_img);
          }else{
            $errorMsg = 'Image too large';
            echo $errorMsg;
          }
        }else{
          $errorMsg = 'Please select a valid image';
          echo $errorMsg;
        }

      }else{
        $aud2_imgArr[$key] = '';
      }
    }
    
    $aud2_finalimg = array();
    if(count($aud2_imgArr) > 0){
      foreach($aud2_imgArr as $k => $v){
        if($v == "" && isset($_POST['aud2_oldfile'][$k])){
          $aud2_finalimg[] = $_POST['aud2_oldfile'][$k];
        }else{
          if($v!='' && $v!=null)
          {
            $aud2_finalimg[] = $store_path.$v;
          }
          
        }
      }
    }


    $aud2_img_title = $_POST['aud2_img_title'];
    $aud2_imgTitle = implode(',', $aud2_img_title);
    $aud2_imgStore = implode(',', $aud2_finalimg);
    
    $aud2_OldDBImg = explode(',', $row['audit2_doc_file']); 
    $aud2_result1=array_diff($aud2_OldDBImg,$aud2_finalimg);
    foreach ($aud2_result1 as  $item) {
        if($item!='')
        {
          $item=trim($item);             
          unlink($unlink_path.$item); 
        }     
    }


  
		if(!isset($errorMsg)){
			$sql = "update stock_audit
									set firm = '".$firm."',
                  	start_yr = '".$start_yr."',
                    end_yr = '".$end_yr."',     
                    audit1_name = '".$audit1_name."',     
                    audit1_addr = '".$audit1_addr."',     
                    audit1_no = '".$audit1_no."',     
                    audit1_doc_file = '".$aud1_imgStore."',
                    audit1_img_title = '".$aud1_imgTitle."',

                    audit2_name = '".$audit2_name."',     
                    audit2_addr = '".$audit2_addr."',     
                    audit2_no = '".$audit2_no."',
                    audit2_doc_file = '".$aud2_imgStore."',
                    audit2_img_title = '".$aud2_imgTitle."',
                    username='".$username."',
                    updated_at='".$timestamp."'

                    
                    

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
    <title>Stock Auditor Report Edit</title>


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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit Stock Audit Report</span></a>
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
          $sqlLastChange="select username,updated_at from stock_audit where id='".$row['id']."'";

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
                Edit Stock Audit Report
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">
                    
                    <div class="row">
                    <div class="form-group col-sm-4">
                      <label for="firm">Select Firm</label>
                      <?php
                            $sql = "select * from party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>
                      
                           <select name="firm" class="form-control">
                              <?php                   
                                  foreach ($conn->query($sql) as $result) 
                                  {

                                    if($row['firm']==$result['id'])
                                    {
                                       echo "<option  value='" .$result['id']. "' selected>" .$result['party_name']. "</option>";  
                                    }
                                    else
                                    {
                                       echo "<option  value='" .$result['id']. "'>" .$result['party_name']. "</option>";  
                                    }

                                     

                                  }
                              ?>
                          </select>
                    </div>

                    
                    <div class="form-group col-sm-4">
                      <label for="start_yr">Report Start Year</label>
                      <select name="start_yr" class="form-control" id="selectElementId">
                       
                    </select>
                    </div>

                    <div class="form-group col-sm-4">
                      <label for="end_yr">Report End Year</label>
                      <select name="end_yr" class="form-control" id="endYear">
                       
                    </select>
                    </div>
</div>
<div class="card-header inn-head">Auditor-1 Details</div>
<div class="row row-border">
                    <div class="form-group col-sm-4">
                      <label for="audit1_name">Auditor Name</label>
                      <input type="text" class="form-control" name="audit1_name" placeholder="Enter Email" value="<?php echo $row['audit1_name']; ?>">
                    </div>

                    <div class="form-group col-sm-4">
                      <label for="audit1_addr">Auditor Address</label>
                      <input type="text" class="form-control" name="audit1_addr" placeholder="Enter Email" value="<?php echo $row['audit1_addr']; ?>">
                    </div>

                    <div class="form-group col-sm-4">
                      <label for="audit1_no">Auditor Contact</label>
                      <input type="text" class="form-control" name="audit1_no" placeholder="Enter Email" value="<?php echo $row['audit1_no']; ?>">
                    </div>                    
                   
</div>


<div class="row aud1_dynamicWrapper">

<?php

if ($row['audit1_doc_file'] != '') {


$prev = explode(',',$row['audit1_doc_file']);
$prev_img_title = explode(',',$row['audit1_img_title']);
foreach ($prev as $key => $imging){
if($imging)
$attend =  $dir.$imging;
{
$attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));
$attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

if(in_array($attendExt, $attend_allowExt)) 
{ ?>

    
      <div class=" form-group  col-sm-4 aud1_imgcount aud1_dynamic_field_<?= $key+1 ?>">
        <label class="image-label" for="doc_file">Auditor 1 Document File <?= $key+1 ?></label>
          <div class="image-upload aud1_dynamic_field">
            <?php if( $key != 0) {?>
              <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="aud1_removeImg(this);">X</button>
            <?php } ?>
            <img id="aud1_preview-img<?= $key+1 ?>" src="<?php echo $dir.$prev[$key] ?>" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />

            <input type="hidden" name="aud1_oldfile[<?= $key?>]" value="<?php echo $prev[$key]; ?>">

            <input type="file" class="form-control" id="img<?= $key+1 ?>" onchange="aud1_readURL(this);" name="aud1_doc_file[<?= $key?>]" value="">
            
            <br>

            <input type="text" class="form-control" placeholder="Enter Image Title" name="aud1_img_title[]" value="<?php echo $prev_img_title[$key]; ?>">
          </div>



      </div>
      <?php
   

}else{
  ?>

  <div class=" form-group  col-sm-4 aud1_imgcount aud1_dynamic_field_<?= $key+1 ?>">
        <label class="image-label" for="doc_file">Auditor 1 Document File <?= $key+1 ?></label>
          <div class="image-upload aud1_dynamic_field">
            <?php if( $key != 0) {?>
              <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="aud1_removeImg(this);">X</button>
            <?php } ?>
            <img id="aud1_preview-img<?= $key+1 ?>" src="<?php echo $dir.$imging ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" class="img-fluid" height="250" width="300">
            <input type="hidden" name="aud1_oldfile[<?= $key?>]" value="<?php echo $imging; ?>">
            <div class="filed-form-control">  
                                 
              <a href="<?php echo $dir.$imging ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>

                               
            </div>

            <input type="file" class="form-control" id="img<?= $key+1?>" onchange="aud1_readURL(this);" name="aud1_doc_file[<?= $key?>]" value="">
            <br>
            <input type="text" class="form-control" placeholder="Enter Image Title" name="aud1_img_title[]" value="<?php echo $prev_img_title[$key]; ?>">
          </div>



      </div>

  <?php
}
}
}
}else{?>

<div class=" form-group  col-sm-4 aud1_imgcount aud1_dynamic_field_1">
    <label class="image-label" for="doc_file">Auditor 1 Document File 1</label>
      <div class="image-upload aud1_dynamic_field">
      
        <img id="aud1_preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
        <input type="file" class="form-control" id="img1" onchange="aud1_readURL(this);" name="aud1_doc_file[1]" value="">
        <br>
        <input type="text" class="form-control" placeholder="Enter Image Title" name="aud1_img_title[]">
      </div>



  </div>


<?php }?>

  <div class="form-group form-group col-sm-4">
    <label class="image-label" for="doc_file">Add more</label>
     <div class="image-upload">
      
     <button type="button" class=" btn btn-defult" id="aud1_add" style="height: 340px;width: 263px;"><i class="fa fa-plus" aria-hidden="true" style="width: 35%;height: 117px;"></i>
      </button>
    

  </div>

  </div>
  </div>




<div class="card-header inn-head">Auditor-2 Details</div>
            <div class="row row-border">

                    <div class="form-group col-sm-4">
                      <label for="audit2_name">Auditor Name</label>
                      <input type="text" class="form-control" name="audit2_name" placeholder="Enter Email" value="<?php echo $row['audit2_name']; ?>">
                    </div>

                    <div class="form-group col-sm-4">
                      <label for="audit2_addr">Auditor Address</label>
                      <input type="text" class="form-control" name="audit2_addr" placeholder="Enter Email" value="<?php echo $row['audit2_addr']; ?>">
                    </div>

                    <div class="form-group col-sm-4">
                      <label for="audit2_no">Auditor Contact</label>
                      <input type="text" class="form-control" name="audit2_no" placeholder="Enter Email" value="<?php echo $row['audit2_no']; ?>">
                    </div>                         

            </div>

            <div class="row aud1_dynamicWrapper">

<?php

if ($row['audit2_doc_file'] != '') {


$prev = explode(',',$row['audit2_doc_file']);
$prev_img_title = explode(',',$row['audit2_img_title']);
foreach ($prev as $key => $imging){
if($imging)
$attend =  $dir.$imging;
{
$attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));
$attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

if(in_array($attendExt, $attend_allowExt)) 
{ ?>

    
      <div class=" form-group  col-sm-4 aud2_imgcount aud2_dynamic_field_<?= $key+1 ?>">
        <label class="image-label" for="doc_file">Auditor 2 Document File <?= $key+1 ?></label>
          <div class="image-upload aud2_dynamic_field">
            <?php if( $key != 0) {?>
              <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="aud2_removeImg(this);">X</button>
            <?php } ?>
            <img id="aud2_preview-img<?= $key+1 ?>" src="<?php echo $dir.$prev[$key] ?>" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />

            <input type="hidden" name="aud2_oldfile[<?= $key?>]" value="<?php echo $prev[$key]; ?>">

            <input type="file" class="form-control" id="img<?= $key+1 ?>" onchange="aud2_readURL(this);" name="aud2_doc_file[<?= $key?>]" value="">
            
            <br>

            <input type="text" class="form-control" placeholder="Enter Image Title" name="aud2_img_title[]" value="<?php echo $prev_img_title[$key]; ?>">
          </div>



      </div>
      <?php
   

}else{
  ?>

  <div class=" form-group  col-sm-4 aud2_imgcount aud2_dynamic_field_<?= $key+1 ?>">
        <label class="image-label" for="doc_file">Auditor 2 Document File <?= $key+1 ?></label>
          <div class="image-upload aud2_dynamic_field">
            <?php if( $key != 0) {?>
              <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="aud2_removeImg(this);">X</button>
            <?php } ?>
            <img id="aud2_preview-img<?= $key+1 ?>" src="<?php echo $dir.$imging ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" class="img-fluid" height="250" width="300">
            <input type="hidden" name="aud2_oldfile[<?= $key?>]" value="<?php echo $imging; ?>">
            <div class="filed-form-control">  
                                 
              <a href="<?php echo $dir.$imging ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>

                               
            </div>

            <input type="file" class="form-control" id="img<?= $key+1?>" onchange="aud2_readURL(this);" name="aud2_doc_file[<?= $key?>]" value="">
            <br>
            <input type="text" class="form-control" placeholder="Enter Image Title" name="aud2_img_title[]" value="<?php echo $prev_img_title[$key]; ?>">
          </div>



      </div>

  <?php
}
}
}
}else{?>

<div class=" form-group  col-sm-4 aud2_imgcount aud2_dynamic_field_1">
    <label class="image-label" for="doc_file">Auditor 2 Document File 1</label>
      <div class="image-upload aud2_dynamic_field">
      
        <img id="aud2_preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
        <input type="file" class="form-control" id="img1" onchange="aud2_readURL(this);" name="aud2_doc_file[1]" value="">
        <br>
        <input type="text" class="form-control" placeholder="Enter Image Title" name="aud2_img_title[]">
      </div>



  </div>


<?php }?>

  <div class="form-group form-group col-sm-4">
    <label class="image-label" for="doc_file">Add more</label>
     <div class="image-upload">
      
     <button type="button" class=" btn btn-defult" id="aud2_add" style="height: 340px;width: 263px;"><i class="fa fa-plus" aria-hidden="true" style="width: 35%;height: 117px;"></i>
      </button>
    

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
       var aud1_delCount=0;
       var aud2_delCount=0;

            
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');

                 });

                 //auditor 1 dynamic image
              var i = 0;

            $("#aud1_add").click(function(){

              var classcount = $('.aud1_imgcount').length

              i=parseInt(classcount)+parseInt(aud1_delCount)+1;
              var varietyfieldHTML= `<div class=" img_section form-group col-sm-4 aud1_imgcount aud1_dynamic_field_`+i+`"><label class="image-label" for="cma">Auditor 1 Document File `+i+`</label><div class="image-upload aud1_dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="aud1_removeImg(this,`+i+`);">X</button><img id="aud1_preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="aud1_readURL(this,`+i+`);" name="aud1_doc_file[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="aud1_img_title[]"></div></div>`;

            //$('.dynamicWrapper').append(varietyfieldHTML);
            
            //$('.dynamic_field_'+i-1).after(varietyfieldHTML);
            /*var j = i-1;

            $('.dynamic_field_'+j).after(varietyfieldHTML);*/


            $('.aud1_imgcount').last().after(varietyfieldHTML);


            });




             //auditor 2 dynamic image
              var i = 0;

            $("#aud2_add").click(function(){

              var classcount = $('.aud2_imgcount').length

              i=parseInt(classcount)+parseInt(aud2_delCount)+1;
              var varietyfieldHTML= `<div class=" img_section form-group col-sm-4 aud2_imgcount aud2_dynamic_field_`+i+`"><label class="image-label" for="cma">Auditor 2 Document File `+i+`</label><div class="image-upload aud2_dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="aud2_removeImg(this,`+i+`);">X</button><img id="aud2_preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="aud2_readURL(this,`+i+`);" name="aud2_doc_file[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="aud2_img_title[]"></div></div>`;

            //$('.dynamicWrapper').append(varietyfieldHTML);
            
            //$('.dynamic_field_'+i-1).after(varietyfieldHTML);
            /*var j = i-1;

            $('.dynamic_field_'+j).after(varietyfieldHTML);*/


            $('.aud2_imgcount').last().after(varietyfieldHTML);


            });




           
        });


         function aud1_removeImg(e,index) {
        $(e).parent('div').parent('div').remove(); 
        aud1_delCount=aud1_delCount+1;
        }

        function aud2_removeImg(e,index) {
        $(e).parent('div').parent('div').remove(); 
        aud2_delCount=aud2_delCount+1;
        }



 function aud1_readURL(input) {
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

                   imgId = '#aud1_preview-'+$(input).attr('id');
                  $(imgId).attr('src', '../../image/no-prev.jpg');

                }
                else
                {
                    reader.onload = function (e) {
                        imgId = '#aud1_preview-'+$(input).attr('id');
                        $(imgId).attr('src', e.target.result);
                    }

                     reader.readAsDataURL(input.files[0]);
                }
                

            }
            else
            {
                  imgId = '#aud1_preview-'+$(input).attr('id');
                  $(imgId).attr('src', '../../image/no-prev.jpg');
                  //$(imgId).find(".msg").html("This is not Image");
                 //$('.imagepreview').attr('src', '/assets/no_preview.png');
            }
}  



 function aud2_readURL(input,id) {
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

                   imgId = '#aud2_preview-'+$(input).attr('id');
                  $(imgId).attr('src', '../../image/no-prev.jpg');

                }
                else
                {
                    reader.onload = function (e) {
                        imgId = '#aud2_preview-'+$(input).attr('id');
                        $(imgId).attr('src', e.target.result);
                    }

                     reader.readAsDataURL(input.files[0]);
                }
                

            }
            else
            {
                  imgId = '#aud2_preview-'+$(input).attr('id');
                  $(imgId).attr('src', '../../image/no-prev.jpg');
                  //$(imgId).find(".msg").html("This is not Image");
                 //$('.imagepreview').attr('src', '/assets/no_preview.png');
            }
}  





var selectedStartYear ="<?php echo $row['start_yr'] ?>";   
var selectedEndYear ="<?php echo $row['end_yr'] ?>";


var min = 1950,
    max = min + 100,
    select = document.getElementById('selectElementId');

for (var i = min; i<=max; i++){

    if(i==selectedStartYear)
    {
        var opt = document.createElement('option');
        opt.value = i;
        opt.selected=true;
        opt.innerHTML = i;
        select.appendChild(opt);
    }
    else
    {
          var opt = document.createElement('option');
          opt.value = i;
          opt.innerHTML = i;
          select.appendChild(opt);
    }
   
}

var min = 1950,
    max = min + 100,
    select = document.getElementById('endYear');

for (var i = min; i<=max; i++){
    if(i==selectedEndYear)
    {
        var opt = document.createElement('option');
        opt.value = i;
        opt.selected=true;
        opt.innerHTML = i;
        select.appendChild(opt);
    }
    else
    {
          var opt = document.createElement('option');
          opt.value = i;
          opt.innerHTML = i;
          select.appendChild(opt);
    }
}



    
        </script>
  </body>
</html>
