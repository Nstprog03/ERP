<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  header("location: ../login.php");
  exit;
}
if(!isset($_SESSION['kap_firm_id']) && !isset($_SESSION['kap_seasonal_year_id']))
{
  header('Location: index.php');
}

$getFirm=$_SESSION["kap_firm"];
$getFirmID=$_SESSION["kap_firm_id"];
$getYear=$_SESSION['kap_seasonal_year'];
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

$sql0="select party_shortform from party where id='".$_SESSION['kap_firm_id']."'";
$result0 = mysqli_query($conn, $sql0);
$row0 = mysqli_fetch_assoc($result0);
$shortFirm=$row0['party_shortform'];

$query_GetLastID="SELECT max(id) as last_id FROM kapasiya";
$result_GetLastID = mysqli_query($conn, $query_GetLastID);


$sqlNextID="SELECT AUTO_INCREMENT as id FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$dbname."' AND TABLE_NAME = 'kapasiya'";

$ResultNextID = mysqli_query($conn, $sqlNextID);
$rowNextID = mysqli_fetch_assoc($ResultNextID);

$nextID = $rowNextID['id'];



$serial_no=$shortFirm.'KAP'.'-'.$shortYear.'-'.$nextID;



//ALTER TABLE tablename AUTO_INCREMENT = 1


