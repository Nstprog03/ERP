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

$getYear=$_SESSION['b2bp_financial_year'];
$year_array=explode("/",$getYear);

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


?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Bill 2 Bill Payment Create</title>
 
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

      $(".datepickerMain").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        maxDate: new Date("<?php echo($year_array[1]) ?>"),
        minDate: new Date("<?php echo($year_array[0]) ?>")}).datepicker("setDate", new Date());
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
        <a class="navbar-brand" href="index1.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New Bill 2 Bill Payment</span></a>
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

      <div class="container-fluid">
        <div class="row justify-content-center">
         
            <div class="card">
              <div class="card-header">Create New Bill 2 Bill Payment</div>
              <div class="card-body">
                <form class="" action="add.php" method="post" enctype="multipart/form-data">
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
                          <input id="total_payment" type="text" class="form-control" name="total_payment" placeholder="Enter Total Payment" onkeypress="return decimalValidation(event,this)" >
                        </div>


                        <div class="form-group col-sm-4">
                          <label for="main_label">Label</label>
                          <input id="main_label" type="text" class="form-control" name="main_label" placeholder="Enter Label" required="">
                        </div>

                         <div class="form-group col-sm-4">
                          <label for="main_date">Date</label>
                          <input id="main_date" type="text" class="form-control datepickerMain" name="main_date" placeholder="Select Date" required="">
                        </div>

                        <div class="form-group col-sm-4">
                          <label for="name">Name</label>
                          <input id="name" type="text" class="form-control" name="name" placeholder="Enter Name">
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


                if(table!='' && record_id!='')
                {
                     $.ajax({
                      type: "POST",
                      url: 'getData.php',
                      data: {
                        table:table,
                        record_id:record_id,
                        getRecord:true,
                      },
                      success: function(response)
                      {
                          var jsonData = JSON.parse(response);
                          console.log(jsonData);

                          var data=jsonData;

                          count = $('.mainCard').length;
                          count+=1;


                          if(table=='transport_payout')
                          {
                                var table_name="Transport Payout";
                              
                                var balesfieldHTML= '<div class="card mainCard" style="margin-top:10px;"><div class="card-header">'+data.party_name+' ('+table_name+')'+'<div style="float: right;"><a href="javascript:void(0);" class="btn btn-sm btn-danger remove_btn">-</a></div></div><div class="card-body"><div class="row"><div class="form-group col-md-4"><label>Table</label><input type="text" class="form-control table" readonly="" value="'+table_name+'"></div><div class="form-group col-md-4"><label>Party / Transport</label><input type="text" class="form-control party_name"  value="'+data.party_name+'" readonly></div><div class="form-group col-md-4"><label>Invoice No. / LR. No.</label><input type="text" class="form-control invoice_no" name="invoice_no[]" value="'+data.invoice_no+'" readonly></div><div class="form-group col-sm-3"><label for="amt_to_be_pay">Amount To Be Pay :</label><input type="text" name="amt_to_be_pay[]" class="form-control amt_to_be_pay" value="'+data.amt_to_be_pay+'" readonly=""></div> <div class="form-group col-md-3"><label for="tds_per">TDS Percentage (%)</label><input type="text" class="form-control tds_per" name="tds_per[]"  placeholder="Enter Pecentage" value="" onkeypress="return decimalValidation(event,this)" onkeyup="calculateTDS(this)"></div><div class="form-group col-md-3"><label for="tds_amount">TDS Amount</label><input type="text" class="form-control tds_amount" name="tds_amount[]"  placeholder="Enter Pecentage" value="" readonly=""></div><div class="form-group col-md-3"><label for="payment">Payment</label><input type="text" class="form-control payment" name="payment[]"  value="" readonly=""></div><div class="form-group col-md-3"><label>Label</label><input type="text" class="form-control label" placeholder="Enter Label" name="label[]" value="'+main_label+'"></div><div class="form-group col-md-3"><label>Date</label><input type="text" class="form-control date datepicker'+count+'" placeholder="Select Date" name="date[]" value="'+main_date+'" autocomplete="off"></div><input type="hidden" name="party_id[]" class="party_id" value="'+data.party_id+'"/><input type="hidden" name="report_id[]" class="report_id" value="'+data.report_id+'"/><input type="hidden" name="table[]" class="table" value="'+data.table+'"/></div></div>';
                             

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

                             
                                var balesfieldHTML= '<div class="card mainCard" style="margin-top:10px;"><div class="card-header">'+data.party_name+' ('+table_name+')'+'<div style="float: right;"><a href="javascript:void(0);" class="btn btn-sm btn-danger remove_btn">-</a></div></div><div class="card-body"><div class="row"><div class="form-group col-md-4"><label>Table</label><input type="text" class="form-control table" readonly="" value="'+table_name+'"></div><div class="form-group col-md-4"><label>Party / Transport</label><input type="text" class="form-control party_name"  value="'+data.party_name+'" readonly></div><div class="form-group col-md-4"><label>Invoice No. / LR. No.</label><input type="text" class="form-control invoice_no" name="invoice_no[]" value="'+data.invoice_no+'" readonly></div><div class="form-group col-sm-3"><label for="amt_to_be_pay">Amount To Be Pay :</label><input type="text" name="amt_to_be_pay[]" class="form-control amt_to_be_pay" value="'+data.amt_to_be_pay+'" readonly=""></div><div class="form-group col-md-3"><label for="payment">Payment</label><input type="text" class="form-control payment" name="payment[]"  placeholder="Enter Amount" value="" onkeypress="return decimalValidation(event,this)" onkeyup="amtValidation(this)"></div><div class="form-group col-md-3"><label>Label</label><input type="text" class="form-control label" placeholder="Enter Label" name="label[]" value="'+main_label+'"></div><div class="form-group col-md-3"><label>Date</label><input type="text" class="form-control date datepicker'+count+'" placeholder="Select Date" name="date[]" value="'+main_date+'" autocomplete="off"></div><input type="hidden" name="tds_per[]" class="table" value=""/><input type="hidden" name="tds_amount[]" class="table" value=""/><input type="hidden" name="party_id[]" class="party_id" value="'+data.party_id+'"/><input type="hidden" name="report_id[]" class="report_id" value="'+data.report_id+'"/><input type="hidden" name="table[]" class="table" value="'+data.table+'"/></div></div>';
                              
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
                $('#party_id').val('');
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

    function calculateTDS(e)
    {
      var amtTopay = $(e).parent().parent().find('.amt_to_be_pay').val();
      var pr = e.value;

      if(amtTopay=='')
      {
        amtTopay=0;
      }

      if(pr=='')
      {
        pr=0;
      }

      var tds_amount=(parseFloat(amtTopay)*pr)/100;
      var payment=parseFloat(amtTopay)-parseFloat(tds_amount);

      $(e).parent().parent().find('.tds_amount').val(tds_amount);
      $(e).parent().parent().find('.payment').val(payment);
      totalPaymentCheck();
 

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
