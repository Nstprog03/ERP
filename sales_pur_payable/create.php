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
$getYear=$_SESSION['sales_conf_financial_year'];
$year_array=explode("/",$getYear);


  if(isset($_POST['ajaxcalc']))
  {
   
    if(isset($_POST['net_amt']))
    {
      $adhocArr=$_POST['adhoc_amount'];
      $debitArr=$_POST['debit_amount'];
      $gstArr=$_POST['gst_amount'];
      $tcsArr=$_POST['tcs_amount'];
      $tdsArr=$_POST['tds_amount'];
      $otherArr=$_POST['other_amount'];

      $total_received=0;

      $out_standing=$_POST['net_amt'];


      //Ad Hoc Calculation
      foreach ($adhocArr as  $value) 
      {
        if ($value == '') {
          $value  = 0;
        }
        $total_received = number_format(($total_received+$value),2,'.', '');
        $out_standing = number_format(($out_standing-$value),2,'.', '');
      }

      //Debit Calculation
      foreach ($debitArr as  $value) 
      {
        if ($value == '') {
          $value  = 0;
        }
        $total_received = number_format(($total_received+$value),2,'.', '');
        $out_standing = number_format(($out_standing-$value),2,'.', '');
      }

      //GST Calculation
      foreach ($gstArr as  $value) 
      {
        if ($value == '') {
          $value  = 0;
        }
        $total_received = number_format(($total_received+$value),2,'.', '');
        $out_standing = number_format(($out_standing-$value),2,'.', '');
      }

      //TCS Calculation
      foreach ($tcsArr as  $value) 
      {
        if ($value == '') {
          $value  = 0;
        }
        $total_received = number_format(($total_received+$value),2,'.', '');
        $out_standing = number_format(($out_standing-$value),2,'.', '');
      }

      //TDS Calculation
      foreach ($tdsArr as  $value) 
      {
        if ($value == '') {
          $value  = 0;
        }
        $total_received = number_format(($total_received+$value),2,'.', '');
        $out_standing = number_format(($out_standing-$value),2,'.', '');
      }

      //Other Calculation
      foreach ($otherArr as  $value) 
      {
        if ($value == '') {
          $value  = 0;
        }
        $total_received = number_format(($total_received+$value),2,'.', '');
        $out_standing = number_format(($out_standing-$value),2,'.', '');
      }

      if(isset($_POST['b2b_amount']))
      {
        $b2b_total=array_sum($_POST['b2b_amount']);
        $total_received = number_format(($total_received+$b2b_total),2,'.', '');
        $out_standing = number_format(($out_standing-$b2b_total),2,'.', '');
      }


      echo json_encode(array(
        "out_standing"=>$out_standing,
        "total_received"=>$total_received
      )); 
      exit;
    }

  }



