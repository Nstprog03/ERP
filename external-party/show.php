<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
$dir = "/static_file_storage/"; 

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

 

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Show External Party</title>

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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Show External Party</span></a>
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
               Show External Party
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">

                  <div class="row">
                    <div class="form-group col-md-4">
                      <label for="name">Party Name</label>
                      <input type="text" class="form-control" name="partyname"  placeholder="Enter Party Name" value="<?php echo $row['partyname']; ?>">
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
                      <input type="text" class="form-control" name="pincode" placeholder="Enter Pincode" value="<?php echo $row['pincode']; ?>">
                    </div>
                   


                    <div class="form-group col-md-4">
                      <label for="pan_no">PAN No.</label>
                      <input type="text" class="form-control" name="pan_no" placeholder="Enter PAN No." value="<?php echo $row['pan_no']; ?>" onchange="validatePanNumber(this)" style="text-transform:uppercase" maxlength="10" minlength="10" id="pan_no">
                    </div>

                     <div class="form-group col-md-4">
                      <label for="gstin">GST IN</label>
                      <input type="text" class="form-control" name="gstin" placeholder="Enter GST IN" value="<?php echo $row['gstin']; ?>">
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
                    
                    </a>

                    </div>
                      <div id="bank_field_<?php echo $key ?>" class="collapse-active">
                        <div class="card-body">
                          <div class="row">
                          
                    
                          <div class="form-group col-md-6">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name[]"  value="<?php echo $item['bank_name'] ?>" readonly>
                          </div>    

                          <div class="form-group col-md-6">
                            <label for="bank_ac_number">Bank Account Number</label>
                            <input type="text" class="form-control" name="bank_ac_number[]"  value="<?php echo $item['bank_ac_number'] ?>" readonly>
                          </div>    


                          <div class="form-group col-md-6">
                            <label for="bank_branch">Bank Branch Name</label>
                            <input type="text" class="form-control" name="bank_branch[]"  value="<?php echo $item['bank_branch'] ?>" readonly>
                          </div>   
                          <div class="form-group col-sm-6">
                            <label for="bank_ifsc">IFSC Code</label>
                            <input type="text" class="form-control" name="ifsc[]"  value="<?php echo $item['ifsc'] ?>"  id="ifsc" maxlength="11" readonly>
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



                                  <div class="row" style="margin: 0;">
                    
                                        <?php
                                        if($row['doc_file'] != ''){
                                         $prev = explode(',',$row['doc_file']);
                                        $prev_img_title = explode(',',$row['img_title']);
                                        foreach ($prev as $key => $imging) {
                                          $attend =  $dir.$imging;

                                          if($attend)
                                              {
                                              $attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));

                                              $attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');


                                              if(in_array($attendExt, $attend_allowExt)) 
                                              {



                                                ?>
                                                          <div class="form-group col-md-4 field-show-image pl-0">
                                                            <div class="image-upload">  
                                                              <div class="label">
                                                              <h6 class="title">Document File <?= $key+1 ?></h6>
                                                            
                                                            </div>
                                                            <div class="filed-form-control">  
                                                            <img src="<?php echo $dir.$imging ?>"  data-toggle="modal" data-target="#myModal" id="1" onerror="this.onerror=null; this.src='../../image/no-image.jpg'" height="300" width="300">
                                                              </div>
                                                            <div class="text-center mt-3"> (Click Image to Open)</div>

                                                 
                                                            </div>
                                                            <br>
                              <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>" readonly>
                                                          </div>

                                                <?php
                                               
                                              }
                                              else
                                              {
                                                ?>
                                                 <div class="form-group col-md-4 field-show-image pl-0">
                                                            <div class="image-upload">  
                                                              <div class="label">
                                                              <h6 class="title"> Document File <?= $key+1 ?></h6>
                                                            
                                                            </div>
                                                            <div class="filed-form-control">  
                                                   <img src="<?php echo $dir.$imging ?>"   id="1" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" height="300" width="300">
                                                <a href="<?php echo $dir.$imging ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>

                                                 
                                                            </div>
                                                            <br>
                                                            <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>" readonly>
                                                          </div>
                                                        </div>
                                                <?php
                                              }

                                            }
                                          }
                                          }

                                 ?>                        
                      </div>

                      <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <img class="img-responsive" src="" width="600" height="600" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                
            </div>
        </div>
    </div>
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

<!-- <script type="text/javascript">
      var delCount=0;
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });

            var i = 0;
            $("#add").click(function(){
              var classcount = $('.imgcount').length
              i=parseInt(classcount)+parseInt(delCount)+1;
              // alert(i);
              var varietyfieldHTML= `<div class=" img_section form-group  col-sm-4 pl-0 imgcount dynamic_field_`+i+`"><label class="image-label" for="doc_file">Document File `+i+`</label><div class="image-upload dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 212px;" onclick="removeImg(this,`+i+`);">X</button><img id="preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="readURL(this,`+i+`);" name="doc_file[`+(i-1)+`]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]"></div></div>`;

           
            $('.imgcount').last().after(varietyfieldHTML);


            });
            $('#pan_no').keyup('click', function () {
                $('span.error-keyup-2').hide();
                $('#submit').attr('disabled',false);
            });
        });

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

</script> -->

<script type="text/javascript">
  $(document).ready(function () {
    $('input[type="text"').attr('readonly', 'true');
        $('#myModal').on('show.bs.modal', function (e) {
            var image = $(e.relatedTarget).attr('src');
            $(".img-responsive").attr("src", image);
        });
});
</script>