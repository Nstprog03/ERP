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
    $sql = "select * from external_party where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  if(isset($_POST['Submit'])){
		$partyname = $_POST['partyname'];
    $contact_per = $_POST['contact_per'];
		$contact_no = $_POST['contact_no'];
   
    $address = $_POST['address'];
    $gstin = $_POST['gstin'];


    $city=$_POST['city'];
    $district=$_POST['district'];
    $state=$_POST['state'];
    $pincode=$_POST['pincode'];
    $pan_no=strtoupper($_POST['pan_no']);
    $fact_lic_no=$_POST['fact_lic_no'];
    $iec_code = $_POST['iec_code'];
    $ud_aadhar = $_POST['ud_aadhar'];
    $party_email = $_POST['party_email'];

    $bankArr=array();
    foreach ($_POST['bank_name'] as $key => $bank_name) 
    {
      $bankArr[$key]['bank_name']=$bank_name;
      $bankArr[$key]['bank_ac_number']=$_POST['bank_ac_number'][$key];
      $bankArr[$key]['bank_branch']=$_POST['bank_branch'][$key];
      $bankArr[$key]['ifsc']=$_POST['ifsc'][$key];
    }

    $bankDetails=json_encode($bankArr);


    include_once('../global_function.php'); 
    $data=getStaticFileStoragePath("external-party");  //from global_function.php
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path
    

   $imgArr=array();
    $filecount = count($_FILES['doc_file']['tmp_name']);  
    foreach ($_FILES['doc_file']['tmp_name'] as $key =>  $imges) {

      $img = $_FILES['doc_file']['name'][$key];

      $imgTmp = $_FILES['doc_file']['tmp_name'][$key];
      $imgSize = $_FILES['doc_file']['size'][$key];

  
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

		if(!isset($errorMsg)){
			$sql = "update external_party
									set partyname = '".$partyname."',
										contact_per = '".$contact_per."',
                    contact_no = '".$contact_no."',
                 
                    address = '".$address."',
                    gstin = '".$gstin."',
                    city='".$city."',
                    district='".$district."',
                    state='".$state."',
                    pincode='".$pincode."',
                    pan_no='".$pan_no."',
                    fact_lic_no='".$fact_lic_no."',
                    iec_code='".$iec_code."',
                    ud_aadhar='".$ud_aadhar."',
                    party_email='".$party_email."',
                    doc_file='".$imgStore."',
                    img_title='".$imgTitle."',
                    bankDetails='".$bankDetails."',
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
    <title>External Party Edit</title>

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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit  External Party</span></a>
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
          $sqlLastChange="select username,updated_at from external_party where id='".$row['id']."'";

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
                Edit External Party
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">

                  <div class="row">
                    <div class="form-group col-md-4">
                      <label for="name">Party Name</label>
                      <input type="text" class="form-control" name="partyname"  placeholder="Enter Party Name" id="name" value="<?php echo $row['partyname']; ?>">
                    </div>

                     <div class="form-group col-md-4">
                      <label for="address">Party Address</label>
                      <input type="text" class="form-control" name="address" placeholder="Enter Address" value="<?php echo $row['address']; ?>">
                    </div>

                      <div class="form-group col-md-4">
                      <label for="city">City</label>
                      <input type="text" class="form-control" name="city" placeholder="Enter City" value="<?php echo $row['city']; ?>">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="district">District</label>
                      <input type="text" class="form-control" name="district" placeholder="Enter District" value="<?php echo $row['district']; ?>">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="state">State</label>
                      <input type="text" class="form-control" name="state" placeholder="Enter State" value="<?php echo $row['state']; ?>">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="pincode">Pincode</label>
                      <input type="text" class="form-control" name="pincode" placeholder="Enter Pincode" value="<?php echo $row['pincode']; ?>" onkeypress=" return OnlyNumberValidation(event);" maxlength="06" id="pincode">
                    </div>
                   


                    <div class="form-group col-md-4">
                      <label for="pan_no">PAN No.</label>
                      <input type="text" class="form-control" name="pan_no" placeholder="Enter PAN No." value="<?php echo $row['pan_no']; ?>" onchange="validatePanNumber(this)" onkeypress="return panValidationKeyup(event)" style="text-transform:uppercase" maxlength="10" minlength="10" id="pan_no">
                    </div>

                     <div class="form-group col-md-4">
                      <label for="gstin">GST IN</label>
                      <input id="gst_in" type="text" class="form-control" name="gstin" placeholder="Enter GST IN" value="<?php echo $row['gstin']; ?>" onkeypress="return GSTValidationKeyup(event)" maxlength="15">
                    </div>

                     <div class="form-group col-md-4">
                      <label for="ud_aadhar">Udhyog Aadhaar</label>
                      <input type="text" class="form-control" name="ud_aadhar" placeholder="Enter Udhyog Aadhaar" value="<?php echo $row['ud_aadhar']; ?>">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="fact_lic_no">Factory Licence No. </label>
                      <input type="text" class="form-control" name="fact_lic_no" placeholder="Factory Licence No."value="<?php echo $row['fact_lic_no']; ?>">
                    </div>

                     <div class="form-group col-md-4">
                      <label for="party_email">Email</label>
                      <input type="text" class="form-control" name="party_email" placeholder="Enter Email" value="<?php echo $row['party_email']; ?>">
                    </div>

                     <div class="form-group col-md-4">
                      <label for="iec_code">IEC Code</label>
                      <input type="text" class="form-control" name="iec_code" placeholder="Enter IEC Code" value="<?php echo $row['iec_code']; ?>">
                    </div>



                     <div class="form-group col-md-4">
                      <label for="contact_per">Contact Person</label>
                      <input type="text" class="form-control" name="contact_per" placeholder="Enter Contact Person" value="<?php echo $row['contact_per']; ?>">
                    </div>
                    <div class="form-group col-md-4">
                      <label for="contact_no">Contact Number</label>
                      <input type="text" class="form-control" name="contact_no" placeholder="Enter Email" value="<?php echo $row['contact_no']; ?>">
                    </div>


                  

                  </div>




                        <br>
                  <br>

                <div class="row">

                 
                   <div class="col-md-12">
                      <button type="button" id="btn_add_bank" class="btn btn-primary btn_add_bank">Add New Bank</button>
                   </div>
                  <br>

                </div>


          <div class="dynamicBankSection">

            <?php
              $bankArr=json_decode($row['bankDetails'],true);
              $bankCount=count($bankArr);
              foreach ($bankArr as $key => $item) 
              {

                
            
            ?>


            <div class="row mx-0">
              <div class="border-row  flex-wrap w-100"> 
                  <div class="card">
                    <div class="card-header p-0">
                    <a class="card-header card-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#bank_field_<?php echo $key ?>">
                    Bank Details
                     <?php
                      if($key!=0)
                      {
                        echo '<button type="button" class="btn btn-sm btn-danger btnRemoveBank" onclick="removeBankSection(this)">-</button>';
                      }
                     
                    ?>
                    </a>



                
                    </div>
                      <div id="bank_field_<?php echo $key ?>" class="collapse-active">
                        <div class="card-body">
                          <div class="row">
                          
                    
                          <div class="form-group col-md-6">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name[]" placeholder="Enter Bank Name" value="<?php echo $item['bank_name'] ?>">
                          </div>    

                          <div class="form-group col-md-6">
                            <label for="bank_ac_number">Bank Account Number</label>
                            <input type="text" class="form-control" name="bank_ac_number[]" placeholder="Enter Bank Account Number" value="<?php echo $item['bank_ac_number'] ?>">
                          </div>    


                          <div class="form-group col-md-6">
                            <label for="bank_branch">Bank Branch Name</label>
                            <input type="text" class="form-control" name="bank_branch[]" placeholder="Bank Branch Name" value="<?php echo $item['bank_branch'] ?>">
                          </div>   
                          <div class="form-group col-sm-6">
                            <label for="bank_ifsc">IFSC Code</label>
                            <input type="text" class="form-control" name="ifsc[]"  placeholder="Enter IFSC Code" onkeypress="return IFSCValidation(event)" value="<?php echo $item['ifsc'] ?>"  id="ifsc" maxlength="11">
                          </div>
    
                          </div>
                        </div>
                      </div>
                
                  </div>             
              </div>        
            </div>

            <?php
                              
              }
            ?>



          </div>

          <br>
          <br>









              <div class="row dynamicWrapper" style="margin-left:0px;">

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

                      
                        <div class=" form-group  col-sm-4 pl-0 imgcount dynamic_field_<?= $key+1 ?>">
                          <label class="image-label" for="doc_file">Document File <?= $key+1 ?></label>
                            <div class="image-upload dynamic_field">
                              <?php if( $key != 0) {?>
                                <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 212px;" onclick="removeImg(this);">X</button>
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

                    <div class=" form-group  col-sm-4 pl-0 imgcount dynamic_field_<?= $key+1 ?>">
                          <label class="image-label" for="doc_file">Document File <?= $key+1 ?></label>
                            <div class="image-upload dynamic_field">
                              <?php if( $key != 0) {?>
                                <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 212px;" onclick="removeImg(this);">X</button>
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

                <div class=" form-group  col-sm-4 pl-0 imgcount dynamic_field_1">
                      <label class="image-label" for="doc_file">Document File 1</label>
                        <div class="image-upload dynamic_field">
                        
                          <img id="preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
                          <input type="file" class="form-control" id="img1" onchange="readURL(this);" name="doc_file[1]" value="">
                          <br>
                          <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]">
                        </div>



                    </div>


            <?php }?>

                    <div class="form-group form-group col-sm-4 pl-0">
                      <label class="image-label" for="doc_file">Add more</label>
                       <div class="image-upload">
                        
                      <button type="button" class=" btn btn-defult" id="add" style="height: 340px;width: 263px;"><i class="fa fa-plus" aria-hidden="true" style="width: 35%;height: 117px;"></i>
                      </button>
                      

                    </div>

                    </div>
                    </div>


                   
                   
                 <div class="form-group">
                      <button type="submit" name="Submit" id="submit" class="btn btn-primary waves">Submit</button>
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
  function IFSCValidation(e) {
          var ifsc = $('#ifsc').val();
          var keycode = (e.which) ? e.which : e.keyCode;
          if(ifsc.length==4)
          {
            if (keycode == 48 )  
            {     
               return true;    
            }
            else
            {
              return false;
            }
          }  
          
        }
      var delCount=0;
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
            $('#ifsc').keyup('click',function () {
                $(this).val($(this).val().toUpperCase());
                $('span.error-keyup-1').hide();
                $('#submit').attr('disabled',false);
            });

            $("#ifsc").change(function () {      
              var inputvalues = $(this).val();      
              var reg = /[a-zA-Z0-9]{4}[0][a-zA-Z0-9]{6}$/;    
                if (inputvalues.match(reg)) {    
                    return true;    
                }    
                else {    
                    $('#ifsc').after('<span class="error error-keyup-1 text-danger">Invalid IFSC</span>');
                    $('#submit').attr('disabled',true);  
                    return false;    
                }    
            });  

            var i = 0;
            $("#add").click(function(){
              var classcount = $('.imgcount').length
              i=parseInt(classcount)+parseInt(delCount)+1;
              // alert(i);
              var varietyfieldHTML= `<div class=" img_section form-group  col-sm-4 pl-0 imgcount dynamic_field_`+i+`"><label class="image-label" for="doc_file">Document File `+i+`</label><div class="image-upload dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 212px;" onclick="removeImg(this,`+i+`);">X</button><img id="preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="readURL(this,`+i+`);" name="doc_file[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]"></div></div>`;

           
            $('.imgcount').last().after(varietyfieldHTML);


            });
            $('#pan_no').keyup('click', function () {
                $('span.error-keyup-2').hide();
                $('#submit').attr('disabled',false);
            });


            
            $('#pincode').keyup(function() {
              $('span.error-keyup-1').hide();
              $("#submit").attr("disabled", false);

              if($(this).val().length < 6 ) {

                $("#submit").attr("disabled", true);

                  $(this).after('<span class="error error-keyup-1 text-danger">Invalid PinCode</span>');
              }
            });




              //GST Validation

              //error hide 
            $('#gst_in').keyup('click', function () {
                $(this).val($(this).val().toUpperCase());

                $('span.error-keyup-3').hide();
                $('#submit').attr('disabled',false);
            });

            //check after focus out
            $("#gst_in").change(function () {

                var inputvalues = $(this).val();    
                var gstinformat = new RegExp('^[0-9]{2}[A-Z]{5}[0-9]{4}([a-zA-Z]){1}([a-zA-Z0-9]){1}([a-zA-Z0-9]){1}([a-zA-Z0-9]){1}?$');    
                if (gstinformat.test(inputvalues)) {    
                    return true;    
                } else {    
                    $(this).after('<span class="error error-keyup-3 text-danger">Invalid GST NO.</span>');   
                    $(".gst").val(''); 
                    $('#submit').attr('disabled',true);   
                    $(".gst").focus();    
                }    
            });



               //dynamic bank details
     
               var b=<?php echo $bankCount ?>;
                $('.btn_add_bank').click(function(){

                    b+=1;
                  
                    var code = '<div class="row mx-0"><div class="border-row  flex-wrap w-100"><div class="card"><div class="card-header p-0"> <a class="card-header card-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#bank_field_'+b+'">Bank Details<button type="button" class="btn btn-sm btn-danger btnRemoveBank" onclick="removeBankSection(this)">-</button></a></div><div id="bank_field_'+b+'" class="collapse-active"><div class="card-body"><div class="row"><div class="form-group col-md-6"><label for="bank_name">Bank Name</label><input type="text" class="form-control" name="bank_name[]" placeholder="Enter Bank Name" value=""></div><div class="form-group col-md-6"><label for="bank_ac_number">Bank Account Number</label><input type="text" class="form-control" name="bank_ac_number[]" placeholder="Enter Bank Account Number" value=""></div><div class="form-group col-md-6"><label for="bank_branch">Bank Branch Name</label><input type="text" class="form-control" name="bank_branch[]" placeholder="Bank Branch Name" value=""></div><div class="form-group col-sm-6"><label for="bank_ifsc">IFSC Code</label><input type="text" class="form-control" name="ifsc[]"  placeholder="Enter IFSC Code" onkeypress="return IFSCValidation(event)" value="" id="ifsc" maxlength="11"></div></div></div></div></div></div></div>';
                      $('.dynamicBankSection').append(code);
                });


                $('#name').on('input', function() {
             
                      $('span.error-keyup-1').hide();

                         var name="<?php echo $row['partyname']; ?>";

                          if(name!=this.value)
                          {
                            checkName();
                          }
                     

                });



        });


      function checkName()
        {
            var name=$('#name').val();
                $.ajax({
                type: "POST",
                url: 'check_nameAJAX.php',
                data: {name:name},
                success: function(response)
                {
                    var jsonData = JSON.parse(response);
                    console.log(jsonData.name_exist);

                   if(jsonData.name_exist)
                   {
                     $('#name').after('<span class="error error-keyup-1 text-danger">Already Exist.</span>');
                     $(':input[type="submit"]').prop('disabled', true);
                   }
                   else
                   {
                    $('span.error-keyup-1').hide();
                    $(':input[type="submit"]').prop('disabled', false);
                   }
                    
               }
              });
        }





       function removeBankSection(e)
        {
           $(e).parent().parent().parent().parent().remove();

        }


           //GST Validation on key up
        function GSTValidationKeyup(e){

          var gst = $('#gst_in').val();
          var keycode = (e.which) ? e.which : e.keyCode;

               // 1 and 2 charcter numeric only
               // 8 to 11 charcter numeric only
              if((gst.length>=0 && gst.length<=1) || (gst.length>6 && gst.length<=10))
              {
                if (keycode >= 48 && keycode <= 57)  
                {     
                   return true;    
                }
                else
                {
                  return false;
                }
              }  
              //  3 to 7 character alpha only
              // 12 charcter alpha only
              else if(gst.length>1 && gst.length<=6 || gst.length==11)
              {
                if ((keycode > 64 && keycode < 91) || (keycode > 96 && keycode < 123))  
                    {     
                           return true;    
                    }
                    else
                    {
                        return false;
                    }
              }
         
        }






        //PAN Validation
        //check after focus out
        function validatePanNumber(pan) {
            let pannumber = $(pan).val();
            var regex = /[a-zA-z]{5}\d{4}[a-zA-Z]{1}/;
            if (pannumber.match(regex)) {
                
            }
            else {
                $('#pan_no').after('<span class="error error-keyup-2 text-danger">Invalid PAN number</span>');
                $('#submit').attr('disabled',true);
            }
        }

          //on key up
        function panValidationKeyup(e){

          var pan = $('#pan_no').val();
          var keycode = (e.which) ? e.which : e.keyCode;

          //first  5 character & last letter alpha only
          if(pan.length>=0 && pan.length<=4 || pan.length>8)
          {
            if ((keycode > 64 && keycode < 91) || (keycode > 96 && keycode < 123))  
                {     
                       return true;    
                }
                else
                {
                    return false;
                }
          }
              // 6 to 9 charcter is numeric only
          else if(pan.length>=5 && pan.length<=8)
          {
            if (keycode >= 48 && keycode <= 57)  
            {     
               return true;    
            }
            else
            {
              return false;
            }
          }  
        }







         function OnlyNumberValidation(e) {
          var keycode = (e.which) ? e.which : e.keyCode;
          if (keycode >= 48 && keycode <= 57)  
          {     
             return true;    
          }
          else
          {
            return false;
          }
        }


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