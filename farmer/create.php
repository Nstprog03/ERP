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
    <title>Farmer Database Create</title>
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New Farmer Database</span></a>
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
              <div class="card-header">Farmer Create</div>
              <div class="card-body">
                 


                <form class="" action="add.php" method="post" enctype="multipart/form-data">
                  <div class="row">
                    <div class="form-group col-md-4">
                      <label for="farmer_name">Farmer Name</label>
                      <input type="text" name="farmer_name" id="name" placeholder="Enter Farmer Name" class="form-control">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="vlg_name">Village Name</label>
                      <input type="text" name="vlg_name" placeholder="Enter Village Name" class="form-control">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="tal_name">Taluka Name</label>
                      <input type="text" name="tal_name" placeholder="Enter Taluka Name" class="form-control">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-4">
                      <label for="dist_name">District Name</label>
                      <input type="text" name="dist_name" placeholder="Enter District Name" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                      <label for="bank_name">Bank Name</label>
                      <input type="text" name="bank_name" placeholder="Enter Bank Name" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                      <label for="ifsc">IFSC</label>
                      <input type="text" name="ifsc" id="ifsc" placeholder="Enter IFSC " onkeypress="return IFSCValidation(event)" class="form-control" maxlength="11">
                    </div>
                    
                  </div>
                  <div class="row">
                    <div class="form-group col-md-6">
                        <label for="branch">Branch Name</label>
                        <input type="text" name="branch" placeholder="Enter Branch Name" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ac_no">Account Number</label>
                        <input type="text" name="ac_no" id="ac_no" placeholder="Enter Account Number" class="form-control">
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


            $('#name').on('input', function() {
             
                    $('span.error-keyup-1').hide();

                      
                          checkName();
                        
              });

              $('#ac_no').on('input', function() {
             
                    $('span.error-keyup-1').hide();

                      
                    check_Account_numbers_unique();
                        
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

  function check_Account_numbers_unique() {
    var ac_no=$('#ac_no').val();
              $.ajax({
              type: "POST",
              url: 'check_account_number_AJAX.php',
              data: {ac_no:ac_no},
              success: function(response)
              {
                  var jsonData = JSON.parse(response);
                  console.log(jsonData.name_exist);

                 if(jsonData.name_exist)
                 {
                   $('#ac_no').after('<span class="error error-keyup-1 text-danger">Account Number Already Exist.</span>');
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


</script>
  </body>
</html>
