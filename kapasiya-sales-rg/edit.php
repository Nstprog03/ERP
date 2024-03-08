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


if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $sql = "select * from kapasiya where id=".$id;
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
  }else {
    $errorMsg = 'Could not Find Any Record';
  }
}

$getFirm=$_SESSION["kap_firm"];
$getFirmID=$_SESSION["kap_firm_id"];
$getYear=$_SESSION['kap_seasonal_year'];


if(!isset($getYear))
{
  $getYear="2000-01-01/2060-12-31";
}


$year_array=explode("/",$getYear);




?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Kapasiy Sales Record</title>
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
      $("#sidebarnav").load("../../nav.html"); 
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



            <?php
            $page=1;
            if(isset($_GET['page']))
            {
              $page=$_GET['page'];
            }
            ?>




            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index1.php?page=<?php echo $page ?>"><i class="fa fa-sign-out-alt"></i>Back</a></li>
            </ul>
          </div>
        </div>
      </nav>

      <!-- last change on table START-->
      <div class="last-updates">
        <div class="firm-selectio">
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
      <div class="last-edits-fl">
        <?php
        $sqlLastChange="select username,updated_at from kapasiya where id='".$row['id']."'";

        $resultlLastChange = mysqli_query($conn, $sqlLastChange);

        if(mysqli_num_rows($resultlLastChange)>0)
        {
          $lastChangeRow=mysqli_fetch_assoc($resultlLastChange);

              //.get username from user master
          $user_name='';
          $sqlgetUser="select * from users where id='".$lastChangeRow['username']."'";
          $sqlResultGetUser = mysqli_query($conn, $sqlgetUser);
          if(mysqli_num_rows($sqlResultGetUser)>0)
          {
            $getUserRow=mysqli_fetch_assoc($sqlResultGetUser);
            $user_name=$getUserRow['name'];
          }

          echo "

          <span class='fullch'><span class='chtext'><span class='icon-edit'></span>Last Updated By :</span> <span class='userch'>".$user_name."</span> - <span class='datech'>".date('d/m/Y h:i:s A', strtotime($lastChangeRow['updated_at']))."</span> </span>

          ";
        }
        ?>

      </div>
    </div>

    <!-- last change on table END-->



      <div class="container-fluid">
        <div class="row justify-content-center">
         
            <div class="card">
              <div class="card-header">edit Kapasiya Sales Record</div>
              <form action="update.php" method="post" enctype="multipart/form-data">
              <div class="card-body">


                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="page" value="<?php echo $page ?>">

                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label for="sr-no">Serial No.</label>                      
                      <input type="text" class="form-control"  name="sr-no" value="<?php echo $row['serialno']  ?>"  readonly>

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
                      
                      <select name="party_name" class="form-control searchDropdown" data-live-search="true">
                        <?php                   
                        foreach ($conn->query($sql) as $result) {

                          $isFirmSelected =""; 
                          if($row['party']==$result['id'])
                          {
                           $isFirmSelected = "selected";
                         }

                         echo "<option  value='" .$result['id']. "'".$isFirmSelected.">" .$result['partyname']. "</option>";  

                       }
                       ?>
                     </select>
                   </div>
                 </div>

                 <div class="col-sm-6">
                  <div class="form-group">
                    <label for="no-truck">No. Of Trucks</label>
                    <div class="input-group">
                      <input type="number" min='1' id="total_trucks" placeholder="Please enter No. Of Trucks" class="form-control"   name="no-truck" value="<?php echo $row['no_of_truck'] ?>" required>
                      <input type="hidden" id="old_total_trucks" value="<?php echo $row['no_of_truck'] ?>" required>
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
                    class="form-control" value="<?php echo $row['rate']; ?>"  name="rate" id="rate" 
                    onkeypress="return NumericValidate(event)">
                  </div>
                </div>


                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="credit">Credit </label>
                    <select id="credit" class="form-control" name="credit">
                      <option value="next" <?php
                      if ($row['credit']=='next') {
                        echo "selected";
                      }

                      ?>>Next Day</option>
                      <option value="weekly"
                      <?php
                      if ($row['credit']=='weekly') {
                        echo "selected";
                      }

                      ?>
                      >Weekly</option>
                      <option value="other"

                      <?php
                      if ($row['credit']=='other') {
                        echo "selected";
                      }

                      ?>

                      >Other</option>
                    </select>
                  </div>
                </div>

                <div class="col-sm-4 otherDaySection">
                  <div class="form-group">
                    <label for="credit">Other Days </label>
                    <input type="text" placeholder="Enter Days"   value="<?php echo $row['other_day']; ?>" 
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
                        $isFirmSelected =""; 
                        if($row['broker']==$result['id'])
                        {
                         $isFirmSelected = "selected";
                       }
                       echo "<option  value='" .$result['id']."'".$isFirmSelected.">" .$result['name']. "</option>";  

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

              <?php
              $conf_date='';
              if($row['conf_date']!='' && $row['conf_date']!='0000-00-00')
              {
                $conf_date= date("d/m/Y", strtotime($row['conf_date']));
              }
              ?>

              <div class="col-sm-4">
                <div class="form-group">
                  <label for="prod_name">Confirmation Date</label>
                  <input type="text" placeholder="Confirmation Date" class="form-control datepicker"  name="conf_date" value="<?php echo $conf_date; ?>" autocomplete="off">
                </div>

              </div>
            </div>

            <div class="trucks">



              <div class="row mx-0 " id="trucks_fields">


                <?php 
                $truckData=json_decode($row['truck'],true);
                foreach ($truckData as $key => $item) 
                {

                  $sales_date='';
                  if($item['sales_date']!='' && $item['sales_date']!='0000-00-00')
                  {
                    $sales_date = str_replace('-', '/', $item['sales_date']);
                    $sales_date = date('d/m/Y', strtotime($sales_date));
                  }

                  ?>



                  <div class="border-row d-flex flex-wrap w-100 truckClass_<?= $key+1 ?>">
                    <div class="card">
                      <div class="card-header p-0">
                        <a class="card-link p-3 d-block" data-toggle="collapse" href="#truck_field_<?php echo $item['truck_id']; ?>">
                          Truck Fields <?php echo $item['truck_id'];  ?>
                        </a>
                      </div>
                      <div id="truck_field_<?php echo $item['truck_id']; ?>" class="collapse">
                        <div class="card-body">
                          <div class="row">

                           <div class="col-sm-4">
                            <div class="form-group">
                              <label for="trucks">Trucks </label>
                              <input type="text"  class="form-control"  name="trucks[]" value="<?php echo $item['truck_id'] ?>" readonly>
                            </div>
                          </div>




                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="sl-date">Sales Date </label>
                              <input type="text" placeholder="Please sales date" class="form-control datepicker"  name="sl-date[]"  value="<?php echo $sales_date; ?>" autocomplete="off">
                            </div>
                          </div>  

                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="weight">Weight</label>
                              <input type="text" placeholder="Please Weight" class="form-control"
                              name="weight[]" id="weight_<?= $key+1 ?>" value="<?php echo $item['weight']; ?>" 

                              onkeypress="return NumericValidate(event,<?= $key+1 ?>);">
                            </div>
                          </div>                    


                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="basic-amt">Basic Amount</label>
                              <input type="text" placeholder=" Basic Amount" class="form-control" name="basic-amt[]" id="b_amt_<?= $key+1 ?>" value="<?php echo $item['basic_amt']; ?>"  readonly>
                            </div>
                          </div>                    

                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="gst">GST</label>
                              <input type="text" placeholder="Please GST" class="form-control"  name="gst[]" 
                              id="gst_<?= $key+1 ?>" value="<?php echo $item['gst_per']; ?>"
                              onkeypress="return NumericValidate1(event,<?= $key+1 ?>)";
                              >
                            </div>
                          </div>  

                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="gst">GST Amount</label>
                              <input type="text" placeholder="GST Amount" class="form-control"  name="gst_amount[]" 
                              id="gst_amount_<?= $key+1 ?>" readonly="" value="<?php echo $item['gst_amount']; ?>" >
                            </div>
                          </div>
                          
                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="gst">Basic Amount + GST Amount</label>
                              <input type="text" placeholder="Basic Amount + GST Amount" class="form-control"  
                              id="basic_gst_amount_<?= $key+1 ?>" readonly="" value="<?php echo (float)$item['gst_amount'] + (float)$item['basic_amt']; ?>" >
                            </div>
                          </div>

                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="tcs-per">TCS Percentage</label>
                              <input type="text" placeholder="Please TCS Percentage" 
                              class="form-control"  name="tcs-per[]" id="tcs_<?= $key+1 ?>" 
                              onkeypress="return NumericTCS(event,<?= $key+1 ?>)";
                              value="<?php echo $item['tcs_per']; ?>"
                              >
                            </div>
                          </div>                    

                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="tcs-amt">TCS Amount</label>
                              <input type="text" placeholder="TCS Amount" 
                              class="form-control"  name="tcs-amt[]" id="tcs_amount_<?= $key+1 ?>" readonly="" value="<?php echo $item['tcs_amt']; ?>">
                            </div>
                          </div>  


                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="tds-per">TDS Percentage</label>
                              <input type="text" placeholder="Please TDS Percentage" 
                              class="form-control"  name="tds-per[]" id="tds_<?= $key+1 ?>" 
                              onkeypress="return NumericTCS(event,<?= $key+1 ?>)";
                              value="<?php echo $item['tds_per']; ?>"
                              >
                            </div>
                          </div>                    

                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="tds-amt">TDS Amount</label>
                              <input type="text" placeholder="TDS Amount" 
                              class="form-control"  name="tds-amt[]" id="tds_amount_<?= $key+1 ?>" value="<?php echo $item['tds_amt'] ?>" readonly="" >
                            </div>
                          </div>                   

                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="final-amt">Final Amount</label>
                              <input type="text" placeholder="Final Amount" 
                              class="form-control"  name="final-amt[]"
                              id="final_amt_<?= $key+1 ?>" readonly="" value="<?php echo $item['final_amt']; ?>">
                            </div>
                          </div>                    

                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="invoice-no">Invoice No</label>
                              <input type="text" placeholder="Please Invoice No" class="form-control"  name="invoice-no[]" value="<?php echo $item['invoice_no']; ?>">
                            </div>
                          </div>   

                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="truck_no">Select Truck</label>
                              <?php
                              $sql = "select * from truck_master";
                              $result = mysqli_query($conn, $sql);

                              ?>

                              <select name="truck_no[]" class="form-control searchDropdown" data-live-search='true'>
                                <?php                   
                                foreach ($conn->query($sql) as $result) {
                                  $ifselected = '';
                                  if ($item['truck_no']==$result['id'] ) {
                                    $ifselected='selected';
                                  }
                                  echo "<option  value='" .$result['id']. "'".$ifselected.">" .$result['truck_no']. "</option>";  

                                }
                                ?>
                              </select>
                            </div>
                          </div>  

                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="payment-st">Payment Status</label>
                              <select name="payment-st[]" class="form-control">
                                <option value="complete" <?php if($item['payment_status']=='complete') {
                                  echo "selected";
                                } ?>>
                                Complete
                              </option>
                              <option value="pending"<?php if($item['payment_status']=='pending') {
                                echo "selected";
                              } ?>>
                              Pending
                            </option>
                          </select>
                        </div>
                      </div>


                      <div style="margin-top: 40px;" class="col-sm-4">

                        <div class="checkbox">
                          <label><input type="checkbox" name="truck_complete[]" value="1" <?php if($item['truck_complete']=='1') { echo "checked"; } ?>> Sold Out</label>
                        </div>

                      </div>





                    </div>
                  </div>
                </div>
              </div>             
            </div>

          <?php } ?> 
        </div>
      </div>



      <div class="form-group col-md-12">
        <button type="submit" name="submit" class="btn btn-primary waves">Submit</button>
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