?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Sales Recievable Database Create</title>
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

       $(".datepicker2").datepicker({
        dateFormat:'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        maxDate: new Date('<?php echo($year_array[1]) ?>'),
        minDate: new Date('<?php echo($year_array[0]) ?>')
    }).datepicker("setDate", new Date());

       $(".datepicker").keydown(false);
       $(".datepicker2").keydown(false);

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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Sales Recievable Database</span></a>
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
              <div class="card-header">Sales Recievable Database</div>
              <div class="card-body">
                <form class="" id="pur_payout_form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                     
                          <?php
                            $sql = "select * from sales_report where firm =".$_SESSION["sales_conf_firm_id"]." ";
                            $result = mysqli_query($conn, $sql);

                            $start_date=$year_array[0];
                            $end_date=$year_array[1];

                            $sql2 = "select DISTINCT party_name from sales_report where firm =".$_SESSION["sales_conf_firm_id"]." AND invoice_date>='".$start_date."' AND invoice_date<='".$end_date."'";
                            $result2 = mysqli_query($conn, $sql2);
                          ?>
                          <div class="row">
                            <div class="form-group col-md-6">
                              <label for="pur_party">Select Party</label>
                              <select id="pur_party" name="pur_party" data-live-search="true" class="form-control searchDropdown">
                                <option value="" disabled selected>Choose option</option>
                                <?php                   
                                  foreach ($conn->query($sql2) as $result2) 
                                  {   

                                  $party = "select * from external_party where id='".$result2['party_name']."'";
                                  $partyresult = mysqli_query($conn, $party);

                                  $partyrow = mysqli_fetch_assoc($partyresult);
                                  

                                        echo "<option value='" .$partyrow['id']. "'>" .$partyrow['partyname']. "</option>";
                                  }
                                ?>                              
                              </select>
                            </div>                      
                            <div class="form-group col-md-6">
                              <label for="pur_invoice_no">Select Invoice No</label>
                              <select id="pur_invoice_no" name="pur_invoice_no" class="form-control">
                                <option value="" disabled selected>Choose option</option>
                                                        
                              </select>
                            </div>
                          </div>
                    <div class="form-group">
                      <button type="submit" name="Submit" class="btn btn-primary waves">Submit</button>
                    </div>
                </form>

                <?php
                if(isset($_POST['Submit']))
                {
                  if(!empty($_POST['pur_party']) && !empty($_POST['pur_invoice_no'])) 
                  {



                    //check already created or not
                    $sql_check="select * from sales_rcvble where firm='".$_SESSION["sales_conf_firm_id"]."' AND financial_year_id='".$_SESSION["sales_financial_year_id"]."' AND pur_invoice_no='".$_POST['pur_invoice_no']."' AND pur_party='".$_POST['pur_party']."' LIMIT 1";
                    $result_check = mysqli_query($conn, $sql_check);
                    $count_check = mysqli_num_rows($result_check);

                    if($count_check>0)
                    { 
                      foreach ($conn->query($sql_check) as $row_check)
                      {

                        if($row_check['OSAmount']=='0.00' || $row_check['OSAmount']=='0')
                        {

                          $sql_ext="select partyname from external_party where id='".$_POST['pur_party']."'";
                            $result_ext = mysqli_query($conn, $sql_ext);
                            $row_ext=mysqli_fetch_assoc($result_ext);

                            ?>
                                  <div class="form-group">
                                  <h5 class="text-success">All Payment Received For Party - <b> <?php echo $row_ext['partyname'] ?></b> And Invoice No.<b> <?php echo $_POST['pur_invoice_no'] ?></b> <br>Click Show Button To View Entry: <a href="show.php?id=<?php echo $row_check['id'] ?>" class="btn btn-success"><i class="fa fa-eye"></i></a></h5>
                                </div>
                            <?php
                        }
                        else
                        {

                          $sql_ext="select partyname from external_party where id='".$_POST['pur_party']."'";
                            $result_ext = mysqli_query($conn, $sql_ext);
                            $row_ext=mysqli_fetch_assoc($result_ext);


                      ?>

                         <div class="form-group">
                          <h5>You have already created entry For Party - <b> <?php echo $row_ext['partyname'] ?></b> And Invoice No.<b> <?php echo $_POST['pur_invoice_no'] ?></b> <br>Click On Edit Button To Edit Entry: <a href="edit.php?id=<?php echo $row_check['id'] ?>" class="btn btn-info"><i class="fa fa-user-edit"></i></a></h5>
                        </div>
                       <?php 
                        }

                       } 
                       
                   }
                   else
                   {
                      $dah_pur_invoice_no = $_POST['pur_invoice_no'];
                      $dah_pur_party = $_POST['pur_party'];
                      $firm_id=$_SESSION["sales_conf_firm_id"];
                      $fyear_id=$_SESSION["sales_financial_year_id"];



                      $start_date=$year_array[0];
                      $end_date=$year_array[1];
                      $sql3 = "select * from sales_report where party_name='".$dah_pur_party."' AND invice_no='".$dah_pur_invoice_no."' AND financial_year_id='".$fyear_id."' AND firm='".$firm_id."' LIMIT 1";

                      $result3 = mysqli_query($conn, $sql3);
                      if (mysqli_num_rows($result3) > 0) 
                      {
                        foreach ($conn->query($sql3) as $result3) 
                        {

                      ?>
                      <hr>
                      <form class="" action="add.php" method="post" enctype="multipart/form-data"> 

                        <div class="row">
                          <input type="hidden" name="bill_date" value="<?php echo $result3['invoice_date'] ?>" >
                          <input type="hidden" name="delivery_city" value="<?php echo $result3['delivery_city'] ?>" >
                          <input type="hidden" name="bill_no" value="<?php echo $result3['invice_no'] ?>" >

                          <input type="hidden" name="conf_no" value="<?php echo $result3['conf_no'] ?>" >

                          <input type="hidden" id="sale_report_id" name="sale_report_id" value="<?php echo $result3['id'] ?>" >


                        
                          
                        <div class="form-group col-md-3">
                          <label for="credit_days">Credit Days :</label>
                          <input type="text" class="form-control" placeholder="Credit Days" name="credit_days" value="<?php echo $result3['credit_days'] ?>" readonly>
                        </div>

                        <input type="hidden" name="parakh_date" value="<?php echo $result3['parakh_date'] ?>">

                            <?php 
                             $due_date='';
                             $due_days='';
                            if($result3['credit_days']!='')
                            {
                                date_default_timezone_set('Asia/Kolkata');
                                $curDate=date('Y-m-d');


                              $credit_days=$result3['credit_days']-1;

                              $parakh_date=$result3['parakh_date'];
                              $due_date1 = date('Y-m-d', strtotime($parakh_date. " + $credit_days day"));

                               
                              $due_date=date("d/m/Y", strtotime($due_date1));
                            
                              //due days count
                              $date1 = date_create($curDate);
                              $date2 = date_create($parakh_date);
                              $diff = date_diff($date1,$date2);
                              $due_days=$diff->format("%a")+1;




                            }




                            
                            ?>

                        <div class="form-group col-md-3">
                          <label for="sales_date">Due Date</label>
                          <input type="text" class="form-control" placeholder="Due Date" name="due_date" autocomplete="off" value="<?php echo $due_date ?>" readonly>
                        </div>

                        <div class="form-group col-md-3">
                          <label for="received_date">Select Received Date :</label>
                          <input type="text" class="form-control datepicker" placeholder="Received Date" name="received_date" autocomplete="off">
                        </div>

                         <div class="form-group col-md-3">
                          <label for="due_days">Due Days</label>
                          <input type="text" class="form-control" placeholder="Due Days" name="due_days" value="<?php echo $due_days ?>" readonly>
                        </div>

                      </div>

                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="pur_party"> Party</label>
                          <?php

                            $party_1 = "select * from external_party where id='".$dah_pur_party."'";
                            $party_1result = mysqli_query($conn, $party_1);
                            $party_1row = mysqli_fetch_assoc($party_1result);


                          ?>
                          <input type="text" class="form-control" value="<?php echo $party_1row['partyname']; ?>" readonly>

                          <input type="hidden" class="form-control" name="pur_party" value="<?php echo $party_1row['id']; ?>" readonly>

                        </div>
                        <div class="form-group col-md-6">
                          <label for="pur_invoice_no"> Invoice No</label>
                          <input type="text" class="form-control" name="pur_invoice_no" value="<?php echo $dah_pur_invoice_no; ?>" readonly>
                        </div>


                        <div class="form-group col-md-4">
                              <label for="gross_amt">Gross Amount</label>
                              <input type="text" id="gross_amt" name="gross_amt" class="form-control" readonly="" value="<?php echo $result3['grs_amt'] ?>">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="tax_amt">Tax Amount</label>
                              <input type="text" id="tax_amt" name="tax_amt" class="form-control" readonly="" value="<?php echo $result3['txn_amt'] ?>">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="other_amt_tcs">Other Amount(TCS)</label>
                              <input type="text" id="other_amt_tcs" name="other_amt_tcs" class="form-control" readonly="" value="<?php echo $result3['other_amt_tcs'] ?>">
                            </div>



                      </div>

                      <!-- Start -->
                      <div class="row">
                          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="">
                              <div class="form-group col-sm-12" style="margin: -14px !important;">
                                <br>
                                <input type="hidden" name="firm" value="<?php echo$result3['firm']; ?>">
                                <label for="netpayableamt">Total Amount</label>
                                <input type="hi" name="total_value" class="form-control" readonly="" id="total_value" value="<?php echo round($result3['total_value']) ?>">
                              </div>

                              <div class="form-group col-sm-12" style="margin: -14px !important;">
                                <br>
                                <label for="netpayableamt">Credit Amount</label>
                                <input type="text" name="credit_amt" class="form-control" onkeypress="return NumericValidate(event,this)" id="credit_amt">
                              </div>

                              <div class="form-group col-sm-12" style="margin: -14px !important;">
                                <br>
                                <label for="netpayableamt">Net Amount</label>
                                <input type="text" name="net_amt" class="form-control" id="net_amt" readonly="">
                              </div>
                            </div>
                          </div>

                          <div class="vl"></div>
                          <style type="text/css">
                          .vl {
                            margin-right: 0px;
                            border-right: 1px solid #6c757d;
                            height: auto;
                            margin-left: -1px;
                          }
                          </style>

                          <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                              <div class="">
                                <br>

                               <div class="dynmicAdhoc"> 
                                  <div class="row">
                                    <div class="form-group col-sm-5">    
                                      <label for="ad_hoc">Ad-Hoc Amount</label>
                                      <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control adhocAmt1" name="adhoc_amount[]" placeholder="Enter Ad-Hoc Amount">
                                    </div>
                                    <div class="form-group col-sm-5">    
                                      <label for="adhoc_date">Date</label>
                                      <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker2" name="adhoc_date[]" placeholder="Enter Date" autocomplete="off">
                                    </div>

                                     <div class="form-group col-sm-2" style="margin-top: 30px;">
                                      <button type="button" class=" btn btn-primary adhoc_add_button"> +</button>
                                    </div>
                                    </div>
                                   
                                </div>

                                <div class="dynamicDebit"> 
                                  <div class="row">
                                    <div class="form-group col-sm-5">    
                                     <label for="debit_amount">Debit Note Amount </label>
                                      <input type="text" class="form-control debitAmt1" name="debit_amount[]" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" placeholder="Enter Debit Note Amount">
                                    </div>
                                    <div class="form-group col-sm-5">    
                                      <label for="debit_date">Date</label>
                                      <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker2" name="debit_date[]" placeholder="Enter Date" autocomplete="off">
                                    </div>

                                     <div class="form-group col-sm-2" style="margin-top: 30px;">
                                      <button type="button" class=" btn btn-primary debit_add_button"> +</button>
                                    </div>
                                    </div>
                                </div>

                                <div class="dynamicGST"> 
                                  <div class="row">
                                    <div class="form-group col-sm-5">    
                                     <label for="gst_amount">GST Amount </label>
                                      <input type="text" class="form-control gstAmt1" name="gst_amount[]" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" placeholder="Enter GST Amount">
                                    </div>
                                    <div class="form-group col-sm-5">    
                                      <label for="gst_date">Date</label>
                                      <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker2" name="gst_date[]" placeholder="Enter Date" autocomplete="off">
                                    </div>

                                     <div class="form-group col-sm-2" style="margin-top: 30px;">
                                      <button type="button" class=" btn btn-primary gst_add_button"> +</button>
                                    </div>
                                    </div>
                                </div>

                                <div class="dynamicTCS"> 
                                  <div class="row">
                                    <div class="form-group col-sm-5">    
                                     <label for="tcs_amount">TCS Amount </label>
                                      <input type="text" class="form-control tcsAmt1" name="tcs_amount[]" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" placeholder="Enter TCS Amount">
                                    </div>
                                    <div class="form-group col-sm-5">    
                                      <label for="tcs_date">Date</label>
                                      <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker2" name="tcs_date[]" placeholder="Enter Date" autocomplete="off">
                                    </div>

                                     <div class="form-group col-sm-2" style="margin-top: 30px;">
                                      <button type="button" class=" btn btn-primary tcs_add_button"> +</button>
                                    </div>
                                    </div>
                                </div>

                                <div class="dynamicTDS"> 
                                  <div class="row">
                                    <div class="form-group col-sm-5">    
                                     <label for="tds_amount">TDS Amount </label>
                                      <input type="text" class="form-control tdsAmt1" name="tds_amount[]" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" placeholder="Enter TDS Amount">
                                    </div>
                                    <div class="form-group col-sm-5">    
                                      <label for="tds_date">Date</label>
                                      <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker2" name="tds_date[]" placeholder="Enter Date" autocomplete="off">
                                    </div>

                                     <div class="form-group col-sm-2" style="margin-top: 30px;">
                                      <button type="button" class=" btn btn-primary tds_add_button"> +</button>
                                    </div>
                                    </div>
                                </div>

                                <div class="dynamicOther"> 
                                  <div class="row">
                                    <div class="form-group col-sm-5">    
                                     <label for="other_amount">Other Amount </label>
                                      <input type="text" class="form-control otherAmt1" name="other_amount[]" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" placeholder="Enter Other Amount">
                                    </div>
                                    <div class="form-group col-sm-5">    
                                      <label for="other_date">Date</label>
                                      <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker2" name="other_date[]" placeholder="Enter Date" autocomplete="off">
                                    </div>

                                     <div class="form-group col-sm-2" style="margin-top: 30px;">
                                      <button type="button" class=" btn btn-primary other_add_button"> +</button>
                                    </div>
                                    </div>
                                </div>
                                <hr>
                                 <div class="row">              
                                  <div class="form-group col-md-4">
                                    <label for="b2bSelect"><b>Bill 2 Bill Payment</b></label>
                                    <select class="form-control b2bSelect" id="b2bSelect">
                                      <option disabled="" value="" selected="">Select Option</option>
                                    </select>                
                                  </div>
                                  <div class="col-md-4 " >
                                      <button type="button" style="margin-top: 32px;" class="btn btn-primary b2baddBtn" disabled="">Add</button>
                                  </div>                                       
                              </div>

                               <div class="b2b_dyamic">
                                </div>



                                  <hr>
                                  <div class="row">
                                  <div class="form-group col-sm-6">
                                    <label for="total_received">Total Received Amount</label>
                                    <input type="text" class="form-control" name="total_received" id="total_received" readonly="">
                                  </div>

                                  <div class="form-group col-sm-6">
                                    <label for="OSAmount">Out-Standing Amount</label>
                                    <input type="text" class="form-control" name="OSAmount" id="OSAmount" onkeypress="return NumericValidate(event,this)" readonly="">
                                  </div>
                                  </div> 
                                  <div class="row">

                                      <div class="form-group col-sm-12">
                                
                                <label for="netpayableamt">Net Amount</label>
                                <input type="text" name="net_amt" class="form-control net_amt"  readonly="">
                              </div>

                                  </div>      

                                
                               
                              </div>
                          </div>
                        </div>

                      <!--End   -->
                        
                        <div class="form-group">
                          <button type="submit" name="submit" class="btn btn-primary waves">Submit</button>
                        </div>
                      </form>

                     <?php 
                       }
                      }
                      else
                      {
                        echo "No data Found!";
                      }
                   }


                   
                  } 

                  else 
                  {
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
         
    <script>

      var bill2billDataArr='';

      $(document).ready(function(){


        $('#b2bSelect').on('change', function() {
                 $('.b2baddBtn').attr("disabled",false);
            });



        //ad_hoc dynamic add
         var dynmicAdhoc = $('.dynmicAdhoc');
         var i = 1; 
        $('.adhoc_add_button').click(function(){
          
          i = parseInt(i)+1;
            var addAdHocFileds = '<div class="row"><div class="form-group col-md-5"><input type="text" class="form-control adhocAmt'+i+'" onkeyup="calulcation()"  onkeypress="return NumericValidate(event,this)" id="ad_hoc" name="adhoc_amount[]" placeholder="Enter Ad-Hoc Amount"></div><div class="form-group col-md-5"><input type="text" class="form-control" id="date'+i+'" name="adhoc_date[]" placeholder="Enter Date" autocomplete="off"></div><div class="form-group col-md-2"><a href="javascript:void(0);" class="btn btn-danger adhoc_remove_btn">-</a></div></div>';
              $(dynmicAdhoc).append(addAdHocFileds);
              $("#date"+i).datepicker({dateFormat:'dd/mm/yy',
                dateFormat:'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                maxDate: new Date('<?php echo($year_array[1]) ?>'),
                minDate: new Date('<?php echo($year_array[0]) ?>')
              }).datepicker("setDate", new Date());
               $("#date"+i).keydown(false);
       
          
        });

        $(dynmicAdhoc).on('click', '.adhoc_remove_btn', function(e){
              e.preventDefault();
           $(this).parent('div').parent('div').remove(); 
         
            calulcation();
        });





          //debit note dynamic add
         var dynamicDebit = $('.dynamicDebit');
         var j = 1; 
        $('.debit_add_button').click(function(){
          
          j = parseInt(j)+1;
            var addDebitAmtFields = '<div class="row"><div class="form-group col-md-5"><input type="text" class="form-control debitAmt'+j+'" onkeyup="calulcation()"  onkeypress="return NumericValidate(event,this)" name="debit_amount[]" placeholder="Enter Debit Note Amount"></div><div class="form-group col-md-5"><input type="text" class="form-control" id="debit_date'+j+'" name="debit_date[]" placeholder="Enter Date" autocomplete="off"></div><div class="form-group col-md-2"><a href="javascript:void(0);" class="btn btn-danger debit_remove_btn">-</a></div></div>';
              $(dynamicDebit).append(addDebitAmtFields);
              $("#debit_date"+j).datepicker({dateFormat:'dd/mm/yy',
                dateFormat:'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                maxDate: new Date('<?php echo($year_array[1]) ?>'),
                minDate: new Date('<?php echo($year_array[0]) ?>')
              }).datepicker("setDate", new Date());
               $("#debit_date"+j).keydown(false);

       
          
        });

        $(dynamicDebit).on('click', '.debit_remove_btn', function(e){
              e.preventDefault();
           $(this).parent('div').parent('div').remove(); 
          
            calulcation();
        });



        //GST dynamic add
         var dynamicGST = $('.dynamicGST');
         var k = 1; 
        $('.gst_add_button').click(function(){
          
          k = parseInt(k)+1;
            var addGSTFields = '<div class="row"><div class="form-group col-md-5"><input type="text" class="form-control debitAmt'+k+'" onkeyup="calulcation()"  onkeypress="return NumericValidate(event,this)" name="gst_amount[]" placeholder="Enter GST Amount"></div><div class="form-group col-md-5"><input type="text" class="form-control" id="gst_date'+k+'" name="gst_date[]" placeholder="Enter Date" autocomplete="off"></div><div class="form-group col-md-2"><a href="javascript:void(0);" class="btn btn-danger gst_remove_btn">-</a></div></div>';
              $(dynamicGST).append(addGSTFields);
              $("#gst_date"+k).datepicker({dateFormat:'dd/mm/yy',
                dateFormat:'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                maxDate: new Date('<?php echo($year_array[1]) ?>'),
                minDate: new Date('<?php echo($year_array[0]) ?>')
              }).datepicker("setDate", new Date());
              $("#gst_date"+k).keydown(false);
       
          
        });

        $(dynamicGST).on('click', '.gst_remove_btn', function(e){
              e.preventDefault();
           $(this).parent('div').parent('div').remove(); 
           
            calulcation();
        });


        //TCS dynamic add
         var dynamicTCS = $('.dynamicTCS');
         var l = 1; 
        $('.tcs_add_button').click(function(){
          
          l = parseInt(l)+1;
            var addTCSFields = '<div class="row"><div class="form-group col-md-5"><input type="text" class="form-control tcsAmt'+l+'"  onkeypress="return NumericValidate(event,this)" onkeyup="calulcation()" name="tcs_amount[]" placeholder="Enter TCS Amount"></div><div class="form-group col-md-5"><input type="text" class="form-control" id="tcs_date'+l+'" name="tcs_date[]" placeholder="Enter Date" autocomplete="off"></div><div class="form-group col-md-2"><a href="javascript:void(0);" class="btn btn-danger tcs_remove_btn">-</a></div></div>';
              $(dynamicTCS).append(addTCSFields);
              $("#tcs_date"+l).datepicker({dateFormat:'dd/mm/yy',
                dateFormat:'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                maxDate: new Date('<?php echo($year_array[1]) ?>'),
                minDate: new Date('<?php echo($year_array[0]) ?>')
              }).datepicker("setDate", new Date());
              $("#tcs_date"+l).keydown(false);
       
          
        });

        $(dynamicTCS).on('click', '.tcs_remove_btn', function(e){
              e.preventDefault();
           $(this).parent('div').parent('div').remove(); 
           
            calulcation();
        });


        //TDS dynamic add
         var dynamicTDS = $('.dynamicTDS');
         var m = 1; 
        $('.tds_add_button').click(function(){
          
          m = parseInt(m)+1;
            var addTDSFields = '<div class="row"><div class="form-group col-md-5"><input type="text" class="form-control tdsAmt'+m+'" onkeyup="calulcation()"  onkeypress="return NumericValidate(event,this)" name="tds_amount[]" placeholder="Enter TDS Amount"></div><div class="form-group col-md-5"><input type="text" class="form-control" id="tds_date'+m+'" name="tds_date[]" placeholder="Enter Date" autocomplete="off"></div><div class="form-group col-md-2"><a href="javascript:void(0);" class="btn btn-danger tds_remove_btn">-</a></div></div>';
              $(dynamicTDS).append(addTDSFields);
              $("#tds_date"+m).datepicker({dateFormat:'dd/mm/yy',
                dateFormat:'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                maxDate: new Date('<?php echo($year_array[1]) ?>'),
                minDate: new Date('<?php echo($year_array[0]) ?>')
              }).datepicker("setDate", new Date());
              $("#tds_date"+m).keydown(false);

       
          
        });

        $(dynamicTDS).on('click', '.tds_remove_btn', function(e){
              e.preventDefault();
           $(this).parent('div').parent('div').remove(); 
            
            calulcation();
        });


         //Other dynamic add
         var dynamicOther = $('.dynamicOther');
         var n = 1; 
        $('.other_add_button').click(function(){
          
          n = parseInt(n)+1;
            var addOtherFields = '<div class="row"><div class="form-group col-md-5"><input type="text" class="form-control otherAmt'+n+'" onkeyup="calulcation()"  onkeypress="return NumericValidate(event,this)" name="other_amount[]" placeholder="Enter Other Amount"></div><div class="form-group col-md-5"><input type="text" class="form-control" id="other_date'+n+'" name="other_date[]" placeholder="Enter Date" autocomplete="off"></div><div class="form-group col-md-2"><a href="javascript:void(0);" class="btn btn-danger other_remove_btn">-</a></div></div>';
              $(dynamicOther).append(addOtherFields);
              $("#other_date"+n).datepicker({dateFormat:'dd/mm/yy',
                dateFormat:'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                maxDate: new Date('<?php echo($year_array[1]) ?>'),
                minDate: new Date('<?php echo($year_array[0]) ?>')
              }).datepicker("setDate", new Date());
              $("#other_date"+n).keydown(false);
       
          
        });

        $(dynamicOther).on('click', '.other_remove_btn', function(e){
              e.preventDefault();
           $(this).parent('div').parent('div').remove(); 
            calulcation();
          
        });


         //bill 2 bill dynamic section
         $('.b2baddBtn').click(function(){


            var credit_amt=$('#credit_amt').val();

            if(credit_amt=='')
            {
              alert('Please Enter Credit Amount...');
              return false;
            }



            var selectedB2b=$('#b2bSelect :selected').val();

            if(selectedB2b!='')
            {
              var count = $('.b2bRow').length;

              var data=bill2billDataArr.find(item => item.id === selectedB2b);

              var date=changeDateFormat(data.date);

              if(count==0)
              {
                var addFileds = '<div class="row b2bRow"><div class="form-group col-md-3"><label for="lable">Label</label><input type="text" class="form-control b2b_label"  name="b2b_label[]" value="'+data.label+'" readonly></div><div class="form-group col-md-3"><label for="b2b_amount">Amount</label><input type="text" class="form-control b2b_amount" name="b2b_amount[]" value="'+data.payment+'" readonly></div><div class="form-group col-md-3"><label for="b2b_date">Date</label><input type="text" class="form-control b2b_date" name="b2b_date[]"value="'+date+'" readonly></div><div class="form-group col-md-3" style="margin-top: 30px;"><a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a></div><input type="hidden" name="b2b_id[]" value="'+data.id+'" class="b2b_id"/></div>';
              }
              else
              {
                var addFileds = '<div class="row b2bRow"><div class="form-group col-md-3"><input type="text" class="form-control b2b_label"  name="b2b_label[]" value="'+data.label+'" readonly></div><div class="form-group col-md-3"><input type="text" class="form-control b2b_amount" name="b2b_amount[]" value="'+data.payment+'" readonly></div><div class="form-group col-md-3"><input type="text" class="form-control b2b_date" name="b2b_date[]"value="'+date+'" readonly></div><div class="form-group col-md-3"><a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a></div><input type="hidden" name="b2b_id[]" value="'+data.id+'" class="b2b_id"/></div>';
              }
  
              
                $('.b2b_dyamic').append(addFileds);
                getB2bData(); 
                calulcation();
            }
        });

          $('.b2b_dyamic').on('click', '.remove_btn', function(e){
              e.preventDefault();
           $(this).parent('div').parent('div').remove(); 
           calulcation();
           getB2bData(); 

        });




       

          $('#credit_amt').keyup(function() 
          {
            var total_value=parseFloat($("#total_value").val());
            var credit_amt=parseFloat($("#credit_amt").val());
            if (isNaN(total_value)) {
              total_value = 0;
            }
            if (isNaN(credit_amt)) {
              credit_amt = 0;
            }
            var netAmount = parseFloat(total_value)+parseFloat(credit_amt);
            $("#net_amt").val(netAmount.toFixed(2));
            $(".net_amt").val(netAmount.toFixed(2));

            var ad_hoc=parseFloat($("#ad_hoc").val());
            var debit_amount=parseFloat($("#debit_amount").val());
            var gst_amount=parseFloat($("#gst_amount").val());
            var tcs_amount=parseFloat($("#tcs_amount").val());
            var tds_amount=parseFloat($("#tds_amount").val());
            var other_amount=parseFloat($("#other_amount").val());

            if (isNaN(ad_hoc)) {
              ad_hoc = 0;
            }
            if (isNaN(debit_amount)) {
              debit_amount = 0;
            }
            if (isNaN(gst_amount)) {
              gst_amount = 0;
            }
            if (isNaN(tcs_amount)) {
              tcs_amount = 0;
            }
            if (isNaN(tds_amount)) {
              tds_amount = 0;
            }
            if (isNaN(other_amount)) {
              other_amount = 0;
            }
            var TotalAmount = parseFloat(ad_hoc)+parseFloat(debit_amount)+parseFloat(gst_amount)+parseFloat(tcs_amount)+parseFloat(tds_amount)+parseFloat(other_amount);
            $("#total_received").val(TotalAmount.toFixed(2));
            var net_amt =  $("#net_amt").val();
            if (isNaN(net_amt)) {
              net_amt = 0;
            }
            var OutStandingAmt = parseFloat(net_amt)-parseFloat(TotalAmount);
            $("#OSAmount").val(OutStandingAmt.toFixed(2)); 

            calulcation();

          });






    $('#pur_party').on('change', function() {

        var value=this.value;
        var $dropdown = $("#pur_invoice_no");
        
        $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {party:value},
            success: function(response)
            {

                var jsonData = JSON.parse(response);
                
                $dropdown.find('option').not(':first').remove();
                $.each(jsonData, function(index,value) {
                console.log(value);                  
                $dropdown.append($("<option />").val(value).text(value));
                });

                 
           }
        });


        
      });



      });



  getB2bData(); 
  //get bill 2 bill data
