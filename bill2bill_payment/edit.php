<?php
session_start();
include('../db.php');
include('getAmountEdit.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}
if(!isset($_SESSION['b2bp_firm_id']) && !isset($_SESSION['b2bp_financial_year_id']))
{
  header('Location: index.php');
}
function convertDate($date)
{
    $final_date='';
  if($date!='')
  {
      $final_date = str_replace('/', '-',$date);
      $final_date = date('Y-m-d', strtotime($final_date));
  }
    return $final_date;
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
function getExternalPartyName($id)
{
    $name='';
    include('../db.php');
    $party = "select * from external_party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);
    if(mysqli_num_rows($partyresult)>0)
    {
      $partyrow = mysqli_fetch_array($partyresult);
      $name=$partyrow['partyname'];
    }
    return $name;
}
function getTransportName($id)
{
    $name='';
    include('../db.php');
    $party = "select * from transport where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);
    if(mysqli_num_rows($partyresult)>0)
    {
      $partyrow = mysqli_fetch_array($partyresult);
      $name=$partyrow['trans_name'];
    }
    return $name;
}

$getYear=$_SESSION['b2bp_financial_year'];
$year_array=explode("/",$getYear);

 if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from bill2bill_payment where id=".$id;

    $result = mysqli_query($conn, $sql);
   

    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      
    }else {
      $errorMsg = 'Could not Find Any Record';
    }

  }



  if(isset($_POST['Submit'])){


    $total_payment = $_POST['total_payment'];

    $main_label = $_POST['main_label'];
    $main_date = convertDate($_POST['main_date']);

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");


  $dataArr=array();
  if(isset($_POST['report_id']))
  {
    $report_id=$_POST['report_id'];
    $invoice_no=$_POST['invoice_no'];
    $amt_to_be_pay=$_POST['amt_to_be_pay'];
    $payment=$_POST['payment'];
    $label=$_POST['label'];
    $date=$_POST['date'];
    $table=$_POST['table'];
    $party_id=$_POST['party_id'];

    $name = $_POST['name'];

    //if table = transport payout then tds per, tds amount get otherwise it will blank.
    $tds_per=$_POST['tds_per'];
    $tds_amount=$_POST['tds_amount'];

    $sub_id=$_POST['sub_id'];

    foreach ($report_id as $key => $item) 
    {
      $dataArr[$key]['report_id']=$item;
      $dataArr[$key]['invoice_no']=$invoice_no[$key];
      $dataArr[$key]['amt_to_be_pay']=$amt_to_be_pay[$key];
      $dataArr[$key]['payment']=$payment[$key];
      $dataArr[$key]['label']=$label[$key];
      $dataArr[$key]['date']=convertDate($date[$key]);
      $dataArr[$key]['party_id']=$party_id[$key];
      $dataArr[$key]['table']=$table[$key];
      $dataArr[$key]['tds_per']=$tds_per[$key];
      $dataArr[$key]['tds_amount']=$tds_amount[$key];

      if(isset($sub_id[$key]))
      {
         $dataArr[$key]['sub_id']=$sub_id[$key];
      }

    }
  }

 



    //get sub table id
    $oldIDArr=array();
    $sqlId="select * from bill2bill_sub_data where bill2bill_id='".$_GET['id']."'";
    $resultId=mysqli_query($conn,$sqlId);
    if(mysqli_num_rows($resultId)>0)
    {
      while ($rowId=mysqli_fetch_assoc($resultId)) 
      {
          $oldIDArr[]=$rowId['id'];
      }
    }
  

    

      $sql = "UPDATE `bill2bill_payment` SET 
      `total_payment`='".$total_payment."',
      `main_date`='".$main_date."',
      `main_label`='".$main_label."',
      `name`='".$name."',
      `username`='".$username."',
      `updated_at`='".$timestamp."'
       WHERE id='".$_GET['id']."'";

      $result = mysqli_query($conn, $sql);
      if($result){

        $curIdArr=array();
        foreach ($dataArr as $key => $item) 
        {

            if(isset($item['sub_id'])) //update
            {
              $curIdArr[]=$item['sub_id'];

              $sqlSub="UPDATE `bill2bill_sub_data` SET 
              `table_indicator`='".$item['table']."',
              `report_id`='".$item['report_id']."',
              `party_id`='".$item['party_id']."',
              `invoice_no`='".$item['invoice_no']."',
              `amt_to_be_pay`='".$item['amt_to_be_pay']."',
              `tds_per`='".$item['tds_per']."',
              `tds_amount`='".$item['tds_amount']."',
              `payment`='".$item['payment']."',
              `label`='".$item['label']."',
              `date`='".$item['date']."',
              `bill2bill_id`='".$_GET['id']."'
               WHERE id='".$item['sub_id']."'";
               $resultSub = mysqli_query($conn, $sqlSub);
            } 
            else //add new
            {
              $sqlSub="INSERT INTO `bill2bill_sub_data`(`table_indicator`, `report_id`, `party_id`, `invoice_no`, `amt_to_be_pay`, `tds_per`, `tds_amount`, `payment`, `label`, `date`, `bill2bill_id`) VALUES ('".$item['table']."', '".$item['report_id']."', '".$item['party_id']."','".$item['invoice_no']."','".$item['amt_to_be_pay']."','".$item['tds_per']."','".$item['tds_amount']."','".$item['payment']."','".$item['label']."','".$item['date']."','".$_GET['id']."')";
              $resultSub = mysqli_query($conn, $sqlSub);
            }        
        }

          //delete record
          $deleteIDArr=array_diff($oldIDArr,$curIdArr);
          foreach ($deleteIDArr as $key => $id) 
          {
              $sql2 = "delete from bill2bill_sub_data where id=".$id;
              mysqli_query($conn, $sql2);
          }

    
        
        $page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
        header("Location: index1.php?page=$page");
      }else{
        $errorMsg = 'Error '.mysqli_error($conn);
      }
    

  }


