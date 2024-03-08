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
    <title>Labroratory Master Create</title>
  
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New Laboratory Master</span></a>
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
              <div class="card-header">Create Laboratory Master</div>
              <div class="card-body">
                <form class="" action="add.php" method="post" enctype="multipart/form-data">
                  <div class="row">
                    <div class="form-group col-md-4">
                      <label for="lab_name">Lab Name</label>
                      <input type="text" class="form-control" name="lab_name"  placeholder="Enter Lab Name" value="" >
                    </div>

                    <div class="form-group col-md-4">
                      <label for="transport">GST No</label>                      
                      <input id="gst_in" type="text" class="form-control" name="gst_no"  placeholder="Enter GST No" value="" onkeypress="return GSTValidationKeyup(event)" maxlength="15" >
                    </div>
                    <div class="form-group col-md-4">
                      <label for="pan_no">PAN No.</label>
                      <input type="text" class="form-control" name="pan_no" placeholder="Enter PAN No."  onchange="validatePanNumber(this)" onkeypress="return panValidationKeyup(event)" style="text-transform:uppercase" maxlength="10" minlength="10" id="pan_no">
                    </div>
                  </div>

                  <div class="row">                                      
                    <div class="form-group col-sm-4">
                      <label for="address">Address</label>                      
                      <input type="text" class="form-control" name="address"  placeholder="Enter Address" value="">
                    </div>
                    <div class="form-group col-sm-4">
                      <label for="city">City</label>                      
                      <input type="text" class="form-control" name="city"  placeholder="Enter City" value="">
                    </div>
                    <div class="form-group col-sm-4">
                      <label for="contact_no">Contact No</label>                      
                      <input type="text" class="form-control" name="contact_no"  placeholder="Enter Contact No" value="" onkeypress="return NumericValidate(event)">
                    </div>
                  </div>
                  
                  <br>
                  <h4>Bank Details</h4>                  
                  <div class="row">                    
                    <div class="form-group col-sm-6">
                      <label for="bank_name">Bank Name</label>                      
                      <input type="text" class="form-control" name="bank_name"  placeholder="Enter Bank Name" value="">
                    </div>
                    <div class="form-group col-sm-6">
                      <label for="bank_ifsc">Bank IFSC</label>                      
                      <input type="text" class="form-control" name="bank_ifsc"  placeholder="Enter Bank IFSC" value="" onkeypress="return IFSCValidation(event)" id="ifsc" maxlength="11">
                    </div>
                  </div>
                  
                  <div class="row">                    
                    <div class="form-group col-sm-6">
                      <label for="account_no">Account No</label>                      
                      <input type="text" class="form-control" name="account_no"  placeholder="Enter Account No" value="" onkeypress="return NumericValidate(event)">
                    </div>
                    <div class="form-group col-sm-6">
                      <label for="branch">Branch</label>                      
                      <input type="text" class="form-control" name="branch"  placeholder="Enter Branch" value="">
                    </div>
                  </div>


                  <div class="row dynamicWrapper">
                    <div class=" form-group  col-sm-4 imgcount dynamic_field_1">
                      <label class="image-label" for="cma">Document File 1</label>
                        <div class="image-upload dynamic_field">
                        
                          <img id="preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
                          <input type="file" class="form-control" id="img1" onchange="readURL(this);" name="doc_file[]" value="">
                          <br>
                          <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]">
                        </div>

                    </div>

              

                    <div class="form-group form-group col-sm-4 pl-0">
                      <label class="image-label" for="cma">Add more</label>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <script>
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


      var delCount=0;
        $(document).ready(function () {

            var i = 0;

            $("#add").click(function(){
              var classcount = $('.imgcount').length


              i=parseInt(classcount)+parseInt(delCount)+1;
              var varietyfieldHTML= `<div class=" img_section form-group col-sm-4 imgcount dynamic_field_`+i+`"><label class="image-label" for="cma">Document File `+i+`</label><div class="image-upload dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this,`+i+`);">X</button><img id="preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="readURL(this,`+i+`);" name="doc_file[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]"></div></div>`;

            //$('.dynamicWrapper').append(varietyfieldHTML);
            
            //$('.dynamic_field_'+i-1).after(varietyfieldHTML);
            /*var j = i-1;

            $('.dynamic_field_'+j).after(varietyfieldHTML);*/


            $('.imgcount').last().after(varietyfieldHTML);


            });
            $('#pan_no').keyup('click', function () {
                $('span.error-keyup-2').hide();
                $('#submit').attr('disabled',false);
            });

            $('#ifsc').keyup('click',function () {
                $(this).val($(this).val().toUpperCase());
                $('span.error-keyup-1').hide();
                $('#submit').attr('disabled',false);
            });

            $("#ifsc").change(function () {      
              var inputvalues = $(this).val(); 

              if(inputvalues!='')
              {
                  var reg = /[a-zA-Z0-9]{4}[0][a-zA-Z0-9]{6}$/;    
                  if (inputvalues.match(reg)) {    
                      return true;    
                  }    
                  else {    
                      $('#ifsc').after('<span class="error error-keyup-1 text-danger">Invalid IFSC</span>');
                      $('#submit').attr('disabled',true);  
                      return false;    
                  } 
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

                if(inputvalues!='')
                {
                    var gstinformat = new RegExp('^[0-9]{2}[A-Z]{5}[0-9]{4}([a-zA-Z]){1}([a-zA-Z0-9]){1}([a-zA-Z0-9]){1}([a-zA-Z0-9]){1}?$');    
                    if (gstinformat.test(inputvalues)) {    
                        return true;    
                    } else {    
                        $(this).after('<span class="error error-keyup-3 text-danger">Invalid GST NO.</span>');   
                        $(".gst").val(''); 
                        $('#submit').attr('disabled',true);   
                        $(".gst").focus();    
                    }    
                }

                
            });


        });


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





        function removeImg(e,index) {
        $(e).parent('div').parent('div').remove(); 
        delCount=delCount+1;
        }


        //PAN Validation

        //check after focus out
        function validatePanNumber(pan) {
            let pannumber = $(pan).val();

            if(pannumber!='')
            {
                 var regex = /[a-zA-z]{5}\d{4}[a-zA-Z]{1}/;
                  if (pannumber.match(regex)) {
                      
                  }
                  else {
                      $('#pan_no').after('<span class="error error-keyup-2 text-danger">Invalid PAN number</span>');
                      $('#submit').attr('disabled',true);
                  }
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
  

  function NumericValidate(key) {
    var keycode = (key.which) ? key.which : key.keyCode;

    if (keycode >= 48 && keycode <= 57)  
    {     
           return true;    
    }
    else
    {
        return false;
    }
         
}     

    </script>
  </body>
</html>
