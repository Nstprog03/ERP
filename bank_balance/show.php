<?php
   session_start();
   require_once('../db.php');
   if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
     header("location: ../login.php");
     exit;
   }

   if(!isset($_SESSION["bank_transaction_firm_id"]) && !isset($_SESSION["bank_transaction_financial_year_id"])){
      header("location:../bank_transation.php");
      exit;
   }
   
    $firm =$_SESSION["bank_transaction_firm_id"];
    $f_year = $_SESSION["bank_transaction_financial_year_id"];

    $getYear = $_SESSION['bank_transaction_financial_year'];
    $year_array = explode("/", $getYear);
 
    date_default_timezone_set('Asia/Kolkata');
    $curruntDate = date("Y-m-d");

    
   
   if (isset($_GET['id'])) {
     $id = $_GET['id'];
     
     $sql = "select * from bank_balance where id=".$id;
     $result = mysqli_query($conn, $sql);
     if (mysqli_num_rows($result) > 0) {
         $row = mysqli_fetch_assoc($result);
         $date = date("d/m/Y" , strtotime($row['date']));

     }else {
       $errorMsg = 'Could not Find Any Record';
     }
   }
   ?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <title>Bank Edit</title>
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
                 minDate: new Date('<?php echo ($curruntDate) ?>'),
                 maxDate: new Date('<?php echo ($year_array[1]) ?>')
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
                  <a class="navbar-brand" href="home.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit Bank</span></a>
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
            <!-- last change on table START-->
            <div class="last-updates">
               <?php
                  $sqlLastChange="select username,updated_at from bank_balance where id='".$row['id']."'";
                  
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
            <!-- last change on table END-->            
            <div class="container-fluid">
               <div class="row justify-content-center">
                  <div class="card">
                     <div class="card-header">
                        Edit Bank Transaction
                     </div>
                     <div class="card-body">
                     <form class="" action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                           <input type="hidden" name="firm" value="<?php echo $_SESSION["bank_transaction_firm_id"]; ?>">
                           <input type="hidden" name="year" value="<?php echo $_SESSION["bank_transaction_financial_year_id"]; ?>">
                           <div class="row">
                              <div class="form-group col-md-5">
                                 <label for="date">Date</label>
                                 <input type="text" class="form-control " id="date" placeholder="Enter Date" name="date" value="<?php echo $date; ?>" autocomplete="off" readonly>
                                 <span id="date_err" style="color: red;font-size: 12px;"></span> 
                              </div>
                              <div class="form-group col-md-5">
                                 <label for="bank_ac_number">Bank Name</label>
                                 <?php
                                    $sql = "select * from party where id = ". $_SESSION["bank_transaction_firm_id"];
                                    $result = mysqli_query($conn, $sql);
                                    if(mysqli_num_rows($result) > 0){
                                       $firm_row = mysqli_fetch_assoc($result);
                                       $bank = json_decode($firm_row['bankDetails'],true);
                                    }
                                       if(isset($bank)){                
                                          foreach ($bank as $result) 
                                          {
                                              if($row['bank'] == $result['bank_ac_number']){
                                ?>
                                            <input type='text' class="form-control" placeholder="Bank Name" value="<?= $result['bank_name']; ?>" readonly>
                                <?php
                                              }
                                          }
                                       }
                                ?>        
                              </div>
                           </div>
                           <div class="row">
                              <div class="form-group col-md-4">
                                 <label for="balance">Opening Balance</label>
                                 <input type="text" class="form-control" id="previous_balance" name="previous_balance" value="<?php echo $row['previous_balance']; ?>" placeholder="Previous Balance"  readonly>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="balance">Receivable</label>
                                 <input type="text" class="form-control" id="balance" name="balance" placeholder="Enter Balance"  value="<?php echo $row['balance']; ?>" readonly>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="balance">Closing Balance</label>
                                 <input type="text" class="form-control" id="total_balance" name="total_balance" placeholder="Total Balance" value="<?php echo $row['total_balance']; ?>" readonly>
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

           });
      </script>
   </body>
</html>