?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Bill 2 Bill Payment Edit</title>
 
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

      $('.searchDropdown').selectpicker();

      $('.datepicker2').datepicker({            
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        maxDate: new Date('<?php echo($year_array[1]) ?>'),
        minDate: new Date('<?php echo($year_array[0]) ?>')                             
    });
    $('.datepicker2').keydown(false);


    $(".datepickerMain").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        maxDate: new Date("<?php echo($year_array[1]) ?>"),
        minDate: new Date("<?php echo($year_array[0]) ?>")});
      $(".datepickerMain").keydown(false);

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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Bill 2 Bill Payment</span></a>

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
                <span class="pre-firm">Firm : </span><span class="pre-firm-name"><?php echo $_SESSION["b2bp_firm"]; ?></span>
            </div>
            <div class="year-selection-pre">
            <span class="pre-year-text">Financial Year :</span> 
            <span class="pre-year">
              <?php 

              $finYearArr=explode('/',$_SESSION["b2bp_financial_year"]);

              $start_date=date('Y', strtotime($finYearArr[0]));
               $end_date=date('Y', strtotime($finYearArr[1]));

              echo $start_date.' - '.$end_date; 

              ?>
            </span>
            </div>
          </div>
          <div class="last-edits-fl">
        <?php
            $sqlLastChange="select username,updated_at from bill2bill_payment where id='".$row['id']."'";

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
              <div class="card-header">Bill 2 Bill Payment Edit</div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">



                    <div class="row">

                      <div class="form-group col-md-4">
                        <label for="table_indicator">Select Table</label>
                        <select id="table_indicator" name="table_indicator" class="form-control">
                              <option value="" disabled selected>Select Table</option>
                              <option value="pur_bales_payout">Purchase Bales Payout</option>
                              <option value="transport_payout">Transport Payout</option>
                              <option value="rd_kapas_pur_payment">RD Kapas Purchase Payment</option>
                              <option value="sales_receivable">Sales Receivable</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-4">
                        <label for="party_id">Select External Party / Transport</label>
                        <select id="party_id" name="party_id" data-live-search="true" class="form-control searchDropdown">
                              <option value="" disabled selected>Select External Party / Transport</option>
                        </select>
                    </div>

                        <div class="form-group col-sm-4">
                          <label for="total_payment">Total Payment</label>
                          <input id="total_payment" type="text" class="form-control" name="total_payment" placeholder="Enter Total Payment" onkeypress="return decimalValidation(event,this)" value="<?php echo $row['total_payment'] ?>">
                        </div>

                         <div class="form-group col-sm-4">
                          <label for="main_label">Label</label>
                          <input id="main_label" type="text" class="form-control" name="main_label" placeholder="Enter Label" value="<?php echo $row['main_label'] ?>" required="">
                        </div>

                         <div class="form-group col-sm-4">
                          <label for="main_date">Date</label>
                          <input id="main_date" type="text" class="form-control datepickerMain" name="main_date" placeholder="Select Date" value="<?php echo convertDate2($row['main_date']) ?>" required="">
                        </div>

                        <div class="form-group col-sm-4">
                          <label for="name">Name</label>
                          <input id="name" type="text" class="form-control" name="name" placeholder="Enter Name" value="<?php echo $row['name'] ?>">
                        </div>


                        </div>


                        <div class=" field_wrapper_dyamic">
                              <span class="row">
                                <div class="form-group col-md-4">
                                  <label for="invoice_no">Invoice No / LR. No.</label>
                                  <select class="form-control invoice_no" id="invoice_no">
                                    <option disabled="" value="" selected="">Select Option</option>
                                  </select>                
                                </div>
                                <div class="col-md-4 " >
                                    <button type="button" style="margin-top: 32px;" class="btn btn-primary addBtn" disabled="">Add</button>
                                </div>
                              </span>

                           

                       <div class="dynamicSection">

                        <?php
                          $sql2="select * from bill2bill_sub_data where bill2bill_id='".$id."'";
                          $result2=mysqli_query($conn,$sql2);
                          if(mysqli_num_rows($result2)>0)
                          {
                            $i=1;
                             while ($item=mysqli_fetch_assoc($result2)) 
                            { 
                               
                               if($item['table_indicator']=='transport_payout')
                               {
                                $table_name="Transport Payout";
                                ?>

                                <div class="card mainCard" style="margin-top:10px;">
                                    <div class="card-header"><?php echo getTransportName($item['party_id'])." ($table_name)" ?>
                                      <div style="float: right;"><a href="javascript:void(0);" class="btn btn-sm btn-danger remove_btn">-</a></div>
                                    </div>
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="form-group col-md-4">
                                          <label>Table</label>
                                          <input type="text" class="form-control table" readonly="" value="<?php echo $table_name ?>">
                                        </div>
                                        <div class="form-group col-md-4">
                                          <label>Party / Transport</label>
                                          <input type="text" class="form-control party_name"  value="<?php echo getTransportName($item['party_id']) ?>" readonly>
                                        </div>
                                        <div class="form-group col-md-4">
                                          <label>Invoice No. / LR. No.</label>
                                          <input type="text" class="form-control invoice_no" name="invoice_no[]" value="<?php echo $item['invoice_no'] ?>" readonly>
                                        </div>
                                        <div class="form-group col-sm-3">
                                          <label for="amt_to_be_pay">Amount To Be Pay :</label>
                                          <input type="text" name="amt_to_be_pay[]" class="form-control amt_to_be_pay" value="<?php echo $item['amt_to_be_pay'] ?>" readonly="">
                                        </div> 
                                        <div class="form-group col-md-3">
                                          <label for="tds_per">TDS Percentage (%)</label>
                                          <input type="text" class="form-control tds_per" name="tds_per[]"  placeholder="Enter Pecentage" onkeypress="return decimalValidation(event,this)" onkeyup="calculateTDS(this)" value="<?php echo $item['tds_per'] ?>">
                                        </div>
                                        <div class="form-group col-md-3">
                                          <label for="tds_amount">TDS Amount</label>
                                          <input type="text" class="form-control tds_amount" name="tds_amount[]"  placeholder="Enter Pecentage" value="<?php echo $item['tds_amount'] ?>" readonly="">
                                        </div>
                                        <div class="form-group col-md-3">
                                          <label for="payment">Payment</label>
                                          <input type="text" class="form-control payment" name="payment[]"  value="<?php echo $item['payment'] ?>" readonly="">
                                        </div>
                                        <div class="form-group col-md-3">
                                          <label>Label</label>
                                          <input type="text" class="form-control label" placeholder="Enter Label" name="label[]" value="<?php echo $item['label'] ?>">
                                        </div>
                                        <div class="form-group col-md-3">
                                          <label>Date</label>
                                          <input type="text" class="form-control date datepicker<?php echo $i ?>" placeholder="Select Date" name="date[]" value="<?php echo convertDate2($item['date']) ?>">
                                        </div>
                                        <input type="hidden" name="party_id[]" class="party_id" value="<?php echo $item['party_id'] ?>"/>
                                        <input type="hidden" name="report_id[]" class="report_id" value="<?php echo $item['report_id'] ?>"/>
                                        <input type="hidden" name="table[]" class="table" value="<?php echo $item['table_indicator'] ?>"/>
                                        <input type="hidden" name="sub_id[]" class="sub_id" value="<?php echo $item['id'] ?>"/>
                                      </div>
                                    </div>

                                    <script type="text/javascript">
                                      $(".datepicker<?php echo $i ?>").datepicker({
                                        dateFormat: "dd/mm/yy",
                                        changeMonth: true,
                                        changeYear: true,
                                        maxDate: new Date("<?php echo($year_array[1]) ?>"),
                                        minDate: new Date("<?php echo($year_array[0]) ?>")
                                      })
                                      $(".datepicker<?php echo $i ?>").keydown(false);
                                   
                                    </script>

                                  </div>
                                 
                                 
                                <?php
                               }
                               else
                               {

                                 //get avl amount
                                 $amt_to_be_pay=(float)getAmtTobePay($item['table_indicator'],$item['report_id']);
                                 $amt_to_be_pay+=(float)$item['payment'];



                                  $table_name='';
                                  if($item['table_indicator']=='pur_bales_payout')
                                  {
                                    $table_name='Purchase Bales Payout';
                                  }
                                  else if($item['table_indicator']=='rd_kapas_pur_payment')
                                  {
                                    $table_name='RD Kapas Purchase Payment';
                                  }
                                  else if($item['table_indicator']=='sales_receivable')
                                  {
                                    $table_name='Sales Receivable';
                                  }

                                  ?>

                                  <div class="card mainCard" style="margin-top:10px;">
                                    <div class="card-header"><?php echo getExternalPartyName($item['party_id'])." ($table_name)" ?>
                                      <div style="float: right;"><a href="javascript:void(0);" class="btn btn-sm btn-danger remove_btn">-</a></div>
                                    </div>
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="form-group col-md-4">
                                          <label>Table</label>
                                          <input type="text" class="form-control table" readonly="" value="<?php echo $table_name ?>">
                                        </div>
                                        <div class="form-group col-md-4">
                                          <label>Party / Transport</label>
                                          <input type="text" class="form-control party_name"  value="<?php echo getExternalPartyName($item['party_id']) ?>" readonly>
                                        </div>
                                        <div class="form-group col-md-4">
                                          <label>Invoice No. / LR. No.</label>
                                          <input type="text" class="form-control invoice_no" name="invoice_no[]" value="<?php echo $item['invoice_no'] ?>" readonly>
                                        </div>
                                        <div class="form-group col-sm-3">
                                          <label for="amt_to_be_pay">Amount To Be Pay :</label>
                                          <input type="text" name="amt_to_be_pay[]" class="form-control amt_to_be_pay" value="<?php echo $amt_to_be_pay ?>" readonly="">
                                        </div> 
                                        
                                        <div class="form-group col-md-3">
                                          <label for="payment">Payment</label>
                                          <input type="text" class="form-control payment" name="payment[]"  value="<?php echo $item['payment'] ?>" onkeyup="amtValidation(this)" placeholder="Enter Amount">
                                        </div>
                                        <div class="form-group col-md-3">
                                          <label>Label</label>
                                          <input type="text" class="form-control label" placeholder="Enter Label" name="label[]" value="<?php echo $item['label'] ?>">
                                        </div>
                                        <div class="form-group col-md-3">
                                          <label>Date</label>
                                          <input type="text" class="form-control date datepicker<?php echo $i ?>" placeholder="Select Date" name="date[]" value="<?php echo convertDate2($item['date']) ?>">
                                        </div>
                                       
                                          <input type="hidden" class="tds_per" name="tds_per[]" value="">
                                        
                                          <input type="hidden" class="tds_amount" name="tds_amount[]"  value="">
                                       
                                        <input type="hidden" name="party_id[]" class="party_id" value="<?php echo $item['party_id'] ?>"/>
                                        <input type="hidden" name="report_id[]" class="report_id" value="<?php echo $item['report_id'] ?>"/>
                                        <input type="hidden" name="table[]" class="table" value="<?php echo $item['table_indicator'] ?>"/>
                                        <input type="hidden" name="sub_id[]" class="sub_id" value="<?php echo $item['id'] ?>"/>
                                      </div>
                                    </div>

                                    <script type="text/javascript">
                                      $(".datepicker<?php echo $i ?>").datepicker({
                                        dateFormat: "dd/mm/yy",
                                        changeMonth: true,
                                        changeYear: true,
                                        maxDate: new Date("<?php echo($year_array[1]) ?>"),
                                        minDate: new Date("<?php echo($year_array[0]) ?>")
                                      })
                                      $(".datepicker<?php echo $i ?>").keydown(false);
                                   
                                    </script>

                                  </div>

                                 <?php

                              
                               }      
                              $i++;                        
                            }
                          }
                        ?>



                       </div>

                    </div>
                        
              
                       <hr/>
              

                    <div class="form-group">
                      <button type="submit" name="Submit" id="submit" class="btn btn-primary waves">Submit</button>
                    </div>
                </form>
              </div>
            </div>
          
        </div>
      </div>

