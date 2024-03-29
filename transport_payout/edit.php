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


  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from transport_payout where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);

    }else {
      $errorMsg = 'Could not Find Any Record';
      echo $errorMsg;
    }
  }

  
  if(isset($_POST['submit']))
  {


  $pay_date = '';
  if($_POST['pay_date']!='')
    {
      $pay_date = str_replace('/', '-', $_POST['pay_date']);
      $pay_date = date('Y-m-d', strtotime($pay_date));
    }

  $tds_per = $_POST['tds_per'];
  $tds_amount = $_POST['tds_amount'];
  $total_amount = $_POST['total_amount'];

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");


        $sql = "UPDATE `transport_payout` SET 
          `pay_date`='".$pay_date."',
          `tds_per`='".$tds_per."',
          `tds_amount`='".$tds_amount."',
          `total_amount`='".$total_amount."',
          `username`='".$username."',
          `updated_at`='".$timestamp."'
            
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
    <title>Transport Payout Database Edit</title>


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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Transport Payout Edit</span></a>
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
           $sqlLastChange="select username,updated_at from transport_payout where id='".$row['id']."'";

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
              <div class="card-header">Transport Payout Edit</div>
              <div class="card-body">
           

                    <form id="main_form" class="" action="" method="post" enctype="multipart/form-data">
                    <div class="row"> 


                      <input type="hidden" name="pur_report_id" value="<?php echo $report_id ?>">


                      <div class="form-group col-md-5">
                        <label for="trans_id">Transport Name</label>
                        <?php 

                          $sql_trans="SELECT * FROM transport WHERE id='".$row['trans_id']."'";
                          $result_trans = mysqli_query($conn, $sql_trans);
                          $row_trans = $result_trans->fetch_assoc();

                          ?>
                        <input type="text"  class="form-control" readonly="" value="<?php  echo $row_trans['trans_name']; ?>" >

                        <input type="hidden" name="trans_id" value="<?php echo $row_trans['id'] ?>">

                      </div>


                      <div class="form-group col-md-2">
                        <label for="invoice_no"> Invoice No</label>
                        <input type="text" name="invoice_no" value="<?php echo $row['invoice_no']; ?>" class="form-control" readonly>
                      </div>


                      <div class="form-group col-md-5">
                        <label for="pur_party"> External Party</label>
                        <?php 

                          $party_sql1="SELECT * FROM external_party WHERE id='".$row['ext_party_id']."'";
                          $party_result1 = mysqli_query($conn, $party_sql1);
                          $party_row1 = $party_result1->fetch_assoc();

                          ?>
                        <input type="text"  class="form-control" readonly="" value="<?php echo $party_row1['partyname']; ?>" >

                        <input type="hidden" name="ext_party_id" value="<?php echo $party_row1['id'] ?>">
                      </div>
                     

                    
                    </div>
                    
                      <?php

                        //lr date convert to dd/mm/yyyy
                        $trans_lr_date='';
                        if($row['trans_lr_date']!='' && $row['trans_lr_date']!='0000-00-00')
                          {
                            $trans_lr_date = str_replace('-', '/', $row['trans_lr_date']);
                            $trans_lr_date = date('d/m/Y', strtotime($trans_lr_date));
                          }

                          ?>



                      <div class="row">
                        <div class="form-group col-md-3">
                              <label for="trans_vehicle_no">Transport Vehicle No.</label>
                              <input type="text" id="trans_veh_no" name="trans_veh_no" class="form-control" readonly="" value="<?php echo $row['trans_veh_no'] ?>">
                            </div>
                            <div class="form-group col-md-3">
                              <label for="trans_lr_date">LR Date</label>
                              <input type="text" id="trans_lr_date" name="trans_lr_date" class="form-control" readonly="" value="<?php echo $trans_lr_date ?>">
                            </div>

                           <div class="form-group col-md-3">
                              <label for="trans_lr_no">LR No</label>
                              <input type="text" id="trans_lr_no" name="trans_lr_no" class="form-control" readonly="" value="<?php echo $row['trans_lr_no']; ?>">
                            </div>

                            <div class="form-group col-md-3">
                              <label for="trans_amount">Transport Amount</label>
                              <input type="text" id="trans_amount" name="trans_amount" class="form-control" readonly="" value="<?php echo $row['trans_amount'] ?>">
                            </div>



                      <?php

                        //lr date convert to dd/mm/yyyy
                        $pay_date='';
                        if($row['pay_date']!='' && $row['pay_date']!='0000-00-00')
                          {
                            $pay_date = str_replace('-', '/', $row['pay_date']);
                            $pay_date = date('d/m/Y', strtotime($pay_date));
                          }

                      ?>


                          <div class="form-group col-md-4">
                            <label for="pay_date">Payment Date </label>
                            <input type="text" class="form-control datepicker" name="pay_date" placeholder="Enter Payment Date" value="<?php echo $pay_date ?>">
                          </div>

                        
                            <div class="form-group col-md-4">
                              <label for="tds_per">TDS Percentage (%)</label>
                              <input type="text" id="tds_per" name="tds_per" class="form-control" placeholder="Enter TDS %" onkeypress="return decimalValidate(event,this)" value="<?php echo $row['tds_per'] ?>">
                            </div>

                            <div class="form-group col-md-4">
                              <label for="tds_amount">TDS Amount</label>
                              <input type="text" id="tds_amount" name="tds_amount" class="form-control" readonly="" value="<?php echo $row['tds_amount'] ?>">
                            </div>

                

                     <div class="form-group col-md-4">
                        <label for="total_amount">Total Amount</label>
                        <input type="text" class="form-control" id="total_amount" name="total_amount" readonly value="<?php echo $row['total_amount'] ?>">
                      </div>

                  </div>

                      <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary waves">Submit</button>
                      </div>
                    </form>
                  
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
        var $dropdown = $("#invoice_no");
        
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

                   var option_data="<option data-reportid="+obj.pur_report_id+" value="+obj.invoice_no+">"+obj.invoice_no+"</option>";
                  $(option_data).appendTo('#invoice_no'); 
                               
                    // $dropdown.append($("<option />").val(value).text(value));
                });

                 
           }
        });


        
      });


        $('#invoice_no').on('change', function() {

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
  </body>
</html>