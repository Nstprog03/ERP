<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}
if(!isset($_SESSION['b2bp_firm_id']) && !isset($_SESSION['b2bp_financial_year_id']))
{
  header('Location: index.php');
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


?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Bill 2 Bill Payment Show</title>
 
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
              <div class="card-header">Bill 2 Bill Payment Show</div>
              <div class="card-body">
                



                    <div class="row">

                  

                        <div class="form-group col-sm-4">
                          <label for="total_payment">Total Payment</label>
                          <input id="total_payment" type="text" class="form-control" name="total_payment"  onkeypress="return decimalValidation(event,this)" value="<?php echo $row['total_payment'] ?>" readonly>
                        </div>

                         <div class="form-group col-sm-4">
                          <label for="main_label">Label</label>
                          <input id="main_label" type="text" class="form-control" name="main_label" value="<?php echo $row['main_label'] ?>" readonly>
                        </div>

                         <div class="form-group col-sm-4">
                          <label for="main_date">Date</label>
                          <input id="main_date" type="text" class="form-control" name="main_date" value="<?php echo convertDate2($row['main_date']) ?>" readonly>
                        </div>

                         <div class="form-group col-sm-4">
                          <label for="name">Name</label>
                          <input id="name" type="text" class="form-control" name="name" value="<?php echo $row['name'] ?>" readonly>
                        </div>

                        </div>


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
                                          <input type="text" class="form-control tds_per" name="tds_per[]"   value="<?php echo $item['tds_per'] ?>"  readonly="">
                                        </div>
                                        <div class="form-group col-md-3">
                                          <label for="tds_amount">TDS Amount</label>
                                          <input type="text" class="form-control tds_amount" name="tds_amount[]"  value="<?php echo $item['tds_amount'] ?>" readonly="">
                                        </div>
                                        <div class="form-group col-md-3">
                                          <label for="payment">Payment</label>
                                          <input type="text" class="form-control payment" name="payment[]"  value="<?php echo $item['payment'] ?>" readonly="">
                                        </div>
                                        <div class="form-group col-md-3">
                                          <label>Label</label>
                                          <input type="text" class="form-control label"  name="label[]" value="<?php echo $item['label'] ?>"  readonly="">
                                        </div>
                                        <div class="form-group col-md-3">
                                          <label>Date</label>
                                          <input type="text" class="form-control date datepicker<?php echo $i ?>" name="date[]" value="<?php echo convertDate2($item['date']) ?>"  readonly="">
                                        </div>
                                       
                                      </div>
                                    </div>


                                  </div>
                                 
                                 
                                <?php
                               }
                               else
                               {
                                //  $amt_to_be_pay=getAmtTobePay($row['table'],$item['report_id']);
                                // $amt_to_be_pay+=$item['payment'];
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
                                          <input type="text" name="amt_to_be_pay[]" class="form-control amt_to_be_pay" value="<?php echo $item['amt_to_be_pay'] ?>" readonly="">
                                        </div> 
                                        
                                        <div class="form-group col-md-3">
                                          <label for="payment">Payment</label>
                                          <input type="text" class="form-control payment" name="payment[]"  value="<?php echo $item['payment'] ?>" readonly="">
                                        </div>
                                        <div class="form-group col-md-3">
                                          <label>Label</label>
                                          <input type="text" class="form-control label" name="label[]" value="<?php echo $item['label'] ?>"  readonly="">
                                        </div>
                                        <div class="form-group col-md-3">
                                          <label>Date</label>
                                          <input type="text" class="form-control date datepicker<?php echo $i ?>"  name="date[]" value="<?php echo convertDate2($item['date']) ?>"  readonly="">
                                        </div>
                                       
                                       
                                      </div>
                                    </div>

                                   

                                  </div>

                                 <?php

                              
                               }      
                              $i++;                        
                            }
                          }
                        ?>



                       </div>
                        
              
              

              </div>
           
          </div>
        </div>
      </div>

</div>
</div>

 <script type="text/javascript">




        $(document).ready(function () {


          //pageload
          pageLoad();




          
        });




    function pageLoad()
    {
      //get party List

        var table = "<?php echo $row['table_indicator'] ?>";
        var curParty="<?php echo $row['party_id'] ?>";

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

                $('#party_id').val(curParty);           

           }
          });
        }




      //get invoce List

        var table = "<?php echo $row['table_indicator'] ?>";
        var party_id = "<?php echo $row['party_id'] ?>";


         //already used invoice
         var usedArr=[];
         $('.report_id').each(function(index){
            usedArr[index]=this.value;
         });

         console.log(usedArr)


          if(table_indicator!='' && party_id!='')
          {
                $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {
              table:table,
              party_id:party_id,
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

                  if(!usedArr.includes(invoiceArr[i].report_id))
                  {
                     $('<option/>').val(invoiceArr[i].report_id).html(invoiceArr[i].invoice_no).appendTo('#invoice_no');
                  }
               
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
