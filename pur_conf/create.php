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

$getFirm=$_SESSION["pur_firm"];
$getFirmID=$_SESSION["pur_firm_id"];

$getYear=$_SESSION['pur_financial_year'];

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


$sql0="select party_shortform from party where id='".$_SESSION['pur_firm_id']."'";

$result0 = mysqli_query($conn, $sql0);
$row0 = mysqli_fetch_assoc($result0);

$shortFirm=$row0['party_shortform'];



$sqlNextID="SELECT AUTO_INCREMENT as id FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$dbname."' AND TABLE_NAME = 'pur_conf'";

$ResultNextID = mysqli_query($conn, $sqlNextID);
$rowNextID = mysqli_fetch_assoc($ResultNextID);

$nextID = $rowNextID['id'];

$conf_no=$shortFirm.'-'.$shortYear;






?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Create New Product Confirmation Record</title>
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
            
              dateFormat: "dd/mm/yy",
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New Product Confirmation</span></a>
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
              <div class="card-header">Product Confirmation Report</div>
              <div class="card-body">
                <form class="" action="add.php" method="post" enctype="multipart/form-data">


                  <div class="row">
                    <div class="col-md-4">

                        <div class="form-group">
                        <label for="pur_conf">Purchase Confirmation No</label>
                        <input type="text" class="form-control" name="pur_conf" id="pur_conf"  placeholder="Purchase Confirmation" value="<?php echo $conf_no; ?>" readonly>
                        </div>
                    </div>
                   
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
                      $sqlLast="select * from pur_conf where firm='".$_SESSION['pur_firm_id']."' AND financial_year='".$_SESSION['pur_financial_year_id']."' order by id DESC LIMIT 1";
                      $resultLast=mysqli_query($conn,$sqlLast);
                      if(mysqli_num_rows($resultLast)>0)
                      {
                        $rowLast=mysqli_fetch_assoc($resultLast);
                        echo '<b>Last Conf. No. </b>: '.$last_conf_no=$rowLast['pur_conf'];
                      }
                      ?>
                    </span>

                    </div>
                  </div>



                  <div class="row">
                  

               <!--    <div class="form-group col-md-4">
                    <label for="pur_conf">Purchase Confirmation No</label>
                    <input type="text" class="form-control" name="pur_conf"  placeholder="Enter Purchase Confirmation" value="<?php echo $conf_no; ?>" readonly>
                  </div> -->








                  <div class="form-group col-md-4">
                    <label for="conf_type">Confirmation Type</label>                                       
                    <select name="conf_type" class="form-control">
                      <option value="0">Original</option>
                      <option value="1">Revised</option>
                      <option value="2">Cancel</option>
                    </select>
                  </div>


                   <div class="form-group col-md-4">
                    <label>Firm</label>
                    <input type="text" class="form-control" name="pur_firm"  value="<?php echo $getFirm; ?>" readonly>
                      <input type="hidden" name="firm_id" value="<?php echo $getFirmID; ?>">
                  </div>


                  <div class="form-group col-md-4">
                    <label for="pur_conf">Financial Year</label>
                    <input type="text" class="form-control" value="<?php echo $getFullYear; ?>" readonly>
                    <input type="hidden" name="financial_year" value="<?php echo $_SESSION['pur_financial_year_id']; ?>">
                  </div>


                  <div class="form-group col-md-4">
                    <label for="pur_date">Select Report Date :</label>
                    <input class="form-control datepicker" type="text"  name="pur_date" autocomplete="off" placeholder="Report Date" required="">
                  </div>

                 

                  <div class="form-group col-md-4">
                    <label for="party">Select External Party</label>

                    <a class="btn btn-primary btn-sm" target="_blank" href="/external-party/create.php"><i class="fa fa-user-plus"></i></a>

                    <?php
                      $sql = "select * from external_party";
                      $result = mysqli_query($conn, $sql);
                    ?>                       
                    <select data-live-search="true" class="form-control searchDropdown" name="party" onchange="get_GSTNO(this.value)">
                      <?php                   
                        foreach ($conn->query($sql) as $result) 
                        {
                          echo "<option  value='".$result['id']."'>" .$result['partyname']. "</option>";
                        }
                      ?>    

                    </select>
                  </div>
                  
                  <div class="form-group col-md-4">
                    <label for="party">GST No.:</label>
                    <input type="text" class="form-control set-gst-no" placeholder="GST No" readonly="readonly">
                  </div>




                  <div class="form-group col-md-4">
                    <label for="broker">Select Broker</label>
                    <a class="btn btn-primary btn-sm" target="_blank" href="/broker/create.php"><i class="fa fa-user-plus"></i></a>

                    <?php
                      $sql = "select * from broker";
                      $result = mysqli_query($conn, $sql);                            
                    ?>                      
                    <select data-live-search="true" name="broker" class="form-control searchDropdown">
                      <?php                   
                        foreach ($conn->query($sql) as $result) 
                        {
                          echo "<option  value='".$result['id']."'>" .$result['name']. "</option>";
                        }
                      ?>                              
                    </select>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="product_name">Select Product</label>


                    <?php
                      $Productquery = "select * from products";
                      $productResult = mysqli_query($conn, $Productquery);
                    ?>                      
                    <select name="product_name" data-live-search="true" class="form-control searchDropdown">
                      <?php                   
                        foreach ($conn->query($Productquery) as $productResult) 
                        {

                          $product = "select * from products where prod_name='Cotton Bales 5%'";
                          $resultProduct = mysqli_query($conn, $product);
                          $rowProduct=mysqli_fetch_assoc($resultProduct);
                          $isTselected='';

                          if($productResult['id']==$rowProduct['id'])
                          {
                            $isTselected='selected';
                          }


                          
                          echo "<option  value='".$productResult['id']."'".$isTselected.">" .$productResult['prod_name']. "</option>";
                        }
                      ?>                              
                    </select>
                  </div>

                  <div class="form-group col-md-4">
                      <label for="pro_length">Product Length</label>
                      <input type="text" class="form-control" name="pro_length"  placeholder="Product Length" value="<?php if(isset($row)){ echo  $row['pro_mic'];}?>" onkeypress="return NumericValidate(event,this)">
                  </div>

                  <div class="form-group col-md-4">
                      <label for="pro_mic">Product MIC</label>
                      <input type="text" class="form-control" name="pro_mic"  placeholder="Product MIC" value="<?php if(isset($row)){ echo  $row['pro_mic'];}?>" onkeypress="return NumericValidate(event,this)">
                  </div>
                  <div class="form-group col-md-4">
                      <label for="pro_rd">Product RD</label>
                      <input type="text" class="form-control" name="pro_rd"  placeholder="Product RD" value="<?php if(isset($row)){ echo  $row['pro_mic'];}?>" onkeypress="return NumericValidate(event,this)">
                  </div>
                  <div class="form-group col-md-4">
                      <label for="pro_trash">Product Trash</label>
                      <input type="text" class="form-control" name="pro_trash"  placeholder="Product Trash" value="<?php if(isset($row)){ echo  $row['pro_mic'];}?>" onkeypress="return NumericValidate(event,this)">
                  </div>
                  <div class="form-group col-md-4">
                      <label for="pro_mois">Product Moisture</label>
                      <input type="text" class="form-control" name="pro_mois"  placeholder="Product Moisture" value="<?php if(isset($row)){ echo  $row['pro_mic'];}?>" onkeypress="return NumericValidate(event,this)">
                  </div>
                  <div class="form-group col-md-4">                      
                    <label for="bales">No. Of Bales</label>
                    <input type="text" class="form-control" placeholder="No. Of Bales" onkeypress="return NumericValidate(event,this)"  name="bales">
                  </div>
                  <div class="form-group col-md-4">                      
                    <label for="candy_rate">Candy Rate</label>
                    <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control" placeholder="Candy Rate"  name="candy_rate">
                  </div>

                  <div class="form-group col-md-4">                  
                    <label for="dispatch">Dispatch</label>
                    <!-- <input type="text" class="form-control" placeholder="Enter dispatch" onkeypress="return lettersValidate(event)"  name="dispatch" id="dispatch"> -->

                    <select name="dispatch" class="form-control">
                      <option value="" selected="" >Select</option>
                      <option value="FOR" >FOR</option>
                      <option value="Regular" >Regular</option>                          
                    </select>

                  </div>




                  

                  <div class="form-group col-md-4">
                    <div class="form-group">
                      <label for="delivery_date">Delivery Date</label>
                       <input type="text" placeholder="Delivery Date" class="form-control datepicker"  name="delivery_date" autocomplete="off" required="">
                      </div>
                  </div>

                  <div class="form-group col-md-4">
                    <div class="form-group">
                      <label for="station">Station</label>
                       <input type="text" placeholder="Enter Station" class="form-control "  name="station" autocomplete="off">
                      </div>
                  </div>


                  </div>


                  
                  <h4>Transport Details</h4>
                  <br>
                  <div class="row">
                    
                  <div class="form-group col-md-4">
                    <label for="transport_name">Select Transnport Name</label>

                    <a class="btn btn-primary btn-sm" target="_blank" href="/transport/create.php"><i class="fa fa-user-plus"></i></a>

                    <?php
                      $sql = "select * from transport";
                      $result = mysqli_query($conn, $sql);
                    ?>                      
                    <select data-live-search="true" name="transport_name" class="form-control searchDropdown">
                      <?php                   
                        foreach ($conn->query($sql) as $result) 
                        {
                          echo "<option  value='".$result['id']."'>" .$result['trans_name']. "</option>";
                        }
                      ?>                              
                    </select>
                  </div>


                
                   <div class="form-group col-md-4">
                    <label for="trans_pay_type">Select Payment Type</label>

                                     
                    <select name="trans_pay_type" class="form-control">
                       <option value="to_be_build">To Be Build</option>
                          <option value="to_be_pay">To Be Pay</option>

                    </select>
                  </div>
 
                  </div>

                   <br>


                  <div class="row">
                          <div class="col-md-4">
                              <div class="form-group">  
                                <label for="dispatch">No. Of Vehicle</label>
                                <input type="text" class="form-control" placeholder="Enter No. Of Vehicle"  name="no_of_vehicle" id="no_of_vehicle" onkeypress="return OnlyNumberValidation(event)">
                              </div>
                          </div>
                           <div class="col-md-3">
                            <div style="margin-top: 30px" class="form-group">
                              <label></label>
                             <button type="button" id="btn_add_veh" class="btn btn-success">Add</button>
                           </div>
                           </div>                         
                  </div> 

                  <div id="" class="row">
                    <div class="col-md-1"></div>
                    <div id="veh_col" class="col-md-5">
                    </div>
                  </div>

                  <br>
                  <h4>Insurance</h4>
                  <div class="row">
                    
                      <div class="form-group col-md-6">  
                    <label for="ins_cmp_name">Company Name</label>
                    <input type="text" class="form-control" placeholder="Enter Insurance Company Name"  name="ins_cmp_name">
                  </div>

                  <div class="form-group col-md-6">  
                    <label for="ins_policy_no">Insaurance Policy No.</label>
                    <input type="text" class="form-control" placeholder="Enter Insaurance Policy No."  name="ins_policy_no">
                  </div>
                      
                    
                  </div>

                 

                  <div class="row">
                   
                    <div class="form-group col-md-6">  
                        <label for="pay_term">Payment Terms </label>
                        <input type="text" class="form-control" placeholder="Enter Payment Terms"  name="pay_term">
                    </div>

                   

                    <div class="form-group col-md-6">
                    <label for="party">Laboratory Master</label>
                    <?php
                      $sql = "select * from laboratory_master";
                      $result = mysqli_query($conn, $sql);
                    ?>                      
                    <select name="laboratory_master" class="form-control">
                      <?php                   
                        foreach ($conn->query($sql) as $result) 
                        {
                          echo "<option  value='".$result['id']."'>" .$result['lab_name']. "</option>";
                        }
                      ?>                              
                    </select>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="spl_rmrk">Special Remark</label>
                    <textarea class="form-control" name="spl_rmrk" id="w3review" rows="4" cols="60" placeholder="Special Remark"></textarea>
                  </div>



                  </div>
                  <div class="row">
                    <div class="col-md-12">
                     <label for="bill_inst">Terms & Condition</label>
                      <textarea class="form-control" name="term_condtion" id="div_editor1" rows="4" cols="60"></textarea>
                    </div>
                  </div> 
                  <br>
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

