<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['sales_conf_firm_id']) && !isset($_SESSION['sales_financial_year_id']))
{
  header('Location: ../sales_conf_index.php');
}

$getFirm=$_SESSION["sales_conf_firm"];
$getFirmID=$_SESSION["sales_conf_firm_id"];

$getYear=$_SESSION['sales_conf_financial_year'];

$year_array=explode("/",$getYear);

$shortYear='';
$getFullYear='';

foreach ($year_array as $key => $value) {
  $shortYear=$shortYear.date("y", strtotime($value));

    if($key==0)
    {
       $getFullYear=$getFullYear.date("Y", strtotime($value));
    }
    else 
    {
       $getFullYear=$getFullYear.'-'.date("Y", strtotime($value));
    }

  
}


$sql0="select party_shortform from party where id='".$_SESSION['sales_conf_firm_id']."'";

$result0 = mysqli_query($conn, $sql0);
$row0 = mysqli_fetch_assoc($result0);

$shortFirm=$row0['party_shortform'];

$sqlNextID="SELECT AUTO_INCREMENT as id FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$dbname."' AND TABLE_NAME = 'seller_conf'";

$ResultNextID = mysqli_query($conn, $sqlNextID);
$rowNextID = mysqli_fetch_assoc($ResultNextID);

$nextID = $rowNextID['id'];