?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create Kapasiy Sales Record</title>
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


  <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
  rel = "stylesheet">

  <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

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
          <a class="navbar-brand" href="index1.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Kapasiya Sales Report</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto"></ul>
            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index1.php"><i class="fa fa-sign-out-alt"></i>Back</a></li>
            </ul>
          </div>
        </div>
      </nav>

      <div class="last-updates">
        <div class="firm-selection-pre">
          <span class="pre-firm">Firm : </span><span class="pre-firm-name"><?php echo $_SESSION["kap_firm"]; ?></span>
        </div>
        <div class="year-selection-pre">
          <span class="pre-year-text">Seasonal Year :</span> 
          <span class="pre-year">
            <?php 

            $finYearArr=explode('/',$_SESSION["kap_seasonal_year"]);

            $start_date=date('Y', strtotime($finYearArr[0]));
            $end_date=date('Y', strtotime($finYearArr[1]));

            echo $start_date.' - '.$end_date; 

            ?>
          </span>
        </div>
      </div>

      <div class="container-fluid">
        <div class="row justify-content-center">
          <form class="" action="add.php" method="post" enctype="multipart/form-data">
            <div class="card">
              <div class="card-header">Create Kapasiya Sales Record</div>
              <div class="card-body">


                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label for="sr-no">Serial No.</label>                      
                      <input type="text" class="form-control"  name="sr-no" value='<?php echo $serial_no; ?>' readonly>

                    </div>

                  </div>


                  <div class="col-sm-4">
                    <div class="form-group">
                      <label for="firm_name">Firm Name</label>

                      <input type="text" class="form-control" value="<?php echo $_SESSION['kap_firm']; ?>" readonly>
                      <input type="hidden" name="firm" value="<?php echo $_SESSION['kap_firm_id']; ?>">     
                    </div>
                  </div>

                  <div class="col-sm-4">
                    <div class="form-group">
                      <label for="party_name">Party Name</label>
                      <?php
                      $sql = "select * from external_party";
                      $result = mysqli_query($conn, $sql);

                      ?>
                      
                      <select name="party_name" data-live-search="true" 
                      class="form-control searchDropdown">
                      <?php                   
                      foreach ($conn->query($sql) as $result) {

                        echo "<option  value='" .$result['id']. "'>" .$result['partyname']. "</option>";  

                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="no-truck">No. Of Trucks</label>
                    <div class="input-group">
                      <input type="number" min='1' id="total_trucks" placeholder="Please enter No. Of Trucks" class="form-control"   name="no-truck" value="1" required>
                      <div class="input-group-append">
                        <a href="javascript:;" class="btn btn-primary" onclick="cont_quantityFunction($('#total_trucks').val())">Add</a>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="rate">Rate </label>
                    <input type="text" placeholder="Please enter Rate"
                    class="form-control"  name="rate" id="rate" 
                    onkeypress="return NumericValidate(event)">
                  </div>
                </div>


                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="credit">Credit </label>
                    <select id="credit" class="form-control" name="credit">
                      <option value="next">Next Day</option>
                      <option value="weekly">Weekly</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                </div>

                <div class="col-sm-4 otherDaySection">
                  <div class="form-group">
                    <label for="credit">Other Days </label>
                    <input type="text" placeholder="Enter Days" 
                    class="form-control"  name="other_day" onkeypress="return NumericValidate(event)">

                  </div>
                </div>



                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="broker">Broker</label>
                    <?php
                    $sql = "select * from broker";
                    $result = mysqli_query($conn, $sql);

                    ?>

                    <select name="broker" class="form-control">
                      <?php                   
                      foreach ($conn->query($sql) as $result) {

                        echo "<option  value='" .$result['id']. "'>" .$result['name']. "</option>";  

                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="prod_name">Select Product</label>
                    <select name="prod_name" class="form-control">
                      <option value="kapasiya">kapasiya</option>
                    </select>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="prod_name">Confirmation Date</label>
                    <input type="text" placeholder="Confirmation Date" class="form-control datepicker"  name="conf_date" autocomplete="off">
                  </div>

                </div>
              </div>

            </div>
            <div class="row mx-0" id="trucks_fields">
              <div class="border-row d-flex flex-wrap w-100"> 
                <div class="card">
                  <div class="card-header p-0">
                    <a class="card-link p-3 d-block" data-toggle="collapse" href="#truck_field_1">
                      Truck Fields 1
                    </a>
                  </div>
                  <div id="truck_field_1" class="collapse">
                    <div class="card-body">
                      <div class="row">

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="trucks">Trucks </label>
                            <input type="text" placeholder="Please enter Trucks" class="form-control"  name="trucks[]" value="1" readonly>
                          </div>
                        </div>


                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="sl-date">Sales Date </label>
                            <input type="text" placeholder="Please sales date" class="form-control datepicker"  name="sl-date[]" value="" autocomplete="off">
                          </div>
                        </div>  

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="weight">Weight</label>
                            <input type="text" placeholder="Please Weight" class="form-control"
                            name="weight[]" id="weight_1" 

                            onkeypress="return NumericValidate(event,1);">
                          </div>
                        </div>                    


                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="basic-amt">Basic Amount</label>
                            <input type="text" placeholder=" Basic Amount" class="form-control" name="basic-amt[]" id="b_amt_1" readonly="">
                          </div>
                        </div>                    

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="gst">GST</label>
                            <input type="text" placeholder="Please GST" class="form-control"  name="gst[]" 
                            id="gst_1" 
                            onkeypress="return NumericValidate1(event,1)";
                            >
                          </div>
                        </div>  

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="gst">GST Amount</label>
                            <input type="text" placeholder="GST Amount" class="form-control"  name="gst_amount[]" 
                            id="gst_amount_1" readonly="" >
                          </div>
                        </div> 
                        
                         <div class="col-sm-4">
                          <div class="form-group">
                            <label for="gst">Basic Amount + GST Amount</label>
                            <input type="text" placeholder="Basic Amount + GST Amount" class="form-control" id="basic_gst_amount_1" readonly="" >
                          </div>
                        </div> 

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="tcs-per">TCS Percentage</label>
                            <input type="text" placeholder="Please TCS Percentage" 
                            class="form-control"  name="tcs-per[]" id="tcs_1"
                            onkeypress="return NumericTCS(event,1)";

                            >
                          </div>
                        </div>                    

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="tcs-amt">TCS Amount</label>
                            <input type="text" placeholder="TCS Amount" 
                            class="form-control"  name="tcs-amt[]" id="tcs_amount_1" readonly="">
                          </div>
                        </div> 


                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="tds-per">TDS Percentage</label>
                            <input type="text" placeholder="Enter TDS Percentage" 
                            class="form-control"  name="tds-per[]" id="tds_1"
                            onkeypress="return NumericTCS(event,1)";

                            >
                          </div>
                        </div>                    

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="tds-amt">TDS Amount</label>
                            <input type="text" placeholder="TDS Amount" 
                            class="form-control"  name="tds-amt[]" id="tds_amount_1" readonly="">
                          </div>
                        </div>                    

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="final-amt">Final Amount</label>
                            <input type="text" placeholder="Final Amount" 
                            class="form-control"  name="final-amt[]"
                            id="final_amt_1" readonly="">
                          </div>
                        </div>                    

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="invoice-no">Invoice No</label>
                            <input type="text" placeholder="Please Invoice No" class="form-control"  name="invoice-no[]" value="">
                          </div>
                        </div>   

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="truck_no">Select Truck</label>
                            <?php
                            $sql = "select * from truck_master";
                            $result = mysqli_query($conn, $sql);

                            ?>

                            <select name="truck_no[]"
                            class="form-control searchDropdown" data-live-search='true'>
                            <?php                   
                            foreach ($conn->query($sql) as $result) {

                              echo "<option  value='" .$result['id']. "'>" .$result['truck_no']. "</option>";  

                            }
                            ?>
                          </select>
                        </div>
                      </div>  

                      <div class="col-sm-4">
                        <div class="form-group">
                          <label for="payment-st">Payment Status</label>
                          <select name="payment-st[]" class="form-control">
                            <option value="complete">
                              Complete
                            </option>
                            <option value="pending" selected>
                              Pending
                            </option>
                          </select>
                        </div>
                      </div> 

                      <div style="margin-top: 40px;" class="col-sm-4">

                        <div class="checkbox">
                          <label><input type="checkbox" name="truck_complete[]" value="1"> Sold Out</label>
                        </div>

                      </div>


                    </div>


                   



                  </div>




                </div>
              </div>


              

            </div> 


          </div>

          
               <div class="form-group col-md-4">
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



<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

<script type="text/javascript">
  $(document).ready(function () {
    $('#sidebarCollapse').on('click', function () {
      $('#sidebar').toggleClass('active');
    });


    $('.collapse').on('show.bs.collapse', function () {
      $('.collapse.in').collapse('hide');
    });





//--------------------------
$('.otherDaySection').hide();
$('#credit').on('change', function() {

  var value=this.value;

  if(value=='other')
  {
    $('.otherDaySection').show();
  }
  else
  {
    $('.otherDaySection').hide();
  }


});


$('#rate').keyup(function() {

  var total_trucks=parseInt($('#total_trucks').val());
  var rate=parseFloat($('#rate').val());

  for(var i=1; i<=total_trucks; i++)
  {
    var weight=parseFloat($('#weight_'+i).val());

    if(weight=='' || weight==undefined)
    {
      weight = 0;
    }
    var total=parseFloat(weight)*parseFloat(rate)/20;

    if(isNaN(total))
    {
      total=0;
    }

    $('#b_amt_'+i).val(total.toFixed(2))

    var gst=parseFloat($('#gst_'+value).val());
    var basic_amount=parseFloat($('#b_amt_'+value).val());
    if(gst=='' && gst==undefined)
    {
      gst = 0;
    }
    var gst_total=parseFloat(basic_amount)*parseFloat(gst)/100;
    if(isNaN(gst_total))
    {
      gst_total=0;
    }
    $('#gst_amount_'+value).val(gst_total.toFixed(2))
          //alert(weight+' '+rate)
          var gst=parseFloat($('#gst_'+value).val());
          var basic_amount=parseFloat($('#b_amt_'+value).val());
          var gst_amount=parseFloat($('#gst_amount_'+value).val());


          //tcs calculation
          var tcs=parseFloat($('#tcs_'+value).val());
          if(tcs=='' && tcs==undefined)
          {
            tcs = 0;
          }
          
          var basic_gst_amount = parseFloat(basic_amount) + parseFloat(gst_amount);
          $('#basic_gst_amount_'+value).val(basic_gst_amount);
          var tcs_total=parseFloat(basic_gst_amount)*parseFloat(tcs)/100;
          if(isNaN(tcs_total))
          {
            tcs_total=0;
          }

          $('#tcs_amount_'+value).val(tcs_total.toFixed(2))


           //tds calculation
           var tds=parseFloat($('#tds_'+value).val());
           if(tds=='' && tds==undefined)
           {
            tds = 0;
          }
          
          var tds_total=parseFloat(basic_amount)*parseFloat(tds)/100;
          if(isNaN(tds_total))
          {
            tds_total=0;
          }

          $('#tds_amount_'+value).val(tds_total.toFixed(2))






          var final_amount = parseFloat(basic_amount)+parseFloat(gst_amount)+parseFloat(tcs_total)-parseFloat(tds_total)
          if(isNaN(final_amount))
          {
            final_amount=0;
          }

          $('#final_amt_'+value).val(final_amount.toFixed(2))

        }




      });









});
/*
        var min = 1950,
    max = min + 100,
    select = document.getElementById('selectElementId');

for (var i = min; i<=max; i++){
    var opt = document.createElement('option');
    opt.value = i;
    opt.innerHTML = i;
    select.appendChild(opt);
}

var min = 1950,
    max = min + 100,
    select = document.getElementById('endYear');

for (var i = min; i<=max; i++){
    var opt = document.createElement('option');
    opt.value = i;
    opt.innerHTML = i;
    select.appendChild(opt);
  }*/
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
     function cont_quantityFunction(value) {
      var newhtml = '';
      $('#trucks_fields').empty();
      for (let i = 1; i <= value; i++) {
        var temp = corehtml.replace('#truck_field_1','#truck_field_'+i);
        temp = temp.replace('id="truck_field_1"','id="truck_field_'+i+'"');
        temp = temp.replace('Truck Fields 1','Truck Fields '+i);
        temp = temp.replace('name="trucks[]" value="1"','name="trucks[]" value="'+i+'"');

        temp = temp.replace('name="weight[]" id="weight_1"','name="weight[]" onkeypress="return NumericValidate(event,'+i+');"  id="weight_'+i+'"');

        temp = temp.replace('name="gst[]" id="gst_1"','name="gst[]" onkeypress="return NumericValidate1(event,'+i+');" id="gst_'+i+'"');


        temp = temp.replace('name="gst_amount[]" id="gst_amount_1"','name="gst_amount[]" id="gst_amount_'+i+'"');
        temp = temp.replace('id="basic_gst_amount_1"','id="basic_gst_amount_'+i+'"');


        temp = temp.replace('name="basic-amt[]" id="b_amt_1"','name="basic-amt[]" id="b_amt_'+i+'"');

    // Tcs 

    temp = temp.replace('name="tcs-per[]" id="tcs_1"','name="tcs-per[]" onkeypress="return NumericTCS(event,'+i+');" id="tcs_'+i+'"');
    temp = temp.replace('name="tcs-amt[]" id="tcs_amount_1"','name="tcs-amt[]" id="tcs_amount_'+i+'"');

    temp = temp.replace('name="tds-per[]" id="tds_1"','name="tds-per[]" onkeypress="return NumericTCS(event,'+i+');" id="tds_'+i+'"');

    temp = temp.replace('name="tds-amt[]" id="tds_amount_1"','name="tds-amt[]" id="tds_amount_'+i+'"');

    // final amount 

    temp = temp.replace('name="final-amt[]" id="final_amt_1"','name="final-amt[]" id="final_amt_'+i+'"');
    
    newhtml += temp;

    newhtml+='<script>$(".searchDropdown").selectpicker();</';
    newhtml+='script>';

    $('#trucks_fields').html(newhtml);

    $(".datepicker").datepicker({
      dateFormat: "dd/mm/yy",
      changeMonth: true,
      changeYear: true,
      maxDate: new Date('<?php echo($year_array[1]) ?>'),
      minDate: new Date('<?php echo($year_array[0]) ?>')
    });
    $(".datepicker").keydown(false);
  }

}
const corehtml = document.getElementById("trucks_fields").innerHTML;


function NumericValidate(key,value) {

    //alert(value);

    var keycode = (key.which) ? key.which : key.keyCode;
    if (keycode > 31 && (keycode < 48 || keycode > 57) && !(keycode == 46 || keycode == 8))
    {
      setTimeout(function(){
        var weight=parseFloat($('#weight_'+value).val());
        var rate=parseFloat($('#rate').val());

        if(weight=='' && weight==undefined)
        {
          $('#b_amt_'+value).val(0)

        }

      },500);

      return false;
    }
    else 
    {    

      setTimeout(function(){
        var weight=parseFloat($('#weight_'+value).val());
        var rate=parseFloat($('#rate').val());

        if(weight=='' && weight==undefined)
        {
          weight = 0;
        }

        var total=parseFloat(weight)*parseFloat(rate)/20;
        if(isNaN(total))
        {
          total=0;
        }
        $('#b_amt_'+value).val(total.toFixed(2))
          //alert(weight+' '+rate)
          var gst=parseFloat($('#gst_'+value).val());
          var basic_amount=parseFloat($('#b_amt_'+value).val());
          if(gst=='' && gst==undefined)
          {
            gst = 0;
          }
          var gst_total=parseFloat(basic_amount)*parseFloat(gst)/100;
          if(isNaN(gst_total))
          {
            gst_total=0;
          }
          $('#gst_amount_'+value).val(gst_total.toFixed(2))
          //alert(weight+' '+rate)
          var gst=parseFloat($('#gst_'+value).val());
          var basic_amount=parseFloat($('#b_amt_'+value).val());
          var gst_amount=parseFloat($('#gst_amount_'+value).val());

          //tcs
          var tcs=parseFloat($('#tcs_'+value).val());
          if(tcs=='' && tcs==undefined)
          {
            tcs = 0;
          }
          
          var basic_gst_amount = parseFloat(basic_amount) + parseFloat(gst_amount);
          $('#basic_gst_amount_'+value).val(basic_gst_amount);
          
          var tcs_total=parseFloat(basic_gst_amount)*parseFloat(tcs)/100;
          if(isNaN(tcs_total))
          {
            tcs_total=0;
          }


         
          
          $('#tcs_amount_'+value).val(tcs_total.toFixed(2))


           //tds
           var tds=parseFloat($('#tds_'+value).val());

           if(tds=='' && tds==undefined)
           {
            tds = 0;
          }
          var tds_total=parseFloat(basic_amount)*parseFloat(tds)/100;


          if(isNaN(tds_total))
          {
            tds_total=0;
          }

          $('#tds_amount_'+value).val(tds_total.toFixed(2));



          var final_amount = parseFloat(basic_amount)+parseFloat(gst_amount)+parseFloat(tcs_total)-parseFloat(tds_total);

          if(isNaN(final_amount))
          {
            final_amount=0;
          }

          $('#final_amt_'+value).val(final_amount.toFixed(2))

        },500);
      return true;    
    }
    

  }

  function NumericValidate1(key,value) {
    // alert(value);
    var keycode = (key.which) ? key.which : key.keyCode;

    if (keycode > 31 && (keycode < 48 || keycode > 57) && !(keycode == 46 || keycode == 8))
    {

      return false;
    }
    else 
    {
      setTimeout(function(){
        var gst=parseFloat($('#gst_'+value).val());
        var basic_amount=parseFloat($('#b_amt_'+value).val());
        if(gst=='' && gst==undefined)
        {
          gst = 0;
        }
        var gst_total=parseFloat(basic_amount)*parseFloat(gst)/100;
        $('#gst_amount_'+value).val(gst_total.toFixed(2))
          //alert(weight+' '+rate)
          var gst=parseFloat($('#gst_'+value).val());
          var basic_amount=parseFloat($('#b_amt_'+value).val());
          var gst_amount=parseFloat($('#gst_amount_'+value).val());

          //tcs
          var tcs=parseFloat($('#tcs_'+value).val());
          if(tcs=='' && tcs==undefined)
          {
            tcs = 0;
          }
          
          var basic_gst_amount = parseFloat(basic_amount) + parseFloat(gst_amount);
         $('#basic_gst_amount_'+value).val(basic_gst_amount);
          var tcs_total=parseFloat(basic_gst_amount)*parseFloat(tcs)/100;
          if(isNaN(tcs_total))
          {
            tcs_total=0;
          }
          $('#tcs_amount_'+value).val(tcs_total.toFixed(2))



          //tds
          var tds=parseFloat($('#tds_'+value).val());

          if(tds=='' && tds==undefined)
          {
            tds = 0;
          }
          var tds_total=parseFloat(basic_amount)*parseFloat(tds)/100;


          if(isNaN(tds_total))
          {
            tds_total=0;
          }

          $('#tds_amount_'+value).val(tds_total.toFixed(2));




          var final_amount = parseFloat(basic_amount)+parseFloat(gst_amount)+parseFloat(tcs_total)-parseFloat(tds_total)
          if(isNaN(final_amount))
          {
            final_amount=0;
          }
          
          $('#final_amt_'+value).val(final_amount.toFixed(2))

          


        },500);
      return true;      

    }

  }



  function NumericTCS(key,value) {
    var keycode = (key.which) ? key.which : key.keyCode;

    if (keycode > 31 && (keycode < 48 || keycode > 57) && !(keycode == 46 || keycode == 8))
    {

      return false;
    }
    else 
    {
      setTimeout(function(){
        var gst=parseFloat($('#gst_'+value).val());
        var basic_amount=parseFloat($('#b_amt_'+value).val()); 
        var gst_amount=parseFloat($('#gst_amount_'+value).val());
        var tcs=parseFloat($('#tcs_'+value).val());

        if(tcs=='' && tcs==undefined)
        {
          tcs = 0;
        }
        
         var basic_gst_amount = parseFloat(basic_amount) + parseFloat(gst_amount);
         $('#basic_gst_amount_'+value).val(basic_gst_amount);
          
        var tcs_total=parseFloat(basic_gst_amount)*parseFloat(tcs)/100;


        if(isNaN(tcs_total))
        {
          tcs_total=0;
        }

        $('#tcs_amount_'+value).val(tcs_total.toFixed(2));


          //tds
          var tds=parseFloat($('#tds_'+value).val());

          if(tds=='' && tds==undefined)
          {
            tds = 0;
          }
          var tds_total=parseFloat(basic_amount)*parseFloat(tds)/100;


          if(isNaN(tds_total))
          {
            tds_total=0;
          }

          $('#tds_amount_'+value).val(tds_total.toFixed(2));




          var final_amount = parseFloat(basic_amount)+parseFloat(gst_amount)+parseFloat(tcs_total)-parseFloat(tds_total)

          if(isNaN(final_amount))
          {
            final_amount=0;
          }


          $('#final_amt_'+value).val(final_amount.toFixed(2))

        },500);
      return true;      

    }
    
  }




</script>


<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>


</form>
</body>
</html>
