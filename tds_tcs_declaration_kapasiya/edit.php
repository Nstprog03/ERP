<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['kap_firm_id']) && !isset($_SESSION['kap_seasonal_year_id']))
{
  header('Location: /kapasiya-sales-rg/');
}

  $dir = "/static_file_storage/"; 
  $unlink_path=$_SERVER['DOCUMENT_ROOT'].$dir;


  $firm_id=$_SESSION['kap_firm_id'];
  $firm_name=$_SESSION['kap_firm'];
  $financiyal_year_id=$_SESSION['kap_seasonal_year_id'];
  $seasonal_year=$_SESSION['kap_seasonal_year'];
   





  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from tds_tcs_declaration_kapasiya where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

    if(isset($_POST['Submit']))
    {

    $ext_party_id = $_POST['ext_party_id'];
    

    if($_POST['date']!='')
    {
      $date = str_replace('/', '-', $_POST['date']);
      $date = date('Y-m-d', strtotime($date));
    }

    $good_exceeding = $_POST['good_exceeding'];
    $audit_report_id = implode(',', $_POST['audit_report_id']);
    $status = $_POST['status'];


    $imgArr=array();
    $filecount = count($_FILES['doc_file']['tmp_name']);  
    foreach ($_FILES['doc_file']['tmp_name'] as $key =>  $imges) {

      $img = $_FILES['doc_file']['name'][$key];

      $imgTmp = $_FILES['doc_file']['tmp_name'][$key];
      $imgSize = $_FILES['doc_file']['size'][$key];


      include_once('../global_function.php'); 
      $data=getStaticFileStoragePath("tds_tcs_declaration_kapasiya");
      $root_path=$data[0]; // file move path
      $store_path=$data[1]; // db store path

  
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


    $sql="select * from tds_tcs_declaration_kapasiya where id='".$id."'";
    $result = mysqli_query($conn, $sql);
    $row=mysqli_fetch_assoc($result);
    
    $OldDBImg = explode(',', $row['doc_file']); 
    $result1=array_diff($OldDBImg,$finalimg);
    foreach ($result1 as  $item) {
      if($item!='')
      {
        $item=trim($item);             
        unlink($unlink_path.$item); 
      }
    }




    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");
  
   
  
  
    if(!isset($errorMsg))
    {
      $sql = "UPDATE `tds_tcs_declaration_kapasiya` SET 
      `ext_party_id`='".$ext_party_id."',
      `date`='".$date."',
      `good_exceeding`='".$good_exceeding."',
      `audit_report_id`='".$audit_report_id."',
      `status`='".$status."',
      `doc_file`='".$imgStore."',
      `img_title`='".$imgTitle."',
      `username`='".$username."',
      `updated_at`='".$timestamp."'

      where id=".$_GET['id'];
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
    <title>Edit TDS/TCS Declaration</title>
   
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


      <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

       <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">




     <script> 
    $(function(){
      $("#sidebarnav").load("../../nav.html"); 
      $("#topnav").load("../nav2.html"); 

      $(".datepicker").datepicker({

        dateFormat:'dd/mm/yy',
        changeMonth: true,
        changeYear: true,  
    });

      $(".datepicker").keydown(false);

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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit TDS/TCS Declaration</span></a>
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


    <!-- last change on table START-->
       <div class="last-updates">
                  <div class="firm-selectio">
             <div class="firm-selection-pre">
                <span class="pre-firm">Firm : </span><span class="pre-firm-name"><?php echo $firm_name; ?></span>
            </div>
            <div class="year-selection-pre">
            <span class="pre-year-text">Seasonal Year :</span> 
            <span class="pre-year">
              <?php 

              $finYearArr=explode('/',$seasonal_year);

              $start_date=date('Y', strtotime($finYearArr[0]));
               $end_date=date('Y', strtotime($finYearArr[1]));

              echo $start_date.' - '.$end_date; 

              ?>
            </span>
            </div>
          </div>
          <div class="last-edits-fl">
        <?php
         $sqlLastChange="select username,updated_at from tds_tcs_declaration_kapasiya where id='".$row['id']."'";

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
                
                      <span class='fullch'><span class='chtext'><span class='icon-edit'></span>Last Updated By :</span> <span class='userch'>".$user_name."</span> - <span class='datech'>".date('d/m/Y h:i:s A', strtotime($lastChangeRow['updated_at']))."</span> </span>
                 
              ";
           }
          ?>

        </div>
          </div>

          <!-- last change on table END-->     

      <div class="container-fluid">
        <div class="row justify-content-center">
          
            <div class="card">
              <div class="card-header">Edit TDS/TCS Declaration</div>
              <div class="card-body">
                 


                <form class=""  method="post" enctype="multipart/form-data">

                  <div class="row">

                    <div class="form-group col-md-4">
                    <label for="ext_party_id">Select External Party</label>

                    <a class="btn btn-primary btn-sm" target="_blank" href="/external-party/create.php"><i class="fa fa-user-plus"></i></a>

                    <?php
                      $sql = "select * from external_party";
                      $result = mysqli_query($conn, $sql);
                    ?>                       
                    <select data-live-search="true" class="form-control searchDropdown" name="ext_party_id">
                      <?php                   
                        foreach ($conn->query($sql) as $result) 
                        {
                          if($row['ext_party_id']==$result['id'])
                          {
                            echo "<option  value='".$result['id']."' selected>" .$result['partyname']. "</option>";
                          }
                          else
                          {
                             echo "<option  value='".$result['id']."'>" .$result['partyname']. "</option>";
                          }
                          
                        }
                      ?>    

                    </select>
                  </div>

                    <?php
                        $date='';
                        if($row['date']!='' && $row['date']!='0000-00-00')
                        {
                         $date = date("d/m/Y", strtotime($row['date']));
                        }
                      ?>

                    <div class="form-group col-md-4">
                      <label for="date">Date</label>
                      <input type="text" name="date" placeholder="Select Date" class="form-control datepicker" autocomplete="off" value="<?php echo $date ?>">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="good_exceeding">Goods Exceeding</label>
                      <input type="text" name="good_exceeding" placeholder="Enter Goods Exceeding" class="form-control" value="<?php echo $row['good_exceeding'] ?>">
                    </div>

                    <div class="form-group col-md-4">
                    <label for="audit_report_id">Select Audit & ITR</label>

              

                    <?php
                     $sql = "select * from party_audit_report where party_name='".$firm_id."' AND ad_report_type='IT Return Reoport'";
                      $result = mysqli_query($conn, $sql);

                      $selectedArr=explode(',',$row['audit_report_id'])

                    ?>                       
                    <select class="form-control" name="audit_report_id[]" multiple="" required="">
                      <?php                   
                        foreach ($conn->query($sql) as $result) 
                        {

                           $financial_yearSQL = "select * from financial_year where id='".$result['financial_year_id']."'";
                                $financial_year_result = mysqli_query($conn, $financial_yearSQL);

                                $financial_year_row = mysqli_fetch_assoc($financial_year_result);

                                $start_yr='';
                                $end_yr='';
                                if(isset($financial_year_row))
                                {
                                  $start_yr =  date("Y", strtotime($financial_year_row['startdate']));
                                  $end_yr =  date("Y", strtotime($financial_year_row['enddate']));
                                }
                                $FinalYears= $start_yr.'-'.$end_yr;


                                if(in_array($result['id'], $selectedArr))
                                {
                                   echo "<option  value='".$result['id']."' selected>" .$FinalYears. "</option>";
                                }
                                else
                                {
                                   echo "<option  value='".$result['id']."'>" .$FinalYears. "</option>";
                                }


                                 
                        }
                      ?>    

                    </select>
                  </div>

                   <div class="form-group col-md-4">
                    <label for="status">Select Status</label>                  
                    <select class="form-control" name="status" required="">
                      <option value="0" <?php if($row['status']=='0'){echo "selected";} ?>>Pending</option>
                      <option value="1" <?php if($row['status']=='1'){echo "selected";} ?>>Complete</option>
                    </select>
                  </div>
                    




                  </div>


                                    <div class="row dynamicWrapper">

<?php

if ($row['doc_file'] != '') {


$prev = explode(',',$row['doc_file']);
$prev_img_title = explode(',',$row['img_title']);
foreach ($prev as $key => $imging){
if($imging)
$attend =  $dir.$imging;
{
$attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));
$attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

if(in_array($attendExt, $attend_allowExt)) 
{ ?>

    
      <div class=" form-group  col-sm-4 imgcount dynamic_field_<?= $key+1 ?>">
        <label class="image-label" for="doc_file">Document File <?= $key+1 ?></label>
          <div class="image-upload dynamic_field">
            <?php if( $key != 0) {?>
              <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this);">X</button>
            <?php } ?>
            <img id="preview-img<?= $key+1 ?>" src="<?php echo $dir.$prev[$key] ?>" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />

            <input type="hidden" name="oldfile[<?= $key?>]" value="<?php echo $prev[$key]; ?>">

            <input type="file" class="form-control" id="img<?= $key+1 ?>" onchange="readURL(this);" name="doc_file[<?= $key?>]" value="">
            
            <br>

            <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>">
          </div>



      </div>
      <?php
   

}else{
  ?>

  <div class=" form-group  col-sm-4 imgcount dynamic_field_<?= $key+1 ?>">
        <label class="image-label" for="doc_file">Document File <?= $key+1 ?></label>
          <div class="image-upload dynamic_field">
            <?php if( $key != 0) {?>
              <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this);">X</button>
            <?php } ?>
            <img id="preview-img<?= $key+1 ?>" src="<?php echo $dir.$imging ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" class="img-fluid" height="250" width="300">
            <input type="hidden" name="oldfile[<?= $key?>]" value="<?php echo $imging; ?>">
            <div class="filed-form-control">  
                                 
              <a href="<?php echo $dir.$imging ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>

                               
            </div>

            <input type="file" class="form-control" id="img<?= $key+1?>" onchange="readURL(this);" name="doc_file[<?= $key?>]" value="">
            <br>
            <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>">
          </div>



      </div>

  <?php
}
}
}
}else{?>

<div class=" form-group  col-sm-4 imgcount dynamic_field_1">
    <label class="image-label" for="doc_file">Document File 1</label>
      <div class="image-upload dynamic_field">
      
        <img id="preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
        <input type="file" class="form-control" id="img1" onchange="readURL(this);" name="doc_file[1]" value="">
        <br>
        <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]">
      </div>



  </div>


