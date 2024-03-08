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

  $attend_dir = 'files/attend/';
  $salary_dir = 'files/salary/';
  $pf_dir = 'files/pf/';
  $epf_dir = 'files/epf/';

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from sales_rcvble where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Sales Recievable Database Show</title>
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Sales Recievable Database Show</span></a>
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
      <div class="last-edits-fl">
        <?php
        $sqlLastChange="select username,update_at from sales_rcvble where id='".$row['id']."'";

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

          <span class='fullch'><span class='chtext'><span class='icon-edit'></span>Last Updated By :</span> <span class='userch'>".$user_name."</span> - <span class='datech'>".date('d/m/Y h:i:s A', strtotime($lastChangeRow['update_at']))."</span> </span>

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
                Sales Recievable Database Show
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data"> 

                   <div class="row">
                         
                      <div class="form-group col-md-3">
                        <label for="credit_days">Credit Days :</label>
                        <input type="text" class="form-control" placeholder="Credit Days" name="credit_days" value="<?php echo $row['credit_days'] ?>" readonly>
                      </div>

                      <?php
                      $due_date='';
                      $received_date='';
                      if($row['due_date']!='' && $row['due_date']!='0000-00-00')
                      {
                        $due_date = str_replace('-', '/', $row['due_date']);
                        $due_date = date('d/m/Y', strtotime($due_date));


                        //count due days
                          date_default_timezone_set('Asia/Kolkata');
                          $curDate=date('Y-m-d');

                          $due_date1=$row['due_date'];
                  
                           //due days count
                            $parakh_date=$row['parakh_date'];
                            $date1 = date_create($curDate);
                            $date2 = date_create($parakh_date);
                            $diff = date_diff($date1,$date2);
                            $due_days=$diff->format("%a")+1;



                      }
                      if($row['received_date']!='' && $row['received_date']!='0000-00-00')
                      {
                        $received_date = str_replace('-', '/', $row['received_date']);
                        $received_date = date('d/m/Y', strtotime($received_date));
                      }







                      ?>

                    
                      <div class="form-group col-md-3">
                        <label for="sales_date">Due Date</label>
                        <input type="text" class="form-control datepicker" placeholder="Due Date" name="due_date" autocomplete="off" value="<?php echo $due_date ?>" readonly>
                      </div>

                      <div class="form-group col-md-3">
                        <label for="received_date">Received Date :</label>
                        <input type="text" class="form-control datepicker" placeholder="Received Date" name="received_date" autocomplete="off" value="<?php echo $received_date ?>">
                      </div>

                      <div class="form-group col-md-3">
                          <label for="due_days">Due Days</label>
                          <input type="text" class="form-control" placeholder="Due Days" name="due_days" value="<?php echo $due_days ?>" readonly>
                        </div>

                    </div>

                    <div class="row">
                      <div class="form-group col-md-6">

                        <?php $party = "select * from external_party where id='".$row['pur_party']."'";
                          $partyresult = mysqli_query($conn, $party);

                          $partyrow = mysqli_fetch_assoc($partyresult);

                          $ex_party='';
                          if(isset($partyrow))
                          {
                            $ex_party=$partyrow['partyname'];
                          }
                          ?>
                        <label for="pur_party"> Party</label>
                        <input type="text" class="form-control" name="pur_party" value="<?php echo $ex_party; ?>" readonly>
                      </div>
                      <div class="form-group col-md-6">
                        <label for="pur_invoice_no"> Invoice No</label>
                        <input type="text" class="form-control" name="pur_invoice_no" value="<?php echo $row['pur_invoice_no']; ?>" readonly>
                      </div>


                      
                       <div class="form-group col-md-4">
                              <label for="gross_amt">Gross Amount</label>
                              <input type="text" id="gross_amt" name="gross_amt" class="form-control" readonly="" value="<?php echo $row['gross_amt'] ?>">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="tax_amt">Tax Amount</label>
                              <input type="text" id="tax_amt" name="tax_amt" class="form-control" readonly="" value="<?php echo $row['tax_amt'] ?>">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="other_amt_tcs">Other Amount(TCS)</label>
                              <input type="text" id="other_amt_tcs" name="other_amt_tcs" class="form-control" readonly="" value="<?php echo $row['other_amt_tcs'] ?>">
                            </div>



                    </div>
                    <!-- Start -->
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                          <div class="">
                            <div class="form-group col-sm-8" style="margin: -14px !important;">
                              <br>
                              <input type="hidden" name="id" value="<?php echo$row['id']; ?>">
                              <input type="hidden" name="firm" value="<?php echo$row['firm']; ?>">

                              <label for="netpayableamt">Total Amount</label>
                              <input type="text" name="total_value" class="form-control" readonly="" id="total_value" value="<?php echo $row['total_value'] ?>">
                            </div>

                            <div class="form-group col-sm-8" style="margin: -14px !important;">
                              <br>
                              <label for="netpayableamt">Credit Amount</label>
                              <input type="text" name="credit_amt" class="form-control numericValidation" id="credit_amt" value="<?php echo $row['credit_amt'] ?>">
                            </div>

                            <div class="form-group col-sm-8" style="margin: -14px !important;">
                              <br>
                              <label for="netpayableamt">Net Amount</label>
                              <input type="text" name="net_amt" class="form-control" id="net_amt" readonly="" value="<?php echo $row['net_amt'] ?>">
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
                        <div class="col-lg-6 col-md-l-sm-6 col-xs-6">
                            <div class="">
                              <br>
                              <!-- Ad_Hoc -->
                              
                              <?php if ($row['adhoc_data'] != '' ) {

                                $adhoc_dataArr = json_decode($row['adhoc_data']);
                                ?>
                                <div class="row">
                                  <div class="form-group col-sm-6">
                                    <label for="ad_hoc">Ad-Hoc Amount</label>
                                  </div>

                                  <div class="form-group col-sm-6">
                                   <label for="ad_hoc" class="lablemove">Date</label>
                                  </div>
                                </div>
                                
                                
                                <?php 
                                $adhocArrCount=count($adhoc_dataArr);

                                if ($adhocArrCount>0) {
                                  foreach ($adhoc_dataArr as $key => $value) {?>
                                    
                                    <div class="row">
                                      <div class="form-group col-sm-6">    
                                        
                                        <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" value="<?php echo $value->adhoc_amount; ?>">
                                      </div>
                                      <?php 
                                      $ad_hocDate = '';
                                      if ($value->date != '') {
                                        $ad_hocDate = date("d/m/Y", strtotime($value->date)); 
                                      } ?>
                                      <div class="form-group col-sm-6">    
                                        <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" value="<?php echo $ad_hocDate; ?>">
                                      </div>
                                    </div>                                  

                                    <?php 
                                  } 
                                }else{?>

                                    <div class="row">
                                      <div class="form-group col-sm-6">    
                                        
                                        <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" placeholder="Ad-Hoc Amount">
                                      </div>

                                      <div class="form-group col-sm-6">    
                                        <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" placeholder="Date">
                                      </div>
                                    </div>


                                <?php }
                                ?>


                                <hr>
                              <?php }

                              ?>

                              <!-- Debit -->
                              <?php if ($row['debit_data'] != '' ) {

                                $debit_dataArr = json_decode($row['debit_data']);?>

                                <div class="row">
                                  <div class="form-group col-sm-6">
                                    <label for="debit_amount">Debit Amount</label>
                                  </div>

                                  <div class="form-group col-sm-6">
                                   <label for="date">Date</label>
                                  </div>
                                </div>
                                <?php
                                $debit_dataArrCount = count($debit_dataArr);
                                if ($debit_dataArrCount>0) {
                                  
                                
                                    foreach ($debit_dataArr as $key => $value) {?>
                                      
                                      <div class="row">
                                        <div class="form-group col-sm-6">    
                                          
                                          <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" value="<?php echo $value->debit_amount; ?>">
                                        </div>
                                        <?php 
                                        $debit_amtDate = '';
                                        if ($value->date != '') {
                                          $debit_amtDate = date("d/m/Y", strtotime($value->date)); 
                                        } ?>
                                        <div class="form-group col-sm-6">    
                                          <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" value="<?php echo $debit_amtDate; ?>">
                                        </div>
                                      </div>                                  
                                      <?php 
                                    } 
                                }
                                
                                else{?>

                                      <div class="row">
                                        <div class="form-group col-sm-6">    
                                          
                                          <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" placeholder="Debit Amount" value="">
                                        </div>

                                        <div class="form-group col-sm-6">    
                                          <input type="text" class="form-control" name="ad_hoc" placeholder="Date" id="ad_hoc" value="">
                                        </div>
                                      </div>

                                <?php }
                                ?>
                                <hr><?php 
                              }?>

                              <!-- GST -->
                              <?php if ($row['gst_data'] != '' ) {

                                $gst_dataArr = json_decode($row['gst_data']);
                                ?>

                                <div class="row">
                                  <div class="form-group col-sm-6">
                                    <label for="gst_amount">GST Amount</label>
                                  </div>

                                  <div class="form-group col-sm-6">
                                   <label for="date">Date</label>
                                  </div>
                                </div>
                                <?php
                                $gst_dataArrCount = count($gst_dataArr);
                                if ($gst_dataArrCount>0) {
                                  
                                
                                  foreach ($gst_dataArr as $key => $value) {?>
                                    
                                    <div class="row">
                                      <div class="form-group col-sm-6">    
                                        <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" value="<?php echo $value->gst_amount; ?>">
                                      </div>
                                      <?php 
                                      $gst_amtDate = '';
                                      if ($value->date != '') {
                                        $gst_amtDate = date("d/m/Y", strtotime($value->date)); 
                                      } ?>
                                      <div class="form-group col-sm-6">    
                                       
                                        <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" value="<?php echo $gst_amtDate; ?>">
                                      </div>
                                    </div>                                  
                                    <?php 
                                  } 
                                }

                                else{?>

                                    <div class="row">
                                      <div class="form-group col-sm-6">    
                                        <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" placeholder="GST Amount">
                                      </div>

                                      <div class="form-group col-sm-6">    
                                       
                                        <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" placeholder="Date">
                                      </div>
                                    </div>  

                                <?php }
                                ?><hr><?php 
                              }?>

                              <!-- TCS -->

                              <?php if ($row['tcs_data'] != '' ) {

                                $tcs_dataArr = json_decode($row['tcs_data']);
                                ?>

                                <div class="row">
                                  <div class="form-group col-sm-6">
                                    <label for="tcs_amount">TCS Amount</label>
                                  </div>

                                  <div class="form-group col-sm-6">
                                   <label for="date">Date</label>
                                  </div>
                                </div>
                                <?php
                                  $tcs_dataArrCount = count($tcs_dataArr);
                                  if ($tcs_dataArrCount>0) {
                                    
                                  
                                    foreach ($tcs_dataArr as $key => $value) {?>
                                      
                                      <div class="row">
                                        <div class="form-group col-sm-6">    
                                         
                                          <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" value="<?php echo $value->tcs_amount; ?>">
                                        </div>
                                        <?php 
                                        $tcs_amtDate = '';
                                        if ($value->date != '') {
                                          $tcs_amtDate = date("d/m/Y", strtotime($value->date)); 
                                        } ?>
                                        <div class="form-group col-sm-6">    
                                         
                                          <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" value="<?php echo $tcs_amtDate; ?>">
                                        </div>
                                      </div>                                  
                                      <?php 
                                    } 
                                  }else{?>

                                      <div class="row">
                                        <div class="form-group col-sm-6">    
                                         
                                          <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" placeholder="TCS Amount">
                                        </div>

                                        <div class="form-group col-sm-6">    
                                         
                                          <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" placeholder="Date">
                                        </div>
                                      </div> 
                                  <?php }
                                ?><hr><?php
                              }?>

                              <!-- TDS -->

                              <?php if ($row['tds_data'] != '' ) {

                                $tds_dataArr = json_decode($row['tds_data']);


                                ?>

                                <div class="row">
                                  <div class="form-group col-sm-6">
                                    <label for="tds_amount">TDS Amount</label>
                                  </div>

                                  <div class="form-group col-sm-6">
                                   <label for="date">Date</label>
                                  </div>
                                </div>
                                <?php
                                $tds_dataArrCount = count($tds_dataArr);
                                if ($tds_dataArrCount>0) {
                                   
                                 
                                    foreach ($tds_dataArr as $key => $value)


                                     {?>


                                      
                                      <div class="row">
                                        <div class="form-group col-sm-6">    
                                         
                                          <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" value="<?php echo $value->tds_amount; ?>">
                                        </div>
                                        <?php 
                                        $tds_amtDate = '';
                                        if ($value->date != '') {
                                          $tds_amtDate = date("d/m/Y", strtotime($value->date)); 
                                        } ?>
                                        <div class="form-group col-sm-6">    
                                         
                                          <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" value="<?php echo $tds_amtDate; ?>">
                                        </div>
                                      </div>                                  
                                      <?php 
                                    }
                                }else{?>

                                  <div class="row">
                                        <div class="form-group col-sm-6">    
                                         
                                          <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" placeholder="TDS Amount">
                                        </div>

                                        <div class="form-group col-sm-6">    
                                         
                                          <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" placeholder="Date">
                                        </div>
                                      </div> 

                                <?php }
                                ?><hr><?php 
                              }?>

                              <!-- OTHER -->

                              <?php if ($row['other_data'] != '' ) {

                                $other_dataArr = json_decode($row['other_data']);
                                ?>

                                <div class="row">
                                  <div class="form-group col-sm-6">
                                    <label for="other_amount">Other Amount</label>
                                  </div>

                                  <div class="form-group col-sm-6">
                                   <label for="date">Date</label>
                                  </div>
                                </div>
                                <?php
                                $other_dataArrCount = count($other_dataArr);
                                if ($other_dataArrCount>0) {

                                  foreach ($other_dataArr as $key => $value) {?>
                                  
                                    <div class="row">
                                      <div class="form-group col-sm-6">    
                                        <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" value="<?php echo $value->other_amount; ?>">
                                      </div>
                                      <?php 
                                      $other_amtDate = '';
                                      if ($value->date != '') {
                                      $other_amtDate = date("d/m/Y", strtotime($value->date)); 
                                      } ?>
                                      <div class="form-group col-sm-6">
                                        <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" value="<?php echo $other_amtDate; ?>">
                                      </div>
                                    </div>                                  
                                    <?php 
                                  }
                                  
                                }else

                                {?>

                                  <div class="row">
                                      <div class="form-group col-sm-6">    
                                        <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" placeholder="Other Amount">
                                      </div>
                                      <div class="form-group col-sm-6">
                                        <input type="text" class="form-control" name="ad_hoc" id="ad_hoc" placeholder="Date">
                                      </div>
                                    </div>
                                <?php 
                                }
                                 
                              }?>
                                     <hr>

                 
                  <label for="b2bSelect"><b>Bill 2 Bill Payment</b></label>

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
                              
                            
                            </div>
                          <?php
                           }
                         }
                      }
                    ?>


                    </div>
                    <br>
                                <div class="row">
                                <div class="form-group col-sm-6">
                                  <label for="total_received">Total Received Amount</label>
                                  <input type="text" class="form-control" name="total_received" id="total_received" value="<?php echo $row['total_received'] ?>" readonly="">
                                </div>

                                <div class="form-group col-sm-6">
                                  <label for="OSAmount">Out-Standing Amount</label>
                                  <input type="text" class="form-control" name="OSAmount" id="OSAmount" value="<?php echo $row['OSAmount'] ?>" readonly="">
                                </div>
                                </div>

                                <div class="row">

                                  <div class="form-group col-sm-12">
                              <label for="netpayableamt">Net Amount</label>
                              <input type="text" name="net_amt" class="form-control" id="net_amt" readonly="" value="<?php echo $row['net_amt'] ?>">
                            </div>

                                </div>       

                              <div class="">
                                
                              
                              </div>
                             
                            </div>
                        </div>
                      </div>

                    <!--End   -->
                      
                      
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
         
    <script>

      $(document).ready(function(){

        $('input[type="text"]').prop('readonly', true);
        
        $('.numericValidation').keypress(function(event){
            if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
              event.preventDefault();
            }
          });

          $('#credit_amt').keyup(function() {
            var total_value=parseInt($("#total_value").val());
            var credit_amt=parseInt($("#credit_amt").val());
            if (isNaN(total_value)) {
              total_value = 0;
            }
            if (isNaN(credit_amt)) {
              credit_amt = 0;
            }
            var netAmount = parseInt(total_value)+parseInt(credit_amt);
            $("#net_amt").val(netAmount);

            var ad_hoc=parseInt($("#ad_hoc").val());
            var debit_amount=parseInt($("#debit_amount").val());
            var gst_amount=parseInt($("#gst_amount").val());
            var tcs_amount=parseInt($("#tcs_amount").val());
            var tds_amount=parseInt($("#tds_amount").val());
            var other_amount=parseInt($("#other_amount").val());

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
            var TotalAmount = parseInt(ad_hoc)+parseInt(debit_amount)+parseInt(gst_amount)+parseInt(tcs_amount)+parseInt(tds_amount)+parseInt(other_amount);
            $("#total_received").val(TotalAmount);
            var net_amt =  $("#net_amt").val();
            if (isNaN(net_amt)) {
              net_amt = 0;
            }
            var OutStandingAmt = parseInt(net_amt)-parseInt(TotalAmount);
            $("#OSAmount").val(OutStandingAmt); 

          });

          // ad_hoc
          $('#ad_hoc').keyup(function() {

            var credit_amt=parseInt($("#credit_amt").val());
            if (isNaN(credit_amt)) {

              $("#ad_hoc").val('');
              alert('Please Select Credit Amount');
            }

            var ad_hoc=parseInt($("#ad_hoc").val());
            var debit_amount=parseInt($("#debit_amount").val());
            var gst_amount=parseInt($("#gst_amount").val());
            var tcs_amount=parseInt($("#tcs_amount").val());
            var tds_amount=parseInt($("#tds_amount").val());
            var other_amount=parseInt($("#other_amount").val());

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
            var TotalAmount = parseInt(ad_hoc)+parseInt(debit_amount)+parseInt(gst_amount)+parseInt(tcs_amount)+parseInt(tds_amount)+parseInt(other_amount);
            $("#total_received").val(TotalAmount);
            var net_amt =  $("#net_amt").val();
            if (isNaN(net_amt)) {
              net_amt = 0;
            }
            var OutStandingAmt = parseInt(net_amt)-parseInt(TotalAmount);
            $("#OSAmount").val(OutStandingAmt); 
          });
          // debit_amount
          $('#debit_amount').keyup(function() {
            var credit_amt=parseInt($("#credit_amt").val());
            if (isNaN(credit_amt)) {

              alert('Please Select Credit Amount');

            }
            var ad_hoc=parseInt($("#ad_hoc").val());
            var debit_amount=parseInt($("#debit_amount").val());
            var gst_amount=parseInt($("#gst_amount").val());
            var tcs_amount=parseInt($("#tcs_amount").val());
            var tds_amount=parseInt($("#tds_amount").val());
            var other_amount=parseInt($("#other_amount").val());

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
            var TotalAmount = parseInt(ad_hoc)+parseInt(debit_amount)+parseInt(gst_amount)+parseInt(tcs_amount)+parseInt(tds_amount)+parseInt(other_amount);
            $("#total_received").val(TotalAmount);

            var net_amt =  $("#net_amt").val();
            if (isNaN(net_amt)) {
              net_amt = 0;
            }
            var OutStandingAmt = parseInt(net_amt)-parseInt(TotalAmount);
            $("#OSAmount").val(OutStandingAmt);

          });

          // gst_amount
          $('#gst_amount').keyup(function() {
            var credit_amt=parseInt($("#credit_amt").val());
            if (isNaN(credit_amt)) {

              alert('Please Select Credit Amount');

            }
            var ad_hoc=parseInt($("#ad_hoc").val());
            var debit_amount=parseInt($("#debit_amount").val());
            var gst_amount=parseInt($("#gst_amount").val());
            var tcs_amount=parseInt($("#tcs_amount").val());
            var tds_amount=parseInt($("#tds_amount").val());
            var other_amount=parseInt($("#other_amount").val());

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
            var TotalAmount = parseInt(ad_hoc)+parseInt(debit_amount)+parseInt(gst_amount)+parseInt(tcs_amount)+parseInt(tds_amount)+parseInt(other_amount);
            $("#total_received").val(TotalAmount);

            var net_amt =  $("#net_amt").val();
            if (isNaN(net_amt)) {
              net_amt = 0;
            }
            var OutStandingAmt = parseInt(net_amt)-parseInt(TotalAmount);
            $("#OSAmount").val(OutStandingAmt);


          });

          // tcs_amount
          $('#tcs_amount').keyup(function() {

            var credit_amt=parseInt($("#credit_amt").val());
            if (isNaN(credit_amt)) {

              alert('Please Select Credit Amount');

            }

            var ad_hoc=parseInt($("#ad_hoc").val());
            var debit_amount=parseInt($("#debit_amount").val());
            var gst_amount=parseInt($("#gst_amount").val());
            var tcs_amount=parseInt($("#tcs_amount").val());
            var tds_amount=parseInt($("#tds_amount").val());
            var other_amount=parseInt($("#other_amount").val());

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
            var TotalAmount = parseInt(ad_hoc)+parseInt(debit_amount)+parseInt(gst_amount)+parseInt(tcs_amount)+parseInt(tds_amount)+parseInt(other_amount);
            $("#total_received").val(TotalAmount);
            var net_amt =  $("#net_amt").val();
            if (isNaN(net_amt)) {
              net_amt = 0;
            }
            var OutStandingAmt = parseInt(net_amt)-parseInt(TotalAmount);
            $("#OSAmount").val(OutStandingAmt); 
          });

          // tds_amount
          $('#tds_amount').keyup(function() {

            var credit_amt=parseInt($("#credit_amt").val());
            if (isNaN(credit_amt)) {

              alert('Please Select Credit Amount');

            }

            var ad_hoc=parseInt($("#ad_hoc").val());
            var debit_amount=parseInt($("#debit_amount").val());
            var gst_amount=parseInt($("#gst_amount").val());
            var tcs_amount=parseInt($("#tcs_amount").val());
            var tds_amount=parseInt($("#tds_amount").val());
            var other_amount=parseInt($("#other_amount").val());

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
            var TotalAmount = parseInt(ad_hoc)+parseInt(debit_amount)+parseInt(gst_amount)+parseInt(tcs_amount)+parseInt(tds_amount)+parseInt(other_amount);
            $("#total_received").val(TotalAmount);

            var net_amt =  $("#net_amt").val();
            if (isNaN(net_amt)) {
              net_amt = 0;
            }
            var OutStandingAmt = parseInt(net_amt)-parseInt(TotalAmount);
            $("#OSAmount").val(OutStandingAmt); 
          });

          // other_amount
          $('#other_amount').keyup(function() {

            var credit_amt=parseInt($("#credit_amt").val());
            if (isNaN(credit_amt)) {

              alert('Please Select Credit Amount');

            }

            var ad_hoc=parseInt($("#ad_hoc").val());
            var debit_amount=parseInt($("#debit_amount").val());
            var gst_amount=parseInt($("#gst_amount").val());
            var tcs_amount=parseInt($("#tcs_amount").val());
            var tds_amount=parseInt($("#tds_amount").val());
            var other_amount=parseInt($("#other_amount").val());

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
            var TotalAmount = parseInt(ad_hoc)+parseInt(debit_amount)+parseInt(gst_amount)+parseInt(tcs_amount)+parseInt(tds_amount)+parseInt(other_amount);
            $("#total_received").val(TotalAmount);

            var net_amt =  $("#net_amt").val();
            if (isNaN(net_amt)) {
              net_amt = 0;
            }
            var OutStandingAmt = parseInt(net_amt)-parseInt(TotalAmount);
            $("#OSAmount").val(OutStandingAmt); 
          });

      });

    </script>
  </body>
</html>
