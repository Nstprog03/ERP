<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['pur_firm_id']) && !isset($_SESSION['pur_financial_year_id']))
{
  header('Location: ../purchase_index.php');
}
  $getFirm=$_SESSION["pur_financial_year"];
  $year_array=explode("/",$getFirm);

  // echo "<pre>";
  // print_r($year_array[0]);
  // echo "</pre>";exit();

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>RD Kapas Purchase Report Create</title>

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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New RD Kapas Purchase Report</span></a>
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

     <div class="last-updates">
            <div class="firm-selection-pre">
                <span class="pre-firm">Firm : </span><span class="pre-firm-name"><?php echo $_SESSION["pur_firm"]; ?></span>
            </div>
            <div class="year-selection-pre">
            <span class="pre-year-text">Financial Year :</span> 
            <span class="pre-year">
              <?php 

              $finYearArr=explode('/',$_SESSION["pur_financial_year"]);

              $start_date=date('Y', strtotime($finYearArr[0]));
               $end_date=date('Y', strtotime($finYearArr[1]));

              echo $start_date.' - '.$end_date;

              ?>
            </span>
            </div>
        </div>


      <div class="container-fluid">
        <div class="row justify-content-center">
          
            <div class="card">
              <div class="card-header">RD Kapas Purchase Report Create</div>
              <div class="card-body">

                <form class="" action="add.php" method="post" enctype="multipart/form-data">
              <div class="row">      

                     <div class="form-group col-md-2">
                      <label for="report_date">Report Date</label>
                    <input type="text"class="form-control datepicker" name="report_date" autocomplete="off" placeholder="Enter Date">
                  </div>


                  <div class="form-group col-md-2">
                      <label for="invoice_no">Invoice No</label>
                    <input type="text"  class="form-control" name="invoice_no" id="invoice_no" placeholder="Enter Invoice No" value="">
                  </div>

                 

                    <div class="form-group col-md-4">

                      <label for="external_party">Select  Party</label>
                       <a class="btn btn-primary btn-sm" target="_blank" href="/external-party/create.php"><i class="fa fa-user-plus"></i></a>
                      <?php
                            $sql = "select * from external_party";
                            $result = mysqli_query($conn, $sql);
                          ?>
                      
                           <select name="external_party" data-live-search="true" class="form-control searchDropdown" id="ext_party" onchange="get_GSTNO(this.value)">
                                <?php                   
                                    foreach ($conn->query($sql) as $result) {

                                        echo "<option  value='".$result['id']. "'>" .$result['partyname']. "</option>";  
                                    }
                                ?>
                            </select>
                    </div>

                    <div class="form-group col-md-4">
                      <label for="party">GST No.:</label>
                      <input type="text" class="form-control set-gst-no" placeholder="GST No" readonly="readonly">
                  </div>
              </div>

              <div class="row">
                    <div class="form-group col-md-4">
                      <label for="firm">Firm</label>
                      <input type="text" class="form-control" value="<?php echo $_SESSION['pur_firm']; ?>" readonly="">

                      <input type="hidden" value="<?php echo $_SESSION['pur_firm_id']; ?>" name="firm">
                      
                      <input type="hidden" value="<?php echo $_SESSION['pur_financial_year_id']; ?>" name="financial_year">

                    </div>

                    <div class="form-group col-md-4">
                      <label for="product">Select Product</label>
                        <?php
                            $sql = "select * from products";
                            $result = mysqli_query($conn, $sql);
                            
                        ?>
                      
                        <select name="product" data-live-search="true" class="form-control searchDropdown">
                            <?php                   
                              foreach ($conn->query($sql) as $result) {

                                echo "<option  value='" .$result['id']. "'>" .$result['prod_name']. "</option>";  

                              }
                              ?>
                        </select>
                    </div>
            

                    <div class="form-group col-md-4">

                      <label for="broker">Select Broker</label>

                        <a class="btn btn-primary btn-sm" target="_blank" href="/broker/create.php"><i class="fa fa-user-plus"></i></a>

                      <?php
                            $sql = "select * from broker";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>
                      
                            <select name="broker" data-live-search="true" class="form-control searchDropdown">
                              <?php                   
                                foreach ($conn->query($sql) as $result) {

                                  echo "<option  value='".$result['id']."'>" .$result['name']. "</option>";  

                                }
                                ?>
                            </select>
                    </div>
              </div>
              <div class="row">
                  <div class="form-group col-md-4">
                     <label for="basic_amt">Basic Amount</label>
                     <input type="text" value="" class="form-control basic" name="basic_amt" onkeypress="return NumericValidate(event,this)"  placeholder="Enter Basic Amount" pattern="[0-9]+">
                  </div>
    

                   <div class="form-group col-md-4">
                      <label for="tax">Tax (In Percentage)</label>
                      <input type="text" value="" class="form-control tax" name="tax" placeholder="Enter Tax" onkeypress="return NumericValidate(event,this)" pattern="[0-9]+">
                  </div>

                   <div class="form-group col-md-4">
                      <label for="tax_amt">Tax Amount</label>
                      <input type="text" value="" class="form-control tax_amt" name="tax_amt" id="tax_amt" readonly>
                  </div>

                    <div class="form-group col-md-4">
                      <label for="tcs">TCS (In Percentage)</label>
                      <input type="text" onkeypress="return NumericValidate(event,this)" value="" class="form-control tcs" name="tcs"  placeholder="Enter TCS">
                  </div>
             
                    <div class="form-group col-md-4">
                      <label for="tcs_amt">TCS Amount</label>
                      <input type="text" value="" class="form-control tcs_amt" name="tcs_amt" id="result" readonly>
                  </div>


                   <div class="form-group col-md-4">
                      <label for="gd_value">Goods Value</label>
                      <input type="text" value="" class="form-control gd_value" name="gd_value" id="gd_amt" readonly>
                  </div>

                 
           
                  <div class="form-group col-md-4">
                      <label for="net_amt">Net Amount</label>
                      <input type="text" value="" class="form-control bold" name="net_amt" id="net_amt" readonly>
                  </div>
            </div>
                  <div class="row dynamicWrapper" style="margin-left:0px">
                    <div class=" form-group  col-sm-4 pl-0 imgcount dynamic_field_1">
                      <label class="image-label" for="docimg">Document File 1</label>
                        <div class="image-upload dynamic_field">
                        
                          <img id="preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
                          <input type="file" class="form-control" id="img1" onchange="readURL(this);" name="docimg[]" value="">
                          <br>
                          <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]">
                        </div>



                    </div>
                    <div class="form-group form-group col-sm-4 pl-0">
                      <label class="image-label" for="docimg">Add more</label>
                        <div class="image-upload">
                        
                          <button type="button" class=" btn btn-defult" id="add" style="height: 340px;width: 263px;"><i class="fa fa-plus" aria-hidden="true" style="width: 35%;height: 117px;"></i>
                          </button>
                        </div>
                    </div>
                  </div>
    
    

                    <div class="form-group">
                      <button type="submit" name="Submit" class="btn btn-primary waves" onclick="save_data(this)">Submit</button>
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
     <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

       <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script type="text/javascript">

      var delCount=0;

        $(document).ready(function () {
           $(".datepicker").datepicker({dateFormat:'dd/mm/yy',
              dateFormat:'dd/mm/yy',
              changeMonth: true,
              changeYear: true,
              maxDate: new Date('<?php echo($year_array[1]) ?>'),
              minDate: new Date('<?php echo($year_array[0]) ?>')
            });

           $(".datepicker").keydown(false);


            var i = 0;
            $("#add").click(function(){
              var classcount = $('.imgcount').length
              i=parseInt(classcount)+parseInt(delCount)+1;

              var varietyfieldHTML= `<div class=" img_section form-group  col-sm-4 pl-0 imgcount dynamic_field_`+i+`"><label class="image-label" for="docimg">Document File `+i+`</label><div class="image-upload dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this,`+i+`);">X</button><img id="preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="readURL(this,`+i+`);" name="docimg[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]"></div></div>`;

            

            $('.imgcount').last().after(varietyfieldHTML);

            });




            $('#invoice_no').on('input', function() {
             
                      $('span.error-keyup-1').hide();

                        checkInvoiceNo();
                    
                });

             $('#ext_party').on('change', function() {
             
                      $('span.error-keyup-1').hide();

                        checkInvoiceNo();
                    
                });


        });

         function checkInvoiceNo()
        {
            var invoice_no=$('#invoice_no').val();
            var ext_party=$('#ext_party :selected').val();

                $.ajax({
                type: "POST",
                url: 'check_invoiceAJAX.php',
                data: {
                  invoice_no:invoice_no,
                  ext_party:ext_party,
                },
                success: function(response)
                {
                    var jsonData = JSON.parse(response);
                    console.log(jsonData.name_exist);

                   if(jsonData.name_exist)
                   {
                     $('#invoice_no').after('<span class="error error-keyup-1 text-danger">Already Exist.</span>');
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


         

  </script>

    <script type="text/javascript">


        $(document).ready(function()
        {
  $('input[type="text"]').keyup(function () 
  {
  var basic_amt = parseFloat($('.basic').val());
  var tax_pr = parseFloat($('.tax').val());
  var tcs_pr = parseFloat($('.tcs').val());
  var tcs_amt = parseFloat($('.tcs_amt').val());
  var debit_amt = parseFloat($('.dbt_amt').val());
  var val6 = parseFloat($('.gd_value').val());
  var tax_amt = parseFloat($('.tax_amt').val());

  if (isNaN(basic_amt)) {
    basic_amt = 0;
  }
  if (isNaN(tax_amt)) {
    tax_amt = 0;
  }
  if (isNaN(tcs_pr)) {
    tcs_pr = 0;
  }
  if (isNaN(tcs_amt)) {
    tcs_amt = 0;
  }
  if (isNaN(debit_amt)) {
    debit_amt = 0;
  }
  if (isNaN(val6)) {
    val6 = 0;
  }
 

          var tax_amt = (basic_amt) * tax_pr/100;
          var tcs_amt = (basic_amt+tax_amt) * tcs_pr/100;
          var mnt = (basic_amt)+(tax_amt)+(tcs_amt);
          var net = (mnt)-(debit_amt);

           if (isNaN(tax_amt)) {
              tax_amt = 0;
            }
             if (isNaN(tcs_amt)) {
              tcs_amt = 0;
            }
             if (isNaN(mnt)) {
              mnt = 0;
            }
             if (isNaN(net)) {
              net = 0;
            }



          $("input#result").val(tcs_amt.toFixed(2));
          $("input#tax_amt").val(tax_amt.toFixed(2));
          $("input#gd_amt").val(mnt.toFixed(2));
          $("input#net_amt").val(net.toFixed(2));       
});
});

        function NumericValidate(evt, element) {

     var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57) && !(charCode == 46 || charCode == 8))
    return false;
  else {
    var len = $(element).val().length;
    var index = $(element).val().indexOf('.');
    if (index > 0 && charCode == 46) {
      return false;
    }
    if (index > 0) {
      var CharAfterdot = (len + 1) - index;
      if (CharAfterdot > 3) {
        return false;
      }
    }

  }
  return true;       
}

</script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>



  </body>
</html>

<script>
  function get_GSTNO(party_id){
    
        $.ajax({
            type: "POST",
            url: 'get_GSTNO.php',
            data: {party_id:party_id},
            success: function(response)
            {
                var jsonData = JSON.parse(response);
             //   console.log(jsonData);

              if(jsonData.status==true){

                  if(jsonData.gstin_data!=''){
                      $('.set-gst-no').val(jsonData.gstin_data);
                }else{
                  $('.set-gst-no').val(''); 
                }
              }else{
                $('.set-gst-no').val(''); 
              }
           }
       });
  }
  
 
  function save_data(e){
        $(e).css("pointer-events", "none");
  }
  
</script>