function getB2bData()
{
    var report_id = $('#sale_report_id').val();


    var alreadyUsed=[];
    $('.b2b_id').each(function(index){
      alreadyUsed[index]=this.value;
    });

    if(report_id!='')
    {
        $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {
              report_id:report_id,
              getB2Bdata:true
            },
            success: function(response)
            {

              console.log(response)
              var jsonData = JSON.parse(response);

              bill2billDataArr=jsonData;
                
              $('#b2bSelect').find('option').not(':first').remove();
              $.each(jsonData, function(index,item) 
              {

                if(!alreadyUsed.includes(item.id))
                {
                  var date=changeDateFormat(item.date);                   
                  $('#b2bSelect').append($("<option />").val(item.id).text(item.label+' - Rs.'+item.payment+' ('+date+')'));
                }
                
              }); 
              $('#b2bSelect').val(''); 

           }
        });
    }
}

function changeDateFormat(inputDate){  // dd/mm/yyyy
    var splitDate = inputDate.split('-');
    if(splitDate.count == 0){
        return null;
    }

    var year = splitDate[0];
    var month = splitDate[1];
    var day = splitDate[2]; 

    return day + '/' + month + '/' + year;
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



var timer = null;
function calulcation()
{
    var credit_amt=parseFloat($("#credit_amt").val());
    if (isNaN(credit_amt)) 
    {
        alert('Please Enter Credit Amount'); 
    }
    else
    {

      clearTimeout(timer); 
      timer = setTimeout(ajaxCalc, 1000)
    }
  
  }

  function ajaxCalc()
  {
    var formdata = $('form').serialize();
        formdata += "&ajaxcalc=1";
         $.ajax({
          data : formdata,
          method : 'post',
          dataType : "json",
         success: function(result){
             console.log(result)

             $("#total_received").val(result.total_received);
            $("#OSAmount").val(result.out_standing);
          }
        });
  }


    </script>
  </body>

  <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>


</html>