$sale_conf_no=$shortFirm.'-'.'SB-'.$shortYear;  
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Create Sales Confirmation</title>
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



       <link rel="stylesheet" href="../PHPLibraries/richtexteditor/rte_theme_default.css" />
    <script type="text/javascript" src="../PHPLibraries/richtexteditor/rte.js"></script>
    <script type="text/javascript" src='../PHPLibraries/richtexteditor/plugins/all_plugins.js'></script>







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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span>Create Sales Confirmation</span></a>
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

        <!-- last change on table START-->
       <div class="last-updates">
                  <div class="firm-selectio">
             <div class="firm-selection-pre">
                <span class="pre-firm">Firm : </span><span class="pre-firm-name"><?php echo $_SESSION["sales_conf_firm"]; ?></span>
            </div>
            <div class="year-selection-pre">
            <span class="pre-year-text">Financial Year :</span> 
            <span class="pre-year">
              <?php 

              $finYearArr=explode('/',$_SESSION["sales_conf_financial_year"]);

              $start_date=date('Y', strtotime($finYearArr[0]));
               $end_date=date('Y', strtotime($finYearArr[1]));

              echo $start_date.' - '.$end_date; 

              ?>
            </span>
            </div>
          </div>
          </div>

          <!-- last change on table END-->
      <div class="container-fluid">
      

        <div class="row justify-content-center">
          
            <div class="card">
              <div class="card-header">Product Sales Confirmation Report</div>
              <div class="card-body">
                <form class="" action="add.php" method="post" enctype="multipart/form-data">


                  <div class="row">
                    <div class="col-md-4">

                        <div class="form-group">
                        <label for="conf_form">Sales Confirmation No</label>
                        <input type="text" class="form-control" name="conf_form" id="conf_form"  placeholder="Purchase Confirmation" value="<?php echo $sale_conf_no; ?>" readonly>
                        </div>
                    </div>
                    <input type="hidden" name="financial_year_id" value="<?php echo $_SESSION['sales_financial_year_id']; ?>">
                    <div class="col-md-2">
                        <div class="form-group">
                          <label for="conf_index"> Index No.</label>
                        <input type="text" class="form-control" name="conf_index" id="conf_index" placeholder="Index No." onkeypress="return NumericValidate(event)" value="<?php echo $nextID; ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group" style="margin-top: 27px">
                        <button type="button" id="conf_check" class="btn btn-success">Check</button>
                      </div>
                    </div>
                    <div class="col-md-4">

                      <span style="margin-top: 28px;">
                      <?php
                     $sqlLast="SELECT * FROM `seller_conf` WHERE firm='".$_SESSION['sales_conf_firm_id']."' AND financial_year_id='".$_SESSION['sales_financial_year_id']."' order by id DESC LIMIT 1";
                      $resultLast=mysqli_query($conn,$sqlLast);
                      if(mysqli_num_rows($resultLast)>0)
                      {
                        $rowLast=mysqli_fetch_assoc($resultLast);
                        echo '<b>Last Conf. No. </b>: '.$last_conf_no=$rowLast['sales_conf'];
                      }
                      ?>
                    </span>

                    </div>
                  </div>


                  <div class="row">

                    <div class="form-group col-md-4">
                      <label for="conf_type">Sales Confirmation Type</label>
                      <select name="conf_type" class="form-control">
                        <option value="" disabled selected>Select</option>
                        <option value="0">Original</option>
                        <option value="1">Revised</option>
                        <option value="2">Cancel</option>
                      </select>
                    </div>


                     <div class="form-group col-md-4">
                        <label for="sales_date">Select Conformation Date :</label>
                        <input type="text" class="form-control datepicker" placeholder="Confirmation Date" name="sales_date" autocomplete="off">
                        </div>

                        <div class="form-group col-md-4">
                        <label for="firm">Firm</label>
                        <input type="text" class="form-control" placeholder="Firm" value="<?php echo $_SESSION['sales_conf_firm'] ?>" readonly>
                        <input type="hidden" name="firm" value="<?php echo $_SESSION['sales_conf_firm_id'] ?>">
                        </div>

    
                  </div>

                  <div class="row">
                      <div class="form-group col-md-4">
                      <label for="external_party">External Party</label>
                      <a class="btn btn-primary btn-sm" target="_blank" href="/external-party/create.php"><i class="fa fa-user-plus"></i></a>
                      <?php
                            $sql = "select * from external_party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="external_party" data-live-search="true" class="form-control searchDropdown" onchange="get_GSTNO(this.value)">
                            <option value="" disabled selected>Select</option>
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                    echo "<option  value='".$result['id']."'>".$result['partyname']. "</option>";
                              }
                            ?>                              
                            </select>
                    </div>

                    <div class="form-group col-md-4">
                      <label for="party">GST No.:</label>
                      <input type="text" class="form-control set-gst-no" placeholder="GST No" readonly="readonly">
                  </div>


                     <div class="form-group col-md-4">
                      <label for="shipping_ext_party_id">Shipping To</label>
                     
                      <?php
                            $sql = "select * from external_party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="shipping_ext_party_id" data-live-search="true" class="form-control searchDropdown">
                            <option value="" disabled selected>Select</option>
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                    echo "<option  value='".$result['id']."'>".$result['partyname']. "</option>";
                              }
                            ?>                              
                            </select>
                    </div>



                  <div class="form-group col-md-4">
                   <label for="broker">Broker</label>
                   

                      <?php
                            $sql = "select * from broker";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="broker" data-live-search="true" class="form-control searchDropdown">
                            <option value="" disabled selected>Select</option>

                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                    echo "<option  value='".$result['id']."'>".$result['name']. "</option>";
                              }
                            ?>                              
                            </select>
                    </div>

                  <div class="form-group col-md-4">
                      <label for="trans_ins">Transit Insurance</label>
                                        
                           <select name="trans_ins" class="form-control">
                          <option value="" disabled selected>Select</option>
                          <option class="form-control" value="Us">Transit Insurance By Us</option>          
                          <option class="form-control" value="Buyer">Transit Insurance By Buyer</option>          
                            </select>
                    </div>


                  
                    <div class="form-group col-md-4">
                      <label for="product">Select Product</label>
                      <?php
                            $sql = "select * from products";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select id="product" name="product" data-live-search="true" class="form-control searchDropdown">
                            <option value="" disabled selected>Select</option>

                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                    echo "<option  value='".$result['id']."'>" .$result['prod_name']."</option>";
                              }
                            ?>                              
                            </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="dispatch_date">Select Dispatch Date :</label>
                        <input type="text" class="form-control datepicker" placeholder="Dispatch Date" name="dispatch_date" autocomplete="off">
                      </div>

                        <div class="form-group col-md-4">
                        <label for="station">Station :</label>
                        <input type="text" class="form-control" placeholder="Enter Station" name="station">
                        </div>

                         <div class="form-group col-md-4">
                        <label for="credit_days">Credit Days :</label>
                        <input type="text" class="form-control" placeholder="Enter Credit Days" name="credit_days" onkeypress="return NumericValidate(event)">
                        </div>

                    </div>

                    
                    <h4 class="spectitle">Contracted Quality Specification</h4>

                    <div class="row">                    

                  <div class="form-group col-md-4">
                      <label for="length">Product Length</label>
                      <input type="text" class="form-control" name="length"  placeholder="Product Length" >
                  </div>

                  <div class="form-group col-md-4">
                      <label for="strength">Product Strength</label>
                      <input type="text" class="form-control" name="strength" placeholder="Product Strength" >
                  </div>

                   <div class="form-group col-md-4">
                      <label for="mic">Product MIC</label>
                      <input type="text" class="form-control" name="mic"  placeholder="Product Strength" >
                  </div>

                    <div class="form-group col-md-4">
                      <label for="rd">Product RD</label>
                      <input type="text" class="form-control" name="rd"  placeholder="Product Strength" >
                  </div>


                  <div class="form-group col-md-4">
                      <label for="trash">Product Trash</label>
                      <input type="text" class="form-control" name="trash"  placeholder="Product Strength" >
                  </div>

                  <div class="form-group col-md-4">
                      <label for="moi">Product Moisture</label>
                      <input type="text" class="form-control" name="moi"  placeholder="Product Moisture" >
                  </div>


                    </div>


                  <p>Select Tax Type:</p>


                  <div class="row">
                  <div class="form-group col-md-4">
                      <input type="radio" name="taxtype" id="taxtype1" value="sgst" checked>
                      <label for="taxtype1">GST</label>
                      <br>
                      <input type="radio" name="taxtype" id="taxtype2" value="igst">
                      <label for="taxtype2">IGST</label>
                  </div>

                  <div class="form-group type_sgst col-md-4">
                      <label for="sgst">SGST</label>
                      <input type="text" class="form-control" name="sgst"  placeholder="Product sgst" value="">
                  </div>

                  <div class="form-group type_sgst col-md-4">
                      <label for="cgst">CGST</label>
                      <input type="text" class="form-control" name="cgst"  placeholder="Product cgst" value="">
                  </div>
                  <div class="form-group type_igst d-none col-md-6">
                      <label for="igst">IGST</label>
                      <input type="text" class="form-control" name="igst"  placeholder="Product igst" value="">
                  </div>

                  </div>

                  <div class="row">
                  

                  <div class="form-group col-md-4">
                      <label for="cont_quantity">Contracted Quantity (In Balse)</label>
                      <input type="text" id="cq_bales" class="form-control" name="cont_quantity" onkeyup="$('input[name=no_lot]').val(value/100);" placeholder="Contracted Quantity" value="" required="">
                      
                  </div>

                  <div class="col-md-4">
                    <a style="margin-top: 27px;" href="javascript:;" class="btn btn-primary" onclick="cont_quantityFunction()">Add Fields</a>
                  </div>

                  

                      <div class="form-group col-md-8">
                      <label for="no_lot">Number of Lot</label>
                      <input type="text" placeholder="Number Of Lot" class="form-control" name="no_lot" id="no_lot">
                    </div>

                     </div>  

                     <div class="row">
                    <?php
                    $sql="select lot_no from seller_conf where firm='".$_SESSION['sales_conf_firm_id']."' AND financial_year_id='".$_SESSION['sales_financial_year_id']."' order by id DESC LIMIT 1";
                    $result = mysqli_query($conn, $sql);
                    $row=mysqli_fetch_assoc($result);
                ?>
                    <div class="form-group col-md-5">
                      <label for="lot_no">Lot No 
                        <?php if($row!='') {
                          $lotArr=json_decode($row['lot_no']);
                         ?>
                        <strong>(Last Entry's Last LOT No. is  <?php echo end($lotArr); ?>)</strong>
                        <?php } ?>

                      </label>
                      <input type="text" placeholder="lot_no" class="form-control mb-2 lot_no" name="lot_no[]" required="">
                    </div> 


                    <div class="form-group col-md-5">
                      <label for="lot_bales">Bales</label>
                      <input type="text" placeholder="lot_bales" class="form-control mb-2 lot_bales" name="lot_bales[]">
                    </div>


                    </div> 

                   

                    <div class="row">
                        
                      <div class="form-group col-md-4">
                        <label for="press_no">Press no</label>
                        <input type="text" placeholder="Press No." class="form-control" name="press_no">
                      </div> 


                      <div class="form-group col-md-4">
                        <label for="quality">Select Product Quality</label>
                                      
                           <select id="quality" name="prod_quality" class="form-control">
                            <option value="" disabled selected>Select</option>

                                                  
                            </select>

                      </div>


                      <div class="form-group col-md-4">
                        <label for="variety">Select Product Variety</label>
                                      
                           <select id="variety" name="variety" class="form-control">
                            <option value="" disabled selected>Select</option>

                                                  
                            </select>

                      </div> 

                          </div>   


                    <div class="row">
                      <div class="form-group col-md-6">
                        <label for="sub_variety">Sub Type Of Variety</label>
                                   
                           <select id="sub_variety" name="sub_variety" class="form-control">
                            <option value="" disabled selected>Select</option>
                                                  
                            
                            </select>

                        </div> 

                            <div class="form-group col-md-6">
                          <label for="candy_rate">Cany Rate</label>
                          <input type="text" name="candy_rate" class="form-control" value="" placeholder="Enter Candy Rate"> 
                        </div>

                           
                    

                  <div class="form-group col-md-5">
                      <label for="spl_rmrk">Special Remark</label>
                      <textarea class="form-control" name="spl_rmrk" id="w3review" rows="4" cols="60"></textarea>
                  </div>

                  </div>

                  <div class="row">
                    <div class="col-md-12">
                     <label for="bill_inst">Bill Instruction</label>
                      <textarea class="form-control" name="bill_inst" id="div_editor1" rows="4" cols="60"></textarea>
                    </div>
                  </div>

                
                    
                  <br>

                  <div class="form-group">
                      <button type="submit" name="Submit" class="btn btn-primary waves sales-conf-create-frm-button">Submit</button>
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
<script>


  var editor1 = new RichTextEditor("#div_editor1");

  editor1.setHTMLCode("<p>Payment terms : 15 days for Sales of date</p><p>Weightment : Average of Our Mills One Outside Weight Bridge.</p><p>Date of Dispatch : (Expected)</p><p>Dispatch of Availability : Subject to availability upcoming of Raw cotton (Kapas)</p><p>Insurance : Transits Insurance by Buyer (i.e. Arvind or Its Groups of companies)</p><p>Claim : in case of any contracted Quality Specification issue representative must inform to us with in Seven days from sales bill date</p><p>Interest : Interest not will be issue if payment not receive in due date. (Subject to Revised if Changes in Billing party Goods conditioned is as it as)</p><p>Confirmation and Acceptance : Mill Representative - Ileshbhai</p>");

  function btngetHTMLCode() {
    alert(editor1.getHTMLCode())
  }

  function btnsetHTMLCode() {
    editor1.setHTMLCode("<h1>editor1.setHTMLCode() sample</h1><p>You clicked the setHTMLCode button at " + new Date() + "</p>")
  }
  function btngetPlainText() {
    alert(editor1.getPlainText())
  }


$(document).ready(function(){
  $('input[name=taxtype]').change(function(){
    var checked = $('input[name=taxtype]:checked').val();
    if(checked == 'sgst'){
      $('.type_sgst').each(function(){
        $(this).removeClass('d-none');
        $(this).find('input[type=text]').val('0');
      });
      $('.type_igst').addClass('d-none');
      $('.type_igst').find('input[type=text]').val('0');
    }else{
      $('.type_sgst').each(function(){
        $(this).addClass('d-none');
        $(this).find('input[type=text]').val('0');
      });
      $('.type_igst').removeClass('d-none');
      $('.type_igst').find('input[type=text]').val('0');
    }
  });

  $('input.lot_no').keyup(function(){
    var input_val = $(this).val();
    $(this).parent().find('input:not(.lot_no)').each(function() {
      input_val++;
      $(this).val(input_val);
    });

    
    var conQtyBales=$('#cq_bales').val();
    var noOfLot=Math.ceil($('#no_lot').val());

    var i=100;
    
    $('.lot_bales').parent().find('input').each(function() 
    {
         
        if(parseInt(conQtyBales)<100)
        {
          $(this).val(conQtyBales);
        }
        else
        {
          conQtyBales=parseInt(conQtyBales)-100;
          $(this).val(i);
        }
        
    });





  });



  //-------------------------------------------------------

  //Confirmation No. Check
   $('#conf_check').on('click', function() {

      $('span.error-confno').hide();

        var conf1=$('#conf_form').val();
        var conf_index=$('#conf_index').val();
        var conf_no=conf1+'-'+conf_index

        $.ajax({
            type: "POST",
            url: 'check_conf_no.php',
            data: {conf_no:conf_no},
            success: function(response)
            {

                var jsonData = JSON.parse(response);
                console.log(jsonData);

                if(jsonData.status==false)
                {
                  $('#conf_index').after('<span class="error error-confno text-success"><b>Available..</b></span>');
                  $('.sales-conf-create-frm-button').removeAttr('disabled');
                }
                else
                {
                 $('#conf_index').after('<span class="error error-confno text-danger"><b>Not Available..</b></span>');
                  $('.sales-conf-create-frm-button').attr('disabled','disabled');
                }

           }
       });
        
      });





   //Get Product Variety
    $('#product').on('change', function() {

        var value=this.value;
       

        $.ajax({
            type: "POST",
            url: 'getVariety.php',
            data: {prod_id:value},
            success: function(response)
            {

                var jsonData = JSON.parse(response);
                console.log(jsonData);


                //set prodcut quality in dropdown
                $('#quality').find('option').not(':first').remove();
                var pquality=jsonData['prod_quality'];

                 $.each(pquality, function (key, val) 
                 {
                      $('<option/>').val(pquality[key].id).html(pquality[key].value).appendTo('#quality');
                 });


                //set variety in dropdown
                $('#variety').find('option').not(':first').remove();
                var main_var=jsonData['main_variety'];

                 $.each(main_var, function (key, val) 
                 {
                      $('<option/>').val(main_var[key].id).html(main_var[key].value).appendTo('#variety');
                 });

                
    
                //set Sub variety in dropdown
                var sub_variety=jsonData['prod_sub_variety'];
                $('#sub_variety').find('option').not(':first').remove();

                $.each(sub_variety, function (key, val) 
                 {
                      $('<option/>').val(sub_variety[key].id).html(sub_variety[key].value).appendTo('#sub_variety');
                 });


              

           }
       });
        
      });








  //-------------------------------------------------------





});
function cont_quantityFunction(value) {
  var addFileds = $('input[name=cont_quantity]').val()/100;

  $('.lot_no').parent().find('input:not(.lot_no)').each(function() 
  {
    $(this).remove();
  });

   $('.lot_bales').parent().find('input:not(.lot_bales)').each(function() 
  {
    $(this).remove();
  });


  for (let i = 1; i < addFileds; i++) {
    $('<input type="text" placeholder="lot_no" class="form-control mb-2" name="lot_no[]">').insertAfter('.lot_no');

    $('<input type="text" placeholder="lot_bales" class="form-control mb-2" name="lot_bales[]">').insertAfter('.lot_bales');


  }
}
function decimalValidate(evt, element) {

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
</script>