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





?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Create Sales Confirmation Split</title>
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create Sales Confirmation Split</span></a>
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

      <div class="container-fluid">
        <div class="row justify-content-center">
         
            <div class="card">
              <div class="card-header">Sales Confirmation Split Report</div>
              <div class="card-body">
                <form class="" action="add.php" method="post" enctype="multipart/form-data">
                  <div class="row">
                    <div class="col-md-4">

                        <div class="form-group">
                        <label for="conf_no">Sales Confirmation No</label>
                          <?php
                            $getDates=explode('/', $_SESSION["sales_conf_financial_year"]);
                            $start_date=$getDates[0];
                           $end_date=$getDates[1];

                          $firm_id=$_SESSION["sales_conf_firm_id"];
                          $fyear_id=$_SESSION["sales_financial_year_id"];

                            $sql = "select s.*,p.party_name from seller_conf s, party p where s.firm=p.id AND s.financial_year_id='".$fyear_id."'  AND s.firm='".$firm_id."' AND s.conf_type!='2'"; 
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="conf_no" id="conf_no" class="form-control">
                            <option value="" disabled selected>Select</option>
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                 //External Party
                                $ext_name='';
                                $Ex_party = "select * from external_party where id='".$result['external_party']."'";
                                $Ex_partyresult = mysqli_query($conn, $Ex_party);
                                if(mysqli_num_rows($Ex_partyresult)>0)
                                {
                                  $Ex_partyrow = mysqli_fetch_assoc($Ex_partyresult);
                                  $ext_name=$Ex_partyrow['partyname'];
                                }


                                $used_bales=0;
                                
                                //check in sales conf split
                                $sql2="SELECT IFNULL(SUM(no_of_bales), 0) as used_bales FROM sales_conf_split WHERE conf_no='".$result['sales_conf']."'";
                                $result2 = mysqli_query($conn, $sql2);
                                $rowScs2=$result2->fetch_assoc();
                                $used_bales+=(int)$rowScs2['used_bales'];

                                //check in sales report
                                $sqlSR="select IFNULL(SUM(noOFBales), 0) as used_bales from sales_report where conf_no='".$result['sales_conf']."'";
                                $resultSR = mysqli_query($conn, $sqlSR);
                                $rowSR=$resultSR->fetch_assoc();
                                $used_bales+=(int)$rowSR['used_bales'];

                             

                                if($result['cont_quantity']!=$used_bales)
                                {
                                  echo "<option   value='".$result['id'].'/'.$result['sales_conf']."'>".$result['sales_conf'].' ('.$ext_name.')'."</option>";
                                }
                                
                              }
                            ?>                              
                            </select>
                        
                        </div>
                    </div>
                    <div class="col-md-4">

                        <div class="form-group">
                        <label for="conf_split_no">Sales Split Confirmation No</label>

                        <input type="text" class="form-control" name="conf_split_no" id="conf_split_no"   value="" readonly="" >
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                      <label for="conf_type">Sales Confirmation  Type</label>
                      <select name="conf_type" class="form-control">
                        <option value="" disabled selected>Select</option>
                        <option value="0">Original</option>
                        <option value="1" selected="">Revised</option>
                        <option value="2">Cancel</option>
                      </select>
                    </div>
                    
                    
                    
                  </div>


                  <div class="row">

                    <div class="form-group col-md-4">
                      <label for="split_party_name">Split Party Name</label>
                        <a class="btn btn-primary btn-sm" target="_blank" href="/external-party/create.php"><i class="fa fa-user-plus"></i></a>


                      <?php
                            $sql = "select * from external_party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="split_party_name" data-live-search="true" class="form-control searchDropdown" required>
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
                      <label for="shipping_ext_party_id">Shipping To</label>
                     
                      <?php
                            $sql = "select * from external_party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select id="shipping_ext_party_id" name="shipping_ext_party_id" data-live-search="true" class="form-control searchDropdown">
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
                        <label for="conf_split_date">Sales Confirmation Split Date :</label>
                        <input type="text" class="form-control datepicker" placeholder="Sales Confirmation Split Date" name="conf_split_date" autocomplete="off" required="">
                        </div>

                        <div class="form-group col-md-4">
                        <label for="firm">Firm</label>
                        <input type="text" class="form-control" placeholder="Firm" value="<?php echo $_SESSION['sales_conf_firm'] ?>" readonly>
                        <input type="hidden" name="firm" value="<?php echo $_SESSION['sales_conf_firm_id'] ?>">
                        </div>

    
                 
                      <div class="form-group col-md-4">
                      <label for="external_party">External Party</label>
                      <input type="text" class="form-control" n id="external_party" 
                        placeholder="External Party" readonly="">
                        <input type="hidden" name="external_party" id="external_party_id">
                    </div>

                  <div class="form-group col-md-4">
                   <label for="broker">Broker</label>
                      <input type="text"  class="form-control" id="broker" placeholder="Broker" readonly="">
                      <input type="hidden" name="broker"  class="form-control" id="broker_id" placeholder="Broker" >
                    </div>

                  <div class="form-group col-md-4">
                      <label for="trans_ins">Transit Insurance</label>                                        
                       
                          <input type="text" name="trans_ins" id="trans_ins" class="form-control" placeholder="Transit Insurance" readonly="">
                    </div>


                    <div class="form-group col-md-4">
                      <label for="product">Product</label>
                     

                      <input type="text"  id="product" class="form-control" placeholder="Product" readonly="">
                      <input type="hidden" name="product" id="product_id" class="form-control" placeholder="Product">

                      <input type="hidden" name="financial_year_id" value="<?php echo $_SESSION['sales_financial_year_id'] ?>">


                    </div>
                    </div>

                    
            
             

                    <h4>Quality Specification</h4>

                    <div class="row">                    

                  <div class="form-group col-md-4">
                      <label for="length">Product Length</label>
                      <input type="text" class="form-control" name="length" id="product_length"  placeholder="Product Length" onkeypress="return NumericValidate(event)">
                  </div>

                  <div class="form-group col-md-4">
                      <label for="strength">Product Strength</label>
                      <input type="text" class="form-control" name="strength" id="strength" placeholder="Product Strength" onkeypress="return NumericValidate(event)">
                  </div>

                   <div class="form-group col-md-4">
                      <label for="mic">Product MIC</label>
                      <input type="text" class="form-control" id="mic" name="mic"  placeholder="Product MIC" onkeypress="return NumericValidate(event)">
                  </div>

                    <div class="form-group col-md-4">
                      <label for="rd">Product RD</label>
                      <input type="text" class="form-control" name="rd"  placeholder="Product RD" id="rd" onkeypress="return NumericValidate(event)">
                  </div>


                  <div class="form-group col-md-4">
                      <label for="trash">Product Trash</label>
                      <input type="text" class="form-control" name="trash"  placeholder="Product Trash" id="trash" onkeypress="return NumericValidate(event)">
                  </div>

                  <div class="form-group col-md-4">
                      <label for="moi">Product Moisture</label>
                      <input type="text" class="form-control" name="moi"  placeholder="Product moi" id="moi" onkeypress="return NumericValidate(event)">
                  </div>

                    </div>


                  <p>Select Tax Type:</p>


                  <div class="row">
                  <div class="form-group col-md-4">
                      <input type="radio" name="taxtype" id="taxtype1" value="sgst" checked>
                      <label for="taxtype1">SGST</label>
                      <br>
                      <input type="radio" name="taxtype" id="taxtype2" value="igst">
                      <label for="taxtype2">IGST</label>
                  </div>

                  <div class="form-group type_sgst col-md-4">
                      <label for="sgst">SGST</label>
                      <input type="text" class="form-control" name="sgst"  id="sgst" placeholder="Product sgst" value="">
                  </div>

                  <div class="form-group type_sgst col-md-4">
                      <label for="cgst">CGST</label>
                      <input type="text" id="cgst" class="form-control" name="cgst"  placeholder="Product cgst" value="">
                  </div>
                  <div class="form-group type_igst my_igst d-none col-md-6">
                      <label for="igst">IGST</label>
                      <input type="text" id="igst" class="form-control" name="igst"  placeholder="Product igst" value="">
                  </div>

                  </div>

                  <div class="row">
                  

                   <div class="col-md-12">
                    <h4>Lot Details</h4>
                  </div>

                  <div class="form-group col-md-4">
                      <label for="no_of_bales">No Of Bales</label>
                      <input type="text" class="form-control" name="no_of_bales" id="noOFBales" placeholder="Contracted Quantity" value="" required="">
                      
                  </div>

                  <div class="form-group col-md-4">
                      <label for="avl_bales">Available Bales</label>
                      <input type="text" class="form-control" name="avl_bales" id="avl_bales" placeholder="Available Balse" readonly="">
                      
                  </div>

                

                    </div>


                <div class="row">

                   <div class="col-md-4">
                      <div class="form-group">
                      <label for="lot_select">Select LOT No.</label>                                   
                        <select id="lot_select" class="form-control">
                          <option value="" disabled="" selected="">Select Option</option>                            
                        </select>
                      </div>
                    </div>
                     <div class="col-md-4 " >
                            <button type="button" style="margin-top: 32px;" class="btn btn-primary add_lot_button" disabled="">Add</button>
                      </div>

                  </div>

                  <div class="dynamicLotSection">
                    
                  </div>
                  <br><br>
  
                    

                    <div class="row">
                        
                      <div class="form-group col-md-6">
                        <label for="press_no">Press no</label>
                        <input type="text" placeholder="Press No." class="form-control" name="press_no">
                      </div> 


                      <div class="form-group col-md-6">
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
                          <label for="price">Price</label>
                          <input type="text" id="price" name="price" class="form-control" value="" placeholder="enter price"> 
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

                  

               
                  
                  <input type="hidden" id="credit_days" name="credit_days" value=""> 

                  <input type="hidden" id="station" name="station" value=""> 
                  <input type="hidden" id="dispatch_date" name="dispatch_date" value=""> 
                  <input type="hidden" id="prod_quality" name="prod_quality" value=""> 


                    
                  <br>

                  <div class="form-group">
                      <button id="submit" type="submit" name="submit" class="btn btn-primary waves">Submit</button>
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
  var check_tax = $('input[name=taxtype]:checked').val();
  if(check_tax == 'sgst'){
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

 


  //-------------------------------------------------------

  //Confirmation No. Check
  $('#conf_no_avl_msg').hide();
  $('#conf_no_not_msg').hide();
   $('#conf_check').on('click', function() {

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
                  $('#conf_no_avl_msg').show();
                  $('#conf_no_not_msg').hide();
                }
                else
                {
                  $('#conf_no_avl_msg').hide();
                  $('#conf_no_not_msg').show();
                }

           }
       });
        
      });

    $('#conf_no').on('change', function() {

       $('#variety').find('option').not(':first').remove();
      $('#sub_variety').find('option').not(':first').remove();

        var value=this.value;
        
        $.ajax({
            type: "POST",
            url: 'check.php',
            data: {conf_no:value},
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                // console.log(jsonData);         
                $('#conf_split_no').val(jsonData['conf_split_no']);
            }
        });
        
      });


    $('#conf_no').on('change', function() {


        getLotNoList();

        var value=this.value;

        $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {conf_no:value},
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                console.log(jsonData);
                // alert(jsonData)
                $('#avl_bales').val(jsonData['cont_quantity']);
                $('#external_party').val(jsonData['ext_name']);
                $('#external_party_id').val(jsonData['ext_id']);
                $('#product_length').val(jsonData['length']);
                $('#strength').val(jsonData['strength']);
                $('#mic').val(jsonData['mic']);
                $('#rd').val(jsonData['rd']);
                $('#trash').val(jsonData['trash']);
                $('#broker').val(jsonData['broker_name']);
                $('#broker_id').val(jsonData['broker_id']);
                $('#trans_ins').val(jsonData['trans_ins']);
                $('#product').val(jsonData['product_name']);
                $('#product_id').val(jsonData['product_id']);
                $('#avl_bales').val(jsonData['cont_quantity']-jsonData['used_bales']);

                $('#cgst').val(jsonData['cgst']);
                $('#sgst').val(jsonData['sgst']);
                $('#igst').val(jsonData['igst']);

                $('#price').val(jsonData['candy_rate']);


                $('#shipping_ext_party_id').val(jsonData['shipping_ext_party_id']);


                $('#moi').val(jsonData['moi']);
                $('#credit_days').val(jsonData['credit_days']);

                $('#station').val(jsonData['station']);
                $('#dispatch_date').val(jsonData['dispatch_date']);
                $('#prod_quality').val(jsonData['prod_quality']);







                var taxtype1 = jsonData['tax_type'];
                if(taxtype1 == 'sgst'){
                $("#taxtype1").prop("checked", true);
                $('.type_sgst').each(function(){
                  $(this).removeClass('d-none');
                  $(this).find('input[type=text]').val();
                });
                $('.type_igst').addClass('d-none');
                $('.type_igst').find('input[type=text]').val('0');
              }else{
                 $("#taxtype2").prop("checked", true);
                $('.type_sgst').each(function(){
                  $(this).addClass('d-none');
                  $(this).find('input[type=text]').val('0');
                });
                $('.type_igst').removeClass('d-none');
                $('#igst').find('input[type=text]').val(jsonData['igst']);
              }


              //set variety in dropdown

               var selected_variety=jsonData['variety'];

              var variety=jsonData['main_variety'];

              $.each(variety, function (key, val) 
               {
                    if(variety[key].id==selected_variety)
                    {
                        $('<option/>').val(variety[key].id).prop("selected", "selected").html(variety[key].value).appendTo('#variety');
                    } 
                    else
                    {
                        $('<option/>').val(variety[key].id).html(variety[key].value).appendTo('#variety');
                    }

                    
               });

               

                //set Sub variety in dropdown
                var sub_variety=jsonData['main_prod_sub_variety'];

                 var selected_sub_variety=jsonData['sub_variety'];


                $('#sub_variety').find('option').not(':first').remove();
                $.each(sub_variety, function (key, val) 
                 {
                      if(sub_variety[key].id==selected_sub_variety)
                      {
                         $('<option/>').val(sub_variety[key].id).prop("selected", "selected").html(sub_variety[key].value).appendTo('#sub_variety');
                      }
                      else
                      {
                        $('<option/>').val(sub_variety[key].id).html(sub_variety[key].value).appendTo('#sub_variety');
                      }
                     
                 });

                
                
                
           }
        });
        
    });



    $("form").submit(function (e) {
             var c=0;
             var noOFBales = $('#noOFBales').val();
            $( ".lot_bales" ).each(function(index) {
              c=parseInt(c)+parseInt(this.value);
            });
            if (parseInt(noOFBales)!= parseInt(c)) {

              e.preventDefault();
              alert('Selected Lot Bales And Entered No. Of Bales Not Match')
              return false;

            }
          }); 




    // balse check
    $('#noOFBales').keyup(function() {


    var noOfBales=parseInt($("#noOFBales").val());
    // alert(noOfBales);
    var avlBales=parseInt($('#avl_bales').val());
    // alert(avlBales);
    $('span.error-keyup-1').hide();
    $("#submit").attr("disabled", false);
    $("#btnAddField").attr("disabled", false); 
    
    if(noOfBales=='0')
    {
      $("#noOFBales").val('');
    }
    if(noOfBales>avlBales)
    {
        $('#noOFBales').after('<span class="error error-keyup-1 text-danger">No Of Bales Should Not be greater than Available Bales...</span>'); 
        $("#submit").attr("disabled", true); 
        $("#btnAddField").attr("disabled", true); 
        
    }

    checkBales();


});

  //-------------------------------------------------------

        //lot_no no change
        $('#lot_select').on('change', function() {

          //enable add lot button
          $('.add_lot_button').attr("disabled",false);
        
        });


        var isFirstRow=true;

        //dynamic lot create
        $('.add_lot_button').click(function()
        {
              //check bales is entered or not
              var bales = $('#noOFBales').val();
              if(bales=='')
              {
                alert('Please Enter Bales...')
                return false
              }


              //check bales count if value is greter than or equal then dont allow to add new lot
              var lotBalesCount=0;
              var enterBales=$('#noOFBales').val();
              if(enterBales=='')
              {
                enterBales=0;
              }

             $(".lot_bales").each(function( index ) 
             {
                lotBalesCount+=parseInt(this.value);
             });


             if(parseInt(lotBalesCount)>=enterBales)
             {
               return false;
             }

            var selectedLotNo=$('#lot_select :selected').val();


          //find object of selected lot no
          var result=MainLotBalesArr[selectedLotNo];
         
            if(selectedLotNo!='')
            {
                  if(isFirstRow==true)
                  {
                  $('.dynamicLotSection').append('<div class="row"><div class="form-group col-md-3"><label for="lot_no">Lot No</label><input type="text" placeholder="Lot No" class="form-control mb-2 lot_no" name="lot_no[]" readonly value='+selectedLotNo+'></div><div class="form-group col-md-3"><label for="lot_bales">Lot Bales</label><input type="text" placeholder="Lot Bales" class="form-control lot_bales"  name="lot_bales[]" value="'+result+'" onkeyup="lotBalesChange(this)"></div><div class="col-md-1"><a href="javascript:void(0);" style="margin-top:30px;" class="btn btn-danger remove_lot_btn" onclick="removeLot(this,'+selectedLotNo+')">-</a></div></div>');
                }
                else
                {
                     $('.dynamicLotSection').append('<div class="row"><div class="form-group col-md-3"><input type="text" placeholder="Lot No" class="form-control mb-2 lot_no" name="lot_no[]" readonly value='+selectedLotNo+'></div><div class="form-group col-md-3"><input type="text" placeholder="Lot Bales" class="form-control lot_bales"  name="lot_bales[]" value="'+result+'" onkeyup="lotBalesChange(this)"></div><div class="col-md-1"><a href="javascript:void(0);" class="btn btn-danger remove_lot_btn" onclick="removeLot(this,'+selectedLotNo+')">-</a></div></div>');
                }

                $("#lot_select option[value="+selectedLotNo+"]").remove();
                $('#lot_select').prop('selectedIndex',0);
                isFirstRow=false;
               
                checkBales();
                
           
            }
 

        });





});



  var MainLotBalesArr=new Array();