</div>
</div>

 <script type="text/javascript">




        $(document).ready(function () {



          $('#table_indicator').on('change', function() {

              $('#invoice_no').find('option').not(':first').remove();
              $('#party_id').find('option').not(':first').remove();
              $('#invoice_no').val('');
              $('#party_id').val('');

                getPartyList();
               

          });

           $('#party_id').on('change', function() {

              $('#invoice_no').find('option').not(':first').remove();
              $('#invoice_no').val('');

                getInvoiceList();
                
             
          });

           $('#main_date').on('change', function() {

               var main_date = this.value;

              $('input[name="date[]"]').each(function(){
                    $(this).val(main_date)
              });
             
          });

           $('#main_label').on('input', function() {

               var main_label = this.value;

              $('input[name="label[]"]').each(function(){
                    $(this).val(main_label)
              });
             
          });



           $('#invoice_no').on('change', function() {

               $('.addBtn').attr("disabled",false);
             
          });



           $('.addBtn').click(function()
           {
                var table = $('#table_indicator :selected').val();
                var record_id = $('#invoice_no :selected').val();

                var main_label = $('#main_label').val();
                var main_date = $('#main_date').val();


                var curRecordId = "<?php echo $row['id'] ?>";





                if(table!='' && record_id!='')
                {
                     $.ajax({
                      type: "POST",
                      url: 'getData.php',
                      data: {
                        table:table,
                        record_id:record_id,
                        curRecordId:curRecordId,
                        getRecord:true,
                      },
                      success: function(response)
                      {

                        console.log(response);
                          var jsonData = JSON.parse(response);
                          

                           var data=jsonData;

                          count = $('.mainCard').length;
                          count+=1;


                          if(table=='transport_payout')
                          {
                                var table_name="Transport Payout";
                              
                                var balesfieldHTML= '<div class="card mainCard" style="margin-top:10px;"><div class="card-header">'+data.party_name+' ('+table_name+')'+'<div style="float: right;"><a href="javascript:void(0);" class="btn btn-sm btn-danger remove_btn">-</a></div></div><div class="card-body"><div class="row"><div class="form-group col-md-4"><label>Table</label><input type="text" class="form-control table" readonly="" value="'+table_name+'"></div><div class="form-group col-md-4"><label>Party / Transport</label><input type="text" class="form-control party_name"  value="'+data.party_name+'" readonly></div><div class="form-group col-md-4"><label>Invoice No. / LR. No.</label><input type="text" class="form-control invoice_no" name="invoice_no[]" value="'+data.invoice_no+'" readonly></div><div class="form-group col-sm-3"><label for="amt_to_be_pay">Amount To Be Pay :</label><input type="text" name="amt_to_be_pay[]" class="form-control amt_to_be_pay" value="'+data.amt_to_be_pay+'" readonly=""></div> <div class="form-group col-md-3"><label for="tds_per">TDS Percentage (%)</label><input type="text" class="form-control tds_per" name="tds_per[]"  placeholder="Enter Pecentage" value="" onkeypress="return decimalValidation(event,this)" onkeyup="calculateTDS(this)"></div><div class="form-group col-md-3"><label for="tds_amount">TDS Amount</label><input type="text" class="form-control tds_amount" name="tds_amount[]"  placeholder="Enter Pecentage" value="" readonly=""></div><div class="form-group col-md-3"><label for="payment">Payment</label><input type="text" class="form-control payment" name="payment[]"  value="" readonly=""></div><div class="form-group col-md-3"><label>Label</label><input type="text" class="form-control label" placeholder="Enter Label" name="label[]" value="'+main_label+'"></div><div class="form-group col-md-3"><label>Date</label><input type="text" class="form-control date datepicker'+count+'" placeholder="Select Date" name="date[]" value="'+main_date+'"></div><input type="hidden" name="party_id[]" class="party_id" value="'+data.party_id+'"/><input type="hidden" name="report_id[]" class="report_id" value="'+data.report_id+'"/><input type="hidden" name="table[]" class="table" value="'+data.table+'"/></div></div>';
                             

                          }
                          else
                          {
                              var table_name=''
                              if(table=='pur_bales_payout')
                              {
                                table_name="Purchase Bales Payout";
                              }
                              if(table=='rd_kapas_pur_payment')
                              {
                                table_name="RD Kapas Purchase Payment";
                              }
                              if(table=='sales_receivable')
                              {
                                table_name="Sales Receivable";
                              }

                             
                                var balesfieldHTML= '<div class="card mainCard" style="margin-top:10px;"><div class="card-header">'+data.party_name+' ('+table_name+')'+'<div style="float: right;"><a href="javascript:void(0);" class="btn btn-sm btn-danger remove_btn">-</a></div></div><div class="card-body"><div class="row"><div class="form-group col-md-4"><label>Table</label><input type="text" class="form-control table" readonly="" value="'+table_name+'"></div><div class="form-group col-md-4"><label>Party / Transport</label><input type="text" class="form-control party_name"  value="'+data.party_name+'" readonly></div><div class="form-group col-md-4"><label>Invoice No. / LR. No.</label><input type="text" class="form-control invoice_no" name="invoice_no[]" value="'+data.invoice_no+'" readonly></div><div class="form-group col-sm-3"><label for="amt_to_be_pay">Amount To Be Pay :</label><input type="text" name="amt_to_be_pay[]" class="form-control amt_to_be_pay" value="'+data.amt_to_be_pay+'" readonly=""></div><div class="form-group col-md-3"><label for="payment">Payment</label><input type="text" class="form-control payment" name="payment[]"  placeholder="Enter Amount" value="" onkeypress="return decimalValidation(event,this)" onkeyup="amtValidation(this)"></div><div class="form-group col-md-3"><label>Label</label><input type="text" class="form-control label" placeholder="Enter Label" name="label[]" value="'+main_label+'"></div><div class="form-group col-md-3"><label>Date</label><input type="text" class="form-control date datepicker'+count+'" placeholder="Select Date" name="date[]" value="'+main_date+'"></div><input type="hidden" name="tds_per[]" class="table" value=""/><input type="hidden" name="tds_amount[]" class="table" value=""/><input type="hidden" name="party_id[]" class="party_id" value="'+data.party_id+'"/><input type="hidden" name="report_id[]" class="report_id" value="'+data.report_id+'"/><input type="hidden" name="table[]" class="table" value="'+data.table+'"/></div></div>';
                              
                          }

                          balesfieldHTML+='<script>$(".datepicker'+count+'").datepicker({dateFormat: "dd/mm/yy",changeMonth: true,changeYear: true,maxDate: new Date("<?php echo($year_array[1]) ?>"),minDate: new Date("<?php echo($year_array[0]) ?>")});$(".datepicker'+count+'").keydown(false);</';
                          balesfieldHTML+='script></div>';
                         

                            $('.dynamicSection').append(balesfieldHTML);

                            $('#invoice_no option[value="'+record_id+'"]').remove();
                            $('#invoice_no').prop('selectedIndex',0); 

                            getInvoiceList();


                         

                     }
                    });
                }
             
          });

           $('.dynamicSection').on('click', '.remove_btn', function(e)
            {
              e.preventDefault();
              $(this).parent('div').parent('div').parent('div').remove(); 
              getInvoiceList();
              totalPaymentCheck();
              
          });

           $('#total_payment').on('change', function() {
              totalPaymentCheck();
             
            });



           //validation on form submit
           $('form').on('submit', function() {

              var total_payment = $('#total_payment').val();

                if(total_payment=='')
                {
                    alert('please Enter Total Payment');
                    return false;
                }

                var usedAmt = 0;
                $('.payment').each(function(index){
                  if(this.value!='')
                  {
                    usedAmt+=parseFloat(this.value);
                  }
                });


                var count = $('.payment').length;
                if(count==0 || usedAmt==0)
                {
                    alert('please enter dynamic Payment');
                    return false;
                }



               if(parseFloat(usedAmt)>parseFloat(total_payment))
               {
                  alert('Dynamic Payment Should be less OR Equal To Total Payment.');
                  return false;
               }            
       
             
            });

          
        });


    function getPartyList()
    {
        var table = $('#table_indicator :selected').val();



        $('#party_id').find('option').not(':first').remove();
        $('#party_id').val('');

          if(table_indicator!='')
          {
                $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {
              table:table,
              getParty:true,
            },
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                

                var partyArr=jsonData.party

                console.log(partyArr);

                for (var i=0; i<partyArr.length;i++)
                {
                 
                    $('<option/>').val(partyArr[i].id).html(partyArr[i].party_name).appendTo('#party_id');
                  
                }
                $("#party_id").selectpicker("refresh");
                
                

           }
          });
        }

      
    }


    function getInvoiceList()
    {
        var table = $('#table_indicator :selected').val();
        var party_id = $('#party_id :selected').val();




          if(table_indicator!='' && party_id!='')
          {
                $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {
              table:table,
              party_id:party_id,
              curRecordId:"<?php echo $row['id'] ?>",
              getInvoice:true
            },
            success: function(response)
            {
                var jsonData = JSON.parse(response);

                var invoiceArr=jsonData;
                
                console.log(invoiceArr);

                 $('#invoice_no').find('option').not(':first').remove();
                 $('#invoice_no').val('');

                for (var i=0; i<invoiceArr.length;i++)
                {
                  $('<option/>').val(invoiceArr[i].report_id).html(invoiceArr[i].invoice_no).appendTo('#invoice_no');
                }
                $('#invoice_no').val(''); 
                
           }
          });
        }

      
    }




    function amtValidation(e)
    {
      var amtTopay = $(e).parent().parent().find('.amt_to_be_pay').val();


        var error = $(e).parent().find('span.error-keyup-500').hide();
        $('#submit').attr('disabled',false);

        if(parseFloat(e.value)>parseFloat(amtTopay))
        {
          $(e).after('<span class="error error-keyup-500 text-danger">Payment should be equal Or less than to Amount To be pay.</span>');
            $('#submit').attr('disabled',true);
        }
         totalPaymentCheck();
      
    }

    function calculateTDS(e)
    {
      var amtTopay = $(e).parent().parent().find('.amt_to_be_pay').val();
      var pr = e.value;

      if(amtTopay=='')
      {
        amtTopay=0;
      }

      if(pr!='')
      {
         var tds_amount=(parseFloat(amtTopay)*pr)/100;
        var payment=parseFloat(amtTopay)-parseFloat(tds_amount);

        $(e).parent().parent().find('.tds_amount').val(tds_amount);
        $(e).parent().parent().find('.payment').val(payment);
      }
       totalPaymentCheck();

    }

    function totalPaymentCheck()
    {
      $('#submit').attr('disabled',false);
      $('span.error-keyup-963').hide();

      var total_payment = $('#total_payment').val();

        if(total_payment!='')
        {
          var usedAmt = 0;
          $('.payment').each(function(index){
            if(this.value!='')
            {
              usedAmt+=parseFloat(this.value);
            }
          });

         if(parseFloat(usedAmt)>parseFloat(total_payment))
         {
             $('#submit').after('<span class="error error-keyup-963 text-danger">&nbsp;&nbsp;<b>Dynamic Payment Should be not greater then Total Payment..</b></span>');
            $('#submit').attr('disabled',true);
         }
      }


    }



    function decimalValidation(evt, element) {

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