<?php }?>

  <div class="form-group form-group col-sm-4">
    <label class="image-label" for="doc_file">Add more</label>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <script type="text/javascript">

      var delCount=0;

        $(document).ready(function () {
           

            var i = 0;
            $("#add").click(function(){
              var classcount = $('.imgcount').length
              i=parseInt(classcount)+parseInt(delCount)+1;

              var varietyfieldHTML= `<div class=" img_section form-group  col-sm-4 imgcount dynamic_field_`+i+`"><label class="image-label" for="doc_file">Document File `+i+`</label><div class="image-upload dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this,`+i+`);">X</button><img id="preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="readURL(this,`+i+`);" name="doc_file[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]"></div></div>`;

            

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
          if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
              var reader = new FileReader();

               reader.onload = function (e) {
                      imgId = '#preview-'+$(input).attr('id');
                      $(imgId).attr('src', e.target.result);
                  }

                  reader.readAsDataURL(input.files[0]);
          }else{
                imgId = '#preview-'+$(input).attr('id');
                $(imgId).attr('src', '../../image/no-prev.jpg');
                //$(imgId).find(".msg").html("This is not Image");
               //$('.imagepreview').attr('src', '/assets/no_preview.png');
          }
      } 

  </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });

function readURL(input) {
    var url = input.value;
    var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
    if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
        var reader = new FileReader();

         reader.onload = function (e) {
                imgId = '#preview-'+$(input).attr('id');
                $(imgId).attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
    }else{
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