function getLotNoList() {

        var conf_no = $("#conf_no").val();
        var noBales = $('#noOFBales').val();

        $.ajax({
            type: "POST",
            url: 'lotGet.php',
            data: {
              conf_no:conf_no,
            },
            success: function(response)
            {
                
                var jsonData = JSON.parse(response);
                console.log(jsonData);


                var Arr=new Array();

                $.each(jsonData.lot_no, function(i, item) {
                    
                    Arr[item]=jsonData.lot_bales[i];

                });

                MainLotBalesArr=Arr;

                $('#lot_select').find('option').not(':first').remove();
                $.each(jsonData.lot_no,function(index,obj)
                {
                 var option_data="<option value="+obj+">"+obj+"</option>";
                  $(option_data).appendTo('#lot_select'); 
                });

               
              
            }
        });

}

function removeLot(e,lot_no)
{


      var lot_no=lot_no


       var result=MainLotBalesArr[lot_no];

    

       $("#lot_select").append(new Option(lot_no, lot_no));
       $("#lot_select option").sort(function(a, b) {
                  a = a.value;
                  b = b.value;
                  return a-b;
              }).appendTo('#lot_select');

        $('#lot_select').prop('selectedIndex',0);
        $(e).parent('div').parent('div').remove(); 
       
        checkBales();
    }


    function lotBalesChange(e,lotno)
    {

       var lot_no = $(e).parent().parent().find('.lot_no').val();

       $('span.error-keyup-11').hide();
       $("#submit").attr("disabled", false);


         var result=MainLotBalesArr[lot_no];
           

       if(parseInt(e.value)>parseInt(result))
        {
             $(e).after('<span class="error error-keyup-11 text-danger">Sorry ! Available Bales is '+result+'.</span>');
            $("#submit").attr("disabled", true);
          
        }

        checkBales();

    }




  function checkBales() {
      $('span.error-keyup-10').hide();

      var noBales = $('#noOFBales').val();
      var countBales = 0;
      $( ".lot_bales" ).each(function( index ) {
        countBales = parseInt(countBales) + parseInt(this.value);

      });
      if (parseInt(noBales) != parseInt(countBales)) {
        $('.lot_bales').last().after('<span class="error error-keyup-10 text-danger">No Of Bales Is not eual to selected bales.</span>'); 
        $("#submit").attr("disabled", true);
      }else{

        $('span.error-keyup-10').hide();
        $("#submit").attr("disabled", false);
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

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

         
  </body>
</html>
