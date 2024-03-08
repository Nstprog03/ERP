<?php
session_start();
include('../../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Create Stock Auditor Report</title>
 

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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New Stock Auditor Details</span></a>
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
              <div class="card-header">Stock Auditor</div>
              <div class="card-body">
                 


                <form class="" action="add.php" method="post" enctype="multipart/form-data">

                  <div class="row row-border">
                    <div class="form-group col-sm-5">
                      <label for="firm">Select Firm</label>
                      <?php
                            $sql = "select * from party";
                            $result = mysqli_query($conn, $sql); 
                          ?>
                      
                           <select name="firm" class="form-control">
                            <?php                   
                                foreach ($conn->query($sql) as $result) {

                                    echo "<option  value='" .$result['id']. "'>" .$result['party_name']. "</option>";  

                                }
                            ?>
                        </select>

                      
                  
                    </div>

 
                    <div class="form-group col-sm-3">
                      <label for="start_yr">Select Start Year</label>
                    <select name="start_yr" class="form-control" id="selectElementId">
                    </select>
                  </div>

                    <div class="form-group col-sm-3">
                      <label for="end_yr">Select End Year</label>
                    <select name="end_yr" class="form-control" id="endYear">
                    </select>
                  </div>
</div>

<div class="card-header inn-head">Auditor-1 Details</div>
                <div class="row row-border">

               
                  <div class="form-group col-sm-4">
                      <label for="audit1_name">Auditor Name</label>
                      <input type="text" class="form-control" name="audit1_name" value="">
                    </div>

                   <div class="form-group col-sm-4">
                      <label for="audit1_addr">Auditor Address</label>
                      <input type="text" class="form-control" name="audit1_addr" value="">
                    </div> 
                   
                   <div class="form-group col-sm-4">
                      <label for="audit1_no">Auditor Contact</label>
                      <input type="text" class="form-control" name="audit1_no" value="">
                    </div>  
                </div>

                <div class="row aud1_dynamicWrapper">
                    
                    <div class="form-group col-md-4 aud1_imgcount aud1_dynamic_field_1">
                      <label class="image-label" for="cma">Auditor 1 Document File 1</label>
                        <div class="image-upload aud1_dynamic_field">
                        
                          <img id="aud1_preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
                          <input type="file" class="form-control" id="img1" onchange="aud1_readURL(this);" name="aud1_doc_file[]" value="">
                          <br>
                          <input type="text" class="form-control" placeholder="Enter Image Title" name="aud1_img_title[]">
                        </div>

                    </div>

              

                     <div class="form-group col-sm-4">
                      <label class="image-label" for="cma">Add more</label>
                       <div class="image-upload">
                        
                      <button type="button" class=" btn btn-defult" id="aud1_add" style="height: 340px; width: 263px;"><i class="fa fa-plus" aria-hidden="true" style="width: 35%;height: 117px;"></i>
                      </button>                 
                    </div>
                    </div> 
            </div>


<div class="card-header inn-head">Auditor-2 Details</div>
<div class="row row-border">
                      <div class="form-group col-sm-4">
                      <label for="audit2_name">Auditor Name</label>
                      <input type="text" class="form-control" name="audit2_name" value="">
                    </div>

                   <div class="form-group col-sm-4">
                      <label for="audit2_addr">Auditor Address</label>
                      <input type="text" class="form-control" name="audit2_addr" value="">
                    </div> 
                   
                   <div class="form-group col-sm-4">
                      <label for="audit2_no">Auditor Contact</label>
                      <input type="text" class="form-control" name="audit2_no" value="">
                    </div>
                  
        </div>

        <div class="row aud2_dynamicWrapper">
                    
                    <div class="form-group col-md-4 aud2_imgcount aud2_dynamic_field_1">
                      <label class="image-label" for="cma">Auditor 2 Document File 1</label>
                        <div class="image-upload aud2_dynamic_field">
                        
                          <img id="aud2_preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
                          <input type="file" class="form-control" id="img1" onchange="aud2_readURL(this);" name="aud2_doc_file[]" value="">
                          <br>
                          <input type="text" class="form-control" placeholder="Enter Image Title" name="aud2_img_title[]">
                        </div>

                    </div>

            
                     <div class="form-group col-sm-4">
                      <label class="image-label" for="cma">Add more</label>
                       <div class="image-upload">
                        
                      <button type="button" class=" btn btn-defult" id="aud2_add" style="height: 340px; width: 263px;"><i class="fa fa-plus" aria-hidden="true" style="width: 35%;height: 117px;"></i>
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





      var min = 1950,
      max = min + 100,
      select = document.getElementById('selectElementId');

      //getCurrent Year
       var d = new Date();
       var curYear = d.getFullYear();

      for (var i = min; i<=max; i++){

        if(i==curYear)
        {
          var opt = document.createElement('option');
          opt.value = i;
          opt.innerHTML = i;
          opt.selected=true;
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

      //getCurrent Year
       var d = new Date();
       var curYear = d.getFullYear();

      for (var i = min; i<=max; i++){

        if(i==curYear)
        {
          var opt = document.createElement('option');
          opt.value = i;
          opt.innerHTML = i;
          opt.selected=true;
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