//--------------------------
  //$('.otherDaySection').hide();

  var credit_check=$('#credit').find(":selected").val();
  if(credit_check=='other')
  {
    $('.otherDaySection').show();
  }else{
    $('.otherDaySection').hide();
  }



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
          var tcs=parseFloat($('#tcs_'+value).val());
          if(tcs=='' && tcs==undefined)
          {
            tcs = 0;
          }
          
          var tcs_total=parseFloat(basic_amount)*parseFloat(tcs)/100;
          if(isNaN(tcs_total))
          {
            tcs_total=0;
          }

          $('#tcs_amount_'+value).val(tcs_total.toFixed(2))

          var final_amount = parseFloat(basic_amount)+parseFloat(gst_amount)+parseFloat(tcs_total);
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


      var select_truck = '<select name="truck_no[]"                                      class="form-control searchDropdown" data-live-search="true"><?php $sql = "select * from truck_master";
      $result = mysqli_query($conn, $sql);
      foreach ($conn->query($sql) as $result) {?><option value="<?php echo $result["id"] ?>"><?php echo $result["truck_no"]; ?></option><?php }
      ?></select>' 

  // $('#trucks_fields').empty();
  var old = $('#old_total_trucks').val();

  if(value !=old){
    if(value > old){
      for (let i = 1; i <= value; i++) {
 

        if($(".truckClass_"+i).length == 0){
          var temp = '<div class="border-row d-flex flex-wrap w-100 truckClass_'+i+'"><div class="card"><div class="card-header p-0"><a class="card-link p-3 d-block collapsed" data-toggle="collapse"href="#truck_field_'+i+'"aria-expanded="false">Truck Fields '+i+'</a></div><div id="truck_field_'+i+'" class="collapse" style=""><div class="card-body"><div class="row"><div class="col-sm-4"><div class="form-group"><label for="trucks">Trucks </label><input type="text" placeholder="Please enter Trucks" class="form-control" name="trucks[]" value='+i+' readonly=""></div></div><div class="col-sm-4"><div class="form-group"><label for="sl-date">Sales Date </label><input type="text"placeholder="Please sales date" class="form-control datepicker" name="sl-date[]" autocomplete="off"></div></div><div class="col-sm-4"><div class="form-group"><label for="weight">Weight</label><input type="text" placeholder="Please Weight" class="form-control" name="weight[]" id="weight_'+i+'" value="" onkeypress="return NumericValidate(event,'+i+');"></div></div><div class="col-sm-4"><div class="form-group"><label for="basic-amt">Basic Amount</label><input type="text" placeholder=" Basic Amount" class="form-control" name="basic-amt[]" id="b_amt_'+i+'" value="" readonly=""></div></div><div class="col-sm-4"><div class="form-group"><label for="gst">GST</label><input type="text" placeholder="Please GST" class="form-control" name="gst[]" id="gst_'+i+'" value="" onkeypress="return NumericValidate1(event,'+i+')" ;=""></div></div><div class="col-sm-4"><div class="form-group"><label for="gst">GST Amount</label><input type="text" placeholder="GST Amount" class="form-control" name="gst_amount[]" id="gst_amount_'+i+'" readonly="" value=""></div></div>  <div class="col-sm-4"> <div class="form-group"> <label for="gst">Basic Amount + GST Amount</label> <input type="text" placeholder="Basic Amount + GST Amount" class="form-control"  id="basic_gst_amount_'+i+'" readonly="" value=""></div></div> <div class="col-sm-4"><div class="form-group"><label for="tcs-per">TCS Percentage</label><input type="text" placeholder="Please TCS Percentage" class="form-control" name="tcs-per[]" id="tcs_'+i+'" onkeypress="return NumericTCS(event,'+i+')" ;="" value=""></div></div><div class="col-sm-4"><div class="form-group"><label for="tcs-amt">TCS Amount</label><input type="text" placeholder="TCS Amount" class="form-control" name="tcs-amt[]" id="tcs_amount_'+i+'" readonly="" value=""></div></div><div class="col-sm-4"><div class="form-group"><label for="tds-per">TDS Percentage</label><input type="text" placeholder="Enter TDS Percentage" class="form-control"  name="tds-per[]" id="tds_'+i+'" onkeypress="return NumericTCS(event,'+i+')"></div></div><div class="col-sm-4"><div class="form-group"><label for="tds-amt">TDS Amount</label><input type="text" placeholder="TDS Amount" class="form-control" name="tds-amt[]" id="tds_amount_'+i+'" readonly=""></div></div> <div class="col-sm-4"><div class="form-group"><label for="final-amt">Final Amount</label><input type="text" placeholder="Final Amount" class="form-control" name="final-amt[]" id="final_amt_'+i+'" readonly="" value=""></div></div><div class="col-sm-4"><div class="form-group"><label for="invoice-no">Invoice No</label><input type="text" placeholder="Please Invoice No" class="form-control" name="invoice-no[]" value=""></div></div><div class="col-sm-4"><div class="form-group"><label for="truck_no">Select Truck</label>'+select_truck+'          </div></div><div class="col-sm-4"><div class="form-group"><label for="payment-st">Payment Status</label><select name="payment-st[]" class="form-control"><option value="complete">Complete</option><option value="pending" selected="">Pending</option></select></div></div> <div class="col-sm-4"><div class="checkbox"><label><input type="checkbox" name="truck_complete[]" value="1"> Sold Out</label></div></div></div></div></div></div></div>'

          temp+='<script>$(".searchDropdown").selectpicker();$(".datepicker").keydown(false);</';
          temp+='script>';
          
          $('#trucks_fields').append(temp);
          

          

        }
      }
    }else{

      for (let i = 1; i <= old; i++) {
        // alert(i +'<'+ value);
        if(i > value){
          if($(".truckClass_"+i).length == 1){
            $(".truckClass_"+i).remove();
          }
        }
      }
    }
  }
  
  $(".datepicker").datepicker({
    dateFormat: "dd/mm/yy",
    changeMonth: true,
    changeYear: true,
    maxDate: new Date('<?php echo($year_array[1]) ?>'),
    minDate: new Date('<?php echo($year_array[0]) ?>')
  });


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
          console.log(basic_gst_amount);
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




</body>
</html>
