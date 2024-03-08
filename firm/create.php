<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Firm Database Create</title>
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New Firm </span></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto"></ul>
            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index.php"><i class="fa fa-sign-out-alt"></i>Back</a></li>
            </ul>
        </div>
      </div>
    </nav>

      <div class="container-fluid">
        <div class="row justify-content-center">
         
            <div class="card">
              <div class="card-header">Create</div>
              <div class="card-body">
                <form class="" action="add.php" method="post" enctype="multipart/form-data">

                  <div class="row">
                    
                

                    <div class="form-group col-md-4">
                      <label for="party_name">Firm Name</label>
                      <input type="text" class="form-control" name="party_name"  placeholder="Enter Firm Name" value="">
                    </div>
                   

                    
                    <div class="form-group col-md-4">
                      <label for="party_address">Firm Address</label>
                      <input type="text" class="form-control" name="party_address" placeholder="Enter Firm Address" value="">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="city">City</label>
                      <input type="text" class="form-control" name="city" placeholder="Enter City" value="">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="district">District</label>
                      <input type="text" class="form-control" name="district" placeholder="Enter District" value="">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="state">State</label>
                      <input type="text" class="form-control" name="state" placeholder="Enter State" value="">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="pincode">Pincode</label>
                      <input type="text" class="form-control" name="pincode" placeholder="Enter Pincode" value="" onkeypress=" return OnlyNumberValidation(event);" maxlength="06" id="pincode">
                    </div>

                     <div class="form-group col-md-4">
                      <label for="pan_no">PAN No.</label>
                      <input type="text" class="form-control" name="pan_no" placeholder="Enter PAN No." id="pan_no" onchange="validatePanNumber(this)" onkeypress="return panValidationKeyup(event)" style="text-transform:uppercase" maxlength="10" minlength="10">
                    </div>

                     <div class="form-group col-md-4">
                      <label for="gst_in">GST IN </label>
                      <input type="text" class="form-control" id="gst_in" name="gst_in" placeholder="Enter GST IN No." value="" onkeypress="return GSTValidationKeyup(event)" maxlength="15">
                    </div>
                    
                    <div class="form-group col-md-4">
                      <label for="tan_no">TAN No. </label>
                      <input type="text" class="form-control" id="tan_no" name="tan_no" placeholder="Enter TAN No." value="">
                      <!-- <input type="text" class="form-control" id="tan_no" name="tan_no" placeholder="Enter TAN No." value="" onkeypress="return GSTValidationKeyup(event)" maxlength="15"> -->
                    </div>



                   

                    <div class="form-group col-md-4">
                      <label for="ud_aadhar">Udhyog Aadhaar</label>
                      <input type="text" class="form-control" name="ud_aadhar" placeholder="Enter Udhyog Aadhaar" value="">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="fact_lic_no">Factory Licence No. </label>
                      <input type="text" class="form-control" name="fact_lic_no" placeholder="Factory Licence No." value="">
                    </div>

                    <!--  <div class="form-group col-md-4">
                      <label for="party_email">Firm Email</label>
                      <input type="text" class="form-control" name="party_email" placeholder="Enter Firm Email" value="">
                    </div> -->

                     <div class="form-group col-md-4">
                      <label for="iec_code">IEC Code</label>
                      <input type="text" class="form-control" name="iec_code" placeholder="Enter IEC Code" value="">
                    </div>


                    <div class="form-group col-md-4">
                      <label for="contact_per">Contact Person</label>
                      <input type="text" class="form-control" name="contact_per" placeholder="Enter Contact Person" value="">
                    </div> 

                     <div class="form-group col-md-4">
                          <div class="checkbox">
                          <label><input name="show_in_qis" type="checkbox" value="1" checked=""><b> Include in QIS Report</b> </label>
                        </div>
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
            <div class="row mx-0">
              <div class="border-row  flex-wrap w-100"> 
                  <div class="card">
                    <div class="card-header p-0">


                    <a class="card-header card-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#bank_field_1">
                    Bank Details
                    </a>

                
                    </div>
                      <div id="bank_field_1" class="collapse-active">
                        <div class="card-body">
                          <div class="row">
                          
                    
                          <div class="form-group col-md-6">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name[]" placeholder="Enter Bank Name" value="">
                          </div>    

                          <div class="form-group col-md-6">
                            <label for="bank_ac_number">Bank Account Number</label>
                            <input type="text" class="form-control" name="bank_ac_number[]" placeholder="Enter Bank Account Number" value="">
                          </div>    


                          <div class="form-group col-md-6">
                            <label for="bank_branch">Bank Branch Name</label>
                            <input type="text" class="form-control" name="bank_branch[]" placeholder="Bank Branch Name" value="">
                          </div>   
                          <div class="form-group col-sm-6">
                            <label for="bank_ifsc">IFSC Code</label>
                            <input type="text" class="form-control" name="ifsc[]"  placeholder="Enter IFSC Code" onkeypress="return IFSCValidation(event)" value="" id="ifsc" maxlength="11">
                          </div>
    
                          </div>
                        </div>
                      </div>
                
                  </div>             
              </div>        
            </div>



          </div>

          <br>
          <br>






                      <div class="row">

                      <!-- Dynamic Email Start -->
                    <div class="col-md-5 email_dynamic">

                      <div class="row">

                        <div class="form-group col-md-10">
                          <label for="party_email">Firm Email</label>
                          <input type="text" class="form-control party_email"    id="party_email" name="party_email[]" placeholder="Enter Email">
                        </div>

                        <div class="form-group col-md-2" style="margin-top: 30px;">
                          <button type="button" class=" btn btn-primary email_add_button"> +</button>
                        </div>
                      </div>
                    </div>
                     <!-- Dynamic Email End -->


                      <!-- Dynamic Contact No. Start -->
                    <div class="col-md-5 contact_dynamic">

                      <div class="row">

                        <div class="form-group col-md-10">
                          <label for="contact_number">Contact No.</label>
                          <input type="text" class="form-control contact_number"   id="contact_number" name="contact_number[]" placeholder="Enter Contact No.">
                        </div>

                        <div class="form-group col-md-2" style="margin-top: 30px;">
                          <button type="button" class=" btn btn-primary contact_add_btn"> +</button>
                        </div>

                      </div>
                    </div>
                  <!-- Dynamic Contact No. End -->

                  </div>










                    <div class="row"> 
                      <div class="form-group col-md-4">
                        <label for="logo_img">LOGO Image</label>
                        <input type="file" class="form-control" name="logo_img"  onchange="readURLLOGO(this);">
                      </div>
                   
                      <div class="form-group col-md-4">
                        <label for="stamp_img">Stamp Image</label>
                        <input type="file" class="form-control" name="stamp_img"  onchange="readURLLOGO(this);">
                      </div>
                    </div>

                                        <div class="row dynamicWrapper">
                    <div class=" form-group  col-sm-4 pl-0 imgcount dynamic_field_1">
                      <label class="image-label" for="docImg">Document File 1</label>
                        <div class="image-upload dynamic_field">
                        
                          <img id="preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
                          <input type="file" class="form-control" id="img1" onchange="readURL(this);" name="docImg[]" value="">
                          <br>
                          <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]">
                        </div>



                    </div>
                    <div class="form-group form-group col-sm-4 pl-0">
                      <label class="image-label" for="docImg">Add more</label>
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
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
    <script type="text/javascript">
      var delCount=0;
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });



            //dynamic email add

               var email_dynamic = $('.email_dynamic');

                $('.email_add_button').click(function(){
                  
                    var emailField = '<div class="row"><div class="form-group col-md-10"><input type="text" class="form-control party_email"  id="party_email" name="party_email[]" placeholder="Enter Email"></div><div class="form-group col-md-2" style="margin-top: 0px;"><a href="javascript:void(0);" class="btn btn-danger email_remove_btn">-</a></div></div>';
                      $(email_dynamic).append(emailField);
                });

                $(email_dynamic).on('click', '.email_remove_btn', function(e){
                      e.preventDefault();
                   $(this).parent('div').parent('div').remove(); 

                });




                //dynamic email add

               var contact_dynamic = $('.contact_dynamic');

                $('.contact_add_btn').click(function(){
                  
                    var contactField = '<div class="row"><div class="form-group col-md-10"><input type="text" class="form-control contact_number"  id="contact_number" name="contact_number[]" placeholder="Enter Contact No."></div><div class="form-group col-md-2" style="margin-top: 0px;"><a href="javascript:void(0);" class="btn btn-danger contact_remove_btn">-</a></div></div>';
                      $(contact_dynamic).append(contactField);
                });

                $(contact_dynamic).on('click', '.contact_remove_btn', function(e){
                      e.preventDefault();
                   $(this).parent('div').parent('div').remove(); 

                });




               //dynamic bank details
     
               var b=1;
                $('.btn_add_bank').click(function(){

                    b+=1;
                  
                    var code = '<div class="row mx-0"><div class="border-row  flex-wrap w-100"><div class="card"><div class="card-header p-0"> <a class="card-header card-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#bank_field_'+b+'">Bank Details<button type="button" class="btn btn-sm btn-danger btnRemoveBank" onclick="removeBankSection(this)">-</button></a></div><div id="bank_field_'+b+'" class="collapse-active"><div class="card-body"><div class="row"><div class="form-group col-md-6"><label for="bank_name">Bank Name</label><input type="text" class="form-control" name="bank_name[]" placeholder="Enter Bank Name" value=""></div><div class="form-group col-md-6"><label for="bank_ac_number">Bank Account Number</label><input type="text" class="form-control" name="bank_ac_number[]" placeholder="Enter Bank Account Number" value=""></div><div class="form-group col-md-6"><label for="bank_branch">Bank Branch Name</label><input type="text" class="form-control" name="bank_branch[]" placeholder="Bank Branch Name" value=""></div><div class="form-group col-sm-6"><label for="bank_ifsc">IFSC Code</label><input type="text" class="form-control" name="ifsc[]"  placeholder="Enter IFSC Code" onkeypress="return IFSCValidation(event)" value="" id="ifsc" maxlength="11"></div></div></div></div></div></div></div>';
                      $('.dynamicBankSection').append(code);
                });





            $('#pincode').keyup(function() {
              $('span.error-keyup-1').hide();
              $("#submit").attr("disabled", false);

              if($(this).val().length < 6 ) {

                $("#submit").attr("disabled", true);

                  $(this).after('<span class="error error-keyup-1 text-danger">Invalid PinCode</span>');
              }
            });
            
            $('#pan_no').keyup('click', function () {
                $('span.error-keyup-2').hide();
                $('#submit').attr('disabled',false);
            });
            $('#gst_in').keyup('click', function () {
                $(this).val($(this).val().toUpperCase());

                $('span.error-keyup-3').hide();
                $('#submit').attr('disabled',false);
            });

            $('#ifsc').keyup('click',function () {
                $(this).val($(this).val().toUpperCase());
                $('span.error-keyup-4').hide();
                $('#submit').attr('disabled',false);
            });

            $("#ifsc").change(function () {      
              var inputvalues = $(this).val();      
              var reg = /[a-zA-Z0-9]{4}[0][a-zA-Z0-9]{6}$/;    
                if (inputvalues.match(reg)) {    
                    return true;    
                }    
                else {    
                    $('#ifsc').after('<span class="error error-keyup-4 text-danger">Invalid IFSC</span>');
                    $('#submit').attr('disabled',true);  
                    return false;    
                }    
            });



            var i = 0;
            $("#add").click(function(){
              var classcount = $('.imgcount').length
              i=parseInt(classcount)+parseInt(delCount)+1;

              var varietyfieldHTML= `<div class=" img_section form-group  col-sm-4 pl-0 imgcount dynamic_field_`+i+`"><label class="image-label" for="docImg">Document File `+i+`</label><div class="image-upload dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this,`+i+`);">X</button><img id="preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="readURL(this,`+i+`);" name="docImg[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]"></div></div>`;

            

            $('.imgcount').last().after(varietyfieldHTML);

            });
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
 

        });


        function removeBankSection(e)
        {
           $(e).parent().parent().parent().parent().remove();

        }




         //on key up
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

        //after focus out
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

function readURLLOGO(input) {
            var url = input.value;
            var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();

            $(input).parent().find('span.error-keyup-110').hide();
            if (input.files && input.files[0]&& (ext == "png" || ext == "jpeg" || ext == "jpg")) 
            {

                var reader = new FileReader();

                const fsize = input.files[0].size;
                const file_size = Math.round((fsize / 1024));


                if(file_size>1150) //1.1 MB
                {
                  $(input).after('<span class="error error-keyup-110 text-danger">Image Size Should Be 1 MB or Lesser...</span>');
                  $(input).val(''); 

                 

                }
               

            }
            else
            {
               alert('Please Select Valid Image... jpg,jpeg,png')  
            }
}  



    </script>



  </body>
</html>
