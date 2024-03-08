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
function convertDate($date)
{
  $final_date='';
  if($date!='' && $date!='0000-00-00')
  {
    $final_date = str_replace('/', '-', $date);
    $final_date = date('Y-m-d', strtotime($final_date));
  }
    return $final_date;
}
 
  if(isset($_POST['ajaxcalc']))
  {
   
    if(isset($_POST['net_amt']) && isset($_POST['amt']) && isset($_POST['ad_hoc']))
    {
      $amtArr=$_POST['amt'];
      $netAmt=$_POST['net_amt'];
      
      // $ad_hoc=$_POST['ad_hoc'];
      // $netAmt=$netAmt-$ad_hoc;

      foreach ($amtArr as  $value) 
      {
        if ($value == '') {
          $value  = 0;
        }
        $netAmt = number_format(($netAmt-$value),2,'.', '');
      }
      if(isset($_POST['b2b_amount']))
      {
        $b2b_total=array_sum($_POST['b2b_amount']);
         $netAmt = number_format(($netAmt-$b2b_total),2,'.', '');
      }

      echo json_encode(array("finalcal"=>$netAmt)); 
      exit;
    }

  }

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from pur_pay where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);

      $sql2 = 'select * from pur_report where id ="'.$row['pur_report_id'].'"';
      $result2 = mysqli_query($conn, $sql2);
      if (mysqli_num_rows($result2) > 0) {
        $row2 = mysqli_fetch_assoc($result2);
       
      }
    }else {
      $errorMsg = 'Could not Find Any Record';
      echo $errorMsg;
    }
  }

  $debit_report = 'select * from debit_report where id ="'.$row['debit_report_id'].'"';
  $debit_report1 = mysqli_query($conn, $debit_report);

  
  if(isset($_POST['submit'])){
    $invoice_amt = $_POST['invoice_amt'];
    $final_debit_amount = $_POST['final_debit_amount'];
    $net_amt = $_POST['net_amt'];
    $pay_amt = $_POST['pay_amt'];
    $debit_report_id = $_POST['debit_report_id'];
    $tds_amount = $_POST['tds_amount'];
    
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");
    $user= $_SESSION["username"];
    $dynamic_field=array();

    $label=$_POST['lable'];
    $amt=$_POST['amt'];
    $date=$_POST['dyn_date'];

    foreach ($label as $key => $value) {
      $final_date = '';     
      if($date[$key]!='')
      {
        $final_date = str_replace('/', '-', $date[$key]);
        $final_date = date('Y-m-d', strtotime($final_date));
      }
      $dynamic_field[$key]['lable'] = $label[$key];
      $dynamic_field[$key]['amt'] = $amt[$key];
      $dynamic_field[$key]['date'] = $final_date;
    }

     $dynamic_field= json_encode($dynamic_field);

     //bill 2 bill payment dynamic data
   $b2bArr=array();
   if(isset($_POST['b2b_id']))
   {
    foreach ($_POST['b2b_id'] as $key => $id) 
    {
      $b2bArr[$key]['b2b_id']=$id;
      $b2bArr[$key]['b2b_label']=$_POST['b2b_label'][$key];
      $b2bArr[$key]['b2b_amount']=$_POST['b2b_amount'][$key];
      $b2bArr[$key]['b2b_date']=convertDate($_POST['b2b_date'][$key]);
    }
   }
   $b2bArr= json_encode($b2bArr);


    // print_r($_POST);exit();

          $sql = "update pur_pay set
              debit_report_id = '".$debit_report_id."',
              invoice_amt = '".$invoice_amt."',
              final_debit_amount = '".$final_debit_amount."',
              net_amt = '".$net_amt."',                    
              pay_amt = '".$pay_amt."',
              final_debit_amount = '".$final_debit_amount."',
              tds_amount = '".$tds_amount."',
              dynamic_field = '".$dynamic_field."',
              bill2bill_dynamic_data = '".$b2bArr."',
              username = '".$user."',
              updated_at = '".$timestamp."'
            where id=".$_GET['id'];
    $result = mysqli_query($conn, $sql);
  
    if($result){
      $successMsg = 'New record updated successfully';

      $page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
        header("Location: index.php?page=$page");

    }else{
      $errorMsg = 'Error '.mysqli_error($conn);
      echo $errorMsg;
    }
    
  }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Bales Payout Edit</title>

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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Bales Payout Edit</span></a>
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
              <li class="nav-item"><a class="btn btn-outline-danger" href="index.php?page=<?php echo $page ?>"><i class="fa fa-sign-out-alt"></i>Back</a></li>
            </ul>
        </div>
      </div>
    </nav>

      <!-- last change on table START-->
       <div class="last-updates">
                  <div class="firm-selectio">
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
          <div class="last-edits-fl">
        <?php
           $sqlLastChange="select username,updated_at from pur_pay where id='".$row['id']."'";

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
              <div class="card-header">
                Bales Payout Edit
              </div>
              <div class="card-body">
                <?php  $sql3 = 'select * from pur_report where party="'.$row['party'].'" AND invoice_no ="'.$row['invoice_no'].'"';
                  $result3 = mysqli_query($conn, $sql3);
                    if (mysqli_num_rows($result3) > 0) {
                      foreach ($conn->query($sql3) as $result3) 
                      {
                 ?>
                <form id="main_form" class="" action="" method="post" enctype="multipart/form-data">
                  <div class="row">
                  <div class="form-group col-md-6">
                    <label for="pur_party"> Party</label> 
                    <input type="text" class="form-control" name="pur_party" value="<?php $party = "select * from external_party where id='".$row['party']."'";
                          $partyresult = mysqli_query($conn, $party);

                          $partyrow = mysqli_fetch_assoc($partyresult);

                          $ex_party='';
                          if(isset($partyrow))
                          {
                            $ex_party=$partyrow['partyname'];
                          }
                          echo $ex_party; ?>" readonly >
                  </div>
                  <div class="form-group col-md-6">
                    <label for="pur_invoice_no"> Invoice No</label>
                    <input type="text" name="pur_invoice_no" class="form-control" value="<?php echo $row['invoice_no']; ?>" readonly>
                  </div>
                 
                  </div> 

                  <div class="row">

                     <div class="form-group col-md-2">
                              <label for="gross_amt">Gross Amount</label>
                              <input type="text" id="gross_amt" name="gross_amt" class="form-control" readonly="" value="<?php echo $row['gross_amt'] ?>">
                            </div>
                            <div class="form-group col-md-2">
                              <label for="tax_amt">Tax Amount</label>
                              <input type="text" id="tax_amt" name="tax_amt" class="form-control" readonly="" value="<?php echo $row['tax_amt'] ?>">
                            </div>
                            <div class="form-group col-md-2">
                              <label for="tcs_amt">TCS Amount</label>
                              <input type="text" id="tcs_amt" name="tcs_amt" class="form-control" readonly="" value="<?php echo $row['tcs_amt'] ?>">
                            </div>
                            <div class="form-group col-md-2">
                              <label for="other_amt">Other Amount</label>
                              <input type="text" id="other_amt" name="other_amt" class="form-control" readonly="" value="<?php echo $row['other_amt'] ?>">
                            </div>

                        
                            <div class="form-group col-md-4">
                              <label for="invoice_amt">Invoice Amount</label>
                              <input type="text" id="invoice_amt" name="invoice_amt" class="form-control" readonly="" value="<?php echo $result3['netpayableamt'] ?>">
                            </div>
                            <?php
                              if (mysqli_num_rows($debit_report1) > 0) 
                              { 
                                  foreach ($conn->query($debit_report) as $debit_report1)
                                  { ?>
                                    <div class="form-group col-md-4">
                                      <label for="final_debit_amount">Final Debit Amount With Tax</label>
                                      <input type="text" name="final_debit_amount" class="form-control" id="final_debit_amount" readonly="" value="<?php echo $debit_report1['final_debit_amount']; ?>">
                                    </div>

                                    <?php
                                       $debit_date='';
                                    if($row['debit_report_date']!='' && $row['debit_report_date']!='0000-00-00')
                                    {
                                     $debit_date = date("d/m/Y", strtotime($row['debit_report_date']));
                                    }
                                    ?>

                                      <div class="form-group col-md-4">
                                      <label for="debit_report_date">Debit Report Date</label>
                                      <input type="text" name="debit_report_date" class="form-control"  value="<?php echo $debit_date; ?>" readonly="">
                                    </div>

                                      <div class="form-group col-md-4">
                                      <label for="tds_amount">TDS Amount</label>
                                      <input type="text" name="tds_amount" id="tds_amount" class="form-control"  value="<?php echo $debit_report1['tds_amount']; ?>" readonly="">
                                    </div>

                                       <input type="hidden" name="debit_report_id" value="<?php echo $debit_report1['id']; ?>">

                                    <?php 
                                  }
                              }else{?>
                                  <div class="form-group col-md-4">
                                      <label for="final_debit_amount">Final Debit Amount With Tax</label>
                                      <input type="text" name="final_debit_amount" class="form-control" value="" readonly="">
                                    </div>

                                    <div class="form-group col-md-4">
                                      <label for="debit_report_date">Debit Report Date</label>
                                      <input type="text" name="debit_report_date" class="form-control" value="" readonly="">
                                    </div>

                                      <div class="form-group col-md-4">
                                      <label for="tds_amount">TDS Amount</label>
                                      <input type="text" name="tds_amount" id="tds_amount" class="form-control"  value="" readonly="">
                                    </div>

                             <?php  } ?>

                              
                      
                    <div class="col-md-4">
                          
                          <div class="form-group">
                          <label for="ad_hoc">Ad-Hoc </label>
                          <input type="text" class="form-control" name="ad_hoc" placeholder="Enter Ad-Hoc " value="<?php echo $debit_report1['ad_hoc'] ?>" id="ad_hoc"  readonly="">
                        </div>
                   
                    </div>

                    <div class="col-md-4">
                          
                          <div class="form-group">
                          <label for="ad_hoc_date">Ad-Hoc Payment Date </label>
                          <input type="text" class="form-control" name="ad_hoc_date" placeholder="Enter Ad-Hoc Payment Date" id="ad_hoc_date" value="<?php echo date("d/m/Y", strtotime($debit_report1['ad_hoc_date'])); ?>" readonly="">
                        </div>
                   
                    </div>

                    <div class="form-group col-md-4">
                                <label for="net_amt">Net Amount</label>
                                <input type="text" class="form-control" id="net_amt" name="net_amt" readonly>
                              </div>
                    </div>





                      <?php

                        if ($row['dynamic_field'] != '' ) {    
                          $dynamic_fieldArr = json_decode($row['dynamic_field']);?>
                          <div class="add_dyamic">
                            <?php foreach ($dynamic_fieldArr as $key => $value) 
                            {
                              ?>

                                                      <div class="row">


                        <?php if ($key == 0) {?>
                                                  <div class="form-group col-md-3">
                          <label for="lable">Label</label>
                          <input type="text" class="form-control" id="lable" name="lable[]" placeholder="Enter Label Name" value="<?php echo $value->lable; ?>">
                        </div>

                        <div class="form-group col-md-3">
                          <label for="amt">Amount</label>
                          <input type="text" class="form-control amt" onkeyup="Amout_pay(this)"  onkeypress="return NumericValidate(event,this)" id="amt" value="<?php echo $value->amt; ?>" name="amt[]" placeholder="Enter Amount">
                        </div>

                        <?php 
                        $GetDate = '';
                        if ($value->date != '') {
                          $GetDate = date("d/m/Y", strtotime($value->date)); 
                        } ?>
                        <div class="form-group col-md-3">
                          <label for="dyn_date">Date</label>
                          <input type="text" class="form-control datepicker" id="date" name="dyn_date[]" placeholder="Enter Date" value="<?php echo $GetDate; ?>">
                        </div>
                          <div class="form-group col-md-3" style="margin-top: 30px;">
                          <button type="button" class=" btn btn-primary add_button"> +</button>
                        </div>  
                        <?php }else{?>

                       
                          <div class="form-group col-md-3">
                            <label for="lable">Label</label>
                            <input type="text" class="form-control" id="lable" name="lable[]" placeholder="Enter Label Name" value="<?php echo $value->lable; ?>">
                          </div>

                          <div class="form-group col-md-3">
                            <label for="amt">Amount</label>
                            <input type="text" class="form-control amt" onkeyup="Amout_pay(this)"  onkeypress="return NumericValidate(event,this)" id="amt" name="amt[]" value="<?php echo $value->amt; ?>" placeholder="Enter Amount">
                          </div>

                          <?php 
                            $GetDate = '';
                            if ($value->date != '') {
                              $GetDate = date("d/m/Y", strtotime($value->date)); 
                            }
                          ?>

                          <div class="form-group col-md-3">
                            <label for="net_amt">Date</label>
                            <input type="text" class="form-control datepicker" id="date<?php echo $key; ?>" name="dyn_date[]" placeholder="Enter Date" value="<?php  echo $GetDate; ?>">
                          </div>
                              <div class="form-group col-md-3" style="margin-top: 30px;"><a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a></div>

                        <?php } ?>
                        

                      </div>

                      <?php }?>
                        </div>
                      <?php }

                     ?>


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
                    <?php
                      $BillArr=json_decode($row['bill2bill_dynamic_data'],true);
                      if(isset($BillArr))
                      {
                         foreach ($BillArr as $key => $item) 
                         {
                           if($key==0)
                           {
                          ?>

                            <div class="row b2bRow">
                              <div class="form-group col-md-3">
                                <label for="lable">Label</label>
                                <input type="text" class="form-control b2b_label"  name="b2b_label[]" value="<?php echo $item['b2b_label'] ?>" readonly>
                              </div>
                              <div class="form-group col-md-3">
                                <label for="b2b_amount">Amount</label>
                                <input type="text" class="form-control b2b_amount" name="b2b_amount[]" value="<?php echo $item['b2b_amount'] ?>" readonly>
                              </div>
                              <div class="form-group col-md-3">
                                <label for="b2b_date">Date</label>
                                <input type="text" class="form-control b2b_date" name="b2b_date[]" value="<?php echo convertDate2($item['b2b_date']) ?>" readonly>
                              </div>
                              
                              <div class="form-group col-md-3" style="margin-top: 30px;">
                                <a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a>
                              </div>
                                <input type="hidden" name="b2b_id[]" value="<?php echo $item['b2b_id'] ?>" class="b2b_id"/>
                            </div>

                          <?php
                           }
                           else
                           {
                          ?>

                          <div class="row b2bRow">
                              <div class="form-group col-md-3">
                                <input type="text" class="form-control b2b_label"  name="b2b_label[]" value="<?php echo $item['b2b_label'] ?>" readonly>
                              </div>
                              <div class="form-group col-md-3">
                                <input type="text" class="form-control b2b_amount" name="b2b_amount[]" value="<?php echo $item['b2b_amount'] ?>" readonly>
                              </div>
                              <div class="form-group col-md-3">
                                <input type="text" class="form-control b2b_date" name="b2b_date[]" value="<?php echo convertDate2($item['b2b_date']) ?>" readonly>
                              </div>
                              
                              <div class="form-group col-md-3">
                                <a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a>
                              </div>
                                <input type="hidden" name="b2b_id[]" value="<?php echo $item['b2b_id'] ?>" class="b2b_id"/>
                            </div>
                          <?php
                           }
                         }
                      }
                    ?>


                    </div>
                    <br>

                    <hr>


                       <div class="row"></div>
                          <div class="form-group">
                          <label for="pay_amt">Amount to be pay</label>
                          <input type="text" class="form-control bold" id="pay_amt" name="pay_amt" placeholder="Amount to be pay" value="<?php echo $row['pay_amt']; ?>" readonly="">
                        </div>


                     
                  <div class="form-group">
                    <button type="submit" name="submit" class="btn btn-primary waves">Submit</button>
                  </div>
                </form>
                <?php
              }
            } ?>
            
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

 

