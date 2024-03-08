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
    <title>Products Database Create</title>
 
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New Products</span></a>
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
              <div class="card-header">Add New Products</div>
              <div class="card-body">
                <form class="" action="add.php" method="post" enctype="multipart/form-data">
                  <div class="row">
                    <div class="form-group col-sm-4">
                      <label for="prod_name">Product Name</label>
                      <input type="text" class="form-control" name="prod_name"  placeholder="Enter Product Name" id="prod_name" value="">
                    </div>
                    <div class="form-group col-sm-4">
                      <label for="prod_hsn">Product HSN</label>
                      <input type="text" class="form-control" name="prod_hsn"  pattern="[0-9]+" placeholder="Enter HSN" value="">
                    </div>
                    <div class="form-group col-sm-4">
                      <label for="prod_rate">Product Rate</label>
                      <input type="text" class="form-control" name="prod_rate" pattern="[0-9]+" placeholder="Enter Product Rate" value="">
                    </div>

                     <div class="col-md-6">
                   <div class="field_wrapper_bales">
                      <div class="bales_1">
                      <div class="row"> 
                        <div class="form-group col-md-8">
                      <label for="prod_bales">Product Quality</label>
                      <input type="text" class="form-control" name="prod_quality[]" placeholder="Enter Product Quality" value="">                
                      </div>
                      <div class="col-md-4">
                      <a style="margin-top: 32px;" href="javascript:void(0);" class="btn btn-primary add_bales_button" title="Add New Bales">Add</a>
                      </div>
                      </div>
                      </div>
                    </div>
                    </div>

                  </div>
                   
                    <div class="row">

                      <div class="col-md-6">
                   <div class="field_wrapper_variety">
                      <div class="main_variety_1">
                      <div class="row"> 
                        <div class="form-group col-md-8">
                      <label for="prod_variety">Product Variety</label>
                      <input type="text" class="form-control" name="prod_variety[]" placeholder="Enter Product Variety" value="">                
                      </div>
                      <div class="col-md-4">
                      <a style="margin-top: 32px;" href="javascript:void(0);" class="btn btn-primary add_variety_button" title="Add Variety">Add</a>
                      </div>
                      </div>
                      </div>
                    </div>
                    </div>

                      <div class="col-md-6">
                   <div class="field_wrapper_sub_variety">
                      <div class="sub_variety_1">
                      <div class="row"> 
                        <div class="form-group col-md-8">
                      <label for="prod_sub_variety">Product Sub Variety</label>
                      <input type="text" class="form-control" name="prod_sub_variety[]" placeholder="Enter Product Sub Variety" value="">                
                      </div>
                      <div class="col-md-4">
                      <a style="margin-top: 32px;" href="javascript:void(0);" class="btn btn-primary add_variety_sub_button" title="Add Sub Variety">Add</a>
                      </div>
                      </div>
                      </div>
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

 <script type="text/javascript">


     

        $(document).ready(function () {


        //Add Variety
        var i=1;
        var varietywrapper = $('.field_wrapper_variety'); 
         
          $('.add_variety_button').click(function(){    
              i=i+1;
              var varietyfieldHTML= '<div class="main_variety_'+i+'"><div class="row"><div class="form-group col-md-8"><input type="text" class="form-control" name="prod_variety[]" placeholder="Enter Product Variety" value=""></div><div class="col-md-4"><a href="javascript:void(0);" class="btn btn-danger remove_prod_variety">-</a></div></div></div>';

            $(varietywrapper).append(varietyfieldHTML);
              
          });
          
          $(varietywrapper).on('click', '.remove_prod_variety', function(e){
              e.preventDefault();
              $(this).parent('div').parent('div').parent('div').remove(); 
              
          });


          //Add Sub Variety
          var j=1;
        var Sub_varietywrapper = $('.field_wrapper_sub_variety'); 
         
          $('.add_variety_sub_button').click(function(){    
              j=j+1;
              var varietyfieldHTML= '<div class="sub_variety_'+j+'"><div class="row"><div class="form-group col-md-8"><input type="text" class="form-control" name="prod_sub_variety[]" placeholder="Enter Product Sub Variety" value=""></div><div class="col-md-4"><a href="javascript:void(0);" class="btn btn-danger remove_prod_sub_variety">-</a></div></div></div>';

            $(Sub_varietywrapper).append(varietyfieldHTML);
              
          });
          
          $(Sub_varietywrapper).on('click', '.remove_prod_sub_variety', function(e){
              e.preventDefault();
              $(this).parent('div').parent('div').parent('div').remove(); 
              
          });


           //Add Product Quality
          var k=1;
        var field_wrapper_bales = $('.field_wrapper_bales'); 
         
          $('.add_bales_button').click(function(){    
              k=k+1;
              var balesfieldHTML= '<div class="bales_'+k+'"><div class="row"><div class="form-group col-md-8"><input type="text" class="form-control" name="prod_quality[]" placeholder="Enter Product Quality" value=""></div><div class="col-md-4"><a href="javascript:void(0);" class="btn btn-danger remove_prod_bales">-</a></div></div></div>';

            $(field_wrapper_bales).append(balesfieldHTML);
              
          });
          
          $(field_wrapper_bales).on('click', '.remove_prod_bales', function(e){
              e.preventDefault();
              $(this).parent('div').parent('div').parent('div').remove(); 
              
          });










          // unique product validation check
          $('#prod_name').focusout(function() {
              checkProduct();
          });

          $('#prod_name').on('input', function() {
             
              $('span.error-keyup-1').hide();

          });


          function checkProduct()
          {
              var prod_name=$('#prod_name').val();
                  $.ajax({
                  type: "POST",
                  url: 'check_product.php',
                  data: {prod_name:prod_name},
                  success: function(response)
                  {
                      var jsonData = JSON.parse(response);
                      console.log(jsonData.product_found);

                     if(jsonData.product_found)
                     {
                       $('#prod_name').after('<span class="error error-keyup-1 text-danger">Product Already Exist.</span>');
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


        });
  </script>
   
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  </body>
</html>
