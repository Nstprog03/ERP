<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}
  $dir = "/file_storage/"; 
  $unlink_path=$_SERVER['DOCUMENT_ROOT'].$dir;

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from courier where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }


  if(isset($_POST['Submit'])){


  include('../global_function.php'); 
  $data=getFileStoragePath("courier");  //this function called from global_function.php file
  $root_path=$data[0]; // file move path
  $store_path=$data[1]; // db store path
  

    $id=$_POST['id'];

    $firm = $_POST['firm'];
    $party = $_POST['party'];
    
    $docket_type = $_POST['docket_type'];
    $docket_no = $_POST['docket_no'];
   //$rcvd_date =date("Y-d-m", strtotime($_POST['rcvd_date']));

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");
    
   $send_date='';
   $rcvd_date='';
   if($_POST['send_date']!='')
    {
      $send_date = str_replace('/', '-',$_POST['send_date']);
      $send_date = date('Y-m-d', strtotime($send_date));
    }
    if($_POST['rcvd_date']!='')
    {
      $rcvd_date = str_replace('/', '-',$_POST['rcvd_date']);
      $rcvd_date = date('Y-m-d', strtotime($rcvd_date));
    }





      $imgArr=array();
    $filecount = count($_FILES['docimg']['tmp_name']);  
    foreach ($_FILES['docimg']['tmp_name'] as $key =>  $imges) {

      $img = $_FILES['docimg']['name'][$key];

      $imgTmp = $_FILES['docimg']['tmp_name'][$key];
      $imgSize = $_FILES['docimg']['size'][$key];

  
      if(!empty($img)){
        
        $imgExt = strtolower(pathinfo($img, PATHINFO_EXTENSION));

        $allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'csv', 'pdf', 'xls', 'xlsx', 'txt');

        $img = time().'_'.rand(1000,9999).'.'.$imgExt;
        // array_push($imgArr,$img);
        $imgArr[$key] = $img;
        if(in_array($imgExt, $allowExt)){

          if($imgSize < 5000000){
            move_uploaded_file($imgTmp ,$root_path.$img);
          }else{
            $errorMsg = 'Image too large';
            echo $errorMsg;
          }
        }else{
          $errorMsg = 'Please select a valid image';
          echo $errorMsg;
        }

      }else{
        $imgArr[$key] = '';
      }
    }
    
    $finalimg = array();
    if(count($imgArr) > 0){
      foreach($imgArr as $k => $v){
        if($v == "" && isset($_POST['oldfile'][$k])){
          $finalimg[] = $_POST['oldfile'][$k];
        }else{
         if($v!='' && $v!=null)
          {
            $finalimg[] = $store_path.$v;
          }
        }
      }
    }


    $img_title = $_POST['img_title'];
    $imgTitle = implode(',', $img_title);
    $imgStore = implode(',', $finalimg);
    
    $OldDBImg = explode(',', $row['docimg']); 
    $result1=array_diff($OldDBImg,$finalimg);
        foreach ($result1 as  $item) {
          if($item!='')
          {
            $item=trim($item);             
            unlink($unlink_path.$item); 
          }
    }
   
  
  
    if(!isset($errorMsg)){
      $sql = "update courier
                  set 
                    firm = '".$firm."',
                    party = '".$party."',
                    send_date = '".$send_date."',
                    docket_type = '".$docket_type."',
                    docket_no = '".$docket_no."',
                    rcvd_date = '".$rcvd_date."',
                    docimg = '".$imgStore."',
                    img_title = '".$imgTitle."',
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
                echo $errorMsg;
      }
    }

  }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Edit Courier Report</title>
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

      <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
         rel = "stylesheet">

      <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

       <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">



     <script> 
    $(function(){
     $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html"); 

        $(".datepicker").datepicker({dateFormat:'dd/mm/yy',
          changeYear:true,
          changeMonth:true,
        }); 
        $(".datepicker").keydown(false);
        
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit Courier Report</span></a>
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
          $sqlLastChange="select username,updated_at from courier where id='".$row['id']."'";

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
              <div class="card-header">Courier Report Edit
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">

                  <input type="hidden" name="id" value="<?php echo $row['id'] ?>">



                  <div class="row">
                     <div class="form-group col-sm-4">
                          <label for="firm">Firm</label>
                              <?php
                                  $sql2 = "select * from party";
                                  $result2 = mysqli_query($conn, $sql2);
                              ?>                      
                          <select id="firm" name="firm" class="form-control">
                          <?php                   
                              foreach ($conn->query($sql2) as $result2) 
                              {

                                 $isFirmSelected =""; 
                                 if($row['firm']==$result2['id'])
                                 {
                                   $isFirmSelected = "selected";
                                 }

                                  echo "<option  value='".$result2['id']."'".$isFirmSelected.">".$result2['party_name']. "</option>";
                              }
                          ?>                             
                          </select>
                      </div>


                     <div class="form-group col-sm-4">
                          <label for="firm">External Party</label>
                              <?php
                                  $sql2 = "select * from external_party";
                                  $result2 = mysqli_query($conn, $sql2);
                              ?>                      
                          <select id="party" name="party" class="form-control">
                          <?php                   
                              foreach ($conn->query($sql2) as $result2) 
                              {

                                 $isPartySelected =""; 
                                 if($row['party']==$result2['id'])
                                 {
                                   $isPartySelected = "selected";
                                 }

                                  echo "<option  value='".$result2['id']."'".$isPartySelected.">".$result2['partyname']. "</option>";
                              }
                          ?>                             
                          </select>
                      </div>  

                      <?php 
                    $send_date='';
                    if($row['send_date']!='0000-00-00') 
                    {
                      $send_date=date("d/m/Y", strtotime($row['send_date']));
                    }
                    ?>

                    <div class="form-group col-sm-4">
                      <label for="send_date">Send Date</label>
                      <input type="text"  class="form-control datepicker" autocomplete="off" name="send_date" placeholder="Enter Send Date" value="<?php echo $send_date; ?>">
                    </div>

                    <div class="form-group col-sm-4">
                      <label for="docket_type">Docket Type</label>
                      <input type="text" class="form-control" name="docket_type" placeholder="Enter Docket Type" value="<?php echo $row['docket_type']; ?>">
                    </div>

                    <div class="form-group col-sm-4">
                      <label for="docket_no">Docket No</label>
                      <input type="text" class="form-control" name="docket_no" placeholder="Enter Docket NoN" value="<?php echo $row['docket_no']; ?>">
                    </div>

                    <?php 
                   $rcvdDate='';
                    if($row['rcvd_date']!='0000-00-00') 
                    {
                      $rcvdDate=date("d/m/Y", strtotime($row['rcvd_date']));
                    }
                    ?>

                        <div class="form-group col-sm-4">
                      <label for="rcvd_date">Reveived Date</label>
                      <input type="text"  class="form-control datepicker" name="rcvd_date" placeholder="Enter Recevied Date" autocomplete="off" value="<?php echo $rcvdDate; ?>">


                    </div>
                  </div>

            <div class="row dynamicWrapper" style="margin-left: 0;">

                  <?php

                  if ($row['docimg'] != '') {
               
              
                  $prev = explode(',',$row['docimg']);
                  $prev_img_title = explode(',',$row['img_title']);

                  foreach ($prev as $key => $imging){
                    if($imging)
                      $attend =  $dir.$imging;
                    {
                      $attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));
                      $attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

                      if(in_array($attendExt, $attend_allowExt)) 
                      { ?>

                      
                        <div class=" form-group  col-sm-4 pl-0 imgcount dynamic_field_<?= $key+1 ?>">
                          <label class="image-label" for="docimg">Document File <?= $key+1 ?></label>
                            <div class="image-upload dynamic_field">
                              <?php if( $key != 0) {?>
                                <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this);">X</button>
                              <?php } ?>
                              <img id="preview-img<?= $key+1 ?>" src="<?php echo $dir.$prev[$key] ?>" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />

                              <input type="hidden" name="oldfile[<?= $key?>]" value="<?php echo $prev[$key]; ?>">

                              <input type="file" class="form-control" id="img<?= $key+1 ?>" onchange="readURL(this);" name="docimg[<?= $key?>]" value="">
                              
                              <br>

                              <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>">
                            </div>



                        </div>
                        <?php
                     
                  
                  }else{
                    ?>

                    <div class=" form-group  col-sm-4 pl-0 imgcount dynamic_field_<?= $key+1 ?>">
                          <label class="image-label" for="docimg">Document File <?= $key+1 ?></label>
                            <div class="image-upload dynamic_field">
                              <?php if( $key != 0) {?>
                                <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this);">X</button>
                              <?php } ?>
                              <img id="preview-img<?= $key+1 ?>" src="<?php echo $dir.$imging ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" class="img-fluid" height="250" width="300">
                              <input type="hidden" name="oldfile[<?= $key?>]" value="<?php echo $imging; ?>">
                              <div class="filed-form-control">  
                                                   
                                <a href="<?php echo $dir.$imging ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>

                                                 
                              </div>

                              <input type="file" class="form-control" id="img<?= $key+1?>" onchange="readURL(this);" name="docimg[<?= $key?>]" value="">
                              <br>
                              <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>">
                            </div>



                        </div>

                    <?php
                  }
                }
              }
            }else{?>

                <div class=" form-group  col-sm-4 pl-0 imgcount dynamic_field_1">
                      <label class="image-label" for="docimg">Document File 1</label>
                        <div class="image-upload dynamic_field">
                        
                          <img id="preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
                          <input type="file" class="form-control" id="img1" onchange="readURL(this);" name="docimg[1]" value="">
                          <br>
                          <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]">
                        </div>



                    </div>


            <?php }?>

                    <div class="form-group form-group col-sm-4 pl-0">
                      <label class="image-label" for="docimg">Add more</label>
                       <div class="image-upload">
                        
                      <button type="button" class=" btn btn-defult" id="add" style="height: 340px;width: 263px;"><i class="fa fa-plus" aria-hidden="true" style="width: 35%;height: 117px;"></i>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
        

</script>

<script type="text/javascript">

      var delCount=0;

        $(document).ready(function () {
           

            var i = 0;
            $("#add").click(function(){
              var classcount = $('.imgcount').length
              i=parseInt(classcount)+parseInt(delCount)+1;

              var varietyfieldHTML= `<div class=" img_section form-group  col-sm-4 pl-0 imgcount dynamic_field_`+i+`"><label class="image-label" for="docimg">Document File `+i+`</label><div class="image-upload dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this,`+i+`);">X</button><img id="preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="readURL(this,`+i+`);" name="docimg[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]"></div></div>`;

            

            $('.imgcount').last().after(varietyfieldHTML);

            });

        });
      function removeImg(e,index) {
        $(e).parent('div').parent('div').remove(); 
        delCount=delCount+1;
      }

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
