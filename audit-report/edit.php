<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}

$dir = "/static_file_storage/"; 
$unlink_path=$_SERVER['DOCUMENT_ROOT'].$dir;

if (isset($_GET['id'])) {
$id = $_GET['id'];
$sql = "select * from party_audit_report where id=".$id;
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
$row = mysqli_fetch_assoc($result);
}else {
$errorMsg = 'Could not Find Any Record';
}
}

  if(isset($_POST['Submit']))
  {
		$party_name = $_POST['party_name'];
    $ad_report_type = $_POST['ad_report_type'];
		$financial_year = $_POST['financial_year'];
    $acknow_no = $_POST['acknow_no'];


    include_once('../global_function.php'); 
    $data=getStaticFileStoragePath("audit-report");  //from global_function.php
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path

    
    
    $date_of_filling ='';
    if($_POST['date_of_filling']!='')
    {
      $date_of_filling = str_replace('/', '-', $_POST['date_of_filling']);
      $date_of_filling = date('Y-m-d', strtotime($date_of_filling));
    }

    $due_date ='';
    if($_POST['due_date']!='')
    {
      $due_date = str_replace('/', '-', $_POST['due_date']);
      $due_date = date('Y-m-d', strtotime($due_date));
    }
    
    $assessment_year_id = $_POST['assessment_year_id'];
    
    

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

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");
  
   
  
  
		if(!isset($errorMsg)){
			$sql = "update party_audit_report
									set party_name = '".$party_name."',
										ad_report_type = '".$ad_report_type."',
                    financial_year_id = '".$financial_year."',
                    assessment_year_id = '".$assessment_year_id."',
                    docimg = '".$imgStore."',
                    img_title = '".$imgTitle."',
                    acknow_no = '".$acknow_no."',
                    date_of_filling = '".$date_of_filling."',
                    due_date = '".$due_date."',
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
    <title>Edit Audit Report</title>
 
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit Audit Report</span></a>
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
          $sqlLastChange="select username,updated_at from party_audit_report where id='".$row['id']."'";

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
                Edit Profile
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">
                  <div class="row">
                    <div class="form-group col-sm-4">
                      <label for="party_name">Select Firm</label>
                      <?php
                            $sql = "select * from party";
                            $result = mysqli_query($conn, $sql);
                          ?>
                      
                           <select name="party_name" class="form-control" >
                            <?php                   
                                foreach ($conn->query($sql) as $result) 
                                {
                                    if($result['id']==$row['party_name'])
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
                      <label for="ad_report_type">Report Type</label>
                    <select select="selected" name="ad_report_type" class="form-control">
                      <option value="Tax/Vat Audit Report" 
                      <?php if ($row['ad_report_type'] == 'Tax/Vat Audit Report') {
                          echo "selected";
                      } ?>
                      >Tax/Vat Audit Report</option>
                      <option value="Gst Audit Report"

                      <?php if ($row['ad_report_type'] == 'Gst Audit Report') {
                          echo "selected";
                      } ?>

                      >Gst Audit Report</option>
                       <option value="IT Return Reoport"
                       <?php if ($row['ad_report_type'] == 'IT Return Reoport') {
                          echo "selected";
                      } ?>
                       >IT Return Reoport</option>
                    </select>
                    </div>


                     <div class="col-sm-4">
                       <div class="form-group">
                <label for="financial_year">Select Financial Year</label>

                     <?php
                        $financialYear = getFinancialYear($conn); 
                    ?>                    

                 <select name="financial_year" class="form-control">

                <?php                   

                    foreach ($financialYear as $result2) 
                    {
                        //get Start Year And End Year
                        $syear = date("Y", strtotime($result2['startdate']));

                        $eyear = date("Y", strtotime($result2['enddate']));

                        if($row['financial_year_id']==$result2['id'])
                         {
                            echo "<option  value='".$result2['id']."' selected=''>" .$syear."-".$eyear."</option>";
                         }
                         else
                         {
                            echo "<option  value='".$result2['id']."' >" .$syear."-".$eyear."</option>";
                         }

                     }

                ?>                              

                </select>

            </div>
                </div>


                  <div class="form-group col-md-4">
                      <label for="acknow_no">Acknowledgement No.</label>
                      <input type="text" name="acknow_no" placeholder="Enter Acknowledgement No." class="form-control" value="<?php echo $row['acknow_no'] ?>">
                    </div>

                    <?php
                        $date_of_filling='';
                        if($row['date_of_filling']!='' && $row['date_of_filling']!='0000-00-00')
                        {
                         $date_of_filling = date("d/m/Y", strtotime($row['date_of_filling']));
                        }
                      ?>


                    <div class="form-group col-md-4">
                      <label for="date_of_filling">Date Of Filling</label>
                      <input type="text" name="date_of_filling" placeholder="Select Date" class="form-control datepicker" value="<?php echo $date_of_filling ?>"  autocomplete="off">
                    </div>

                

                     <div class="col-sm-4">
                       <div class="form-group">
                <label for="assessment_year_id">Select Assessment Year</label>

                    <?php
                        $financialYear = getFinancialYear($conn); 
                    ?>                    

                 <select name="assessment_year_id" id="assessment_year" class="form-control">

                <?php                   

                    foreach ($financialYear as $result2) 
                    {
                        //get Start Year And End Year
                        $syear = date("Y", strtotime($result2['startdate']));
                        $eyear = date("Y", strtotime($result2['enddate']));

                         $startdate=date('Y-m-d', strtotime($result2['startdate']));
                        $enddate=date('Y-m-d', strtotime($result2['enddate']));

                        if($row['assessment_year_id']==$result2['id'])
                         {
                            echo "<option data-startdate='".$startdate."' data-enddate='".$enddate."'  value='".$result2['id']."' selected=''>" .$syear."-".$eyear."</option>";
                         }
                         else
                         {
                            echo "<option data-startdate='".$startdate."' data-enddate='".$enddate."'  value='".$result2['id']."' >" .$syear."-".$eyear."</option>";
                         }

                     }

                ?>                              

                </select>

            </div>
                </div>

                    <?php
                        $due_date='';
                        if($row['due_date']!='' && $row['due_date']!='0000-00-00')
                        {
                         $due_date = date("d/m/Y", strtotime($row['due_date']));
                        }
                      ?>

                     <div class="form-group col-md-4">
                      <label for="due_date">Due Date Of Filling</label>
                      <input type="text" name="due_date" id="due_date" placeholder="Select Date" class="form-control datepicker" value="<?php echo $due_date ?>"  autocomplete="off">
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

              var varietyfieldHTML= `<div class=" img_section form-group  col-sm-4 pl-0 imgcount dynamic_field_`+i+`"><label class="image-label" for="docimg">Document File `+i+`</label><div class="image-upload dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this,`+i+`);">X</button><img id="preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="readURL(this,`+i+`);" name="docimg[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]"></div></div>`;

            

            $('.imgcount').last().after(varietyfieldHTML);

            });

            $('#assessment_year').on('change',function(){
               
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

    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
      

</script>
  </body>
</html>