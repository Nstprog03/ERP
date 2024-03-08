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
$getYear=$_SESSION['sales_conf_financial_year'];
$year_array=explode("/",$getYear);
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



if(isset($_POST['Submit'])){

  $id = $_GET['id'];


  $total_value = $_POST['total_value'];
  $credit_amt = $_POST['credit_amt'];
  $net_amt = $_POST['net_amt'];



  $adhoc_data = array();
  $debit_data = array();
  $gst_data= array();
  $tcs_data = array();
  $tds_data = array();
  $other_data = array();

  $adhoc_amount = $_POST['adhoc_amount'];
  $debit_amount = $_POST['debit_amount'];
  $gst_amount = $_POST['gst_amount'];
  $tcs_amount = $_POST['tcs_amount'];
  $tds_amount = $_POST['tds_amount'];
  $other_amount = $_POST['other_amount'];

  $adhoc_date = $_POST['adhoc_date'];
  $debit_date = $_POST['debit_date'];
  $gst_date = $_POST['gst_date'];
  $tcs_date = $_POST['tcs_date'];
  $tds_date = $_POST['tds_date'];
  $other_date = $_POST['other_date'];

        //adhoc
  foreach ($adhoc_amount as $key => $value) 
  {
    if($value!='')
    {
      $date = '';
      if($adhoc_date[$key]!='')
      {
        $date = str_replace('/', '-', $adhoc_date[$key]);
        $date = date('Y-m-d', strtotime($date));
      }
      $adhoc_data[$key]['adhoc_amount']=$value;
      $adhoc_data[$key]['date']=$date;
    }
    
  }

  //debit amount
  foreach ($debit_amount as $key => $value) 
  {
    if($value!='')
    {
      $date = '';
      if($debit_date[$key]!='')
      {
        $date = str_replace('/', '-', $debit_date[$key]);
        $date = date('Y-m-d', strtotime($date));
      }
      $debit_data[$key]['debit_amount']=$value;
      $debit_data[$key]['date']=$date;
    }
    
  }

  //gst amount
  foreach ($gst_amount as $key => $value) 
  {
    if($value!='')
    {
      $date = '';
      if($gst_date[$key]!='')
      {
        $date = str_replace('/', '-', $gst_date[$key]);
        $date = date('Y-m-d', strtotime($date));
      }
      $gst_data[$key]['gst_amount']=$value;
      $gst_data[$key]['date']=$date;
    }
    
  }

  //tcs amount
  foreach ($tcs_amount as $key => $value) 
  {
    if($value!='')
    {
      $date = '';
      if($tcs_date[$key]!='')
      {
        $date = str_replace('/', '-', $tcs_date[$key]);
        $date = date('Y-m-d', strtotime($date));
      }
      $tcs_data[$key]['tcs_amount']=$value;
      $tcs_data[$key]['date']=$date;
    }
    
  }

  //tds amount
  foreach ($tds_amount as $key => $value) 
  {
    if($value!='')
    {
      $date = '';
      if($tds_date[$key]!='')
      {
        $date = str_replace('/', '-', $tds_date[$key]);
        $date = date('Y-m-d', strtotime($date));
      }
      $tds_data[$key]['tds_amount']=$value;
      $tds_data[$key]['date']=$date;
    }
    
  }

  //other amount
  foreach ($other_amount as $key => $value) 
  {
    if($value!='')
    {
      $date = '';
      if($other_date[$key]!='')
      {
        $date = str_replace('/', '-', $other_date[$key]);
        $date = date('Y-m-d', strtotime($date));
      }
      $other_data[$key]['other_amount']=$value;
      $other_data[$key]['date']=$date;
    }
    
  }




  $adhoc_data=json_encode($adhoc_data);
  $debit_data=json_encode($debit_data);
  $gst_data=json_encode($gst_data);
  $tcs_data=json_encode($tcs_data);
  $tds_data=json_encode($tds_data);
  $other_data=json_encode($other_data);


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




  date_default_timezone_set('Asia/Kolkata');
  $timestamp=date("Y-m-d H:i:s");
  $username=$_SESSION['username'];

  $total_received = $_POST['total_received'];
  $OSAmount = $_POST['OSAmount'];


  $due_date='';
  $received_date='';
  if($_POST['due_date']!='')
  {
    $due_date = str_replace('/', '-', $_POST['due_date']);
    $due_date = date('Y-m-d', strtotime($due_date));
  }
  if($_POST['received_date']!='')
  {
    $received_date = str_replace('/', '-', $_POST['received_date']);
    $received_date = date('Y-m-d', strtotime($received_date));
  }

  $credit_days=$_POST['credit_days'];



  if(!isset($errorMsg)){
   $sql = "update sales_rcvble
   set           
   total_value = '".$total_value."',
   credit_amt = '".$credit_amt."',
   net_amt = '".$net_amt."',
   adhoc_data = '".$adhoc_data."',
   debit_data = '".$debit_data."',
   gst_data = '".$gst_data."',                    
   tcs_data = '".$tcs_data."',
   tds_data = '".$tds_data."',
   other_data = '".$other_data."',
   bill2bill_dynamic_data= '".$b2bArr."',
   total_received = '".$total_received."',
   OSAmount = '".$OSAmount."',                   
   credit_days = '".$credit_days."',
   due_date = '".$due_date."',
   received_date = '".$received_date."',
   username = '".$username."',
   update_at = '".$timestamp."'


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
  }
}

}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sales Recievable Database Edit</title>
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Sales Recievable Database Edit</span></a>
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
              Sales Recievable Database edit
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
                  <input type="text" class="form-control" placeholder="Due Date" name="due_date" autocomplete="off" value="<?php echo $due_date ?>" readonly>
                </div>

                <div class="form-group col-md-3">
                  <label for="received_date">Select Received Date :</label>
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
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                  <div class="">
                    <div class="form-group col-sm-12" style="margin: -14px !important;">
                      <br>
                      <input type="hidden" name="firm" value="<?php echo$result3['firm']; ?>">
                      <label for="netpayableamt">Total Amount</label>
                      <input type="hi" name="total_value" class="form-control" readonly="" id="total_value" value="<?php echo round($row['total_value']) ?>">
                    </div>

                    <div class="form-group col-sm-12" style="margin: -14px !important;">
                      <br>
                      <label for="netpayableamt">Credit Amount</label>
                      <input type="text" name="credit_amt" class="form-control" onkeypress="return NumericValidate(event,this)" id="credit_amt" value="<?php echo $row['credit_amt'] ?>">
                    </div>

                    <div class="form-group col-sm-12" style="margin: -14px !important;">
                      <br>
                      <label for="netpayableamt">Net Amount</label>
                      <input type="text" name="net_amt" class="form-control" id="net_amt" value="<?php echo $row['net_amt'] ?>" readonly="">
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

                      <?php
                      $adhocArr=json_decode($row['adhoc_data']);
                      $adhocArrCount=count($adhocArr);

                      //if adhoc have data
                      if($adhocArrCount>0)
                      {
                        foreach ($adhocArr as $key => $value) 
                        {
                          $key=$key+1;

                          $ad_hocDate='';
                          if($value->date != '') 
                          {
                            $ad_hocDate = date("d/m/Y", strtotime($value->date)); 
                          }   

                          //if key is One
                          if($key==1)
                          {


                            ?>
                            <div class="row">
                              <div class="form-group col-sm-5">    
                                <label for="ad_hoc">Ad-Hoc Amount</label>
                                <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control adhocAmt<?php echo $key ?>" name="adhoc_amount[]" placeholder="Enter Ad-Hoc Amount" value="<?php echo $value->adhoc_amount  ?>">
                              </div>
                              <div class="form-group col-sm-5">    
                                <label for="adhoc_date">Date</label>
                                <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker" name="adhoc_date[]" placeholder="Enter Date" autocomplete="off" value="<?php echo $ad_hocDate  ?>">
                              </div>

                              <div class="form-group col-sm-2" style="margin-top: 30px;">
                                <button type="button" class=" btn btn-primary adhoc_add_button"> +</button>
                              </div>
                            </div>



                            <?php
                          }
                          else 
                          { //if key is not ONE
                            ?>

                            <div class="row">
                              <div class="form-group col-sm-5">    
                                <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control adhocAmt<?php echo $key ?>" name="adhoc_amount[]" placeholder="Enter Ad-Hoc Amount" value="<?php echo $value->adhoc_amount  ?>">
                              </div>
                              <div class="form-group col-sm-5">    
                                <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker"  name="adhoc_date[]" placeholder="Enter Date" autocomplete="off" value="<?php echo $ad_hocDate  ?>">
                              </div>



                              <div class="form-group col-md-2"><a href="javascript:void(0);" class="btn btn-danger adhoc_remove_btn">-</a></div>
                            </div>

                            

                            <?php
                          }

                        }
                      }
                      else  //if not data available in adhoc_data
                      {
                        ?>
                        <div class="row">
                          <div class="form-group col-sm-5">    
                            <label for="ad_hoc">Ad-Hoc Amount</label>
                            <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control adhocAmt1" name="adhoc_amount[]" placeholder="Enter Ad-Hoc Amount">
                          </div>
                          <div class="form-group col-sm-5">    
                            <label for="adhoc_date">Date</label>
                            <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker2" name="adhoc_date[]" placeholder="Enter Date">
                          </div>

                          <div class="form-group col-sm-2" style="margin-top: 30px;">
                            <button type="button" class=" btn btn-primary adhoc_add_button"> +</button>
                          </div>
                        </div>
                        <?php
                      }
                      ?>

                    </div>



                    <div class="dynamicDebit"> 

                      <?php
                      $debitArr=json_decode($row['debit_data']);
                      $debitArrCount=count($debitArr);

                      //if debit have data
                      if($debitArrCount>0)
                      {
                        foreach ($debitArr as $key => $value) 
                        {
                          $key=$key+1;

                          $debitDate='';
                          if($value->date != '') 
                          {
                            $debitDate = date("d/m/Y", strtotime($value->date)); 
                          }   

                          //if key is One
                          if($key==1)
                          {                                
                            ?>
                            <div class="row">
                              <div class="form-group col-sm-5">    
                                <label for="debit_amount">Debit Note Amount</label>
                                <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control debitAmt<?php echo $key ?>" name="debit_amount[]" placeholder="Enter Debit Note Amount" value="<?php echo $value->debit_amount  ?>">
                              </div>
                              <div class="form-group col-sm-5">    
                                <label for="debit_date">Date</label>
                                <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker" name="debit_date[]" placeholder="Enter Date" autocomplete="off" value="<?php echo $debitDate  ?>">
                              </div>

                              <div class="form-group col-sm-2" style="margin-top: 30px;">
                                <button type="button" class=" btn btn-primary debit_add_button"> +</button>
                              </div>
                            </div>

                            <?php
                          }
                          else 
                          { //if key is not ONE
                            ?>

                            <div class="row">
                              <div class="form-group col-sm-5">    
                                <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control debitAmt<?php echo $key ?>" name="debit_amount[]" placeholder="Enter Debit Note Amount" value="<?php echo $value->debit_amount  ?>">
                              </div>
                              <div class="form-group col-sm-5">    
                               <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker" name="debit_date[]" placeholder="Enter Date" autocomplete="off" value="<?php echo $debitDate  ?>">
                             </div>                              

                             <div class="form-group col-md-2"><a href="javascript:void(0);" class="btn btn-danger debit_remove_btn">-</a></div>
                           </div>       

                           <?php
                         }

                       }
                     }
                      else  //if not data available in debit_data
                      {
                        ?>
                        <div class="row">
                          <div class="form-group col-sm-5">    
                           <label for="debit_amount">Debit Note Amount </label>
                           <input type="text" class="form-control debitAmt1" name="debit_amount[]" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" placeholder="Enter Debit Note Amount">
                         </div>
                         <div class="form-group col-sm-5">    
                          <label for="debit_date">Date</label>
                          <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker2" name="debit_date[]" placeholder="Enter Date">
                        </div>

                        <div class="form-group col-sm-2" style="margin-top: 30px;">
                          <button type="button" class=" btn btn-primary debit_add_button"> +</button>
                        </div>
                      </div>
                      <?php
                    }
                    ?>

                  </div>




                  <div class="dynamicGST"> 

                    <?php
                    $GstArr=json_decode($row['gst_data']);
                    $GstArrCount=count($GstArr);

                      //if gst have data
                    if($GstArrCount>0)
                    {
                      foreach ($GstArr as $key => $value) 
                      {
                        $key=$key+1;

                        $gstDate='';
                        if($value->date != '') 
                        {
                          $gstDate = date("d/m/Y", strtotime($value->date)); 
                        }   

                          //if key is One
                        if($key==1)
                        {                                
                          ?>
                          <div class="row">
                            <div class="form-group col-sm-5">    
                              <label for="gst_amount">GST Amount</label>
                              <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control debitAmt<?php echo $key ?>" name="gst_amount[]" placeholder="Enter GST Amount" value="<?php echo $value->gst_amount  ?>">
                            </div>
                            <div class="form-group col-sm-5">    
                              <label for="gst_date">Date</label>
                              <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker" name="gst_date[]" placeholder="Enter Date" autocomplete="off" value="<?php echo $gstDate  ?>">
                            </div>

                            <div class="form-group col-sm-2" style="margin-top: 30px;">
                              <button type="button" class=" btn btn-primary gst_add_button"> +</button>
                            </div>
                          </div>

                          <?php
                        }
                        else 
                          { //if key is not ONE
                            ?>

                            <div class="row">
                              <div class="form-group col-sm-5">    
                               <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control debitAmt<?php echo $key ?>" name="gst_amount[]" placeholder="Enter GST Amount" value="<?php echo $value->gst_amount  ?>">
                             </div>
                             <div class="form-group col-sm-5">    
                              <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker" name="gst_date[]" placeholder="Enter Date" autocomplete="off" value="<?php echo $gstDate  ?>">
                            </div>                              

                            <div class="form-group col-md-2"><a href="javascript:void(0);" class="btn btn-danger gst_remove_btn">-</a></div>
                          </div>       

                          <?php
                        }

                      }
                    }
                      else  //if not data available in gst_data
                      {
                        ?>
                        <div class="row">
                          <div class="form-group col-sm-5">    
                           <label for="gst_amount">GST Amount </label>
                           <input type="text" class="form-control gstAmt1" name="gst_amount[]" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" placeholder="Enter GST Amount">
                         </div>
                         <div class="form-group col-sm-5">    
                          <label for="gst_date">Date</label>
                          <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker2" name="gst_date[]" placeholder="Enter Date">
                        </div>

                        <div class="form-group col-sm-2" style="margin-top: 30px;">
                          <button type="button" class=" btn btn-primary gst_add_button"> +</button>
                        </div>
                      </div>
                      <?php
                    }
                    ?>

                  </div>



                  <div class="dynamicTCS"> 

                    <?php
                    $TcsArr=json_decode($row['tcs_data']);
                    $TcsArrCount=count($TcsArr);

                      //if tcs have data
                    if($TcsArrCount>0)
                    {
                      foreach ($TcsArr as $key => $value) 
                      {
                        $key=$key+1;

                        $TcsDate='';
                        if($value->date != '') 
                        {
                          $TcsDate = date("d/m/Y", strtotime($value->date)); 
                        }   

                          //if key is One
                        if($key==1)
                        {                                
                          ?>
                          <div class="row">
                            <div class="form-group col-sm-5">    
                              <label for="tcs_amount">TCS Amount</label>
                              <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control tcsAmt<?php echo $key ?>" name="tcs_amount[]" placeholder="Enter TCS Amount" value="<?php echo $value->tcs_amount  ?>">
                            </div>
                            <div class="form-group col-sm-5">    
                              <label for="tcs_date">Date</label>
                              <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker" name="tcs_date[]" placeholder="Enter Date" autocomplete="off" value="<?php echo $TcsDate  ?>">
                            </div>

                            <div class="form-group col-sm-2" style="margin-top: 30px;">
                              <button type="button" class=" btn btn-primary tcs_add_button"> +</button>
                            </div>
                          </div>

                          <?php
                        }
                        else 
                          { //if key is not ONE
                            ?>

                            <div class="row">
                              <div class="form-group col-sm-5">    
                                <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control tcsAmt<?php echo $key ?>" name="tcs_amount[]" placeholder="Enter TCS Amount" value="<?php echo $value->tcs_amount  ?>">
                              </div>
                              <div class="form-group col-sm-5">    
                               <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker" name="tcs_date[]" placeholder="Enter Date" autocomplete="off" value="<?php echo $TcsDate  ?>">
                             </div>                              

                             <div class="form-group col-md-2"><a href="javascript:void(0);" class="btn btn-danger tcs_remove_btn">-</a></div>
                           </div>       

                           <?php
                         }

                       }
                     }
                      else  //if not data available in tcs_data
                      {
                        ?>
                        <div class="row">
                          <div class="form-group col-sm-5">    
                           <label for="tcs_amount">TCS Amount </label>
                           <input type="text" class="form-control tcsAmt1" name="tcs_amount[]" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" placeholder="Enter TCS Amount">
                         </div>
                         <div class="form-group col-sm-5">    
                          <label for="tcs_date">Date</label>
                          <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker2" name="tcs_date[]" placeholder="Enter Date">
                        </div>

                        <div class="form-group col-sm-2" style="margin-top: 30px;">
                          <button type="button" class=" btn btn-primary tcs_add_button"> +</button>
                        </div>
                      </div>
                      <?php
                    }
                    ?>

                  </div>



                  <div class="dynamicTDS"> 

                    <?php
                    $tdsArr=json_decode($row['tds_data']);
                    $tdsArrCount=count($tdsArr);

                      //if tds have data
                    if($tdsArrCount>0)
                    {
                      foreach ($tdsArr as $key => $value) 
                      {
                        $key=$key+1;

                        $tdsDate='';
                        if($value->date != '') 
                        {
                          $tdsDate = date("d/m/Y", strtotime($value->date)); 
                        }   

                          //if key is One
                        if($key==1)
                        {                                
                          ?>
                          <div class="row">
                            <div class="form-group col-sm-5">    
                              <label for="tds_amount">TDS Amount</label>
                              <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control tdsAmt<?php echo $key ?>" name="tds_amount[]" placeholder="Enter TDS Amount" value="<?php echo $value->tds_amount  ?>">
                            </div>
                            <div class="form-group col-sm-5">    
                              <label for="tds_date">Date</label>
                              <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker" name="tds_date[]" placeholder="Enter Date" autocomplete="off" value="<?php echo $tdsDate  ?>">
                            </div>

                            <div class="form-group col-sm-2" style="margin-top: 30px;">
                              <button type="button" class=" btn btn-primary tds_add_button"> +</button>
                            </div>
                          </div>

                          <?php
                        }
                        else 
                          { //if key is not ONE
                            ?>

                            <div class="row">
                              <div class="form-group col-sm-5">    
                                <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control tcsAmt<?php echo $key ?>" name="tds_amount[]" placeholder="Enter TDS Amount" value="<?php echo $value->tds_amount  ?>">
                              </div>
                              <div class="form-group col-sm-5">    
                                <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker" name="tds_date[]" placeholder="Enter Date" autocomplete="off" value="<?php echo $tdsDate  ?>">
                              </div>                              

                              <div class="form-group col-md-2"><a href="javascript:void(0);" class="btn btn-danger tds_remove_btn">-</a></div>
                            </div>       

                            <?php
                          }

                        }
                      }
                      else  //if not data available in tds_data
                      {
                        ?>
                        <div class="row">
                          <div class="form-group col-sm-5">    
                           <label for="tds_amount">TDS Amount </label>
                           <input type="text" class="form-control tdsAmt1" name="tds_amount[]" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" placeholder="Enter TDS Amount">
                         </div>
                         <div class="form-group col-sm-5">    
                          <label for="tds_date">Date</label>
                          <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker2" name="tds_date[]" placeholder="Enter Date">
                        </div>

                        <div class="form-group col-sm-2" style="margin-top: 30px;">
                          <button type="button" class=" btn btn-primary tds_add_button"> +</button>
                        </div>
                      </div>
                      <?php
                    }
                    ?>

                  </div>



                  <div class="dynamicOther"> 

                    <?php
                    $otherArr=json_decode($row['other_data']);
                    $otherArrCount=count($otherArr);

                      //if other have data
                    if($otherArrCount>0)
                    {
                      foreach ($otherArr as $key => $value) 
                      {
                        $key=$key+1;

                        $otherDate='';
                        if($value->date != '') 
                        {
                          $otherDate = date("d/m/Y", strtotime($value->date)); 
                        }   

                          //if key is One
                        if($key==1)
                        {                                
                          ?>
                          <div class="row">
                            <div class="form-group col-sm-5">    
                              <label for="other_amount">Other Amount</label>
                              <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control otherAmt<?php echo $key ?>" name="other_amount[]" placeholder="Enter Other Amount" value="<?php echo $value->other_amount  ?>">
                            </div>
                            <div class="form-group col-sm-5">    
                              <label for="other_date">Date</label>
                              <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker" name="other_date[]" placeholder="Enter Date" autocomplete="off" value="<?php echo $otherDate  ?>">
                            </div>

                            <div class="form-group col-sm-2" style="margin-top: 30px;">
                              <button type="button" class=" btn btn-primary other_add_button"> +</button>
                            </div>
                          </div>

                          <?php
                        }
                        else 
                          { //if key is not ONE
                            ?>

                            <div class="row">
                              <div class="form-group col-sm-5">    
                               <input type="text" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" class="form-control otherAmt<?php echo $key ?>" name="other_amount[]" placeholder="Enter Other Amount" value="<?php echo $value->other_amount  ?>">
                             </div>
                             <div class="form-group col-sm-5">    
                              <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker" name="other_date[]" placeholder="Enter Date" autocomplete="off" value="<?php echo $otherDate  ?>">
                            </div>                              

                            <div class="form-group col-md-2"><a href="javascript:void(0);" class="btn btn-danger other_remove_btn">-</a></div>
                          </div>       

                          <?php
                        }

                      }
                    }
                      else  //if not data available in other_data
                      {
                        ?>
                        <div class="row">
                          <div class="form-group col-sm-5">    
                           <label for="other_amount">Other Amount </label>
                           <input type="text" class="form-control otherAmt1" name="other_amount[]" onkeyup="calulcation()" onkeypress="return NumericValidate(event,this)" placeholder="Enter Other Amount">
                         </div>
                         <div class="form-group col-sm-5">    
                          <label for="other_date">Date</label>
                          <input type="text" onkeypress="return NumericValidate(event,this)" class="form-control datepicker2" name="other_date[]" placeholder="Enter Date">
                        </div>

                        <div class="form-group col-sm-2" style="margin-top: 30px;">
                          <button type="button" class=" btn btn-primary other_add_button"> +</button>
                        </div>
                      </div>
                      <?php
                    }
                    ?>

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
                <div class="row">
                  <div class="form-group col-sm-6">
                    <label for="total_received">Total Received Amount</label>
                    <input type="text" class="form-control" name="total_received" id="total_received" value="<?php echo $row['total_received'] ?>" readonly="">
                  </div>

                  <div class="form-group col-sm-6">
                    <label for="OSAmount">Out-Standing Amount</label>
                    <input type="text" class="form-control" name="OSAmount" id="OSAmount" onkeypress="return NumericValidate(event,this)" value="<?php echo $row['OSAmount'] ?>" readonly="">
                  </div>
                </div> 
                <div class="row">

                  <div class="form-group col-sm-12">

                    <label for="netpayableamt">Net Amount</label>
                    <input type="text" name="net_amt" class="form-control net_amt" value="<?php echo $row['net_amt'] ?>"  readonly="">
                  </div>

                </div>      



              </div>
            </div>
          </div>

          <!--End   -->

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
                calulcation();
              }
            });


             $('.b2b_dyamic').on('click', '.remove_btn', function(e){
              e.preventDefault();
              $(this).parent('div').parent('div').remove(); 
              calulcation();
              getB2bData(); 

            });





           });


getB2bData(); 
  //get bill 2 bill data
  function getB2bData()
  {
    var report_id = "<?php echo $row['sale_report_id'] ?>";


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




  function changeParty() {
    $('select[name=pur_invoice_no] option').hide();
    $('select[name=pur_invoice_no] option[data-party='+$("select[name=pur_party] option:selected").attr("data-value")+']').show();
  }
</script>
</body>
</html>