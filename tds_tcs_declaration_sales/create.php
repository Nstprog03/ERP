<?php
   session_start();
   include('../db.php');
   
   if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
       header("location: ../login.php");
       exit;
   }
   
    if(isset($_GET['module']))
    {
      if($_GET['module'] == "sales"){
        $module=$_GET['module'];
      }elseif($_GET['module'] == "purchase_cotton"){
        $module="purchase_cotton";
      }
    }
   
   
   ?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <title>Create New TDS/TCS Declaration</title>
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
                  <a class="navbar-brand" href="index.php?module=<?= $module ?>"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New TDS/TCS Declaration</span></a>
                  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                  <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarSupportedContent">
                     <ul class="navbar-nav mr-auto"></ul>
                     <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="btn btn-outline-danger" href="index.php?module=<?= $module ?>"><i class="fa fa-sign-out-alt"></i>Back</a></li>
                     </ul>
                  </div>
               </div>
            </nav>
            
            <div class="container-fluid">
               <div class="row justify-content-center">
                  <div class="card">
                     <div class="card-header">Create New TDS/TCS Declaration</div>
                     <div class="card-body">
                        <form class="" action="add.php" method="post" enctype="multipart/form-data">
                           <input type="hidden" name="module" value="<?= $module ?>">
                           <div class="row">
                              <div class="col-md-4">
                                 <div class="form-group">
                                    <label for="firm">Select Party (Firm)</label>
                                    <?php
                                       $sql = "select * from party";
                                       $result = mysqli_query($conn, $sql);
                                       ?>                      
                                    <select name="firm" id="firm" class="form-control">
                                       <option selected="">Select Option</option>
                                    <?php                   
                                       foreach ($conn->query($sql) as $result) 
                                       {
                                           echo "<option  value='".$result['id']."'>".$result['party_name']. "</option>";
                                       }
                                       ?>                              
                                    </select>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <div class="form-group">
                                    <label for="financial_year">Select Financial Year</label>
                                      <?php
                                       $sql2 = "select * from financial_year";
                                       
                                       $result2 = mysqli_query($conn, $sql2);
                                       
                                       $storeDates=array();
                                       $Dates=array();
                                       while($rwdata=mysqli_fetch_assoc($result2)){
                                       
                                       
                                         $Dates=[
                                           'startdate'=>date('Y-m-d',strtotime($rwdata['startdate'])),
                                           'enddate'=>date('Y-m-d',strtotime($rwdata['enddate'])),
                                           'id'=>$rwdata['id'],
                                         ];
                                       
                                         //array_push($storeDates, date('Y-m-d',strtotime($rwdata['startdate']))."-".date('Y-m-d',strtotime($rwdata['enddate'])));
                                         array_push($storeDates, $Dates);
                                       }
                                       ?>                     
                                    <select name="financial_year" class="form-control">
                                      <?php                   
                                       rsort($storeDates);
                                       $arrlength = count($storeDates);
                                       
                                       for($x = 0; $x < $arrlength; $x++) {
                                       
                                         //current financial year selected
                                               $curDate=date('Y-m-d');
                                               $startdate=date('Y-m-d', strtotime($storeDates[$x]['startdate']));
                                               $enddate=date('Y-m-d', strtotime($storeDates[$x]['enddate']));
                                             
                                               if($curDate>=$startdate && $curDate<=$enddate)
                                               {
                                               echo "<option value=" .$storeDates[$x]['id']." selected=''>".date('Y',strtotime($storeDates[$x]['startdate']))."-".date('Y',strtotime($storeDates[$x]['enddate']))."</option>";
                                               }else{
                                               echo "<option value=" .$storeDates[$x]['id'].">".date('Y',strtotime($storeDates[$x]['startdate']))."-".date('Y',strtotime($storeDates[$x]['enddate']))."</option>";
                                               }
                                       }
                                       ?>                              
                                    </select>
                                </div>
                              </div>
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
                                         echo "<option  value='".$result['id']."'>" .$result['partyname']. "</option>";
                                       }
                                       ?>    
                                    </select>
                                 </div>
                              <div class="form-group col-md-4">
                                 <label for="date">Date</label>
                                 <input type="text" name="date" placeholder="Select Date" class="form-control datepicker" autocomplete="off">
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="good_exceeding">Goods Exceeding</label>
                                 <input type="text" name="good_exceeding" placeholder="Enter Goods Exceeding" class="form-control">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="status">Select Turnover</label>                  
                                 <select class="form-control" name="turnover" required="">
                                    <option value="Rs.10 crores">Rs.10 crores</option>
                                    <option value="Less 10 crore">Less 10 crore</option>
                                 </select>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="audit_report_id">Select Audit & ITR</label>
                                 <?php
                                    $sql = "select * from party_audit_report where ad_report_type='IT Return Reoport'";
                                    $result = mysqli_query($conn, $sql);
                                 ?>
                                 <select class="form-control" id="audit_year" name="audit_report_id[]" multiple="" required="">
                                    
                                 </select>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="status">Select Status</label>                  
                                 <select class="form-control" name="status" required="">
                                    <option value="0">Pending</option>
                                    <option value="1">Complete</option>
                                 </select>
                              </div>
                           </div>
                           <div class="row dynamicWrapper">
                              <div class=" form-group  col-sm-4 imgcount dynamic_field_1">
                                 <label class="image-label" for="cma">Document File 1</label>
                                 <div class="image-upload dynamic_field">
                                    <img id="preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
                                    <input type="file" class="form-control" id="img1" onchange="readURL(this);" name="doc_file[]" value="">
                                    <br>
                                    <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="">
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
                           <div class="row">
                              <div class="form-group col-md-12">
                                 <button type="submit" name="Submit" class="btn btn-primary waves">Submit</button>
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
      <script type="text/javascript">
         var delCount=0;
         
           $(document).ready(function () {
              
            $("#firm").on("change", function(){
               var firm = $(this).val();
               $("#audit_year option").remove();
               $.ajax({
                  type : "POST",
                  url : "getData.php",
                  data : {
                     Getyear : true,
                     firm : firm
                  },
                  success: function(response){
                     var data = JSON.parse(response);
                     if(data.status == true){
                        $.each(data.year, function(index,item){
                           var option = "<option  value='"+item.id+"'>"+item.year+"</option>";
                           $("#audit_year").append(option);
                        });
                     }
                  }

               });
            });
         
               var i = 0;
               $("#add").click(function(){
                 var classcount = $('.imgcount').length
                 i=parseInt(classcount)+parseInt(delCount)+1;
         
                var varietyfieldHTML= `<div class=" img_section form-group col-sm-4 imgcount dynamic_field_`+i+`"><label class="image-label" for="cma">Document File `+i+`</label><div class="image-upload dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this,`+i+`);">X</button><img id="preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="readURL(this,`+i+`);" name="doc_file[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value=''></div></div>`;
         
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