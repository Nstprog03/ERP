<?php
session_start();
require_once('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}


  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from bank_receipt where id=".$id;
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
    <title>Bank Receipt Show</title>
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Show Bank Receipt Details</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
              <ul class="navbar-nav ml-auto"><a class="btn btn-outline-danger" href="home.php"><i class="fa fa-sign-out-alt"></i><span>Back</span></a>
              </ul>
          </div>
        </div>
      </nav>

       <!-- last change on table START-->
    <div class="last-updates">
     <?php
     $sqlLastChange="select username,updated_at from bank_master where id='".$row['id']."'";

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
            Bank Receipt Details
            </div>
            <div class="card-body">
              <div class="row">

                    <div class="form-group col-md-4">
                      <label for="date">Date</label>
                      <input type="text" class="form-control datepicker" placeholder=" Date" name="date" autocomplete="off" value="<?php echo date("d/m/Y", strtotime($row['date']));  ?>">
                    </div>   

                    <div class="form-group col-md-4">
                      <label for="bank_ac_number">Bank</label>
                        <?php
                            $bank = "";

                            if(isset($row['bank']) && $row['bank'] != ""){
                              $sql = "select * from party where id = ". $_SESSION["bank_transaction_firm_id"];
                              $result = mysqli_query($conn, $sql);
                              if(mysqli_num_rows($result) > 0){
                                 $firm_row = mysqli_fetch_assoc($result);
                                 $bankDetails = json_decode($firm_row['bankDetails'],true);

                                 foreach($bankDetails as $Bdata){
                                  if(isset($Bdata['bank_ac_number']) && $Bdata['bank_ac_number'] == $row['bank']){
                                    $bank = $Bdata['bank_name'];
                                  }
                                 }
                              }
                            }
                        ?> 
                           
                        <input type="text" class="form-control datepicker" placeholder=" Date" name="date" autocomplete="off" value="<?php if(isset($bank)){ echo $bank;} ?>">
                    </div>   
                   
                    <div class="form-group col-md-4">
                      <label for="balance">Bank Balance</label>
                      <input type="text" class="form-control" name="balance" placeholder=" Balance" value="<?php echo $row['balance'] ?>">
                    </div>
                    
                    <div class="form-group col-md-4">
                      <label for="balance">Table</label>
                      <input type="text" class="form-control"  placeholder="Table" value="<?php echo $row['table_indicator'] ?>">
                    </div> 

 
                        
                    <div class="form-group col-sm-4">
                      <label for="ext_prt">External Party</label>
                      <?php
                        $sql1 = "select * from external_party where id = ".$row['party'];
                        $result1 = mysqli_query($conn, $sql1);
                        if(mysqli_num_rows($result1) > 0){
                            $row1 = mysqli_fetch_assoc($result1);
                        }
                      ?>  
                      <input type="text" class="form-control" name="balance" placeholder="Enter External Party" value="<?php if(isset($row1)){ echo $row1['partyname']; } ?>">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="payment">Payment</label>
                        <input type="text" class="form-control" name ="total_payment"  placeholder="Enter Payment" id="total_payment" value="<?php echo $row['total_payment']; ?>" readonly>
                        <span id="amount_err" style="color: red;font-size: 12px;"></span>
                    </div>
                </div>

                <div class="InvoiceSection" style="<?php if($row['payment_method'] == "Party Wise" ){ echo "display:none"; } ?>">
                  <div class="dynamicSection">
                    <?php
                          if($row['table_indicator'] == "Sales Recievable"){
                            $select = "SELECT * FROM `bank_receipt_payment_data` WHERE `bank_receipt_id` = ".$row['id'];
                            $sql_result = mysqli_query($conn,$select);
                            if(mysqli_num_rows($sql_result) > 0){
                      ?>
                            <div class="row mb-0">
                                <div class="form-group col-md-4 mb-0">
                                    <label for="invoice_no">Invoice No.</label>
                                </div>
                                <div class="col-md-4 form-group mb-0">
                                    <label for="payment">Payment</label>
                                </div>
                            </div>
                      <?php
                       
                            foreach($conn->query($select) as $data){
                      ?>
                          <div class="row">
                              <div class="form-group col-md-4">
                                  <input type="text" class="form-control invoice_no"  value="<?php echo $data['invoice_no']; ?>" readonly>
                              </div>
                              <div class="col-md-4 form-group">
                                  <input type="text" name="payment[]" value="<?php echo $data['payment']; ?>" class="form-control avl_bal" readonly>
                              </div>
                          </div>
                      <?php
                              }
                            }
                          }
                    ?>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                      <div class="form-group">
                        <label for="remark">Remark</label>
                        <textarea class="form-control" name="remark" rows="3" cols="60" placeholder="Enter Remark"><?= $row['remark'];?></textarea>
                      </div>
                  </div>
                </div>
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

    <script type="text/javascript">
        $(document).ready(function () {
          $('input[type="text"], textarea').attr('readonly','readonly');

            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
                $(document).ready(function () {
          $('#myModal').on('show.bs.modal', function (e) {
              var image = $(e.relatedTarget).attr('src');
              $(".img-responsive").attr("src", image);
          });
        });
    </script>



    </body>
  </html>
