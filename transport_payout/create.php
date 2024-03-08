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
//dd/mm/yyy
function convertDate2($date)
{
  $final_date='';
  if($date!='' && $date!='0000-00-00')
  {
    $final_date = str_replace('-', '/', $date);
    $final_date = date('d/m/Y', strtotime($final_date));
  }
    return $final_date;

}

  if(isset($_POST['ajaxcalc']))
  {
   
    if(isset($_POST['tds_per']) && isset($_POST['trans_amount']))
    {
      $response=array();

      $tds_per=$_POST['tds_per'];
      $trans_amount=$_POST['trans_amount'];

      if($tds_per=='')
      {
        $tds_per=0;
      }


      $tds_amount=($trans_amount*$tds_per)/100;
      $total_amount=$trans_amount-$tds_amount;
      

      $response['tds_amount']=$tds_amount;
      $response['total_amount']=$total_amount;

      echo json_encode($response); 
      exit;
    }

  }

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Transport Payout Database Create</title>


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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New Transport Payout</span></a>
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
              <div class="card-header">Transport Payout Database</div>
              <div class="card-body">
                <form class="" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                     
                      <?php

                            $getFinancialYear=$_SESSION['pur_financial_year'];
                             $getFinancialYearID=$_SESSION['pur_financial_year_id'];
                             $firm_id= $_SESSION["pur_firm_id"];
                            
                            $sql2 = "select DISTINCT trans_id from pur_report where firm='".$firm_id."' AND financial_year='".$getFinancialYearID."' AND trans_pay_type='to_be_pay' AND invoice_no != ''";
                            $result2 = mysqli_query($conn, $sql2);
                            ?>
                            <div class="form-group">
                              <label for="transport_id">Select Transport</label>
                             

                           <select name="transport_id" data-live-search="true" class="form-control searchDropdown" id="transport_id">
                           <option value="" disabled selected>Choose option</option>
                            <?php                   
                              foreach ($conn->query($sql2) as $result2) 
                              {
                                $party_sql="SELECT * FROM transport WHERE id='".$result2['trans_id']."'";
                                        $party_result = mysqli_query($conn, $party_sql);
                                          $party_row = $party_result->fetch_assoc();

                                    echo "<option value='" .$result2['trans_id']. "'>" .$party_row['trans_name']. "</option>";
                              }
                            ?>                              
                            </select>
                      </div>                      
                            <div class="form-group">
                              <label for="trans_lr_no">Select LR No</label>
                           <select name="trans_lr_no" id="trans_lr_no" class="form-control">
                           <option value="" disabled selected>Choose option</option>
                                                  
                            </select>
                      </div>

                      <input type="hidden" id="pur_report_id" name="pur_report_id" value="">

                    <div class="form-group">
                      <button type="submit" id="submit" name="Submit" class="btn btn-primary waves">Submit</button>
                    </div>
                </form>

                <?php
                if(isset($_POST['Submit'])){
                  
                  if(!empty($_POST['trans_lr_no']) && !empty($_POST['transport_id']) && !empty($_POST['pur_report_id'])) {
                    

                    $report_id = $_POST['pur_report_id'];
                   

                    $sql3 = 'select * from pur_report where id="'.$report_id.'"';
                
                    $result3 = mysqli_query($conn, $sql3);
                    if (mysqli_num_rows($result3) > 0) {
                      foreach ($conn->query($sql3) as $result3) 
                      {
                    ?>
                    <hr>
                    <form id="main_form" class="" action="add.php" method="post" enctype="multipart/form-data">
                    <div class="row"> 


                      <input type="hidden" name="pur_report_id" value="<?php echo $report_id ?>">



                    
                    </div>
                      <?php 
                      $sql4 = "select * from transport_payout where pur_report_id='".$result3['id']."'";
                      $result4 = mysqli_query($conn, $sql4);
                      if (mysqli_num_rows($result4) > 0) { 
                        foreach ($conn->query($sql4) as $result4){
                        ?>
                        <div class="form-group">
                          <h4>You have already created payout entry. Please go to edit page: <a href="edit.php?id=<?php echo $result4['id'] ?>" class="btn btn-info"><i class="fa fa-user-edit"></i></a></h4>
                        </div>
                      <?php
                        } 
                      }
                      else{


                        //lr date convert to dd/mm/yyyy
                        $trans_lr_date='';
                        if($result3['trans_lr_date']!='' && $result3['trans_lr_date']!='0000-00-00')
                          {
                            $trans_lr_date = str_replace('-', '/', $result3['trans_lr_date']);
                            $trans_lr_date = date('d/m/Y', strtotime($trans_lr_date));
                          }




                     //check if entery created in bill 2 bill payment if,created then grab data from there
                      $date="";
                      $tds_per="";
                      $tds_amt="";
                      $total_amt="";
                      $sqlb2b="select * from bill2bill_sub_data where table_indicator='transport_payout' AND report_id='".$report_id."'";
                      $resultb2b=mysqli_query($conn,$sqlb2b);
                      if(mysqli_num_rows($resultb2b)>0)
                      {
                        $rowb2b=mysqli_fetch_assoc($resultb2b);
                         $date=convertDate2($rowb2b['date']);
                          $tds_per=$rowb2b['tds_per'];
                          $tds_amt=$rowb2b['tds_amount'];
                          $total_amt=$rowb2b['payment'];
                      }
                    ?>



                      <div class="row">


                      <div class="form-group col-md-5">
                        <label for="trans_id">Transport Name</label>
                        <?php 

                          $sql_trans="SELECT * FROM transport WHERE id='".$result3['trans_id']."'";
                          $result_trans = mysqli_query($conn, $sql_trans);
                          $row_trans = $result_trans->fetch_assoc();

                          ?>
                        <input type="text"  class="form-control" readonly="" value="<?php  echo $row_trans['trans_name']; ?>" >

                        <input type="hidden" name="trans_id" value="<?php echo $row_trans['id'] ?>">

                      </div>


                      <div class="form-group col-md-2">
                        <label for="invoice_no"> Invoice No</label>
                        <input type="text" name="invoice_no" value="<?php echo $result3['invoice_no']; ?>" class="form-control" readonly>
                      </div>


                      <div class="form-group col-md-5">
                        <label for="pur_party"> External Party</label>
                        <?php 

                          $party_sql1="SELECT * FROM external_party WHERE id='".$result3['party']."'";
                          $party_result1 = mysqli_query($conn, $party_sql1);
                          $party_row1 = $party_result1->fetch_assoc();

                          ?>
                        <input type="text"  class="form-control" readonly="" value="<?php echo $party_row1['partyname']; ?>" >

                        <input type="hidden" name="ext_party_id" value="<?php echo $party_row1['id'] ?>">
                      </div>
                     






                        <div class="form-group col-md-3">
                              <label for="trans_vehicle_no">Transport Vehicle No.</label>
                              <input type="text" id="trans_veh_no" name="trans_veh_no" class="form-control" readonly="" value="<?php echo $result3['trans_veh_no'] ?>">
                            </div>
                            <div class="form-group col-md-3">
                              <label for="trans_lr_date">LR Date</label>
                              <input type="text" id="trans_lr_date" name="trans_lr_date" class="form-control" readonly="" value="<?php echo $trans_lr_date ?>">
                            </div>

                           <div class="form-group col-md-3">
                              <label for="trans_lr_no">LR No</label>
                              <input type="text" id="trans_lr_no" name="trans_lr_no" class="form-control" readonly="" value="<?php echo $result3['trans_lr_no']; ?>">
                            </div>

                            <div class="form-group col-md-3">
                              <label for="trans_amount">Transport Amount</label>
                              <input type="text" id="trans_amount" name="trans_amount" class="form-control" readonly="" value="<?php echo $result3['trans_amount'] ?>">
                            </div>


                          <div class="form-group col-md-4">
                            <label for="pay_date">Payment Date </label>
                            <input type="text" class="form-control datepicker" name="pay_date" placeholder="Enter Payment Date" autocomplete="off" value="<?php echo $date ?>">
                          </div>

                        
                            <div class="form-group col-md-4">
                              <label for="tds_per">TDS Percentage (%)</label>
                              <input type="text" id="tds_per" name="tds_per" class="form-control" placeholder="Enter TDS %" onkeypress="return decimalValidate(event,this)" value="<?php echo $tds_per ?>">
                            </div>

                            <div class="form-group col-md-4">
                              <label for="tds_amount">TDS Amount</label>
                              <input type="text" id="tds_amount" name="tds_amount" class="form-control" value="<?php echo $tds_amt ?>"readonly="" >
                            </div>

                

                     <div class="form-group col-md-4">
                        <label for="total_amount">Total Amount</label>
                        <input type="text" class="form-control" id="total_amount" name="total_amount" value="<?php echo $total_amt ?>" readonly>
                      </div>

                  </div>

                      <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary waves">Submit</button>
                      </div>
                    </form>
                   <?php 
                      }
                    }
                    }else{
                      echo "No data Found!";
                    }
                  } else {
                    echo 'Please select the value.';
                  }
                  
                }
              ?>
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

     <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

       <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script>






    $(document).ready(function() {

         $(".datepicker").datepicker({dateFormat:'dd/mm/yy',
              dateFormat:'dd/mm/yy',
              changeMonth: true,
              changeYear: true,
            });

         $(".datepicker").keydown(false);


          var timer = null;
         $('#tds_per').on('input', function() {
               clearTimeout(timer); 
              timer = setTimeout(calulcation, 1000)
         });
      
      

        $('#transport_id').on('change', function() {

        var value=this.value;
        var $dropdown = $("#trans_lr_no");
        
        $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {transport_id:value},
            success: function(response)
            {
                console.log(response)
                var jsonData = JSON.parse(response);
                
                $dropdown.find('option').not(':first').remove();
                $.each(jsonData, function(index,obj) 
                {

                   var option_data="<option data-reportid="+obj.pur_report_id+" value="+obj.trans_lr_no+">"+obj.trans_lr_no+ " (Invoice No. : "+obj.invoice_no+")</option>";
                  $(option_data).appendTo('#trans_lr_no'); 

                  $('#trans_lr_no').prop('selectedIndex',0);
                  $('#pur_report_id').val('');
                               
                    // $dropdown.append($("<option />").val(value).text(value));
                });


                 
           }
        });


        
      });


        $('#trans_lr_no').on('change', function() {

          var report_id=$(this).find(':selected').attr('data-reportid')
          $('#pur_report_id').val(report_id);

        
        });





    });


    function calulcation()
    {
        var formdata = $('form').serialize();
        formdata += "&ajaxcalc=1";
         $.ajax({
          data : formdata,
          method : 'post',
          dataType : "json",
         success: function(result){


            $('#tds_amount').val(result.tds_amount.toFixed(2));
            $('#total_amount').val(result.total_amount.toFixed(2));

            console.log(result)
             
          }
        });
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
    </script>

    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>



  </body>
</html>