<script src="https://cdn.ckeditor.com/[version.number]/[distribution]/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/4.16.1/standard/ckeditor.js"></script>

<script>
$(document).ready(function () {


  var editor1 = new RichTextEditor("#div_editor1");

  editor1.setHTMLCode('<p>Payment terms : 15 days</p><p>Quality Checking Report Laboratory : White Gold Testing Lab - Ahmadabad. Re-Test will be done only if necessary circumstances arise. Re-test will be done only one time and the Expenses of Re-test will born by Seller Party</p><p>No required to add TCS Under Section 206(1H) in Invoice. GST will be release only after the party Filled the GSTR-1 &amp; GSTR-3B and after the Credit seen in the GSTR-2B.</p><p>If quality is not as per above parameter than Ginner/Traders will bear loading /Unloading &amp; transportation both side &amp; replace with new bales or any other decision as decide only by us(i.e. Buyer). Any Dispute or difference whatsoever arising between the parties out of this contract shall be settled by arbitration in accordance with the Statutory By-Laws and Rules of Arbitration of cotton Association of India.</p><p>The award made in pursuance there of shall be binding on the parties. In case goods are rejected and returned, "Transit risk will be the responsibility of ginner/Traders(Seller Parties), has to take the insurance policy to cover the transits risk from our factory of return/rejected goods".</p><p>If case purchase return, a proper printed purchase return challan/note should be Issued along With LR.</p><p>Subject To Passing Direct Dispatch Mill passing. Subject to Wankaner Jurisdiction Only</p>');

  function btngetHTMLCode() {
    alert(editor1.getHTMLCode())
  }

  function btnsetHTMLCode() {
    editor1.setHTMLCode("<h1>editor1.setHTMLCode() sample</h1><p>You clicked the setHTMLCode button at " + new Date() + "</p>")
  }
  function btngetPlainText() {
    alert(editor1.getPlainText())
  }
  
    $('#btn_add_veh').on('click', function() {

          var numClass = $('.vehClass').length
          var count_veh = parseInt($('#no_of_vehicle').val());
           
          if(count_veh>numClass)
          {
            
            for (i = 0; i < count_veh-numClass; i++) 
            {
              $('#veh_col').append('<div class="form-group vehClass"><input type="text" placeholder="Enter Vehicle No." class="form-control" name="veh_nos[]"/></div>');
            }
          }
          else
          {
            $( ".vehClass" ).each(function(index) {
              var noofveh = index + 1;
              if(noofveh > count_veh ){
                $(this).remove();
              }
            });
          }


    });


     //Confirmation No. Check

   $('#conf_check').on('click', function() {

        $('span.error-confno').hide();

        var conf1=$('#pur_conf').val();
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
                }
                else
                {
                 $('#conf_index').after('<span class="error error-confno text-danger"><b>Not Available..</b></span>');
                }

           }
       });
        
      });



});
</script>

<script type="text/javascript">
  function lettersValidate(key) {
    var keycode = (key.which) ? key.which : key.keyCode;

    if ((keycode > 64 && keycode < 91) || (keycode > 96 && keycode < 123))  
    {     
           return true;    
    }
    else
    {
        return false;
    }
         
}

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
   
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>



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
