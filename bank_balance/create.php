<?php
   session_start();
   require_once ('../db.php');
   if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
       header("location: ../login.php");
       exit;
   }
   if(!isset($_SESSION["bank_transaction_firm_id"]) && !isset($_SESSION["bank_transaction_financial_year_id"])){
      header("location:../bank_transation.php");
      exit;
  }

   if(!isset($_SESSION['bank'])){
      header("location:index.php");
      exit;
   }
   

   $getYear = $_SESSION['bank_transaction_financial_year'];
   $year_array = explode("/", $getYear);

   $bank_sql = "SELECT * FROM bank_balance WHERE bank = '".$_SESSION['bank']."' AND firm='".$_SESSION["bank_transaction_firm_id"]."' AND financial_year='".$_SESSION["bank_transaction_financial_year_id"]."' ORDER BY `bank_balance`.`date` DESC";
   $bank_result = mysqli_query($conn,$bank_sql);
   if(mysqli_num_rows($bank_result) > 0){
      $bank_data = mysqli_fetch_assoc($bank_result);
      $curruntDate = $bank_data['date'];
   }else{
      date_default_timezone_set('Asia/Kolkata');
      $curruntDate = "0000-00-00";
   }
   ?>

<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <title>Bank Balance Create</title>
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
      <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
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
                 
             });
             $(".datepicker").keydown(false);
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
                  <a class="navbar-brand" href="home.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New Bank Balance</span></a>
                  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                  <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarSupportedContent">
                     <ul class="navbar-nav mr-auto"></ul>
                     <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="btn btn-outline-danger" href="home.php"><i class="fa fa-sign-out-alt"></i>Back</a></li>
                     </ul>
                  </div>
               </div>
            </nav>
            <div class="container-fluid">
               <div class="row justify-content-center">
                  <div class="card">
                     <div class="card-header">Create New Bank Balance</div>
                     <div class="card-body">
                        <form class="" action="add.php" method="post" enctype="multipart/form-data">
                           <input type="hidden" name="bank" value='<?php echo $_SESSION['bank']; ?>'> 
                           <input type="hidden" name="firm" value="<?php echo $_SESSION["bank_transaction_firm_id"]; ?>">
                           <input type="hidden" name="year" value="<?php echo $_SESSION["bank_transaction_financial_year_id"]; ?>">
                           <div class="row">
                              <div class="form-group col-md-4">
                                 <label for="date">Date</label>
                                 <input type="text" class="form-control datepicker" id="date" placeholder="Enter Date" name="date" autocomplete="off">
                                 <span id="date_err" style="color: red;font-size: 12px;"></span> 
                              </div>

                              <div class="form-group col-md-4">
                                 <label for="balance">Opening Balance</label>
                                 <input type="text" class="form-control" id="previous_balance" name="previous_balance" placeholder="Previous Balance" value="" readonly>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="balance">Receivable</label>
                                 <input type="text" class="form-control" id="balance" name="balance" placeholder="Enter Balance" value="">
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="balance">Closing Balance</label>
                                 <input type="text" class="form-control" id="total_balance" name="total_balance" placeholder="Total Balance" value="" readonly>
                              </div>
                           </div>
                           <div class="row">
                              <div class="form-group">
                                 <button type="submit" name="Submit" id="submit" class="btn btn-primary waves ml-3">Submit</button>
                              </div>
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

      <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
      
      <script type="text/javascript">
           $(document).ready(function () {
               $('#sidebarCollapse').on('click', function () {
                   $('#sidebar').toggleClass('active');
               });

               $("#date").change(function() {
                  var date = $("#date").val();
                  var bank = $("input[name=bank]").val();
                  if(date != ""){
                     $("#date_err").removeClass("error").text('');
                  }
                  $(".previous_balance").val("0");
                  
                  $.ajax({
                     type: "POST",
                     url: 'getData.php',
                     data: {
                        getPreviousBalance:true,
                        date:date,
                        bank:bank,
                        bank_id: '0',
                     },
                     success: function(response)
                     {
                        var jsonData = JSON.parse(response);
                        $("#previous_balance").val(jsonData.PreviousBalance);
                        TotalBalance();
                     }
                  });
               });
               
               $('#balance').on("keypress keyup keydown",function() {
                  if($('#balance').val() != ""){
                     TotalBalance();
                  }
               });
               
           });

           function TotalBalance(){
               setTimeout(function(){
                  var previous_balance = $("#previous_balance").val();
                  var balance = $("#balance").val();

                  var total = "";

                  if(previous_balance != "" && balance != ""){
                     var total =  parseFloat(previous_balance) + parseFloat(balance);
                  }

                  if(balance == ""){
                     $("#total_balance").val();
                  }
                  
                  $("#total_balance").val(total);
               }, 100);
           }
      </script>
   </body>
</html>