var bill2billDataArr='';

 $(document).ready(function() {


      

      var invoice_amt = $('#invoice_amt').val();
      var ad_hoc = $('#ad_hoc').val();
      var final_debit_amount = $('#final_debit_amount').val();
      var tds_amount = $('#tds_amount').val();

      if(invoice_amt=='')
      {
        invoice_amt=0;
      }
      if(ad_hoc=='')
      {
        ad_hoc=0;
      }
      if(final_debit_amount=='')
      {
        final_debit_amount=0;
      }
      if(tds_amount=='')
      {
        tds_amount=0;
      }

      var net_amt = parseFloat(invoice_amt)-parseFloat(final_debit_amount)-parseFloat(ad_hoc)-parseFloat(tds_amount);

      net_amt= Math.round(net_amt);

      $('#net_amt').val(net_amt.toFixed(2));



      $('#b2bSelect').on('change', function() {
                 $('.b2baddBtn').attr("disabled",false);
            });




        $(".datepicker").datepicker({dateFormat:'dd/mm/yy',
              changeMonth: true,
              changeYear: true
            });
        $(".datepicker").keydown(false);

        var add_dyamic = $('.add_dyamic');

        var i = $("input[name='lable[]'").length;
        $('.add_button').click(function(){
          
          i = parseInt(i)+1;
          var Amount_pay =  $('#pay_amt').val();
          
          if (Amount_pay === '0.00') {
              alert('Sorry You Can Not Add New Fileds Beacuse Amount To Be Pay Is Zero');
          }else{

            var addFileds = '<div class="row"><div class="form-group col-md-3"><label for="lable">Label</label><input type="text" class="form-control" id="lable" name="lable[]" placeholder="Enter Label Name"></div><div class="form-group col-md-3"><label for="amt">Amount</label><input type="text" class="form-control amt" onkeyup="Amout_pay(this)"  onkeypress="return NumericValidate(event,this)" id="amt" name="amt[]" placeholder="Enter Amount"></div><div class="form-group col-md-3"><label for="net_amt">Date</label><input type="text" class="form-control" id="date'+i+'" name="dyn_date[]" placeholder="Enter Date"></div><div class="form-group col-md-3" style="margin-top: 30px;"><a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a></div></div>';
              $(add_dyamic).append(addFileds);
              $("#date"+i).datepicker({dateFormat:'dd/mm/yy',
                changeMonth: true,
                changeYear: true
              }).datepicker("setDate", new Date());
              $("#date"+i).keydown(false);
               Amout_pay();
          }
        });

  
        $(add_dyamic).on('click', '.remove_btn', function(e){
              e.preventDefault();
           $(this).parent('div').parent('div').remove(); 
           Amout_pay();

        });


         //bill 2 bill dynamic section
         $('.b2baddBtn').click(function(){

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
                Amout_pay();
            }
        });


        $('.b2b_dyamic').on('click', '.remove_btn', function(e){
              e.preventDefault();
           $(this).parent('div').parent('div').remove(); 
           Amout_pay();
           getB2bData(); 

        });

       

      

        
    });


  getB2bData(); 
  //get bill 2 bill data
function getB2bData()
{
    var report_id = "<?php echo $row['debit_report_id'] ?>";


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



 // Amout_pay();
    var net_amt = $('#net_amt').val();
    
     var timer = null;
    function Amout_pay(e) {
      clearTimeout(timer); 
      timer = setTimeout(AjxCal, 1000)

    }



    function AjxCal() {
      var formdata = $('#main_form').serialize();
        formdata += "&ajaxcalc=1";
         $.ajax({
          data : formdata,
          method : 'post',
          dataType : "json",
          success: function(result){
            $("#pay_amt").val(result.finalcal);
            console.log(result);
          }
        });
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
      else 
      {
        var len = $(element).val().length;
        var index = $(element).val().indexOf('.');
        if (index > 0 && charCode == 46) 
        {
          return false;
        }
        if (index > 0)
        {
          var CharAfterdot = (len + 1) - index;
          if (CharAfterdot > 3)
          {
            return false;
          }
        }
      }



  return true;       
}  
    </script>
  </body>
</html>
