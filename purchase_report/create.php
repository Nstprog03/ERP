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
  // print_r($year_array);
  // echo "</pre>";exit();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Create New Purchase Report</title>
   
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



     <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
         rel = "stylesheet">

      <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

       <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">




     <script> 
    $(function(){
     $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html"); 

       $(".datepicker").datepicker({

        dateFormat:'dd/mm/yy',
         changeMonth: true,
          changeYear: true,
        maxDate: new Date('<?php echo($year_array[1]) ?>'),
        minDate: new Date('<?php echo($year_array[0]) ?>')
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New Purchase Report Database</span></a>
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
              <div class="card-header">Purchase Report Database</div>
              <div class="card-body">
                <form class="" action="add.php" method="post"
                     enctype="multipart/form-data">
                     

                            <input type="hidden" name="pur_firm" value="<?php echo $_SESSION['pur_firm_id']; ?>">

                            <div class="row">
                      
                            <div class="form-group col-md-5">
                              <label for="party">Select External Party</label>
                              <?php
                                  

                                    $sql = "select * from pur_conf where firm ='".$_SESSION['pur_firm_id']."' AND financial_year = '".$_SESSION['pur_financial_year_id']."' AND conf_type!=2" ;

                                    $result = mysqli_query($conn, $sql);
                                    
                                  ?>                      
                                   <select id="epartySelect" name="party" data-live-search="true" class="form-control searchDropdown" required onchange="get_GSTNO(this.value)">
                                    <option value="" disabled selected>Select Party</option>
                                    <?php                   
                                      foreach ($conn->query($sql) as $result) 
                                      {

                                        $sql2="SELECT SUM(bales) as used_bales FROM pur_report WHERE conf_no='".$result['pur_conf']."'";
                                        $result2 = mysqli_query($conn, $sql2);
                                          $row1 = $result2->fetch_assoc();


                                        $party_sql="SELECT * FROM external_party WHERE id='".$result['party']."'";
                                        $party_result = mysqli_query($conn, $party_sql);
                                          $party_row = $party_result->fetch_assoc();
                                          if($result['bales']-$row1['used_bales']!=0)
                                          {

                                        echo "<option  value='" .$party_row['id'].'/'.$result['id']."'>" .$party_row['partyname'].' ('.$result['pur_conf'].')'."</option>";
                                            // echo "<option  value='" .$result['partyname'].'/'.$result['pur_conf']."'>" .$result['partyname'].' ('.$result['pur_conf'].')'."</option>";
                                          }
                                      }
                                    ?>                              
                                    </select>
                            </div>

                            <div class="form-group col-md-5">
                              <label for="party">GST No.:</label>
                              <input type="text" class="form-control set-gst-no" placeholder="GST No" readonly="readonly">
                          </div>

                            </div>
                        

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="pur_conf">Confirmation No.</label>
                              <input type="text" name="pur_conf" class="form-control" id="pur_conf" readonly/>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="avl_bales">Available Bales</label>
                                <input type="text" name="avl_bales" class="form-control" id="avl_bales" readonly />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="candy_rate">Candy Rate</label>
                                <input type="text" name="candy_rate" class="form-control" id="candy_rate" readonly />
                            </div>
                        </div>

                        <div class="col-md-3">  
                            <div class="form-group">
                                <label for="broker">Broker</label>
                                <input type="text" class="form-control"  id="broker"   readonly>

                                <input type="hidden" name="broker" id="broker_id">
                            </div>
                        </div>
                        
                    </div>

                    <div class="form-group">
                                <label for="invoice_no">Invoice No.</label>
                                <input type="text" name="invoice_no" class="form-control" id="invoice_no" placeholder="Enter Invoice No." onkeypress="return NumericValidate(event,this)" />
                    </div>


                    <div class="row">
                        <div class="col-md-6">  
                            <div class="form-group">
                                <label for="lot_no">Lot No.</label>
                                <input type="text" name="lot_no" class="form-control" id="lot_no" placeholder="Enter LOT No." />
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="report_date">Select Report Date :</label>
                                <input class="form-control datepicker" type="text" id="datepicker" name="report_date" placeholder="Report Date" required="" autocomplete="off">
                            </div>
                        </div>
                        


                    </div>

                    <div class="row">
                       
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pr_no_start">start PR No.</label>
                                <input type="text" name="pr_no_start" class="form-control" id="pr_no_start" placeholder="Enter PR No. Start" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pr_no_start">End PR No.</label>                
                                <input type="text" name="pr_no_end" class="form-control" id="pr_no_end" placeholder="Enter PR No. End" />
                            </div>
                        </div>
                   
                    </div>

                      


                <input type="hidden" name="trans_pay_type" id="trans_pay_type" value="">

                  <div id="transportSection" >
                      <br>
                      <div class="row">
                      <br>
                      <h4 class="col-md-12">Transport Details</h4>
                      <br>

                      <div class="form-group col-md-4">
                        <label for="trans_name">Transport Name</label>
                        <input type="text" class="form-control" name="trans_name" id="trans_name" value="" readonly>

                        <input type="hidden" id="trans_id" name="trans_id" value="">
                      </div>

                      <div class="form-group col-md-4">
                        <label for="trans_veh_no">Transport Vehicle No</label>
                        <input type="text" class="form-control" name="trans_veh_no" id="trans_veh_no" placeholder="Enter Transport Vehicle No" value="">     
                      </div>


                          <div class="form-group col-md-4 trans_lr_date">
                              <label for="trans_lr_date">Select LR Date :</label>
                              <input class="form-control datepicker" type="text" id="trans_lr_date" name="trans_lr_date" placeholder="LR Date" autocomplete="off">
                          </div>

                          <div class="form-group col-md-4 trans_lr_no">
                                <label for="lr_no">LR No.</label>
                                <input type="text" class="form-control" name="trans_lr_no" placeholder="Enter LR No." id="trans_lr_no">
                          </div>

                           <div class="form-group col-md-4 trans_amount">
                                <label for="trans_amount">Transport Amount</label>
                                <input type="text" class="form-control" name="trans_amount" placeholder="Enter Transport Amount" id="trans_amount" onkeypress="return NumericValidate(event,this)">
                            </div>
                      

                    </div>
                    <br>
                </div>
                    

                    <div class="row">
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="bales">No Of Bales</label>
                                <input type="text" class="form-control" name="bales"  placeholder="Enter Bales Quantity" required="" id="noOFBales" onkeypress="return NumericValidate(event,this)">
                            </div>
                        </div>

                        <div class="col-md-3">  
                            <div class="form-group">
                                <label for="weight">Weight</label>
                                <input type="text" class="form-control" name="weight" placeholder="Enter Weight" id="weight" onkeypress="return NumericValidate(event,this)">
                            </div>
                        </div>

                        <div class="col-md-3">  
                            <div class="form-group">
                                <label for="grs_amt">Gross Amount</label>
                                <input type="text" class="form-control" name="gross_amount" placeholder="Enter Gross Amount" id="gross_amount" onkeypress="return NumericValidate(event,this)">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="txn">Tax (%)</label>
                                <input type="text" class="form-control" name="tax" id="tax"  placeholder="Enter Tax (%)" onkeypress="return NumericValidate(event,this)">
                            </div>
                        </div>
                        


                    </div>


                    <div class="row">
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="txn">Tax Amount</label>
                                <input type="text" class="form-control" name="tax_amount" id="tax_amount" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">  
                            <div class="form-group">
                                <label for="tcs">TCS (%)</label>
                               <input type="text" class="form-control" name="tcs" id="tcs" placeholder="Enter TCS (%)" onkeypress="return NumericValidate(event,this)">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="txn">TCS Amount</label>
                               <input type="text" class="form-control" name="tcs_amount" id="tcs_amount" readonly>
                            </div>
                        </div>


                         <div class="col-md-4">
                            <div class="form-group">
                                <label for="other_amt">Other Amount</label>
                               <input type="text" class="form-control" name="other_amt" id="other_amt" placeholder="Enter Other Amount" onkeypress="return NumericValidate(event,this)">
                            </div>
                        </div>
                        


                    </div>

                    <div class="form-group">
                          <label for="total_amount">Total Amount</label>
                               <input type="text" class="form-control bold" name="total_amount" id="total_amount" readonly>
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
                      <button id="submit" type="submit" name="Submit" class="btn btn-primary waves">Submit</button>
                  </div>
                </form>
              </div>
            </div>
          
        </div>
      </div>
      </div>
</div>
   
</div>
</div>


             

 <script>  
  var delCount=0;
 $(document).ready(function(){ 

// dynamic image create--------- 
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


//dynamic image create END----------



      //hide transport section on page load
      $('#transportSection').hide();


      $('#epartySelect').on('change', function() {

        var value=this.value;
        data=value.split("/");
       

        $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {record_id:data[1]},
            success: function(response)
            {

                var jsonData = JSON.parse(response);
                console.log(jsonData);

                 $('#pur_conf').val(jsonData['pur_conf']);
                 $('#candy_rate').val(jsonData['candy_rate']);
                 $('#avl_bales').val(jsonData['bales']-jsonData['used_bales']);
                 $('#broker').val(jsonData['broker_name']);
                 $('#broker_id').val(jsonData['broker_id']);


                 //transport details

                 if(jsonData['trans_pay_type']=='to_be_pay')
                 {
                    $('#transportSection').show();
                    $('.trans_lr_date').show();
                    $('.trans_amount').show();
                    $('.trans_lr_no').show();
                    
                    $('#trans_pay_type').val(jsonData['trans_pay_type']);
                    $('#trans_name').val(jsonData['transport_name']);
                    $('#trans_id').val(jsonData['trans_name']);
                 }
                 else if(jsonData['trans_pay_type']=='to_be_build')
                 {
                     $('#transportSection').show();
                     $('.trans_lr_date').hide();
                     $('.trans_amount').hide();
                     $('.trans_lr_no').hide();


                     $('#trans_pay_type').val(jsonData['trans_pay_type']);
                      $('#trans_name').val(jsonData['transport_name']);
                     $('#trans_id').val(jsonData['trans_name']);
                     
                 }
                 



           }
       });

        checkInvoiceNo()
        
      });


       $('#tax').on('input', function() {

          TaxCalculate();
          tcs_calculate();
          total_amount_calculate();


       });

       $('#gross_amount').on('input', function() {

          TaxCalculate();
          tcs_calculate();
          total_amount_calculate();


       });

       $('#invoice_no').on('focusout', function() {

          checkInvoiceNo();

       });


       function TaxCalculate()
       {
           var total_tax_amt=0.00;
          var g = parseFloat($('#gross_amount').val());
          var p = parseFloat($('#tax').val());


          total_tax_amt=g*(p/100);


          if(!Number.isNaN(total_tax_amt))
            {
                $('#tax_amount').val(total_tax_amt.toFixed(2));
            }
            else
            {
              $('#tax_amount').val(0);
            }
       }




       $('#tcs').on('input', function() {

          tcs_calculate();
          total_amount_calculate();

       });

       function tcs_calculate()
       {
           var total_tcs_amt=0.00;
          var g = parseFloat($('#gross_amount').val());
          var t = parseFloat($('#tax_amount').val());
          var p = parseFloat($('#tcs').val());
         
            total_tcs_amt=(g+t)*(p/100);

            if(!Number.isNaN(total_tcs_amt))
            {
                $('#tcs_amount').val(total_tcs_amt.toFixed(2));
            }
            else
            {
              $('#tcs_amount').val(0);
            }
       }



       //other amount calculation
       $('#other_amt').on('input', function() {

          total_amount_calculate();

       });




       function total_amount_calculate()
       {
           var total_amount=0.00;
            var g = parseFloat($('#gross_amount').val());
            var tx = parseFloat($('#tax_amount').val());
            var tcs = parseFloat($('#tcs_amount').val());

            total_amount=g+tx+tcs;


            var other_amt=$('#other_amt').val();
            if(other_amt!='')
            {
              total_amount=parseFloat(total_amount)+parseFloat(other_amt);
            }


            if(!Number.isNaN(total_amount))
            {
                
                $('#total_amount').val(total_amount.toFixed(2));
            }
            else
            {
              $('#total_amount').val(0);
            }
       }




//Gross Amount Number Validation
$('#gross_amount').keyup(function() {

    $('span.error-keyup-1').hide();
    $("#submit").attr("disabled", false);

    var inputVal = $(this).val();
    var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
    if(!numericReg.test(inputVal)) {

      $("#submit").attr("disabled", true);

        $(this).after('<span class="error error-keyup-1 text-danger">Only Numeric Values Allowed.</span>');
    }
});

// weight validation 

$('#weight').keyup(function() {

    $('span.error-keyup-10').hide();
    $("#submit").attr("disabled", false);

    var inputVal = $(this).val();
    var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
    if(!numericReg.test(inputVal)) {

      $("#submit").attr("disabled", true);

        $(this).after('<span class="error error-keyup-10 text-danger">Only Numeric Values Allowed.</span>');
    }
});
    
    //bales
$('#noOFBales').keyup(function() {


    var noOfBales=parseInt($("#noOFBales").val());
    var avlBales=parseInt($('#avl_bales').val());

    $('span.error-keyup-1').hide();
    $("#submit").attr("disabled", false);
    
    if(noOfBales=='0')
    {
      $("#noOFBales").val('');
    }
    if(noOfBales>avlBales)
    {
        $('#noOFBales').after('<span class="error error-keyup-1 text-danger">No Of Bales Should Not be greater than Available Bales...</span>'); 
        $("#submit").attr("disabled", true); 
    }
});



      //--------------------------



 
 });



        function checkInvoiceNo()
        {

          $('span.error-keyup-1').hide();
            $(':input[type="submit"]').prop('disabled', false);

            var invoice_no=$('#invoice_no').val();
            var ext_party=$('#epartySelect :selected').val();

            if(invoice_no!='' && ext_party!='')
            {
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
                      console.log(jsonData.invoice_exist);

                     if(jsonData.invoice_exist)
                     {
                       $('#invoice_no').after('<span class="error error-keyup-1 text-danger">Already Exist.</span>');
                       $(':input[type="submit"]').prop('disabled', true);
                     }
                     
                      
                 }
                });
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
 









 function netpayable_val(){
   var total = $('input[name=grs_amt]').val() - ($('input[name=ratebykg]').val()*$('input[name=sampleDifferenceamt]').val()) - ($('input[name=ratebykg]').val()*$('input[name=ntwgtDifferenceamt]').val()) - $('input[name=brokerage]').val() - $('input[name=rdrateamt]').val() - $('input[name=lengthrateamt]').val() - $('input[name=micrateamt]').val() - $('input[name=otherrateamt]').val() - $('input[name=moistureamt]').val() - $('input[name=trashrateamt]').val() - $('input[name=discountrateamt]').val() - $('input[name=othertrashrateamt]').val();
   total += parseInt($('input[name=shippnetparty]').val());
   $('#netpayable_val').html(total);
   $('input[name=netpayableamt]').val(total);
   $('button[type=submit]').removeAttr('disabled');
 }
</script>


<script type="text/javascript">

function yesnoCheck() {
    if (document.getElementById('yesCheck').checked) {
        document.getElementById('ifYes').style.display = 'block';
    }
    else document.getElementById('ifYes').style.display = 'none';

}

</script>

   
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>


    
          <script>


function OnlyNumberValidation(key) {
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
</script>

